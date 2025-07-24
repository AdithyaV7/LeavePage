@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Show remark if returned -->
    @isset($remark)
    <div class="alert alert-warning fw-semibold">
        Returned with remark:
        <pre class="mb-0">{{ $remark }}</pre>
    </div>
    @endisset
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">
                @if(isset($leave))
                    Application for Conference/ Seminar/ Training and Workshop
                @else
                    Application for Conference/ Seminar/ Training and Workshop
                @endif
            </h2>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
        @endif

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee No</label>
                        <input type="text" name="empno" class="form-control" value="{{ $user->empno }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIC</label>
                        <input type="text" name="nic" class="form-control" value="{{ $user->nic }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name with Initials</label>
                        <input type="text" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                    </div>
                                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Names Denoted by Initials</label>
                        <input type="text" class="form-control" value="{{ $user->names_denoted_by_initials }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Faculty</label>
                        <input type="text" class="form-control" value="{{ $user->faculty }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Designation</label>
                        <input type="text" class="form-control" value="{{ $user->designation }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile</label>
                        <input type="text" class="form-control" value="{{ $user->mobile }}" readonly>
                    </div>
            </div>
        </div>

        <!-- Previous Leaves -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white fw-semibold">
                <i class="fas fa-history me-2"></i>Previous Leaves
            </div>
            <div class="card-body">
                <p><strong>Academic Year: {{ date('Y') }}</strong></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Duration (Days)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($previousLeaves as $leave)
                            <tr>
                                <td>{{ $leave->leave_type}}</td>
                                <td>{{ $leave->from_date }}</td>
                                <td>{{ $leave->to_date }}</td>
                                <td>{{ $leave->duration }}</td>
                                <td>{{ $leave->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No previous approved leaves in academic year {{ date('Y') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Apply New Leave -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white fw-semibold">
                <i class="fas fa-calendar-plus me-2"></i>Applying New Leave
            </div>
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Required Leave Type *</label>
                    <select name="leave_type" class="form-select">
                        <option selected disabled value="">Select</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ isset($leave) && $leave->leave_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date *</label>
                    <input type="date" name="from_date" class="form-control" id="fromDate" value="{{ isset($leave) ? $leave->from_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date *</label>
                    <input type="date" name="to_date" class="form-control" id="toDate" value="{{ isset($leave) ? $leave->to_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days)</label>
                    <input type="number" name="duration" class="form-control" id="duration" value="{{ isset($leave) ? $leave->duration : '' }}">
                </div>

                <!-- need to add leave Details -->

                
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Leave Request Document <span class="text-danger">*</span></label>
                    <input type="file" id="leave_document_input" class="form-control mb-2" multiple>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="upload_leave_document_btn">Upload Leave Document(s)</button>
                    <div id="leave_document_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->leave_document))
                            @foreach($leave->leave_document as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="leave_document" data-file="{{ $file }}" aria-label="Delete"></button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div id="leave_document_required" class="text-danger small d-none">At least one document is required.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Consent Letter <span class="text-danger">*</span></label>
                    <input type="file" id="consent_letter_input" class="form-control mb-2" multiple>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="upload_consent_letter_btn">Upload Consent Letter(s)</button>
                    <div id="consent_letter_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->consent_letter))
                            @foreach($leave->consent_letter as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="consent_letter" data-file="{{ $file }}" aria-label="Delete"></button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div id="consent_letter_required" class="text-danger small d-none">At least one consent letter is required.</div>
                    <small class="form-text">
                        Download sample: 
                        <a href="{{ asset('sample-consent-letter.pdf') }}" target="_blank" class="text-success fw-semibold">Download the Consent Letter</a>
                    </small>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" name="confirm" class="form-check-input">
                    <label class="form-check-label">I confirm that the above details are true and correct.</label>
                </div>
            </div>
        </div>

        <!-- Buttons + Hidden Status Field -->
        <input type="hidden" name="form_status" id="formStatus" value="4">

        <div class="text-center">
            <button type="submit" class="btn btn-success px-4" onclick="setFormStatus(2)">Submit</button>
            <button type="submit" class="btn btn-warning px-4" onclick="setFormStatus(1)">Save Draft</button>
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary px-4" id="cancel-btn">Cancel</a>
        </div>
    </form>
@if(isset($leave) && $leave->form_status == 1)
    <form id="delete-draft-form" action="{{ route('leaves.destroy', $leave->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        document.getElementById('cancel-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Are you sure you want to cancel and delete this draft?')) {
                document.getElementById('delete-draft-form').submit();
            }
        });
    </script>
@endif
</div>

<script>
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("fromDate").setAttribute('min', today);
    document.getElementById("toDate").setAttribute('min', today);

    function setFormStatus(status) {
        document.getElementById("formStatus").value = status;
    }

    // Calculate duration excluding weekends
    document.getElementById("fromDate").addEventListener('change', calculateDuration);
    document.getElementById("toDate").addEventListener('change', calculateDuration);

    const previousLeaves = @json($previousLeaves);

    function calculateDuration() {
        const fromVal = document.getElementById("fromDate").value;
        const toVal = document.getElementById("toDate").value;

        if (!fromVal || !toVal) return;

        const from = new Date(fromVal);
        const to = new Date(toVal);

        if (to < from) {
            document.getElementById("duration").value = '';
            return;
        }

        for (let leave of previousLeaves) {
            let prevFrom = new Date(leave.from_date);
            let prevTo = new Date(leave.to_date);

            if ((from >= prevFrom && from <= prevTo) || (to >= prevFrom && to <= prevTo)) {
                alert("Selected range overlaps with an already approved leave.");
                document.getElementById("fromDate").value = '';
                document.getElementById("toDate").value = '';
                document.getElementById("duration").value = '';
                return;
            }
        }

        let count = 0;
        let current = new Date(from);
        while (current <= to) {
            if (current.getDay() !== 0 && current.getDay() !== 6) count++;
            current.setDate(current.getDate() + 1);
        }
        document.getElementById("duration").value = count;
    }

    // Calculate duration on page load if editing
    @if(isset($leave))
        calculateDuration();
    @endif

    document.querySelector('button.btn-success').addEventListener('click', function(e) {
        // Set required for all fields
        document.querySelector('[name="leave_type"]').required = true;
        document.querySelector('[name="from_date"]').required = true;
        document.querySelector('[name="to_date"]').required = true;
        document.querySelector('[name="duration"]').required = true;
        document.querySelector('[name="leave_document"]').required = true;
        document.querySelector('[name="consent_letter"]').required = true;
        document.querySelector('[name="confirm"]').required = true;
    });

    document.querySelector('button.btn-warning').addEventListener('click', function(e) {
        // Remove required for all fields
        document.querySelector('[name="leave_type"]').required = false;
        document.querySelector('[name="from_date"]').required = false;
        document.querySelector('[name="to_date"]').required = false;
        document.querySelector('[name="duration"]').required = false;
        document.querySelector('[name="leave_document"]').required = false;
        document.querySelector('[name="consent_letter"]').required = false;
        document.querySelector('[name="confirm"]').required = false;
    });

    let leaveId = {{ isset($leave) ? $leave->id : 'null' }};
    // AJAX upload for Save Draft
    function uploadFilesAJAX(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;
        if (!leaveId) {
            alert('Please save the form as draft at least once before uploading files.');
            return;
        }
        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('type', type);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.uploadFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(tagsId, data.files, type);
                    input.value = '';
                } else {
                    alert(data.error || 'Upload failed');
                }
            });
        }
    }
    document.getElementById('upload_leave_document_btn').addEventListener('click', function() {
        uploadFilesAJAX('leave_document_input', 'leave_document', 'leave_document_tags');
    });
    document.getElementById('upload_consent_letter_btn').addEventListener('click', function() {
        uploadFilesAJAX('consent_letter_input', 'consent_letter', 'consent_letter_tags');
    });
    // Render file tags
    function renderFileTags(tagsId, files, type) {
        const tagsDiv = document.getElementById(tagsId);
        tagsDiv.innerHTML = '';
        files.forEach(file => {
            const span = document.createElement('span');
            span.className = 'badge bg-secondary me-1';
            span.innerHTML = `<a href="/storage/${file}" target="_blank" class="text-white text-decoration-none">${file.split('/').pop()}</a> <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="${type}" data-file="${file}" aria-label="Delete"></button>`;
            tagsDiv.appendChild(span);
        });
    }
    // AJAX delete for already uploaded files in drafts
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-file-btn')) {
            const type = e.target.getAttribute('data-type');
            const file = e.target.getAttribute('data-file');
            if (!leaveId) return;
            const formData = new FormData();
            formData.append('type', type);
            formData.append('file', file);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.deleteFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(type === 'leave_document' ? 'leave_document_tags' : 'consent_letter_tags', data.files, type);
                } else {
                    alert(data.error || 'Delete failed');
                }
            });
        }
    });

    // Disable submit if not draft and files missing
    function updateRequiredState() {
        const isDraft = document.getElementById('formStatus').value == 1;
        const submitBtn = document.querySelector('button.btn-success');
        if (!isDraft) {
            if (leaveDocumentFiles.length === 0) {
                document.getElementById('leave_document_required').classList.remove('d-none');
            } else {
                document.getElementById('leave_document_required').classList.add('d-none');
            }
            if (consentLetterFiles.length === 0) {
                document.getElementById('consent_letter_required').classList.remove('d-none');
            } else {
                document.getElementById('consent_letter_required').classList.add('d-none');
            }
            submitBtn.disabled = (leaveDocumentFiles.length === 0 || consentLetterFiles.length === 0);
        } else {
            document.getElementById('leave_document_required').classList.add('d-none');
            document.getElementById('consent_letter_required').classList.add('d-none');
            submitBtn.disabled = false;
        }
    }
    updateRequiredState();
    document.getElementById('formStatus').addEventListener('change', updateRequiredState);
</script>
@endsection


