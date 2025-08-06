@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Application Review (HOD)</h2>
            <p class="text-muted mb-0">Reference: {{ $application->reference_no }}</p>
        </div>
        <a href="{{ route('hod.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Application Status -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark fw-semibold">
            <i class="fas fa-clock me-2"></i>Application Status
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Status:</strong> 
                    <span class="badge bg-warning">{{ $application->status }}</span>
                </div>
                <div class="col-md-6">
                    <strong>Applied Date:</strong> 
                    {{ \Carbon\Carbon::parse($application->applied_date)->format('F d, Y \a\t h:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Details -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-user me-2"></i>Personal Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->empno }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name with Initials</label>
                    <input type="text" class="form-control" value="{{ $application->name_with_initials }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Names Denoted by Initials</label>
                    <input type="text" class="form-control" value="{{ $application->names_denoted_by_initials }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" class="form-control" value="{{ $application->department }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Faculty</label>
                    <input type="text" class="form-control" value="{{ $application->faculty }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" class="form-control" value="{{ $application->designation }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile</label>
                    <input type="text" class="form-control" value="{{ $application->mobile }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIC</label>
                    <input type="text" class="form-control" value="{{ $application->nic }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Details -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-semibold">
            <i class="fas fa-calendar me-2"></i>Leave Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Leave Type</label>
                    <input type="text" class="form-control" value="{{ $application->leave_type_name }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($application->from_date)->format('F d, Y') }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($application->to_date)->format('F d, Y') }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Duration (Days)</label>
                    <input type="text" class="form-control" value="{{ $application->duration }} days" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Travel Details -->
    @if(isset($travelDetails) && count($travelDetails) > 0)
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark fw-semibold">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-plane me-2"></i>Travel Details
                </div>
                <small class="badge bg-dark">{{ count($travelDetails) }} destination(s)</small>
            </div>
        </div>
        <div class="card-body">
            @foreach($travelDetails as $index => $detail)
                <div class="travel-detail-entry {{ $index > 0 ? 'border-top pt-3 mt-3' : '' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel Details</label>
                            <textarea class="form-control" rows="3" readonly>{{ $detail->detail }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" class="form-control" value="{{ $detail->country }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel From Date</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($detail->travel_from_date)->format('F d, Y') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel To Date</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($detail->travel_to_date)->format('F d, Y') }}" readonly>
                        </div>
                        @if(!empty($detail->documents) && count($detail->documents) > 0)
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Travel Documents</label>
                            <div class="travel-documents-container">
                                @foreach($detail->documents as $doc)
                                    <div class="document-item mb-3 p-3 border rounded">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i>
                                                <div>
                                                    <span class="fw-semibold">{{ basename($doc) }}</span>
                                                    <br>
                                                    <small class="text-muted">Travel Document</small>
                                                </div>
                                            </div>
                                            <div class="document-actions">
                                                <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                                    <i class="fas fa-external-link-alt me-1"></i>Open
                                                </a>
                                                <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" download="{{ basename($doc) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-download me-1"></i>Download
                                                </a>
                                            </div>
                                        </div>

                                        <div class="pdf-frame-container">
                                            <iframe src="{{ asset('storage/' . ltrim($doc, '/')) }}"
                                                    class="pdf-frame"
                                                    frameborder="0">
                                                <p>Your browser does not support this document format.
                                                   <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank">Download the document</a>.
                                                </p>
                                            </iframe>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Travel Documents</label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No travel documents uploaded for this destination.
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- No Travel Details -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white fw-semibold">
            <i class="fas fa-plane me-2"></i>Travel Details
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                No travel details provided for this leave application.
            </div>
        </div>
    </div>
    @endif

    <!-- Documents -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="fas fa-file me-2"></i>Documents
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Leave Request Documents</label>
                    @if(!empty($application->leave_documents) && count($application->leave_documents))
                        @foreach($application->leave_documents as $doc)
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span class="text-muted">{{ basename($doc) }}</span>
                                    </div>
                                    <div class="document-actions">
                                        <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="fas fa-external-link-alt me-1"></i>Open
                                        </a>
                                        <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" download="{{ basename($doc) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                                <div class="pdf-frame-container">
                                    <iframe src="{{ asset('storage/' . ltrim($doc, '/')) }}"
                                            class="pdf-frame"
                                            frameborder="0">
                                        <p>Your browser does not support PDFs.
                                           <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank">Download the PDF</a>.
                                        </p>
                                    </iframe>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Consent Letters</label>
                    @if(!empty($application->consent_letters) && count($application->consent_letters))
                        @foreach($application->consent_letters as $letter)
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span class="text-muted">{{ basename($letter) }}</span>
                                    </div>
                                    <div class="document-actions">
                                        <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="fas fa-external-link-alt me-1"></i>Open
                                        </a>
                                        <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" download="{{ basename($letter) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                                <div class="pdf-frame-container">
                                    <iframe src="{{ asset('storage/' . ltrim($letter, '/')) }}"
                                            class="pdf-frame"
                                            frameborder="0">
                                        <p>Your browser does not support PDFs.
                                           <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" target="_blank">Download the PDF</a>.
                                        </p>
                                    </iframe>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- HOD Review Section -->
    @if(empty($readonly) || !$readonly)
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>HOD Review Actions
        </div>
        <div class="card-body">
            <form id="approveForm" action="{{ route('hod.approve', $application->id) }}" method="POST" class="mb-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Whether adequate staff available for the continuation of academic programs during the period of applicant's leave *</label><br>
                    <input type="radio" name="hod_adequate_staff" value="1" required> Yes
                    <input type="radio" name="hod_adequate_staff" value="0"> No
                    <div id="hod_adequate_staff_error" class="text-danger small d-none">Please select whether adequate staff is available.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Whether satisfactory agreements can be made to cover applicant's teaching activities and other commitments *</label><br>
                    <input type="radio" name="hod_teaching_covered" value="1" required> Yes
                    <input type="radio" name="hod_teaching_covered" value="0"> No
                    <div id="hod_teaching_covered_error" class="text-danger small d-none">Please select whether teaching activities can be covered.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Whether the applicant has completed all requirements regarding examinations-related work *</label><br>
                    <input type="radio" name="hod_exam_work_completed" value="1" required> Yes
                    <input type="radio" name="hod_exam_work_completed" value="0"> No
                    <div id="hod_exam_work_completed_error" class="text-danger small d-none">Please select whether exam work is completed.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Leave recommend or Leave not recommend *</label><br>
                    <input type="radio" name="hod_recommend" value="1" required id="recommend_yes"> Recommend
                    <input type="radio" name="hod_recommend" value="0" id="recommend_no"> Not Recommend
                    <div id="hod_recommend_error" class="text-danger small d-none">Please select whether to recommend or not recommend.</div>
                </div>
                <div class="mb-3" id="not-recommend-reason-div" style="display:none;">
                    <label class="form-label fw-semibold">If not recommended, please give reasons <span class="text-danger">*</span></label>
                    <textarea name="hod_not_recommend_reason" class="form-control" id="not-recommend-reason"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Any other remarks (optional)</label>
                    <textarea name="hod_other_remarks" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Today's Date</label>
                    <input type="text" class="form-control" value="{{ date('Y-m-d') }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Signature (Name with initials) *</label>
                    <input type="text" name="hod_signature" class="form-control" value="O. Wickramasinghe" required>
                    <div id="hod_signature_error" class="text-danger small d-none">Please enter your signature.</div>
                </div>
                <button type="button" class="btn btn-success me-2" id="hod-submit-btn">
                    <i class="fas fa-check me-2"></i>Forward
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info mt-4">
        <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
    </div>
    @endif
</div>

<script>
    // HOD Form Validation
    function validateHODForm() {
        hideAllHODErrors();
        let isValid = true;

        // Validate adequate staff
        const adequateStaff = document.querySelector('input[name="hod_adequate_staff"]:checked');
        if (!adequateStaff) {
            showHODError('hod_adequate_staff_error');
            isValid = false;
        }

        // Validate teaching covered
        const teachingCovered = document.querySelector('input[name="hod_teaching_covered"]:checked');
        if (!teachingCovered) {
            showHODError('hod_teaching_covered_error');
            isValid = false;
        }

        // Validate exam work completed
        const examWork = document.querySelector('input[name="hod_exam_work_completed"]:checked');
        if (!examWork) {
            showHODError('hod_exam_work_completed_error');
            isValid = false;
        }

        // Validate recommendation
        const recommend = document.querySelector('input[name="hod_recommend"]:checked');
        if (!recommend) {
            showHODError('hod_recommend_error');
            isValid = false;
        }

        // Validate signature
        const signature = document.querySelector('input[name="hod_signature"]');
        if (!signature || !signature.value.trim()) {
            showHODError('hod_signature_error');
            isValid = false;
        }

        // Validate not recommend reason if "Not Recommend" is selected
        const notRecommend = document.getElementById('recommend_no').checked;
        const reasonTextarea = document.getElementById('not-recommend-reason');
        if (notRecommend && (!reasonTextarea.value || !reasonTextarea.value.trim())) {
            alert('Please provide a reason for not recommending.');
            isValid = false;
        }

        return isValid;
    }

    function showHODError(errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.classList.remove('d-none');
        }
    }

    function hideAllHODErrors() {
        const errorIds = [
            'hod_adequate_staff_error',
            'hod_teaching_covered_error',
            'hod_exam_work_completed_error',
            'hod_recommend_error',
            'hod_signature_error'
        ];

        errorIds.forEach(function(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
        });
    }

    function scrollToFirstHODError() {
        const errorSelectors = [
            '#hod_adequate_staff_error:not(.d-none)',
            '#hod_teaching_covered_error:not(.d-none)',
            '#hod_exam_work_completed_error:not(.d-none)',
            '#hod_recommend_error:not(.d-none)',
            '#hod_signature_error:not(.d-none)'
        ];

        for (let selector of errorSelectors) {
            const errorElement = document.querySelector(selector);
            if (errorElement) {
                // Add a small delay to ensure the error is visible
                setTimeout(function() {
                    errorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                    // Add a highlight effect
                    errorElement.style.fontWeight = 'bold';
                    setTimeout(function() {
                        errorElement.style.fontWeight = '500';
                    }, 2000);
                }, 100);
                break;
            }
        }
    }

    // Add event listeners to hide errors when fields are filled
    document.addEventListener('DOMContentLoaded', function() {
        // Submit button event listener
        document.getElementById('hod-submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if (validateHODForm()) {
                document.getElementById('approveForm').submit();
            } else {
                // Scroll to first error in document order
                scrollToFirstHODError();
            }
        });

        // Hide errors when fields are filled
        document.querySelectorAll('input[name="hod_adequate_staff"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('hod_adequate_staff_error').classList.add('d-none');
            });
        });

        document.querySelectorAll('input[name="hod_teaching_covered"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('hod_teaching_covered_error').classList.add('d-none');
            });
        });

        document.querySelectorAll('input[name="hod_exam_work_completed"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('hod_exam_work_completed_error').classList.add('d-none');
            });
        });

        document.querySelectorAll('input[name="hod_recommend"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('hod_recommend_error').classList.add('d-none');
            });
        });

        document.querySelector('input[name="hod_signature"]').addEventListener('input', function() {
            document.getElementById('hod_signature_error').classList.add('d-none');
        });
    });
