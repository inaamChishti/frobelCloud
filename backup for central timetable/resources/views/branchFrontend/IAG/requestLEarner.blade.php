@extends('layouts.branchDashboardApp')

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">
                Unapproved Learner Requests
            </h1>
            <p class="text-lg text-[var(--text-dark)]">
                Review and manage pending learner requests awaiting approval.
            </p>
        </div>

        <!-- Table Section -->
        <div class="card form-section">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-users h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Pending Requests</h2>
            </div>
            <div class="student-data-container p-4">
                @if ($learners->isEmpty())
                    <p class="text-center text-[var(--text-light)] text-lg">No unapproved learner requests found.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-[var(--text-dark)]">
                            <thead class="text-xs uppercase bg-[var(--secondary)] text-[var(--text-dark)]">
                                <tr>
                                    <th class="px-6 py-4">Family ID</th>
                                    <th class="px-6 py-4">Learner Name</th>
                                    <th class="px-6 py-4">Branch</th>
                                    <th class="px-6 py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($learners as $learner)
                                    <tr class="border-b hover:bg-[var(--secondary)] transition-colors duration-200">
                                        <td class="px-6 py-4">{{ $learner->family_id }}</td>
                                        <td class="px-6 py-4">{{ $learner->learner_name }}
                                        @php
                                            $reqKey = strtolower(trim((string)$learner->family_id) . '|' . trim($learner->learner_name));
                                        @endphp
                                        @if (!empty($flaggedKeys[$reqKey]))
                                            <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>
                                        @endif
                                    </td>
                                        <td class="px-6 py-4">{{ $learner->branch_name }}</td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('getLearnerDetails', $learner->id) }}"
                                               class="btn-primary inline-flex items-center text-base">
                                                <i class="fas fa-eye mr-2"></i>View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back Button -->

    </div>
</div>

<!-- Styles -->
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

    .registration-container .form-section {
        width: 100%;
        margin: 0;
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
    }

    .registration-container .section-icon {
        transition: transform 0.3s ease;
    }

    .registration-container .section-icon:hover {
        transform: scale(1.2);
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    .registration-container .student-data-container {
        background: #f8fafc;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
    }

    .registration-container .text-muted {
        color: var(--text-light) !important;
    }

    @media (max-width: 768px) {
        .registration-container table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    $(document).ready(function() {
        // Session notifications
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
    });
</script>
@endsection
