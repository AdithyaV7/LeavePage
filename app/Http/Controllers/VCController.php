<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VCController extends Controller
{
    // Hardcoded VC employee number - change this to switch to a different VC
    private const VC_EMP_NO = 1001; // Example VC emp_no

    public function index()
    {
        // Get all applications for VC review (status_id = 7)
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 7) // Processing VC
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.initials as name_with_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

        return view('vc.index', compact('applications'));
    }

    public function show($id)
    {
        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 7)
            ->select(
                'leave_details.*',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile',
                'employees.nic',
                'leave_types.name as leave_type_name',
                'statuses.status'
            )
            ->first();

        if (!$application) {
            return redirect()->route('vc.index')->with('error', 'Application not found.');
        }

        // Decode JSON fields to arrays for multiple files
        $application->leave_documents = $application->leave_document
            ? json_decode($application->leave_document, true)
            : [];
        $application->consent_letters = $application->consent_letter
            ? json_decode($application->consent_letter, true)
            : [];

        // Get travel details for this application
        $travelDetails = DB::table('leave_request_details')
            ->where('reference_no', $application->reference_no)
            ->get()
            ->map(function ($detail) {
                // Decode documents JSON
                $detail->documents = $detail->documents ? json_decode($detail->documents, true) : [];

                // Ensure documents is always an array
                if (!is_array($detail->documents)) {
                    $detail->documents = [];
                }

                return $detail;
            });

        return view('vc.show', compact('application', 'travelDetails'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'vc_recommend_committee' => 'nullable|boolean',
            'vc_approved_council' => 'nullable|boolean',
            'vc_remarks' => 'nullable|string',
        ]);

        // At least one of the two must be set (yes/no)
        if (!isset($request->vc_recommend_committee) && !isset($request->vc_approved_council)) {
            return back()->with('error', 'Please select Yes or No for at least one of the options.');
        }

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2)
            ->where('status_id', 7)
            ->first();

        if (!$application) {
            return redirect()->route('vc.index')->with('error', 'Application not found.');
        }

        // Forward to MA dashboard (status_id = 8, for example)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'vc_recommend_committee' => $request->vc_recommend_committee,
                'vc_approved_council' => $request->vc_approved_council,
                'vc_remarks' => $request->vc_remarks,
                'vc_signature' => 'Dr. A. Silva', // Example signature
                'vc_name' => 'Dr. A. Silva',
                'vc_reviewed_at' => Carbon::now(),
                'vc_checked' => true,
                'vc_empno' => self::VC_EMP_NO, // Record which VC processed this
                'status_id' => 8, // Forwarded to MA dashboard (implement as needed)
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('vc.index')->with('success', 'Application forwarded.');
    }
} 