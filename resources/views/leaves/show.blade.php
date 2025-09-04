@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Application for Conference/ Seminar/ Training and Workshop
            </h2>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-maroon">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Personal Details (readonly) -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-user me-2"></i>Personal Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $user->empno }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIC</label>
                    <input type="text" class="form-control" value="{{ $user->nic }}" readonly>
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
                    @forelse ($previousLeaves as $prevLeave)
                        <tr>
                            <td>{{ $prevLeave->leave_type}}</td>
                            <td>{{ $prevLeave->from_date }}</td>
                            <td>{{ $prevLeave->to_date }}</td>
                            <td>{{ $prevLeave->duration }}</td>
                            <td>{{ $prevLeave->status }}</td>
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

    <!-- Applied Leave Details -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="fas fa-calendar-plus me-2"></i>Applied Leave Details
        </div>
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Leave Type</label>
                <input type="text" class="form-control" value="{{ $leave->leaveType->name ?? 'N/A' }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Start Date</label>
                <input type="text" class="form-control" value="{{ $leave->from_date }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">End Date</label>
                <input type="text" class="form-control" value="{{ $leave->to_date }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Duration (Days)</label>
                <input type="text" class="form-control" value="{{ $leave->duration }}" readonly>
            </div>
        </div>
    </div>

    <!-- Travel Details Card -->
    <div class="card mb-4 travel-details-card">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <i class="fas fa-plane me-2"></i>
                    <span class="fw-bold">Leave Request Documents</span>
                </div>
                <small class="opacity-75">Travel destinations and supporting documents</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Travel Details:</strong> Below are the travel details and documents for this application.
            </div>

            <!-- Travel Details Table -->
            <div class="mt-4" id="travel-details-table-container">
                <h6 class="text-primary mb-3">
                    <i class="fas fa-list me-2"></i>Travel Details
                </h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="travel-details-table">
                        <thead class="table-primary">
                            <tr>
                                <th>Details</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Country</th>
                                <th>Documents</th>
                            </tr>
                        </thead>
                        <tbody id="travel-details-tbody">
                            @if(isset($travelDetails) && count($travelDetails) > 0)
                                @foreach($travelDetails as $detail)
                                    <tr data-id="{{ $detail->id }}">
                                        <td>{{ $detail->detail }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->travel_from_date)->format('M d, Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($detail->travel_to_date)->format('M d, Y') }}</td>
                                        <td>{{ $detail->country }}</td>
                                        <td>
                                            @if($detail->documents && count($detail->documents) > 0)
                                                @foreach($detail->documents as $doc)
                                                    <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="badge bg-primary text-decoration-none me-1 mb-1">{{ basename($doc) }}</a>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No documents</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr id="no-travel-details">
                                    <td colspan="5" class="text-center text-muted">No travel details found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Request Documents -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-file-alt me-2"></i>Leave Request Documents
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Other Leave Request Documents</label>
                    <div class="mb-2">
                        @if(isset($leave) && is_array($leave->leave_document) && count($leave->leave_document) > 0)
                            @foreach($leave->leave_document as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">No documents uploaded</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Consent Letter</label>
                    <div class="mb-2">
                        @if(isset($leave) && is_array($leave->consent_letter) && count($leave->consent_letter) > 0)
                            @foreach($leave->consent_letter as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">No consent letter uploaded</span>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>



    <!-- Back Button -->
    <div class="text-center">
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-maroon px-4">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
</div>

<style>
/* Travel Details Enhanced Styles */
.travel-details-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.bg-gradient-primary {
    background: linear-gradient(135deg,rgb(0, 0, 0) 0%, #000000 100%);
}

.bg-info {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}

.bg-primary {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}

.bg-secondary {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}

/* Travel Details Table Styles */
#travel-details-table-container {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1.5rem;
    border: 1px solid #dee2e6;
}

#travel-details-table {
    margin-bottom: 0;
}

#travel-details-table th {
    background-color:rgb(4, 4, 4);
    color: white;
    font-weight: 600;
    border: none;
}

#travel-details-table td {
    vertical-align: middle;
    border-color: #dee2e6;
}

#travel-details-table tbody tr:hover {
    background-color: #e3f2fd;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

/* Header color change */
.text-maroon {
    color: rgba(0, 0, 0) !important;
}

/* Read-only form styling */
.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

.form-control[readonly]:focus {
    border-color: #dee2e6;
    box-shadow: none;
}
</style>

@endsection
