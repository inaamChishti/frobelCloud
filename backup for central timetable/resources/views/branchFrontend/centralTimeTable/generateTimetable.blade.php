@extends('layouts.branchDashboardApp')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: #6c757d; /* Keep Bootstrap's secondary color for contrast */
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-success {
            background-color: #28a745; /* Keep success color for "Back" button */
            border-color: #28a745;
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

        /* Modal styling */
        .email-modal {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            background-color: white;
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

        h3 {
            color: var(--primary-dark);
        }
    </style>
    <div class="container mt-5">
        <div class="mb-3">
            <!-- Back button -->
            <a href="{{ url('central-timetable') }}" class="btn btn-success">Back to Timetable</a>
        </div>
        <div class="custom-section">
            <form action="{{ url('generate-staff-timetable') }}" method="GET">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-sm-12 form-group">
                        <label for="teacher" class="form-label">Select Teacher</label>
                        <select id="teacher" name="teacher" class="form-select">
                            <option value="">Choose a teacher</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->teacher_name }}"
                                    @if ($teacher->teacher_name == $prev) selected @endif>
                                    {{ $teacher->teacher_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-sm-12 form-group mt-2">
                        <button type="submit" class="btn btn-primary">
                            Generate Timetable
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if (!empty($mergedRecords))
            <div class="mt-5 print">
                <div class="header-section">
                    <img style="max-width: 250px; display: block; margin: 0 auto;"
                        src="{{ asset('img/datesheetLogo.png') }}" alt="Datesheet Logo">
                </div>
                <h3 class="text-center mb-4">Teacher Timetable (Week:
                    {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} -
                    {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }})</h3>

                <table class="table table-bordered">
                    <tbody>
                        @foreach ($mergedRecords as $index => $record)
                            @php
                                $teacher_name = $record['teacher_id'];
                                $from = $fromDate;
                                $to = $toDate;
                            @endphp
                            @if ($index === 0)
                                <tr class="teacher-row">
                                    <td style="font-weight: bold;">Teacher Name</td>
                                    <td>{{ $record['teacher_id'] }}</td>
                                </tr>
                                <tr class="date-row">
                                    <td style="font-weight: bold;">Date Range</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} -
                                        {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Day</th>
                                    <th>Date</th>
                                    <th>Subjects</th>
                                    <th>Session</th>
                                </tr>
                            @endif

                            @foreach ($record['student_names'] as $index => $studentName)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($record['date'])->format('l') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($record['date'])->format('d/m/Y') }}</td>
                                    <td>{{ $record['subjects'][$index] }}</td>
                                    <td class="time-slot">
                                        @if (in_array(\Carbon\Carbon::parse($record['date'])->format('l'), ['Saturday', 'Sunday']))
                                            @switch($record['slot'])
                                                @case(1)
                                                    09:00 – 11:00
                                                @break
                                                @case(2)
                                                    11:20 – 01:20
                                                @break
                                                @case(3)
                                                    02:00 – 04:00
                                                @break
                                                @default
                                                    No Time Slot
                                            @endswitch
                                        @else
                                            @switch($record['slot'])
                                                @case(1)
                                                    11:00 – 01:00
                                                @break
                                                @case(2)
                                                    01:30 – 03:30
                                                @break
                                                @case(3)
                                                    04:30 – 06:30
                                                @break
                                                @case(4)
                                                    06:45 – 20 Growth45
                                                @break
                                                @default
                                                    No Time Slot
                                            @endswitch
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="action-buttons mt-4"
                style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <button id="emailButton" class="btn btn-secondary" style="margin-right: 10px;">Email</button>
                <button id="printButton" class="btn btn-primary" style="margin-left: 10px;">Print</button>
            </div>
        @endif

        <div id="emailModal" class="email-modal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="emailForm" method="POST" action="{{ url('send-teacher-email') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="emailInput">Email Address</label>
                                <input type="hidden" id="timetableContent" name="timetable_content"
                                    value="{{ json_encode($mergedRecords) }}">
                                <input type="email" class="form-control" id="emailInput" name="email"
                                    placeholder="Enter email" required>
                                <input type="hidden" name="teacher_name" value="{{ @$teacher_name }}">
                                <input type="hidden" name="from_date" value="{{ @$from }}">
                                <input type="hidden" name="to_date" value="{{ @$to }}">
                            </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#teacher').select2({
                placeholder: "Choose a Teacher",
                allowClear: true
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#printButton").on("click", function() {
                var printContent = $(".print").html();
                var originalContent = $("body").html();
                $("body").html(printContent);
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
@endsection
