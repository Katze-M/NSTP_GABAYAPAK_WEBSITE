<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;

class StudentApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Authorization handled by middleware
        $q = trim($request->input('q', ''));

        $query = Approval::where('type', 'student')
            ->where('status', 'pending')
            ->with('user');

        if ($q !== '') {
            $query->whereHas('user', function($u) use ($q) {
                $u->where('user_Name', 'like', "%{$q}%")
                  ->orWhere('user_Email', 'like', "%{$q}%")
                  ->orWhereHas('student', function($s) use ($q) {
                      $s->where('student_section', 'like', "%{$q}%")
                        ->orWhere('student_course', 'like', "%{$q}%")
                        ->orWhere('student_component', 'like', "%{$q}%");
                  });
            });
        }

        $perPage = 15;
        $pending = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('approvals.student_index', compact('pending', 'q'));
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

        return redirect()->back()->with('status', 'Student registration approved.');
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

        return redirect()->back()->with('status', 'Student registration rejected.');
    }
}
