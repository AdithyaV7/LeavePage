<!-- Personal Details -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="fas fa-user me-2"></i>Personal Details
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Employee No</label>
                <input type="text" class="form-control" value="{{ $application->empno }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Name with Initials</label>
                <input type="text" class="form-control" value="{{ $application->name_with_initials }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Names Denoted by Initials</label>
                <input type="text" class="form-control" value="{{ $application->names_denoted_by_initials }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Department</label>
                <input type="text" class="form-control" value="{{ $application->department }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Faculty</label>
                <input type="text" class="form-control" value="{{ $application->faculty }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Designation</label>
                <input type="text" class="form-control" value="{{ $application->designation }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Mobile</label>
                <input type="text" class="form-control" value="{{ $application->mobile }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">NIC</label>
                <input type="text" class="form-control" value="{{ $application->nic }}" readonly>
            </div>
        </div>
    </div>
</div>

<!-- Leave Details -->
<div class="card mb-4">
    <div class="card-header bg-info text-white fw-semibold">
        <i class="fas fa-calendar me-2"></i>Leave Details
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Leave Type</label>
                <input type="text" class="form-control" value="{{ $application->leave_type_name }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Start Date</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($application->from_date)->format('F d, Y') }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">End Date</label>
                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($application->to_date)->format('F d, Y') }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Duration (Days)</label>
                <input type="text" class="form-control" value="{{ $application->duration }} days" readonly>
            </div>
        </div>
    </div>
</div>

<!-- Leave request documents -->
@if(isset($travelDetails) && count($travelDetails) > 0)
<div class="card mb-4">
    <div class="card-header bg-warning text-dark fw-semibold">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-plane me-2"></i>Details
            </div>
            <small class="badge bg-dark">{{ count($travelDetails) }} destination(s)</small>
        </div>
    </div>
    <div class="card-body">
        @foreach($travelDetails as $index => $detail)
            <div class="travel-detail-entry {{ $index > 0 ? 'border-top pt-3 mt-3' : '' }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Details</label>
                        <textarea class="form-control" rows="3" readonly>{{ $detail->detail }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Country</label>
                        <input type="text" class="form-control" value="{{ $detail->country }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Start date</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($detail->travel_from_date)->format('F d, Y') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">End date</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($detail->travel_to_date)->format('F d, Y') }}" readonly>
                    </div>
                    @if(!empty($detail->documents) && count($detail->documents) > 0)
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Documents</label>
                        <div class="travel-documents-container">
                            @foreach($detail->documents as $doc)
                                <div class="document-item mb-3 p-3 border rounded">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i>
                                            <div>
                                                <span class="fw-semibold">{{ basename($doc) }}</span>
                                                <br>
                                                <small class="text-muted">Document</small>
                                            </div>
                                        </div>
                                        <div class="document-actions">
                                            <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                                <i class="fas fa-external-link-alt me-1"></i>Open
                                            </a>
                                            <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" download="{{ basename($doc) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>

                                    <div class="pdf-frame-container">
                                        <iframe src="{{ asset('storage/' . ltrim($doc, '/')) }}"
                                                class="pdf-frame"
                                                frameborder="0">
                                            <p>Your browser does not support this document format.
                                               <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank">Download the document</a>.
                                            </p>
                                        </iframe>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Documents</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No documents uploaded for this destination.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
<!-- No Leave request documents -->
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-semibold">
        <i class="fas fa-plane me-2"></i>Details
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No details provided for this leave application.
        </div>
    </div>
</div>
@endif

<!-- Documents -->
<div class="card mb-4">
    <div class="card-header bg-success text-white fw-semibold">
        <i class="fas fa-file me-2"></i>Documents
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Consent Letters</label>
                @if(!empty($application->consent_letters) && count($application->consent_letters))
                    @foreach($application->consent_letters as $letter)
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <span class="text-muted">{{ basename($letter) }}</span>
                                </div>
                                <div class="document-actions">
                                    <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                        <i class="fas fa-external-link-alt me-1"></i>Open
                                    </a>
                                    <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" download="{{ basename($letter) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                            </div>
                            <div class="pdf-frame-container">
                                <iframe src="{{ asset('storage/' . ltrim($letter, '/')) }}"
                                        class="pdf-frame"
                                        frameborder="0">
                                    <p>Your browser does not support PDFs.
                                       <a href="{{ asset('storage/' . ltrim($letter, '/')) }}" target="_blank">Download the PDF</a>.
                                    </p>
                                </iframe>
                            </div>
                        </div>
                    @endforeach
                @else
                    <span class="text-muted">No document uploaded</span>
                @endif
            </div>
            @if(!empty($application->leave_documents) && count($application->leave_documents))
            <div class="col-md-6">
                <label class="form-label fw-semibold">Leave Request Documents</label>
                @foreach($application->leave_documents as $doc)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span class="text-muted">{{ basename($doc) }}</span>
                            </div>
                            <div class="document-actions">
                                <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank" class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fas fa-external-link-alt me-1"></i>Open
                                </a>
                                <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" download="{{ basename($doc) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        <div class="pdf-frame-container">
                            <iframe src="{{ asset('storage/' . ltrim($doc, '/')) }}"
                                    class="pdf-frame"
                                    frameborder="0">
                                <p>Your browser does not support PDFs.
                                   <a href="{{ asset('storage/' . ltrim($doc, '/')) }}" target="_blank">Download the PDF</a>.
                                </p>
                            </iframe>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Previous Remarks -->
@if($application->remark)
<div class="card mb-4">
    <div class="card-header bg-secondary text-white fw-semibold">
        <i class="fas fa-comments me-2"></i>Previous Remarks
    </div>
    <div class="card-body">
        <div class="remarks-container">
            {!! nl2br(e($application->remark)) !!}
        </div>
    </div>
</div>
@endif
