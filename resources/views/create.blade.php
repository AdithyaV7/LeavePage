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
    <div class="alert alert-warning fw-semibold">
        Returned with remark:
        <pre class="mb-0">{{ $remark }}</pre>
    </div>
    @endisset
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">
                @if(isset($leave))
                    Application for Conference/ Seminar/ Training and Workshop
                @else
                    Application for Conference/ Seminar/ Training and Workshop
                @endif
            </h2>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
        @endif

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee No</label>
                        <input type="text" name="empno" class="form-control" value="{{ $user->empno }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIC</label>
                        <input type="text" name="nic" class="form-control" value="{{ $user->nic }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name with Initials</label>
                        <input type="text" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                    </div>
                                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Names Denoted by Initials</label>
                        <input type="text" class="form-control" value="{{ $user->names_denoted_by_initials }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Faculty</label>
                        <input type="text" class="form-control" value="{{ $user->faculty }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Designation</label>
                        <input type="text" class="form-control" value="{{ $user->designation }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile</label>
                        <input type="text" class="form-control" value="{{ $user->mobile }}" readonly>
                    </div>
            </div>
        </div>

        <!-- Previous Leaves -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white fw-semibold">
                <i class="fas fa-history me-2"></i>Previous Leaves
            </div>
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
            <div class="card-header bg-success text-white fw-semibold">
                <i class="fas fa-calendar-plus me-2"></i>Applying New Leave
            </div>
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Required Leave Type *</label>
                    <select name="leave_type" class="form-select">
                        <option selected disabled value="">Select</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ isset($leave) && $leave->leave_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date *</label>
                    <input type="date" name="from_date" class="form-control" id="fromDate" value="{{ isset($leave) ? $leave->from_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date *</label>
                    <input type="date" name="to_date" class="form-control" id="toDate" value="{{ isset($leave) ? $leave->to_date : '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days)</label>
                    <input type="number" name="duration" class="form-control" id="duration" value="{{ isset($leave) ? $leave->duration : '' }}">
                </div>

                <!-- Travel Details Card -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white fw-semibold">
                        <i class="fas fa-plane me-2"></i>Travel Details
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Please provide details pertaining to your travel request. Relevant documents should be attached for each travel entry.</p>
                    <div id="travel-entries">
                        @if(isset($travelDetails) && count($travelDetails) > 0)
                            @foreach($travelDetails as $index => $detail)
                                <div class="row g-3 travel-entry">
                                    <div class="col-md-3">
                                        <label for="travel_detail_{{ $index }}" class="form-label">Detail</label>
                                        <textarea class="form-control" id="travel_detail_{{ $index }}" name="travel_detail[]" rows="3" placeholder="Enter details about this travel">{{ $detail->detail }}</textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="travel_country_{{ $index }}" class="form-label">Country</label>
                                        <select class="form-select" id="travel_country_{{ $index }}" name="travel_country[]">
                                            <option value="">Select Country</option>
                                            @php
                                                $countries = [
                                                    'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria',
                                                    'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
                                                    'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia',
                                                    'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
                                                    'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
                                                    'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon',
                                                    'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                                                    'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel',
                                                    'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Laos',
                                                    'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi',
                                                    'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova',
                                                    'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands',
                                                    'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau',
                                                    'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania',
                                                    'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal',
                                                    'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea',
                                                    'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan',
                                                    'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
                                                    'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
                                                    'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
                                                ];
                                            @endphp
                                            @foreach($countries as $country)
                                                <option value="{{ $country }}" {{ $detail->country == $country ? 'selected' : '' }}>{{ $country }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="travel_from_datetime_{{ $index }}" class="form-label">Dates of travel (From)</label>
                                        <input type="date" class="form-control" id="travel_from_datetime_{{ $index }}" name="travel_from_datetime[]" value="{{ $detail->travel_from_date }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="travel_to_datetime_{{ $index }}" class="form-label">Dates of travel (To)</label>
                                        <input type="date" class="form-control" id="travel_to_datetime_{{ $index }}" name="travel_to_datetime[]" value="{{ $detail->travel_to_date }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Documents</label>
                                        <div class="d-flex flex-column">
                                            <input type="file" class="form-control form-control-sm mb-1 travel-document-input" id="travel_document_{{ $index }}" data-index="{{ $index }}">
                                            <button type="button" class="btn btn-outline-primary btn-sm upload-travel-document" data-index="{{ $index }}">Upload</button>
                                            <div class="travel-document-tags mt-1" id="travel_document_tags_{{ $index }}">
                                                @if($detail->documents)
                                                    @foreach($detail->documents as $doc)
                                                        <div class="badge bg-secondary me-1 mb-1 d-flex align-items-center">
                                                            <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="text-white text-decoration-none me-1">{{ basename($doc) }}</a>
                                                            <span class="ms-1 remove-travel-document" style="cursor: pointer;" data-file="{{ $doc }}" data-index="{{ $index }}">&times;</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <input type="hidden" name="travel_detail_id[{{ $index }}]" value="{{ $detail->id }}">
                                        </div>
                                    </div>
                                    @if($index > 0)
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-travel-entry" title="Remove">&minus;</button>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="row g-3 travel-entry">
                                <div class="col-md-3">
                                    <label for="travel_detail_0" class="form-label">Detail</label>
                                    <textarea class="form-control" id="travel_detail_0" name="travel_detail[]" rows="3" placeholder="Enter details about this travel"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="travel_country_0" class="form-label">Country</label>
                                    <select class="form-select" id="travel_country_0" name="travel_country[]">
                                        <option value="">Select Country</option>
                                        @php
                                            $countries = [
                                                'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia', 'Austria',
                                                'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan',
                                                'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cabo Verde', 'Cambodia',
                                                'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
                                                'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
                                                'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon',
                                                'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                                                'Haiti', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel',
                                                'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Laos',
                                                'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi',
                                                'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova',
                                                'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands',
                                                'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman', 'Pakistan', 'Palau',
                                                'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania',
                                                'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal',
                                                'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea',
                                                'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan',
                                                'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
                                                'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
                                                'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
                                            ];
                                        @endphp
                                        @foreach($countries as $country)
                                            <option value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="travel_from_datetime_0" class="form-label">Dates of travel (From)</label>
                                    <input type="date" class="form-control" id="travel_from_datetime_0" name="travel_from_datetime[]">
                                </div>
                                <div class="col-md-2">
                                    <label for="travel_to_datetime_0" class="form-label">Dates of travel (To)</label>
                                    <input type="date" class="form-control" id="travel_to_datetime_0" name="travel_to_datetime[]">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Documents</label>
                                    <div class="d-flex flex-column">
                                        <input type="file" class="form-control form-control-sm mb-1 travel-document-input" id="travel_document_0" data-index="0">
                                        <button type="button" class="btn btn-outline-primary btn-sm upload-travel-document" data-index="0">Upload</button>
                                        <div class="travel-document-tags mt-1" id="travel_document_tags_0"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-travel-entry">
                                <i class="fas fa-plus me-1"></i>Add Another Country/Date
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Travel Details JavaScript -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let entryIndex = {{ isset($travelDetails) ? count($travelDetails) : 1 }};
                        const countries = @json($countries);

                        // Add new travel entry
                        document.getElementById('add-travel-entry').addEventListener('click', function() {
                            const travelEntries = document.getElementById('travel-entries');
                            const row = document.createElement('div');
                            row.className = 'row g-3 travel-entry';
                            row.innerHTML = `
                                <div class="col-md-3">
                                    <label for="travel_detail_${entryIndex}" class="form-label">Detail</label>
                                    <textarea class="form-control" id="travel_detail_${entryIndex}" name="travel_detail[]" rows="3" placeholder="Enter details about this travel"></textarea>
                                </div>
                                <div class="col-md-3">
                                    <label for="travel_country_${entryIndex}" class="form-label">Country</label>
                                    <select class="form-select" id="travel_country_${entryIndex}" name="travel_country[]">
                                        <option value="">Select Country</option>
                                        ${countries.map(c => `<option value=\"${c}\">${c}</option>`).join('')}
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="travel_from_datetime_${entryIndex}" class="form-label">Dates of travel (From)</label>
                                    <input type="date" class="form-control" id="travel_from_datetime_${entryIndex}" name="travel_from_datetime[]">
                                </div>
                                <div class="col-md-2">
                                    <label for="travel_to_datetime_${entryIndex}" class="form-label">Dates of travel (To)</label>
                                    <input type="date" class="form-control" id="travel_to_datetime_${entryIndex}" name="travel_to_datetime[]">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Documents</label>
                                    <div class="d-flex flex-column">
                                        <input type="file" class="form-control form-control-sm mb-1 travel-document-input" id="travel_document_${entryIndex}" data-index="${entryIndex}">
                                        <button type="button" class="btn btn-outline-primary btn-sm upload-travel-document" data-index="${entryIndex}">Upload</button>
                                        <div class="travel-document-tags mt-1" id="travel_document_tags_${entryIndex}"></div>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-travel-entry" title="Remove">&minus;</button>
                                </div>
                            `;
                            travelEntries.appendChild(row);
                            entryIndex++;
                        });

                        // Remove travel entry
                        document.getElementById('travel-entries').addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-travel-entry')) {
                                e.target.closest('.travel-entry').remove();
                            }
                        });

                        // Upload travel document
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('upload-travel-document')) {
                                const index = e.target.getAttribute('data-index');
                                const input = document.getElementById(`travel_document_${index}`);
                                const files = input.files;

                                if (files.length === 0) {
                                    alert('Please select a file to upload');
                                    return;
                                }

                                // Get form data for the specific travel entry
                                const detail = document.getElementById(`travel_detail_${index}`).value;
                                const country = document.getElementById(`travel_country_${index}`).value;
                                const fromDate = document.getElementById(`travel_from_datetime_${index}`).value;
                                const toDate = document.getElementById(`travel_to_datetime_${index}`).value;

                                if (!detail || !country || !fromDate || !toDate) {
                                    alert('Please fill in all fields for this travel entry before uploading documents');
                                    return;
                                }

                                uploadTravelDocument(index, files[0], detail, country, fromDate, toDate);
                            }
                        });

                        // Function to upload travel document via AJAX
                        function uploadTravelDocument(index, file, detail, country, fromDate, toDate) {
                            const formData = new FormData();
                            formData.append('file', file);
                            formData.append('detail', detail);
                            formData.append('country', country);
                            formData.append('travel_from_date', fromDate);
                            formData.append('travel_to_date', toDate);
                            formData.append('index', index);
                            formData.append('_token', '{{ csrf_token() }}');

                            // If we have a leave ID, include it
                            @if(isset($leave))
                            formData.append('reference_no', '{{ $leave->reference_no }}');
                            @endif

                            // Show loading indicator
                            const uploadBtn = document.querySelector(`.upload-travel-document[data-index="${index}"]`);
                            const originalText = uploadBtn.innerHTML;
                            uploadBtn.innerHTML = 'Uploading...';
                            uploadBtn.disabled = true;

                            fetch("{{ route('leaves.uploadTravelDocument') }}", {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                uploadBtn.innerHTML = originalText;
                                uploadBtn.disabled = false;

                                if (data.success) {
                                    // Clear the file input
                                    document.getElementById(`travel_document_${index}`).value = '';

                                    // Update the document tags
                                    renderTravelDocumentTags(index, data.documents);

                                    // Store the detail ID for future uploads
                                    if (data.detail_id) {
                                        const hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = `travel_detail_id[${index}]`;
                                        hiddenInput.value = data.detail_id;
                                        document.getElementById(`travel_document_${index}`).parentNode.appendChild(hiddenInput);
                                    }
                                } else {
                                    alert(data.error || 'Upload failed');
                                }
                            })
                            .catch(error => {
                                uploadBtn.innerHTML = originalText;
                                uploadBtn.disabled = false;
                                console.error('Error:', error);
                                alert('An error occurred during upload');
                            });
                        }

                        // Function to render document tags
                        function renderTravelDocumentTags(index, documents) {
                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);
                            if (!tagsContainer) return;

                            tagsContainer.innerHTML = '';

                            if (documents && documents.length > 0) {
                                documents.forEach(doc => {
                                    const tag = document.createElement('div');
                                    tag.className = 'badge bg-secondary me-1 mb-1 d-flex align-items-center';

                                    const link = document.createElement('a');
                                    link.href = `/storage/${doc}`;
                                    link.target = '_blank';
                                    link.className = 'text-white text-decoration-none me-1';
                                    link.textContent = doc.split('/').pop();

                                    const removeBtn = document.createElement('span');
                                    removeBtn.className = 'ms-1 remove-travel-document';
                                    removeBtn.innerHTML = '&times;';
                                    removeBtn.style.cursor = 'pointer';
                                    removeBtn.setAttribute('data-file', doc);
                                    removeBtn.setAttribute('data-index', index);

                                    tag.appendChild(link);
                                    tag.appendChild(removeBtn);
                                    tagsContainer.appendChild(tag);
                                });
                            }
                        }


                        // Remove travel document
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-travel-document')) {
                                const file = e.target.getAttribute('data-file');
                                const index = e.target.getAttribute('data-index');

                                if (confirm('Are you sure you want to remove this document?')) {
                                    // Get the detail ID if it exists
                                    const detailIdInput = document.querySelector(`input[name="travel_detail_id[${index}]"]`);
                                    const detailId = detailIdInput ? detailIdInput.value : null;

                                    const formData = new FormData();
                                    formData.append('file', file);
                                    formData.append('index', index);
                                    formData.append('_token', '{{ csrf_token() }}');

                                    if (detailId) {
                                        formData.append('detail_id', detailId);
                                    }

                                    fetch("{{ route('leaves.removeTravelDocument') }}", {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            renderTravelDocumentTags(index, data.documents);
                                        } else {
                                            alert(data.error || 'Removal failed');
                                        }
                                    });
                                }
                            }
                        });
                    });
                </script>
<!-- End of need to add leave Details -->
                
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Leave Request Document <span class="text-danger">*</span></label>
                    <input type="file" id="leave_document_input" class="form-control mb-2" multiple>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="upload_leave_document_btn">Upload Leave Document(s)</button>
                    <div id="leave_document_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->leave_document))
                            @foreach($leave->leave_document as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="leave_document" data-file="{{ $file }}" aria-label="Delete"></button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div id="leave_document_required" class="text-danger small d-none">At least one document is required.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Consent Letter <span class="text-danger">*</span></label>
                    <input type="file" id="consent_letter_input" class="form-control mb-2" multiple>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-2" id="upload_consent_letter_btn">Upload Consent Letter(s)</button>
                    <div id="consent_letter_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->consent_letter))
                            @foreach($leave->consent_letter as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="consent_letter" data-file="{{ $file }}" aria-label="Delete"></button>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div id="consent_letter_required" class="text-danger small d-none">At least one consent letter is required.</div>
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
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary px-4" id="cancel-btn">Cancel</a>
        </div>
    </form>
@if(isset($leave) && $leave->form_status == 1)
    <form id="delete-draft-form" action="{{ route('leaves.destroy', $leave->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        document.getElementById('cancel-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('Are you sure you want to cancel and delete this draft?')) {
                document.getElementById('delete-draft-form').submit();
            }
        });
    </script>
@endif
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

    let leaveId = {{ isset($leave) ? $leave->id : 'null' }};
    // AJAX upload for Save Draft
    function uploadFilesAJAX(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;
        if (!leaveId) {
            alert('Please save the form as draft at least once before uploading files.');
            return;
        }
        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('type', type);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.uploadFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(tagsId, data.files, type);
                    input.value = '';
                } else {
                    alert(data.error || 'Upload failed');
                }
            });
        }
    }
    document.getElementById('upload_leave_document_btn').addEventListener('click', function() {
        uploadFilesAJAX('leave_document_input', 'leave_document', 'leave_document_tags');
    });
    document.getElementById('upload_consent_letter_btn').addEventListener('click', function() {
        uploadFilesAJAX('consent_letter_input', 'consent_letter', 'consent_letter_tags');
    });
    // Render file tags
    function renderFileTags(tagsId, files, type) {
        const tagsDiv = document.getElementById(tagsId);
        tagsDiv.innerHTML = '';
        files.forEach(file => {
            const span = document.createElement('span');
            span.className = 'badge bg-secondary me-1';
            span.innerHTML = `<a href="/storage/${file}" target="_blank" class="text-white text-decoration-none">${file.split('/').pop()}</a> <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="${type}" data-file="${file}" aria-label="Delete"></button>`;
            tagsDiv.appendChild(span);
        });
    }
    // AJAX delete for already uploaded files in drafts
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-file-btn')) {
            const type = e.target.getAttribute('data-type');
            const file = e.target.getAttribute('data-file');
            if (!leaveId) return;
            const formData = new FormData();
            formData.append('type', type);
            formData.append('file', file);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.deleteFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(type === 'leave_document' ? 'leave_document_tags' : 'consent_letter_tags', data.files, type);
                } else {
                    alert(data.error || 'Delete failed');
                }
            });
        }
    });

    // Disable submit if not draft and files missing
    function updateRequiredState() {
        const isDraft = document.getElementById('formStatus').value == 1;
        const submitBtn = document.querySelector('button.btn-success');
        if (!isDraft) {
            if (leaveDocumentFiles.length === 0) {
                document.getElementById('leave_document_required').classList.remove('d-none');
            } else {
                document.getElementById('leave_document_required').classList.add('d-none');
            }
            if (consentLetterFiles.length === 0) {
                document.getElementById('consent_letter_required').classList.remove('d-none');
            } else {
                document.getElementById('consent_letter_required').classList.add('d-none');
            }
            submitBtn.disabled = (leaveDocumentFiles.length === 0 || consentLetterFiles.length === 0);
        } else {
            document.getElementById('leave_document_required').classList.add('d-none');
            document.getElementById('consent_letter_required').classList.add('d-none');
            submitBtn.disabled = false;
        }
    }
    updateRequiredState();
    document.getElementById('formStatus').addEventListener('change', updateRequiredState);
</script>
@endsection


