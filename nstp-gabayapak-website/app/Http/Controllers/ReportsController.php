<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;

class ReportsController extends Controller
{
    /**
     * Display reports overview.
     */
    public function index(Request $request)
    {
        // Project proposals by submission status
        // Count projects marked 'approved' as approved/active.
        // Treat completed and archived projects as approved proposals as well.
        $approved = Project::whereIn('Project_Status', ['approved', 'completed', 'archived'])->count();
        $pending  = Project::where('Project_Status', 'pending')->count();
        $draft    = Project::where('Project_Status', 'draft')->count();
        $total    = Project::count();

        $project_proposals = [
            'approved' => $approved,
            'pending' => $pending,
            'draft' => $draft,
            'total' => $total,
        ];

        // Project implementation status
        $project_status = [
            'ongoing' => Project::where('Project_Status', 'ongoing')->count(),
            // Include archived projects in completed counts as requested
            'completed' => Project::whereIn('Project_Status', ['completed', 'archived'])->count(),
            'archived' => Project::where('Project_Status', 'archived')->count(),
        ];

        // Components breakdown (ROTC, LTS, CWTS)
        $components = Project::select('Project_Component', DB::raw('count(*) as cnt'))
            ->groupBy('Project_Component')
            ->pluck('cnt', 'Project_Component')
            ->toArray();

        $components_breakdown = [
            'ROTC' => $components['ROTC'] ?? 0,
            'LTS'  => $components['LTS'] ?? 0,
            'CWTS' => $components['CWTS'] ?? 0,
        ];

        // Project progress: percent complete (activities completed / total activities) and total budget
        $projects = Project::withCount([
            'activities',
            // alias for completed activities count
            'activities as activities_completed_count' => function ($q) {
                $q->where('status', 'completed');
            },
            // count planned activities
            'activities as activities_planned_count' => function ($q) {
                $q->where('status', 'planned');
            },
            // count ongoing activities
            'activities as activities_ongoing_count' => function ($q) {
                $q->where('status', 'ongoing');
            }
        ])->get();

        $project_progress = $projects->map(function ($p) {
            // Consider planned and ongoing activities as part of the work scope.
            // New weighting per activity:
            // - Each activity accounts for 1 / total_activities of the project's progress.
            // - Planned activity contributes 1/3 of its share.
            // - Ongoing activity contributes 2/3 of its share.
            // - Completed activity contributes the full share.
            // So effectiveCompleted = completed*1 + ongoing*(2/3) + planned*(1/3)
            $planned = $p->activities_planned_count ?: 0;
            $ongoing = $p->activities_ongoing_count ?: 0;
            $completed = $p->activities_completed_count ?: 0;
            $activitiesTotal = $planned + $ongoing + $completed;
            if ($activitiesTotal > 0) {
                $effectiveCompleted = $completed + ($ongoing * (2/3)) + ($planned * (1/3));
                $progress = round(($effectiveCompleted / $activitiesTotal) * 100);
            } else {
                $progress = 0;
            }

            return [
                'name' => $p->Project_Name ?? '—',
                'component' => $p->Project_Component ?? '—',
                'progress' => $progress,
                'budget' => $p->total_budget ?? 0,
                // Include status and section so the client-side filter can operate without extra queries
                'status' => $p->Project_Status ?? null,
                'proposal_status' => $p->Project_Status ?? null,
                'section' => $p->Project_Section ?? null,
            ];
        })->toArray();

        return view('reports.index', compact(
            'project_proposals',
            'project_status',
            'components_breakdown',
            'project_progress'
        ));
    }
}
