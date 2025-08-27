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
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                @if(isset($leave))
                    Application for Conference/ Seminar/ Training and Workshop
                @else
                    Application for Conference/ Seminar/ Training and Workshop
                @endif
            </h2>
        </div>
        <a href="{{ route('leaves.index') }}" class="btn btn-outline-maroon">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
            <input type="hidden" name="reference_no" id="reference_no" value="{{ $leave->reference_no }}">
        @else
            <input type="hidden" name="reference_no" id="reference_no" value="">
        @endif

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
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
                    <select name="leave_type" class="form-select @error('leave_type') is-invalid @enderror" id ="select-leave-types">
                        <option selected disabled value="">Select</option>
                        @foreach ($leaveTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ (old('leave_type') == $type->id) || (isset($leave) && $leave->leave_type_id == $type->id) ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('leave_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="leave_type_error" class="text-danger small d-none">Please select a leave type.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Start Date *</label>
                    <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" id="fromDate"
                           value="{{ old('from_date', isset($leave) ? $leave->from_date : '') }}">
                    @error('from_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="from_date_error" class="text-danger small d-none">Please select a start date.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date *</label>
                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" id="toDate"
                           value="{{ old('to_date', isset($leave) ? $leave->to_date : '') }}">
                    @error('to_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="to_date_error" class="text-danger small d-none">Please select an end date.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days) *</label>
                    <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" id="duration"
                           value="{{ old('duration', isset($leave) ? $leave->duration : '') }}">
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="duration_error" class="text-danger small d-none">Please enter the duration.</div>
                </div>

                <!-- Travel Details Card -->
                <div class="card mb-4 travel-details-card">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-plane me-2"></i>
                                <span class="fw-bold">Travel Details</span>
                            </div>
                            <small class="opacity-75">Add travel destinations and upload supporting documents</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="fw-semibold">Reference No:</span>
                            <span id="client_ref_display" class="text-primary fw-bold"></span>
                        </div>
                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions:</strong> Please provide details for each travel destination. You can upload multiple documents for each entry. <strong class="text-danger">At least one document is required for each travel detail.</strong>
                        </div>

                        <div id="travel-entries">
                            <!-- Single Travel Entry Form -->
                                <div class="travel-entry-card mb-4" data-index="0">
                                    <div class="card border-2 border-primary">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-map-marker-alt me-2"></i>Travel Destination 1
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="travel_detail_0" class="form-label fw-semibold">
                                                        <i class="fas fa-edit me-1 text-primary"></i>Travel Details
                                                    </label>
                                                    <textarea class="form-control" id="travel_detail_0" name="travel_detail[]" rows="3"
                                                            placeholder="Describe the purpose and details of this travel..."></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="travel_country_0" class="form-label fw-semibold">
                                                        <i class="fas fa-globe me-1 text-primary"></i>Destination Country
                                                    </label>
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
                                                            <option value="{{ $country }}" {{ $country == 'Sri Lanka' ? 'selected' : '' }}>{{ $country }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="travel_from_datetime_0" class="form-label fw-semibold">
                                                        <i class="fas fa-calendar-alt me-1 text-primary"></i>Travel Start Date
                                                    </label>
                                                    <input type="date" class="form-control travel-start-date" id="travel_from_datetime_0"
                                                           name="travel_from_datetime[]" min="{{ date('Y-m-d') }}" data-index="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="travel_to_datetime_0" class="form-label fw-semibold">
                                                        <i class="fas fa-calendar-check me-1 text-primary"></i>Travel End Date
                                                    </label>
                                                    <input type="date" class="form-control travel-end-date" id="travel_to_datetime_0"
                                                           name="travel_to_datetime[]" min="{{ date('Y-m-d') }}" data-index="0">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">
                                                        <i class="fas fa-file-upload me-1 text-primary"></i>Supporting Documents
                                                    </label>
                                                    <div class="upload-area border-2 border-dashed border-primary rounded p-4 text-center bg-light">
                                                        <div class="upload-content">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                                            <h6 class="text-primary">Upload Travel Documents</h6>
                                                            <p class="text-muted mb-3">Drag and drop files here or click to browse</p>
                                                            <input type="file" class="form-control travel-document-input d-none"
                                                                   id="travel_document_0" data-index="0" name="travel_documents_0[]" multiple
                                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                            <button type="button" class="btn btn-primary btn-upload-trigger" data-index="0">
                                                                <i class="fas fa-plus me-2"></i>Choose Files
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="uploaded-files mt-3" id="travel_document_tags_0"></div>
                                                </div>
                                            </div>
                                        </div>

                                                                <!-- Submit Travel Details Button -->
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success btn-lg" id="submit-travel-details">
                                <i class="fas fa-plus me-2"></i>Add Travel Details
                            </button>
                            <div id="travel_details_error" class="text-danger small d-none mt-2">At least one travel detail with documents is required.</div>
                        </div>
                                    </div>
                                </div>
                        </div>

                        <!-- Travel Details Table -->
                        <div class="mt-4" id="travel-details-table-container">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-list me-2"></i>Added Travel Details
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="travel-details-table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Details</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Country</th>
                                            <th>Documents</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="travel-details-tbody">
                                        @if(isset($travelDetails) && count($travelDetails) > 0)
                                            @foreach($travelDetails as $detail)
                                                <tr data-id="{{ $detail->id }}">
                                                    <td>{{ $detail->detail }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($detail->travel_from_date)->format('M d, Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($detail->travel_to_date)->format('M d, Y') }}</td>
                                                    <td>{{ $detail->country }}</td>
                                                    <td>
                                                        @if($detail->documents && count($detail->documents) > 0)
                                                            @foreach($detail->documents as $doc)
                                                                <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="badge bg-primary text-decoration-none me-1 mb-1">{{ basename($doc) }}</a>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">No documents</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm delete-travel-btn" data-id="{{ $detail->id }}">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="no-travel-details">
                                                <td colspan="6" class="text-center text-muted">No travel details added yet</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Hidden container to archive file inputs for each added entry so they post with the form -->
                        <div id="archived-file-inputs" style="display:none;"></div>

                    </div>
                </div>
                <!-- Travel Details JavaScript -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Generate client-side reference number if empty
                        const refInput = document.getElementById('reference_no');
                        const refDisplay = document.getElementById('client_ref_display');
                        if (refInput && !refInput.value) {
                            const empNo = @json($user->empno);
                            const deptId = @json($user->department_id);
                            const facId = @json($user->faculty_id);
                            const appType = '4';
                            const seq = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
                            const clientRef = `${empNo}${deptId}${facId}${appType}${seq}`;
                            refInput.value = clientRef;
                            if (refDisplay) refDisplay.textContent = clientRef;
                        } else if (refInput && refInput.value && refDisplay) {
                            refDisplay.textContent = refInput.value;
                        }
                        // Restore main form data if preserved in sessionStorage
                        const preservedData = sessionStorage.getItem('preserveMainFormData');
                        if (preservedData) {
                            try {
                                const formData = JSON.parse(preservedData);

                                // Restore leave type
                                if (formData.leave_type) {
                                    const leaveTypeSelect = document.querySelector('select[name="leave_type"]');
                                    if (leaveTypeSelect) {
                                        leaveTypeSelect.value = formData.leave_type;
                                    }
                                }

                                // Restore dates and duration
                                if (formData.from_date) {
                                    const fromDateInput = document.querySelector('input[name="from_date"]');
                                    if (fromDateInput) {
                                        fromDateInput.value = formData.from_date;
                                    }
                                }

                                if (formData.to_date) {
                                    const toDateInput = document.querySelector('input[name="to_date"]');
                                    if (toDateInput) {
                                        toDateInput.value = formData.to_date;
                                    }
                                }

                                if (formData.duration) {
                                    const durationInput = document.querySelector('input[name="duration"]');
                                    if (durationInput) {
                                        durationInput.value = formData.duration;
                                    }
                                }

                                // Clear the preserved data after restoring
                                sessionStorage.removeItem('preserveMainFormData');
                            } catch (e) {
                                console.error('Error restoring form data:', e);
                                sessionStorage.removeItem('preserveMainFormData');
                            }
                        }

                        let entryIndex = {{ isset($travelDetails) ? count($travelDetails) : 1 }};
                        const countries = @json($countries);

                        // Client-side travel entries holder
                        let travelEntries = [];
                        let nextTravelIndex = 0;





                        // Handle file upload trigger
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('btn-upload-trigger')) {
                                const index = e.target.getAttribute('data-index');
                                console.log('Upload button clicked for index:', index); // Debug log
                                const fileInput = document.getElementById(`travel_document_${index}`);
                                console.log('File input element:', fileInput); // Debug log
                                if (fileInput) {
                                    console.log('Triggering file input click'); // Debug log
                                    fileInput.click();
                                } else {
                                    console.error('File input not found for index:', index);
                                }
                            }
                        });

                        // Handle file selection
                        document.addEventListener('change', function(e) {
                            console.log('Change event triggered on:', e.target); // Debug log
                            console.log('Target classes:', e.target.classList); // Debug log

                            if (e.target.classList.contains('travel-document-input')) {
                                const index = e.target.getAttribute('data-index');
                                const files = e.target.files;

                                console.log('File selection for index:', index, 'Files:', files.length); // Debug log
                                console.log('Selected files:', Array.from(files).map(f => f.name)); // Debug log

                                if (files.length > 0) {
                                    displaySelectedFiles(index, files);
                                } else {
                                    console.log('No files selected'); // Debug log
                                }
                            }
                        });

                        // Handle drag and drop
                        document.addEventListener('dragover', function(e) {
                            if (e.target.closest('.upload-area')) {
                                e.preventDefault();
                                e.target.closest('.upload-area').classList.add('dragover');
                            }
                        });

                        document.addEventListener('dragleave', function(e) {
                            if (e.target.closest('.upload-area')) {
                                e.target.closest('.upload-area').classList.remove('dragover');
                            }
                        });

                        document.addEventListener('drop', function(e) {
                            if (e.target.closest('.upload-area')) {
                                e.preventDefault();
                                const uploadArea = e.target.closest('.upload-area');
                                uploadArea.classList.remove('dragover');

                                const index = uploadArea.querySelector('.travel-document-input').getAttribute('data-index');
                                const files = e.dataTransfer.files;

                                if (files.length > 0) {
                                    uploadTravelDocuments(index, files);
                                }
                            }
                        });

                        // Function to display selected files
                        function displaySelectedFiles(index, files) {
                            console.log('Displaying files for index:', index); // Debug log

                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);
                            if (!tagsContainer) return;

                            // Clear existing display
                            tagsContainer.innerHTML = '';

                            // Process each file
                            Array.from(files).forEach(file => {
                                // Validate file type
                                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
                                if (!allowedTypes.includes(file.type)) {
                                    showNotification(`File ${file.name} is not a supported format`, 'error');
                                    return;
                                }

                                // Validate file size (2MB)
                                if (file.size > 2 * 1024 * 1024) {
                                    showNotification(`File ${file.name} is too large (max 2MB)`, 'error');
                                    return;
                                }

                                // Create file display element
                                const fileItem = document.createElement('div');
                                fileItem.className = 'uploaded-file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2';
                                fileItem.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span class="file-name">${file.name}</span>
                                        <small class="text-muted ms-2">(${(file.size / 1024).toFixed(1)} KB)</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-file-btn" data-index="${index}" data-file-index="${Array.from(files).indexOf(file)}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                `;
                                tagsContainer.appendChild(fileItem);
                            });

                            showNotification(`${files.length} file(s) selected!`, 'success');
                        }

                        // Function to remove file from input
                        function removeFileFromInput(index) {
                            const fileInput = document.getElementById(`travel_document_${index}`);
                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);

                            if (fileInput) {
                                fileInput.value = '';
                            }
                            if (tagsContainer) {
                                tagsContainer.innerHTML = '';
                            }

                            showNotification('Files removed', 'success');
                        }
                        
                        // Function to remove specific file from input (for individual file deletion)
                        function removeSpecificFileFromInput(index, fileIndex) {
                            const fileInput = document.getElementById(`travel_document_${index}`);
                            if (!fileInput || !fileInput.files) return;
                            
                            // Create a new FileList without the removed file
                            const dt = new DataTransfer();
                            Array.from(fileInput.files).forEach((file, idx) => {
                                if (idx !== parseInt(fileIndex)) {
                                    dt.items.add(file);
                                }
                            });
                            fileInput.files = dt.files;
                            
                            // Re-render the file display
                            displaySelectedFiles(index, fileInput.files);
                            
                            showNotification('File removed', 'success');
                        }



                        // Debug function to show current state
                        function debugTravelState() {}

                        // Handle submit travel details: keep in client array only
                        const submitBtn = document.getElementById('submit-travel-details');
                        if (submitBtn) {
                            submitBtn.addEventListener('click', function() {
                                console.log('Add Travel Details button clicked'); // Debug log

                                // Get form values
                                const detail = document.getElementById('travel_detail_0').value.trim();
                                const country = document.getElementById('travel_country_0').value;
                                const fromDate = document.getElementById('travel_from_datetime_0').value;
                                const toDate = document.getElementById('travel_to_datetime_0').value;
                                const fileInput = document.getElementById('travel_document_0');
                                console.log('File input element in submit:', fileInput); // Debug log
                                console.log('File input files:', fileInput ? fileInput.files : 'No file input'); // Debug log

                                // Validate required fields
                                if (!detail) {
                                    showNotification('Please enter travel details', 'error');
                                    return;
                                }
                                if (!country) {
                                    showNotification('Please select a country', 'error');
                                    return;
                                }
                                if (!fromDate) {
                                    showNotification('Please select travel start date', 'error');
                                    return;
                                }
                                if (!toDate) {
                                    showNotification('Please select travel end date', 'error');
                                    return;
                                }
                                
                                // Validate that at least one file is uploaded
                                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                                    showNotification('Please upload at least one document for travel details', 'error');
                                    return;
                                }

                                // Determine index for this entry
                                const currentIndex = nextTravelIndex++;

                                // Clone the file input into archived container with indexed name so it posts
                                const archived = document.getElementById('archived-file-inputs');
                                const originalInput = document.getElementById('travel_document_0');
                                let clonedInput = null;
                                if (originalInput) {
                                    clonedInput = originalInput.cloneNode();
                                    clonedInput.id = `travel_document_${currentIndex}`;
                                    clonedInput.name = `travel_documents_${currentIndex}[]`;
                                    // Transfer the FileList by reassigning the original input into the archived container and creating a fresh one in the UI
                                    archived.appendChild(originalInput);
                                    const replacement = document.createElement('input');
                                    replacement.type = 'file';
                                    replacement.className = 'form-control travel-document-input d-none';
                                    replacement.id = 'travel_document_0';
                                    replacement.setAttribute('data-index', '0');
                                    replacement.setAttribute('multiple', 'multiple');
                                    replacement.setAttribute('accept', '.pdf');
                                    document.querySelector('.upload-content').insertBefore(replacement, document.querySelector('.btn-upload-trigger'));
                                }

                                // Push to client-side holder
                                travelEntries.push({
                                    detail,
                                    country,
                                    from_date: fromDate,
                                    to_date: toDate,
                                    index: currentIndex
                                });

                                // Clear the travel form
                                clearTravelForm();
                                showNotification('Travel details added locally. They will be saved on submit/draft.', 'success');
                                
                                // Hide travel details error if it was showing
                                const travelError = document.getElementById('travel_details_error');
                                if (travelError) {
                                    travelError.classList.add('d-none');
                                }

                                // Update visual table immediately
                                const tbody = document.getElementById('travel-details-tbody');
                                const noRow = document.getElementById('no-travel-details');
                                if (noRow) noRow.remove();
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${detail}</td>
                                    <td>${fromDate}</td>
                                    <td>${toDate}</td>
                                    <td>${country}</td>
                                    <td><span class="badge bg-secondary">${(clonedInput && clonedInput.files && clonedInput.files.length) ? clonedInput.files.length : (originalInput && originalInput.files ? originalInput.files.length : 0)} file(s)</span></td>
                                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-local-travel" data-index="${currentIndex}"><i class="fas fa-trash"></i> Remove</button></td>
                                `;
                                tbody.appendChild(row);
                            });
                        } else {
                            console.error('Submit travel details button not found');
                        }

                        // Handle delete button clicks using event delegation
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('delete-travel-btn') || e.target.closest('.delete-travel-btn')) {
                                const button = e.target.classList.contains('delete-travel-btn') ? e.target : e.target.closest('.delete-travel-btn');
                                const id = button.getAttribute('data-id');
                                console.log('Delete button clicked via event delegation for ID:', id); // Debug log
                                deleteTravelDetail(id);
                            }
                        });

                        // Function to clear travel form
                        function clearTravelForm() {
                            document.getElementById('travel_detail_0').value = '';
                            document.getElementById('travel_country_0').value = '';
                            document.getElementById('travel_from_datetime_0').value = '';
                            document.getElementById('travel_to_datetime_0').value = '';

                            // Clear file input and display
                            const fileInput = document.getElementById('travel_document_0');
                            const tagsContainer = document.getElementById('travel_document_tags_0');

                            if (fileInput) {
                                fileInput.value = '';
                            }
                            if (tagsContainer) {
                                tagsContainer.innerHTML = '';
                            }
                        }



                        // Function to delete travel detail from database
                        function deleteTravelDetail(id) {
                            console.log('Delete button clicked for ID:', id); // Debug log
                            if (confirm('Are you sure you want to remove this travel detail?')) {
                                console.log('User confirmed deletion'); // Debug log
                                const deleteUrl = `{{ url('/leave/delete-travel-detail') }}/${id}`;
                                console.log('Delete URL:', deleteUrl); // Debug log

                                fetch(deleteUrl, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => {
                                    console.log('Delete response status:', response.status); // Debug log
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Delete response data:', data); // Debug log
                                    if (data.success) {
                                        // Remove the row from table
                                        const row = document.querySelector(`tr[data-id="${id}"]`);
                                        if (row) {
                                            row.remove();
                                        }

                                        // Check if table is empty
                                        const tbody = document.getElementById('travel-details-tbody');
                                        if (tbody.children.length === 0) {
                                            tbody.innerHTML = '<tr id="no-travel-details"><td colspan="6" class="text-center text-muted">No travel details added yet</td></tr>';
                                        }

                                        showNotification('Travel detail deleted successfully', 'success');
                                        
                                        // Hide travel details error if there are still travel details
                                        const remainingRows = document.getElementById('travel-details-tbody').querySelectorAll('tr:not(#no-travel-details)');
                                        if (remainingRows.length > 0) {
                                            const travelError = document.getElementById('travel_details_error');
                                            if (travelError) {
                                                travelError.classList.add('d-none');
                                            }
                                        }
                                    } else {
                                        showNotification(data.message || 'Error deleting travel detail', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showNotification('Error deleting travel detail', 'error');
                                });
                            }
                        }

                        // Function to view document (placeholder)
                        function viewDocument(filename) {
                            showNotification(`Document: ${filename}`, 'info');
                        }
                        // Function to render document tags
                        function renderTravelDocumentTags(index) {
                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);
                            if (!tagsContainer) return;

                            tagsContainer.innerHTML = '';

                            if (tempUploadedFiles[index] && tempUploadedFiles[index].length > 0) {
                                tempUploadedFiles[index].forEach(fileData => {
                                    const fileItem = document.createElement('div');
                                    fileItem.className = 'uploaded-file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2';

                                    fileItem.innerHTML = `
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-${getFileIcon(fileData.type)} me-2"></i>
                                            <div>
                                                <div class="file-name fw-semibold">${fileData.name}</div>
                                                <small class="text-muted">${formatFileSize(fileData.size)}</small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-temp-file"
                                                data-index="${index}" data-file-id="${fileData.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    `;

                                    tagsContainer.appendChild(fileItem);
                                });
                            }
                        }

                        // Helper function to get file icon
                        function getFileIcon(fileType) {
                            if (fileType.includes('pdf')) return 'pdf text-danger';
                            if (fileType.includes('word') || fileType.includes('document')) return 'word text-primary';
                            if (fileType.includes('image')) return 'image text-success';
                            return 'file text-secondary';
                        }

                        // Helper function to format file size
                        function formatFileSize(bytes) {
                            if (bytes === 0) return '0 Bytes';
                            const k = 1024;
                            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                            const i = Math.floor(Math.log(bytes) / Math.log(k));
                            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                        }

                        // Function to update hidden input with file data
                        function updateTravelDocumentsInput(index) {
                            const hiddenInput = document.querySelector(`input[name="travel_documents[${index}]"]`);
                            if (hiddenInput && tempUploadedFiles[index]) {
                                hiddenInput.value = JSON.stringify(tempUploadedFiles[index].map(f => ({
                                    name: f.name,
                                    size: f.size,
                                    type: f.type
                                })));
                            }
                        }

                        // Function to show notifications
                        function showNotification(message, type = 'info') {
                            const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
                            const notification = document.createElement('div');
                            notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
                            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                            notification.innerHTML = `
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            `;
                            document.body.appendChild(notification);

                            // Auto remove after 5 seconds
                            setTimeout(() => {
                                if (notification.parentNode) {
                                    notification.remove();
                                }
                            }, 5000);
                        }


                        // Remove temporary file
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-temp-file') || e.target.closest('.remove-temp-file')) {
                                const button = e.target.classList.contains('remove-temp-file') ? e.target : e.target.closest('.remove-temp-file');
                                const index = button.getAttribute('data-index');
                                const fileId = button.getAttribute('data-file-id');

                                if (confirm('Are you sure you want to remove this document?')) {
                                    // Remove from temporary storage
                                    if (tempUploadedFiles[index]) {
                                        tempUploadedFiles[index] = tempUploadedFiles[index].filter(f => f.id != fileId);
                                    }

                                    // Update display
                                    renderTravelDocumentTags(index);
                                    updateTravelDocumentsInput(index);

                                    showNotification('File removed successfully', 'success');
                                }
                            }
                        });

                        // Remove local travel entry handler
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-local-travel') || e.target.closest('.remove-local-travel')) {
                                const btn = e.target.classList.contains('remove-local-travel') ? e.target : e.target.closest('.remove-local-travel');
                                const idx = parseInt(btn.getAttribute('data-index'));
                                travelEntries = travelEntries.filter(te => te.index !== idx);
                                const archived = document.getElementById('archived-file-inputs');
                                const archivedInput = document.getElementById(`travel_document_${idx}`);
                                if (archived && archivedInput) archived.removeChild(archivedInput);
                                btn.closest('tr').remove();
                                
                                // Hide travel details error if there are still travel details
                                const remainingRows = document.getElementById('travel-details-tbody').querySelectorAll('tr:not(#no-travel-details)');
                                if (remainingRows.length > 0) {
                                    const travelError = document.getElementById('travel_details_error');
                                    if (travelError) {
                                        travelError.classList.add('d-none');
                                    }
                                }
                            }
                        });
                        
                        // Remove individual file handler
                        document.addEventListener('click', function(e) {
                            if (e.target.classList.contains('remove-file-btn') || e.target.closest('.remove-file-btn')) {
                                const btn = e.target.classList.contains('remove-file-btn') ? e.target : e.target.closest('.remove-file-btn');
                                const index = btn.getAttribute('data-index');
                                const fileIndex = btn.getAttribute('data-file-index');
                                removeSpecificFileFromInput(index, fileIndex);
                            }
                        });

                        // Form submission handler - attach client-side travel entries as JSON and files
                        document.querySelector('form').addEventListener('submit', function(e) {
                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'travel_entries';
                            hidden.value = JSON.stringify(travelEntries);
                            this.appendChild(hidden);
                        });

                        // Handle date restrictions
                        document.addEventListener('change', function(e) {
                            if (e.target.classList.contains('travel-start-date')) {
                                const index = e.target.getAttribute('data-index');
                                const startDate = e.target.value;
                                const endDateInput = document.getElementById(`travel_to_datetime_${index}`);

                                if (endDateInput) {
                                    // Set minimum date for end date to be the start date
                                    endDateInput.min = startDate;

                                    // If end date is before start date, clear it
                                    if (endDateInput.value && endDateInput.value < startDate) {
                                        endDateInput.value = '';
                                    }
                                }
                            }

                            if (e.target.classList.contains('travel-end-date')) {
                                const index = e.target.getAttribute('data-index');
                                const endDate = e.target.value;
                                const startDateInput = document.getElementById(`travel_from_datetime_${index}`);

                                if (startDateInput && startDateInput.value) {
                                    // Ensure end date is not before start date
                                    if (endDate < startDateInput.value) {
                                        e.target.value = '';
                                        showNotification('End date cannot be before start date', 'error');
                                    }
                                }
                            }
                        });

                        // Initialize date restrictions for existing entries
                        document.querySelectorAll('.travel-start-date').forEach(function(startInput) {
                            const index = startInput.getAttribute('data-index');
                            const endInput = document.getElementById(`travel_to_datetime_${index}`);

                            if (startInput.value && endInput) {
                                endInput.min = startInput.value;
                            }
                        });
                    });
                </script>
