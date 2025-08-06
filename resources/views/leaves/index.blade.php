<!-- resources/views/leaves/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4 fw-bold text-center text-maroon dashboard-header">
            <i class="fas fa-file-alt me-2 icon-gold"></i>New Leave Applications
        </h3>

        <!-- New Application -->
        <div class="mb-4 text-center">
            <a href="{{ route('leaves.draft.create') }}" class="d-inline-block text-decoration-none" id="new-application-button">
                <div class="new-app-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <div><span class="fw-semibold text-maroon">Start a New Application</span></div>
            </a>
        </div>

        <!-- Drafts -->
        <div class="mb-4">
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-maroon">
                        <i class="fas fa-edit me-2 icon-gold"></i>Drafts
                    </h5>
                    <button class="btn btn-outline-maroon btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#draftsCollapse" aria-expanded="false" aria-controls="draftsCollapse">
                        <i class="bi bi-plus" id="toggleIcon"></i>
                    </button>
                </div>
                <div class="collapse show card-body pt-2 pb-0 px-3" id="draftsCollapse">
                    @forelse ($drafts as $draft)
                        <div class="bg-light border-0 rounded-2 p-3 mb-2 d-flex justify-content-between align-items-center shadow-sm hover-gold">
                            <a href="{{ route('leaves.create', ['id' => $draft->id]) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-pencil-square me-2 text-maroon"></i>
                                <strong>Ref No:</strong> {{ $draft->reference_no ?? 'N/A' }}<br>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($draft->updated_at)->addHours(5)->addMinutes(30)->format('Y-m-d h:i a') }}</span>
                            </a>
                            <form action="{{ route('leaves.destroy', $draft->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this draft?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted">No drafts available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Previous Leaves -->
        <div class="mb-4">
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-maroon">
                        <i class="fas fa-history me-2 icon-gold"></i>Previous Leaves
                    </h5>
                </div>
                <div class="card-body pt-2 pb-0 px-3">
                    <table class="table table-striped table-bordered bg-white align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Applied Date</th>
                                <th>Ref No</th>
                                <th>Leave Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($previousLeaves as $leave)
                                <tr
                                    @if ($leave->form_status == 3)
                                        onclick="window.location='{{ route('leaves.create', ['id' => $leave->id]) }}'"
                                        style="cursor: pointer; background-color: #fff8e1;"
                                        title="Click to edit returned form"
                                    @endif
                                    class="@if($leave->form_status == 3) table-warning @endif hoverable-row"
                                >
                                    <td>{{ \Carbon\Carbon::parse($leave->applied_date)->addHours(5)->addMinutes(30)->format('d/m/Y') }}</td>
                                    <td>{{ $leave->reference_no }}</td>
                                    <td>{{ $leave->leave_type }}</td>
                                    <td>
                                        {{ $leave->status }}
                                        @if ($leave->form_status == 3)
                                            <span class="badge bg-warning text-dark ms-2">Returned</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No previous leaves found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
.card-header {
    background: #f8fafc !important;
}
.hoverable-row:hover {
    background-color: #fbeed7 !important;
    transition: background 0.2s;
}
.table th {
    font-weight: 600;
    color: #2d2d2d;
    background: #f8fafc;
}
.table td {
    color: #3a3a3a;
}
.new-app-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #f0f6ff;
    border: 2px solid #0d6efd;
    font-size: 2.5rem;
    color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13,110,253,0.08);
}
</style>

<script>
    const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');
    const toggleIcon = document.getElementById('toggleIcon');
    const draftsCollapse = document.getElementById('draftsCollapse');

    draftsCollapse.addEventListener('shown.bs.collapse', () => {
        toggleIcon.classList.replace('bi-plus', 'bi-dash');
    });

    draftsCollapse.addEventListener('hidden.bs.collapse', () => {
        toggleIcon.classList.replace('bi-dash', 'bi-plus');
    });
</script>
