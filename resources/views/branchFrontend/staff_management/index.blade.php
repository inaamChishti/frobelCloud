```html
@extends('layouts.branchDashboardApp')

<link rel="stylesheet" href="{{ asset('assets/libs/datatables/datatables.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #93c5fd;
    --secondary: #e0f2fe;
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
    padding: 1rem 2rem;
}

.registration-container .card {
    background: #fff;
    border-radius: 0.5rem;
    border: 1px solid var(--border);
    box-shadow: 0 4px 16px rgba(29, 78, 216, 0.1);
    padding: 1rem;
}

.registration-container .table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.registration-container .table thead th {
    background: var(--secondary);
    color: var(--primary-dark);
    font-weight: 600;
    font-size: 0.875rem;
    padding: 0.75rem;
    border-bottom: 2px solid var(--border);
    text-align: left;
}

.registration-container .table th.col-name,
.registration-container .table td.col-name { width: 30%; }
.registration-container .table th.col-subject,
.registration-container .table td.col-subject { width: 30%; }
.registration-container .table th.col-date,
.registration-container .table td.col-date { width: 20%; }
.registration-container .table th.col-actions,
.registration-container .table td.col-actions { width: 20%; text-align: center; }

.registration-container .table tbody td {
    vertical-align: middle;
    padding: 0.75rem;
    color: var(--text-dark);
    font-size: 0.875rem;
    font-weight: 500;
    border-top: 1px solid var(--border);
}

.registration-container .btn-primary {
    background: var(--primary);
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    color: white;
    font-weight: 500;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

.registration-container .btn-danger {
    background: var(--error);
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    color: white;
    font-weight: 500;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

.registration-container .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.registration-container .modal-content {
    border-radius: 0.5rem;
    border: 1px solid var(--border);
    box-shadow: 0 4px 16px rgba(29, 78, 216, 0.1);
}

.registration-container .modal-header {
    background: var(--secondary);
    color: var(--primary-dark);
    border-radius: 0.5rem 0.5rem 0 0;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
}

.registration-container .form-label {
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.875rem;
}

.registration-container .form-control {
    border: 1px solid var(--border);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.registration-container .error-message {
    color: var(--error);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.registration-container .btn-secondary {
    background: var(--text-light);
    border: none;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    color: white;
    font-weight: 500;
    font-size: 0.875rem;
}

.registration-container .btn-close {
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23111827'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707A1 1 0 01.293.293z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    opacity: 0.8;
}

.custom-select {
    position: relative;
    width: 100%;
}

.custom-select .select-input {
    border: 1px solid var(--border);
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
    background: white;
    min-height: 38px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.custom-select .select-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    outline: none;
}

.custom-select .selected-item {
    background: var(--primary);
    color: white;
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    display: flex;
    align-items: center;
    margin: 0.25rem 0;
}

.custom-select .remove-item {
    color: var(--error);
    margin-left: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    background: none;
    border: none;
}

.custom-select .dropdown {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    border: 1px solid var(--border);
    border-radius: 0.375rem;
    background: white;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1060;
    box-shadow: 0 4px 16px rgba(29, 78, 216, 0.1);
}

.custom-select .dropdown.active {
    display: block;
}

.custom-select .dropdown-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
}

.custom-select .dropdown-item:hover {
    background: var(--secondary);
}

.custom-select .dropdown-item.selected {
    background: var(--primary-light);
    color: var(--primary-dark);
}

.custom-select .dropdown-item.placeholder {
    background: var(--secondary);
    color: #000000;
    font-style: italic;
    cursor: default;
}

.custom-select .dropdown-item.placeholder:hover {
    background: var(--secondary);
}

.selected-subjects p {
    color: #000000;
}
</style>

@section('content')
<div class="registration-container scroll-smooth">
    <div class="container">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[var(--primary-dark)] mb-1 tracking-tight sm:text-3xl">Manage Staff</h1>
                <p class="text-base text-[var(--text-light)]">View and manage all staff members for this branch.</p>
            </div>
            <button type="button" class="btn-primary text-sm flex items-center" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fas fa-plus-circle mr-1.5"></i>Add New Staff
            </button>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="col-name">Staff Name</th>
                            <th class="col-subject">Subject</th>
                            <th class="col-date">Joining Date</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rosters as $roster)
                        <tr>
                            <td class="col-name">{{ $roster['teacher_name'] }}</td>
                            <td class="col-subject">{{ implode(', ', $roster['subject']) }}</td>
                            <td class="col-date">{{ \Carbon\Carbon::parse($roster['joining_date'])->format('d/m/Y') }}</td>
                            <td class="col-actions">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn-primary btn-sm flex items-center edit-staff-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStaffModal"
                                        data-id="{{ $roster['id'] }}"
                                        data-name="{{ $roster['teacher_name'] }}"
                                        data-subjects="{{ json_encode($roster['subject']) }}"
                                        data-date="{{ \Carbon\Carbon::parse($roster['joining_date'])->format('Y-m-d') }}">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <form action="{{ route('teacher.roster.delete', $roster['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger btn-sm flex items-center" onclick="return confirm('Are you sure you want to delete this staff member?')">
                                            <i class="fas fa-trash-alt mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Staff Modal -->
        <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('teacher.roster.store') }}" method="POST" id="addStaffForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-4">
                                <label for="staffName" class="form-label">Staff Name</label>
                                <input type="text" class="form-control" id="staffName" name="name" required>
                                <div class="error-message" id="staffNameError"></div>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="joiningDate" class="form-label" style="font-size: 14px; color: var(--text-dark); font-weight: bold;">
                                    Joining Date <span style="color: var(--error);">(required)</span>
                                </label>
                                <input type="text"   autocomplete="off"
  inputmode="none"
  onfocus="this.showPicker?.()"  class="form-control" id="joiningDate" name="joining_date" placeholder="dd/mm/yyyy" required>
                                @error('joining_date')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="subject" class="form-label">Subjects</label>
                                <div class="custom-select">
                                    <div class="select-input" tabindex="0" id="subject-input">
                                        <span class="placeholder">Select Subjects</span>
                                    </div>
                                    <input type="hidden" name="subjects[]" id="subject-hidden">
                                    <div class="dropdown" id="subject-dropdown">
                                        @foreach($subjects as $subject)
                                            <div class="dropdown-item" data-value="{{ $subject->name }}">{{ $subject->name }}</div>
                                        @endforeach
                                    </div>
                                    <div class="selected-subjects mt-2 p-2 border-2 border-[var(--border)] bg-[var(--secondary)] rounded-lg" id="selectedSubjects">
                                        <p class="text-sm text-[var(--text-light)]">Selected subjects will appear here</p>
                                    </div>
                                </div>
                                @error('subjects')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Staff Modal -->
        <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStaffModalLabel">Edit Staff Member</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('teacher.roster.update') }}" method="POST" id="editStaffForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="editStaffId" name="id">
                            <div class="mb-4">
                                <label for="editStaffName" class="form-label">Staff Name</label>
                                <input type="text" class="form-control" id="editStaffName" name="name" readonly required>
                                <div class="error-message" id="editStaffNameError"></div>
                                @error('name')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="editJoiningDate" class="form-label" style="font-size: 14px; color: var(--text-dark); font-weight: bold;">
                                    Joining Date <span style="color: var(--error);">(required)</span>
                                </label>
                                <input type="text" class="form-control" id="editJoiningDate" name="joining_date" placeholder="dd/mm/yyyy" required>
                                @error('joining_date')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="editSubject" class="form-label">Subjects</label>
                                <div class="custom-select">
                                    <div class="select-input" tabindex="0" id="edit-subject-input">
                                        <span class="placeholder">Select Subjects</span>
                                    </div>
                                    <input type="hidden" name="subjects[]" id="edit-subject-hidden">
                                    <div class="dropdown" id="edit-subject-dropdown">
                                        @foreach($subjects as $subject)
                                            <div class="dropdown-item" data-value="{{ $subject->name }}">{{ $subject->name }}</div>
                                        @endforeach
                                    </div>
                                    <div class="selected-subjects mt-2 p-2 border-2 border-[var(--border)] bg-[var(--secondary)] rounded-lg" id="editSelectedSubjects">
                                        <p class="text-sm text-[var(--text-light)]">Selected subjects will appear here</p>
                                    </div>
                                </div>
                                @error('subjects')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Staff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    // Initialize rosters data
    const rosters = {!! json_encode($rosters->map(function($roster) {
        return [
            'id' => $roster['id'],
            'teacher_name' => $roster['teacher_name'],
            'subjects' => $roster['subject'],
            'joining_date' => $roster['joining_date']
        ];
    })->toArray(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

    // Initialize custom select for Add Staff
    const $subjectInput = $('#subject-input');
    const $subjectDropdown = $('#subject-dropdown');
    const $subjectHidden = $('#subject-hidden');
    const $selectedSubjectsContainer = $('#selectedSubjects');

    // Initialize custom select for Edit Staff
    const $editSubjectInput = $('#edit-subject-input');
    const $editSubjectDropdown = $('#edit-subject-dropdown');
    const $editSubjectHidden = $('#edit-subject-hidden');
    const $editSelectedSubjectsContainer = $('#editSelectedSubjects');

    // Function to update selected items display
    function updateSelectedItems($input, $dropdown, $hiddenInput, $container) {
        const selectedValues = $hiddenInput.val().split(',').filter(v => v);
        $container.html(selectedValues.length ? selectedValues.map(value => `
            <span class="selected-item">
                ${value}
                <button type="button" class="remove-item" data-value="${value}">&times;</button>
            </span>
        `).join('') : '<p class="text-sm text-[var(--text-light)]">Selected subjects will appear here</p>');

        $('.remove-item').off('click').on('click', function() {
            const value = $(this).data('value');
            $hiddenInput.val($hiddenInput.val().split(',').filter(v => v !== value).join(','));
            updateSelectedItems($input, $dropdown, $hiddenInput, $container);
            filterDropdown($dropdown, '');
        });

        $dropdown.find('.dropdown-item').each(function() {
            $(this).toggleClass('selected', selectedValues.includes($(this).data('value')));
        });
    }

    // Function to filter dropdown
    function filterDropdown($dropdown, searchTerm) {
        $dropdown.find('.dropdown-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).css('display', text.includes(searchTerm.toLowerCase()) ? 'block' : 'none');
        });
    }

    // Event listeners for Add Staff
    $subjectInput.on('click', function() {
        $subjectDropdown.toggleClass('active');
        const $placeholder = $subjectInput.find('.placeholder');
        if ($subjectDropdown.hasClass('active')) {
            $placeholder.css('display', !$subjectHidden.val() ? 'inline' : 'none');
        } else {
            $placeholder.css('display', 'none');
        }
    });

    $subjectInput.on('input', function() {
        filterDropdown($subjectDropdown, $(this).text().trim());
    });

    $subjectDropdown.on('click', '.dropdown-item:not(.selected)', function() {
        let values = $subjectHidden.val() ? $subjectHidden.val().split(',').filter(v => v) : [];
        values.push($(this).data('value'));
        $subjectHidden.val(values.join(','));
        updateSelectedItems($subjectInput, $subjectDropdown, $subjectHidden, $selectedSubjectsContainer);
        $subjectInput.text('');
        $subjectDropdown.removeClass('active');
        $subjectInput.find('.placeholder').css('display', 'none');
    });

    $(document).on('click', function(e) {
        if (!$subjectInput.is(e.target) && !$subjectInput.has(e.target).length &&
            !$subjectDropdown.is(e.target) && !$subjectDropdown.has(e.target).length) {
            $subjectDropdown.removeClass('active');
        }
    });

    // Event listeners for Edit Staff
    $editSubjectInput.on('click', function() {
        $editSubjectDropdown.toggleClass('active');
        const $placeholder = $editSubjectInput.find('.placeholder');
        if ($editSubjectDropdown.hasClass('active')) {
            $placeholder.css('display', !$editSubjectHidden.val() ? 'inline' : 'none');
        } else {
            $placeholder.css('display', 'none');
        }
    });

    $editSubjectInput.on('input', function() {
        filterDropdown($editSubjectDropdown, $(this).text().trim());
    });

    $editSubjectDropdown.on('click', '.dropdown-item:not(.selected)', function() {
        let values = $editSubjectHidden.val() ? $editSubjectHidden.val().split(',').filter(v => v) : [];
        values.push($(this).data('value'));
        $editSubjectHidden.val(values.join(','));
        updateSelectedItems($editSubjectInput, $editSubjectDropdown, $editSubjectHidden, $editSelectedSubjectsContainer);
        $editSubjectInput.text('');
        $editSubjectDropdown.removeClass('active');
        $editSubjectInput.find('.placeholder').css('display', 'none');
    });

    $(document).on('click', function(e) {
        if (!$editSubjectInput.is(e.target) && !$editSubjectInput.has(e.target).length &&
            !$editSubjectDropdown.is(e.target) && !$editSubjectDropdown.has(e.target).length) {
            $editSubjectDropdown.removeClass('active');
        }
    });

    // Initialize Flatpickr for Add Staff modal
    flatpickr("#joiningDate", {
        dateFormat: "d/m/Y",
        allowInput: true,
        locale: {
            firstDayOfWeek: 1 // Set Monday as the first day of the week
        }
    });

    // Initialize Flatpickr for Edit Staff modal
    const editJoiningDatePicker = flatpickr("#editJoiningDate", {
        dateFormat: "d/m/Y",
        allowInput: true,
        locale: {
            firstDayOfWeek: 1 // Set Monday as the first day of the week
        }
    });

    // Populate edit modal
    $('.edit-staff-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const subjects = $(this).data('subjects');
        const date = $(this).data('date'); // Date in YYYY-MM-DD format

        $('#editStaffId').val(id);
        $('#editStaffName').val(name);
        $('#edit-subject-hidden').val(subjects.join(','));

        // Set the date in dd/mm/yyyy format for Flatpickr
        if (date) {
            const [year, month, day] = date.split('-');
            const formattedDate = `${day}/${month}/${year}`;
            editJoiningDatePicker.setDate(formattedDate, true, "d/m/Y");
        } else {
            editJoiningDatePicker.clear();
        }

        updateSelectedItems($editSubjectInput, $editSubjectDropdown, $editSubjectHidden, $editSelectedSubjectsContainer);
        $editSubjectInput.find('.placeholder').css('display', subjects.length ? 'none' : 'inline');
        $('#editStaffNameError').text('');
    });

    // Function to convert dd/mm/yyyy to yyyy-mm-dd
    function convertDateFormat(dateStr) {
        if (!dateStr) return '';
        const [day, month, year] = dateStr.split('/');
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }

    // Client-side validation for Add Staff form (includes duplicate check)
    function validateAddForm($nameInput, $hiddenInput, $errorElement) {
        const name = $nameInput.val().trim();
        const selectedSubjects = $hiddenInput.val().split(',').filter(v => v);

        if (!name) {
            $nameInput.addClass('is-invalid');
            $errorElement.text('Staff name is required.');
            return false;
        }

        const hasDuplicate = rosters.some(roster =>
            roster.teacher_name.toLowerCase() === name.toLowerCase()
        );

        if (hasDuplicate) {
            $nameInput.addClass('is-invalid');
            $errorElement.text('This staff name already exists.');
            return false;
        }

        if (selectedSubjects.length === 0) {
            $hiddenInput.closest('.custom-select').addClass('is-invalid');
            $errorElement.text('At least one subject must be selected.');
            return false;
        }

        $nameInput.removeClass('is-invalid');
        $errorElement.text('');
        $hiddenInput.closest('.custom-select').removeClass('is-invalid');
        return true;
    }

    // Client-side validation for Edit Staff form (excludes duplicate check)
    function validateEditForm($nameInput, $hiddenInput, $errorElement) {
        const name = $nameInput.val().trim();
        const selectedSubjects = $hiddenInput.val().split(',').filter(v => v);

        if (!name) {
            $nameInput.addClass('is-invalid');
            $errorElement.text('Staff name is required.');
            return false;
        }

        if (selectedSubjects.length === 0) {
            $hiddenInput.closest('.custom-select').addClass('is-invalid');
            $errorElement.text('At least one subject must be selected.');
            return false;
        }

        $nameInput.removeClass('is-invalid');
        $errorElement.text('');
        $hiddenInput.closest('.custom-select').removeClass('is-invalid');
        return true;
    }

    // Add staff form validation and date conversion
    $('#addStaffForm').on('submit', function(e) {
        if (!validateAddForm($('#staffName'), $subjectHidden, $('#staffNameError'))) {
            e.preventDefault();
            Toastify({
                text: "Please fix the errors before submitting.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444",
                stopOnFocus: true,
            }).showToast();
            return;
        }
        // Convert date format before submission
        const joiningDate = $('#joiningDate').val();
        if (joiningDate) {
            $('#joiningDate').val(convertDateFormat(joiningDate));
        }
    });

    // Edit staff form validation and date conversion
    $('#editStaffForm').on('submit', function(e) {
        if (!validateEditForm($('#editStaffName'), $editSubjectHidden, $('#editStaffNameError'))) {
            e.preventDefault();
            Toastify({
                text: "Please fix the errors before submitting.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ef4444",
                stopOnFocus: true,
            }).showToast();
            return;
        }
        // Convert date format before submission
        const joiningDate = $('#editJoiningDate').val();
        if (joiningDate) {
            $('#editJoiningDate').val(convertDateFormat(joiningDate));
        }
    });

    // Show session messages
    @if (session('success'))
        Toastify({
            text: "{{ session('success') }}",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#10b981",
            stopOnFocus: true,
        }).showToast();
    @endif
    @if (session('error'))
        Toastify({
            text: "{{ session('error') }}",
            duration: 5000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ef4444",
            stopOnFocus: true,
        }).showToast();
    @endif
});
</script>
@endsection
```
