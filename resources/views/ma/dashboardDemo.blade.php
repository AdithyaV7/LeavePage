<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Leave Management</title>

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
                <!--<img src="https://via.placeholder.com/40x40/007bff/ffffff?text=MA" alt="MA Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
                <span class="brand-text font-weight-light">MA Dashboard</span>
            </a>

            <!-- Sidebar -->
            @php 
                 $pageName = 'Dashboard';
            @endphp
            @include('ma.partials.sidebar')	
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Info boxes -->
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Applications</span>
                                    <span class="info-box-number">{{ $applications->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Review</span>
                                    <span class="info-box-number">{{ $applications->where('remark', null)->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Reviewed</span>
                                    <span class="info-box-number">{{ $applications->where('remark', '!=', null)->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Today</span>
                                    <span class="info-box-number">{{ $applications->where('applied_date', '>=', now()->startOfDay())->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Applications Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-list mr-2"></i>
                                        Applications Pending Review
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if($applications->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Reference No</th>
                                                        <th>Employee No</th>
                                                        <th>Name</th>
                                                        <th>Department</th>
                                                        <th>Faculty</th>
                                                        <th>Leave Type</th>
                                                        <th>Applied Date</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($applications as $application)
                                                        <tr>
                                                            <td>
                                                                <span class="badge badge-primary">{{ $application->reference_no }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-secondary">{{ $application->empno }}</span>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $application->name_with_initials }}</strong>
                                                            </td>
                                                            <td>{{ $application->department }}</td>
                                                            <td>{{ $application->faculty }}</td>
                                                            <td>
                                                                <span class="badge badge-info">{{ $application->leave_type }}</span>
                                                            </td>
                                                            <td>
                                                                {{ \Carbon\Carbon::parse($application->applied_date)->format('M d, Y') }}
                                                                <br>
                                                                <small class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($application->applied_date)->format('h:i A') }}
                                                                </small>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $status = $application->status;
                                                                    $badgeClass = 'badge-primary'; // default blue
                                                                    if (strtolower($status) === 'processing ma') {
                                                                        $badgeClass = 'badge-warning'; // yellow
                                                                    } elseif (strtolower($status) === 'vc checked') {
                                                                        $badgeClass = 'badge-success'; // green
                                                                    } elseif (strtolower($status) === 'returned') {
                                                                        $badgeClass = 'badge-danger'; // red
                                                                    }
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('ma.show', $application->id) }}" 
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No Applications Pending</h5>
                                            <p class="text-muted">There are no applications currently waiting for review.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
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
</body>
</html> 