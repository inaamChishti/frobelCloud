@extends('layouts.branchDashboardApp')

@section('content')
@include('branchFrontend.centralTimeTable.timetableCss')
@include('branchFrontend.centralTimeTable.TimetableJSForModal')

<div id="overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999; opacity: 0; transition: opacity 0.3s;">
</div>

<div id="calendar-container"
    style="display: flex; justify-content: center; flex-direction: column; padding: 20px; height: 100vh; cursor: pointer;">
    <div class="search-container" style="margin: 20px auto; max-width: 600px; display: flex; justify-content: center;">
        <input type="text" id="search-bar" placeholder="Search by Family ID or Name (e.g., 123 John)..."
            style="width: 100%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; outline: none; transition: border-color 0.3s;">
    </div>
    <div class="mb-4 d-flex justify-content-between" style="margin-top: -20px;">
        <a href="{{ url('generate-staff-timetable') }}" class="btn btn-success" id="generate-staff-report">Generate
            Staff Timetable</a>
        <a href="{{ url('generate-student-timetable') }}" class="btn btn-success" id="generate-student-report">Student
            Timetable</a>
    </div>
    <div style="display: flex; justify-content: center; width: 100%; margin-bottom: 20px;">
        <button id="prev-day"
            style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; margin: 0 15px; cursor: pointer; font-size: 1.2rem; transition: background-color 0.3s ease, transform 0.3s ease;">
            < Prev</button>
        <div id="calendar-day" style="font-size: 2rem; font-weight: bold; margin: 0 20px;"></div>
        <button id="next-day"
            style="background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 20px; margin: 0 15px; cursor: pointer; font-size: 1.2rem; transition: background-color 0.3s ease, transform 0.3s ease;">Next
            ></button>
    </div>
    <div class="term-break-container">
        <button id="add-term-break">Add Term Break</button>
    </div>
    <div id="time-slots-container" style="display: flex; flex-direction: column; width: 100%; gap: 15px;"></div>
    <div id="class-modal" class="modal">
        <div class="modal-header">
            <h3>Create Class</h3>
            <button type="button" id="close-modal" class="close-modal">×</button>
        </div>
        <form id="class-form">
            <div class="form-group">
                <label for="teacher">Teacher</label>
                <select id="teacher" name="teacher" class="form-control">
                    <option value="">Select Teacher</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->teacher_name }}">{{ $teacher->teacher_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="additional-info-container" style="display: none;">
                <label for="additional-info-checkbox" style="cursor: pointer; display: flex; align-items: center;">
                    <input type="checkbox" id="additional-info-checkbox" name="additional-info-checkbox" value="No" style="margin-right: 10px;"
                        onchange="this.value = this.checked ? 'Yes' : 'No'; document.getElementById('additional-info-input').style.display = this.checked ? 'block' : 'none';">
                    Additional Comments
                </label>
                <input type="text" id="additional-info-input" name="additional_comments" class="form-control" placeholder="Enter additional comments" style="display: none; margin-top: 10px;">
            </div>
            <input type="hidden" id="class-id" value="">
          {{-- <table class="table table-bordered" style="zoom:0.8;">
                <thead>
                    <tr>
    <th>Student</th>
                        <th style="display: none;">Year in School</th>
                        <th>Subj</th>
    <th>Book</th>
    <th>Ch</th>
    <th>Behav</th>
    <th>Perf</th>
    <th>Att</th>
    <th>H/W</th>
    <th>Perma</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 1; $i <= 10; $i++)
                        <tr>
                            <td>
                                <input type="text" id="student-{{ $i }}" class="form-control"
                                    placeholder="Student Name" autocomplete="off">
                                <div id="student-{{ $i }}-dropdown" class="dropdown-menu"
                                    style="display:none;">
                                </div>
                            </td>
                            <td style="display: none;">
                                <input type="text" id="yearinschool-{{ $i }}" class="form-control"
                                    placeholder="Year in School">
                            </td>
                            <td>
                                <select id="subject-{{ $i }}-2" class="form-control">
                                    <option disabled selected>Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject }}">{{ $subject }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select id="book-{{ $i }}" class="form-control">
                                    <option disabled selected>Book</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="ch-{{ $i }}" class="form-control"
                                    placeholder="Chapter">
                            </td>
                             <td>
                                <select id="behaviour-{{ $i }}" class="form-control">
                                    <option value="" disabled selected>Behaviour</option>
                                    <option value="Good">Good</option>
                                    <option value="Satisfactory">Satisfactory</option>
                                    <option value="Poor">Poor</option>
                                </select>
                            </td>
                            <td>
                                <select id="performance-{{ $i }}" class="form-control">
                                    <option value="" disabled selected>Performance</option>
                                    <option value="On Target">On Target</option>
                                    <option value="Exceeding Target">Exceeding Target</option>
                                    <option value="Below Target">Below Target</option>
                                </select>
                            </td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" id="checkbox-{{ $i }}"
                                    onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                                    value="No" style="display: none;">
                                <label for="checkbox-{{ $i }}"
                                    style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <span
                                        style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #fff; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                                </label>
                            </td>

                            <td style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" id="homework-{{ $i }}"
                                    onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                                    value="No" style="display: none;">
                                <label for="homework-{{ $i }}"
                                    style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <span
                                        style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #fff; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                                </label>
                            </td>

                            <td style="text-align: center; vertical-align: middle;">
                                <input type="checkbox" id="permanent-{{ $i }}"
                                    onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                                    value="Yes" checked style="display: none;">
                                <label for="permanent-{{ $i }}"
                                    style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <span
                                        style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #4CAF50; border-color: #4CAF50; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                                </label>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table> --}}
            <table class="table table-bordered" style="zoom:0.8;">
    <thead>
        <tr>
            <th>Student</th>
            <th style="display: none;">Year in School</th>
            <th>Subj</th>
            <th>Book</th>
            <th>Ch</th>
            <th>Behav</th>
            <th>Perf</th>
            <th>Att</th>
            <th>H/W</th>
            <th>Perma</th>
        </tr>
    </thead>
    <tbody>
        @php
        $user = auth()->user();
        $pagePermissions = [];

        if ($user && $user->permissions) {
            $pagePermissions = json_decode($user->permissions->page_name, true) ?? [];
        }
    @endphp
        @for ($i = 1; $i <= 10; $i++)
            <tr>
                <!-- Student Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['add_atten_student_access']) || $pagePermissions['add_atten_student_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <input type="text" id="student-{{ $i }}" class="form-control"
                        placeholder="Student Name" autocomplete="off">
                    <div id="student-{{ $i }}-dropdown" class="dropdown-menu"
                        style="display:none;">
                    </div>
                </td>
                <!-- Year in School (Hidden) -->
                <td style="display: none;">
                    <input type="text" id="yearinschool-{{ $i }}" class="form-control"
                        placeholder="Year in School">
                </td>
                <!-- Subject Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['add_atten_student_access']) || $pagePermissions['add_atten_student_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <select id="subject-{{ $i }}-2" class="form-control">
                        <option disabled selected>Subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    </select>
                </td>
                <!-- Book Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <select id="book-{{ $i }}" class="form-control">
                        <option disabled selected>Book</option>
                    </select>
                </td>
                <!-- Chapter Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <input type="text" id="ch-{{ $i }}" class="form-control"
                        placeholder="Chapter">
                </td>
                <!-- Behaviour Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <select id="behaviour-{{ $i }}" class="form-control">
                        <option value="" disabled selected>Behaviour</option>
                        <option value="Good">Good</option>
                        <option value="Satisfactory">Satisfactory</option>
                        <option value="Poor">Poor</option>
                    </select>
                </td>
                <!-- Performance Column -->
                <td style="position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <select id="performance-{{ $i }}" class="form-control">
                        <option value="" disabled selected>Performance</option>
                        <option value="On Target">On Target</option>
                        <option value="Exceeding Target">Exceeding Target</option>
                        <option value="Below Target">Below Target</option>
                    </select>
                </td>
                <!-- Attendance Column -->
                <td style="text-align: center; vertical-align: middle; position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <input type="checkbox" id="checkbox-{{ $i }}"
                        onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                        value="No" style="display: none;">
                    <label for="checkbox-{{ $i }}"
                        style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <span
                            style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #fff; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                    </label>
                </td>
                <!-- Homework Column -->
                <td style="text-align: center; vertical-align: middle; position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <input type="checkbox" id="homework-{{ $i }}"
                        onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                        value="No" style="display: none;">
                    <label for="homework-{{ $i }}"
                        style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <span
                            style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #fff; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                    </label>
                </td>
                <!-- Permanent Column -->
                <td style="text-align: center; vertical-align: middle; position: relative;">
                    @if (!isset($pagePermissions['attendance_access']) || $pagePermissions['attendance_access'] !== 'on')
                        <div class="restrict-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;"></div>
                    @endif
                    <input type="checkbox" id="permanent-{{ $i }}"
                        onchange="this.value = this.checked ? 'Yes' : 'No'; this.nextElementSibling.querySelector('span').style.backgroundColor = this.checked ? '#4CAF50' : '#fff'; this.nextElementSibling.querySelector('span').style.borderColor = this.checked ? '#4CAF50' : '#ccc';"
                        value="Yes" checked style="display: none;">
                    <label for="permanent-{{ $i }}"
                        style="cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <span
                            style="width: 20px; height: 20px; border: 2px solid #ccc; border-radius: 4px; background-color: #4CAF50; border-color: #4CAF50; transition: background-color 0.3s ease, border-color 0.3s ease;"></span>
                    </label>
                </td>
            </tr>
        @endfor
    </tbody>
</table>

<style>
    .restrict-overlay {
        background: transparent;
        pointer-events: auto; /* Prevents clicks from passing through */
        cursor: not-allowed; /* Shows a "not allowed" cursor */
    }
</style>

<script>
    // Optional: Add a click event to show a message when restricted fields are clicked
    document.querySelectorAll('.restrict-overlay').forEach(overlay => {
        overlay.addEventListener('click', () => {
            alert('You do not have permission to interact with this field.');
        });
    });
</script>
            <input type="hidden" id="selected-day" name="selected-day">
            <input type="hidden" id="additional_student" name="additional_student">
            <input type="hidden" id="selected-slot" name="selected-slot">
            <button type="button" id="save-class" class="btn btn-success">Save</button>
        </form>
    </div>
    <div id="term-break-modal" class="modal">
        <div class="modal-header">
            <h3>{{ $termBreak ? 'Update' : 'Add' }} Term Break</h3>
            <button type="button" id="close-term-break-modal" class="close-modal">×</button>
        </div>
        <form id="term-break-form">
            @csrf
            <div class="form-group">
                <label for="start-date">From Date</label>
                <input type="date" id="start-date" name="start_date" class="form-control"
                    value="{{ $termBreak->start_term_break ?? '' }}" required>
            </div>
            <div class="form-group">
                <label for="end-date">To Date</label>
                <input type="date" id="end-date" name="end_date" class="form-control"
                    value="{{ $termBreak->end_term_break ?? '' }}" required>
            </div>
            <button type="button" id="save-term-break" class="btn btn-success">
                {{ $termBreak ? 'Update' : 'Save' }}
            </button>
        </form>
    </div>
    <div id="confirm-modal" class="confirm-modal">
        <div class="confirm-modal-content">
            <p>Are you sure you want to perform this action?</p>
            <button id="confirm-btn" class="confirm-btn">Yes</button>
            <button id="cancel-btn" class="cancel-btn">No</button>
        </div>
    </div>
    <div id="loadingMessage"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); color: white; font-size: 1.5rem; font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; z-index: 1000; flex-direction: column; text-align: center;">
        <div
            style="border: 3px solid white; padding: 20px; border-radius: 10px; background: rgba(255, 255, 255, 0.2);">
            <p style="margin: 0; font-size: 1.8rem;">⏳ Please wait...</p>
            <p style="margin: 10px 0 0; font-size: 1.2rem;">Fetching data for you...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Select2 for teacher dropdown
            $('#teacher').select2({
                placeholder: "Select Teacher",
                allowClear: true,
                width: '100%'
            });

            const timeSlotsContainer = document.getElementById('time-slots-container');
            const calendarDay = document.getElementById('calendar-day');
            const classModal = document.getElementById('class-modal');
            const termBreakModal = document.getElementById('term-break-modal');
            const closeClassModalButton = document.getElementById('close-modal');
            const closeTermBreakModalButton = document.getElementById('close-term-break-modal');
            const saveClassButton = document.getElementById('save-class');
            const saveTermBreakButton = document.getElementById('save-term-break');
            const addTermBreakButton = document.getElementById('add-term-break');
            const overlay = document.getElementById('overlay');
            let currentDate = new Date();

            let isTermBreak = false;

            // Time slot mapping for display and backend
            const timeSlotMapping = {
                '1': {
                    weekday: { display: '11:00 - 01:00', value: '11:00 - 01:00pm', label: 'Lesson 1' },
                    weekend: { display: '09:00 - 11:00', value: '09:00 - 11:00am', label: 'Lesson 1' }
                },
                '2': {
                    weekday: { display: '01:30 - 03:30', value: '01:30 - 03:30pm', label: 'Lesson 2' },
                    weekend: { display: '11:20 - 01:20', value: '11:20 - 01:20pm', label: 'Lesson 2' }
                },
                '3': {
                    weekday: { display: '04:30 - 06:30', value: '04:30 - 06:30pm', label: 'Lesson 3' },
                    weekend: { display: '02:00 - 04:00', value: '02:00 - 04:00pm', label: 'Lesson 3' }
                },
                '4': {
                    weekday: { display: '06:45 - 08:45', value: '06:45 - 08:45pm', label: 'Lesson 4' },
                    weekend: { display: '', value: '', label: '' }
                },
                '7': {
                    weekday: { display: '11:00 - 01:00', value: '11:00 - 01:00pm', label: 'Term Break Session 1' },
                    weekend: { display: '11:00 - 01:00', value: '11:00 - 01:00pm', label: 'Term Break Session 1' }
                },
                '8': {
                    weekday: { display: '01:30 - 03:30', value: '01:30 - 03:30pm', label: 'Term Break Session 2' },
                    weekend: { display: '01:30 - 03:30', value: '01:30 - 03:30pm', label: 'Term Break Session 2' }
                }
            };

            // Function to update attendance checkbox based on book and chapter
            function updateAttendanceAndHomeworkCheckbox(index) {
                const book = document.getElementById(`book-${index}`).value;
                const chapter = document.getElementById(`ch-${index}`).value.trim();
                const attendanceCheckbox = document.getElementById(`checkbox-${index}`);
                const attendanceLabel = attendanceCheckbox?.nextElementSibling?.querySelector('span');

                const isValid = book && book !== 'Book' && chapter && chapter.toLowerCase() !== 'null';

                if (attendanceCheckbox && attendanceLabel) {
                    attendanceCheckbox.disabled = !isValid;
                    attendanceLabel.style.cursor = isValid ? 'pointer' : 'not-allowed';

                    if (!isValid) {
                        attendanceCheckbox.checked = false;
                        attendanceCheckbox.value = 'No';
                        attendanceLabel.style.backgroundColor = '#fff';
                        attendanceLabel.style.borderColor = '#ccc';
                    } else {
                        attendanceCheckbox.checked = true;
                        attendanceCheckbox.value = 'Yes';
                        attendanceLabel.style.backgroundColor = '#4CAF50';
                        attendanceLabel.style.borderColor = '#4CAF50';
                    }
                }
            }

            // Initialize checkbox and input event listeners
            for (let i = 1; i <= 10; i++) {
                const attendanceCheckbox = document.getElementById(`checkbox-${i}`);
                const homeworkCheckbox = document.getElementById(`homework-${i}`);
                const bookSelect = document.getElementById(`book-${i}`);
                const chapterInput = document.getElementById(`ch-${i}`);
                const behaviourSelect = document.getElementById(`behaviour-${i}`);
                const performanceSelect = document.getElementById(`performance-${i}`);

                // Initialize attendance checkbox
                if (attendanceCheckbox) {
                    attendanceCheckbox.value = 'No';
                    const attendanceLabel = attendanceCheckbox.nextElementSibling?.querySelector('span');
                    if (attendanceLabel) {
                        attendanceLabel.style.backgroundColor = '#fff';
                        attendanceLabel.style.borderColor = '#ccc';
                        attendanceLabel.style.cursor = 'not-allowed';
                    }
                }

                // Initialize homework checkbox
                if (homeworkCheckbox) {
                    homeworkCheckbox.value = 'No';
                    const homeworkLabel = homeworkCheckbox.nextElementSibling?.querySelector('span');
                    if (homeworkLabel) {
                        homeworkLabel.style.backgroundColor = '#fff';
                        homeworkLabel.style.borderColor = '#ccc';
                    }

                    homeworkCheckbox.addEventListener('change', function() {
                        const label = this.nextElementSibling?.querySelector('span');
                        if (label) {
                            this.value = this.checked ? 'Yes' : 'No';
                            label.style.backgroundColor = this.checked ? '#4CAF50' : '#fff';
                            label.style.borderColor = this.checked ? '#4CAF50' : '#ccc';
                        }
                    });
                }

                // Initialize behaviour and performance dropdowns
                if (behaviourSelect) {
                    behaviourSelect.value = '';
                }
                if (performanceSelect) {
                    performanceSelect.value = '';
                }

                // Update attendance based on book and chapter
                [bookSelect, chapterInput].forEach(input => {
                    if (input) {
                        input.addEventListener('change', () => updateAttendanceAndHomeworkCheckbox(i));
                        input.addEventListener('input', () => updateAttendanceAndHomeworkCheckbox(i));
                    }
                });
            }

            // Initialize additional info container
            const additionalInfoContainer = document.getElementById('additional-info-container');
            if (additionalInfoContainer) {
                additionalInfoContainer.style.display = 'none';
            }

            saveClassButton.addEventListener('click', () => {
                const header = document.querySelector('#class-modal .modal-header h3');
                if (header) header.textContent = 'Create Class';
            });

            closeClassModalButton.addEventListener('click', () => {
                const header = document.querySelector('#class-modal .modal-header h3');
                if (header) header.textContent = 'Create Class';
            });

            // Term Break Modal Handling
            addTermBreakButton.addEventListener('click', () => {
                const additionalParam = currentDate.toISOString().split('T')[0];
                const loadingMessage = document.getElementById('loadingMessage');
                loadingMessage.style.display = 'flex';

                fetch(`{{ url('get-term-break') }}?date=${additionalParam}`)
                    .then(response => response.json())
                    .then(data => {
                        const startDateInput = document.getElementById('start-date');
                        const endDateInput = document.getElementById('end-date');
                        const modalHeader = document.querySelector('#term-break-modal .modal-header h3');

                        startDateInput.value = '';
                        endDateInput.value = '';
                        modalHeader.textContent = 'Add Term Break';

                        if (data.is_term_break && data.start_date && data.end_date) {
                            startDateInput.value = data.start_date;
                            endDateInput.value = data.end_date;
                            modalHeader.textContent = 'Update Term Break';
                        }

                        termBreakModal.style.display = 'block';
                        termBreakModal.classList.add('show');
                        overlay.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching term break data:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch term break data.',
                        });
                        termBreakModal.style.display = 'block';
                        termBreakModal.classList.add('show');
                        overlay.style.display = 'block';
                    })
                    .finally(() => {
                        loadingMessage.style.display = 'none';
                    });
            });

            closeTermBreakModalButton.addEventListener('click', () => {
                termBreakModal.style.display = 'none';
                termBreakModal.classList.remove('show');
                overlay.style.display = 'none';
                document.getElementById('start-date').value = '';
                document.getElementById('end-date').value = '';
            });

            saveTermBreakButton.addEventListener('click', () => {
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in both From and To dates.',
                    });
                    return;
                }

                if (new Date(endDate) < new Date(startDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'End date cannot be earlier than start date.',
                    });
                    return;
                }

                const data = new URLSearchParams({
                    'start_date': startDate,
                    'end_date': endDate
                });

                const loadingMessage = document.getElementById('loadingMessage');
                loadingMessage.style.display = 'flex';

                fetch(`{{ url('store-term-break') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: data
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Term break saved successfully!',
                            });
                            termBreakModal.style.display = 'none';
                            termBreakModal.classList.remove('show');
                            overlay.style.display = 'none';
                            document.getElementById('start-date').value = '';
                            document.getElementById('end-date').value = '';
                            updateCalendar();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to save term break.',
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving the term break.',
                        });
                        console.error('Error saving term break:', error);
                    })
                    .finally(() => {
                        loadingMessage.style.display = 'none';
                    });
            });

            const createTimeSlots = () => {
                const dayOfWeek = currentDate.getDay();
                const additionalParam = currentDate.toISOString().split('T')[0];
                const termBreakSlots = [
                    { slotNumber: 7, display: timeSlotMapping['7'].weekday.display, label: timeSlotMapping['7'].weekday.label },
                    { slotNumber: 8, display: timeSlotMapping['8'].weekday.display, label: timeSlotMapping['8'].weekday.label }
                ];
                let regularSlots = [];

                if (dayOfWeek >= 1 && dayOfWeek <= 5) {
                    regularSlots = [
                        { slotNumber: 1, display: timeSlotMapping['1'].weekday.display, label: timeSlotMapping['1'].weekday.label },
                        { slotNumber: 2, display: timeSlotMapping['2'].weekday.display, label: timeSlotMapping['2'].weekday.label },
                        { slotNumber: 3, display: timeSlotMapping['3'].weekday.display, label: timeSlotMapping['3'].weekday.label },
                        { slotNumber: 4, display: timeSlotMapping['4'].weekday.display, label: timeSlotMapping['4'].weekday.label }
                    ];
                } else {
                    regularSlots = [
                        { slotNumber: 1, display: timeSlotMapping['1'].weekend.display, label: timeSlotMapping['1'].weekend.label },
                        { slotNumber: 2, display: timeSlotMapping['2'].weekend.display, label: timeSlotMapping['2'].weekend.label },
                        { slotNumber: 3, display: timeSlotMapping['3'].weekend.display, label: timeSlotMapping['3'].weekend.label }
                    ];
                }

                fetch(`{{ url('get-term-break') }}?date=${additionalParam}`)
                    .then(response => response.json())
                    .then(data => {
                        isTermBreak = false;
                        if (data.is_term_break && data.start_date && data.end_date) {
                            const current = new Date(additionalParam);
                            const start = new Date(data.start_date);
                            const end = new Date(data.end_date);
                            isTermBreak = current >= start && current <= end;
                        }

                        timeSlotsContainer.innerHTML = '';

                        if (isTermBreak && dayOfWeek >= 1 && dayOfWeek <= 5) {
                            const termBreakHeading = document.createElement('div');
                            termBreakHeading.className = 'section-heading';
                            termBreakHeading.textContent = 'Term Break';
                            timeSlotsContainer.appendChild(termBreakHeading);

                            termBreakSlots.forEach(slot => {
                                const timeSlot = document.createElement('div');
                                timeSlot.className = 'time-slot col-md-12';
                                timeSlot.dataset.slot = slot.slotNumber;
                                timeSlot.style.cssText =
                                    'background-color: #007bff; color: white; padding: 15px; text-align: center; height: auto; display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; margin-top: 15px;';
                                timeSlot.innerHTML = `<h4>${slot.label} – ${slot.display}</h4>`;
                                timeSlotsContainer.appendChild(timeSlot);

                                timeSlot.addEventListener('click', function(event) {
                                    if (!event.target.closest('.edit-btns')) {
                                        resetModalFields();
                                        document.getElementById('selected-slot').value = slot.slotNumber;
                                        document.getElementById('selected-day').value = currentDate.toISOString().split('T')[0];
                                        classModal.style.display = 'block';
                                        classModal.classList.add('show');
                                        overlay.style.display = 'none';
                                    }
                                });
                            });
                        } else {
                            regularSlots.forEach(slot => {
                                const timeSlot = document.createElement('div');
                                timeSlot.className = 'time-slot col-md-12';
                                timeSlot.dataset.slot = slot.slotNumber;
                                timeSlot.style.cssText =
                                    'background-color: #007bff; color: white; padding: 15px; text-align: center; height: auto; display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; margin-top: 31px;';
                                timeSlot.innerHTML = `<h4>${slot.label} – ${slot.display}</h4>`;
                                timeSlotsContainer.appendChild(timeSlot);

                                timeSlot.addEventListener('click', function(event) {
                                    if (!event.target.closest('.edit-btns')) {
                                        resetModalFields();
                                        document.getElementById('selected-slot').value = slot.slotNumber;
                                        document.getElementById('selected-day').value = currentDate.toISOString().split('T')[0];
                                        classModal.style.display = 'block';
                                        classModal.classList.add('show');
                                        overlay.style.display = 'none';
                                    }
                                });
                            });

                            const termBreakHeading = document.createElement('div');
                            termBreakHeading.className = 'section-heading';
                            termBreakHeading.textContent = 'Term Break';
                            timeSlotsContainer.appendChild(termBreakHeading);

                            termBreakSlots.forEach(slot => {
                                const timeSlot = document.createElement('div');
                                timeSlot.className = 'time-slot col-md-12';
                                timeSlot.dataset.slot = slot.slotNumber;
                                timeSlot.style.cssText =
                                    'background-color: #007bff; color: white; padding: 15px; text-align: center; height: auto; display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; margin-top: 15px;';
                                timeSlot.innerHTML = `<h4>${slot.label} – ${slot.display}</h4>`;
                                timeSlotsContainer.appendChild(timeSlot);

                                timeSlot.addEventListener('click', function(event) {
                                    if (!event.target.closest('.edit-btns')) {
                                        resetModalFields();
                                        document.getElementById('selected-slot').value = slot.slotNumber;
                                        document.getElementById('selected-day').value = currentDate.toISOString().split('T')[0];
                                        classModal.style.display = 'block';
                                        classModal.classList.add('show');
                                        overlay.style.display = 'none';
                                    }
                                });
                            });
                        }

                        fetchTimetable(currentDate);
                    })
                    .catch(error => {
                        console.error('Error fetching term break status:', error);
                        isTermBreak = false;
                        timeSlotsContainer.innerHTML = '';

                        regularSlots.forEach(slot => {
                            const timeSlot = document.createElement('div');
                            timeSlot.className = 'time-slot col-md-12';
                            timeSlot.dataset.slot = slot.slotNumber;
                            timeSlot.style.cssText =
                                'background-color: #007bff; color: white; padding: 15px; text-align: center; height: auto; display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; margin-top: 31px;';
                            timeSlot.innerHTML = `<h4>${slot.label} – ${slot.display}</h4>`;
                            timeSlotsContainer.appendChild(timeSlot);

                            timeSlot.addEventListener('click', function(event) {
                                if (!event.target.closest('.edit-btns')) {
                                    resetModalFields();
                                    document.getElementById('selected-slot').value = slot.slotNumber;
                                    document.getElementById('selected-day').value = currentDate.toISOString().split('T')[0];
                                    classModal.style.display = 'block';
                                    classModal.classList.add('show');
                                    overlay.style.display = 'none';
                                }
                            });
                        });

                        const termBreakHeading = document.createElement('div');
                        termBreakHeading.className = 'section-heading';
                        termBreakHeading.textContent = 'Term Break';
                        timeSlotsContainer.appendChild(termBreakHeading);

                        termBreakSlots.forEach(slot => {
                            const timeSlot = document.createElement('div');
                            timeSlot.className = 'time-slot col-md-12';
                            timeSlot.dataset.slot = slot.slotNumber;
                            timeSlot.style.cssText =
                                'background-color: #007bff; color: white; padding: 15px; text-align: center; height: auto; display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; margin-top: 15px;';
                            timeSlot.innerHTML = `<h4>${slot.label} – ${slot.display}</h4>`;
                            timeSlotsContainer.appendChild(timeSlot);

                            timeSlot.addEventListener('click', function(event) {
                                if (!event.target.closest('.edit-btns')) {
                                    resetModalFields();
                                    document.getElementById('selected-slot').value = slot.slotNumber;
                                    document.getElementById('selected-day').value = currentDate.toISOString().split('T')[0];
                                    classModal.style.display = 'block';
                                    classModal.classList.add('show');
                                    overlay.style.display = 'none';
                                }
                            });
                        });

                        fetchTimetable(currentDate);
                    });
            };

            const updateCalendar = () => {
                const dateFormatted = currentDate.toLocaleDateString('en-GB', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const dayOfWeek = currentDate.toLocaleDateString('en-GB', {
                    weekday: 'long'
                });
                calendarDay.textContent = `${dateFormatted}, ${dayOfWeek}`;
                createTimeSlots();
            };

            const fetchTimetable = (date) => {

                const loadingMessage = document.getElementById('loadingMessage');
                loadingMessage.style.display = 'flex';

                 const additionalParam = date.getFullYear() + "-"
        + String(date.getMonth() + 1).padStart(2, '0') + "-"
        + String(date.getDate()).padStart(2, '0');
                fetch(`{{ url('get-general-timetable') }}?date=${additionalParam}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            appendTimetableToSlots(data, additionalParam);
                        } else {
                            clearSlots(additionalParam);
                        }
                    })
                    .catch(error => console.error('Error fetching timetable:', error))
                    .finally(() => loadingMessage.style.display = 'none');
            };

            const clearSlots = (additionalParam) => {
                const slots = document.querySelectorAll('.time-slot');
                const date = new Date(additionalParam);
                const currentDay = date.getDay();

                slots.forEach(slot => {
                    const slotIndex = parseInt(slot.dataset.slot);
                    let timeDisplay = '';
                    let timeLabel = '';
                    if (timeSlotMapping[slotIndex]) {
                        timeDisplay = (currentDay >= 1 && currentDay <= 5) ? timeSlotMapping[slotIndex].weekday.display : timeSlotMapping[slotIndex].weekend.display;
                        timeLabel = timeSlotMapping[slotIndex].weekday.label;
                    } else {
                        timeDisplay = 'Unknown Slot';
                        timeLabel = 'Unknown Slot';
                    }
                    slot.innerHTML = `<h4>${timeLabel} – ${timeDisplay}</h4>`;
                });
            };

            const appendTimetableToSlots = (timetableData, date) => {
                const slots = document.querySelectorAll('.time-slot');
                const inputDate = new Date(date);
                const inputDay = inputDate.getDay();

                const subjectColors = {
                    'MATH': '#90CAF9',
                    'ENGLISH': '#A5D6A7',
                    'SCIENCE': '#FFF59D',
                    'default': '#FFF59D'
                };

                const hoverColors = {
                    'MATH': '#64B5F6',
                    'ENGLISH': '#81C784',
                    'SCIENCE': '#FFF176',
                    'default': '#FFF176'
                };

                const subjectOrder = ['MATH', 'ENGLISH', 'SCIENCE'];

                slots.forEach(slot => {
                    const slotIndex = parseInt(slot.dataset.slot);
                    let timeDisplay = '';
                    let timeLabel = '';
                    if (timeSlotMapping[slotIndex]) {
                        timeDisplay = (inputDay >= 1 && inputDay <= 5) ? timeSlotMapping[slotIndex].weekday.display : timeSlotMapping[slotIndex].weekend.display;
                        timeLabel = timeSlotMapping[slotIndex].weekday.label;
                    } else {
                        timeDisplay = 'Unknown Slot';
                        timeLabel = 'Unknown Slot';
                    }
                    slot.innerHTML = `<h4>${timeLabel} – ${timeDisplay}</h4>`;

                    const slotClasses = timetableData.filter(classItem => {
                        const slotDate = new Date(classItem.date);
                        return slotDate.toLocaleDateString('en-GB') === inputDate.toLocaleDateString('en-GB') && parseInt(classItem.slot) === slotIndex;
                    });

                    slotClasses.sort((a, b) => {
                        const subjectsA = JSON.parse(a.subjects || "[]");
                        const subjectsB = JSON.parse(b.subjects || "[]");
                        const primarySubjectA = subjectsA[0]?.toUpperCase() || 'default';
                        const primarySubjectB = subjectsB[0]?.toUpperCase() || 'default';
                        const indexA = subjectOrder.indexOf(primarySubjectA) === -1 ? subjectOrder.length : subjectOrder.indexOf(primarySubjectA);
                        const indexB = subjectOrder.indexOf(primarySubjectB) === -1 ? subjectOrder.length : subjectOrder.indexOf(primarySubjectB);
                        return indexA - indexB;
                    });

                    let classInfoContainer = slot.querySelector('.time-class-info');
                    if (!classInfoContainer) {
                        classInfoContainer = document.createElement('div');
                        classInfoContainer.className = 'time-class-info';
                        slot.appendChild(classInfoContainer);
                    } else {
                        classInfoContainer.innerHTML = '';
                    }

                    slotClasses.forEach(classItem => {
                        const studentIds = JSON.parse(classItem.student_ids || "[]");
                        const studentNames = JSON.parse(classItem.student_names || "[]");
                        const studentAges = classItem.student_ages || [];
                        const subjects = JSON.parse(classItem.subjects || "[]");
                        const attendances = JSON.parse(classItem.is_attendance || "[]");

                        let mergedInfo = studentNames.map((student, studentIndex) => {
                            const studentId = String(studentIds[studentIndex] ?? "").trim();
                            const firstName = String(student ?? "").trim();
                            const subject = String(subjects[studentIndex] ?? "").trim();
                            const age = studentAges[studentIndex] || "";
                            const attendance = attendances[studentIndex];
                            const attendanceMark = attendance === "Yes" ? '<span style="color: green; font-size: 14px;">✔</span>' : '<span style="color: red; font-size: 14px;">✘</span>';
                            const ageDisplay = age ? `-Y ${age}` : "";

                            if (!studentId || !firstName) {
                                return "";
                            }

                            const namePart = [studentId, firstName, ageDisplay].filter(Boolean).join(" ");
                            const subjectPart = subject ? ` - ${subject}` : "";
                            return `
                                <div style="display: table-row; white-space: nowrap;" onmouseover="this.style.backgroundColor='#f5f5f5'" onmouseout="this.style.backgroundColor='transparent'">
                                    <span style="font-weight: bold; display: table-cell; font-size: 14px; padding: 4px 8px; border: 2px solid #999; text-align: left; vertical-align: middle; white-space: nowrap;">${namePart}${subjectPart} ${attendanceMark}</span>
                                </div>
                            `;
                        }).join('');

                        let bgColor = subjectColors['default'];
                        let hoverColor = hoverColors['default'];
                        if (subjects.length > 0) {
                            const primarySubject = subjects[0]?.toUpperCase();
                            bgColor = subjectColors[primarySubject] || subjectColors['default'];
                            hoverColor = hoverColors[primarySubject] || hoverColors['default'];
                        }

                        const classInfoHTML = `
                            <div class="edit-btns" data-id="${classItem.id}" style="font-size: 14px; border: 1px solid #ccc; padding: 20px; border-radius: 5px; width: 350px; text-align: left; margin-top: 5px; position: relative; background-color: ${bgColor}; color: black; display: inline-block; margin-bottom: 15px; flex-shrink: 0;" onmouseover="this.style.backgroundColor='${hoverColor}'" onmouseout="this.style.backgroundColor='${bgColor}'">
                                <button data-id="${classItem.id}" class="close-btn">×</button>
                                <button data-id="${classItem.id}" class="edit-btn">✏️</button>
                                <br>
                                <strong>Teacher:</strong><br><span style="font-size: 13px; font-weight: normal; color: #6aa12a;" class="teacher">${classItem.teacher_id}</span><br>
                                <strong>Students & Subjects:</strong><br>
                                <div class="merged-info-container">${mergedInfo}</div>
                            </div>
                        `;

                        classInfoContainer.innerHTML += classInfoHTML;
                    });

                    if (classInfoContainer.scrollHeight > slot.offsetHeight) {
                        slot.style.height = `${classInfoContainer.scrollHeight + 30}px`;
                    }

                    slot.querySelectorAll('.edit-btns').forEach(btnContainer => {
                        btnContainer.addEventListener('click', (event) => {
                            event.stopPropagation();
                        });
                    });
                });

              document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const classId = button.getAttribute('data-id');
        const slotNumber = button.closest('.time-slot').getAttribute('data-slot');
        document.getElementById('confirm-modal').style.display = 'flex';
        document.querySelector('#confirm-modal p').innerText =
            `Are you sure you want to edit this item in Slot ${slotNumber}?`;

        document.getElementById('confirm-btn').onclick = function() {
            const url = `{{ url('get-edit-info') }}/${classId}`;
            resetModalFields();

            const loadingMessage = document.getElementById('loadingMessage');
            if (loadingMessage) {
                loadingMessage.style.display = 'flex';
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (loadingMessage) {
                            loadingMessage.style.display = 'none';
                        }

                        const teacher = data.data.teacher_id;
                        const students = JSON.parse(data.data.student_names || "[]");
                        const studentIds = JSON.parse(data.data.student_ids || "[]");
                        const subjects = JSON.parse(data.data.subjects || "[]");
                        const books = JSON.parse(data.data.books || "[]");
                        const chapters = JSON.parse(data.data.chapters || "[]");
                        const selectedDate = data.data.date;
                        const selectedSlot = data.data.slot;
                        let permanents = JSON.parse(data.data.permanent || "[]");
                        let attendances = JSON.parse(data.data.is_attendance || "[]");
                        let homeworks = data.is_homework || [];
                        let behaviours = data.behaviours || [];
                        let performances = data.performances || [];

                        document.querySelector('#class-modal .modal-header h3').textContent = 'Update Class';
                        $('#teacher').val(teacher).trigger('change');

                        // Show additional info container for Update Class
                        const additionalInfoContainer = document.getElementById('additional-info-container');
                        if (additionalInfoContainer) {
                            additionalInfoContainer.style.display = 'block';
                            const additionalInfoCheckbox = document.getElementById('additional-info-checkbox');
                            const additionalInfoInput = document.getElementById('additional-info-input');
                            if (data.data.additional_info) {
                                additionalInfoCheckbox.checked = true;
                                additionalInfoCheckbox.value = 'Yes';
                                additionalInfoInput.style.display = 'block';
                                additionalInfoInput.value = data.data.additional_info;
                            } else {
                                additionalInfoCheckbox.checked = false;
                                additionalInfoCheckbox.value = 'No';
                                additionalInfoInput.style.display = 'none';
                                additionalInfoInput.value = '';
                            }
                        }

                        data.students.forEach((studentData, index) => {
                            if (index < 10) {
                                const i = index + 1;
                                const studentName = `${studentIds[index]} ${students[index]}`;
                                const bookDropdown = document.getElementById(`book-${i}`);
                                const chInput = document.getElementById(`ch-${i}`);
                                const subjectSelect = document.getElementById(`subject-${i}-2`);
                                const attendanceCheckbox = document.getElementById(`checkbox-${i}`);
                                const homeworkCheckbox = document.getElementById(`homework-${i}`);
                                const behaviourSelect = document.getElementById(`behaviour-${i}`);
                                const performanceSelect = document.getElementById(`performance-${i}`);
                                const permanentCheckbox = document.getElementById(`permanent-${i}`);

                                document.getElementById(`student-${i}`).value = studentName;
                                document.getElementById(`yearinschool-${i}`).value = studentData.studentyearinschool || '';

                                // Update subject dropdown with student's subject_names
                                updateSubjectDropdown(subjectSelect, studentData.subject_names || [], studentData.subject);

                                bookDropdown.innerHTML = '<option disabled selected>Book</option>';
                                const category = `${studentData.family_id}-${studentData.student_name}`;
                                const availableBooks = data.books[category] || [];
                                availableBooks.forEach(book => {
                                    const option = document.createElement('option');
                                    option.value = book.book_name;
                                    option.textContent = book.book_name;
                                    if (book.make_dropdown_selected === 'yes') {
                                        option.selected = true;
                                    }
                                    bookDropdown.appendChild(option);
                                });

                                bookDropdown.value = studentData.book_name || 'Book';
                                chInput.value = studentData.ch || '';

                                const isValid = studentData.book_name && studentData.book_name !== 'Book' && studentData.ch && studentData.ch.toLowerCase() !== 'null';
                                if (attendanceCheckbox) {
                                    const attendanceLabel = attendanceCheckbox.nextElementSibling?.querySelector('span');
                                    attendanceCheckbox.disabled = !isValid;
                                    attendanceLabel.style.cursor = isValid ? 'pointer' : 'not-allowed';
                                    attendanceCheckbox.checked = isValid && attendances[index] === 'Yes';
                                    attendanceCheckbox.value = attendanceCheckbox.checked ? 'Yes' : 'No';
                                    if (attendanceLabel) {
                                        attendanceLabel.style.backgroundColor = attendanceCheckbox.checked ? '#4CAF50' : '#fff';
                                        attendanceLabel.style.borderColor = attendanceCheckbox.checked ? '#4CAF50' : '#ccc';
                                    }
                                }

                                if (homeworkCheckbox) {
                                    const homeworkLabel = homeworkCheckbox.nextElementSibling?.querySelector('span');
                                    const homeworkValue = homeworks[index] || 'No';
                                    homeworkCheckbox.checked = homeworkValue === 'Yes';
                                    homeworkCheckbox.value = homeworkValue;
                                    if (homeworkLabel) {
                                        homeworkLabel.style.backgroundColor = homeworkCheckbox.checked ? '#4CAF50' : '#fff';
                                        homeworkLabel.style.borderColor = homeworkCheckbox.checked ? '#4CAF50' : '#ccc';
                                    }
                                }

                                if (behaviourSelect) {
                                    behaviourSelect.value = behaviours[index] || '';
                                }

                                if (performanceSelect) {
                                    performanceSelect.value = performances[index] || '';
                                }

                                if (permanentCheckbox) {
                                    const permanentLabel = permanentCheckbox.nextElementSibling?.querySelector('span');
                                    const permanentValue = permanents[index] || 'Yes';
                                    permanentCheckbox.checked = permanentValue === 'Yes';
                                    permanentCheckbox.value = permanentValue;
                                    if (permanentLabel) {
                                        permanentLabel.style.backgroundColor = permanentValue === 'Yes' ? '#4CAF50' : '#fff';
                                        permanentLabel.style.borderColor = permanentValue === 'Yes' ? '#4CAF50' : '#ccc';
                                    }
                                }

                                if (studentData.subject) {
                                    $.ajax({
                                        url: '{{ url('fetch-bk-ch') }}',
                                        type: 'GET',
                                        data: {
                                            subject: studentData.subject,
                                            student: studentName
                                        },
                                        success: function(response) {
                                            bookDropdown.innerHTML = '<option disabled selected>Book</option>';
                                            response.forEach(book => {
                                                const option = document.createElement('option');
                                                option.value = book.name;
                                                option.textContent = book.name;
                                                if (book.name === studentData.book_name) {
                                                    option.selected = true;
                                                }
                                                bookDropdown.appendChild(option);
                                            });
                                            // Trigger update after books are populated
                                            updateAttendanceAndHomeworkCheckbox(i);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error fetching books:', error);
                                        }
                                    });
                                }
                            }
                        });

                        document.getElementById('selected-day').value = selectedDate;
                        document.getElementById('selected-slot').value = selectedSlot;
                        document.getElementById('class-id').value = classId;

                        classModal.style.display = 'block';
                        classModal.classList.add('show');
                        overlay.style.display = 'none';
                    } else {
                        if (loadingMessage) {
                            loadingMessage.style.display = 'none';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch class data.',
                        });
                    }
                })
                .catch(error => {
                    if (loadingMessage) {
                        loadingMessage.style.display = 'none';
                    }
                    console.error('Error during the AJAX request:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching class data.',
                    });
                });
            document.getElementById('confirm-modal').style.display = 'none';
        };
        document.getElementById('cancel-btn').onclick = function() {
            document.getElementById('confirm-modal').style.display = 'none';
        };
    });
});

                document.querySelectorAll('.close-btn').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const classId = e.target.getAttribute('data-id');
                        const slotNumber = e.target.closest('.time-slot').getAttribute('data-slot');
                        document.getElementById('confirm-modal').style.display = 'flex';
                        document.querySelector('#confirm-modal p').innerText =
                            `Are you sure you want to delete this item from Slot ${slotNumber}?`;
                        document.getElementById('confirm-btn').onclick = function() {
                            const formattedDate = currentDate.toISOString().split('T')[0];

                            const url = `{{ url('delete-general') }}/${classId}?date=${formattedDate}`;
                            fetch(url, {
                                method: 'GET'
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const classItemElement = e.target.closest('div');
                                        if (classItemElement) classItemElement.remove();
                                        fetchTimetable(currentDate);
                                    } else {
                                        console.error('Error deleting data:', data.message);
                                    }
                                })
                                .catch(error => console.error('Error deleting data:', error));
                            document.getElementById('confirm-modal').style.display = 'none';
                        };
                        document.getElementById('cancel-btn').onclick = function() {
                            document.getElementById('confirm-modal').style.display = 'none';
                        };
                    });
                });
            };

            function resetModalFields() {
                $('#teacher').val('').trigger('change');
                document.querySelector('#class-modal .modal-header h3').textContent = 'Create Class';
                for (let i = 1; i <= 10; i++) {
                    document.getElementById(`student-${i}`).value = '';
                    document.getElementById(`yearinschool-${i}`).value = '';
                    document.getElementById(`subject-${i}-2`).value = 'select subject';
                    document.getElementById(`book-${i}`).innerHTML = '<option disabled selected>Book</option>';
                    document.getElementById(`ch-${i}`).value = '';
                    document.getElementById(`behaviour-${i}`).value = '';
                    document.getElementById(`performance-${i}`).value = '';

                    const attendanceCheckbox = document.getElementById(`checkbox-${i}`);
                    const attendanceLabel = attendanceCheckbox?.nextElementSibling?.querySelector('span');
                    if (attendanceCheckbox && attendanceLabel) {
                        attendanceCheckbox.checked = false;
                        attendanceCheckbox.value = 'No';
                        attendanceCheckbox.disabled = true;
                        attendanceLabel.style.backgroundColor = '#fff';
                        attendanceLabel.style.borderColor = '#ccc';
                        attendanceLabel.style.cursor = 'not-allowed';
                    }

                    const homeworkCheckbox = document.getElementById(`homework-${i}`);
                    const homeworkLabel = homeworkCheckbox?.nextElementSibling?.querySelector('span');
                    if (homeworkCheckbox && homeworkLabel) {
                        homeworkCheckbox.checked = false;
                        homeworkCheckbox.value = 'No';
                        homeworkLabel.style.backgroundColor = '#fff';
                        homeworkLabel.style.borderColor = '#ccc';
                    }

                    const permanentCheckbox = document.getElementById(`permanent-${i}`);
                    const permanentLabel = permanentCheckbox?.nextElementSibling?.querySelector('span');
                    if (permanentCheckbox && permanentLabel) {
                        permanentCheckbox.checked = true;
                        permanentCheckbox.value = 'Yes';
                        permanentLabel.style.backgroundColor = '#4CAF50';
                        permanentLabel.style.borderColor = '#4CAF50';
                    }
                }

                // Hide additional info container for Create Class
                const additionalInfoContainer = document.getElementById('additional-info-container');
                if (additionalInfoContainer) {
                    additionalInfoContainer.style.display = 'none';
                    const additionalInfoCheckbox = document.getElementById('additional-info-checkbox');
                    const additionalInfoInput = document.getElementById('additional-info-input');
                    if (additionalInfoCheckbox && additionalInfoInput) {
                        additionalInfoCheckbox.checked = false;
                        additionalInfoCheckbox.value = 'No';
                        additionalInfoInput.style.display = 'none';
                        additionalInfoInput.value = '';
                    }
                }

                document.getElementById('selected-day').value = '';
                document.getElementById('selected-slot').value = '';
                const classIdInput = document.getElementById('class-id');
                if (classIdInput) classIdInput.value = '';
            }

            closeClassModalButton.addEventListener('click', () => {
                resetModalFields();
                classModal.style.display = 'none';
                classModal.classList.remove('show');
                overlay.style.display = 'none';
            });

            document.getElementById('prev-day').addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() - 1);
                updateCalendar();
            });

            document.getElementById('next-day').addEventListener('click', function() {
                currentDate.setDate(currentDate.getDate() + 1);
                updateCalendar();
            });

            saveClassButton.addEventListener('click', () => {
                const teacher = $('#teacher').val();
                if (!teacher || teacher === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Teacher Required',
                        text: 'Please select a teacher before saving the class.',
                    });
                    return;
                }
                const students = [];
                const yearinschools = [];
                const subjects = [];
                const books = [];
                const chapters = [];
                const attendance = [];
                const homeworks = [];
                const behaviours = [];
                const performances = [];
                const permanents = [];
                const additionalInfoContainer = document.getElementById('additional-info-container');
                let additionalInfo = '';

                // Only include additional_info if the container is visible (Update mode)
                if (additionalInfoContainer && additionalInfoContainer.style.display === 'block') {
                    const additionalInfoCheckbox = document.getElementById('additional-info-checkbox').value;
                    additionalInfo = additionalInfoCheckbox === 'Yes' ? document.getElementById('additional-info-input').value : '';
                }

                for (let i = 1; i <= 10; i++) {
                    const student = document.getElementById(`student-${i}`).value;
                    const yearinschool = document.getElementById(`yearinschool-${i}`).value;
                    const subject = document.getElementById(`subject-${i}-2`).value;
                    const book = document.getElementById(`book-${i}`).value;
                    const chapter = document.getElementById(`ch-${i}`).value.trim();
                    const attendanceCheckbox = document.getElementById(`checkbox-${i}`);
                    const homeworkCheckbox = document.getElementById(`homework-${i}`);
                    const behaviourSelect = document.getElementById(`behaviour-${i}`);
                    const performanceSelect = document.getElementById(`performance-${i}`);
                    const permanentCheckbox = document.getElementById(`permanent-${i}`);

                    const attendanceValue = attendanceCheckbox ? attendanceCheckbox.value : 'No';
                    attendance.push(attendanceValue);

                    const homeworkValue = homeworkCheckbox ? homeworkCheckbox.value : 'No';
                    homeworks.push(homeworkValue);

                    const behaviourValue = behaviourSelect ? behaviourSelect.value : '';
                    behaviours.push(behaviourValue);

                    const performanceValue = performanceSelect ? performanceSelect.value : '';
                    performances.push(performanceValue);

                    if (chapter === "" || chapter.toLowerCase() === "null") {
                        chapters.push(null);
                    } else {
                        chapters.push(chapter);
                    }

                    books.push(book && book !== 'Book' ? book : null);
                    yearinschools.push(yearinschool && yearinschool.trim() !== '' ? yearinschool : null);

                    if (student) students.push(student);
                    if (subject && subject !== 'select subject') subjects.push(subject);

                    const permanentValue = permanentCheckbox.checked ? 'Yes' : 'No';
                    permanents.push(permanentValue);
                }

                const classId = document.getElementById('class-id').value;
                const data = new URLSearchParams({
                    'selected-day': currentDate.toISOString().split('T')[0],
                    'selected-slot': document.getElementById('selected-slot').value,
                    'teacher': teacher,
                    'students': JSON.stringify(students),
                    'yearinschools': JSON.stringify(yearinschools),
                    'subjects': JSON.stringify(subjects),
                    'books': JSON.stringify(books),
                    'chapters': JSON.stringify(chapters),
                    'attendance': JSON.stringify(attendance),
                    'homeworks': JSON.stringify(homeworks),
                    'behaviours': JSON.stringify(behaviours),
                    'performances': JSON.stringify(performances),
                    'permanents': JSON.stringify(permanents),
                    'additional_info': additionalInfo
                });

                const additionalStudent = document.getElementById('additional_student').value;
                if (additionalStudent === 'yes') data.append('additional_student', additionalStudent);
                if (classId) data.append('class-id', classId);

                const loadingMessage = document.getElementById('loadingMessage');
                loadingMessage.style.display = 'flex';

                fetch(`{{ url('store-generalTimetable') }}?${data.toString()}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('additional_student').value = '';
                        classModal.style.display = 'none';
                        classModal.classList.remove('show');
                        overlay.style.display = 'none';
                        fetchTimetable(currentDate);
                    })
                    .catch(error => {
                        document.getElementById('additional_student').value = '';
                        console.error('Error saving class:', error);
                        fetchTimetable(currentDate);
                        resetModalFields();
                    })
                    .finally(() => loadingMessage.style.display = 'none');
            });

            const typingTimerInterval = 500;
            let typingTimer;

            $('#teacher').on('change', function() {
                const selectedTeacher = $(this).val();
                const selectedSlotValue = document.getElementById("selected-slot").value;
                const currentSlot = document.querySelector(`.time-slot[data-slot="${selectedSlotValue}"]`);
                if (currentSlot) {
                    const teachers = currentSlot.querySelectorAll(".teacher");
                    let teacherExists = false;
                    teachers.forEach((teacher) => {
                        if (teacher.textContent.trim() === selectedTeacher) teacherExists = true;
                    });
                    if (teacherExists) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Teacher Conflict',
                            text: `Teacher ${selectedTeacher} already exists in slot ${selectedSlotValue}`,
                        });
                        $('#teacher').val('').trigger('change');
                    }
                }
            });

         for (let i = 1; i <= 10; i++) {
    const studentInput = document.getElementById(`student-${i}`);
    const dropdown = document.getElementById(`student-${i}-dropdown`);
    const subjectSelect = document.getElementById(`subject-${i}-2`); // Get the subject dropdown

    studentInput.addEventListener("input", function() {
        clearTimeout(typingTimer);
        const studentValue = studentInput.value.trim();
        if (studentValue.length > 0) {
            typingTimer = setTimeout(() => {
                const url = `{{ url('get-general-student-name') }}/${i}?query=${encodeURIComponent(studentValue)}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = "";
                        if (data.length > 0) {
                            dropdown.style.display = "block";
                            data.forEach(student => {
                                const option = document.createElement("a");
                                option.classList.add("dropdown-item");

                                let displayParts = [student.admissionid];
                                if (student.studentname && student.studentname.trim()) {
                                    displayParts.push(student.studentname);
                                }
                                if (student.studentsur && student.studentsur.trim()) {
                                    displayParts.push(student.studentsur);
                                }
                                option.textContent = displayParts.join(" - ");

                                option.addEventListener("click", function() {
                                    let inputParts = [student.admissionid];
                                    if (student.studentname && student.studentname.trim()) {
                                        inputParts.push(student.studentname);
                                    }
                                    if (student.studentsur && student.studentsur.trim()) {
                                        inputParts.push(student.studentsur);
                                    }
                                    studentInput.value = inputParts.join(" ");

                                    document.getElementById(`yearinschool-${i}`).value = student.studentyearinschool || '';

                                    // Update the subject dropdown with student's subjects
                                    const subjectNames = JSON.parse(student.subject_names || "[]");
                                    updateSubjectDropdown(subjectSelect, subjectNames);

                                    dropdown.style.display = "none";

                                    // Check for student conflict in the slot
                                    const selectedSlotValue = document.getElementById("selected-slot").value;
                                    const currentSlot = document.querySelector(`.time-slot[data-slot="${selectedSlotValue}"]`);
                                    if (currentSlot) {
                                        const mergedInfoContainers = currentSlot.querySelectorAll(".merged-info-container");
                                        if (mergedInfoContainers.length > 0) {
                                            let allMergedInfo = "";
                                            mergedInfoContainers.forEach(container => {
                                                allMergedInfo += container.innerHTML.trim() + " ";
                                            });
                                            allMergedInfo = allMergedInfo.trim();

                                            if (allMergedInfo.includes(studentInput.value)) {
                                                const student = studentInput.value;
                                                const selectedSlot = selectedSlotValue;
                                                Swal.fire({
                                                    title: 'Student already in slot!',
                                                    text: `Student "${student}" is already added in slot ${selectedSlot}. Do you want to move them temporarily to this class?`,
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Yes, move temporarily',
                                                    cancelButtonText: 'No',
                                                }).then(({ isConfirmed }) => {
                                                    if (isConfirmed) {
                                                        const formattedDate = currentDate.toISOString().split('T')[0];
                                                        fetch(`{{ url('re-arrange-student') }}?student=${encodeURIComponent(student)}&slot=${encodeURIComponent(selectedSlot)}&date=${encodeURIComponent(formattedDate)}`)
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                Swal.fire('Success!', 'Student moved temporarily.', 'success');
                                                            })
                                                            .catch(() => {
                                                                Swal.fire('Error!', 'Failed to move student.', 'error');
                                                                studentInput.value = '';
                                                                document.getElementById(`yearinschool-${i}`).value = '';
                                                                dropdown.style.display = 'none';
                                                                subjectSelect.innerHTML = '<option disabled selected>Subject</option>'; // Reset subject dropdown
                                                            });
                                                    } else {
                                                        studentInput.value = '';
                                                        document.getElementById(`yearinschool-${i}`).value = '';
                                                        dropdown.style.display = 'none';
                                                        subjectSelect.innerHTML = '<option disabled selected>Subject</option>'; // Reset subject dropdown
                                                    }
                                                });
                                            }
                                        }
                                    }
                                });
                                dropdown.appendChild(option);
                            });
                        } else {
                            dropdown.style.display = "none";
                            subjectSelect.innerHTML = '<option disabled selected>Subject</option>'; // Reset subject dropdown if no data
                        }
                    })
                    .catch(() => {
                        dropdown.style.display = "none";
                        subjectSelect.innerHTML = '<option disabled selected>Subject</option>'; // Reset subject dropdown on error
                    });
            }, typingTimerInterval);
        } else {
            dropdown.style.display = "none";
            subjectSelect.innerHTML = '<option disabled selected>Subject</option>'; // Reset subject dropdown if input is empty
        }
    });
}

