@extends('layouts.branchDashboardApp')

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">Previous Payments</h1>
            <p class="text-lg text-[var(--text-dark)]">View and manage all previous payment records.</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Toastify({
                            text: "{{ $error }}",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                    });
                </script>
            @endforeach
        @endif

        <!-- Search Form -->
        <div class="card form-section">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-search h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Search Payments</h2>
            </div>
            <form method="GET" action="{{ route('payment.previous.show') }}" id="searchForm" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-dark)] mb-1">Family ID *</label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="number" value="{{ @$family_id }}" name="family_id" id="family_id" class="input-field pl-10" placeholder="Enter Family ID" required>
                        </div>
                        <div class="error-message" id="family_id_feedback">Please enter a valid Family ID.</div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Payments Table -->
        @isset($payments)
            @if (count($payments) > 0)
                <div class="card form-section mt-5">
                    <div class="flex items-center mb-4">
                        <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                            <i class="fas fa-table h-5 w-5 text-white text-lg"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">Payment Records</h2>
                    </div>
                    <div class="mb-4">
                        <div class="relative">
                            <i class="fas fa-comment absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                            <input type="text" id="payment_comment" value="{{ @$paymentComment->comments }}" class="input-field pl-10" placeholder="Add a comment...">
                            <input type="hidden" value="{{ @$family_id }}" id="fam_id">
                        </div>
                        <button class="btn-primary mt-2 flex items-center saveComment">
                            <i class="fas fa-save mr-2"></i> Save Comment
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="paymentTable" class="table w-full" style="table-layout: fixed; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 65px; padding: 0.75rem 0.5rem;">Family<br>ID</th>
                                    <th style="width: 85px; padding: 0.75rem 0.5rem;">Payment<br>From</th>
                                    <th style="width: 85px; padding: 0.75rem 0.5rem;">Payment<br>To</th>
                                    <th style="width: 95px; padding: 0.75rem 0.5rem;">Payment<br>Date</th>
                                    <th style="width: 65px; padding: 0.75rem 0.5rem;">Package</th>
                                    <th style="width: 55px; padding: 0.75rem 0.5rem;">Paid</th>
                                    <th style="width: 55px; padding: 0.75rem 0.15rem;">Cash<br>Payment</th>
                                    <th style="width: 55px; padding: 0.75rem 0.15rem;">Card<br>Payment</th>
                                    <th style="width: 60px; padding: 0.75rem 0.15rem;">Bank<br>Transfer</th>
                                    <th style="width: 55px; padding: 0.75rem 0.15rem;">Adjustment</th>
                                    <th style="width: 60px; padding: 0.75rem 0.5rem;">Total</th>
                                    <th style="width: 75px; padding: 0.75rem 0.5rem;">Collector</th>
                                    <th style="width: 85px; padding: 0.75rem 0.5rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $isSuperAdmin = auth()->user()->email === 'superadmin@frobel.com';
                                    $cutoffDate = \Carbon\Carbon::now()->subMonths(4);
                                @endphp
                                @foreach ($payments as $payment)
                                    @php
                                        $paymentDate = \Carbon\Carbon::parse($payment->paymentdate);
                                        $isOld = $paymentDate->lt($cutoffDate);
                                    @endphp
                                    @if (!$isOld || $isSuperAdmin)
                                    <tr id="{{ $payment->paymentid }}">
                                        <td>{{ $payment->paymentfamilyid }}</td>
                                        <td>{{ $payment->formatted_paymentfrom }}</td>
                                        <td>{{ $payment->formatted_paymentto }}</td>
                                        <td>{{ $payment->formatted_paymentdate }}</td>
                                        <td>{!! str_replace('for ', 'for<br>', $payment->package) !!}</td>
                                        <td>{{ $payment->paid }}</td>
                                        <td>{{ $payment->cash_payment ?? '0' }}</td>
                                        <td>{{ $payment->card_payment ?? '0' }}</td>
                                        <td>{{ $payment->bank_transfer ?? '0' }}</td>
                                        <td>{{ $payment->adjustment ?? '0' }}</td>
                                        <td>{{ $payment->paid }}</td>
                                        <td>{!! preg_replace('/^(\S+)\s/', '$1<br>', $payment->collector) !!}</td>
                                        <td>
                                            <div class="flex gap-2">
                                                <button class="btn-primary btn-sm print-row-btn printRecp-{{ $payment->paymentid }}">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                @php
                                                    $userPermissions = auth()->user()->permissions ?? null;
                                                    $pagePermissions = [];
                                                    if ($userPermissions && !is_null($userPermissions->page_name)) {
                                                        $pagePermissions = json_decode($userPermissions->page_name, true) ?? [];
                                                    }
                                                    $hasDeletePreviousPaymentsAccess = isset($pagePermissions['delete_previous_payments_button']) && $pagePermissions['delete_previous_payments_button'] === 'on';
                                                @endphp
                                                @if ($hasDeletePreviousPaymentsAccess)
                                                    <form action="{{ url('PrevDelete', $payment->paymentid) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center text-[var(--error)] mt-8">
                    <h5>No record found corresponding to this family ID.</h5>
                </div>
            @endif
        @endisset
    </div>
