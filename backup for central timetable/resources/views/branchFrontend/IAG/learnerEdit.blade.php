@extends('layouts.branchDashboardApp')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Edit Learner</h1>
            <p class="text-lg text-[var(--text-dark)]">Update the learner's details below.</p>
        </div>

        <!-- Form Section -->
        <div class="card form-section">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-user-edit h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner Details</h2>
            </div>
            <form id="editLearnerForm" action="{{ url('learner/Update') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Hidden Learner ID -->
                    <input type="hidden" value="{{ @$learner->id }}" id="id" name="id" class="input-field">

                    <!-- Family ID Field -->
                    <div>
                        <label for="family_id" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID</label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="family_id" name="family_id" class="input-field pl-10" value="{{ @$learner->family_id }}" readonly>
                            @error('family_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Learner Name Field -->
                    <div>
                        <label for="learner_name" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner Name</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="learner_name" name="learner_name" class="input-field pl-10" value="{{ @$learner->learner_name }}" readonly>
                        </div>
                    </div>

                    <!-- Learner Name Template Field -->
                    <div>
                        <label for="learner_name_template" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner Name Template</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="learner_name_template" name="learner_name_template" class="input-field pl-10" value="{{ @$learner->learner_name_template }}" required>
                            <div class="error-message">Please enter the learner's name template.</div>
                        </div>
                    </div>

                    <!-- Staff Lead Name Field -->
                    <div>
                        <label for="staff_lead_name" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Staff Lead Name</label>
                        <div class="relative">
                            <i class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="staff_lead_name" name="staff_lead_name" class="input-field pl-10" value="{{ @$learner->staff_lead_name }}" required>
                            <div class="error-message">Please enter the staff lead name.</div>
                        </div>
                    </div>

                    <!-- Category Field -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Category</label>
                        <div class="relative">
                            <i class="fas fa-list absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="category" name="category" class="input-field pl-10" required>
                                <option value="" disabled>Select Category</option>
                                <option value="KS4" {{ old('category', @$learner->category) == 'KS4' ? 'selected' : '' }}>KS4</option>
                                <option value="KS5" {{ old('category', @$learner->category) == 'KS5' ? 'selected' : '' }}>KS5</option>
                                <option value="Adult Learner" {{ old('category', @$learner->category) == 'Adult Learner' ? 'selected' : '' }}>Adult Learner</option>
                            </select>
                            <div class="error-message">Please select a category.</div>
                        </div>
                    </div>

                    <!-- Courses/Subjects Being Studied Field -->
                    <div>
                        <label for="courses_subjects_being_studied" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Courses/Subjects Being Studied</label>
                        <div class="relative">
                            <i class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="courses_subjects_being_studied" name="courses_subjects_being_studied" class="input-field pl-10" value="{{ @$learner->courses_subjects_being_studied }}" required>
                            <div class="error-message">Please enter the courses or subjects being studied.</div>
                        </div>
                    </div>

                    <!-- Meeting Date Field -->
                    {{-- <div>
                        <label for="meeting_date" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Date of Meeting</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="date" id="meeting_date" name="meeting_date" class="input-field pl-10" value="{{ @$learner->meeting_date }}" required>
                            <div class="error-message">Please select a meeting date.</div>
                        </div>
                    </div> --}}
                    <div>
                        <label for="meeting_date" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Date of Meeting
                            <span style="color: red; font-size: 12px;">(required)</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="meeting_date" name="meeting_date" class="input-field pl-10" value="{{ @$learner->meeting_date ? \Carbon\Carbon::parse($learner->meeting_date)->format('d/m/Y') : '' }}" placeholder="dd/mm/yyyy" required>
                            <div class="error-message">Please select a meeting date.</div>
                        </div>
                    </div>

                    <!-- Career Next Steps Field -->
                    <div>
                        <label for="career_next_steps" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you decided upon your career next steps?</label>
                        <div class="relative">
                            <i class="fas fa-map-signs absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="career_next_steps" name="career_next_steps" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->career_next_steps == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->career_next_steps == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ @$learner->career_next_steps == 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Interested Fields Field -->
                    <div>
                        <label for="interested_fields" class="block text-sm font-medium text-[var(--text-dark)] mb-1">What field/s are you interested in?</label>
                        <div class="relative">
                            <i class="fas fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="interested_fields" name="interested_fields" class="input-field pl-10" value="{{ @$learner->interested_fields }}" required>
                            <div class="error-message">Please enter the fields of interest.</div>
                        </div>
                    </div>

                    <!-- Researched Application Process Field -->
                    <div>
                        <label for="researched_application_process_deadlines" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you researched the application process and deadlines?</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="researched_application_process_deadlines" name="researched_application_process_deadlines" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->researched_application_process_deadlines === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->researched_application_process_deadlines === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ @$learner->researched_application_process_deadlines === 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Clear on Information Field -->
                    <div>
                        <label for="clear_go_information" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Are you clear on where to go for information?</label>
                        <div class="relative">
                            <i class="fas fa-info-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="clear_go_information" name="clear_go_information" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->clear_go_information === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->clear_go_information === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Unsure" {{ @$learner->clear_go_information === 'Unsure' ? 'selected' : '' }}>Unsure</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Helpful Information Field -->
                    <div>
                        <label for="is_helpful_information" class="block text-sm font-medium text-[var(--text-dark)] mb-1">What other information would you find helpful?</label>
                        <div class="relative">
                            <i class="fas fa-question-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="is_helpful_information" name="is_helpful_information" class="input-field pl-10" value="{{ @$learner->is_helpful_information }}" required>
                            <div class="error-message">Please enter the information needed.</div>
                        </div>
                    </div>

                    <!-- Started Application Field -->
                    <div>
                        <label for="started_application" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you started the application process?</label>
                        <div class="relative">
                            <i class="fas fa-play absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="started_application" name="started_application" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->started_application === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->started_application === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Need Help in Application Field -->
                    <div>
                        <label for="need_help_in_application" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Do you need any resources to help with applications?</label>
                        <div class="relative">
                            <i class="fas fa-tools absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="need_help_in_application" name="need_help_in_application" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->need_help_in_application === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->need_help_in_application === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Visited Resources Online Field -->
                    <div>
                        <label for="visited_our_resources_on_line" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you visited our resources online?</label>
                        <div class="relative">
                            <i class="fas fa-globe absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="visited_our_resources_on_line" name="visited_our_resources_on_line" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->visited_our_resources_on_line === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->visited_our_resources_on_line === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- Resources in Career Library Field -->
                    <div>
                        <label for="resources_in_career_library" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you used the resources in our careers library?</label>
                        <div class="relative">
                            <i class="fas fa-book-open absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="resources_in_career_library" name="resources_in_career_library" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->resources_in_career_library === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->resources_in_career_library === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- IAG Learner Plan 1 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="IAG_lerner_plan_1" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 1</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_1" name="IAG_lerner_plan_1" class="input-field pl-10" rows="3" required>{{ @$learner->IAG_lerner_plan_1 }}</textarea>
                            <div class="error-message">Please enter action plan 1 details.</div>
                        </div>
                    </div>

                    <!-- IAG Learner Plan 2 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="IAG_lerner_plan_2" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 2</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_2" name="IAG_lerner_plan_2" class="input-field pl-10" rows="3" required>{{ @$learner->IAG_lerner_plan_2 }}</textarea>
                            <div class="error-message">Please enter action plan 2 details.</div>
                        </div>
                    </div>

                    <!-- IAG Learner Plan 3 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="IAG_lerner_plan_3" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 3</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <textarea id="IAG_lerner_plan_3" name="IAG_lerner_plan_3" class="input-field pl-10" rows="3" required>{{ @$learner->IAG_lerner_plan_3 }}</textarea>
                            <div class="error-message">Please enter action plan 3 details.</div>
                        </div>
                    </div>

                    <!-- Learner on Secure Pathway Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="learner_on_secure_pathway" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Is learner on secure pathway?</label>
                        <div class="relative">
                            <i class="fas fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="learner_on_secure_pathway" name="learner_on_secure_pathway" class="input-field pl-10" required>
                                <option value="" disabled>Select an option</option>
                                <option value="Yes" {{ @$learner->learner_on_secure_pathway === 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ @$learner->learner_on_secure_pathway === 'No' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="error-message">Please select an option.</div>
                        </div>
                    </div>

                    <!-- File Upload 1 -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="file_upload_one" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 1</label>
                        <div class="relative">
                            <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="file" id="file_upload_one" name="file_upload_one[]" class="input-field pl-10" accept="application/pdf" multiple>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs">Only PDF files are allowed for upload</small>
                        @if (isset($learner->file_input_1) && !empty($learner->file_input_1))
                            @php $fileInput1 = explode(',', $learner->file_input_1); @endphp
                            <div class="mt-2">
                                <p><strong>Docs 1:</strong></p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($fileInput1 as $file)
                                        <div class="flex items-center">
                                           @php
                                                $encoded = base64_encode($file);
                                            @endphp
                                            <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="text-primary">View File</a>

                                            <button type="button" class="btn btn-danger btn-sm delete-file ml-2" data-file="{{ $file }}" data-type="file_input_1">Delete</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-muted">No Docs 1 uploaded yet.</p>
                        @endif
                    </div>

                    <!-- File Upload 2 -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="file_upload_two" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 2</label>
                        <div class="relative">
                            <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="file" id="file_upload_two" name="file_upload_two[]" class="input-field pl-10" accept="application/pdf" multiple>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs">Only PDF files are allowed for upload</small>
                        @if (isset($learner->file_input_2) && !empty($learner->file_input_2))
                            @php $fileInput2 = explode(',', $learner->file_input_2); @endphp
                            <div class="mt-2">
                                <p><strong>Docs 2:</strong></p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($fileInput2 as $file)
                                        <div class="flex items-center">

                                             @php
                                                $encoded = base64_encode($file);
                                            @endphp
                                            <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="text-primary">View File</a>

                                            {{-- <a href="{{ asset($file) }}" target="_blank" class="text-primary">View File</a> --}}
                                            <button type="button" class="btn btn-danger btn-sm delete-file ml-2" data-file="{{ $file }}" data-type="file_input_2">Delete</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-muted">No Docs 2 uploaded yet.</p>
                        @endif
                    </div>

                    <!-- File Upload 3 -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="file_upload_three" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 3</label>
                        <div class="relative">
                            <i class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="file" id="file_upload_three" name="file_upload_three[]" class="input-field pl-10" accept="application/pdf" multiple>
                            <div class="error-message">Please upload a PDF file.</div>
                        </div>
                        <small class="text-[var(--error)] text-xs">Only PDF files are allowed for upload</small>
                        @if (isset($learner->file_input_3) && !empty($learner->file_input_3))
                            @php $fileInput3 = explode(',', $learner->file_input_3); @endphp
                            <div class="mt-2">
                                <p><strong>Docs 3:</strong></p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($fileInput3 as $file)
                                        <div class="flex items-center">
                                            {{-- <a href="{{ asset($file) }}" target="_blank" class="text-primary">View File</a> --}}
                                              @php
                                                $encoded = base64_encode($file);
                                            @endphp
                                            <a href="{{ route('file.view', ['encoded' => $encoded]) }}" target="_blank" class="text-primary">View File</a>

                                            <button type="button" class="btn btn-danger btn-sm delete-file ml-2" data-file="{{ $file }}" data-type="file_input_3">Delete</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-muted">No Docs 3 uploaded yet.</p>
                        @endif
                    </div>

                    <!-- Terms Section -->
                    <div class="col-span-1 sm:col-span-2">
                        <div class="card form-section">
                            <div class="flex items-center mb-4">
                                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                                    <i class="fas fa-calendar-alt h-5 w-5 text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Term Data</h2>
                            </div>
                            <div class="mb-4">
                                <button type="button" id="addTermBtn" class="btn btn-primary flex items-center">
                                    <i class="fas fa-plus mr-2"></i>Add Term
                                </button>
                            </div>
                            <div id="termsContainer"></div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-span-1 sm:col-span-2 flex justify-center">
                        <button type="submit" class="btn btn-primary flex items-center">
                            <i class="fas fa-save mr-2"></i>Update Learner
                        </button>
                    </div>
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

    .registration-container .input-field.valid {
        border-color: #10b981;
        background-color: #ecfdf5;
    }

    .registration-container .input-field[readonly] {
        background: #e6ebf5;
        opacity: 0.85;
        cursor: not-allowed;
    }

    .registration-container .input-field::placeholder {
        color: var(--text-light);
        opacity: 0.7;
        font-style: normal;
    }

    .registration-container textarea.input-field {
        height: auto;
        resize: vertical;
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

    .registration-container .input-field.error + .error-message {
        display: block;
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

    .registration-container .section-icon {
        transition: transform 0.3s ease;
    }

    .registration-container .section-icon:hover {
        transform: scale(1.2);
    }

    .registration-container .term-form {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .registration-container .term-form .section-title {
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
        .registration-container .grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for Meeting Date
        flatpickr("#meeting_date", {
            dateFormat: "d/m/Y",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1 // Set Monday as the first day of the week
            },
            onChange: function(selectedDates, dateStr, instance) {
                // Trigger validation on date selection
                const input = instance.element;
                validateField({ target: input });
            }
        });
        </script>

    <script>
        $(document).ready(function() {
            let termCount = 0;
            const learnerData = @json($learner);
            let assetBaseUrl = "{{ asset('') }}";

            // Populate existing terms
            if (learnerData.term_name && learnerData.term_name.length > 0) {
                learnerData.term_name.forEach((term, index) => {
                    termCount++;
                    let newForm = `
                        <div class="term-form rounded" id="term-${termCount}">
                            <h6 class="section-title">Term ${termCount}</h6>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label">Term Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                        <input type="text" class="form-control" name="term_name[]" value="${learnerData.term_name[index]}" required>
                                        <div class="invalid-feedback">Please enter term name.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Staff Lead</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                        <input type="text" class="form-control" name="staff_lead[]" value="${learnerData.staff_lead[index]}" required>
                                        <div class="invalid-feedback">Please enter staff lead.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" class="form-control" name="date[]" value="${learnerData.date[index]}" required>
                                        <div class="invalid-feedback">Please select a date.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Meeting Notes</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                        <textarea class="form-control" rows="3" name="meeting_notes[]" required>${learnerData.meeting_notes[index]}</textarea>
                                        <div class="invalid-feedback">Please enter meeting notes.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Secure Pathway?</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                        <select class="form-select" name="secure_pathway[]" required>
                                            <option value="Yes" ${learnerData.secure_pathway[index] === 'Yes' ? 'selected' : ''}>Yes</option>
                                            <option value="No" ${learnerData.secure_pathway[index] === 'No' ? 'selected' : ''}>No</option>
                                        </select>
                                        <div class="invalid-feedback">Please select an option.</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Deadline</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" class="form-control" name="deadline[]" value="${learnerData.deadline[index]}" required>
                                        <div class="invalid-feedback">Please select a deadline.</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">IAG Target 1</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                        <input type="text" class="form-control" name="iag_target1[]" value="${learnerData.iag_target1[index]}" required>
                                        <div class="invalid-feedback">Please enter IAG target 1.</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">IAG Target 2</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                        <input type="text" class="form-control" name="iag_target2[]" value="${learnerData.iag_target2[index]}" required>
                                        <div class="invalid-feedback">Please enter IAG target 2.</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">IAG Target 3</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                        <input type="text" class="form-control" name="iag_target3[]" value="${learnerData.iag_target3[index]}" required>
                                        <div class="invalid-feedback">Please enter IAG target 3.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Choose File</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-pdf"></i></span>
                                        <input type="file" class="form-control file-input" name="file_${termCount}" accept="application/pdf">
                                        <span class="remove-file" style="display: none; cursor: pointer; padding: 0 10px; color: #dc3545;">✖</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">View Document</label>
                                    <div class="file-links">
                                        ${learnerData.file && learnerData.file[index] ? `
                                            <a href="${assetBaseUrl}${learnerData.file[index]}" target="_blank" class="btn btn-link">View File</a>
                                        ` : 'No files available'}
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger btn-sm removeTerm">Remove Term</button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#termsContainer').append(newForm);
                });
            }

            // Add new Term Section
            $('#addTermBtn').click(function(e) {
                e.preventDefault();
                termCount++;
                let newForm = `
                    <div class="term-form rounded" id="term-${termCount}">
                        <h6 class="section-title">Term ${termCount}</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label">Term Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                    <input type="text" class="form-control" name="term_name[]" required>
                                    <div class="invalid-feedback">Please enter term name.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Staff Lead</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                    <input type="text" class="form-control" name="staff_lead[]" required>
                                    <div class="invalid-feedback">Please enter staff lead.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control" name="date[]" required>
                                    <div class="invalid-feedback">Please select a date.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Meeting Notes</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sticky-note"></i></span>
                                    <textarea class="form-control" rows="3" name="meeting_notes[]" required></textarea>
                                    <div class="invalid-feedback">Please enter meeting notes.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secure Pathway?</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                    <select class="form-select" name="secure_pathway[]" required>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                    <div class="invalid-feedback">Please select an option.</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Deadline</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control" name="deadline[]" required>
                                    <div class="invalid-feedback">Please select a deadline.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">IAG Target 1</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                    <input type="text" class="form-control" name="iag_target1[]" required>
                                    <div class="invalid-feedback">Please enter IAG target 1.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">IAG Target 2</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                    <input type="text" class="form-control" name="iag_target2[]" required>
                                    <div class="invalid-feedback">Please enter IAG target 2.</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">IAG Target 3</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-bullseye"></i></span>
                                    <input type="text" class="form-control" name="iag_target3[]" required>
                                    <div class="invalid-feedback">Please enter IAG target 3.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Choose File</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-pdf"></i></span>
                                    <input type="file" class="form-control file-input" name="file_${termCount}" accept="application/pdf">
                                    <span class="remove-file" style="display: none; cursor: pointer; padding: 0 10px; color: #dc3545;">✖</span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end align-items-end">
                                <button type="button" class="btn btn-danger btn-sm removeTerm">Remove Term</button>
                            </div>
                        </div>
                    </div>
                `;
                $('#termsContainer').append(newForm);
            });

            // Remove Term Section
            $(document).on('click', '.removeTerm', function(e) {
                e.preventDefault();
                var $termForm = $(this).closest('.term-form');
                var termCount = $termForm.attr('id').replace('term-', '');
                var learnerId = '{{ @$learner->id }}';

                $termForm.remove();
                $.ajax({
                    url: '{{ url('sort-terms') }}',
                    method: 'POST',
                    data: {
                        termCount: termCount,
                        learnerId: learnerId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            resetTermNumbers();
                        } else {
                            console.error('Failed to update term data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
                resetTermNumbers();
            });

            // Reset Term Numbers
            function resetTermNumbers() {
                $('.term-form').each(function(index) {
                    let newTermNumber = index + 1;
                    $(this).attr('id', `term-${newTermNumber}`);
                    $(this).find('.section-title').text(`Term ${newTermNumber}`);
                    $(this).find('.file-input').attr('name', `file_${newTermNumber}`);
                });
            }

            // Show/Hide Remove File Button
            $(document).on('change', '.file-input', function() {
                let removeBtn = $(this).siblings('.remove-file');
                if (this.files.length > 0) {
                    removeBtn.show();
                } else {
                    removeBtn.hide();
                }
            });

            // Remove File Selection
            $(document).on('click', '.remove-file', function() {
                let fileInput = $(this).siblings('.file-input');
                fileInput.val('');
                $(this).hide();
            });

            // Delete File via AJAX
            $(document).on('click', '.delete-file', function() {
                let filePath = $(this).data('file');
                let fileType = $(this).data('type');
                let button = $(this);
                let learnerId = $('#id').val();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete this file.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('delete-doc') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                file_path: filePath,
                                file_type: fileType,
                                id: learnerId
                            },
                            success: function(response) {
                                if (response.success) {
                                    button.closest('.d-flex').remove();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'File deleted successfully!',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error deleting file!',
                                        text: response.message || 'Please try again.',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Something went wrong!',
                                    showConfirmButton: false,
                                    timer: 2000
                                });
                            }
                        });
                    }
                });
            });

            // Form Validation
            const form = document.getElementById('editLearnerForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    const emptyFields = Array.from(form.querySelectorAll('input, select, textarea')).filter(field => {
                        return !field.value || field.value === '';
                    }).map(field => {
                        return `• ${field.name.replace(/_/g, ' ').replace(/(?:^|\s)\S/g, a => a.toUpperCase())}`;
                    });

                    if (emptyFields.length > 0) {
                        Swal.fire({
                            title: 'Please complete the form',
                            html: `The following fields are empty:<br>${emptyFields.join('<br>')}`,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                    return;
                }
                form.classList.add('was-validated');
            });

            // Success Notification
            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

@endsection
