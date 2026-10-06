```html
@extends('layouts.branchDashboardApp')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@section('content')
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Payments Logs</h1>
            <p>View and manage payment records in the system.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="toast-error mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('payment.log.show') }}" id="paymentLogForm">
        <!-- Filters -->
        <div class="ui-bordered px-4 pt-4 mb-4 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 col-lg-5 col-xs-12 mb-3">
                        <select name="users[]" class="form-control select2" multiple required>
                            <option value="all">All</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 col-lg-5 col-xs-12 mb-3">
                        {{-- <label for="date" class="form-label" style="font-size: 14px; color: #2045A5; font-weight: bold;">
                            Date <span style="color: #dc3545;">(required)</span>
                        </label> --}}
                        <input type="text" id="date" name="date" class="form-control"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"
                            value="{{ request()->query('date') ? \Carbon\Carbon::createFromFormat('Y-m-d', request()->query('date'))->format('d/m/Y') : \Carbon\Carbon::today()->format('d/m/Y') }}"
                            placeholder="DD/MM/YYYY" required>
                        @error('date')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 col-lg-2 col-xs-12 mb-3">
                        <input type="submit" class="btn btn-primary w-100" value="View">
                    </div>
                </div>
            </div>
        </div>
    </form>

    @isset($allPayments)
        @if (count($allPayments) > 0)
            <h6 class="text-center mb-4"><b>View Previous Record</b></h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Family Id</th>
                            <th>Total Payment</th>
                            <th>Paid</th>
                            <th>Bank/Online Payment</th>
                            <th>Cash Payment</th>
                            <th>Card Payment</th>
                            <th>Adjustment</th>
                            <th>Collector</th>
                            <th>Payment Date</th>
                            <th>Is deleted</th>
                            <th>Deleted By</th>
                            <th>Created at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $isSuperAdmin = in_array(auth()->user()->email, [
                                'superadmin@frobel.com',
                                'developer@frobel.co.uk',
                            ]);
                            $cutoffDate = \Carbon\Carbon::now()->subMonths(4);
                        @endphp
                        @foreach ($allPayments as $payment)
                            @php
                                $paymentDate = \Carbon\Carbon::parse($payment->paymentdate);
                                $isOld = $paymentDate->lt($cutoffDate);
                            @endphp
                            @if (!$isOld || $isSuperAdmin)
                            <tr @if ($payment->is_deleted !== null) class="deleted-row" @endif>
                                <td>{{ $payment->paymentfamilyid ? $payment->paymentfamilyid : '' }}</td>
                                <td>{{ $payment->package }}</td>
                                <td>{{ $payment->paid }}</td>
                                <td>{{ $payment->bank_transfer }}</td>
                                <td>{{ $payment->cash_payment }}</td>
                                <td>{{ $payment->card_payment }}</td>
                                <td>{{ $payment->adjustment }}</td>
                                <td>{{ $payment->collector }}</td>
                                <td>
                                    @php
                                        $formattedDate = !empty($payment->paymentdate)
                                            ? \Carbon\Carbon::parse($payment->paymentdate)->format('d M Y')
                                            : '';
                                        echo $formattedDate;
                                    @endphp
                                </td>
                                <td>{{ $payment->is_deleted }}</td>
                                <td>{{ $payment->deleted_by }}</td>
                                <td>{{ $payment->created_at }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <h5 class="text-center text-danger mt-5">No record found corresponding to this family id.</h5>
        @endif
    @endisset
</div>

<style>
    .main-content {
        background: transparent;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.6s ease-in-out;
        border: 1px solid #2045A5;
    }

    .main-content h1 {
        font-size: 32px;
        font-weight: 700;
        color: #2045A5;
        margin-bottom: 15px;
    }

    .main-content p {
        font-size: 18px;
        color: #2045A5;
        margin-bottom: 20px;
    }

    .ui-bordered {
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid #2045A5;
    }

    .form-label {
        font-size: 14px;
        color: #2045A5;
        font-weight: bold;
    }

    .form-control {
        border-color: #2045A5;
        color: #2045A5;
        background: transparent;
    }

    .form-control:focus {
        border-color: #4ba8d2;
        box-shadow: 0 0 8px rgba(103, 192, 234, 0.5);
    }

    .error-message {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table.table-striped.table-hover {
        --bs-table-bg: transparent;
        --bs-table-color: #2045A5;
        --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
        --bs-table-striped-color: #2045A5;
        --bs-table-hover-bg: rgba(103, 192, 234, 0.2);
        --bs-table-hover-color: #ffffff;
        color: var(--bs-table-color);
        background: var(--bs-table-bg);
        border-color: #2045A5;
        border-radius: 8px;
        margin-bottom: 0;
        width: 100%;
    }

    .table.table-striped.table-hover thead th {
        background: transparent;
        color: #2045A5;
        border-bottom: 2px solid #2045A5;
        font-weight: 600;
        padding: 12px;
        text-align: left;
    }

    .table.table-striped.table-hover tbody {
        background: transparent;
    }

    .table.table-striped.table-hover tbody tr {
        transition: all 0.3s ease;
    }

    .table.table-striped.table-hover tbody td {
        vertical-align: middle;
        border-color: #2045A5;
        padding: 12px;
        color: #2045A5;
        font-size: 14px;
        font-weight: 500;
    }

    .table.table-striped.table-hover tbody tr:hover {
        background: #2045A5 !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table.table-striped.table-hover tbody tr:hover td {
        color: #ffffff !important;
    }

    .table.table-striped.table-hover tbody tr.deleted-row {
        background-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .table.table-striped.table-hover tbody tr.deleted-row td {
        color: #ffffff !important;
    }

    .table.table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-color-type: #2045A5 !important;
        --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
        color: var(--bs-table-color-type) !important;
        background-color: var(--bs-table-bg-type) !important;
    }

    .btn-primary {
        background: #2045A5;
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #4ba8d2;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        scale: 1.05;
    }

    .btn-primary:active {
        transform: translateY(0);
        scale: 0.98;
    }

    .toast-error {
        background-color: #dc3545 !important;
        color: #ffffff !important;
        border: 2px solid #a71d2a;
        border-radius: 8px;
        font-weight: 500;
        padding: 15px;
    }

    /* Select2 Styling */
    .select2-container .select2-selection--multiple {
        border-color: #2045A5;
        background: transparent;
        color: #2045A5;
        border-radius: 8px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2045A5;
        color: #ffffff;
        border: 1px solid #4ba8d2;
        border-radius: 4px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff0000;
    }

    .select2-container .select2-selection--multiple:focus {
        border-color: #4ba8d2;
        box-shadow: 0 0 8px rgba(103, 192, 234, 0.5);
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2045A5;
        color: #ffffff;
    }

    .select2-container .select2-selection--multiple .select2-selection__rendered {
        color: #2045A5;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 15px;
        }

        .main-content h1 {
            font-size: 24px;
        }

        .main-content p {
            font-size: 16px;
        }

        .table.table-striped.table-hover thead th,
        .table.table-striped.table-hover tbody td {
            font-size: 13px;
            padding: 8px;
        }

        .btn-primary {
            padding: 8px 16px;
            font-size: 14px;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select User(s)",
        allowClear: true,
        width: '100%',
        closeOnSelect: false
    });

    // Initialize Flatpickr
    flatpickr("#date", {
        dateFormat: "d/m/Y",
        allowInput: true,
        locale: {
            firstDayOfWeek: 1 // Set Monday as the first day of the week
        }
    });

    // Convert date format to YYYY-MM-DD before form submission
    $('#paymentLogForm').on('submit', function(e) {
        const dateInput = $('#date').val();
        if (dateInput) {
            const [day, month, year] = dateInput.split('/');
            const formattedDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            $('#date').val(formattedDate);
        }
    });

    // Initialize DataTable
    $('#example').DataTable({
        order: [[7, "asc"]]
    });

    // Show session error messages
    @if (session('error'))
        Toastify({
            text: "{{ session('error') }}",
            duration: 5000,
            gravity: "top",
            position: "right",
            backgroundColor: "#dc3545",
            stopOnFocus: true,
        }).showToast();
    @endif
});
</script>
@endsection
```
