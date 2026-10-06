@extends('layouts.branchDashboardApp')

@section('content')
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>

<style>

/* Main Content Styling */
.main-content {
    background: transparent;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.6s ease-in-out;
    border: 1px solid #2045A4;
}

.main-content h1 {
    font-size: 32px;
    font-weight: 700;
    color: #2045A4;
    margin-bottom: 15px;
}

.main-content p {
    font-size: 18px;
    color: #2045A4;
    margin-bottom: 20px;
}

/* Card Section Styling */
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

/* Section Header Styling */
.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(103, 192, 234, 0.3);
}

.section-header i {
    font-size: 24px;
    color: #2045A4;
    margin-right: 15px;
}

.section-header h2 {
    font-size: 22px;
    font-weight: 600;
    color: #2045A4;
    margin: 0;
}

/* Form Elements Styling */
.form-label {
    font-weight: 600;
    color: #2045A4;
    margin-bottom: 8px;
}

.required-star {
    color: #ff4d4d;
    font-size: 14px;
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid #2045A4;
    color: #495057;
    transition: all 0.3s;
    border-radius: 6px;
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.2);
    border-color: #4ba8d2;
    box-shadow: 0 0 0 0.25rem rgba(103, 192, 234, 0.25);
    color: #495057;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #601d2485;
}

.input-group-text {
    background: rgba(103, 192, 234, 0.2);
    border: 2px solid #2045A4;
    color: #2045A4;
    border-radius: 6px 0 0 6px;
}

/* Table Styling */
.table.table-striped.table-hover {
    --bs-table-bg: transparent;
    --bs-table-color: #2045A4;
    --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
    --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
    --bs-line-height: 1;
    color: var(--bs-table-color);
    border-color: #2045A4;
}

.table th {
    background: rgba(103, 192, 234, 0.1);
    color: #2045A4;
    border-bottom: 2px solid #2045A4;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
    color: #2045A4;
}

.table tr {
    height: 40px; /* Reduced row height */
}

.table td, .table th {
    padding: 6px 10px; /* Reduced padding for smaller cells */
    font-size: 14px; /* Slightly smaller font size */
}

.table th:nth-child(9), .table td:nth-child(9) { /* Additional Info column */
    width: 200px; /* Adjust width as needed */
    white-space: normal; /* Allow text wrapping */
    word-break: break-word; /* Break long words */
}

.table th:last-child, .table td:last-child {
    width: 200px; /* Increased width for Action column to prevent wrapping */
    text-align: center; /* Center content in the Action column */
}

/* Action Buttons Styling */
.table .action-buttons {
    display: flex !important; /* Ensure flex display with high specificity */
    flex-wrap: nowrap !important; /* Prevent wrapping */
    gap: 5px; /* Space between buttons */
    justify-content: center; /* Center buttons horizontally */
    align-items: center; /* Align buttons vertically */
    width: 100%; /* Ensure container takes full cell width */
}

.table .action-buttons .btn {
    padding: 4px 8px; /* Reduced padding for compact buttons */
    font-size: 12px; /* Smaller font size for better fit */
    line-height: 1.5; /* Uniform line height */
    min-width: 60px; /* Reduced min-width for better fit */
    height: 28px; /* Fixed height for all buttons */
    display: inline-flex; /* Ensure buttons align content properly */
    align-items: center; /* Center content vertically */
    justify-content: center; /* Center content horizontally */
    border-radius: 6px; /* Consistent border radius */
    text-transform: capitalize; /* Improve button text readability */
    white-space: nowrap; /* Prevent text wrapping in buttons */
    box-sizing: border-box; /* Ensure padding doesn't affect width */
}

.table .action-buttons .btn-primary {
    background: #2045A4;
    border: none;
    color: #ffffff;
}

.table .action-buttons .btn-primary:hover {
    background: #4ba8d2;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.table .action-buttons .btn-warning {
    background: #ffc107;
    border: none;
    color: #212529;
}

