<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MAController extends Controller
{
    public function index()
    {
        // Get all submitted applications (form_status = 2) that are being processed by MA (status_id = 4)
        $applications = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
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

        return view('ma.index', compact('applications'));
    }

    public function show($id)
    {
        // Get the specific application with all details
        $application = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->select(
                'leave_details.*',
                'personal_details.empno',
                'personal_details.name_with_initials',
                'personal_details.names_denoted_by_initials',
                'personal_details.department',
                'personal_details.faculty',
                'personal_details.designation',
                'personal_details.mobile',
                'personal_details.nic',
                'leave_types.name as leave_type_name',
                'statuses.status'
            )
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Decode JSON fields to arrays for multiple files
        $application->leave_documents = $application->leave_document
            ? json_decode($application->leave_document, true)
            : [];
        $application->consent_letters = $application->consent_letter
            ? json_decode($application->consent_letter, true)
            : [];

        return view('ma.show', compact('application'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2) // Complete/Submitted
            ->where('status_id', 4) // Processing MA
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Update status to Processing HOD (status_id = 5)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'status_id' => 5, // Processing HOD
                'remark' => $request->remark,
                'updated_at' => now()
            ]);

        return redirect()->route('ma.index')->with('success', 'Application forwarded to HOD successfully.');
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
            ->where('form_status', 2) // Complete/Submitted
            ->where('status_id', 4) // Processing MA
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
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

        return redirect()->route('ma.index')->with('success', 'Application returned to user successfully.');
    }

    public function dashboard()
    {
        // Get all applications that are being processed by MA
        $applications = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->whereIn('leave_details.form_status', [2, 3]) // Complete/Submitted
            ->whereIn('leave_details.status_id', [1, 2, 4, 5, 6, 7,8])
            //->where('leave_details.status_id', 4 ,'OR', 3, 'OR', 2) // Processing MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'personal_details.name_with_initials',
                'personal_details.department',
                'personal_details.faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status',
                'leave_details.remark'
            )
            ->get();

        return view('ma.dashboardDemo', compact('applications'));
    }
} 

