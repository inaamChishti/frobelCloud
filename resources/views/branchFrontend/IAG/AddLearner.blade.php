@extends('layouts.branchDashboardApp')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Add Learner</h1>
            </div>

            <!-- Form -->
            <form id="addLearnerForm" action="{{ url('store/learner') }}" method="POST" enctype="multipart/form-data"
                class="space-y-8 mt-8" novalidate>
                @csrf
                <!-- Learner Information Card -->
                <div id="learner-info" class="card form-section">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-user h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner Information</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Current Session *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-calendar-week absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" name="current_session" value="{{ $session }}"
                                    class="input-field pl-10" readonly>
                            </div>
                        </div>
                        <!-- Family ID Field -->

                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="family_id" name="family_id" class="input-field pl-10"
                                    placeholder="Enter family ID" required>
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
                                <i
                                    class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="learner_name" name="learner_name" class="input-field pl-10" required>
                                    <option value="" disabled selected>Select a learner</option>
                                </select>
                                <div class="error-message">Please select a learner.</div>
                            </div>
                        </div>

                        <!-- Learner Name (Text) Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner Name *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="learner_name_template" name="learner_name_template"
                                    class="input-field pl-10" placeholder="Enter learner name" required>
                                <div class="error-message">Please enter the learner's name.</div>
                            </div>
                        </div>




                        <!-- Staff Lead Name Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Staff Lead Name *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-user-tie absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="staff_lead_name" name="staff_lead_name" class="input-field pl-10"
                                    placeholder="Enter staff lead name" required>
                                <div class="error-message">Please enter the staff lead name.</div>
                            </div>
                        </div>

                        <!-- Category Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Category *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-list absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="category" name="category" class="input-field pl-10" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="KS4">KS4</option>
                                    <option value="KS5">KS5</option>
                                    <option value="Adult Learner">Adult Learner</option>
                                </select>
                                <div class="error-message">Please select a category.</div>
                            </div>
                        </div>

                        <!-- Courses/Subjects Being Studied Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Courses/Subjects Being
                                Studied *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="courses_subjects_being_studied"
                                    name="courses_subjects_being_studied" class="input-field pl-10"
                                    placeholder="Enter courses/subjects" required>
                                <div class="error-message">Please enter the courses or subjects being studied.</div>
                            </div>
                        </div>

                        <!-- Meeting Date Field -->
                        {{-- <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Date of Meeting *</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="date" id="meeting_date" name="meeting_date" class="input-field pl-10" required>
                            <div class="error-message">Please select a meeting date.</div>
                        </div>
                    </div> --}}
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Date of Meeting
                                <span style="color: red; font-size: 12px;">(required)</span>
                            </label>
                            <div class="relative">
                                <i
                                    class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="meeting_date" name="meeting_date" class="input-field pl-10"
                                    autocomplete="off" inputmode="none" onfocus="this.showPicker?.()"
                                    placeholder="dd/mm/yyyy" required>
                                <div class="error-message">Please select a meeting date.</div>
                            </div>
                        </div>

                        <!-- Career Next Steps Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you decided upon your
                                career next steps? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-map-signs absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="career_next_steps" name="career_next_steps" class="input-field pl-10"
                                    required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Unsure">Unsure</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Interested Fields Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">What field/s are you
                                interested in? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-briefcase absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="interested_fields" name="interested_fields"
                                    class="input-field pl-10" placeholder="Enter interested fields" required>
                                <div class="error-message">Please enter the fields of interest.</div>
                            </div>
                        </div>

                        <!-- Researched Application Process Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you researched the
                                application process and deadlines? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="researched_application_process_deadlines"
                                    name="researched_application_process_deadlines" class="input-field pl-10" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Unsure">Unsure</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Clear on Information Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Are you clear on where to
                                go for information? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-info-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="clear_go_information" name="clear_go_information" class="input-field pl-10"
                                    required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Unsure">Unsure</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Helpful Information Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">What other information
                                would you find helpful? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-question-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text" id="is_helpful_information" name="is_helpful_information"
                                    class="input-field pl-10" placeholder="Enter helpful information needed" required>
                                <div class="error-message">Please enter the information needed.</div>
                            </div>
                        </div>

                        <!-- Started Application Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you started the
                                application process? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-play absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="started_application" name="started_application" class="input-field pl-10"
                                    required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Need Help in Application Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Do you need any resources
                                to help with applications? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-tools absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="need_help_in_application" name="need_help_in_application"
                                    class="input-field pl-10" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Visited Resources Online Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you visited our
                                resources online? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-globe absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="visited_our_resources_on_line" name="visited_our_resources_on_line"
                                    class="input-field pl-10" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>

                        <!-- Resources in Career Library Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Have you used the
                                resources in our careers library? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-book-open absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="resources_in_career_library" name="resources_in_career_library"
                                    class="input-field pl-10" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
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
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner IAG Action Plans
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <!-- IAG Learner Plan 1 Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 1
                                *</label>
                            <div class="relative">
                                <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                                <textarea id="IAG_lerner_plan_1" name="IAG_lerner_plan_1" class="input-field pl-10" rows="3"
                                    placeholder="Enter action plan 1 details" required></textarea>
                                <div class="error-message">Please enter action plan 1 details.</div>
                            </div>
                        </div>

                        <!-- IAG Learner Plan 2 Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 2
                                *</label>
                            <div class="relative">
                                <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                                <textarea id="IAG_lerner_plan_2" name="IAG_lerner_plan_2" class="input-field pl-10" rows="3"
                                    placeholder="Enter action plan 2 details" required></textarea>
                                <div class="error-message">Please enter action plan 2 details.</div>
                            </div>
                        </div>

                        <!-- IAG Learner Plan 3 Field -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Learner IAG Action Plan 3
                                *</label>
                            <div class="relative">
                                <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                                <textarea id="IAG_lerner_plan_3" name="IAG_lerner_plan_3" class="input-field pl-10" rows="3"
                                    placeholder="Enter action plan 3 details" required></textarea>
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
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Is learner on secure
                                pathway? *</label>
                            <div class="relative">
                                <i
                                    class="fas fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select id="learner_on_secure_pathway" name="learner_on_secure_pathway"
                                    class="input-field pl-10" required>
                                    <option value="" disabled selected>Select an option</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                <div class="error-message">Please select an option.</div>
                            </div>
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <!-- File Upload 1 -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 1 </label>
                            <div class="relative">
                                <i
                                    class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="file" id="file_upload_one" name="file_upload_one[]"
                                    class="input-field pl-10" accept="application/pdf" multiple>
                                <div class="error-message">Please upload a PDF file.</div>
                            </div>
                            <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                        </div>

                        <!-- File Upload 2 -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 2</label>
                            <div class="relative">
                                <i
                                    class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="file" id="file_upload_two" name="file_upload_two[]"
                                    class="input-field pl-10" accept="application/pdf" multiple>
                                <div class="error-message">Please upload a PDF file.</div>
                            </div>
                            <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                        </div>

                        <!-- File Upload 3 -->
                        <div>
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Upload Docs 3</label>
                            <div class="relative">
                                <i
                                    class="fas fa-file-pdf absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="file" id="file_upload_three" name="file_upload_three[]"
                                    class="input-field pl-10" accept="application/pdf" multiple>
                                <div class="error-message">Please upload a PDF file.</div>
                            </div>
                            <small class="text-[var(--error)] text-xs mt-1">Only PDF files are allowed for upload</small>
                        </div>

                        <!-- Secure Pathway Definition -->
                        <div class="col-span-1 sm:col-span-2">
                            <div class="bg-[var(--secondary)] p-4 rounded-lg text-center">
                                <strong class="text-[var(--primary-dark)]">Definition of Secure:</strong><br>
                                <span class="text-[var(--text-dark)] text-sm">Learner understands how to access and make
                                    effective use of IAG resources AND Learner is confident with next steps for short,
                                    medium, and long-term careers' ideas and planning.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center mt-8 gap-4">
                    <button type="submit" class="btn-primary text-base flex items-center">
                        <i class="fas fa-save mr-2"></i>Save Learner
                    </button>
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
            display: flex;
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
        .registration-container .grid-cols-2>div {
            padding: 0 0.5rem;
        }

        .registration-container textarea.input-field {
            height: auto;
            resize: vertical;
            min-height: 4rem;
        }
    </style>

 <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Initialize Flatpickr for meeting_date
