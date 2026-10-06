@extends('layouts.branchDashboardApp')

@section('content')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link href="{{ asset('assets/libs/datetimepicker/css/classic.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/datetimepicker/css/classic.date.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Float container and child */
        .float-container {
            border: 3px solid #fff;
            padding: 20px;
        }

        .float-child {
            width: 45%;
            margin-left: 5px;
            float: left;
        }

        /* Required field indicator */
        #star {
            color: red;
        }

        /* Sticker styling for input group labels */
        .sticker {
            background-color: #1F3F97;
            color: #fff;
            font-weight: bold;
            border-radius: 0.25rem 0 0 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Card styling */
        .card {
            border: 1px solid #1F3F97;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(103, 192, 234, 0.2);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(103, 192, 234, 0.3);
        }

        .card-header {
            background-color: #1F3F97;
            color: white;
            font-weight: 600;
            border-bottom: 1px solid #1F3F97;
        }

        /* Button styling */
        .btn-primary {
            background-color: #1F3F97;
            border-color: #1F3F97;
        }

        .btn-primary:hover {
            background-color: #4ba8d2;
            border-color: #4ba8d2;
        }

        /* Alert styling */
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        /* Form control focus */
        .form-control:focus {
            border-color: #1F3F97;
            box-shadow: 0 0 0 0.2rem rgba(103, 192, 234, 0.25);
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #1F3F97;
        }

        /* Table styling */
        .table th {
            color: #1F3F97;
            border-color: #1F3F97;
        }

        .table td {
            color: #495057;
        }

        /* Payment feedback */
        #payment-feedback {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        /* CSS-Based Modal Styles */
        .css-modal-container {
            display: none;
            position: fixed;
            top: -15%;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            /* No fade effect */
            z-index: 1060;
            justify-content: center;
            align-items: center;
            pointer-events: none;
        }

        .css-modal {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            pointer-events: auto;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            /* Enhanced shadow */
            border: 1px solid #ccc;
            /* Optional border for clarity */
        }

        .css-modal-header {
            background: #1F3F97;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            /* Ensure header stays above body content */
            border-radius: 8px 8px 0 0;
            /* Maintain top rounded corners */
        }

        .css-modal-body {
            padding: 20px;
            flex: 1;
            /* Allow body to take available space */
            overflow-y: auto;
            /* Enable vertical scrolling */
            min-height: 300px;
            max-height: calc(80vh - 120px);
            /* Adjust height for header and footer */
        }

        .css-modal-footer {
            padding: 15px;
            text-align: right;
            position: sticky;
            bottom: 0;
            background: white;
            /* Match modal background */
            z-index: 10;
            /* Ensure footer stays above body content */
            border-top: 1px solid #ccc;
            /* Separator for clarity */
            border-radius: 0 0 8px 8px;
            /* Maintain bottom rounded corners */
        }

        .css-modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
        }

        #cssModalToggle:checked~.css-modal-container {
            display: flex !important;
        }

        .css-modal-container.active {
            display: flex !important;
        }

        .css-loading-books {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        /* Card container for books */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding-top: 10px;
        }

        /* jQuery-Based Modal Styles (Fallback, commented out but included for completeness) */
        #jqueryBooksModal {
            z-index: 1060;
            pointer-events: auto !important;
        }

        #jqueryBooksModal .modal-content {
            min-height: 400px;
            overflow-y: auto;
        }

        .jquery-loading-books {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        /* Centered search form */
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            max-width: 500px;
            width: 100%;
        }

        /* Payment method inputs */
        .amount-input-group {
            display: none;
            margin-top: 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }

            .search-form .btn {
                margin-top: 10px;
                width: 100%;
            }

            .card-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="text-primary">Pay Student Fee</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pay Student Fee</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Student Fee Form</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                @endif

                @if (session()->has('success-message'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        {{ session()->get('success-message') }}
                    </div>
                @endif

                <!-- Centered Search Form -->
                <div class="search-container">
                    <form method="GET" action="{{ route('payment.show') }}" class="search-form">
                        <input type="number" name="family_id" class="form-control" id="family_id" placeholder="Family ID"
                            required style="margin-right: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </form>
                </div>

                @isset($family_id)
                    <div class="card mt-4">
                        <div class="card-body">
                            <form method="POST" id="payment_form" action="{{ route('payment.store') }}">
                                @csrf
                                <div class="form-group mt-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="number" name="existing_family_id"
                                                class="form-control existing_family_id" id="existing_family_id"
                                                placeholder="Family ID" value="{{ $family_id }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="total_student" class="form-control"
                                                placeholder="Family students" value="{{ $student_names }}" readonly>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <label class="input-group-text sticker" for="Last Paid">Last Package</label>
                                                <input type="text" class="form-control" id="last_payment_package"
                                                    name="last_payment_package" placeholder="£0" value="{{ $last_package }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <label class="input-group-text sticker" for="Last Paid">Last Paid (£)</label>
                                                <input type="text" class="form-control" id="last_package" name="last_paid"
                                                    placeholder="£0" value="{{ $last_paid }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4">
                                    <div class="col-md-4">
                                        <div class="input-group">

                                            <label class="input-group-text sticker" for="Paid up to Date">Paid up to
                                                Date</label>
                                            <input type="text" class="form-control" id="paid_up_to_date"
                                                name="paid_up_to_date" placeholder="dd-mm-yyyy" value="{{ $paid_up_to_date }}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <label class="input-group-text sticker" for="Collector">Collector</label>
                                            <input type="text" name="collector" class="form-control"
                                                placeholder="Collector name" value="{{ $auth_user }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <label class="input-group-text sticker" for="Last Paid Date">Last Paid
                                                Date</label>
                                            <input type="text" name="last_paid_date" class="form-control"
                                                placeholder="dd/mm/yyyy"
                                                value="{{ $last_payment_date ? $last_payment_date : '' }}" readonly>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="Inactive Students">Inactive Students</label>
                                        <input type="text" name="" id="" class="form-control"
                                            style="color: red;" placeholder="" value="{{ $inactiveMessage }}" readonly>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button id="printBtn" class="btn btn-primary">
                                        <i class="fas fa-print me-2"></i>Print Previous Receipt
                                    </button>
                                </div>
                        </div>
                    </div>

                    {{-- @if ($latestAssignBook->isNotEmpty())
                        <div class="card mt-4" id="assigned-books-section">
                            <div class="card-header">
                                <h5 class="mb-0">Assigned Books</h5>
                            </div>
                            <div class="card-body">
                                <div style="max-height: 400px; overflow-x: auto; padding-top: 10px;">
                                    <div class="card-container" style="display: flex; flex-wrap: wrap; gap: 15px;">
                                        @foreach ($latestAssignBook->sortByDesc('created_at') as $book)
                                            <div class="card" style="min-width: 280px; max-width: 300px;">
                                                <div class="card-body">
                                                    <p class="card-text"><strong>Book:</strong> {{ $book->book }}</p>
                                                    <p class="card-text"><strong>Subject:</strong> {{ $book->subject }}</p>
                                                    <p class="card-text"><strong>Teacher:</strong> {{ $book->teacher }}</p>
                                                    <p class="card-text"><strong>Price:</strong> £{{ @$book->price }}</p>
                                                    <p class="card-text"><strong>Assigned Date:</strong>
                                                        {{ \Carbon\Carbon::parse($book->created_at)->format('d M, Y') }}</p>
                                                    <p class="card-text">
                                                        <strong>Payment Status:</strong>
                                                        @if ($book->paid_status == 1)
                                                            <span class="badge bg-success">Paid</span>
                                                        @else
                                                            <span class="badge bg-warning">Pending</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <label for="cssModalToggle" class="btn btn-primary css-open-modal">
                                        <i class="fas fa-book me-2"></i>Receive Book Payment
                                    </label>
                                    <!-- Uncomment for jQuery-based modal -->
                                    <!-- <button class="btn btn-primary jquery-open-modal">
                                            <i class="fas fa-book me-2"></i>Receive Book Payment (jQuery)
                                        </button> -->
                                </div>
                            </div>
                        </div>
                    @endif --}}

                    <!-- CSS-Based Modal -->
                    {{-- <input type="checkbox" id="cssModalToggle" style="display: none;">
                    <div class="css-modal-container" id="cssModalContainer">
                        <div class="css-modal">
                            <div class="css-modal-header">
                                <h5 class="css-modal-title">Manage Book Payments</h5>
                                <button type="button" class="css-modal-close">×</button>
                            </div>
                            <div class="css-modal-body" id="cssBooksContainer">
                                <div class="css-loading-books">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="css-modal-footer">
                                <button type="button" class="btn btn-secondary css-modal-close">Close</button>
                                <button type="button" class="btn btn-primary css-save-changes">Save Changes</button>
                            </div>
                        </div>
                    </div> --}}
                    <!-- CSS-Based Modal -->
<input type="checkbox" id="cssModalToggle" style="display: none;">
<div class="css-modal-container" id="cssModalContainer">
    <div class="css-modal">
        <div class="css-modal-header">
            <h5 class="css-modal-title">Manage Book Payments</h5>
            <button type="button" class="css-modal-close">×</button>
        </div>
        <div class="css-modal-body" id="cssBooksContainer">
            <div class="css-loading-books">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
        <div class="css-modal-footer">
            <button type="button" class="btn btn-secondary css-modal-close">Close</button>
            <button type="button" class="btn btn-primary css-save-changes">Save Changes</button>
        </div>
    </div>
</div>

                    <!-- jQuery-Based Modal (Fallback, uncomment to use) -->
                    <!--
                                <div class="modal fade" id="jqueryBooksModal" tabindex="-1" aria-labelledby="jqueryBooksModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="jqueryBooksModalLabel">Manage Book Payments</h5>
                                                <button type="button" class="btn-close jquery-modal-close" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" id="jqueryBooksContainer">
                                                <div class="jquery-loading-books">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary jquery-modal-close">Close</button>
                                                <button type="button" class="btn btn-primary jquery-save-changes">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                -->

                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="package" style="font-size: 14px; color: black; font-weight: bold;">Package
                                        (£)</label>
                                    <input type="text" name="package" id="package" class="form-control"
                                        value="{{ $package }}" placeholder="Package" readonly>
                                    <span id="lblError" class="text-danger"></span>
                                </div>

                                <div class="col-md-4">
                                    <label for="main_amount" style="font-size: 14px; color: black; font-weight: bold;">Paid
                                        (£) <span id="star" style="font-size: 12px;">(required)</span></label>
                                    <input type="number" name="paid" id="main_amount" class="form-control"
                                        value="" placeholder="Amount only" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="balance"
                                        style="font-size: 14px; color: black; font-weight: bold;">Balance</label>
                                    <input type="text" name="balance" class="form-control" value="{{ $balance }}"
                                        placeholder="£0">
                                </div>
                            </div>

                            <div class="row g-3 mt-3">
                                <div class="col-md-4">
                                    <label for="paid_from" style="font-size: 14px; color: black; font-weight: bold;">Paid from
                                        <span id="star" style="font-size: 12px;">(required)</span></label>
                                    {{-- <input type="text" id="datee" name="paid_from" class="form-control"
                                        value="" placeholder="dd/mm/yyyy" required> --}}
                                        <input
  type="text"
  id="datee"
  name="paid_from"
  class="form-control"
  value=""
  placeholder="dd/mm/yyyy"
  autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
  required
>

                                </div>

                                <div class="col-md-4">
                                    <label for="paid_to" style="font-size: 14px; color: black; font-weight: bold;">Paid to
                                        <span id="star" style="font-size: 12px;">(required)</span></label>
                                    <input
  type="text"
  id="date"
  name="paid_to"
  class="form-control"
  value=""
  placeholder="dd/mm/yyyy"
  autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
  required
>

                                </div>

                                <div class="col-md-4">
                                    <label for="payment_date"
                                        style="font-size: 14px; color: black; font-weight: bold;">Payment Date <span
                                            id="star" style="font-size: 12px;">(required)</span></label>
                                    {{-- <input type="text" id="payment_date" name="payment_date" class="form-control"
                                        value="" placeholder="dd/mm/yyyy" required> --}}
                                        <input
  type="text"
  id="payment_date"
  name="payment_date"
  class="form-control"
  value=""
  placeholder="dd/mm/yyyy"
  autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
  required
>

                                </div>
                            </div>


                            <div class="row mt-4">
                                <div class="col-12">
                                    <label for="comment"
                                        style="font-size: 14px; color: black; font-weight: bold;">Comment</label>
                                    <textarea name="comment" id="commentInput" class="form-control" cols="30" rows="5"
                                        placeholder="Enter comment here...">{{ @$comments->comments }}</textarea>
                                    <button onclick="updateComment(event)" class="btn btn-success mt-2">
                                        <i class="fas fa-save me-2"></i>Update Comment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="payment_method"
                                        style="font-size: 14px; color: black; font-weight: bold;">Payment Method <span
                                            id="star" style="font-size: 12px;">(required)</span></label>
                                    <select name="payment_method" id="payment_method" class="form-select" required>
                                        <option value="" disabled selected>Choose option</option>
                                        <option value="Cash Payment">Cash Payment</option>
                                        <option value="Card Payment">Card Payment</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Adjustment">Adjustment</option>
                                    </select>
                                </div>
                            </div>

                            <div id="cash_payment_group" class="amount-input-group mt-3">
                                <label for="cash_payment_amount"
                                    style="font-size: 14px; color: black; font-weight: bold;">Cash Amount (£)</label>
                                <input type="number" name="cash_payment_amount" class="form-control amount-input"
                                    value="" placeholder="Cash Payment Amount">
                            </div>

                            <div id="card_payment_group" class="amount-input-group mt-3">
                                <label for="card_payment_amount"
                                    style="font-size: 14px; color: black; font-weight: bold;">Card Amount (£)</label>
                                <input type="number" name="card_payment_amount" class="form-control amount-input"
                                    value="" placeholder="Card Payment Amount">
                            </div>

                            <div id="bank_transfer_group" class="amount-input-group mt-3">
                                <label for="bank_transfer_amount"
                                    style="font-size: 14px; color: black; font-weight: bold;">Bank Transfer Amount (£)</label>
                                <input type="number" name="bank_transfer_amount" class="form-control amount-input"
                                    value="" placeholder="Bank Transfer Amount">
                            </div>

                            <div id="adjustment_group" class="amount-input-group mt-3">
                                <label for="adjustment_amount"
                                    style="font-size: 14px; color: black; font-weight: bold;">Adjustment Amount (£)</label>
                                <input type="number" name="adjustment_amount" class="form-control amount-input"
                                    value="" placeholder="Adjustment Amount">
                            </div>

                            <div id="appended-values" class="mt-3"></div>
                            <div id="total-amount" class="mt-3 fw-bold">Total: £0</div>
                            <div id="payment-feedback" class="mt-3 fw-bold"></div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="signal_work" checked>
                                    <label class="form-check-label" for="signal_work">Print Receipt</label>
                                    <input type="hidden" name="signal" id="signal" value="1">
                                </div>

                                <button type="submit" class="btn btn-primary" id="submitPayment">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Payment
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (count(@$previousPayments) > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Payment History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Payment From</th>
                                                <th>Payment To</th>
                                                <th>Payment Date</th>
                                                <th>Balance</th>
                                                <th>Paid(£)</th>
                                                <th>Collector</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($previousPayments as $payment)
                                                <tr>
                                                    {{-- {{dd($payment->paymentfrom,$payment->paymentto )}} --}}
                                                    <td>{{ explode(' ', $payment->paymentfrom)[0] }}</td>
                                                    <td>{{ explode(' ', $payment->paymentto)[0] }}</td>
                                                    <td>{{ $payment->paymentdate }}</td>
                                                    <td>{{ $payment->package }}</td>
                                                    <td>{{ $payment->paid }}</td>
                                                    <td>{{ $payment->collector }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    </form>
                @endisset
            </div>
        </div>

        <div id="#pdf"></div>

        <script src="{{ asset('assets/libs/datetimepicker/js/picker.js') }}"></script>
        <script src="{{ asset('assets/libs/datetimepicker/js/picker.date.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Initialize date pickers
                flatpickr("#datee", {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    locale: {
                        firstDayOfWeek: 1 // Set Monday as the first day of the week
                    }
                });

                flatpickr("#date", {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    locale: {
                        firstDayOfWeek: 1 // Set Monday as the first day of the week
                    }
                });

                flatpickr("#payment_date", {
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    defaultDate: "today",
                    locale: {
                        firstDayOfWeek: 1 // Set Monday as the first day of the week
                    }
                });

                // Debug: Log to check if script runs
                console.log('Script loaded');

                // CSS-Based Modal Logic
                let isLoadingBooks = false;
                $('.css-open-modal').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Open modal clicked');
                    if (isLoadingBooks) return;
                    isLoadingBooks = true;

                    $('#cssModalToggle').prop('checked', true);
                    $('#cssModalContainer').addClass('active');
                    const booksContainer = $('#cssBooksContainer');
                    booksContainer.html(`
                        <div class="css-loading-books">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);

                    const familyId = $('#existing_family_id').val();
                    $.ajax({
                        url: "{{ url('payment-books') }}",
                        type: 'POST',
                        data: {
                            family_id: familyId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('AJAX success:', response);
                            isLoadingBooks = false;
                            if (response.data && response.data.length > 0) {
                                response.data.sort((a, b) => new Date(b.created_at) - new Date(a
                                    .created_at));
                                renderBooks(response.data, 'cssBooksContainer',
                                    'css-payment-checkbox');
                            } else {
                                booksContainer.html(`
                                    <div class="alert alert-info text-center">
                                        No books found for this family ID.
                                    </div>
                                `);
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX error:', xhr);
                            isLoadingBooks = false;
                            booksContainer.html(`
                                <div class="alert alert-danger text-center">
                                    Failed to load books data: ${xhr.statusText}
                                </div>
                            `);
                        }
                    });
                });

                // Close CSS modal
                $('.css-modal-close').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Close modal clicked');
                    $('#cssModalToggle').prop('checked', false);
                    $('#cssModalContainer').removeClass('active');
                    $('.main-content').removeAttr('style'); // Reset main content styles
                    $('#assigned-books-section').removeAttr('style'); // Reset assigned books styles
                    refreshBookList();
                });

                // Save changes for CSS modal
                $('.css-save-changes').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Save changes clicked');
                    $('#cssModalToggle').prop('checked', false);
                    $('#cssModalContainer').removeClass('active');
                    $('.main-content').removeAttr('style'); // Reset main content styles
                    $('#assigned-books-section').removeAttr('style'); // Reset assigned books styles
                    refreshBookList();
                });

                // Prevent modal and close button blinking
                $('.css-modal-container, .css-modal-close').on('mouseover mouseout mousemove', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                });

                // Render books
               function renderBooks(books, containerId, checkboxClass) {
    const booksContainer = $(`#${containerId}`);
    let booksHtml = '<div class="row row-cols-1 row-cols-md-2 g-4">';
    books.forEach(book => {
        const isPaid = book.paid_status == 1;
        const showCheckbox = isPaid ? 'd-none' : 'd-block';
        booksHtml += `
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">${book.book}</h5>
                        <p class="card-text mb-1"><strong>Subject:</strong> ${book.subject}</p>
                        <p class="card-text mb-1"><strong>Teacher:</strong> ${book.teacher || 'N/A'}</p>
                        <p class="card-text mb-1"><strong>Price:</strong> £2.00</p>
                        <p class="card-text mb-3"><strong>Assigned:</strong> ${formatDate(book.created_at)}</p>
                        <div class="mb-3">
                            <label for="book_payment_method_${book.id}" class="form-label"><strong>Payment Method</strong></label>
                            <select class="form-select book-payment-method" id="book_payment_method_${book.id}"
                                    data-book-id="${book.id}" ${isPaid ? 'disabled' : ''}>
                                <option value="" disabled selected>Select Payment Method</option>
                                <option value="Card">Card</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge ${isPaid ? 'bg-success' : 'bg-warning'}">
                                ${isPaid ? 'Paid' : 'Pending'}
                            </span>
                            <div class="form-check ${showCheckbox}">
                                <input class="form-check-input ${checkboxClass}"
                                       type="checkbox"
                                       id="${checkboxClass}${book.id}"
                                       data-book-id="${book.id}"
                                       ${isPaid ? 'checked disabled' : ''}>
                                <label class="form-check-label" for="${checkboxClass}${book.id}">
                                    Mark Paid
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    booksHtml += '</div>';
    booksContainer.html(booksHtml);
}

                // Format date
                function formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }

                // Handle checkbox changes
              $(document).on('change', '.css-payment-checkbox', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Checkbox changed:', $(this).data('book-id'));
    const bookId = $(this).data('book-id');
    const isChecked = $(this).is(':checked');
    const paymentMethod = $(`#book_payment_method_${bookId}`).val();

    if (isChecked && !paymentMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Payment Method',
            text: 'Please select a payment method before marking as paid.'
        });
        $(this).prop('checked', false);
        return;
    }

    Swal.fire({
        title: 'Updating...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: "{{ url('update-book-paid') }}",
        type: 'POST',
        data: {
            book_id: bookId,
            paid: isChecked ? 1 : 0,
            payment_method: paymentMethod,
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            console.log('Checkbox update success');
            Swal.fire({
                title: 'Success',
                text: 'Payment status updated',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            $(e.target).prop('disabled', true);
            $(`#book_payment_method_${bookId}`).prop('disabled', true);
        },
        error: function(xhr) {
            console.error('Checkbox update error:', xhr);
            Swal.fire({
                title: 'Error',
                text: 'Update failed: ' + xhr.statusText,
                icon: 'error'
            });
            $(e.target).prop('checked', !isChecked);
        }
    });
});

                // Refresh book list
                function refreshBookList() {
                    console.log('Refreshing book list');
                    const familyId = $('#existing_family_id').val();
                    const cardContainer = $('.card-container');
                    cardContainer.html(
                        '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                    );
                    $.ajax({
                        url: "{{ url('payment-books') }}",
                        type: 'POST',
                        data: {
                            family_id: familyId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Refresh book list success:', response);
                            if (response.data?.length) {
                                response.data.sort((a, b) => new Date(b.created_at) - new Date(a
                                    .created_at));
                                let booksHtml =
                                    '<div class="card-container" style="display: flex; flex-wrap: wrap; gap: 15px;">';
                                response.data.forEach(book => {
                                    booksHtml += `
                                        <div class="card" style="min-width: 280px; max-width: 300px;">
                                            <div class="card-body">
                                                <p class="card-text"><strong>Book:</strong> ${book.book}</p>
                                                <p class="card-text"><strong>Subject:</strong> ${book.subject}</p>
                                                <p class="card-text"><strong>Teacher:</strong> ${book.teacher || 'N/A'}</p>
                                                <p class="card-text"><strong>Price:</strong> £2.00</p>
                                                <p class="card-text"><strong>Assigned Date:</strong> ${formatDate(book.created_at)}</p>
                                                <p class="card-text">
                                                    <strong>Payment Status:</strong>
                                                    <span class="badge ${book.paid_status == 1 ? 'bg-success' : 'bg-warning'}">
                                                        ${book.paid_status == 1 ? 'Paid' : 'Pending'}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    `;
                                });
                                booksHtml += '</div>';
                                cardContainer.html(booksHtml);
                            } else {
                                cardContainer.html(
                                    '<div class="alert alert-info">No books available.</div>');
                            }
                            $('#assigned-books-section').removeAttr('style'); // Ensure no residual styles
                        },
                        error: function(xhr) {
                            console.error('Refresh book list error:', xhr);
                            cardContainer.html(
                                '<div class="alert alert-danger">Failed to refresh book list: ${xhr.statusText}</div>'
                            );
                        }
                    });
                }

                // Payment method selection handler
                $('#payment_method').off('change').on('change', function() {
                    // Hide all amount input groups
                    $('.amount-input-group').hide();
                    // Show only the selected payment method's input group
                    const method = $(this).val();
                    if (method === 'Cash Payment') $('#cash_payment_group').show();
                    else if (method === 'Card Payment') $('#card_payment_group').show();
                    else if (method === 'Bank Transfer') $('#bank_transfer_group').show();
                    else if (method === 'Adjustment') $('#adjustment_group').show();
                    // Validate amounts to update feedback
                    validateAmounts();
                });

                // Payment amount validation
                const mainAmountInput = document.getElementById("main_amount");
                const amountInputs = document.querySelectorAll(".amount-input");
                const feedbackDiv = document.getElementById("payment-feedback");
                const totalAmountDiv = document.getElementById("total-amount");
                const appendedValuesDiv = document.getElementById("appended-values");
                const submitButton = document.getElementById("submitPayment");
                let userChangedInput = false;

                function validateAmounts() {
                    if (!userChangedInput) return;
                    let total = 0;
                    let enteredMethods = [];
                    amountInputs.forEach(input => {
                        const amount = parseFloat(input.value) || 0;
                        if (amount > 0) {
                            total += amount;
                            enteredMethods.push({
                                name: input.name,
                                amount: amount
                            });
                        }
                    });
                    let mainAmount = parseFloat(mainAmountInput.value) || 0;
                    if (total > mainAmount) {
                        feedbackDiv.style.color = "red";
                        feedbackDiv.innerHTML = "⚠️ Total payment exceeds the entered amount!";
                        submitButton.disabled = true;
                    } else if (total < mainAmount) {
                        feedbackDiv.style.color = "orange";
                        feedbackDiv.innerHTML = "⚠️ Payment is less than the required amount.";
                        submitButton.disabled = true;
                    } else if (total === mainAmount && total > 0) {
                        feedbackDiv.style.color = "green";
                        feedbackDiv.innerHTML = "✅ Payment amounts match!";
                        submitButton.disabled = false;
                    } else {
                        feedbackDiv.innerHTML = "";
                        submitButton.disabled = true;
                    }
                    totalAmountDiv.innerHTML = "Total: £" + total.toFixed(2);
                    // Update appended values
                    appendedValuesDiv.innerHTML = '';
                    enteredMethods.forEach(method => {
                        const methodName = method.name.replace('_amount', '').replace('_', ' ').toUpperCase();
                        const newDiv = document.createElement('div');
                        newDiv.id = 'payment-' + method.name;
                        newDiv.innerHTML = `${methodName}: £${method.amount.toFixed(2)}`;
                        appendedValuesDiv.appendChild(newDiv);
                    });
                }

                function restrictInput(event) {
                    userChangedInput = true;
                    let mainAmount = parseFloat(mainAmountInput.value) || 0;
                    let currentTotal = 0;
                    amountInputs.forEach(input => {
                        if (input !== event.target) {
                            currentTotal += parseFloat(input.value) || 0;
                        }
                    });
                    let maxAllowed = mainAmount - currentTotal;
                    let inputValue = parseFloat(event.target.value) || 0;
                    if (inputValue > maxAllowed) {
                        event.target.value = maxAllowed > 0 ? maxAllowed : 0;
                        inputValue = maxAllowed > 0 ? maxAllowed : 0;
                    }
                    const paymentMethod = event.target.name.replace('_amount', '').replace('_', ' ');
                    const existingDiv = document.getElementById('payment-' + event.target.name);
                    if (existingDiv) {
                        existingDiv.innerHTML = paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1) + ': £' +
                            inputValue.toFixed(2);
                    } else if (inputValue > 0) {
                        const newDiv = document.createElement('div');
                        newDiv.id = 'payment-' + event.target.name;
                        newDiv.innerHTML = paymentMethod.charAt(0).toUpperCase() + paymentMethod.slice(1) + ': £' +
                            inputValue.toFixed(2);
                        appendedValuesDiv.appendChild(newDiv);
                    }
                    validateAmounts();
                }

                amountInputs.forEach(input => {
                    input.addEventListener("input", restrictInput);
                });

                mainAmountInput.addEventListener("input", function() {
                    userChangedInput = false;
                    feedbackDiv.innerHTML = "";
                    submitButton.disabled = true;
                    validateAmounts();
                });

                // Print button handler
                $('#printBtn').off('click').on('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('Print button clicked');
                    const family_id = $('.existing_family_id').val();
                    Swal.fire({
                        title: 'Generating PDF...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    $.ajax({
                        url: '{{ url('pdfGenerate') }}/' + family_id,
                        method: 'GET',
                        success: function(data) {
                            console.log('PDF generation success:', data);
                            Swal.close();
                            if (data.pdfDataUri) {
                                const newWindow = window.open();
                                newWindow.document.write('<iframe src="' + data.pdfDataUri +
                                    '" style="width:100%; height:100%;"></iframe>');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No PDF data received'
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('PDF generation error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to generate PDF: ' + xhr.statusText
                            });
                        }
                    });
                });

                // Comment update handler
                window.updateComment = function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('Update comment clicked');
                    const newComment = $('#commentInput').val();
                    const existingFamilyId = $('#existing_family_id').val();
                    if (!newComment.trim()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Empty Comment',
                            text: 'Please enter a comment before updating'
                        });
                        return;
                    }
                    Swal.fire({
                        title: 'Updating comment...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    $.ajax({
                        url: "{{ url('/update/payment/comment') }}",
                        type: "GET",
                        data: {
                            comment: newComment,
                            id: existingFamilyId
                        },
                        success: function(response) {
                            console.log('Comment update success:', response);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message || 'Comment updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(error) {
                            console.error('Comment update error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update comment: ' + (error.responseJSON
                                    ?.message || 'Server error')
                            });
                        }
                    });
                };

                // Form submission handler
                $('#payment_form').off('submit').on('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Form submitted');
                    const paymentMethod = $('#payment_method').val();
                    if (!paymentMethod || paymentMethod === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Information',
                            text: 'Please select a payment method'
                        });
                        return false;
                    }
                    let amountEntered = false;
                    $('.amount-input').each(function() {
                        if ($(this).val() && parseFloat($(this).val()) > 0) {
                            amountEntered = true;
                            return false;
                        }
                    });
                    if (!amountEntered) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Information',
                            text: 'Please enter at least one payment amount'
                        });
                        return false;
                    }
                    const paidFrom = $('[name="paid_from"]').val();
                    const paidTo = $('[name="paid_to"]').val();
                    const paymentDate = $('[name="payment_date"]').val();
                    if (!paidFrom || !paidTo || !paymentDate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Information',
                            text: 'Please fill all date fields'
                        });
                        return false;
                    }
                    $('#submitPayment').prop('disabled', true);
                    Swal.fire({
                        title: 'Processing Payment...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    this.submit();
                });

                // Print receipt checkbox handler
                $('#signal_work').off('change').on('change', function() {
                    console.log('Print receipt checkbox changed:', $(this).is(':checked'));
                    $('#signal').val($(this).is(':checked') ? '1' : '0');
                });
            });
        </script>
    </div>
@endsection
