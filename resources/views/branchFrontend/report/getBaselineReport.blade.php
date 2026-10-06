```html
@extends('layouts.branchDashboardApp')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-3" style="max-width: 800px; margin: auto;">
            <div class="card-header bg-primary text-white text-center py-2" style="background-color: #2048AC;">
                <h2 class="mb-0 fw-bold" style="font-size: 1.8rem;">Baseline Report</h2>
            </div>
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="family_id" class="form-label fw-semibold">Select Family ID</label>
                        <select class="form-select select2" id="family_id" name="family_id" style="text-align: center !important;">
                            <option value="">Select Family ID</option>
                            @foreach ($guardianIds as $guardianId)
                                <option value="{{ $guardianId }}">{{ $guardianId }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="student_id" class="form-label fw-semibold">Select Student</label>
                        <select class="form-select select2" id="student_id" name="student_id" disabled style="text-align: center !important;">
                            <option value="">Select a Family ID first</option>
                        </select>
                    </div>
                </div>
                <!-- Email and Print Buttons -->
                <div class="mt-3 text-end">
                    <button id="email-report" class="btn btn-primary me-2" style="background-color: #2048AC; border-color: #2048AC;" disabled>Email Report</button>
                    <button id="print-report" class="btn btn-secondary" disabled>Print Report</button>
                </div>
                <!-- Container to display the report -->
                <div id="report-container" class="mt-3" style="min-height: 350px; border: 2px solid #2048AC; border-radius: 8px; padding: 15px; background-color: #fff;">
                    <!-- Default message when no data is loaded -->
                    <div id="default-message" class="text-center text-muted">
                        <p>Please select a Family ID and Student to view the baseline report.</p>
                    </div>
                    <!-- Report template (hidden initially, populated via AJAX) -->
                    <div id="report-template" style="display: none;">
                        <div class="report" style="position: relative; padding: 20px;">
                            <!-- Logo centered at top -->
                            <div class="logo-container" style="background-color: #2048AC; padding: 12px; border-radius: 6px; text-align: center; margin: 0 auto 15px auto; width: fit-content;">
                                <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite%20(1).png" alt="Frobel Education Logo" style="max-width: 120px; width: 100%; height: auto; display: block;">
                            </div>
                            <!-- Report Title -->
                            <h1 style="color: #2048AC; font-size: 1.8rem; text-align: center; margin-bottom: 15px;">Baseline Report</h1>
                            <!-- Family ID and Student Name in Grid -->
                            <div class="report-info-grid">
                                <div class="grid-header">Family ID</div>
                                <div class="grid-content" id="family-id">[Family ID]</div>
                                <div class="grid-header">Student Name</div>
                                <div class="grid-content" id="student-name">[Student Name]</div>
                            </div>
                            <!-- Table for Subjects, Grades, and Hours -->
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Subjects Studied</th>
                                        <th>Target Grades</th>
                                        <th>Hours of Learning</th>
                                    </tr>
                                </thead>
                                <tbody id="report-data">
                                    <tr>
                                        <td id="subject-names">[Subjects]</td>
                                        <td id="target-grades">[Grades]</td>
                                        <td id="student-hours">[Hours]</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery, Bootstrap, Toastr.js, Select2, and SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS for styling -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 8px;
            overflow: hidden;
        }
        .form-select, .select2-container--default .select2-selection--single {
            border-color: #2048AC !important;
            border-radius: 5px;
            padding: 0.4rem;
            text-align: center !important;
            font-size: 0.9rem;
        }
        .select2-container--default .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
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
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #2048AC;
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
        #report-container {
            border: 2px solid #2048AC;
            border-radius: 8px;
            padding: 15px;
            background-color: #fff;
            position: relative;
        }
        .report {
            font-family: 'Arial', sans-serif;
            position: relative;
            padding: 20px;
            background: #fff;
            border: 1px solid #2048AC;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .logo-container {
            background-color: #2048AC;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin: 0 auto 15px auto;
            width: fit-content;
            overflow: hidden;
        }
        .report img {
            max-width: 120px;
            width: 100%;
            height: auto;
            display: block;
            filter: none;
            opacity: 1;
        }
        .report h1 {
            font-family: 'Times New Roman', Times, serif;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        .report-info-grid {
            display: grid;
            grid-template-columns: 1fr 3fr;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 0.95rem;
            color: #333;
        }
        .grid-header {
            font-weight: 600;
            color: #2048AC;
            padding: 10px;
            background-color: #e6f0ff;
            border: 1px solid #2048AC;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }
        .grid-content {
            padding: 10px;
            border: 1px solid #2048AC;
            border-radius: 4px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }
        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
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
            letter-spacing: 0.05em;
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
        /* Print-specific styles */
        @media print {
            @page {
                margin: 5mm; /* Reduced margin for maximum content */
                size: A4;
            }
            body {
                margin: 0;
                padding: 0;
                background: #fff;
                font-size: 12pt;
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
                top: 0;
                left: 0;
                width: 100%;
                margin: 0;
                padding: 5mm;
                border: none;
                background: #fff;
                min-height: auto;
            }
            .report {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                background: #fff;
                page-break-inside: auto;
            }
            .logo-container {
                margin: 0 auto 5mm auto !important;
                padding: 5mm;
                background-color: #2048AC;
                border-radius: 6px;
                text-align: center;
                width: fit-content;
                display: block;
            }
            .logo-container img {
                max-width: 120px;
                width: 100%;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            .report h1 {
                margin: 5mm 0;
                font-size: 1.6rem;
                text-align: center;
                color: #2048AC;
            }
            .report-info-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                margin: 5mm 0;
                page-break-inside: auto;
            }
            .report-info-table td {
                border: 1px solid #000;
                padding: 8px;
                font-size: 10pt;
            }
            .report-info-table .grid-header {
                font-weight: bold;
                background: #e6f0ff;
                color: #2048AC;
                width: 30%;
            }
            .report-info-table .grid-content {
                background: #fff;
            }
            .report-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                margin: 5mm 0;
                page-break-inside: auto;
            }
            .report-table th,
            .report-table td {
                border: 1px solid #000;
                padding: 8px;
                font-size: 10pt;
                text-align: left;
            }
            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            * {
                box-sizing: border-box;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
            .report-info-grid {
                grid-template-columns: 1fr;
                font-size: 0.85rem;
            }
            .report img {
                max-width: 100px;
            }
            .report-table th, .report-table td {
                font-size: 0.8rem;
                padding: 8px;
            }
        }
    </style>

    <!-- JavaScript for Select2, AJAX, Email, and Print -->
    <script>
        $(document).ready(function () {
            // Initialize Select2 for both dropdowns
            $('#family_id, #student_id').select2({
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                },
                allowClear: true
            });

            // Initialize Toastr options
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000
            };

            // Enable/disable buttons based on report data
            function toggleButtons(hasData) {
                $('#email-report').prop('disabled', !hasData);
                $('#print-report').prop('disabled', !hasData);
            }

            // On change of family_id dropdown
            $('#family_id').on('change', function () {
                var guardianId = $(this).val();
                console.log('Family ID selected:', guardianId); // Debug
                if (guardianId) {
                    toastr.info('Please wait, getting student data...');
                    $('#student_id').prop('disabled', false);

                    $.ajax({
                        url: '{{ route("getStudentsByGuardian") }}',
                        type: 'GET',
                        data: { guardian_id: guardianId },
                        dataType: 'json',
                        success: function (response) {
                            console.log('Student data response:', response); // Debug
                            $('#student_id').empty().append('<option value="">Select Student</option>');
                            $.each(response, function (index, student) {
                                $('#student_id').append(
                                    '<option value="' + student.full_name + '">' + student.full_name + '</option>'
                                );
                            });
                            $('#student_id').trigger('change');
                            toastr.success('Student data loaded successfully!');
                        },
                        error: function (xhr) {
                            if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.blocked) {
                                toastr.error('Family ID Blocked. Please contact admin office for more details.');
                                $('#family_id').val(null).trigger('change');
                                $('#student_id').prop('disabled', true).empty().append('<option value="">Select a Family ID first</option>');
                                $('#report-container').html($('#default-message').prop('outerHTML'));
                                toggleButtons(false);
                                return;
                            }
                            console.error('Student data error:', xhr.responseText); // Debug
                            toastr.error('Error fetching student data: ' + xhr.statusText);
                            $('#student_id').empty().append('<option value="">Error loading students</option>');
                            $('#student_id').trigger('change');
                            $('#report-container').html($('#default-message').prop('outerHTML'));
                            toggleButtons(false);
                        }
                    });
                } else {
                    console.log('No Family ID selected, resetting'); // Debug
                    $('#student_id').prop('disabled', true).empty().append('<option value="">Select a Family ID first</option>');
                    $('#student_id').trigger('change');
                    $('#report-container').html($('#default-message').prop('outerHTML'));
                    toggleButtons(false);
                }
            });

            // On change of student_id dropdown, make AJAX request to get-baseline-report
         // On change of student_id dropdown, make AJAX request to get-baseline-report
$('#student_id').on('change', function () {
    var studentId = $(this).val();
    var familyId = $('#family_id').val();
    console.log('Student ID:', studentId, 'Family ID:', familyId); // Debug

    if (studentId && familyId) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Fetching baseline report data',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ url("get-baseline-report") }}',
            type: 'GET',
            data: {
                family_id: familyId,
                student_name: studentId
            },
            dataType: 'json',
            success: function (response) {
                Swal.close();
                console.log('Guardian email:', response[0]?.email); // Debug email
                if (response.length > 0) {
                    var student = response[0]; // Assuming JSON is an array
                    // Handle null or empty arrays for subjects, grades, and hours
                    var subjectNames = Array.isArray(student.subject_names) && student.subject_names.length > 0 ? student.subject_names : ['-'];
                    var targetGrades = Array.isArray(student.target_grades) && student.target_grades.length > 0 ? student.target_grades : ['-'];
                    var studentHours = Array.isArray(student.studenthours) && student.studenthours.length > 0 ? student.studenthours : [student.studenthours !== null ? student.studenthours : '-'];
                    // Generate table rows dynamically based on arrays
                    var tableRows = '';
                    var maxLength = Math.max(subjectNames.length, targetGrades.length, studentHours.length);
                    for (var i = 0; i < maxLength; i++) {
                        tableRows += `
                            <tr>
                                <td>${subjectNames[i] || '-'}</td>
                                <td>${targetGrades[i] || '-'}</td>
                                <td>${studentHours[i] || '-'}</td>
                            </tr>`;
                    }
                    // Grid-based table-like report layout
                    var html = `
