@extends('layouts.branchDashboardApp')

@section('content')
<div class="main-content">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><i class="fas fa-flag me-2" style="color:#2044A2;"></i>Flag Candidate</h1>
            <p>Search by Family ID to view and flag/unflag students in this branch.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Search Form --}}
    <div class="card-section">
        <div class="section-header">
            <i class="fas fa-search me-2"></i>
            <h2>Search by Family ID</h2>
        </div>
        <form method="GET" action="{{ route('flag.candidate') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="family_id" class="form-label">Family ID</label>
                    <input
                        type="text"
                        class="form-control"
                        id="family_id"
                        name="family_id"
                        value="{{ $familyId ?? '' }}"
                        placeholder="Enter Family ID..."
                        onkeypress="if(!/\d/.test(event.key)) event.preventDefault()"
                    >
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if (isset($familyId))
        <div class="card-section">
            <div class="section-header">
                <i class="fas fa-users me-2"></i>
                <h2>Students under Family ID: <strong>{{ $familyId }}</strong></h2>
            </div>

            @if ($students->isEmpty())
                <div class="alert alert-info">
                    No students found for Family ID <strong>{{ $familyId }}</strong> in this branch.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Surname</th>
                                <th>Year in School</th>
                                <th>Status</th>
                                <th>Flag Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $index => $student)
                                <tr id="row-{{ $student->studentid }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->studentname }}</td>
                                    <td>{{ $student->studentsur ?? 'N/A' }}</td>
                                    <td>{{ $student->studentyearinschool ?? 'N/A' }}</td>
                                    <td>
                                        @if (strtolower($student->student_status) === 'active')
                                            <span class="badge-status badge-active">Active</span>
                                        @else
                                            <span class="badge-status badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td id="flag-status-{{ $student->studentid }}">
                                        @if ($student->is_flag == 1)
                                            <span class="badge-status badge-flagged">
                                                <i class="fas fa-flag me-1"></i>Flagged
                                            </span>
                                        @else
                                            <span class="badge-status badge-unflagged">
                                                <i class="far fa-flag me-1"></i>Not Flagged
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($student->is_flag == 1)
                                            <button
                                                class="btn btn-sm btn-undo"
                                                onclick="toggleFlag({{ $student->studentid }}, 'undo', this)"
                                                title="Undo Flag"
                                            >
                                                <i class="fas fa-undo me-1"></i>Undo
                                            </button>
                                        @else
                                            <button
                                                class="btn btn-sm btn-flag"
                                                onclick="toggleFlag({{ $student->studentid }}, 'flag', this)"
                                                title="Flag Candidate"
                                            >
                                                <i class="fas fa-flag me-1"></i>Flag
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

