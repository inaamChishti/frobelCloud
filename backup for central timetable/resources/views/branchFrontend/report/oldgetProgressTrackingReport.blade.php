@extends('layouts.branchDashboardApp')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3" style="max-width: 1000px; margin: auto;">
            <div class="card-header bg-primary text-white text-center py-2" style="background-color: #2048AC;">
                <h2 class="mb-0 fw-bold" style="font-size: 1.8rem;">Progress Tracking Report</h2>
            </div>
            <div class="card-body p-3">
                <!-- Filter Section -->
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label for="family_id" class="form-label fw-semibold">Select Family ID</label>
                        <select class="form-select select2" id="family_id" name="family_id" style="text-align: center !important;">
                            <option value="">Select Family ID</option>
                            @foreach ($guardianIds as $guardianId)
                                <option value="{{ $guardianId }}">{{ $guardianId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="student_id" class="form-label fw-semibold">Select Student</label>
                        <select class="form-select select2" id="student_id" name="student_id" disabled style="text-align: center !important;">
                            <option value="">Select a Family ID first</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label fw-semibold" style="font-size: 14px; color: #2048AC; font-weight: bold;">
                            From Date <span style="color: red;">(required)</span>
                        </label>
                        <input type="text" class="form-control" id="date_from" name="date_from" placeholder="dd/mm/yyyy" style="text-align: center;" required>
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label fw-semibold" style="font-size: 14px; color: #2048AC; font-weight: bold;">
                            To Date <span style="color: red;">(required)</span>
                        </label>
                        <input type="text" class="form-control" id="date_to" name="date_to" placeholder="dd/mm/yyyy" style="text-align: center;" required>
                    </div>
                </div>
                <!-- Email and Print Buttons -->
                <div class="mt-3 text-end">
                    <button id="email-report" class="btn btn-primary me-2" style="background-color: #2048AC; border-color: #2048AC;" disabled>Email Report</button>
                    <button id="print-report" class="btn btn-secondary" disabled>Print Report</button>
                </div>
                <!-- Report Container -->
                <div id="report-container" class="mt-3" style="min-height: 350px; border: 2px solid #2048AC; border-radius: 8px; padding: 15px; background-color: #fff;">
                    <div id="default-message" class="text-center text-muted">
                        <p>Please select a Family ID, Student, and Date Range to view the progress report.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS, Select2, SweetAlert2, and Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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
        .form-select, .form-control, .select2-container--default .select2-selection--single {
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
        .toast-info {
            background-color: #2048AC !important;
            opacity: 0.9;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #333;
            border: 1px solid #2048AC;
            border-radius: 5px;
            overflow: hidden;
        }
        .report-table th, .report-table td {
            border: 1px solid #2048AC;
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }
        .report-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .report-table td {
            background-color: #fff;
        }
        .report-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .report-table tbody tr:hover {
            background-color: #e6f0ff;
        }
        .flag-low {
            color: red;
            font-weight: bold;
        }
        .progress {
            height: 20px;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar-success {
            background-color: #28a745;
            transition: width 0.5s ease-in-out;
        }
        .progress-bar-danger {
            background-color: #dc3545;
            transition: width 0.5s ease-in-out;
        }
        .progress-section {
            position: relative;
        }
        .comment-section {
            margin-top: 20px;
        }
        .comment-section label {
            color: #2048AC;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .comment-section textarea {
            width: 100%;
            border: 1px solid #2048AC;
            border-radius: 5px;
            padding: 10px;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 100px;
        }
        .comment-section .comment-display {
            display: none;
        }
        @media print {
            @page {
                margin: 5mm;
                size: A4;
            }
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #fff;
                font-size: 10pt;
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
                position: static;
                width: 100%;
                max-width: 190mm;
                margin: 0;
                padding: 5mm;
                border: none;
                background: #fff;
                min-height: 0;
                box-sizing: border-box;
            }
            .report {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                background: #fff;
                page-break-inside: auto;
                width: 100%;
            }
            .logo-container {
                margin: 0 auto 3mm auto !important;
                padding: 3mm;
                background-color: #2048AC;
                border-radius: 4px;
                text-align: center;
                width: fit-content;
                display: block;
            }
            .logo-container img {
                max-width: 100px;
                width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            .report h1 {
                margin: 3mm 0;
                font-size: 1.4rem;
                text-align: center;
                color: #2048AC;
                display: block;
            }
            .report-info-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 9pt;
                margin: 3mm 0;
                page-break-inside: auto;
            }
            .report-info-table td {
                border: 1px solid #000;
                padding: 6px;
                font-size: 9pt;
                overflow-wrap: break-word;
            }
            .report-info-table .info-header {
                font-weight: bold;
                background: #e6f0ff;
                color: #2048AC;
                width: 30%;
                display: table-cell;
            }
            .report-info-table .info-content {
                background: #fff;
                display: table-cell;
            }
            .report-table {
                width: 100%;
                max-width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
                font-size: 9pt;
                margin: 3mm 0;
                page-break-inside: auto;
            }
            .report-table th,
            .report-table td {
                border: 1px solid #000;
                padding: 6px;
                font-size: 9pt;
                text-align: left;
                display: table-cell;
                overflow-wrap: break-word;
                word-break: break-all;
                width: 12.5%;
            }
            .report-table th {
                background: #2048AC;
                color: #fff;
            }
            .report-table tbody tr {
                page-break-inside: auto;
                page-break-after: auto;
            }
            .report-table tbody td {
                page-break-inside: auto;
            }
            .progress-section {
                display: none;
            }
            .flag-low {
                color: red;
                font-weight: bold;
                display: inline;
            }
            .comment-section {
                margin-top: 10px;
                page-break-inside: auto;
            }
            .comment-section label {
                font-weight: bold;
                color: #2048AC;
            }
            .comment-section textarea {
                display: none;
            }
            .comment-section .comment-display {
                display: block;
                border: 1px solid #000;
                padding: 10px;
                font-size: 9pt;
                background: #fff;
                border-radius: 4px;
                overflow-wrap: break-word;
            }
            * {
                box-sizing: border-box;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .container, .card, .card-body {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
                min-height: 0 !important;
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
            .report h1 {
                font-size: 1.6rem;
            }
            .report-table th,
            .report-table td {
                font-size: 0.8rem;
                padding: 8px;
            }
            .comment-section textarea {
                font-size: 0.8rem;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#family_id, #student_id').select2({
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                },
                allowClear: true
            });

            // Initialize Flatpickr
            flatpickr("#date_from", {
                dateFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today",
                locale: {
                    firstDayOfWeek: 1 // Set Monday as the first day of the week
                }
            });
            flatpickr("#date_to", {
                dateFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today",
                locale: {
                    firstDayOfWeek: 1
                }
            });

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
            }

            // Convert dd/mm/yyyy to yyyy-mm-dd for backend
            function formatDateForBackend(dateStr) {
                if (!dateStr) return '';
                const [day, month, year] = dateStr.split('/');
                return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }

            // Fetch students by Family ID
            $('#family_id').on('change', function () {
                var familyId = $(this).val();
                if (familyId) {
                    toastr.info('Fetching student data...');
                    $('#student_id').prop('disabled', false);
                    $.ajax({
                        url: '{{ route("getStudentsByFamilyId") }}',
                        type: 'GET',
                        data: { family_id: familyId },
                        dataType: 'json',
                        success: function (response) {
                            $('#student_id').empty().append('<option value="">Select Student</option>');
                            $.each(response, function (index, student) {
                                var fullName = student.studentname + ' ' + (student.studentsur || '');
                                $('#student_id').append(
                                    '<option value="' + fullName.trim() + '">' + fullName.trim() + '</option>'
                                );
                            });
                            toastr.success('Student data loaded successfully!');
                        },
                        error: function (xhr) {
                            toastr.error('Error fetching student data: ' + xhr.statusText);
                            $('#student_id').empty().append('<option value="">Error loading students</option>');
                            $('#student_id').prop('disabled', true);
                            $('#report-container').html($('#default-message').prop('outerHTML'));
                            toggleButtons(false);
                        }
                    });
                } else {
                    $('#student_id').prop('disabled', true).empty().append('<option value="">Select a Family ID first</option>');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                }
            });

            // Calculate overall progress score
            function calculateOverallProgress(data) {
                if (!data || data.length === 0) return 0;
                let totalProgress = data.reduce((sum, row) => sum + parseFloat(row.daily_progress), 0);
                return (totalProgress / data.length).toFixed(2);
            }

            // Update report on input change
            $('#student_id, #date_from, #date_to').on('change', function () {
                let familyId = $('#family_id').val();
                let studentName = $('#student_id').val();
                let dateFrom = $('#date_from').val();
                let dateTo = $('#date_to').val();

                if (!familyId || !studentName || !dateFrom || !dateTo) {
                    toastr.error('Please select Family ID, Student, From Date, and To Date.');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                    return;
                }

                // Validate date format (dd/mm/yyyy)
                const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
                if (!dateRegex.test(dateFrom) || !dateRegex.test(dateTo)) {
                    toastr.error('Please enter valid dates in dd/mm/yyyy format.');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                    return;
                }

                // Convert to backend format (yyyy-mm-dd)
                const formattedDateFrom = formatDateForBackend(dateFrom);
                const formattedDateTo = formatDateForBackend(dateTo);

                if (new Date(formattedDateFrom) > new Date(formattedDateTo)) {
                    toastr.error('From Date cannot be later than To Date.');
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
                    url: '{{ route("getProgressTrackingReportData") }}',
                    type: 'GET',
                    data: {
                        family_id: familyId,
                        student_name: studentName,
                        date_from: formattedDateFrom,
                        date_to: formattedDateTo
                    },
                    dataType: 'json',
                    success: function (response) {
                        Swal.close();
                        if (response && response.data.length > 0) {
                            let overallProgress = calculateOverallProgress(response.data);
                            let remainingProgress = (100 - overallProgress).toFixed(2);
                            let tableRows = '';
                            response.data.forEach(row => {
                                let flag = row.test_percentage && parseFloat(row.test_percentage) < 70 ? '<span class="flag-low">Below 70%</span>' : '';
                                tableRows += `
                                    <tr>
                                        <td>${row.date || '-'}</td>
                                        <td>${row.subjects || '-'}</td>
                                        <td>${row.test_percentage || '-'}</td>
                                        <td>${row.attendance_status || '-'}</td>
                                        <td>${row.behaviour || '-'}</td>
                                        <td>${row.performance || '-'}</td>
                                        <td>${row.daily_progress}%</td>
                                        <td>${flag}</td>
                                    </tr>`;
                            });
                            let html = `
<div class="report">
    <div class="logo-container" style="background-color: #2048AC; padding: 12px; border-radius: 6px; text-align: center; margin: 0 auto 15px auto; width: fit-content;">
        <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite%20(1).png" alt="Frobel Education Logo" style="max-width: 120px; width: 100%; height: auto; display: block;">
    </div>
    <h1 style="color: #2048AC; font-size: 1.8rem; text-align: center; margin-bottom: 15px;">Progress Tracking Report</h1>
    <table class="report-info-table" style="width:100%; border-collapse: collapse; margin-bottom:15px;">
        <tr>
            <td class="info-header" style="font-weight:bold; background:#AED6F1; padding:8px; border:1px solid #ddd; width:30%;">Family ID</td>
            <td class="info-content" style="padding:8px; background:#fff; border:1px solid #ddd;">${response.family_id || '-'}</td>
        </tr>
        <tr>
            <td class="info-header" style="font-weight:bold; background:#AED6F1; padding:8px; border:1px solid #ddd;">Student Name</td>
            <td class="info-content" style="padding:8px; background:#fff; border:1px solid #ddd;">${response.student_name || '-'}</td>
        </tr>
    </table>
    <div class="progress-section mb-3">
        <h4>Overall Progress</h4>
        <div class="progress mb-2" style="height: 20px;">
            <div class="progress-bar progress-bar-success" role="progressbar" style="width: ${overallProgress}%;" aria-valuenow="${overallProgress}" aria-valuemin="0" aria-valuemax="100"></div>
            <div class="progress-bar progress-bar-danger" role="progressbar" style="width: ${remainingProgress}%;" aria-valuenow="${remainingProgress}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p class="text-muted">Overall Progress Score: ${overallProgress}% (Remaining: ${remainingProgress}%)</p>
    </div>
    <table class="report-table" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Date</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Subjects</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Test Score (%)</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Attendance</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Behaviour</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Performance</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Daily Progress</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff;">Flag</th>
            </tr>
        </thead>
        <tbody>${tableRows}</tbody>
    </table>
    <div class="comment-section">
        <label for="teacher-comment">Teacher's Comments</label>
        <textarea id="teacher-comment" class="form-control" placeholder="Enter teacher's comments here"></textarea>
        <div class="comment-display" id="teacher-comment-display">No comments provided.</div>
    </div>
</div>`;
                            $('#report-container').html(html);
                            // Update comment-display when textarea changes
                            $('#teacher-comment').on('input', function() {
                                const comment = $(this).val() || 'No comments provided.';
                                $('#teacher-comment-display').text(comment);
                            });
                            const guardianEmail = response.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(response.email) ? response.email : '';
                            $('#email-report').data('guardian-email', guardianEmail);
                            toggleButtons(true);
                            toastr.success('Progress report loaded successfully!');
                        } else {
                            $('#report-container').html('<p class="text-warning">No data found for the selected student and date range.</p>');
                            $('#email-report').data('guardian-email', '');
                            toggleButtons(false);
                        }
                    },
                    error: function (xhr) {
                        Swal.close();
                        toastr.error('Error fetching report data: ' + xhr.statusText);
                        $('#report-container').html('<p class="text-danger">Error loading report data.</p>');
                        $('#email-report').data('guardian-email', '');
                        toggleButtons(false);
                    }
                });
            });

            // Email button handler
            $('#email-report').on('click', async function () {
                const guardianEmail = $(this).data('guardian-email');
                const { value: email } = await Swal.fire({
                    title: 'Enter Email Address',
                    input: 'email',
                    inputValue: guardianEmail || '',
                    inputPlaceholder: 'Enter recipient email',
                    showCancelButton: true,
                    confirmButtonText: 'Send',
                    confirmButtonColor: '#2048AC',
                    inputValidator: (value) => {
                        if (!value) return 'Please enter an email address';
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Please enter a valid email address';
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

                    // Clone report container and remove textarea
                    const $reportClone = $('#report-container').clone();
                    $reportClone.find('#teacher-comment').remove();
                    $reportClone.find('.comment-display').show();
                    const reportHtml = $reportClone.html();
                    const teacherComment = $('#teacher-comment').val() || 'No comments provided.';
                    $.ajax({
                        url: '{{ route("sendProgressReportEmail") }}',
                        type: 'POST',
                        data: {
                            email: email,
                            report_html: reportHtml,
                            teacher_comment: teacherComment,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function () {
                            Swal.close();
                            toastr.success('Email sent successfully!');
                        },
                        error: function (xhr) {
                            Swal.close();
                            toastr.error('Error sending email: ' + xhr.statusText);
                        }
                    });
                }
            });

            // Print button handler
            $('#print-report').on('click', function () {
                console.log('Printing report:', $('#report-container').html());
                window.print();
            });
        });
    </script>
@endsection
