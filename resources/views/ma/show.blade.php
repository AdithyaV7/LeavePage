<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Application Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('ma.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('ma.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">MA Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php($pageName = 'Applications')
            @include('ma.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
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
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    <span class="text-muted">{{ basename($doc) }}</span>
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
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    <span class="text-muted">{{ basename($letter) }}</span>
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

                    <!-- Previous Remarks -->
                    @if($application->remark)
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white fw-semibold">
                            <i class="fas fa-comments me-2"></i>Previous Remarks
                        </div>
                        <div class="card-body">
                            <div class="remarks-container">
                                {!! nl2br(e($application->remark)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Section -->
                    @if(empty($readonly) || !$readonly)
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
                                    <i class="fas fa-check me-2"></i>Forward
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
                    @else
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
                    </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('ma.partials.footer')
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

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

.remarks-container {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    max-height: 200px;
    overflow-y: auto;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    line-height: 1.5;
    white-space: pre-wrap;
}

.remarks-container::-webkit-scrollbar {
    width: 6px;
}

.remarks-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.remarks-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.remarks-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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
</style>
</body>
</html>