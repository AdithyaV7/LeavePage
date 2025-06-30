@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Application Review</h2>
            <p class="text-muted mb-0">Reference: {{ $application->reference_no }}</p>
        </div>
        <a href="{{ route('ma.index') }}" class="btn btn-outline-secondary">
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
                    <label class="form-label fw-semibold">Name with Initials</label>
                    <input type="text" class="form-control" value="{{ $application->name_with_initials }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Names Denoted by Initials</label>
                    <input type="text" class="form-control" value="{{ $application->names_denoted_by_initials }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" class="form-control" value="{{ $application->department }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Faculty</label>
                    <input type="text" class="form-control" value="{{ $application->faculty }}" readonly>
                </div>
                <div class="col-md-4">
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
                    <label class="form-label fw-semibold">Leave Request Document</label>
                    @if($application->leave_document)
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                            <a href="{{ Storage::url($application->leave_document) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Consent Letter</label>
                    @if($application->consent_letter)
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-pdf text-danger me-2"></i>
                            <a href="{{ Storage::url($application->consent_letter) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Section -->
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>Review Actions
        </div>
        <div class="card-body">
            <form id="approveForm" action="{{ route('ma.approve', $application->id) }}" method="POST" class="d-inline">
                @csrf
                <div class="mb-3">
                    <label for="approveRemark" class="form-label fw-semibold">Remarks (Optional)</label>
                    <textarea class="form-control" id="approveRemark" name="remark" rows="3" 
                              placeholder="Add any comments or remarks (optional)"></textarea>
                </div>
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-check me-2"></i>Forward to HOD
                </button>
            </form>

            <form id="returnForm" action="{{ route('ma.return', $application->id) }}" method="POST" class="d-inline">
                @csrf
                <div class="mb-3">
                    <label for="returnRemark" class="form-label fw-semibold text-danger">Return Remarks *</label>
                    <textarea class="form-control" id="returnRemark" name="remark" rows="3" 
                              placeholder="Please provide a reason for returning this application (required)" required></textarea>
                    <div class="form-text text-danger">Remarks are required when returning an application.</div>
                </div>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-undo me-2"></i>Return to User
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
document.getElementById('approveForm').addEventListener('submit', function(e) {
    // Approve form doesn't need validation for remarks
});

document.getElementById('returnForm').addEventListener('submit', function(e) {
    const remark = document.getElementById('returnRemark').value.trim();
    if (!remark) {
        e.preventDefault();
        alert('Please provide remarks when returning an application.');
        document.getElementById('returnRemark').focus();
    }
});

// Confirm actions
document.querySelector('#approveForm button[type="submit"]').addEventListener('click', function(e) {
    if (!confirm('Are you sure you want to forward this application to HOD?')) {
        e.preventDefault();
    }
});

document.querySelector('#returnForm button[type="submit"]').addEventListener('click', function(e) {
    if (!confirm('Are you sure you want to return this application to the user?')) {
        e.preventDefault();
    }
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
</style>
@endsection 