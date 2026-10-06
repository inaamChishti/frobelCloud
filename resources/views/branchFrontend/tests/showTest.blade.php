@extends('layouts.branchDashboardApp')

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Student Tests</h1>
                <p>View and manage student test records for this branch.</p>
            </div>
            <button id="exportButton" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="datepicker_from" class="form-label">Date From <span class="text-danger" style="font-size: 12px;">(required)</span></label>
                    <input type="text" name="date_from"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  id="datepicker_from" class="form-control" value="{{ request('date_from') ? \Carbon\Carbon::createFromFormat('Y-m-d', request('date_from'))->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="datepicker_to" class="form-label">Date To <span class="text-danger" style="font-size: 12px;">(required)</span></label>
                    <input type="text"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  name="date_to" id="datepicker_to" class="form-control" value="{{ request('date_to') ? \Carbon\Carbon::createFromFormat('Y-m-d', request('date_to'))->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="family_id" class="form-label">Family ID</label>
                    <input type="number" name="family_id" id="family_id" class="form-control" value="{{ request('family_id') }}" min="0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="student_name" class="form-label">Student</label>
                    <select name="student_name" id="student_name" class="form-control">
                        <option value="">All Students</option>
                        @if (request('family_id') && $students->isNotEmpty())
                            @foreach ($students as $student)
        @php
            $fullName = trim(($student->studentname ?? '') . ' ' . ($student->studentsur ?? ''));
        @endphp
        <option value="{{ $fullName }}" {{ request('student_name') == $fullName ? 'selected' : '' }}>
            {{ $fullName }}
        </option>
    @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select name="subject_id" id="subject_id" class="form-control">
                        <option value="">All Subjects</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->name }}" {{ request('subject_id') == $subject->name ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3 mt-auto">
                <button type="button" id="resetFilters" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt me-2"></i>Reset Filters
                </button>
            </div>
        </div>

        <div class="table-responsive" style="zoom:0.9;">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 10%;">Family ID</th>
                        <th style="width: 15%;">Student Name</th>
                        <th style="width: 10%;">Book</th>
                        <th style="width: 10%;">Test No</th>
                        <th style="width: 8%;">Attempt</th>
                        <th style="width: 10%;">Date</th>
                        <th style="width: 10%;">Percentage</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Subject</th>
                        <th style="width: 10%;">Tutor</th>
                        <th style="width: 10%;">Updated By</th>
                        <th style="width: 17%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($student_tests as $test)
                        <tr>
                            <td class="col-family-id">{{ $test->family_id ?? 'N/A' }}</td>
                            <td class="col-student-name">{{ $test->student_name ?? 'N/A' }}</td>
                            <td class="col-book">{{ $test->book ?? 'N/A' }}</td>
                            <td class="col-test-no">{{ $test->test_no ?? 'N/A' }}</td>
                            <td class="col-attempt">{{ $test->attempt ?? 'N/A' }}</td>
                            <td class="col-date">{{ $test->test_date ? \Carbon\Carbon::parse($test->test_date)->format('d M Y') : 'N/A' }}</td>
                            <td class="col-percentage">{{ $test->percentage ?? 'N/A' }}{{ $test->percentage ? '%' : '' }}</td>
                            <td class="col-status">{{ $test->status ?? 'N/A' }}</td>
                            <td class="col-subject">{{ $test->subject ?? 'N/A' }}</td>
                            <td class="col-tutor">{{ $test->tutor ?? 'N/A' }}</td>
                            <td class="col-updated-by">{{ $test->tutor_updated_by ?? 'N/A' }}</td>
                            <td class="col-actions">
                                <div class="btn-group" role="group">
                                    <a href="{{ url('student/test/edit/' . $test->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ url('student/test/delete/' . $test->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this test?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center">No records found matching your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-3">
            {{ $student_tests->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #2046A6;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2046A6;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #2046A6;
            margin-bottom: 20px;
        }

        .form-group label {
            color: #2046A6;
            font-weight: 600;
        }

        .form-control {
            background: transparent;
            border: 1px solid #2046A6;
            color: #2046A6;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4ba8d2;
            box-shadow: 0 0 8px rgba(103, 192, 234, 0.3);
            background: rgba(103, 192, 234, 0.1);
        }

        .flatpickr-input {
            background: transparent !important;
            border: 1px solid #2046A6 !important;
            color: #2046A6 !important;
            border-radius: 8px !important;
            padding: 10px !important;
        }

        .flatpickr-input:focus {
            border-color: #4ba8d2 !important;
            box-shadow: 0 0 8px rgba(103, 192, 234, 0.3) !important;
            background: rgba(103, 192, 234, 0.1) !important;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table.table-striped.table-hover {
            --bs-table-bg: transparent;
            --bs-table-color: #2046A6;
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
            padding: 12px;
            text-align: left;
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
            padding: 12px;
            color: #2046A6;
            font-size: 14px;
            font-weight: 500;
        }

        .table.table-striped.table-hover tbody tr:hover {
            background: #2046A6 !important;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table.table-striped.table-hover tbody tr:hover td {
            color: #ffffff !important;
        }

        .table.table-striped.table-hover tbody tr:hover .btn-primary {
            background: #4ba8d2;
            color: #ffffff;
        }

        .table.table-striped.table-hover tbody tr:hover .btn-danger {
            background: #a71d2a;
            color: #ffffff;
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
            border-radius: 8px;
            padding: 12px 24px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4ba8d2;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            scale: 1.05;
        }

        .btn-primary:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: #a71d2a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            scale: 1.05;
        }

        .btn-danger:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 14px;
        }

        .btn-group .btn {
            margin-right: 8px;
            border-radius: 6px;
        }

        .alert-success {
            background-color: #28a745;
            color: #ffffff;
            border: 2px solid #1e7e34;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #dc3545;
            color: #ffffff;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 13px;
                padding: 8px;
            }
            .btn-sm {
                padding: 6px 10px;
                font-size: 12px;
            }
            .form-control {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>

    <!-- Add Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Flatpickr for date inputs
            flatpickr("#datepicker_from", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1 // Set Monday as the first day of the week
                }
            });

            flatpickr("#datepicker_to", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1 // Set Monday as the first day of the week
                }
            });

            // Filter table on input change
            $('#datepicker_from, #datepicker_to, #family_id, #student_name, #subject_id').on('change', function() {
                // If student changes, clear subject to show all tests for that student
                if (this.id === 'student_name') {
                    $('#subject_id').val('');
                }
                applyFilters();
            });

            // Fetch students by family ID
            $('#family_id').on('focusout', function() {
                let family_id = this.value;
                let studentSelect = $('#student_name');
                const currentSelectedStudent = '{{ request('student_name') }}';

                // Clear the student dropdown and add default option
                studentSelect.val('').empty().append('<option value="">All Students</option>');

                if (family_id) {
                    $.ajax({
                        url: "{{ route('get-family-students', '') }}/" + family_id,
                        method: "POST",
                        data: { family_id: family_id },
                        success: function(data) {
                            if (data && data.blocked) {
                                const blockMsg = data.message || data.error || 'Family ID Blocked. Please contact admin office for more details.';
                                toastr.error(blockMsg);
                                $('#family_id').val('');
                                studentSelect.val('').empty().append('<option value="">All Students</option>');
                                return;
                            }
                            if (data && data.length > 0) {
                               $.each(data, function(i, item) {
    const fullName = ((item.studentname ?? '') + ' ' + (item.studentsur ?? '')).trim();
    studentSelect.append($('<option>', {
        value: fullName,
        text: fullName
    }));
});

                                // Re-select previously selected student if present
                                if (currentSelectedStudent) {
                                    studentSelect.val(currentSelectedStudent);
                                }
                            } else {
                                toastr.info("No students found for this Family ID.");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            if (xhr && xhr.responseJSON && xhr.responseJSON.blocked) {
                                const blockMsg = xhr.responseJSON.message || xhr.responseJSON.error || 'Family ID Blocked. Please contact admin office for more details.';
                                toastr.error(blockMsg);
                                $('#family_id').val('');
                            } else {
                                toastr.error("Failed to fetch students. Please try again.");
                            }
                            studentSelect.val('').empty().append('<option value="">All Students</option>');
                        }
                    });
                }
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#datepicker_from, #datepicker_to, #family_id').val('');
                $('#student_name').empty().append('<option value="">All Students</option>');
                $('#subject_id').val('');
                applyFilters();
            });

            // On initial load, ensure the selected student persists even if not yet populated
            (function ensureStudentPreselect() {
                const preselectedStudent = '{{ request('student_name') }}';
                const familyId = $('#family_id').val();
                if (preselectedStudent && !$('#student_name option[value="' + preselectedStudent.replace(/"/g, '\\"') + '"]').length) {
                    // Add the preselected option so it remains visible/selected
                    $('#student_name').append(
                        $('<option>', { value: preselectedStudent, text: preselectedStudent })
                    );
                }
                if (preselectedStudent) {
                    $('#student_name').val(preselectedStudent);
                }
                // If family ID exists, trigger fetch to replace with full list and keep selection
                if (familyId) {
                    $('#family_id').trigger('focusout');
                }
            })();

            // Export button
            $('#exportButton').on('click', function(e) {
                e.preventDefault();
                let dateFrom = $('#datepicker_from').val() || '';
                let dateTo = $('#datepicker_to').val() || '';
                let familyId = $('#family_id').val() || '';
                let studentName = $('#student_name').val() || '';
                let subjectId = $('#subject_id').val() || '';

                // Convert date format from dd/mm/yyyy to yyyy-mm-dd for export
                if (dateFrom) {
                    let [day, month, year] = dateFrom.split('/');
                    dateFrom = `${year}-${month}-${day}`;
                }
                if (dateTo) {
                    let [day, month, year] = dateTo.split('/');
                    dateTo = `${year}-${month}-${day}`;
                }

                let queryString = `?date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}&family_id=${encodeURIComponent(familyId)}&student_name=${encodeURIComponent(studentName)}&subject_id=${encodeURIComponent(subjectId)}`;

                window.location.href = `{{ url('export-tests') }}${queryString}`;
            });

            function applyFilters() {
                let dateFrom = $('#datepicker_from').val() || '';
                let dateTo = $('#datepicker_to').val() || '';
                let familyId = $('#family_id').val() || '';
                let studentName = $('#student_name').val() || '';
                let subjectId = $('#subject_id').val() || '';

                // Convert date format from dd/mm/yyyy to yyyy-mm-dd for filtering
                if (dateFrom) {
                    let [day, month, year] = dateFrom.split('/');
                    dateFrom = `${year}-${month}-${day}`;
                }
                if (dateTo) {
                    let [day, month, year] = dateTo.split('/');
                    dateTo = `${year}-${month}-${day}`;
                }

                let queryString = `?date_from=${encodeURIComponent(dateFrom)}&date_to=${encodeURIComponent(dateTo)}&family_id=${encodeURIComponent(familyId)}&student_name=${encodeURIComponent(studentName)}&subject_id=${encodeURIComponent(subjectId)}`;

                window.location.href = "{{ route('student.test.index') }}" + queryString;
            }
        });

        // Toastr notifications
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>

    <!-- Add Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection
