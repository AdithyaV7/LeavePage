<?php
namespace App\Http\Controllers;


use App\Models\LeaveDetail;
use App\Models\LeaveRequestDetail;
use App\Models\LeaveType;
use App\Models\Status;
use Illuminate\Support\Facades\Session;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Generate a new reference number following the pattern: REF<empNo><Current year><04><auto increment>
     */
    private function generateReferenceNumber($empNo)
    {
        $currentYear = date('Y');
        $prefix = "REF{$empNo}{$currentYear}04";

        // Get the highest auto increment number for this employee and year
        $lastRef = DB::table('leave_details')
            ->where('empno', $empNo)
            ->where('reference_no', 'LIKE', $prefix . '%')
            ->orderByDesc('reference_no')
            ->value('reference_no');

        if ($lastRef) {
            // Extract the auto increment part and increment it
            $lastIncrement = (int) substr($lastRef, strlen($prefix));
            $newIncrement = $lastIncrement + 1;
        } else {
            // First application for this employee this year
            $newIncrement = 1;
        }

        // Pad with zeros to make it at least 3 digits
        $incrementPart = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);

        return $prefix . $incrementPart;
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

        DB::table('leave_details')->where('id', $id)->delete();

        return redirect()->route('leaves.index')->with('success', 'Draft deleted successfully.');
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
        // Note: For new applications, $leave will be null and the form will work without a database record

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

        // Load existing travel details if editing
        $travelDetails = [];
        if ($leave) {
            $travelDetails = LeaveRequestDetail::where('reference_no', $leave->reference_no)->get();
        }

        return view('create', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'remark', 'travelDetails'));
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
                $rules['consent_letter'] = 'required';
            }
        }

        $validated = $request->validate($rules);

        // Additional validation for final submission (not draft)
        if (!$isDraft) {
            // Check if travel details exist for final submission
            $referenceNo = $request->reference_no;
            if ($referenceNo) {
                $travelDetailsCount = \App\Models\LeaveRequestDetail::where('reference_no', $referenceNo)->count();
                if ($travelDetailsCount === 0) {
                    return back()->withErrors(['travel_details' => 'At least one travel detail with supporting documents is required for final submission.'])->withInput();
                }

                // Check if all travel details have at least one document
                $travelDetailsWithoutDocs = \App\Models\LeaveRequestDetail::where('reference_no', $referenceNo)
                    ->where(function($query) {
                        $query->whereNull('documents')
                              ->orWhereRaw('JSON_LENGTH(documents) = 0');
                    })->count();

                if ($travelDetailsWithoutDocs > 0) {
                    return back()->withErrors(['travel_details' => 'All travel details must have at least one supporting document.'])->withInput();
                }
            }
        }

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
                'department_id' => $user->department_id,
                'faculty_id' => $user->faculty_id,
                // Preserve existing remarks instead of setting to null
            ]);

            // Handle travel details
            $this->saveTravelDetails($request, $leave->reference_no);

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
                if (count($consentLetterPaths) == 0) {
                    return back()->with('error', 'At least one consent letter is required.');
                }
            }
            // Generate new reference number using the new pattern
            $refNo = $this->generateReferenceNumber($user->employee_no);
            \App\Models\LeaveDetail::create([
                'empno' => $user->employee_no,
                'nic' => $user->nic,
                'leave_type_id' => $request->leave_type,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'duration' => $request->duration,
                'leave_document' => $leaveDocPaths,
                'consent_letter' => $consentLetterPaths,
                'status_id' => $request->form_status == 1 ? 3 : 4, // 3=Editing/Draft, 4=Processing MA
                'form_status' => $request->form_status, // 1=Draft, 2=Submitted
                'reference_no' => $refNo,
                'applied_date' => now()->addHours(5)->addMinutes(30),
                'department_id' => $user->department_id,
                'faculty_id' => $user->faculty_id,
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

        $user = DB::table('employees')->where('employee_no', session('empno'))->first();
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

    // Helper method to save travel details (now handled via AJAX)
    private function saveTravelDetails(Request $request, $referenceNo)
    {
        // Travel details are now saved individually via AJAX
        // This method is kept for backward compatibility but does nothing
        // Travel details are saved using the saveTravelDetail() method
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
}