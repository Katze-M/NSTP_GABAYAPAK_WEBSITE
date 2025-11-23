<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        // Count projects by status (use actual DB values)
        $projectStatusCounts = Project::select('Project_Status', DB::raw('count(*) as count'))
            ->groupBy('Project_Status')
            ->pluck('count', 'Project_Status')
            ->toArray();

        // Ensure expected keys exist (defaults to 0)
        $project_status_counts = [
            'pending' => (int) ($projectStatusCounts['pending'] ?? 0),
            // Use 'current' status as the measure for approved/current projects
            'approved' => (int) ($projectStatusCounts['current'] ?? 0),
            'rejected' => (int) ($projectStatusCounts['rejected'] ?? 0),
        ];

        $total_projects = Project::count();
        $total_students = Student::count();

        // Filters from request
        $search = $request->input('q');
        $filterDate = $request->input('date'); // expected YYYY-MM-DD
        $filterSection = $request->input('section');
        $filterComponent = $request->input('component');

        // Build activities query: implementation date today or later
        $activityQuery = Activity::whereNotNull('Implementation_Date')
            ->whereDate('Implementation_Date', '>=', now())
            ->with('project');

        // Apply search across activity title, project name and team name
        if (!empty($search)) {
            $activityQuery->where(function ($q) use ($search) {
                $q->where('Specific_Activity', 'like', '%' . $search . '%')
                  ->orWhereHas('project', function ($p) use ($search) {
                      $p->where('Project_Name', 'like', '%' . $search . '%')
                        ->orWhere('Project_Team_Name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by exact date if provided
        if (!empty($filterDate)) {
            $activityQuery->whereDate('Implementation_Date', $filterDate);
        }

        // Filter by project section
        if (!empty($filterSection)) {
            $activityQuery->whereHas('project', function ($p) use ($filterSection) {
                $p->where('Project_Section', $filterSection);
            });
        }

        // Filter by project component
        if (!empty($filterComponent)) {
            $activityQuery->whereHas('project', function ($p) use ($filterComponent) {
                $p->where('Project_Component', $filterComponent);
            });
        }

        // Filters: sections A-Z and fixed components order (ROTC, LTS, CWTS)
        $sections = collect(range('A', 'Z'));
        $components = collect(['ROTC', 'LTS', 'CWTS']);

        $upcoming_activities = $activityQuery->orderBy('Implementation_Date')->take(50)->get()->map(function ($a) {
            return [
                'project_id' => $a->project?->Project_ID ?? null,
                'activity_id' => $a->Activity_ID,
                'title' => $a->Specific_Activity,
                'project_name' => $a->project?->Project_Name ?? '',
                'component' => $a->project?->Project_Component ?? '',
                'date' => $a->Implementation_Date?->format('Y-m-d') ?? null,
                'location' => $a->project?->Project_Target_Community ?? '',
                'team' => $a->project?->Project_Team_Name ?? '',
            ];
        });

            return view('dashboard', compact(
            'project_status_counts',
            'total_projects',
            'total_students',
            'upcoming_activities',
            'sections',
            'components'
        ));
    }
}
