@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0/css/bootstrap-select.min.css">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-2 tracking-tight sm:text-4xl">Sale Record</h1>
                    <p class="text-lg text-[var(--text-light)]">Fill in the details below to record a new sale.</p>
                </div>

            </div>

            <!-- Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Sale Record</h5>
                </div>
                <div class="card-body">
                    <form id="saleRecordForm" action="{{ url('sales/store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="row g-4">
                            <!-- Book Field -->
                            <div class="col-md-6">
                                <label for="book_id" class="form-label">Book <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-book"></i></span>
                                    <select class="form-select" id="book_id" name="book_id" required>
                                        <option value="" disabled selected>Choose Book</option>
                                        @foreach ($books as $book)
                                            <option value="{{ $book->id }}" data-quantity="{{ $book->quantity }}">{{ $book->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a book.
                                    </div>
                                    @error('book_id')
                                        <div class="error-message mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quantity Field -->
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span>
                                    <span class="quantity-info">(Available: <span id="available-quantity">0</span>)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                                    <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter quantity" required min="1">
                                    <div class="invalid-feedback">
                                        Please enter a valid quantity (minimum 1).
                                    </div>
                                    @error('quantity')
                                        <div class="error-message mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Amount Received Field -->
                            <div class="col-md-6">
                                <label for="amount_received" class="form-label">Amount Received <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                    <input type="number" step="0.01" class="form-control" id="amount_received" name="amount_received" placeholder="Enter amount received" required min="0">
                                    <div class="invalid-feedback">
                                        Please enter a valid amount.
                                    </div>
                                    @error('amount_received')
                                        <div class="error-message mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Receipt Field -->
                            <div class="col-md-6">
                                <label for="receipt" class="form-label">Receipt</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-upload"></i></span>
                                    <input type="file" class="form-control" id="receipt" name="receipt">
                                    <div class="invalid-feedback">
                                        Please upload a valid receipt file.
                                    </div>
                                    @error('receipt')
                                        <div class="error-message mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Record Sale
                            </button>
                            <a href="{{ url('sales') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
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

        .registration-container .form-control::placeholder,
        .registration-container .form-select::placeholder {
            color: var(--text-light);
            opacity: 0.8;
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

        .registration-container .error-message,
        .registration-container .invalid-feedback {
            color: var(--error);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .registration-container .valid-feedback {
            color: #10b981;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .registration-container .was-validated .form-control:invalid,
        .registration-container .was-validated .form-select:invalid {
            border-color: var(--error);
            background-image: none;
        }

        .registration-container .was-validated .form-control:valid,
        .registration-container .was-validated .form-select:valid {
            border-color: #10b981;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        .registration-container .quantity-info {
            margin-left: 0.5rem;
            color: #10b981;
            font-weight: normal;
            font-size: 0.875rem;
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
            .registration-container .input-group-text {
                font-size: 0.75rem;
            }
            .registration-container .btn-primary,
            .registration-container .btn-secondary {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
                height: 2rem;
            }
            .registration-container .quantity-info {
                font-size: 0.75rem;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('assets/libs/toastr/toastr.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Bootstrap Select
            $('#book_id').selectpicker();

            // Bootstrap Form Validation
            const form = document.getElementById('saleRecordForm');
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();

                // Trigger Bootstrap validation
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    Toastify({
                        text: 'Please fill out all required fields correctly.',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#ef4444',
                        stopOnFocus: true,
                    }).showToast();
                    return;
                }

                // Custom quantity validation
                const quantityInput = document.getElementById('quantity');
                const bookSelect = document.getElementById('book_id');
                const selectedOption = bookSelect.options[bookSelect.selectedIndex];
                const availableQuantity = parseInt(selectedOption.getAttribute('data-quantity')) || 0;
                const enteredQuantity = parseInt(quantityInput.value) || 0;

                if (enteredQuantity > availableQuantity) {
                    quantityInput.classList.add('is-invalid');
                    quantityInput.classList.remove('is-valid');
                    quantityInput.nextElementSibling.textContent = `Quantity cannot exceed available stock (${availableQuantity}).`;
                    Toastify({
                        text: `Quantity cannot exceed available stock (${availableQuantity}).`,
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#ef4444',
                        stopOnFocus: true,
                    }).showToast();
                    return;
                }

                // If all validations pass, submit the form
                form.classList.add('was-validated');
                form.submit();
            }, false);

            // Quantity and Book Selection Logic
            const bookSelect = document.getElementById('book_id');
            const quantityInput = document.getElementById('quantity');
            const availableQuantitySpan = document.getElementById('available-quantity');

            bookSelect.addEventListener('change', function () {
                const selectedOption = bookSelect.options[bookSelect.selectedIndex];
                const availableQuantity = selectedOption.getAttribute('data-quantity') || '0';
                availableQuantitySpan.textContent = availableQuantity;
                quantityInput.value = ''; // Reset quantity input
                quantityInput.classList.remove('is-valid', 'is-invalid');
                quantityInput.nextElementSibling.textContent = 'Please enter a valid quantity (minimum 1).';
            });

            quantityInput.addEventListener('input', function () {
                const enteredQuantity = parseInt(this.value) || 0;
                const availableQuantity = parseInt(availableQuantitySpan.textContent) || 0;
                if (enteredQuantity > availableQuantity) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    this.nextElementSibling.textContent = `Quantity cannot exceed available stock (${availableQuantity}).`;
                } else if (enteredQuantity <= 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    this.nextElementSibling.textContent = 'Quantity must be at least 1.';
                } else {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    this.nextElementSibling.textContent = 'Quantity is valid!';
                }
            });

            // Trigger change event to set initial quantity
            bookSelect.dispatchEvent(new Event('change'));

            // Toastify notifications for session messages and validation errors
            @if (session('success'))
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: '#10b981',
                    stopOnFocus: true,
                }).showToast();
            @endif

            @if (session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 5000,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: '#ef4444',
                    stopOnFocus: true,
                }).showToast();
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Toastify({
                        text: "{{ $error }}",
                        duration: 5000,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#ef4444',
                        stopOnFocus: true,
                    }).showToast();
                @endforeach
            @endif
        });
    </script>
@endsection