<div class="report">
    <div class="logo-container" style="background-color: #2048AC; padding: 12px; border-radius: 6px; text-align: center; margin: 0 auto 15px auto; width: fit-content;">
        <img src="https://frobelcloud.efrobel.com/FrobelEducationWhite%20(1).png" alt="Frobel Education Logo" style="max-width: 120px; width: 100%; height: auto; display: block;">
    </div>
    <h1 style="color: #2048AC; font-size: 1.8rem; text-align: center; margin-bottom: 15px;">Baseline Report</h1>

    <table style="width:100%; border-collapse: collapse; margin-bottom:20px;">
        <tr>
            <td style="font-weight:bold; background:#AED6F1; padding:8px; border:1px solid #ddd; width:30%;">Family ID</td>
            <td style="padding:8px; background:#fff; border:1px solid #ddd;">${student.familyid || '-'}</td>
        </tr>
        <tr>
            <td style="font-weight:bold; background:#AED6F1; padding:8px; border:1px solid #ddd;">Student Name</td>
            <td style="padding:8px; background:#fff; border:1px solid #ddd;">${student.full_name || '-'}</td>
        </tr>
        <tr>
            <td style="font-weight:bold; background:#AED6F1; padding:8px; border:1px solid #ddd;">Year in School</td>
            <td style="padding:8px; background:#fff; border:1px solid #ddd;">${student.studentyearinschool || '-'}</td>
        </tr>
    </table>

    <table class="report-table" style="width:100%; border-collapse: collapse; margin-top:20px;">
        <thead>
            <tr>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff; text-align:left;">Subjects Studied</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff; text-align:left;">Target Grades</th>
                <th style="border:1px solid #ddd; padding:10px; background:#2048AC; color:#fff; text-align:left;">Sessions</th>
            </tr>
        </thead>
        <tbody>
            ${tableRows}
        </tbody>
    </table>
