@extends('layouts.branchDashboardApp')

@section('content')
    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">View Learner</h1>
                <p class="text-lg text-[var(--text-dark)]">Enter the family ID and select a learner to view their details.
                </p>
            </div>

            <!-- Form -->
            <div id="learner-selection" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-user h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Learner Selection</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Family ID Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID *</label>
                        <div class="relative">
                            <i
                                class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="family_id" name="family_id" class="input-field pl-10"
                                placeholder="Enter family ID" required>
                            <div class="error-message">Please enter a valid family ID.</div>
                            @error('family_id')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Learner Name (Select) Field -->
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Select Learner *</label>
                        <div class="relative">
                            <i
                                class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="learner_name" name="learner_name" class="input-field pl-10" required>
                                <option value="" disabled selected>Select a learner</option>
                            </select>
                            <div class="error-message">Please select a learner.</div>
                            @error('learner_name')
                                <div class="error-message" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div id="session-wrapper" style="display: none;">
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Select Session *</label>
                        <div class="relative">
                            <i
                                class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="session_select" name="session_select" class="input-field pl-10" required>
                                <option value="" disabled selected>Loading sessions...</option>
                            </select>
                            <div class="error-message">Please select a session.</div>
                        </div>
                    </div>


                    <div id="loading-message" class="text-sm text-[var(--primary)] mt-2" style="display: none;">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Please wait, loading sessions...
                    </div>
                </div>
            </div>

            <!-- View Learner Data Button -->
            <div class="flex justify-center mt-8 gap-4">
                <button type="button" id="viewLearnerData" class="btn-primary text-base flex items-center">
                    <i class="fas fa-eye mr-2"></i>View Learner Data
                </button>
            </div>
        </div>

        <!-- Student Data Section -->
        <div class="student-data mt-8">
            <div id="student-data" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-info-circle h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Student Data</h2>
                </div>
                <div class="student-data-container p-4">
                    <!-- Data will be populated via AJAX -->
                </div>
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Real-time validation for inputs and selects
            document.querySelectorAll('.registration-container .input-field').forEach(field => {
                field.addEventListener('input', validateField);
                field.addEventListener('change', validateField);
                field.addEventListener('blur', validateField);
            });

            function validateField(e) {
                const field = e.target;
                let isValid = true;
                let errorMessage = '';

                if (field.hasAttribute('readonly')) return;

                const label = field.parentElement.previousElementSibling;
                const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

                if (!field.value || field.value.trim() === '') {
                    isValid = false;
                    errorMessage = `Please enter a ${labelText}`;
                }

                const errorElement = field.parentElement.nextElementSibling?.classList.contains('error-message') ?
                    field.parentElement.nextElementSibling :
                    field.parentElement.parentElement.nextElementSibling;

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

            // Family ID AJAX for Learner Names
            $('#family_id').on('blur', function() {
                const familyId = $(this).val().trim();
                const $nameSelect = $('#learner_name');
                $nameSelect.find('option:not(:first)').remove(); // Clear options
                $nameSelect.val('').removeClass('valid error').prop('disabled', false); // Reset + re-enable
                $('.student-data-container').empty(); // Clear old data
                $('#session-wrapper').hide(); // Hide session dropdown

                if (familyId !== "") {

                    $.ajax({
                        url: "{{ url('get/family/rec') }}",
                        type: "GET",
                        data: {
                            family_id: familyId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Toastify({
                                text: "Data loaded successfully!",
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
                                $('#session-wrapper').hide();
                                $('.student-data-container').empty();
                                return;
                            }
                            Toastify({
                                text: "No students found within year 10–13 for this family ID.",
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


            function showFieldError(selector, message) {
    const $field = $(selector);
    const $wrapper = $field.closest('.relative');
    const $error = $wrapper.siblings('.error-message').first();

    $field.addClass('error').removeClass('valid');
    if ($error.length) {
        $error.text(message).show();
    }
}

            // View Learner Data
            $('#viewLearnerData').on('click', function(event) {
                event.preventDefault();

                const familyId = $('#family_id').val().trim();
                const learnerName = $('#learner_name').val();
                const session = $('#session_select').val();

                // Reset previous errors
                $('.error-message').hide();
                $('.input-field').removeClass('error');

                let hasError = false;

                // Validate Family ID
                if (!familyId) {
                    showFieldError('#family_id', 'Please enter a valid family ID.');
                    hasError = true;
                }

                // Validate Learner Name
                if (!learnerName) {
                    showFieldError('#learner_name', 'Please select a learner.');
                    hasError = true;
                }

                // Validate Session
                if (!$('#session-wrapper').is(':visible')) {
                    Toastify({
                        text: "please select session first.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                    return;
                }

                if (!session) {
                    showFieldError('#session_select', 'Please select a session.');
                    hasError = true;
                }

                if (hasError) return;


                $.ajax({
                    url: '{{ url('get/family/data') }}',
                    type: 'GET',
                    data: {
                        family_id: familyId,
                        learner_name: learnerName,
                        session: session,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Toastify({
                            text: "Student data loaded successfully!",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#10b981",
                            stopOnFocus: true,
                        }).showToast();

                        $('.student-data-container').html(`
                        <h3 class="section-title text-center mb-4">Student Data</h3>
                        <div class="student-data-card p-3 rounded">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Application ID:</strong> <span class="text-muted">${response.id || 'N/A'}</span></div>
                                <div><strong>Family ID:</strong> <span class="text-muted">${response.family_id || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Name:</strong> <span class="text-muted">${response.learner_name || 'N/A'}</span>${response.is_flag == 1 ? ' <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>' : ''}</div>
                                <div><strong>Name Template:</strong> <span class="text-muted">${response.learner_name_template || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Staff Lead Name:</strong> <span class="text-muted">${response.staff_lead_name || 'N/A'}</span></div>
                                <div><strong>Category:</strong> <span class="text-muted">${response.category || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Courses/Subjects:</strong> <span class="text-muted">${response.courses_subjects_being_studied || 'N/A'}</span></div>
                                <div><strong>Date of Meeting:</strong> <span class="text-muted">${formatDate(response.meeting_date) || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Next Steps:</strong> <span class="text-muted">${response.career_next_steps || 'N/A'}</span></div>
                                <div><strong>Interested Fields:</strong> <span class="text-muted">${response.interested_fields || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Application Deadlines:</strong> <span class="text-muted">${response.researched_application_process_deadlines || 'N/A'}</span></div>
                                <div><strong>Clear Go Info:</strong> <span class="text-muted">${response.clear_go_information || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Helpful Information:</strong> <span class="text-muted">${response.is_helpful_information || 'N/A'}</span></div>
                                <div><strong>Started Application:</strong> <span class="text-muted">${response.started_application || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Need Help:</strong> <span class="text-muted">${response.need_help_in_application || 'N/A'}</span></div>
                                <div><strong>Visited Resources:</strong> <span class="text-muted">${response.visited_our_resources_on_line || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                <div><strong>Career Library Resources:</strong> <span class="text-muted">${response.resources_in_career_library || 'N/A'}</span></div>
                                <div><strong>Is learner on secure pathway:</strong> <span class="text-muted">${response.learner_on_secure_pathway || 'N/A'}</span></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>IAG Learner Plan 1:</strong>
                                    <p class="text-muted">${response.IAG_lerner_plan_1 || 'N/A'}</p>
                                </div>
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>IAG Learner Plan 2:</strong>
                                    <p class="text-muted">${response.IAG_lerner_plan_2 || 'N/A'}</p>
                                </div>
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>IAG Learner Plan 3:</strong>
                                    <p class="text-muted">${response.IAG_lerner_plan_3 || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-3">
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>File Input 1:</strong><br>
                                    ${generateFileLinks(response.file_input_1) || 'N/A'}
                                </div>
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>File Input 2:</strong><br>
                                    ${generateFileLinks(response.file_input_2) || 'N/A'}
                                </div>
                                <div class="bg-[var(--secondary)] border rounded p-3">
                                    <strong>File Input 3:</strong><br>
                                    ${generateFileLinks(response.file_input_3) || 'N/A'}
                                </div>
                            </div>
                            <div class="term-data-container p-4 border rounded">
                                <h3 class="section-title text-center mb-4">Term Data</h3>
                                ${Array.isArray(response.term_name) && response.term_name.length > 0 ?
                                    response.term_name.map((term, index) => `
                                                            <div class="term-set mb-4 p-3 border rounded">
                                                                <h4 class="term-title text-center mb-3">Term Set ${index + 1}</h4>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                                                    <div><strong>Term Name:</strong> <span class="text-muted">${term || 'N/A'}</span></div>
                                                                    <div><strong>Staff Lead:</strong> <span class="text-muted">${response.staff_lead[index] || 'N/A'}</span></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                                                    <div><strong>Date:</strong> <span class="text-muted">${formatDate(response.date[index]) || 'N/A'}</span></div>
                                                                    <div><strong>Meeting Notes:</strong> <span class="text-muted">${response.meeting_notes[index] || 'N/A'}</span></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                                                    <div><strong>Secure Pathway:</strong> <span class="text-muted">${response.secure_pathway[index] || 'N/A'}</span></div>
                                                                    <div><strong>IAG Target 1:</strong> <span class="text-muted">${response.iag_target1[index] || 'N/A'}</span></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                                                    <div><strong>Deadline:</strong> <span class="text-muted">${formatDate(response.deadline[index]) || 'N/A'}</span></div>
                                                                    <div><strong>IAG Target 2:</strong> <span class="text-muted">${response.iag_target2[index] || 'N/A'}</span></div>
                                                                </div>
                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                                                    <div><strong>IAG Target 3:</strong> <span class="text-muted">${response.iag_target3[index] || 'N/A'}</span></div>
                                                                    <div><strong>File Link:</strong> ${generateFileLinks(response.file[index]) || 'N/A'}</div>
                                                                </div>
                                                            </div>
                                                        `).join('') :
                                    `<p class="text-center text-muted">No term data available.</p>`
                                            }
                                        </div>
                                    </div>
                                `);
                    },
                    error: function(xhr) {
                        Toastify({
                            text: xhr.status === 404 ? xhr.responseJSON.message :
                                'candidate not found',
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                    }
                });
            });

            // Format Date Function
            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return 'N/A';
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }

            // Generate File Links Function
            function generateFileLinks(files) {
                if (!files) return 'N/A';
                const fileArray = files.split(',').filter(file => file.trim() !== '');
                if (fileArray.length === 0) return 'N/A';
                return fileArray.map((file, index) => {
                    const filePath = `/${file.trim()}`;
                    return `<a href="${filePath}" target="_blank" class="text-primary">Doc ${index + 1}</a>`;
                }).join(', ');
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
    <script>
        // learner-name-change.js
        $(document).ready(function() {
            $('#learner_name').on('change', function() {
                const learnerName = $(this).val();
                const familyId = $('#family_id').val().trim();

                // Hide & reset session dropdown + loading
                $('#session-wrapper, #loading-message').hide();
                $('#session_select').empty().append(
                    '<option value="" disabled selected>Loading...</option>');

                // If no name or family ID, stop
                if (!learnerName || !familyId) return;

                // Show "Please wait..."
                $('#loading-message').show();

                $.ajax({
                    url: "{{ url('get/learner/sessions') }}",
                    type: "GET",
                    data: {
                        family_id: familyId,
                        learner_name: learnerName,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#loading-message').hide();

                        if (response.sessions && response.sessions.length > 0) {
                            const $select = $('#session_select');
                            $select.empty().append(
                                '<option value="" disabled selected>Select a session</option>'
                            );

                            $.each(response.sessions, function(idx, session) {
                                $select.append(
                                    `<option value="${session}">${session}</option>`
                                );
                            });

                            $('#session-wrapper').show();
                            $select.removeClass('error').addClass('valid');
                        } else {
                            Toastify({
                                text: "No sessions & learner found for this learner",
                                duration: 4000,
                                backgroundColor: "#ef4444",
                                gravity: "top",
                                position: "right"
                            }).showToast();
                        }
                    },
                    error: function() {
                        $('#loading-message').hide();
                        Toastify({
                            text: "No sessions & learner found for this learner.",
                            duration: 4000,
                            backgroundColor: "#ef4444",
                            gravity: "top",
                            position: "right"
                        }).showToast();
                    }
                });
            });
        });
    </script>
@endsection
