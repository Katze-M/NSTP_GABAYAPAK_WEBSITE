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
        
        // Only allow editing activities for approved projects (DB canonical)
        $projStatus = strtolower(trim((string)($activity->project->Project_Status ?? '')));
        if ($projStatus !== 'approved') {
            return redirect()->route('projects.show', $activity->project)->with('warning', 'Activity status and proof can only be updated for approved projects.');
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
        
        // Only allow updating activities for approved projects (DB canonical)
        $projStatus = strtolower(trim((string)($activity->project->Project_Status ?? '')));
        if ($projStatus !== 'approved') {
            return redirect()->route('projects.show', $activity->project)->with('warning', 'Activity status and proof can only be updated for approved projects.');
        }

        // Prevent changing status once activity is already completed
        if (strtolower((string)$activity->status) === 'completed') {
            $requestedStatus = strtolower((string)$request->input('status', ''));
            if ($requestedStatus !== '' && $requestedStatus !== 'completed') {
                return redirect()->route('projects.show', $activity->project)->with('error', 'Activity status cannot be changed after it has been marked as completed. You may still upload proof.');
            }
        }
        
        // Check if activity already has any proof pictures stored in updates
        $hasExistingProof = (bool) $activity->updates()->whereHas('pictures')->exists();

        // Determine if the status is being changed by the user
        $newStatus = $request->input('status', '');
        $statusChanged = strtolower((string)$newStatus) !== strtolower((string)$activity->status);

        // Disallow reverting from Ongoing -> Planned for students
        try {
            $currentLower = strtolower((string)$activity->status);
            $requestedLower = strtolower((string)$newStatus);
            if ($currentLower === 'ongoing' && $requestedLower === 'planned' && Auth::check() && method_exists(Auth::user(), 'isStudent') && Auth::user()->isStudent()) {
                return redirect()->back()->with('error', 'You cannot change an activity from Ongoing back to Planned.');
            }
        } catch (\Throwable $e) {
            // If anything fails in this check, fall through to normal validation and rely on validation rules.
        }

        // STRICT RULE (refined): If the latest update was created by this same student and it already
        // contains proof pictures, disallow submitting further proof for the SAME status again.
        // However, allow the student to submit a new update when they change the status (e.g., Ongoing -> Completed).
        try {
            $latestUpdateCheck = $activity->updates()->orderByDesc('created_at')->first();
            if (Auth::check() && method_exists(Auth::user(), 'isStudent') && Auth::user()->isStudent() && $latestUpdateCheck && $latestUpdateCheck->user_id == Auth::id() && $latestUpdateCheck->pictures()->exists()) {
                $latestStatus = strtolower(trim((string)$latestUpdateCheck->status));
                $requestedLower = strtolower(trim((string)$newStatus));
                // If the user is attempting to submit for the same status they already submitted proof for, block it.
                if ($latestStatus === $requestedLower) {
                    return redirect()->back()->with('error', 'You already submitted proof for this status; change status to submit new proof.');
                }
                // Otherwise allow: user is submitting proof for a different/new status.
            }
        } catch (\Throwable $e) {
            // ignore and continue
        }

        // If the student is changing the status, require at least one new proof picture.
        // Otherwise, require a picture only if none exists yet. We accept up to 5 files.
        $proofArrayRule = ($statusChanged || ! $hasExistingProof)
            ? 'required|array|max:5'
            : 'nullable|array|max:5';

        $validatedData = $request->validate([
            'status' => 'required|string|in:Planned,Ongoing,Completed,planned,ongoing,completed',
            'Implementation_Date' => 'nullable|date',
            'proof_pictures' => $proofArrayRule,
            'proof_pictures.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        // Always create a new ActivityUpdate for this action (no appending to previous updates)
        $statusNormalized = ucfirst(strtolower($validatedData['status']));
        $updateRecord = \App\Models\ActivityUpdate::create([
            'activity_id' => $activity->Activity_ID,
            'user_id' => Auth::id(),
            'status' => $statusNormalized,
        ]);

        // Handle multiple file upload (up to 5) and attach them to the update record
        if ($request->hasFile('proof_pictures')) {
            $files = $request->file('proof_pictures');

            // Enforce per-update maximum of 5 pictures (existing + new)
            $existingCount = $updateRecord->pictures()->count();
            $incomingCount = is_array($files) ? count($files) : 0;
            if ($existingCount + $incomingCount > 5) {
                return redirect()->back()->with('error', 'Cannot attach more than 5 pictures to a single update.')->withInput();
            }

            $lastStoredPath = null;
            foreach ($files as $file) {
                if (! $file->isValid()) {
                    continue;
                }
                logger()->info('Proof picture uploaded for activity update:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                // Store file (do NOT delete previous activity proof files — we keep history)
                $path = $file->store('proof_pictures', 'public');
                logger()->info('Stored proof picture for update:', ['path' => $path]);

                \App\Models\ActivityUpdatePicture::create([
                    'activity_update_id' => $updateRecord->id,
                    'path' => $path,
                ]);

                $lastStoredPath = $path;
            }

                // Note: we intentionally do NOT set a legacy `activities.proof_picture` column.
                // Proof images are stored in `activity_updates` -> `activity_update_pictures`.
        }

        // Prepare update payload. Preserve existing Implementation_Date when not provided.
        $updatePayload = ['status' => $statusNormalized];
        if (array_key_exists('Implementation_Date', $validatedData) && $validatedData['Implementation_Date'] !== null && $validatedData['Implementation_Date'] !== '') {
            $updatePayload['Implementation_Date'] = $validatedData['Implementation_Date'];
        }

        // Do not include legacy `proof_picture` in payload; activity updates hold proof images.

        $activity->update($updatePayload);
        
        return redirect()->route('projects.show', $activity->project)->with('success', 'Activity updated successfully!');
    }
}