<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        
        

  
        // FIXED: Pull statuses into memory to bypass strict PostgreSQL GroupBy constraints entirely
        $allProjectStatuses = Project::pluck('Project_Status')->map(function ($status) {
            return strtolower(trim((string)$status));
        });

        // Count directly via Laravel Collection helpers
        $project_status_counts = [
            'pending'  => $allProjectStatuses->where('pending')->count(),
            // Combine both approved and completed statuses safely into one metric
            'approved' => $allProjectStatuses->where('approved')->count() + $allProjectStatuses->where('completed')->count(),
            'rejected' => $allProjectStatuses->where('rejected')->count(),
            'archived' => $allProjectStatuses->where('archived')->count(),
        ];

        // Total submitted projects excluding drafts
        $total_projects = array_sum($project_status_counts);
        
        // Only count students whose registration is approved
        $total_students = Student::whereHas('user', function($q) {
            $q->where('approved', true);
        })->count();

        // Filters from request
        $search = $request->input('q');
        $filterDate = $request->input('date'); // expected YYYY-MM-DD
        $filterSection = $request->input('section'); //NSTP section filter
        $filterComponent = $request->input('component'); //NSTP component filter
        $filterStatus = $request->input('activity_status'); //allow filtering activities by status (e.g., pending, ongoing, completed)

        // If component is ROTC and no section specified, default to 'Section A' (DB stores sections with prefix)
        if (empty($filterSection) && !empty($filterComponent) && strtoupper($filterComponent) === 'ROTC') {
            $filterSection = 'Section A';
        }

        // Build an upcoming activities list for projects whose `Project_Status` is active.
        /* By default exclude activities with status 'completed'. If a status filter is provided, 
           show only activities that match that status (case-insensitive). */
        $upcomingQuery = Activity::whereHas('project', function ($q) {
                // Only show activities for projects with status 'approved'
                $q->where('Project_Status', 'approved');
            });

        if (!empty($filterStatus)) {
            $upcomingQuery->where('status', 'ilike', strtolower($filterStatus)); 
        } else {
            // Exclude completed by default
            $upcomingQuery->where('status', '!=', 'completed');
        }

        $upcoming_activities = $upcomingQuery->with('project')
            ->orderBy('Implementation_Date')
            ->paginate(10)
            ->through(function ($a) {
                    $projComp = $a->project?->Project_Component ?? '';
                    return [
                        'project_id' => $a->project?->id ?? null,
                        'activity_id' => $a->id,
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
                        'project_logo' => $a->project?->Project_Logo ? Storage::disk('s3')->url($a->project->Project_Logo) : null,
                    ];
            });

        // Build filtered activities by applying the form filters to the same set of "upcoming" activities (projects with status current/approved).
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
                $filteredQuery->where('status', 'ilike', strtolower($filterStatus));
            } else {
                $filteredQuery->where('status', '!=', 'completed');
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
                        'project_id' => $a->project?->id ?? null,
                        'activity_id' => $a->id,
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
                        'project_logo' => $a->project?->Project_Logo ? Storage::disk('s3')->url($a->project->Project_Logo) : null,
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
