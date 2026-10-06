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
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: #000;
    }
    
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: #fff;
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
    
    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #000;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
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
    
    .filter-section {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #e0e7ff;
    }
    
    .view-records-section {
        margin-top: 30px;
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
    <h1 style="color: #20439F; margin-bottom: 30px;">
        <i class="fas fa-bolt"></i> Crash Course Management
    </h1>
    
    <!-- Section 1: Package Management -->
    <div class="section-container">
        <h2 class="section-title">
            <i class="fas fa-box"></i> Package Management
        </h2>
        
        <form id="packageForm">
            @csrf
            <input type="hidden" id="package_id" name="package_id">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Package Name *</label>
                        <input type="text" class="form-control" id="package_name" name="package_name" required placeholder="Enter package name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Price *</label>
                        <input type="number" class="form-control" id="package_price" name="package_price" required placeholder="Enter price" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary" id="packageSubmitBtn">
                                <i class="fas fa-save"></i> Save Package
                            </button>
                            <button type="button" class="btn btn-secondary" id="packageCancelBtn" style="display:none;" onclick="resetPackageForm()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Package Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="packagesTableBody">
                    @if(isset($packages) && $packages->count() > 0)
                        @foreach($packages as $package)
                            <tr id="package_row_{{ $package->id }}">
                                <td>{{ $package->id }}</td>
                                <td>{{ $package->name }}</td>
                                <td>£{{ number_format($package->price, 2) }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning btn-sm btn-icon-only" onclick="editPackage({{ $package->id }}, '{{ addslashes($package->name) }}', {{ $package->price }})" title="Edit Package">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-icon-only" onclick="deletePackage({{ $package->id }})" title="Delete Package">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p>No packages created yet. Create your first package above.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Section 2: Student Registration for Packages -->
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
                            <option value="">-- Select a Student --</option>
                            @foreach ($students as $student)
                                @php
                                    $studentName = trim(($student->studentname ?? '') . ' ' . ($student->studentsur ?? ''));
                                @endphp
                                @if(!empty($studentName) && !empty($student->admissionid))
                                    <option
                                        value="{{ $studentName }}-{{ $student->admissionid }}">
                                        {{ $studentName }}-{{ $student->admissionid }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Select Package *</label>
                        <select class="form-control" id="package_id_reg" name="package_id" required>
                            <option value="">-- Select Package --</option>
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
    
    <!-- Section 3: Receive Payment -->
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
                                    <option
                                        value="{{ $studentName }}-{{ $student->admissionid }}">
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
                                <td>{{ $payment->family_id }}</td>
                                <td>£{{ number_format($payment->amount_paid, 2) }}</td>
                                <td><span class="badge badge-success">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                                <td>{{ date('d/m/Y', strtotime($payment->payment_date)) }}</td>
                                <td>
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
    
    <!-- Section 4: Attendance Management -->
    <div class="section-container">
        <h2 class="section-title">
            <i class="fas fa-calendar-check"></i> Attendance Management for Packages
        </h2>
        
        <form id="attendanceForm">
            @csrf
            <input type="hidden" id="attendance_id" name="attendance_id">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label">Select Candidate *</label>
                        <select class="form-control" id="attendance_candidate_id" name="candidate_id" required>
                            <option value="">-- Select a Student --</option>
                            @foreach ($students as $student)
                                @php
                                    $studentName = trim(($student->studentname ?? '') . ' ' . ($student->studentsur ?? ''));
                                @endphp
                                @if(!empty($studentName) && !empty($student->admissionid))
                                    <option
                                        value="{{ $studentName }}-{{ $student->admissionid }}">
                                        {{ $studentName }}-{{ $student->admissionid }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Subject *</label>
                        <input type="text" class="form-control" id="attendance_subject" name="subject" required placeholder="Enter Subject">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Teacher *</label>
                        <input type="text" class="form-control" id="attendance_teacher" name="teacher" required placeholder="Enter Teacher Name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Time Slot *</label>
                        <input type="text" class="form-control" id="attendance_timeslot" name="timeslot" required placeholder="e.g., 9:00 AM - 10:00 AM">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="attendanceSubmitBtn">
                        <i class="fas fa-check-circle"></i> Mark Attendance
                    </button>
                    <button type="button" class="btn btn-secondary" id="attendanceCancelBtn" style="display:none;" onclick="resetAttendanceForm()">
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
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Time Slot</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    @if(isset($attendanceRecords) && $attendanceRecords->count() > 0)
                        @foreach($attendanceRecords as $attendance)
                            <tr id="attendance_row_{{ $attendance->id }}">
                                <td>{{ $attendance->id }}</td>
                                <td>{{ $attendance->candidate_name }}</td>
                                <td>{{ $attendance->family_id }}</td>
                                <td>{{ $attendance->subject }}</td>
                                <td>{{ $attendance->teacher }}</td>
                                <td>{{ $attendance->timeslot }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deleteAttendance({{ $attendance->id }})" title="Delete Attendance">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No attendance records yet. Mark attendance above.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Section 5: View Records -->
    <div class="section-container view-records-section">
        <h2 class="section-title">
            <i class="fas fa-list-alt"></i> View Records
        </h2>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <h4 style="color: #20439F; margin-bottom: 15px;">Filter & Search</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Search by Student</label>
                        <input type="text" class="form-control" id="filter_student" placeholder="Student ID or Name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Filter by Package</label>
                        <select class="form-control" id="filter_package">
                            <option value="">All Packages</option>
                            @if(isset($packages) && $packages->count() > 0)
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Filter by Date</label>
                        <input type="date" class="form-control" id="filter_date">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" class="btn btn-info" onclick="applyFilters()">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> Reset Filters
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabs for different record types -->
        <ul class="nav nav-tabs" id="recordsTab" role="tablist" style="margin-top: 20px;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="registrations-tab" data-bs-toggle="tab" data-bs-target="#registrations" type="button" role="tab">
                    <i class="fas fa-user-check"></i> Registration Records
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                    <i class="fas fa-money-bill"></i> Payment History
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                    <i class="fas fa-calendar-alt"></i> Attendance Logs
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="recordsTabContent" style="margin-top: 20px;">
            <!-- Registration Records Tab -->
            <div class="tab-pane fade show active" id="registrations" role="tabpanel">
                <div style="margin-bottom: 15px; text-align: right;">
                    <button type="button" class="btn btn-success" onclick="exportRecords('pdf', 'registrations')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportRecords('excel', 'registrations')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Candidate Name</th>
                                <th>Family ID</th>
                                <th>Package Name</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody id="viewRegistrationsTableBody">
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-user-slash"></i>
                                    <p>No registration records found.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Payment History Tab -->
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div style="margin-bottom: 15px; text-align: right;">
                    <button type="button" class="btn btn-success" onclick="exportRecords('pdf', 'payments')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportRecords('excel', 'payments')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Candidate Name</th>
                                <th>Family ID</th>
                                <th>Amount Paid</th>
                                <th>Payment Method</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody id="viewPaymentsTableBody">
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-money-bill-alt"></i>
                                    <p>No payment records found.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Attendance Logs Tab -->
            <div class="tab-pane fade" id="attendance" role="tabpanel">
                <div style="margin-bottom: 15px; text-align: right;">
                    <button type="button" class="btn btn-success" onclick="exportRecords('pdf', 'attendance')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportRecords('excel', 'attendance')">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Candidate Name</th>
                                <th>Family ID</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Time Slot</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="viewAttendanceTableBody">
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>No attendance records found.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
    
    // Button disable/enable helper functions
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
    
    // Function to format date as DD/MM/YYYY
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
    // Function to update package dropdowns
    function updatePackageDropdowns(pkg) {
        const dropdowns = ['#package_id_reg', '#payment_package_name', '#filter_package'];
        dropdowns.forEach(selector => {
            const dropdown = $(selector);
            // Check if option already exists
            if (dropdown.find(`option[value="${pkg.id}"]`).length === 0) {
                dropdown.append(`<option value="${pkg.id}">${pkg.name} - £${parseFloat(pkg.price).toFixed(2)}</option>`);
            } else {
                // Update existing option
                dropdown.find(`option[value="${pkg.id}"]`).text(`${pkg.name} - £${parseFloat(pkg.price).toFixed(2)}`);
            }
        });
    }
    
    // Function to add package row to table
    function addPackageRow(pkg) {
        const tbody = $('#packagesTableBody');
        // Remove empty state if exists
        tbody.find('.empty-state').closest('tr').remove();
        
        const row = `
            <tr id="package_row_${pkg.id}">
                <td>${pkg.id}</td>
                <td>${pkg.name}</td>
                <td>£${parseFloat(pkg.price).toFixed(2)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-warning btn-sm btn-icon-only" onclick="editPackage(${pkg.id}, '${pkg.name.replace(/'/g, "\\'")}', ${pkg.price})" title="Edit Package">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-icon-only" onclick="deletePackage(${pkg.id})" title="Delete Package">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.prepend(row);
    }
    
    // Function to update package row in table
    function updatePackageRow(pkg) {
        const row = $(`#package_row_${pkg.id}`);
        row.find('td:eq(1)').text(pkg.name);
        row.find('td:eq(2)').text('£' + parseFloat(pkg.price).toFixed(2));
        row.find('button.btn-warning').attr('onclick', `editPackage(${pkg.id}, '${pkg.name.replace(/'/g, "\\'")}', ${pkg.price})`);
    }
    
    // Package Management - Backend Integration
    $('#packageForm').on('submit', function(e) {
        e.preventDefault();
        const packageId = $('#package_id').val();
        const submitBtnText = packageId ? '<i class="fas fa-save"></i> Update Package' : '<i class="fas fa-save"></i> Save Package';
        
        // Disable button
        disableSubmitButton('packageSubmitBtn', submitBtnText);
        
        const formData = {
            package_id: packageId,
            name: $('#package_name').val(),
            price: $('#package_price').val(),
            _token: $('input[name="_token"]').val()
        };
        
        const url = packageId ? '/crash-course/package/update' : '/crash-course/package/store';
        const method = packageId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (packageId) {
                        // Update existing row
                        updatePackageRow(response.package);
                        updatePackageDropdowns(response.package);
                        showToast(response.message, 'success');
                    } else {
                        // Add new row
                        addPackageRow(response.package);
                        updatePackageDropdowns(response.package);
                        // Update global data
                        if (window.crashCourseData) {
                            window.crashCourseData.packages.unshift(response.package);
                        }
                        showToast(response.message, 'success');
                    }
                    resetPackageForm();
                    refreshViewRecords();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                // Re-enable button
                enableSubmitButton('packageSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                // Re-enable button
                enableSubmitButton('packageSubmitBtn');
            }
        });
    });
    
    function editPackage(id, name, price) {
        $('#package_id').val(id);
        $('#package_name').val(name);
        $('#package_price').val(price);
        $('#packageSubmitBtn').html('<i class="fas fa-save"></i> Update Package');
        $('#packageCancelBtn').show();
    }
    
    function resetPackageForm() {
        $('#packageForm')[0].reset();
        $('#package_id').val('');
        $('#packageSubmitBtn').html('<i class="fas fa-save"></i> Save Package');
        $('#packageCancelBtn').hide();
    }
    
    function deletePackage(id) {
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
                    url: '/crash-course/package/delete/' + id,
                    method: 'DELETE',
                    data: { _token: $('input[name="_token"]').val() },
                    success: function(response) {
                        if (response.success) {
                            // Remove row from table
                            $('#package_row_' + id).remove();
                            // Remove from dropdowns
                            $('#package_id_reg, #payment_package_name, #filter_package').find(`option[value="${id}"]`).remove();
                            // Check if table is empty
                            if ($('#packagesTableBody tr').length === 0) {
                                $('#packagesTableBody').html('<tr><td colspan="4" class="empty-state"><i class="fas fa-box-open"></i><p>No packages created yet. Create your first package above.</p></td></tr>');
                            }
                            // Update global data
                            if (window.crashCourseData) {
                                window.crashCourseData.packages = window.crashCourseData.packages.filter(p => p.id != id);
                            }
                            showToast(response.message, 'success');
                            refreshViewRecords();
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
    
    // Function to add registration row
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
    
    // Function to update registration row
    function updateRegistrationRow(registration, pkg) {
        const row = $(`#registration_row_${registration.id}`);
        row.find('td:eq(1)').text(registration.candidate_name || '');
        row.find('td:eq(2)').text(registration.family_id || '');
        row.find('td:eq(3)').text(pkg ? pkg.name : 'N/A');
    }
    
    // Student Registration - Backend Integration
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        const registrationId = $('#registration_id').val();
        const candidateValue = $('#student_id').val();
        const packageId = $('#package_id_reg').val();
        
        if (!candidateValue || !packageId) {
            showToast('Please fill all required fields', 'error');
            return;
        }
        
        // Disable button
        disableSubmitButton('registrationSubmitBtn');
        
        const formData = {
            registration_id: registrationId,
            candidate_value: candidateValue,
            package_id: packageId,
            _token: $('input[name="_token"]').val()
        };
        
        const url = registrationId ? '/crash-course/registration/update' : '/crash-course/registration/store';
        const method = registrationId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (registrationId) {
                        updateRegistrationRow(response.registration, response.package);
                        // Update global data
                        if (window.crashCourseData) {
                            const index = window.crashCourseData.registrations.findIndex(r => r.id == registrationId);
                            if (index !== -1) {
                                window.crashCourseData.registrations[index] = response.registration;
                            }
                        }
                        showToast(response.message, 'success');
                    } else {
                        addRegistrationRow(response.registration, response.package);
                        // Update global data
                        if (window.crashCourseData) {
                            window.crashCourseData.registrations.unshift(response.registration);
                        }
                        showToast(response.message, 'success');
                    }
                    resetRegistrationForm();
                    refreshViewRecords();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                // Re-enable button
                enableSubmitButton('registrationSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                // Re-enable button
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
                            // Update global data
                            if (window.crashCourseData) {
                                window.crashCourseData.registrations = window.crashCourseData.registrations.filter(r => r.id != id);
                            }
                            showToast(response.message, 'success');
                            refreshViewRecords();
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
    
    // Function to add payment row
    function addPaymentRow(payment) {
        const tbody = $('#paymentsTableBody');
        tbody.find('.empty-state').closest('tr').remove();
        
        const row = `
            <tr id="payment_row_${payment.id}">
                <td>${payment.id}</td>
                <td>${payment.candidate_name || ''}</td>
                <td>${payment.family_id || ''}</td>
                <td>£${parseFloat(payment.amount_paid || 0).toFixed(2)}</td>
                <td><span class="badge badge-success">${payment.payment_method ? payment.payment_method.replace('_', ' ') : ''}</span></td>
                <td>${payment.payment_date ? formatDate(payment.payment_date) : ''}</td>
                <td>
                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deletePayment(${payment.id})" title="Delete Payment">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.prepend(row);
    }
    
    // Function to update payment row
    function updatePaymentRow(payment) {
        const row = $(`#payment_row_${payment.id}`);
        row.find('td:eq(1)').text(payment.candidate_name || '');
        row.find('td:eq(2)').text(payment.family_id || '');
        row.find('td:eq(3)').text('£' + parseFloat(payment.amount_paid || 0).toFixed(2));
        row.find('td:eq(4)').html(`<span class="badge badge-success">${payment.payment_method ? payment.payment_method.replace('_', ' ') : ''}</span>`);
        row.find('td:eq(5)').text(payment.payment_date ? formatDate(payment.payment_date) : '');
    }
    
    // Payment Management - Backend Integration
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        const paymentId = $('#payment_id').val();
        const candidateValue = $('#payment_student_id').val();
        const packageId = $('#payment_package_name').val();
        
        if (!candidateValue || !packageId) {
            showToast('Please fill all required fields', 'error');
            return;
        }
        
        // Disable button
        disableSubmitButton('paymentSubmitBtn');
        
        const formData = {
            payment_id: paymentId,
            candidate_value: candidateValue,
            package_id: packageId,
            amount_paid: $('#payment_amount').val(),
            payment_method: $('#payment_method').val(),
            _token: $('input[name="_token"]').val()
        };
        
        const url = paymentId ? '/crash-course/payment/update' : '/crash-course/payment/store';
        const method = paymentId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (paymentId) {
                        updatePaymentRow(response.payment);
                        // Update global data
                        if (window.crashCourseData) {
                            const index = window.crashCourseData.payments.findIndex(p => p.id == paymentId);
                            if (index !== -1) {
                                window.crashCourseData.payments[index] = response.payment;
                            }
                        }
                        showToast(response.message, 'success');
                    } else {
                        addPaymentRow(response.payment);
                        // Update global data
                        if (window.crashCourseData) {
                            window.crashCourseData.payments.unshift(response.payment);
                        }
                        showToast(response.message, 'success');
                    }
                    resetPaymentForm();
                    refreshViewRecords();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                // Re-enable button
                enableSubmitButton('paymentSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                // Re-enable button
                enableSubmitButton('paymentSubmitBtn');
            }
        });
    });
    
    function resetPaymentForm() {
        $('#paymentForm')[0].reset();
        $('#payment_id').val('');
        $('#paymentCancelBtn').hide();
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
                            // Update global data
                            if (window.crashCourseData) {
                                window.crashCourseData.payments = window.crashCourseData.payments.filter(p => p.id != id);
                            }
                            showToast(response.message, 'success');
                            refreshViewRecords();
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
    
    // Function to add attendance row
    function addAttendanceRow(attendance) {
        const tbody = $('#attendanceTableBody');
        tbody.find('.empty-state').closest('tr').remove();
        
        const row = `
            <tr id="attendance_row_${attendance.id}">
                <td>${attendance.id}</td>
                <td>${attendance.candidate_name || ''}</td>
                <td>${attendance.family_id || ''}</td>
                <td>${attendance.subject || ''}</td>
                <td>${attendance.teacher || ''}</td>
                <td>${attendance.timeslot || ''}</td>
                <td>
                    <button class="btn btn-danger btn-sm btn-icon-only" onclick="deleteAttendance(${attendance.id})" title="Delete Attendance">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.prepend(row);
    }
    
    // Function to update attendance row
    function updateAttendanceRow(attendance) {
        const row = $(`#attendance_row_${attendance.id}`);
        row.find('td:eq(1)').text(attendance.candidate_name || '');
        row.find('td:eq(2)').text(attendance.family_id || '');
        row.find('td:eq(3)').text(attendance.subject || '');
        row.find('td:eq(4)').text(attendance.teacher || '');
        row.find('td:eq(5)').text(attendance.timeslot || '');
    }
    
    // Attendance Management - Backend Integration
    $('#attendanceForm').on('submit', function(e) {
        e.preventDefault();
        const attendanceId = $('#attendance_id').val();
        const candidateValue = $('#attendance_candidate_id').val();
        
        if (!candidateValue) {
            showToast('Please select a candidate', 'error');
            return;
        }
        
        // Disable button
        disableSubmitButton('attendanceSubmitBtn');
        
        const formData = {
            attendance_id: attendanceId,
            candidate_value: candidateValue,
            subject: $('#attendance_subject').val(),
            teacher: $('#attendance_teacher').val(),
            timeslot: $('#attendance_timeslot').val(),
            _token: $('input[name="_token"]').val()
        };
        
        const url = attendanceId ? '/crash-course/attendance/update' : '/crash-course/attendance/store';
        const method = attendanceId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if (attendanceId) {
                        updateAttendanceRow(response.attendance);
                        // Update global data
                        if (window.crashCourseData) {
                            const index = window.crashCourseData.attendanceRecords.findIndex(a => a.id == attendanceId);
                            if (index !== -1) {
                                window.crashCourseData.attendanceRecords[index] = response.attendance;
                            }
                        }
                        showToast(response.message, 'success');
                    } else {
                        addAttendanceRow(response.attendance);
                        // Update global data
                        if (window.crashCourseData) {
                            window.crashCourseData.attendanceRecords.unshift(response.attendance);
                        }
                        showToast(response.message, 'success');
                    }
                    resetAttendanceForm();
                    refreshViewRecords();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                // Re-enable button
                enableSubmitButton('attendanceSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                // Re-enable button
                enableSubmitButton('attendanceSubmitBtn');
            }
        });
    });
    
    function resetAttendanceForm() {
        $('#attendanceForm')[0].reset();
        $('#attendance_id').val('');
        $('#attendanceCancelBtn').hide();
    }
    
    function deleteAttendance(id) {
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
                    url: '/crash-course/attendance/delete/' + id,
                    method: 'DELETE',
                    data: { _token: $('input[name="_token"]').val() },
                    success: function(response) {
                        if (response.success) {
                            $('#attendance_row_' + id).remove();
                            if ($('#attendanceTableBody tr').length === 0) {
                                $('#attendanceTableBody').html('<tr><td colspan="7" class="empty-state"><i class="fas fa-calendar-times"></i><p>No attendance records yet. Mark attendance above.</p></td></tr>');
                            }
                            // Update global data
                            if (window.crashCourseData) {
                                window.crashCourseData.attendanceRecords = window.crashCourseData.attendanceRecords.filter(a => a.id != id);
                            }
                            showToast(response.message, 'success');
                            refreshViewRecords();
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
    
    // View Records Functions
    function applyFilters() {
        const studentFilter = $('#filter_student').val().toLowerCase();
        const packageFilter = $('#filter_package').val();
        const dateFilter = $('#filter_date').val();
        
        updateViewRecords(studentFilter, packageFilter, dateFilter);
        showToast('Filters applied successfully', 'success');
    }
    
    function resetFilters() {
        $('#filter_student').val('');
        $('#filter_package').val('');
        $('#filter_date').val('');
        updateViewRecords();
    }
    
    function exportRecords(format, type = 'all') {
        const studentFilter = $('#filter_student').val();
        const packageFilter = $('#filter_package').val();
        const dateFilter = $('#filter_date').val();
        
        // Build query string
        const params = new URLSearchParams();
        params.append('type', type);
        if (studentFilter) params.append('student', studentFilter);
        if (packageFilter) params.append('package', packageFilter);
        if (dateFilter) params.append('date', dateFilter);
        
        const url = `/crash-course/export/${format}?${params.toString()}`;
        window.open(url, '_blank');
    }
    
    function updateViewRecords(studentFilter = '', packageFilter = '', dateFilter = '') {
        // Get data from backend (already loaded in page or from real-time fetch)
        const registrations = window.crashCourseData ? window.crashCourseData.registrations : @json($registrations ?? []);
        const payments = window.crashCourseData ? window.crashCourseData.payments : @json($payments ?? []);
        const attendanceRecords = window.crashCourseData ? window.crashCourseData.attendanceRecords : @json($attendanceRecords ?? []);
        const packages = window.crashCourseData ? window.crashCourseData.packages : @json($packages ?? []);
        
        // Filter registrations
        let filteredRegistrations = registrations;
        if (studentFilter) {
            filteredRegistrations = filteredRegistrations.filter(r => 
                (r.candidate_name && r.candidate_name.toLowerCase().includes(studentFilter)) ||
                (r.family_id && r.family_id.toString().includes(studentFilter))
            );
        }
        if (packageFilter) {
            filteredRegistrations = filteredRegistrations.filter(r => r.package_id == packageFilter);
        }
        
        renderViewRegistrationsTable(filteredRegistrations, packages);
        
        // Filter payments
        let filteredPayments = payments;
        if (studentFilter) {
            filteredPayments = filteredPayments.filter(p => 
                (p.candidate_name && p.candidate_name.toLowerCase().includes(studentFilter)) ||
                (p.family_id && p.family_id.toString().includes(studentFilter))
            );
        }
        if (dateFilter) {
            filteredPayments = filteredPayments.filter(p => p.payment_date === dateFilter);
        }
        
        renderViewPaymentsTable(filteredPayments);
        
        // Filter attendance
        let filteredAttendance = attendanceRecords;
        if (studentFilter) {
            filteredAttendance = filteredAttendance.filter(a => 
                (a.candidate_name && a.candidate_name.toLowerCase().includes(studentFilter)) ||
                (a.family_id && a.family_id.toString().includes(studentFilter))
            );
        }
        
        renderViewAttendanceTable(filteredAttendance);
    }
    
    function renderViewRegistrationsTable(data, packages) {
        const tbody = $('#viewRegistrationsTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.html('<tr><td colspan="5" class="empty-state"><i class="fas fa-user-slash"></i><p>No registration records found.</p></td></tr>');
            return;
        }
        
        data.forEach(reg => {
            const package = packages.find(p => p.id == reg.package_id);
            const row = `
                <tr>
                    <td>${reg.id}</td>
                    <td>${reg.candidate_name || ''}</td>
                    <td>${reg.family_id || ''}</td>
                    <td>${package ? package.name : 'N/A'}</td>
                    <td>${reg.created_at ? formatDate(reg.created_at) : ''}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    function renderViewPaymentsTable(data) {
        const tbody = $('#viewPaymentsTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.html('<tr><td colspan="6" class="empty-state"><i class="fas fa-money-bill-alt"></i><p>No payment records found.</p></td></tr>');
            return;
        }
        
        data.forEach(payment => {
            const row = `
                <tr>
                    <td>${payment.id}</td>
                    <td>${payment.candidate_name || ''}</td>
                    <td>${payment.family_id || ''}</td>
                    <td>£${parseFloat(payment.amount_paid || 0).toFixed(2)}</td>
                    <td>${payment.payment_method ? payment.payment_method.replace('_', ' ') : ''}</td>
                    <td>${payment.payment_date ? formatDate(payment.payment_date) : ''}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    function renderViewAttendanceTable(data) {
        const tbody = $('#viewAttendanceTableBody');
        tbody.empty();
        
        if (data.length === 0) {
            tbody.html('<tr><td colspan="7" class="empty-state"><i class="fas fa-calendar-times"></i><p>No attendance records found.</p></td></tr>');
            return;
        }
        
        data.forEach(attendance => {
            const row = `
                <tr>
                    <td>${attendance.id}</td>
                    <td>${attendance.candidate_name || ''}</td>
                    <td>${attendance.family_id || ''}</td>
                    <td>${attendance.subject || ''}</td>
                    <td>${attendance.teacher || ''}</td>
                    <td>${attendance.timeslot || ''}</td>
                    <td>${attendance.created_at ? formatDate(attendance.created_at) : ''}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Function to fetch and update View Records in real-time
    function refreshViewRecords() {
        $.ajax({
            url: '/crash-course/data',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update the global data variables
                    window.crashCourseData = {
                        registrations: response.registrations || [],
                        payments: response.payments || [],
                        attendanceRecords: response.attendanceRecords || [],
                        packages: response.packages || []
                    };
                    // Refresh the view records
                    updateViewRecords();
                }
            },
            error: function() {
                console.error('Failed to refresh view records');
                // Fallback to existing data
                updateViewRecords();
            }
        });
    }
    
    // Initialize Select2 - same as assign-book page
    $(document).ready(function() {
        // Initialize Select2 for student dropdowns - searchable by default
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
        
        $('#attendance_candidate_id').select2({
            placeholder: "-- Select a Student --",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize data object
        window.crashCourseData = {
            registrations: @json($registrations ?? []),
            payments: @json($payments ?? []),
            attendanceRecords: @json($attendanceRecords ?? []),
            packages: @json($packages ?? [])
        };
        
        // Load packages into dropdowns on page load
        const packages = @json($packages ?? []);
        packages.forEach(pkg => {
            $('#package_id_reg, #payment_package_name, #filter_package').each(function() {
                if ($(this).find(`option[value="${pkg.id}"]`).length === 0) {
                    $(this).append(`<option value="${pkg.id}">${pkg.name} - £${parseFloat(pkg.price).toFixed(2)}</option>`);
                }
            });
        });
        
        updateViewRecords();
    });
</script>
@endsection
