@extends('layouts.superAdminApp')

@section('content')

<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Add Users</h1>

        </div>

        <!-- Users Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <button type="button" id="add_new_button" class="btn-primary text-base flex items-center" data-bs-toggle="modal" data-bs-target="#ajaxModal">
                    <i class="fas fa-plus mr-2"></i>Add User
                </button>
                {{-- <a href="{{ url('logout-all') }}" class="btn-danger text-base flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout All Users
                </a> --}}
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Visible Password</th>
                            <th>User Type</th>
                            <th>Login Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->visible_password ?? '' }}</td>
                                <td>{{ $user->usertype ?? '' }}</td>
                                <td>
                                    @if ($user->login_session == 1)
                                        <span class="text-[var(--primary)]">●</span> Online
                                    @else
                                        <span class="text-[var(--text-light)]">●</span> Offline
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0)" data-id="{{ $user->id }}" title="Edit" class="btn-primary btn-sm flex items-center editButton">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="javascript:void(0)" data-id="{{ $user->id }}" title="Delete" class="btn-danger btn-sm flex items-center deleteButton">
                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                        </a>
                                        <a href="{{ route('admin.superadmin-user.logout', $user->id) }}" title="Logout" class="btn-warning btn-sm flex items-center">
                                            <i class="fas fa-sign-out-alt mr-1"></i>Logout
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add New User Modal -->
        <div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="modalHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm" name="userForm">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="mb-3">
                                <label for="username" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Username <span class="text-[var(--error)]">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-at absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                    <input type="text" name="username" id="username" class="input-field pl-10" placeholder="Enter username">
                                </div>
                                <div class="error-message" id="usernameError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Email address <span class="text-[var(--error)]">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                    <input type="email" name="email" id="email" class="input-field pl-10" placeholder="Enter email">
                                </div>
                                <div class="error-message" id="emailError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Password <span id="star" class="text-[var(--error)]">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                    <input type="text" name="password" id="password" class="input-field pl-10 pr-10" placeholder="Enter password">
                                    <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] hover:text-[var(--primary)]" id="togglePassword">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                                <div class="text-sm text-[var(--text-light)] mt-1" id="customPasswordLabel"></div>
                                <div class="error-message" id="passwordError"></div>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="block text-sm font-medium text-[var(--text-dark)] mb-1">Role <span class="opacity-50">(optional)</span></label>
                                <div class="relative">
                                    <i class="fas fa-user-tag absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                    <select name="role" id="role" class="input-field pl-10">
                                        <option value="" disabled selected>Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="error-message" id="roleError"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-primary" style="background: #d1d5db;" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn-primary saveBtnn" id="saveBtn">Save User</button>
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
        --warning: #f59e0b;
        --warning-light: #fed7aa;
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

    .registration-container .card-header {
        background: var(--secondary);
        color: var(--primary-dark);
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem;
        font-weight: 600;
        font-size: 1rem;
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
    }

    .registration-container .btn-warning {
        background: linear-gradient(45deg, var(--warning), #fbbf24);
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

    .registration-container .btn-warning:hover {
        background: linear-gradient(45deg, #fbbf24, var(--warning-light));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.2);
    }

    .registration-container .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        height: 2rem;
    }

    .registration-container .btn-group .btn {
        margin-right: 0.5rem;
    }

    .registration-container .modal-content {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.15);
    }

    .registration-container .modal-header {
        background: linear-gradient(45deg, var(--primary-dark), var(--primary));
        color: white;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem;
    }

    .registration-container .modal-body {
        color: var(--text-dark);
        background: #ffffff;
        padding: 1.5rem;
    }

    .registration-container .modal-footer {
        border-top: 1px solid var(--border);
        padding: 0.75rem 1rem;
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

    .registration-container .relative button i {
        font-size: 0.875rem;
    }

    .registration-container .error-message {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
    }

    .registration-container .custom-message {
        position: fixed;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(45deg, #10b981, #34d399);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        display: none;
        z-index: 1050;
        max-width: 300px;
        font-size: 0.875rem;
    }

    .registration-container .btn-close {
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707A1 1 0 01.293.293z'/%3e%3c/svg%3e") center/1em auto no-repeat;
        opacity: 0.8;
    }

    .registration-container .btn-close:hover {
        opacity: 1;
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

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Password Visibility Toggle
        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        // Real-time validation for modal inputs
        document.querySelectorAll('#userForm .input-field').forEach(field => {
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

            if (field.name === 'username') {
                const username = field.value.trim();
                if (!username) {
                    isValid = false;
                    errorMessage = 'Please enter a username';
                } else if (username.length > 50) {
                    isValid = false;
                    errorMessage = 'Username cannot exceed 50 characters';
                }
            } else if (field.name === 'email') {
                const email = field.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email) {
                    isValid = false;
                    errorMessage = 'Please enter an email address';
                } else if (!emailRegex.test(email)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
            } else if (field.name === 'password' && field.value && field.value.length < 8) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters long';
            }

            const errorElement = field.parentElement.nextElementSibling;
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

        // Save User
        $('#saveBtn').click(function(e) {
            e.preventDefault();
            $(this).html('Saving...');

            // Reset validation states
            $('#userForm .input-field').removeClass('error valid');
            $('#userForm .error-message').hide();

            let isValid = true;
            let errorMessages = [];
            let firstErrorElement = null;

            $('#userForm .input-field').each(function() {
                const field = $(this)[0];
                let fieldValid = true;
                let errorMessage = '';

                const label = field.parentElement.previousElementSibling;
                const labelText = label ? label.textContent.replace('*', '').trim() : field.name;

                if (field.name === 'username') {
                    const username = field.value.trim();
                    if (!username) {
                        fieldValid = false;
                        errorMessage = 'Please enter a username';
                    } else if (username.length > 50) {
                        fieldValid = false;
                        errorMessage = 'Username cannot exceed 50 characters';
                    }
                } else if (field.name === 'email') {
                    const email = field.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!email) {
                        fieldValid = false;
                        errorMessage = 'Please enter an email address';
                    } else if (!emailRegex.test(email)) {
                        fieldValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                } else if (field.name === 'password' && field.value && field.value.length < 8) {
                    fieldValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                }

                const errorElement = field.parentElement.nextElementSibling;
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

            if (!isValid) {
                $(this).html('Save User');
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
                    firstErrorElement.focus();
                }
                return;
            }

            var userId = $('#user_id').val();
            var url = userId ? "{{ url('superadmin-users') }}/" + userId : "{{ route('admin.superadmin-user.store') }}";
            var type = userId ? "PUT" : "POST";
            $.ajax({
                data: $('#userForm').serialize(),
                url: url,
                type: type,
                dataType: 'json',
                success: function(data) {
                    $('#userForm').trigger("reset");
                    $('#ajaxModal').modal('hide');
                    Toastify({
                        text: userId ? "User updated successfully!" : "User saved successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#10b981",
                        stopOnFocus: true,
                    }).showToast();
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Save error:', xhr.responseText);
                    if (xhr.responseJSON.errorMessage) {
                        $("#passwordError").html(xhr.responseJSON.errorMessage).show();
                    }
                    if (xhr.responseJSON.errors) {
                        $("#usernameError").html(xhr.responseJSON.errors.username || '').show();
                        $("#emailError").html(xhr.responseJSON.errors.email || '').show();
                        $("#passwordError").html(xhr.responseJSON.errors.password || '').show();
                        $("#roleError").html(xhr.responseJSON.errors.role || '').show();
                        $("#customPasswordLabel").html('');
                    }
                    if (xhr.responseJSON.email_error) {
                        $("#emailError").html(xhr.responseJSON.email_error).show();
                    }
                    if (xhr.responseJSON.username_error) {
                        $("#usernameError").html(xhr.responseJSON.username_error).show();
                    }
                    $('#saveBtn').html('Save User');
                    Toastify({
                        text: "Failed to save user. Please check the form for errors.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                }
            });
        });

        // Delete User
        $('body').on('click', '.deleteButton', function() {
            var user_id = $(this).data("id");
            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('superadmin-users/delete') }}/" + user_id,
                    success: function(data) {
                        Toastify({
                            text: "User deleted successfully!",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#10b981",
                            stopOnFocus: true,
                        }).showToast();
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText);
                        Toastify({
                            text: "Failed to delete user. Please try again.",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                    }
                });
            }
        });

        // Add/Edit User
        $('#add_new_button').click(function() {
            $('#saveBtn').html("Save User");
            $('#user_id').val('');
            $('.saveBtn').val('create-user');
            $('.error_messages').html('');
            $('#userForm').trigger("reset");
            $('#modalHeading').html("Create User");
            $('#role').val('').trigger('change');
            $('#customPasswordLabel').html('');
            $('#star').html('*');
            $('#userForm .input-field').removeClass('error valid');
            $('#userForm .error-message').hide();
        });

        $('body').on('click', '.editButton', function() {
            var user_id = $(this).data('id');
            $.get("{{ url('superadmin-users') }}/" + user_id + "/edit", function(data) {
                $('#modalHeading').html("Edit User");
                $('#saveBtn').html("Save User");
                $("#ajaxModal").modal("show");
                $('#user_id').val(data.user.id);
                $('#username').val(data.user.name);
                $('#email').val(data.user.email);
                $('#customPasswordLabel').html('Previous password: ');
                $('#password').val(data.user.visible_password || '');
                var roleValue = data.user.usertype || data.user.role || '';
                $('#role').val(roleValue).trigger('change');
                $('#star').html('');
                $('.error_messages').html('');
                $('#userForm .input-field').removeClass('error valid');
                $('#userForm .error-message').hide();
            }).fail(function(xhr) {
                console.error('Edit error:', xhr.responseText);
                Toastify({
                    text: "Failed to load user data. Please try again.",
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
            });
        });
    });
</script>
@endsection
