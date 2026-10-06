@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Teacher Comments</h1>
                <p class="text-lg text-[var(--text-light)]">View and manage all comments for students in this branch.</p>
            </div>
            <button type="button" class="btn btn-primary text-base flex items-center" data-bs-toggle="modal" data-bs-target="#addCommentModal">
                <i class="fas fa-plus-circle mr-2"></i>Add New Comment
            </button>
        </div>

        <!-- DataTable Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Student Comments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th class="col-id">Family ID</th>
                                <th class="col-name">Student Name</th>
                                <th class="col-comment">Comment</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                            <tr>
                                <td class="col-id">{{ $comment->family_id }}</td>
                                <td class="col-name">{{ $comment->student_name }}</td>
                                <td class="col-comment">{{ $comment->comment }}</td>
                                <td class="col-actions">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary edit-comment-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCommentModal"
                                            data-id="{{ $comment->id }}"
                                            data-family-id="{{ $comment->family_id }}"
                                            data-student-name="{{ $comment->student_name }}"
                                            data-comment="{{ $comment->comment }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ url('delete-comment/' . $comment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Comment Modal -->
        <div class="modal fade" id="addCommentModal" tabindex="-1" aria-labelledby="addCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCommentModalLabel">Add New Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('comment.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="family_idd" class="form-label">Family ID <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="family_id" id="family_idd" class="form-control" placeholder="Enter Family ID" required>
                                </div>
                                @error('family_id')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="students" class="form-label">Student <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <select name="student" id="students" class="form-select" required>
                                        <option selected disabled>Choose name</option>
                                    </select>
                                </div>
                                @error('student')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Enter comment" required></textarea>
                                </div>
                                @error('comment')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Comment Modal -->
        <div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCommentModalLabel">Edit Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('teacher/commentstore') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" name="comment_id" id="comment_id">
                            <div class="mb-3">
                                <label for="family_id" class="form-label">Family ID</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="family_id" id="family_id" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="student" class="form-label">Student</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="student" id="student" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_comment" class="form-label">Comment <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea name="comment" id="edit_comment" class="form-control" rows="4" placeholder="Enter comment" required></textarea>
                                </div>
                                @error('comment')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Hidden fields for branch_id and branch_name -->
                            <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                            <input type="hidden" name="branch_name" value="{{ $branch->branch_name ?? 'N/A' }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Comment</button>
                        </div>
                    </form>
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
        border-bottom: 2px solid var(--border);
    }

    .registration-container .card-title {
        font-weight: 600;
        font-size: 1rem;
        color: var(--primary-dark);
    }

    .registration-container .card-body {
        padding: 1.25rem 1.5rem;
    }

    .registration-container .table-responsive {
        border-radius: 0.5rem;
        overflow-x: auto;
    }

    .registration-container .table {
        --bs-table-bg: #ffffff;
        --bs-table-color: var(--text-dark);
        --bs-table-striped-bg: var(--secondary);
        --bs-table-striped-color: var(--text-dark);
        --bs-table-hover-bg: var(--primary-light);
        --bs-table-hover-color: var(--text-dark);
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: var(--border);
        margin-bottom: 0;
        width: 100%;
        table-layout: fixed;
    }

    .registration-container .table thead th {
        background: var(--secondary);
        color: var(--primary-dark);
        border-bottom: 2px solid var(--border);
        font-weight: 600;
        padding: 0.75rem 1rem;
        text-align: left;
        vertical-align: middle;
    }

    .registration-container .table th.col-id,
    .registration-container .table td.col-id {
        width: 15%;
        text-align: center;
    }

    .registration-container .table th.col-name,
    .registration-container .table td.col-name {
        width: 30%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .registration-container .table th.col-comment,
    .registration-container .table td.col-comment {
        width: 35%;
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .registration-container .table th.col-actions,
    .registration-container .table td.col-actions {
        width: 20%;
        text-align: center;
    }

    .registration-container .table tbody tr {
        transition: all 0.3s ease;
    }

    .registration-container .table tbody td {
        vertical-align: middle;
        border-color: var(--border);
        padding: 0.75rem 1rem;
        color: var(--text-dark);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .registration-container .table tbody tr:hover {
        background: var(--primary-light) !important;
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .registration-container .table tbody tr:hover td {
        color: var(--text-dark) !important;
    }

    .registration-container .table tbody tr:hover .btn-primary {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
    }

    .registration-container .table tbody tr:hover .btn-danger {
        background: linear-gradient(45deg, #dc3545, #f87171);
    }

    .registration-container .form-label {
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.875rem;
    }

    .registration-container .form-control,
    .registration-container .form-select {
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
        background: #ffffff;
        color: var(--text-dark);
    }

    .registration-container .form-control:focus,
    .registration-container .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .registration-container .form-control::placeholder {
        color: var(--text-light);
        opacity: 0.8;
    }

    .registration-container .form-control[readonly] {
        background: #f8fafc;
        opacity: 0.85;
        cursor: not-allowed;
    }

    .registration-container .input-group-text {
        background: var(--secondary);
        color: var(--primary-dark);
        border: 1px solid var(--border);
        border-right: none;
        border-radius: 0.5rem 0 0 0.5rem;
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
    }

    .registration-container .error-message {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
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

    .registration-container .btn-secondary {
        background: var(--text-light);
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

    .registration-container .btn-secondary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-secondary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-danger {
        background: linear-gradient(45deg, #dc3545, #ef4444);
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
        background: linear-gradient(45deg, #ef4444, #f87171);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
        scale: 1.05;
    }

    .registration-container .btn-danger:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        height: 2rem;
    }

    .registration-container .modal-content {
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.2);
    }

    .registration-container .modal-header {
        background: var(--secondary);
        color: var(--primary-dark);
        border-bottom: 2px solid var(--border);
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem;
    }

    .registration-container .modal-title {
        font-weight: 600;
        font-size: 1rem;
    }

    .registration-container .modal-body {
        padding: 1.25rem 1.5rem;
    }

    .registration-container .modal-footer {
        border-top: 1px solid var(--border);
        padding: 0.75rem 1rem;
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    @media (max-width: 768px) {
        .registration-container .container {
            padding: 0.75rem 2rem;
        }
        .registration-container .form-label,
        .registration-container .form-control,
        .registration-container .form-select,
        .registration-container .input-group-text,
        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.75rem;
        }
        .registration-container .btn-primary,
        .registration-container .btn-secondary,
        .registration-container .btn-danger {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            height: 2rem;
        }
        .registration-container .table th.col-id,
        .registration-container .table td.col-id {
            width: 20%;
        }
        .registration-container .table th.col-name,
        .registration-container .table td.col-name {
            width: 25%;
        }
        .registration-container .table th.col-comment,
        .registration-container .table td.col-comment {
            width: 30%;
        }
        .registration-container .table th.col-actions,
        .registration-container .table td.col-actions {
            width: 25%;
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

<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bootstrap Form Validation for Add and Edit Modals
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    Toastify({
                        text: 'Please fill out all required fields correctly.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                } else {
                    form.classList.add('was-validated');
                }
            }, false);
        });

        // Clear Add Comment Modal on open
        const addCommentModal = document.getElementById('addCommentModal');
        addCommentModal.addEventListener('show.bs.modal', function () {
            const form = addCommentModal.querySelector('form');
            form.reset();
            form.classList.remove('was-validated');
            document.getElementById('students').innerHTML = '<option selected disabled>Choose name</option>';
        });

        // Populate Edit Comment Modal
        const editButtons = document.querySelectorAll('.edit-comment-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const familyId = this.getAttribute('data-family-id');
                const studentName = this.getAttribute('data-student-name');
                const comment = this.getAttribute('data-comment');

                document.getElementById('comment_id').value = id;
                document.getElementById('family_id').value = familyId;
                document.getElementById('student').value = studentName;
                document.getElementById('edit_comment').value = comment;
                document.getElementById('editCommentModal').querySelector('form').classList.remove('was-validated');
            });
        });

        // Fetch students based on Family ID for Add Comment Modal
        const familyIdInput = document.getElementById('family_idd');
        const studentSelect = document.getElementById('students');
        familyIdInput.addEventListener('focusout', function () {
            const familyId = this.value.trim();
            if (familyId) {
                fetch("{{ url('teacher/getStudents') }}?family_id=" + encodeURIComponent(familyId), {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    studentSelect.innerHTML = '<option selected disabled>Choose name</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(student => {
                            const option = document.createElement('option');
                            option.value = `${student.studentname} ${student.studentsur}`;
                            option.textContent = `${student.studentname} ${student.studentsur}`;
                            studentSelect.appendChild(option);
                        });
                        studentSelect.classList.add('is-valid').removeClass('is-invalid');
                    } else {
                        Toastify({
                            text: 'No students found for this Family ID.',
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#f59e0b",
                            stopOnFocus: true,
                        }).showToast();
                        studentSelect.classList.add('is-invalid').removeClass('is-valid');
                    }
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                    Toastify({
                        text: 'Failed to fetch students. Please try again.',
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                    studentSelect.classList.add('is-invalid').removeClass('is-valid');
                });
            } else {
                studentSelect.innerHTML = '<option selected disabled>Choose name</option>';
                studentSelect.classList.add('is-invalid').removeClass('is-valid');
            }
        });

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
    });
</script>
@endsection
