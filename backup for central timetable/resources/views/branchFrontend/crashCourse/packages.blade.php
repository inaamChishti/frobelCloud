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
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        color: #000;
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
        <i class="fas fa-box"></i> Package Management
    </h1>
    
    <!-- Package Management Section -->
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
    
    // Function to add package row to table
    function addPackageRow(pkg) {
        const tbody = $('#packagesTableBody');
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
                        updatePackageRow(response.package);
                        showToast(response.message, 'success');
                    } else {
                        addPackageRow(response.package);
                        showToast(response.message, 'success');
                    }
                    resetPackageForm();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                enableSubmitButton('packageSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
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
                            $('#package_row_' + id).remove();
                            if ($('#packagesTableBody tr').length === 0) {
                                $('#packagesTableBody').html('<tr><td colspan="4" class="empty-state"><i class="fas fa-box-open"></i><p>No packages created yet. Create your first package above.</p></td></tr>');
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
</script>
@endsection

