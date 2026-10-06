@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Attendance</h1>
                <p>Mark and manage student attendance efficiently.</p>
            </div>
        </div>

        <!-- Attendance Filters Section -->
        <div class="card-section">
            <div class="section-header">
                <i class="fas fa-filter"></i>
                <h2>Attendance Filters</h2>
            </div>
            <div class="row g-4">
                <!-- Family ID Filter -->
                <div class="col-md-4 col-lg-3">
                    <div class="card-section-inner">
                        <label for="family_id" class="form-label">Family ID <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="number" id="family_id" class="form-control" placeholder="Enter Family ID"
                                required>
                        </div>
                        <span id="family_id_error" class="invalid-feedback"></span>
                        <button type="button" id="btnShow" class="btn btn-primary mt-3 w-100 show_family_button"
                            style="zoom:0.9;">
                            <i class="fas fa-search me-2"></i>Show Students
                        </button>
                    </div>
                </div>
                <!-- Student Filters -->
                <div class="col-md-8 col-lg-9">
                    <div class="card-section-inner">
                        <div class="row g-4">
                            <!-- First Row: Student Name, Date, Subject -->
                            <div class="col-md-4 col-lg-4">
                                <label for="student_id" class="form-label">Student Name <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                                    <select id="student_id" class="form-select student_dropdown" required>
                                        <option value="" selected disabled>Choose Student</option>
                                    </select>
                                </div>
                                <input type="hidden" name="student_unique" id="stu_idd">
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <label for="date" class="form-label">Date <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="text" id="date" autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  class="form-control" placeholder="Select Date"
                                        readonly required>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <label for="subject" class="form-label">Subject <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <select id="subject" class="form-select" required>
                                        <option value="" selected disabled>Choose Subject</option>
                                        @if (count($subjects) > 0)
                                            @foreach ($subjects as $subject)
                                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <!-- Second Row: Teacher, Time, Show Button -->
                            <div class="col-md-4 col-lg-4">
                                <label for="teacher" class="form-label">Teacher <span
                                        class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                                    <select id="teacher" class="form-select" required>
                                        <option value="" selected disabled>Choose Teacher</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <label for="time" class="form-label">Time <span class="required-star">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <select id="time" class="form-select" required>
                                        <option value="" selected disabled>Choose Time</option>
                                        <option value="09:00 - 11:00am">09:00 - 11:00am</option>
                                        <option value="11:00 - 01:00pm">11:00 - 01:00pm</option>
                                        <option value="11:20 - 01:20pm">11:20 - 01:20pm</option>
                                        <option value="11:30 - 01:30pm">11:30 - 01:30pm</option>
                                        <option value="01:30 - 03:30pm">01:30 - 03:30pm</option>
                                        <option value="02:00 - 04:00pm">02:00 - 04:00pm</option>
                                        <option value="03:45 - 05:45pm">03:45 - 05:45pm</option>
                                        <option value="04:15 - 06:15pm">04:15 - 06:15pm</option>
                                        <option value="04:30 - 06:30pm">04:30 - 06:30pm</option>
                                        <option value="06:45 - 08:45pm">06:45 - 08:45pm</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <button type="button" id="btnSave" class="btn btn-primary w-100 mt-4">
                                    <i class="fas fa-search me-2"></i>Show Attendance
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card-section">
            <div class="section-header">
                <i class="fas fa-table"></i>
                <h2>Attendance Details</h2>
            </div>
            <div class="table-responsive">
                <table id="attendance_table" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Family ID</th>
                            <th>Student Name</th>
                            <th>Year</th>
                            <th>BK + CH <span class="required-star">*</span></th>
                            {{-- <th>Session <span class="required-star">*</span></th> --}}
                            <th>H/W</th>
                            <th>1:1 Lesson</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Main Content Styles */
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #2047A8;
            position: relative;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2047A8;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #2047A8;
            margin-bottom: 20px;
        }

        /* Card Section Styles */
        .card-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(103, 192, 234, 0.3);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-section-inner {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            border: 1px solid rgba(103, 192, 234, 0.2);
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
            color: #2047A8;
            margin-right: 15px;
        }

        .section-header h2 {
            font-size: 22px;
            font-weight: 600;
            color: #2047A8;
            margin: 0;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 8px;
        }

        .required-star {
            color: #ff4d4d;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #2047A8;
            color: #495057;
            transition: all 0.3s;
            border-radius: 6px;
            height: 42px;
        }

        .form-control:focus,
        .form-select:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #4ba8d2;
            box-shadow: 0 0 0 0.25rem rgba(103, 192, 234, 0.25);
            color: #495057;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #601d2485;
        }

        .input-group-text {
            background: rgba(103, 192, 234, 0.2);
            border: 2px solid #2047A8;
            color: #2047A8;
            border-radius: 6px 0 0 6px;
        }

        /* Table Styles */
        .table.table-striped.table-hover {
            --bs-table-bg: transparent;
            --bs-table-color: #2047A8;
            --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
            --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
            --bs-line-height: 1;
            color: var(--bs-table-color);
            border-color: #2047A8;
        }

        .table th {
            background: rgba(103, 192, 234, 0.1);
            color: #2047A8;
            border-bottom: 2px solid #2047A8;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
            color: #2047A8;
        }

        /* Button Styles */
        .btn-primary {
            background: #2047A8;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            color: #ffffff;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4ba8d2;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        .btn-primary:disabled {
            background: #a0c4e4;
            cursor: not-allowed;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .loading-text {
            margin-top: 15px;
            font-size: 18px;
            color: #2047A8;
            font-weight: 500;
        }

        /* Toastr Styles */
        .toast-success {
            background-color: #28a745 !important;
            color: #ffffff !important;
            border: 2px solid #1e7e34 !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
        }

        #toast-container>.toast {
            opacity: 1 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }

        /* Animations */
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

        /* Responsive Styles */
        @media (max-width: 991px) {
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }

            .btn-primary {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .table th,
            .table td {
                font-size: 13px;
                padding: 8px;
            }

            .btn-primary {
                padding: 10px 20px;
                font-size: 14px;
            }

            .form-label {
                font-size: 14px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Configure Toastr
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000,
                extendedTimeOut: 1500,
                showMethod: 'slideDown',
                hideMethod: 'slideUp',
                showDuration: 300,
                hideDuration: 300,
            };

            // Show loading indicator
            function showLoading(message = 'Processing your request...') {
                $('#loadingIndicator .loading-text').text(message);
                $('#loadingIndicator').show();
            }

            // Hide loading indicator
            function hideLoading() {
                $('#loadingIndicator').hide();
            }

            // Show Success/Error Messages
            function showSuccessMessage(message) {
                toastr.success(message, 'Success', {
                    toastClass: 'toast toast-success',
                    iconClass: 'toast-success'
                });
            }

            function showErrorMessage(message) {
                toastr.error(message, 'Error', {
                    toastClass: 'toast toast-error',
                    iconClass: 'toast-error'
                });
            }

            // Initialize Flatpickr for Date
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                defaultDate: new Date(),
                locale: {
                    firstDayOfWeek: 1
                }
            });

            // Fetch Students by Family ID
            $("#btnShow").click(function(e) {
                e.preventDefault();
                const familyId = $('#family_id').val();
                if (!familyId) {
                    $('#family_id_error').text('Family ID is required.');
                    $('#family_id').addClass('is-invalid');
                    return;
                }

                $('#family_id_error').text('');
                $('#family_id').removeClass('is-invalid');
                $(".show_family_button").prop("disabled", true);
                showLoading('Fetching student data...');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('search.family') }}',
                    data: {
                        family_id: familyId
                    },
                    success: function(data) {
                        hideLoading();
                        $(".show_family_button").prop("disabled", false);
                        $('#stu_idd').val('');
                        $('#student_id').empty().append(
                            '<option value="" selected disabled>Choose Student</option>');

                        if (data.length === 0) {
                            showErrorMessage('No students found for this Family ID');
                            return;
                        }

                        $.each(data, function(i, item) {
                            $('#stu_idd').val(item.admissionid);

                            // Function to validate name
                            function cleanName(value) {
                                return (
                                    value !== null &&
                                    value !== undefined &&
                                    typeof value === 'string' &&
                                    value.trim().toLowerCase() !== 'null'
                                ) ? value.trim() : '';
                            }

                            const firstName = cleanName(item.studentname);
                            const lastName = cleanName(item.studentsur);

                            const fullName = (firstName + ' ' + lastName).trim();

                            if (fullName !== '') {
                                $('#student_id').append($('<option>', {
                                    value: fullName,
                                    text: fullName
                                }));
                            }
                        });


                        showSuccessMessage('Students loaded successfully');
                    },
                    error: function(error) {
                        hideLoading();
                        $(".show_family_button").prop("disabled", false);
                        $('#student_id').empty().append(
                            '<option value="" selected disabled>Choose Student</option>');

                        if (error.status === 403 && error.responseJSON && error.responseJSON.blocked) {
                            showErrorMessage('Family ID Blocked. Please contact admin office for more details.');
                            return;
                        }

                        showErrorMessage(error.responseJSON?.error ||
                            'Failed to fetch students. Please try again.');
                    }
                });
            });

            // Capitalize First Letter
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Fetch Subjects by Student
            $(".student_dropdown").change(function() {
                const studentId = this.value;
                if (!studentId) return;

                $('#attendance_table tbody').empty();
                $('#mark_attendance').remove();
                showLoading('Fetching subjects...');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('search.subject') }}',
                    data: {
                        student_name: studentId
                    },
                    success: function(data) {
                        hideLoading();
                        $('#subject').empty().append(
                            '<option value="" selected disabled>Choose Subject</option>');
                        if (data.length === 0) {
                            showErrorMessage('No subjects found for this student');
                            return;
                        }

                        $.each(data, function(i, item) {
                            $('#subject').append($('<option>', {
                                value: item.name,
                                text: capitalizeFirstLetter(item.name)
                            }));
                        });
                        showSuccessMessage('Subjects loaded successfully');
                    },
                    error: function(error) {
                        hideLoading();
                        $('#subject').empty().append(
                            '<option value="" selected disabled>Choose Subject</option>');
                        showErrorMessage(error.responseJSON?.error ||
                            'Failed to fetch subjects. Please try again.');
                    }
                });
            });

            // Fetch Teachers by Subject
            $("#subject").change(function() {
                const subject = this.value;
                if (!subject) return;

                const url = '{{ route('getTeacherName', ':name') }}'.replace(':name', subject);
                showLoading('Fetching teachers...');

                $.ajax({
                    type: 'GET',
                    url: url,
                    success: function(response) {
                        hideLoading();
                        $('#teacher').empty().append(
                            '<option value="" selected disabled>Choose Teacher</option>');
                        if (response.length === 0) {
                            showErrorMessage('No teachers found for this subject');
                            return;
                        }

                        $.each(response, function(index, teacher) {
                            $('#teacher').append($('<option>', {
                                value: teacher,
                                text: teacher
                            }));
                        });
                        showSuccessMessage('Teachers loaded successfully');
                    },
                    error: function() {
                        hideLoading();
                        showErrorMessage('Failed to fetch teachers. Please try again.');
                    }
                });
            });

            // Show Attendance Table
            let isShowingAttendance = false;
            $("#btnSave").click(function(e) {
                e.preventDefault();
                
                // Prevent multiple submissions
                if (isShowingAttendance) {
                    return false;
                }

                $('#attendance_table tbody').empty();
                $('#mark_attendance').remove();

                const data = {
                    student_name: $('#student_id').val(),
                    date: $('#date').val(),
                    teacher: $('#teacher').val(),
                    subject: $('#subject').val(),
                    time: $('#time').val(),
                    stu_idd: $('#stu_idd').val()
                };

                if (!data.date || !data.student_name || !data.subject || !data.teacher || !data.time) {
                    showErrorMessage('Please fill all required fields!');
                    return;
                }

                // Set submitting flag and disable button
                isShowingAttendance = true;
                const btnSave = $('#btnSave');
                const originalText = btnSave.html();
                btnSave.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');

                showLoading('Fetching attendance data...');

                $.ajax({
                    type: 'GET',
                    url: '{{ route('attendance.search') }}',
                    data: data,
                    success: function(response) {
                        hideLoading();
                        isShowingAttendance = false;
                        $('#btnSave').prop('disabled', false).html('<i class="fas fa-search me-2"></i>Show Attendance');
                        
                        if (response.message === "Attendance entry already exists") {
                            showErrorMessage("Attendance already marked for this student!");
                            return;
                        }

                        const familyId = $('#family_id').val();
                        $('#attendance_table tbody').append(`
                    <tr id="attendance_table_row">
                        <td><input type="number" disabled class="form-control" value="${familyId}"></td>
                        <td><input type="text" disabled class="form-control" value="${response.timetable.studentname}"></td>
                        <td><input type="text" disabled class="form-control" value="${response.years_in_school}"></td>
                        <td><input type="text" id="bk_ch" class="form-control" placeholder="Enter BK + CH"></td>
                        <td style="display:none;"><input type="hidden" id="session" class="form-control" placeholder="Enter Session"></td>
                        <td><input type="checkbox" id="hw" class="form-check-input"></td>
                        <td style="text-align:center; vertical-align:middle;">
                            <input type="checkbox" id="one_to_one" class="form-check-input"
                                style="width:18px; height:18px; cursor:pointer; accent-color:#6f42c1;">
                        </td>
                    </tr>
                `);
                        $('<div class="col-md-5 offset-md-4"><button id="mark_attendance" class="btn btn-primary mt-3 w-100"><i class="fas fa-check me-2"></i>Mark Attendance</button></div>')
                            .insertAfter("#attendance_table");

                        showSuccessMessage('Attendance data loaded successfully');
                    },
                    error: function(error) {
                        hideLoading();
                        isShowingAttendance = false;
                        $('#btnSave').prop('disabled', false).html('<i class="fas fa-search me-2"></i>Show Attendance');
                        showErrorMessage(error.responseJSON?.error ||
                            'Failed to fetch attendance data. Please try again.');
                    }
                });
            });

            // Mark Attendance
            let isMarkingAttendance = false;
            $('body').on('click', '#mark_attendance', function() {
                const button = $(this);
                
                // Prevent multiple submissions
                if (isMarkingAttendance) {
                    return false;
                }

                const data = {
                    student_name: $('#student_id').val(),
                    family_id: $('#family_id').val(),
                    teacher: $('#teacher').val(),
                    years_in_school: $('#attendance_table_row input').eq(2).val(),
                    subject: $('#subject').val(),
                    time: $('#time').val(),

                    date: $('#date').val(),
                    bk_ch: $('#bk_ch').val(),
                    status: $('#hw').is(':checked') ? 'completed' : '',
                    one_to_one: $('#one_to_one').is(':checked') ? 'Yes' : 'No'
                };

                if (!data.date || !data.student_name || !data.subject || !data.teacher || !data.time || !data.bk_ch || !data.years_in_school) {
                    showErrorMessage('Please fill all required fields!');
                    return;
                }

                // Set submitting flag and disable button
                isMarkingAttendance = true;
                const originalText = button.html();
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Marking...');
                showLoading('Marking attendance...');

                $.ajax({
                    type: 'POST',
                    url: '{{ route('attendance.store') }}',
                    data: data,
                    success: function(data, textStatus, xhr) {
                        hideLoading();
                        isMarkingAttendance = false;
                        button.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Mark Attendance');

                        if (xhr.status === 203) {
                            showErrorMessage("Attendance already marked for this student!");
                        } else {
                            showSuccessMessage("Attendance marked successfully!");
                            $('#date').val('');
                            $('#time').val('');
                            $('#subject').val('');
                            $('#student_id').val('');
                            $('#teacher').val('');
                            $('#attendance_table_row').remove();
                            $('#mark_attendance').remove();
                        }
                    },
                    error: function(error) {
                        hideLoading();
                        isMarkingAttendance = false;
                        button.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Mark Attendance');
                        showErrorMessage(error.responseJSON?.error ||
                            "Failed to mark attendance. Please try again.");
                    }
                });
            });

            // Handle session-based notifications
            @if (session('success'))
                toastr.success("{{ session('success') }}", 'Success');
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", 'Error');
            @endif
        });
    </script>
@endsection
