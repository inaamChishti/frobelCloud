@extends('layouts.branchDashboardApp')


    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Add Student Test</h1>
                <p class="text-lg text-[var(--text-light)]">Add a new test record for a student.</p>
            </div>

        </div>

        <!-- Form Card -->
        <div class="card">
            <form method="POST" action="{{ route('student.test.manual.store') }}" class="needs-validation" novalidate>
                @csrf
                <div class="row g-4">
                    <!-- Family ID Field -->
                    <div class="col-md-6">
                        <label for="family_id" class="form-label">Family ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="family_id" id="family_id" value="{{ old('family_id') }}"
                                class="form-control" placeholder="Enter Family ID" required>
                            @error('family_id')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Student Name Field -->
                    <div class="col-md-6">
                        <label for="student_name" class="form-label">Student Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <select name="student_name" id="student_name" class="form-select" required>
                                <option value="" disabled selected>Choose Option</option>
                            </select>
                            @error('student_name')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject Field -->
                    <div class="col-md-6">
                        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                            <select name="subject" id="subject" class="form-select" required>
                                <option value="" disabled selected>Choose Option</option>
                                @if (count($subjects) > 0)
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->name }}"
                                            {{ old('subject') === $subject->name ? 'selected' : '' }}>
                                            {{ $subject->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('subject')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Book Field -->
                    <div class="col-md-6">
                        <label for="book" class="form-label">Book <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book-open"></i></span>
                            <input type="text" name="book" value="{{ old('book') }}" class="form-control"
                                placeholder="Enter Book Name" required>
                            @error('book')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Test No Field -->
                    <div class="col-md-6">
                        <label for="test_no" class="form-label">Test No <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                            <input type="text" name="test_no" value="{{ old('test_no') }}" class="form-control"
                                placeholder="Enter Test No" required>
                            @error('test_no')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Attempt Field -->
                    <div class="col-md-6">
                        <label for="attempt" class="form-label">Attempt <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-redo"></i></span>
                            <input type="text" name="attempt" value="{{ old('attempt') }}" class="form-control"
                                placeholder="Enter Attempt" required>
                            @error('attempt')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Date Field -->
                    <div class="col-md-6">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="text" name="date" autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  id="date" value="{{ old('date') }}" class="form-control"
                                placeholder="dd/mm/yyyy" required>
                            @error('date')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Percentage Field -->
                    <div class="col-md-6">
                        <label for="percentage" class="form-label">Percentage <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                            <input type="text" name="percentage" value="{{ old('percentage') }}" class="form-control"
                                placeholder="Enter Percentage" required>
                            @error('percentage')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status Field -->
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                            <input type="text" name="status" value="{{ old('status') }}" class="form-control"
                                placeholder="Enter Status" required>
                            @error('status')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Tutor Field -->
                   <!-- Tutor Field -->
<div class="col-md-6">
    <label for="tutor" class="form-label">Tutor <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
        <select name="tutor" id="tutor" class="form-select" required>
            <option value="" disabled selected>Choose Option</option>
            @if (count($all_teachers) > 0)
                @foreach ($all_teachers as $teacher)
                    <option value="{{ $teacher->teacher_name }}"
                        {{ old('tutor') === $teacher->teacher_name ? 'selected' : '' }}>
                        {{ $teacher->teacher_name }}
                    </option>
                @endforeach
            @else
                <option value="" disabled>No teachers available</option>
            @endif
        </select>
        @error('tutor')
            <div class="error-message mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

                    <!-- Updated By Field -->
                    <div class="col-md-6">
                        <label for="updated_by" class="form-label">Updated By <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-edit"></i></span>
                            <input type="text" name="updated_by" value="{{ old('updated_by') }}" class="form-control"
                                placeholder="Enter Updated By" required>
                            @error('updated_by')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Test
                    </button>
                    <a href="{{ url('student-tests') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .registration-container {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --primary-light: #93c5fd;
        --secondary: #e0f2fe;
        --accent: #3b82f6;
        --text-dark: #111827;
        --text-light: #6b7280;
        --border: #bfdbfe;
        --background: #f1f5f9;
        --error: #ef4444;
        --error-light: #fee2e2;
    }

    .registration-container .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 3rem;
    }

    @media (max-width: 768px) {
        .registration-container .container {
            padding: 0.75rem 2rem;
        }
    }

    @media (max-width: 640px) {
        .registration-container .container {
            padding: 0.5rem 1.5rem;
        }
    }

    .registration-container .card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.1);
        padding: 1.25rem 1.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        position: relative;
        margin: 0;
    }

    .registration-container .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        transition: height 0.3s ease;
    }

    .registration-container .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(29, 78, 216, 0.15);
        border-color: var(--primary-light);
    }

    .registration-container .card:hover::before {
        height: 5px;
    }

    .registration-container .form-label {
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.875rem;
    }

    .registration-container .form-control,
    .registration-container .form-select {
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
        background: #ffffff;
        color: var(--text-dark);
    }

    .registration-container .form-control:focus,
    .registration-container .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .registration-container .form-control::placeholder {
        color: var(--text-light);
        opacity: 0.8;
    }

    .registration-container .input-group-text {
        background: var(--secondary);
        color: var(--primary-dark);
        border: 1px solid var(--border);
        border-right: none;
        border-radius: 0.5rem 0 0 0.5rem;
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
    }

    .registration-container .error-message,
    .registration-container .invalid-feedback {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .registration-container .btn-primary {
        background: linear-gradient(45deg, var(--primary-dark), var(--primary));
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        height: 2.5rem;
        display: flex;
        align-items: center;
    }

    .registration-container .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-primary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-secondary {
        background: var(--text-light);
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        height: 2.5rem;
        display: flex;
        align-items: center;
    }

    .registration-container .btn-secondary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-secondary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .row {
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }

    .registration-container .row > * {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    @media (max-width: 768px) {
        .registration-container .container {
            padding: 0.75rem 2rem;
        }
        .registration-container .form-label,
        .registration-container .form-control,
        .registration-container .form-select,
        .registration-container .input-group-text {
            font-size: 0.75rem;
        }
        .registration-container .btn-primary,
        .registration-container .btn-secondary {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            height: 2rem;
        }
    }

    @media (max-width: 640px) {
        .registration-container .container {
            padding: 0.5rem 1.5rem;
        }
        .registration-container .card {
            padding: 0.75rem 1rem;
        }
    }
</style>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#date", {
            dateFormat: "d/m/Y",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1 // Set Monday as the first day of the week
            }
        });
        // Bootstrap Form Validation
        const form = document.querySelector('.needs-validation');
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                Toastify({
                    text: 'Please fill out all required fields correctly.',
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            } else {
                form.classList.add('was-validated');
            }
        }, false);

        // AJAX for fetching student names based on family ID
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

      $('#family_id').on('focusout', function () {
            const family_id = this.value.trim();
            const studentSelect = $('#student_name');
            studentSelect.empty();
            studentSelect.append('<option value="" disabled selected>Choose Option</option>');

            if (family_id) {
                $.ajax({
                    url: "{{ route('get-family-students', '') }}/" + encodeURIComponent(family_id),
                    method: "POST",
                    data: { family_id },
                    success: function (data) {
                        if (Array.isArray(data) && data.length > 0) {
                            $.each(data, function (i, item) {
                                // Concatenate studentname and studentsur, handling null studentsur
                                const fullName = item.studentsur
                                    ? `${item.studentname} ${item.studentsur}`
                                    : item.studentname;
                                studentSelect.append($('<option>', {
                                    value: fullName,
                                    text: fullName
                                }));
                            });
                            studentSelect.addClass('is-valid').removeClass('is-invalid');
                        } else {
                            Toastify({
                                text: 'No students found for this Family ID.',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#f59e0b",
                                stopOnFocus: true,
                            }).showToast();
                            studentSelect.addClass('is-invalid').removeClass('is-valid');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', status, error, xhr.responseText);
                        Toastify({
                            text: 'Failed to fetch student names. Please try again or contact the administrator.',
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                        studentSelect.addClass('is-invalid').removeClass('is-valid');
                    }
                });
            }
        });

        // Toastify notifications for session messages
        @if (Session::has('alert-success'))
            Toastify({
                text: "{{ Session::get('alert-success') }}",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
                stopOnFocus: true,
            }).showToast();
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toastify({
                    text: "{{ $error }}",
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            @endforeach
        @endif
    });
</script>
@endsection
