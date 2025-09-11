<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeanController extends Controller
{
    // Hardcoded Dean employee number - change this to switch to a different Dean
    private const DEAN_EMP_NO = 5045; // Dean for faculty 1 (has leave applications)

    /**
     * Get faculty IDs for the current Dean
     * Returns array of faculty IDs or shows error page if Dean not found
     */
    private function getDeanFaculties()
    {
        $deanEmpNo = self::DEAN_EMP_NO;

        // Check if this employee is a valid Dean in faculty_deans table
        $faculties = DB::table('faculty_deans')
            ->where('emp_no', $deanEmpNo)
            ->where('active_status', 1) // Only active appointments
            ->whereRaw('(end_date IS NULL OR end_date >= CURDATE())') // Current or future end date
            ->pluck('faculty_id')
            ->toArray();

        if (empty($faculties)) {
            // Dean not found in faculty_deans table - show error
            abort(403, 'Access denied. Employee ' . $deanEmpNo . ' is not authorized as a Dean.');
        }

        return $faculties;
    }

    public function index()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Get all applications for Dean review (status_id = 6) from assigned faculties
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

        return view('dean.index', compact('applications'));
    }

    public function show($id)
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select(
                'leave_details.*',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
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
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Merge other leave details into application object
        if ($otherLeaveDetails) {
            $application->leave_type_id = $otherLeaveDetails->leave_type_id;
            $application->from_date = $otherLeaveDetails->from_date;
            $application->end_date = $otherLeaveDetails->end_date;
            $application->duration = $otherLeaveDetails->duration;
            $application->leave_document = $otherLeaveDetails->leave_document;
            $application->consent_letter = $otherLeaveDetails->consent_letter;
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

        return view('dean.show', compact('application', 'travelDetails'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'dean_recommend' => 'required|boolean',
            'dean_remarks' => 'required_if:dean_recommend,0',
        ]);

        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Always forward to VC (status_id = 7)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'dean_recommend' => $request->dean_recommend,
                'dean_remarks' => $request->dean_remarks,
                'dean_reviewed_by' => 'Dean', // Or get from auth if available
                'dean_reviewed_at' => now(),
                'dean_name' => 'Dean',
                'dean_designation' => 'Dean',
                'dean_empno' => self::DEAN_EMP_NO, // Record which Dean processed this
                'status_id' => 7, // Processing VC
                'updated_at' => now(),
            ]);

        $msg = $request->dean_recommend ? 'Application forwarded to VC with recommendation.' : 'Application forwarded to VC without recommendation.';
        return redirect()->route('dean.index')->with('success', $msg);
    }
} 