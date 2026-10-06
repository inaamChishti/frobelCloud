@extends('layouts.branchDashboardApp')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
    <!-- Flatpickr CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Bootstrap CSS CDN (if not already included in branchDashboardApp) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

@section('content')
    <div class="main-content">
        <h1>Edit Student Test</h1>
        <p>Update the details of the student test record.</p>

        <!-- Alerts -->
        @if (Session::has('alert-success'))
            <div class="alert alert-success">
                <strong>Success!</strong> {{ Session::get('alert-success') }}.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form -->
        <div class="card">
            <div class="card-header"><h5><b>Edit Student Test</b></h5></div>
            <div class="card-body">
                <form method="POST" action="{{ url('student/test/update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $test->id }}">
                    <div class="form-group mb-3">
                        <label for="family_id" class="form-label">Family ID</label>
                        <input type="text" readonly name="family_id" id="family_id" value="{{ $test->family_id ?? old('family_id') }}" class="form-control" placeholder="Family ID">
                    </div>
                    <div class="form-group mb-3">
                        <label for="student_name" class="form-label">Student Name</label>
                        <input type="text" name="student_name" id="student_name" value="{{ $test->student_name ?? old('student_name') }}" class="form-control" placeholder="Student Name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <select name="subject" id="subject" class="form-control">
                            <option value="" disabled {{ !isset($test->subject) ? 'selected' : '' }}>Choose Option</option>
                            @if (count($subjects) > 0)
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->name }}" {{ isset($test) && $test->subject == $subject->name ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="book" class="form-label">Book</label>
                        <input type="text" name="book" value="{{ $test->book ?? old('book') }}" class="form-control" placeholder="Book Name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="test_no" class="form-label">Test No</label>
                        <input type="text" name="test_no" value="{{ $test->test_no ?? old('test_no') }}" class="form-control" placeholder="Test No">
                    </div>
                    <div class="form-group mb-3">
                        <label for="attempt" class="form-label">Attempt</label>
                        <input type="text" name="attempt" value="{{ $test->attempt ?? old('attempt') }}" class="form-control" placeholder="Attempt">
                    </div>
                    <div class="form-group mb-3">
                        <label for="date" class="form-label">Date <span id="star">(required)</span></label>
                        <input type="text" name="date" id="datee"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  value="{{ isset($test) ? \Carbon\Carbon::parse($test->test_date)->format('d/m/Y') : old('date') }}" class="form-control" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="percentage" class="form-label">Percentage</label>
                        <input type="text" name="percentage" value="{{ $test->percentage ?? old('percentage') }}" class="form-control" placeholder="Percentage">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" name="status" value="{{ $test->status ?? old('status') }}" class="form-control" placeholder="Status">
                    </div>
                    <div class="form-group mb-3">
    <label for="tutor" class="form-label">Tutor</label>
    <select name="tutor" id="tutor" class="form-control" required>
        <option value="" disabled {{ !isset($test->tutor) ? 'selected' : '' }}>Choose Option</option>
        @if (count($all_teachers) > 0)
            @foreach ($all_teachers as $teacher)
                <option value="{{ $teacher->teacher_name }}"
                    {{ isset($test) && $test->tutor == $teacher->teacher_name ? 'selected' : '' }}>
                    {{ $teacher->teacher_name }}
                </option>
            @endforeach
        @else
            <option value="" disabled>No teachers available</option>
        @endif
    </select>
</div>
                    <div class="form-group mb-3">
                        <label for="updated_by" class="form-label">Updated By</label>
                        <input type="text" name="updated_by" value="{{ $test->tutor_updated_by ?? old('updated_by') }}" class="form-control" placeholder="Updated By">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
            border: 1px solid #214BB1;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #214BB1;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #214BB1;
            margin-bottom: 20px;
        }

        .card {
            background: transparent;
            border: 1px solid #214BB1;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: #214BB1;
            color: #ffffff;
            border-bottom: 2px solid #1a3a8c;
            padding: 12px;
            border-radius: 8px 8px 0 0;
        }

        .card-header h5 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .form-group label {
            color: #214BB1;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control {
            background: transparent;
            border: 1px solid #214BB1;
            color: #214BB1;
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1a3a8c;
            box-shadow: 0 0 8px rgba(33, 75, 177, 0.3);
            background: rgba(33, 75, 177, 0.1);
        }

        .btn-primary {
            background: #214BB1;
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
            background: #1a3a8c;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
            scale: 1.05;
        }

        .btn-primary:active {
            transform: translateY(0);
            scale: 0.98;
        }

        .alert-success {
            background-color: #28a745;
            color: #ffffff;
            border: 2px solid #1e7e34;
            border-radius: 8px;
            font-weight: 500;
            position: relative;
            padding: 15px;
        }

        .alert-danger {
            background-color: #dc3545;
            color: #ffffff;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
            position: relative;
            padding: 15px;
        }

        .alert ul {
            margin: 0;
            padding-left: 20px;
        }

        .btn-close {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 14px;
            position: absolute;
            top: 15px;
            right: 15px;
            cursor: pointer;
        }

        .btn-close:hover {
            color: #f0f0f0;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
        }

        #star {
            color: red;
            font-size: 12px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .card-header h5 {
                font-size: 16px;
            }

            .form-control {
                font-size: 14px;
                padding: 8px;
            }

            .btn-primary {
                padding: 10px 20px;
                font-size: 14px;
            }
        }

        /* Flatpickr Custom Styles */
        .flatpickr-calendar {
            background: #ffffff;
            border: 1px solid #214BB1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover {
            background: #214BB1;
            border-color: #214BB1;
            color: #ffffff;
        }

        .flatpickr-day.today {
            border-color: #1a3a8c;
            background: rgba(33, 75, 177, 0.1);
        }

        .flatpickr-day:hover {
            background: rgba(33, 75, 177, 0.2);
            border-color: #1a3a8c;
        }

        .flatpickr-monthDropdown-months,
        .flatpickr-current-month span.cur-month,
        .flatpickr-weekdays .flatpickr-weekday {
            color: #214BB1;
            font-weight: 600;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            color: #214BB1;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            color: #1a3a8c;
        }
    </style>

    <!-- Bootstrap JS and Popper.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <!-- Flatpickr JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#family_id').on('focusout', function() {
                var family_id = this.value;
                $('#student_name').empty();
                $('#student_name').append('<option value="" disabled selected>Choose Option</option>');

                $.ajax({
                    url: "{{ route('get-family-students', '') }}/" + family_id,
                    method: "POST",
                    data: { family_id },
                    success: function(data) {
                        $.each(data, function(i, item) {
                            $('#student_name').append($('<option>', {
                                value: item.studentname,
                                text: item.studentname
                            }));
                        });
                    },
                    error: function() {
                        swal("Error!", "Something went wrong, Please refresh the webpage and try again, if still problem persists contact with administrator", "error");
                    }
                });
            });

            // Flatpickr Initialization
            flatpickr("#datee", {
                dateFormat: "d/m/Y",
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1 // Set Monday as the first day of the week
                }
            });

            // Toastr notifications
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (Session::has('alert-success'))
                toastr.success("{{ Session::get('alert-success') }}");
            @endif
            @if ($errors->any())
                toastr.error("Please correct the errors in the form.");
            @endif
        });
    </script>
@endsection
