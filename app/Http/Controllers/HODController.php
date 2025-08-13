<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HODController extends Controller
{
    // Hardcoded HOD employee number - change this to switch to a different HOD
    private const HOD_EMP_NO = 5305; // HOD for department 114 (has leave applications)

    /**
     * Get department IDs for the current HOD
     * Returns array of department IDs or shows error page if HOD not found
     */
    private function getHodDepartments()
    {
        $hodEmpNo = self::HOD_EMP_NO;

        // Check if this employee is a valid HOD in department_heads table
        $departments = DB::table('department_heads')
            ->where('emp_no', $hodEmpNo)
            ->where('active_status', 1) // Only active appointments
            ->whereRaw('(end_date IS NULL OR end_date >= CURDATE())') // Current or future end date
            ->pluck('department_id')
            ->toArray();

        if (empty($departments)) {
            // HOD not found in department_heads table - show error
            abort(403, 'Access denied. Employee ' . $hodEmpNo . ' is not authorized as a Head of Department.');
        }

        return $departments;
    }

    public function index()
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Get all applications for HOD review (status_id = 5) from assigned departments
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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

        return view('hod.index', compact('applications'));
    }

    public function show($id)
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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
            return redirect()->route('hod.index')->with('error', 'Application not found.');
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

        return view('hod.show', compact('application', 'travelDetails'));
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

        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('hod.index')->with('error', 'Application not found.');
        }

        // If recommended or not, always forward to Dean (status_id = 6)
        $status_id = 6;

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
                // New HOD fields
                'hod_name' => 'O. Wickramasinghe',
                'hod_recommendation' => $request->hod_recommend,
                'hod_forwarded_date' => now(),
                'hod_designation' => 'Head of Department', // Can be made configurable in future
                'hod_empno' => self::HOD_EMP_NO, // Record which HOD processed this
                'status_id' => $status_id,
                'updated_at' => now(),
            ]);

        $msg = $request->hod_recommend ? 'Application forwarded to Dean.' : 'Application not recommended.';
        return redirect()->route('hod.index')->with('success', $msg);
    }

    
} 