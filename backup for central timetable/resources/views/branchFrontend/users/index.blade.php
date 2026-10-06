@extends('layouts.branchDashboardApp')

@section('content')

<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">User Management</h1>
                <p class="text-lg text-[var(--text-light)]">View and manage all users in the system.</p>
            </div>
            <button type="button" id="add_new_button" class="btn-primary text-base flex items-center" data-bs-toggle="modal" data-bs-target="#ajaxModal">
                <i class="fas fa-plus-circle mr-2"></i>Add New User
            </button>
        </div>

        <!-- Add New User Modal -->
        <div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalHeading"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm" name="userForm">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span>*</span></label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Username">
                                <span id="usernameError" class="error_messages"></span>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address <span>*</span></label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                                <div class="form-text">We'll never share your email with anyone else.</div>
                                <span id="emailError" class="error_messages"></span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Regex Password <span id="star"></span></label>
                                <input type="text" name="password" id="password" class="form-control" placeholder="Password">
                                <span id="customPasswordLabel"></span>
                                <span id="passwordError" class="error_messages"></span>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span style="opacity: 0.5;">(optional)</span></label>
                                <select name="role" id="role" class="form-control bootstrap-select" data-style="btn-light">
                                    <option disabled selected>Select option</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            @if (old('role') == $role->name) selected @endif>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <span id="roleError" class="error_messages"></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary saveBtnn" id="saveBtn">Save User</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="font-weight-bold mb-0">User List</h4>
                <a href="{{ url('logout-all') }}" class="btn-primary flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout All Users
                </a>
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
                                        <span style="color: var(--primary); font-size: 0.8rem;">●</span> Online
                                    @else
                                        <span style="color: var(--primary); font-size: 0.8rem;">●</span> Offline
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
                                        <a href="{{ route('logout.single', $user->id) }}" title="Logout" class="btn-warning btn-sm flex items-center">
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
        --warning: #FFCA28;
        --warning-dark: #FFB300;
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
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid var(--border);
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

    .registration-container .btn-warning {
        background: linear-gradient(45deg, var(--warning-dark), var(--warning));
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
        background: linear-gradient(45deg, var(--warning), #ffd966);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(255, 202, 40, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-warning:active {
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

    .registration-container .modal-content {
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.1);
        background: #ffffff;
    }

    .registration-container .modal-header {
        background: var(--secondary);
        color: var(--primary-dark);
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid var(--border);
    }

    .registration-container .modal-body {
        color: var(--text-dark);
        font-weight: 500;
        background: #ffffff;
        padding: 1.25rem;
    }

    .registration-container .form-control,
    .registration-container .bootstrap-select .dropdown-toggle {
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        padding: 0.5rem 0.75rem;
        background: var(--secondary);
        color: var(--text-dark);
        font-size: 0.875rem;
    }

    .registration-container .form-control::placeholder,
    .registration-container .bootstrap-select .dropdown-toggle::placeholder {
        color: var(--text-light);
    }

    .registration-container .form-label {
        color: var(--primary-dark);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .registration-container .form-text {
        color: var(--text-light);
        font-weight: 500;
        font-size: 0.75rem;
    }

    .registration-container .error_messages {
        font-size: 0.75rem;
        color: var(--error);
    }

    .registration-container .btn-close {
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23111827'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707A1 1 0 01.293.293z'/%3e%3c/svg%3e") center/1em auto no-repeat;
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

        // Save User
        $('#saveBtn').click(function(e) {
            e.preventDefault();
            $(this).html('Saving...');
            $.ajax({
                data: $('#userForm').serialize(),
                url: "{{ route('admin.user.store') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $('#userForm').trigger("reset");
                    $('#ajaxModal').modal('hide');
                    Toastify({
                        text: "User saved successfully!",
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
                        $("#passwordError").html(xhr.responseJSON.errorMessage);
                    }
                    if (xhr.responseJSON.errors) {
                        $("#usernameError").html(xhr.responseJSON.errors.username || '');
                        $("#emailError").html(xhr.responseJSON.errors.email || '');
                        $("#passwordError").html(xhr.responseJSON.errors.password || '');
                        $("#roleError").html(xhr.responseJSON.errors.role || '');
                        $("#customPasswordLabel").html('');
                    }
                    if (xhr.responseJSON.email_error) {
                        $("#emailError").html(xhr.responseJSON.email_error);
                    }
                    if (xhr.responseJSON.username_error) {
                        $("#usernameError").html(xhr.responseJSON.username_error);
                    }
                    $('#saveBtn').html('Save User');
                }
            });
        });

        // Delete User
        $('body').on('click', '.deleteButton', function() {
            var user_id = $(this).data("id");
            if (confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/users/delete') }}/" + user_id,
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
                            text: "Failed to delete user.",
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
        });

        $('body').on('click', '.editButton', function() {
            var user_id = $(this).data('id');
            $.get("{{ url('admin/users') }}/" + user_id + "/edit", function(data) {
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
                $('.error_messages').html('');
            }).fail(function(xhr) {
                console.error('Edit error:', xhr.responseText);
                Toastify({
                    text: "Failed to load user data.",
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
