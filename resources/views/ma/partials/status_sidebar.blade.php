<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-stream mr-2"></i> Application Status Tracker
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Ref No.</th>
                        <th>Name</th>
                        <th>Leave Type</th>
                        <th class="text-center">MA</th>
                        <th class="text-center">Dean</th>
                        <th class="text-center">VC</th>
                        <th class="text-center">Final</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statusApplications as $app)
                        @php
                            // Map your status_id to stages
                            $stages = [
                                'ma' => 4,
                                'dean' => 5,
                                'vc' => 6,
                                'final' => 8, // Adjust if needed
                            ];
                            $current = $app->status_id;
                        @endphp
                        <tr>
                            <td><span class="badge badge-primary">{{ $app->reference_no }}</span></td>
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
                            <td colspan="7" class="text-center text-muted">No applications in this stage.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div> 