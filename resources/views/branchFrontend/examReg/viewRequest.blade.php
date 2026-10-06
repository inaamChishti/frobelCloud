@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="main-content" style="zoom:0.8;">
        <h1>Review Student Request</h1>
        <p>Please review and approve the student registration details below.</p>

        @if ($errors->any())
            <div class="alert alert-danger" data-server-error="true">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (Session::has('created-status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ Session::get('created-status') }}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ url('approve-reuest') }}" method="POST" class="needs-validation" novalidate>
            @csrf
            <input type="hidden" name="request_id" value="{{ @$request_id }}">

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
                            <input type="text" name="family_id" readonly class="form-control"
                                value="{{ $family_id ?? '3454' }}" placeholder="Auto-generated family ID">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="form_date" class="form-label">Form Filling Date <span
                                class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="text" name="form_date" id="form_date" class="form-control flatpickr-date"
                                autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                                value="{{ old('form_date', $formDate ?? now()->format('d/m/Y')) }}" required
                                placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="joining_date" class="form-label">Joining Date <span
                                class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            <input type="text" name="joining_date" id="joining_date" class="form-control flatpickr-date"
                                autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                                value="{{ old('joining_date', $joiningDate ?? now()->format('d/m/Y')) }}" required
                                placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="family_status" class="form-label">Status <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                            <select name="family_status" class="form-select" required>
                                <option value="" disabled>Choose</option>
                                <option value="Active"
                                    {{ old('family_status', $familyStatus ?? '') == 'Active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="De-Active"
                                    {{ old('family_status', $familyStatus ?? '') == 'De-Active' ? 'selected' : '' }}>
                                    De-Active</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Information Section -->
            @for ($i = 0; $i < count($data['firstName'] ?? []); $i++)
                <div class="card-section">
                    <div class="section-header">
                        <i class="fas fa-user-graduate"></i>
                        <h2>Student {{ $i + 1 }} Information</h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="student[{{ $i }}][firstName]"
                                    class="form-control student-first-name" data-student-index="{{ $i }}"
                                    value="{{ $data['firstName'][$i] ?? '' }}" required placeholder="Enter first name"
                                    onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="student[{{ $i }}][middleName]"
                                    class="form-control" value="{{ $data['middleName'][$i] ?? '' }}"
                                    placeholder="Enter middle name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="student[{{ $i }}][lastName]" class="form-control"
                                    value="{{ $data['lastName'][$i] ?? '' }}" required placeholder="Enter last name"
                                    onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Student Status <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                <select name="student[{{ $i }}][student_status]" class="form-select" required>
                                    <option value="" disabled>Choose</option>
                                    <option value="active"
                                        {{ ($data['student_status'][$i] ?? '') == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive"
                                        {{ ($data['student_status'][$i] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="text" name="student[{{ $i }}][dob]"
                                    class="form-control flatpickr-date" autocomplete="off" inputmode="none"
                                    onfocus="this.showPicker?.()"
                                    value="{{ @$data['dob'][$i] ? \Carbon\Carbon::parse($data['dob'][$i])->format('d/m/Y') : '' }}"
                                    required placeholder="dd/mm/yyyy">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                <select name="student[{{ $i }}][gender]" class="form-select" required>
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ ($data['gender'][$i] ?? '') == 'Male' ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="Female"
                                        {{ ($data['gender'][$i] ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ ($data['gender'][$i] ?? '') == 'Other' ? 'selected' : '' }}>
                                        Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year in School <small class="text-muted">(auto from DOB)</small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                @php
                                    $currentYear = $data['yearInSchool'][$i] ?? '';
                                @endphp
                                <select id="year-in-school-display-{{ $i }}"
                                    class="form-select year-in-school-display bg-light"
                                    data-student-index="{{ $i }}"
                                    disabled>
                                    <option value="" {{ $currentYear === '' ? 'selected' : '' }}>-- Auto assigned --</option>
                                    @for ($y = 1; $y <= 13; $y++)
                                        <option value="{{ $y }}" {{ (string)$currentYear === (string)$y ? 'selected' : '' }}>
                                            Year {{ $y }}</option>
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
                        <div class="col-md-4">
                            <label class="form-label">Tuition Hours</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <input type="number" name="student[{{ $i }}][tuitionHours]"
                                    class="form-control" value="{{ $data['tuitionHours'][$i] ?? '' }}" min="1"
                                    max="3" placeholder="Enter hours">
                            </div>
                        </div>
                        <!-- Medical Conditions Dropdown -->
                        <div class="col-md-4">
                            <label class="form-label">Medical Conditions</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                                <select name="student[{{ $i }}][has_medical_conditions]"
                                    class="form-select medical-conditions-select"
                                    data-student-index="{{ $i }}">
                                    <option value="" disabled
                                        {{ !isset($data['has_medical_conditions'][$i]) ? 'selected' : '' }}>Select option
                                    </option>
                                    <option value="yes"
                                        {{ ($data['has_medical_conditions'][$i] ?? '') == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ ($data['has_medical_conditions'][$i] ?? '') == 'no' ? 'selected' : '' }}
                                        selected>No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- Medical Conditions Explanation -->
                        <div class="col-md-4 medical-conditions-explanation"
                            style="display: {{ ($data['has_medical_conditions'][$i] ?? '') == 'yes' ? 'block' : 'none' }};">
                            <label class="form-label">Medical Conditions Details</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                                <textarea name="student[{{ $i }}][medical_conditions_explanation]" class="form-control"
                                    placeholder="Enter medical conditions details">{{ $data['medical_conditions_explanation'][$i] ?? '' }}</textarea>
                            </div>
                        </div>
                        <!-- Allergies Dropdown -->
                        <div class="col-md-4">
                            <label class="form-label">Allergies</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-allergies"></i></span>
                                <select name="student[{{ $i }}][has_allergies]"
                                    class="form-select allergies-select" data-student-index="{{ $i }}">
                                    <option value="" disabled
                                        {{ !isset($data['has_allergies'][$i]) ? 'selected' : '' }}>Select option</option>
                                    <option value="yes"
                                        {{ ($data['has_allergies'][$i] ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no"
                                        {{ ($data['has_allergies'][$i] ?? '') == 'no' ? 'selected' : '' }} selected>No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- Allergies Explanation -->
                        <div class="col-md-4 allergies-explanation"
                            style="display: {{ ($data['has_allergies'][$i] ?? '') == 'yes' ? 'block' : 'none' }};">
                            <label class="form-label">Allergies Details</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-allergies"></i></span>
                                <textarea name="student[{{ $i }}][allergies_explanation]" class="form-control"
                                    placeholder="Enter allergies details">{{ $data['allergies_explanation'][$i] ?? '' }}</textarea>
                            </div>
                        </div>
                        <!-- Additional Needs Dropdown -->
                        <div class="col-md-4">
                            <label class="form-label">Additional Needs</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                <select name="student[{{ $i }}][has_additional_needs]"
                                    class="form-select additional-needs-select" data-student-index="{{ $i }}">
                                    <option value="" disabled
                                        {{ !isset($data['has_additional_needs'][$i]) ? 'selected' : '' }}>Select option
                                    </option>
                                    <option value="yes"
                                        {{ ($data['has_additional_needs'][$i] ?? '') == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ ($data['has_additional_needs'][$i] ?? '') == 'no' ? 'selected' : '' }} selected>
                                        No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- Additional Needs Explanation -->
                        <div class="col-md-4 additional-needs-explanation"
                            style="display: {{ ($data['has_additional_needs'][$i] ?? '') == 'yes' ? 'block' : 'none' }};">
                            <label class="form-label">Additional Needs Details</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                <textarea name="student[{{ $i }}][additional_needs_explanation]" class="form-control"
                                    placeholder="Enter additional needs details">{{ $data['additional_needs_explanation'][$i] ?? '' }}</textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <label class="form-label">GP Prefix</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-md"></i></span>
                                <select name="student[{{ $i }}][gpPrefix]" class="form-select">
                                    <option value="Dr." {{ ($data['gpPrefix'][$i] ?? '') == 'Dr.' ? 'selected' : '' }}>
                                        Dr.</option>
                                    <option value="Mr." {{ ($data['gpPrefix'][$i] ?? '') == 'Mr.' ? 'selected' : '' }}>
                                        Mr.</option>
                                    <option value="Ms." {{ ($data['gpPrefix'][$i] ?? '') == 'Ms.' ? 'selected' : '' }}>
                                        Ms.</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-4">
                            <label class="form-label">GP Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="student[{{ $i }}][gpFirstName]"
                                    class="form-control" value="{{ $data['gpFirstName'][$i] ?? '' }}"
                                    placeholder="Enter GP first name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <label class="form-label">GP Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="student[{{ $i }}][gpLastName]"
                                    class="form-control" value="{{ $data['gpLastName'][$i] ?? '' }}"
                                    placeholder="Enter GP last name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GP Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="student[{{ $i }}][gpAddress]"
                                    class="form-control" value="{{ $data['gpAddress'][$i] ?? '' }}"
                                    placeholder="Enter GP address">
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-4">
                            <label class="form-label">GP Address Line 2</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="student[{{ $i }}][gpAddressLineTwo]"
                                    class="form-control" value="{{ $data['gpAddressLineTwo'][$i] ?? '' }}"
                                    placeholder="Enter GP address line 2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GP City</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                <input type="text" name="student[{{ $i }}][gp_city]" class="form-control"
                                    value="{{ $data['gp_city'][$i] ?? '' }}" placeholder="Enter GP city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GP County/State/Region</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map"></i></span>
                                <input type="text" name="student[{{ $i }}][gp_countyStateRegion]"
                                    class="form-control" value="{{ $data['gp_countyStateRegion'][$i] ?? '' }}"
                                    placeholder="Enter GP county/state/region">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GP ZIP/Postal Code</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                                <input type="text" name="student[{{ $i }}][gpzipCode]"
                                    class="form-control" value="{{ $data['gpzipCode'][$i] ?? '' }}"
                                    placeholder="Enter GP ZIP/postal code">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">GP Country</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                <select name="student[{{ $i }}][gpcountry]" class="form-select">
                                    <option value="United Kingdom"
                                        {{ ($data['gpcountry'][$i] ?? '') == 'United Kingdom' ? 'selected' : '' }}>United
                                        Kingdom</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-4">
                            <label class="form-label">GP Phone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="student[{{ $i }}][GPPhone]" class="form-control"
                                    value="{{ $data['GPPhone'][$i] ?? '' }}" placeholder="Enter GP phone"
                                    onkeypress="return /\d/.test(event.key)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Medical Consent</label>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="student[{{ $i }}][medicalConsent]" value="yes"
                                    class="form-check-input"
                                    {{ ($data['medicalConsent'][$i] ?? '') == 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="student[{{ $i }}][medicalConsent]" value="no"
                                    class="form-check-input"
                                    {{ ($data['medicalConsent'][$i] ?? '') == 'no' ? 'checked' : '' }}>
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Photo Consent</label>
                            <div class="form-check">
                                <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                    value="website" class="form-check-input"
                                    {{ in_array('website', $data['photoConsent'][$i] ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label">Website</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                    value="socialMedia" class="form-check-input"
                                    {{ in_array('socialMedia', $data['photoConsent'][$i] ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label">Social Media</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="student[{{ $i }}][photoConsent][]"
                                    value="marketing" class="form-check-input"
                                    {{ in_array('marketing', $data['photoConsent'][$i] ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label">Marketing</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Leave Alone</label>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="student[{{ $i }}][leaveAlone]" value="yes"
                                    class="form-check-input"
                                    {{ ($data['leaveAlone'][$i] ?? '') == 'yes' ? 'checked' : '' }}>
                                <label class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="student[{{ $i }}][leaveAlone]" value="no"
                                    class="form-check-input"
                                    {{ ($data['leaveAlone'][$i] ?? '') == 'no' ? 'checked' : '' }}>
                                <label class="form-check-label">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor

            <!-- Parent/Guardian 1 Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-user-shield"></i>
                    <h2>Parent/Guardian 1 Details</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">First Name <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="parent1_first_name" class="form-control"
                                value="{{ $data['parent1_first_name'] ?? '' }}" required placeholder="Enter first name"
                                onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="parent1_last_name" class="form-control"
                                value="{{ $data['parent1_last_name'] ?? '' }}" placeholder="Enter last name"
                                onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="parent1_Address" class="form-control"
                                value="{{ $data['parent1_Address'] ?? '' }}" placeholder="Enter address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address Line 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="parent1_Address_line2" class="form-control"
                                value="{{ $data['parent1_Address_line2'] ?? '' }}" placeholder="Enter address line 2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                            <input type="text" name="parent1_city" class="form-control"
                                value="{{ $data['parent1_city'] ?? '' }}" placeholder="Enter city">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">County/State/Region</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                            <input type="text" name="parent1_country_state_region" class="form-control"
                                value="{{ $data['parent1_country_state_region'] ?? '' }}"
                                placeholder="Enter county/state/region">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ZIP/Postal Code</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                            <input type="text" name="parent1_zipCode" class="form-control"
                                value="{{ $data['parent1_zipCode'] ?? '' }}"
                                placeholder="Enter ZIP/postal code">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            <select name="parent1_country" class="form-select">
                                <option value="United Kingdom"
                                    {{ ($data['parent1_country'] ?? '') == 'United Kingdom' ? 'selected' : '' }}>United
                                    Kingdom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="parent1_email" class="form-control"
                                value="{{ $data['parent1_email'] ?? '' }}" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="parent1_mobile" class="form-control"
                                value="{{ $data['parent1_mobile'] ?? '' }}" placeholder="Enter mobile number"
                                onkeypress="return /\d/.test(event.key)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact 1 Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-user-friends"></i>
                    <h2>Emergency Contact 1 Details</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">First Name <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="emergency_conatct1_first_name" class="form-control"
                                value="{{ $data['emergency_conatct1_first_name'] ?? '' }}" required
                                placeholder="Enter first name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="emergency_conatct1_last_name" class="form-control"
                                value="{{ $data['emergency_conatct1_last_name'] ?? '' }}"
                                placeholder="Enter last name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="emergency_conatct1_Address" class="form-control"
                                value="{{ $data['emergency_conatct1_Address'] ?? '' }}"
                                placeholder="Enter address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address Line 2</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="emergency_conatct1_Address_line2" class="form-control"
                                value="{{ $data['emergency_conatct1_Address_line2'] ?? '' }}"
                                placeholder="Enter address line 2">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-city"></i></span>
                            <input type="text" name="emergency_conatct1_city" class="form-control"
                                value="{{ $data['emergency_conatct1_city'] ?? '' }}" placeholder="Enter city">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">County/State/Region</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                            <input type="text" name="emergency_conatct1_country_state_region" class="form-control"
                                value="{{ $data['emergency_conatct1_country_state_region'] ?? '' }}"
                                placeholder="Enter county/state/region">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ZIP/Postal Code</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-mail-bulk"></i></span>
                            <input type="text" name="emergency_conatct1_zipCode" class="form-control"
                                value="{{ $data['emergency_conatct1_zipCode'] ?? '' }}"
                                placeholder="Enter ZIP/postal code">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-globe"></i></span>
                            <select name="emergency_conatct1_country" class="form-select">
                                <option value="United Kingdom"
                                    {{ ($data['emergency_conatct1_country'] ?? '') == 'United Kingdom' ? 'selected' : '' }}>
                                    United Kingdom</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="emergency_conatct1_email" class="form-control"
                                value="{{ $data['emergency_conatct1_email'] ?? '' }}" placeholder="Enter email">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="emergency_conatct1_mobile" class="form-control"
                                value="{{ $data['emergency_conatct1_mobile'] ?? '' }}" placeholder="Enter mobile number"
                                onkeypress="return /\d/.test(event.key)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consent Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-file-signature"></i>
                    <h2>Consent</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Parent/Guardian First Name <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="consent_1_first_name"
                                value="{{ $data['consent_1_first_name'] ?? '' }}" class="form-control" required
                                placeholder="Enter first name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Parent/Guardian Last Name <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="consent_1_last_name"
                                value="{{ $data['consent_1_last_name'] ?? '' }}" class="form-control" required
                                placeholder="Enter last name" onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="consent_1_checkbox" value="yes" class="form-check-input"
                                {{ ($data['consent_1_checkbox'] ?? '') == 'yes' ? 'checked' : '' }}>
                            <label class="form-check-label">I have read and understand the terms and conditions and agree
                                to be bound by them.</label>
                        </div>
                    </div>
                    <input type="hidden" name="signature" value="{{ @$data['consent_1signature'] }}">
                    <div class="col-md-6">
                        <label class="form-label">Signature</label>
                        <div class="border p-2 rounded" style="background: #fff;">
                            <img src="{{ @$data['consent_1signature'] ? $data['consent_1signature'] : '' }}"
                                alt="Signature 1" style="width: 100%; height: auto;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="text" name="consent_1date" autocomplete="off" inputmode="none"
                                onfocus="this.showPicker?.()"
                                value="{{ @$data['consent_1date'] ? \Carbon\Carbon::parse($data['consent_1date'])->format('d/m/Y') : '' }}"
                                class="form-control flatpickr-date" required placeholder="dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">How did you hear about us?</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-question-circle"></i></span>
                            <select name="how_did_you_hear" class="form-select">
                                <option value="" {{ ($data['how_did_you_hear'] ?? '') == '' ? 'selected' : '' }}>
                                    Please Select...</option>
                                <option value="Social Media"
                                    {{ ($data['how_did_you_hear'] ?? '') == 'Social Media' ? 'selected' : '' }}>Social
                                    Media</option>
                                <option value="Search Engine"
                                    {{ ($data['how_did_you_hear'] ?? '') == 'Search Engine' ? 'selected' : '' }}>Search
                                    Engine</option>
                                <option value="Leaflets"
                                    {{ ($data['how_did_you_hear'] ?? '') == 'Leaflets' ? 'selected' : '' }}>Leaflets
                                </option>
                                <option value="Friends"
                                    {{ ($data['how_did_you_hear'] ?? '') == 'Friends' ? 'selected' : '' }}>Friends
                                </option>
                                <option value="Other"
                                    {{ ($data['how_did_you_hear'] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-info-circle"></i>
                    <h2>Additional Information</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Medical Conditions Notes</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-file-medical"></i></span>
                            <textarea name="medical_condition" class="form-control" rows="3"
                                placeholder="Enter any additional medical information">{{ @$admission->medicalcondition }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other School Attendance</label>
                        <div class="mb-3">
                            @php $childSchoolChecked = !empty($admission->child_name1) && !empty($admission->school_name1); @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="child_school" id="yes"
                                    value="yes" {{ $childSchoolChecked ? 'checked' : '' }}
                                    onclick="toggleInputs(true)">
                                <label class="form-check-label" for="yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="child_school" id="no"
                                    value="no" {{ !$childSchoolChecked ? 'checked' : '' }}
                                    onclick="toggleInputs(false)">
                                <label class="form-check-label" for="no">No</label>
                            </div>
                        </div>
                        <div id="childSchoolInputs" style="display: {{ $childSchoolChecked ? 'block' : 'none' }};">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="row g-2 mb-2">
                                    <div class="col">
                                        <input type="text" class="form-control" placeholder="Enter child's name"
                                            name="child_name{{ $i }}"
                                            value="{{ @$admission->{"child_name$i"} }}"
                                            onkeypress="return /[a-zA-Z\s]/i.test(event.key)">
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
                        <label class="form-label">Fee Details <span class="required-star">*</span></label>
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
                        <label class="form-label">Payment Method <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                            <select name="payment_method" class="form-select" required>
                                <option value="" disabled>Choose an option</option>
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
                        <label class="form-label">Additional Comments</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                            <textarea name="add_comment" class="form-control" rows="3" placeholder="Enter any additional comments">{{ @$admission->add_comment }}</textarea>
                        </div>
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
                    @for ($i = 0; $i < count($data['firstName'] ?? []); $i++)
                        <div class="student-subject-section" data-student-index="{{ $i }}">
                            <h3>Subjects for {{ $data['firstName'][$i] ?? 'Student ' . ($i + 1) }}</h3>
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
                                            $subjectNames = $data['subject_names'][$i] ?? [];
                                            $qualifications = $data['qualifications'][$i] ?? [];
                                            $tiers = $data['tiers'][$i] ?? [];
                                            $sessions = $data['sessions'][$i] ?? [];
                                            $currentGrades = $data['current_grades'][$i] ?? [];
                                            $targetGrades = $data['target_grades'][$i] ?? [];
                                            $maxCount = max(
                                                count($subjectNames),
                                                count($qualifications),
                                                count($tiers),
                                                count($sessions),
                                                count($currentGrades),
                                                count($targetGrades),
                                            );
                                        @endphp
                                        @for ($j = 0; $j < ($maxCount ?: 1); $j++)
                                            <tr data-student-index="{{ $i }}">
                                                <td class="student-name">
                                                    {{ $data['firstName'][$i] ?? 'Student ' . ($i + 1) }}</td>
                                                <td>
                                                    <select name="student[{{ $i }}][subject_names][]"
                                                        class="form-select" required>
                                                        <option value="" disabled
                                                            {{ !isset($subjectNames[$j]) || empty($subjectNames[$j]) ? 'selected' : '' }}>
                                                            Select subject</option>
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
                                                            {{ !isset($qualifications[$j]) || empty($qualifications[$j]) ? 'selected' : '' }}>
                                                            Select qualification</option>
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
                                                            {{ !isset($tiers[$j]) || empty($tiers[$j]) ? 'selected' : '' }}>
                                                            Select tier</option>
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
                                                            {{ !isset($sessions[$j]) || empty($sessions[$j]) ? 'selected' : '' }}>
                                                            Select sessions</option>
                                                        @for ($s = 1; $s <= 26; $s++)
                                                            <option value="{{ $s }}"
                                                                {{ ($sessions[$j] ?? '') == $s ? 'selected' : '' }}>
                                                                {{ $s }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="student[{{ $i }}][current_grades][]"
                                                        class="form-select current-grade">
                                                        <option value="" disabled
                                                            {{ !isset($currentGrades[$j]) || empty($currentGrades[$j]) ? 'selected' : '' }}>
                                                            Select grade</option>
                                                        <!-- Grades will be populated dynamically by JavaScript -->
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="student[{{ $i }}][target_grades][]"
                                                        class="form-select target-grade">
                                                        <option value="" disabled
                                                            {{ !isset($targetGrades[$j]) || empty($targetGrades[$j]) ? 'selected' : '' }}>
                                                            Select grade</option>
                                                        <!-- Grades will be populated dynamically by JavaScript -->
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
            </div>

            <!-- Form Actions -->
            <div class="form-actions mt-4">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Approve Request
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-2"></i>Reset Form
                </button>
            </div>
        </form>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 2px solid #214AB0;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #214AB0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #214AB0;
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
            color: #214AB0;
            margin-right: 15px;
        }

        .section-header h2,
        .section-header h3 {
            font-size: 22px;
            font-weight: 600;
            color: #214AB0;
            margin: 0;
            flex-grow: 1;
        }

        .form-label {
            font-weight: 600;
            color: #214AB0;
            margin-bottom: 8px;
        }

        .required-star {
            color: #ff4d4d;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #214AB0;
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
            border: 2px solid #214AB0;
            color: #214AB0;
        }

        .table {
            color: #495057;
        }

        .table th {
            background: rgba(103, 192, 234, 0.1);
            color: #214AB0;
            border-bottom: 2px solid #214AB0;
        }

        .table td {
            vertical-align: middle;
        }

        .btn-primary {
            background-color: #214AB0;
            border-color: #214AB0;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #4ba8d2;
            border-color: #4ba8d2;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            color: #214AB0;
            border-color: #214AB0;
        }

        .btn-outline-secondary:hover {
            background-color: #214AB0;
            color: white;
        }

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

        .form-check-input:checked {
            background-color: #214AB0;
            border-color: #214AB0;
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
            color: #214AB0;
            margin-bottom: 15px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Grade options
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
            upperKeyStages: ['KS3 Foundation', 'KS3 Higher', 'U', 'E', 'D', 'C', 'B', 'A', 'A*', '1', '2', '3', '4',
                '5', '6', '7', '8', '9'
            ],
            ks4Foundation: ['KS3 Foundation', 'KS3 Higher', '1', '2', '3', '4', '5'],
            ks4Higher: ['KS3 Foundation', 'KS3 Higher', '1', '2', '3', '4', '5', '6', '7', '8', '9']
        };

        // Pass PHP subjects array to JavaScript
        const subjects = @json($subjects);

        // Generate session and subject options
        const sessionOptions = Array.from({
            length: 26
        }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('');
        const subjectOptions = subjects.map(subject => `<option value="${subject}">${subject}</option>`).join('');

        $(document).ready(function() {
            let studentSubjectsMap = new Map();

            // Initialize Flatpickr
            flatpickr(".flatpickr-date", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1
                }
            });

            // ── Auto-assign Year in School from DOB ───────────────────────
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

            // On page load: auto-fill year from DOB for ALL students (always override with correct value)
            @for ($i = 0; $i < count($data['firstName'] ?? []); $i++)
                @if (!empty($data['dob'][$i]))
                    autoAssignYearFromDob('{{ \Carbon\Carbon::parse($data['dob'][$i])->format('d/m/Y') }}', {{ $i }});
                @endif
            @endfor
            // ── End Auto-assign ───────────────────────────────────────────

            // Initialize student subjects map
            @for ($i = 0; $i < count($data['firstName'] ?? []); $i++)
                studentSubjectsMap.set({{ $i }}, {
                    firstName: "{{ $data['firstName'][$i] ?? 'Student ' . ($i + 1) }}",
                    subjects: []
                });
                updateGradeDropdowns({{ $i }});
            @endfor

            // Toggle child school inputs
            function toggleInputs(show) {
                document.getElementById('childSchoolInputs').style.display = show ? 'block' : 'none';
            }

            // Add subject row
            function addSubjectRow(studentIndex, firstName) {
                const currentGradeOptionsHtml = getGradeOptions('', '', 'current', '');
                const targetGradeOptionsHtml = getGradeOptions('', '', 'target', '');

                const subjectRow = `
                    <tr data-student-index="${studentIndex}">
                        <td class="student-name">${firstName}</td>
                        <td>
                            <select name="student[${studentIndex}][subject_names][]" class="form-select" required>
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
                            <select name="student[${studentIndex}][current_grades][]" class="form-select current-grade" required>
                                <option value="" selected disabled>Select grade</option>
                                ${currentGradeOptionsHtml}
                            </select>
                        </td>
                        <td>
                            <select name="student[${studentIndex}][target_grades][]" class="form-select target-grade" required>
                                <option value="" selected disabled>Select grade</option>
                                ${targetGradeOptionsHtml}
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-subject-row">Remove</button>
                        </td>
                    </tr>`;

                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody`)
                    .append(subjectRow);
            }

            // Get grade options
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
                    grades = gradeOptions
                        .lowerKeyStages; // Default to lowerKeyStages if qualification is not selected
                }
                return grades.map(g => `<option value="${g}">${g}</option>`).join('');
            }

            // Update grade dropdowns
            function updateGradeDropdowns(studentIndex) {
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody tr`)
                    .each(function() {
                        const subject = $(this).find(`select[name^="student[${studentIndex}][subject_names]"]`)
                            .val() || '';
                        const qualification = $(this).find('.qualification-select').val() || '';
                        const tier = $(this).find('.tier-select').val() || 'N/A';
                        const currentGradesHtml = getGradeOptions(qualification, subject, 'current', tier);
                        const targetGradesHtml = getGradeOptions(qualification, subject, 'target', tier);

                        const currentSelect = $(this).find('.current-grade');
                        const targetSelect = $(this).find('.target-grade');
                        const currentValue = currentSelect.val();
                        const targetValue = targetSelect.val();

                        currentSelect.html(
                            `<option value="" disabled selected>Select grade</option>${currentGradesHtml}`);
                        targetSelect.html(
                            `<option value="" disabled selected>Select grade</option>${targetGradesHtml}`);
                        if (currentValue && currentGradesHtml.includes(`value="${currentValue}"`)) currentSelect
                            .val(currentValue);
                        if (targetValue && targetGradesHtml.includes(`value="${targetValue}"`)) targetSelect
                            .val(targetValue);
                    });
            }

            // Add / Remove Subject Row
            $(document).on('click', '.add-subject-row', function() {
                const studentIndex = $(this).data('student-index');
                addSubjectRow(studentIndex, studentSubjectsMap.get(studentIndex).firstName);
                updateGradeDropdowns(studentIndex);
            });

            $(document).on('click', '.remove-subject-row', function() {
                const row = $(this).closest('tr');
                const studentIndex = row.data('student-index');
                if ($(
                        `#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .subjects-tbody tr`
                        )
                    .length <= 1) {
                    alert('Cannot remove the last subject row for this student.');
                    return;
                }
                row.remove();
            });

            // Update Grades When Subject, Qualification, or Tier Changes
            $(document).on('change',
                'select[name^="student["][name$="[subject_names][]"], .qualification-select, .tier-select',
                function() {
                    updateGradeDropdowns($(this).closest('tr').data('student-index'));
                });

            // Update subject header when first name changes
            $('.student-first-name').on('input', function() {
                const studentIndex = $(this).data('student-index');
                const firstName = $(this).val() || `Student ${parseInt(studentIndex) + 1}`;
                studentSubjectsMap.get(studentIndex).firstName = firstName;
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] h3`)
                    .text(`Subjects for ${firstName}`);
                $(`#subjects-sections .student-subject-section[data-student-index="${studentIndex}"] .student-name`)
                    .text(firstName);
            });

            // Form Validation
            const form = document.querySelector('.needs-validation');
            if (!form) {
                console.error('Form with class .needs-validation not found');
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const resetButton = form.querySelector('button[type="reset"]');

            function validateField(field) {
                let isValid = true;
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.style.borderColor = 'red';
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field.value.trim()) {
                    if (field.name.includes('firstName') || field.name.includes('lastName') || field.name.includes(
                            'consent_1_first_name') || field.name.includes('consent_1_last_name')) {
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

            form.querySelectorAll(
                'input[required], select[required], textarea[required], input[name*="firstName"], input[name*="lastName"]'
            ).forEach(field => {
                field.addEventListener('input', () => validateField(field));
                field.addEventListener('change', () => validateField(field));
                if (field.name.includes('firstName') || field.name.includes('lastName')) {
                    field.addEventListener('keypress', (event) => {
                        const nameRegex = /[a-zA-Z\s]/i;
                        if (!nameRegex.test(event.key)) {
                            event.preventDefault();
                        }
                    });
                }
            });

            function validateSubjectRows() {
                let isValid = true;
                const subjectSections = document.querySelectorAll('#subjects-sections .student-subject-section');
                subjectSections.forEach(section => {
                    const studentIndex = section.getAttribute('data-student-index');
                    const rows = section.querySelectorAll('.subjects-tbody tr');
                    if (rows.length === 0) {
                        displayError('At least one subject row is required for each student.');
                        isValid = false;
                        return;
                    }
                    rows.forEach(row => {
                        const requiredFields = row.querySelectorAll('select[required]');
                        requiredFields.forEach(field => {
                            if (!validateField(field)) {
                                isValid = false;
                            }
                        });
                    });
                });
                return isValid;
            }

            function displayError(message) {
                const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                if (existingError) existingError.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = message;
                form.prepend(errorDiv);
                setTimeout(() => {
                    if (errorDiv.parentNode) errorDiv.remove();
                }, 3000);
            }

            let isSubmitting = false;
            submitButton.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                // Prevent multiple submissions
                if (isSubmitting) {
                    return false;
                }

                let isValid = true;

                form.querySelectorAll(
                    'input[required], select[required], textarea[required], input[name*="firstName"], input[name*="lastName"]'
                ).forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });

                if (!validateSubjectRows()) {
                    isValid = false;
                }

                if (!isValid) {
                    displayError('Please fill out all required fields correctly.');
                } else {
                    // Set submitting flag and disable button
                    isSubmitting = true;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
                    
                    const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                    if (existingError) existingError.remove();
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
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
            });

            resetButton.addEventListener('click', function() {
                isSubmitting = false;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Approve Request';
                form.querySelectorAll('input, select, textarea').forEach(field => {
                    field.style.borderColor = '#214AB0';
                    field.classList.remove('is-invalid');
                });
                const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                if (existingError) existingError.remove();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            const form = document.querySelector('.needs-validation');
            if (!form) {
                console.error('Form with class .needs-validation not found');
                return;
            }

            const submitButton = form.querySelector('button[type="submit"]');
            const resetButton = form.querySelector('button[type="reset"]');

            // Function to display error message
            function displayError(message) {
                const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                if (existingError) existingError.remove();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = message;
                form.prepend(errorDiv);
                setTimeout(() => {
                    if (errorDiv.parentNode) errorDiv.remove();
                }, 3000);
            }

            // Validate individual field
            function validateField(field) {
                let isValid = true;
                if (field.hasAttribute('required') && !field.value.trim()) {
                    field.style.borderColor = 'red';
                    field.classList.add('is-invalid');
                    isValid = false;
                } else if (field.value.trim()) {
                    if (field.name.includes('firstName') || field.name.includes('lastName') || field.name.includes(
                            'consent_1_first_name') || field.name.includes('consent_1_last_name')) {
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

            // Validate subjects and sessions section
            // Validate subjects and sessions section - FIXED VERSION
            function validateSubjectsAndSessions() {
                let isValid = true;
                let firstInvalidField = null;
                const subjectSections = document.querySelectorAll('#subjects-sections .student-subject-section');

                if (subjectSections.length === 0) {
                    displayError('At least one student must be added.');
                    return {
                        isValid: false,
                        firstInvalidField: null
                    };
                }

                subjectSections.forEach(section => {
                    const studentIndex = section.getAttribute('data-student-index');
                    const rows = section.querySelectorAll('.subjects-tbody tr');

                    if (rows.length === 0) {
                        displayError('At least one subject row is required for each student.');
                        isValid = false;
                        return;
                    }

                    rows.forEach(row => {
                        const currentGradeField = row.querySelector(
                            `select[name^="student[${studentIndex}][current_grades][]"]`);
                        const targetGradeField = row.querySelector(
                            `select[name^="student[${studentIndex}][target_grades][]"]`);

                        // Debug logging
                        console.log('Current Grade Value:', currentGradeField ? currentGradeField
                            .value : 'null');
                        console.log('Target Grade Value:', targetGradeField ? targetGradeField
                            .value : 'null');

                        // Validate Current Grade
                        if (currentGradeField) {
                            if (!currentGradeField.value || currentGradeField.value === '') {
                                currentGradeField.style.borderColor = 'red';
                                currentGradeField.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) firstInvalidField = currentGradeField;
                            } else {
                                currentGradeField.style.borderColor = 'green';
                                currentGradeField.classList.remove('is-invalid');
                            }
                        }

                        // Validate Target Grade
                        if (targetGradeField) {
                            if (!targetGradeField.value || targetGradeField.value === '') {
                                targetGradeField.style.borderColor = 'red';
                                targetGradeField.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) firstInvalidField = targetGradeField;
                            } else {
                                targetGradeField.style.borderColor = 'green';
                                targetGradeField.classList.remove('is-invalid');
                            }
                        }
                    });
                });

                return {
                    isValid,
                    firstInvalidField
                };
            }

            // Add validation listeners to all fields
            form.querySelectorAll(
                'input[required], select[required], textarea[required], input[name*="firstName"], input[name*="lastName"]'
                ).forEach(field => {
                field.addEventListener('input', () => validateField(field));
                field.addEventListener('change', () => validateField(field));
                if (field.name.includes('firstName') || field.name.includes('lastName')) {
                    field.addEventListener('keypress', (event) => {
                        const nameRegex = /[a-zA-Z\s]/i;
                        if (!nameRegex.test(event.key)) {
                            event.preventDefault();
                        }
                    });
                }
            });

            // Form submission handler
            let isSubmitting2 = false;
            submitButton.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();

                // Prevent multiple submissions
                if (isSubmitting2) {
                    return false;
                }

                let isValid = true;
                let firstInvalidField = null;

                // Validate all form fields
                form.querySelectorAll(
                    'input[required], select[required], textarea[required], input[name*="firstName"], input[name*="lastName"]'
                    ).forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    }
                });

                // Validate subjects and sessions
                const subjectValidation = validateSubjectsAndSessions();
                if (!subjectValidation.isValid) {
                    isValid = false;
                    if (!firstInvalidField && subjectValidation.firstInvalidField) {
                        firstInvalidField = subjectValidation.firstInvalidField;
                    }
                }

                if (!isValid) {
                    displayError(
                        'Please fill out all required fields correctly, including Current Grade and Target Grade.'
                        );
                    if (firstInvalidField) {
                        firstInvalidField.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstInvalidField.focus();
                    }
                } else {
                    // Set submitting flag and disable button
                    isSubmitting2 = true;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
                    
                    const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                    if (existingError) existingError.remove();
                    HTMLFormElement.prototype.submit.call(form);
                }
            });

            // Prevent form double submission
            form.addEventListener('submit', function(event) {
                if (isSubmitting2) {
                    event.preventDefault();
                    return false;
                }
                isSubmitting2 = true;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
            });

            // Reset form handler
            resetButton.addEventListener('click', function() {
                isSubmitting2 = false;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Approve Request';
                form.querySelectorAll('input, select, textarea').forEach(field => {
                    field.style.borderColor = '#214AB0';
                    field.classList.remove('is-invalid');
                });
                const existingError = form.querySelector('.alert-danger:not([data-server-error])');
                if (existingError) existingError.remove();
            });
        });
    </script>

    <script>
        // Handle Medical Conditions, Allergies, and Additional Needs dropdowns
        $(document).on('change', '.medical-conditions-select', function() {
            const studentIndex = $(this).data('student-index');
            const value = $(this).val();
            const explanationDiv = $(this).closest('.row').find('.medical-conditions-explanation');
            explanationDiv.css('display', value === 'yes' ? 'block' : 'none');
            if (value !== 'yes') {
                $(explanationDiv).find('textarea').val('');
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
    </script>
@endsection