</script>

<script>
    // Show/hide not recommend reason
    document.addEventListener('DOMContentLoaded', function() {
        const recommendNo = document.getElementById('recommend_no');
        const recommendYes = document.getElementById('recommend_yes');
        const reasonDiv = document.getElementById('not-recommend-reason-div');
        const reasonInput = document.getElementById('not-recommend-reason');
        const sendToDeanBtn = document.getElementById('send-to-dean');
        
        function toggleReason() {
            if (recommendNo.checked) {
                reasonDiv.style.display = 'block';
                reasonInput.required = true;
                // Don't disable the button - allow forwarding even if not recommended
            } else {
                reasonDiv.style.display = 'none';
                reasonInput.required = false;
            }
        }
        
        recommendNo.addEventListener('change', toggleReason);
        recommendYes.addEventListener('change', toggleReason);
        toggleReason();
    });
</script>

<style>
.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.card-header {
    border-bottom: none;
}

.btn {
    font-weight: 500;
}

.badge {
    font-size: 0.875rem;
}

.pdf-frame-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    background-color: #f8f9fa;
}

.pdf-frame {
    width: 100%;
    height: 300px;
    border: none;
    display: block;
}

.pdf-frame-container:hover {
    border-color: #adb5bd;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.travel-detail-entry {
    position: relative;
}

.travel-detail-entry .border-top {
    border-top: 1px solid #dee2e6 !important;
}

.travel-documents-container {
    max-height: 600px;
    overflow-y: auto;
}

.document-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.document-item:hover {
    background-color: #e9ecef;
    border-color: #007bff !important;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
}

.document-actions .btn {
    transition: all 0.2s ease;
}

.document-actions .btn:hover {
    transform: translateY(-1px);
}

.image-preview-container {
    text-align: center;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.document-preview-container {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.travel-documents-container::-webkit-scrollbar {
    width: 6px;
}

.travel-documents-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.travel-documents-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.travel-documents-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Validation Error Styling */
.text-danger.small {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
    display: block;
    padding: 0.25rem 0;
    border-radius: 0.25rem;
}

.text-danger.small:not(.d-none) {
    animation: fadeIn 0.3s ease-in;
}

/* Scroll target highlighting */
.text-danger.small:target,
.text-danger.small:focus {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 3px solid #dc3545;
    padding-left: 0.5rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endsection