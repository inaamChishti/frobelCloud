@extends('layouts.branchDashboardApp')

@section('content')

<div class="registration-container scroll-smooth">
    <div class="container">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">
                {{ isset($learner) ? 'Learner Request Details' : 'Learner Request' }}
            </h1>
        </div>

        <form id="addLearnerForm" action="{{ isset($learner) ? route('approveLearnerRequest', $learner->id) : route('storeLearnerRequest') }}" method="POST" enctype="multipart/form-data" class="space-y-8 mt-8" novalidate>
            @csrf
            @if (isset($learner))
                <input type="hidden" name="learner_id" value="{{ $learner->id }}">
            @endif

            <!-- Learner Information Card -->
            <div id="learner-info" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-user h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner Information</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Branch Dropdown Field -->
                    {{-- <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Select Branch *</label>
                        <div class="relative">
                            <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="branch_id" name="branch_id" class="input-field pl-10" required>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select a branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}" {{ isset($learner) && $learner->branch_id == $branch->branch_id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message" id="branch_id_feedback">Please select a branch.</div>
                        </div>
                    </div> --}}

                    <!-- Family ID Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID *</label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="family_id" name="family_id" class="input-field pl-10" placeholder="Enter family ID" value="{{ isset($learner) ? $learner->family_id : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message" id="family_id_feedback">Please enter a valid family ID.</div>
                            @error('family_id')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Learner Name (Select) Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Select Name *</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="learner_name" name="learner_name" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select Name *</option>
                                @if (isset($learner))
                                    <option value="{{ $learner->learner_name }}" selected>{{ $learner->learner_name }}</option>
                                @endif
                            </select>
                            <div class="error-message" id="learner_name_feedback">Please select a learner.</div>
                        </div>
                    </div>

                    <!-- Learner Name (Text) Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner Name *</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="learner_name_template" name="learner_name_template" class="input-field pl-10" placeholder="Enter learner name" value="{{ isset($learner) ? $learner->learner_name_template : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please enter the learner's name.</div>
                        </div>
                    </div>

                    <!-- Staff Lead Name Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Staff Lead Name *</label>
                        <div class="relative">
                            <i class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="staff_lead_name" name="staff_lead_name" class="input-field pl-10" placeholder="Enter staff lead name" value="{{ isset($learner) ? $learner->staff_lead_name : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please enter the staff lead name.</div>
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Category *</label>
                        <div class="relative">
                            <i class="fas fa-list absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="category" name="category" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select Category</option>
                                <option value="KS4" {{ isset($learner) && $learner->category == 'KS4' ? 'selected' : '' }}>KS4</option>
                                <option value="KS5" {{ isset($learner) && $learner->category == 'KS5' ? 'selected' : '' }}>KS5</option>
                                <option value="Adult Learner" {{ isset($learner) && $learner->category == 'Adult Learner' ? 'selected' : '' }}>Adult Learner</option>
                            </select>
                            <div class="error-message">Please select a category.</div>
                        </div>
                    </div>

                    <!-- Courses/Subjects Being Studied Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Courses/Subjects Being Studied *</label>
                        <div class="relative">
                            <i class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="courses_subjects_being_studied" name="courses_subjects_being_studied" class="input-field pl-10" placeholder="Enter courses/subjects" value="{{ isset($learner) ? $learner->courses_subjects_being_studied : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please enter the courses or subjects being studied.</div>
                        </div>
                    </div>

                    <!-- Meeting Date Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Date of Meeting *</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="date" id="meeting_date" name="meeting_date" class="input-field pl-10" value="{{ isset($learner) ? $learner->meeting_date : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please select a meeting date.</div>
                        </div>
                    </div>

                    <!-- Career Next Steps Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you decided upon your career next steps? *</label>
                        <div class="relative">
                            <i class="fas fa-map-signs absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="career_next_steps" name="career_next_steps" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->career_next_steps == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->career_next_steps == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ isset($learner) && $learner->career_next_steps == 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Interested Fields Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">What field/s are you interested in? *</label>
                        <div class="relative">
                            <i class="fas fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="interested_fields" name="interested_fields" class="input-field pl-10" placeholder="Enter interested fields" value="{{ isset($learner) ? $learner->interested_fields : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please enter the fields of interest.</div>
                        </div>
                    </div>

                    <!-- Researched Application Process Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you researched the application process and deadlines? *</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="researched_application_process_deadlines" name="researched_application_process_deadlines" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->researched_application_process_deadlines == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->researched_application_process_deadlines == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ isset($learner) && $learner->researched_application_process_deadlines == 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Clear on Information Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Are you clear on where to go for information? *</label>
                        <div class="relative">
                            <i class="fas fa-info-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="clear_go_information" name="clear_go_information" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->clear_go_information == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->clear_go_information == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ isset($learner) && $learner->clear_go_information == 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Helpful Information Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">What other information would you find helpful? *</label>
                        <div class="relative">
                            <i class="fas fa-question-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="is_helpful_information" name="is_helpful_information" class="input-field pl-10" placeholder="Enter helpful information needed" value="{{ isset($learner) ? $learner->is_helpful_information : '' }}" required {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please enter the information needed.</div>
                        </div>
                    </div>

                    <!-- Started Application Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you started the application process? *</label>
                        <div class="relative">
                            <i class="fas fa-play absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="started_application" name="started_application" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->started_application == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->started_application == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Need Help in Application Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Do you need any resources to help with applications? *</label>
                        <div class="relative">
                            <i class="fas fa-tools absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="need_help_in_application" name="need_help_in_application" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->need_help_in_application == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->need_help_in_application == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Visited Resources Online Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you visited our resources online? *</label>
                        <div class="relative">
                            <i class="fas fa-globe absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="visited_our_resources_on_line" name="visited_our_resources_on_line" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->visited_our_resources_on_line == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->visited_our_resources_on_line == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Resources in Career Library Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you used the resources in our careers library? *</label>
                        <div class="relative">
                            <i class="fas fa-book-open absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="resources_in_career_library" name="resources_in_career_library" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->resources_in_career_library == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->resources_in_career_library == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- IAG Action Plans Card -->
            <div id="iag-plans" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-list-ol h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner IAG Action Plans</h2>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <!-- IAG Learner Plan 1 Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 1 *</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_1" name="IAG_lerner_plan_1" class="input-field pl-10" rows="3" placeholder="Enter action plan 1 details" required {{ !isset($learner) ? 'disabled' : '' }}>{{ isset($learner) ? $learner->IAG_lerner_plan_1 : '' }}</textarea>
                            <div class="error-message">Please enter action plan 1 details.</div>
                        </div>
                    </div>

                    <!-- IAG Learner Plan 2 Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 2 *</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_2" name="IAG_lerner_plan_2" class="input-field pl-10" rows="3" placeholder="Enter action plan 2 details" required {{ !isset($learner) ? 'disabled' : '' }}>{{ isset($learner) ? $learner->IAG_lerner_plan_2 : '' }}</textarea>
                            <div class="error-message">Please enter action plan 2 details.</div>
                        </div>
                    </div>

                    <!-- IAG Learner Plan 3 Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 3 *</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_3" name="IAG_lerner_plan_3" class="input-field pl-10" rows="3" placeholder="Enter action plan 3 details" required {{ !isset($learner) ? 'disabled' : '' }}>{{ isset($learner) ? $learner->IAG_lerner_plan_3 : '' }}</textarea>
                            <div class="error-message">Please enter action plan 3 details.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pathway and Documents Card -->
            <div id="pathway-documents" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-check-circle h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Pathway and Documents</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Learner on Secure Pathway Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Is learner on secure pathway? *</label>
                        <div class="relative">
                            <i class="fas fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="learner_on_secure_pathway" name="learner_on_secure_pathway" class="input-field pl-10" required {{ !isset($learner) ? 'disabled' : '' }}>
                                <option value="" disabled {{ !isset($learner) ? 'selected' : '' }}>Select an option</option>
                                <option value="Yes" {{ isset($learner) && $learner->learner_on_secure_pathway == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ isset($learner) && $learner->learner_on_secure_pathway == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="grid grid-cols-1 gap-4 mt-4">
                    <!-- File Upload 1 -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 1 {{ isset($learner) ? '(Optional - Replace Existing)' : '*' }}</label>
                        <div class="relative">
                            {{-- <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i> --}}
                            @if (isset($learner) && $learner->file_input_1)
                                <div class="mb-2">
                                    <strong>Existing Files:</strong><br>
                                    @foreach (explode(',', $learner->file_input_1) as $file)
                                        @php
                                            $encoded = base64_encode($file);
                                        @endphp
                                        <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="btn-primary inline-flex items-center text-sm px-3 py-1 mr-2 mb-2">
                                            <i class="fas fa-eye mr-2"></i> View Doc {{ $loop->index + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" id="file_upload_one" name="file_upload_one[]" class="input-field pl-10" accept="application/pdf" multiple {{ !isset($learner) ? 'required disabled' : '' }}>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                    </div>

                    <!-- File Upload 2 -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 2 (Optional)</label>
                        <div class="relative">
                            {{-- <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i> --}}
                            @if (isset($learner) && $learner->file_input_2)
                                <div class="mb-2">
                                    <strong>Existing Files:</strong><br>
                                    @foreach (explode(',', $learner->file_input_2) as $file)
                                        @php
                                            $encoded = base64_encode($file);
                                        @endphp
                                        <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="btn-primary inline-flex items-center text-sm px-3 py-1 mr-2 mb-2">
                                            <i class="fas fa-eye mr-2"></i> View Doc {{ $loop->index + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" id="file_upload_two" name="file_upload_two[]" class="input-field pl-10" accept="application/pdf" multiple {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                    </div>

                    <!-- File Upload 3 -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 3 (Optional)</label>
                        <div class="relative">
                            {{-- <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i> --}}
                            @if (isset($learner) && $learner->file_input_3)
                                <div class="mb-2">
                                    <strong>Existing Files:</strong><br>
                                    @foreach (explode(',', $learner->file_input_3) as $file)
                                        @php
                                            $encoded = base64_encode($file);
                                        @endphp
                                        <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="btn-primary inline-flex items-center text-sm px-3 py-1 mr-2 mb-2">
                                            <i class="fas fa-eye mr-2"></i> View Doc {{ $loop->index + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <input type="file" id="file_upload_three" name="file_upload_three[]" class="input-field pl-10" accept="application/pdf" multiple {{ !isset($learner) ? 'disabled' : '' }}>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                    </div>

                    <!-- Secure Pathway Definition -->
                    <div class="col-span-1 sm:col-span-2">
                        <div class="bg-[var(--secondary)] p-4 rounded-lg text-center">
                            <strong class="text-[var(--primary-dark)]">Definition of Secure:</strong><br>
                            <span class="text-[var(--text-dark)] text-sm">Learner understands how to access and make effective use of IAG resources AND Learner is confident with next steps for short, medium, and long-term careers' ideas and planning.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit/Approve Button -->
            <div class="flex justify-center mt-8 gap-4">
                @if (isset($learner))
                    <button type="submit" class="btn-primary text-base flex items-center">
                        <i class="fas fa-check mr-2"></i>Approve Request
                    </button>
                @else
                    <button type="submit" class="btn-primary text-base flex items-center" disabled>
                        <i class="fas fa-save mr-2"></i>Save Learner
                    </button>
                @endif
            </div>
        </form>
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

    .registration-container .input-field.valid {
        border-color: #10b981;
        background-color: #ecfdf5;
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

    .registration-container .input-field {
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem 0.5rem 2.5rem;
        background: #ffffff;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        width: 100%;
        box-sizing: border-box;
        height: 2.5rem;
    }

    .registration-container .input-field:focus,
    .registration-container .input-field:hover {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .registration-container .input-field.error {
        border-color: var(--error);
        background-color: var(--error-light);
    }

    .registration-container .input-field::placeholder {
        color: var(--text-light);
        opacity: 0.7;
        font-style: normal;
    }

    .registration-container .relative i {
        font-size: 0.875rem;
        z-index: 1;
    }

    .registration-container .error-message {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
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
        display: inline-flex;
        align-items: center;
    }

    .registration-container .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
    }

    .registration-container .btn-primary:disabled {
        background: #d1d5db;
        cursor: not-allowed;
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
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    .registration-container .form-section {
        margin-bottom: 1.5rem;
    }

    .registration-container .grid-cols-1 > div,
    .registration-container .grid-cols-2 > div {
        padding: 0 0.5rem;
    }

    .registration-container textarea.input-field {
        height: auto;
        resize: vertical;
        min-height: 4rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Document ready, initializing form validation...');

        const form = document.getElementById('addLearnerForm');
        const fields = form.querySelectorAll('.input-field');

        // Real-time validation for inputs, selects, and textareas
        fields.forEach(field => {
            field.addEventListener('input', validateField);
            field.addEventListener('change', validateField);
            field.addEventListener('blur', validateField);
        });

        function validateField(e) {
            const field = e.target;
            let isValid = true;
            let errorMessage = '';

            const label = field.parentElement.previousElementSibling;
            const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

            if (!field.value || field.value.trim() === '') {
                if (field.hasAttribute('required')) {
                    isValid = false;
                    errorMessage = `Please enter a ${labelText}`;
                }
            } else if (field.type === 'file' && field.files.length > 0) {
                const validTypes = ['application/pdf'];
                const files = Array.from(field.files);
                if (!files.every(file => validTypes.includes(file.type))) {
                    isValid = false;
                    errorMessage = 'Only PDF files are allowed.';
                }
            }

            const errorElement = field.parentElement.nextElementSibling?.classList.contains('error-message')
                ? field.parentElement.nextElementSibling
                : field.parentElement.parentElement.nextElementSibling;

            if (isValid) {
                field.classList.remove('error');
                field.classList.add('valid');
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.style.display = 'none';
                }
            } else {
                field.classList.remove('valid');
                field.classList.add('error');
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.textContent = errorMessage;
                    errorElement.style.display = 'block';
                }
            }
        }

        // Form Submission Validation
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submit event triggered');

            // Reset validation states
            fields.forEach(field => field.classList.remove('error', 'valid'));
            document.querySelectorAll('.registration-container .error-message').forEach(msg => msg.style.display = 'none');

            let isValid = true;
            let firstErrorElement = null;
            let errorMessages = [];

            fields.forEach(field => {
                let fieldValid = true;
                let errorMessage = '';

                const label = field.parentElement.previousElementSibling;
                const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

                if (!field.value || field.value.trim() === '') {
                    if (field.hasAttribute('required')) {
                        fieldValid = false;
                        errorMessage = `Please enter a ${labelText}`;
                    }
                } else if (field.type === 'file' && field.files.length > 0) {
                    const validTypes = ['application/pdf'];
                    const files = Array.from(field.files);
                    if (!files.every(file => validTypes.includes(file.type))) {
                        fieldValid = false;
                        errorMessage = 'Only PDF files are allowed.';
                    }
                }

                const errorElement = field.parentElement.nextElementSibling?.classList.contains('error-message')
                    ? field.parentElement.nextElementSibling
                    : field.parentElement.parentElement.nextElementSibling;

                if (!fieldValid) {
                    field.classList.add('error');
                    field.classList.remove('valid');
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.textContent = errorMessage;
                        errorElement.style.display = 'block';
                    }
                    isValid = false;
                    errorMessages.push(errorMessage);
                    if (!firstErrorElement) firstErrorElement = field;
                } else {
                    field.classList.add('valid');
                    field.classList.remove('error');
                    if (errorElement && errorElement.classList.contains('error-message')) {
                        errorElement.style.display = 'none';
                    }
                }
            });

            if (isValid) {
                console.log('Form is valid, submitting to server...');
                this.submit();
            } else {
                console.log('Form validation failed:', errorMessages);
                const uniqueErrors = [...new Set(errorMessages)];
                Toastify({
                    text: 'Please fix the following errors:\n' + uniqueErrors.join('\n'),
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();

                if (firstErrorElement) {
                    const offset = firstErrorElement.offsetTop - 100;
                    window.scrollTo({ top: offset, behavior: 'smooth' });
                    setTimeout(() => firstErrorElement.focus(), 500);
                }
            }
        });

        // Branch Selection to Enable Family ID
        $(document).ready(function() {
            @if (!isset($learner))
                // Initially disable all fields except branch dropdown
                $('#family_id, #learner_name, #learner_name_template, #staff_lead_name, #category, #courses_subjects_being_studied, #meeting_date, #career_next_steps, #interested_fields, #researched_application_process_deadlines, #clear_go_information, #is_helpful_information, #started_application, #need_help_in_application, #visited_our_resources_on_line, #resources_in_career_library, #IAG_lerner_plan_1, #IAG_lerner_plan_2, #IAG_lerner_plan_3, #learner_on_secure_pathway, #file_upload_one, #file_upload_two, #file_upload_three').prop('disabled', true);
                $('form button[type="submit"]').prop('disabled', true);

                // Enable family_id field when a branch is selected
                $('#branch_id').on('change', function() {
                    if ($(this).val() !== "") {
                        $('#family_id').prop('disabled', false);
                        $(this).addClass('valid');
                        $(this).removeClass('error');
                        $('#branch_id_feedback').css('display', 'none');
                    } else {
                        $('#family_id').prop('disabled', true);
                        $('#learner_name').prop('disabled', true);
                        $('#learner_name').val('');
                        $('#learner_name_feedback').css('display', 'none');
                        $('form input:not(#branch_id, #family_id), form select:not(#branch_id, #learner_name), form textarea').prop('disabled', true);
                        $('form button[type="submit"]').prop('disabled', true);
                        $(this).addClass('error');
                        $(this).removeClass('valid');
                        $('#branch_id_feedback').css('display', 'block');
                        $('#branch_id_feedback').text('Please select a branch.');
                    }
                });

                // Family ID AJAX for Learner Names
                $('#family_id').on('blur', function() {
                    const familyId = $(this).val().trim();
                    const branchId = $('#branch_id').val();

                    if (familyId !== "" && branchId !== "") {
                        Swal.fire({
                            title: 'Please wait',
                            html: 'Fetching data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ url('get/family/rec') }}",
                            type: "GET",
                            data: {
                                family_id: familyId,
                                branch_id: branchId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.close();
                                const nameSelect = $('#learner_name');
                                nameSelect.find('option:not(:first)').remove();
                                nameSelect.prop('disabled', false);
                                nameSelect.find('option:first').text('Select Name *');

                                if (response.length > 0) {
                                    $.each(response, function(index, item) {
                                        const fullName = (typeof item === 'object' && item !== null) ? item.name : item;
                                        const isFlagged = (typeof item === 'object' && item !== null) ? item.is_flag == 1 : false;
                                        const flagIcon = isFlagged ? ' &#x1F6A9;' : '';
                                        nameSelect.append(`<option value="${fullName}">${fullName}${flagIcon}</option>`);
                                    });
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Data fetched successfully!',
                                        icon: 'success',
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                    nameSelect.removeClass('error');
                                    nameSelect.addClass('valid');
                                    $('#learner_name_feedback').css('display', 'none');
                                } else {
                                    Swal.fire({
                                        title: 'No records found',
                                        text: 'No records found for this family ID and branch.',
                                        icon: 'warning',
                                        timer: 5000,
                                        showConfirmButton: false
                                    });
                                    nameSelect.addClass('error');
                                    nameSelect.removeClass('valid');
                                    $('#learner_name_feedback').css('display', 'block');
                                    $('#learner_name_feedback').text('No learners found for this family ID.');
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while fetching data. Please try again later.',
                                    icon: 'error',
                                    timer: 5000,
                                    showConfirmButton: false
                                });
                                console.error(xhr.responseText);
                                $('#learner_name').addClass('error');
                                $('#learner_name').removeClass('valid');
                                $('#learner_name_feedback').css('display', 'block');
                                $('#learner_name_feedback').text('Error fetching learner names.');
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Missing Information',
                            text: 'Please provide both Family ID and Branch.',
                            icon: 'warning',
                            timer: 5000,
                            showConfirmButton: false
                        });
                        $('#form button[type="submit"]').prop('disabled', true);
                        $('#learner_name').prop('disabled', true);
                        $('#learner_name').val('');
                        $('#learner_name_feedback').css('display', 'none');
                    }
                });

                // Enable/Disable Form Fields Based on Learner Name Selection
                $('#learner_name').on('change', function() {
                    if ($(this).val() !== "") {
                        $('#learner_name_template').val($(this).val());
                        $('form input:not(#branch_id, #family_id, #learner_name), form select:not(#branch_id, #learner_name), form textarea').prop('disabled', false);
                        $('form button[type="submit"]').prop('disabled', false);
                        $(this).removeClass('error');
                        $(this).addClass('valid');
                        $('#learner_name_feedback').css('display', 'none');
                    } else {
                        $('#learner_name_template').val('');
                        $('form input:not(#branch_id, #family_id, #learner_name), form select:not(#branch_id, #learner_name), form textarea').prop('disabled', true);
                        $('form button[type="submit"]').prop('disabled', true);
                        $(this).addClass('error');
                        $(this).removeClass('valid');
                        $('#learner_name_feedback').css('display', 'block');
                        $('#learner_name_feedback').text('Please select a learner.');
                    }
                });
            @else
                // Ensure submit button is enabled in approval mode
                $('form button[type="submit"]').prop('disabled', false);
            @endif

            // Session Notifications using SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Swal.fire({
                        title: 'Error!',
                        text: "{{ $error }}",
                        icon: 'error',
                        timer: 5000,
                        showConfirmButton: false
                    });
                @endforeach
            @endif
        });
    });
</script>

@endsection
