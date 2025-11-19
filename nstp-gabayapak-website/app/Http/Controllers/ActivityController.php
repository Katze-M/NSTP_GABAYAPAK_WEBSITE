<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    /**
     * Show the form for editing the specified activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        // Check if the authenticated user owns the project this activity belongs to
        if (Auth::user()->student->id !== $activity->project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only allow editing activities for submitted projects
        if ($activity->project->Project_Status !== 'submitted') {
            return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status and proof can only be updated for submitted projects.');
        }
        
        return view('activities.edit', compact('activity'));
    }

    /**
     * Update the specified activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        // Check if the authenticated user owns the project this activity belongs to
        if (Auth::user()->student->id !== $activity->project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only allow updating activities for submitted projects
        if ($activity->project->Project_Status !== 'submitted') {
            return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status and proof can only be updated for submitted projects.');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            // accept common casings, we'll normalize before saving
            'status' => 'required|string|in:Planned,Ongoing,Completed,planned,ongoing,completed',
            'Implementation_Date' => 'nullable|date',
            'proof_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        // Handle file upload
        if ($request->hasFile('proof_picture')) {
            // Delete old proof picture if exists
            // Note: We need to find the budget directly now since we removed the activity relationship
            $budget = Budget::where('project_id', $activity->project_id)->first();
            if ($budget && $budget->proof_picture) {
                Storage::disk('public')->delete($budget->proof_picture);
            }
            
            // Store new proof picture
            $validatedData['proof_picture'] = $request->file('proof_picture')->store('proof_pictures', 'public');
            
            // Update the budget with the proof picture
            $budget = Budget::where('project_id', $activity->project_id)->first();
            if ($budget) {
                $budget->update(['proof_picture' => $validatedData['proof_picture']]);
            } else {
                // Create a budget if it doesn't exist
                Budget::create([
                    'project_id' => $activity->project_id,
                    'proof_picture' => $validatedData['proof_picture'],
                ]);
            }
        }
        
        // Normalize status casing (store with initial capital) and update the activity
        $statusNormalized = ucfirst(strtolower($validatedData['status']));

        $activity->update([
            'status' => $statusNormalized,
            'Implementation_Date' => $validatedData['Implementation_Date'] ?? null,
        ]);
        
        return redirect()->route('projects.show', $activity->project)->with('success', 'Activity updated successfully!');
    }
}