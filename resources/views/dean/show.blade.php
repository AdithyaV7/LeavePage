@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Application Review (Dean)</h2>
            <p class="text-muted mb-0">Reference: {{ $application->reference_no }}</p>
        </div>
        <a href="{{ route('dean.index') }}" class="btn btn-outline-secondary">
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
                        <ul class="list-unstyled">
                            @foreach($application->leave_documents as $doc)
                                <li class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <span class="text-muted">{{ basename($doc) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Consent Letters</label>
                    @if(!empty($application->consent_letters) && count($application->consent_letters))
                        <ul class="list-unstyled">
                            @foreach($application->consent_letters as $letter)
                                <li class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <span class="text-muted">{{ basename($letter) }}</span>
                                </li>
                            @endforeach
                        </ul>
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
    <!-- Dean Review Section -->
    @if(empty($readonly) || !$readonly)
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>Dean Review Actions
        </div>
        <div class="card-body">
            <form id="recommendForm" action="{{ route('dean.recommend', $application->id) }}" method="POST" class="mb-3">
                @csrf
                <!-- Add your Dean review fields here -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dean's Recommendation</label><br>
                    <input type="radio" id="recommend_yes" name="dean_recommend" value="1" required> Recommend
                    <input type="radio" id="recommend_no" name="dean_recommend" value="0"> Not Recommend
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" id="remarks-label">Remarks (optional)</label>
                    <textarea name="dean_remarks" id="dean_remarks" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-2">
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
    <!-- Signature Block -->
    <div class="mt-5 text-start">
        <div class="fw-bold">Dr. S. Perera</div>
        <div>Dean FAS</div>
        <div>Applied Science</div>
        <div>University of Sri Jayewardenepura</div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recommendYes = document.getElementById('recommend_yes');
        const recommendNo = document.getElementById('recommend_no');
        const remarks = document.getElementById('dean_remarks');
        const remarksLabel = document.getElementById('remarks-label');

        function toggleRemarks() {
            if (recommendNo.checked) {
                remarks.required = true;
                remarksLabel.innerHTML = "Remarks <span class='text-danger'>*</span>";
            } else {
                remarks.required = false;
                remarksLabel.textContent = "Remarks (optional)";
            }
        }
        recommendYes.addEventListener('change', toggleRemarks);
        recommendNo.addEventListener('change', toggleRemarks);
        toggleRemarks();
    });
</script>
@endsection 