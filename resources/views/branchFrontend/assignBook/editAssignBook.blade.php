```blade
@extends('layouts.branchDashboardApp')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <div class="main-content" style="zoom:0.9;">
        <!-- Edit Assigned Book Section -->
        <div class="interactive-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Edit Assigned Book</h1>
                    <p>Update the assigned book details for this branch.</p>
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
            @if (session('error'))
                <div class="alert alert-danger toast-error">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ url('update-assign-book/'.$assignBook->id) }}">
                @csrf
                <input type="hidden" name="book_price" id="book_price" value="{{ $assignBook->price ?? '' }}">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-3">
                        <label for="student" class="form-label">Student</label>
                        <input type="text" name="student" readonly id="student" class="form-control" value="{{ $assignBook->student_name }}">
                        <input type="hidden" name="rec_id" readonly id="rec_id" value="{{ $assignBook->id }}">
                        <input type="hidden" name="family_id" readonly id="family_id" value="{{ $assignBook->family_id }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="subject" class="form-label">Select Subject</label>
                        <select id="subject" class="form-control" name="subject" required>
                            <option value="" {{ $assignBook->subject == '' ? 'selected' : '' }}>-- Select a Subject --</option>
                            <option value="MATH" {{ $assignBook->subject == 'MATH' ? 'selected' : '' }}>MATH</option>
                            <option value="ENGLISH" {{ $assignBook->subject == 'ENGLISH' ? 'selected' : '' }}>ENGLISH</option>
                            <option value="SCIENCE" {{ $assignBook->subject == 'SCIENCE' ? 'selected' : '' }}>SCIENCE</option>
                            <option value="PHYSICS" {{ $assignBook->subject == 'PHYSICS' ? 'selected' : '' }}>PHYSICS</option>
                            <option value="CHEMISTRY" {{ $assignBook->subject == 'CHEMISTRY' ? 'selected' : '' }}>CHEMISTRY</option>
                            <option value="BIOLOGY" {{ $assignBook->subject == 'BIOLOGY' ? 'selected' : '' }}>BIOLOGY</option>
                            <option value="E.LANGUAGE" {{ $assignBook->subject == 'E.LANGUAGE' ? 'selected' : '' }}>E.LANGUAGE</option>
                            <option value="E.LITERATURE" {{ $assignBook->subject == 'E.LITERATURE' ? 'selected' : '' }}>E.LITERATURE</option>
                            <option value="BUSINESS" {{ $assignBook->subject == 'BUSINESS' ? 'selected' : '' }}>BUSINESS</option>
                            <option value="ECONOMICS" {{ $assignBook->subject == 'ECONOMICS' ? 'selected' : '' }}>ECONOMICS</option>
                            <option value="COMPUTER SCIENCE" {{ $assignBook->subject == 'COMPUTER SCIENCE' ? 'selected' : '' }}>COMPUTER SCIENCE</option>
                            <option value="NON-VERBAL REASONING" {{ $assignBook->subject == 'NON-VERBAL REASONING' ? 'selected' : '' }}>NON-VERBAL REASONING</option>
                            <option value="VERBAL REASONING" {{ $assignBook->subject == 'VERBAL REASONING' ? 'selected' : '' }}>VERBAL REASONING</option>
                            <option value="PSYCHOLOGY" {{ $assignBook->subject == 'PSYCHOLOGY' ? 'selected' : '' }}>PSYCHOLOGY</option>
                            <option value="SOCIOLOGY" {{ $assignBook->subject == 'SOCIOLOGY' ? 'selected' : '' }}>SOCIOLOGY</option>
                            <option value="CITIZENSHIP" {{ $assignBook->subject == 'CITIZENSHIP' ? 'selected' : '' }}>CITIZENSHIP</option>
                            <option value="POLITICS" {{ $assignBook->subject == 'POLITICS' ? 'selected' : '' }}>POLITICS</option>
                            <option value="LAW" {{ $assignBook->subject == 'LAW' ? 'selected' : '' }}>LAW</option>
                            <option value="Arabic" {{ $assignBook->subject == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                            <option value="History" {{ $assignBook->subject == 'History' ? 'selected' : '' }}>History</option>
                        </select>
                        @error('subject')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="teacher" class="form-label">Select Teacher</label>
                        <select id="teacher" class="form-control" name="teacher_name" required>
                            <option value="" {{ $assignBook->teacher == '' ? 'selected' : '' }}>-- Select a Teacher --</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->teacher_name }}" {{ $assignBook->teacher == $teacher->teacher_name ? 'selected' : '' }}>
                                    {{ $teacher->teacher_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="book_name" class="form-label">Book Name</label>
                        <select id="book_name" class="form-control" name="book_name" required>
                            <option value="" >-- Select a Book --</option>
                            <!-- Book options will be populated dynamically via AJAX -->
                        </select>
                        @error('book_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mt-2 text-center">
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #2047A8;
        }

        .main-content h1 {
            font-size: 28px;
            font-weight: 600;
            color: #2047A8;
            margin-bottom: 10px;
        }

        .main-content p {
            font-size: 16px;
            color: #2047A8;
            margin-bottom: 15px;
        }

        .interactive-section {
            background: transparent;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #2047A8;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #2047A8;
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
            background: #1a3a8a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            scale: 1.03;
        }

        .btn-primary:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
        }

        .form-label {
            color: #2047A8;
            font-weight: 500;
        }

        .form-control, .select2-container--default .select2-selection--single {
            border: 1px solid #2047A8;
            border-radius: 6px;
            background: transparent;
            color: #2047A8;
        }

        .form-control:focus, .select2-container--default .select2-selection--single:focus {
            border-color: #1a3a8a;
            box-shadow: 0 0 0 0.2rem rgba(32, 71, 168, 0.25);
            color: #2047A8;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #2047A8;
            line-height: 34px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .form-control, .select2-container--default .select2-selection--single {
                font-size: 12px;
            }
            .btn-primary {
                padding: 8px 16px;
                font-size: 12px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#subject').select2({
                placeholder: "-- Select a Subject --",
                allowClear: true,
                width: '100%'
            });
            $('#teacher').select2({
                placeholder: "-- Select a Teacher --",
                allowClear: true,
                width: '100%'
            });
            $('#book_name').select2({
                placeholder: "-- Select a Book --",
                allowClear: true,
                width: '100%'
            });

            // Function to fetch books based on subject
            function fetchBooks(subject, selectedBook) {
                let $bookSelect = $('#book_name');
                let $bookPriceInput = $('#book_price');

                // Clear existing options except the placeholder
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
                                    let isSelected = (book.book_name === selectedBook) ? 'selected' : '';
                                    $bookSelect.append(
                                        $('<option></option>')
                                            .val(book.book_name)
                                            .text(book.book_name)
                                            .data('price', book.price || '')
                                            .prop('selected', isSelected)
                                    );
                                });
                                $bookSelect.prop('disabled', false).trigger('change');

                                // Set price for the selected book
                                let selectedOption = $bookSelect.find('option:selected');
                                let price = selectedOption.data('price') || '';
                                $bookPriceInput.val(price);
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
            }

            // AJAX to fetch books when subject changes
            $('#subject').on('change', function() {
                let subject = $(this).val();
                fetchBooks(subject, null); // No pre-selected book on subject change
            });

            // Update hidden price input when a book is selected
            $('#book_name').on('change', function() {
                let selectedOption = $(this).find('option:selected');
                let price = selectedOption.data('price') || '';
                $('#book_price').val(price);
            });

            // Trigger book fetch on page load with the current subject and book
            let currentSubject = $('#subject').val();
            let currentBook = '{{ $assignBook->book }}';
            if (currentSubject) {
                fetchBooks(currentSubject, currentBook);
            }

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
@endsection
```
