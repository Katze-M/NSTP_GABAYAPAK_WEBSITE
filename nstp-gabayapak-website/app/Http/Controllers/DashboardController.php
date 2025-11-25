<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Ensure expected keys exist (defaults to 0).
        // Note: some records may use 'approved' or 'current' interchangeably; treat both as "current/approved".
        // Treat 'approved' and 'completed' as part of the Current/approved group
        $project_status_counts = [
            'pending' => (int) ($projectStatusCounts['pending'] ?? 0),
            // Treat both 'approved' and 'completed' as current/approved projects so
            // completed projects are counted among current projects and in totals.
            'approved' => (int) (($projectStatusCounts['approved'] ?? 0) + ($projectStatusCounts['completed'] ?? 0)),
            'rejected' => (int) ($projectStatusCounts['rejected'] ?? 0),
            'archived' => (int) ($projectStatusCounts['archived'] ?? 0),
        ];

        // Total submitted projects should exclude drafts. Count only pending, current/approved, and rejected.
        $total_projects = $project_status_counts['pending'] + $project_status_counts['approved'] + $project_status_counts['rejected'] + $project_status_counts['archived'];
        $total_students = Student::count();

        // Filters from request
        $search = $request->input('q');
        $filterDate = $request->input('date'); // expected YYYY-MM-DD
        $filterSection = $request->input('section');
        $filterComponent = $request->input('component');
        // New: allow filtering activities by status (e.g., pending, ongoing, completed)
        $filterStatus = $request->input('activity_status');

        // If component is ROTC and no section specified, default to 'Section A' (DB stores sections with prefix)
        if (empty($filterSection) && !empty($filterComponent) && strtoupper($filterComponent) === 'ROTC') {
            $filterSection = 'Section A';
        }

        // Build an upcoming activities list for projects whose `Project_Status` is active.
        // By default exclude activities with status 'completed'. If a status filter is provided,
        // show only activities that match that status (case-insensitive).
        $upcomingQuery = Activity::whereHas('project', function ($q) {
                // Consider both 'approved' and 'completed' as active (unarchived) projects
                $q->whereIn('Project_Status', ['approved', 'completed']);
            });

        if (!empty($filterStatus)) {
            $upcomingQuery->whereRaw('LOWER(`status`) = ?', [strtolower($filterStatus)]);
        } else {
            // exclude completed by default
            $upcomingQuery->whereRaw('LOWER(`status`) <> ?', ['completed']);
        }

        $upcoming_activities = $upcomingQuery->with('project')
            ->orderBy('Implementation_Date')
            ->paginate(10)
            ->through(function ($a) {
                    $projComp = $a->project?->Project_Component ?? '';
                    return [
                        'project_id' => $a->project?->Project_ID ?? null,
                        'activity_id' => $a->Activity_ID,
                        'title' => $a->Specific_Activity,
                        // store a normalized component key for logic and keep original label for display
                        'component' => strtoupper(trim((string) $projComp)),
                        'component_label' => $projComp,
                        'project_name' => $a->project?->Project_Name ?? '',
                        'date' => $a->Implementation_Date?->format('Y-m-d') ?? null,
                        'point_persons' => $a->Point_Persons ?? ($a->project?->Project_Team_Name ?? ''),
                        'section' => $a->project?->Project_Section ?? null,
                        'timeframe' => $a->Time_Frame ?? '',
                        'team' => $a->project?->Project_Team_Name ?? '',
                        'status' => $a->status ?? null,
                        'project_logo' => $a->project?->Project_Logo ? asset('storage/' . $a->project->Project_Logo) : null,
                    ];
            });

        // Build filtered activities by applying the form filters to the same
        // set of "upcoming" activities (projects with status current/approved).
        // This ensures filters operate on the list users see below.
        $filtered_activities = collect();
        $hasAnyFilter = !empty($search) || !empty($filterDate) || !empty($filterSection) || !empty($filterComponent) || !empty($filterStatus);

        if ($hasAnyFilter) {
            $filteredQuery = Activity::with('project')
                ->whereHas('project', function ($q) {
                    // Consider both 'approved' and 'completed' as active projects
                    $q->whereIn('Project_Status', ['approved', 'completed']);
                });

            // Apply status filter same as upcoming list: default exclude 'completed', or match provided status
            if (!empty($filterStatus)) {
                $filteredQuery->whereRaw('LOWER(`status`) = ?', [strtolower($filterStatus)]);
            } else {
                $filteredQuery->whereRaw('LOWER(`status`) <> ?', ['completed']);
            }

            // Debugging: log counts for component-only, section-only, and both
            try {
                $compCount = Project::where('Project_Component', $filterComponent)->count();
                $sectCount = Project::where('Project_Section', $filterSection)->count();
                $bothCount = Project::where('Project_Component', $filterComponent)
                    ->where('Project_Section', $filterSection)
                    ->count();
                Log::info('Dashboard filters debug', [
                    'component' => $filterComponent,
                    'section' => $filterSection,
                    'component_count' => $compCount,
                    'section_count' => $sectCount,
                    'both_count' => $bothCount,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Dashboard filter debug failed: ' . $e->getMessage());
            }

            // Apply search across activity title, project name and team name
            if (!empty($search)) {
                $filteredQuery->where(function ($q) use ($search) {
                    $q->where('Specific_Activity', 'like', '%' . $search . '%')
                      ->orWhereHas('project', function ($p) use ($search) {
                          $p->where('Project_Name', 'like', '%' . $search . '%')
                            ->orWhere('Project_Team_Name', 'like', '%' . $search . '%');
                      });
                });
            }

            // Filter by exact date if provided
            if (!empty($filterDate)) {
                $filteredQuery->whereDate('Implementation_Date', $filterDate);
            }

            // Filter by project section
            if (!empty($filterSection)) {
                $filteredQuery->whereHas('project', function ($p) use ($filterSection) {
                    $p->where('Project_Section', $filterSection);
                });
            }

            // Filter by project component
            if (!empty($filterComponent)) {
                $filteredQuery->whereHas('project', function ($p) use ($filterComponent) {
                    $p->where('Project_Component', $filterComponent);
                });
            }

            $filtered_activities = $filteredQuery->orderBy('Implementation_Date')->take(200)->get()->map(function ($a) {
                    $projComp = $a->project?->Project_Component ?? '';
                    return [
                        'project_id' => $a->project?->Project_ID ?? null,
                        'activity_id' => $a->Activity_ID,
                        'title' => $a->Specific_Activity,
                        'component' => strtoupper(trim((string) $projComp)),
                        'component_label' => $projComp,
                        'project_name' => $a->project?->Project_Name ?? '',
                        'date' => $a->Implementation_Date?->format('Y-m-d') ?? null,
                        'point_persons' => $a->Point_Persons ?? ($a->project?->Project_Team_Name ?? ''),
                        'section' => $a->project?->Project_Section ?? null,
                        'team' => $a->project?->Project_Team_Name ?? '',
                        'timeframe' => $a->Time_Frame ?? '',
                        'status' => $a->status ?? null,
                        'project_logo' => $a->project?->Project_Logo ? asset('storage/' . $a->project->Project_Logo) : null,
                    ];
            });
        }

        // Filters: sections A-Z and fixed components order (ROTC, LTS, CWTS)
        $sections = collect(range('A', 'Z'));
        $components = collect(['ROTC', 'LTS', 'CWTS']);

        return view('dashboard', compact(
            'project_status_counts',
            'total_projects',
            'total_students',
            'upcoming_activities',
            'filtered_activities',
            'sections',
            'components'
        ));
    }
}
