@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('branchFrontend.schedular.timetableSchedulerCss')
    <style>
        .name-input-wrapper {
            position: relative;
        }
        .suggestions-popup {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #d9d9d9;
            border-top: none;
            z-index: 9999;
            max-height: 220px;
            overflow-y: auto;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }
        .suggestions-item {
            padding: 6px 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            background: #fff;
            font-size: 14px;
        }
        .suggestions-item:hover {
            background: #f2f2f2;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Term Break Scheduler</h1>
                <p>Only Slot 7 and Slot 8 are available for term break.</p>
            </div>
        </div>

        <div class="card p-4 shadow-sm border-0">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="date-input" class="form-label fw-semibold">Choose Day</label>
                    <input type="date" id="date-input" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="slot-select" class="form-label fw-semibold">Term Break Slots</label>
                    <select id="slot-select" class="form-select">
                        <option value="">-- Choose slot --</option>
                        <option value="7">11:00 - 01:00 (Term Break Slot 7)</option>
                        <option value="8">01:30 - 03:30 (Term Break Slot 8)</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button id="add-class-btn" class="btn btn-primary" style="display:none;">
                    Add New Class
                </button>
            </div>

            <div id="placeholder" class="mt-4 text-muted">
                Please select a date and slot to view term break classes.
            </div>

            <div id="classes-container" class="mt-4" style="display:none;">
                <div class="row row-cols-1 row-cols-md-2 g-4" id="classes-grid"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date-input');
            const slotSelect = document.getElementById('slot-select');
            const addClassBtn = document.getElementById('add-class-btn');
            const classesGrid = document.getElementById('classes-grid');
            const classesContainer = document.getElementById('classes-container');
            const placeholder = document.getElementById('placeholder');
            let temporaryMoves = [];

            function resetUI() {
                addClassBtn.style.display = 'none';
                classesContainer.style.display = 'none';
                placeholder.style.display = 'block';
                classesGrid.innerHTML = '';
            }

            function renderClassCard(cls = null) {
                const cardId = cls?.class_id || '';
                const studentIds = JSON.parse(cls?.student_ids || '[]');
                const studentNames = JSON.parse(cls?.student_names || '[]');
                const subjects = JSON.parse(cls?.subjects || '[]');
                const years = JSON.parse(cls?.year_in_schools || '[]');
                const subjectOptions = JSON.parse(cls?.subject_options || '[]');
                const teacherOptionsGroup = JSON.parse(cls?.teacher_options || '[]');
                const teacherOptions = teacherOptionsGroup[0] || [];

                let teacherHtml = '<option value="">-- Select Teacher --</option>';
                teacherOptions.forEach(name => {
                    const selected = cls?.teacher_id === name ? 'selected' : '';
                    teacherHtml += `<option value="${name}" ${selected}>${name}</option>`;
                });

                let rows = '';
                for (let i = 0; i < 10; i++) {
                    const id = studentIds[i] || '';
                    const name = studentNames[i] || '';
                    const year = years[i] || '';
                    const currentSubject = subjects[i] || '';
                    const options = Array.isArray(subjectOptions[i]) ? subjectOptions[i] : [];

                    let subjectHtml = '<option value="">-- Select --</option>';
                    options.forEach(opt => {
                        const selected = opt === currentSubject ? 'selected' : '';
                        subjectHtml += `<option value="${opt}" ${selected}>${opt}</option>`;
                    });
                    if (currentSubject && !options.includes(currentSubject)) {
                        subjectHtml += `<option value="${currentSubject}" selected>${currentSubject}</option>`;
                    }

                    rows += `
                        <tr>
                            <td><input type="text" class="form-control form-control-sm id-input" value="${id}" placeholder="Family ID"></td>
                            <td>
                                <div class="name-input-wrapper">
                                    <input type="text" class="form-control form-control-sm name-input" value="${name}" placeholder="Student Name">
                                    <div class="suggestions-popup"></div>
                                </div>
                            </td>
                            <td><select class="form-select form-select-sm subject-dropdown">${subjectHtml}</select></td>
                            <td><input type="text" class="form-control form-control-sm year-input" value="${year}" placeholder="Year"></td>
                        </tr>
                    `;
                }

                const col = document.createElement('div');
                col.className = 'col';
                col.dataset.classId = cardId;
                col.innerHTML = `
                    <div class="class-card" style="background-color:#f7f9fc; border-radius:10px; padding:10px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <select class="teacher-select form-select form-select-sm" style="max-width: 70%;">${teacherHtml}</select>
                            <button class="btn btn-sm btn-danger delete-class-btn">Delete</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>Family ID</th><th>Student Name</th><th>Subject</th><th>Year</th></tr></thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>
                `;

                classesGrid.appendChild(col);
                bindClassEvents(col);
            }

            function bindClassEvents(col) {
                const saveTriggers = col.querySelectorAll('.id-input, .name-input, .subject-dropdown, .year-input');
                saveTriggers.forEach(el => {
                    el.addEventListener('change', () => saveSingleClass(col));
                    if (el.classList.contains('id-input') || el.classList.contains('name-input') || el.classList.contains('year-input')) {
                        el.addEventListener('blur', () => saveSingleClass(col));
                    }
                });

                const teacherSelect = col.querySelector('.teacher-select');
                if (teacherSelect) {
                    teacherSelect.dataset.prevValue = teacherSelect.value || '';

                    teacherSelect.addEventListener('focus', function() {
                        this.dataset.prevValue = this.value || '';
                    });

                    teacherSelect.addEventListener('change', function() {
                        if (!this.value) return;

                        if (hasDuplicateTeacher(col)) {
                            const previousValue = this.dataset.prevValue || '';
                            this.value = previousValue;
                            showDuplicateTeacherAlert().then(() => {
                                this.focus();
                            });
                            return;
                        }

                        this.dataset.prevValue = this.value || '';
                        saveSingleClass(col);
                    });
                }

                col.querySelector('.delete-class-btn').addEventListener('click', async function() {
                    const classId = col.dataset.classId;
                    if (!classId) {
                        col.remove();
                        return;
                    }

                    const selectedDate = dateInput.value;
                    const deleteUrl = `{{ url('/termbreak-scheduler-delete') }}/${classId}?date=${encodeURIComponent(selectedDate)}`;

                    const res = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        toastr.success('Class deleted');
                        fetchClasses();
                    } else {
                        toastr.error(data.message || 'Delete failed');
                    }
                });

                col.querySelectorAll('.id-input').forEach(input => {
                    let debounceTimer = null;

                    input.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const familyId = this.value.trim();
                        const row = this.closest('tr');
                        const popup = row.querySelector('.suggestions-popup');
                        const nameInput = row.querySelector('.name-input');
                        const yearInput = row.querySelector('.year-input');
                        const subjectDropdown = row.querySelector('.subject-dropdown');

                        if (!familyId) {
                            popup.style.display = 'none';
                            nameInput.value = '';
                            yearInput.value = '';
                            subjectDropdown.innerHTML = '<option value="">-- Select --</option>';
                            return;
                        }

                        debounceTimer = setTimeout(async () => {
                            const res = await fetch(`{{ route('termBreakSchedularGetStudents') }}?family_id=${encodeURIComponent(familyId)}`);
                            const payload = await res.json();

                            if (!res.ok && payload.blocked) {
                                popup.style.display = 'none';
                                input.value = '';
                                toastr.error('Family ID Blocked. Please contact admin office for more details.');
                                return;
                            }

                            const students = payload.students || [];

                            popup.innerHTML = '';
                            if (!students.length) {
                                popup.innerHTML = '<div class="text-center text-muted p-2">No student found</div>';
                                popup.style.display = 'block';
                                return;
                            }

                            students.forEach(student => {
                                const item = document.createElement('div');
                                item.className = 'suggestions-item';
                                item.innerHTML = `
                                    <div style="padding:6px 10px; cursor:pointer; border-bottom:1px solid #2b3d5b;
                                                background:#e8eefc; font-size:12px; color:#1a237e; font-weight:500;
                                                white-space:nowrap; overflow:hidden;">
                                        ${student.name || ''}
                                    </div>
                                `;

                                item.addEventListener('click', () => {
                                    const fillStudent = (autoSave = true) => {
                                        nameInput.value = student.name || '';
                                        yearInput.value = student.year || '';
                                        subjectDropdown.innerHTML = '<option value="">-- Select --</option>';
                                        (student.subjects || []).forEach(subject => {
                                            const opt = document.createElement('option');
                                            opt.value = subject;
                                            opt.textContent = subject;
                                            subjectDropdown.appendChild(opt);
                                        });
                                        if ((student.subjects || []).length) {
                                            subjectDropdown.value = student.subjects[0];
                                        }
                                        popup.style.display = 'none';
                                        if (autoSave) {
                                            saveSingleClass(col);
                                        }
                                    };

                                    // Duplicate-student check across all visible class rows
                                    const targetId = String(student.id || '').trim();
                                    const targetName = String(student.name || '').trim().toLowerCase();
                                    let duplicateRow = null;
                                    document.querySelectorAll('#classes-grid tr').forEach(r => {
                                        const rid = (r.querySelector('.id-input')?.value || '').trim();
                                        const rname = (r.querySelector('.name-input')?.value || '').trim().toLowerCase();
                                        if (r !== row && rid === targetId && rname === targetName) {
                                            duplicateRow = r;
                                        }
                                    });

                                    if (!duplicateRow) {
                                        fillStudent();
                                        return;
                                    }

                                    const oldCardCol = duplicateRow.closest('.col');
                                    const oldTeacher = oldCardCol?.querySelector('.teacher-select')?.value || 'N/A';
                                    const oldClassId = oldCardCol?.dataset?.classId || '';
                                    const oldSubject = duplicateRow.querySelector('.subject-dropdown')?.value || '';

                                    showSwal({
                                        title: 'Student Already in Another Class',
                                        html: `<div style="text-align:left;font-size:14px;">
                                            <b>Student:</b> ${student.name || ''}<br>
                                            <b>Family ID:</b> ${student.id || ''}<br>
                                            <b>Old Teacher:</b> ${oldTeacher}<br>
                                            <b>Old Class ID:</b> ${oldClassId || 'N/A'}
                                        </div><hr>Move temporarily to the new class?`,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, Move',
                                        cancelButtonText: 'No'
                                    }).then((result) => {
                                        if (!result.isConfirmed) {
                                            popup.style.display = 'none';
                                            return;
                                        }

                                        temporaryMoves.push({
                                            family_id: String(student.id || '').trim(),
                                            student_name: String(student.name || '').trim(),
                                            old_teacher: oldTeacher,
                                            old_class_id: oldClassId || null,
                                            old_subject: oldSubject || '',
                                            permanent: 'No'
                                        });
                                        fillStudent(false);
                                        saveSingleClass(col);
                                        toastr.info(`${student.name || 'Student'} moved temporarily.`);
                                    });
                                });

                                popup.appendChild(item);
                            });

                            popup.style.display = 'block';
                        }, 250);
                    });

                    input.addEventListener('blur', function() {
                        const row = this.closest('tr');
                        const popup = row.querySelector('.suggestions-popup');
                        setTimeout(() => {
                            popup.style.display = 'none';
                        }, 200);
                    });
                });
            }

            function hasDuplicateTeacher(targetCol) {
                const normalizeTeacher = (value) => String(value || '')
                    .trim()
                    .replace(/\s+/g, ' ')
                    .toLowerCase();
                const targetTeacher = normalizeTeacher(targetCol.querySelector('.teacher-select')?.value);
                if (!targetTeacher) return false;

                let duplicateFound = false;
                document.querySelectorAll('#classes-grid .col').forEach(col => {
                    if (col === targetCol) return;
                    const otherTeacher = normalizeTeacher(col.querySelector('.teacher-select')?.value);
                    if (otherTeacher && otherTeacher === targetTeacher) {
                        duplicateFound = true;
                    }
                });

                return duplicateFound;
            }

            function showSwal(options) {
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    return window.Swal.fire(options);
                }

                // Force-load SweetAlert and then show popup
                return new Promise((resolve) => {
                    const firePopup = () => {
                        if (window.Swal && typeof window.Swal.fire === 'function') {
                            window.Swal.fire(options).then(resolve).catch(() => resolve({ isConfirmed: false }));
                        } else {
                            // Minimal fallback (no crash)
                            if (window.toastr) {
                                window.toastr.error('Popup library unavailable.');
                            }
                            resolve({ isConfirmed: false });
                        }
                    };

                    const existingLoader = document.querySelector('script[data-swal-loader="termbreak"]');
                    if (existingLoader) {
                        if (window.Swal) {
                            firePopup();
                            return;
                        }
                        existingLoader.addEventListener('load', firePopup, { once: true });
                        existingLoader.addEventListener('error', () => resolve({ isConfirmed: false }), { once: true });
                        return;
                    }

                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                    script.async = true;
                    script.dataset.swalLoader = 'termbreak';
                    script.onload = firePopup;
                    script.onerror = () => resolve({ isConfirmed: false });
                    document.head.appendChild(script);
                });
            }

            function showDuplicateTeacherAlert() {
                return showSwal({
                        icon: 'error',
                        title: 'Duplicate Teacher!',
                        text: 'This teacher is already assigned to another class in this slot.',
                        confirmButtonText: 'OK'
                });
            }

            async function fetchTeachers() {
                const res = await fetch(`{{ route('termBreakSchedularGetTeachers') }}`);
                const data = await res.json();
                return (data.teachers || []).map(t => t.name);
            }

            async function fetchClasses() {
                const date = dateInput.value;
                const slot = slotSelect.value;
                if (!date || !slot) return;

                const res = await fetch(`{{ route('termBreakSchedularGet') }}?date=${date}&slot=${slot}`);
                const data = await res.json();
                classesGrid.innerHTML = '';
                (data.classes || []).forEach(cls => renderClassCard(cls));

                classesContainer.style.display = 'block';
                placeholder.style.display = (data.classes || []).length ? 'none' : 'block';
            }

            async function saveSingleClass(col) {
                const date = dateInput.value;
                const slot = slotSelect.value;
                if (!date || !slot) return;

                const teacher = col.querySelector('.teacher-select')?.value || '';
                if (!teacher) return;

                if (hasDuplicateTeacher(col)) {
                    showDuplicateTeacherAlert();
                    return;
                }

                const student_ids = [];
                const student_names = [];
                const subjects = [];
                const year_in_schools = [];

                col.querySelectorAll('tbody tr').forEach(row => {
                    const id = row.querySelector('.id-input')?.value.trim();
                    const name = row.querySelector('.name-input')?.value.trim();
                    const subject = row.querySelector('.subject-dropdown')?.value || '';
                    const year = row.querySelector('.year-input')?.value.trim() || '';
                    if (id && name) {
                        student_ids.push(id);
                        student_names.push(name);
                        subjects.push(subject);
                        year_in_schools.push(year);
                    }
                });

                const payload = {
                    date,
                    slot,
                    class_id: col.dataset.classId || null,
                    teacher_id: teacher,
                    student_ids,
                    student_names,
                    subjects,
                    year_in_schools,
                    temporary_moves: temporaryMoves.filter(move => student_ids.includes(move.family_id))
                };

                const res = await fetch(`{{ route('termBreakSchedularSaveSingle') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    if (data.class_id) {
                        col.dataset.classId = data.class_id;
                    }
                    toastr.success('Saved');
                } else {
                    toastr.error(data.error || 'Save failed');
                }
            }

            addClassBtn.addEventListener('click', async function() {
                const teachers = await fetchTeachers();
                renderClassCard({
                    class_id: '',
                    teacher_id: '',
                    student_ids: '[]',
                    student_names: '[]',
                    subjects: '[]',
                    year_in_schools: '[]',
                    subject_options: JSON.stringify(Array.from({ length: 10 }, () => [])),
                    teacher_options: JSON.stringify([teachers]),
                });
                classesContainer.style.display = 'block';
                placeholder.style.display = 'none';
            });

            dateInput.addEventListener('change', function() {
                if (dateInput.value && slotSelect.value) {
                    addClassBtn.style.display = 'inline-block';
                    fetchClasses();
                } else {
                    resetUI();
                }
            });

            slotSelect.addEventListener('change', function() {
                if (dateInput.value && slotSelect.value) {
                    addClassBtn.style.display = 'inline-block';
                    fetchClasses();
                } else {
                    resetUI();
                }
            });

            dateInput.value = new Date().toISOString().split('T')[0];

            resetUI();
        });
    </script>
@endsection
