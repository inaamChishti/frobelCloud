@extends('layouts.branchDashboardApp')

@section('content')
<style>
    .btn-group .btn-primary:not(:last-child) {
        margin-right: 10px;
    }
    .filter-container .form-control {
        width: auto;
    }
    .pagination .page-item.active .page-link {
        background-color: #2046A6;
        border-color: #2046A6;
    }
    .pagination .page-link {
        color: #2046A6;
    }
    .form-group.date-input {
        margin-right: 15px;
    }
    .form-control {
        padding: 8px;
        border: 1px solid #2046A6;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
        max-width: 200px;
        text-align: center;
    }
    .form-control:focus {
        border-color: #4ba8d2;
        box-shadow: 0 0 0 0.2rem rgba(103, 192, 234, 0.25);
    }
    .form-label {
        color: #2046A6;
        font-weight: 500;
        margin-bottom: 5px;
    }
    .main-content {
        background: transparent;
        padding: 20px;
        border-radius: 8px;
        animation: fadeIn 0.6s ease-in-out;
    }
    .main-content h1 {
        font-size: 28px;
        font-weight: 600;
        color: #2046A6;
        margin-bottom: 10px;
    }
    .main-content p {
        font-size: 16px;
        color: #2046A6;
        margin-bottom: 15px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow-x: auto;
    }
    .table.table-striped.table-hover {
        --bs-table-bg: transparent;
        --bs-table-color: #67c0ea;
        --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
        --bs-table-striped-color: #2046A6;
        --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
        --bs-table-hover-color: #ffffff;
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: #2046A6;
        border-radius: 8px;
        margin-bottom: 0;
        width: 100%;
    }
    .table.table-striped.table-hover thead th {
        background: transparent;
        color: #2046A6;
        border-bottom: 2px solid #2046A6;
        font-weight: 600;
        padding: 10px;
        text-align: center;
        vertical-align: middle;
    }
    .table.table-striped.table-hover tbody {
        background: transparent;
    }
    .table.table-striped.table-hover tbody tr {
        transition: all 0.3s ease;
    }
    .table.table-striped.table-hover tbody td {
        vertical-align: middle;
        border-color: #2046A6;
        padding: 10px;
        color: #2046A6;
        font-size: 13px;
        font-weight: 500;
        text-align: center;
    }
    .table.table-striped.table-hover tbody tr:hover {
        background: #2046A6 !important;
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .table.table-striped.table-hover tbody tr:hover td {
        color: #ffffff !important;
    }
    .table.table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-color-type: #2046A6 !important;
        --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
        color: var(--bs-table-color-type) !important;
        background-color: var(--bs-table-bg-type) !important;
    }
    .btn-primary {
        background: #2046A6;
        border: none;
        border-radius: 6px;
        padding: 8px 16px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: #4ba8d2;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        scale: 1.03;
    }
    .btn-primary:active {
        transform: translateY(0);
        scale: 0.98;
    }
    .card {
        background: transparent;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.6s ease-in-out;
    }
    .card-body {
        padding: 20px;
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
            padding: 6px 12px;
            font-size: 12px;
        }
        .form-control {
            font-size: 14px;
            max-width: 100%;
        }
        .main-content {
            padding: 15px;
        }
    }
</style>

