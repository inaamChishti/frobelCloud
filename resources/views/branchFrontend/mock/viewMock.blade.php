@extends('layouts.branchDashboardApp')

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Mock Results</h1>
                <p class="text-lg text-[var(--text-dark)]">View and manage all mock results in the system.</p>
            </div>
            <a href="{{ route('export.mock.results') }}" class="btn btn-primary flex items-center">
                <i class="fas fa-file-export mr-2"></i>Export to CSV
            </a>
        </div>

        <!-- Table Section -->
        <div class="card" style="zoom:0.8;">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-table h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Mock Results Overview</h2>
            </div>
            <div class="table-responsive" >
                <table class="table"style="zoom:0.9;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Mock Type</th>
                            <th>Exam Date</th>
                            <th>Percentage</th>
                            <th>Qualifications</th>
                            <th>Tier</th>
                            <th>Fine Grade</th>
                            <th>Marked By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mocks as $mock)
                            <tr>
                                <td>{{ $mock->family_id }}</td>
                                <td>
                                    {{ $mock->name }}
                                    @if (!empty($mock->is_flagged))
                                        <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>
                                    @endif
                                </td>
                                <td>{{ $mock->subject }}</td>
                                <td>{{ $mock->mock_type }}</td>
                                <td>{{ \Carbon\Carbon::parse($mock->exam_date)->format('d M Y') }}</td>
                                <td>{{ $mock->percentage }}%</td>
                                <td>{{ $mock->qualifications }}</td>
                                <td>{{ $mock->tier }}</td>
                                <td>{{ $mock->fine_grade }}</td>
                                <td>{{ $mock->exam_marked_by }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ url('mock/edit/' . $mock->id) }}" class="btn btn-primary btn-sm flex items-center">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <button class="btn btn-danger btn-sm flex items-center" onclick="confirmDelete('{{ $mock->id }}')">
                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

    @media (max-width: 640px) {
        .registration-container .card {
            padding: 0.75rem 1rem;
            margin: 0;
        }
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

    .registration-container .table-responsive {
        border-radius: 0.5rem;
        overflow-x: auto;
    }

    .registration-container .table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        color: var(--text-dark);
        table-layout: auto;
    }

    .registration-container .table thead th {
        background: #f8fafc;
        color: var(--primary-dark);
        border-bottom: 2px solid var(--border);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .registration-container .table tbody td {
        border-bottom: 1px solid var(--border);
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-dark);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .registration-container .table tbody tr {
        transition: all 0.3s ease;
    }

    .registration-container .table tbody tr:hover {
        background: var(--secondary);
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(29, 78, 216, 0.1);
    }

    .registration-container .table tbody tr:hover td {
        color: var(--primary-dark);
    }

    .registration-container .table tbody tr:nth-child(odd) {
        background: #f9fafb;
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
        justify-content: center;
    }

    .registration-container .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
    }

    .registration-container .btn-danger {
        background: linear-gradient(45deg, #b91c1c, #ef4444);
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
        justify-content: center;
    }

    .registration-container .btn-danger:hover {
        background: linear-gradient(45deg, #991b1b, #b91c1c);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
    }

    .registration-container .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        height: 2rem;
    }

    .registration-container .section-icon {
        transition: transform 0.3s ease;
    }

    .registration-container .section-icon:hover {
        transform: scale(1.2);
    }

    @media (max-width: 768px) {
        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .registration-container .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        .registration-container .container {
            padding: 0.75rem 1rem;
        }

        .registration-container h1 {
            font-size: 1.75rem;
        }

        .registration-container p {
            font-size: 0.875rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(mockId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this mock result?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ url('mock/delete') }}/" + mockId;
        }
    });
}

    // Session Notifications
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
</script>

@endsection
