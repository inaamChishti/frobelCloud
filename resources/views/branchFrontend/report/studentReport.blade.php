@extends('layouts.branchDashboardApp')

@section('content')
<style>
    .btn-group .btn-primary:not(:last-child) {
        margin-right: 10px;
    }
</style>
<div class="main-content" style="zoom:0.9;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Students List</h1>
            <p>View and manage student information for this branch.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="exportExcel">
                <i class="fas fa-file-excel me-2"></i>Export to Excel
            </button>
        </div>
    </div>

    <div class="filter-container mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" id="activeStudents">
                    <i class="fas fa-users me-2"></i>Active Students
                </button>
                <button type="button" class="btn btn-primary" id="medicalStudents">
                    <i class="fas fa-medkit me-2"></i>Medical Condition
                </button>
            </div>
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" id="family_id" placeholder="Enter Family ID">
                <button class="btn btn-primary" id="submit">Submit</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="studentsTable" class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th class="col-family-id">Family ID</th>
                    <th class="col-name">Name</th>
                    <th class="col-gender">Gender</th>
                    <th class="col-dob">DOB</th>
                    <th class="col-year">Year in School</th>
                    <th class="col-medical">Medical Condition</th>
                    <th class="col-additional-needs">Additional Needs</th>
                    <th class="col-allergies">Allergies</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">
                <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
    .main-content {
        background: transparent;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.6s ease-in-out;
        border: 1px solid #2045A5;
    }

    .main-content h1 {
        font-size: 28px;
        font-weight: 600;
        color: #2045A5;
        margin-bottom: 10px;
    }

    .main-content p {
        font-size: 16px;
        color: #2045A5;
        margin-bottom: 15px;
    }

    .table-responsive {
        border-radius: 8px;
        overflow-x: auto;
    }

    .table.table-striped.table-hover {
        --bs-table-bg: transparent;
        --bs-table-color: #2045A5;
        --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
        --bs-table-striped-color: #2045A5;
        --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
        --bs-table-hover-color: #ffffff;
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: #2045A5;
        border-radius: 8px;
        margin-bottom: 0;
        width: 100%;
        table-layout: fixed;
    }

    .table.table-striped.table-hover thead th {
        background: transparent;
        color: #2045A5;
        border-bottom: 2px solid #2045A5;
        font-weight: 600;
        padding: 10px;
        text-align: left;
        vertical-align: middle;
    }

    .table.table-striped.table-hover th.col-family-id,
    .table.table-striped.table-hover td.col-family-id {
        width: 10%;
        text-align: center;
    }

    .table.table-striped.table-hover th.col-name,
    .table.table-striped.table-hover td.col-name {
        width: 20%;
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table.table-striped.table-hover th.col-gender,
    .table.table-striped.table-hover td.col-gender {
        width: 10%;
        text-align: center;
    }

    .table.table-striped.table-hover th.col-dob,
    .table.table-striped.table-hover td.col-dob {
        width: 15%;
        text-align: center;
    }

    .table.table-striped.table-hover th.col-year,
    .table.table-striped.table-hover td.col-year {
        width: 15%;
        text-align: center;
    }

    .table.table-striped.table-hover th.col-medical,
    .table.table-striped.table-hover td.col-medical {
        width: 15%;
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table.table-striped.table-hover th.col-additional-needs,
    .table.table-striped.table-hover td.col-additional-needs {
        width: 15%;
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table.table-striped.table-hover th.col-allergies,
    .table.table-striped.table-hover td.col-allergies {
        width: 15%;
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table.table-striped.table-hover tbody {
        background: transparent;
    }

    .table.table-striped.table-hover tbody tr {
        transition: all 0.3s ease;
    }

    .table.table-striped.table-hover tbody td {
        vertical-align: middle;
        border-color: #2045A5;
        padding: 10px;
        color: #2045A5;
        font-size: 13px;
        font-weight: 500;
    }

    .table.table-striped.table-hover tbody tr:hover {
        background: #2045A5 !important;
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .table.table-striped.table-hover tbody tr:hover td {
        color: #ffffff !important;
    }

    .table.table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-color-type: #2045A5 !important;
        --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
        color: var(--bs-table-color-type) !important;
        background-color: var(--bs-table-bg-type) !important;
    }

    .btn-primary {
        background: #2045A5;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #2045A5;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        scale: 1.03;
    }

    .btn-primary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .input-group .form-control {
        border: 1px solid #2045A5;
        border-radius: 6px;
    }

    .input-group .form-control:focus {
        border-color: #2045A5;
        box-shadow: 0 0 0 0.2rem rgba(103, 192, 234, 0.25);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 768px) {
        .table.table-striped.table-hover thead th,
        .table.table-striped.table-hover tbody td {
            font-size: 12px;
            padding: 6px;
        }
        .btn-primary {
            padding: 8px 16px;
            font-size: 12px;
        }
        .input-group {
            max-width: 100%;
        }
        .table.table-striped.table-hover th.col-family-id,
        .table.table-striped.table-hover td.col-family-id {
            width: 15%;
        }
        .table.table-striped.table-hover th.col-name,
        .table.table-striped.table-hover td.col-name {
            width: 20%;
        }
        .table.table-striped.table-hover th.col-gender,
        .table.table-striped.table-hover td.col-gender {
            width: 10%;
        }
        .table.table-striped.table-hover th.col-dob,
        .table.table-striped.table-hover td.col-dob {
            width: 15%;
        }
        .table.table-striped.table-hover th.col-year,
        .table.table-striped.table-hover td.col-year {
            width: 15%;
        }
        .table.table-striped.table-hover th.col-medical,
        .table.table-striped.table-hover td.col-medical {
            width: 15%;
        }
        .table.table-striped.table-hover th.col-additional-needs,
        .table.table-striped.table-hover td.col-additional-needs {
            width: 15%;
        }
        .table.table-striped.table-hover th.col-allergies,
        .table.table-striped.table-hover td.col-allergies {
            width: 15%;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Track current report type
        let currentReport = null;

        // Hide medical condition, additional needs, and allergies columns initially
        $('.col-medical, .col-additional-needs, .col-allergies').hide();

        // Active Students button click event
        $('#activeStudents').on('click', function() {
            Swal.fire({
                title: 'Please wait, system is loading data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                type: "get",
                url: "{{ url('getReport') }}",
                dataType: "json",
                success: function(response) {
                    $('#studentsTableBody').empty();
                    $.each(response.response, function(index, activeStudent) {
                        $.each(activeStudent.details, function(i, student) {
                            $('#studentsTableBody').append(`
                                <tr>
                                    <td class="col-family-id">${student.admissionid}</td>
                                    <td class="col-name">${student.studentname}</td>
                                    <td class="col-gender">${student.studentgender}</td>
                                    <td class="col-dob">${student.studentdob}</td>
                                    <td class="col-year">${student.studentyearinschool}</td>
                                    <td class="col-medical"></td>
                                    <td class="col-additional-needs"></td>
                                    <td class="col-allergies"></td>
                                </tr>
                            `);
                        });
                    });
                    $('.col-medical, .col-additional-needs, .col-allergies').hide();
                    currentReport = 'active';
                    Swal.close();
                }
            });
        });

        // Medical Condition button click event
        $('#medicalStudents').on('click', function() {
            Swal.fire({
                title: 'Please wait, system is loading data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                type: "get",
                url: "{{ url('getMedicalReport') }}",
                dataType: "json",
                success: function(response) {
                    $('#studentsTableBody').empty();
                    $.each(response.response, function(index, activeStudent) {
                        $('#studentsTableBody').append(`
                            <tr>
                                <td class="col-family-id">${activeStudent.admissionid}</td>
                                <td class="col-name">${activeStudent.studentname}</td>
                                <td class="col-gender">${activeStudent.studentgender}</td>
                                <td class="col-dob">${activeStudent.studentdob}</td>
                                <td class="col-year">${activeStudent.studentyearinschool}</td>
                                <td class="col-medical">${activeStudent.medicalcondition || ''}</td>
                                <td class="col-additional-needs">${activeStudent.additionalNeeds || ''}</td>
                                <td class="col-allergies">${activeStudent.allergies || ''}</td>
                            </tr>
                        `);
                    });
                    $('.col-medical, .col-additional-needs, .col-allergies').show();
                    currentReport = 'medical';
                    Swal.close();
                }
            });
        });

        // Submit button click event
        $("#submit").on('click', function() {
            var family_id = $('#family_id').val();
            if (!family_id) return;

            $.ajax({
                type: "get",
                url: "{{ url('getfamilyReport') }}/" + family_id,
                dataType: "json",
                success: function(response) {
                    $('#studentsTableBody').empty();
                    $.each(response.response, function(index, activeStudent) {
                        $.each(activeStudent.details, function(i, student) {
                            $('#studentsTableBody').append(`
                                <tr>
                                    <td class="col-family-id">${student.admissionid}</td>
                                    <td class="col-name">${student.studentname}</td>
                                    <td class="col-gender">${student.studentgender}</td>
                                    <td class="col-dob">${student.studentdob}</td>
                                    <td class="col-year">${student.studentyearinschool}</td>
                                    <td class="col-medical"></td>
                                    <td class="col-additional-needs"></td>
                                    <td class="col-allergies"></td>
                                </tr>
                            `);
                        });
                    });
                    $('.col-medical, .col-additional-needs, .col-allergies').hide();
                    currentReport = 'family';
                    Swal.close();
                },
                error: function(xhr) {
                    if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.blocked) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Family ID Blocked',
                            text: 'Family ID Blocked. Please contact admin office for more details.',
                            confirmButtonColor: '#dc3545',
                        });
                        $('#studentsTableBody').empty();
                        return;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Failed to load report. Please try again.',
                    });
                }
            });
        });

        // Export to Excel button click event
        $('#exportExcel').on('click', function() {
            // Check if table is empty or no report is selected
            if ($('#studentsTableBody').children().length === 0 || !currentReport) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Report Selected',
                    text: 'Please select a report to export data.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Get table headers
            let headers = [];
            $('#studentsTable thead tr th').each(function() {
                if ($(this).is(':visible')) {
                    headers.push($(this).text().trim());
                }
            });

            // Get table data
            let data = [];
            $('#studentsTable tbody tr').each(function() {
                let row = [];
                $(this).find('td').each(function() {
                    if ($(this).is(':visible')) {
                        row.push($(this).text().trim());
                    }
                });
                if (row.length > 0) {
                    data.push(row);
                }
            });

            // Create workbook and worksheet
            let wb = XLSX.utils.book_new();
            let ws_data = [headers, ...data];
            let ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Students");

            // Set file name based on report type
            let fileName;
            if (currentReport === 'active') {
                fileName = 'Active_Students';
            } else if (currentReport === 'medical') {
                fileName = 'Medical_Condition_Students';
            } else if (currentReport === 'family') {
                fileName = 'Family_Students';
            }
            let timestamp = new Date().toISOString().replace(/[:.]/g, '-');
            XLSX.writeFile(wb, `${fileName}_${timestamp}.xlsx`);
        });
    });
</script>
@endsection