// Function to update the subject dropdown with relevant subjects
function updateSubjectDropdown(subjectSelect, subjectNames, selectedSubject) {
    // Clear existing options
    subjectSelect.innerHTML = '<option disabled selected>Subject</option>';

    // Add subjects from subject_names array
    if (subjectNames && subjectNames.length > 0) {
        subjectNames.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            if (subject === selectedSubject) {
                option.selected = true;
            }
            subjectSelect.appendChild(option);
        });
        subjectSelect.disabled = false;
    } else {
        // If no subjects are available, keep the dropdown with only the default option

    }
}

            const searchBar = document.getElementById('search-bar');
            if (searchBar) {
                searchBar.addEventListener('input', function() {
                    const searchTerm = searchBar.value.trim().toLowerCase();
                    const classInfos = document.querySelectorAll('#calendar-container .time-class-info > div');
                    classInfos.forEach(info => {
                        info.classList.remove('highlight');
                    });
                    if (searchTerm.length > 0) {
                        const searchParts = searchTerm.split(' ').filter(part => part.length > 0);
                        classInfos.forEach(info => {
                            const mergedInfo = info.querySelector('.merged-info-container');
                            if (mergedInfo) {
                                const textContent = mergedInfo.textContent.toLowerCase();
                                let matches = true;
                                for (let part of searchParts) {
                                    if (!textContent.includes(part)) {
                                        matches = false;
                                        break;
                                    }
                                }
                                if (matches) {
                                    info.classList.add('highlight');
                                    info.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        });
                    }
                });
            }

            updateCalendar();
        });

        $(document).ready(function() {
            $('[id^="subject-"]').on('change', function() {
                var subjectValue = $(this).val();
                var currentDropdown = $(this);
                var parentRow = currentDropdown.closest('tr');
                var studentInputValue = parentRow.find('input[id^="student-"]').val();

                $.ajax({
                    url: '{{ url('fetch-bk-ch') }}',
                    type: 'GET',
                    data: {
                        subject: subjectValue,
                        student: studentInputValue
                    },
                    success: function(response) {
                        var nextDropdown = parentRow.find('select').not(currentDropdown).first();
                        nextDropdown.empty();
                        nextDropdown.append('<option disabled selected>Book</option>');
                        $.each(response, function(index, book) {
                            var isSelected = book.is_latest ? 'selected' : '';
                            nextDropdown.append('<option value="' + book.name + '" ' + isSelected + '>' + book.name + '</option>');
                        });
                        var index = parentRow.find('input[id^="student-"]').attr('id').split('-')[1];
                        setTimeout(() => {
                            document.getElementById(`book-${index}`).dispatchEvent(new Event('change'));
                        }, 0);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error occurred: ' + error);
                    }
                });
            });
        });
    </script>


@endsection
