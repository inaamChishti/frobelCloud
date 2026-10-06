@extends('layouts.branchDashboardApp')

@section('content')
    <!-- Add Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="main-content" style="zoom:0.8;">
        <h1>Student Admission Form</h1>
        <p>Fill in the details below to register new students.</p>

        <!-- Display validation errors if any -->
        @if ($errors->any())
            <div class="alert alert-danger" data-server-error="true">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li><form
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Display success message if admission was created -->
        @if (Session::has('created-status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ Session::get('created-status') }}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Admission form -->
        <form method="POST" class="needs-validation" action="{{ route('admin.admission.store') }}" novalidate>
            @csrf

            <!-- Family Information Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-home"></i>
                    <h2>Family Information</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="family_id" class="form-label">Family ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="family_id" class="form-control"
                                value="@isset($family_id) {{ $family_id }} @endisset" readonly
                                placeholder="Auto-generated family ID">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="form_date" class="form-label">Form Date <span class="required-star"
                                style="color: red; font-size: 12px;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="text" id="form_date" name="form_date" class="form-control" autocomplete="off"
                                inputmode="none" onfocus="this.showPicker?.()"
                                value="{{ old('form_date', now()->format('d/m/Y')) }}" required placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="joining_date" class="form-label">Joining Date <span class="required-star"
                                style="color: red; font-size: 12px;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            <input type="text" id="joining_date" name="joining_date" class="form-control" autocomplete="off"
                                inputmode="none" onfocus="this.showPicker?.()"
                                value="{{ old('joining_date', now()->format('d/m/Y')) }}" required placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="family_status" class="form-label">Status <span class="required-star"
                                style="color: red; font-size: 12px;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                            <select name="family_status" class="form-select" required>
                                <option value="" disabled>Select Status</option>
                                <option value="Active" @if (old('family_status') == 'Active') selected @endif>Active</option>
                                <option value="De-Active" @if (old('family_status') == 'De-Active') selected @endif>De-Active</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-user-graduate"></i>
                    <h2>Student Information</h2>
                    <button type="button" id="add-row" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>First Name <span class="required-star" style="color: red; font-size: 12px;">*</span></th>
                                <th>Last Name <span class="required-star" style="color: red; font-size: 12px;">*</span></th>
                                <th>Date of Birth <span class="required-star" style="color: red; font-size: 12px;">*</span></th>
                                <th>Gender <span class="required-star" style="color: red; font-size: 12px;">*</span></th>
                                <th>Medical Condition</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody">
                            <tr id="template-row" class="student-row">
                                <td>
                                    <input type="text" name="first_name[]" class="form-control"
                                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)" required
                                        placeholder="Enter first name">
                                </td>
                                <td>
                                    <input type="text" name="surname[]" class="form-control"
                                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)" required
                                        placeholder="Enter last name">
                                </td>
                                <td>
                                    <input type="text" name="dob[]" class="form-control dob-picker" autocomplete="off"
                                        inputmode="none" onfocus="this.showPicker?.()" required
                                        placeholder="dd/mm/yyyy">
                                </td>
                                <td>
                                    <select name="gender[]" class="form-select" required>
                                        <option value="" disabled selected>Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input med-toggle" type="checkbox" role="switch">
                                        <button type="button" class="btn btn-sm btn-outline-primary add-condition"
                                            style="white-space: nowrap;" disabled>
                                            <i class="fas fa-file-medical"></i> med info
                                        </button>
                                        <input type="hidden" name="medical_conditions[]" class="medical-condition-data">
                                    </div>
                                </td>
                                <td>
                                    <select name="student_status[]" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-row"
                                        style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Medical Condition Modal -->
            <div class="modal fade" id="condition-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="d-flex align-items-center w-100">
                                <img src="{{ asset('img/logo-frobel.jpg') }}" width="120" class="me-3">
                                <div>
                                    <h5 class="modal-title">Medical Information Form</h5>
                                    <p class="mb-0 text-muted">For students with medical conditions at school</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <h4>Frobel Education</h4>
                                <h6>Medical Details Form</h6>
                            </div>
                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-stethoscope me-2"></i>Medical Condition</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="doctor_name" class="form-label">Doctor Name</label>
                                    <input type="text" class="form-control" id="doctor_name"
                                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)"
                                        placeholder="Enter doctor's name">
                                </div>
                                <div class="col-md-6">
                                    <label for="doctor_number" class="form-label">Doctor Number</label>
                                    <input type="number" class="form-control" id="doctor_number"
                                        onkeypress="return /\d/.test(event.key)"
                                        placeholder="Enter doctor's phone number">
                                </div>
                                <div class="col-12">
                                    <label for="maddress" class="form-label">Address</label>
                                    <textarea class="form-control" id="maddress" rows="3" placeholder="Enter doctor's clinic address"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary save-condition">Save Details</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guardian & Next of Kin Section -->
            <div class="row g-4">
                <!-- Guardian Information -->
                <div class="col-lg-6">
                    <div class="card-section">
                        <div class="section-header">
                            <i class="fas fa-user-shield"></i>
                            <h2>Parent/Guardian Details</h2>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="guardian_name" class="form-label">Name <span class="required-star"
                                        style="color: red; font-size: 12px;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="guardian_name" class="form-control"
                                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)"
                                        value="{{ old('guardian_name') }}" required
                                        placeholder="Enter guardian's full name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_email" class="form-label">Email <span class="required-star"
                                        style="color: red; font-size: 12px;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="guardian_email" class="form-control"
                                        value="{{ old('guardian_email') }}" required
                                        placeholder="Enter guardian's email">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="guardian_address" class="form-label">Address <span class="required-star"
                                        style="color: red; font-size: 12px;">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea name="guardian_address" class="form-control" required placeholder="Enter full address">{{ old('guardian_address') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="guardian_mobile" class="form-label">Mobile</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="number" name="guardian_mobile" class="form-control"
                                        value="{{ old('guardian_mobile') }}" placeholder="Enter mobile number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="parent_relationship" class="form-label">Relationship</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="text" name="parent_relationship" class="form-control"
                                        value="{{ old('parent_relationship') }}" placeholder="E.g. Mother, Father, etc.">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next of Kin Information -->
                <div class="col-lg-6">
                    <div class="card-section">
                        <div class="section-header">
                            <i class="fas fa-user-friends"></i>
                            <h2>Next of Kin Details</h2>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kin_name" class="form-label">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="kin_name" class="form-control"
                                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)"
                                        value="{{ old('kin_name') }}" placeholder="Enter next of kin's name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="kin_email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="kin_email" class="form-control"
                                        value="{{ old('kin_email') }}" placeholder="Enter next of kin's email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="kin_address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" name="kin_address" class="form-control"
                                        value="{{ old('kin_address') }}" placeholder="Enter next of kin's address">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="kin_mobile" class="form-label">Mobile</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="kin_mobile" class="form-control"
                                        onkeypress="return /\d/.test(event.key)" value="{{ old('kin_mobile') }}"
                                        placeholder="Enter next of kin's phone">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other Information Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>Additional Information</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="medical_condition" class="form-label">Medical Conditions Notes</label>
                        <textarea name="medical_condition" class="form-control" rows="3"
                            placeholder="Enter any additional medical information">{{ @$admission->medicalcondition }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other School Attendance</label>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="child_school" id="yes"
                                    value="yes"
                                    {{ !empty($admission->child_name1) && !empty($admission->school_name1) ? 'checked' : '' }}
                                    onclick="toggleInputs(true)">
                                <label class="form-check-label" for="yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="child_school" id="no"
                                    value="no"
                                    {{ empty($admission->child_name1) || empty($admission->school_name1) ? 'checked' : '' }}
                                    onclick="toggleInputs(false)">
                                <label class="form-check-label" for="no">No</label>
                            </div>
                        </div>
                        <div id="childSchoolInputs"
                            style="display: {{ !empty($admission->child_name1) && !empty($admission->school_name1) ? 'block' : 'none' }};">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="row g-2 mb-2">
                                    <div class="col">
                                        <input type="text" class="form-control" placeholder="Enter child's name"
                                            name="child_name{{ $i }}"
                                            value="{{ @$admission->{"child_name$i"} }}">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" placeholder="Enter school name"
                                            name="school_name{{ $i }}"
                                            value="{{ @$admission->{"school_name$i"} }}">
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="fee_detail" class="form-label">Fee Details <span class="required-star"
                                style="color: red; font-size: 12px;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="d-flex align-items-center gap-2" style="flex: 1;">
                                <input type="number" 
                                       name="package_amount" 
                                       id="package_amount" 
                                       class="form-control" 
                                       placeholder="Amount" 
                                       value="{{ $packageAmount ?? '' }}"
                                       pattern="[0-9]*"
                                       inputmode="numeric"
                                       min="0"
                                       step="1"
                                       required
                                       style="width: 100px;">
                                <label class="mb-0" style="font-weight: bold; white-space: nowrap;">For</label>
                                <select name="package_weeks" id="package_weeks" class="form-control" required style="flex: 1;">
                                    <option value="">Select</option>
                                    <option value="2 weeks" {{ (isset($packageWeeks) && strtolower($packageWeeks) == '2 weeks') ? 'selected' : '' }}>2 Weeks</option>
                                    <option value="4 weeks" {{ (isset($packageWeeks) && strtolower($packageWeeks) == '4 weeks') ? 'selected' : '' }}>4 Weeks</option>
                                    <option value="ucas session" {{ (isset($packageWeeks) && strtolower($packageWeeks) == 'ucas session') ? 'selected' : '' }}>UCAS Session</option>
                                    <option value="per month" {{ (isset($packageWeeks) && strtolower($packageWeeks) == 'per month') ? 'selected' : '' }}>Per Month</option>
                                    <option value="per session" {{ (isset($packageWeeks) && strtolower($packageWeeks) == 'per session') ? 'selected' : '' }}>Per Session</option>
                                </select>
                            </div>
                            <input type="hidden" name="fee_detail" id="fee_detail_hidden" value="{{ @$admission->feedetail }}">
                        </div>
                        <small class="text-muted">Package will be stored as: <span id="package_preview" class="font-semibold">-</span></small>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">Payment Method <span class="required-star"
                                style="color: red; font-size: 12px;">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                            <select name="payment_method" class="form-select" required>
                                <option value="" disabled selected>Select payment method</option>
                                <option value="Cash Payment"
                                    {{ @$admission->payment_method == 'Cash Payment' ? 'selected' : '' }}>Cash Payment
                                </option>
                                <option value="Card Payment"
                                    {{ @$admission->payment_method == 'Card Payment' ? 'selected' : '' }}>Card Payment
                                </option>
                                <option value="Bank Transfer"
                                    {{ @$admission->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="add_comment" class="form-label">Additional Comments</label>
                        <textarea name="add_comment" class="form-control" rows="3" placeholder="Enter any additional comments">{{ @$admission->add_comment }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Subjects and Sessions Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-book"></i>
                    <h2>Subjects and Sessions</h2>
                </div>
                <div id="subjects-sections">
                    <!-- Student-specific subject sections dynamically added here -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions mt-4">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Submit Admission
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-2"></i>Reset Form
                </button>
            </div>
        </form>
    </div>

    <!-- CSS Styles (unchanged) -->
    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 2px solid #2149AF;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2149AF;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #2149AF;
            margin-bottom: 20px;
        }

        .card-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(103, 192, 234, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(103, 192, 234, 0.3);
        }

        .section-header i {
            font-size: 24px;
            color: #2149AF;
            margin-right: 15px;
        }

        .section-header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #2149AF;
            margin: 0;
            flex-grow: 1;
        }

        .form-label {
            font-weight: 600;
            color: #2149AF;
            margin-bottom: 8px;
        }

        .required-star {
            color: #ff4d4d;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #2149AF;
            color: #495057;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #4ba8d2;
            box-shadow: 0 0 0 0.25rem rgba(103, 192, 234, 0.25);
            color: #495057;
        }

        .input-group-text {
            background: rgba(103, 192, 234, 0.2);
            border: 2px solid #2149AF;
            color: #2149AF;
        }

        .table {
            color: #495057;
        }

        .table th {
            background: rgba(103, 192, 234, 0.1);
            color: #2149AF;
            border-bottom: 2px solid #2149AF;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-primary {
            background-color: #2149AF;
            border-color: #2149AF;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #4ba8d2;
            border-color: #4ba8d2;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            color: #2149AF;
            border-color: #2149AF;
        }

        .btn-outline-secondary:hover {
            background-color: #2149AF;
            color: white;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .invalid-feedback {
            color: #ff4d4d;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .med-toggle:checked {
            background-color: #2149AF;
            border-color: #2149AF;
        }

        .student-subject-section {
            margin-bottom: 20px;
            border: 1px solid rgba(103, 192, 234, 0.3);
            border-radius: 8px;
            padding: 15px;
        }

        .student-subject-section h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2149AF;
            margin-bottom: 15px;
        }
    </style>

    <!-- Include Flatpickr JS and other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const subjects = @json($subjects);
        const gradeOptions = {
            lowerKeyStages: [
                '1', '2', '3', '4', '5', '6', '7', '8', '9',
                '11 Plus English', 'Level 3 English', 'Level 4 English', 'Level 5 English',
                'English Exam Booklet 1', 'English Exam Booklet 2',
                'Level 3 NVR', 'Level 4 NVR', 'Level 5 NVR',
                'NVR Exam Booklet 1', 'NVR Exam Booklet 2',
                'Level 3 VR', 'Level 4 VR', 'Level 5 VR',
                'VR Exam Booklet 1', 'VR Exam Booklet 2',
                'E1A', 'E1B', 'E1C', 'E2A', 'E2B', 'E2C',
                'E3A', 'E3B', 'E3C', 'E4A', 'E4B', 'E4C',
                'E5A', 'E5B', 'E5C', 'E6A', 'E6B', 'E6C',
                'E7A', 'E7B', 'E7C', 'E8A', 'E8B', 'E8C',
                'E9A', 'E9B', 'E9C',
                'KS3 Foundation','KS3 Higher',
                'KS2 SATs Book 1', 'KS2 SATs Book 2', 'KS2 SATs Book 3',
                'KS2 SATs Past Paper Booklet 1', 'KS2 SATs Past Paper Booklet 2', 'KS2 SATs Past Paper Booklet 3',
                '1 Plus Math', 'Level 3 Math', 'Level 4 Math', 'Level 5 Math',
                'Math Exam Booklet 1', 'Math Exam Booklet 2',
                'Math 11 Plus Book 1', 'Math 11 Plus Book 2',
                'M1A', 'M1B', 'M1C',
                'M2A', 'M2B', 'M2C',
                'M3A', 'M3B', 'M3C',
                'M4A', 'M4B', 'M4C',
                'M5A', 'M5B', 'M5C',
                'M6A', 'M6B', 'M6C',
                'M7A', 'M7B', 'M7C',
                'M8A', 'M8B', 'M8C',
                'M9A', 'M9B', 'M9C'
            ],
            upperKeyStages: ['KS3 Foundation','KS3 Higher','U', 'E', 'D', 'C', 'B', 'A', 'A*', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            ks4Foundation: ['KS3 Foundation','KS3 Higher','1', '2', '3', '4', '5'],
            ks4Higher: ['KS3 Foundation','KS3 Higher','1', '2', '3', '4', '5', '6', '7', '8', '9']
        };

        // Generate session and subject options
        const sessionOptions = Array.from({ length: 26 }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('');
        const subjectOptions = subjects.map(subject => `<option value="${subject}">${subject}</option>`).join('');

        $(document).ready(function() {
            let counter = 0;
            let currentRowIndex = 0;
            let studentSubjectsMap = new Map();

            // Initialize Flatpickr
            flatpickr("#form_date, #joining_date, .dob-picker", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: { firstDayOfWeek: 1 }
            });

            // Initialize Medical Condition button
            $('#tbody .add-condition').prop('disabled', true);

            // --- Add Student Row ---
            $("#add-row").click(function() {
                counter++;
                let newRow = $("#template-row").clone();
                newRow.removeAttr("id");
                newRow.data('student-index', counter);
                newRow.attr('data-student-index', counter);
                newRow.find('input').val('');
                newRow.find('select').prop('selectedIndex', 0);
                newRow.find('.med-toggle').attr('id', 'medEnable_' + counter).prop('checked', false);
                newRow.find('.add-condition').attr('data-row-index', counter).prop('disabled', true);
                newRow.find('.remove-row').show();
                newRow.find('.medical-condition-data').val('');
                $("#tbody").append(newRow);

                newRow.find('.dob-picker').each(function() {
                    flatpickr(this, {
                        dateFormat: "d/m/Y",
                        allowInput: true,
                        locale: { firstDayOfWeek: 1 }
                    });
                });

                addStudentToSubjectsTable(counter);
            });

            // --- Remove Student Row ---
            $(document).on("click", ".remove-row", function() {
                if ($('#tbody .student-row').length <= 1) {
                    alert('Cannot remove the last student row.');
                    return;
                }
                const studentIndex = $(this).closest('tr').data('student-index');
                $(this).closest("tr").remove();
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"]`).remove();
                studentSubjectsMap.delete(studentIndex);
            });

            // --- Toggle Medical Condition ---
            $(document).on('change', '.med-toggle', function() {
                $(this).closest('td').find('.add-condition').prop('disabled', !$(this).is(':checked'));
            });

            // --- Add Medical Condition Modal ---
            $(document).on('click', '.add-condition:not(:disabled)', function(e) {
                e.preventDefault();
                currentRowIndex = $(this).closest('tr').index();
                let modal = $('#condition-modal');
                let medicalData = $(this).closest('tr').find('.medical-condition-data').val();
                modal.find('#doctor_name, #doctor_number, #maddress').val('');
                if (medicalData) {
                    try {
                        const data = JSON.parse(medicalData);
                        modal.find('#doctor_name').val(data.doctor_name || '');
                        modal.find('#doctor_number').val(data.doctor_number || '');
                        modal.find('#maddress').val(data.maddress || '');
                    } catch (e) {}
                }
                new bootstrap.Modal(document.getElementById('condition-modal')).show();
            });

            // --- Save Medical Condition ---
            $(document).on('click', '.save-condition', function() {
                const modal = $('#condition-modal');
                const medicalData = {
                    doctor_name: modal.find('#doctor_name').val(),
                    doctor_number: modal.find('#doctor_number').val(),
                    maddress: modal.find('#maddress').val()
                };
                const row = $('#tbody tr').eq(currentRowIndex);
                if (medicalData.doctor_name || medicalData.doctor_number || medicalData.maddress) {
                    row.find('.medical-condition-data').val(JSON.stringify(medicalData));
                    row.find('.med-toggle').prop('checked', true);
                    row.find('.add-condition').prop('disabled', false);
                } else {
                    row.find('.medical-condition-data').val('');
                    row.find('.med-toggle').prop('checked', false);
                    row.find('.add-condition').prop('disabled', true);
                }
                bootstrap.Modal.getInstance(document.getElementById('condition-modal')).hide();
            });

            // --- Add Student to Subject Table ---
            function addStudentToSubjectsTable(studentIndex) {
                const studentRow = $(`#tbody tr[data-student-index="${studentIndex}"]`);
                const firstName = studentRow.find('input[name="first_name[]"]').val() || `Student ${studentIndex + 1}`;
                studentSubjectsMap.set(studentIndex, { firstName, subjects: [] });

                const sectionHtml = `
                    <div class="student-subject-section" data-student-index="${studentIndex}">
                        <h3>Subjects for ${firstName}</h3>
                        <button type="button" class="btn btn-primary btn-sm add-subject-row" data-student-index="${studentIndex}">
                            <i class="fas fa-plus me-2"></i>Add Subject
                        </button>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Qualification</th>
                                        <th>Tier</th>
                                        <th>Lessons</th>
                                        <th>Current Grade</th>
                                        <th>Target Grade</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="subjects-tbody" data-student-index="${studentIndex}"></tbody>
                            </table>
                        </div>
                    </div>`;
                $('#subjects-sections').append(sectionHtml);
                addSubjectRow(studentIndex, firstName);

                studentRow.find('input[name="first_name[]"]').on('input', function() {
                    const newName = $(this).val() || `Student ${studentIndex + 1}`;
                    studentSubjectsMap.get(studentIndex).firstName = newName;
                    const sectionSelector = `#subjects-sections .student-subject-section[data-student-index="${studentIndex}"]`;
                    $(`${sectionSelector} h3`).text(`Subjects for ${newName}`);
                    $(`${sectionSelector} .subjects-tbody .student-name`).text(newName);
                });
            }

            // --- Add Subject Row ---
            function addSubjectRow(studentIndex, firstName) {
                const currentGradeOptionsHtml = getGradeOptions('', '', 'current', '');
                const targetGradeOptionsHtml = getGradeOptions('', '', 'target', '');

                const subjectRow = `
                    <tr data-student-index="${studentIndex}">
                        <td class="student-name">${firstName}</td>
                        <td>
                            <select name="subject_names[${studentIndex}][]" class="form-select" required>
                                <option value="" selected disabled>Select subject</option>
                                ${subjectOptions}
                            </select>
                        </td>
                        <td>
                            <select name="qualifications[${studentIndex}][]" class="form-select qualification-select" required>
                                <option value="" selected disabled>Select qualification</option>
                                <option value="KS1">KS1</option>
                                <option value="KS2">KS2</option>
                                <option value="KS3">KS3</option>
                                <option value="KS4">KS4</option>
                                <option value="KS5">KS5</option>
                            </select>
                        </td>
                        <td>
                            <select name="tiers[${studentIndex}][]" class="form-select tier-select" required>
                                <option value="" selected disabled>Select tier</option>
                                <option value="Higher Tier">Higher Tier</option>
                                <option value="Foundation Tier">Foundation Tier</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </td>
                        <td>
                            <select name="sessions[${studentIndex}][]" class="form-select" required>
                                <option value="" selected disabled>Select sessions</option>
                                ${sessionOptions}
                            </select>
                        </td>
                        <td>
                            <select name="current_grades[${studentIndex}][]" class="form-select current-grade" required>
                                <option value="" selected disabled>Select grade</option>
                                ${currentGradeOptionsHtml}
                            </select>
                        </td>
                        <td>
                            <select name="target_grades[${studentIndex}][]" class="form-select target-grade" required>
                                <option value="" selected disabled>Select grade</option>
                                ${targetGradeOptionsHtml}
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-subject-row">Remove</button>
                        </td>
                    </tr>`;
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody`).append(subjectRow);
            }

            // --- Get Grade Options ---
            function getGradeOptions(qualification, subject, gradeType, tier) {
                let grades = [];
                const qual = qualification ? qualification.toLowerCase() : '';
                const subj = subject ? subject.toLowerCase() : '';
                const tierLower = tier ? tier.toLowerCase() : 'n/a';

                if (['ks1', 'ks2', 'ks3'].includes(qual)) {
                    grades = gradeOptions.lowerKeyStages;
                } else if (['ks4', 'ks5'].includes(qual)) {
                    if (qual === 'ks4' && ['math', 'physics', 'chemistry', 'biology'].includes(subj)) {
                        if (tierLower === 'foundation tier') {
                            grades = gradeOptions.ks4Foundation;
                        } else if (tierLower === 'higher tier') {
                            grades = gradeOptions.ks4Higher;
                        } else {
                            grades = gradeOptions.upperKeyStages;
                        }
                    } else {
                        grades = gradeOptions.upperKeyStages;
                    }
                } else {
                    grades = gradeOptions.lowerKeyStages; // Default to lowerKeyStages if qualification is not selected
                }
                return grades.map(g => `<option value="${g}">${g}</option>`).join('');
            }

            // --- Update Grade Dropdowns ---
            function updateGradeDropdowns(studentIndex) {
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody tr`).each(function() {
                    const subject = $(this).find(`select[name^="subject_names"]`).val() || '';
                    const qualification = $(this).find('.qualification-select').val() || '';
                    const tier = $(this).find('.tier-select').val() || 'N/A';
                    const currentGradesHtml = getGradeOptions(qualification, subject, 'current', tier);
                    const targetGradesHtml = getGradeOptions(qualification, subject, 'target', tier);

                    const currentSelect = $(this).find('.current-grade');
                    const targetSelect = $(this).find('.target-grade');
                    const currentValue = currentSelect.val();
                    const targetValue = targetSelect.val();

                    currentSelect.html(`<option value="" disabled selected>Select grade</option>${currentGradesHtml}`);
                    targetSelect.html(`<option value="" disabled selected>Select grade</option>${targetGradesHtml}`);
                    if (currentValue && currentGradesHtml.includes(`value="${currentValue}"`)) currentSelect.val(currentValue);
                    if (targetValue && targetGradesHtml.includes(`value="${targetValue}"`)) targetSelect.val(targetValue);
                });
            }

            // --- Add / Remove Subject Row ---
            $(document).on('click', '.add-subject-row', function() {
                addSubjectRow($(this).data('student-index'), studentSubjectsMap.get($(this).data('student-index')).firstName);
            });
            $(document).on('click', '.remove-subject-row', function() {
                const row = $(this).closest('tr');
                const studentIndex = row.data('student-index');
                if ($(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody tr`).length <= 1) {
                    alert('Cannot remove the last subject row for this student.');
                    return;
                }
                row.remove();
            });

            // --- Update Grades When Qualification, Subject, or Tier Changes ---
            $(document).on('change', 'select[name^="subject_names"], .qualification-select, .tier-select', function() {
                updateGradeDropdowns($(this).closest('tr').data('student-index'));
            });

            // --- Initialize First Student ---
            $('#tbody tr:first').data('student-index', 0);
            $('#tbody tr:first').attr('data-student-index', 0);
            addStudentToSubjectsTable(0);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.needs-validation');
            if (!form) {
                console.error('Form with class .needs-validation not found');
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const resetButton = form.querySelector('button[type="reset"]');

            // Function to validate a single field
            function validateField(field) {
                let isValid = true;

                // Check for required field
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.style.borderColor = 'red';
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field.value.trim()) {
                    // Specific validation for guardian_name and kin_name
                    if (field.name === 'guardian_name' || field.name === 'kin_name') {
                        const nameRegex = /^[a-zA-Z\s]*$/;
                        if (!nameRegex.test(field.value)) {
                            field.style.borderColor = 'red';
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.style.borderColor = 'green';
                            field.classList.remove('is-invalid');
                        }
                    } else {
                        field.style.borderColor = 'green';
                        field.classList.remove('is-invalid');
                    }
                }
                return isValid;
            }

            // Real-time validation for all required fields and kin_name
            form.querySelectorAll('input[required], select[required], textarea[required], input[name="kin_name"]')
                .forEach(field => {
                    field.addEventListener('input', () => validateField(field));
                    field.addEventListener('change', () => validateField(field));

                    // Add keypress validation for guardian_name and kin_name
                    if (field.name === 'guardian_name' || field.name === 'kin_name') {
                        field.addEventListener('keypress', (event) => {
                            const nameRegex = /[a-zA-Z\s]/i;
                            if (!nameRegex.test(event.key)) {
                                event.preventDefault();
                            }
                        });
                    }
                });

            // Validate student table rows (dynamic fields)
            function validateStudentRows() {
                let isValid = true;
                const studentRows = document.querySelectorAll('#tbody .student-row');
                if (studentRows.length === 0) {
                    displayError('At least one student row is required.');
                    return false;
                }
                studentRows.forEach(row => {
                    const requiredFields = row.querySelectorAll('input[required], select[required]');
                    requiredFields.forEach(field => {
                        if (!validateField(field)) {
                            isValid = false;
                        }
                    });
                });
                return isValid;
            }

            // Display error message
            function displayError(message) {
                const existingError = form.querySelector('.alert-danger');
                if (existingError && !existingError.dataset.serverError) {
                    existingError.remove();
                }
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = message;
                form.prepend(errorDiv);
                setTimeout(() => {
                    if (errorDiv.parentNode) errorDiv.remove();
                }, 3000);
            }

            // Form submission handling
            let isSubmitting = false;
            submitButton.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                // Prevent multiple submissions
                if (isSubmitting) {
                    return false;
                }

                let isValid = true;

                // Validate all required fields and kin_name outside student table
                form.querySelectorAll(
                        'input[required], select[required], textarea[required], input[name="kin_name"]')
                    .forEach(field => {
                        if (!validateField(field)) {
                            isValid = false;
                        }
                    });

                // Validate student table rows
                if (!validateStudentRows()) {
                    isValid = false;
                }

                if (!isValid) {
                    displayError('Please fill out all required fields correctly.');
                } else {
                    // Combine package_amount and package_weeks into fee_detail before submission
                    const packageAmount = document.getElementById('package_amount').value;
                    const packageWeeks = document.getElementById('package_weeks').value;
                    if (packageAmount && packageWeeks) {
                        document.getElementById('fee_detail_hidden').value = packageAmount + ' for ' + packageWeeks.toLowerCase();
                    }
                    
                    // Set submitting flag and disable button
                    isSubmitting = true;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                    
                    // Remove client-side error messages but keep server-side errors
                    const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                    if (existingError) existingError.remove();
                    // Submit the form
                    HTMLFormElement.prototype.submit.call(form);
                }
            });

            // Prevent form double submission
            form.addEventListener('submit', function(event) {
                if (isSubmitting) {
                    event.preventDefault();
                    return false;
                }
                isSubmitting = true;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            });

            // Reset form and clear styles
            resetButton.addEventListener('click', function() {
                isSubmitting = false;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Submit Admission';
                form.querySelectorAll('input, select, textarea').forEach(field => {
                    field.style.borderColor = '#2149AF';
                    field.classList.remove('is-invalid');
                });
                const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                if (existingError) existingError.remove();
            });

            // Update package preview when amount or weeks change
            function updatePackagePreview() {
                const packageAmount = document.getElementById('package_amount').value;
                const packageWeeks = document.getElementById('package_weeks').value;
                const preview = document.getElementById('package_preview');
                
                if (packageAmount && packageWeeks) {
                    const packageValue = packageAmount + ' for ' + packageWeeks.toLowerCase();
                    preview.textContent = packageValue;
                    document.getElementById('fee_detail_hidden').value = packageValue;
                } else {
                    preview.textContent = '-';
                    document.getElementById('fee_detail_hidden').value = '';
                }
            }

            document.getElementById('package_amount').addEventListener('input', updatePackagePreview);
            document.getElementById('package_weeks').addEventListener('change', updatePackagePreview);
            // Initialize preview on page load
            updatePackagePreview();
        });
    </script>
@endsection
