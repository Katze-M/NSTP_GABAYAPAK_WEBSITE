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
        
        // Only allow editing activities for submitted or current projects
        if ($activity->project->Project_Status !== 'submitted' && $activity->project->Project_Status !== 'current') {
            return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status and proof can only be updated for submitted or current projects.');
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
        
        // Only allow updating activities for submitted or current projects
        if ($activity->project->Project_Status !== 'submitted' && $activity->project->Project_Status !== 'current') {
            return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status and proof can only be updated for submitted or current projects.');
        }

        // Prevent changing status once activity is already completed
        if (strtolower((string)$activity->status) === 'completed') {
            $requestedStatus = strtolower((string)$request->input('status', ''));
            if ($requestedStatus !== '' && $requestedStatus !== 'completed') {
                return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status cannot be changed after it has been marked as completed. You may still upload proof.');
            }
        }
        
        // Check if activity already has a proof picture
        $hasExistingProof = (bool) $activity->proof_picture;

        // Determine if the status is being changed by the user
        $newStatus = $request->input('status', '');
        $statusChanged = strtolower((string)$newStatus) !== strtolower((string)$activity->status);

        // If the student is changing the status, require a new proof picture.
        // Otherwise, require a picture only if none exists yet.
        $proofRule = ($statusChanged || ! $hasExistingProof)
            ? 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            : 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';

        // Validate the request
        $validatedData = $request->validate([
            // accept common casings, we'll normalize before saving
            'status' => 'required|string|in:Planned,Ongoing,Completed,planned,ongoing,completed',
            'Implementation_Date' => 'nullable|date',
            'proof_picture' => $proofRule,
        ]);
        
        // Handle file upload if a new file is provided
        if ($request->hasFile('proof_picture')) {
            $file = $request->file('proof_picture');
            // Log file details
            logger()->info('File uploaded:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            // Delete old proof picture if exists (from Activity)
            if ($activity->proof_picture) {
                Storage::disk('public')->delete($activity->proof_picture);
                logger()->info('Old proof picture deleted from Activity:', ['path' => $activity->proof_picture]);
            }

            // Store new proof picture
            $newProofPath = $file->store('proof_pictures', 'public');
            logger()->info('New proof picture stored:', ['path' => $newProofPath]);

            // Update the activity with the new proof picture
            $activity->update(['proof_picture' => $newProofPath]);

            // Optionally, update the budget as well if needed (keep if you want to sync)
            $budget = Budget::where('project_id', $activity->project_id)->first();
            if ($budget) {
                $budget->update(['proof_picture' => $newProofPath]);
                logger()->info('Budget updated with new proof picture:', ['budget_id' => $budget->id]);
            }
        }
        
        // Normalize status casing (store with initial capital) and update the activity
        $statusNormalized = ucfirst(strtolower($validatedData['status']));

        $activity->update([
            'status' => $statusNormalized,
            'Implementation_Date' => $validatedData['Implementation_Date'] ?? null,
            // proof_picture is already updated above if a new file was uploaded
        ]);
        
        return redirect()->route('projects.show', $activity->project)->with('success', 'Activity updated successfully!');
    }
}