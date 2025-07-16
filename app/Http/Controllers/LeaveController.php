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
            $leave = \App\Models\LeaveDetail::where('id', $request->id)->where('nic', $user->nic)->first();
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
            $rules = array_merge($rules, [
                'leave_type' => 'required|exists:leave_types,id',
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
                'duration' => 'required|integer|min:1',
                'confirm' => 'required',
            ]);
            if (!$isUpdate) {
                $rules['leave_document'] = 'required';
                $rules['consent_letter'] = 'required';
            }
        }

        $validated = $request->validate($rules);

        $user = \App\Models\PersonalDetail::where('empno', session('empno'))->first();
        if (!$user) return back()->with('error', 'User not found.');

        if ($isUpdate) {
            $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
                ->where('nic', $user->nic)
                ->whereIn('form_status', [1, 3])
                ->first();
            if (!$leave) {
                return back()->with('error', 'Record not found or cannot be updated.');
            }
            // For submit, ensure at least one file exists in DB for each field
            $leaveDocPaths = is_array($leave->leave_document) ? $leave->leave_document : [];
            $consentLetterPaths = is_array($leave->consent_letter) ? $leave->consent_letter : [];
            // Handle new uploads
            if ($request->hasFile('leave_document')) {
                foreach ($request->file('leave_document') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads', $filename, 'public');
                    $leaveDocPaths[] = $path;
                }
            }
            if ($request->hasFile('consent_letter')) {
                foreach ($request->file('consent_letter') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads', $filename, 'public');
                    $consentLetterPaths[] = $path;
                }
            }
            if (!$isDraft) {
                if (count($leaveDocPaths) == 0) {
                    return back()->with('error', 'At least one leave document is required.');
                }
                if (count($consentLetterPaths) == 0) {
                    return back()->with('error', 'At least one consent letter is required.');
                }
            }
            $leave->update([
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'duration' => $request->duration,
                'leave_document' => $leaveDocPaths,
                'consent_letter' => $consentLetterPaths,
                'status_id' => $request->form_status == 1 ? 3 : 4,
                'form_status' => $request->form_status,
                // Preserve existing remarks instead of setting to null
            ]);
            return redirect()->route('leaves.index')->with('success', 'Leave ' . ($request->form_status == 2 ? 'submitted' : 'saved as draft') . ' successfully!');
        } else {
            // Handle multiple file uploads for new applications
            $leaveDocPaths = [];
            if ($request->hasFile('leave_document')) {
                foreach ($request->file('leave_document') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads', $filename, 'public');
                    $leaveDocPaths[] = $path;
                }
            }
            $consentLetterPaths = [];
            if ($request->hasFile('consent_letter')) {
                foreach ($request->file('consent_letter') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads', $filename, 'public');
                    $consentLetterPaths[] = $path;
                }
            }
            if (!$isDraft) {
                if (count($leaveDocPaths) == 0) {
                    return back()->with('error', 'At least one leave document is required.');
                }
                if (count($consentLetterPaths) == 0) {
                    return back()->with('error', 'At least one consent letter is required.');
                }
            }
            $timestamp = now()->addHours(5)->addMinutes(30)->format('YmdHis');
            $refNo = 'OL' . $timestamp . 'E' . $user->empno;
            \App\Models\LeaveDetail::create([
                'nic' => $user->nic,
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'duration' => $request->duration,
                'leave_document' => $leaveDocPaths,
                'consent_letter' => $consentLetterPaths,
                'status_id' => $request->form_status == 1 ? 3 : 4,
                'form_status' => $request->form_status,
                'reference_no' => $refNo,
                'applied_date' => now()->addHours(5)->addMinutes(30),
            ]);
            return redirect()->route('leaves.index')->with('success', 'Leave ' . ($request->form_status == 2 ? 'submitted' : 'saved as draft') . ' successfully!');
        }
    }

    // AJAX: Upload file for leave_document or consent_letter
    public function uploadFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave_document,consent_letter',
            'file' => 'required|file|mimes:pdf|max:2048',
            'leave_id' => 'required|integer|exists:leave_details,id',
        ]);

        $user = \App\Models\PersonalDetail::where('empno', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
            ->where('nic', $user->nic)
            ->whereIn('form_status', [1, 3])
            ->first();
        if (!$leave) return response()->json(['error' => 'Record not found or cannot be updated.'], 404);

        $type = $request->type;
        $files = $leave->$type ?? [];
        if (!is_array($files)) $files = [];

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $date = now()->format('Ymd_His');
        $filename = $originalName . '_' . $date . '.' . $ext;
        $path = $file->storeAs('uploads', $filename, 'public');

        // If file with same original name exists, replace it
        $files = array_filter($files, function($f) use ($originalName) {
            return strpos($f, $originalName . '_') !== 0;
        });
        $files[] = $path;
        $leave->$type = array_values($files);
        $leave->save();

        return response()->json(['success' => true, 'files' => $leave->$type]);
    }

    // AJAX: Delete file for leave_document or consent_letter
    public function deleteFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave_document,consent_letter',
            'file' => 'required|string',
            'leave_id' => 'required|integer|exists:leave_details,id',
        ]);

        $user = \App\Models\PersonalDetail::where('empno', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
            ->where('nic', $user->nic)
            ->whereIn('form_status', [1, 3])
            ->first();
        if (!$leave) return response()->json(['error' => 'Record not found or cannot be updated.'], 404);

        $type = $request->type;
        $files = $leave->$type ?? [];
        if (!is_array($files)) $files = [];

        $files = array_filter($files, function($f) use ($request) {
            return $f !== $request->file;
        });
        // Delete file from storage
        \Storage::disk('public')->delete($request->file);
        $leave->$type = array_values($files);
        $leave->save();

        return response()->json(['success' => true, 'files' => $leave->$type]);
    }

    /**
     * Create a new draft leave application and redirect to the create form.
     */
    public function createDraft(Request $request)
    {
        $user = DB::table('personal_details')->where('empno', session('empno'))->first();
        if (!$user) abort(404, 'User not found');

        // Create a new draft leave record
        $draft = DB::table('leave_details')->insertGetId([
            'nic' => $user->nic,
            'reference_no' => 'REF-' . strtoupper(uniqid()),
            'form_status' => 1, // Draft
            'status_id' => 3, // Editing or Draft status
            'applied_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect to the create form with the draft's ID
        return redirect()->route('leaves.create', ['id' => $draft]);
    }

}