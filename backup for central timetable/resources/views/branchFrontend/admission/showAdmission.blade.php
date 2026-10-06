```blade
@extends('layouts.branchDashboardApp')

@section('content')

    <style>
        /* Main Container and Layout */
        .container-fluid {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Profile Header */
        .profile-header {
            display: grid;
            grid-template-columns: 120px 1fr auto;
            align-items: center;
            gap: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-avatar img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #67c0ea;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile-info h4 {
            margin: 0;
            font-size: 1.5rem;
            color: #333;
        }

        .profile-info .text-muted {
            font-size: 1rem;
            margin-top: 5px;
        }

        .profile-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            border-radius: 5px;
            padding: 8px 16px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4285f4;
            border-color: #4285f4;
        }

        .btn-primary:hover {
            background-color: #3267d6;
        }

        .btn-warning {
            background-color: #ff9800;
            border-color: #ff9800;
            color: white;
        }

        .btn-warning:hover {
            background-color: #e68900;
        }

        /* Profile Heading */
        .profile-heading {
            font-size: 1.75rem;
            color: #333;
            border-bottom: 2px solid #67c0ea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .profile-heading i {
            margin-right: 10px;
        }

        /* Card Styles */
        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 25px;
        }

        .card-title {
            margin-bottom: 20px;
        }

        .card-title i {
            margin-right: 10px;
            color: #67c0ea;
        }

        /* Data Card Grid */
        .data-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .data-item {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .data-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .data-item i {
            margin-right: 8px;
            color: #67c0ea;
        }

        /* Signature Image */
        .signature img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
            margin-top: 10px;
        }

        /* Academic Details Table */
        .academic-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .academic-table th,
        .academic-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .academic-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .academic-table td i {
            margin-right: 8px;
            color: #67c0ea;
        }

        /* Date Formatting */
        .date-format {
            font-family: monospace;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .profile-header {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .profile-avatar {
                margin-bottom: 15px;
            }

            .profile-actions {
                justify-content: center;
            }

            .data-card {
                grid-template-columns: 1fr;
            }

            .academic-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Profile Heading -->
        <h2 class="profile-heading font-weight-bold mb-4">
            <i class="fas fa-user-circle"></i> Student Profile
        </h2>

        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="{{ asset('img/avatars/user-default.png') }}" alt="Student Avatar">
            </div>
            <div class="profile-info">
                <h4>
                    {{ $students->studentname ?? 'N/A' }}
                    <span class="text-muted">{{ $students->studentsur ?? 'N/A' }}</span>
                    @if (!empty($students->is_flag) && $students->is_flag == 1)
                        <span title="Flagged Student" style="color:red;font-size:18px;margin-left:6px;">&#x1F6A9;</span>
                    @endif
                </h4>
                <div class="text-muted">ID: {{ $students->studentid ?? 'N/A' }}</div>
            </div>
            <div class="profile-actions">
                <a href="{{ url('admission-editNew', [$students->studentid ?? '']) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.admission.index') }}" class="btn btn-warning">Back</a>
            </div>
        </div>

        <!-- Student Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user"></i> Student Details</h5>
                <div class="data-card">
                    @foreach ([
            // 'studentid' => ['icon' => 'fas fa-id-card', 'label' => 'Student ID'],
            'admissionid' => ['icon' => 'fas fa-file-alt', 'label' => 'Family ID'],
            // 'guardianid' => ['icon' => 'fas fa-shield-alt', 'label' => 'Guardian ID'],
            // 'kinid' => ['icon' => 'fas fa-user-friends', 'label' => 'Kin ID'],
            'studentname' => ['icon' => 'fas fa-user', 'label' => 'Name'],
            'studentsur' => ['icon' => 'fas fa-user', 'label' => 'Surname'],
            'studentdob' => ['icon' => 'fas fa-birthday-cake', 'label' => 'DOB'],
            'studentgender' => ['icon' => 'fas fa-venus-mars', 'label' => 'Gender'],
            'studentyearinschool' => ['icon' => 'fas fa-graduation-cap', 'label' => 'Year in School'],
            'student_status' => ['icon' => 'fas fa-info-circle', 'label' => 'Status'],
            'additionalNeeds' => ['icon' => 'fas fa-wheelchair', 'label' => 'Additional Needs'],
            'medical_condition' => ['icon' => 'fas fa-heartbeat', 'label' => 'Medical Condition'],
            'medicalConsent' => ['icon' => 'fas fa-check-circle', 'label' => 'Medical Consent'],
            'photoConsent' => ['icon' => 'fas fa-camera', 'label' => 'Photo Consent'],
            'leaveAlone' => ['icon' => 'fas fa-user-times', 'label' => 'Leave Alone'],
            'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
        ] as $key => $data)
                        <div class="data-item">
                            <i class="{{ $data['icon'] }}"></i>
                            <strong>{{ $data['label'] }}:</strong>
                            @if ($key === 'photoConsent')
                                {{ $students->$key ? (is_array(json_decode($students->$key, true)) ? implode(', ', json_decode($students->$key, true)) : $students->$key) : 'N/A' }}
                            @elseif ($key === 'studentdob')
                                <span class="date-format">{{ $students->$key ?? 'N/A' }}</span>
                            @elseif ($key === 'studentname')
                                {{ $students->$key ?? 'N/A' }}
                                @if (!empty($students->is_flag) && $students->is_flag == 1)
                                    <span title="Flagged Student" style="color:red;font-size:14px;margin-left:4px;">&#x1F6A9;</span>
                                @endif
                            @else
                                {{ $students->$key ?? 'N/A' }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Admission Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-school"></i> Admission Details</h5>
                <div class="data-card">
                    @foreach ([
            // 'admissionid' => ['icon' => 'fas fa-id-card', 'label' => 'Admission ID'],
            // 'familyno' => ['icon' => 'fas fa-file-alt', 'label' => 'Family Number'],
            'formfilingdate' => ['icon' => 'fas fa-calendar-alt', 'label' => 'Form Filing Date'],
            'joiningdate' => ['icon' => 'fas fa-calendar-check', 'label' => 'Joining Date'],
            'medicalcondition' => ['icon' => 'fas fa-heartbeat', 'label' => 'Medical Condition'],
            'feedetail' => ['icon' => 'fas fa-money-bill-wave', 'label' => 'Fee Detail'],
            'timing' => ['icon' => 'fas fa-clock', 'label' => 'Timing'],
            'familystatus' => ['icon' => 'fas fa-home', 'label' => 'Family Status'],
            'meetingdetail' => ['icon' => 'fas fa-handshake', 'label' => 'Meeting Detail'],
            'payment_method' => ['icon' => 'fas fa-credit-card', 'label' => 'Payment Method'],
            'add_comment' => ['icon' => 'fas fa-comment', 'label' => 'Additional Comment'],
            // 'child_name1' => ['icon' => 'fas fa-child', 'label' => 'Child Name 1'],
            // 'school_name1' => ['icon' => 'fas fa-school', 'label' => 'School Name 1'],
            // 'child_name2' => ['icon' => 'fas fa-child', 'label' => 'Child Name 2'],
            // 'school_name2' => ['icon' => 'fas fa-school', 'label' => 'School Name 2'],
            // 'child_name3' => ['icon' => 'fas fa-child', 'label' => 'Child Name 3'],
            // 'school_name3' => ['icon' => 'fas fa-school', 'label' => 'School Name 3'],
            // 'child_name4' => ['icon' => 'fas fa-child', 'label' => 'Child Name 4'],
            // 'school_name4' => ['icon' => 'fas fa-school', 'label' => 'School Name 4'],
            // 'child_name5' => ['icon' => 'fas fa-child', 'label' => 'Child Name 5'],
            // 'school_name5' => ['icon' => 'fas fa-school', 'label' => 'School Name 5'],
            // 'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
        ] as $key => $data)
                        <div class="data-item">
                            <i class="{{ $data['icon'] }}"></i>
                            <strong>{{ $data['label'] }}:</strong>
                            @if (in_array($key, ['formfilingdate', 'joiningdate']))
                                <span class="date-format">{{ $admission->$key ?? 'N/A' }}</span>
                            @else
                                {{ $admission->$key ?? 'N/A' }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Consent Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-signature"></i> Consent Details</h5>
                @if ($consent)
                    <div class="data-card">
                        @foreach ([
            // 'id' => ['icon' => 'fas fa-id-card', 'label' => 'Consent ID'],
            // 'family_id' => ['icon' => 'fas fa-file-alt', 'label' => 'Family ID'],
            'consent_1_first_name' => ['icon' => 'fas fa-user', 'label' => 'First Name'],
            'consent_1_last_name' => ['icon' => 'fas fa-user', 'label' => 'Last Name'],
            'consent_1_date' => ['icon' => 'fas fa-calendar-alt', 'label' => 'Consent Date'],
            'how_did_you_hear' => ['icon' => 'fas fa-question-circle', 'label' => 'How Did You Hear'],
            // 'consent_1signature' => ['icon' => 'fas fa-signature', 'label' => 'Signature'],
            // 'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
            // 'created_at' => ['icon' => 'fas fa-clock', 'label' => 'Created At'],
            // 'updated_at' => ['icon' => 'fas fa-clock', 'label' => 'Updated At'],
        ] as $key => $data)
                            <div class="data-item">
                                <i class="{{ $data['icon'] }}"></i>
                                <strong>{{ $data['label'] }}:</strong>
                                @if ($key === 'consent_1signature')
                                    @if ($consent->$key)
                                        <div class="signature">
                                            <img src="{{ $consent->$key }}" alt="Signature">
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                @elseif ($key === 'consent_1_date' || $key === 'created_at' || $key === 'updated_at')
                                    <span class="date-format">{{ $consent->$key ?? 'N/A' }}</span>
                                @else
                                    {{ $consent->$key ?? 'N/A' }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        No consent information available.
                    </div>
                @endif
            </div>
        </div>

        <!-- Medical Condition Details -->
        {{-- <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-md"></i> Medical Condition Details</h5>
                <div class="data-card">
                    @foreach ([
            // 'guardianid' => ['icon' => 'fas fa-shield-alt', 'label' => 'Guardian ID'],
            // 'student_id' => ['icon' => 'fas fa-id-card', 'label' => 'Student ID'],
            // 'family_id' => ['icon' => 'fas fa-file-alt', 'label' => 'Family ID'],
            'drName' => ['icon' => 'fas fa-user-md', 'label' => 'Doctor Name'],
            'drNumber' => ['icon' => 'fas fa-phone', 'label' => 'Doctor Number'],
            'medicalDetails' => ['icon' => 'fas fa-file-medical', 'label' => 'Medical Details'],
            'allergies' => ['icon' => 'fas fa-allergies', 'label' => 'Allergies'],
            'medicalConsent' => ['icon' => 'fas fa-check-circle', 'label' => 'Medical Consent'],
            'gpPrefix' => ['icon' => 'fas fa-user-md', 'label' => 'GP Prefix'],
            'gpAddress' => ['icon' => 'fas fa-map', 'label' => 'GP Address'],
            'gpAddressLineTwo' => ['icon' => 'fas fa-map', 'label' => 'GP Address Line Two'],
            'gp_city' => ['icon' => 'fas fa-city', 'label' => 'GP City'],
            'gp_countyStateRegion' => ['icon' => 'fas fa-globe', 'label' => 'GP County/State/Region'],
            'gpzipCode' => ['icon' => 'fas fa-map-pin', 'label' => 'GP Zip Code'],
            'gpcountry' => ['icon' => 'fas fa-flag', 'label' => 'GP Country'],
            // 'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
        ] as $key => $data)
                        <div class="data-item">
                            <i class="{{ $data['icon'] }}"></i>
                            <strong>{{ $data['label'] }}:</strong> {{ $medical_condition->$key ?? 'N/A' }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div> --}}
        <!-- Medical Condition Details -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title"><i class="fas fa-user-md"></i> Medical Condition Details</h5>
        <div class="data-card">
            @foreach ([
                'additionalNeeds' => ['icon' => 'fas fa-wheelchair', 'label' => 'Additional Needs'],
                'additional_needs_explanation' => ['icon' => 'fas fa-comment-medical', 'label' => 'Additional Needs Explanation'],
                'medicalDetails' => ['icon' => 'fas fa-file-medical', 'label' => 'Medical Details'],
                'medical_conditions_explanation' => ['icon' => 'fas fa-notes-medical', 'label' => 'Medical Conditions Explanation'],
                'allergies' => ['icon' => 'fas fa-allergies', 'label' => 'Allergies'],
                'allergies_explanation' => ['icon' => 'fas fa-comment-medical', 'label' => 'Allergies Explanation'],
                'drName' => ['icon' => 'fas fa-user-md', 'label' => 'Doctor Name'],
                'drNumber' => ['icon' => 'fas fa-phone', 'label' => 'Doctor Number'],
                'medicalConsent' => ['icon' => 'fas fa-check-circle', 'label' => 'Medical Consent'],
                'gpPrefix' => ['icon' => 'fas fa-user-md', 'label' => 'GP Prefix'],
                'gpAddress' => ['icon' => 'fas fa-map', 'label' => 'GP Address'],
                'gpAddressLineTwo' => ['icon' => 'fas fa-map', 'label' => 'GP Address Line Two'],
                'gp_city' => ['icon' => 'fas fa-city', 'label' => 'GP City'],
                'gp_countyStateRegion' => ['icon' => 'fas fa-globe', 'label' => 'GP County/State/Region'],
                'gpzipCode' => ['icon' => 'fas fa-map-pin', 'label' => 'GP Zip Code'],
                'gpcountry' => ['icon' => 'fas fa-flag', 'label' => 'GP Country'],
            ] as $key => $data)
                <div class="data-item">
                    <i class="{{ $data['icon'] }}"></i>
                    <strong>{{ $data['label'] }}:</strong>
                    @if (in_array($key, ['additionalNeeds', 'additional_needs_explanation']))
                        {{ $students->$key ?? 'N/A' }}
                    @else
                        {{ $medical_condition->$key ?? 'N/A' }}
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

        <!-- Guardian Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-shield"></i> Guardian Details</h5>
                <div class="data-card">
                    @foreach ([
            // 'Guardianid' => ['icon' => 'fas fa-id-card', 'label' => 'Guardian ID'],
            'guardianname' => ['icon' => 'fas fa-user', 'label' => 'Name'],
            'guardianaddress' => ['icon' => 'fas fa-map-marker-alt', 'label' => 'Address'],
            'address_line_2' => ['icon' => 'fas fa-map', 'label' => 'Address Line 2'],
            'city' => ['icon' => 'fas fa-city', 'label' => 'City'],
            'countyStateRegion' => ['icon' => 'fas fa-globe', 'label' => 'State/Region'],
            'zIPCode' => ['icon' => 'fas fa-map-pin', 'label' => 'Zip Code'],
            'country' => ['icon' => 'fas fa-flag', 'label' => 'Country'],
            'guardiantel' => ['icon' => 'fas fa-envelope', 'label' => 'Email'],
            'guardianmob' => ['icon' => 'fas fa-phone', 'label' => 'Mobile'],
            'parent_relationship' => ['icon' => 'fas fa-users', 'label' => 'Parent Relationship'],
            // 'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
        ] as $key => $data)
                        <div class="data-item">
                            <i class="{{ $data['icon'] }}"></i>
                            <strong>{{ $data['label'] }}:</strong> {{ $guardian->$key ?? 'N/A' }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kin (Next of Kin) Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-friends"></i> Next of Kin Details</h5>
                <div class="data-card">
                    @foreach ([
            // 'kinid' => ['icon' => 'fas fa-id-card', 'label' => 'Kin ID'],
            'kinname' => ['icon' => 'fas fa-user', 'label' => 'Name'],
            'kinaddress' => ['icon' => 'fas fa-map-marker-alt', 'label' => 'Address'],
            'emergency_conatct1_Address_line2' => ['icon' => 'fas fa-map', 'label' => 'Address Line 2'],
            'emergency_conatct1_city' => ['icon' => 'fas fa-city', 'label' => 'City'],
            'emergency_conatct1_country_state_region' => ['icon' => 'fas fa-globe', 'label' => 'State/Region'],
            'emergency_conatct1_zipCode' => ['icon' => 'fas fa-map-pin', 'label' => 'Zip Code'],
            'emergency_conatct1_country' => ['icon' => 'fas fa-flag', 'label' => 'Country'],
            'kintel' => ['icon' => 'fas fa-envelope', 'label' => 'Email'],
            'kinmob' => ['icon' => 'fas fa-phone', 'label' => 'Mobile'],
            // 'branch_name' => ['icon' => 'fas fa-building', 'label' => 'Branch Name'],
            // 'branch_id' => ['icon' => 'fas fa-id-badge', 'label' => 'Branch ID'],
        ] as $key => $data)
                        <div class="data-item">
                            <i class="{{ $data['icon'] }}"></i>
                            <strong>{{ $data['label'] }}:</strong> {{ $kin->$key ?? 'N/A' }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Academic Details Table -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-book"></i> Academic Details</h5>
                <table class="academic-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-book"></i> Subject Names</th>
                             <th><i class="fas fa-graduation-cap"></i> Qualification</th>
                            <th><i class="fas fa-layer-group"></i> Tier</th>
                            <th><i class="fas fa-clock"></i> Sessions</th>
                             <th><i class="fas fa-clipboard-check"></i> Current Grade</th>
                            <th><i class="fas fa-bullseye"></i> Target Grades</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
    // Parse subject_names
    $subjectNames = $students->subject_names
        ? (is_array(json_decode($students->subject_names, true))
            ? json_decode($students->subject_names, true)
            : explode(',', $students->subject_names))
        : [];
    $subjectNames = array_map('trim', $subjectNames);

    // Parse qualifications
    $qualifications = $students->qualifications
        ? (is_array(json_decode($students->qualifications, true))
            ? json_decode($students->qualifications, true)
            : explode(',', $students->qualifications))
        : [];
    $qualifications = array_map('trim', $qualifications);

    // Parse tier
    $tiers = $students->tier
        ? (is_array(json_decode($students->tier, true))
            ? json_decode($students->tier, true)
            : explode(',', $students->tier))
        : [];
    $tiers = array_map('trim', $tiers);

    // Parse studenthours
    $studentHours = $students->studenthours
        ? (is_array(json_decode($students->studenthours, true))
            ? json_decode($students->studenthours, true)
            : explode(',', $students->studenthours))
        : [];
    $studentHours = array_map('trim', $studentHours);

    // Parse current_grades
    $current_grades = $students->current_grades
        ? (is_array(json_decode($students->current_grades, true))
            ? json_decode($students->current_grades, true)
            : explode(',', $students->current_grades))
        : [];
    $current_grades = array_map('trim', $current_grades);

    // Parse target_grades
    $targetGrades = $students->target_grades
        ? (is_array(json_decode($students->target_grades, true))
            ? json_decode($students->target_grades, true)
            : explode(',', $students->target_grades))
        : [];
    $targetGrades = array_map('trim', $targetGrades);

    // Find the maximum number of rows
    $maxRows = max(
        count($subjectNames),
        count($qualifications),
        count($tiers),
        count($studentHours),
        count($current_grades),
        count($targetGrades)
    );

    // Pad arrays to make them equal length
    $subjectNames = array_pad($subjectNames, $maxRows, 'N/A');
    $qualifications = array_pad($qualifications, $maxRows, 'N/A');
    $tiers = array_pad($tiers, $maxRows, 'N/A');
    $studentHours = array_pad($studentHours, $maxRows, 'N/A');
    $current_grades = array_pad($current_grades, $maxRows, 'N/A');
    $targetGrades = array_pad($targetGrades, $maxRows, 'N/A');
@endphp


                        @if ($maxRows > 0)
                            @for ($i = 0; $i < $maxRows; $i++)
                                <tr>
                                    <td>{{ $subjectNames[$i] }}</td>
                                    <td>{{$qualifications[$i]}}</td>
                                    <td>{{ $tiers[$i] }}</td>
                                    <td>{{ $studentHours[$i] }}</td>
                                    <td>{{$current_grades[$i]}}</td>
                                    <td>{{ $targetGrades[$i] }}</td>
                                </tr>
                            @endfor
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No academic details available.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateElements = document.querySelectorAll('.date-format');
            dateElements.forEach(el => {
                const dateText = el.textContent;
                if (dateText && dateText !== 'N/A') {
                    const date = new Date(dateText);
                    if (!isNaN(date)) {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();
                        el.textContent = `${day}/${month}/${year}`;
                    } else {
                        el.textContent = 'N/A';
                    }
                } else {
                    el.textContent = 'N/A';
                }
            });
        });
    </script>
@endsection
```
