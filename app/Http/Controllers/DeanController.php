<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeanController extends Controller
{
    public function index()
    {
        // Get all applications for Dean review (status_id = 6)
        $applications = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 6) // Processing Dean
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

        return view('dean.index', compact('applications'));
    }

    public function show($id)
    {
        $application = DB::table('leave_details')
            ->join('personal_details', 'leave_details.nic', '=', 'personal_details.nic')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 6)
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
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Decode JSON fields to arrays for multiple files
        $application->leave_documents = $application->leave_document
            ? json_decode($application->leave_document, true)
            : [];
        $application->consent_letters = $application->consent_letter
            ? json_decode($application->consent_letter, true)
            : [];

        return view('dean.show', compact('application'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'dean_recommend' => 'required|boolean',
            'dean_remarks' => 'required_if:dean_recommend,0',
        ]);

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2)
            ->where('status_id', 6)
            ->first();

        if (!$application) {
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Always forward to VC (status_id = 7)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'dean_recommend' => $request->dean_recommend,
                'dean_remarks' => $request->dean_remarks,
                'dean_reviewed_by' => 'Dr. S. Perera', // Or get from auth if available
                'dean_reviewed_at' => now(),
                'dean_name' => 'Dr. S. Perera',
                'dean_designation' => 'Dean FAS',
                'status_id' => 7, // Processing VC
                'updated_at' => now(),
            ]);

        $msg = $request->dean_recommend ? 'Application forwarded to VC with recommendation.' : 'Application forwarded to VC without recommendation.';
        return redirect()->route('dean.index')->with('success', $msg);
    }
} 