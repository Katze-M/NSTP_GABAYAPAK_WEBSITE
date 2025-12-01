<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project)
    {
        if ($user->isStaff()) return true;
        if ($user->isStudent()) {
            $sid = $user->student->id ?? null;
            if ($sid && $project->student_id === $sid) return true;
            // allow members to view
            if ($sid && is_array($project->student_ids) && in_array($sid, $project->student_ids)) return true;
            // allow public statuses
            $public = ['current', 'approved', 'completed'];
            if (in_array(strtolower((string)$project->Project_Status), $public)) return true;
        }
        return false;
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project)
    {
        if ($user->isStaff()) return true;
        if ($user->isStudent()) {
            $sid = $user->student->id ?? null;
            // only owner/leader may update
            return $sid && $project->student_id === $sid;
        }
        return false;
    }

    /**
     * Determine whether the user can create projects.
     * Students are blocked when they already have an active project (owner or member).
     */
    public function create(User $user)
    {
        if (!$user->isStudent()) return false;
        return $user->activeProject() === null;
    }
}
