@extends('layouts.superAdminApp')

@section('content')




<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Create New Branch</h1>

        </div>

        <!-- Form -->
        <form id="createUserForm" action="{{ url('store-branch') }}" method="POST" class="space-y-8 mt-8" novalidate>
            @csrf
            <!-- Branch Admin Details Card -->
            <div id="admin-details" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-user-tie h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Branch Admin Details</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Username *</label>
                        <div class="relative">
                            <i class="fas fa-at absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" class="input-field pl-10" id="username" name="username" placeholder="Enter username" maxlength="50" required>
                        </div>
                        <div class="error-message" id="username_feedback">Please enter a username (max 50 characters).</div>
                        @error('username')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Password *</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="password" class="input-field pl-10 pr-10" id="password" name="password" placeholder="Enter password" minlength="8" required>
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] hover:text-[var(--primary)]" id="togglePassword">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="error-message">Password must be at least 8 characters long.</div>
                        @error('password')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Role *</label>
                        <div class="relative">
                            <i class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <select class="input-field pl-10" id="role" name="role" required>
                                <option value="" disabled>Select Role</option>
                                <option value="branch_admin" selected>Branch Admin</option>
                            </select>
                        </div>
                        <div class="error-message">Please select a role.</div>
                        @error('role')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Branch Information Card -->
            <div id="branch-info" class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-code-branch h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Branch Information</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div id="branchField">
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Branch Name *</label>
                        <div class="relative">
                            <i class="fas fa-code-branch absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" class="input-field pl-10" id="branch_name" name="branch_name" placeholder="Enter branch name (e.g., Frobel Branch)" maxlength="255" required>
                        </div>
                        <div class="error-message" id="branch_name_feedback">Please enter a valid branch name (min 2 characters).</div>
                        @error('branch_name')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="branchIdField">
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Branch ID *</label>
                        <div class="relative">
                            <i class="fas fa-id-badge absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" class="input-field pl-10" id="branch_id" name="branch_id" placeholder="Auto-generated branch ID" readonly required>
                        </div>
                        <div class="error-message" id="branch_id_feedback">Please enter a valid branch name to generate the branch ID.</div>
                        @error('branch_id')
                            <div class="error-message" style="display: block;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="flex justify-center mt-8 gap-4">
                <button type="submit" class="btn-primary text-base flex items-center">
                    <i class="fas fa-save mr-2"></i>Create Branch
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
        padding: 0.5rem 0.8rem 0.5rem 2.5rem; /* Adjusted padding for icon */
        background: #ffffff;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        width: 100%;
        box-sizing: border-box;
        height: 2.5rem; /* Consistent height */
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
        font-style: normal; /* Remove italic to avoid visual clutter */
    }

    .registration-container .relative i {
        font-size: 0.875rem; /* Match input font size */
        z-index: 1; /* Ensure icon is above input */
    }

    .registration-container .relative button i {
        font-size: 0.875rem; /* Consistent icon size for toggle button */
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
        height: 2.5rem; /* Match input height */
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
</style>

<script>
    // Pass PHP users data to JavaScript
    const users = @json($users->pluck('name')->toArray());

    // Track username validity
    let isUsernameValid = false;

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

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Document ready, initializing form validation...');

        // Password Visibility Toggle
        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
            console.log('Password visibility toggled');
        });

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
            } else if (field.name === 'username') {
                const username = field.value.trim();
                if (username.length > 50) {
                    isValid = false;
                    errorMessage = 'Username cannot exceed 50 characters';
                } else {
                    const lowercaseUsername = username.toLowerCase();
                    const lowercaseUsers = users.map(user => user.toLowerCase());
                    if (lowercaseUsers.includes(lowercaseUsername)) {
                        isValid = false;
                        errorMessage = 'This username already exists. Please choose a different one.';
                        isUsernameValid = false;
                    } else {
                        isUsernameValid = true;
                    }
                }
            } else if (field.name === 'password' && field.value.length < 8) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters long';
            } else if (field.name === 'role' && field.value === '') {
                isValid = false;
                errorMessage = 'Please select a role';
            } else if (field.name === 'branch_name' && field.value.trim().length < 2) {
                isValid = false;
                errorMessage = 'Branch name must be at least 2 characters';
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

        // Branch ID Generation
        const branchNameInput = document.getElementById('branch_name');
        const branchIdInput = document.getElementById('branch_id');
        const branchNameFeedback = document.getElementById('branch_name_feedback');
        const branchIdFeedback = document.getElementById('branch_id_feedback');

        function generateBranchId(branchName) {
            if (!branchName || branchName.trim().length < 2) {
                branchIdInput.value = '';
                branchNameInput.classList.remove('valid');
                branchIdInput.classList.remove('valid');
                branchNameFeedback.textContent = 'Please enter a valid branch name (min 2 characters).';
                branchNameFeedback.style.display = 'block';
                branchIdFeedback.style.display = 'block';
                return;
            }

            let processedName = branchName.toLowerCase()
                .replace(/\s+/g, '_')
                .replace(/[^a-z0-9_]/g, '');
            const randomNum = Math.floor(Math.random() * 900) + 100;
            const branchId = `${processedName}_${randomNum}`;

            branchIdInput.value = branchId;
            branchNameInput.classList.add('valid');
            branchIdInput.classList.add('valid');
            branchNameFeedback.textContent = 'Branch ID generated!';
            branchNameFeedback.style.display = 'block';
            branchIdFeedback.style.display = 'none';
        }

        branchNameInput.addEventListener('input', function() {
            generateBranchId(this.value);
        });

        // Role-based Branch Field Logic
        const roleSelect = document.getElementById('role');
        const branchField = document.getElementById('branchField');
        const branchIdField = document.getElementById('branchIdField');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'super_admin') {
                branchField.style.display = 'none';
                branchIdField.style.display = 'none';
                branchNameInput.removeAttribute('required');
                branchNameInput.value = '';
                branchIdInput.value = '';
                branchIdInput.removeAttribute('required');
                branchNameInput.classList.remove('valid', 'error');
                branchIdInput.classList.remove('valid', 'error');
                branchNameFeedback.style.display = 'none';
                branchIdFeedback.style.display = 'none';
            } else {
                branchField.style.display = 'block';
                branchIdField.style.display = 'block';
                branchNameInput.setAttribute('required', '');
                branchIdInput.setAttribute('required', '');
                if (branchNameInput.value.trim().length >= 2) {
                    generateBranchId(branchNameInput.value);
                }
            }
        });

        // Trigger role change on page load
        roleSelect.dispatchEvent(new Event('change'));

        // Form Submission Validation
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submit event triggered');

            // Reset validation states
            document.querySelectorAll('.registration-container .input-field').forEach(field => field.classList.remove('error', 'valid'));
            document.querySelectorAll('.registration-container .error-message').forEach(msg => msg.style.display = 'none');

            let isValid = true;
            let firstErrorElement = null;
            let errorMessages = [];

            console.log('Starting form validation...');

            document.querySelectorAll('.registration-container .input-field').forEach(field => {
                if (field.hasAttribute('readonly')) return;

                let fieldValid = true;
                let errorMessage = '';

                const label = field.parentElement.previousElementSibling;
                const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

                if (!field.value || field.value.trim() === '') {
                    fieldValid = false;
                    errorMessage = `Please enter a ${labelText}`;
                } else if (field.name === 'username') {
                    const username = field.value.trim();
                    if (username.length > 50) {
                        fieldValid = false;
                        errorMessage = 'Username cannot exceed 50 characters';
                    } else {
                        const lowercaseUsername = username.toLowerCase();
                        const lowercaseUsers = users.map(user => user.toLowerCase());
                        if (lowercaseUsers.includes(lowercaseUsername)) {
                            fieldValid = false;
                            errorMessage = 'This username already exists. Please choose a different one.';
                            isUsernameValid = false;
                        } else {
                            isUsernameValid = true;
                        }
                    }
                } else if (field.name === 'password' && field.value.length < 8) {
                    fieldValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                } else if (field.name === 'role' && field.value === '') {
                    fieldValid = false;
                    errorMessage = 'Please select a role';
                } else if (field.name === 'branch_name' && field.value.trim().length < 2) {
                    fieldValid = false;
                    errorMessage = 'Branch name must be at least 2 characters';
                } else if (field.name === 'branch_id' && !field.value) {
                    fieldValid = false;
                    errorMessage = 'Branch ID is required';
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
                    console.log(`Validation failed for ${field.name}: ${errorMessage}`);
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
    });
</script>
@endsection
