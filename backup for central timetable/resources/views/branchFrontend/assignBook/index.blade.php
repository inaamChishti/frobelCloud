@extends('layouts.branchDashboardApp')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <div class="main-content">
        <!-- Assign Books Section -->
        <div class="interactive-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Assign Books</h1>
                    <p>Assign books to students for this branch.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger toast-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ url('store-assign-book') }}">
                @csrf
                <input type="hidden" name="book_price" id="book_price">
                <div class="row align-items-center">
                    <div class="col-12 col-md-3 mb-3">
                        <label for="students" class="form-label">Select Student</label>
                        <select id="students" class="form-control" name="student_id" required>
                            <option value="">-- Select a Student --</option>
                            @foreach ($students as $student)
                                <option
                                    value="{{ $student->studentname }} {{ $student->studentsur }}-{{ $student->admissionid }}">
                                    {{ $student->studentname }} {{ $student->studentsur }}-{{ $student->admissionid }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label for="subject" class="form-label">Select Subject</label>
                        <select id="subject" class="form-control" name="subject" required>
                            <option value="" selected>-- Select a Subject --</option>
                            {{-- @foreach ($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach --}}
                        </select>
                        @error('subject')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label for="teacher" class="form-label">Select Teacher</label>
                        <select id="teacher" class="form-control" name="teacher_name" required>
                            <option value="" selected>-- Select a Teacher --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->teacher_name }}">
                                    {{ $teacher->teacher_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <label for="book_name" class="form-label">Book Name</label>
                        <select id="book_name" class="form-control" name="book_name" required>
                            <option value="" selected>-- Select a Book --</option>
                        </select>
                        @error('book_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-3 text-center">
                        <button type="submit" class="btn btn-primary">Assign Book</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Assigned Books Section -->
        <div class="interactive-section mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Assigned Books</h1>
                    <p>View and manage assigned books for this branch.</p>
                </div>
                <a href="/subject-book-assigner" class="btn btn-primary">Subject Book Assigner</a>
            </div>

            <!-- Family ID Filter Input -->
            <div class="mb-3">
                <label for="familyFilterInput" class="form-label">Filter by Family ID</label>
                <input type="text" id="familyFilterInput" class="form-control" placeholder="Enter Family ID to filter">
            </div>

            <!-- Table for Assigned Books -->
            <div class="table-responsive">
                <table id="assignBooksTable" class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th class="col-family-id">Family ID</th>
                            <th class="col-student">Student</th>
                            <th class="col-subject">Subject</th>
                            <th class="col-teacher">Teacher</th>
                            <th class="col-book">Book Name</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignBooks as $assignBook)
                            <tr>
                                <td class="col-family-id">{{ $assignBook->family_id }}</td>
                                <td class="col-student">{{ $assignBook->student_name }}</td>
                                <td class="col-subject">{{ $assignBook->subject }}</td>
                                <td class="col-teacher">{{ @$assignBook->teacher }}</td>
                                <td class="col-book">{{ $assignBook->book }}</td>
                                <td class="col-actions">
                                    <div class="btn-group" role="group">
                                        <a href="{{ url('edit-assign-book/' . $assignBook->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('delete-assign-book/' . $assignBook->id) }}"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this book assignment?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #2047A8;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            margin: 10px;
        }

        .main-content:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 0, 235, 0.15);
        }

        .main-content h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 10px;
            text-align: center;
        }

        .main-content p {
            font-size: 1rem;
            color: #2047A8;
            margin-bottom: 15px;
            text-align: center;
        }

        .interactive-section {
            background: transparent;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #2047A8;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table.table-striped.table-hover {
            --bs-table-bg: transparent;
            --bs-table-color: #2047A8;
            --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
            --bs-table-striped-color: #2047A8;
            --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
            --bs-table-hover-color: #ffffff;
            color: var(--bs-table-color);
            background: var(--bs-table-bg);
            border-color: #2047A8;
            border-radius: 8px;
            margin-bottom: 0;
            width: 100%;
            min-width: 600px;
            /* Ensures horizontal scrolling on small screens */
        }

        .table.table-striped.table-hover thead th {
            background: transparent;
            color: #2047A8;
            border-bottom: 2px solid #2047A8;
            font-weight: 600;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table.table-striped.table-hover th.col-family-id,
        .table.table-striped.table-hover td.col-family-id {
            width: 12%;
            text-align: center;
        }

        .table.table-striped.table-hover th.col-student,
        .table.table-striped.table-hover td.col-student {
            width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table.table-striped.table-hover th.col-subject,
        .table.table-striped.table-hover td.col-subject {
            width: 15%;
            text-align: center;
        }

        .table.table-striped.table-hover th.col-teacher,
        .table.table-striped.table-hover td.col-teacher {
            width: 15%;
            text-align: center;
        }

        .table.table-striped.table-hover th.col-book,
        .table.table-striped.table-hover td.col-book {
            width: 23%;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table.table-striped.table-hover th.col-actions,
        .table.table-striped.table-hover td.col-actions {
            width: 15%;
            text-align: center;
        }

        .table.table-striped.table-hover tbody {
            background: transparent;
        }

        .table.table-striped.table-hover tbody tr {
            transition: all 0.3s ease;
        }

        .table.table-striped.table-hover tbody td {
            vertical-align: middle;
            border-color: #2047A8;
            padding: 10px;
            color: #2047A8;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .table.table-striped.table-hover tbody tr:hover {
            background: #2047A8 !important;
            transform: translateX(3px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
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
            --bs-table-color-type: #2047A8 !important;
            --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
            color: var(--bs-table-color-type) !important;
            background-color: var(--bs-table-bg-type) !important;
        }

        .btn-primary {
            background: #2047A8;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            touch-action: manipulation;
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

        .btn-danger {
            background: #dc3545;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            touch-action: manipulation;
        }

        .btn-danger:hover {
            background: #a71d2a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            scale: 1.03;
        }

        .btn-danger:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.75rem;
        }

        .btn-group .btn {
            margin-right: 6px;
            border-radius: 5px;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .form-label {
            color: #2047A8;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .form-control,
        .select2-container--default .select2-selection--single {
            border: 1px solid #2047A8;
            border-radius: 6px;
            background: transparent;
            color: #2047A8;
            font-size: 0.875rem;
            padding: 8px;
            height: 40px;
        }

        .form-control:focus,
        .select2-container--default .select2-selection--single:focus {
            border-color: #4ba8d2;
            box-shadow: 0 0 0 0.2rem rgba(103, 192, 234, 0.25);
            color: #2047A8;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #2047A8;
            line-height: 40px;
            padding-left: 10px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #2047A8;
            margin-top: -6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 0;
        }

        .select2-container {
            width: 100% !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                padding: 16px;
                margin: 8px;
            }

            .main-content h1 {
                font-size: 1.5rem;
            }

            .main-content p {
                font-size: 0.875rem;
            }

            .interactive-section {
                padding: 14px;
            }

            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 0.813rem;
                padding: 8px;
            }

            .btn-primary,
            .btn-danger {
                padding: 8px 16px;
                font-size: 0.813rem;
            }

            .form-control,
            .select2-container--default .select2-selection--single {
                font-size: 0.813rem;
                padding: 7px;
                height: 38px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 38px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 38px;
            }
        }

        @media (max-width: 992px) {
            .main-content {
                padding: 14px;
                margin: 6px;
            }

            .row.align-items-center {
                flex-direction: column;
                align-items: stretch;
            }

            .row.align-items-center .col-md-3,
            .row.align-items-center .col-12 {
                width: 100%;
                margin-bottom: 12px;
            }

            .table.table-striped.table-hover th.col-family-id,
            .table.table-striped.table-hover td.col-family-id {
                width: 15%;
            }

            .table.table-striped.table-hover th.col-student,
            .table.table-striped.table-hover td.col-student {
                width: 25%;
            }

            .table.table-striped.table-hover th.col-subject,
            .table.table-striped.table-hover td.col-subject {
                width: 15%;
            }

            .table.table-striped.table-hover th.col-teacher,
            .table.table-striped.table-hover td.col-teacher {
                width: 15%;
            }

            .table.table-striped.table-hover th.col-book,
            .table.table-striped.table-hover td.col-book {
                width: 20%;
            }

            .table.table-striped.table-hover th.col-actions,
            .table.table-striped.table-hover td.col-actions {
                width: 10%;
            }

            .btn-primary,
            .btn-danger {
                padding: 7px 14px;
                font-size: 0.75rem;
            }

            .btn-sm {
                padding: 5px 8px;
                font-size: 0.688rem;
            }

            .form-control,
            .select2-container--default .select2-selection--single {
                font-size: 0.75rem;
                padding: 6px;
                height: 36px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 36px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 12px;
                margin: 5px;
                border-radius: 8px;
            }

            .main-content h1 {
                font-size: 1.25rem;
            }

            .main-content p {
                font-size: 0.813rem;
            }

            .interactive-section {
                padding: 12px;
                margin-bottom: 16px;
            }

            .table.table-striped.table-hover {
                min-width: 500px;
            }

            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 0.688rem;
                padding: 6px;
            }

            .table.table-striped.table-hover th.col-family-id,
            .table.table-striped.table-hover td.col-family-id {
                min-width: 60px;
            }

            .table.table-striped.table-hover th.col-student,
            .table.table-striped.table-hover td.col-student {
                min-width: 100px;
            }

            .table.table-striped.table-hover th.col-subject,
            .table.table-striped.table-hover td.col-subject {
                min-width: 60px;
            }

            .table.table-striped.table-hover th.col-teacher,
            .table.table-striped.table-hover td.col-teacher {
                min-width: 60px;
            }

            .table.table-striped.table-hover th.col-book,
            .table personally.table-striped.table-hover td.col-book {
                min-width: 80px;
            }

            .table.table-striped.table-hover th.col-actions,
            .table.table-striped.table-hover td.col-actions {
                min-width: 60px;
            }

            .btn-primary,
            .btn-danger {
                padding: 6px 12px;
                font-size: 0.688rem;
            }

            .btn-sm {
                padding: 4px 6px;
                font-size: 0.625rem;
            }

            .form-control,
            .select2-container--default .select2-selection--single {
                font-size: 0.688rem;
                padding: 5px;
                height: 34px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 34px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 34px;
            }

            .form-label {
                font-size: 0.813rem;
            }

            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .d-flex.justify-content-between.align-items-center .btn-primary {
                width: 100%;
                text-align: center;
            }

            .select2-container .select2-selection--single {
                min-height: 34px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                padding-right: 24px;
            }
        }

        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
                margin: 4px;
                border-radius: 6px;
            }

            .main-content h1 {
                font-size: 1.125rem;
            }

            .main-content p {
                font-size: 0.75rem;
            }

            .interactive-section {
                padding: 10px;
                margin-bottom: 12px;
            }

            .table.table-striped.table-hover {
                min-width: 400px;
            }

            .table.table-striped.table-hover thead th,
            .table.table-striped.table-hover tbody td {
                font-size: 0.625rem;
                padding: 5px;
            }

            .table.table-striped.table-hover th.col-family-id,
            .table.table-striped.table-hover td.col-family-id {
                min-width: 50px;
            }

            .table.table-striped.table-hover th.col-student,
            .table.table-striped.table-hover td.col-student {
                min-width: 80px;
            }

            .table.table-striped.table-hover th.col-subject,
            .table.table-striped.table-hover td.col-subject {
                min-width: 50px;
            }

            .table.table-striped.table-hover th.col-teacher,
            .table.table-striped.table-hover td.col-teacher {
                min-width: 50px;
            }

            .table.table-striped.table-hover th.col-book,
            .table.table-striped.table-hover td.col-book {
                min-width: 70px;
            }

            .table.table-striped.table-hover th.col-actions,
            .table.table-striped.table-hover td.col-actions {
                min-width: 50px;
            }

            .btn-primary,
            .btn-danger {
                padding: 5px 10px;
                font-size: 0.625rem;
            }

            .btn-sm {
                padding: 3px 5px;
                font-size: 0.563rem;
            }

            .form-control,
            .select2-container--default .select2-selection--single {
                font-size: 0.625rem;
                padding: 4px;
                height: 32px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 32px;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 32px;
            }

            .form-label {
                font-size: 0.75rem;
            }

            .toast-error {
                font-size: 0.75rem;
                padding: 8px;
            }

            .select2-container .select2-selection--single {
                min-height: 32px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                padding-right: 20px;
            }
        }
    </style>

   <script>
    $(document).ready(function() {
        $('#students').select2({
            placeholder: "-- Select a Student --",
            allowClear: true,
            width: '100%'
        });
        $('#teacher').select2({
            placeholder: "-- Select a Teacher --",
            allowClear: true,
            width: '100%'
        });
        $('#subject').select2({
            placeholder: "-- Select a Subject --",
            allowClear: true,
            width: '100%'
        });
        $('#book_name').select2({
            placeholder: "-- Select a Book --",
            allowClear: true,
            width: '100%'
        });

        // Client-side filter for Family ID
        $('#familyFilterInput').on('keyup', function () {
            let filterValue = $(this).val().toLowerCase();
            $('#assignBooksTable tbody tr').each(function () {
                let familyId = $(this).find('.col-family-id').text().toLowerCase();
                if (familyId.includes(filterValue) || filterValue === '') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // AJAX to fetch books based on selected subject
        $('#subject').on('change', function() {
            let subject = $(this).val();
            let $bookSelect = $('#book_name');
            let $bookPriceInput = $('#book_price');

            // Clear existing options except the placeholder and reset price
            $bookSelect.find('option:not(:first)').remove();
            $bookSelect.prop('disabled', true).trigger('change');
            $bookPriceInput.val('');

            if (subject) {
                $.ajax({
                    url: '{{ url("get-books-by-subject") }}',
                    type: 'POST',
                    data: {
                        subject: subject,
                        _token: '{{ csrf_token() }}',
                        branch_id: '{{ session("branch_id") }}'
                    },
                    success: function(response) {
                        if (response.books && response.books.length > 0) {
                            $.each(response.books, function(index, book) {
                                // Add book option with price as data attribute
                                $bookSelect.append(
                                    $('<option></option>')
                                        .val(book.book_name)
                                        .text(book.book_name)
                                        .data('price', book.price || '') // Store price in data attribute
                                );
                            });
                            $bookSelect.prop('disabled', false).trigger('change');
                        } else {
                            toastr.warning('No books found for the selected subject.');
                            $bookSelect.val('').trigger('change');
                            $bookPriceInput.val('');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error fetching books.');
                        $bookSelect.val('').trigger('change');
                        $bookPriceInput.val('');
                    }
                });
            } else {
                $bookSelect.val('').trigger('change');
                $bookPriceInput.val('');
            }
        });

        // Update hidden price input when a book is selected
        $('#book_name').on('change', function() {
            let selectedOption = $(this).find('option:selected');
            let price = selectedOption.data('price') || '';
            $('#book_price').val(price);
        });

        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>


<script>
 $(document).ready(function() {
    // Initialize Select2 for the student dropdown
    $('#students').select2({
        placeholder: "-- Select a Student --",
        allowClear: true,
        width: '100%'
    });

    // Handle student selection change event
    $('#students').on('change', function() {
        let studentValue = $(this).val(); // Get the selected student value
        let $subjectSelect = $('#subject');
        let $bookSelect = $('#book_name');
        let $bookPriceInput = $('#book_price');

        // Clear book select and price input, but keep subject options
        $bookSelect.find('option:not(:first)').remove();
        $bookSelect.prop('disabled', true).trigger('change');
        $bookPriceInput.val('');

        if (studentValue) {
            $.ajax({
                url: '{{ url("get-student-details") }}',
                type: 'GET',
                data: {
                    student: studentValue,
                    branch_id: '{{ session("branch_id") }}'
                },
               success: function(response) {
    if (response.success && response.data && response.data.subjects) {

        // ✅ Clear old subjects (except the first default option)
        $subjectSelect.find('option:not(:first)').remove();

        // Append new subjects
        $.each(response.data.subjects, function(index, subject) {
            $subjectSelect.append(
                $('<option></option>')
                    .val(subject)
                    .text(subject)
            );
        });

        $subjectSelect.prop('disabled', false).trigger('change');
        toastr.success('Subjects loaded successfully.');
    } else {
        toastr.warning(response.message || 'No subjects found for the selected student.');
    }
}
,
                error: function(xhr) {
                    toastr.error('Error fetching student details.');
                    console.error('AJAX Error:', xhr);
                }
            });
        }
    });

    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
});
</script>
@endsection
