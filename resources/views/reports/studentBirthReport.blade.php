@extends('layouts.auth')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">Student Birth Report</h2>
                    </div>
                    <div class="card-body p-4">
                        <!-- Date Range Form -->
                        <form method="GET" action="{{ route('student.birth.report') }}" class="mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control"
                                        value="{{ $startDate ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control"
                                        value="{{ $endDate ?? '' }}" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                </div>
                            </div>
                        </form>

                        <!-- Data Table (Shown only if students exist) -->
                        @if (!empty($startDate) && !empty($endDate))
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Family ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">DOB</th>
                                            <th scope="col">Registered in Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($students as $student)
                                            <tr>
                                                <td>{{ $student->admissionid }}</td>
                                                <td>{{ trim($student->studentname . ' ' . ($student->studentsur ?? '')) }}
                                                </td>
                                                <td>
                                                    @if (!empty($student->studentdob) && !in_array($student->studentdob, ['1970-01-01', '//']))
                                                        {{ \Carbon\Carbon::parse($student->studentdob)->format('d/m/Y') }}
                                                    @else
                                                        Not Available
                                                    @endif
                                                </td>
                                            <td>
    @php
        $year = strtolower($student->studentyearinschool ?? 'not specified');
        $displayYear = (stripos($year, 'year') !== false) ? ucfirst($year) : ucfirst($year) . ' Year';
    @endphp
    {{ $displayYear }}
</td>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                No students found in the selected date range. Please adjust the dates and try again.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@section('styles')
    <style>
        .card-header {
            border-radius: 0.3rem 0.3rem 0 0;
        }

        .table {
            margin-bottom: 0;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
@endsection
