@extends('layouts.branchDashboardApp')

@section('content')
    <!-- Include External CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-style.css') }}">

    <div class="registration-container scroll-smooth">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Export Payments</h1>

            </div>

            <!-- Error and Success Messages -->
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            toastr.error("{{ $error }}", "Error", {
                                closeButton: true,
                                progressBar: true,
                                positionClass: "toast-top-right",
                                timeOut: 5000,
                                toastClass: "toast-error"
                            });
                        });
                    @endforeach
                </script>
            @endif
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toastr.success("{{ session('success') }}", "Success", {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right",
                            timeOut: 5000,
                            toastClass: "toast-success"
                        });
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        toastr.error("{{ session('error') }}", "Error", {
                            closeButton: true,
                            progressBar: true,
                            positionClass: "toast-top-right",
                            timeOut: 5000,
                            toastClass: "toast-error"
                        });
                    });
                </script>
            @endif

            <!-- Search Form -->
            <div class="card form-section">
                <div class="flex items-center mb-4">
                    <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                        <i class="fas fa-filter h-5 w-5 text-white text-lg"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Filter Payments</h2>
                </div>
                <form method="GET" action="{{ route('payment.export.show') }}" id="filterForm" class="space-y-6">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">From <span class="text-[var(--error)]">*</span></label>
                            <div class="relative">
                                <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  id="from_date" name="from_date" class="input-field pl-10 flatpickr" value="{{ request()->from_date ?: date('Y-m-d') }}" placeholder="yyyy-mm-dd" required>
                            </div>
                            <div class="error-message" id="from_date_feedback">Please select a valid date.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">To <span class="text-[var(--error)]">*</span></label>
                            <div class="relative">
                                <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <input type="text"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  id="to_date" name="to_date" class="input-field pl-10 flatpickr" value="{{ request()->to_date ?: date('Y-m-d') }}" placeholder="yyyy-mm-dd" required>
                            </div>
                            <div class="error-message" id="to_date_feedback">Please select a valid date.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Payment Method <span class="text-[var(--error)]">*</span></label>
                            <div class="relative">
                                <i class="fas fa-credit-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                <select name="payment_method" class="input-field pl-10 select2" required>
                                    <option value="" disabled selected>Choose</option>
                                    <option {{ request()->payment_method == 'Cash Payment' ? 'selected' : '' }} value="Cash Payment">Cash Payment</option>
                                    <option {{ request()->payment_method == 'Card Payment' ? 'selected' : '' }} value="Card Payment">Card Payment</option>
                                    <option {{ request()->payment_method == 'Bank Transfer' ? 'selected' : '' }} value="Bank Transfer">Bank Transfer</option>
                                    <option {{ request()->payment_method == 'Adjustment' ? 'selected' : '' }} value="Adjustment">Adjustment</option>
                                    <option {{ request()->payment_method == 'all' ? 'selected' : '' }} value="all">All</option>
                                </select>
                            </div>
                            <div class="error-message" id="payment_method_feedback">Please select a payment method.</div>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn-primary w-100 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i> View Records
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Payments Table -->
            @isset($payments)
                @if (count($payments) > 0)
                    <div class="card form-section mt-8">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                                    <i class="fas fa-table h-5 w-5 text-white text-lg"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Payment Records</h2>
                            </div>
                            @if(auth()->user()->email === 'superadmin@frobel.com')
                                <button id="exportCsvBtn" class="btn-primary flex items-center">
                                    <i class="fas fa-file-export mr-2"></i> Export to CSV
                                </button>
                            @endif
                        </div>
                        <h6 class="text-center mb-4 text-[var(--text-dark)]"><b>Student Fees Payments - Total (£) <span id="totalPaid"></span></b></h6>
                        <div class="table-responsive">
                            <table id="paymentTable" class="table w-full">
                                <thead>
                                    <tr>
                                        <th>Family ID</th>
                                        <th>Start Date</th>
                                        <th>Cash Payment</th>
                                        <th>Card Payment</th>
                                        <th>Bank Transfer</th>
                                        <th>Adjustment</th>
                                        <th>Total Payment</th>
                                        <th>Payment Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $isSuperAdmin = auth()->user()->name === 'developer';
                                        $cutoffDate = \Carbon\Carbon::now()->subMonths(4);
                                    @endphp
                                    @foreach ($payments as $payment)
                                        @php
                                            $paymentDate = \Carbon\Carbon::parse($payment->paymentdate);
                                            $isOld = $paymentDate->lt($cutoffDate);
                                        @endphp
                                        @if (!$isOld || $isSuperAdmin)
                                        <tr>
                                            <td>{{ $payment->paymentfamilyid }}</td>
                                            <td>{{ $payment->paymentdate ? date('d F Y', strtotime($payment->paymentdate)) : 'N/A' }}</td>
                                            <td>{{ $payment->cash_payment ?? '0' }}</td>
                                            <td>{{ $payment->card_payment ?? '0' }}</td>
                                            <td>{{ $payment->bank_transfer ?? '0' }}</td>
                                            <td>{{ $payment->adjustment ?? '0' }}</td>
                                            <td>{{ $payment->paid }}</td>
                                            @php
                                                $methods = [];
                                                if (!empty($payment->cash_payment) && $payment->cash_payment > 0) $methods[] = 'Cash Payment';
                                                if (!empty($payment->card_payment) && $payment->card_payment > 0) $methods[] = 'Card Payment';
                                                if (!empty($payment->bank_transfer) && $payment->bank_transfer > 0) $methods[] = 'Bank Transfer';
                                                if (!empty($payment->adjustment) && $payment->adjustment > 0) $methods[] = 'Adjustment';
                                                $methodStr = count($methods) > 0 ? implode(' | ', $methods) : 'N/A';
                                            @endphp
                                            @if(auth()->user()->name === 'developer')
                                                @php
                                                    // Determine selected value: prefer DB payment_method if valid, else derive from amount columns
                                                    $validMethods = ['Cash Payment', 'Card Payment', 'Bank Transfer', 'Adjustment'];
                                                    $dbMethod = trim($payment->payment_method ?? '');
                                                    if (in_array($dbMethod, $validMethods)) {
                                                        $selectedMethod = $dbMethod;
                                                    } elseif (count($methods) === 1) {
                                                        $selectedMethod = $methods[0];
                                                    } else {
                                                        $selectedMethod = ''; // multiple or none — no pre-select
                                                    }
                                                @endphp
                                                <td data-export="{{ $methodStr }}">
                                                    <select class="payment-method-dropdown"
                                                        style="min-width:160px; border:1px solid #bfdbfe; border-radius:0.4rem; padding:0.3rem 0.5rem; font-size:0.85rem; color:#111827; background:#fff; cursor:pointer;"
                                                        data-payment-id="{{ $payment->paymentid }}"
                                                        data-paid="{{ $payment->paid }}">
                                                        <option value="" disabled {{ $selectedMethod === '' ? 'selected' : '' }}>-- Select --</option>
                                                        <option value="Cash Payment"  {{ $selectedMethod === 'Cash Payment'  ? 'selected' : '' }}>Cash Payment</option>
                                                        <option value="Card Payment"  {{ $selectedMethod === 'Card Payment'  ? 'selected' : '' }}>Card Payment</option>
                                                        <option value="Bank Transfer" {{ $selectedMethod === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                        <option value="Adjustment"    {{ $selectedMethod === 'Adjustment'    ? 'selected' : '' }}>Adjustment</option>
                                                    </select>
                                                </td>
                                            @else
                                                <td data-export="{{ $methodStr }}">{{ $methodStr }}</td>
                                            @endif
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center text-[var(--error)] mt-8">
                        <h5>No payment records found.</h5>
                    </div>
                @endif
            @endisset
        </div>
    </div>

    <!-- Include Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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

        .registration-container .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(29, 78, 216, 0.15);
            border-color: var(--primary-light);
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

        .registration-container .card:hover::before {
            height: 5px;
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
            color: var(--text-dark);
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

        .registration-container .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-dark);
            --bs-table-striped-bg: var(--secondary);
            --bs-table-striped-color: var(--text-dark);
            --bs-table-hover-bg: rgba(37, 99, 235, 0.1);
            --bs-table-hover-color: var(--text-dark);
            color: var(--bs-table-color);
            border-color: var(--border);
            border-radius: 0.5rem;
            margin-bottom: 0;
        }

        .registration-container .table thead th {
            background: var(--secondary);
            color: var(--text-dark);
            border-bottom: 2px solid var(--border);
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
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
            background: var(--bs-table-hover-bg);
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .registration-container .section-icon {
            transition: transform 0.3s ease;
        }

        .registration-container .section-icon:hover {
            transform: scale(1.2);
        }

        .registration-container .scroll-smooth {
            scroll-behavior: smooth;
        }

        /* Select2-specific styles */
        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid var(--border) !important;
            border-radius: 0.5rem !important;
            background: #ffffff !important;
            height: 2.5rem !important;
            line-height: 2.5rem !important;
            padding-left: 2.5rem !important;
            font-size: 0.875rem !important;
            color: var(--text-dark) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: var(--text-dark) !important;
            line-height: 2.5rem !important;
            padding: 0 0.8rem !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 2.5rem !important;
            color: var(--text-light) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            color: var(--text-dark) !important;
            background: #ffffff !important;
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background: var(--secondary) !important;
            color: var(--text-dark) !important;
        }

        /* Flatpickr-specific styles */
        .flatpickr-input {
            padding-left: 2.5rem !important;
            border: 1px solid var(--border) !important;
            border-radius: 0.5rem !important;
            background: #ffffff !important;
            height: 2.5rem !important;
            font-size: 0.875rem !important;
            color: var(--text-dark) !important;
        }

        .flatpickr-input:focus,
        .flatpickr-input:hover {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }

        /* Toastr custom styles */
        .toast-error {
            background-color: var(--error) !important;
            color: #ffffff !important;
            border: 2px solid #b91c1c !important;
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
        }

        .toast-success {
            background-color: #10b981 !important;
            color: #ffffff !important;
            border: 2px solid #059669 !important;
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
        }
    </style>

    <script>
        $(document).ready(function () {
            // Initialize Toastr options
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right",
                timeOut: 5000,
                extendedTimeOut: 1000,
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };

            // Initialize Flatpickr
            flatpickr(".flatpickr", {
                dateFormat: "Y-m-d",
                locale: { firstDayOfWeek: 1 }
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownCssClass: 'select2-dropdown'
            });

            // Initialize DataTable
            var table = $('#paymentTable').DataTable({
                order: [[1, 'desc']],
                pageLength: -1,
                lengthChange: false,
                language: {
                    emptyTable: "No payment records available"
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-sm btn-primary',
                        title: 'Payment Records'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-sm btn-primary',
                        title: 'Payment Records',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    var exportVal = $(node).attr('data-export');
                                    return exportVal !== undefined ? exportVal : data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-sm btn-primary',
                        title: 'Payment Records',
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    var exportVal = $(node).attr('data-export');
                                    return exportVal !== undefined ? exportVal : data;
                                }
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-sm btn-primary',
                        title: 'Payment Records'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-sm btn-primary',
                        title: 'Payment Records'
                    }
                ]
            });

            // Calculate and display total paid
            function updateTotalPaid() {
                var paymentMethod = "{{ request()->payment_method }}";
                var colIndex;
                // Column indexes: 0=FamilyID, 1=Date, 2=Cash, 3=Card, 4=BankTransfer, 5=Adjustment, 6=TotalPayment, 7=PaymentMethod
                if (paymentMethod === 'Cash Payment') {
                    colIndex = 2;
                } else if (paymentMethod === 'Card Payment') {
                    colIndex = 3;
                } else if (paymentMethod === 'Bank Transfer') {
                    colIndex = 4;
                } else if (paymentMethod === 'Adjustment') {
                    colIndex = 5;
                } else {
                    colIndex = 6; // 'all' => paid column
                }

                var totalPaid = table.rows({ search: 'applied' }).data()
                    .pluck(colIndex)
                    .reduce(function (a, b) {
                        var parsedValue = parseFloat(b);
                        return a + (isNaN(parsedValue) ? 0 : parsedValue);
                    }, 0);
                $('#totalPaid').text(totalPaid.toFixed(2));
            }

            // Initial update and on draw
            updateTotalPaid();
            table.on('draw', updateTotalPaid);

            // Superadmin: Payment Method dropdown change → update DB via AJAX
            $(document).on('change', '.payment-method-dropdown', function () {
                var $select    = $(this);
                var paymentId  = $select.data('payment-id');
                var newMethod  = $select.val();
                var paid       = parseFloat($select.data('paid')) || 0;
                var $row       = $select.closest('tr');

                // Store original for revert on error
                if (!$select.data('original')) {
                    $select.data('original', $select.find('option:selected').val());
                }

                $.ajax({
                    url: '{{ route("payment.update.method") }}',
                    method: 'POST',
                    data: {
                        _token:         '{{ csrf_token() }}',
                        payment_id:     paymentId,
                        payment_method: newMethod,
                    },
                    success: function (res) {
                        if (res.success) {
                            $select.data('original', newMethod);

                            // Update row cells live (col: 2=Cash,3=Card,4=Bank,5=Adjustment)
                            var cols = {
                                'Cash Payment':  2,
                                'Card Payment':  3,
                                'Bank Transfer': 4,
                                'Adjustment':    5,
                            };
                            var $cells = $row.find('td');
                            // Reset all method columns to 0
                            $cells.eq(2).text('0');
                            $cells.eq(3).text('0');
                            $cells.eq(4).text('0');
                            $cells.eq(5).text('0');
                            // Set selected column to paid total
                            if (cols[newMethod] !== undefined) {
                                $cells.eq(cols[newMethod]).text(paid);
                            }

                            toastr.success(res.message, 'Updated', { toastClass: 'toast-success' });
                        }
                    },
                    error: function () {
                        $select.val($select.data('original'));
                        toastr.error('Failed to update payment method.', 'Error', { toastClass: 'toast-error' });
                    }
                });
            });
            $('#exportCsvBtn').on('click', function () {
                var csvContent = "data:text/csv;charset=utf-8,Family ID,Start Date,Cash Payment,Card Payment,Bank Transfer,Adjustment,Total Payment,Payment Method\n";
                var rows = table.rows().data();
                rows.each(function (row) {
                    csvContent += row.join(',') + "\n";
                });
                var encodedUri = encodeURI(csvContent);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "payments.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                toastr.success("CSV exported successfully!", "Success", { toastClass: "toast-success" });
            });

            // Form validation
            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                const fromDate = $('#from_date');
                const toDate = $('#to_date');
                const paymentMethod = $('select[name="payment_method"]');
                let valid = true;

                if (!fromDate.val()) {
                    fromDate.addClass('error').removeClass('valid');
                    $('#from_date_feedback').show();
                    valid = false;
                } else {
                    fromDate.addClass('valid').removeClass('error');
                    $('#from_date_feedback').hide();
                }

                if (!toDate.val()) {
                    toDate.addClass('error').removeClass('valid');
                    $('#to_date_feedback').show();
                    valid = false;
                } else {
                    toDate.addClass('valid').removeClass('error');
                    $('#to_date_feedback').hide();
                }

                if (!paymentMethod.val()) {
                    paymentMethod.addClass('error').removeClass('valid');
                    $('#payment_method_feedback').show();
                    valid = false;
                } else {
                    paymentMethod.addClass('valid').removeClass('error');
                    $('#payment_method_feedback').hide();
                }

                if (valid) {
                    this.submit();
                } else {
                    toastr.error("Please fill all required fields.", "Error", { toastClass: "toast-error" });
                }
            });

            // Real-time validation
            $('#from_date, #to_date').on('input', function () {
                const feedback = $(`#${this.id}_feedback`);
                if (this.value && this.value.trim() !== '') {
                    $(this).addClass('valid').removeClass('error');
                    feedback.hide();
                } else {
                    $(this).addClass('error').removeClass('valid');
                    feedback.show();
                }
            });

            $('select[name="payment_method"]').on('change', function () {
                const feedback = $('#payment_method_feedback');
                if (this.value && this.value.trim() !== '') {
                    $(this).addClass('valid').removeClass('error');
                    feedback.hide();
                } else {
                    $(this).addClass('error').removeClass('valid');
                    feedback.show();
                }
            });
        });
    </script>
@endsection
