@extends('layouts.branchDashboardApp')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .year-in-school.is-invalid {
            border-color: #dc3545;
        }



        /* General container styling */
        .container {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 2px solid #2047A8;
            margin-top: 20px;
        }

        /* Section title */
        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #2047A8;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid rgba(103, 192, 234, 0.3);
            padding-bottom: 10px;
        }

        /* Section divider */
        .section-divider {
            border-top: 2px solid rgba(103, 192, 234, 0.3);
            margin: 20px 0;
        }

        .text-info {
            color: #2045A3 !important;
        }

        /* Form group styling */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 8px;
        }

        /* Form control styling */
        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #2047A8;
            color: #495057;
            border-radius: 5px;
            padding: 8px 12px;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #2047A8;
            box-shadow: 0 0 0 0.25rem rgba(103, 192, 234, 0.25);
            color: #495057;
        }

        /* Submit button */
        .btn-submit {
            background-color: #2047A8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: #2047A8;
            border-color: #2047A8;
            transform: translateY(-2px);
        }

        /* Student section styling */
        .student-section {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(103, 192, 234, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .student-section h5 {
            font-size: 20px;
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 15px;
        }

        /* Radio and Checkbox Alignment */
        .form-check {
            margin-bottom: 10px;
        }

        .form-check-label {
            margin-left: 5px;
            color: #2047A8;
        }

        .form-check-input:checked {
            background-color: #2047A8;
            border-color: #2047A8;
        }

        /* Consent section styling */
        .consent-section {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(103, 192, 234, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .consent-section h4 {
            font-size: 22px;
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 15px;
        }

        /* Card section for other details */
        .card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(103, 192, 234, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #2047A8;
        }

        /* Add student button */
        .btn-primary {
            background-color: #2047A8;
            border-color: #2047A8;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2047A8;
            border-color: #2047A8;
            transform: translateY(-2px);
        }

        /* Remove button */
        .btn-danger {
            background-color: #ff4d4d;
            border-color: #ff4d4d;
            transition: all 0.3s;
        }

        .btn-danger:hover {
            background-color: #e63939;
            border-color: #e63939;
            transform: translateY(-2px);
        }

        /* Animation for fade-in effect */
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

        /* Input group text styling for Font Awesome icons */
        .input-group-text {
            background: transparent;
            color: #2047A8;
            border: 2px solid #2047A8;
            border-right: none;
            border-radius: 8px 0 0 8px;
            font-size: 16px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .input-group-text i {
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Subjects section styling */
        .student-subjects-section {
            margin-top: 20px;
            border-left: 4px solid #2047A8;
            padding-left: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .subject-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .subject-header h3 {
            margin: 0;
            font-size: 18px;
            color: #2047A8;
        }

        .table {
            color: #495057;
        }

        .table th {
            background: rgba(103, 192, 234, 0.1);
            color: #2047A8;
            border-bottom: 2px solid #2047A8;
        }

        .table td {
            vertical-align: middle;
        }

        .required-star {
            color: #ff4d4d;
            font-size: 12px;
        }
    </style>

    <div class="container mt-4" style="zoom:0.9;">
        <form action="{{ url('update-admission-form') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="family_id" value="{{ $family_id }}">
            <input type="hidden" name="guardianid" value="{{ $guardian->Guardianid ?? '' }}">
            <input type="hidden" name="kinid" value="{{ $kin->kinid ?? '' }}">
            <input type="hidden" name="admissionid" value="{{ $admission->admissionid ?? '' }}">

            <div class="student-section row mb-3" style="margin-left: 0; margin-right: 0;">
                <!-- Family ID -->
                <div class="col-md-3 mb-3">
                    <label class="text-info" for="family_id" style="font-weight: bold; font-size:18px;">Family ID</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="family_id" readonly class="form-control"
                            value="{{ $family_id ?? '3454' }}">
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="form_date" style="font-weight: bold; font-size:18px;" class="text-info">
                        Form Filling Date <span class="required-star">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="text" autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                            name="form_date" id="form_date" class="form-control flatpickr-date" placeholder="dd/mm/yyyy"
                            value="{{ @$admission->formfilingdate ? \Carbon\Carbon::parse($admission->formfilingdate)->format('d/m/Y') : '' }}"
                            required>
                    </div>
                </div>

                <!-- Joining Date -->
                <div class="col-md-3 mb-3">
                    <label for="joining_date" style="font-weight: bold; font-size:18px;" class="text-info">
                        Joining Date <span class="required-star">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                        <input type="text" autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                            name="joining_date" id="joining_date" class="form-control flatpickr-date"
                            placeholder="dd/mm/yyyy"
                            value="{{ @$admission->joiningdate ? \Carbon\Carbon::parse($admission->joiningdate)->format('d/m/Y') : '' }}"
                            required>
                    </div>
                </div>

                <!-- Family Status -->
                <div class="col-md-3 mb-3">
                    <label for="family_status" style="font-weight: bold; font-size:18px;" class="text-info">
                        Status <span class="required-star">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                        <select name="family_status" id="family_status" class="form-select" required>
                            <option value="" disabled>Choose</option>
                            <option value="Active" {{ @$admission->familystatus == 'Active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="De-Active" {{ @$admission->familystatus == 'De-Active' ? 'selected' : '' }}>
                                De-Active</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <button type="button" class="form-control btn btn-primary" id="add-student-btn"
                        style="color: white !important;">Add Student</button>
                </div>
            </div>
            <div id="appendStudent">
                <!-- Inside the student loop -->
                @for ($i = 0; $i < count($students); $i++)
                    <div class="replicate" data-student-index="{{ $i }}">
                        <div class="student-section" style="margin-bottom: 20px;">
                            <input type="hidden" name="student[{{ $i }}][studentid]"
                                value="{{ $students[$i]->studentid ?? '' }}">
                            <input type="hidden" name="student[{{ $i }}][oldName]"
                                value="{{ ($students[$i]->studentname ?? '') . ' ' . ($students[$i]->studentsur ?? '') }}">
                            <button type="button" class="btn btn-danger remove-student-btn"
                                style="float: right; margin-top: 10px;">Remove</button>
                            <h5>Student {{ $i + 1 }}</h5>
                            <div class="row">
                                <!-- Personal Details -->
                                <div class="col-md-4 form-group">
                                    <label>
                                        First Name <span class="required-star">*</span>
                                    </label>
                                    <div class="input-group" style="position:relative;">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="student[{{ $i }}][firstName]"
                                            class="form-control student-first-name"
                                            data-student-index="{{ $i }}"
                                            value="{{ $students[$i]->studentname ?? '' }}" required
                                            onkeypress="return /[a-zA-Z\s]/i.test(event.key)"
                                            style="{{ (!empty($students[$i]->is_flag) && $students[$i]->is_flag == 1) ? 'padding-right: 32px;' : '' }}">
                                        @if (!empty($students[$i]->is_flag) && $students[$i]->is_flag == 1)
                                            <span title="Flagged Student" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); color:red; font-size:16px; pointer-events:none; z-index:5;">&#x1F6A9;</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Last Name <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="student[{{ $i }}][lastName]"
                                            class="form-control" value="{{ $students[$i]->studentsur ?? '' }}" required
                                            onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                                    </div>
                                </div>
                                {{-- {{dd($students[$i]->student_status)}} --}}
                                <div class="col-md-4 mb-3">
                                    <label for="student_status" style="font-weight: bold; font-size:18px;">Student Status
                                        <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                                        <select name="student[{{ $i }}][student_status]" id="student_status"
                                            class="form-select" required>
                                            <option value="" disabled>Choose</option>
                                            <option value="active"
                                                {{ $students[$i]->student_status == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive"
                                                {{ $students[$i]->student_status == 'inactive' ? 'selected' : '' }}>
                                                In-Active</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Date of Birth <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="text" name="student[{{ $i }}][dob]"
                                            autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                                            class="form-control flatpickr-date" placeholder="dd/mm/yyyy"
                                            value="{{ @$students[$i]->studentdob ? \Carbon\Carbon::parse($students[$i]->studentdob)->format('d/m/Y') : '' }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Gender <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                        <select name="student[{{ $i }}][gender]" class="form-select" required>
                                            @php $gender = strtolower($students[$i]->studentgender ?? ''); @endphp
                                            <option value="" disabled>Select gender</option>
                                            <option value="Male" {{ $gender == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ $gender == 'female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="Other" {{ $gender == 'other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Year in School <small class="text-muted">(auto from DOB)</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-school"></i></span>
                                        @php
                                            $currentYear = $students[$i]->studentyearinschool ?? '';
                                        @endphp
                                        <select id="year-in-school-display-{{ $i }}"
                                            class="form-select year-in-school-display bg-light"
                                            data-student-index="{{ $i }}"
                                            disabled>
                                            <option value="" {{ $currentYear === '' ? 'selected' : '' }}>-- Auto assigned --</option>
                                            @for ($year = 1; $year <= 13; $year++)
                                                <option value="{{ $year }}" {{ (string)$currentYear === (string)$year ? 'selected' : '' }}>
                                                    Year {{ $year }}</option>
                                            @endfor
                                            <option value="Reception" {{ $currentYear === 'Reception' ? 'selected' : '' }}>Reception</option>
                                            <option value="Adult" {{ $currentYear === 'Adult' ? 'selected' : '' }}>Adult</option>
                                        </select>
                                        {{-- Hidden input carries the actual value to the server --}}
                                        <input type="hidden"
                                            name="student[{{ $i }}][yearInSchool]"
                                            id="year-in-school-hidden-{{ $i }}"
                                            value="{{ $currentYear }}">
                                    </div>
                                </div>
                                <!-- Medical Conditions Dropdown -->
                                <div class="col-md-4 form-group">
                                    <label>Medical Conditions</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                                        <select name="student[{{ $i }}][has_medical_conditions]"
                                            class="form-select medical-conditions-select"
                                            data-student-index="{{ $i }}">
                                            <option value="" disabled
                                                {{ is_null($students[$i]->medical_condition) ? 'selected' : '' }}>Select
                                                option</option>
                                            <option value="yes"
                                                {{ ($students[$i]->medical_condition ?? '') == 'yes' ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="no"
                                                {{ ($students[$i]->medical_condition ?? '') == 'no' ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Medical Conditions Explanation -->
                                <div class="col-md-4 form-group medical-conditions-explanation"
                                    data-student-index="{{ $i }}"
                                    style="display: {{ ($students[$i]->medical_condition ?? '') == 'yes' ? 'block' : 'none' }};">
                                    <label>Medical Conditions Details</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                                        <textarea name="student[{{ $i }}][medical_conditions_explanation]" class="form-control"
                                            placeholder="Enter medical conditions details">{{ $students[$i]->medical_conditions_explanation ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!-- Allergies Dropdown -->
                                <div class="col-md-4 form-group">
                                    <label>Allergies</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-allergies"></i></span>
                                        <select name="student[{{ $i }}][has_allergies]"
                                            class="form-select allergies-select"
                                            data-student-index="{{ $i }}">
                                            <option value="" disabled
                                                {{ is_null($students[$i]->allergies) ? 'selected' : '' }}>Select option
                                            </option>
                                            <option value="yes"
                                                {{ ($students[$i]->allergies ?? '') == 'yes' ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="no"
                                                {{ ($students[$i]->allergies ?? '') == 'no' ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Allergies Explanation -->
                                <div class="col-md-4 form-group allergies-explanation"
                                    data-student-index="{{ $i }}"
                                    style="display: {{ ($students[$i]->allergies ?? '') == 'yes' ? 'block' : 'none' }};">
                                    <label>Allergies Details</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-allergies"></i></span>
                                        <textarea name="student[{{ $i }}][allergies_explanation]" class="form-control"
                                            placeholder="Enter allergies details">{{ $students[$i]->allergies_explanation ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!-- Additional Needs Dropdown -->
                                <div class="col-md-4 form-group">
                                    <label>Additional Needs</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hands-helping"></i></span>
                                        <select name="student[{{ $i }}][has_additional_needs]"
                                            class="form-select additional-needs-select"
                                            data-student-index="{{ $i }}">
                                            <option value="" disabled
                                                {{ is_null($students[$i]->additionalNeeds) ? 'selected' : '' }}>Select
                                                option</option>
                                            <option value="yes"
                                                {{ ($students[$i]->additionalNeeds ?? '') == 'yes' ? 'selected' : '' }}>Yes
                                            </option>
                                            <option value="no"
                                                {{ ($students[$i]->additionalNeeds ?? '') == 'no' ? 'selected' : '' }}>No
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Additional Needs Explanation -->
                                <div class="col-md-4 form-group additional-needs-explanation"
                                    data-student-index="{{ $i }}"
                                    style="display: {{ ($students[$i]->additionalNeeds ?? '') == 'yes' ? 'block' : 'none' }};">
                                    <label>Additional Needs Details</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-hands-helping"></i></span>
                                        <textarea name="student[{{ $i }}][additional_needs_explanation]" class="form-control"
                                            placeholder="Enter additional needs details">{{ $students[$i]->additional_needs_explanation ?? '' }}</textarea>
                                    </div>
                                </div>
                                <!-- GP Details -->
                                {{-- <div class="col-md-4 form-group">
                    <label>GP Prefix</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                        <select name="student[{{ $i }}][gpPrefix]" class="form-select">
                            <option value="Dr." {{ ($students[$i]->medical_condition_array->gpPrefix ?? '') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                            <option value="Mr." {{ ($students[$i]->medical_condition_array->gpPrefix ?? '') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                            <option value="Ms." {{ ($students[$i]->medical_condition_array->gpPrefix ?? '') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                        </select>
                    </div>
                </div> --}}
                                <div class="col-md-4 form-group">
                                    <label>GP Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="student[{{ $i }}][gpFirstName]"
                                            class="form-control"
                                            value="{{ explode(' ', $students[$i]->medical_condition_array->drName ?? '')[0] ?? '' }}"
                                            onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                                    </div>
                                </div>
                                {{-- <div class="col-md-4 form-group">
                    <label>GP Last Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="student[{{ $i }}][gpLastName]" class="form-control" value="{{ explode(' ', $students[$i]->medical_condition_array->drName ?? '')[1] ?? '' }}" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" name="student[{{ $i }}][gpAddress]" class="form-control" value="{{ $students[$i]->medical_condition_array->gpAddress ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP Address Line 2</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        <input type="text" name="student[{{ $i }}][gpAddressLineTwo]" class="form-control" value="{{ $students[$i]->medical_condition_array->gpAddressLineTwo ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP City</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                        <input type="text" name="student[{{ $i }}][gp_city]" class="form-control" value="{{ $students[$i]->medical_condition_array->gp_city ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP County / State / Region</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-map"></i></span>
                        <input type="text" name="student[{{ $i }}][gp_countyStateRegion]" class="form-control" value="{{ $students[$i]->medical_condition_array->gp_countyStateRegion ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP ZIP / Postal Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                        <input type="text" name="student[{{ $i }}][gpzipCode]" class="form-control" value="{{ $students[$i]->medical_condition_array->gpzipCode ?? '' }}">
                    </div>
                </div>
                <div class="col-md-4 form-group">
                    <label>GP Country</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                        <select name="student[{{ $i }}][gpcountry]" class="form-select">
                            <option value="United Kingdom" {{ ($students[$i]->medical_condition_array->gpcountry ?? '') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                        </select>
                    </div>
                </div> --}}
                                <div class="col-md-4 form-group">
                                    <label>GP Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" name="student[{{ $i }}][GPPhone]"
                                            class="form-control"
                                            value="{{ $students[$i]->medical_condition_array->drNumber ?? '' }}"
                                            onkeypress="return /\d/.test(event.key)">
                                    </div>
                                </div>
                                <div class="col-md-4 form-group mt-4">
                                    <label>Medical Consent</label>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="student[{{ $i }}][medicalConsent]"
                                            value="yes" class="form-check-input"
                                            {{ ($students[$i]->medicalConsent ?? '') == 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="student[{{ $i }}][medicalConsent]"
                                            value="no" class="form-check-input"
                                            {{ ($students[$i]->medicalConsent ?? '') == 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Photo Consent</label>
                                    <div class="form-check">
                                        <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                            value="website" class="form-check-input"
                                            {{ in_array('website', json_decode($students[$i]->photoConsent ?? '[]', true) ?: []) ? 'checked' : '' }}>
                                        <label class="form-check-label">Website</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                            value="socialMedia" class="form-check-input"
                                            {{ in_array('socialMedia', json_decode($students[$i]->photoConsent ?? '[]', true) ?: []) ? 'checked' : '' }}>
                                        <label class="form-check-label">Social Media</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                            value="marketing" class="form-check-input"
                                            {{ in_array('marketing', json_decode($students[$i]->photoConsent ?? '[]', true) ?: []) ? 'checked' : '' }}>
                                        <label class="form-check-label">Marketing</label>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Leave Alone</label>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="student[{{ $i }}][leaveAlone]"
                                            value="yes" class="form-check-input"
                                            {{ ($students[$i]->leaveAlone ?? '') == 'yes' ? 'checked' : '' }}>
                                        <label class="form-check-label">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" name="student[{{ $i }}][leaveAlone]"
                                            value="no" class="form-check-input"
                                            {{ ($students[$i]->leaveAlone ?? '') == 'no' ? 'checked' : '' }}>
                                        <label class="form-check-label">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider"></div>
                @endfor

                <!-- JavaScript for Dynamic Display -->
            @section('scripts')
                <script>
                    $(document).ready(function() {
                        // Medical Conditions Toggle
                        $('.medical-conditions-select').change(function() {
                            var index = $(this).data('student-index');
                            var explanationDiv = $('.medical-conditions-explanation[data-student-index="' + index +
                                '"]');
                            if ($(this).val() === 'yes') {
                                explanationDiv.show();
                            } else {
                                explanationDiv.hide();
                            }
                        });

                        // Allergies Toggle
                        $('.allergies-select').change(function() {
                            var index = $(this).data('student-index');
                            var explanationDiv = $('.allergies-explanation[data-student-index="' + index + '"]');
                            if ($(this).val() === 'yes') {
                                explanationDiv.show();
                            } else {
                                explanationDiv.hide();
                            }
                        });

                        // Additional Needs Toggle
                        $('.additional-needs-select').change(function() {
                            var index = $(this).data('student-index');
                            var explanationDiv = $('.additional-needs-explanation[data-student-index="' + index + '"]');
                            if ($(this).val() === 'yes') {
                                explanationDiv.show();
                            } else {
                                explanationDiv.hide();
                            }
                        });

                        // Remove Student Button (if needed)
                        $('.remove-student-btn').click(function() {
                            $(this).closest('.replicate').remove();
                        });
                    });
                </script>
            @endsection
        </div>

        <!-- Parent/Guardian 1 Section -->
        <div class="row"
            style="margin-left: 0; margin-right: 0; background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 1px solid rgba(103, 192, 234, 0.3); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-bottom: 20px;">
            <div class="col-md-12 formengage">
                <h4 class="section-title text-info">Parent / Guardian 1 Details</h4>
            </div>
            <div class="col-md-4 form-group">
                <label>Parent / Guardian Name <span class="required-star">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="parent1_first_name" class="form-control"
                        value="{{ $guardianFirstName ?? '' }}" required
                        placeholder="First Name"
                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Last Name <span class="required-star">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="parent1_last_name" class="form-control"
                        value="{{ $guardianLastName ?? '' }}" required
                        placeholder="Last Name"
                        onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="parent1_Address" class="form-control"
                        value="{{ $guardian->guardianaddress ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Address Line 2</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="parent1_Address_line2" class="form-control"
                        value="{{ $guardian->address_line_2 ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>City</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                    <input type="text" name="parent1_city" class="form-control"
                        value="{{ $guardian->city ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>County / State / Region</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map"></i></span>
                    <input type="text" name="parent1_country_state_region" class="form-control"
                        value="{{ $guardian->countyStateRegion ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>ZIP / Postal Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                    <input type="text" name="parent1_zipCode" class="form-control"
                        value="{{ $guardian->zIPCode ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Country</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                    <select name="parent1_country" class="form-select">
                        <option value="United Kingdom"
                            {{ ($guardian->country ?? '') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="parent1_email" class="form-control"
                        value="{{ $guardian->guardiantel ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Mobile</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" name="parent1_mobile" class="form-control"
                        value="{{ $guardian->guardianmob ?? '' }}" onkeypress="return /\d/.test(event.key)">
                </div>
            </div>
        </div>

        <!-- Emergency Contact 1 Section -->
        <div class="row"
            style="margin-left: 0; margin-right: 0; background: rgba(255, 255, 255, 0.1); border-radius: 10px; border: 1px solid rgba(103, 192, 234, 0.3); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-bottom: 20px;">
            <div class="col-md-12 form-group">
                <h4 class="section-title">Emergency Contact 1 Details</h4>
            </div>
            <div class="col-md-4 form-group">
                <label>Kin Name <span class="required-star">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="emergency_conatct1_first_name" class="form-control"
                        value="{{ @$kin->kinname }}" required onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="emergency_conatct1_Address" class="form-control"
                        value="{{ $kin->kinaddress ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Address Line 2</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="emergency_conatct1_Address_line2" class="form-control"
                        value="{{ $kin->emergency_conatct1_Address_line2 ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>City</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                    <input type="text" name="emergency_conatct1_city" class="form-control"
                        value="{{ $kin->emergency_conatct1_city ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>County / State / Region</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map"></i></span>
                    <input type="text" name="emergency_conatct1_country_state_region" class="form-control"
                        value="{{ $kin->emergency_conatct1_country_state_region ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>ZIP / Postal Code</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                    <input type="text" name="emergency_conatct1_zipCode" class="form-control"
                        value="{{ $kin->emergency_conatct1_zipCode ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Country</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                    <select name="emergency_conatct1_country" class="form-select">
                        <option value="United Kingdom"
                            {{ ($kin->emergency_conatct1_country ?? '') == 'United Kingdom' ? 'selected' : '' }}>United
                            Kingdom</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="emergency_conatct1_email" class="form-control"
                        value="{{ $kin->kintel ?? '' }}">
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label>Mobile</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    <input type="text" name="emergency_conatct1_mobile" class="form-control"
                        value="{{ $kin->kinmob ?? '' }}" onkeypress="return /\d/.test(event.key)">
                </div>
            </div>
        </div>

        <!-- Consent Section -->
        <div class="consent-section mt-4">
            <h4>Consent <span class="required-star">*</span></h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label text-info">Parent / Guardian Name <span
                            class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="consent_1_first_name"
                        value="{{ $guardianFirstName ?? '' }}"  placeholder="First Name"
                            class="form-control" required onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-info">Last Name <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="consent_1_last_name"
                            value="{{ $guardianLastName ?? $guardianLastName ?? '' }}" placeholder="Last Name"
                            class="form-control" required onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                    </div>
                </div>
            </div>
            <br>
            <input type="hidden" name="signature" value="{{ $consent->consent_1signature ?? '' }}">
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="signature" class="text-info">Signature:</label>
                    <div class="border p-2 rounded" style="background: rgba(255, 255, 255, 0.2);">
                        <img src="{{ $consent->consent_1signature ?? '' }}" alt="Signature 1"
                            style="width: 100%; height: auto;">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="consent_1date" class="text-info">Date:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <input type="text" id="consent_1date" name="consent_1date"
                            class="form-control flatpickr-date" placeholder="dd/mm/yyyy"
                            value="{{ @$consent->consent_1_date ? \Carbon\Carbon::parse($consent->consent_1_date)->format('d/m/Y') : '' }}"
                            required>
                    </div>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="col-md-12 mb-3 mt-4" style="margin-top: 3.5rem !important;">
                    <label for="how_did_you_hear" class="form-label text-info">How did you hear about us?</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-question-circle"></i></span>
                        <select name="how_did_you_hear" id="how_did_you_hear" class="form-select">
                            <option value="" {{ ($consent->how_did_you_hear ?? '') == '' ? 'selected' : '' }}>
                                Please Select...</option>
                            <option value="Social Media"
                                {{ ($consent->how_did_you_hear ?? '') == 'Social Media' ? 'selected' : '' }}>Social
                                Media</option>
                            <option value="Search Engine"
                                {{ ($consent->how_did_you_hear ?? '') == 'Search Engine' ? 'selected' : '' }}>Search
                                Engine</option>
                            <option value="Leaflets"
                                {{ ($consent->how_did_you_hear ?? '') == 'Leaflets' ? 'selected' : '' }}>Leaflets
                            </option>
                            <option value="Friends"
                                {{ ($consent->how_did_you_hear ?? '') == 'Friends' ? 'selected' : '' }}>Friends
                            </option>
                            <option value="Other"
                                {{ ($consent->how_did_you_hear ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="student_2" style="flex: 1 1 50%;">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="border p-4 rounded">
                            <div class="card-title d-flex align-items-center">
                                <i class="bx bxs-user me-1 font-22 text-info"></i>
                                <h3 class="mb-0 text-info" style="font-weight: bold; font-size:18px; color: #2045A3;">
                                    See Other detail</h3>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="medical_condition" class="text-info"
                                        style="font-weight: bold; font-size:18px;">Does Any Child Have Medical
                                        Conditions?</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-notes-medical"></i></span>
                                        <textarea name="medical_condition" class="form-control" cols="50" rows="5" placeholder="comment here">{{ @$admission->medicalcondition }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="child_school" style="font-weight: bold; font-size:18px;"
                                        class="text-info">Does any child attend any other school?</label>
                                    <div class="mt-2">
                                        @php $childSchoolChecked = !empty($admission->child_name1) && !empty($admission->school_name1); @endphp
                                        <input type="radio" id="yes" name="child_school" value="yes"
                                            {{ $childSchoolChecked ? 'checked' : '' }} onclick="toggleInputs(true)">
                                        <label for="yes">Yes</label>
                                        <input type="radio" id="no" name="child_school" value="no"
                                            {{ !$childSchoolChecked ? 'checked' : '' }}
                                            onclick="toggleInputs(false)">
                                        <label for="no">No</label>
                                    </div>
                                    <div class="mt-3" id="childSchoolInputs"
                                        style="display: {{ $childSchoolChecked ? 'block' : 'none' }}; zoom:0.8">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-user"></i></span>
                                                        <input type="text" placeholder="Enter Child Name"
                                                            id="child_name{{ $i }}"
                                                            value="{{ $admission->{'child_name' . $i} ?? '' }}"
                                                            name="child_name{{ $i }}"
                                                            class="form-control"
                                                            onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i
                                                                class="fas fa-school"></i></span>
                                                        <input type="text" placeholder="Enter School Name"
                                                            id="school_name{{ $i }}"
                                                            value="{{ $admission->{'school_name' . $i} ?? '' }}"
                                                            name="school_name{{ $i }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="text-info" for="fee_detail"
                                    style="font-weight: bold; font-size:18px;">Fee Details <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
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
                            <div class="col-md-12">
                                <label for="payment_method" class="text-info"
                                    style="font-weight: bold; font-size:18px;">Initial Payment <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                    <select name="payment_method" class="form-select" required>
                                        <option value="" disabled>Choose an option</option>
                                        <option {{ @$admission->payment_method == 'Cash Payment' ? 'selected' : '' }}
                                            value="Cash Payment">Cash Payment</option>
                                        <option {{ @$admission->payment_method == 'Card Payment' ? 'selected' : '' }}
                                            value="Card Payment">Card Payment</option>
                                        <option {{ @$admission->payment_method == 'Bank Transfer' ? 'selected' : '' }}
                                            value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="add_comment" class="text-info"
                                    style="font-weight: bold; font-size:18px;">Comment</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea style="margin-top: 20px;" name="add_comment" class="form-control" cols="50" rows="5"
                                        placeholder="comment here">{{ @$admission->add_comment }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects and Sessions Section -->
            <div class="subjects-container" id="subjects-container">
                @for ($i = 0; $i < count($students); $i++)
                    <div class="student-subjects-section" id="student-subjects-{{ $i }}"
                        data-student-index="{{ $i }}">
                        <div class="subject-header">
                            <i class="fas fa-book me-2"></i>
                            <h3>Subjects for {{ $students[$i]->studentname ?? 'Student ' . ($i + 1) }}</h3>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm add-subject-row"
                            data-student-index="{{ $i }}">
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
                                <tbody class="subjects-tbody" data-student-index="{{ $i }}">
                                    @php
                                        $subjectNames = is_array($students[$i]->subject_names)
                                            ? $students[$i]->subject_names
                                            : (is_string($students[$i]->subject_names) &&
                                            is_array(json_decode($students[$i]->subject_names, true))
                                                ? json_decode($students[$i]->subject_names, true)
                                                : []);

                                        $qualifications = is_array($students[$i]->qualifications)
                                            ? $students[$i]->qualifications
                                            : (is_string($students[$i]->qualifications) &&
                                            is_array(json_decode($students[$i]->qualifications, true))
                                                ? json_decode($students[$i]->qualifications, true)
                                                : []);

                                        $tiers = is_array($students[$i]->tier)
                                            ? $students[$i]->tier
                                            : (is_string($students[$i]->tier) &&
                                            is_array(json_decode($students[$i]->tier, true))
                                                ? json_decode($students[$i]->tier, true)
                                                : []);

                                        $sessions = is_array($students[$i]->studenthours)
                                            ? $students[$i]->studenthours
                                            : (is_string($students[$i]->studenthours) &&
                                            is_array(json_decode($students[$i]->studenthours, true))
                                                ? json_decode($students[$i]->studenthours, true)
                                                : []);

                                        $currentGrades = is_array($students[$i]->current_grades)
                                            ? $students[$i]->current_grades
                                            : (is_string($students[$i]->current_grades) &&
                                            is_array(json_decode($students[$i]->current_grades, true))
                                                ? json_decode($students[$i]->current_grades, true)
                                                : []);

                                        $targetGrades = is_array($students[$i]->target_grades)
                                            ? $students[$i]->target_grades
                                            : (is_string($students[$i]->target_grades) &&
                                            is_array(json_decode($students[$i]->target_grades, true))
                                                ? json_decode($students[$i]->target_grades, true)
                                                : []);

                                        $maxCount = max(
                                            is_array($subjectNames) ? count($subjectNames) : 0,
                                            is_array($qualifications) ? count($qualifications) : 0,
                                            is_array($tiers) ? count($tiers) : 0,
                                            is_array($sessions) ? count($sessions) : 0,
                                            is_array($currentGrades) ? count($currentGrades) : 0,
                                            is_array($targetGrades) ? count($targetGrades) : 0,
                                            1, // Ensure at least one row
                                        );
                                    @endphp
                                    @for ($j = 0; $j < $maxCount; $j++)
                                        <tr data-student-index="{{ $i }}">
                                            <td class="student-name">
                                                {{ $students[$i]->studentname ?? 'Student ' . ($i + 1) }}</td>
                                            <td>
                                                <select name="student[{{ $i }}][subject_names][]"
                                                    class="form-select subject-select" required>
                                                    <option value="" disabled
                                                        {{ empty($subjectNames[$j]) ? 'selected' : '' }}>Select
                                                        subject</option>
                                                    @foreach ($subjects as $subject)
                                                        <option value="{{ $subject }}"
                                                            {{ ($subjectNames[$j] ?? '') === $subject ? 'selected' : '' }}>
                                                            {{ $subject }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="student[{{ $i }}][qualifications][]"
                                                    class="form-select qualification-select" required>
                                                    <option value="" disabled
                                                        {{ empty($qualifications[$j]) ? 'selected' : '' }}>Select
                                                        qualification</option>
                                                    <option value="KS1"
                                                        {{ ($qualifications[$j] ?? '') === 'KS1' ? 'selected' : '' }}>
                                                        KS1</option>
                                                    <option value="KS2"
                                                        {{ ($qualifications[$j] ?? '') === 'KS2' ? 'selected' : '' }}>
                                                        KS2</option>
                                                    <option value="KS3"
                                                        {{ ($qualifications[$j] ?? '') === 'KS3' ? 'selected' : '' }}>
                                                        KS3</option>
                                                    <option value="KS4"
                                                        {{ ($qualifications[$j] ?? '') === 'KS4' ? 'selected' : '' }}>
                                                        KS4</option>
                                                    <option value="KS5"
                                                        {{ ($qualifications[$j] ?? '') === 'KS5' ? 'selected' : '' }}>
                                                        KS5</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="student[{{ $i }}][tiers][]"
                                                    class="form-select tier-select">
                                                    <option value="" disabled
                                                        {{ empty($tiers[$j]) ? 'selected' : '' }}>Select tier</option>
                                                    <option value="Higher Tier"
                                                        {{ ($tiers[$j] ?? '') === 'Higher Tier' ? 'selected' : '' }}>
                                                        Higher Tier</option>
                                                    <option value="Foundation Tier"
                                                        {{ ($tiers[$j] ?? '') === 'Foundation Tier' ? 'selected' : '' }}>
                                                        Foundation Tier</option>
                                                    <option value="N/A"
                                                        {{ ($tiers[$j] ?? '') === 'N/A' ? 'selected' : '' }}>N/A
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="student[{{ $i }}][sessions][]"
                                                    class="form-select" required>
                                                    <option value="" disabled
                                                        {{ empty($sessions[$j]) ? 'selected' : '' }}>Select sessions
                                                    </option>
                                                    @for ($s = 1; $s <= 26; $s++)
                                                        <option value="{{ $s }}"
                                                            {{ ($sessions[$j] ?? '') == $s ? 'selected' : '' }}>
                                                            {{ $s }}</option>
                                                    @endfor
                                                </select>
                                            </td>
                                            <td>
                                                <select name="student[{{ $i }}][current_grades][]"
                                                    class="form-select current-grade"
                                                    data-current-grade="{{ $currentGrades[$j] ?? '' }}" required>
                                                    <option value="" disabled selected>Select grade</option>
                                                    <!-- Grades populated by JavaScript -->
                                                </select>
                                            </td>
                                            <td>
                                                <select name="student[{{ $i }}][target_grades][]"
                                                    class="form-select target-grade"
                                                    data-target-grade="{{ $targetGrades[$j] ?? '' }}" required>
                                                    <option value="" disabled selected>Select grade</option>
                                                    <!-- Grades populated by JavaScript -->
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-subject-row">Remove</button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-submit mb-5" id="submitAll">Update Records</button>
            </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {

  // ── Auto-assign Year in School from DOB ────────────────────────────────
// Year field is readonly (disabled select + hidden input). DOB change → AJAX.
function autoAssignYearFromDob(dobValue, studentIndex) {
    if (!dobValue) return;
    $.get('{{ route("get.year.from.dob") }}', { dob: dobValue }, function(res) {
        const year = res.year;
        const $display = $('#year-in-school-display-' + studentIndex);
        const $hidden  = $('#year-in-school-hidden-'  + studentIndex);

        if (year) {
            if ($display.find('option[value="' + year + '"]').length === 0) {
                $display.append('<option value="' + year + '">' + year + '</option>');
            }
            $display.val(year);
            $hidden.val(year);
        } else {
            $display.val('');
            $hidden.val('');
        }
    });
}

// Fire on DOB change (flatpickr fires native "change" after selection)
$(document).on('change', '.flatpickr-date', function() {
    const dobValue = $(this).val();
    const name     = $(this).attr('name');   // student[0][dob]
    const match    = name && name.match(/student\[(\d+)\]/);
    if (match) {
        autoAssignYearFromDob(dobValue, match[1]);
    }
});

        // Define grade options
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
                'KS3 Foundation', 'KS3 Higher',
                'KS2 SATs Book 1', 'KS2 SATs Book 2', 'KS2 SATs Book 3',
                'KS2 SATs Past Paper Booklet 1', 'KS2 SATs Past Paper Booklet 2',
                'KS2 SATs Past Paper Booklet 3',
                '1 Plus Math', 'Level 3 Math', 'Level 4 Math', 'Level 5 Math',
                'Math Exam Booklet 1', 'Math Exam Booklet 2',
                'Math 11 Plus Book 1', 'Math 11 Plus Book 2',
                'M1A', 'M1B', 'M1C', 'M2A', 'M2B', 'M2C',
                'M3A', 'M3B', 'M3C', 'M4A', 'M4B', 'M4C',
                'M5A', 'M5B', 'M5C', 'M6A', 'M6B', 'M6C',
                'M7A', 'M7B', 'M7C', 'M8A', 'M8B', 'M8C',
                'M9A', 'M9B', 'M9C'
            ],
            upperKeyStages: ['KS3 Foundation', 'KS3 Higher', 'U', 'E', 'D', 'C', 'B', 'A', 'A*', '1', '2',
                '3', '4', '5', '6', '7', '8', '9'
            ],
            ks4Foundation: ['KS3 Foundation', 'KS3 Higher', '1', '2', '3', '4', '5'],
            ks4Higher: ['KS3 Foundation', 'KS3 Higher', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        };

        // Pass PHP subjects array to JavaScript
        const subjects = @json($subjects);
        const sessionOptions = Array.from({
            length: 26
        }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('');
        const subjectOptions = subjects.map(subject => `<option value="${subject}">${subject}</option>`).join(
            '');

        // Initialize Flatpickr for date fields
        flatpickr(".flatpickr-date", {
            dateFormat: "d/m/Y",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1
            }
        });

        // On page load: auto-fill year from DOB for ALL students (always correct value)
        @for ($i = 0; $i < count($students); $i++)
            @if (!empty($students[$i]->studentdob))
                autoAssignYearFromDob('{{ \Carbon\Carbon::parse($students[$i]->studentdob)->format('d/m/Y') }}', {{ $i }});
            @endif
        @endfor

        // Initialize student subjects map
        let studentSubjectsMap = new Map();
        @for ($i = 0; $i < count($students); $i++)
            studentSubjectsMap.set({{ $i }}, {
                firstName: "{{ $students[$i]->studentname ?? 'Student ' . ($i + 1) }}",
                subjects: []
            });
        @endfor

        let studentIndex = $('.replicate').length;

        // Toggle child school inputs
        window.toggleInputs = function(show) {
            const childSchoolInputs = document.getElementById('childSchoolInputs');
            if (childSchoolInputs) {
                childSchoolInputs.style.display = show ? 'block' : 'none';
            } else {
                console.warn('Element #childSchoolInputs not found');
            }
        };

        // Get grade options
        function getGradeOptions(qualification, subject, tier) {
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
                grades = gradeOptions.lowerKeyStages;
            }
            return grades.map(g => `<option value="${g}">${g}</option>`).join('');
        }

        // Update grade dropdowns for a specific row
        function updateGradeDropdowns(row) {
            const $row = $(row);
            const subject = $row.find('.subject-select').val() || '';
            const qualification = $row.find('.qualification-select').val() || '';
            const tier = $row.find('.tier-select').val() || 'N/A';

            // Get pre-selected grades from data attributes
            const currentGrade = $row.find('.current-grade').attr('data-current-grade') || '';
            const targetGrade = $row.find('.target-grade').attr('data-target-grade') || '';

            const currentGradesHtml = getGradeOptions(qualification, subject, tier);
            const targetGradesHtml = getGradeOptions(qualification, subject, tier);

            const $currentSelect = $row.find('.current-grade');
            const $targetSelect = $row.find('.target-grade');

            // Populate dropdowns
            $currentSelect.html(
                `<option value="" disabled ${!currentGrade ? 'selected' : ''}>Select grade</option>${currentGradesHtml}`
                );
            $targetSelect.html(
                `<option value="" disabled ${!targetGrade ? 'selected' : ''}>Select grade</option>${targetGradesHtml}`
                );

            // Set pre-selected values if valid
            if (currentGrade && currentGradesHtml.includes(`value="${currentGrade}"`)) {
                $currentSelect.val(currentGrade);
                console.log(`Setting current grade for row ${$row.index()}: ${currentGrade}`);
            } else if (currentGrade) {
                console.warn(
                    `Invalid current grade '${currentGrade}' for qualification=${qualification}, subject=${subject}, tier=${tier}`
                    );
                $currentSelect.val('');
            } else {
                $currentSelect.val('');
            }

            if (targetGrade && targetGradesHtml.includes(`value="${targetGrade}"`)) {
                $targetSelect.val(targetGrade);
                console.log(`Setting target grade for row ${$row.index()}: ${targetGrade}`);
            } else if (targetGrade) {
                console.warn(
                    `Invalid target grade '${targetGrade}' for qualification=${qualification}, subject=${subject}, tier=${tier}`
                    );
                $targetSelect.val('');
            } else {
                $targetSelect.val('');
            }
        }

        // Initialize grade dropdowns for all rows on page load
        $('#subjects-container .student-subjects-section .subjects-tbody tr').each(function() {
            updateGradeDropdowns(this);
        });

        // Add subject row
        function addSubjectRow(studentIndex, firstName) {
            const currentGradeOptionsHtml = getGradeOptions('', '', 'N/A');
            const targetGradeOptionsHtml = getGradeOptions('', '', 'N/A');

            const subjectRow = `
            <tr data-student-index="${studentIndex}">
                <td class="student-name">${firstName}</td>
                <td>
                    <select name="student[${studentIndex}][subject_names][]" class="form-select subject-select" required>
                        <option value="" selected disabled>Select subject</option>
                        ${subjectOptions}
                    </select>
                </td>
                <td>
                    <select name="student[${studentIndex}][qualifications][]" class="form-select qualification-select" required>
                        <option value="" selected disabled>Select qualification</option>
                        <option value="KS1">KS1</option>
                        <option value="KS2">KS2</option>
                        <option value="KS3">KS3</option>
                        <option value="KS4">KS4</option>
                        <option value="KS5">KS5</option>
                    </select>
                </td>
                <td>
                    <select name="student[${studentIndex}][tiers][]" class="form-select tier-select">
                        <option value="" selected disabled>Select tier</option>
                        <option value="Higher Tier">Higher Tier</option>
                        <option value="Foundation Tier">Foundation Tier</option>
                        <option value="N/A">N/A</option>
                    </select>
                </td>
                <td>
                    <select name="student[${studentIndex}][sessions][]" class="form-select" required>
                        <option value="" selected disabled>Select sessions</option>
                        ${sessionOptions}
                    </select>
                </td>
                <td>
                    <select name="student[${studentIndex}][current_grades][]" class="form-select current-grade" data-current-grade="" required>
                        <option value="" selected disabled>Select grade</option>
                        ${currentGradeOptionsHtml}
                    </select>
                </td>
                <td>
                    <select name="student[${studentIndex}][target_grades][]" class="form-select target-grade" data-target-grade="" required>
                        <option value="" selected disabled>Select grade</option>
                        ${targetGradeOptionsHtml}
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-subject-row">Remove</button>
                </td>
            </tr>`;

            const $newRow = $(subjectRow);
            $(`#subjects-container .student-subjects-section[data-student-index="${studentIndex}"] .subjects-tbody`)
                .append($newRow);
            updateGradeDropdowns($newRow[0]);
        }

        // Add new student section
        $('#add-student-btn').click(function(event) {
            event.preventDefault();
            if ($('.replicate').length >= 10) {
                alert('Maximum 10 students allowed');
                return;
            }

            let clonedSection = $('.replicate').first().clone(true);
            let newIndex = studentIndex;

            clonedSection.find('input, select, textarea').each(function() {
                let nameAttr = $(this).attr('name');
                if (nameAttr) {
                    nameAttr = nameAttr.replace(/\[\d+\]/, `[${newIndex}]`);
                    $(this).attr('name', nameAttr);
                }
                if ($(this).is(':radio') || $(this).is(':checkbox')) {
                    $(this).prop('checked', false);
                } else {
                    $(this).val('');
                }
                if ($(this).hasClass('student-first-name') || $(this).hasClass(
                    'year-in-school')) {
                    $(this).attr('data-student-index', newIndex);
                }
            });

            // Update year-in-school display/hidden ids for the cloned student
            clonedSection.find('.year-in-school-display')
                .attr('id', 'year-in-school-display-' + newIndex)
                .attr('data-student-index', newIndex)
                .val('');
            clonedSection.find('[id^="year-in-school-hidden-"]')
                .attr('id', 'year-in-school-hidden-' + newIndex)
                .val('');

            clonedSection.find('h5').text(`Student ${newIndex + 1}`);
            clonedSection.attr('data-student-index', newIndex);
            clonedSection.find('.remove-student-btn').remove();
            clonedSection.find('.student-section').prepend(
                '<button type="button" class="btn btn-danger remove-student-btn" style="float: right; margin-top: 10px;">Remove</button>'
            );

            $('#appendStudent').prepend(clonedSection);
            clonedSection.find('.flatpickr-date').each(function() {
                flatpickr(this, {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            });

            const firstName = `Student ${newIndex + 1}`;
            studentSubjectsMap.set(newIndex, {
                firstName,
                subjects: []
            });
            const subjectsSection = `
            <div class="student-subjects-section" id="student-subjects-${newIndex}" data-student-index="${newIndex}">
                <div class="subject-header">
                    <i class="fas fa-book me-2"></i>
                    <h3>Subjects for ${firstName}</h3>
                </div>
                <button type="button" class="btn btn-primary btn-sm add-subject-row" data-student-index="${newIndex}">
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
                        <tbody class="subjects-tbody" data-student-index="${newIndex}">
                            <tr data-student-index="${newIndex}">
                                <td class="student-name">${firstName}</td>
                                <td>
                                    <select name="student[${newIndex}][subject_names][]" class="form-select subject-select" required>
                                        <option value="" selected disabled>Select subject</option>
                                        ${subjectOptions}
                                    </select>
                                </td>
                                <td>
                                    <select name="student[${newIndex}][qualifications][]" class="form-select qualification-select" required>
                                        <option value="" selected disabled>Select qualification</option>
                                        <option value="KS1">KS1</option>
                                        <option value="KS2">KS2</option>
                                        <option value="KS3">KS3</option>
                                        <option value="KS4">KS4</option>
                                        <option value="KS5">KS5</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="student[${newIndex}][tiers][]" class="form-select tier-select">
                                        <option value="" selected disabled>Select tier</option>
                                        <option value="Higher Tier">Higher Tier</option>
                                        <option value="Foundation Tier">Foundation Tier</option>
                                        <option value="N/A">N/A</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="student[${newIndex}][sessions][]" class="form-select" required>
                                        <option value="" selected disabled>Select sessions</option>
                                        ${sessionOptions}
                                    </select>
                                </td>
                                <td>
                                    <select name="student[${newIndex}][current_grades][]" class="form-select current-grade" data-current-grade="" required>
                                        <option value="" selected disabled>Select grade</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="student[${newIndex}][target_grades][]" class="form-select target-grade" data-target-grade="" required>
                                        <option value="" selected disabled>Select grade</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-subject-row">Remove</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
            $('#subjects-container').append(subjectsSection);
            updateGradeDropdowns($(`#student-subjects-${newIndex} .subjects-tbody tr`)[0]);

            clonedSection.find(`input[name="student[${newIndex}][firstName]"]`).on('input', function() {
                updateStudentName(newIndex, $(this).val());
            });

            studentIndex++;
        });

        // Remove student section
        $(document).on('click', '.remove-student-btn', function() {
            if ($('.replicate').length <= 1) {
                alert('Cannot remove the last student section.');
                return;
            }
            const studentIndex = $(this).closest('.replicate').data('student-index');
            $(this).closest('.replicate').remove();
            $(`#subjects-container .student-subjects-section[data-student-index="${studentIndex}"]`)
                .remove();
            studentSubjectsMap.delete(studentIndex);
        });

        // Add / Remove Subject Row
        $(document).on('click', '.add-subject-row', function() {
            const studentIndex = $(this).data('student-index');
            const firstName = studentSubjectsMap.get(studentIndex)?.firstName ||
                `Student ${studentIndex + 1}`;
            addSubjectRow(studentIndex, firstName);
        });

        $(document).on('click', '.remove-subject-row', function() {
            const row = $(this).closest('tr');
            const studentIndex = row.data('student-index');
            if ($(
                    `#subjects-container .student-subjects-section[data-student-index="${studentIndex}"] .subjects-tbody tr`)
                .length <= 1) {
                alert('Cannot remove the last subject row for this student.');
                return;
            }
            row.remove();
        });

        // Update Grades When Subject, Qualification, or Tier Changes
        $(document).on('change', '.subject-select, .qualification-select, .tier-select', function() {
            updateGradeDropdowns($(this).closest('tr'));
        });

        // Update student name
        function updateStudentName(studentIndex, firstName) {
            firstName = firstName || `Student ${parseInt(studentIndex) + 1}`;
            studentSubjectsMap.set(studentIndex, {
                firstName: firstName,
                subjects: studentSubjectsMap.get(studentIndex)?.subjects || []
            });
            $(`#subjects-container .student-subjects-section[data-student-index="${studentIndex}"] h3`).text(
                `Subjects for ${firstName}`);
            $(`#subjects-container .student-subjects-section[data-student-index="${studentIndex}"] .student-name`)
                .text(firstName);
        }

        // Update subject header when first name changes
        $(document).on('input', '.student-first-name', function() {
            const studentIndex = $(this).data('student-index');
            updateStudentName(studentIndex, $(this).val());
        });

        // Form Validation
        const form = document.querySelector('.needs-validation');
        const submitButton = document.querySelector('#submitAll');

        if (!form) {
            console.error('Form with class .needs-validation not found');
            return;
        }

        if (!submitButton) {
            console.error('Submit button with ID submitAll not found');
            return;
        }

        function validateField(field) {
            if (!field) return true;
            let isValid = true;

            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else if (field.value.trim()) {
                if (field.name && (
                        field.name.includes('firstName') ||
                        field.name.includes('lastName') ||
                        field.name.includes('consent_1_first_name') ||
                        field.name.includes('consent_1_last_name')
                    )) {
                    const nameRegex = /^[a-zA-Z\s]*$/;
                    if (!nameRegex.test(field.value)) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            } else {
                field.classList.remove('is-invalid');
            }
            return isValid;
        }

        function validateSubjectRows() {
            let isValid = true;
            let firstInvalidField = null;
            const subjectSections = document.querySelectorAll('#subjects-container .student-subjects-section');

            if (subjectSections.length === 0) {
                displayError('At least one student with subjects is required.');
                return false;
            }

            subjectSections.forEach(section => {
                const studentIndex = section.getAttribute('data-student-index');
                const rows = section.querySelectorAll('.subjects-tbody tr');
                if (rows.length === 0) {
                    displayError(
                        `At least one subject row is required for Student ${parseInt(studentIndex) + 1}.`
                        );
                    isValid = false;
                    return;
                }
                rows.forEach(row => {
                    const fields = [
                        row.querySelector('select[name$="[subject_names][]"]'),
                        row.querySelector('select[name$="[qualifications][]"]'),
                        row.querySelector('select[name$="[sessions][]"]'),
                        row.querySelector('select[name$="[current_grades][]"]'),
                        row.querySelector('select[name$="[target_grades][]"]')
                    ];
                    fields.forEach(field => {
                        if (!field || !field.value || field.value === '') {
                            if (!firstInvalidField) firstInvalidField = field;
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                });
            });

            if (!isValid && firstInvalidField) {
                scrollToInvalidField(firstInvalidField);
            }
            return isValid;
        }

        function scrollToInvalidField(field) {
            if (!field) return;
            field.classList.add('is-invalid');
            const offset = field.getBoundingClientRect().top + window.pageYOffset - 100;
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
            field.focus();
        }

        function displayError(message) {
            const existingError = form.querySelector('.alert-danger');
            if (existingError) existingError.remove();
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = message;
            form.prepend(errorDiv);
            setTimeout(() => {
                if (errorDiv.parentNode) errorDiv.remove();
            }, 3000);
        }


        // Form submission handler
        let isSubmitting = false;
        submitButton.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Prevent multiple submissions
            if (isSubmitting) {
                return false;
            }

            let isValid = true;
            let firstInvalidField = null;

            // Validate all required fields
            const requiredFields = form.querySelectorAll(
                'input[required], select[required], textarea[required]');
            requiredFields.forEach(field => {
                if (!validateField(field) && !firstInvalidField) {
                    firstInvalidField = field;
                    isValid = false;
                }
            });


            $('.year-in-school').each(function() {
                // year-in-school is now auto-assigned from DOB — no manual validation needed
            });

            // Validate subject rows
            if (!validateSubjectRows()) {
                isValid = false;
            }

            // Submit form if all validations pass
            if (isValid) {
                // Set submitting flag and disable button
                isSubmitting = true;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
                form.submit();
            } else {
                if (firstInvalidField) {
                    scrollToInvalidField(firstInvalidField);
                }
                displayError(
                    'Please fill all required fields, including Current Grade and Target Grade for all subjects.'
                    );
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
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        });

        // Attach validation to input fields
        form.querySelectorAll(
            'input[required], select[required], textarea[required], input[name*="firstName"], input[name*="lastName"]'
            ).forEach(field => {
            field.addEventListener('input', () => validateField(field));
            field.addEventListener('change', () => validateField(field));
            if (field.name && (field.name.includes('firstName') || field.name.includes('lastName'))) {
                field.addEventListener('keypress', (event) => {
                    const nameRegex = /[a-zA-Z\s]/i;
                    if (!nameRegex.test(event.key)) {
                        event.preventDefault();
                    }
                });
            }
        });

        // Add required attribute to current and target grade dropdowns
        $('#subjects-container .current-grade, #subjects-container .target-grade').prop('required', true);
    });




    // Add this at the end of the existing JavaScript section
    $(document).on('change', '.medical-conditions-select', function() {
        const studentIndex = $(this).data('student-index');
        const value = $(this).val();
        const explanationDiv = $(this).closest('.row').find('.medical-conditions-explanation');
        explanationDiv.css('display', value === 'yes' ? 'block' : 'none');
        if (value !== 'yes') {
            $(explanationDiv).find('textarea').val('');
        }
    });

    // Update package preview when amount or weeks change
    function updatePackagePreview() {
        const packageAmount = $('#package_amount').val();
        const packageWeeks = $('#package_weeks').val();
        const preview = $('#package_preview');
        
        if (packageAmount && packageWeeks) {
            const packageValue = packageAmount + ' for ' + packageWeeks.toLowerCase();
            preview.text(packageValue);
            $('#fee_detail_hidden').val(packageValue);
        } else {
            preview.text('-');
            $('#fee_detail_hidden').val('');
        }
    }

    $('#package_amount').on('input', updatePackagePreview);
    $('#package_weeks').on('change', updatePackagePreview);
    // Initialize preview on page load
    updatePackagePreview();

    // Combine package fields before form submission
    $('form').on('submit', function() {
        const packageAmount = $('#package_amount').val();
        const packageWeeks = $('#package_weeks').val();
        if (packageAmount && packageWeeks) {
            $('#fee_detail_hidden').val(packageAmount + ' for ' + packageWeeks.toLowerCase());
        }
    });

    $(document).on('change', '.allergies-select', function() {
        const studentIndex = $(this).data('student-index');
        const value = $(this).val();
        const explanationDiv = $(this).closest('.row').find('.allergies-explanation');
        explanationDiv.css('display', value === 'yes' ? 'block' : 'none');
        if (value !== 'yes') {
            $(explanationDiv).find('textarea').val('');
        }
    });

    $(document).on('change', '.additional-needs-select', function() {
        const studentIndex = $(this).data('student-index');
        const value = $(this).val();
        const explanationDiv = $(this).closest('.row').find('.additional-needs-explanation');
        explanationDiv.css('display', value === 'yes' ? 'block' : 'none');
        if (value !== 'yes') {
            $(explanationDiv).find('textarea').val('');
        }
    });
</script>
@endsection
