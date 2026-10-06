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
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Books Records</h1>
                <p class="text-lg text-[var(--text-light)]">View and manage all books for this branch.</p>
            </div>
            <button type="button" class="btn btn-primary text-base flex items-center justify-center" data-bs-toggle="modal" data-bs-target="#addBookModal">Add New Book</button>
        </div>

        <!-- DataTable Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Book Inventory</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="booksTable">
                        <thead>
                            <tr>
                                <th class="col-name">Book Name</th>
                                <th class="col-code">Code</th>
                                <th class="col-price">Price (£)</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                            <tr>
                                <td class="col-name">{{ $book->name }}</td>
                                <td class="col-code">{{ $book->code }}</td>
                                <td class="col-price">£ {{ number_format($book->price, 2) }}</td>
                                <td class="col-actions">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary edit-book-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBookModal"
                                            data-id="{{ e($book->id) }}"
                                            data-name="{{ e($book->name) }}"
                                            data-code="{{ e($book->code) }}"
                                            data-price="{{ e($book->price) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ url('books-destroy', $book->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">
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

        <!-- Add Book Modal -->
        <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addBookModalLabel">Add New Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('books') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="bookName" class="form-label">Book Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <input type="text" class="form-control" id="bookName" name="name" placeholder="Enter book name" required>
                                </div>
                                @error('name')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="bookCode" class="form-label">Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" class="form-control" id="bookCode" name="code" placeholder="Enter book code" required>
                                </div>
                                @error('code')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="bookPrice" class="form-label">Price (£) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-pound-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control" id="bookPrice" name="price" placeholder="Enter price" required min="0">
                                </div>
                                @error('price')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                            <button type="submit" class="btn btn-primary">Save Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Book Modal -->
        <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ url('books-update') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="editBookId" name="id">
                            <div class="mb-3">
                                <label for="editBookName" class="form-label">Book Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <input type="text" class="form-control" id="editBookName" name="name" placeholder="Enter book name" required>
                                </div>
                                @error('name')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editBookCode" class="form-label">Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" class="form-control" id="editBookCode" name="code" placeholder="Enter book code" required>
                                </div>
                                @error('code')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editBookPrice" class="form-label">Price (£) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-pound-sign"></i></span>
                                    <input type="number" step="0.01" class="form-control" id="editBookPrice" name="price" placeholder="Enter price" required min="0">
                                </div>
                                @error('price')
                                    <div class="error-message mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Book</button>
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
        padding: 1.5rem 2rem;
    }

    .registration-container .card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin: 0;
    }

    .registration-container .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(29, 78, 216, 0.15);
    }

    .registration-container .card-header {
        background: var(--secondary);
        color: var(--primary-dark);
        border-radius: 0.75rem 0.75rem 0 0;
        padding: 1rem 1.5rem;
        border-bottom: 2px solid var(--border);
    }

    .registration-container .card-title {
        font-weight: 600;
        font-size: 1.125rem;
        color: var(--primary-dark);
    }

    .registration-container .card-body {
        padding: 1.5rem;
    }

    .registration-container .table-responsive {
        border-radius: 0.5rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .registration-container .table {
        --bs-table-bg: #ffffff;
        --bs-table-color: var(--text-dark);
        --bs-table-striped-bg: var(--secondary);
        --bs-table-hover-bg: var(--primary-light);
        --bs-table-hover-color: var(--text-dark);
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: var(--border);
        margin-bottom: 0;
        width: 100%;
        min-width: 500px; /* Ensures horizontal scrolling on small screens */
    }

    .registration-container .table thead th {
        background: var(--secondary);
        color: var(--primary-dark);
        border-bottom: 2px solid var(--border);
        font-weight: 600;
        padding: 0.75rem;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .registration-container .table th.col-name,
    .registration-container .table td.col-name {
        width: 35%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }

    .registration-container .table th.col-code,
    .registration-container .table td.col-code {
        width: 25%;
        text-align: center;
    }

    .registration-container .table th.col-price,
    .registration-container .table td.col-price {
        width: 20%;
        text-align: center;
    }

    .registration-container .table th.col-actions,
    .registration-container .table td.col-actions {
        width: 20%;
        text-align: center;
    }

    .registration-container .table tbody tr {
        transition: all 0.2s ease;
    }

    .registration-container .table tbody td {
        vertical-align: middle;
        border-color: var(--border);
        padding: 0.75rem;
        color: var(--text-dark);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .registration-container .table tbody tr:hover {
        background: var(--primary-light) !important;
        transform: translateX(2px);
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

    .registration-container .form-control {
        border: 1px solid var(--border);
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        background: #ffffff;
        color: var(--text-dark);
        height: 2.5rem;
    }

    .registration-container .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .registration-container .form-control::placeholder {
        color: var(--text-light);
        opacity: 0.8;
    }

    .registration-container .input-group-text {
        background: var(--secondary);
        color: var(--primary-dark);
        border: 1px solid var(--border);
        border-right: none;
        border-radius: 0.375rem 0 0 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .registration-container .error-message {
        color: var(--error);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .registration-container .btn-primary {
        background: linear-gradient(45deg, var(--primary-dark), var(--primary));
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        touch-action: manipulation;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center; /* Center content */
        gap: 0.25rem; /* Space between icon and text */
    }

    .registration-container .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        scale: 1.03;
    }

    .registration-container .btn-primary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-secondary {
        background: var(--text-light);
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        touch-action: manipulation;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .registration-container .btn-secondary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        scale: 1.03;
    }

    .registration-container .btn-secondary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-danger {
        background: linear-gradient(45deg, #dc3545, #ef4444);
        border: none;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        touch-action: manipulation;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .registration-container .btn-danger:hover {
        background: linear-gradient(45deg, #ef4444, #f87171);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        scale: 1.03;
    }

    .registration-container .btn-danger:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .registration-container .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        height: 2rem;
    }

    .registration-container .modal-content {
        border-radius: 0.75rem;
        border: 1px solid var(--border);
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.15);
    }

    .registration-container .modal-header {
        background: var(--secondary);
        color: var(--primary-dark);
        border-bottom: 2px solid var(--border);
        border-radius: 0.75rem 0.75rem 0 0;
        padding: 1rem 1.5rem;
    }

    .registration-container .modal-title {
        font-weight: 600;
        font-size: 1.125rem;
    }

    .registration-container .modal-body {
        padding: 1.5rem;
    }

    .registration-container .modal-footer {
        border-top: 1px solid var(--border);
        padding: 1rem 1.5rem;
    }

    .registration-container .scroll-smooth {
        scroll-behavior: smooth;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #2047A8;
        margin-top: -6px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .registration-container .container {
            max-width: 100%;
            padding: 1.25rem 1.75rem;
        }

        .registration-container h1 {
            font-size: 2rem;
        }

        .registration-container p {
            font-size: 1rem;
        }

        .registration-container .card-title,
        .registration-container .modal-title {
            font-size: 1rem;
        }

        .registration-container .form-label,
        .registration-container .form-control,
        .registration-container .input-group-text,
        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.813rem;
        }

        .registration-container .btn-primary,
        .registration-container .btn-secondary,
        .registration-container .btn-danger {
            padding: 0.4rem 0.9rem;
            font-size: 0.813rem;
            height: 2.25rem;
        }

        .registration-container .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.688rem;
            height: 1.75rem;
        }
    }

    @media (max-width: 992px) {
        .registration-container .container {
            padding: 1rem 1.5rem;
        }

        .registration-container .flex {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .registration-container .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .registration-container .table {
            min-width: 450px;
        }

        .registration-container .table th.col-name,
        .registration-container .table td.col-name {
            width: 40%;
        }

        .registration-container .table th.col-code,
        .registration-container .table td.col-code {
            width: 25%;
        }

        .registration-container .table th.col-price,
        .registration-container .table td.col-price {
            width: 15%;
        }

        .registration-container .table th.col-actions,
        .registration-container .table td.col-actions {
            width: 20%;
        }

        .registration-container .modal-dialog {
            max-width: 90%;
        }
    }

    @media (max-width: 768px) {
        .registration-container .container {
            padding: 0.75rem 1.25rem;
        }

        .registration-container h1 {
            font-size: 1.75rem;
        }

        .registration-container p {
            font-size: 0.875rem;
        }

        .registration-container .card {
            padding: 0.75rem;
        }

        .registration-container .card-header {
            padding: 0.75rem 1rem;
        }

        .registration-container .card-body {
            padding: 1rem;
        }

        .registration-container .table {
            min-width: 400px;
        }

        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.75rem;
            padding: 0.5rem;
        }

        .registration-container .form-label,
        .registration-container .form-control,
        .registration-container .input-group-text {
            font-size: 0.75rem;
        }

        .registration-container .form-control {
            height: 2.25rem;
        }

        .registration-container .input-group-text {
            padding: 0.4rem 0.6rem;
        }

        .registration-container .btn-primary,
        .registration-container .btn-secondary,
        .registration-container .btn-danger {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            height: 2rem;
        }

        .registration-container .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.625rem;
            height: 1.5rem;
        }

        .registration-container .modal-title {
            font-size: 0.875rem;
        }

        .registration-container .modal-body {
            padding: 1rem;
        }

        .registration-container .modal-footer {
            padding: 0.75rem 1rem;
        }

        .registration-container .error-message {
            font-size: 0.688rem;
        }
    }

    @media (max-width: 576px) {
        .registration-container .container {
            padding: 0.5rem 1rem;
        }

        .registration-container h1 {
            font-size: 1.5rem;
        }

        .registration-container p {
            font-size: 0.75rem;
        }

        .registration-container .card {
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .registration-container .table {
            min-width: 350px;
        }

        .registration-container .table thead th,
        .registration-container .table tbody td {
            font-size: 0.688rem;
            padding: 0.4rem;
        }

        .registration-container .form-label,
        .registration-container .form-control,
        .registration-container .input-group-text {
            font-size: 0.688rem;
        }

        .registration-container .form-control {
            height: 2rem;
        }

        .registration-container .input-group-text {
            padding: 0.3rem 0.5rem;
        }

        .registration-container .btn-primary,
        .registration-container .btn-secondary,
        .registration-container .btn-danger {
            padding: 0.3rem 0.7rem;
            font-size: 0.688rem;
            height: 1.75rem;
        }

        .registration-container .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.563rem;
            height: 1.25rem;
        }

        .registration-container .modal-dialog {
            max-width: 95%;
            margin: 0.5rem;
        }

        .registration-container .modal-content {
            border-radius: 0.5rem;
        }

        .registration-container .modal-header {
            padding: 0.5rem 1rem;
        }

        .registration-container .modal-title {
            font-size: 0.75rem;
        }

        .registration-container .modal-body {
            padding: 0.75rem;
        }

        .registration-container .modal-footer {
            padding: 0.5rem 0.75rem;
        }

        .registration-container .error-message {
            font-size: 0.625rem;
        }
    }
</style>

<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables/datatables.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize DataTable
        $('#booksTable').DataTable({
            responsive: true,
            processing: false,
            serverSide: false,
            searching: true,
            info: false,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: 3 } // Disable sorting on Actions column
            ]
        });

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

        // Clear Add Book Modal on open
        const addBookModal = document.getElementById('addBookModal');
        addBookModal.addEventListener('show.bs.modal', function () {
            const form = addBookModal.querySelector('form');
            form.reset();
            form.classList.remove('was-validated');
        });

        // Clear Edit Book Modal on open
        const editBookModal = document.getElementById('editBookModal');
        editBookModal.addEventListener('show.bs.modal', function () {
            const form = editBookModal.querySelector('form');
            form.reset();
            form.classList.remove('was-validated');
        });

        // Populate Edit Book Modal
        const editButtons = document.querySelectorAll('.edit-book-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                try {
                    const id = this.getAttribute('data-id') || '';
                    const name = this.getAttribute('data-name') || '';
                    const code = this.getAttribute('data-code') || '';
                    const price = this.getAttribute('data-price') || '';

                    const idInput = document.getElementById('editBookId');
                    const nameInput = document.getElementById('editBookName');
                    const codeInput = document.getElementById('editBookCode');
                    const priceInput = document.getElementById('editBookPrice');

                    if (!idInput || !nameInput || !codeInput || !priceInput) {
                        Toastify({
                            text: 'Error: Modal input fields not found.',
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                        return;
                    }

                    idInput.value = id;
                    nameInput.value = name;
                    codeInput.value = code;
                    priceInput.value = price;

                    const form = editBookModal.querySelector('form');
                    if (form) {
                        form.classList.remove('was-validated');
                    } else {
                        Toastify({
                            text: 'Error: Edit modal form not found.',
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                    }
                } catch (error) {
                    Toastify({
                        text: 'Error populating edit modal.',
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                }
            });
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
