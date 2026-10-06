@extends('layouts.branchDashboardApp')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .main-content {
        background: #ffffff;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #20439F;
        margin: 20px;
    }

    .main-content h1 {
        font-size: 28px;
        font-weight: 700;
        color: #20439F;
        margin-bottom: 12px;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #1F3F97;
    }

    .sticker {
        background-color: #1F3F97;
        color: #fff;
        font-weight: bold;
        border-radius: 0.25rem 0 0 0.25rem;
    }

    .card {
        border: 1px solid #1F3F97;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(103, 192, 234, 0.2);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #1F3F97;
        color: white;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #1F3F97;
        border-color: #1F3F97;
    }

    .btn-primary:hover {
        background-color: #4ba8d2;
        border-color: #4ba8d2;
    }

    .status-badge-blocked {
        background-color: #dc3545;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge-active {
        background-color: #28a745;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-container {
        width: 100% !important;
    }

    #block-reason-group {
        display: none;
    }

    .family-info-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 14px 18px;
        margin-top: 12px;
    }

    .family-info-box.blocked {
        background: #fff5f5;
        border-color: #f5c6cb;
    }

    .action-section {
        margin-top: 20px;
    }
</style>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-primary">Family Block Management</h1>
            <nav aria-label="breadcrumb">
                
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <!-- Main Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-search me-2"></i> Search & Manage Family ID
                </div>
                <div class="card-body">

                    <!-- Step 1: Select Family ID -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color:#1F3F97;">
                            <i class="fas fa-users me-1"></i> Select Family ID
                        </label>
                        <select id="family-id-select" class="form-control">
                            <option value="">-- Type or select a Family ID --</option>
                            @foreach($admissions as $admission)
                                <option
                                    value="{{ $admission->admissionid }}"
                                    data-familyno="{{ $admission->familyno }}"
                                    data-blocked="{{ $admission->is_blocked }}"
                                    data-reason="{{ $admission->block_reason }}">
                                    Family ID: {{ $admission->familyno }}
                                    @if($admission->is_blocked) 🔴 (Blocked) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Family Info + Action Panel (hidden until selection) -->
                    <div id="family-panel" style="display:none;">

                        <!-- Info Box -->
                        <div class="family-info-box" id="info-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Family ID:</strong> <span id="info-familyno"></span></p>
                                    <p class="mb-0"><strong>Status:</strong> <span id="info-status"></span></p>
                                </div>
                                <div class="col-md-6" id="reason-display-col" style="display:none;">
                                    <p class="mb-0"><strong>Block Reason:</strong><br>
                                        <span id="info-reason" class="text-danger"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Section -->
                        <div class="action-section">

                            <!-- Block Reason Input (shown only when blocking) -->
                            <div id="block-reason-group" class="mb-3">
                                <label class="form-label fw-bold" style="color:#1F3F97;">
                                    <i class="fas fa-comment-alt me-1"></i> Block Reason
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    id="block-reason-input"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Enter reason for blocking this family ID..."
                                    maxlength="500"></textarea>
                                <small class="text-muted">This reason will be shown to staff when they try to access this family.</small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 mt-3">
                                <button id="btn-block" class="btn btn-danger" style="display:none;">
                                    <i class="fas fa-ban me-2"></i> Block Family
                                </button>
                                <button id="btn-unblock" class="btn btn-success" style="display:none;">
                                    <i class="fas fa-unlock me-2"></i> Unblock Family
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Blocked Families Summary Card -->
            <div class="card mt-2">
                <div class="card-header">
                    <i class="fas fa-list me-2"></i> Currently Blocked Families
                </div>
                <div class="card-body p-0">
                    @php
                        $blockedFamilies = $admissions->where('is_blocked', 1);
                    @endphp
                    @if($blockedFamilies->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success" style="font-size:30px;"></i>
                            <p class="mt-2">No families are currently blocked.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="blocked-table">
                                <thead style="background:#1F3F97; color:white;">
                                    <tr>
                                        <th>Family ID</th>
                                        <th>Block Reason</th>
                                        <th>Status</th>
                                        <th>Quick Unblock</th>
                                    </tr>
                                </thead>
                                <tbody id="blocked-table-body">
                                    @foreach($blockedFamilies as $b)
                                    <tr id="row-{{ $b->admissionid }}">
                                        <td><strong>{{ $b->familyno }}</strong></td>
                                        <td>{{ $b->block_reason ?? '—' }}</td>
                                        <td><span class="status-badge-blocked">Blocked</span></td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-success quick-unblock-btn"
                                                data-id="{{ $b->admissionid }}"
                                                data-familyno="{{ $b->familyno }}">
                                                <i class="fas fa-unlock me-1"></i> Unblock
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    // Toastr options
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000,
    };

    // Initialize Select2 with search
    $('#family-id-select').select2({
        placeholder: '-- Type or select a Family ID --',
        allowClear: true,
    });

    // On family selection
    $('#family-id-select').on('change', function () {
        renderPanel();
    });

    function renderPanel() {
        const selected    = $('#family-id-select').find(':selected');
        const admissionId = selected.val();

        if (!admissionId) {
            $('#family-panel').hide();
            $('#block-reason-input').val('');
            return;
        }

        const familyNo  = selected.data('familyno');
        const isBlocked = parseInt(selected.data('blocked'));
        const reason    = selected.data('reason') || '';

        $('#info-familyno').text(familyNo);
        $('#family-panel').show();

        if (isBlocked) {
            $('#info-box').addClass('blocked');
            $('#info-status').html('<span class="status-badge-blocked">Blocked</span>');
            $('#reason-display-col').show();
            $('#info-reason').text(reason || 'No reason provided');
            $('#btn-block').hide();
            $('#btn-unblock').show();
            $('#block-reason-group').hide();
            $('#block-reason-input').val('');
        } else {
            $('#info-box').removeClass('blocked');
            $('#info-status').html('<span class="status-badge-active">Active</span>');
            $('#reason-display-col').hide();
            $('#block-reason-input').val('');
            $('#btn-block').show();
            $('#btn-unblock').hide();
            $('#block-reason-group').show();
        }
    }

    // BLOCK button click
    $('#btn-block').on('click', function () {
        const selected    = $('#family-id-select').find(':selected');
        const admissionId = selected.val();
        const familyNo    = selected.data('familyno');
        const reason      = $('#block-reason-input').val().trim();

        if (!reason) {
            toastr.warning('Please enter a block reason before proceeding.');
            return;
        }

        Swal.fire({
            title: 'Block Family ID ' + familyNo + '?',
            text: 'All actions for this family will be restricted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Block',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                setButtonLoading('#btn-block', true);
                performToggle(admissionId, 'block', reason, familyNo, '#btn-block');
            }
        });
    });

    // UNBLOCK button click
    $('#btn-unblock').on('click', function () {
        const selected    = $('#family-id-select').find(':selected');
        const admissionId = selected.val();
        const familyNo    = selected.data('familyno');

        Swal.fire({
            title: 'Unblock Family ID ' + familyNo + '?',
            text: 'This family will regain full access.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Unblock',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                setButtonLoading('#btn-unblock', true);
                performToggle(admissionId, 'unblock', '', familyNo, '#btn-unblock');
            }
        });
    });

    // Quick unblock from blocked table
    $(document).on('click', '.quick-unblock-btn', function () {
        const admissionId = $(this).data('id');
        const familyNo    = $(this).data('familyno');
        const $btn        = $(this);

        Swal.fire({
            title: 'Unblock Family ID ' + familyNo + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Unblock',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // Loading on this specific button
                $btn.prop('disabled', true).data('orig', $btn.html())
                    .html('<span class="spinner-border spinner-border-sm"></span> Please wait...');
                performToggle(admissionId, 'unblock', '', familyNo, null, $btn);
            }
        });
    });

    // Button loading helper (for #btn-block / #btn-unblock)
    function setButtonLoading(selector, isLoading) {
        const btn = $(selector);
        if (isLoading) {
            btn.prop('disabled', true).data('orig', btn.html())
               .html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Please wait...');
        } else {
            btn.prop('disabled', false).html(btn.data('orig'));
        }
    }

    // Core AJAX — no page reload, update DOM directly
    function performToggle(admissionId, action, reason, familyNo, btnSelector, $quickBtn) {
        $.ajax({
            url: '{{ route("family.block.toggle") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                admission_id: admissionId,
                action: action,
                block_reason: reason,
            },
            success: function (res) {

                // 1. Toast
                if (action === 'block') {
                    toastr.error(res.success);
                } else {
                    toastr.success(res.success);
                }

                // 2. Update the dropdown option's data attributes
                const $option = $('#family-id-select option[value="' + admissionId + '"]');
                if (action === 'block') {
                    $option.data('blocked', 1).data('reason', reason);
                    $option.text('Family ID: ' + familyNo + ' 🔴 (Blocked)');
                } else {
                    $option.data('blocked', 0).data('reason', '');
                    $option.text('Family ID: ' + familyNo);
                }

                // 3. Re-render the info panel
                if (btnSelector) {
                    setButtonLoading(btnSelector, false);
                }
                renderPanel();

                // 4. Update blocked families table
                if (action === 'block') {
                    // Add row to table (remove empty state if present)
                    $('#blocked-table-body').closest('.card-body').find('.text-center.py-4').remove();

                    // Make sure table exists
                    if ($('#blocked-table').length === 0) {
                        const tableHtml = `
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="blocked-table">
                                    <thead style="background:#1F3F97; color:white;">
                                        <tr>
                                            <th>Family ID</th>
                                            <th>Block Reason</th>
                                            <th>Status</th>
                                            <th>Quick Unblock</th>
                                        </tr>
                                    </thead>
                                    <tbody id="blocked-table-body"></tbody>
                                </table>
                            </div>`;
                        $('#blocked-table-body').closest('.card-body').html(tableHtml);
                    }

                    const escapedReason = $('<div>').text(reason).html();
                    $('#blocked-table-body').prepend(`
                        <tr id="row-${admissionId}">
                            <td><strong>${familyNo}</strong></td>
                            <td>${escapedReason || '—'}</td>
                            <td><span class="status-badge-blocked">Blocked</span></td>
                            <td>
                                <button class="btn btn-sm btn-success quick-unblock-btn"
                                    data-id="${admissionId}"
                                    data-familyno="${familyNo}">
                                    <i class="fas fa-unlock me-1"></i> Unblock
                                </button>
                            </td>
                        </tr>`);

                } else {
                    // Remove row from table
                    if ($quickBtn) {
                        $quickBtn.prop('disabled', false).html('<i class="fas fa-unlock me-1"></i> Unblock');
                    }
                    $('#row-' + admissionId).fadeOut(300, function () {
                        $(this).remove();
                        // If table is now empty, show empty state
                        if ($('#blocked-table-body tr').length === 0) {
                            $('#blocked-table').closest('.table-responsive').replaceWith(`
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success" style="font-size:30px;"></i>
                                    <p class="mt-2">No families are currently blocked.</p>
                                </div>`);
                        }
                    });
                }
            },
            error: function (xhr) {
                if (btnSelector) setButtonLoading(btnSelector, false);
                if ($quickBtn) {
                    $quickBtn.prop('disabled', false).html('<i class="fas fa-unlock me-1"></i> Unblock');
                }
                toastr.error(xhr.responseJSON?.error || 'Something went wrong. Please try again.');
            }
        });
    }

});
</script>
@endsection
