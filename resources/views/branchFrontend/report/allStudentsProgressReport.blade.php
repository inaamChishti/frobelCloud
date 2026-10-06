@extends('layouts.branchDashboardApp')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3" style="max-width: 1000px; margin: auto;">
            <div class="card-header bg-primary text-white text-center py-2" style="background-color: #2048AC;">
                <h2 class="mb-0 fw-bold" style="font-size: 1.6rem;">All Students Progress Report</h2>
            </div>
            <div class="card-body p-3">
                <!-- Filter Section -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label for="subject" class="form-label fw-semibold">Select Subject</label>
                        <select class="form-select select2" id="subject" name="subject" style="text-align: center !important;">
                            <option value="">Select Subject</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_from" class="form-label fw-semibold" style="font-size: 14px; color: #2048AC; font-weight: bold;">
                            From Date <span style="color: red;">(required)</span>
                        </label>
                        <input type="text" autocomplete="off" inputmode="none" onfocus="this.showPicker?.()" class="form-control" id="date_from" name="date_from" placeholder="dd/mm/yyyy" style="text-align: center;" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date_to" class="form-label fw-semibold" style="font-size: 14px; color: #2048AC; font-weight: bold;">
                            To Date <span style="color: red;">(required)</span>
                        </label>
                        <input type="text" autocomplete="off" inputmode="none" onfocus="this.showPicker?.()" class="form-control" id="date_to" name="date_to" placeholder="dd/mm/yyyy" style="text-align: center;" required>
                    </div>
                </div>

                <!-- Print Button -->
                <div class="mt-3 text-end no-print">
                    <button id="print-report" class="btn btn-secondary" disabled>
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                </div>

                <!-- Report Container -->
                <div id="report-container" class="mt-3" style="min-height: 350px; border: 2px solid #2048AC; border-radius: 8px; padding: 15px; background-color: #fff;">
                    <div id="default-message" class="text-center text-muted">
                        <p>Please select a Subject and Date Range to view the progress report.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 8px;
            overflow: visible;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .form-select, .form-control {
            border: 1px solid #2048AC !important;
            border-radius: 5px;
            padding: 0.4rem;
            text-align: center !important;
            font-size: 0.9rem;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.8rem;
            color: #333;
            border: 1px solid #2048AC;
            table-layout: fixed;
        }
        .report-table th, .report-table td {
            border: 1px solid #2048AC;
            padding: 6px;
            text-align: left;
            vertical-align: middle;
            word-break: break-word;
            font-size: 0.75rem;
        }
        .report-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
            /* Removed text-transform: uppercase to make headers inline */
        }
        .flag-low {
            color: red;
            font-weight: bold;
        }
        .report-logo {
            max-width: 150px;
            margin: 0 auto 10px auto;
            display: block;
        }

        /* PRINT CSS FIX - Only logo and table */
        @media print {
            /* Hide global layout elements */
            .header,
            .navbar,
            nav,
            .sidebar,
            .footer,
            #sidebar,
            .app-header,
            .main-header {
                display: none !important;
            }

            /* Hide toastr notifications */
            .toast,
            .toastr,
            #toast-container,
            .toast-container,
            .toast-top-right,
            .toast-bottom-right,
            .toast-top-left,
            .toast-bottom-left,
            .toast-top-center,
            .toast-bottom-center,
            .toast-top-full-width,
            .toast-bottom-full-width,
            .toast-container .toast {
                display: none !important;
                visibility: hidden !important;
            }

            /* Hide page-specific elements */
            body, .container, .card, .card-body, #report-container {
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                background: #fff !important;
                width: 100% !important;
                height: auto !important;
            }
            .no-print, .row.g-2, .card-header, #default-message,
            button, input, select, label, .form-label, .btn {
                display: none !important;
            }

            /* Ensure only the logo and table are visible */
            #report-container > * {
                display: none !important;
            }
            #report-container > .report {
                display: block !important;
            }
            #report-container > .report > .report-logo,
            #report-container > .report > .report-table {
                display: block !important;
            }
            .report-logo {
                max-width: 100px !important;
                margin: 0 auto 5mm !important;
            }
            .report-table {
                font-size: 8pt !important;
                border: 1px solid #000 !important;
                width: 100% !important;
                margin: 0 !important;
                page-break-inside: auto !important;
            }
            .report-table th {
                background: #2048AC !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                border: 1px solid #000 !important;
                font-weight: normal !important; /* Inline text style */
                text-transform: none !important; /* No uppercase */
                font-size: 8pt !important; /* Match body text */
            }
            .report-table td {
                border: 1px solid #000 !important;
                font-size: 8pt !important;
            }
            .report-table tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }
            /* Ensure print colors are accurate */
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            html, body {
                height: auto !important;
                min-height: unset !important;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        $(document).ready(function () {
            // Toastr configuration
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000,
                preventDuplicates: true
            };

            // Select2 initialization
            $('#subject').select2({
                width: '100%',
                placeholder: "Select Subject",
                allowClear: true
            });

            // Flatpickr initialization
            flatpickr("#date_from", { dateFormat: "d/m/Y", allowInput: true, maxDate: "today" });
            flatpickr("#date_to", { dateFormat: "d/m/Y", allowInput: true, maxDate: "today" });

            // Toggle print button based on data availability
            function toggleButtons(hasData) {
                $('#print-report').prop('disabled', !hasData);
            }

            // Format date for backend (dd/mm/yyyy to yyyy-mm-dd)
            function formatDateForBackend(dateStr) {
                if (!dateStr) return '';
                const [day, month, year] = dateStr.split('/');
                return `${year}-${month}-${day}`;
            }

            // Show loading indicator
            function showLoading() {
                $('#report-container').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading report...</p>
                    </div>
                `);
                toggleButtons(false);
            }

            // Render table with logo
            function renderTable(data) {
                let rows = '';
                data.forEach(row => {
                    let score = row.test_score && parseFloat(row.test_score) < 70
                        ? `<span class="flag-low">${row.test_score}%</span>`
                        : `${row.test_score || '-'}`;
                    rows += `
                        <tr>
                            <td>${row.family_id || '-'}</td>
                            <td>${row.student_name || '-'}</td>
                            <td>${row.school_year || '-'}</td>
                            <td>${row.start_date || '-'}</td>
                            <td>${row.subject || '-'}</td>
                            <td>${row.tier || 'N/A'}</td>
                            <td>${row.sessions_booked || '-'}</td>
                            <td>${row.sessions_attended || '-'}</td>
                            <td>${row.target_grade || '-'}</td>
                            <td>${row.current_grade || '-'}</td>
                            <td>${row.behavior || '-'}</td>
                            <td>${row.performance || '-'}</td>
                            <td>${score}</td>
                        </tr>`;
                });

                const html = `
                    <div class="report">
                        <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite%20(1).png" alt="Frobel Education Logo" style="background-color:#655DCC;" class="report-logo">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Family ID</th>
                                    <th>Student Name</th>
                                    <th>School Year</th>
                                    <th>Start Date</th>
                                    <th>Subject</th>
                                    <th>Tier</th>
                                    <th>Booked</th>
                                    <th>Attended</th>
                                    <th>Target Grade</th>
                                    <th>Current Grade</th>
                                    <th>Behavior</th>
                                    <th>Performance</th>
                                    <th>Score (%)</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>`;
                $('#report-container').html(html);
                toggleButtons(data.length > 0);
                toastr.success("Report loaded successfully!");
            }

            // Fetch subjects
            function fetchSubjects() {
                $.ajax({
                    url: '{{ route("getAllSubjects") }}',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function () {
                        toastr.info("Loading subjects...");
                    },
                    success: function (res) {
                        $('#subject').empty().append('<option value="">Select Subject</option>');
                        if (res && res.length > 0) {
                            $.each(res, function (i, sub) {
                                $('#subject').append(`<option value="${sub}">${sub}</option>`);
                            });
                            toastr.success("Subjects loaded successfully!");
                        } else {
                            toastr.warning("No subjects available.");
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Error loading subjects.";
                        if (xhr.status === 404) {
                            errorMsg = "Subjects endpoint not found.";
                        } else if (xhr.status === 500) {
                            errorMsg = "Server error while loading subjects.";
                        }
                        toastr.error(errorMsg);
                    }
                });
            }

            // Fetch report data
            function fetchReportData(subject, from, to) {
                $.ajax({
                    url: '{{ route("getAllStudentsProgressReportData") }}',
                    type: 'GET',
                    data: { subject, date_from: from, date_to: to },
                    dataType: 'json',
                    beforeSend: function () {
                        showLoading();
                        toastr.info("Fetching report data...");
                    },
                    success: function (res) {
                        if (res && res.data && res.data.length > 0) {
                            renderTable(res.data);
                        } else {
                            $('#report-container').html('<p class="text-warning">No data found for the selected criteria.</p>');
                            toggleButtons(false);
                            toastr.info("No records found.");
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = "Error loading report data.";
                        if (xhr.status === 400) {
                            errorMsg = "Invalid request parameters.";
                        } else if (xhr.status === 404) {
                            errorMsg = "Report data endpoint not found.";
                        } else if (xhr.status === 500) {
                            errorMsg = "Server error while fetching report.";
                        }
                        $('#report-container').html(`<p class="text-danger">${errorMsg}</p>`);
                        toggleButtons(false);
                        toastr.error(errorMsg);
                    }
                });
            }

            // Initialize subjects on page load
            fetchSubjects();

            // Input change event
            $('#subject, #date_from, #date_to').on('change', function () {
                let subject = $('#subject').val(),
                    d1 = $('#date_from').val(),
                    d2 = $('#date_to').val();

                if (!subject || !d1 || !d2) {
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                    toastr.warning("Please select a Subject and Date Range.");
                    return;
                }

                // Validate date format (dd/mm/yyyy)
                const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;
                if (!dateRegex.test(d1) || !dateRegex.test(d2)) {
                    $('#report-container').html('<p class="text-danger">Invalid date format. Use dd/mm/yyyy.</p>');
                    toggleButtons(false);
                    toastr.error("Invalid date format. Please use dd/mm/yyyy.");
                    return;
                }

                const from = formatDateForBackend(d1);
                const to = formatDateForBackend(d2);

                // Validate date range
                const dateFrom = new Date(from);
                const dateTo = new Date(to);
                if (dateFrom > dateTo) {
                    $('#report-container').html('<p class="text-danger">From date cannot be later than To date.</p>');
                    toggleButtons(false);
                    toastr.error("From date cannot be later than To date.");
                    return;
                }

                fetchReportData(subject, from, to);
            });

            // Print report
            $('#print-report').on('click', function () {
                // Suppress toastr during print
                toastr.clear();
                setTimeout(() => { window.print(); }, 500);
            });
        });
    </script>
@endsection