</div>

<!-- Include DataTables and Font Awesome -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

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

    .registration-container .btn-danger {
        background: linear-gradient(45deg, var(--error), #b91c1c);
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
        background: linear-gradient(45deg, #b91c1c, var(--error));
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.2);
    }

    .registration-container .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.65rem;
        height: 1.8rem;
    }
    
    #paymentTable {
        width: 100% !important;
        max-width: 100%;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
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
        padding: 0.75rem 0.5rem;
        text-align: left;
        font-size: 0.75rem;
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.3;
    }

    .registration-container .table tbody td {
        vertical-align: middle;
        border-color: var(--border);
        padding: 0.75rem 0.5rem;
        color: var(--text-dark);
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .registration-container .table tbody td:nth-child(5) {
        white-space: normal;
        word-break: break-word;
    }

    .registration-container .table tbody td:nth-child(12) {
        white-space: normal;
        word-break: break-word;
    }

    .registration-container .table tbody td:nth-child(7),
    .registration-container .table tbody td:nth-child(8),
    .registration-container .table tbody td:nth-child(9),
    .registration-container .table tbody td:nth-child(10) {
        padding: 0.75rem 0.15rem;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        $('#paymentTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copy',
                    className: 'btn btn-sm btn-primary',
                    title: 'Previous Payments',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'csv',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    className: 'btn btn-sm btn-primary',
                    title: 'Previous Payments',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-primary',
                    title: 'Previous Payments',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-primary',
                    title: 'Previous Payments',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-sm btn-primary',
                    title: 'Previous Payments',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ],
            pageLength: 10,
            responsive: false,
            scrollX: false,
            autoWidth: false,
            columnDefs: [
                { targets: '_all', orderable: false }
            ]
        });

        // Print receipt button handler
        document.querySelectorAll('.print-row-btn').forEach(button => {
            button.addEventListener('click', function() {
                var paymentId = this.className.match(/printRecp-(\d+)/)[1];
                fetch('{{ url('getIndividualReceipt') }}/' + paymentId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.pdfDataUri) {
                            var newWindow = window.open();
                            newWindow.document.write('<iframe src="' + data.pdfDataUri + '" style="width:100%; height:100%;"></iframe>');
                        } else {
                            Toastify({
                                text: "Failed to generate receipt. No data returned.",
                                duration: 5000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#ef4444",
                                stopOnFocus: true,
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Toastify({
                            text: "Failed to generate receipt. Please try again.",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#ef4444",
                            stopOnFocus: true,
                        }).showToast();
                    });
            });
        });

        // Save comment handler
        $('.saveComment').on('click', function() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var paymentComment = $('#payment_comment').val();
            var familyId = $('#fam_id').val();

            var postData = {
                _token: csrfToken,
                payment_comment: paymentComment,
                family_id: familyId
            };

            $.ajax({
                url: "{{ route('updatePaymentComment') }}",
                method: "POST",
                data: postData,
                success: function(response) {
                    Toastify({
                        text: "Payment comment updated successfully!",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#10b981",
                        stopOnFocus: true,
                    }).showToast();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Toastify({
                        text: "Failed to update payment comment. Please try again.",
                        duration: 5000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#ef4444",
                        stopOnFocus: true,
                    }).showToast();
                }
            });
        });

        // Form validation
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const familyId = document.getElementById('family_id');
            const feedback = document.getElementById('family_id_feedback');

            if (!familyId.value || familyId.value.trim() === '') {
                familyId.classList.add('error');
                familyId.classList.remove('valid');
                feedback.style.display = 'block';
                Toastify({
                    text: "Please enter a Family ID",
                    duration: 5000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#ef4444",
                    stopOnFocus: true,
                }).showToast();
                familyId.focus();
                return false;
            }

            familyId.classList.add('valid');
            familyId.classList.remove('error');
            feedback.style.display = 'none';
            this.submit();
        });

        // Real-time validation
        document.getElementById('family_id').addEventListener('input', function() {
            const feedback = document.getElementById('family_id_feedback');
            if (this.value && this.value.trim() !== '') {
                this.classList.add('valid');
                this.classList.remove('error');
                feedback.style.display = 'none';
            } else {
                this.classList.add('error');
                this.classList.remove('valid');
                feedback.style.display = 'block';
            }
        });
    });
</script>
@endsection
