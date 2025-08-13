<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MAController extends Controller
{
    // Hardcoded MA user ID - change this to switch to a different MA
    private const MA_USER_ID = 12466; //15097 for testing 

    public function index()
    {
        $maUserId = self::MA_USER_ID;

        // Get all submitted applications (form_status = 2) that are being processed by MA (status_id = 4)
        // and are assigned to this specific MA
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

        return view('ma.index', compact('applications'));
    }

    public function show($id)
    {
        $maUserId = self::MA_USER_ID;

        // Get the specific application with all details
        // Only show if the employee is assigned to this MA
        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
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
            return redirect()->route('ma.index')->with('error', 'Application not found.');
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

        // Decide which blade to use and readonly status
        $readonly = false;
        $view = 'ma.show';
        switch ($application->status_id) {
            case 4: // Processing MA
                $view = 'ma.show';
                $readonly = false;
                break;
            case 5: // Processing HOD
                $view = 'hod.show';
                $readonly = true;
                break;
            case 6: // Processing Dean
                $view = 'dean.show';
                $readonly = true;
                break;
            case 7: // Processing VC
            case 8: // VC Approved
                $view = 'vc.show';
                $readonly = true;
                break;
            default:
                $view = 'ma.show';
                $readonly = true;
        }

        return view($view, compact('application', 'readonly', 'travelDetails'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $maUserId = self::MA_USER_ID;

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Prepare new remark by appending to existing remarks
        $newRemark = '';
        if ($request->remark) {
            $timestamp = now()->format('Y-m-d');
            $newRemark = "\n\n[MA Review - " . $timestamp . "]\n" . $request->remark;
        }

        // Update status to Processing HOD (status_id = 5)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'status_id' => 5, // Processing HOD
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'remark' => DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')"),
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

        $maUserId = self::MA_USER_ID;

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Prepare new remark by appending to existing remarks
        $timestamp = now()->format('Y-m-d');
        $newRemark = "\n\n[MA Return - " . $timestamp . "]\n" . $request->remark;

        // Update status to Returned (form_status = 3, status_id = 2 for Rejected)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'form_status' => 3, // Returned
                'status_id' => 2, // Rejected
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'remark' => DB::raw("CONCAT(COALESCE(remark, ' '), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('ma.index')->with('success', 'Application returned to user successfully.');
    }

    public function dashboard()
    {
        $maUserId = self::MA_USER_ID;

        // Get all applications that are being processed by MA and assigned to this MA
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->whereIn('leave_details.form_status', [2, 3]) // Complete/Submitted
            ->whereIn('leave_details.status_id', [1, 2, 4, 5, 6, 7,8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status',
                'leave_details.status_id',
                'leave_details.remark'
            )
            ->get();

        // Applications for status sidebar (status_id 4-8) assigned to this MA
        $statusApplications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->whereBetween('leave_details.status_id', [4, 8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'leave_types.name as leave_type',
                'leave_details.status_id'
            )
            ->get();

        return view('ma.dashboardDemo', compact('applications', 'statusApplications'));
    }

    public function dashboardVcApproved()
    {
        $maUserId = self::MA_USER_ID;

        // Get all applications with status_id = 8 (VC Approved) assigned to this MA
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 8) // VC Approved
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status',
                'leave_details.remark'
            )
            ->get();

        return view('ma.vcapproved', compact('applications'));
    }

    public function statusPage()
    {
        $maUserId = self::MA_USER_ID;

        $statusApplications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->whereBetween('leave_details.status_id', [4, 8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                'employees.initials as name_with_initials',
                'leave_types.name as leave_type',
                'leave_details.status_id'
            )
            ->get();
        return view('ma.status', compact('statusApplications'));
    }
} 

