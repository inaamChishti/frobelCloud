@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Grades Module</h1>
                <p>Enter and manage student grades efficiently.</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="card-section">
            <div class="section-header">
                <i class="fas fa-filter"></i>
                <h2>Student Information</h2>
            </div>
            <div class="row g-4">
                <!-- Family ID -->
                <div class="col-md-4 col-lg-4">
                    <div class="card-section-inner">
                        <label for="family_id" class="form-label">Family ID <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <select id="family_id" class="form-select" required>
                                <option value="" selected disabled>Search Family ID</option>
                            </select>
                        </div>
                        <span id="family_id_error" class="invalid-feedback"></span>
                    </div>
                </div>

                <!-- Student Name -->
                <div class="col-md-4 col-lg-4">
                    <div class="card-section-inner">
                        <label for="student_name" class="form-label">Student Name <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                            <select id="student_name" class="form-select" required disabled>
                                <option value="" selected disabled>Select Student</option>
                            </select>
                        </div>
                        <span id="student_name_error" class="invalid-feedback"></span>
                    </div>
                </div>

                <!-- Month -->
                <div class="col-md-4 col-lg-4">
                    <div class="card-section-inner">
                        <label for="month" class="form-label">Month <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <select id="month" class="form-select" required>
                                <option value="">Select Month</option>
                            </select>
                        </div>
                        <span id="month_error" class="invalid-feedback"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects and Grades Section -->
        <div class="card-section grades-section" id="subjects_section" style="display: none;">
            <div class="section-header-compact">
                <div class="header-title-compact">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Subject Grades</h3>
                    <span class="subjects-count-badge" id="subjects_count"></span>
                </div>
                <div class="validation-rule" id="validation_rule">
                    <i class="fas fa-info-circle"></i>
                    <span id="validation_text">Grade format: Single digit <strong>0-9</strong> or single letter <strong>A-Z</strong> only (e.g., A, B, 5, 7). For KS1/KS2/KS3, grades are auto-populated from attendance.</span>
                </div>
            </div>
            
            <div class="grades-table-compact">
                <table id="grades_table" class="table-compact">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade <span class="required-star">*</span></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="grades-footer-compact">
                <div class="auto-save-status" id="autoSaveStatus" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span id="autoSaveText">Saving...</span>
                </div>
                <button type="button" id="btnSaveGrades" class="btn-save-compact">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Card Section Styles */
        .card-section {
            background: var(--card-bg);
            border: 2px solid var(--primary-blue);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border-light);
        }

        .section-header i {
            color: var(--primary-blue);
            font-size: 20px;
        }

        .section-header h2 {
            color: var(--primary-blue-dark);
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .card-section-inner {
            margin-bottom: 0;
        }

        /* Form Label Styles */
        .form-label {
            font-weight: 600;
            color: var(--primary-blue-dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required-star {
            color: var(--red-logout);
        }

        /* Input Group Styles */
        .input-group-text {
            background: var(--primary-blue);
            color: var(--text-light);
            border: 2px solid var(--primary-blue);
        }

        .form-control,
        .form-select {
            border: 2px solid var(--primary-blue);
            border-radius: 6px;
            padding: 10px 12px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-blue-dark);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
            outline: none;
        }

        /* Select2 Styling - Fix for input-group layout */
        .input-group .select2-container {
            flex: 1 1 auto;
            width: 1% !important;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid var(--primary-blue);
            border-left: none;
            border-radius: 0 6px 6px 0;
            height: auto;
            min-height: 42px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            color: var(--text-dark);
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 10px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary-blue-dark);
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        }

        /* Fix border radius for input-group with Select2 */
        .input-group > .input-group-text:first-child {
            border-radius: 6px 0 0 6px;
            border-right: none;
        }

        .input-group .select2-container--default .select2-selection--single {
            border-left: none;
        }

        .input-group .select2-container--default.select2-container--focus .select2-selection--single,
        .input-group .select2-container--default.select2-container--open .select2-selection--single {
            border-left: 2px solid var(--primary-blue-dark);
        }

        .select2-dropdown {
            border: 2px solid var(--primary-blue);
            border-radius: 6px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-blue);
            color: var(--text-light);
        }

        /* Ensure input-group displays correctly */
        .input-group {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }

        .input-group > .form-control,
        .input-group > .form-select,
        .input-group > .select2-container {
            position: relative;
            flex: 1 1 auto;
            width: 1% !important;
            min-width: 0;
        }

        .form-control:disabled,
        .form-select:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        /* Compact Grades Section Styles */
        .grades-section {
            margin-top: 20px;
        }

        .section-header-compact {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-light);
        }

        .header-title-compact {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title-compact i {
            font-size: 18px;
            color: var(--primary-blue);
        }

        .header-title-compact h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-blue-dark);
        }

        .subjects-count-badge {
            background: var(--primary-blue);
            color: var(--text-light);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: auto;
        }

        /* Compact Table */
        .grades-table-compact {
            margin-bottom: 16px;
        }

        .table-compact {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-compact thead {
            background: var(--primary-blue);
            color: var(--text-light);
        }

        .table-compact thead th {
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            text-align: left;
            border: none;
        }

        .table-compact thead th:first-child {
            border-radius: 6px 0 0 0;
        }

        .table-compact thead th:last-child {
            border-radius: 0 6px 0 0;
        }

        .table-compact tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table-compact tbody tr:last-child td {
            border-bottom: none;
        }

        .table-compact tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-compact tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        /* Subject Name - Compact */
        .subject-name {
            font-weight: 500;
            color: var(--text-dark);
            font-size: 14px;
        }

        /* Validation Rule */
        .validation-rule {
            margin-top: 8px;
            padding: 8px 12px;
            background: #e3f2fd;
            border-left: 3px solid var(--primary-blue);
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--primary-blue-dark);
        }

        .validation-rule i {
            color: var(--primary-blue);
            font-size: 14px;
        }

        .validation-rule strong {
            color: var(--primary-blue-dark);
            font-weight: 700;
        }

        /* Grade Input - Compact */
        .grade-input {
            width: 100%;
            min-width: 200px;
            padding: 8px 12px;
            border: 2px solid var(--primary-blue);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s ease;
            background: #fff;
        }

        .grade-input:focus {
            border-color: var(--primary-blue-dark);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .grade-input.is-invalid {
            border-color: var(--red-logout);
            background-color: #fff5f5;
        }

        .grade-input.is-valid {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        /* Footer - Compact */
        .grades-footer-compact {
            display: flex;
            justify-content: flex-end;
            padding-top: 12px;
            border-top: 1px solid var(--border-light);
        }

        .btn-save-compact {
            background: var(--primary-blue);
            border: none;
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            color: var(--text-light);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-save-compact:hover {
            background: var(--primary-blue-dark);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
        }

        .btn-save-compact:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Auto-save status styles */
        .auto-save-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #e3f2fd;
            border: 1px solid var(--primary-blue);
            border-radius: 6px;
            font-size: 13px;
            color: var(--primary-blue-dark);
            margin-right: auto;
        }

        .auto-save-status i {
            color: var(--primary-blue);
        }

        .auto-save-status.saved {
            background: #f0fdf4;
            border-color: #10b981;
            color: #10b981;
        }

        .auto-save-status.saved i {
            color: #10b981;
        }

        .auto-save-status.error {
            background: #fff5f5;
            border-color: var(--red-logout);
            color: var(--red-logout);
        }

        .auto-save-status.error i {
            color: var(--red-logout);
        }

        /* Invalid Feedback */
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 4px;
            font-size: 12px;
            color: var(--red-logout);
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 20px;
            color: var(--primary-blue);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-section {
                padding: 16px;
            }

            .header-title-compact {
                flex-wrap: wrap;
            }

            .subjects-count-badge {
                margin-left: 0;
                margin-top: 8px;
            }

            .table-compact thead th {
                padding: 8px 10px;
                font-size: 12px;
            }

            .table-compact tbody td {
                padding: 10px;
            }

            .subject-name {
                font-size: 13px;
            }

            .grade-input {
                min-width: 100%;
                font-size: 14px;
                padding: 8px 10px;
            }

            .btn-save-compact {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize month dropdown with current month as default
            initializeMonthDropdown();

            // Initialize Family ID as searchable dropdown using Select2
            // Allow user to type and search for Family ID
            $('#family_id').select2({
                placeholder: 'Search or type Family ID (min 3 characters)',
                allowClear: true,
                tags: true, // Allow custom values to be entered
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route('get.students.by.family.grades') }}',
                    delay: 500,
                    dataType: 'json',
                    type: 'GET',
                    data: function(params) {
                        return {
                            family_id: params.term
                        };
                    },
                    processResults: function(data, params) {
                        // 🔒 Block check: If family ID is blocked, reject immediately
                        if (data.blocked) {
                            var blockMsg = data.message || data.error || data.block_reason ||
                                'Family ID Blocked. Please contact admin office for more details.';
                            toastr.error(blockMsg);
                            // Also clear the currently typed value
                            $('#family_id').val(null).trigger('change');
                            return { results: [] };
                        }
                        if (data.success && data.students && data.students.length > 0) {
                            // Extract unique family IDs from students
                            const familyIds = [...new Set(data.students.map(s => s.admissionid))];
                            const results = familyIds.map(function(id) {
                                return {
                                    id: id,
                                    text: id
                                };
                            });
                            // Also include the searched term as an option if not already present
                            const searchedTerm = data.family_id || params.term;
                            if (searchedTerm && !familyIds.includes(searchedTerm)) {
                                results.unshift({
                                    id: searchedTerm,
                                    text: searchedTerm
                                });
                            }
                            return { results: results };
                        }
                        // Even if no students found, allow the searched term to be selected
                        const searchedTerm = data.family_id || params.term || '';
                        return {
                            results: [{
                                id: searchedTerm,
                                text: searchedTerm
                            }]
                        };
                    },
                    error: function(xhr) {
                        // 🔒 HTTP error case (4xx / 5xx) - handle block if thrown via error status
                        if (xhr && xhr.responseJSON && xhr.responseJSON.blocked) {
                            var blockMsg = xhr.responseJSON.message || xhr.responseJSON.error ||
                                'Family ID Blocked. Please contact admin office for more details.';
                            toastr.error(blockMsg);
                            $('#family_id').val(null).trigger('change');
                            resetStudentDropdown();
                        }
                    },
                    cache: true
                }
            });

            // When Family ID is selected, fetch students
            $('#family_id').on('select2:select', function(e) {
                const familyId = e.params.data.id;
                if (familyId) {
                    fetchStudentsByFamilyId(familyId);
                    // Hide subjects section when Family ID changes
                    $('#subjects_section').hide();
                }
            });

            // When Family ID is cleared, reset student dropdown
            $('#family_id').on('select2:clear', function() {
                resetStudentDropdown();
            });

            // Also handle when Family ID value changes directly
            $('#family_id').on('change', function() {
                const familyId = $(this).val();
                if (!familyId) {
                    resetStudentDropdown();
                }
            });

            // Get current month name
            function getCurrentMonth() {
                const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                return months[new Date().getMonth()];
            }

            // Initialize month dropdown
            function initializeMonthDropdown() {
                const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                const currentMonthIndex = new Date().getMonth();
                const currentYear = new Date().getFullYear();

                const monthSelect = $('#month');
                monthSelect.empty();
                monthSelect.append('<option value="">Select Month</option>');

                // Add current month first
                const currentMonth = months[currentMonthIndex];
                const currentValue = `${currentMonth}_${currentYear}`;
                const currentOption = $('<option>', {
                    value: currentValue,
                    text: `${currentMonth} ${currentYear}`
                });
                currentOption.attr('selected', true);
                monthSelect.append(currentOption);

                // Add previous months going back (avoid duplicates)
                for (let i = 1; i <= 12; i++) {
                    const prevDate = new Date(currentYear, currentMonthIndex - i, 1);
                    const prevMonth = months[prevDate.getMonth()];
                    const prevYear = prevDate.getFullYear();
                    const value = `${prevMonth}_${prevYear}`;
                    
                    // Check if this option already exists to avoid duplicates
                    if (monthSelect.find(`option[value="${value}"]`).length === 0) {
                        monthSelect.append($('<option>', {
                            value: value,
                            text: `${prevMonth} ${prevYear}`
                        }));
                    }
                }
            }

            // Fetch students by Family ID
            function fetchStudentsByFamilyId(familyId) {
                if (!familyId) {
                    resetStudentDropdown();
                    return;
                }

                $('#student_name').prop('disabled', true).empty().append(
                    '<option value="" selected disabled>Loading...</option>'
                );

                $.ajax({
                    url: '{{ route('get.students.by.family.grades') }}',
                    type: 'GET',
                    data: {
                        family_id: familyId
                    },
                    success: function(response) {
                        // 🔒 Block check
                        if (response.blocked) {
                            resetStudentDropdown();
                            $('#family_id').val(null).trigger('change');
                            toastr.error('Family ID Blocked. Please contact admin office for more details.');
                            return;
                        }

                        const studentSelect = $('#student_name');
                        studentSelect.empty();
                        studentSelect.append('<option value="" selected disabled>Select Student</option>');

                        if (response.success && response.students && response.students.length > 0) {
                            $.each(response.students, function(index, student) {
                                // Parse subject_names if it's a string, otherwise use as is
                                let subjects = [];
                                if (student.subject_names) {
                                    if (typeof student.subject_names === 'string') {
                                        try {
                                            subjects = JSON.parse(student.subject_names);
                                        } catch (e) {
                                            // If parsing fails, treat as single subject or empty
                                            subjects = student.subject_names ? [student.subject_names] : [];
                                        }
                                    } else if (Array.isArray(student.subject_names)) {
                                        subjects = student.subject_names;
                                    }
                                }

                                studentSelect.append($('<option>', {
                                    value: student.studentid || student.admissionid,
                                    text: student.full_name || 'Name Not Available',
                                    'data-subjects': JSON.stringify(subjects),
                                    'data-admissionid': student.admissionid
                                }));
                            });
                            studentSelect.prop('disabled', false);
                            toastr.success('Students loaded successfully');
                        } else {
                            studentSelect.append('<option value="" disabled>No students found</option>');
                            const message = response.message || 'No students found for this Family ID';
                            toastr.warning(message);
                        }
                    },
                    error: function(xhr) {
                        resetStudentDropdown();
                        // 🔒 Block check (403 from endpoint)
                        if (xhr.responseJSON && xhr.responseJSON.blocked) {
                            $('#family_id').val(null).trigger('change');
                        }
                        const errorMsg = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to fetch students. Please try again.';
                        toastr.error(errorMsg);
                    }
                });
            }

            // Reset student dropdown
            function resetStudentDropdown() {
                $('#student_name').prop('disabled', true).empty().append(
                    '<option value="" selected disabled>Select Student</option>'
                );
                $('#subjects_section').hide();
                $('#grades_table tbody').empty();
                $('#subjects_count').empty();
            }

            // Student Name change handler
            $('#student_name').on('change', function() {
                checkAndDisplaySubjects();
            });

            // Month change handler
            $('#month').on('change', function() {
                checkAndDisplaySubjects();
            });

            // Function to check all three fields and display subjects
            function checkAndDisplaySubjects() {
                const familyId = $('#family_id').val();
                const studentName = $('#student_name').val();
                const month = $('#month').val();

                // Only display subjects if ALL three fields are selected
                if (!familyId || !studentName || !month) {
                    $('#subjects_section').hide();
                    return;
                }

                const selectedOption = $('#student_name').find('option:selected');
                let subjectsJson = selectedOption.data('subjects');

                // Parse subjects if it's a string
                let subjects = [];
                if (subjectsJson) {
                    if (typeof subjectsJson === 'string') {
                        try {
                            subjects = JSON.parse(subjectsJson);
                        } catch (e) {
                            subjects = [];
                        }
                    } else if (Array.isArray(subjectsJson)) {
                        subjects = subjectsJson;
                    }
                }

                if (!subjects || subjects.length === 0) {
                    toastr.warning('No subjects found for this student');
                    $('#subjects_section').hide();
                    return;
                }

                displaySubjectsAndGrades(subjects);
            }

            // Display subjects and grades input table
            function displaySubjectsAndGrades(subjects) {
                const tbody = $('#grades_table tbody');
                tbody.empty();

                if (!subjects || subjects.length === 0) {
                    toastr.warning('No subjects found for this student');
                    $('#subjects_section').hide();
                    return;
                }

                // Update subjects count
                const subjectsCount = subjects.length;
                $('#subjects_count').text(subjectsCount + ' Subject' + (subjectsCount > 1 ? 's' : ''));

                // Fetch existing grades for this student and month
                const studentId = $('#student_name').val();
                const month = $('#month').val();
                
                fetchExistingGrades(studentId, month, subjects);
            }

            // Fetch existing grades from database
            function fetchExistingGrades(studentId, month, subjects) {
                if (!studentId || !month) {
                    // Display empty inputs if student or month not selected
                    populateGradeInputs(subjects, {}, {});
                    return;
                }

                $.ajax({
                    url: '{{ route('grades.get.existing') }}',
                    type: 'GET',
                    data: {
                        student_id: studentId,
                        month: month
                    },
                    success: function(response) {
                        if (response.success) {
                            const grades = response.grades || {};
                            const keyStages = response.keyStages || {};
                            const noDataFound = response.noDataFound || {};
                            
                            // Check if any subjects have no attendance data
                            const hasNoData = Object.keys(noDataFound).length > 0;
                            if (hasNoData) {
                                const noDataSubjects = Object.keys(noDataFound);
                                toastr.warning('No attendance data found for the selected month for: ' + noDataSubjects.join(', '));
                            }
                            
                            if (response.hasGrades && Object.keys(grades).length > 0) {
                                // Check if any grades are from attendance (KS1, KS2, KS3)
                                const hasAttendanceGrades = Object.keys(keyStages).some(subject => {
                                    const ks = keyStages[subject];
                                    return ks && ['KS1', 'KS2', 'KS3'].includes(ks.toUpperCase());
                                });
                                
                                if (hasAttendanceGrades) {
                                    toastr.info('Grades auto-populated from last attendance for KS1/KS2/KS3 subjects.');
                                } else {
                                    toastr.info('Existing grades loaded. You can edit them.');
                                }
                            } else {
                                // Check if we have key stages to determine if auto-population should happen
                                const hasKS123 = Object.values(keyStages).some(ks => 
                                    ks && ['KS1', 'KS2', 'KS3'].includes(ks.toUpperCase())
                                );
                                
                                if (hasKS123 && !hasNoData) {
                                    toastr.info('Grades auto-populated from last attendance for KS1/KS2/KS3 subjects.');
                                } else if (!hasKS123) {
                                    toastr.success('Subjects loaded. Please enter grades.');
                                }
                            }
                            
                            populateGradeInputs(subjects, grades, keyStages, noDataFound);
                        } else {
                            populateGradeInputs(subjects, {}, {}, {});
                            toastr.warning('Failed to load existing grades');
                        }
                    },
                    error: function(xhr) {
                        // On error, still show empty inputs
                        populateGradeInputs(subjects, {}, {});
                        console.error('Error fetching existing grades:', xhr);
                    }
                });
            }

            // Populate grade inputs with subjects and existing grades
            function populateGradeInputs(subjects, existingGrades, keyStages, noDataFound) {
                const tbody = $('#grades_table tbody');
                tbody.empty();

                subjects.forEach(function(subject) {
                    const row = $('<tr>');
                    row.append($('<td>', {
                        class: 'subject-name',
                        text: subject
                    }));

                    const gradeCell = $('<td>');
                    const existingGrade = existingGrades[subject] || '';
                    const keyStage = keyStages[subject] || '';
                    const hasNoData = noDataFound[subject] || false;
                    
                    // Check if key stage is KS1, KS2, or KS3
                    const isKS123 = keyStage && ['KS1', 'KS2', 'KS3'].includes(keyStage.toUpperCase());
                    
                    if (isKS123 && hasNoData) {
                        // Show "No data found" message for KS1/KS2/KS3 with no attendance
                        const noDataSpan = $('<span>', {
                            class: 'no-data-message',
                            style: 'color: #dc3545; font-style: italic;',
                            text: 'No data found'
                        });
                        gradeCell.append(noDataSpan);
                    } else {
                        const gradeInput = $('<input>', {
                            type: 'text',
                            class: 'grade-input' + (isKS123 ? ' auto-populated' : ''),
                            'data-subject': subject,
                            'data-key-stage': keyStage,
                            placeholder: isKS123 ? 'Auto-populated from attendance' : 'Enter grade (single digit 0-9 or letter A-Z)',
                            maxlength: isKS123 ? 100 : 1, // Allow longer text for book names
                            title: isKS123 ? 'Grade auto-populated from last attendance (read-only)' : 'Grade format: Single digit (0-9) or single letter (A-Z) only',
                            value: existingGrade,
                            readonly: isKS123, // Make readonly ONLY for KS1, KS2, KS3; all others are editable
                            style: isKS123 ? 'background-color: #f0f0f0; cursor: not-allowed;' : ''
                        });
                        
                        // Explicitly ensure non-KS123 inputs are NOT readonly (editable)
                        if (!isKS123) {
                            gradeInput.prop('readonly', false);
                        }
                        
                        // Add validation class if grade exists
                        if (existingGrade) {
                            gradeInput.addClass('is-valid');
                        }
                        
                        // Add info icon for auto-populated grades
                        if (isKS123 && existingGrade) {
                            const infoIcon = $('<i>', {
                                class: 'fas fa-info-circle',
                                style: 'margin-left: 8px; color: #2563eb; cursor: help;',
                                title: 'This grade is auto-populated from the last attendance in the selected month'
                            });
                            gradeCell.append(gradeInput);
                            gradeCell.append(infoIcon);
                        } else {
                            gradeCell.append(gradeInput);
                        }
                    }
                    
                    row.append(gradeCell);

                    tbody.append(row);
                });

                $('#subjects_section').slideDown();
            }

            // Validate grade format
            // For KS1, KS2, KS3: allows book names (any text)
            // For KS4, KS5, Adult: accepts single A-Z or single 0-9 only
            function isValidGrade(grade, keyStage) {
                if (!grade || grade.trim() === '') {
                    return false;
                }
                const trimmedGrade = grade.trim();
                
                // Check if key stage is KS1, KS2, or KS3
                const isKS123 = keyStage && ['KS1', 'KS2', 'KS3'].includes(keyStage.toUpperCase());
                
                if (isKS123) {
                    // For KS1, KS2, KS3: allow any non-empty text (book names)
                    return trimmedGrade.length > 0;
                } else {
                    // For KS4, KS5, Adult: only single character allowed
                    const gradePattern = /^[A-Za-z0-9]$/;
                    return gradePattern.test(trimmedGrade) && trimmedGrade.length === 1;
                }
            }

            // Filter input to allow only A-Z and 0-9 characters, and limit to single character
            // Skip filtering for KS1, KS2, KS3 (readonly auto-populated fields)
            function filterGradeInput(input) {
                const $input = $(input);
                const keyStage = $input.data('key-stage');
                const isKS123 = keyStage && ['KS1', 'KS2', 'KS3'].includes(keyStage.toUpperCase());
                
                // Skip filtering for KS1, KS2, KS3 (they are readonly anyway)
                if (isKS123 || $input.prop('readonly')) {
                    return;
                }
                
                // Remove any character that is not A-Z (uppercase/lowercase) or 0-9
                let filtered = input.value.replace(/[^A-Za-z0-9]/g, '');
                // Limit to only first character (single digit or single letter)
                if (filtered.length > 1) {
                    filtered = filtered.charAt(0);
                    toastr.warning('Only single digit (0-9) or single letter (A-Z) allowed', '', {timeOut: 2000});
                }
                if (input.value !== filtered) {
                    input.value = filtered;
                    // Trigger input event to update validation
                    $(input).trigger('input');
                }
            }

            // Validate all grades before saving (skip "No data found" subjects)
            function validateGrades() {
                let isValid = true;
                const gradeInputs = $('.grade-input');

                gradeInputs.each(function() {
                    const $input = $(this);
                    const grade = $input.val().trim();
                    const keyStage = $input.data('key-stage');

                    $input.removeClass('is-invalid is-valid');

                    // Skip validation for empty grades (they might be "No data found" cases)
                    // Only validate if there's a value
                    if (grade && !isValidGrade(grade, keyStage)) {
                        $input.addClass('is-invalid');
                        isValid = false;
                    } else if (grade) {
                        $input.addClass('is-valid');
                    }
                });

                return isValid;
            }

            // Auto-save debounce timer
            let autoSaveTimer = null;
            const AUTO_SAVE_DELAY = 1500; // 1.5 seconds after user stops typing

            // Common save function (used by both manual save and auto-save)
            function saveGrades(showToast = true, isAutoSave = false) {
                // Validate form fields
                const familyId = $('#family_id').val().trim();
                const studentName = $('#student_name').val();
                const month = $('#month').val();

                if (!familyId) {
                    if (showToast) {
                        toastr.error('Please select Family ID');
                        $('#family_id').focus();
                    }
                    return false;
                }

                if (!studentName) {
                    if (showToast) {
                        toastr.error('Please select Student Name');
                        $('#student_name').focus();
                    }
                    return false;
                }

                if (!month) {
                    if (showToast) {
                        toastr.error('Please select Month');
                        $('#month').focus();
                    }
                    return false;
                }

                // Validate grades
                if (!validateGrades()) {
                    if (showToast) {
                        toastr.error('Please fill all grade fields with valid values');
                    }
                    return false;
                }

                // Collect grade data (skip "No data found" subjects)
                const grades = [];
                $('.grade-input').each(function() {
                    const $input = $(this);
                    const grade = $input.val().trim();
                    // Only include if grade is not empty (skip "No data found" cases)
                    if (grade) {
                        grades.push({
                            subject: $input.data('subject'),
                            grade: grade
                        });
                    }
                });

                // Don't save if no grades to save
                if (grades.length === 0) {
                    return false;
                }

                // Prepare data for saving
                const gradeData = {
                    family_id: familyId,
                    student_id: studentName,
                    student_name: $('#student_name option:selected').text(),
                    month: month,
                    grades: grades
                };

                // Show loading state
                if (isAutoSave) {
                    const $status = $('#autoSaveStatus');
                    $status.show().removeClass('saved error').addClass('saving');
                    $('#autoSaveText').text('Saving...');
                    $status.find('i').removeClass('fa-check fa-times').addClass('fa-spinner fa-spin');
                } else {
                    const $btn = $('#btnSaveGrades');
                    const originalText = $btn.html();
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
                }

                // Save to backend
                return $.ajax({
                    url: '{{ route('grades.store') }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: gradeData,
                    success: function(response) {
                        if (response.success) {
                            if (isAutoSave) {
                                // Show auto-save success status
                                const $status = $('#autoSaveStatus');
                                $status.removeClass('saving error').addClass('saved');
                                $('#autoSaveText').text('Saved');
                                $status.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                                
                                // Hide status after 2 seconds
                                setTimeout(function() {
                                    $status.fadeOut(300);
                                }, 2000);
                            } else {
                                if (response.action === 'updated') {
                                    toastr.success('Grades updated successfully!');
                                } else {
                                    toastr.success('Grades saved successfully!');
                                }
                                // Reset button
                                $('#btnSaveGrades').prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                            }
                        } else {
                            if (isAutoSave) {
                                const $status = $('#autoSaveStatus');
                                $status.removeClass('saving saved').addClass('error');
                                $('#autoSaveText').text('Save failed');
                                $status.find('i').removeClass('fa-spinner fa-spin').addClass('fa-times');
                            } else {
                                toastr.error(response.message || 'Failed to save grades');
                                $('#btnSaveGrades').prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                            }
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Failed to save grades. Please try again.';
                        
                        if (isAutoSave) {
                            const $status = $('#autoSaveStatus');
                            $status.removeClass('saving saved').addClass('error');
                            $('#autoSaveText').text('Save failed');
                            $status.find('i').removeClass('fa-spinner fa-spin').addClass('fa-times');
                        } else {
                            // Handle validation errors
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const errors = xhr.responseJSON.errors;
                                let errorMessages = [];
                                for (let field in errors) {
                                    errorMessages.push(errors[field][0]);
                                }
                                toastr.error(errorMessages.join('<br>'));
                            } else {
                                toastr.error(errorMsg);
                            }
                            $('#btnSaveGrades').prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                        }
                    }
                });
            }

            // Save Grades button handler
            $('#btnSaveGrades').on('click', function() {
                saveGrades(true, false);
            });

            // Real-time input filtering and validation with auto-save
            $(document).on('input', '.grade-input', function() {
                const $input = $(this);
                
                // Skip if readonly (KS1, KS2, KS3)
                if ($input.prop('readonly')) {
                    return;
                }
                
                // Filter out invalid characters in real-time
                filterGradeInput(this);
                
                const grade = $input.val().trim();
                const keyStage = $input.data('key-stage');

                $input.removeClass('is-invalid is-valid');

                if (grade && isValidGrade(grade, keyStage)) {
                    $input.addClass('is-valid');
                } else if (grade) {
                    $input.addClass('is-invalid');
                }

                // Auto-save functionality - debounce to avoid too many requests
                // Clear existing timer
                if (autoSaveTimer) {
                    clearTimeout(autoSaveTimer);
                }

                // Only auto-save if grade is valid
                if (grade && isValidGrade(grade, keyStage)) {
                    // Set new timer for auto-save
                    autoSaveTimer = setTimeout(function() {
                        saveGrades(false, true); // Auto-save without toast notifications
                    }, AUTO_SAVE_DELAY);
                }
            });

            // Prevent paste of invalid characters
            $(document).on('paste', '.grade-input', function(e) {
                const $input = $(this);
                const originalValue = $input.val();
                
                // Get pasted data
                const pastedData = (e.originalEvent || e).clipboardData.getData('text');
                
                // Filter pasted data to allow only A-Z and 0-9
                const filtered = pastedData.replace(/[^A-Za-z0-9]/g, '');
                
                if (pastedData !== filtered) {
                    e.preventDefault();
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    const currentValue = $input.val();
                    const newValue = currentValue.substring(0, start) + filtered + currentValue.substring(end);
                    $input.val(newValue);
                    this.setSelectionRange(start + filtered.length, start + filtered.length);
                    $input.trigger('input');
                    toastr.warning('Only A-Z and 0-9 characters are allowed');
                }
            });

            // Prevent invalid key presses and limit to single character
            // Skip for KS1, KS2, KS3 (readonly fields)
            $(document).on('keypress', '.grade-input', function(e) {
                const $input = $(this);
                
                // Skip if readonly (KS1, KS2, KS3)
                if ($input.prop('readonly')) {
                    e.preventDefault();
                    return false;
                }
                
                const char = String.fromCharCode(e.which);
                const currentValue = $(this).val();
                
                // If input already has a character, prevent typing more
                if (currentValue.length >= 1) {
                    e.preventDefault();
                    toastr.warning('Only single digit (0-9) or single letter (A-Z) allowed', '', {timeOut: 2000});
                    return false;
                }
                
                // Allow only A-Z (uppercase/lowercase) and 0-9
                if (!/[A-Za-z0-9]/.test(char)) {
                    e.preventDefault();
                    toastr.warning('Only single digit (0-9) or single letter (A-Z) allowed', '', {timeOut: 2000});
                    return false;
                }
            });
        });
    </script>
@endsection