.table .action-buttons .btn-warning:hover {
    background: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.table .action-buttons .btn-danger {
    background: #dc3545;
    border: none;
    color: #ffffff;
}

.table .action-buttons .btn-danger:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

/* DataTable Pagination Styling */
.dataTables_paginate {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}

.dataTables_paginate .paginate_button {
    background: #2045A4 !important;
    color: #ffffff !important;
    border: 1px solid #4ba8d2 !important;
    border-radius: 4px;
    margin: 0 5px;
    padding: 5px 10px;
}

.dataTables_paginate .paginate_button:hover {
    background: #4ba8d2 !important;
}

.dataTables_paginate .paginate_button.current {
    background: #4ba8d2 !important;
    color: #ffffff !important;
}

/* General Button Styling */
.btn-primary {
    background: #2045A4;
    border: none;
    border-radius: 8px;
    padding: 5px 24px;
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

/* Toastr Styling */
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

#toast-container > .toast {
    opacity: 1 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Hide DataTable Filter */
#attendance_table_filter {
    display: none;
}

/* Responsive Adjustments */
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

    .table th, .table td {
        font-size: 12px; /* Smaller font size for mobile */
        padding: 6px 8px; /* Further reduced padding for mobile */
    }

    .table .action-buttons .btn {
        padding: 3px 6px; /* Further reduced padding for mobile */
        font-size: 11px; /* Smaller font size for mobile */
        min-width: 50px; /* Smaller min-width for mobile */
        height: 26px; /* Smaller height for mobile */
    }

    .table .action-buttons .btn-primary,
    .table .action-buttons .btn-warning,
    .table .action-buttons .btn-danger {
        padding: 3px 6px; /* Match mobile padding */
        height: 26px; /* Match mobile height */
        font-size: 11px; /* Match mobile font size */
    }

    .table th:last-child, .table td:last-child {
        width: 180px; /* Slightly smaller width for mobile */
    }

    .btn-primary {
        padding: 10px 20px;
        font-size: 14px;
    }

    .form-label {
        font-size: 14px;
    }
}

