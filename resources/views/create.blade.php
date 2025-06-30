@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Show remark if returned -->
    @isset($remark)
        <div class="alert alert-warning fw-semibold">Returned with remark: {{ $remark }}</div>
    @endisset
    <h3 class="text-center mb-4 fw-bold">
        @if(isset($leave))
            Edit Application for Conference/ Seminar/ Training and Workshop
        @else
            Application for Conference/ Seminar/ Training and Workshop
        @endif
    </h3>

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
        @endif

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">Personal Details</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee No</label>
                    <input type="text" name="empno" class="form-control" value="{{ $user->empno }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">NIC</label>
                    <input type="text" name="nic" class="form-control" value="{{ $user->nic }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Name with Initials</label>
                    <input type="text" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Names Denoted by Initials</label>
                    <input type="text" class="form-control" value="{{ $user->names_denoted_by_initials }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Faculty</label>
                    <input type="text" class="form-control" value="{{ $user->faculty }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control" value="{{ $user->designation }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mobile</label>
                    <input type="text" class="form-control" value="{{ $user->mobile }}" readonly>
                </div>
            </div>
        </div>

        <!-- Previous Leaves -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">Previous Leaves</div>
            <div class="card-body">
                <p><strong>Academic Year: {{ date('Y') }}</strong></p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Duration (Days)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($previousLeaves as $leave)
                            <tr>
                                <td>{{ $leave->leave_type}}</td>
                                <td>{{ $leave->from_date }}</td>
                                <td>{{ $leave->to_date }}</td>
                                <td>{{ $leave->duration }}</td>
                                <td>{{ $leave->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No previous approved leaves in academic year {{ date('Y') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Apply New Leave -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">Applying New Leave</div>
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Required Leave Type *</label>
                    <select name="leave_type" class="form-select">
                        <option selected disabled value="">Select</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ isset($leave) && $leave->leave_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="from_date" class="form-control" id="fromDate" value="{{ isset($leave) ? $leave->from_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="to_date" class="form-control" id="toDate" value="{{ isset($leave) ? $leave->to_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Duration (Days)</label>
                    <input type="number" name="duration" class="form-control" id="duration" value="{{ isset($leave) ? $leave->duration : '' }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Upload Leave Request Document *</label>
                    <input type="file" name="leave_document" class="form-control mb-3">
                    @if(isset($leave) && $leave->leave_document)
                        <small class="form-text text-muted">Current file: {{ basename($leave->leave_document) }} (Upload new file to replace)</small>
                    @endif
                </div>

                <div class="col-md-12">
                    <label class="form-label">Upload Consent Letter *</label>
                    <input type="file" name="consent_letter" class="form-control">
                    @if(isset($leave) && $leave->consent_letter)
                        <small class="form-text text-muted">Current file: {{ basename($leave->consent_letter) }} (Upload new file to replace)</small>
                    @endif
                    <small class="form-text">
                        Download sample: 
                        <a href="{{ asset('sample-consent-letter.pdf') }}" target="_blank" class="text-success fw-semibold">Download the Consent Letter</a>
                    </small>
                </div>

                <div class="form-check mt-3">
                    <input type="checkbox" name="confirm" class="form-check-input">
                    <label class="form-check-label">I confirm that the above details are true and correct.</label>
                </div>
            </div>
        </div>

        <!-- Buttons + Hidden Status Field -->
        <input type="hidden" name="form_status" id="formStatus" value="4">

        <div class="text-center">
            <button type="submit" class="btn btn-success px-4" onclick="setFormStatus(2)">Submit</button>
            <button type="submit" class="btn btn-warning px-4" onclick="setFormStatus(1)">Save Draft</button>
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary px-4">Cancel</a>
        </div>
    </form>
</div>

<script>
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("fromDate").setAttribute('min', today);
    document.getElementById("toDate").setAttribute('min', today);

    function setFormStatus(status) {
        document.getElementById("formStatus").value = status;
    }

    // Calculate duration excluding weekends
    document.getElementById("fromDate").addEventListener('change', calculateDuration);
    document.getElementById("toDate").addEventListener('change', calculateDuration);

    const previousLeaves = @json($previousLeaves);

    function calculateDuration() {
        const fromVal = document.getElementById("fromDate").value;
        const toVal = document.getElementById("toDate").value;

        if (!fromVal || !toVal) return;

        const from = new Date(fromVal);
        const to = new Date(toVal);

        if (to < from) {
            document.getElementById("duration").value = '';
            return;
        }

        for (let leave of previousLeaves) {
            let prevFrom = new Date(leave.from_date);
            let prevTo = new Date(leave.to_date);

            if ((from >= prevFrom && from <= prevTo) || (to >= prevFrom && to <= prevTo)) {
                alert("Selected range overlaps with an already approved leave.");
                document.getElementById("fromDate").value = '';
                document.getElementById("toDate").value = '';
                document.getElementById("duration").value = '';
                return;
            }
        }

        let count = 0;
        let current = new Date(from);
        while (current <= to) {
            if (current.getDay() !== 0 && current.getDay() !== 6) count++;
            current.setDate(current.getDate() + 1);
        }
        document.getElementById("duration").value = count;
    }

    // Calculate duration on page load if editing
    @if(isset($leave))
        calculateDuration();
    @endif

    document.querySelector('button.btn-success').addEventListener('click', function(e) {
        // Set required for all fields
        document.querySelector('[name="leave_type"]').required = true;
        document.querySelector('[name="from_date"]').required = true;
        document.querySelector('[name="to_date"]').required = true;
        document.querySelector('[name="duration"]').required = true;
        document.querySelector('[name="leave_document"]').required = true;
        document.querySelector('[name="consent_letter"]').required = true;
        document.querySelector('[name="confirm"]').required = true;
    });

    document.querySelector('button.btn-warning').addEventListener('click', function(e) {
        // Remove required for all fields
        document.querySelector('[name="leave_type"]').required = false;
        document.querySelector('[name="from_date"]').required = false;
        document.querySelector('[name="to_date"]').required = false;
        document.querySelector('[name="duration"]').required = false;
        document.querySelector('[name="leave_document"]').required = false;
        document.querySelector('[name="consent_letter"]').required = false;
        document.querySelector('[name="confirm"]').required = false;
    });
</script>
@endsection