<div class="main-content" style="zoom:0.9;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Staff Reports</h1>
            <p>View and manage staff reports and sessions.</p>
        </div>
    </div>

    <div class="card mb-4" style="border: 1px solid #2046A6;">
        <div class="card-body">
            <form action="{{url('teacher-report')}}" method="GET">
                @csrf
                <div class="filter-container mb-3">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reportType" class="form-label">Report Type:</label>
                                <select class="form-control" name="report_type" id="reportType">
                                    <option value="" selected disabled>Choose an option</option>
                                    <option value="staffReport">Staff Report</option>
                                    <option value="staffSession">Staff Session</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-9" id="dateRangeInputs" style="display: none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group date-input">
                                        <label for="fromDate" class="form-label" style="font-size: 14px; color: #2046A6; font-weight: bold;">
                                            From: <span style="color: red;">(required)</span>
                                        </label>
                                        <input type="text" id="fromDate" name="fromDate" class="form-control"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
                                            placeholder="dd/mm/yyyy" required value="{{ old('fromDate') ? \Carbon\Carbon::parse(old('fromDate'))->format('d/m/Y') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group date-input">
                                        <label for="toDate" class="form-label" style="font-size: 14px; color: #2046A6; font-weight: bold;">
                                            To: <span style="color: red;">(required)</span>
                                        </label>
                                        <input type="text" id="toDate" name="toDate" class="form-control"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
                                            placeholder="dd/mm/yyyy" required value="{{ old('toDate') ? \Carbon\Carbon::parse(old('toDate'))->format('d/m/Y') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="teacherDropdown" class="form-label">Staff:</label>
                                        <select class="form-control" id="teacherDropdown" name="teacher_name">
                                            <option value="" selected disabled>Choose an option</option>
                                            @foreach ($teacherNames as $teacherName)
                                                <option value="{{ $teacherName }}" {{ old('teacher_name') == $teacherName ? 'selected' : '' }}>
                                                    {{ $teacherName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Show</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($staffReport))
    <div class="card mb-4" style="border: 1px solid #2046A6;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Staff Report Results</h5>
                <button id="exportStaffReportPdfBtn" class="btn btn-primary">Export to PDF</button>
            </div>
            <div class="table-responsive">
                <table id="staffReportTable" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Staff</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $perPage = 10;
                            $currentPage = request()->get('page', 1);
                            $items = $staffReport->slice(($currentPage - 1) * $perPage, $perPage);
                            $totalPages = ceil(count($staffReport) / $perPage);
                        @endphp

                        @foreach($items as $staff)
                        <tr>
                            <td class="text-center">{{@$staff->teacher_name}}</td>
                            <td class="text-center">{{@$staff->subject}}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($staff->date)->format('d F Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($totalPages > 1)
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        @if($currentPage > 1)
                            <li class="page-item">
                                <a class="page-link" href="?page={{ $currentPage - 1 }}" aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                </a>
                            </li>
                        @endif

                        @for($i = 1; $i <= $totalPages; $i++)
                            <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor

                        @if($currentPage < $totalPages)
                            <li class="page-item">
                                <a class="page-link" href="?page={{ $currentPage + 1 }}" aria-label="Next">
                                    <span aria-hidden="true">»</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </nav>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($condition)
    <div class="card mb-4" style="border: 1px solid #2046A6;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Staff Session Results</h5>
                <button id="exportStaffSessionPdfBtn" class="btn btn-primary">Export to PDF</button>
            </div>
            <div class="table-responsive">
                <table id="staffSessionTable" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Staff</th>
                            <th class="text-center">Total Lectures</th>
                            <th class="text-center">Attended Lectures</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{@$teacher}}</td>
                            <td class="text-center">{{@$totalTeacherCount}}</td>
                            <td class="text-center">{{@$attendence}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Include Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script>
    $(document).ready(function() {
        // Initialize Flatpickr
        flatpickr("#fromDate", {
            dateFormat: "d/m/Y",
            allowInput: true,
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1 // Set Monday as the first day of the week
            }
        });
        flatpickr("#toDate", {
            dateFormat: "d/m/Y",
            allowInput: true,
            maxDate: "today",
            locale: {
                firstDayOfWeek: 1
            }
        });

        // Show/hide date range inputs based on report type
        $("#reportType").change(function() {
            $("#dateRangeInputs").toggle($(this).val() === "staffReport" || $(this).val() === "staffSession");
        });

        // Initialize DataTables for both tables
        $('#staffReportTable').DataTable({
            order: [[1, "asc"]],
            paging: false, // Disable DataTables paging since you're handling it manually
            searching: true,
            info: true,
            responsive: true,
        });

        $('#staffSessionTable').DataTable({
            order: [[1, "asc"]],
            paging: false,
            searching: true,
            info: true,
            responsive: true,
        });

        // Function to export a table to PDF
        function exportToPDF(tableId, filename) {
            var element = document.getElementById(tableId);
            html2pdf(element, {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            });
        }

        // Bind click events to export buttons
        $('#exportStaffReportPdfBtn').on('click', function() {
            exportToPDF('staffReportTable', 'staff_report.pdf');
        });

        $('#exportStaffSessionPdfBtn').on('click', function() {
            exportToPDF('staffSessionTable', 'staff_session.pdf');
        });

        // Form submission handler to convert dates
        $('form').on('submit', function(e) {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const dateRegex = /^\d{2}\/\d{2}\/\d{4}$/;

            if (fromDate && !dateRegex.test(fromDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Please enter From Date in dd/mm/yyyy format.',
                });
                return;
            }

            if (toDate && !dateRegex.test(toDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Please enter To Date in dd/mm/yyyy format.',
                });
                return;
            }

            if (fromDate && toDate) {
                // Convert dd/mm/yyyy to yyyy-mm-dd for backend
                const formatDateForBackend = (dateStr) => {
                    if (!dateStr) return '';
                    const [day, month, year] = dateStr.split('/');
                    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                };

                const formattedFromDate = formatDateForBackend(fromDate);
                const formattedToDate = formatDateForBackend(toDate);

                if (new Date(formattedFromDate) > new Date(formattedToDate)) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'From Date cannot be later than To Date.',
                    });
                    return;
                }

                // Update form inputs with formatted dates
                $('#fromDate').val(formattedFromDate);
                $('#toDate').val(formattedToDate);
            }
        });
    });
</script>
@endsection
