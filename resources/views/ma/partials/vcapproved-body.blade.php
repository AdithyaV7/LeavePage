<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-check-double mr-2"></i>
            VC Checked Applications
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
                                    
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $application->status }}</span>
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
                <h5 class="text-muted">No VC Checked Applications</h5>
                <p class="text-muted">There are no VC Checked applications currently.</p>
            </div>
        @endif
    </div>
</div> 