<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StaffApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Authorization handled by middleware
        $q = trim($request->input('q', ''));

        $query = Approval::where('type', 'staff')
            ->where('status', 'pending')
            ->with('user');

        if ($q !== '') {
            $query->whereHas('user', function($u) use ($q) {
                $u->where('user_Name', 'like', "%{$q}%")
                  ->orWhere('user_Email', 'like', "%{$q}%")
                  ->orWhere('user_role', 'like', "%{$q}%");
            });
        }

        $perPage = 15;

        // Get approvals (as a collection)
        $approvals = $query->orderBy('created_at', 'desc')->get();

        // Find legacy staff users: staff users not approved and without any approval records
        $legacyUsersQuery = User::where('user_Type', 'staff')
            ->where(function($q2) {
                $q2->whereNull('approved')->orWhere('approved', false);
            })
            ->whereDoesntHave('approvals')
            ->with('staff');

        if ($q !== '') {
            $legacyUsersQuery->where(function($u) use ($q) {
                $u->where('user_Name', 'like', "%{$q}%")
                  ->orWhere('user_Email', 'like', "%{$q}%")
                  ->orWhere('user_role', 'like', "%{$q}%");
            });
        }

        $legacyUsers = $legacyUsersQuery->get();

        // Convert legacy users into Approval-like objects so the view can render them similarly
        $legacyApprovals = $legacyUsers->map(function($user) {
            $fake = new Approval();
            $fake->id = 0; // indicator it's synthetic
            $fake->user_id = $user->user_id;
            $fake->type = 'staff';
            $fake->status = 'pending';
            $fake->remarks = null;
            $fake->created_at = $user->created_at;
            // attach the user relation
            $fake->setRelation('user', $user);
            return $fake;
        });

        // Merge and sort by created_at desc
        $all = $approvals->merge($legacyApprovals)->sortByDesc(function($item){
            return optional($item->created_at);
        })->values();

        // Manual pagination using LengthAwarePaginator
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $all->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $pending = new LengthAwarePaginator($items, $all->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);

        return view('approvals.staff_index', compact('pending', 'q'));
    }

    public function history(Request $request)
    {
        // Authorization handled by middleware
        $q = trim($request->input('q', ''));

        $query = Approval::where('type', 'staff')
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['user', 'approver']);

        if ($q !== '') {
            $query->where(function($query) use ($q) {
                $query->whereHas('user', function($u) use ($q) {
                    $u->where('user_Name', 'like', "%{$q}%")
                      ->orWhere('user_Email', 'like', "%{$q}%")
                      ->orWhere('user_role', 'like', "%{$q}%");
                })
                ->orWhereHas('approver', function($a) use ($q) {
                    $a->where('user_Name', 'like', "%{$q}%");
                });
            });
        }

        $perPage = 15;
        $history = $query->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('approvals.staff_history', compact('history', 'q'));
    }

    public function approve(Request $request, $id)
    {
        $user = auth()->user();

        $approval = Approval::findOrFail($id);
        $approval->status = 'approved';
        $approval->approver_id = $user->user_id;
        $approval->approver_role = $user->user_role;
        $approval->remarks = $request->remarks;
        $approval->save();

        // mark user approved
        $u = $approval->user;
        $u->approved = true;
        $u->save();

        return redirect()->back()->with('status', 'Staff registration approved.');
    }

    public function reject(Request $request, $id)
    {
        $user = auth()->user();

        $approval = Approval::findOrFail($id);
        $approval->status = 'rejected';
        $approval->approver_id = $user->user_id;
        $approval->approver_role = $user->user_role;
        $approval->remarks = $request->remarks;
        $approval->save();

        // leave user not approved
        return redirect()->back()->with('status', 'Staff registration rejected.');
    }
}
