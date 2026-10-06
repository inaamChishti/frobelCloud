@extends('layouts.superAdminApp')

@section('content')


<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Branch List</h1>
                <p class="text-lg text-[var(--text-light)]">View and manage all branch admins in the system.</p>
            </div>
            <a href="{{ url('create-branch') }}" class="btn-primary text-base flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>Add New Branch
            </a>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Branch Name</th>
                            <th>Branch ID</th>
                            <th>Password</th>
                            <th>Initiated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                        <tr>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->branch_name ?? 'N/A' }}</td>
                            <td>{{ $branch->branch_id ?? 'N/A' }}</td>
                            <td>{{ $branch->visible_password }}</td>
                            <td>{{ $branch->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ url('edit-branch/' . $branch->id) }}" class="btn-primary btn-sm flex items-center">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    {{-- <form action="{{ url('delete-branch/' . $branch->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm flex items-center" onclick="return confirm('Are you sure you want to delete this branch?')">
                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                        </button>
                                    </form> --}}
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
        overflow: hidden;
    }

    .registration-container .table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .registration-container .table thead th {
        background: var(--secondary);
        color: var(--primary-dark);
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid var(--border);
        text-align: left;
    }

    .registration-container .table tbody tr {
        transition: all 0.3s ease;
    }

    .registration-container .table tbody td {
        vertical-align: middle;
        padding: 0.75rem 1rem;
        color: var(--text-dark);
        font-size: 0.875rem;
        font-weight: 500;
        border-top: 1px solid var(--border);
    }

    .registration-container .table tbody tr:hover {
        background: var(--secondary) !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(29, 78, 216, 0.1);
    }

    .registration-container .table tbody tr:hover td {
        color: var(--primary-dark) !important;
    }

    .registration-container .table tbody tr:nth-of-type(odd) {
        background: #f8fafc;
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

    .registration-container .btn-danger {
        background: linear-gradient(45deg, var(--error), #f87171);
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

    .registration-container .btn-danger:hover {
        background: linear-gradient(45deg, #f87171, var(--error-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-danger:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        height: 2rem;
    }

    .registration-container .btn-group .btn {
        margin-right: 0.5rem;
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    @media (max-width: 768px) {
        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
        .registration-container .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
            height: 1.8rem;
        }
        .registration-container .btn-group .btn {
            margin-right: 0.3rem;
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

<script>
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
</script>
@endsection
