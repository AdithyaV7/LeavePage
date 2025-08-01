<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Status - Leave Management</title>

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
            @php $pageName = 'Status' @endphp
            @include('ma.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Status Tracker</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('ma.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Status</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-stream mr-2"></i> Application Status Tracker
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Ref No.</th>
                                                    <th>Employee No</th>
                                                    <th>Name</th>
                                                    <th>Leave Type</th>
                                                    <th class="text-center">MA</th>
                                                    <th class="text-center">HOD</th>
                                                    <th class="text-center">Dean</th>
                                                    <th class="text-center">VC</th>
                                                    <th class="text-center">Final</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($statusApplications as $app)
                                                    @php
                                                        $stages = [
                                                            'ma' => 4,      // Processing MA
                                                            'hod' => 5,     // Processing HOD
                                                            'dean' => 6,    // Processing Dean
                                                            'vc' => 7,      // Processing VC
                                                            'final' => 8,   // VC Checked (Final)
                                                        ];
                                                        $current = $app->status_id;
                                                    @endphp
                                                    <tr>
                                                        <td><span class="badge badge-primary">{{ $app->reference_no }}</span></td>
                                                        <td><span class="badge badge-secondary">{{ $app->empno }}</span></td>
                                                        <td>{{ $app->name_with_initials }}</td>
                                                        <td><span class="badge badge-info">{{ $app->leave_type }}</span></td>
                                                        @foreach($stages as $stage => $sid)
                                                            <td class="text-center">
                                                                @if($current > $sid)
                                                                    <span class="rounded-circle bg-success text-white p-1"><i class="fas fa-check"></i></span>
                                                                @elseif($current == $sid)
                                                                    <span class="rounded-circle bg-warning text-white p-1"><i class="fas fa-spinner fa-spin"></i></span>
                                                                @else
                                                                    <span class="rounded-circle bg-secondary text-white p-1"><i class="fas fa-circle"></i></span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center text-muted">No applications in this stage.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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