<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HODController extends Controller
{
    public function index()
    {
        // Get all applications for HOD review (status_id = 5)
        $applications = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 5) // Processing HOD
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'personal_details.name_with_initials',
                'personal_details.department',
                'personal_details.faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

        return view('hod.index', compact('applications'));
    }

    public function show($id)
    {
        $application = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 5)
            ->select(
                'leave_details.*',
                'personal_details.name_with_initials',
                'personal_details.names_denoted_by_initials',
                'personal_details.department',
                'personal_details.faculty',
                'personal_details.designation',
                'personal_details.mobile',
                'leave_types.name as leave_type_name',
                'statuses.status'
            )
            ->first();

        if (!$application) {
            return redirect()->route('hod.index')->with('error', 'Application not found.');
        }

        return view('hod.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'hod_adequate_staff' => 'required|boolean',
            'hod_teaching_covered' => 'required|boolean',
            'hod_exam_work_completed' => 'required|boolean',
            'hod_recommend' => 'required|boolean',
            'hod_not_recommend_reason' => 'required_if:hod_recommend,0',
            'hod_signature' => 'required|string|max:100',
        ]);

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2)
            ->where('status_id', 5)
            ->first();

        if (!$application) {
            return redirect()->route('hod.index')->with('error', 'Application not found.');
        }

        // If recommended, forward to Dean (status_id = 6), else keep at HOD with not recommended
        $status_id = $request->hod_recommend ? 6 : 5;

        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'hod_adequate_staff' => $request->hod_adequate_staff,
                'hod_teaching_covered' => $request->hod_teaching_covered,
                'hod_exam_work_completed' => $request->hod_exam_work_completed,
                'hod_recommend' => $request->hod_recommend,
                'hod_not_recommend_reason' => $request->hod_not_recommend_reason,
                'hod_other_remarks' => $request->hod_other_remarks,
                'hod_reviewed_by' => 'O. Wickramasinghe',
                'hod_reviewed_at' => now(),
                'hod_signature' => $request->hod_signature,
                'status_id' => $status_id,
                'updated_at' => now(),
            ]);

        $msg = $request->hod_recommend ? 'Application forwarded to Dean.' : 'Application not recommended.';
        return redirect()->route('hod.index')->with('success', $msg);
    }

    public function return(Request $request, $id)
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning an application.'
        ]);

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2)
            ->where('status_id', 5)
            ->first();

        if (!$application) {
            return redirect()->route('hod.index')->with('error', 'Application not found.');
        }

        // Update status to Returned (form_status = 3, status_id = 2 for Rejected)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'form_status' => 3, // Returned
                'status_id' => 2, // Rejected
                'remark' => $request->remark,
                'updated_at' => now()
            ]);

        return redirect()->route('hod.index')->with('success', 'Application returned to user successfully.');
    }
} 