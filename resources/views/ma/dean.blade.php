<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Dean Processing</title>
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
                            <h2 class="mb-0 fw-bold">Application Review - Dean Processing</h2>
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

                    @if($readonly)
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-eye"></i> This application is currently being processed by Dean. You have read-only access.
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
