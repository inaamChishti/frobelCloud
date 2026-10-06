@extends('layouts.branchDashboardApp')

@section('content')

    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Learner Information
                </h1>
                <p class="text-lg text-[var(--text-dark)]">View detailed information about the learner and their associated
                    data.</p>
            </div>

            <!-- Learner Data Section -->
            <div class="student-data mt-8">
                <div class="card form-section">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-user h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner Data</h2>
                    </div>
                    <div class="student-data-container p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Application ID:</strong> <span
                                    class="text-muted">{{ $meetings->id ?? 'N/A' }}</span></div>
                            <div><strong>Family ID:</strong> <span
                                    class="text-muted">{{ $meetings->family_id ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Learner Name:</strong> <span
                                    class="text-muted">{{ $meetings->learner_name ?? 'N/A' }}</span></div>
                            <div><strong>Name Template:</strong> <span
                                    class="text-muted">{{ $meetings->learner_name_template ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Staff Lead Name:</strong> <span
                                    class="text-muted">{{ $meetings->staff_lead_name ?? 'N/A' }}</span></div>
                            <div><strong>Category:</strong> <span
                                    class="text-muted">{{ $meetings->category ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Courses/Subjects:</strong> <span
                                    class="text-muted">{{ $meetings->courses_subjects_being_studied ?? 'N/A' }}</span></div>
                            <div><strong>Meeting Date:</strong> <span
                                    class="text-muted">{{ $meetings->meeting_date ? \Carbon\Carbon::parse($meetings->meeting_date)->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Career Next Steps:</strong> <span
                                    class="text-muted">{{ $meetings->career_next_steps ?? 'N/A' }}</span></div>
                            <div><strong>Interested Fields:</strong> <span
                                    class="text-muted">{{ $meetings->interested_fields ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Application Deadlines Researched:</strong> <span
                                    class="text-muted">{{ $meetings->researched_application_process_deadlines ?? 'N/A' }}</span>
                            </div>
                            <div><strong>Clear GO Information:</strong> <span
                                    class="text-muted">{{ $meetings->clear_go_information ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Helpful Information:</strong> <span
                                    class="text-muted">{{ $meetings->is_helpful_information ?? 'N/A' }}</span></div>
                            <div><strong>Started Application:</strong> <span
                                    class="text-muted">{{ $meetings->started_application ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Needs Help:</strong> <span
                                    class="text-muted">{{ $meetings->need_help_in_application ?? 'N/A' }}</span></div>
                            <div><strong>Visited Resources:</strong> <span
                                    class="text-muted">{{ $meetings->visited_our_resources_on_line ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                            <div><strong>Resources in Career Library:</strong> <span
                                    class="text-muted">{{ $meetings->resources_in_career_library ?? 'N/A' }}</span></div>
                            <div><strong>Secure Pathway:</strong> <span
                                    class="text-muted">{{ $meetings->learner_on_secure_pathway ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- IAG Learner Plans Section -->
            <div class="student-data mt-8">
                <div class="card form-section">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-clipboard-list h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">IAG Learner Plans</h2>
                    </div>
                    <div class="student-data-container p-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                            <div class="bg-[var(--secondary)] border rounded p-3">
                                <strong>Plan 1:</strong>
                                <p class="text-muted">{{ $meetings->IAG_lerner_plan_1 ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-[var(--secondary)] border rounded p-3">
                                <strong>Plan 2:</strong>
                                <p class="text-muted">{{ $meetings->IAG_lerner_plan_2 ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-[var(--secondary)] border rounded p-3">
                                <strong>Plan 3:</strong>
                                <p class="text-muted">{{ $meetings->IAG_lerner_plan_3 ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PDF Documents Section -->
            <div class="student-data mt-8">
                <div class="card form-section">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-file-pdf h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">PDF Documents</h2>
                    </div>
                    <div class="student-data-container p-4">
                        @php
                            $docsFound = false;
                        @endphp
                        @foreach (['file_input_1', 'file_input_2', 'file_input_3'] as $index => $fileField)
                            @if ($meetings->$fileField)
                                @php
                                    $files = explode(',', $meetings->$fileField);
                                    $docsFound = true;
                                @endphp
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-3">
                                    <div class="bg-[var(--secondary)] border rounded p-3">
                                        <strong>Document {{ $index + 1 }}:</strong><br>
                                        {{-- @foreach ($files as $file)
                                        <a href="{{ asset($file) }}" target="_blank" class="text-primary">Doc {{ $loop->index + 1 }}</a>{{ !$loop->last ? ', ' : '' }}
                                    @endforeach --}}
                                        @foreach ($files as $file)
                                            @php
                                                $encoded = base64_encode($file);
                                            @endphp
                                            <a href="{{ route('file.view', ['encoded' => $encoded]) }}"
                                                class="text-primary" target="_blank"> {{-- <-- Open in new tab --}}
                                                Doc {{ $loop->index + 1 }}
                                            </a>{{ !$loop->last ? ', ' : '' }}
                                        @endforeach


                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if (!$docsFound)
                            <p class="text-center text-muted">No documents found</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Term Data Section -->
            <div class="student-data mt-8">
                <div class="card form-section">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-calendar-alt h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Term Data</h2>
                    </div>
                    <div class="term-data-container p-4">
                        @if (isset($meetings->term_name) && !empty($meetings->term_name) && is_array($meetings->term_name))
                            @foreach ($meetings->term_name as $index => $term)
                                <div class="term-set mb-4 p-3 border rounded">
                                    <h4 class="term-title text-center mb-3">Term Set {{ $index + 1 }}</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                        <div><strong>Term Name:</strong> <span
                                                class="text-muted">{{ $term ?? 'N/A' }}</span></div>
                                        <div><strong>Staff Lead:</strong> <span
                                                class="text-muted">{{ $meetings->staff_lead[$index] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                        <div><strong>Date:</strong> <span
                                                class="text-muted">{{ $meetings->date[$index] ? \Carbon\Carbon::parse($meetings->date[$index])->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                        <div><strong>Meeting Notes:</strong> <span
                                                class="text-muted">{{ $meetings->meeting_notes[$index] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                        <div><strong>Secure Pathway:</strong> <span
                                                class="text-muted">{{ $meetings->secure_pathway[$index] ?? 'N/A' }}</span>
                                        </div>
                                        <div><strong>IAG Target 1:</strong> <span
                                                class="text-muted">{{ $meetings->iag_target1[$index] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                        <div><strong>Deadline:</strong> <span
                                                class="text-muted">{{ $meetings->deadline[$index] ? \Carbon\Carbon::parse($meetings->deadline[$index])->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                        <div><strong>IAG Target 2:</strong> <span
                                                class="text-muted">{{ $meetings->iag_target2[$index] ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                        <div><strong>IAG Target 3:</strong> <span
                                                class="text-muted">{{ $meetings->iag_target3[$index] ?? 'N/A' }}</span>
                                        </div>
                                        <div><strong>File:</strong>
                                            @if (isset($meetings->file[$index]) && !empty($meetings->file[$index]))
                                                <a href="{{ asset($meetings->file[$index]) }}" target="_blank"
                                                    class="text-primary">View Document</a>
                                            @else
                                                <span class="text-muted">No document available</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-center text-muted">No term data available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="flex justify-center mt-8">
                <a href="{{ url()->previous() }}" class="btn btn-primary text-base flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
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

        .registration-container .form-section {
            width: 100%;
            margin: 0;
        }

        .registration-container .form-section.active {
            border-color: var(--primary);
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
        }

        .registration-container .section-icon {
            transition: transform 0.3s ease;
        }

        .registration-container .section-icon:hover {
            transform: scale(1.2);
        }

        .registration-container .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .registration-container .scroll-smooth {
            scroll-behavior: smooth;
        }

        .registration-container .form-section {
            margin-bottom: 1.5rem;
        }

        .registration-container .grid-cols-1>div,
        .registration-container .grid-cols-2>div,
        .registration-container .grid-cols-3>div {
            padding: 0 0.5rem;
        }

        .registration-container .student-data-container {
            background: #f8fafc;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
        }

        .registration-container .student-data-card,
        .registration-container .term-set {
            background: #ffffff;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .registration-container .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }

        .registration-container .term-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 0.75rem;
        }

        .registration-container .text-muted {
            color: var(--text-light) !important;
        }

        .registration-container a.text-primary {
            color: var(--primary);
            text-decoration: underline;
        }

        .registration-container a.text-primary:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 768px) {

            .registration-container .grid-cols-2,
            .registration-container .grid-cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
