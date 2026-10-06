@extends('layouts.branchDashboardApp')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .main-content {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        margin: 20px;
        border: 1px solid #20439F;
    }

    .section-container {
        background: #f8fafc;
        border: 1px solid #e0e7ff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #20439F;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #20439F;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #20439F;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #20439F;
        box-shadow: 0 0 0 0.2rem rgba(32, 67, 159, 0.25);
        outline: none;
    }

    .btn-primary {
        background-color: #20439F;
        border-color: #20439F;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #163080;
        border-color: #163080;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
    }

    .table {
        background: #ffffff;
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: #20439F;
        color: #ffffff;
    }

    .table thead th {
        padding: 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    .table tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(32, 67, 159, 0.05);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success {
        background-color: #28a745;
        color: #fff;
    }

    .btn-icon-only {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .btn-icon-only i {
        font-size: 14px;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #20439F;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    /* Select2 Styling */
    .select2-container--default .select2-selection--single {
        border: 1px solid #e0e7ff;
        border-radius: 8px;
        height: 42px;
        padding: 5px 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #20439F;
    }

    .select2-dropdown {
        border: 1px solid #e0e7ff;
        border-radius: 8px;
    }
</style>

<div class="main-content">
    <a href="{{ url('crash-course') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Crash Course Dashboard
    </a>

    <h1 style="color: #20439F; margin-bottom: 30px;">
        <i class="fas fa-user-plus"></i> Registration & Payment
    </h1>

    <!-- Section 1: Student Registration for Packages -->
    <div class="section-container">
        <h2 class="section-title">
            <i class="fas fa-user-plus"></i> Student Registration for Packages
        </h2>

        <form id="registrationForm">
            @csrf
            <input type="hidden" id="registration_id" name="registration_id">
            <div class="row">
                <div class="col-md-6">
    <div class="form-group">
        <label class="form-label">Select Candidate *</label>
        <select class="form-control" id="student_id" name="student_id" required>
            <option value="">-- Select a Candidate --</option>
            @foreach ($paid_candidates as $candidate)
                @php
                    $name = $candidate['candidate_name'];
                    $family_id = $candidate['family_id'];
                    $value = $name . '-' . $family_id;
                @endphp
                
                <option value="{{ $value }}">
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>
</div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Select Package *</label>
                        <select class="form-control" id="package_id_reg" name="package_id" required>
                            <option value="">-- Select Package --</option>
                            @if(isset($packages) && $packages->count() > 0)
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }} - £{{ number_format($package->price, 2) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="registrationSubmitBtn">
                        <i class="fas fa-link"></i> Assign Package to Student
                    </button>
                    <button type="button" class="btn btn-secondary" id="registrationCancelBtn" style="display:none;" onclick="resetRegistrationForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Candidate Name</th>
                        <th>Family ID</th>
                        <th>Package Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="registrationsTableBody">
                    @if(isset($registrations) && $registrations->count() > 0)
                        @foreach($registrations as $reg)
                            @php
                                $package = isset($packages) ? $packages->firstWhere('id', $reg->package_id) : null;
                            @endphp
                            <tr id="registration_row_{{ $reg->id }}">
                                <td>{{ $reg->id }}</td>
                                <td>{{ $reg->candidate_name }}</td>
                                <td>{{ $reg->family_id }}</td>
                                <td>{{ $package ? $package->name : 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deleteRegistration({{ $reg->id }})" title="Delete Registration">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <p>No students registered yet. Register a student above.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Receive Payment for Packages -->
    <div class="section-container">
        <h2 class="section-title">
            <i class="fas fa-money-bill-wave"></i> Receive Payment for Packages
        </h2>

        <form id="paymentForm">
            @csrf
            <input type="hidden" id="payment_id" name="payment_id">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Select Candidate *</label>
                        <select class="form-control" id="payment_student_id" name="student_id" required>
                            <option value="">-- Select a Student --</option>
                            @foreach ($students as $student)
                                @php
                                    $studentName = trim(($student->studentname ?? '') . ' ' . ($student->studentsur ?? ''));
                                @endphp
                                @if(!empty($studentName) && !empty($student->admissionid))
                                    <option value="{{ $studentName }}-{{ $student->admissionid }}">
                                        {{ $studentName }}-{{ $student->admissionid }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Package Name *</label>
                        <select class="form-control" id="payment_package_name" name="package_name" required>
                            <option value="">-- Select Package --</option>
                            @if(isset($packages) && $packages->count() > 0)
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }} - £{{ number_format($package->price, 2) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Amount Paid *</label>
                        <input type="number" class="form-control" id="payment_amount" name="amount" required placeholder="Enter amount" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">-- Select Method --</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">
                        <i class="fas fa-save"></i> Save Payment
                    </button>
                    <button type="button" class="btn btn-secondary" id="paymentCancelBtn" style="display:none;" onclick="resetPaymentForm()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Package Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody">
                    @if(isset($payments) && $payments->count() > 0)
                        @foreach($payments as $payment)
                            <tr id="payment_row_{{ $payment->id }}">
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->candidate_name }}</td>
                                <td>{{ isset($payment->package_name) ? $payment->package_name : 'N/A' }}</td>
                                <td>£{{ number_format($payment->amount_paid, 2) }}</td>
                                <td><span class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                                <td>{{ date('d/m/Y', strtotime($payment->payment_date)) }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm btn-icon-only" onclick="generateReceipt({{ $payment->id }})" title="Generate Receipt" style="margin-right: 5px;">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deletePayment({{ $payment->id }})" title="Delete Payment">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-money-bill-alt"></i>
                                <p>No payments recorded yet. Add a payment above.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toastify helper functions
    function showToast(message, type = 'success') {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        Toastify({
            text: message,
            duration: type === 'error' ? 5000 : 3000,
            gravity: "top",
            position: "right",
            backgroundColor: colors[type] || colors.success,
            stopOnFocus: true,
        }).showToast();
    }

    function disableSubmitButton(buttonId, originalText) {
        const $btn = $('#' + buttonId);
        $btn.data('original-text', originalText || $btn.html());
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    }

    function enableSubmitButton(buttonId) {
        const $btn = $('#' + buttonId);
        const originalText = $btn.data('original-text');
        if (originalText) {
            $btn.html(originalText);
        }
        $btn.prop('disabled', false);
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }

    // Registration Functions
    function addRegistrationRow(registration, pkg) {
        const tbody = $('#registrationsTableBody');
        tbody.find('.empty-state').closest('tr').remove();

        const row = `
            <tr id="registration_row_${registration.id}">
                <td>${registration.id}</td>
                <td>${registration.candidate_name || ''}</td>
                <td>${registration.family_id || ''}</td>
                <td>${pkg ? pkg.name : 'N/A'}</td>
                <td>
                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deleteRegistration(${registration.id})" title="Delete Registration">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.prepend(row);
    }

    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        const candidateValue = $('#student_id').val();
        const packageId = $('#package_id_reg').val();

        if (!candidateValue || !packageId) {
            showToast('Please fill all required fields', 'error');
            return;
        }

        disableSubmitButton('registrationSubmitBtn');

        const formData = {
            candidate_value: candidateValue,
            package_id: packageId,
            _token: $('input[name="_token"]').val()
        };

        $.ajax({
            url: '/crash-course/registration/store',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    addRegistrationRow(response.registration, response.package);
                    showToast(response.message, 'success');
                    resetRegistrationForm();
                    // Refresh candidate dropdown
                    refreshCandidatesDropdown();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                enableSubmitButton('registrationSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                enableSubmitButton('registrationSubmitBtn');
            }
        });
    });

    function resetRegistrationForm() {
        $('#registrationForm')[0].reset();
        $('#registration_id').val('');
        $('#registrationCancelBtn').hide();
    }

    function deleteRegistration(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/crash-course/registration/delete/' + id,
                    method: 'DELETE',
                    data: { _token: $('input[name="_token"]').val() },
                    success: function(response) {
                        if (response.success) {
                            $('#registration_row_' + id).remove();
                            if ($('#registrationsTableBody tr').length === 0) {
                                $('#registrationsTableBody').html('<tr><td colspan="5" class="empty-state"><i class="fas fa-user-slash"></i><p>No students registered yet. Register a student above.</p></td></tr>');
                            }
                            showToast(response.message, 'success');
                        } else {
                            showToast(response.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            }
        });
    }

    // Payment Functions
    function addPaymentRow(payment) {
        const tbody = $('#paymentsTableBody');
        tbody.find('.empty-state').closest('tr').remove();

        const row = `
            <tr id="payment_row_${payment.id}">
                <td>${payment.id}</td>
                <td>${payment.candidate_name || ''}</td>
                <td>${payment.package_name || 'N/A'}</td>
                <td>£${parseFloat(payment.amount_paid || 0).toFixed(2)}</td>
                <td><span class="badge badge-success">${payment.payment_method ? payment.payment_method.replace('_', ' ') : ''}</span></td>
                <td>${payment.payment_date ? formatDate(payment.payment_date) : ''}</td>
                <td>
                    <button class="btn btn-success btn-sm btn-icon-only" onclick="generateReceipt(${payment.id})" title="Generate Receipt" style="margin-right: 5px;">
                        <i class="fas fa-receipt"></i>
                    </button>
                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deletePayment(${payment.id})" title="Delete Payment">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.prepend(row);
    }

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        const candidateValue = $('#payment_student_id').val();
        const packageId = $('#payment_package_name').val();

        if (!candidateValue || !packageId) {
            showToast('Please fill all required fields', 'error');
            return;
        }

        disableSubmitButton('paymentSubmitBtn');

        const formData = {
            candidate_value: candidateValue,
            package_id: packageId,
            amount_paid: $('#payment_amount').val(),
            payment_method: $('#payment_method').val(),
            _token: $('input[name="_token"]').val()
        };

        $.ajax({
            url: '/crash-course/payment/store',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    addPaymentRow(response.payment);
                    showToast(response.message, 'success');
                    resetPaymentForm();
                    // Generate receipt in new tab
                    setTimeout(function() {
                        generateReceipt(response.payment.id);
                    }, 500);
                    // Refresh candidate dropdown
                    refreshCandidatesDropdown();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                enableSubmitButton('paymentSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                enableSubmitButton('paymentSubmitBtn');
            }
        });
    });

    function resetPaymentForm() {
        $('#paymentForm')[0].reset();
        $('#payment_id').val('');
        $('#paymentCancelBtn').hide();
    }

    function generateReceipt(id) {
        // Open receipt in new tab
        window.open('/crash-course/payment/receipt/' + id, '_blank');
    }

    function refreshCandidatesDropdown() {
        $.ajax({
            url: '/crash-course/candidates',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Clear existing options except the first one
                    $('#student_id').empty().append('<option value="">-- Select a Candidate --</option>');

                    // Add new candidates
                    response.candidates.forEach(function(candidate) {
                        const value = candidate.candidate_name + '-' + candidate.family_id;
                        $('#student_id').append('<option value="' + value + '">' + value + '</option>');
                    });

                    // Refresh Select2
                    $('#student_id').trigger('change');
                }
            },
            error: function(xhr) {
                console.error('Error refreshing candidates:', xhr);
            }
        });
    }

    function deletePayment(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/crash-course/payment/delete/' + id,
                    method: 'DELETE',
                    data: { _token: $('input[name="_token"]').val() },
                    success: function(response) {
                        if (response.success) {
                            $('#payment_row_' + id).remove();
                            if ($('#paymentsTableBody tr').length === 0) {
                                $('#paymentsTableBody').html('<tr><td colspan="7" class="empty-state"><i class="fas fa-money-bill-alt"></i><p>No payments recorded yet. Add a payment above.</p></td></tr>');
                            }
                            showToast(response.message, 'success');
                        } else {
                            showToast(response.message || 'Something went wrong', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                    }
                });
            }
        });
    }

    // Initialize Select2
    $(document).ready(function() {
        $('#student_id').select2({
            placeholder: "-- Select a Student --",
            allowClear: true,
            width: '100%'
        });

        $('#payment_student_id').select2({
            placeholder: "-- Select a Student --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection

