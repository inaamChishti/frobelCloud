{{-- resources/views/branchFrontend/IAG/reportLearner.blade.php --}}
@extends('layouts.branchDashboardApp')

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">

        {{-- ====================== HEADER ====================== --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-[var(--primary-dark)] mb-3 tracking-tight sm:text-4xl">
                IAG Learner Alumni Report
            </h1>
        </div>

        {{-- ====================== FILTER & TABLE ====================== --}}
        <div id="learner-table" class="card form-section">
            <div class="flex items-center mb-4">
                <div class="bg-[var(--primary)] p-2 rounded-lg mr-3 section-icon">
                    <i class="fas fa-list h-5 w-5 text-white text-lg"></i>
                </div>
                <h2 class="text-xl font-semibold text-[var(--primary-dark)] sm:text-2xl">
                    Learner Progress
                </h2>
            </div>

            {{-- Filter - Only Year in School (Status filter removed) --}}
            <div class="mb-4 grid grid-cols-1 md:grid-cols-1 gap-4">
                <div>
                    <label for="year_filter" class="block text-sm font-medium text-[var(--text-dark)] mb-1">
                        Filter by Year in School
                    </label>
                    <div class="relative">
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                        <select id="year_filter" class="input-field pl-10 w-full" onchange="applyFilters()">
                            <option value="all">All Years</option>
                            <option value="10">Year 10</option>
                            <option value="11">Year 11</option>
                            <option value="12">Year 12</option>
                            <option value="13">Year 13</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover w-full" id="learnerTable">
                    <thead>
                        <tr>
                            <th class="col-id">Family ID</th>
                            <th class="col-name">Learner Name</th>
                            <th class="col-name">Destination</th>
                            <th class="col-id">Type</th>
                            <th class="col-id">Year in School</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">

                        {{-- GREEN ROWS: Both Terms (still green background) --}}
                        @foreach ($greenRows as $row)
                            <tr class="status-both" data-year="{{ $row->student_year_in_school }}" style="background:#d4edda; color:#155724;">
                                <td class="col-id">{{ $row->family_id }}</td>
                                <td class="col-name">{{ $row->full_student_name ?? $row->learner_name }}
                                @if (!empty($row->is_flag) && $row->is_flag == 1)
                                    <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>
                                @endif
                                </td>

                                {{-- Destination Input --}}
                                <td class="col-name">
                                    <div class="relative">
                                        <i class="fas fa-map-marker-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                        <input type="text"
                                               class="input-field pl-10 destination-input w-full"
                                               value="{{ $row->destination ?? '' }}"
                                               maxlength="40"
                                               placeholder="Enter Destination"
                                               data-id="{{ $row->id }}">
                                        <div class="error-message">Max 40 chars.</div>
                                    </div>
                                </td>
                                <td class="col-id">{{ $row->category ?? '—' }}</td>
                                <td class="col-id">{{ $row->student_year_in_school }}</td>

                                {{-- Actions --}}
                                <td class="col-actions" style="zoom:0.7;">
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                                class="btn btn-sm btn-primary open-email-modal"
                                                data-id="{{ $row->id }}"
                                                data-family-id="{{ $row->family_id }}"
                                                data-learner-name="{{ $row->full_student_name ?? $row->learner_name }}"
                                                data-guardian-email="{{ $row->guardian_email ?? '' }}">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <a href="{{ url('learner-view/' . $row->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('learner-edit/' . $row->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $row->id }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        {{-- ORANGE ROWS: One Term (still orange background) --}}
                        @foreach ($orangeRows as $row)
                            <tr class="status-single" data-year="{{ $row->student_year_in_school }}" style="background:#fff3cd; color:#856404;">
                                <td class="col-id">{{ $row->family_id }}</td>
                                <td class="col-name">{{ $row->full_student_name ?? $row->learner_name }}
                                @if (!empty($row->is_flag) && $row->is_flag == 1)
                                    <span title="Flagged Student" style="color:red;font-size:14px;margin-left:3px;">&#x1F6A9;</span>
                                @endif
                                </td>

                                {{-- Destination Input --}}
                                <td class="col-name">
                                    <div class="relative">
                                        <i class="fas fa-map-marker-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-[var(--text-light)] text-sm"></i>
                                        <input type="text"
                                               class="input-field pl-10 destination-input w-full"
                                               value="{{ $row->destination ?? '' }}"
                                               maxlength="40"
                                               placeholder="Enter Destination"
                                               data-id="{{ $row->id }}">
                                        <div class="error-message">Max 40 chars.</div>
                                    </div>
                                </td>
                                <td class="col-id">{{ $row->category ?? '—' }}</td>
                                <td class="col-id">{{ $row->student_year_in_school }}</td>

                                {{-- Actions --}}
                                <td class="col-actions" style="zoom:0.7;">
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                                class="btn btn-sm btn-primary open-email-modal"
                                                data-id="{{ $row->id }}"
                                                data-family-id="{{ $row->family_id }}"
                                                data-learner-name="{{ $row->full_student_name ?? $row->learner_name }}"
                                                data-guardian-email="{{ $row->guardian_email ?? '' }}">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <a href="{{ url('learner-view/' . $row->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('learner-edit/' . $row->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $row->id }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        {{-- ====================== EMAIL MODAL ====================== --}}
        <div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="emailForm" action="{{ url('send-learner-email') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="modal-id" name="id">
                            <div class="mb-3">
                                <label class="form-label">Family ID</label>
                                <input type="text" id="modal-family-id" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Learner Name</label>
                                <input type="text" id="modal-learner-name" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Guardian Email</label>
                                <input type="email" id="modal-guardian-email" name="guardian_email" class="form-control" required>
                                <div class="text-danger small mt-1" id="email-error" style="display:none;">Invalid email.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send Email</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====================== SCRIPTS ====================== --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    // Apply Year filter + Sort: Green (Both Terms) first → Year → Name
    function applyFilters() {
        const yearVal = $('#year_filter').val();
        const $rows   = $('#tableBody tr');

        // Show/Hide based on Year filter only
        $rows.each(function() {
            const $row    = $(this);
            const rowYear = $row.data('year') + '';

            if (yearVal === 'all' || rowYear === yearVal) {
                $row.show();
            } else {
                $row.hide();
            }
        });

        // Sort visible rows: Green first → Year → Name
        const visibleRows = $rows.filter(':visible').sort(function(a, b) {
            // 1. Both Terms (green) first
            const isBothA = $(a).hasClass('status-both');
            const isBothB = $(b).hasClass('status-both');
            if (isBothA && !isBothB) return -1;
            if (!isBothA && isBothB) return 1;

            // 2. Then by Year
            const yearA = $(a).data('year');
            const yearB = $(b).data('year');
            if (yearA !== yearB) return yearA - yearB;

            // 3. Then by Name (now in column index 1)
            const nameA = $(a).find('td:eq(1)').text().trim().toLowerCase();
            const nameB = $(b).find('td:eq(1)').text().trim().toLowerCase();
            return nameA.localeCompare(nameB);
        });

        $('#tableBody').append(visibleRows);
    }

    // Run on load
    $(document).ready(function() {
        applyFilters();
    });

    // Destination Save on Enter
    $(document).on('keypress', '.destination-input', function(e) {
        if (e.which === 13) {
            const input = $(this);
            const dest = input.val().trim();
            const id = input.data('id');
            const errorMsg = input.parent().find('.error-message');

            if (dest.length > 40) {
                errorMsg.text('Destination cannot exceed 40 characters.').show();
                return;
            }
            errorMsg.hide();

            Toastify({
                text: "Saving...",
                duration: -1,
                backgroundColor: "#2563eb"
            }).showToast();

            $.get("{{ url('store-destination') }}", {
                id: id,
                destination: dest,
                _token: '{{ csrf_token() }}'
            }, function() {
                Toastify({
                    text: "Saved!",
                    backgroundColor: "#10b981",
                    duration: 3000
                }).showToast();
            }).fail(function(xhr) {
                const msg = (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : "Error saving destination!";
                Toastify({
                    text: msg,
                    backgroundColor: "#ef4444",
                    duration: 6000
                }).showToast();
            });
        }
    });

    // Open Email Modal
    $(document).on('click', '.open-email-modal', function() {
        $('#modal-id').val($(this).data('id'));
        $('#modal-family-id').val($(this).data('family-id'));
        $('#modal-learner-name').val($(this).data('learner-name'));
        $('#modal-guardian-email').val($(this).data('guardian-email'));
        $('#emailModal').modal('show');
    });

    // Email Form Submit
    $('#emailForm').on('submit', function(e) {
        e.preventDefault();
        const email = $('#modal-guardian-email').val().trim();
        const error = $('#email-error');

        if (!/^\S+@\S+\.\S+$/.test(email)) {
            error.show();
            return;
        }
        error.hide();

        Toastify({ text: "Sending...", duration: -1, backgroundColor: "#2563eb" }).showToast();

        $.ajax({
            url: this.action,
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                Toastify({ text: "Email sent!", backgroundColor: "#10b981", duration: 3000 }).showToast();
                $('#emailModal').modal('hide');
            },
            error: function() {
                Toastify({ text: "Failed!", backgroundColor: "#ef4444", duration: 5000 }).showToast();
            }
        });
    });

    // Delete Confirm
    function confirmDelete(id) {
        if (confirm('Delete this record?')) {
            window.location.href = "{{ url('learner-delete') }}/" + id;
        }
    }
</script>

{{-- ====================== STYLES ====================== --}}
<style>
    .registration-container { --primary:#2563eb; --primary-dark:#1e40af; --primary-light:#93c5fd;
        --secondary:#e0f2fe; --text-dark:#111827; --text-light:#6b7280; --border:#bfdbfe; --error:#ef4444; }
    .registration-container .container { max-width:1200px; margin:auto; padding:1rem 3rem; }
    @media (max-width:768px){ .registration-container .container { padding:.75rem 2rem; } }
    .card { background:#fff; border-radius:.75rem; border:1px solid var(--border);
        box-shadow:0 8px 24px rgba(29,78,216,.1); padding:1.25rem 1.5rem; position:relative; }
    .card::before { content:''; position:absolute; top:0; left:0; width:100%; height:3px;
        background:linear-gradient(90deg,var(--primary),var(--primary-light)); }
    .card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(29,78,216,.15); }
    .input-field { border:1px solid var(--border); border-radius:.5rem; padding:.5rem .8rem .5rem 2.5rem;
        background:#fff; height:2.5rem; width:100%; font-size:.875rem; }
    .input-field:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.1); outline:none; }
    .table-responsive { overflow-x:auto; }
    .table { width:100%; border-collapse:collapse; background:#fff; border:1px solid var(--border); border-radius:.5rem; }
    .table thead th { background:var(--secondary); color:var(--text-dark); border-bottom:2px solid var(--border);
        padding:.75rem; text-align:left; font-weight:600; }
    .table tbody td { padding:.75rem; color:var(--text-dark); font-size:.875rem; font-weight:500;
        border-bottom:1px solid var(--border); }
    .table tbody tr:hover { background:var(--secondary); }
    .table th.col-id, .table td.col-id { width:10%; text-align:center; }
    .table th.col-name, .table td.col-name { width:22%; }
    .table th.col-actions, .table td.col-actions { width:18%; text-align:center; }
    .btn-group .btn { margin:0 2px; }
    .error-message { color:var(--error); font-size:.7rem; margin-top:.25rem; display:none; }
    @media (max-width:768px){
        .table thead th, .table tbody td { font-size:.75rem; padding:.5rem; }
        .table th.col-id, .table td.col-id { width:12%; }
        .table th.col-name, .table td.col-name { width:20%; }
    }
</style>
@endsection