<style>
    .main-content {
        background: transparent;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border: 1px solid #2044A2;
        animation: fadeIn 0.5s ease-in-out;
    }

    .main-content h1 { font-size: 26px; font-weight: 700; color: #2044A2; margin-bottom: 6px; }
    .main-content p  { font-size: 15px; color: #2044A2; margin-bottom: 12px; }

    .card-section {
        background: transparent;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #2044A2;
        box-shadow: 0 3px 8px rgba(0,0,0,0.08);
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #2044A2;
    }

    .section-header i  { font-size: 18px; color: #2044A2; }
    .section-header h2 { font-size: 18px; font-weight: 600; color: #2044A2; margin: 0; flex-grow: 1; }

    .form-label { font-weight: 600; color: #2044A2; font-size: 14px; }

    .form-control {
        background: transparent;
        border: 2px solid #2044A2;
        color: #2044A2;
        border-radius: 6px;
        font-size: 13px;
        padding: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        background: transparent;
        border-color: #4ba8d2;
        box-shadow: 0 0 8px rgba(103,192,234,0.5);
        color: #2044A2;
    }

    .form-control::placeholder { color: #2044A2; opacity: 0.7; font-style: italic; font-size: 12px; }

    .btn-primary {
        background: #2044A2; border: none; border-radius: 6px;
        padding: 8px 16px; color: #fff; font-size: 14px; font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary:hover { background: #4ba8d2; transform: translateY(-1px); }

    /* Table */
    .table-responsive { border-radius: 6px; overflow: hidden; }

    .table.table-striped.table-hover {
        --bs-table-bg: transparent;
        --bs-table-color: #2044A2;
        --bs-table-striped-bg: rgba(103,192,234,0.1);
        --bs-table-hover-bg: rgba(103,192,234,0.2);
        color: #2044A2;
        border-color: #2044A2;
        width: 100%;
    }

    .table thead th {
        background: transparent;
        color: #2044A2;
        border-bottom: 2px solid #2044A2;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 10px;
    }

    .table tbody td {
        vertical-align: middle;
        border-color: #2044A2;
        padding: 6px 10px;
        color: #2044A2;
        font-size: 13px;
    }

    .table tbody tr:hover td { color: #fff !important; }
    .table tbody tr:hover { background: #2044A2 !important; }

    .table.table-striped > tbody > tr:nth-of-type(odd) > * {
        --bs-table-color-type: #2044A2 !important;
        --bs-table-bg-type: rgba(103,192,234,0.1) !important;
    }

    /* Status badges */
    .badge-status {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-active   { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .badge-inactive { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-flagged  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    .badge-unflagged{ background: #e2e8f0; color: #475569; border: 1px solid #cbd5e1; }

    /* Action buttons */
    .btn-flag {
        background: #dc3545; color: #fff; border: none;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        padding: 5px 10px; transition: all 0.3s ease;
    }
    .btn-flag:hover { background: #b02a37; transform: translateY(-1px); }

    .btn-undo {
        background: #6c757d; color: #fff; border: none;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        padding: 5px 10px; transition: all 0.3s ease;
    }
    .btn-undo:hover { background: #495057; transform: translateY(-1px); }

    .alert-info {
        background: rgba(103,192,234,0.15); color: #2044A2;
        border: 1px solid #2044A2; border-radius: 6px;
        padding: 10px; font-size: 14px; text-align: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleFlag(studentId, action, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Please wait...';

        fetch('{{ route("flag.candidate.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ student_id: studentId, action: action }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const statusCell = document.getElementById('flag-status-' + studentId);

                if (data.is_flag == 1) {
                    // Just flagged
                    statusCell.innerHTML = `
                        <span class="badge-status badge-flagged">
                            <i class="fas fa-flag me-1"></i>Flagged
                        </span>`;
                    btn.className = 'btn btn-sm btn-undo';
                    btn.innerHTML = '<i class="fas fa-undo me-1"></i>Undo';
                    btn.setAttribute('onclick', `toggleFlag(${studentId}, 'undo', this)`);
                    btn.setAttribute('title', 'Undo Flag');
                } else {
                    // Just unflagged
                    statusCell.innerHTML = `
                        <span class="badge-status badge-unflagged">
                            <i class="far fa-flag me-1"></i>Not Flagged
                        </span>`;
                    btn.className = 'btn btn-sm btn-flag';
                    btn.innerHTML = '<i class="fas fa-flag me-1"></i>Flag';
                    btn.setAttribute('onclick', `toggleFlag(${studentId}, 'flag', this)`);
                    btn.setAttribute('title', 'Flag Candidate');
                }

                toastr.success(data.message);
            } else {
                toastr.error(data.message || 'Something went wrong.');
            }
            btn.disabled = false;
        })
        .catch(() => {
            toastr.error('Network error. Please try again.');
            btn.disabled = false;
            btn.innerHTML = action === 'flag'
                ? '<i class="fas fa-flag me-1"></i>Flag'
                : '<i class="fas fa-undo me-1"></i>Undo';
        });
    }
</script>
@endsection
