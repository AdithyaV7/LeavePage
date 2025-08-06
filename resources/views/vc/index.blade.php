@extends('layouts.screen1')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-crown me-2 icon-gold"></i>Applications for VC Recommendation
            </h2>
        </div>
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
    <div class="card">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-list me-2"></i>Applications List
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reference No</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Faculty</th>
                        <th>Applied Date</th>
                        <th>Leave Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td><span class="fw-semibold text-maroon">{{ $app->reference_no }}</span></td>
                            <td><strong>{{ $app->name_with_initials }}</strong></td>
                            <td>{{ $app->department }}</td>
                            <td>{{ $app->faculty }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->applied_date)->format('Y-m-d') }}</td>
                            <td><span class="badge badge-gold">{{ $app->leave_type }}</span></td>
                            <td><span class="badge status-pending">{{ $app->status }}</span></td>
                            <td>
                                <a href="{{ route('vc.show', $app->id) }}" class="btn btn-sm btn-outline-maroon">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 