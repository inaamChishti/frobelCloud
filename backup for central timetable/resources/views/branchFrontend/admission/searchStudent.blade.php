@extends('layouts.branchDashboardApp')

@section('content')
    <div class="main-content">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Search Student</h1>
                <p>Find and manage student records in the system.</p>
            </div>
        </div>

        <!-- Error and Success Messages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search Form -->
        <div class="card-section">
            <div class="section-header">
                <i class="fas fa-search me-2"></i>
                <h2>Search Criteria</h2>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('admission.getadmissions') }}">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <!-- Search By Dropdown -->
                            <div class="col-md-4">
                                <label for="search-option" class="form-label">Search By</label>
                                <select class="form-select" name="option" id="search-option" required>
                                    <option value="" disabled {{ old('option', request('option')) == '' ? 'selected' : '' }}>Select an option</option>
                                    <option value="student_name" {{ old('option', request('option')) == 'student_name' ? 'selected' : '' }}>Student Name</option>
                                    <option value="parent_name" {{ old('option', request('option')) == 'parent_name' ? 'selected' : '' }}>Parent Name</option>
                                    <option value="phone" {{ old('option', request('option')) == 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="family_id" {{ old('option', request('option')) == 'family_id' ? 'selected' : '' }}>Family ID</option>
                                    <option value="Year" {{ old('option', request('option')) == 'Year' ? 'selected' : '' }}>Year</option>
                                    <option value="student_dob" {{ old('option', request('option')) == 'student_dob' ? 'selected' : '' }}>Date of Birth</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a search option.
                                </div>
                            </div>
                            <!-- Search Term Input -->
                            <div class="col-md-4">
                                <label for="search-input" class="form-label">Search Term</label>
                                <input type="text" class="form-control" id="search-input" name="search" required
                                    value="{{ old('search', request('search')) }}"
                                    placeholder="Enter search term...">
                                <div class="invalid-feedback">
                                    Please enter a search term.
                                </div>
                            </div>
                            <!-- Status Dropdown -->
                            <div class="col-md-2">
                                <label for="status-option" class="form-label">Status</label>
                                <select class="form-select" name="status" id="status-option">
                                    <option value="" {{ old('status', request('status')) == '' ? 'selected' : '' }}>All</option>
                                    <option value="active" {{ old('status', request('status')) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', request('status')) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <!-- Search Button -->
                            <div class="col-md-2 d-flex align-items-end mt-5">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Search Results Table -->
        @if (@$students && $students->count() > 0)
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-users me-2"></i>
                    <h2>Search Results</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Family ID</th>
                                <th>Name</th>
                                <th>Surname</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Years in School</th>
                                <th>Status</th>
                                <th>Status Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td>{{ $student->studentid }}</td>
                                    <td>{{ $student->admissionid }}</td>
                                    <td>
                                        {{ $student->studentname }}
                                        @if (!empty($student->is_flag) && $student->is_flag == 1)
                                            <span title="Flagged Student" style="color:red;font-size:14px;margin-left:4px;">&#x1F6A9;</span>
                                        @endif
                                    </td>
                                    <td>{{ $student->studentsur ?? 'N/A' }}</td>
                                    <td>
                                        @if (!empty($student->studentdob) && preg_match('/\d/', $student->studentdob))
                                            {{ \Carbon\Carbon::parse($student->studentdob)->format('d-F-Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $student->studentgender ?? 'N/A' }}</td>
                                    <td>{{ $student->studentyearinschool ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($student->student_status) ?? 'N/A' }}</td>
                                    <td>N/A</td> <!-- Placeholder for Status Date -->
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ url('new-admission-show', [$student->studentid, $student->admissionid]) }}"
                                                data-toggle="tooltip" title="View" class="btn btn-sm btn-success">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @php
                                                $user = auth()->user();
                                                $pagePermissions = $user && $user->permissions
                                                    ? json_decode($user->permissions->page_name, true) ?? []
                                                    : [];
                                            @endphp
                                            @if (isset($pagePermissions['admission_edit']) && $pagePermissions['admission_edit'] === 'on')
                                                <a href="{{ url('admission-editNew', $student->studentid) }}"
                                                    data-toggle="tooltip" title="Edit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif (@$students)
            <div class="card-section">
                <div class="section-header">
                    <i class="fas fa-users me-2"></i>
                    <h2>Search Results</h2>
                </div>
                <div class="alert alert-info">
                    No students found matching your search criteria.
                </div>
            </div>
        @endif
    </div>

    <!-- Styles -->
    <style>
        .main-content {
            background: transparent;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #2044A2;
        }

        .main-content h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2044A2;
            margin-bottom: 10px;
        }

        .main-content p {
            font-size: 16px;
            color: #2044A2;
            margin-bottom: 15px;
        }

        .card-section {
            background: transparent;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #2044A2;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #2044A2;
        }

        .section-header i {
            font-size: 20px;
            color: #2044A2;
            margin-right: 10px;
        }

        .section-header h2 {
            font-size: 20px;
            font-weight: 600;
            color: #2044A2;
            margin: 0;
            flex-grow: 1;
        }

        .form-label {
            font-weight: 600;
            color: #2044A2;
            font-size: 14px;
            margin-bottom: 4px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
        }

        .form-control,
        .form-select {
            background: transparent;
            border: 2px solid #2044A2;
            color: #2044A2;
            border-radius: 6px;
            font-size: 13px;
            padding: 8px;
            height: 36px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: transparent;
            border-color: #4ba8d2;
            box-shadow: 0 0 8px rgba(103, 192, 234, 0.5);
            color: #2044A2;
            transform: scale(1.01);
        }

        .form-control::placeholder {
            color: #2044A2;
            opacity: 0.7;
            font-style: italic;
            font-size: 12px;
        }

        .invalid-feedback {
            color: #ad0f0f;
            font-size: 12px;
            font-weight: 600;
            margin-top: 4px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            background: rgba(103, 192, 234, 0.15);
            padding: 3px 6px;
            border-radius: 3px;
        }

        .table-responsive {
            border-radius: 6px;
            overflow: hidden;
        }

        .table.table-striped.table-hover {
            --bs-table-bg: transparent;
            --bs-table-color: #2044A2;
            --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
            --bs-table-striped-color: #2044A2;
            --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
            --bs-table-hover-color: #ffffff;
            color: var(--bs-table-color);
            background: var(--bs-table-bg);
            border-color: #2044A2;
            border-radius: 6px;
            margin-bottom: 0;
            width: 100%;
            table-layout: fixed;
        }

        .table.table-striped.table-hover thead th {
            background: transparent;
            color: #2044A2;
            border-bottom: 2px solid #2044A2;
            font-weight: 600;
            padding: 8px 10px;
            font-size: 13px;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table.table-striped.table-hover tbody tr {
            transition: all 0.3s ease;
            height: 40px;
        }

        .table.table-striped.table-hover tbody td {
            vertical-align: middle;
            border-color: #2044A2;
            padding: 6px 10px;
            color: #2044A2;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table.table-striped.table-hover tbody tr:hover {
            background: #2044A2 !important;
            transform: translateX(3px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .table.table-striped.table-hover tbody tr:hover td {
            color: #ffffff !important;
        }

        .table.table-striped.table-hover tbody tr:hover .btn-primary {
            background: #4ba8d2;
            color: #ffffff;
        }

        .table.table-striped.table-hover tbody tr:hover .btn-success {
            background: #28a745;
            color: #ffffff;
        }

        .table.table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-color-type: #2044A2 !important;
            --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
            color: var(--bs-table-color-type) !important;
            background-color: var(--bs-table-bg-type) !important;
        }

        .btn-primary {
            background: #2044A2;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(0, sumption 0, 0.15);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #4ba8d2;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            scale: 1.03;
        }

        .btn-primary:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .btn-success {
            background: #28a745;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            scale: 1.03;
        }

        .btn-success:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .btn-sm {
            padding: 5px 8px;
            font-size: 12px;
        }

        .btn-group .btn {
            margin-right: 6px;
            border-radius: 5px;
        }

        .alert-info {
            background: rgba(103, 192, 234, 0.15);
            color: #2044A2;
            border: 1px solid #2044A2;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
            text-align: center;
        }

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

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .main-content h1 {
                font-size: 24px;
            }

            .main-content p {
                font-size: 14px;
            }

            .card-section {
                padding: 15px;
            }

            .section-header {
                margin-bottom: 10px;
            }

            .section-header i {
                font-size: 18px;
            }

            .section-header h2 {
                font-size: 18px;
            }

            .form-label {
                font-size: 12px;
            }

            .form-control,
            .form-select {
                font-size: 12px;
                padding: 6px;
                height: 32px;
            }

            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 11px;
                padding: 5px 8px;
            }

            .btn-sm {
                padding: 4px 6px;
                font-size: 10px;
            }
        }
    </style>

    <!-- Scripts -->
    <script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>
    <script>
        const searchOption = document.getElementById('search-option');
        const searchInput = document.getElementById('search-input');
        const statusOption = document.getElementById('status-option');

        // Define event listeners for search option
        function addEventListenersForOption(option) {
            searchInput.removeEventListener('keypress', alphaNumericOnly);
            searchInput.removeEventListener('keypress', numericOnly);
            if (option === 'student_name' || option === 'parent_name') {
                searchInput.addEventListener('keypress', alphaNumericOnly);
            } else if (option === 'phone' || option === 'family_id') {
                searchInput.addEventListener('keypress', numericOnly);
            } else if (option === 'student_dob') {
                window.location.href = "{{ url('student/birth/report') }}";
            }
        }

        // Initialize event listeners with the current option value
        addEventListenersForOption('{{ old('option', request('option')) }}');

        // Update event listeners on search option change
        searchOption.addEventListener('change', function () {
             searchInput.value = '';
            searchInput.removeEventListener('keypress', alphaNumericOnly);
            searchInput.removeEventListener('keypress', numericOnly);
            addEventListenersForOption(this.value);
        });

        // Alpha-numeric validator
        function alphaNumericOnly(event) {
            if (!/[a-zA-Z\s]/i.test(event.key)) {
                event.preventDefault();
            }
        }

        // Numeric validator
        function numericOnly(event) {
            if (!/\d/.test(event.key)) {
                event.preventDefault();
            }
        }

        // Toastr notifications
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
@endsection