<!-- End of need to add leave Details -->
                
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Other Leave Request Documents (Optional)</label>
                    <input type="file" id="leave_document_input" name="leave_document[]" class="form-control mb-2" multiple accept=".pdf">
                    <div id="leave_document_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->leave_document))
                            @foreach($leave->leave_document as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
                                </span>
                            @endforeach
                        @endif
                    </div>
                    <div id="leave_document_error" class="text-danger small d-none">Please upload at least one leave request document.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Consent Letter <span class="text-danger">*</span></label>
                    <input type="file" id="consent_letter_input" name="consent_letter[]" class="form-control mb-2" multiple accept=".pdf">
                    <div id="consent_letter_tags" class="mb-2">
                        @if(isset($leave) && is_array($leave->consent_letter))
                            @foreach($leave->consent_letter as $file)
                                <span class="badge bg-secondary me-1">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-white text-decoration-none">{{ basename($file) }}</a>
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
                    <input type="checkbox" name="confirm" class="form-check-input" id="confirm_checkbox">
                    <label class="form-check-label">I confirm that the above details are true and correct. *</label>
                    <div id="confirm_error" class="text-danger small d-none">Please confirm that the details are true and correct.</div>
                </div>
            </div>
        </div>

        <!-- Buttons + Hidden Status Field -->
        <input type="hidden" name="form_status" id="formStatus" value="4">

        <div class="text-center">
            <button type="button" class="btn btn-maroon px-4" id="submit-btn">
                <i class="fas fa-paper-plane me-2"></i>Submit
            </button>
            <button type="submit" class="btn btn-gold px-4" onclick="setFormStatus(1)">
                <i class="fas fa-save me-2"></i>Save Draft
            </button>
            <a href="{{ route('leaves.index') }}" class="btn btn-outline-maroon px-4" id="cancel-btn">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
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

    // Validation function for compulsory fields
    function validateAndSubmit(formStatus) {
        console.log('validateAndSubmit called with formStatus:', formStatus);

        // Hide all previous error messages
        hideAllErrors();

        let isValid = true;

        // Validate Leave Type
        const leaveType = document.querySelector('[name="leave_type"]');
        if (!leaveType || !leaveType.value) {
            showError('leave_type_error');
            isValid = false;
        }

        // Validate Start Date
        const fromDate = document.querySelector('[name="from_date"]');
        if (!fromDate || !fromDate.value) {
            showError('from_date_error');
            isValid = false;
        }

        // Validate End Date
        const toDate = document.querySelector('[name="to_date"]');
        if (!toDate || !toDate.value) {
            showError('to_date_error');
            isValid = false;
        }

        // Validate Duration
        const duration = document.querySelector('[name="duration"]');
        if (!duration || !duration.value || duration.value <= 0) {
            showError('duration_error');
            isValid = false;
        }

        // Leave Request Document is optional; enforce PDF type if present
        const leaveDocInput = document.getElementById('leave_document_input');
        if (leaveDocInput && leaveDocInput.files && leaveDocInput.files.length > 0) {
            for (let f of leaveDocInput.files) {
                if (!f.name.toLowerCase().endsWith('.pdf')) {
                    showError('leave_document_error');
                    isValid = false;
                    break;
                }
            }
        }

        // Validate Consent Letter (must be at least one PDF or already existing when editing)
        const consentInput = document.getElementById('consent_letter_input');
        const existingConsentCount = document.querySelectorAll('#consent_letter_tags span').length;
        let hasConsentLetters = (consentInput && consentInput.files && consentInput.files.length > 0) || existingConsentCount > 0;
        if (consentInput && consentInput.files && consentInput.files.length > 0) {
            for (let f of consentInput.files) {
                if (!f.name.toLowerCase().endsWith('.pdf')) {
                    hasConsentLetters = false;
                    break;
                }
            }
        }
        if (!hasConsentLetters) {
            showError('consent_letter_required');
            isValid = false;
        }

        // Validate Confirmation Checkbox
        const confirmCheckbox = document.getElementById('confirm_checkbox');
        if (!confirmCheckbox || !confirmCheckbox.checked) {
            showError('confirm_error');
            isValid = false;
        }
        
        // Validate Travel Details - at least one travel detail with documents is required
        const travelDetailsTable = document.getElementById('travel-details-tbody');
        const existingTravelRows = travelDetailsTable.querySelectorAll('tr:not(#no-travel-details)');
        const hasTravelDetails = existingTravelRows.length > 0;
        
        // Also check client-side travel entries
        const hasClientTravelEntries = travelEntries && travelEntries.length > 0;
        
        if (!hasTravelDetails && !hasClientTravelEntries) {
            showError('travel_details_error');
            isValid = false;
        } else {
            // Check if each travel detail has at least one document
            let allHaveDocuments = true;
            
            // Check existing travel rows
            existingTravelRows.forEach(row => {
                const documentCell = row.querySelector('td:nth-child(5)');
                if (documentCell) {
                    const documentText = documentCell.textContent.trim();
                    if (documentText === 'No documents' || documentText === '0 file(s)') {
                        allHaveDocuments = false;
                    }
                }
            });
            
            // Check client-side travel entries
            if (hasClientTravelEntries) {
                travelEntries.forEach(entry => {
                    const archivedInput = document.getElementById(`travel_document_${entry.index}`);
                    if (!archivedInput || !archivedInput.files || archivedInput.files.length === 0) {
                        allHaveDocuments = false;
                    }
                });
            }
            
            if (!allHaveDocuments) {
                showError('travel_details_error');
                isValid = false;
            }
        }

        // If all validations pass, submit the form
        if (isValid) {
            console.log('Validation passed, submitting form');
            setFormStatus(formStatus);
            document.querySelector('form').submit();
        } else {
            console.log('Validation failed');
            // Scroll to the first error in document order
            scrollToFirstError();
        }
    }

    // Helper function to show error message
    function showError(errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.classList.remove('d-none');
        }
    }

    // Helper function to hide all error messages
    function hideAllErrors() {
        const errorElements = [
            'leave_type_error',
            'from_date_error',
            'to_date_error',
            'duration_error',
            'leave_document_error',
            'consent_letter_required',
            'confirm_error',
            'travel_details_error'
        ];

        errorElements.forEach(function(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
        });
    }

    // Helper function to scroll to first error in document order
    function scrollToFirstError() {
        const errorSelectors = [
            '#leave_type_error:not(.d-none)',
            '#from_date_error:not(.d-none)',
            '#to_date_error:not(.d-none)',
            '#duration_error:not(.d-none)',
            '#leave_document_error:not(.d-none)',
            '#consent_letter_required:not(.d-none)',
            '#confirm_error:not(.d-none)',
            '#travel_details_error:not(.d-none)'
        ];

        for (let selector of errorSelectors) {
            const errorElement = document.querySelector(selector);
            if (errorElement) {
                // Add a small delay to ensure the error is visible
                setTimeout(function() {
                    errorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                    // Add a highlight effect
                    errorElement.style.fontWeight = 'bold';
                    setTimeout(function() {
                        errorElement.style.fontWeight = '500';
                    }, 2000);
                }, 100);
                break;
            }
        }
    }

    // Hide errors when user starts filling the fields
    document.querySelector('[name="leave_type"]').addEventListener('change', function() {
        document.getElementById('leave_type_error').classList.add('d-none');
    });

    document.querySelector('[name="from_date"]').addEventListener('change', function() {
        document.getElementById('from_date_error').classList.add('d-none');
    });

    document.querySelector('[name="to_date"]').addEventListener('change', function() {
        document.getElementById('to_date_error').classList.add('d-none');
    });

    document.querySelector('[name="duration"]').addEventListener('input', function() {
        document.getElementById('duration_error').classList.add('d-none');
    });

    document.getElementById('confirm_checkbox').addEventListener('change', function() {
        document.getElementById('confirm_error').classList.add('d-none');
    });

    // ============ Inline preview badges with remove (x) for documents ============
    function renderFileBadges(inputEl, tagsContainerId) {
        const container = document.getElementById(tagsContainerId);
        if (!container || !inputEl) return;

        container.innerHTML = '';
        const files = inputEl.files ? Array.from(inputEl.files) : [];

        files.forEach((file, idx) => {
            // Only allow PDFs; skip others
            const isPdf = file.name.toLowerCase().endsWith('.pdf');
            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary me-1 mb-1 d-inline-flex align-items-center';
            const safeName = file.name.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            badge.innerHTML = `<span class="me-1">${safeName}</span>` +
                              `<button type="button" class="btn-close btn-close-white btn-sm ms-1 remove-selected-file" aria-label="Delete" data-input-id="${inputEl.id}" data-file-index="${idx}"></button>`;

            if (isPdf) {
                container.appendChild(badge);
            }
        });
    }

    function removeSelectedFile(inputId, removeIndex) {
        const input = document.getElementById(inputId);
        if (!input || !input.files) return;

        const dt = new DataTransfer();
        Array.from(input.files).forEach((file, idx) => {
            if (idx !== removeIndex) dt.items.add(file);
        });
        input.files = dt.files;
    }

    // Hook change listeners for previews
    const leaveDocInput = document.getElementById('leave_document_input');
    const consentInput = document.getElementById('consent_letter_input');

    if (leaveDocInput) {
        leaveDocInput.addEventListener('change', function() {
            // Filter non-PDFs out visually by not rendering; validation will catch as well
            renderFileBadges(leaveDocInput, 'leave_document_tags');
            // Hide error if at least one file now
            if (leaveDocInput.files.length > 0) {
                const err = document.getElementById('leave_document_error');
                if (err) err.classList.add('d-none');
            }
        });
    }

    if (consentInput) {
        consentInput.addEventListener('change', function() {
            renderFileBadges(consentInput, 'consent_letter_tags');
            // Hide error if at least one file now
            if (consentInput.files.length > 0) {
                const err = document.getElementById('consent_letter_required');
                if (err) err.classList.add('d-none');
            }
        });
    }

    // Delegate remove click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-selected-file')) {
            const inputId = e.target.getAttribute('data-input-id');
            const idx = parseInt(e.target.getAttribute('data-file-index'));
            if (isNaN(idx)) return;

            removeSelectedFile(inputId, idx);

            // Re-render badges for the affected input
            const input = document.getElementById(inputId);
            if (inputId === 'leave_document_input') {
                renderFileBadges(input, 'leave_document_tags');
            } else if (inputId === 'consent_letter_input') {
                renderFileBadges(input, 'consent_letter_tags');
            }
        }
    });

    // Add event listener for submit button
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        e.preventDefault();
        validateAndSubmit(2);
    });

    // For draft saving, remove required validation
    document.querySelector('button.btn-gold').addEventListener('click', function(e) {
        // Remove required for all fields when saving as draft
        document.querySelector('[name="leave_type"]').required = false;
        document.querySelector('[name="from_date"]').required = false;
        document.querySelector('[name="to_date"]').required = false;
        document.querySelector('[name="duration"]').required = false;
        document.querySelector('[name="confirm"]').required = false;
    });

    // Note: Holding files on the client until submit/save; no AJAX uploads here
