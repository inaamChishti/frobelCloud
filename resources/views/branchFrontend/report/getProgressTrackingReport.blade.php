@extends('layouts.branchDashboardApp')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3" style="max-width: 1200px; margin: auto;">
            <div class="card-header bg-primary text-white text-center py-2" style="background-color: #2048AC;">
                <h2 class="mb-0 fw-bold" style="font-size: 1.8rem;">Progress Tracking Report</h2>
            </div>
            <div class="card-body p-3">
                <!-- Filter Section -->
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label for="family_id" class="form-label fw-semibold">Family ID</label>
                        <select class="form-select select2" id="family_id" name="family_id"
                            style="text-align: center !important;">
                            <option value="">Select Family ID</option>
                            @foreach ($guardianIds as $guardianId)
                                <option value="{{ $guardianId }}">{{ $guardianId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="student_id" class="form-label fw-semibold">Student Name</label>
                        <select class="form-select select2" id="student_id" name="student_id" disabled
                            style="text-align: center !important;">
                            <option value="">Select a Family ID first</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="subject" class="form-label fw-semibold">Subject</label>
                        <select class="form-select select2" id="subject" name="subject" disabled
                            style="text-align: center !important;">
                            <option value="">Select a Student first</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label fw-semibold">Month</label>
                        <select class="form-select select2" id="month" name="month" disabled
                            style="text-align: center !important;">
                            <option value="">Select a Student first</option>
                        </select>
                    </div>
                </div>
                <!-- Export Buttons -->
                <div class="mt-3 text-end">
                    <button id="pdf-export" class="btn btn-danger me-2" disabled>
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button id="email-report" class="btn btn-primary me-2"
                        style="background-color: #2048AC; border-color: #2048AC;" disabled>
                        <i class="fas fa-envelope"></i> Email Report
                    </button>
                    <button id="print-report" class="btn btn-secondary" disabled>
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
                <!-- Report Container -->
                <div id="report-container" class="mt-3"
                    style="min-height: 350px; border: 2px solid #2048AC; border-radius: 8px; padding: 15px; background-color: #fff;">
                    <div id="default-message" class="text-center text-muted">
                        <p>Please select Family ID, Student, Subject, and Month to view the progress report.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS, Select2, SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .form-select,
        .form-control,
        .select2-container--default .select2-selection--single {
            border: 1px solid #2048AC !important;
            border-radius: 5px;
            padding: 0.4rem;
            text-align: center !important;
            font-size: 0.9rem;
        }

        .select2-container--default .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #2048AC;
            font-weight: 500;
            text-align: center !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
            right: 8px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2048AC !important;
        }

        .form-label {
            color: #2048AC;
            font-size: 0.9rem;
        }

        /* Report Styling */
        .report-header {
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
            color: #2048AC;
            margin-bottom: 20px;
        }

        .report-info-section {
            margin-bottom: 20px;
        }

        .report-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .report-info-table td {
            border: 1px solid #2048AC;
            padding: 8px;
            font-size: 0.9rem;
        }

        .report-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 16.66%;
        }

        .report-info-table .info-content {
            background: #fff;
            width: 16.66%;
        }

        .subject-overview-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .subject-overview-table th,
        .subject-overview-table td {
            border: 1px solid #2048AC;
            padding: 10px;
            text-align: left;
        }

        .subject-overview-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .subject-overview-table td {
            background-color: #fff;
        }

        .subject-overview-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .performance-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .performance-title {
            color: #FF8C00;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .subject-name {
            color: #28a745;
            font-weight: bold;
        }

        .performance-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .performance-info-table td {
            border: 1px solid #2048AC;
            padding: 8px;
            font-size: 0.9rem;
        }

        .performance-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 30%;
        }

        .performance-info-table .info-content {
            background: #fff;
        }

        .test-records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .test-records-table th,
        .test-records-table td {
            border: 1px solid #2048AC;
            padding: 10px;
            text-align: left;
        }

        .test-records-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .test-records-table td {
            background-color: #fff;
        }

        .test-records-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .flag-pass {
            color: #28a745;
            font-weight: bold;
        }

        .flag-fail {
            color: #dc3545;
            font-weight: bold;
        }

        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
                font-size: 9pt;
                font-family: 'Arial', sans-serif;
            }

            body * {
                visibility: hidden;
            }

            #report-container,
            #report-container * {
                visibility: visible;
            }

            #report-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                max-width: 210mm;
                margin: 0 !important;
                padding: 5mm;
                border: none;
                background: #fff;
            }

            .container,
            .card,
            .card-body,
            .card-header {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none !important;
            }

            .report-header {
                font-size: 14pt;
                margin-bottom: 5mm;
            }

            .report-info-table,
            .subject-overview-table,
            .performance-info-table,
            .test-records-table {
                font-size: 7pt;
                margin-bottom: 3mm;
            }

            .report-info-table td,
            .subject-overview-table th,
            .subject-overview-table td,
            .performance-info-table td,
            .test-records-table th,
            .test-records-table td {
                padding: 2px;
                font-size: 7pt;
            }

            .report-info-table .info-header {
                width: 16.66%;
            }

            .report-info-table .info-content {
                width: 16.66%;
            }

            .performance-title {
                font-size: 10pt;
                color: #FF8C00;
            }

            .subject-name {
                color: #28a745;
            }

            .performance-section {
                page-break-inside: avoid;
                margin-bottom: 5mm;
            }
        }

        @media (max-width: 576px) {
            .card {
                margin: 0.8rem;
                max-width: 100%;
            }

            .card-header h2 {
                font-size: 1.4rem;
            }

            .report-header {
                font-size: 1.2rem;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#family_id, #student_id, #subject, #month').select2({
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                },
                allowClear: true
            });

            // Generate month options (excluding current month)
            // Example: If current month is January 2025, it will show December 2024, November 2024, etc.
            // JavaScript Date automatically handles year transitions when month goes negative
            function generateMonthOptions() {
                const months = [];
                const currentDate = new Date();
                const currentMonth = currentDate.getMonth(); // 0-11 (0 = January, 11 = December)
                const currentYear = currentDate.getFullYear();

                // Go back up to 12 months (excluding current month)
                // i=1 means 1 month ago, i=2 means 2 months ago, etc.
                for (let i = 1; i <= 12; i++) {
                    // When currentMonth - i is negative, JavaScript Date automatically adjusts to previous year
                    // Example: January (0) - 1 = -1, which becomes December of previous year
                    const date = new Date(currentYear, currentMonth - i, 1);
                    const monthName = date.toLocaleString('default', { month: 'long' });
                    const year = date.getFullYear();
                    const value = `${year}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                    months.push({ value: value, text: `${monthName} ${year}` });
                }

                return months;
            }

            // Initialize Toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000
            };

            // Toggle buttons
            function toggleButtons(hasData) {
                $('#email-report').prop('disabled', !hasData);
                $('#print-report').prop('disabled', !hasData);
                $('#pdf-export').prop('disabled', !hasData);
            }

            // Fetch students by Family ID
            $('#family_id').on('change', function() {
                var familyId = $(this).val();
                if (familyId) {
                    toastr.info('Fetching student data...');
                    $('#student_id').prop('disabled', false);
                    $.ajax({
                        url: '{{ route('getStudentsByFamilyId') }}',
                        type: 'GET',
                        data: {
                            family_id: familyId
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#student_id').empty().append(
                                '<option value="">Select Student</option>');
                            $.each(response, function(index, student) {
                                var fullName = student.studentname + ' ' + (student
                                    .studentsur || '');
                                $('#student_id').append(
                                    '<option value="' + fullName.trim() + '">' +
                                    fullName.trim() + '</option>'
                                );
                            });
                            $('#student_id').trigger('change.select2');
                            toastr.success('Student data loaded successfully!');
                            $('#subject').prop('disabled', true).empty().append(
                                '<option value="">Select a Student first</option>').trigger(
                                'change.select2');
                            $('#month').prop('disabled', true).empty().append(
                                '<option value="">Select a Student first</option>').trigger(
                                'change.select2');
                        },
                        error: function(xhr) {
                            if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.blocked) {
                                toastr.error('Family ID Blocked. Please contact admin office for more details.');
                                $('#family_id').val(null).trigger('change');
                                $('#student_id').prop('disabled', true).empty().append('<option value="">Select a Family ID first</option>').trigger('change.select2');
                                $('#subject').prop('disabled', true).empty().append('<option value="">Select a Student first</option>').trigger('change.select2');
                                $('#month').prop('disabled', true).empty().append('<option value="">Select a Student first</option>').trigger('change.select2');
                                $('#report-container').html($('#default-message').prop('outerHTML'));
                                toggleButtons(false);
                                return;
                            }
                            console.error('Error fetching students:', xhr.responseText);
                            toastr.error('Error fetching student data: ' + xhr.statusText);
                            $('#student_id').empty().append(
                                '<option value="">Error loading students</option>');
                            $('#student_id').prop('disabled', true);
                            $('#subject').prop('disabled', true).empty().append(
                                '<option value="">Select a Student first</option>').trigger(
                                'change.select2');
                            $('#month').prop('disabled', true).empty().append(
                                '<option value="">Select a Student first</option>').trigger(
                                'change.select2');
                            $('#report-container').html($('#default-message').prop('outerHTML'));
                            toggleButtons(false);
                        }
                    });
                } else {
                    $('#student_id').prop('disabled', true).empty().append(
                        '<option value="">Select a Family ID first</option>').trigger('change.select2');
                    $('#subject').prop('disabled', true).empty().append(
                        '<option value="">Select a Student first</option>').trigger('change.select2');
                    $('#month').prop('disabled', true).empty().append(
                        '<option value="">Select a Student first</option>').trigger('change.select2');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                }
            });

            // Fetch subjects by Student and populate month dropdown
            $('#student_id').on('change', function() {
                var familyId = $('#family_id').val();
                var studentName = $(this).val();
                if (studentName) {
                    toastr.info('Fetching subject data...');
                    $.ajax({
                        url: '{{ route('getSubjectsByStudent') }}',
                        type: 'GET',
                        data: {
                            family_id: familyId,
                            student_name: studentName
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#subject').empty().append(
                                '<option value="">Select Subject</option>');
                            $('#subject').append('<option value="all">All Subjects</option>');
                            var subjects = Array.isArray(response) ? response : (response
                                .subjects || []);
                            if (subjects.length > 0) {
                                $.each(subjects, function(index, subject) {
                                    $('#subject').append(
                                        `<option value="${subject}">${subject}</option>`
                                    );
                                });
                                $('#subject').prop('disabled', false).trigger('change.select2');
                                toastr.success('Subject data loaded successfully!');
                            } else {
                                $('#subject').empty().append(
                                    '<option value="">No subjects available</option>').prop(
                                    'disabled', true).trigger('change.select2');
                                toastr.warning('No subjects available for this student.');
                            }

                            // Populate month dropdown
                            const months = generateMonthOptions();
                            $('#month').empty().append('<option value="">Select Month</option>');
                            $.each(months, function(index, month) {
                                $('#month').append(
                                    `<option value="${month.value}">${month.text}</option>`
                                );
                            });
                            $('#month').prop('disabled', false).trigger('change.select2');
                        },
                        error: function(xhr) {
                            console.error('Error fetching subjects:', xhr.responseText);
                            toastr.error('Error fetching subject data: ' + xhr.statusText);
                            $('#subject').empty().append(
                                '<option value="">Error loading subjects</option>').prop(
                                'disabled', true).trigger('change.select2');
                            $('#month').prop('disabled', true).empty().append(
                                '<option value="">Select a Student first</option>').trigger(
                                'change.select2');
                            $('#report-container').html($('#default-message').prop('outerHTML'));
                            toggleButtons(false);
                        }
                    });
                } else {
                    $('#subject').prop('disabled', true).empty().append(
                        '<option value="">Select a Student first</option>').trigger('change.select2');
                    $('#month').prop('disabled', true).empty().append(
                        '<option value="">Select a Student first</option>').trigger('change.select2');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                }
            });

            // Update report on filter change
            $('#student_id, #subject, #month').on('change', function() {
                let familyId = $('#family_id').val();
                let studentName = $('#student_id').val();
                let subject = $('#subject').val();
                let month = $('#month').val();

                if (!familyId || !studentName || !subject || !month) {
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                    return;
                }

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Fetching progress report data',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('getProgressTrackingReportData') }}',
                    type: 'GET',
                    data: {
                        family_id: familyId,
                        student_name: studentName,
                        subject: subject,
                        month: month
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Report response:', response);
                        Swal.close();
                        if (response && response.data) {
                            renderReport(response);
                            const guardianEmail = response.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/
                                .test(response.email) ? response.email : '';
                            $('#email-report').data('guardian-email', guardianEmail);
                            $('#email-report').data('report-data', response);
                            toggleButtons(true);
                            toastr.success('Progress report loaded successfully!');
                        } else {
                            $('#report-container').html(
                                '<p class="text-warning" style="margin: 0; padding: 5px;">No data found for the selected criteria.</p>'
                            );
                            $('#email-report').data('guardian-email', '');
                            toggleButtons(false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching report:', xhr.responseText);
                        Swal.close();
                        
                        let errorMessage = 'Error fetching report data.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                            // Show specific error for current month validation
                            if (errorMessage.includes('Current month cannot be selected')) {
                                toastr.error('Current month cannot be selected. Please select a previous month.');
                            } else {
                                toastr.error(errorMessage);
                            }
                        } else {
                            toastr.error('Error fetching report data: ' + xhr.statusText);
                        }
                        
                        $('#report-container').html(
                            '<p class="text-danger" style="margin: 0; padding: 5px;">' + errorMessage + '</p>'
                        );
                        $('#email-report').data('guardian-email', '');
                        toggleButtons(false);
                    }
                });
            });

            // Render report HTML
            function renderReport(response) {
                let html = '<div class="report">';
                html += '<div class="report-header">Progress Tracking Report</div>';

                // Header Info - Single Line Table
                html += '<div class="report-info-section">';
                html += '<table class="report-info-table">';
                html += '<tr>';
                html += '<td class="info-header">Family ID</td><td class="info-content">' + (response.family_id || '-') + '</td>';
                html += '<td class="info-header">Full Name</td><td class="info-content">' + (response.student_name || '-') + '</td>';
                html += '<td class="info-header">School Year</td><td class="info-content">' + (response.school_year || '-') + '</td>';
                html += '</tr>';
                html += '</table>';
                html += '</div>';

                // Subject Overview Table
                if (response.data.subject_overview && response.data.subject_overview.length > 0) {
                    html += '<table class="subject-overview-table">';
                    html += '<thead><tr>';
                    html += '<th>Subject Name</th>';
                    html += '<th>Start Date</th>';
                    html += '<th>Tier</th>';
                    html += '<th>Session Booked</th>';
                    html += '<th>Session Attended</th>';
                    html += '<th>Attendance</th>';
                    html += '<th>Home Work</th>';
                    html += '<th>Behaviour</th>';
                    html += '<th>Performance</th>';
                    html += '</tr></thead><tbody>';

                    response.data.subject_overview.forEach(function(subject) {
                        html += '<tr>';
                        html += '<td>' + (subject.subject_name || '-') + '</td>';
                        html += '<td>' + (subject.start_date || '-') + '</td>';
                        html += '<td>' + (subject.tier || '-') + '</td>';
                        html += '<td>' + (subject.sessions_booked || '-') + '</td>';
                        html += '<td>' + (subject.sessions_attended || '-') + '</td>';
                        html += '<td>' + (subject.attendance || '-') + '</td>';
                        html += '<td>' + (subject.homework || '-') + '</td>';
                        html += '<td>' + (subject.behaviour || '-') + '</td>';
                        html += '<td>' + (subject.performance || '-') + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody></table>';
                }

                // Performance Sections (one per subject)
                if (response.data.performance_sections && response.data.performance_sections.length > 0) {
                    response.data.performance_sections.forEach(function(section) {
                        html += '<div class="performance-section">';
                        html += '<div class="performance-title">Performance</div>';

                        // Subject Info
                        html += '<table class="performance-info-table">';
                        html += '<tr><td class="info-header">Subject Name</td><td class="info-content"><span class="subject-name">' + (section.subject_name || '-') + '</span></td></tr>';
                        html += '<tr><td class="info-header">Current Grade</td><td class="info-content">' + (section.current_grade || '-') + '</td></tr>';
                        html += '<tr><td class="info-header">Target Grade</td><td class="info-content">' + (section.target_grade || '-') + '</td></tr>';
                        html += '</table>';

                        // Test Records Table
                        html += '<table class="test-records-table">';
                        html += '<thead><tr>';
                        html += '<th>Test Date</th>';
                        html += '<th>Book Name</th>';
                        html += '<th>Test No</th>';
                        html += '<th>Test Score</th>';
                        html += '<th>Flag</th>';
                        html += '</tr></thead><tbody>';

                        if (section.test_records && section.test_records.length > 0) {
                            section.test_records.forEach(function(test) {
                                const flag = parseFloat(test.test_score) >= 70 ? 
                                    '<span class="flag-pass">Pass</span>' : 
                                    '<span class="flag-fail">Fail</span>';
                                html += '<tr>';
                                html += '<td>' + (test.test_date || '-') + '</td>';
                                html += '<td>' + (test.book_name || '-') + '</td>';
                                html += '<td>' + (test.test_no || '-') + '</td>';
                                html += '<td>' + (test.test_score || '-') + '</td>';
                                html += '<td>' + flag + '</td>';
                                html += '</tr>';
                            });
                        } else {
                            html += '<tr><td colspan="5" style="text-align: center;">No test records available</td></tr>';
                        }

                        html += '</tbody></table>';
                        html += '</div>';
                    });
                }

                html += '</div>';
                $('#report-container').html(html);
            }

            // Email button handler
            $('#email-report').on('click', async function() {
                const guardianEmail = $(this).data('guardian-email');
                const {
                    value: email
                } = await Swal.fire({
                    title: 'Enter Email Address',
                    input: 'email',
                    inputValue: guardianEmail || '',
                    inputPlaceholder: 'Enter recipient email',
                    showCancelButton: true,
                    confirmButtonText: 'Send',
                    confirmButtonColor: '#2048AC',
                    inputValidator: (value) => {
                        if (!value) return 'Please enter an email address';
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value))
                            return 'Please enter a valid email address';
                    }
                });

                if (email) {
                    Swal.fire({
                        title: 'Please wait...',
                        text: 'Sending email...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const reportHtml = $('#report-container').html();
                    const reportData = $(this).data('report-data');
                    $.ajax({
                        url: '{{ route('sendProgressReportEmail') }}',
                        type: 'POST',
                        data: {
                            email: email,
                            report_html: reportHtml,
                            report_data: JSON.stringify(reportData),
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            Swal.close();
                            toastr.success('Email sent successfully!');
                        },
                        error: function(xhr) {
                            Swal.close();
                            toastr.error('Error sending email: ' + xhr.statusText);
                        }
                    });
                }
            });

            // PDF Export button handler
            $('#pdf-export').on('click', function() {
                const familyId = $('#family_id').val();
                const studentName = $('#student_id').val();
                const subject = $('#subject').val();
                const month = $('#month').val();

                if (!familyId || !studentName || !subject || !month) {
                    toastr.error('Please select all required filters.');
                    return;
                }

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Generating PDF...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create a form and submit it to download PDF
                const form = $('<form>', {
                    method: 'GET',
                    action: '{{ route('exportProgressReportPDF') }}',
                    target: '_blank'
                });

                form.append($('<input>', {
                    type: 'hidden',
                    name: 'family_id',
                    value: familyId
                }));

                form.append($('<input>', {
                    type: 'hidden',
                    name: 'student_name',
                    value: studentName
                }));

                form.append($('<input>', {
                    type: 'hidden',
                    name: 'subject',
                    value: subject
                }));

                form.append($('<input>', {
                    type: 'hidden',
                    name: 'month',
                    value: month
                }));

                $('body').append(form);
                form.submit();
                form.remove();

                setTimeout(() => {
                    Swal.close();
                    toastr.success('PDF generated successfully!');
                }, 1000);
            });

            // Print button handler
            $('#print-report').on('click', function() {
                // Create a new window with only report content
                const reportContent = $('#report-container').html();
                const printWindow = window.open('', '_blank');
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Progress Tracking Report</title>
                        <style>
                            @page {
                                margin: 1cm;
                                size: A4;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                                background: #fff;
                                font-family: Arial, sans-serif;
                                font-size: 9pt;
                            }
                            .report {
                                width: 100%;
                                margin: 0;
                                padding: 0;
                            }
                            .report-header {
                                text-align: center;
                                font-weight: bold;
                                font-size: 14pt;
                                color: #2048AC;
                                margin-bottom: 5mm;
                            }
                            .report-info-table,
                            .subject-overview-table,
                            .performance-info-table,
                            .test-records-table {
                                width: 100%;
                                border-collapse: collapse;
                                font-size: 7pt;
                                margin-bottom: 3mm;
                                page-break-inside: auto;
                            }
                            .report-info-table td,
                            .subject-overview-table th,
                            .subject-overview-table td,
                            .performance-info-table td,
                            .test-records-table th,
                            .test-records-table td {
                                border: 1px solid #2048AC;
                                padding: 2px;
                                font-size: 7pt;
                            }
                            .report-info-table .info-header,
                            .performance-info-table .info-header {
                                font-weight: bold;
                                background: #AED6F1;
                                color: #2048AC;
                            }
                            .report-info-table .info-header {
                                width: 16.66%;
                            }
                            .report-info-table .info-content {
                                width: 16.66%;
                            }
                            .subject-overview-table th {
                                background-color: #e6f0ff;
                                color: #2048AC;
                                font-weight: 600;
                            }
                            .test-records-table th {
                                background-color: #e6f0ff;
                                color: #2048AC;
                                font-weight: 600;
                            }
                            .performance-title {
                                color: #FF8C00;
                                font-weight: bold;
                                font-size: 10pt;
                                margin-bottom: 3mm;
                            }
                            .subject-name {
                                color: #28a745;
                                font-weight: bold;
                            }
                            .performance-section {
                                margin-bottom: 5mm;
                                page-break-inside: auto;
                            }
                            .flag-pass {
                                color: #28a745;
                                font-weight: bold;
                            }
                            .flag-fail {
                                color: #dc3545;
                                font-weight: bold;
                            }
                        </style>
                    </head>
                    <body>
                        ${reportContent}
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
                printWindow.focus();
                
                // Wait for content to load then print
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 250);
            });
        });
    </script>
@endsection
