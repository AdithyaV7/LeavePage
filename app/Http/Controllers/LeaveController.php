<?php
namespace App\Http\Controllers;


use App\Models\LeaveDetail;
use App\Models\LeaveRequestDetail;
use App\Models\LeaveType;
use App\Models\Status;
use App\Models\OtherLeavesDetail;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Generate a new reference number following the pattern: <empNo><year><04><No>
     */
    private function generateReferenceNumber($empNo)
    {
        $currentYear = date('Y');
        
        // Get the highest ID from leave_details table and add 1
        $lastId = DB::table('leave_details')->max('id') ?? 0;
        $newNo = $lastId + 1;

        // Format: <empNo><year><04><No>
        return "{$empNo}{$currentYear}04{$newNo}";
    }

    public function index()
    {
        // 1. Get employee detail from empno
        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user)
            abort(404, 'User not found');

        // 2. Get drafts (form_status = 1)
        $drafts = DB::table('leave_details')
            ->where('nic', $user->nic)
            ->where('form_status', 1)
            ->orderByDesc('updated_at')
            ->get();

        // Check if user has an active draft (form_status = 1, status_id = 3)
        $hasActiveDraft = DB::table('leave_details')
            ->where('nic', $user->nic)
            ->where('form_status', 1)
            ->where('status_id', 3)
            ->exists();

        // 3. Get previous leaves (form_status = 2 = submitted, 3 = returned)
        // Note: In the future, you could join with form_statuses table to get readable status names
        // Example: ->join('form_statuses', 'leave_details.form_status', '=', 'form_statuses.form_stat_id')
        $previousLeaves = DB::table('leave_details')
            ->join('otherleavesdetails', 'leave_details.reference_no', '=', 'otherleavesdetails.reference_no')
            ->join('leave_types', 'otherleavesdetails.leave_type_id', '=', 'leave_types.id')
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

        return view('leaves.index', compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft'));
    }

    public function destroy($id)
    {
        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) abort(404);

        // Make sure it's a draft belonging to this user
        $leave = DB::table('leave_details')->where('id', $id)->where('nic', $user->nic)->first();

        if (!$leave || $leave->form_status != 1) {
            return redirect()->route('leaves.index')->with('error', 'Cannot delete this record.');
        }

        $referenceNo = $leave->reference_no;

        // Delete from all three tables
        DB::beginTransaction();
        try {
            // Delete from leave_request_details table
            DB::table('leave_request_details')->where('reference_no', $referenceNo)->delete();
            
            // Delete from otherleavesdetails table
            DB::table('otherleavesdetails')->where('reference_no', $referenceNo)->delete();
            
            // Delete from leave_details table
            DB::table('leave_details')->where('id', $id)->delete();
            
            DB::commit();
            
            return redirect()->route('leaves.index')->with('success', 'Draft deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('leaves.index')->with('error', 'Error deleting record: ' . $e->getMessage());
        }
    }

    //.................................................................................
    public function create(Request $request)
    {
        // Get employee data from employees table with related information
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', session('empno'))
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');

        $leaveTypes = DB::table('leave_types')->get();
        $statuses = DB::table('statuses')->pluck('status', 'stat_id');

        // Check if we're editing an existing record
        $leave = null;
        $otherLeave = null;
        $remark = null;

        if ($request->has('id')) {
            $leave = \App\Models\LeaveDetail::where('id', $request->id)->where('nic', $user->nic)->first();
            if (!$leave || !in_array($leave->form_status, [1, 3])) {
                return redirect()->route('leaves.index')->with('error', 'You can only edit Drafts or Returned forms.');
            }

            // Get the otherleavesdetails record for this reference
            $otherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();

            // If it's a returned form, get the remark
            if ($leave->form_status == 3) {
                $remark = $leave->remark;
            }
        }
        // Note: For new applications, $leave and $otherLeave will be null and the form will work without a database record

        // Only fetch previous leaves with status_id = 1 (approved) for the current academic year
        $previousLeaves = DB::table('leave_details')
            ->join('otherleavesdetails', 'leave_details.reference_no', '=', 'otherleavesdetails.reference_no')
            ->join('leave_types', 'otherleavesdetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.nic', $user->nic)
            ->where('leave_details.status_id', 1)
            ->whereYear('leave_details.applied_date', now()->year)
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_types.name as leave_type',
                'otherleavesdetails.from_date',
                'otherleavesdetails.end_date as to_date',
                'otherleavesdetails.duration',
                'statuses.status',
                'leave_details.applied_date'
            )
            ->get();

        // Load existing travel details if editing
        $travelDetails = [];
        if ($leave) {
            $travelDetails = LeaveRequestDetail::where('reference_no', $leave->reference_no)->get();
        }

        return view('create', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'otherLeave', 'remark', 'travelDetails'));
    }

    public function show($id)
    {
        // Get employee data from employees table with related information
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', session('empno'))
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');

        $leaveTypes = DB::table('leave_types')->get();
        $statuses = DB::table('statuses')->pluck('status', 'stat_id');

        // Get the leave record for viewing (only submitted or approved applications)
        $leave = \App\Models\LeaveDetail::with(['leaveType', 'status'])
            ->where('id', $id)
            ->where('nic', $user->nic)
            ->first();
        if (!$leave || !in_array($leave->form_status, [2, 4, 5, 6, 7])) {
            return redirect()->route('leaves.index')->with('error', 'Application not found or cannot be viewed.');
        }

        // Get the otherleavesdetails record for this reference
        $otherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();

        // Get the remark if it exists
        $remark = $leave->remark;

        // Only fetch previous leaves with status_id = 1 (approved) for the current academic year
        $previousLeaves = DB::table('leave_details')
            ->join('otherleavesdetails', 'leave_details.reference_no', '=', 'otherleavesdetails.reference_no')
            ->join('leave_types', 'otherleavesdetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.nic', $user->nic)
            ->where('leave_details.status_id', 1)
            ->whereYear('leave_details.applied_date', now()->year)
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_types.name as leave_type',
                'otherleavesdetails.from_date',
                'otherleavesdetails.end_date as to_date',
                'otherleavesdetails.duration',
                'statuses.status',
                'leave_details.applied_date'
            )
            ->get();

        // Load existing travel details
        $travelDetails = [];
        if ($leave) {
            $travelDetails = LeaveRequestDetail::where('reference_no', $leave->reference_no)->get();
        }

        return view('leaves.show', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'otherLeave', 'remark', 'travelDetails'));
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
        }

        $validated = $request->validate($rules);

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return back()->with('error', 'User not found.');

        if ($isUpdate) {
            $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
                ->where('nic', $user->nic)
                ->whereIn('form_status', [1, 3])
                ->first();
            if (!$leave) {
                return back()->with('error', 'Record not found or cannot be updated.');
            }

            // Handle file uploads for otherleavesdetails
            $leaveDocPaths = [];
            $consentLetterPaths = [];

            // Handle direct file uploads
            if ($request->hasFile('leave_document')) {
                foreach ($request->file('leave_document') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                    $leaveDocPaths[] = $path;
                }
            }

            if ($request->hasFile('consent_letter')) {
                foreach ($request->file('consent_letter') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                    $consentLetterPaths[] = $path;
                }
            }

            // Handle temporary file paths from hidden inputs
            if ($request->has('temp_leave_documents')) {
                $tempPaths = json_decode($request->temp_leave_documents, true);
                if (is_array($tempPaths)) {
                    foreach ($tempPaths as $tempPath) {
                        if (Storage::disk('public')->exists($tempPath)) {
                            $filename = basename($tempPath);
                            $newPath = 'uploads/other_leaves/' . $filename;
                            Storage::disk('public')->move($tempPath, $newPath);
                            $leaveDocPaths[] = $newPath;
                        }
                    }
                }
            }

            if ($request->has('temp_consent_letters')) {
                $tempPaths = json_decode($request->temp_consent_letters, true);
                if (is_array($tempPaths)) {
                    foreach ($tempPaths as $tempPath) {
                        if (Storage::disk('public')->exists($tempPath)) {
                            $filename = basename($tempPath);
                            $newPath = 'uploads/other_leaves/' . $filename;
                            Storage::disk('public')->move($tempPath, $newPath);
                            $consentLetterPaths[] = $newPath;
                        }
                    }
                }
            }

            if (!$isDraft) {
                if (count($consentLetterPaths) == 0) {
                    return back()->with('error', 'At least one consent letter is required.');
                }
            }

            // Update or create otherleavesdetails record
            $otherLeaveData = [
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'end_date' => $request->to_date, // Note: to_date becomes end_date
                'duration' => $request->duration,
                'leave_document' => $leaveDocPaths,
                'consent_letter' => $consentLetterPaths,
            ];

            $existingOtherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();
            if ($existingOtherLeave) {
                $existingOtherLeave->update($otherLeaveData);
            } else {
                $otherLeaveData['reference_no'] = $leave->reference_no;
                OtherLeavesDetail::create($otherLeaveData);
            }

            // Update leave_details table (only status and form_status)
            $leave->update([
                'status_id' => $request->form_status == 1 ? 3 : 4,
                'form_status' => $request->form_status,
                'department_id' => $user->department_id,
                'faculty_id' => $user->faculty_id,
            ]);

            // Handle travel details
            $this->saveTravelDetails($request, $leave->reference_no);

            return redirect()->route('leaves.index')->with('success', 'Leave ' . ($request->form_status == 2 ? 'submitted' : 'saved as draft') . ' successfully!');
        } else {
            // Handle file uploads for new applications
            $leaveDocPaths = [];
            $consentLetterPaths = [];

            // Handle direct file uploads
            if ($request->hasFile('leave_document')) {
                foreach ($request->file('leave_document') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                    $leaveDocPaths[] = $path;
                }
            }

            if ($request->hasFile('consent_letter')) {
                foreach ($request->file('consent_letter') as $file) {
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                    $consentLetterPaths[] = $path;
                }
            }

            // Handle temporary file paths from hidden inputs
            if ($request->has('temp_leave_documents')) {
                $tempPaths = json_decode($request->temp_leave_documents, true);
                if (is_array($tempPaths)) {
                    foreach ($tempPaths as $tempPath) {
                        if (Storage::disk('public')->exists($tempPath)) {
                            $filename = basename($tempPath);
                            $newPath = 'uploads/other_leaves/' . $filename;
                            Storage::disk('public')->move($tempPath, $newPath);
                            $leaveDocPaths[] = $newPath;
                        }
                    }
                }
            }

            if ($request->has('temp_consent_letters')) {
                $tempPaths = json_decode($request->temp_consent_letters, true);
                if (is_array($tempPaths)) {
                    foreach ($tempPaths as $tempPath) {
                        if (Storage::disk('public')->exists($tempPath)) {
                            $filename = basename($tempPath);
                            $newPath = 'uploads/other_leaves/' . $filename;
                            Storage::disk('public')->move($tempPath, $newPath);
                            $consentLetterPaths[] = $newPath;
                        }
                    }
                }
            }

            if (!$isDraft) {
                if (count($consentLetterPaths) == 0) {
                    return back()->with('error', 'At least one consent letter is required.');
                }
            }

            // Generate new reference number
            $refNo = $this->generateReferenceNumber($user->employee_no);

            // Create leave_details record (minimal data)
            \App\Models\LeaveDetail::create([
                'empno' => $user->employee_no,
                'nic' => $user->nic,
                'status_id' => $request->form_status == 1 ? 3 : 4,
                'form_status' => $request->form_status,
                'reference_no' => $refNo,
                'applied_date' => now()->addHours(5)->addMinutes(30),
                'department_id' => $user->department_id,
                'faculty_id' => $user->faculty_id,
            ]);

            // Create otherleavesdetails record
            OtherLeavesDetail::create([
                'reference_no' => $refNo,
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'end_date' => $request->to_date, // Note: to_date becomes end_date
                'duration' => $request->duration,
                'leave_document' => $leaveDocPaths,
                'consent_letter' => $consentLetterPaths,
            ]);

            // Handle travel details
            $this->saveTravelDetails($request, $refNo);

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

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
            ->where('nic', $user->nic)
            ->whereIn('form_status', [1, 3])
            ->first();
        if (!$leave) return response()->json(['error' => 'Record not found or cannot be updated.'], 404);

        // Get or create otherleavesdetails record
        $otherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();
        if (!$otherLeave) {
            $otherLeave = OtherLeavesDetail::create([
                'reference_no' => $leave->reference_no,
                'leave_type_id' => null,
                'from_date' => null,
                'end_date' => null,
                'duration' => null,
                'leave_document' => [],
                'consent_letter' => [],
            ]);
        }

        $type = $request->type;
        $files = $otherLeave->$type ?? [];
        if (!is_array($files)) $files = [];

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $date = now()->format('Ymd_His');
        $filename = $originalName . '_' . $date . '.' . $ext;
        $path = $file->storeAs('uploads/other_leaves', $filename, 'public');

        // If file with same original name exists, replace it
        $files = array_filter($files, function($f) use ($originalName) {
            return strpos($f, $originalName . '_') !== 0;
        });
        $files[] = $path;
        $otherLeave->$type = array_values($files);
        $otherLeave->save();

        return response()->json(['success' => true, 'files' => $otherLeave->$type]);
    }

    // AJAX: Delete file for leave_document or consent_letter
    public function deleteFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave_document,consent_letter',
            'file' => 'required|string',
            'leave_id' => 'required|integer|exists:leave_details,id',
        ]);

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        $leave = \App\Models\LeaveDetail::where('id', $request->leave_id)
            ->where('nic', $user->nic)
            ->whereIn('form_status', [1, 3])
            ->first();
        if (!$leave) return response()->json(['error' => 'Record not found or cannot be updated.'], 404);

        // Get otherleavesdetails record
        $otherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();
        if (!$otherLeave) return response()->json(['error' => 'Other leave details not found.'], 404);

        $type = $request->type;
        $files = $otherLeave->$type ?? [];
        if (!is_array($files)) $files = [];

        $files = array_filter($files, function($f) use ($request) {
            return $f !== $request->file;
        });
        // Delete file from storage
        \Storage::disk('public')->delete($request->file);
        $otherLeave->$type = array_values($files);
        $otherLeave->save();

        return response()->json(['success' => true, 'files' => $otherLeave->$type]);
    }

    /**
     * Create a new draft leave application and redirect to the create form.
     */
    public function createDraft(Request $request)
    {
        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) abort(404, 'User not found');

        // Check if user already has an active draft (form_status = 1, status_id = 3)
        $existingDraft = DB::table('leave_details')
            ->where('nic', $user->nic)
            ->where('form_status', 1)
            ->where('status_id', 3)
            ->first();

        if ($existingDraft) {
            // Redirect to the existing draft instead of creating a new one
            return redirect()->route('leaves.create', ['id' => $existingDraft->id])
                ->with('info', 'You already have an active draft. Please complete or delete it before starting a new application.');
        }

        // Create a new draft leave record
        $draft = DB::table('leave_details')->insertGetId([
            'empno' => $user->employee_no,
            'nic' => $user->nic,
            'reference_no' => 'REF-' . strtoupper(uniqid()),
            'form_status' => 1, // Draft
            'status_id' => 3, // Editing or Draft status
            'applied_date' => now(),
            'department_id' => $user->department_id,
            'faculty_id' => $user->faculty_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect to the create form with the draft's ID
        return redirect()->route('leaves.create', ['id' => $draft]);
    }

    // AJAX: Upload travel document
    public function uploadTravelDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'detail' => 'required|string',
            'country' => 'required|string',
            'travel_from_date' => 'required|date',
            'travel_to_date' => 'required|date|after_or_equal:travel_from_date',
            'index' => 'required|integer',
            'reference_no' => 'nullable|string',
        ]);

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        // If we have a reference_no, we're updating an existing leave
        $referenceNo = $request->reference_no;
        if (!$referenceNo) {
            // For new applications, we need to create a temporary reference number
            // This will be updated when the main form is saved
            $timestamp = now()->addHours(5)->addMinutes(30)->format('YmdHis');
            $referenceNo = 'TEMP_' . $timestamp . '_' . $user->employee_no;
        }

        // Upload the file
        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $date = now()->format('Ymd_His');
        $filename = $originalName . '_' . $date . '.' . $ext;
        $path = $file->storeAs('uploads/travel_documents', $filename, 'public');

        // Check if a travel detail already exists for this reference and index
        $travelDetail = \App\Models\LeaveRequestDetail::where('reference_no', $referenceNo)
            ->where('detail', $request->detail)
            ->where('country', $request->country)
            ->where('travel_from_date', $request->travel_from_date)
            ->where('travel_to_date', $request->travel_to_date)
            ->first();

        if ($travelDetail) {
            // Update existing detail
            $documents = $travelDetail->documents ?? [];

            // Remove any existing file with the same original name
            $documents = array_filter($documents, function($doc) use ($originalName) {
                return strpos($doc, $originalName . '_') === false;
            });

            $documents[] = $path;
            $travelDetail->documents = array_values($documents);
            $travelDetail->save();
        } else {
            // Create new travel detail
            $travelDetail = \App\Models\LeaveRequestDetail::create([
                'reference_no' => $referenceNo,
                'detail' => $request->detail,
                'country' => $request->country,
                'travel_from_date' => $request->travel_from_date,
                'travel_to_date' => $request->travel_to_date,
                'documents' => [$path],
            ]);
        }

        return response()->json([
            'success' => true,
            'documents' => $travelDetail->documents,
            'detail_id' => $travelDetail->id
        ]);
    }

    // AJAX: Remove travel document
    public function removeTravelDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|string',
            'index' => 'required|integer',
            'detail_id' => 'nullable|integer',
        ]);

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        if ($request->detail_id) {
            $travelDetail = \App\Models\LeaveRequestDetail::find($request->detail_id);
            if (!$travelDetail) {
                return response()->json(['error' => 'Travel detail not found.'], 404);
            }

            $documents = $travelDetail->documents ?? [];
            $documents = array_filter($documents, function($doc) use ($request) {
                return $doc !== $request->file;
            });

            // Delete file from storage
            \Storage::disk('public')->delete($request->file);

            $travelDetail->documents = array_values($documents);
            $travelDetail->save();

            return response()->json([
                'success' => true,
                'documents' => $travelDetail->documents
            ]);
        }

        return response()->json(['error' => 'No detail ID provided.'], 400);
    }

    // Helper method to save travel details from form submission
    private function saveTravelDetails(Request $request, $referenceNo)
    {
        // Handle temporary travel details from frontend
        if ($request->has('temp_travel_details')) {
            $tempTravelDetails = json_decode($request->temp_travel_details, true);

            if (is_array($tempTravelDetails)) {
                foreach ($tempTravelDetails as $detail) {
                    $finalDocuments = [];

                    // Handle document paths - move from temp to permanent location
                    if (isset($detail['documents']) && is_array($detail['documents'])) {
                        foreach ($detail['documents'] as $tempPath) {
                            if (Storage::disk('public')->exists($tempPath)) {
                                // Move from temp to permanent location
                                $filename = basename($tempPath);
                                $newPath = 'uploads/travel_documents/' . $filename;

                                // Ensure the directory exists
                                Storage::disk('public')->makeDirectory('uploads/travel_documents');

                                // Move the file
                                Storage::disk('public')->move($tempPath, $newPath);
                                $finalDocuments[] = $newPath;
                            }
                        }
                    }

                    // Save travel detail to database
                    LeaveRequestDetail::create([
                        'reference_no' => $referenceNo,
                        'detail' => $detail['detail'],
                        'country' => $detail['country'],
                        'travel_from_date' => $detail['travel_from_date'],
                        'travel_to_date' => $detail['travel_to_date'],
                        'documents' => $finalDocuments,
                    ]);
                }
            }
        }
    }

    // Helper method to save data to otherLeavesDetails table
    private function saveToOtherLeavesDetails(Request $request, $referenceNo)
    {
        try {
            Log::info('Saving to otherLeavesDetails table for reference: ' . $referenceNo);
            
            // Get the leave_details record to access existing files
            $leaveDetail = \App\Models\LeaveDetail::where('reference_no', $referenceNo)->first();
            
            if (!$leaveDetail) {
                Log::error('LeaveDetail record not found for reference: ' . $referenceNo);
                return;
            }
            
            // Check if record already exists for this reference number
            $existingRecord = OtherLeavesDetail::where('reference_no', $referenceNo)->first();
            
            // Get existing files from leave_details table and copy them to other_leaves directory
            $existingLeaveDocs = is_array($leaveDetail->leave_document) ? $leaveDetail->leave_document : [];
            $existingConsentLetters = is_array($leaveDetail->consent_letter) ? $leaveDetail->consent_letter : [];
            
            // Copy existing files to other_leaves directory
            $copiedLeaveDocs = $this->copyFilesToOtherLeaves($existingLeaveDocs);
            $copiedConsentLetters = $this->copyFilesToOtherLeaves($existingConsentLetters);
            
            // Get new files from request
            $newLeaveDocs = $this->getFilePaths($request, 'leave_document');
            $newConsentLetters = $this->getFilePaths($request, 'consent_letter');
            
            // Combine copied existing files and new files
            $allLeaveDocs = array_merge($copiedLeaveDocs, $newLeaveDocs);
            $allConsentLetters = array_merge($copiedConsentLetters, $newConsentLetters);
            
            $data = [
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'end_date' => $request->to_date, // Note: form uses 'to_date' but table uses 'end_date'
                'duration' => $request->duration,
                'leave_document' => $allLeaveDocs,
                'consent_letter' => $allConsentLetters,
            ];
            
            Log::info('Existing leave documents from leave_details: ' . json_encode($existingLeaveDocs));
            Log::info('Existing consent letters from leave_details: ' . json_encode($existingConsentLetters));
            Log::info('Copied leave documents: ' . json_encode($copiedLeaveDocs));
            Log::info('Copied consent letters: ' . json_encode($copiedConsentLetters));
            Log::info('New leave documents from request: ' . json_encode($newLeaveDocs));
            Log::info('New consent letters from request: ' . json_encode($newConsentLetters));
            Log::info('Final leave documents: ' . json_encode($allLeaveDocs));
            Log::info('Final consent letters: ' . json_encode($allConsentLetters));
            
            if ($existingRecord) {
                // Update existing record
                $existingRecord->update($data);
                Log::info('Updated existing otherLeavesDetails record for reference: ' . $referenceNo);
            } else {
                // Create new record
                $data['reference_no'] = $referenceNo;
                OtherLeavesDetail::create($data);
                Log::info('Created new otherLeavesDetails record for reference: ' . $referenceNo);
            }
        } catch (\Exception $e) {
            // Log the error but don't break the main flow
            Log::error('Error saving to otherLeavesDetails: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    // Helper method to get file paths from request
    private function getFilePaths(Request $request, $fieldName)
    {
        $filePaths = [];

        // Handle direct file uploads
        if ($request->hasFile($fieldName)) {
            foreach ($request->file($fieldName) as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $date = now()->format('Ymd_His');
                $filename = $originalName . '_' . $date . '.' . $ext;
                $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                $filePaths[] = $path;
            }
        }

        // Handle temporary file paths from hidden inputs
        if ($request->has('temp_' . $fieldName . 's')) {
            $tempPaths = json_decode($request->input('temp_' . $fieldName . 's'), true);
            if (is_array($tempPaths)) {
                foreach ($tempPaths as $tempPath) {
                    if (Storage::disk('public')->exists($tempPath)) {
                        // Move from temp to permanent location
                        $filename = basename($tempPath);
                        $newPath = 'uploads/other_leaves/' . $filename;
                        Storage::disk('public')->move($tempPath, $newPath);
                        $filePaths[] = $newPath;
                    }
                }
            }
        }

        return $filePaths;
    }

    // Helper method to copy files from uploads to uploads/other_leaves
    private function copyFilesToOtherLeaves($filePaths)
    {
        $copiedPaths = [];
        
        foreach ($filePaths as $filePath) {
            if (Storage::disk('public')->exists($filePath)) {
                $filename = basename($filePath);
                $newPath = 'uploads/other_leaves/' . $filename;
                
                // Copy the file to other_leaves directory
                Storage::disk('public')->copy($filePath, $newPath);
                $copiedPaths[] = $newPath;
            }
        }
        
        return $copiedPaths;
    }

    // AJAX method to save individual travel detail
    public function saveTravelDetail(Request $request)
    {
        try {
            $request->validate([
                'reference_no' => 'required|string',
                'detail' => 'required|string',
                'country' => 'required|string',
                'travel_from_date' => 'required|date',
                'travel_to_date' => 'required|date',
                'documents' => 'required|array|min:1',
                'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            ]);

            // Handle document uploads
            $uploadedDocuments = [];
            \Log::info('Checking for uploaded files...'); // Debug log
            \Log::info('Request has files: ' . ($request->hasFile('documents') ? 'Yes' : 'No')); // Debug log

            if ($request->hasFile('documents')) {
                \Log::info('Number of files: ' . count($request->file('documents'))); // Debug log
                foreach ($request->file('documents') as $file) {
                    \Log::info('Processing file: ' . $file->getClientOriginalName()); // Debug log
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = $file->getClientOriginalExtension();
                    $date = now()->format('Ymd_His');
                    $filename = $originalName . '_' . $date . '.' . $ext;
                    $path = $file->storeAs('uploads/travel_documents', $filename, 'public');
                    $uploadedDocuments[] = $path;
                    \Log::info('File saved to: ' . $path); // Debug log
                }
            } else {
                \Log::info('No files found in request'); // Debug log
            }

            // Create travel detail record
            $travelDetail = \App\Models\LeaveRequestDetail::create([
                'reference_no' => $request->reference_no,
                'detail' => $request->detail,
                'country' => $request->country,
                'travel_from_date' => $request->travel_from_date,
                'travel_to_date' => $request->travel_to_date,
                'documents' => $uploadedDocuments,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Travel detail saved successfully',
                'travel_detail' => $travelDetail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving travel detail: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX method to delete travel detail
    public function deleteTravelDetail($id)
    {
        try {
            Log::info('Delete travel detail request for ID: ' . $id); // Debug log
            $travelDetail = \App\Models\LeaveRequestDetail::find($id);
            Log::info('Travel detail found: ' . ($travelDetail ? 'Yes' : 'No')); // Debug log

            if (!$travelDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Travel detail not found'
                ], 404);
            }

            // Delete associated documents from storage
            if ($travelDetail->documents) {
                Log::info('Deleting documents: ' . json_encode($travelDetail->documents)); // Debug log
                foreach ($travelDetail->documents as $doc) {
                    if (Storage::disk('public')->exists($doc)) {
                        Storage::disk('public')->delete($doc);
                        Log::info('Deleted file: ' . $doc); // Debug log
                    }
                }
            }

            $travelDetail->delete();
            Log::info('Travel detail deleted successfully'); // Debug log

            return response()->json([
                'success' => true,
                'message' => 'Travel detail deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting travel detail: ' . $e->getMessage()
            ], 500);
        }
    }

    // AJAX: Upload temporary file for leave_document, consent_letter, or travel_document (without requiring leave_id)
    public function uploadTempFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave_document,consent_letter,travel_document',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
        if (!$user) return response()->json(['error' => 'User not found.'], 403);

        // Upload the file to temporary storage (no session storage)
        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $date = now()->format('Ymd_His');
        $filename = $originalName . '_' . $date . '.' . $ext;

        // Use different temp directories based on file type
        $tempDir = $request->type === 'travel_document' ? 'uploads/temp/travel_documents' : 'uploads/temp';
        $path = $file->storeAs($tempDir, $filename, 'public');

        return response()->json([
            'success' => true,
            'file_path' => $path,
            'file_name' => basename($path),
            'message' => 'File uploaded successfully'
        ]);
    }

    // AJAX: Delete temporary file
    public function deleteTempFile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:leave_document,consent_letter,travel_document',
            'file_path' => 'required|string',
        ]);

        // Delete physical file only (no session management)
        if (Storage::disk('public')->exists($request->file_path)) {
            Storage::disk('public')->delete($request->file_path);
        }

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully'
        ]);
    }

    // Test method for debugging file uploads
    public function testUpload(Request $request)
    {
        Log::info('=== TEST UPLOAD DEBUG ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request has files: ' . ($request->hasFile('documents') ? 'Yes' : 'No'));
        Log::info('All request data: ' . json_encode($request->all()));
        Log::info('Files in request: ' . json_encode(array_keys($request->allFiles())));

        if ($request->hasFile('documents')) {
            Log::info('Documents count: ' . count($request->file('documents')));
            foreach ($request->file('documents') as $index => $file) {
                Log::info("File {$index}: " . $file->getClientOriginalName() . ' (' . $file->getSize() . ' bytes)');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Test upload received',
            'has_files' => $request->hasFile('documents'),
            'files_count' => $request->hasFile('documents') ? count($request->file('documents')) : 0
        ]);
    }

    // Test method to manually trigger saveToOtherLeavesDetails for debugging
    public function testSaveToOtherLeaves($referenceNo)
    {
        try {
            // Get the leave_details record
            $leaveDetail = \App\Models\LeaveDetail::where('reference_no', $referenceNo)->first();
            
            if (!$leaveDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'LeaveDetail record not found for reference: ' . $referenceNo
                ], 404);
            }

            // Create a mock request object
            $mockRequest = new \Illuminate\Http\Request();
            $mockRequest->merge([
                'leave_type' => $leaveDetail->leave_type_id,
                'from_date' => $leaveDetail->from_date,
                'to_date' => $leaveDetail->to_date,
                'duration' => $leaveDetail->duration,
            ]);

            // Call the saveToOtherLeavesDetails method
            $this->saveToOtherLeavesDetails($mockRequest, $referenceNo);

            // Get the created/updated record
            $otherLeave = OtherLeavesDetail::where('reference_no', $referenceNo)->first();

            return response()->json([
                'success' => true,
                'message' => 'Successfully saved to otherLeavesDetails',
                'leave_detail' => $leaveDetail,
                'other_leave' => $otherLeave
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store data to otherLeavesDetails table
     */
    public function storeOtherLeave(Request $request)
    {
        $rules = [
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:from_date',
            'duration' => 'required|integer|min:1',
        ];

        $validated = $request->validate($rules);

        // Generate unique reference number
        $referenceNo = 'OTHER_' . now()->format('YmdHis') . '_' . rand(1000, 9999);

        // Handle file uploads
        $leaveDocPaths = [];
        $consentLetterPaths = [];

        // Handle leave document uploads
        if ($request->hasFile('leave_document')) {
            foreach ($request->file('leave_document') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $date = now()->format('Ymd_His');
                $filename = $originalName . '_' . $date . '.' . $ext;
                $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                $leaveDocPaths[] = $path;
            }
        }

        // Handle consent letter uploads
        if ($request->hasFile('consent_letter')) {
            foreach ($request->file('consent_letter') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $date = now()->format('Ymd_His');
                $filename = $originalName . '_' . $date . '.' . $ext;
                $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                $consentLetterPaths[] = $path;
            }
        }

        // Create the other leave detail record
        $otherLeave = OtherLeavesDetail::create([
            'reference_no' => $referenceNo,
            'leave_type_id' => $request->leave_type_id,
            'from_date' => $request->from_date,
            'end_date' => $request->end_date,
            'duration' => $request->duration,
            'leave_document' => $leaveDocPaths,
            'consent_letter' => $consentLetterPaths,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Other leave details saved successfully',
            'data' => $otherLeave
        ]);
    }

    /**
     * Get all other leave details
     */
    public function getOtherLeaves()
    {
        $otherLeaves = OtherLeavesDetail::with('leaveType')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $otherLeaves
        ]);
    }

    /**
     * Get a specific other leave detail by ID
     */
    public function getOtherLeave($id)
    {
        $otherLeave = OtherLeavesDetail::with('leaveType')->find($id);

        if (!$otherLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Other leave detail not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $otherLeave
        ]);
    }

    /**
     * Update other leave detail
     */
    public function updateOtherLeave(Request $request, $id)
    {
        $otherLeave = OtherLeavesDetail::find($id);

        if (!$otherLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Other leave detail not found'
            ], 404);
        }

        $rules = [
            'leave_type_id' => 'required|exists:leave_types,id',
            'from_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:from_date',
            'duration' => 'required|integer|min:1',
        ];

        $validated = $request->validate($rules);

        // Handle file uploads
        $leaveDocPaths = $otherLeave->leave_document ?? [];
        $consentLetterPaths = $otherLeave->consent_letter ?? [];

        // Handle new leave document uploads
        if ($request->hasFile('leave_document')) {
            foreach ($request->file('leave_document') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $date = now()->format('Ymd_His');
                $filename = $originalName . '_' . $date . '.' . $ext;
                $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                $leaveDocPaths[] = $path;
            }
        }

        // Handle new consent letter uploads
        if ($request->hasFile('consent_letter')) {
            foreach ($request->file('consent_letter') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext = $file->getClientOriginalExtension();
                $date = now()->format('Ymd_His');
                $filename = $originalName . '_' . $date . '.' . $ext;
                $path = $file->storeAs('uploads/other_leaves', $filename, 'public');
                $consentLetterPaths[] = $path;
            }
        }

        // Update the other leave detail record
        $otherLeave->update([
            'leave_type_id' => $request->leave_type_id,
            'from_date' => $request->from_date,
            'end_date' => $request->end_date,
            'duration' => $request->duration,
            'leave_document' => $leaveDocPaths,
            'consent_letter' => $consentLetterPaths,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Other leave detail updated successfully',
            'data' => $otherLeave
        ]);
    }

    /**
     * Delete other leave detail
     */
    public function deleteOtherLeave($id)
    {
        $otherLeave = OtherLeavesDetail::find($id);

        if (!$otherLeave) {
            return response()->json([
                'success' => false,
                'message' => 'Other leave detail not found'
            ], 404);
        }

        // Delete associated files from storage
        if ($otherLeave->leave_document) {
            foreach ($otherLeave->leave_document as $doc) {
                if (Storage::disk('public')->exists($doc)) {
                    Storage::disk('public')->delete($doc);
                }
            }
        }

        if ($otherLeave->consent_letter) {
            foreach ($otherLeave->consent_letter as $doc) {
                if (Storage::disk('public')->exists($doc)) {
                    Storage::disk('public')->delete($doc);
                }
            }
        }

        $otherLeave->delete();

        return response()->json([
            'success' => true,
            'message' => 'Other leave detail deleted successfully'
        ]);
    }
}