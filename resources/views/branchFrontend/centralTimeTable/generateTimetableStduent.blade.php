@extends('layouts.branchDashboardApp')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Base color variables for consistency */
        :root {
            --primary-color: #67C0EA; /* Main theme color */
            --primary-dark: #4A9FD5; /* Darker shade for hover/active states */
            --primary-light: #E6F3FF; /* Lighter shade for backgrounds */
            --border-color: #B3D6F2; /* Border color derived from primary */
            --text-color: #333333; /* Dark text for readability */
            --table-header-bg: #A5C9E3; /* Slightly darker for table headers */
        }

        .custom-section {
            border: 2px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
            background-color: var(--primary-light);
        }

        .custom-section .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .custom-section .form-control,
        .custom-section .form-select {
            width: 100%;
            border-color: var(--border-color);
        }

        .custom-section .form-control:focus,
        .custom-section .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(103, 192, 234, 0.25);
        }

        .custom-section .form-group {
            margin-bottom: 1rem;
        }

        .custom-section .btn {
            margin-top: 20px;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #FFFFFF; /* White text for contrast */
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: #6c757d; /* Bootstrap secondary color */
            border-color: #6c757d;
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-success {
            background-color: #28a745; /* Keep success color for "Back" button */
            border-color: #28a745;
            color: #FFFFFF;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
            color: var(--text-color);
        }

        /* Ensure no gap between weekday and weekend tables */
.weekday-table {
    margin-bottom: 0 !important;
}

.weekend-table {
    margin-top: 0 !important;
}

        .table th {
            text-align: center;
            background-color: var(--table-header-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }

        .teacher-row {
            font-weight: bold;
            text-align: center;
            background-color: var(--primary-light);
        }

        .date-row {
            font-style: italic;
            text-align: center;
            background-color: var(--primary-light);
        }

        table tbody td.time-slot {
            font-weight: bold;
            color: var(--text-color);
        }

        /* Table row alternating colors */
        .table tbody tr:nth-child(even) {
            background-color: #F0F8FF; /* AliceBlue for even rows */
        }

        .table tbody tr:nth-child(odd) {
            background-color: #EAF5FF; /* Slightly darker light blue for odd rows */
        }

        /* Ensure table borders are consistent */
        .table {
            border-collapse: collapse;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            zoom: 0.8;
        }

        .table, .table th, .table td {
            border: 1px solid var(--border-color);
        }

        /* Remove bottom border from weekday table's last row and top border from weekend table's header */
        .weekday-table tbody tr:last-child td,
        .weekday-table tbody tr:last-child th {
            border-bottom: none;
        }

        .weekend-table thead tr th {
            border-top: none;
        }

        h3 {
            color: var(--primary-dark);
        }

        /* Modal styling */
        .email-modal {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            background-color: var(--primary-light);
            border: 1px solid var(--border-color);
            box-shadow: 0 0.25rem 0.5rem rgba(103, 192, 234, 0.15);
            width: 90%;
            max-width: 400px;
            display: none;
        }

        .modal-content {
            border: none;
            background-color: var(--primary-light);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-title {
            color: var(--text-color);
        }

        .close {
            color: var(--text-color);
            opacity: 0.8;
        }

        .close:hover {
            opacity: 1;
        }

        /* Print-specific styles */
        @media print {
            .print {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .header-section img {
                max-width: 150px !important; /* Reduce logo size */
                margin: 5px auto !important;
            }

            h3 {
                font-size: 12px !important; /* Reduce title font size */
                margin-bottom: 5px !important;
            }

            .table {
                width: 100% !important;
                font-size: 8px !important; /* Reduce table font size */
                zoom: 0.6 !important; /* Scale down table */
            }

            .table th, .table td {
                border: 1px solid var(--border-color);
                padding: 4px !important; /* Reduce cell padding */
                line-height: 1.2 !important; /* Reduce line height for compact rows */
            }

            .weekday-table tbody tr:last-child td,
            .weekday-table tbody tr:last-child th {
                border-bottom: none !important;
            }

            .weekend-table thead tr th {
                border-top: none !important;
            }

            .teacher-row, .date-row {
                font-size: 8px !important; /* Match table font size */
            }

            /* Remove unnecessary elements for print */
            .action-buttons, .custom-section, .btn-success {
                display: none !important;
            }

            /* Ensure page breaks for multiple timetables */
            .print {
                page-break-after: always;
            }

            .print:last-child {
                page-break-after: avoid;
            }
        }
    </style>
    <div class="container mt-5" >
        <div class="mb-3">
            <!-- Back button -->
            <a href="{{ url('central-timetable') }}" class="btn btn-success">Back to Timetable</a>
        </div>

        <div class="custom-section">
            <form action="{{ url('generate-student-timetable') }}" method="GET" id="timetableForm">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4 col-sm-12 form-group mb-0">
                        <label for="student_name" class="form-label">Select Student</label>
                        <select id="student_name" name="student_name" class="form-select">
                            <option value="">Choose a student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->full_name }}" {{ request('student_name') == $student->full_name ? 'selected' : '' }}>
                                    {{ $student->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-12 form-group mb-0">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate ?? \Carbon\Carbon::now()->startOfWeek()->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-3 col-sm-12 form-group mb-0">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-control" value="{{ request('to_date', $toDate ?? \Carbon\Carbon::now()->endOfWeek()->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-2 col-sm-12 form-group mb-0">
                        <button type="submit" class="btn btn-primary w-100">
                            Generate Timetable
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if (!empty($mergedRecords))

            @php
            //  dd($mergedRecords);
                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

                $timetable = [];

                // Weekday & weekend slot times
                $weekdaySlotTimes = [1=>'11:00 - 01:00',2=>'01:30 - 03:30',3=>'04:30 - 06:30',4=>'06:45 - 08:45'];
                $weekendSlotTimes = [1=>'09:00 - 11:00',2=>'11:20 - 01:20',3=>'02:00 - 04:00'];

                // Fill timetable
                foreach($mergedRecords as $record) {
                    $day = \Carbon\Carbon::parse($record['date'])->format('l');
                    $teacher = $record['teacher_id'] ?? '-';

                    foreach($record['subjects'] as $subject) {
                        $slot = (int)$record['slot'];

                        if(!isset($timetable[$day])) $timetable[$day] = [];

                        // Append multiple subjects if needed
                        if(isset($timetable[$day][$slot])) {
                            $timetable[$day][$slot] .= ', '.$subject.' ('.$teacher.')';
                        } else {
                            $timetable[$day][$slot] = $subject.' ('.$teacher.')';
                        }
                    }
                }

                // Get student details from first record
                $firstRecord = $mergedRecords[0] ?? null;
                $studentName = $firstRecord['student_names'][0] ?? '-';
                $studentYear = $firstRecord['year'] ?? '-';
                $studentRef = $firstRecord['student_ids'][0] ?? '-';

                $weekdayDays = array_slice($days,0,5);
                $weekendDays = array_slice($days,5);
            @endphp

            <div class="print">
                <!-- Logo for print -->
                <div class="header-section">
                    <img style="max-width: 250px; display: block; margin: 0 auto;"
                        src="https://frobelschoolsystemnew.frobel.co.uk/img/datesheetLogo.png" alt="Datesheet Logo">
                </div>

                <!-- Timetable title with date range -->
                @php
                    $fromDateFormatted = isset($fromDate) && $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : '';
                    $toDateFormatted = isset($toDate) && $toDate ? \Carbon\Carbon::parse($toDate)->format('d M Y') : '';
                @endphp

                <!-- Student Info -->
                <div class="mb-3 text-center" style="font-weight: bold; font-size: 16px;">
                    <span>Student Name: {{ $firstName }}</span> &nbsp;  &nbsp; &nbsp;  &nbsp;
                    <span>Year: {{ $yearinshcool }}</span> &nbsp;  &nbsp; &nbsp;  &nbsp;
                    <span>Ref#: {{ $family_id }}</span>
                </div>

                <!-- Date Range Info -->
                @if($fromDateFormatted && $toDateFormatted)
                <div class="mb-3 text-center" style="font-weight: bold; font-size: 14px; color: var(--primary-dark);">
                    <span>Report Period: {{ $fromDateFormatted }} to {{ $toDateFormatted }}</span>
                </div>
                @endif

                <!-- Weekday Table -->
                <table class="table table-bordered weekday-table">
                    <thead>
                        <tr>
                            <th>DAY</th>
                            @foreach($weekdaySlotTimes as $time) <th>{{$time}}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekdayDays as $day)
                            <tr>
                                <td>{{$day}}</td>
                                @foreach($weekdaySlotTimes as $slot => $time)
                                    <td>{{ $timetable[$day][$slot] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Weekend Table -->
                <table class="table table-bordered weekend-table" style="margin-top: 0;">
                    <thead>
                        <tr>
                            <th>DAY</th>
                            @foreach($weekendSlotTimes as $time) <th>{{$time}}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekendDays as $day)
                            <tr>
                                <td>{{$day}}</td>
                                @foreach($weekendSlotTimes as $slot => $time)
                                    <td>{{ $timetable[$day][$slot] ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons for Email and Print -->
            <div class="action-buttons mt-4"
                style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <button id="emailButton" class="btn btn-secondary" style="margin-right: 10px;">Email</button>
                <button id="printButton" class="btn btn-primary" style="margin-left: 10px;">Print</button>
            </div>
        @else
            <p class="text-center" style="color: var(--text-color);">No records found for the selected student.</p>
        @endif

        <!-- Modal Structure -->
        <div id="emailModal" class="email-modal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="emailForm" method="POST" action="{{ url('send-student-email') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="emailInput">Email Address</label>
                                <input type="email" class="form-control" id="emailInput" value="{{ @$mail }}"
                                    name="email" placeholder="Enter email" required>
                            </div>
                            <!-- Hidden field to hold the timetable content -->
                            <input type="hidden" id="timetableContent" name="timetable_content"
                                value="{{ json_encode(@$mergedRecords) }}">
                            <input type="hidden" name="student_name" value="{{ @$firstName }}">
                            <input type="hidden" name="from_date" value="{{ @$fromDate }}">
                            <input type="hidden" name="to_date" value="{{ @$toDate }}">
                            <input type="hidden" name="year" value="{{ @$yearinshcool }}">
                            <input type="hidden" name="family_id" value="{{ @$family_id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="sendEmailButton" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                timer: 5000
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            toastr.error('{{ session('error') }}');
        </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle Print button click
            $("#printButton").on("click", function() {
                var printContent = $(".print").html();
                var originalContent = $("body").html();

                // Inject inline <style> tag for 50% zoom during print
                var zoomStyle = `
                    <style>
                    @media print {
                        body {
                            zoom: 0.5;
                            -webkit-transform: scale(0.9);
                            -webkit-transform-origin: top left;
                        }
                    }
                    </style>
                `;

                $("body").html(zoomStyle + printContent);
                window.print();
                $("body").html(originalContent);
                location.reload();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#emailButton').on('click', function() {
                var timetableHtml = $('.print').html();
                $('#emailModal').fadeIn();
            });

            $(document).on('click', '[data-dismiss="modal"], .close', function() {
                $('#emailModal').fadeOut();
            });

            $('#sendEmailButton').on('click', function() {
                const email = $('#emailInput').val();
                if (email) {
                    $('#emailForm').submit();
                } else {
                    alert('Please enter a valid email address.');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#student_name').select2({
                placeholder: "Choose a student",
                allowClear: true
            });

            // Set default dates to current week if not set from request
            var fromDateVal = $('#from_date').val();
            var toDateVal = $('#to_date').val();
            
            if (!fromDateVal) {
                var monday = moment().startOf('week').format('YYYY-MM-DD');
                $('#from_date').val(monday);
            }
            if (!toDateVal) {
                var sunday = moment().endOf('week').format('YYYY-MM-DD');
                $('#to_date').val(sunday);
            }

            // Auto-generate report on page load if student is selected
            // Check if we have student_name in URL but no results (means we need to auto-submit with date range)
            var urlParams = new URLSearchParams(window.location.search);
            var studentInUrl = urlParams.get('student_name');
            var fromDateInUrl = urlParams.get('from_date');
            var toDateInUrl = urlParams.get('to_date');
            var hasResults = {{ !empty($mergedRecords) ? 'true' : 'false' }};
            
            // If student is in URL but dates are not, or if student is selected but no results, auto-submit
            if (studentInUrl && (!fromDateInUrl || !toDateInUrl) && !hasResults) {
                // Ensure dates are set
                if (!fromDateInUrl) {
                    var monday = moment().startOf('week').format('YYYY-MM-DD');
                    $('#from_date').val(monday);
                }
                if (!toDateInUrl) {
                    var sunday = moment().endOf('week').format('YYYY-MM-DD');
                    $('#to_date').val(sunday);
                }
                // Small delay to ensure form is ready
                setTimeout(function() {
                    $('#timetableForm').submit();
                }, 100);
            }
        });
    </script>
@endsection
