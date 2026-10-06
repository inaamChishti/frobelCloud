@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Edit Attendance</h1>
                <p class="text-lg text-[var(--text-light)]">Update the attendance details below.</p>
            </div>

        </div>

        <!-- Form Card -->
        <div class="card">
            <form id="editAttendanceForm" action="{{ url('attendance/update') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id" value="{{ $attendance->id }}">

                <div class="row g-4">
                    <!-- Family ID Field -->
                    <div class="col-md-6">
                        <label for="family_id" class="form-label">Family ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control" id="family_id" name="family_id"
                                value="{{ old('family_id', $attendance->family_id) }}" readonly>
                        </div>
                    </div>

                    <!-- Student Name Field -->
                    <div class="col-md-6">
                        <label for="student_name" class="form-label">Student Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="student_name" name="student_name"
                                value="{{ old('student_name', $attendance->student_name) }}" readonly>
                        </div>
                    </div>

                    <!-- Subject Field -->
                    <div class="col-md-6">
                        <label for="subject_list" class="form-label">Subject</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book"></i></span>
                            <select class="form-select" id="subject_list" name="subject" required onchange="fetchTeachers()">
                                <option value="" disabled>Select subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->name }}"
                                        {{ old('subject', $attendance->subject) === $subject->name ? 'selected' : '' }}>
                                        {{ $subject->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a subject.
                            </div>
                            @error('subject')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Teacher Name Field -->
                    <div class="col-md-6">
                        <label for="current_teacher" class="form-label">Current Teacher</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-chalkboard-teacher"></i></span>
                            <input type="text" class="form-control" id="current_teacher"
                                value="{{ old('teacher_name', $attendance->teacher_name) }}" readonly>
                        </div>
                    </div>

                    <!-- Change Teacher Field -->
                    <div class="col-md-6">
                        <label for="teacher_list" class="form-label">Change Teacher</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-edit"></i></span>
                            <select class="form-select" id="teacher_list" name="teacher_name" required>
                                <option value="" disabled selected>Choose teacher</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a teacher.
                            </div>
                            @error('teacher_name')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- BK + CH Field -->
                    <div class="col-md-6">
                        <label for="bk_ch" class="form-label">BK + CH</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-book-open"></i></span>
                            <input type="text" class="form-control" id="bk_ch" name="bk_ch"
                                value="{{ old('bk_ch', $attendance->bk_ch) }}" placeholder="Enter BK + CH" required>
                            <div class="invalid-feedback">
                                Please enter BK + CH.
                            </div>
                            @error('bk_ch')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Date Field -->
                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ old('date', $attendance->date) }}" required>
                            <div class="invalid-feedback">
                                Please select a date.
                            </div>
                            @error('date')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Time Slot Field -->
                    <div class="col-md-6">
                        <label for="time_slot" class="form-label">Time Slot</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <select class="form-select" id="time_slot" name="time_slot" required>
                                <option value="" disabled>Select time</option>
                                <option value="09:00 - 11:00am"
                                    {{ old('time_slot', $attendance->time_slot) === '09:00 - 11:00am' ? 'selected' : '' }}>
                                    09:00 - 11:00am</option>
                                <option value="11:00 - 01:00pm"
                                    {{ old('time_slot', $attendance->time_slot) === '11:00 - 01:00pm' ? 'selected' : '' }}>
                                    11:00 - 01:00pm</option>
                                <option value="11:20 - 01:20pm"
                                    {{ old('time_slot', $attendance->time_slot) === '11:20 - 01:20pm' ? 'selected' : '' }}>
                                    11:20 - 01:20pm</option>
                                <option value="11:30 - 01:30pm"
                                    {{ old('time_slot', $attendance->time_slot) === '11:30 - 01:30pm' ? 'selected' : '' }}>
                                    11:30 - 01:30pm</option>
                                <option value="01:30 - 03:30pm"
                                    {{ old('time_slot', $attendance->time_slot) === '01:30 - 03:30pm' ? 'selected' : '' }}>
                                    01:30 - 03:30pm</option>
                                <option value="02:00 - 04:00pm"
                                    {{ old('time_slot', $attendance->time_slot) === '02:00 - 04:00pm' ? 'selected' : '' }}>
                                    02:00 - 04:00pm</option>
                                <option value="03:45 - 05:45pm"
                                    {{ old('time_slot', $attendance->time_slot) === '03:45 - 05:45pm' ? 'selected' : '' }}>
                                    03:45 - 05:45pm</option>
                                <option value="04:15 - 06:15pm"
                                    {{ old('time_slot', $attendance->time_slot) === '04:15 - 06:15pm' ? 'selected' : '' }}>
                                    04:15 - 06:15pm</option>
                                <option value="04:30 - 06:30pm"
                                    {{ old('time_slot', $attendance->time_slot) === '04:30 - 06:30pm' ? 'selected' : '' }}>
                                    04:30 - 06:30pm</option>
                                <option value="06:45 - 08:45pm"
                                    {{ old('time_slot', $attendance->time_slot) === '06:45 - 08:45pm' ? 'selected' : '' }}>
                                    06:45 - 08:45pm</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a time slot.
                            </div>
                            @error('time_slot')
                                <div class="error-message mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Update Attendance
                    </button>
                    <a href="{{ url('view-attendance') }}" class="btn btn-secondary">
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

    .registration-container .form-control[readonly],
    .registration-container .form-control:disabled {
        background: #f8fafc;
        opacity: 0.85;
        cursor: not-allowed;
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

<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/libs/datetimepicker/js/picker.js') }}"></script>
<script src="{{ asset('assets/libs/datetimepicker/js/picker.date.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bootstrap Form Validation
        const form = document.getElementById('editAttendanceForm');
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

        // Function to fetch teachers based on selected subject
        function fetchTeachers() {
            const selectedValue = $('#subject_list').val();
            console.log('Subject selected:', selectedValue); // Debug subject selection

            if (!selectedValue) {
                console.log('No subject selected, clearing teacher list');
                $('#teacher_list').html('<option value="" disabled selected>Choose teacher</option>');
                $('#teacher_list').addClass('is-invalid').removeClass('is-valid');
                return;
            }

            const url = '{{ route('getTeacherName', ':name') }}'.replace(':name', encodeURIComponent(selectedValue));
            console.log('Fetching teachers from:', url); // Debug URL

            $.ajax({
                url: url,
                method: 'GET',
                success: function (response) {
                    console.log('Teachers fetched:', response); // Debug response
                    const teacherList = $('#teacher_list');
                    teacherList.html('<option value="" disabled selected>Choose teacher</option>');
                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach(teacher => {
                            const selected = teacher === '{{ $attendance->teacher_name }}' ? 'selected' : '';
                            teacherList.append(`<option value="${teacher}" ${selected}>${teacher}</option>`);
                        });
                        teacherList.addClass('is-valid').removeClass('is-invalid');
                    } else {
                        console.log('No teachers found for subject:', selectedValue);
                        Toastify({
                            text: 'No teachers available for the selected subject.',
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#f59e0b",
                            stopOnFocus: true,
                        }).showToast();
                        teacherList.addClass('is-invalid').removeClass('is-valid');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText); // Debug error
                    Toastify({
                        text: 'Failed to fetch teacher list. Please try again.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                    $('#teacher_list').addClass('is-invalid').removeClass('is-valid');
                }
            });
        }

        // Trigger fetchTeachers on page load if a subject is selected
        const subjectList = $('#subject_list').val();
        if (subjectList) {
            console.log('Subject pre-selected:', subjectList); // Debug pre-selected subject
            fetchTeachers();
        }

        // Toastify notifications for session messages
        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
                stopOnFocus: true,
            }).showToast();
        @endif

        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444",
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
