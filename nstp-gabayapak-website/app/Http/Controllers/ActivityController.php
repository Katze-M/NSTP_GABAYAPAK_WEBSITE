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
        
        // Validate the request
        $validatedData = $request->validate([
            'status' => 'required|string|in:planned,ongoing,completed',
            'proof_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        // Handle file upload
        if ($request->hasFile('proof_picture')) {
            // Delete old proof picture if exists
            if ($activity->budget && $activity->budget->proof_picture) {
                Storage::disk('public')->delete($activity->budget->proof_picture);
            }
            
            // Store new proof picture
            $validatedData['proof_picture'] = $request->file('proof_picture')->store('proof_pictures', 'public');
            
            // Update the budget with the proof picture
            if ($activity->budget) {
                $activity->budget->update(['proof_picture' => $validatedData['proof_picture']]);
            } else {
                // Create a budget if it doesn't exist
                Budget::create([
                    'activity_id' => $activity->Activity_ID,
                    'proof_picture' => $validatedData['proof_picture'],
                ]);
            }
        }
        
        // Update the activity status
        $activity->update(['status' => $validatedData['status']]);
        
        return redirect()->route('projects.show', $activity->project)->with('success', 'Activity updated successfully!');
    }
}