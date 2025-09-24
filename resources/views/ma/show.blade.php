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
                        <a href="{{ route('ma.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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



                    @include('ma.partials.application-details')



                    <!-- Action Section -->
                    @if(empty($readonly) || !$readonly)
                    <div class="card">
                        <div class="card-header bg-dark text-white fw-semibold">
                            <i class="fas fa-tasks me-2"></i>Review Actions
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="actionRemark" class="form-label fw-semibold">Remarks</label>
                                <textarea class="form-control" id="actionRemark" name="remark" rows="3"
                                          placeholder="Add any comments or remarks"></textarea>
                                <div id="remarkError" class="form-text text-danger" style="display: none;">
                                    Remarks are required when returning an application.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-start">
                                <form id="returnForm" action="{{ route('ma.return', $application->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" id="returnRemarkInput" name="remark" value="">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-undo me-2"></i>Return to User
                                    </button>
                                </form>

                                <div class="text-right">
                                    <form id="approveForm" action="{{ route('ma.approve', $application->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                        <button type="submit" class="btn btn-success" {{ empty($departmentHead) ? 'disabled' : '' }}>
                                            <i class="fas fa-check me-2"></i>Forward
                                        </button>
                                    </form>

                                    @if(isset($departmentHead))
                                    <div class="card mt-2" style="min-width: 260px;">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center">
                                                <strong>Forward to,&nbsp;</strong>
                                                <div>
                                                    <div class="fw-semibold">{{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? '' }}</div>
                                                    <div class="text-muted small">{{ $departmentHead->head_position ?? '' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="card mt-2 border-warning" style="min-width: 260px;">
                                        <div class="card-body py-2">
                                            <div class="text-danger">
                                                <strong>No active Department Head</strong>
                                                <div class="text-muted small">Forwarding is disabled until a head is active.</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
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
// Form validation and submission handling
document.getElementById('approveForm').addEventListener('submit', function(e) {
    // Get the remark value and set it to the hidden input
    const remarkValue = document.getElementById('actionRemark').value.trim();
    document.getElementById('approveRemarkInput').value = remarkValue;

    // Clear any previous error highlighting
    clearRemarkError();

    // Confirm action
    if (!confirm('Are you sure you want to forward this application to HOD?')) {
        e.preventDefault();
    }
});

document.getElementById('returnForm').addEventListener('submit', function(e) {
    const remarkValue = document.getElementById('actionRemark').value.trim();

    // Validate that remarks are provided for return action
    if (!remarkValue) {
        e.preventDefault();
        showRemarkError();
        return;
    }

    // Set the remark value to the hidden input
    document.getElementById('returnRemarkInput').value = remarkValue;

    // Clear any previous error highlighting
    clearRemarkError();

    // Confirm action
    if (!confirm('Are you sure you want to return this application to the user?')) {
        e.preventDefault();
    }
});

// Helper functions for error handling
function showRemarkError() {
    const remarkTextarea = document.getElementById('actionRemark');
    const errorDiv = document.getElementById('remarkError');

    // Highlight the textarea
    remarkTextarea.classList.add('is-invalid');
    remarkTextarea.style.borderColor = '#dc3545';
    remarkTextarea.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';

    // Add shake animation
    remarkTextarea.classList.add('shake-animation');
    setTimeout(() => {
        remarkTextarea.classList.remove('shake-animation');
    }, 500);

    // Show error message
    errorDiv.style.display = 'block';

    // Focus on the textarea
    remarkTextarea.focus();

    // Show alert
    alert('Please provide remarks when returning an application.');
}

function clearRemarkError() {
    const remarkTextarea = document.getElementById('actionRemark');
    const errorDiv = document.getElementById('remarkError');

    // Remove highlighting
    remarkTextarea.classList.remove('is-invalid');
    remarkTextarea.style.borderColor = '';
    remarkTextarea.style.boxShadow = '';

    // Hide error message
    errorDiv.style.display = 'none';
}

// Clear error highlighting when user starts typing
document.getElementById('actionRemark').addEventListener('input', function() {
    if (this.value.trim()) {
        clearRemarkError();
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

/* Error highlighting for remarks */
.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.form-control.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Animation for error highlighting */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.shake-animation {
    animation: shake 0.5s ease-in-out;
}
</style>
</body>
</html>