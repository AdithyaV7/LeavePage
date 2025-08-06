@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Application Review (VC)</h2>
            <p class="text-muted mb-0">Reference: {{ $application->reference_no }}</p>
        </div>
        <a href="{{ route('vc.index') }}" class="btn btn-outline-secondary">
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
                    {{ \Carbon\Carbon::parse($application->applied_date)->format('F d, Y') }}
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
    <!-- HOD Remarks Section -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-comments me-2"></i>HOD Remarks
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-7">Whether adequate staff available for the continuation of academic programs during the period of applicant's leave:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_adequate_staff === 1 ? 'bg-success' : ($application->hod_adequate_staff === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_adequate_staff === 1 ? 'Yes' : ($application->hod_adequate_staff === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Whether satisfactory agreements can be made to cover applicant's teaching activities and other commitments:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_teaching_covered === 1 ? 'bg-success' : ($application->hod_teaching_covered === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_teaching_covered === 1 ? 'Yes' : ($application->hod_teaching_covered === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Whether the applicant has completed all requirements regarding examinations-related work:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_exam_work_completed === 1 ? 'bg-success' : ($application->hod_exam_work_completed === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_exam_work_completed === 1 ? 'Yes' : ($application->hod_exam_work_completed === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Recommendation:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_recommend === 1 ? 'bg-success' : ($application->hod_recommend === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_recommend === 1 ? 'Recommended' : ($application->hod_recommend === 0 ? 'Not Recommended' : 'N/A') }}
                    </span>
                </dd>
                @if($application->hod_recommend === 0)
                    <dt class="col-sm-7">Reason (if not recommended):</dt>
                    <dd class="col-sm-5">{{ $application->hod_not_recommend_reason }}</dd>
                @endif
                <dt class="col-sm-7">Other Remarks:</dt>
                <dd class="col-sm-5">{{ $application->hod_other_remarks }}</dd>
                <dt class="col-sm-7">Reviewed By:</dt>
                <dd class="col-sm-5">{{ $application->hod_reviewed_by }}</dd>
                <dt class="col-sm-7">Reviewed At:</dt>
                <dd class="col-sm-5">{{ $application->hod_reviewed_at }}</dd>
            </dl>
        </div>
    </div>
    <!-- Dean Recommendation Section -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>Dean Recommendation
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-7">Recommendation:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->dean_recommend === 1 ? 'bg-success' : ($application->dean_recommend === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->dean_recommend === 1 ? 'Recommended' : ($application->dean_recommend === 0 ? 'Not Recommended' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Remarks:</dt>
                <dd class="col-sm-5">{{ $application->dean_remarks }}</dd>
                <dt class="col-sm-7">Reviewed By:</dt>
                <dd class="col-sm-5">{{ $application->dean_reviewed_by }}</dd>
                <dt class="col-sm-7">Reviewed At:</dt>
                <dd class="col-sm-5">{{ $application->dean_reviewed_at }}</dd>
            </dl>
        </div>
    </div>
    <!-- VC Review Section -->
    @if(empty($readonly) || !$readonly)
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>VC Recommendation
        </div>
        <div class="card-body">
            <form id="vcRecommendForm" action="{{ route('vc.recommend', $application->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">1. Recommended to submit to Leave and Awards Committee *</label><br>
                    <input type="radio" name="vc_recommend_committee" value="1" id="vc_recommend_committee_yes"> Yes
                    <input type="radio" name="vc_recommend_committee" value="0" id="vc_recommend_committee_no"> No
                    <div id="vc_recommend_committee_error" class="text-danger small d-none">Please select Yes or No for committee recommendation.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">2. Approved subject to the covering approval of the council *</label><br>
                    <input type="radio" name="vc_approved_council" value="1" id="vc_approved_council_yes"> Yes
                    <input type="radio" name="vc_approved_council" value="0" id="vc_approved_council_no"> No
                    <div id="vc_approved_council_error" class="text-danger small d-none">Please select Yes or No for council approval.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Remarks (optional)</label>
                    <textarea name="vc_remarks" class="form-control"></textarea>
                </div>
                <div class="mb-3 text-danger" id="vc-form-error" style="display:none;"></div>
                <button type="button" class="btn btn-success" id="vc-submit-btn">Forward</button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info mt-4">
        <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
    </div>
    @endif
    <!-- Signature Block -->
    <div class="mt-5 text-start">
        <div class="fw-bold">Dr. A. Silva</div>
        <div>Vice Chancellor</div>
        <div>University of Sri Jayewardenepura</div>
    </div>
</div>
<script>
    // VC Form Validation
    function validateVCForm() {
        hideAllVCErrors();
        let isValid = true;

        // Validate committee recommendation
        const recommendCommittee = document.querySelector('input[name="vc_recommend_committee"]:checked');
        if (!recommendCommittee) {
            showVCError('vc_recommend_committee_error');
            isValid = false;
        }

        // Validate council approval
        const approvedCouncil = document.querySelector('input[name="vc_approved_council"]:checked');
        if (!approvedCouncil) {
            showVCError('vc_approved_council_error');
            isValid = false;
        }

        return isValid;
    }

    function showVCError(errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.classList.remove('d-none');
        }
    }

    function hideAllVCErrors() {
        const errorIds = [
            'vc_recommend_committee_error',
            'vc_approved_council_error'
        ];

        errorIds.forEach(function(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
        });
    }

    function scrollToFirstVCError() {
        const errorSelectors = [
            '#vc_recommend_committee_error:not(.d-none)',
            '#vc_approved_council_error:not(.d-none)'
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

    document.addEventListener('DOMContentLoaded', function() {
        // Submit button event listener
        document.getElementById('vc-submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if (validateVCForm()) {
                document.getElementById('vcRecommendForm').submit();
            } else {
                // Scroll to first error in document order
                scrollToFirstVCError();
            }
        });

        // Hide errors when fields are filled
        document.querySelectorAll('input[name="vc_recommend_committee"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('vc_recommend_committee_error').classList.add('d-none');
            });
        });

        document.querySelectorAll('input[name="vc_approved_council"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('vc_approved_council_error').classList.add('d-none');
            });
        });
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