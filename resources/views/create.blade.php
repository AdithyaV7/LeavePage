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

    <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
            <input type="hidden" name="reference_no" value="{{ $leave->reference_no }}">
        @else
            <input type="hidden" name="reference_no" value="">
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
                                {{ (isset($leave) && $leave->leave_type_id == $type->id) ? 'selected' : '' }}>
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
                           value="{{ isset($leave) ? $leave->from_date : '' }}">
                    @error('from_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="from_date_error" class="text-danger small d-none">Please select a start date.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">End Date *</label>
                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" id="toDate"
                           value="{{ isset($leave) ? $leave->to_date : '' }}">
                    @error('to_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="to_date_error" class="text-danger small d-none">Please select an end date.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Duration (Days) *</label>
                    <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" id="duration"
                           value="{{ isset($leave) ? $leave->duration : '' }}">
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
                                <span class="fw-bold">Leave Request Documents</span>
                            </div>
                            <small class="opacity-75">Add travel destinations and upload supporting documents</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info border-0 mb-4">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions:</strong> Please provide details. You can upload multiple documents for each entry.
                        </div>

                        <div id="travel-entries">
                            <!-- Single Travel Entry Form -->
                                <div class="travel-entry-card mb-4" data-index="0">
                                    <div class="card border-2 border-primary">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-map-marker-alt me-2"></i>Document
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="travel_detail_0" class="form-label fw-semibold">
                                                        <i class="fas fa-edit me-1 text-primary"></i>Details
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
                                                        <i class="fas fa-calendar-alt me-1 text-primary"></i>Start Date
                                                    </label>
                                                    <input type="date" class="form-control travel-start-date" id="travel_from_datetime_0"
                                                           name="travel_from_datetime[]" min="{{ date('Y-m-d') }}" data-index="0">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="travel_to_datetime_0" class="form-label fw-semibold">
                                                        <i class="fas fa-calendar-check me-1 text-primary"></i>End Date
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
                                                            
                                                            <h6 class="text-primary">Upload Documents</h6>
                                                            <p class="text-muted mb-3">Click to browse</p>
                                                            <input type="file" class="form-control travel-document-input d-none"
                                                                   id="travel_document_0" data-index="0" multiple
                                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                            <button type="button" class="btn btn-primary btn-upload-trigger" data-index="0">
                                                                <i class="fas fa-plus me-2"></i>Choose Files
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="uploaded-files mt-3" id="travel_document_tags_0"></div>
                                                    <input type="hidden" name="travel_documents[0]" value="" class="travel-documents-input">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Travel Details Button -->
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-success btn-lg" id="submit-travel-details">
                                                <i class="fas fa-plus me-2"></i>Add Travel Details
                                            </button>
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
                            <div id="travel_details_error" class="text-danger small d-none mt-2">Please add at least one travel detail with supporting documents.</div>
                            @error('travel_details')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
                <!-- Travel Details JavaScript -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {


                        let entryIndex = {{ isset($travelDetails) ? count($travelDetails) : 1 }};
                        const countries = @json($countries);

                        // Store uploaded files temporarily - ensure each index has its own storage
                        let tempUploadedFiles = {};

                        // Store submitted travel details
                        let submittedTravelDetails = [];

                        // Initialize storage for existing entries
                        for (let i = 0; i < entryIndex; i++) {
                            tempUploadedFiles[i] = [];
                        }





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
                                fileItem.setAttribute('data-file-name', file.name);
                                fileItem.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <span class="file-name">${file.name}</span>
                                        <small class="text-muted ms-2">(${(file.size / 1024).toFixed(1)} KB)</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSpecificFile(${index}, '${file.name}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                `;
                                tagsContainer.appendChild(fileItem);
                            });

                            showNotification(`${files.length} file(s) selected!`, 'success');
                        }

                        // Function to remove file from input (make it globally accessible)
                        window.removeFileFromInput = function(index) {
                            const fileInput = document.getElementById(`travel_document_${index}`);
                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);

                            if (fileInput) {
                                fileInput.value = '';
                            }
                            if (tagsContainer) {
                                tagsContainer.innerHTML = '';
                            }

                            showNotification('All files removed', 'success');
                        }

                        // Function to remove specific file from display (make it globally accessible)
                        window.removeSpecificFile = function(index, fileName) {
                            const tagsContainer = document.getElementById(`travel_document_tags_${index}`);
                            const fileInput = document.getElementById(`travel_document_${index}`);

                            if (tagsContainer && fileInput) {
                                // Remove the specific file item from display
                                const fileItems = tagsContainer.querySelectorAll('.uploaded-file-item');
                                fileItems.forEach(item => {
                                    if (item.getAttribute('data-file-name') === fileName) {
                                        item.remove();
                                    }
                                });

                                // Create a new FileList without the removed file
                                const dt = new DataTransfer();
                                const files = fileInput.files;

                                for (let i = 0; i < files.length; i++) {
                                    if (files[i].name !== fileName) {
                                        dt.items.add(files[i]);
                                    }
                                }

                                fileInput.files = dt.files;
                                showNotification(`File "${fileName}" removed`, 'success');
                            }
                        }



                        // Debug function to show current state
                        function debugTravelState() {
                            console.log('=== Travel Debug State ===');
                            console.log('Current entryIndex:', entryIndex);
                            console.log('tempUploadedFiles:', tempUploadedFiles);

                            // Show all travel entries
                            const entries = document.querySelectorAll('.travel-entry-card');
                            console.log('Total travel entries:', entries.length);
                            entries.forEach((entry, idx) => {
                                const dataIndex = entry.getAttribute('data-index');
                                console.log(`Entry ${idx}: data-index=${dataIndex}`);
                            });
                            console.log('========================');
                        }

                        // Handle submit travel details (frontend only)
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

                                // Validate that at least one document is uploaded
                                if (fileInput.files.length === 0) {
                                    showNotification('Please upload at least one document for this travel detail', 'error');
                                    return;
                                }

                                // Show loading state
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

                                // Upload files first, then create travel detail
                                uploadTravelDocumentsAndCreateDetail(detail, country, fromDate, toDate, fileInput.files, submitBtn);
                            });
                        } else {
                            console.error('Submit travel details button not found');
                        }

                        // Function to upload travel documents and create travel detail
                        async function uploadTravelDocumentsAndCreateDetail(detail, country, fromDate, toDate, files, submitBtn) {
                            try {
                                const uploadedDocuments = [];

                                // Upload each file
                                for (let i = 0; i < files.length; i++) {
                                    const formData = new FormData();
                                    formData.append('file', files[i]);
                                    formData.append('type', 'travel_document');
                                    formData.append('_token', '{{ csrf_token() }}');

                                    const response = await fetch("{{ route('leaves.uploadTempFile') }}", {
                                        method: 'POST',
                                        body: formData
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        uploadedDocuments.push(data.file_path);
                                    } else {
                                        throw new Error(data.error || 'Upload failed');
                                    }
                                }

                                // Create travel detail object with uploaded file paths
                                const travelDetail = {
                                    id: Date.now(), // Use timestamp as temporary ID
                                    detail: detail,
                                    country: country,
                                    travel_from_date: fromDate,
                                    travel_to_date: toDate,
                                    documents: uploadedDocuments
                                };

                                // Add to temporary storage
                                tempTravelDetails.push(travelDetail);

                                // Update hidden input for form submission
                                updateHiddenInput('temp_travel_details', tempTravelDetails);

                                // Update the travel details table
                                updateTravelDetailsTable();

                                // Clear the travel form
                                clearTravelForm();

                                showNotification('Travel details added successfully!', 'success');

                            } catch (error) {
                                console.error('Error uploading travel documents:', error);
                                showNotification('Error uploading documents: ' + error.message, 'error');
                            } finally {
                                // Restore button state
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i>Add Travel Details';
                            }
                        }

                        // Function to update travel details table in frontend
                        function updateTravelDetailsTable() {
                            const tbody = document.getElementById('travel-details-tbody');
                            if (!tbody) return;

                            // Clear existing rows except the "no details" row
                            tbody.innerHTML = '';

                            if (tempTravelDetails.length === 0) {
                                tbody.innerHTML = '<tr id="no-travel-details"><td colspan="6" class="text-center text-muted">No travel details added yet</td></tr>';
                                return;
                            }

                            // Add rows for each travel detail
                            tempTravelDetails.forEach(detail => {
                                const row = document.createElement('tr');
                                row.setAttribute('data-temp-id', detail.id);

                                // Handle documents - they are now file paths, not file objects
                                const documentsHtml = detail.documents.map(docPath => {
                                    const fileName = docPath.split('/').pop(); // Get filename from path
                                    return `<a href="{{ asset('storage/') }}/${docPath}" target="_blank" class="badge bg-primary text-decoration-none me-1 mb-1">${fileName}</a>`;
                                }).join('');

                                row.innerHTML = `
                                    <td>${detail.detail}</td>
                                    <td>${formatDate(detail.travel_from_date)}</td>
                                    <td>${formatDate(detail.travel_to_date)}</td>
                                    <td>${detail.country}</td>
                                    <td>${documentsHtml || '<span class="text-muted">No documents</span>'}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-temp-travel-btn" data-temp-id="${detail.id}">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                        }

                        // Function to format date for display
                        function formatDate(dateString) {
                            const date = new Date(dateString);
                            return date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: 'numeric'
                            });
                        }

                        // Handle delete button clicks using event delegation
                        document.addEventListener('click', function(e) {
                            // Handle database travel detail deletion
                            if (e.target.classList.contains('delete-travel-btn') || e.target.closest('.delete-travel-btn')) {
                                const button = e.target.classList.contains('delete-travel-btn') ? e.target : e.target.closest('.delete-travel-btn');
                                const id = button.getAttribute('data-id');
                                console.log('Delete button clicked via event delegation for ID:', id); // Debug log
                                deleteTravelDetail(id);
                            }

                            // Handle temporary travel detail deletion
                            if (e.target.classList.contains('delete-temp-travel-btn') || e.target.closest('.delete-temp-travel-btn')) {
                                const button = e.target.classList.contains('delete-temp-travel-btn') ? e.target : e.target.closest('.delete-temp-travel-btn');
                                const tempId = button.getAttribute('data-temp-id');
                                console.log('Delete temp travel detail clicked for ID:', tempId); // Debug log
                                deleteTempTravelDetail(tempId);
                            }
                        });

                        // Function to delete temporary travel detail
                        function deleteTempTravelDetail(tempId) {
                            if (confirm('Are you sure you want to remove this travel detail?')) {
                                // Find the travel detail to get its documents
                                const travelDetail = tempTravelDetails.find(detail => detail.id == tempId);

                                if (travelDetail && travelDetail.documents) {
                                    // Delete uploaded files
                                    travelDetail.documents.forEach(filePath => {
                                        const formData = new FormData();
                                        formData.append('type', 'travel_document');
                                        formData.append('file_path', filePath);
                                        formData.append('_token', '{{ csrf_token() }}');

                                        fetch("{{ route('leaves.deleteTempFile') }}", {
                                            method: 'POST',
                                            body: formData
                                        }).catch(error => {
                                            console.error('Error deleting file:', error);
                                        });
                                    });
                                }

                                // Remove from temporary storage
                                tempTravelDetails = tempTravelDetails.filter(detail => detail.id != tempId);

                                // Update hidden input for form submission
                                updateHiddenInput('temp_travel_details', tempTravelDetails);

                                // Update the table
                                updateTravelDetailsTable();

                                showNotification('Travel detail removed successfully', 'success');
                            }
                        }

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

                        // Form submission handler - travel details are already saved via AJAX
                        document.querySelector('form').addEventListener('submit', function(e) {
                            // Travel details are already saved to database via AJAX
                            // No need to add them to form submission
                            console.log('Form submitted - travel details already saved in database');
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
                    <div class="upload-area border-2 border-dashed border-primary rounded p-3 text-center bg-light mb-2">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                            <h6 class="text-primary">Upload Leave Documents</h6>
                            <p class="text-muted mb-2">Drag and drop files here or click to browse</p>
                            <input type="file" id="leave_document_input" class="form-control d-none" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('leave_document_input').click()">
                                <i class="fas fa-plus me-2"></i>Choose Files
                            </button>
                        </div>
                    </div>
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
                    <!-- Hidden input to track temporary files -->
                    <input type="hidden" name="temp_leave_documents" id="temp_leave_documents" value="[]">
                    <div id="leave_document_error" class="text-danger small d-none">Please upload at least one leave request document.</div>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Upload Consent Letter <span class="text-danger">*</span></label>
                    <div class="upload-area border-2 border-dashed border-primary rounded p-3 text-center bg-light mb-2">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                            <h6 class="text-primary">Upload Consent Letters</h6>
                            <p class="text-muted mb-2">Drag and drop files here or click to browse</p>
                            <input type="file" id="consent_letter_input" class="form-control d-none" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('consent_letter_input').click()">
                                <i class="fas fa-plus me-2"></i>Choose Files
                            </button>
                        </div>
                    </div>
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
                    <!-- Hidden input to track temporary files -->
                    <input type="hidden" name="temp_consent_letters" id="temp_consent_letters" value="[]">

                    <!-- Hidden input to track temporary travel details -->
                    <input type="hidden" name="temp_travel_details" id="temp_travel_details" value="[]">
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
        <input type="hidden" name="form_status" id="formStatus" value="1">

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

        // Leave Request Document is optional; no validation enforced here
        // Validate Consent Letter (check if files are uploaded or temporary files exist)
        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
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

        // Travel details validation is handled by the backend
        // Frontend only validates basic required fields

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
            'confirm_error'
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
            '#confirm_error:not(.d-none)'
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

    // Add event listener for submit button with validation
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Validate required fields
        if (validateRequiredFields()) {
            // If validation passes, submit with form_status = 2
            setFormStatus(2);
            document.querySelector('form').submit();
        }
    });

    // Function to validate only required fields
    function validateRequiredFields() {
        console.log('Validating required fields for submit');

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

        // Validate Consent Letter (check if files are uploaded or temporary files exist)
        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
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

        // Validate Travel Details - check if at least one entry exists
        const travelDetailsTable = document.getElementById('travel-details-tbody');
        let hasValidTravelDetails = false;

        if (travelDetailsTable && travelDetailsTable.children.length > 0) {
            // Count rows that are not the "no-travel-details" placeholder
            const validRows = Array.from(travelDetailsTable.children).filter(row =>
                row.id !== 'no-travel-details'
            );
            if (validRows.length > 0) {
                hasValidTravelDetails = true;
            }
        }

        // Also check temporary travel details (newly added)
        if (!hasValidTravelDetails && typeof tempTravelDetails !== 'undefined' && tempTravelDetails.length > 0) {
            hasValidTravelDetails = true;
        }

        if (!hasValidTravelDetails) {
            showError('travel_details_error');
            isValid = false;
        }

        // If validation fails, show popup with missing fields and then scroll to first error
        if (!isValid) {
            console.log('Required field validation failed');
            showMissingFieldsPopup();
            scrollToFirstError();
        }

        return isValid;
    }

    // Function to show popup with missing fields
    function showMissingFieldsPopup() {
        let missingFields = [];

        // Check each field and add to missing list if invalid
        const leaveType = document.querySelector('[name="leave_type"]');
        if (!leaveType || !leaveType.value) {
            missingFields.push("• Leave Type");
        }

        const fromDate = document.querySelector('[name="from_date"]');
        if (!fromDate || !fromDate.value) {
            missingFields.push("• Start Date");
        }

        const toDate = document.querySelector('[name="to_date"]');
        if (!toDate || !toDate.value) {
            missingFields.push("• End Date");
        }

        const duration = document.querySelector('[name="duration"]');
        if (!duration || !duration.value || duration.value <= 0) {
            missingFields.push("• Duration");
        }

        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
        if (!hasConsentLetters) {
            missingFields.push("• Consent Letter");
        }

        const confirmCheckbox = document.getElementById('confirm_checkbox');
        if (!confirmCheckbox || !confirmCheckbox.checked) {
            missingFields.push("• Confirmation Checkbox");
        }

        // Check travel details
        const travelDetailsTable = document.getElementById('travel-details-tbody');
        let hasValidTravelDetails = false;

        if (travelDetailsTable && travelDetailsTable.children.length > 0) {
            const validRows = Array.from(travelDetailsTable.children).filter(row =>
                row.id !== 'no-travel-details'
            );
            if (validRows.length > 0) {
                hasValidTravelDetails = true;
            }
        }

        if (!hasValidTravelDetails && typeof tempTravelDetails !== 'undefined' && tempTravelDetails.length > 0) {
            hasValidTravelDetails = true;
        }

        if (!hasValidTravelDetails) {
            missingFields.push("• Travel Details (at least one entry required)");
        }

        // Show popup with missing fields
        if (missingFields.length > 0) {
            const message = "Please fill in the following required fields:\n\n" + missingFields.join("\n");
            alert(message);
        }
    }

    // For draft saving, remove required validation
    document.querySelector('button.btn-gold').addEventListener('click', function(e) {
        // Remove required for all fields when saving as draft
        document.querySelector('[name="leave_type"]').required = false;
        document.querySelector('[name="from_date"]').required = false;
        document.querySelector('[name="to_date"]').required = false;
        document.querySelector('[name="duration"]').required = false;
        document.querySelector('[name="confirm"]').required = false;
    });

    let leaveId = {{ isset($leave) ? $leave->id : 'null' }};

    // AJAX upload for temporary files (no draft required)
    function uploadFilesAJAX(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;

        // Use temporary upload if no leaveId exists
        if (!leaveId) {
            uploadTempFiles(inputId, type, tagsId);
            return;
        }

        // Use existing draft upload if leaveId exists
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

                    // Hide error messages when files are uploaded
                    if (type === 'leave_document') {
                        document.getElementById('leave_document_error').classList.add('d-none');
                    } else if (type === 'consent_letter') {
                        document.getElementById('consent_letter_required').classList.add('d-none');
                    }
                } else {
                    alert(data.error || 'Upload failed');
                }
            });
        }
    }

    // In-memory storage for temporary files and travel details
    let tempLeaveFiles = [];
    let tempConsentFiles = [];
    let tempTravelDetails = [];

    // Upload temporary files (for new applications)
    function uploadTempFiles(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;

        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('type', type);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('leaves.uploadTempFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store in memory
                    if (type === 'leave_document') {
                        tempLeaveFiles.push(data.file_path);
                        updateHiddenInput('temp_leave_documents', tempLeaveFiles);
                    } else if (type === 'consent_letter') {
                        tempConsentFiles.push(data.file_path);
                        updateHiddenInput('temp_consent_letters', tempConsentFiles);
                    }

                    addTempFileTag(tagsId, data.file_path, data.file_name, type);

                    // Hide error messages when files are uploaded
                    if (type === 'leave_document') {
                        document.getElementById('leave_document_error').classList.add('d-none');
                    } else if (type === 'consent_letter') {
                        document.getElementById('consent_letter_required').classList.add('d-none');
                    }
                } else {
                    alert(data.error || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                alert('Upload failed');
            });
        }

        input.value = ''; // Clear the input
    }

    // Update hidden input with file paths
    function updateHiddenInput(inputId, fileArray) {
        document.getElementById(inputId).value = JSON.stringify(fileArray);
    }
    // Auto-upload when files are selected for leave documents
    document.getElementById('leave_document_input').addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFilesAJAX('leave_document_input', 'leave_document', 'leave_document_tags');
        }
    });

    // Auto-upload when files are selected for consent letters
    document.getElementById('consent_letter_input').addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFilesAJAX('consent_letter_input', 'consent_letter', 'consent_letter_tags');
        }
    });

    // Function to add temporary file tag
    function addTempFileTag(tagsId, filePath, fileName, type) {
        const tagsDiv = document.getElementById(tagsId);
        const span = document.createElement('span');
        span.className = 'badge bg-secondary me-1';
        span.innerHTML = `
            <span class="text-white">${fileName}</span>
            <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-temp-file-btn"
                    data-type="${type}" data-file-path="${filePath}" aria-label="Delete"></button>
        `;
        tagsDiv.appendChild(span);
    }

    // Handle temporary file deletion
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-temp-file-btn')) {
            const type = e.target.getAttribute('data-type');
            const filePath = e.target.getAttribute('data-file-path');

            const formData = new FormData();
            formData.append('type', type);
            formData.append('file_path', filePath);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('leaves.deleteTempFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from in-memory arrays
                    if (type === 'leave_document') {
                        tempLeaveFiles = tempLeaveFiles.filter(file => file !== filePath);
                        updateHiddenInput('temp_leave_documents', tempLeaveFiles);
                    } else if (type === 'consent_letter') {
                        tempConsentFiles = tempConsentFiles.filter(file => file !== filePath);
                        updateHiddenInput('temp_consent_letters', tempConsentFiles);
                    }

                    e.target.closest('.badge').remove();
                } else {
                    alert(data.error || 'Delete failed');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Delete failed');
            });
        }
    });

    // Add drag and drop functionality for leave documents and consent letters
    function setupSpecificDragAndDrop(uploadArea, inputId, type, tagsId) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById(inputId);
                input.files = files;
                uploadFilesAJAX(inputId, type, tagsId);
            }
        });
    }

    // Setup drag and drop for specific upload areas
    const leaveDocUploadArea = document.querySelector('#leave_document_input').closest('.upload-area');
    const consentLetterUploadArea = document.querySelector('#consent_letter_input').closest('.upload-area');

    if (leaveDocUploadArea) {
        setupSpecificDragAndDrop(leaveDocUploadArea, 'leave_document_input', 'leave_document', 'leave_document_tags');
    }

    if (consentLetterUploadArea) {
        setupSpecificDragAndDrop(consentLetterUploadArea, 'consent_letter_input', 'consent_letter', 'consent_letter_tags');
    }
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

                    // Show error messages if no files remain after deletion
                    if (data.files.length === 0) {
                        if (type === 'leave_document') {
                            // Don't show error for leave document as it's not always required
                        } else if (type === 'consent_letter') {
                            // Don't auto-show error, let validation handle it
                        }
                    }
                } else {
                    alert(data.error || 'Delete failed');
                }
            });
        }
    });

    // Note: updateRequiredState function removed as validation is now handled by validateAndSubmit function
</script>

<style>
/* Travel Details Enhanced Styles */
.travel-details-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.bg-gradient-primary {
    background: linear-gradient(135deg,rgb(4, 4, 4) 100%,rgb(4, 4, 4) 100%);
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
    background-color: rgb(4 4 4) !important;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
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

.upload-content .btn {
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
    background-color:rgb(3, 3, 3);
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

<script>
// No form data persistence - clean slate on every refresh
</script>

@endsection