</script>

<style>
/* Travel Details Enhanced Styles */
.travel-details-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #800000 0%, #800000 100%);
}

.travel-entry-card .card {
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.travel-entry-card .card:hover {
    border-color: #00ff37;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.bg-info {
    --bs-bg-opacity: 1;
    background-color: rgb(128 5 5) !important;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(128 5 5) !important;
}


.upload-area {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: #007bff !important;
    background-color: #f8f9ff !important;
}

.upload-area.dragover {
    border-color: #28a745 !important;
    background-color: #f8fff8 !important;
    transform: scale(1.02);
}

.upload-content {
    text-align: center;
    pointer-events: none;
}

.upload-content .btn-upload-trigger {
    pointer-events: auto;
}

.uploaded-file-item {
    background-color: #f8f9fa;
    transition: all 0.2s ease;
}

.uploaded-file-item:hover {
    background-color: #e9ecef;
    border-color: #090a5f !important;
}

.file-name {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-upload-trigger {
    transition: all 0.3s ease;
}

.btn-upload-trigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.travel-entry-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Animation for new entries */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.travel-entry-card {
    animation: slideIn 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .upload-area {
        min-height: 120px;
    }

    .upload-content i {
        font-size: 2rem !important;
    }

    .file-name {
        max-width: 150px;
    }
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Alert positioning */
.alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Travel Details Table Styles */
#travel-details-table-container {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1.5rem;
    border: 1px solid #dee2e6;
}

#travel-details-table {
    margin-bottom: 0;
}

#travel-details-table th {
    background-color: #007bff;
    color: white;
    font-weight: 600;
    border: none;
}

#travel-details-table td {
    vertical-align: middle;
    border-color: #dee2e6;
}

#travel-details-table tbody tr:hover {
    background-color: #e3f2fd;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

/* Validation Error Styling */
.text-danger.small {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
    display: block;
    padding: 0.25rem 0;
    border-radius: 0.25rem;
}

.text-danger.small:not(.d-none) {
    animation: fadeIn 0.3s ease-in;
}

/* Scroll target highlighting */
.text-danger.small:target,
.text-danger.small:focus {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 3px solid #dc3545;
    padding-left: 0.5rem;
}

/* Header color change */

.text-maroon {
    color: rgba(0, 0, 0) !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Highlight required fields when validation fails */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>

@endsection


