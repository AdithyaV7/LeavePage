<!-- resources/views/leaves/index.blade.php -->

@extends('layouts.app')

@section('content')
<style>

    .text-warning {
    --bs-text-opacity: 1;
    color: rgb(16 16 15) !important;
    }
    
    .active-draft-alert {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .active-draft-item {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
        border: 2px solid !important;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3) !important;
        animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
        0% { box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3); }
        50% { box-shadow: 0 6px 12px rgba(255, 193, 7, 0.5); }
        100% { box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3); }
    }

    .draft-action-buttons {
        margin: 2rem 0;
    }

    .draft-action-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .draft-action-item:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .draft-action-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        font-size: 2.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .draft-action-item:first-child .draft-action-icon {
        background: #fff3cd;
        border: 2px solid #ffc107;
        color: #856404;
    }

    .draft-action-item:first-child:hover .draft-action-icon {
        background: #ffc107;
        color: #212529;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .draft-action-item:last-child .draft-action-icon {
        background: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
    }

    .draft-action-item:last-child:hover .draft-action-icon {
        background: #dc3545;
        color: white;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 768px) {
        .draft-action-buttons {
            flex-direction: column;
            align-items: center;
            gap: 2rem !important;
            margin: 1rem 0;
        }

        .draft-action-item {
            margin-bottom: 1rem;
        }
    }
</style>
    <div class="container py-4">
        <h3 class="mb-4 fw-bold text-center text-maroon dashboard-header">
            <i class="fas fa-file-alt me-2 icon-gold"></i>New Leave Applications
        </h3>

        <!-- Flash Messages -->
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- New Application -->
        <div class="mb-4 text-center">
            @if($hasActiveDraft)
                <!-- Show draft continuation options when user has an active draft -->
                @php
                    $activeDraft = $drafts->where('form_status', 1)->first();
                @endphp
                @if($activeDraft)
                    <div class="text-center mb-3">
                        <p class="text-muted mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            You have an active draft. Choose an option below:
                        </p>
                    </div>
                    <div class="d-flex justify-content-center gap-4 draft-action-buttons flex-wrap">
                        <a href="{{ route('leaves.create', ['id' => $activeDraft->id]) }}" class="d-inline-block text-decoration-none draft-action-item">
                            <div class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div class="text-center">
                                <span class="fw-semibold text-maroon">Continue Draft</span><br>
                                <small class="text-muted">{{ $activeDraft->reference_no ?? 'N/A' }}</small>
                            </div>
                        </a>

                        <form action="{{ route('leaves.destroy', $activeDraft->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn p-0 border-0 bg-transparent draft-action-item" onclick="return confirm('Are you sure you want to delete this draft and start a new application?')">
                                <div class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="bi bi-trash"></i>
                                </div>
                                <div class="text-center">
                                    <span class="fw-semibold text-maroon">Delete Draft & Start New</span>
                                </div>
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <!-- Show new application button when no active draft -->
                <a href="{{ route('leaves.draft.create') }}" class="d-inline-block text-decoration-none" id="new-application-button">
                    <div class="new-app-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                        <i class="bi bi-journal-plus"></i>
                    </div>
                    <div><span class="fw-semibold text-maroon">Start a New Application</span></div>
                </a>
            @endif
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
                        @php
                            // Check if this is an active draft (form_status = 1, status_id = 3)
                            $isActiveDraft = $draft->form_status == 1 &&
                                           DB::table('leave_details')
                                             ->where('id', $draft->id)
                                             ->where('status_id', 3)
                                             ->exists();
                        @endphp

                        <div class="bg-light border-0 rounded-2 p-3 mb-2 d-flex justify-content-between align-items-center shadow-sm hover-gold {{ $isActiveDraft ? 'active-draft-item' : '' }}">
                            <a href="{{ route('leaves.create', ['id' => $draft->id]) }}" class="text-decoration-none text-dark">
                                @if($isActiveDraft)
                                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                                    <strong class="text-warning">Active Draft - </strong>
                                @else
                                    <i class="bi bi-pencil-square me-2 text-maroon"></i>
                                @endif
                                <strong>Ref No:</strong> {{ $draft->reference_no ?? 'N/A' }}<br>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($draft->updated_at)->addHours(5)->addMinutes(30)->format('Y-m-d h:i a') }}</span>
                                @if($isActiveDraft)
                                    <br><small class="text-warning"><strong>Click to continue this draft</strong></small>
                                @endif
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
