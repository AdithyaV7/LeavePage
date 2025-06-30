@extends('layouts.screen1')

@section('content')
<div class="container mt-4">
    <h2>Applications for HOD Review</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Reference No</th>
                <th>Name</th>
                <th>Department</th>
                <th>Faculty</th>
                <th>Leave Type</th>
                <th>Applied Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
                <tr>
                    <td>{{ $app->reference_no }}</td>
                    <td>{{ $app->name_with_initials }}</td>
                    <td>{{ $app->department }}</td>
                    <td>{{ $app->faculty }}</td>
                    <td>{{ $app->leave_type }}</td>
                    <td>{{ $app->applied_date }}</td>
                    <td>{{ $app->status }}</td>
                    <td>
                        <a href="{{ route('hod.show', $app->id) }}" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 