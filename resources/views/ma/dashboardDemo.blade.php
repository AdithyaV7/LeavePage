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

    <!-- Custom CSS for Search and Sort -->
    <style>
        .search-sort-section {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1rem;
            margin-bottom: 0;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .search-results-info {
            font-size: 0.9rem;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
    </style>
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
                                        <span class="badge badge-primary ml-2">{{ $applications->count() }}</span>
                                    </h3>
                                    <div class="card-tools">
                                        <span class="text-muted">
                                            <i class="fas fa-clock mr-1"></i>
                                            Last updated: {{ now()->format('M d, Y h:i A') }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Search and Sort Controls -->
                                <div class="card-body border-bottom search-sort-section">
                                    <form method="GET" action="{{ route('ma.dashboard') }}" class="row align-items-end">
                                        <div class="col-md-4">
                                            <label for="search" class="form-label">Search Applications</label>
                                            <div class="input-group">
                                                <input type="text"
                                                       class="form-control"
                                                       id="search"
                                                       name="search"
                                                       value="{{ $search ?? '' }}"
                                                       placeholder="Search by reference, employee, name, department...">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="submit">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="sort_by" class="form-label">Sort By</label>
                                            <select class="form-control" id="sort_by" name="sort_by">
                                                <option value="applied_date" {{ ($sortBy ?? 'applied_date') == 'applied_date' ? 'selected' : '' }}>Applied Date</option>
                                                <option value="reference_no" {{ ($sortBy ?? '') == 'reference_no' ? 'selected' : '' }}>Reference No</option>
                                                <option value="empno" {{ ($sortBy ?? '') == 'empno' ? 'selected' : '' }}>Employee No</option>
                                                <option value="name" {{ ($sortBy ?? '') == 'name' ? 'selected' : '' }}>Name</option>
                                                <option value="department" {{ ($sortBy ?? '') == 'department' ? 'selected' : '' }}>Department</option>
                                                <option value="faculty" {{ ($sortBy ?? '') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                                                <option value="leave_type" {{ ($sortBy ?? '') == 'leave_type' ? 'selected' : '' }}>Leave Type</option>
                                                <option value="status" {{ ($sortBy ?? '') == 'status' ? 'selected' : '' }}>Status</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="sort_order" class="form-label">Order</label>
                                            <select class="form-control" id="sort_order" name="sort_order">
                                                <option value="desc" {{ ($sortOrder ?? 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ ($sortOrder ?? '') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-filter"></i> Apply Filters
                                            </button>
                                            <a href="{{ route('ma.dashboard') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Clear
                                            </a>
                                        </div>
                                    </form>

                                    <!-- Quick Search Shortcuts -->
                                    <div class="mt-3">
                                        <small class="text-muted">Quick filters:</small>
                                        <div class="btn-group btn-group-sm ml-2" role="group">
                                            <a href="{{ route('ma.dashboard', ['search' => 'Processing MA']) }}"
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-clock"></i> Processing MA
                                            </a>
                                            <a href="{{ route('ma.dashboard', ['sort_by' => 'applied_date', 'sort_order' => 'desc']) }}"
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-calendar"></i> Latest First
                                            </a>
                                            <a href="{{ route('ma.dashboard', ['sort_by' => 'name', 'sort_order' => 'asc']) }}"
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-sort-alpha-down"></i> Name A-Z
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Search Results Summary -->
                                    @if($search || ($sortBy ?? 'applied_date') != 'applied_date' || ($sortOrder ?? 'desc') != 'desc')
                                        <div class="alert alert-info search-results-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Filters Applied:</strong>
                                            @if($search)
                                                Search: "<em>{{ $search }}</em>"
                                            @endif
                                            @if(($sortBy ?? 'applied_date') != 'applied_date' || ($sortOrder ?? 'desc') != 'desc')
                                                | Sorted by: <em>{{ ucwords(str_replace('_', ' ', $sortBy ?? 'applied_date')) }}</em>
                                                ({{ ($sortOrder ?? 'desc') == 'asc' ? 'Ascending' : 'Descending' }})
                                            @endif
                                            | Showing {{ $applications->count() }} result(s)
                                        </div>
                                    @endif

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

    <!-- Custom JavaScript for Search and Sort -->
    <script>
        $(document).ready(function() {
            // Auto-submit form when sort options change
            $('#sort_by, #sort_order').change(function() {
                $(this).closest('form').submit();
            });

            // Enter key search
            $('#search').keypress(function(e) {
                if (e.which == 13) {
                    $(this).closest('form').submit();
                    return false;
                }
            });

            // Clear search when clear button is clicked
            $('.btn-secondary').click(function(e) {
                e.preventDefault();
                $('#search').val('');
                $('#sort_by').val('applied_date');
                $('#sort_order').val('desc');
                window.location.href = '{{ route("ma.dashboard") }}';
            });
        });
    </script>
</body>
</html> 