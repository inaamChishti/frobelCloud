
@extends('layouts.branchDashboardApp')

@section('content')
<style>
    /* General container styles */
    .btn-group .btn-primary:not(:last-child) {
        margin-right: 10px;
    }
    .main-content {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        animation: fadeIn 0.6s ease-in-out;
        border: 1px solid #20439F;
        margin: 20px;
    }
    .main-content h1 {
        font-size: 28px;
        font-weight: 700;
        color: #20439F;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }
    .main-content p {
        font-size: 16px;
        color: #20439F;
        margin-bottom: 20px;
        font-weight: 400;
        line-height: 1.5;
    }
    .table-responsive {
        border-radius: 12px;
        overflow-x: auto;
        background: #ffffff;
        border: 1px solid #e0e7ff;
    }

    .status-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .status-card {
        flex: 1 1 150px;
        background: #f8fafc;
        border: 1px solid #e0e7ff;
        border-radius: 10px;
        padding: 12px 16px;
        color: #20439F;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .status-card h3 {
        font-size: 14px;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-card span {
        font-size: 24px;
        font-weight: 700;
    }

    /* Table styles */
    .table.table-striped {
        --bs-table-bg: #ffffff;
        --bs-table-color: #20439F;
        --bs-table-striped-bg: rgba(103, 192, 234, 0.05);
        --bs-table-striped-color: #20439F;
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: #e0e7ff;
        border-radius: 12px;
        margin-bottom: 0;
        width: 100%;
        table-layout: auto;
        font-family: 'Inter', sans-serif;
    }
    .table.table-striped thead th {
        background: #f8fafc;
        color: #20439F;
        border-bottom: 2px solid #20439F;
        font-weight: 600;
        padding: 12px 16px;
        text-align: left;
        vertical-align: middle;
        min-width: 100px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table.table-striped th,
    .table.table-striped td {
        text-align: left;
        padding: 12px 16px;
        vertical-align: middle;
        border-color: #e0e7ff;
        font-size: 13px;
        font-weight: 500;
        color: #20439F;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    .table.table-striped tbody tr {
        transition: background 0.2s ease;
    }
    .table.table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-bg-type: rgba(103, 192, 234, 0.05);
        --bs-table-color-type: #20439F;
        background-color: var(--bs-table-bg-type);
        color: var(--bs-table-color-type);
    }

    /* Button, select, and search bar styles */
    .btn-primary {
        background: #20439F;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        color: #ffffff;
        font-size: 13px;
        font-weight: 500;
        transition: background 0.3s ease, transform 0.2s ease;
    }
    .btn-primary:hover {
        background: #4ba8d2;
        transform: translateY(-1px);
    }
    .btn-primary:active {
        transform: translateY(0);
    }
    .btn-secondary {
        background: #6c757d;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        color: #ffffff;
        font-size: 13px;
        font-weight: 500;
        transition: background 0.3s ease, transform 0.2s ease;
    }
    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }
    .btn-secondary:active {
        transform: translateY(0);
    }
    .form-select, .form-control {
        background: #f8fafc;
        color: #333;
        border: 1px solid #20439F;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        height: 36px;
        transition: background 0.3s ease, border-color 0.3s ease;
    }
    .form-control:hover, .form-select:hover {
        background: #e9ecef;
        border-color: #4ba8d2;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4ba8d2;
        box-shadow: 0 0 0 3px rgba(75, 168, 210, 0.2);
        outline: none;
    }
    .input-group {
        max-width: 400px;
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .main-content {
            margin: 10px;
            padding: 16px;
        }
        .main-content h1 {
            font-size: 24px;
        }
        .main-content p {
            font-size: 14px;
        }
        .table.table-striped thead th,
        .table.table-striped tbody td {
            font-size: 12px;
            padding: 8px 12px;
            max-width: 150px;
        }
        .btn-primary,
        .btn-secondary,
        .form-select,
        .form-control {
            padding: 6px 10px;
            font-size: 12px;
            height: 32px;
        }
        .input-group {
            max-width: 100%;
        }
    }
</style>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Active/Inactive Students List</h1>
            <p>View and manage active or inactive student information for this branch.</p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="input-group me-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Search students...">
                <button class="btn btn-primary" type="button" id="searchButton">Search</button>
                <button class="btn btn-secondary" type="button" id="resetButton">Reset</button>
            </div>
            <select id="statusFilter" class="form-select me-3" style="width:auto; min-width: 180px;">
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
                <option value="all">All Students</option>
            </select>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" id="exportToCSV">
                    Export to CSV
                </button>
            </div>
        </div>
    </div>

    <div class="status-summary">
        <div class="status-card">
            <h3>Total Active</h3>
            <span id="totalActive">0</span>
        </div>
        <div class="status-card">
            <h3>Total Inactive</h3>
            <span id="totalInactive">0</span>
        </div>
    </div>

    <div class="table-responsive" style="zoom:0.9;">
        <table id="studentsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name of Student</th>
                    <th>Lessons</th>
                    <th>Date of Birth</th>
                    <th>Year in School</th>
                    <th>Start Date</th>
                    <th>Last Attendance</th>
                    <th>Record Type</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">
                <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    let table;
    const defaultStatus = 'active';
    let currentStatus = defaultStatus;
    const $statusFilter = $('#statusFilter');

    // Initialize DataTables with searching disabled (since we'll handle it server-side)
    table = $('#studentsTable').DataTable({
        paging: false, // Disable pagination
        searching: false, // Disable client-side search
        ordering: false, // Disable sorting
        info: false, // Disable info
    });

    // Set default filter and fetch students on page load
    $statusFilter.val(defaultStatus);
    updateSummary(); // set initial totals to zero
    fetchData('', currentStatus);

    function fetchData(search = '', status = '') {
        Swal.fire({
            title: 'Please wait, system is loading data...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ url('/get-active-inactive') }}',
            method: 'GET',
            data: { search: search, status: status },
            success: function(data) {
                console.log('Fetched data:', data.paymentRecords); // Debug: Check fetched data
                table.clear(); // Clear existing rows
                if (data.paymentRecords && Array.isArray(data.paymentRecords)) {
                    if (data.paymentRecords.length === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Results',
                            text: 'No records match your search criteria.'
                        });
                        table.row.add(['No records found', '', '', '', '', '', '', '']).draw();
                    } else {
                        $.each(data.paymentRecords, function(index, value) {
                            var recordType = value.recordType ? value.recordType.toLowerCase() : '';
                            var isNullOrEmpty = !recordType || recordType === '';
                            var fullName = [value.studentname, value.studentsur].filter(Boolean).join(' ').trim();
                            // Calculate lesson count from subject_names JSON
                            var lessonCount = 0;
                            if (value.subject_names) {
                                try {
                                    var subjects = typeof value.subject_names === 'string'
                                        ? JSON.parse(value.subject_names)
                                        : value.subject_names;
                                    lessonCount = Array.isArray(subjects) ? subjects.length : 0;
                                } catch (e) {
                                    lessonCount = 0;
                                }
                            }
                            table.row.add([
                                value.admissionid || 'Not Set',
                                fullName || 'Not Set',
                                lessonCount,
                                value.studentdob || 'Not Set',
                                value.studentyearinschool || 'Not Set',
                                value.joiningdate || 'Not Set',
                                value.latest_attendance_date || 'Not Set',
                                `<select class="form-select record-type"
                                        data-student-id="${value.studentid || ''}"
                                        data-admission-id="${value.admissionid || ''}"
                                        data-student-name="${value.studentname || ''}"
                                        data-student-sur="${value.studentsur || ''}">
                                    <option value="" ${isNullOrEmpty ? 'selected' : ''}>Select Status</option>
                                    <option value="active" ${recordType === 'active' ? 'selected' : ''}>Active</option>
                                    <option value="inactive" ${recordType === 'inactive' ? 'selected' : ''}>Inactive</option>
                                </select>`
                            ]);
                        });
                        table.draw(); // Redraw table with new data
                    }
                } else {
                    console.error('Invalid data format:', data);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid data format received from server.'
                    });
                        table.row.add(['No records found', '', '', '', '', '', '', '']).draw();
                }
                updateSummary(data.counts || {});
                Swal.close();
            },
            error: function(error) {
                updateSummary();
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch data. Please try again.'
                });
                console.error('Error fetching data:', error);
            }
        });
    }

    function updateSummary(counts = {}) {
        $('#totalActive').text(counts.active ?? 0);
        $('#totalInactive').text(counts.inactive ?? 0);
    }

    // Handle search button click
    $('#searchButton').on('click', function() {
        var searchValue = $('#searchInput').val().trim();
        currentStatus = $statusFilter.val();
        fetchData(searchValue, currentStatus); // Fetch data with search term
    });

    // Handle reset button click
    $('#resetButton').on('click', function() {
        $('#searchInput').val(''); // Clear the search input
        $statusFilter.val(defaultStatus);
        currentStatus = defaultStatus;
        fetchData('', currentStatus); // Fetch default records
    });

    // Handle Enter key press in search input
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            $('#searchButton').trigger('click'); // Trigger search button click
        }
    });

    // Handle status filter change
    $statusFilter.on('change', function() {
        currentStatus = $(this).val();
        var searchValue = $('#searchInput').val().trim();
        fetchData(searchValue, currentStatus);
    });

    // Handle dropdown change for record type
    $(document).on('change', '.record-type', function() {
        var studentId = $(this).data('student-id');
        var admissionId = $(this).data('admission-id');
        var studentName = $(this).data('student-name');
        var studentSur = $(this).data('student-sur');
        var newStatus = $(this).val();

        if (!studentId || studentId === 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Student ID is not defined. Please check the data.'
            });
            return;
        }

        if (!newStatus) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Please select a valid status (Active or Inactive).'
            });
            return;
        }

        Swal.fire({
            title: 'Please wait, updating status...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ url('/update-student-status') }}',
            method: 'POST',
            data: {
                student_id: studentId,
                admission_id: admissionId,
                student_status: newStatus,
                student_name: studentName,
                student_sur: studentSur,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Student status updated successfully!'
                });
                // Update DataTables data
                var row = $(this).closest('tr');
                var rowData = table.row(row).data();
                rowData[7] = `<select class="form-select record-type"
                                data-student-id="${studentId}"
                                data-admission-id="${admissionId}"
                                data-student-name="${studentName}"
                                data-student-sur="${studentSur}">
                                <option value="" ${newStatus === '' ? 'selected' : ''}>Select Status</option>
                                <option value="active" ${newStatus === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${newStatus === 'inactive' ? 'selected' : ''}>Inactive</option>
                            </select>`;
                table.row(row).data(rowData).draw();
            }.bind(this),
            error: function(error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update status. Please try again.'
                });
                console.error('Error updating status:', error);
            }
        });
    });

    // Export to CSV
    $('#exportToCSV').on('click', function() {
        var csv = [];
        csv.push([
            'ID', 'Name of Student', 'Lessons', 'Date of Birth',
            'Year in School', 'Start Date', 'Last Attendance', 'Record Type'
        ].map(val => `"${val}"`).join(','));

        $('#studentsTable tbody tr').each(function() {
            var cols = $(this).find('td');

            if (cols.length < 8) return; // skip rows without full data

            // Record type select safe extraction
            var $select = $(cols[7]).find('select.record-type');
            var recordTypeText = "Not Set";
            if ($select.length > 0) {
                var selectedText = $select.find("option:selected").text().trim();
                if (selectedText && selectedText !== "Select Status") {
                    recordTypeText = selectedText;
                }
            } else {
                var textValue = $(cols[7]).text().trim();
                if (textValue) {
                    recordTypeText = textValue;
                }
            }

            var rowData = [
                $(cols[0]).text().trim() || 'Not Set',
                $(cols[1]).text().trim() || 'Not Set',
                $(cols[2]).text().trim() || 'Not Set',
                $(cols[3]).text().trim() || 'Not Set',
                $(cols[4]).text().trim() || 'Not Set',
                $(cols[5]).text().trim() || 'Not Set',
                $(cols[6]).text().trim() || 'Not Set',
                recordTypeText
            ].map(val => `"${val.replace(/"/g, '""')}"`);

            csv.push(rowData.join(','));
        });

        var csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
        var downloadLink = document.createElement('a');
        downloadLink.download = 'students_data.csv';
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    });

});
</script>
@endsection

