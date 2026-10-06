@extends('layouts.branchDashboardApp')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Add Mock Results</h1>
        </div>

        <!-- Form Section -->
        <div class="card form-section">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-clipboard-list h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Mock Results Details</h2>
            </div>
            <form id="addMockResultsForm" action="{{ url('store/mock-results') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Family ID Field -->
                    <div>
                        <label for="family_id" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID</label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="family_id" name="family_id" class="input-field pl-10" placeholder="Enter family ID" required>
                            <div class="error-message">Please enter a valid family ID.</div>
                            @error('family_id')
                                <div class="error-message d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Student Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Student Name</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="name" name="name" class="input-field pl-10" required>
                                <option value="" disabled selected>Select a student</option>
                            </select>
                            <div class="error-message">Please select a student.</div>
                        </div>
                    </div>

                    <!-- Subject Field -->
                    <div>
                        <label for="subject" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Subject</label>
                        <div class="relative">
                            <i class="fas fa-book absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="subject" name="subject" class="input-field pl-10" required>
                                <option value="" disabled selected>Choose Subject</option>
                                <option value="MATH">MATH</option>
                                <option value="ENGLISH">ENGLISH</option>
                                <option value="SCIENCE">SCIENCE</option>
                                <option value="PHYSICS">PHYSICS</option>
                                <option value="CHEMISTRY">CHEMISTRY</option>
                                <option value="BIOLOGY">BIOLOGY</option>
                                <option value="E.LANGUAGE">E.LANGUAGE</option>
                                <option value="E.LITERATURE">E.LITERATURE</option>
                                <option value="BUSINESS">BUSINESS</option>
                                <option value="ECONOMICS">ECONOMICS</option>
                                <option value="COMPUTER SCIENCE">COMPUTER SCIENCE</option>
                                <option value="NON-VERBAL REASONING">NON-VERBAL REASONING</option>
                                <option value="VERBAL REASONING">VERBAL REASONING</option>
                                <option value="PSYCHOLOGY">PSYCHOLOGY</option>
                                <option value="SOCIOLOGY">SOCIOLOGY</option>
                                <option value="CITIZENSHIP">CITIZENSHIP</option>
                                <option value="POLITICS">POLITICS</option>
                                <option value="LAW">LAW</option>
                                <option value="Arabic">Arabic</option>
                                <option value="History">History</option>
                            </select>
                            <div class="error-message">Please select a subject.</div>
                        </div>
                    </div>

                    <!-- Mock Type Field -->
                    <div>
                        <label for="mock_type" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Mock Type</label>
                        <div class="relative">
                            <i class="fas fa-clipboard-list absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="mock_type" name="mock_type" class="input-field pl-10" required>
                                <option value="" disabled selected>Select mock type</option>
                                <option value="October">October</option>
                                <option value="December">December</option>
                            </select>
                            <div class="error-message">Please select a mock type.</div>
                        </div>
                    </div>

                    <!-- Exam Date Field -->
                    {{-- <div>
                        <label for="exam_date" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Exam Date</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="date" id="exam_date" name="exam_date" class="input-field pl-10" required>
                            <div class="error-message">Please select an exam date.</div>
                        </div>
                    </div> --}}

                    <div>
                        <label for="exam_date" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Exam Date
                            <span style="color: red; font-size: 12px;">(required)</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="exam_date" name="exam_date" class="input-field pl-10" autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  placeholder="dd/mm/yyyy" required>
                            <div class="error-message">Please select an exam date.</div>
                        </div>
                    </div>

                    <!-- Percentage Field -->
                    <div>
                        <label for="percentage" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Percentage</label>
                        <div class="relative">
                            <i class="fas fa-percentage absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="number" id="percentage" name="percentage" class="input-field pl-10" min="0" max="100" placeholder="Enter percentage" required>
                            <div class="error-message">Please enter a percentage between 0 and 100.</div>
                        </div>
                    </div>

                    <!-- Qualification Field -->
                    <div>
                        <label for="qualification" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Qualification</label>
                        <div class="relative">
                            <i class="fas fa-graduation-cap absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="qualification" name="qualification" class="input-field pl-10" required>
                                <option value="" disabled selected>Select Qualification</option>
                                <option value="GCSE">GCSE</option>
                                <option value="A-Level">A-Level</option>
                            </select>
                            <div class="error-message">Please select a qualification.</div>
                        </div>
                    </div>

                    <!-- Tier Field -->
                    <div>
                        <label for="tier" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Tier</label>
                        <div class="relative">
                            <i class="fas fa-layer-group absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="tier" name="tier" class="input-field pl-10" required>
                                <option value="" disabled selected>Select Tier</option>
                                <option value="Higher">Higher</option>
                                <option value="Foundation">Foundation</option>
                                <option value="N/A">N/A</option>
                            </select>
                            <div class="error-message">Please select a tier.</div>
                        </div>
                    </div>

                    <!-- Fine Grade Field -->
                    <div>
                        <label for="fine_grade" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Fine Grade</label>
                        <div class="relative">
                            <i class="fas fa-award absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select id="fine_grade" name="fine_grade" class="input-field pl-10" required>
                                <option value="" disabled selected>Select a grade</option>
                            </select>
                            <div class="error-message">Please select a grade.</div>
                        </div>
                    </div>

                    <!-- Exam Marked By Field -->
                    <div>
                        <label for="exam_marked_by" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Exam Marked By</label>
                        <div class="relative">
                            <i class="fas fa-user-check absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="exam_marked_by" name="exam_marked_by" class="input-field pl-10" placeholder="Enter examiner name" required>
                            <div class="error-message">Please enter the examiner's name.</div>
                        </div>
                    </div>

                    <!-- Updated By Field -->
                    <div>
                        <label for="updated_by" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Updated By</label>
                        <div class="relative">
                            <i class="fas fa-user-edit absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="updated_by" name="updated_by" class="input-field pl-10" value="{{ auth()->user()->name ?? 'N/A' }}" readonly>
                        </div>
                    </div>

                    <!-- Step 1 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="step1" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Step 1</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="step1" name="step1" class="input-field pl-10" placeholder="Enter step 1 details" required>
                            <div class="error-message">Please enter step 1 details.</div>
                        </div>
                    </div>

                    <!-- Step 2 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="step2" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Step 2</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="step2" name="step2" class="input-field pl-10" placeholder="Enter step 2 details" required>
                            <div class="error-message">Please enter step 2 details.</div>
                        </div>
                    </div>

                    <!-- Step 3 Field -->
                    <div class="col-span-1 sm:col-span-2">
                        <label for="step3" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Step 3</label>
                        <div class="relative">
                            <i class="fas fa-list-ol absolute left-3 top-3 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="step3" name="step3" class="input-field pl-10" placeholder="Enter step 3 details" required>
                            <div class="error-message">Please enter step 3 details.</div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-span-1 sm:col-span-2 flex justify-center mt-4">
                        <button type="submit" class="btn btn-primary flex items-center">
                            <i class="fas fa-save mr-2"></i>Add Results
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

    .registration-container .error-message.d-block {
        display: block;
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

    .registration-container .section-icon {
        transition: transform 0.3s ease;
    }

    .registration-container .section-icon:hover {
        transform: scale(1.2);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        flatpickr("#exam_date", {
            dateFormat: "d/m/Y",
            allowInput: true,
            locale: {
                firstDayOfWeek: 1 // Set Monday as the first day of the week
            }
        });
        // Form Validation
        const form = document.getElementById('addMockResultsForm');
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const fields = form.querySelectorAll('input[required], select[required]');
            const emptyFields = [];

            fields.forEach(field => {
                if (!field.value || field.value === '' || field.value === 'Choose Subject' || field.value === 'Select a student' || field.value === 'Select mock type' || field.value === 'Select Qualification' || field.value === 'Select Tier' || field.value === 'Select a grade') {
                    isValid = false;
                    field.classList.add('error');
                    field.classList.remove('valid');
                    const errorElement = field.parentElement.querySelector('.error-message');
                    if (errorElement) errorElement.style.display = 'block';
                    emptyFields.push(field.name.replace(/_/g, ' ').replace(/(?:^|\s)\S/g, a => a.toUpperCase()));
                } else {
                    field.classList.add('valid');
                    field.classList.remove('error');
                    const errorElement = field.parentElement.querySelector('.error-message');
                    if (errorElement) errorElement.style.display = 'none';
                }
            });

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
                Toastify({
                    text: `Please complete the form:\n${emptyFields.map(f => `• ${f}`).join('\n')}`,
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            }
        });

        // Real-time validation
        $(document).on('input change', 'input[required], select[required]', function() {
            const field = $(this);
            const errorElement = field.parent().find('.error-message');
            if (field.val().trim() === '' || field.val() === 'Choose Subject' || field.val() === 'Select a student' || field.val() === 'Select mock type' || field.val() === 'Select Qualification' || field.val() === 'Select Tier' || field.val() === 'Select a grade') {
                field.addClass('error').removeClass('valid');
                errorElement.show();
            } else {
                field.addClass('valid').removeClass('error');
                errorElement.hide();
            }
        });

        // Family ID AJAX for Student Names
        $('#family_id').on('blur', function() {
            const familyId = $(this).val().trim();

            if (familyId !== "") {


                $.ajax({
                    url: "{{ url('get/family/rec') }}",
                    type: "GET",
                    data: {
                        family_id: familyId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        const nameSelect = $('#name');
                        nameSelect.find('option:not(:first)').remove();

                        if (response.length > 0) {
                            $.each(response, function(index, item) {
                                const fullName = (typeof item === 'object' && item !== null) ? item.name : item;
                                const isFlagged = (typeof item === 'object' && item !== null) ? item.is_flag == 1 : false;
                                const flagIcon = isFlagged ? ' &#x1F6A9;' : '';
                                nameSelect.append(`<option value="${fullName}">${fullName}${flagIcon}</option>`);
                            });
                            nameSelect.addClass('valid').removeClass('error');
                            Toastify({
                                text: "Student names loaded successfully!",
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#10b981",
                                stopOnFocus: true,
                            }).showToast();
                        } else {
                            Toastify({
                                text: "No records found for this family ID.",
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#ef4444",
                                stopOnFocus: true,
                            }).showToast();
                            nameSelect.addClass('error').removeClass('valid');
                        }
                    },
                    error: function(xhr) {
                        Toastify({
                            text: "An error occurred while fetching data. Please try again later.",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                        console.error(xhr.responseText);
                        $('#name').addClass('error').removeClass('valid');
                    }
                });
            }
        });

        // Qualification-based Fine Grade Population
        $('#qualification').on('change', function() {
            const fineGradeSelect = $('#fine_grade');
            fineGradeSelect.find('option:not(:first)').remove();

            if (this.value === 'GCSE') {
                const gcseGrades = [
                    @foreach (range(9, 1) as $grade)
                        {value: "{{ $grade }}A", text: "{{ $grade }}A"},
                        {value: "{{ $grade }}B", text: "{{ $grade }}B"},
                        {value: "{{ $grade }}C", text: "{{ $grade }}C"},
                    @endforeach
                    {value: "U", text: "U"}
                ];

                gcseGrades.forEach(grade => {
                    fineGradeSelect.append(`<option value="${grade.value}">${grade.text}</option>`);
                });
            } else if (this.value === 'A-Level') {
                const aLevelGrades = ['A*', 'A', 'B', 'C', 'D', 'E'];
                aLevelGrades.forEach(grade => {
                    fineGradeSelect.append(`<option value="${grade}">${grade}</option>`);
                });
            }
            fineGradeSelect.val('');
            fineGradeSelect.removeClass('valid error');
            fineGradeSelect.parent().find('.error-message').hide();
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
</script>

@endsection
