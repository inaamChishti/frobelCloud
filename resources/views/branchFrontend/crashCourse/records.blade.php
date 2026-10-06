@extends('layouts.branchDashboardApp')

@section('content')
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
    
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: #fff;
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
</style>

<div class="main-content">
    <a href="{{ url('crash-course') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Crash Course Dashboard
    </a>
    
    <h1 style="color: #20439F; margin-bottom: 30px;">
        <i class="fas fa-folder-open"></i> View Records
    </h1>
    
    <!-- View Records Section -->
    <div class="section-container">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
    
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    }
    
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
        
        const params = new URLSearchParams();
        params.append('type', type);
        if (studentFilter) params.append('student', studentFilter);
        if (packageFilter) params.append('package', packageFilter);
        if (dateFilter) params.append('date', dateFilter);
        
        const url = `/crash-course/export/${format}?${params.toString()}`;
        window.open(url, '_blank');
    }
    
    function updateViewRecords(studentFilter = '', packageFilter = '', dateFilter = '') {
        const registrations = @json($registrations ?? []);
        const payments = @json($payments ?? []);
        const attendanceRecords = @json($attendanceRecords ?? []);
        const packages = @json($packages ?? []);
        
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
    
    $(document).ready(function() {
        updateViewRecords();
    });
</script>
@endsection

