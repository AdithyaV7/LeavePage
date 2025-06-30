<?php
namespace App\Http\Controllers;

use App\Models\PersonalDetail;
use App\Models\LeaveDetail;
use App\Models\LeaveType;
use App\Models\Status;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{

    public function index()
    {
        // 1. Get personal detail from empno
        $user = DB::table('personal_details')->where('empno', session('empno'))->first();
        if (!$user)
            abort(404, 'User not found');

        // 2. Get drafts (form_status = 1)
        $drafts = DB::table('leave_details')
            ->where('nic', $user->nic)
            ->where('form_status', 1)
            ->orderByDesc('updated_at')
            ->get();

        // 3. Get previous leaves (form_status = 2 = submitted, 3 = returned)
        // Note: In the future, you could join with form_statuses table to get readable status names
        // Example: ->join('form_statuses', 'leave_details.form_status', '=', 'form_statuses.form_stat_id')
        $previousLeaves = DB::table('leave_details')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.nic', $user->nic)
            ->whereIn('leave_details.form_status', [2, 3])
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'leave_types.name as leave_type',
                'statuses.status as status',
                'leave_details.status_id',
                'leave_details.applied_date',
                'leave_details.form_status',
                'leave_details.remark'
            )
            ->get();

        return view('leaves.index', compact('user', 'drafts', 'previousLeaves'));
    }

    public function destroy($id)
    {
        $user = DB::table('personal_details')->where('empno', session('empno'))->first();
        if (!$user) abort(404);

        // Make sure it's a draft belonging to this user
        $leave = DB::table('leave_details')->where('id', $id)->where('nic', $user->nic)->first();

        if (!$leave || $leave->form_status != 1) {
            return redirect()->route('leaves.index')->with('error', 'Cannot delete this record.');
        }

        DB::table('leave_details')->where('id', $id)->delete();

        return redirect()->route('leaves.index')->with('success', 'Draft deleted successfully.');
    }

    //.................................................................................
    public function create(Request $request)
    {
        $user = DB::table('personal_details')->where('empno', session('empno'))->first();
        if (!$user)
            abort(404, 'User not found');

        $leaveTypes = DB::table('leave_types')->get();
        $statuses = DB::table('statuses')->pluck('status', 'stat_id');

        // Check if we're editing an existing record
        $leave = null;
        $remark = null;
        
        if ($request->has('id')) {
            $leave = DB::table('leave_details')->where('id', $request->id)->where('nic', $user->nic)->first();
            if (!$leave || !in_array($leave->form_status, [1, 3])) {
                return redirect()->route('leaves.index')->with('error', 'You can only edit Drafts or Returned forms.');
            }
            
            // If it's a returned form, get the remark
            if ($leave->form_status == 3) {
                $remark = $leave->remark;
            }
        }

        // Only fetch previous leaves with status_id = 1 (approved) for the current academic year
        $previousLeaves = DB::table('leave_details')
            ->join('leave_types', 'leave_details.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.nic', $user->nic)
            ->where('leave_details.status_id', 1)
            ->whereYear('leave_details.applied_date', now()->year)
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_types.name as leave_type',
                'from_date',
                'to_date',
                'leave_details.duration',
                'statuses.status',
                'leave_details.applied_date'
            )
            ->get();

        return view('create', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'remark'));
    }

    public function store(Request $request)
    {
        $isDraft = $request->form_status == 1;
        $isUpdate = $request->has('leave_id');

        $rules = [
            'form_status' => 'required|in:1,2',
        ];

        if (!$isDraft) {
            // Only require all fields if not a draft
            $rules = array_merge($rules, [
                'leave_type' => 'required|exists:leave_types,id',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'duration' => 'required|integer|min:1',
                'leave_document' => 'required|file|mimes:pdf|max:2048',
                'consent_letter' => 'required|file|mimes:pdf|max:2048',
                'confirm' => 'required',
            ]);
        }

        $validated = $request->validate($rules);

        $user = \App\Models\PersonalDetail::where('empno', session('empno'))->first();
        if (!$user) return back()->with('error', 'User not found.');

        if ($isUpdate) {
            // Update existing record
            $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
                ->where('nic', $user->nic)
                ->whereIn('form_status', [1, 3])
                ->first();
                
            if (!$leave) {
                return back()->with('error', 'Record not found or cannot be updated.');
            }

            // Handle file uploads - preserve existing files if no new ones uploaded
            $leaveDocPath = $leave->leave_document;
            $consentLetterPath = $leave->consent_letter;
            
            if ($request->hasFile('leave_document')) {
                // Delete old file if exists
                if ($leave->leave_document) {
                    Storage::delete($leave->leave_document);
                }
                $leaveDocPath = $request->file('leave_document')->store('uploads', 'public');
            }
            
            if ($request->hasFile('consent_letter')) {
                // Delete old file if exists
                if ($leave->consent_letter) {
                    Storage::delete($leave->consent_letter);
                }
                $consentLetterPath = $request->file('consent_letter')->store('uploads', 'public');
            }

            // Update the record
            $leave->update([
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'duration' => $request->duration,
                'leave_document' => $leaveDocPath,
                'consent_letter' => $consentLetterPath,
                'status_id' => $request->form_status == 1 ? 3 : 4, // 3 for Editing (drafts), 4 for Processing MA (submitted)
                'form_status' => $request->form_status,
                'remark' => null, // Clear remark when resubmitting
            ]);

            return redirect()->route('leaves.index')->with('success', 'Leave ' . ($request->form_status == 2 ? 'submitted' : 'saved as draft') . ' successfully!');
        } else {
            // Create new record
            // Handle file uploads only if present
            $leaveDocPath = $request->hasFile('leave_document') ? $request->file('leave_document')->store('uploads', 'public') : null;
            $consentLetterPath = $request->hasFile('consent_letter') ? $request->file('consent_letter')->store('uploads', 'public') : null;

            // Generate reference number with GMT+5:30 time (manually add 5 hours 30 minutes) including seconds
            $timestamp = now()->addHours(5)->addMinutes(30)->format('YmdHis');
            $refNo = 'OL' . $timestamp . 'E' . $user->empno;

            // Save record
            \App\Models\LeaveDetail::create([
                'nic' => $user->nic,
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'duration' => $request->duration,
                'leave_document' => $leaveDocPath,
                'consent_letter' => $consentLetterPath,
                'status_id' => $request->form_status == 1 ? 3 : 4, // 3 for Editing (drafts), 4 for Processing MA (submitted)
                'form_status' => $request->form_status,
                'reference_no' => $refNo,
                'applied_date' => now()->addHours(5)->addMinutes(30),
            ]);

            return redirect()->route('leaves.index')->with('success', 'Leave ' . ($request->form_status == 2 ? 'submitted' : 'saved as draft') . ' successfully!');
        }
    }

}