@extends('layouts.branchDashboardApp')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white text-center py-3" style="background-color: #2048AC;">
            <h2 class="mb-0 fw-bold" style="font-size: 1.8rem;">Lesson Tracking Report</h2>
            <p class="mb-0 mt-2" style="font-size: 0.9rem;">Track lessons from first date in General Timetable to today</p>
        </div>
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-4">
                    <label for="family_id" class="form-label fw-semibold">Enter Family ID</label>
                    <input type="text" class="form-control" id="family_id" name="family_id" placeholder="Enter Family ID">
                </div>
                <div class="col-md-4">
                    <label for="student_select" class="form-label fw-semibold">Select Student</label>
                    <select class="form-select" id="student_select" name="student_select" disabled>
                        <option value="">First select Family ID</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="search-btn" class="btn btn-primary w-100" style="background-color: #2048AC; border-color: #2048AC;" disabled>
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="loading" class="text-center" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading report data...</p>
            </div>

            <!-- Error message -->
            <div id="error-message" class="alert alert-danger" style="display: none;"></div>

            <!-- Report container -->
            <div id="report-container" style="display: none;">
                <!-- Report will be inserted here -->
            </div>
        </div>
    </div>
</div>

<style>
    .report-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        background-color: #f8f9fa;
    }
    .student-header {
        background-color: #2048AC;
        color: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .info-box {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .info-box h5 {
        color: #2048AC;
        margin-bottom: 10px;
    }
    .week-row {
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 12px;
        margin-bottom: 8px;
    }
    .week-row:hover {
        background-color: #f0f0f0;
    }
    .subjects-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .subjects-list li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .subjects-list li:last-child {
        border-bottom: none;
    }
    .badge-custom {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .quota-change-stamp {
        position: relative;
        display: inline-block;
        margin: 10px 10px 10px 0;
        padding: 0;
    }
    .quota-change-stamp:hover {
        transform: rotate(0deg) scale(1.05) !important;
    }
    .quota-change-stamp > div:hover {
        transform: rotate(0deg) !important;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5) !important;
    }
    .quota-badge-mini {
        transition: all 0.3s ease;
    }
    .quota-badge-mini:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('search-btn');
        const familyIdInput = document.getElementById('family_id');
        const studentSelect = document.getElementById('student_select');
        const loadingDiv = document.getElementById('loading');
        const errorMessage = document.getElementById('error-message');
        const reportContainer = document.getElementById('report-container');

        let allReportData = []; // Store all students data

        // Fetch students when family ID is entered
        familyIdInput.addEventListener('blur', function() {
            const familyId = familyIdInput.value.trim();
            if (!familyId) {
                studentSelect.innerHTML = '<option value="">First select Family ID</option>';
                studentSelect.disabled = true;
                searchBtn.disabled = true;
                return;
            }
            fetchStudents(familyId);
        });

        // Search on button click
        searchBtn.addEventListener('click', function() {
            const familyId = familyIdInput.value.trim();
            const selectedStudent = studentSelect.value;
            if (!familyId || !selectedStudent) {
                alert('Please enter Family ID and select a student');
                return;
            }
            fetchReportData(familyId);
        });

        // Search on Enter key in family ID
        familyIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur(); // Trigger blur to fetch students
            }
        });

        // Enable search when student is selected
        studentSelect.addEventListener('change', function() {
            searchBtn.disabled = !this.value;
        });

        function fetchStudents(familyId) {
            studentSelect.disabled = true;
            studentSelect.innerHTML = '<option value="">Loading students...</option>';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value || '';

            fetch('{{ route("get.students.by.family") }}?family_id=' + familyId, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(({ status, data }) => {
                // Block check
                if (status === 403 && data.blocked) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Family ID Blocked. Please contact admin office for more details.');
                    } else {
                        alert('Family ID Blocked. Please contact admin office for more details.');
                    }
                    familyIdInput.value = '';
                    studentSelect.innerHTML = '<option value="">First select Family ID</option>';
                    studentSelect.disabled = true;
                    searchBtn.disabled = true;
                    return;
                }

                studentSelect.innerHTML = '<option value="">Select Student</option>';
                
                if (data.students && data.students.length > 0) {
                    data.students.forEach(student => {
                        const studentName = student.full_name || student.name || (student.studentname + ' ' + (student.studentsur || '')).trim();
                        if (studentName && studentName !== 'Name Not Available') {
                            const option = document.createElement('option');
                            option.value = studentName.trim();
                            option.textContent = studentName.trim();
                            studentSelect.appendChild(option);
                        }
                    });
                    studentSelect.disabled = false;
                } else {
                    studentSelect.innerHTML = '<option value="">No students found</option>';
                    studentSelect.disabled = true;
                    searchBtn.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                studentSelect.innerHTML = '<option value="">Error loading students</option>';
                studentSelect.disabled = true;
                searchBtn.disabled = true;
            });
        }

        function fetchReportData(familyId) {
            // Show loading, hide error and report
            loadingDiv.style.display = 'block';
            errorMessage.style.display = 'none';
            reportContainer.style.display = 'none';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value || '';

            fetch('{{ route("get.lesson.tracking.data") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    family_id: familyId
                })
            })
            .then(response => {
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(({ status, data }) => {
                loadingDiv.style.display = 'none';

                // Block check
                if (status === 403 && data.blocked) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Family ID Blocked. Please contact admin office for more details.');
                    } else {
                        alert('Family ID Blocked. Please contact admin office for more details.');
                    }
                    familyIdInput.value = '';
                    studentSelect.innerHTML = '<option value="">First select Family ID</option>';
                    studentSelect.disabled = true;
                    searchBtn.disabled = true;
                    reportContainer.style.display = 'none';
                    return;
                }

                if (!data.success) {
                    errorMessage.textContent = data.message || 'Error fetching report data';
                    errorMessage.style.display = 'block';
                    return;
                }

                if (!data.data || data.data.length === 0) {
                    errorMessage.textContent = 'No data found for this Family ID';
                    errorMessage.style.display = 'block';
                    return;
                }

                // Store all data
                allReportData = data.data;
                
                // If student is already selected, show that student's data
                if (studentSelect.value) {
                    displayStudentReport(studentSelect.value);
                } else {
                    // Show all students (default behavior)
                    displayReport(data.data);
                    reportContainer.style.display = 'block';
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                errorMessage.textContent = 'Error: ' + error.message;
                errorMessage.style.display = 'block';
                console.error('Error:', error);
            });
        }

        // Also display report when student changes (if data already loaded)
        studentSelect.addEventListener('change', function() {
            if (this.value && allReportData.length > 0) {
                displayStudentReport(this.value);
            }
        });

        function displayStudentReport(selectedStudentName) {
            // Filter data for selected student
            const studentData = allReportData.filter(s => s.student_name === selectedStudentName);
            
            if (studentData.length === 0) {
                errorMessage.textContent = 'No data found for selected student';
                errorMessage.style.display = 'block';
                reportContainer.style.display = 'none';
                return;
            }

            displayReport(studentData);
            reportContainer.style.display = 'block';
            errorMessage.style.display = 'none';
        }

        function displayReport(reportData) {
            let html = '<h3 class="mb-4">Report for Family ID: <strong>' + document.getElementById('family_id').value + '</strong></h3>';

            reportData.forEach((student, index) => {
                html += '<div class="report-card">';
                
                // Student header
                html += '<div class="student-header">';
                html += '<h4 class="mb-0">Student: ' + student.student_name + '</h4>';
                html += '</div>';

                // First timetable date and reset date
                html += '<div class="info-box">';
                html += '<h5><i class="fas fa-calendar-alt me-2"></i>Tracking Period</h5>';
                if (student.is_reset_date && student.reset_date) {
                    html += '<p class="mb-0"><strong>Tracking Start Date:</strong> ' + formatDate(student.reset_date) + '</p>';
                    html += '<p class="mb-0 mt-2 text-info"><i class="fas fa-info-circle me-1"></i>All calculations (quota, carry forward, breakdown) start from this date. Previous data excluded.</p>';
                    if (student.first_timetable_date) {
                        html += '<p class="mb-0 mt-1 text-muted"><small>Original first date in system: ' + formatDate(student.first_timetable_date) + '</small></p>';
                    }
                } else if (student.first_timetable_date) {
                    html += '<p class="mb-0"><strong>First Date in General Timetable:</strong> ' + formatDate(student.first_timetable_date) + '</p>';
                } else {
                    html += '<p class="mb-0 text-muted">Not yet added to timetable</p>';
                }
                html += '</div>';

                // Subjects and weekly quota
                html += '<div class="info-box">';
                html += '<h5><i class="fas fa-book me-2"></i>Subjects & Weekly Quota</h5>';
                if (student.subjects && student.subjects.length > 0) {
                    html += '<ul class="subjects-list">';
                    student.subjects.forEach((subject, idx) => {
                        const hours = student.studenthours && student.studenthours[idx] ? student.studenthours[idx] : 0;
                        html += '<li><strong>' + subject + ':</strong> ' + hours + ' session(s) per week</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="text-muted mb-0">No subjects assigned</p>';
                }
                html += '<p class="mt-2 mb-0"><strong>Current Weekly Quota:</strong> <span class="badge badge-custom" style="background-color: #2048AC; color: white;">' + (student.current_weekly_quota || student.total_weekly_quota || 0) + ' sessions</span></p>';
                
                // Show quota changes if any - Professional stamp design
                if (student.quota_changes && student.quota_changes.length > 0) {
                    html += '<div class="mt-3">';
                    student.quota_changes.forEach((change, idx) => {
                        const isToday = change.change_date === new Date().toISOString().split('T')[0];
                        html += '<div class="quota-change-stamp" style="position: relative; display: inline-block; margin: 10px 10px 10px 0; padding: 0;">';
                        html += '<div style="position: relative; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; padding: 12px 20px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); transform: rotate(-2deg); transition: transform 0.3s ease;">';
                        html += '<div style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; background: #ffc107; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.5);">';
                        html += '<i class="fas fa-sync-alt" style="color: #fff; font-size: 12px;"></i>';
                        html += '</div>';
                        html += '<div style="color: white; text-align: center;">';
                        html += '<div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 4px;">Quota Updated</div>';
                        html += '<div style="font-size: 14px; font-weight: bold; margin-bottom: 2px;">' + formatDate(change.change_date) + '</div>';
                        html += '<div style="font-size: 11px; opacity: 0.85; margin-top: 4px;">';
                        html += '<span style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; margin-right: 4px;">' + change.old_quota + '</span>';
                        html += '<i class="fas fa-arrow-right" style="font-size: 9px; margin: 0 4px;"></i>';
                        html += '<span style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 3px; font-weight: bold;">' + change.new_quota + '</span>';
                        html += '</div>';
                        if (isToday) {
                            html += '<div style="font-size: 9px; margin-top: 4px; background: rgba(255,255,255,0.25); padding: 2px 6px; border-radius: 3px; display: inline-block;">Effective Today</div>';
                        }
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    });
                    html += '<div class="mt-2" style="font-size: 12px; color: #6c757d; font-style: italic;">';
                    html += '<i class="fas fa-info-circle me-1"></i>Previous periods use their respective quota values. New quota applies from change date onwards.';
                    html += '</div>';
                    html += '</div>';
                }
                
                html += '</div>';

                // Total lessons summary
                html += '<div class="info-box">';
                html += '<h5><i class="fas fa-chart-line me-2"></i>Total Lessons Summary</h5>';
                html += '<div class="row">';
                html += '<div class="col-md-4"><p class="mb-1"><strong>Total Available:</strong> <span class="badge bg-info">' + (student.total_available_lessons || 0) + '</span></p></div>';
                html += '<div class="col-md-4"><p class="mb-1"><strong>Lessons Taken:</strong> <span class="badge bg-success">' + student.total_lessons_taken + '</span></p></div>';

                // Remaining: split into paid (yellow) + unpaid (red)
                const paidRem   = student.paid_remaining   || 0;
                const unpaidRem = student.unpaid_remaining || 0;
                const totalRem  = student.total_remaining_lessons || 0;
                html += '<div class="col-md-4"><p class="mb-1"><strong>Remaining:</strong> ';
                if (paidRem > 0 && unpaidRem > 0) {
                    html += '<span class="badge" style="background-color:#ffc107;color:#000;" title="Paid lessons remaining">' + paidRem + ' Paid</span> ';
                    html += '<span class="badge bg-danger" title="Unpaid lessons remaining">' + unpaidRem + ' Unpaid</span>';
                } else if (paidRem > 0) {
                    html += '<span class="badge" style="background-color:#ffc107;color:#000;" title="Paid lessons remaining">' + paidRem + ' Paid</span>';
                } else if (unpaidRem > 0) {
                    html += '<span class="badge bg-danger" title="Unpaid lessons remaining">' + unpaidRem + ' Unpaid</span>';
                } else {
                    html += '<span class="badge bg-secondary">0</span>';
                }
                html += '</p>';
                if (paidRem > 0 || unpaidRem > 0) {
                    html += '<small class="text-muted"><span style="color:#856404;font-weight:600;">■</span> Yellow = payment received &nbsp; <span style="color:#dc3545;font-weight:600;">■</span> Red = no payment</small>';
                }
                html += '</div>';

                html += '</div>';
                const startDate = student.is_reset_date && student.reset_date ? student.reset_date : student.first_timetable_date;
                html += '<p class="mb-0 mt-2"><small class="text-muted">From ' + (startDate ? formatDate(startDate) : 'N/A') + ' to ' + formatDate(new Date().toISOString().split('T')[0]) + '</small></p>';
                html += '</div>';

                // Monthly breakdown with carry forward
                html += '<div class="info-box">';
                html += '<h5><i class="fas fa-calendar-alt me-2"></i>Monthly Breakdown (with Carry Forward)</h5>';
                
                if (student.monthly_breakdown && student.monthly_breakdown.length > 0) {
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-bordered table-hover">';
                    html += '<thead style="background-color: #2048AC; color: white;">';
                    html += '<tr>';
                    html += '<th>Month</th>';
                    html += '<th>Monthly Quota</th>';
                    html += '<th>Carry Forward<br><small>(From Previous)</small></th>';
                    html += '<th>Available<br><small>(Quota + Carry)</small></th>';
                    html += '<th>Lessons Taken</th>';
                    html += '<th>Remaining</th>';
                    html += '<th>Carry Forward<br><small>(To Next Month)</small></th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    student.monthly_breakdown.forEach(month => {
                        const availableFormula = month.monthly_quota + month.carry_forward_from_previous;
                        const quotaChanged = month.quota_changed_in_month && month.quota_change_date;

                        // Row color: Yellow = paid, Red = unpaid
                        let rowStyle = '';
                        if (month.is_paid) {
                            rowStyle = 'style="background-color: #fff9db;"'; // soft yellow
                        } else {
                            rowStyle = 'style="background-color: #ffe5e5;"'; // soft red
                        }
                        // If quota changed in month, add left border on top of color
                        if (quotaChanged) {
                            rowStyle = month.is_paid
                                ? 'style="background-color: #fff9db; border-left: 4px solid #667eea;"'
                                : 'style="background-color: #ffe5e5; border-left: 4px solid #667eea;"';
                        }

                        html += '<tr ' + rowStyle + '>';
                        html += '<td><strong>' + month.month_name + '</strong>';
                        if (quotaChanged) {
                            html += '<div class="mt-2">';
                            html += '<span class="quota-badge-mini" style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);">';
                            html += '<i class="fas fa-sync-alt me-1" style="font-size: 8px;"></i>';
                            html += 'Updated: ' + formatDate(month.quota_change_date);
                            html += '</span>';
                            html += '</div>';
                        }
                        // Payment status indicator
                        html += '<div class="mt-1">';
                        if (month.is_paid) {
                            html += '<span style="font-size:11px;color:#856404;font-weight:600;"><i class="fas fa-check-circle me-1"></i>Payment Received</span>';
                        } else {
                            html += '<span style="font-size:11px;color:#dc3545;font-weight:600;"><i class="fas fa-exclamation-circle me-1"></i>No Payment</span>';
                        }
                        html += '</div>';
                        html += '</td>';
                        html += '<td>' + month.monthly_quota;
                        if (month.weekly_quota_used) {
                            html += '<br><small class="text-muted">(' + month.complete_weeks + ' weeks × ' + month.weekly_quota_used + '/week)</small>';
                        }
                        html += '</td>';
                        html += '<td>' + (month.carry_forward_from_previous > 0 ? '<span class="badge bg-secondary">' + month.carry_forward_from_previous + '</span>' : '0') + '</td>';
                        html += '<td><strong>' + month.available_this_month + '</strong><br><small class="text-muted">(' + month.monthly_quota + ' + ' + month.carry_forward_from_previous + ')</small></td>';
                        html += '<td><span class="badge bg-primary">' + month.lessons_taken + '</span></td>';
                        html += '<td>' + (month.remaining > 0 ? '<span class="badge bg-warning">' + month.remaining + '</span>' : '<span class="badge bg-success">0</span>') + '</td>';
                        html += '<td>' + (month.carry_forward_to_next > 0 ? '<span class="badge bg-info">' + month.carry_forward_to_next + '</span>' : '0') + '</td>';
                        html += '</tr>';
                    });

                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';

                    // Monthly legend
                    html += '<div class="mt-2 d-flex gap-3 flex-wrap" style="font-size:13px;">';
                    html += '<span><span style="display:inline-block;width:14px;height:14px;background:#fff9db;border:1px solid #ffe066;vertical-align:middle;margin-right:4px;"></span><span style="color:#856404;">Yellow = Payment received</span></span>';
                    html += '<span><span style="display:inline-block;width:14px;height:14px;background:#ffe5e5;border:1px solid #f5c6cb;vertical-align:middle;margin-right:4px;"></span><span style="color:#dc3545;">Red = No payment for this month</span></span>';
                    html += '</div>';
                } else {
                    html += '<p class="text-muted mb-0">No monthly data available</p>';
                }

                html += '</div>';

                // Weekly breakdown
                html += '<div class="info-box">';
                html += '<h5><i class="fas fa-calendar-week me-2"></i>Weekly Breakdown</h5>';
                
                if (student.weekly_breakdown && student.weekly_breakdown.length > 0) {
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-bordered">';
                    html += '<thead style="background-color: #2048AC; color: white;">';
                    html += '<tr>';
                    html += '<th>Week Start</th>';
                    html += '<th>Week End</th>';
                    html += '<th>Lessons Taken</th>';
                    html += '<th>Weekly Quota</th>';
                    html += '<th>Status</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';

                    student.weekly_breakdown.forEach(week => {
                        const status = week.lessons_taken >= week.quota ? 'Complete' : 'Partial';
                        const statusColor = week.lessons_taken >= week.quota ? 'success' : 'warning';
                        // Yellow = paid, Red = unpaid
                        const rowBg = week.is_paid
                            ? 'style="background-color: #fff9db;"'
                            : 'style="background-color: #ffe5e5;"';
                        const dateColor = week.is_paid
                            ? 'style="color: #856404; font-weight: 600;"'
                            : 'style="color: #dc3545; font-weight: 600;"';
                        html += '<tr ' + rowBg + '>';
                        html += '<td ' + dateColor + '>' + formatDate(week.week_start) + '</td>';
                        html += '<td ' + dateColor + '>' + formatDate(week.week_end) + (!week.is_paid ? ' <span title="No payment" style="color:#dc3545;">⚠</span>' : '') + '</td>';
                        html += '<td><strong>' + week.lessons_taken + '</strong></td>';
                        html += '<td>' + week.quota + '</td>';
                        html += '<td><span class="badge bg-' + statusColor + '">' + status + '</span></td>';
                        html += '</tr>';
                    });

                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';

                    // Legend
                    html += '<div class="mt-2 d-flex gap-3 flex-wrap" style="font-size:13px;">';
                    html += '<span><span style="display:inline-block;width:14px;height:14px;background:#fff9db;border:1px solid #ffe066;vertical-align:middle;margin-right:4px;"></span><span style="color:#856404;">Yellow = Payment received</span></span>';
                    html += '<span><span style="display:inline-block;width:14px;height:14px;background:#ffe5e5;border:1px solid #f5c6cb;vertical-align:middle;margin-right:4px;"></span><span style="color:#dc3545;">Red = No payment for this week</span></span>';
                    html += '</div>';
                } else {
                    html += '<p class="text-muted mb-0">No lessons recorded yet</p>';
                }

                html += '</div>';
                html += '</div>';
            });

            reportContainer.innerHTML = html;
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    });
</script>
@endsection