@media (max-width: 576px) {
    .table .action-buttons {
        flex-direction: row; /* Ensure row layout even on small screens */
        flex-wrap: nowrap; /* Prevent wrapping */
    }

    .table .action-buttons .btn {
        padding: 2px 5px; /* Even smaller padding for very small screens */
        font-size: 10px; /* Smaller font size */
        min-width: 45px; /* Smaller min-width */
        height: 24px; /* Smaller height */
    }

    .table th:last-child, .table td:last-child {
        width: 160px; /* Adjust width for very small screens */
    }
}
</style>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>View Attendance</h1>
            <p>View and manage student attendance records efficiently.</p>
        </div>
        <button class="btn btn-primary" id="exp"><i class="fas fa-download me-2"></i>Export All</button>
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
                        <input type="number" id="family_id" class="form-control" placeholder="Enter Family ID" required>
                    </div>
                    <span id="family_id_error" class="invalid-feedback"></span>
                    <button type="button" id="btnShow" class="btn btn-primary mt-3 w-100 show_family_button" style="zoom:0.9;">
                        <i class="fas fa-search me-2"></i>Show Students
                    </button>
                </div>
            </div>
            <!-- Other Filters -->
            <div class="col-md-8 col-lg-9">
                <div class="card-section-inner">
                    <div class="row g-4">
                        <!-- First Row: Student Name, From Date, To Date -->
                        <div class="col-md-4 col-lg-4">
                            <label for="student_id" class="form-label">Student Name <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-graduate"></i></span>
                                <select id="student_id" class="form-select student_name_filter" required>
                                    <option value="" selected disabled>Choose Student</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label for="from_date" class="form-label">From Date <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="text" id="from_date" autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  class="form-control" placeholder="dd/mm/yyyy" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label for="to_date" class="form-label">To Date <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="text" id="to_date" autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  class="form-control" placeholder="dd/mm/yyyy" readonly required>
                            </div>
                        </div>
                        <!-- Second Row: Subject, Time Slot, Clear Button -->
                        <div class="col-md-4 col-lg-4">
                            <label for="subject_list" class="form-label">Subject <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                <select id="subject_list" class="form-select" required>
                                    <option value="" selected disabled>Choose Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label for="time_slot" class="form-label">Time Slot <span class="required-star">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                <select id="time_slot" class="form-select" required>
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
                            <button type="button" id="clearButton" class="btn btn-primary mt-4 w-100">
                                <i class="fas fa-eraser me-2"></i>Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card-section"  >
        <div class="section-header">
            <i class="fas fa-table"></i>
            <h2>Attendance Details</h2>
        </div>
        <div class="table-responsive" >
            <table id="attendance_table" class="table table-striped table-hover" >
                <thead>
                    <tr>
                        <th>Family ID</th>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Year</th>
                        <th>Teacher</th>
                        <th>Subject</th>
                        <th>Time Slot</th>
                        <th>BK + CH</th>
                        <th>comments</th>
                        <th>Adjustment</th>
                         <th>Start Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="zoom:0.8;"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    // Show "Please Wait" Swal for all AJAX requests
    $(document).ajaxStart(function() {
        Swal.fire({
            title: 'Please Wait...',
            text: 'Retrieving data',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    }).ajaxStop(function() {
        Swal.close();
    });

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
        timeOut: 2000,
        extendedTimeOut: 1000,
        showMethod: 'slideDown',
        hideMethod: 'slideUp',
        showDuration: 300,
        hideDuration: 300,
    };

    // Initialize Flatpickr for Dates
    flatpickr("#from_date", {
        dateFormat: "d/m/Y",
        locale: { firstDayOfWeek: 1 }
    });

    flatpickr("#to_date", {
        dateFormat: "d/m/Y",
        locale: { firstDayOfWeek: 1 }
    });

    // Handle Filter Changes
    var selectElement = document.getElementById("student_id");
    var fromDateElement = document.getElementById("from_date");
    var toDateElement = document.getElementById("to_date");
    var timeSlotElement = document.getElementById("time_slot");
    var subjectListElement = document.getElementById("subject_list");

    function handleElementChange() {
        var selectedValue = selectElement.value;
        var fromDate = fromDateElement.value;
        var toDate = toDateElement.value;
        var timeSlot = timeSlotElement.value;
        var subject = subjectListElement.value;
        var familyId = $('#family_id').val(); // Get family_id

        // Convert dates to Date objects for comparison
        var startDate = moment(fromDate, "DD/MM/YYYY").toDate();
        var endDate = moment(toDate, "DD/MM/YYYY").toDate();
        var diffTime = endDate - startDate;
        var diffDays = diffTime / (1000 * 3600 * 24);

        if (diffDays > 365) {
            Swal.fire({
                icon: 'error',
                title: 'Date Range Error',
                text: 'The selected date range exceeds one year. Please select a smaller range.'
            });
            return;
        }

        table.clear().draw();
        table.ajax.url("{{ route('attendance.viewz.view') }}?selectedValue=" +
            encodeURIComponent(selectedValue) +
            "&fromDate=" + encodeURIComponent(fromDate) +
            "&toDate=" + encodeURIComponent(toDate) +
            "&timeSlot=" + encodeURIComponent(timeSlot) +
            "&subject=" + encodeURIComponent(subject) +
            "&family_id=" + encodeURIComponent(familyId)).load(); // Add family_id
    }

    selectElement.addEventListener("change", handleElementChange);
    fromDateElement.addEventListener("change", handleElementChange);
    toDateElement.addEventListener("change", handleElementChange);
    timeSlotElement.addEventListener("change", handleElementChange);
    subjectListElement.addEventListener("change", handleElementChange);

    // Initialize DataTable
    var table = $('#attendance_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('attendance.viewz.view') }}",
            type: "GET",
            data: function(d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.student_id = $('#student_id').val();
                d.time_slot = $('#time_slot').val();
                d.subject = $('#subject_list').val();
                d.family_id = $('#family_id').val(); // Add family_id to the request
            }
        },
        columns: [
            { data: 'family_id', orderable: false },
            {
                data: 'date',
                render: function(data) {
                    return moment(data).format('DD-MMMM-YYYY');
                },
                orderable: false
            },
            { data: 'student_name', orderable: false },
            { data: 'year', orderable: false },
            { data: 'teacher_name', orderable: false },
            { data: 'subject', orderable: false },
            { data: 'time_slot', orderable: false },
            { data: 'bk_ch', orderable: false },
            { data: 'additional_info', orderable: false },
            { data: 'adjustment', orderable: false },
            {
                data: 'start_date', // ✅ Add start_date column
                render: function(data) {
                    return data ? moment(data).format('DD-MMMM-YYYY') : '';
                },
                orderable: false
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
    });

    // Filter by Student Name
    $(".student_name_filter").on('change', function() {
        table.columns(2).search($(this).val()).draw();
    });

    // Clear Filters
    $('#clearButton').click(function() {
        $('#family_id').val('');
        $('#from_date').val('');
        $('#to_date').val('');
        $('#student_id').empty().append('<option value="" selected disabled>Choose Student</option>');
        $('#time_slot').val('');
        $('#subject_list').val('');
        table.columns().search('').draw();
    });

    // Fetch Students by Family ID
    $("#btnShow").click(function(e) {
        e.preventDefault();
        var family_id = $('#family_id').val();
        if (family_id === '') {
            $('#family_id_error').text('Family ID is required.');
            $('#family_id').addClass('is-invalid');
            return;
        }

        $('#family_id_error').text('');
        $('#family_id').removeClass('is-invalid');
        $(".show_family_button").prop("disabled", true);

        $.ajax({
            type: 'POST',
            url: '{{ route('search.family.view') }}',
            data: { family_id: family_id },
            success: function(data) {
                $(".show_family_button").prop("disabled", false);
                $('#student_id').empty().append('<option value="" selected disabled>Choose Student</option>');
                $.each(data, function(i, item) {
                    // Filter out null, undefined, or empty strings
                    var nameParts = [item.studentname, item.studentsur].filter(part => part && part.trim() !== '');
                    var fullName = nameParts.join(' '); // Join valid parts with a space
                    $('#student_id').append($('<option>', {
                        value: fullName,
                        text: fullName
                    }));
                });
            },
            error: function(error) {
                $(".show_family_button").prop("disabled", false);
                toastr.error(error.responseJSON?.error || 'Something went wrong, please contact customer care!', 'Error');
            }
        });
    });

    // Export Functionality
    $('#exp').on('click', function() {
        var familyId = $('#family_id').val();
        var studentName = $('#student_id').val();
        var fromDate = $('#from_date').val();
        var toDate = $('#to_date').val();
        var subject = $('#subject_list').val();
        var timeSlot = $('#time_slot').val();

        if (!familyId && !studentName && !fromDate && !toDate && !subject && !timeSlot) {
            toastr.error('Please apply at least one filter to export data.', 'Error');
            return;
        }

        var form = $('<form>', {
            'method': 'GET',
            'action': "{{ url('exportAttenView') }}",
            'style': 'display:none;'
        });

        form.append($('<input>', { 'type': 'hidden', 'name': 'familyId', 'value': familyId }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'studentName', 'value': studentName }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'fromDate', 'value': fromDate }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'toDate', 'value': toDate }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'subject', 'value': subject }));
        form.append($('<input>', { 'type': 'hidden', 'name': 'timeSlot', 'value': timeSlot }));

        $('body').append(form);
        form.submit();
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