</div>`;

                    $('#report-container').html(html);
                    // Only pre-fill email if it's a valid email format
                    const guardianEmail = student.email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(student.email) ? student.email : '';
                    $('#email-report').data('guardian-email', guardianEmail);
                    toggleButtons(true);
                    toastr.success('Baseline report data loaded successfully!');
                } else {
                    $('#report-container').html('<p class="text-warning">No data found for this student.</p>');
                    $('#email-report').data('guardian-email', '');
                    toggleButtons(false);
                }
            },
            error: function (xhr) {
                console.error('Report data error:', xhr.responseText); // Debug
                Swal.close();
                toastr.error('Error fetching report data: ' + xhr.statusText);
                $('#report-container').html('<p class="text-danger">Error loading report data. Please try again.</p>');
                $('#email-report').data('guardian-email', '');
                toggleButtons(false);
            }
        });
    } else {
        console.log('No valid selection, showing default message'); // Debug
        $('#report-container').html($('#default-message').prop('outerHTML'));
        $('#email-report').data('guardian-email', '');
        toggleButtons(false);
    }
});

            // Email button click handler
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
                        if (!value) {
                            return 'Please enter an email address';
                        }
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            return 'Please enter a valid email address';
                        }
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
                    $.ajax({
                        url: '{{ url("send-baseline-report-email") }}',
                        type: 'POST',
                        data: {
                            email: email,
                            report_html: reportHtml,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.close();
                            toastr.success('Email sent successfully!');
                        },
                        error: function (xhr) {
                            console.error('Email send error:', xhr.responseText); // Debug
                            Swal.close();
                            toastr.error('Error sending email: ' + xhr.statusText);
                        }
                    });
                }
            });

            // Print button click handler
            $('#print-report').on('click', function () {
                console.log('Printing report:', $('#report-container').html()); // Debug
                window.print();
            });
        });
    </script>
@endsection
```