flatpickr("#meeting_date", {
    dateFormat: "d/m/Y",
    allowInput: true,
    locale: {
        firstDayOfWeek: 1 // Set Monday as the first day of the week
    },
    onChange: function(selectedDates, dateStr, instance) {
        validateField({ target: instance.element });
    }
});

// Form Validation
document.addEventListener('DOMContentLoaded', function() {
    console.log('Document ready, initializing form validation...');

    const form = document.getElementById('addLearnerForm');
    const fields = form.querySelectorAll('.input-field');
    const submitButton = form.querySelector('button[type="submit"]');

    // Real-time validation for inputs, selects, and textareas
    fields.forEach(field => {
        field.addEventListener('input', validateField);
        field.addEventListener('change', validateField);
        field.addEventListener('blur', validateField);
    });

    // Validate individual field
    function validateField(e) {
        const field = e.target;
        let isValid = true;
        let errorMessage = '';

        // Skip validation for file inputs
        if (field.id === 'file_upload_one' || field.id === 'file_upload_two' || field.id === 'file_upload_three') {
            return;
        }

        if (field.hasAttribute('readonly')) return;

        const label = field.parentElement.previousElementSibling;
        const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

        // Check if field is empty
        if (!field.value || field.value.trim() === '') {
            isValid = false;
            errorMessage = `Please enter ${labelText}.`;
        } else if (field.tagName === 'SELECT' && field.value === '') {
            isValid = false;
            errorMessage = `Please select ${labelText}.`;
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

        return isValid;
    }

    // Validate all required fields
    function validateAllFields() {
        let isValid = true;
        let errorMessages = [];
        let firstErrorElement = null;

        fields.forEach(field => {
            // Skip validation for file inputs
            if (field.id === 'file_upload_one' || field.id === 'file_upload_two' || field.id === 'file_upload_three') {
                return;
            }

            if (field.hasAttribute('readonly')) return;

            const label = field.parentElement.previousElementSibling;
            const labelText = label ? label.textContent.replace('*', '').trim() : field.name;
            let fieldValid = true;
            let errorMessage = '';

            // Check if field is empty
            if (!field.value || field.value.trim() === '') {
                fieldValid = false;
                errorMessage = `Please enter ${labelText}.`;
            } else if (field.tagName === 'SELECT' && field.value === '') {
                fieldValid = false;
                errorMessage = `Please select ${labelText}.`;
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

        return { isValid, errorMessages, firstErrorElement };
    }

    // Form Submission Validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submit event triggered');

        // Validate all fields
        const { isValid, errorMessages, firstErrorElement } = validateAllFields();

        if (isValid) {
            console.log('Form is valid, submitting to server...');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

            this.submit(); // Submit the form
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
                window.scrollTo({
                    top: offset,
                    behavior: 'smooth'
                });
                setTimeout(() => firstErrorElement.focus(), 500);
            }
        }
    });

    // Family ID AJAX for Learner Names
    $(document).ready(function() {
        $('#family_id').on('blur', function() {
            const familyId = $(this).val().trim();

            // Always reset the dropdown state first
            const $nameSelect = $('#learner_name');
            $nameSelect.find('option:not(:first)').remove();
            $nameSelect.val('').removeClass('error valid').prop('disabled', false);
            // Re-enable all form fields (will be locked again if blocked or no data)
            $('form input:not(#family_id), form select, form textarea').prop('disabled', false);
            $('form button[type="submit"]').prop('disabled', false);

            if (familyId !== "") {
                $.ajax({
                    url: "{{ url('get/family/rec') }}",
                    type: "GET",
                    data: {
                        family_id: familyId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Toastify({
                            text: "Data fetched successfully!",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#10b981",
                            stopOnFocus: true,
                        }).showToast();

                        const nameSelect = $('#learner_name');
                        nameSelect.find('option:not(:first)').remove();

                        if (response.length > 0) {
                            $.each(response, function(index, item) {
                                const fullName = (typeof item === 'object' && item !== null) ? item.name : item;
                                const isFlagged = (typeof item === 'object' && item !== null) ? item.is_flag == 1 : false;
                                const flagIcon = isFlagged ? ' &#x1F6A9;' : '';
                                nameSelect.append(
                                    `<option value="${fullName}">${fullName}${flagIcon}</option>`
                                );
                            });
                            nameSelect.classList.add('valid');
                            nameSelect.classList.remove('error');
                        } else {
                            Toastify({
                                text: "No records found for this family ID.",
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#ef4444",
                                stopOnFocus: true,
                            }).showToast();
                            nameSelect.classList.add('error');
                            nameSelect.classList.remove('valid');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.blocked) {
                            Toastify({
                                text: "Family ID Blocked. Please contact admin office for more details.",
                                duration: 6000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                                stopOnFocus: true,
                            }).showToast();
                            // Clear & disable name select so user cannot proceed
                            $('#learner_name').find('option:not(:first)').remove();
                            $('#learner_name').val('').addClass('error').removeClass('valid').prop('disabled', true);
                            $('form input:not(#family_id), form select, form textarea').prop('disabled', true);
                            $('form button[type="submit"]').prop('disabled', true);
                            return;
                        }
                        Toastify({
                            text: "No students found within year between 10 and 13 for the given family ID.",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                        console.error(xhr.responseText);
                        $('#learner_name').addClass('error').removeClass('valid');
                    }
                });
            }
        });

        // Enable/Disable Form Fields Based on Learner Name Selection
        $('form input:not(#family_id, #learner_name), form select:not(#learner_name), form textarea')
            .prop('disabled', true);
        $('form button[type="submit"]').prop('disabled', true);

        $('#learner_name').on('change', function() {
            if ($(this).val() !== "") {
                $('form input, form select, form textarea').prop('disabled', false);
                $('form button[type="submit"]').prop('disabled', false);
            } else {
                $('form input:not(#family_id, #learner_name), form select:not(#learner_name), form textarea')
                    .prop('disabled', true);
                $('form button[type="submit"]').prop('disabled', true);
            }
        });

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
});
</script>
@endsection
