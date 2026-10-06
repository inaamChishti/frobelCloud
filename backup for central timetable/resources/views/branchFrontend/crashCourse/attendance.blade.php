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
        <i class="fas fa-calendar-check"></i> Attendance Management
    </h1>
    
    <!-- Attendance Management Section -->
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
                            <option value="">-- Select a Candidate --</option>
                            @if(isset($paid_candidates) && $paid_candidates->count() > 0)
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
                            @else
                                <option value="" disabled>No candidates found. Please add payment first.</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Subject *</label>
                        <select class="form-control" id="attendance_subject" name="subject" required>
                            <option value="">-- Select Subject --</option>
                            @if(isset($subjects) && $subjects->count() > 0)
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject }}">{{ $subject }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Teacher *</label>
                        <select class="form-control" id="attendance_teacher" name="teacher" required>
                            <option value="">-- Select Teacher --</option>
                            @if(isset($teachers) && $teachers->count() > 0)
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher }}">{{ $teacher }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Time Slot *</label>
                        <select class="form-control" id="attendance_timeslot" name="timeslot" required>
                            <option value="">-- Select Time Slot --</option>
                            <optgroup label="Weekday Slots">
                                <option value="Lesson 1 – 11:00 - 01:00">Lesson 1 – 11:00 - 01:00</option>
                                <option value="Lesson 2 – 01:30 - 03:30">Lesson 2 – 01:30 - 03:30</option>
                                <option value="Lesson 3 – 04:30 - 06:30">Lesson 3 – 04:30 - 06:30</option>
                                <option value="Lesson 4 – 06:45 - 08:45">Lesson 4 – 06:45 - 08:45</option>
                            </optgroup>
                            <optgroup label="Weekend Slots">
                                <option value="Lesson 1 – 09:00 - 11:00">Lesson 1 – 09:00 - 11:00</option>
                                <option value="Lesson 2 – 11:20 - 01:20">Lesson 2 – 11:20 - 01:20</option>
                                <option value="Lesson 3 – 02:00 - 04:00">Lesson 3 – 02:00 - 04:00</option>
                            </optgroup>
                        </select>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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
    
    // Handle subject change to load teachers
    $('#attendance_subject').on('change', function() {
        const subject = $(this).val();
        const teacherSelect = $('#attendance_teacher');
        
        if (!subject) {
            teacherSelect.html('<option value="">-- Select Teacher --</option>').trigger('change');
            return;
        }
        
        // Show loading state
        teacherSelect.prop('disabled', true).html('<option value="">Loading teachers...</option>').trigger('change');
        
        // Fetch teachers for selected subject
        $.ajax({
            url: '/getTeacherName/' + encodeURIComponent(subject),
            method: 'GET',
            success: function(response) {
                teacherSelect.html('<option value="">-- Select Teacher --</option>');
                if (Array.isArray(response) && response.length > 0) {
                    response.forEach(function(teacher) {
                        teacherSelect.append('<option value="' + teacher + '">' + teacher + '</option>');
                    });
                } else {
                    // If no teachers found for subject, show all teachers
                    @if(isset($teachers) && $teachers->count() > 0)
                        @foreach($teachers as $teacher)
                            teacherSelect.append('<option value="{{ $teacher }}">{{ $teacher }}</option>');
                        @endforeach
                    @endif
                }
                teacherSelect.prop('disabled', false).trigger('change');
            },
            error: function() {
                // Fallback to all teachers on error
                teacherSelect.html('<option value="">-- Select Teacher --</option>');
                @if(isset($teachers) && $teachers->count() > 0)
                    @foreach($teachers as $teacher)
                        teacherSelect.append('<option value="{{ $teacher }}">{{ $teacher }}</option>');
                    @endforeach
                @endif
                teacherSelect.prop('disabled', false).trigger('change');
            }
        });
    });
    
    // Handle custom time slot
    $('#attendance_timeslot').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#custom_timeslot').show().focus();
        } else {
            $('#custom_timeslot').hide().val('');
        }
    });
    
    $('#attendanceForm').on('submit', function(e) {
        e.preventDefault();
        const candidateValue = $('#attendance_candidate_id').val();
        
        if (!candidateValue) {
            showToast('Please select a candidate', 'error');
            return;
        }
        
        // Get time slot (use custom if selected)
        let timeslot = $('#attendance_timeslot').val();
        if (timeslot === 'custom') {
            timeslot = $('#custom_timeslot').val();
            if (!timeslot) {
                showToast('Please enter a custom time slot', 'error');
                return;
            }
        }
        
        disableSubmitButton('attendanceSubmitBtn');
        
        const formData = {
            candidate_value: candidateValue,
            subject: $('#attendance_subject').val(),
            teacher: $('#attendance_teacher').val(),
            timeslot: timeslot,
            _token: $('input[name="_token"]').val()
        };
        
        $.ajax({
            url: '/crash-course/attendance/store',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    addAttendanceRow(response.attendance);
                    showToast(response.message, 'success');
                    
                    // If custom time slot was used, add it to dropdown
                    const customSlot = $('#custom_timeslot').val();
                    if (customSlot) {
                        const timeslotSelect = $('#attendance_timeslot');
                        // Check if it doesn't already exist
                        if (timeslotSelect.find('option[value="' + customSlot + '"]').length === 0) {
                            // Remove the custom option temporarily
                            timeslotSelect.find('option[value="custom"]').remove();
                            // Add the new time slot
                            timeslotSelect.append('<option value="' + customSlot + '">' + customSlot + '</option>');
                            // Re-add custom option
                            timeslotSelect.append('<option value="custom">+ Add Custom Time Slot</option>');
                        }
                    }
                    
                    resetAttendanceForm();
                } else {
                    showToast(response.message || 'Something went wrong', 'error');
                }
                enableSubmitButton('attendanceSubmitBtn');
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong', 'error');
                enableSubmitButton('attendanceSubmitBtn');
            }
        });
    });
    
    function resetAttendanceForm() {
        $('#attendanceForm')[0].reset();
        $('#attendance_id').val('');
        $('#attendanceCancelBtn').hide();
        $('#custom_timeslot').hide().val('');
        
        // Reset Select2 dropdowns
        $('#attendance_candidate_id').val(null).trigger('change');
        $('#attendance_subject').val(null).trigger('change');
        $('#attendance_teacher').html('<option value="">-- Select Teacher --</option>');
        @if(isset($teachers) && $teachers->count() > 0)
            @foreach($teachers as $teacher)
                $('#attendance_teacher').append('<option value="{{ $teacher }}">{{ $teacher }}</option>');
            @endforeach
        @endif
        $('#attendance_teacher').val(null).trigger('change');
        $('#attendance_timeslot').val(null).trigger('change');
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
    
    $(document).ready(function() {
        // Initialize Select2 for candidate dropdown
        $('#attendance_candidate_id').select2({
            placeholder: "-- Select a Student --",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for subject dropdown
        $('#attendance_subject').select2({
            placeholder: "-- Select Subject --",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for teacher dropdown
        $('#attendance_teacher').select2({
            placeholder: "-- Select Teacher --",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for time slot dropdown
        $('#attendance_timeslot').select2({
            placeholder: "-- Select Time Slot --",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection

