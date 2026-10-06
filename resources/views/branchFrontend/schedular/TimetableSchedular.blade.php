@extends('layouts.branchDashboardApp')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('branchFrontend.schedular.timetableSchedulerCss')

    <style>
    /* highlight row if student has medical condition */
    .medical-row {
        background-color: #ffd6d6 !important;
    }
    /* ensure cells also turn red inside colored cards/tables */
    tr.medical-row > td {
        background-color: #ffd6d6 !important;
    }
    /* ensure controls inherit red background */
    tr.medical-row input.form-control,
    tr.medical-row select.form-select,
    tr.medical-row .form-control,
    tr.medical-row .form-select {
        background-color: #ffd6d6 !important;
    }
    
    /* Bulk Operations Styling */
    #bulk-operations {
        padding: 12px 18px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .class-checkbox {
        width: 20px !important;
        height: 20px !important;
        cursor: pointer;
        accent-color: #0d6efd;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
        border: 2px solid #dee2e6;
    }
    
    .class-checkbox:hover {
        transform: scale(1.15);
        box-shadow: 0 3px 6px rgba(13, 110, 253, 0.3);
        border-color: #0d6efd;
    }
    
    .class-checkbox:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.4);
    }
    
    .class-checkbox:focus {
        outline: 2px solid rgba(13, 110, 253, 0.3);
        outline-offset: 2px;
    }
    
    .class-card {
        transition: all 0.3s ease;
    }
    
    .class-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    
    #delete-selected-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    #bulk-operations .btn {
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    #bulk-operations .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    </style>

@endsection

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Select Date & Slot</h1>
                <p>Choose a date – slots will appear according to weekday/weekend.</p>
            </div>
        </div>
        <!-- 🔍 Search Section (hidden until slot selected) -->
<div id="search-section" class="container mb-4" style="display: none;">
    <div class="row align-items-center">
        <div class="col-md-3">
            <select id="searchType" class="form-select form-select-sm">
                <option value="teacher">Search by Teacher Name</option>
                <option value="family">Search by Family ID</option>
                <option value="student">Search by Student Name</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Enter search text...">
        </div>
        <div class="col-md-3 text-end">
            <button id="searchBtn" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> Search
            </button>
            <button id="clearSearchBtn" class="btn btn-outline-secondary btn-sm" style="display:none;">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>
</div>


        <div class="card p-4 shadow-sm border-0">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="date-input" class="form-label fw-semibold">Choose Day</label>
                    <input type="date" id="date-input" class="form-control" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="slot-select" class="form-label fw-semibold">Available Slots</label>
                    <select id="slot-select" class="form-select">
                        <option value="">-- Select a date first --</option>
                    </select>
                </div>
            </div>

            <div id="selected-info" class="alert alert-info mt-4" style="display:none;">
                <strong>Selected Date:</strong> <span id="show-date"></span><br>
                <strong>Selected Slot:</strong> <span id="show-slot"></span>
            </div>

            <!-- Better button alignment -->
            <div class="d-flex justify-content-between align-items-center gap-3 mt-4 flex-wrap">
                <!-- Bulk Operations Section -->
                <div id="bulk-operations" class="d-flex align-items-center gap-2" style="display: none !important;">
                    <button id="select-all-btn" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-check-square me-1"></i> Select All
                    </button>
                    <button id="deselect-all-btn" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-square me-1"></i> Deselect All
                    </button>
                    <button id="delete-selected-btn" class="btn btn-danger btn-sm" disabled>
                        <i class="fas fa-trash-alt me-1"></i> Delete Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
                <div class="d-flex gap-2 ms-auto">
                    <button id="add-class-btn" class="btn btn-primary" style="display:none;">
                        <i class="bi bi-plus-circle me-1"></i> Add New Class
                    </button>
                    {{-- <button id="save-classes-btn" class="btn btn-success" style="display:none;">
                        <i class="bi bi-save2 me-1"></i> Save All Classes
                    </button> --}}
                </div>
            </div>

            <!-- Centered loading spinner -->
            <div id="loading" class="mt-4 text-center" style="display:none;">
                <div class="spinner-border text-primary" role="status" style="width:2.2rem;height:2.2rem;"></div>
                <p class="mt-2 mb-0">Loading classes...</p>
            </div>

            <div id="placeholder" class="mt-4 text-muted">
                Please select a date and slot to view classes.
            </div>
           <h2 id="dayContainer" style="display: none; text-align: center; margin-top: 20px;">
  <strong> <span id="show-day" style="font-size: 30px;"></span></strong>
</h2>
            <div id="classes-container" class="mt-4" style="display:none;">
                <div class="row row-cols-1 row-cols-md-3 g-4" id="classes-grid"></div>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Immediately hide bulk operations on page load (before DOMContentLoaded)
        (function() {
            const bulkOps = document.getElementById('bulk-operations');
            if (bulkOps) {
                bulkOps.style.setProperty('display', 'none', 'important');
            }
        })();
    </script>
    <script>
        let currentFetchController = null;
        let isAddingClass = false;
        let currentRequestKey = null; // Track current request to prevent duplicates
        let pendingRequestKey = null; // Track pending request

        // Subject color mapping function - returns unique lite color for each unique subject
        // Each different subject gets a different color, same subject always gets same color
        function getSubjectColor(subject) {
            if (!subject) return '#f8f9fa'; // default light gray
            
            // Normalize subject name (remove extra spaces, convert to lowercase)
            const normalizedSubject = subject.toLowerCase().trim().replace(/\s+/g, ' ');
            
            // Large palette of very lite/pastel colors - each unique subject gets unique color
            const liteColors = [
                '#e8f5e9',  // Very lite green
                '#fce4ec',  // Very lite pink
                '#e0f2f1',  // Very lite teal
                '#fff9e6',  // Very lite yellow
                '#e3f2fd',  // Very lite blue
                '#f3e5f5',  // Very lite purple
                '#ede7f6',  // Very lite lavender
                '#fff9c4',  // Very lite yellow
                '#e1f5fe',  // Very lite cyan
                '#f1f8e9',  // Very lite lime
                '#fce4ec',  // Very lite rose
                '#e8eaf6',  // Very lite indigo
                '#fff3e0',  // Very lite orange
                '#f3e5f5',  // Very lite violet
                '#e0f7fa',  // Very lite aqua
                '#f9fbe7',  // Very lite light green
                '#fce4ec',  // Very lite pink
                '#e8f5e9',  // Very lite mint
                '#fff8e1',  // Very lite amber
                '#e1bee7',  // Very lite light purple
                '#b2dfdb',  // Very lite teal
                '#c5e1a5',  // Very lite light green
                '#ffccbc',  // Very lite peach
                '#d1c4e9',  // Very lite light purple
                '#b39ddb',  // Very lite purple
                '#90caf9',  // Very lite blue
                '#81d4fa',  // Very lite light blue
                '#80deea',  // Very lite cyan
                '#80cbc4',  // Very lite teal
                '#a5d6a7',  // Very lite green
                '#c8e6c9',  // Very lite light green
                '#dcedc8',  // Very lite lime
                '#f0f4c3',  // Very lite lime yellow
                '#fff9c4',  // Very lite yellow
                '#ffe082',  // Very lite amber
                '#ffcc80',  // Very lite orange
                '#ffab91',  // Very lite deep orange
                '#ffb3ba',  // Very lite pink
                '#ffdfba',  // Very lite peach
                '#ffffba',  // Very lite yellow
                '#baffc9',  // Very lite mint
                '#bae1ff',  // Very lite sky blue
                '#ffb3d9',  // Very lite pink
                '#c9c9ff',  // Very lite lavender
                '#d4a5ff',  // Very lite purple
                '#ffcccb',  // Very lite light pink
            ];
            
            // Create consistent hash from normalized subject name
            // This ensures same subject always gets same color
            let hash = 0;
            for (let i = 0; i < normalizedSubject.length; i++) {
                hash = normalizedSubject.charCodeAt(i) + ((hash << 5) - hash);
                hash = hash & hash; // Convert to 32bit integer
            }
            
            // Use absolute value and modulo to get index
            const colorIndex = Math.abs(hash) % liteColors.length;
            return liteColors[colorIndex];
        }

        // Helper function to apply colors to all subject dropdown options
        function applyColorsToSubjectDropdowns() {
            document.querySelectorAll('.subject-dropdown').forEach(dropdown => {
                Array.from(dropdown.options).forEach(option => {
                    if (option.value && option.value !== '') {
                        const color = getSubjectColor(option.value);
                        option.style.backgroundColor = color;
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let temporaryMoves = [];
            const SLOTS = {
                weekday: [{
                        value: '1',
                        label: '11:00 - 01:00 (Lesson 1)'
                    },
                    {
                        value: '2',
                        label: '01:30 - 03:30 (Lesson 2)'
                    },
                    {
                        value: '3',
                        label: '04:30 - 06:30 (Lesson 3)'
                    },
                    {
                        value: '4',
                        label: '06:45 - 08:45 (Lesson 4)'
                    }
                ],
                weekend: [{
                        value: '1',
                        label: '09:00 - 11:00 (Lesson 1)'
                    },
                    {
                        value: '2',
                        label: '11:20 - 01:20 (Lesson 2)'
                    },
                    {
                        value: '3',
                        label: '02:00 - 04:00 (Lesson 3)'
                    }
                ]
            };

            // DOM Elements
            const dateInput = document.getElementById('date-input');
            const slotSelect = document.getElementById('slot-select');
            const selectedInfo = document.getElementById('selected-info');
            const addClassBtn = document.getElementById('add-class-btn');
            const saveClassesBtn = document.getElementById('save-classes-btn');
            const showDate = document.getElementById('show-date');
            const showSlot = document.getElementById('show-slot');
            const loading = document.getElementById('loading');
            const placeholder = document.getElementById('placeholder');
            const classesContainer = document.getElementById('classes-container');
            const classesGrid = document.getElementById('classes-grid');
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;

            /* --------------------------------------------------------------
               UI Helpers
            -------------------------------------------------------------- */
            function resetUI() {
                selectedInfo.style.display = 'none';
                addClassBtn.style.display = 'none';
                if (saveClassesBtn) saveClassesBtn.style.display = 'none';
                loading.style.display = 'none';
                placeholder.style.display = 'block';
                classesContainer.style.display = 'none';
                classesGrid.innerHTML = '';
                document.querySelectorAll('.suggestions-popup').forEach(p => p.style.display = 'none');
            }

            function updateSlots() {
                const val = dateInput.value;
                if (!val) {
                    slotSelect.innerHTML = '<option value="">-- Select a date first --</option>';
                    slotSelect.disabled = true;
                    resetUI();
                    return;
                }
                const day = new Date(val).getDay();
                const isWeekday = day >= 1 && day <= 5;
                const slots = isWeekday ? SLOTS.weekday : SLOTS.weekend;
                slotSelect.innerHTML = '<option value="">-- Choose a slot --</option>';
                slots.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.value;
                    opt.textContent = s.label;
                    slotSelect.appendChild(opt);
                });
                slotSelect.disabled = false;
            }

            function showSelected() {
                if (dateInput.value && slotSelect.value) {
                    const dateStr = new Date(dateInput.value).toLocaleDateString('en-GB', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    showDate.textContent = dateStr;
                    showSlot.textContent = slotSelect.options[slotSelect.selectedIndex].text;
                    selectedInfo.style.display = 'block';
                    addClassBtn.style.display = 'block';
                    if (saveClassesBtn) saveClassesBtn.style.display = 'block';
                    placeholder.style.display = 'none';
                    fetchClasses();
                } else {
                    resetUI();
                }
            }

            /* --------------------------------------------------------------
               Fetch Classes from Backend
            -------------------------------------------------------------- */
          function fetchClasses() {
    const date = dateInput.value;
    const slot = slotSelect.value;

     const showDay = document.getElementById('show-day');
      const dayContainer = document.getElementById('dayContainer');

     const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const selectedDate = new Date(date);
    const dayName = days[selectedDate.getDay()];

    // Day show karna aur container visible karna
    showDay.textContent = dayName;
    dayContainer.style.display = "block";


    if (!date || !slot) return;

    // ✅ Request deduplication: Check if same request is already pending
    const requestKey = `${date}_${slot}`;
    if (pendingRequestKey === requestKey) {
        console.log('Duplicate request prevented:', requestKey);
        return; // Don't make duplicate request
    }

    // Cancel previous fetch if it's still running
    if (currentFetchController) {
        currentFetchController.abort();
    }

    // ✅ Mark this request as pending
    pendingRequestKey = requestKey;
    currentRequestKey = requestKey;

    currentFetchController = new AbortController();
    const signal = currentFetchController.signal;

    // Disable inputs while loading
    slotSelect.disabled = true;
    dateInput.disabled = true;

    // 🔥 Show SweetAlert loader
    Swal.fire({
        title: 'Please wait...',
        text: 'Loading classes for the selected slot and date.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const url = `{{ route('timeTableSchedularGet') }}?date=${date}&slot=${slot}`;

    fetch(url, { signal })
        .then(response => response.json())
        .then(data => {
            // ✅ Check if this response is still valid (not stale)
            if (signal.aborted || currentRequestKey !== requestKey) {
                console.log('Stale response ignored:', requestKey);
                return;
            }

            Swal.close(); // ✅ Close the loader popup
            slotSelect.disabled = false;
            dateInput.disabled = false;

            // ✅ Clear pending request key
            pendingRequestKey = null;

            const classes = data.classes || [];

            // ✅ Clear existing cards first
            classesGrid.innerHTML = '';

            if (classes.length === 0) {
                classesGrid.innerHTML = `
                    <div class="col-12 text-center text-muted p-5">
                        No classes found for this slot.
                    </div>`;
                classesContainer.style.display = 'block';
                return;
            }

            // ✅ Track added class_ids to prevent duplicates
            const addedClassIds = new Set();

            // Render classes (your existing logic)
            classes.forEach(cls => {
                const classId = cls.class_id || '';

                // ✅ Skip if this class_id already added (prevent duplicates)
                if (classId && addedClassIds.has(classId)) {
                    console.warn('Duplicate class_id skipped:', classId);
                    return;
                }

                // ✅ Mark this class_id as added
                if (classId) {
                    addedClassIds.add(classId);
                }
                const studentIds = JSON.parse(cls.student_ids || '[]');
                const studentNames = JSON.parse(cls.student_names || '[]');
                const subjects = JSON.parse(cls.subjects || '[]');
                const yearInSchools = JSON.parse(cls.year_in_schools || '[]');
                const subjectOptions = JSON.parse(cls.subject_options || '[]');
                const teacherOptionsPer = JSON.parse(cls.teacher_options || '[]');
                const medicalFlags = JSON.parse(cls.student_medicals || '[]'); // boolean array aligned by index

                let rowsHTML = '';
                const totalRows = 10;
                for (let i = 0; i < totalRows; i++) {
                    const hasData = i < studentIds.length;
                    const id = hasData ? studentIds[i] : '';
                    const name = hasData ? (studentNames[i] || '') : '';
                    const subj = hasData ? (subjects[i] || '') : '';
                    const year = hasData ? (yearInSchools[i] || '') : '';
                    const medical = hasData ? !!medicalFlags[i] : false;

                    let subjDD = '<option value="">-- Select --</option>';
                    const options = Array.isArray(subjectOptions[i]) ? subjectOptions[i] : [];
                    options.forEach(opt => {
                        const selected = subj && opt === subj ? 'selected' : '';
                        const color = getSubjectColor(opt);
                        subjDD += `<option value="${opt}" ${selected} style="background-color: ${color};">${opt}</option>`;
                    });
                    if (hasData && subj && subj !== '' && !options.includes(subj)) {
                        const color = getSubjectColor(subj);
                        subjDD += `<option value="${subj}" selected style="background-color: ${color};">${subj}</option>`;
                    }

                    rowsHTML += `
                        <tr class="${medical ? 'medical-row' : ''}" ${medical ? 'style="background-color:#ffd6d6;"' : ''}>
                            <td ${medical ? 'style="background-color:#ffd6d6;"' : ''}>
                                <input type="text" class="form-control form-control-sm id-input" ${medical ? 'style="background-color:#ffd6d6;"' : ''} value="${id}" placeholder="Family ID">
                            </td>
                            <td style="position:relative; ${medical ? 'background-color:#ffd6d6;' : ''}">
                                <div class="name-input-wrapper">
                                    <input type="text" class="form-control form-control-sm name-input" ${medical ? 'style="background-color:#ffd6d6;"' : ''} value="${name}" placeholder="Student Name">
                                    <div class="suggestions-popup" style="display:none;"><div class="suggestions-list"></div></div>
                                </div>
                            </td>
                            <td ${medical ? 'style="background-color:#ffd6d6;"' : ''}>
                                <select class="form-select form-select-sm subject-dropdown" ${medical ? 'style="background-color:#ffd6d6;"' : ''}>${subjDD}</select>
                            </td>
                            <td ${medical ? 'style="background-color:#ffd6d6;"' : ''}>
                                <input type="text" class="form-control form-control-sm year-input" ${medical ? 'style="background-color:#ffd6d6;"' : ''} value="${year}">
                            </td>
                        </tr>`;
                }

                let teacherDD = '<option value="">-- Select Teacher --</option>';
                const allTeachers = new Set();
                teacherOptionsPer.forEach(arr => arr.forEach(t => allTeachers.add(t)));
                allTeachers.forEach(t => {
                    const sel = t === cls.teacher_id ? 'selected' : '';
                    teacherDD += `<option value="${t}" ${sel}>${t}</option>`;
                });
                // ✅ Check if card with same class_id already exists in DOM (extra safety)
                if (classId) {
                    const existingCard = classesGrid.querySelector(`[data-class-id="${classId}"]`);
                    if (existingCard) {
                        console.warn('Card with class_id already exists in DOM, skipping:', classId);
                        return;
                    }
                }

                const card = document.createElement('div');
                card.className = 'col';
                card.dataset.classId = classId;
                card.dataset.existing = 'true';

                // 🎨 Slot color map
                const slotColors = {
                    "1": "#d4edda", // light green
                    "2": "#ffe0f0", // clearer pink
                    "3": "#ffb3b3", // clearer red
                    "4": "#fff3cd"  // light orange/yellow
                };
                const slotColor = slotColors[slot] || "#ffffff"; // default white

                card.innerHTML = `
                    <div class="class-card" style="background-color: ${slotColor}; border-radius: 10px; padding: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); position: relative;">
                        <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
                            <input type="checkbox" class="class-checkbox" data-class-id="${classId}" title="Select this class for bulk operations">
                        </div>
                        <div class="card-header">
                            <select class="teacher-select form-select-custom form-select-sm">${teacherDD}</select>
                            <button class="delete-class-btn" title="Delete this class" data-class-id="${cls.class_id}" data-date="${date}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>Family ID</th><th>Student Name</th><th>Subject</th><th>Year</th></tr></thead>
                                <tbody>${rowsHTML}</tbody>
                            </table>
                        </div>
                    </div>`;

                classesGrid.appendChild(card);

            });

            classesContainer.style.display = 'block';
            attachPopupSuggestions();
            attachTeacherDuplicateCheck();
            applyColorsToSubjectDropdowns(); // Apply colors to all subject dropdowns
            
            // Initialize bulk operations (but keep hidden until checkbox is selected)
            setTimeout(() => {
                if (typeof initializeBulkOperations === 'function') {
                    initializeBulkOperations();
                }
            }, 100);
        })
        .catch(err => {
            if (err.name === 'AbortError') {
                // ✅ Request was aborted, clear pending key
                if (currentRequestKey === requestKey) {
                    pendingRequestKey = null;
                }
                return;
            }

            // ✅ Check if this error is for current request
            if (currentRequestKey !== requestKey) {
                console.log('Stale error ignored:', requestKey);
                return;
            }

            // ✅ Clear pending request key on error
            pendingRequestKey = null;

            Swal.close();
            slotSelect.disabled = false;
            dateInput.disabled = false;
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Failed to load classes. Please try again!'
            });
            console.error(err);
        });
}

            /* --------------------------------------------------------------
               Prevent Duplicate Teacher Selection
            -------------------------------------------------------------- */
            function attachTeacherDuplicateCheck() {
                document.removeEventListener('change', handleTeacherChange);
                document.addEventListener('change', handleTeacherChange);
            }

            function handleTeacherChange(e) {
                const select = e.target;
                if (!select.classList.contains('teacher-select')) return;

                const selectedValue = select.value;
                if (!selectedValue) return;

                let duplicateFound = false;
                document.querySelectorAll('.teacher-select').forEach(other => {
                    if (other !== select && other.value === selectedValue) {
                        duplicateFound = true;
                    }
                });

                if (duplicateFound) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Duplicate Teacher!',
                        text: 'This teacher is already assigned to another class in this slot.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        select.value = '';
                        select.focus();
                    });
                    return;
                }

                // Auto-save when teacher is changed
                const card = select.closest('.class-card');
                if (card) {
                    setTimeout(() => saveSingleClass(card), 500);
                }
            }

            /* --------------------------------------------------------------
               Create New Class Table
            -------------------------------------------------------------- */
          function createNewClassTable() {
    if (isAddingClass) return; // prevent duplicate trigger
    isAddingClass = true;

    Swal.fire({
        title: 'Add a new class?',
        showCancelButton: true,
        confirmButtonText: 'Yes',
    }).then((result) => {
        if (result.isConfirmed) {
            const card = document.createElement('div');
            card.className = 'col';
            card.dataset.classId = '';
            card.dataset.existing = 'false';

            let rowsHTML = '';
            for (let i = 0; i < 10; i++) {
                rowsHTML += `
                    <tr>
                        <td><input type="text" class="form-control form-control-sm id-input" placeholder="Family ID"></td>
                        <td style="position:relative;">
                            <div class="name-input-wrapper">
                                <input type="text" class="form-control form-control-sm name-input" placeholder="Student Name">
                                <div class="suggestions-popup" style="display:none;"><div class="suggestions-list"></div></div>
                            </div>
                        </td>
                        <td><select class="form-select form-select-sm subject-dropdown"><option value="">-- Select --</option></select></td>
                        <td><input type="text" class="form-control form-control-sm year-input"></td>
                    </tr>`;
            }

            // ✅ Slot-based background color
            const slot = document.getElementById('slot-select').value;
            const slotColors = {
                "1": "#d4edda", // light green
                "2": "#ffe0f0", // clearer pink
                "3": "#ffb3b3", // clearer red
                "4": "#fff3cd"  // light orange
            };
            const slotColor = slotColors[slot] || "#ffffff";

            card.innerHTML = `
                <div class="class-card" style="background-color:${slotColor}; border-radius:10px; padding:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1); position: relative;">
                    <div style="position: absolute; top: 12px; right: 12px; z-index: 10;">
                        <input type="checkbox" class="class-checkbox" data-class-id="" title="Select this class for bulk operations">
                    </div>
                    <div class="card-header">
                        <select class="teacher-select form-select-custom">
                            <option value="">-- Select Teacher --</option>
                        </select>
                        <button class="delete-class-btn" title="Remove Class">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead><tr><th>Family ID</th><th>Student Name</th><th>Subject</th><th>Year</th></tr></thead>
                            <tbody>${rowsHTML}</tbody>
                        </table>
                    </div>
                </div>`;

            classesGrid.insertBefore(card, classesGrid.firstChild);
            classesContainer.style.display = 'block';
            attachPopupSuggestions();
            applyColorsToSubjectDropdowns(); // Apply colors to all subject dropdowns
            
            // Initialize bulk operations (but keep hidden until checkbox is selected)
            setTimeout(() => {
                if (typeof initializeBulkOperations === 'function') {
                    initializeBulkOperations();
                }
            }, 100);

            // ✅ Load teachers
            const branchId = "{{ auth()->user()->branch_id ?? 1 }}";
            fetch(`{{ route('get.teachers') }}?branch_id=${branchId}`)
                .then(r => r.json())
                .then(data => {
                    const teacherSelect = card.querySelector('.teacher-select');
                    if (data.teachers && Array.isArray(data.teachers)) {
                        data.teachers.forEach(t => {
                            const opt = document.createElement('option');
                            opt.value = t.id;
                            opt.textContent = t.name;
                            teacherSelect.appendChild(opt);
                        });
                    }

                    // Add change event listener to auto-save when teacher is selected
                    teacherSelect.addEventListener('change', function() {
                        if (this.value) {
                            // Auto-save the new class when teacher is selected
                            const classCard = card.querySelector('.class-card');
                            if (classCard) {
                                setTimeout(() => saveSingleClass(classCard), 500);
                            }
                        }
                    });
                })
                .catch(() => toastr.error('Failed to load teachers'));

            attachTeacherDuplicateCheck();
        }

        // ✅ Always reset flag after SweetAlert finishes
        isAddingClass = false;
    });
}
            /* --------------------------------------------------------------
               Attach Family ID → Suggestions Popup (FIXED)
            -------------------------------------------------------------- */
            function attachPopupSuggestions() {
                document.querySelectorAll('.id-input').forEach(input => {
                    if (input._idHandler) input.removeEventListener('input', input._idHandler);
                    if (input._closeHandler) document.removeEventListener('click', input._closeHandler);

                    const row = input.closest('tr');
                    const wrapper = row.querySelector('.name-input-wrapper');
                    const nameInput = wrapper.querySelector('.name-input');
                    const subjectDD = row.querySelector('.subject-dropdown');
                    const yearInput = row.querySelector('.year-input');
                    const popup = wrapper.querySelector('.suggestions-popup');
                    const list = popup.querySelector('.suggestions-list');
                    let debounceTimer;

                    const idInputHandler = function() {
                        clearTimeout(debounceTimer);
                        const query = input.value.trim();
                        if (query && (nameInput.value || subjectDD.value || yearInput.value)) {
                            nameInput.value = '';
                            subjectDD.innerHTML = '<option value="">-- Select --</option>';
                            yearInput.value = '';
                        }
                        if (!query) {
                            popup.style.display = 'none';
                            // Clear entire row when family_id is cleared
                            nameInput.value = '';
                            subjectDD.innerHTML = '<option value="">-- Select --</option>';
                            yearInput.value = '';
                            // Remove medical row highlight if present
                            if (row) {
                                row.classList.remove('medical-row');
                            }
                            // Auto-save after clearing - save immediately to remove student from DB
                            const card = row.closest('.class-card');
                            if (card) {
                                // Save immediately to update DB (student will be excluded from arrays)
                                // Use setTimeout to ensure DOM is updated first
                                setTimeout(() => {
                                    const teacherSelect = card.querySelector('.teacher-select');
                                    const classId = card.parentElement?.dataset?.classId || '';
                                    // Only save if teacher is selected OR if it's an existing class
                                    if (teacherSelect && (teacherSelect.value || classId)) {
                                        saveSingleClass(card);
                                    }
                                }, 200);
                            }
                            return;
                        }
                        debounceTimer = setTimeout(() => {
                            fetch(
                                    `{{ route('get.students.by.family') }}?family_id=${encodeURIComponent(query)}`)
                                .then(async r => {
                                    const data = await r.json();
                                    if (!r.ok && data.blocked) {
                                        popup.style.display = 'none';
                                        input.value = '';
                                        toastr.error('Family ID Blocked. Please contact admin office for more details.');
                                        return null;
                                    }
                                    return data;
                                })
                                .then(data => {
                                    if (!data) return; // blocked — already handled above

                                    list.innerHTML = '';

                                    if (!data.students || data.students.length === 0) {
                                        list.innerHTML =
                                            '<div class="text-center text-muted p-2">No student found</div>';
                                        popup.style.display = 'block';
                                        return;
                                    }
                                    data.students.forEach(s => {
                                        const item = document.createElement('div');
                                        item.className = 'suggest-popup-item';
                                        item.style.cssText =
                                            'padding:6px 10px;cursor:pointer;border-bottom:1px solid #ddd;background:#fff;font-size:14px;';
                                        item.innerHTML = `
    <div style="padding:6px 10px; cursor:pointer; border-bottom:1px solid #2b3d5b;
                background:#e8eefc; font-size:12px; color:#1a237e; font-weight:500;
                white-space:nowrap; overflow:hidden;">
        ${s.name}
    </div>
`;



                                        item.onmouseover = () => item.style
                                            .background = '#f2f2f2';
                                        item.onmouseout = () => item.style
                                            .background = '#fff';
                                        item.onclick = () => {
                                            const allRows = document
                                                .querySelectorAll(
                                                    '#classes-grid tr');
                                            let duplicateRow = null;
                                            allRows.forEach(r => {
                                                const idIn = r
                                                    .querySelector(
                                                        '.id-input');
                                                const nameIn = r
                                                    .querySelector(
                                                        '.name-input');
                                                if (idIn && nameIn &&
                                                    idIn.value == s
                                                    .id && nameIn.value
                                                    .toLowerCase() == s
                                                    .name.toLowerCase()
                                                    ) {
                                                    if (idIn !== input)
                                                        duplicateRow =
                                                        r;
                                                }
                                            });

                                            const fillStudent = (targetRow,
                                                student) => {
                                                const targetId = targetRow
                                                    .querySelector(
                                                        '.id-input');
                                                const targetName = targetRow
                                                    .querySelector(
                                                        '.name-input');
                                                const targetSubject =
                                                    targetRow.querySelector(
                                                        '.subject-dropdown'
                                                        );
                                                const targetYear = targetRow
                                                    .querySelector(
                                                        '.year-input');

                                                targetId.value = student.id;
                                                targetName.value = student
                                                    .name;
                                                targetYear.value = student
                                                    .year || '';

                                                targetSubject.innerHTML =
                                                    '<option value="">-- Select --</option>';
                                                if (Array.isArray(student
                                                        .subjects) &&
                                                    student.subjects
                                                    .length > 0) {
                                                    student.subjects
                                                        .forEach(subj => {
                                                            const opt =
                                                                document
                                                                .createElement(
                                                                    'option'
                                                                    );
                                                            opt.value =
                                                                subj;
                                                            opt.textContent =
                                                                subj;
                                                            const color = getSubjectColor(subj);
                                                            opt.style.backgroundColor = color;
                                                            targetSubject
                                                                .appendChild(
                                                                    opt
                                                                    );
                                                        });
                                                    targetSubject.value =
                                                        student.subjects[0];
                                                }
                                                applyColorsToSubjectDropdowns(); // Apply colors after filling student data
                                            };

                                            if (duplicateRow) {
                                                const oldCard = duplicateRow
                                                    .closest('.class-card');
                                                const oldTeacher = oldCard
                                                    .querySelector(
                                                        '.teacher-select')
                                                    .value || 'N/A';
                                                const oldClassId = oldCard
                                                    .parentElement.dataset
                                                    .classId || 'N/A';
                                                const oldSubject = duplicateRow
                                                    .querySelector(
                                                        '.subject-dropdown')
                                                    .value;

                                                Swal.fire({
                                                    title: 'Student Already in Another Class',
                                                    html: `<div style="text-align:left;font-size:14px;">
                                                        <b>Student:</b> ${s.name}<br>
                                                        <b>Family ID:</b> ${s.id}<br>
                                                        <b>Old Teacher:</b> ${oldTeacher}<br>
                                                        <b>Old Class ID:</b> ${oldClassId}
                                                    </div><hr>Move temporarily to the new class?`,
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'Yes, Move',
                                                    cancelButtonText: 'No'
                                                }).then(result => {
                                                    if (result
                                                        .isConfirmed) {
                                                        // ✅ Do NOT clear old row - student should remain in previous class
                                                        // duplicateRow
                                                        //     .querySelector(
                                                        //         '.id-input'
                                                        //         )
                                                        //     .value = '';
                                                        // duplicateRow
                                                        //     .querySelector(
                                                        //         '.name-input'
                                                        //         )
                                                        //     .value = '';
                                                        // duplicateRow
                                                        //     .querySelector(
                                                        //         '.subject-dropdown'
                                                        //         )
                                                        //     .innerHTML =
                                                        //     '<option value="">-- Select --</option>';
                                                        // duplicateRow
                                                        //     .querySelector(
                                                        //         '.year-input'
                                                        //         )
                                                        //     .value = '';

                                                        // Fill new row with subjects
                                                        fillStudent(row,
                                                            s);

                                                        temporaryMoves
                                                            .push({
                                                                family_id: s
                                                                    .id,
                                                                student_name: s
                                                                    .name,
                                                                old_teacher: oldTeacher,
                                                                old_class_id: oldClassId,
                                                                old_subject: oldSubject,
                                                                permanent: 'No'
                                                            });
                                                        toastr.info(
                                                            `${s.name} moved temporarily.`
                                                            );
                                                        // Auto-save after temporary move
                                                        const card = row.closest('.class-card');
                                                        if (card) {
                                                            setTimeout(() => saveSingleClass(card), 500);
                                                        }
                                                    }
                                                });
                                            } else {
                                                fillStudent(row, s);
                                                // Auto-save after student is selected
                                                const card = row.closest('.class-card');
                                                if (card) {
                                                    setTimeout(() => saveSingleClass(card), 500);
                                                }
                                            }
                                            popup.style.display = 'none';
                                        };
                                        list.appendChild(item);
                                    });
                                    popup.style.display = 'block';
                                })
                                .catch(() => {
                                    popup.style.display = 'none';
                                    toastr.error('Failed to load suggestions');
                                });
                        }, 300);
                    };

                    input._idHandler = idInputHandler;
                    input.addEventListener('input', idInputHandler);

                    const closeHandler = e => {
                        if (wrapper && !wrapper.contains(e.target)) popup.style.display = 'none';
                    };
                    document.addEventListener('click', closeHandler);
                    input._closeHandler = closeHandler;

                    nameInput.addEventListener('input', () => {
                        if (!nameInput.value.trim()) {
                            input.value = '';
                            subjectDD.innerHTML = '<option value="">-- Select --</option>';
                            yearInput.value = '';
                            // Auto-save after clearing name
                            const card = row.closest('.class-card');
                            if (card) {
                                setTimeout(() => saveSingleClass(card), 500);
                            }
                        }
                        // Remove medical row highlight if present
                        if (row) {
                            row.classList.remove('medical-row');
                        }
                    });
                });
            }

            /* --------------------------------------------------------------
               Save Single Class (Auto-save on student select/clear/move)
            -------------------------------------------------------------- */
            function saveSingleClass(cardElement) {
                const date = dateInput.value;
                const slot = slotSelect.value;
                if (!date || !slot) return;

                const card = cardElement || document.querySelector('.class-card');
                if (!card) return;

                const classId = card.parentElement.dataset.classId || '';
                const teacherId = card.querySelector('.teacher-select')?.value;

                // Don't save if no teacher selected
                // For existing classes, teacher should always be present
                if (!teacherId) {
                    console.warn('Cannot save class: Teacher is required');
                    return;
                }

                const studentIds = [];
                const studentNames = [];
                const subjects = [];
                const years = [];

                card.querySelectorAll('tbody tr').forEach(row => {
                    const id = row.querySelector('.id-input')?.value.trim();
                    const name = row.querySelector('.name-input')?.value.trim();
                    const subj = row.querySelector('.subject-dropdown')?.value;
                    const year = row.querySelector('.year-input')?.value.trim();
                    if (id && name) {
                        studentIds.push(id);
                        studentNames.push(name);
                        subjects.push(subj || '');
                        years.push(year || '');
                    }
                });

                // Get temporary moves for this specific class only
                // Include moves where student was moved TO this class (student is in current class)
                const classTemporaryMoves = temporaryMoves.filter(move => {
                    return studentIds.includes(move.family_id);
                });

                const classData = {
                    date,
                    slot,
                    class_id: classId || null,
                    teacher_id: teacherId,
                    student_ids: studentIds,
                    student_names: studentNames,
                    subjects,
                    year_in_schools: years,
                    temporary_moves: classTemporaryMoves
                };

                // Show saving notification
                toastr.info('Saving, please wait...', '', {
                    timeOut: 2000,
                    progressBar: true
                });

                // Save with notifications
                fetch(`{{ route('timeTableSchedularSaveSingle') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(classData)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Update class_id if it was a new class
                        if (data.class_id && !classId) {
                            card.parentElement.dataset.classId = data.class_id;
                        }
                        // Show success notification
                        toastr.success('Saved successfully!', '', {
                            timeOut: 2000,
                            progressBar: true
                        });
                    } else {
                        toastr.error(data.error || 'Failed to save class', '', {
                            timeOut: 3000,
                            progressBar: true
                        });
                        console.error('Auto-save failed:', data.error);
                    }
                })
                .catch(err => {
                    toastr.error('Error saving class. Please try again.', '', {
                        timeOut: 3000,
                        progressBar: true
                    });
                    console.error('Auto-save error:', err);
                });
            }

            /* --------------------------------------------------------------
               Save All Classes
            -------------------------------------------------------------- */
            function saveClasses() {
                const date = dateInput.value;
                const slot = slotSelect.value;
                if (!date || !slot) return toastr.error('Select date and slot');

                const saveBtn = document.getElementById('save-classes-btn');
                saveBtn.disabled = true;

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Saving...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const classesData = [];
                document.querySelectorAll('.class-card').forEach(card => {
                    const classId = card.parentElement.dataset.classId || '';
                    const teacherId = card.querySelector('.teacher-select').value;
                    const studentIds = [],
                        studentNames = [],
                        subjects = [],
                        years = [];

                    card.querySelectorAll('tbody tr').forEach(row => {
                        const id = row.querySelector('.id-input').value.trim();
                        const name = row.querySelector('.name-input').value.trim();
                        const subj = row.querySelector('.subject-dropdown').value;
                        const year = row.querySelector('.year-input').value.trim();
                        if (id && name) {
                            studentIds.push(id);
                            studentNames.push(name);
                            subjects.push(subj || '');
                            years.push(year || '');
                        }
                    });

                    // ✅ Save classes that have at least a teacher (students are optional)
                    if (teacherId) {
                        classesData.push({
                            class_id: classId,
                            teacher_id: teacherId,
                            student_ids: studentIds,
                            student_names: studentNames,
                            subjects,
                            year_in_schools: years,
                            is_attendance: []
                        });
                    }
                });

                if (classesData.length === 0) {
                    Swal.close();
                    saveBtn.disabled = false;
                    return toastr.warning('No data to save');
                }

                // ✅ Extra validation: ensure all classes have at least a teacher
                const validClasses = classesData.filter(cls => {
                    return cls.teacher_id && cls.teacher_id.trim() !== '';
                });

                if (validClasses.length === 0) {
                    Swal.close();
                    saveBtn.disabled = false;
                    return toastr.warning('All classes must have at least one teacher');
                }

                // 🔍 Debug: Log what we're sending
                console.log('Saving data:', {
                    date,
                    slot,
                    classes: validClasses,
                    temporary_moves: temporaryMoves
                });

                fetch(`{{ route('timeTableSchedularSave') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            date,
                            slot,
                            classes: validClasses,
                            temporary_moves: temporaryMoves
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        Swal.close();
                        saveBtn.disabled = false;

                        // 🔍 Debug: Log response
                        console.log('Server response:', data);

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1600);
                        } else {
                            // Show detailed error information
                            let errorMessage = data.error || data.message || 'Save failed';

                            // If there are validation errors, show them
                            if (data.errors) {
                                console.error('Validation errors:', data.errors);
                                errorMessage = Object.values(data.errors).flat().join('\n');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: errorMessage.replace(/\n/g, '<br>'),
                                width: 600
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        saveBtn.disabled = false;
                        console.error('Network error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Network error'
                        });
                    });
            }

            /* --------------------------------------------------------------
               Event Listeners
            -------------------------------------------------------------- */
            dateInput.addEventListener('change', () => {
                  const dayContainer = document.getElementById('dayContainer');
    const showDay = document.getElementById('show-day');
    dayContainer.style.display = "none";
    showDay.textContent = "";
                updateSlots();
                showSelected();
            });
            slotSelect.addEventListener('change', showSelected);
            addClassBtn.addEventListener('click', createNewClassTable);
            if (saveClassesBtn) saveClassesBtn.addEventListener('click', saveClasses);

            // Initial setup
            updateSlots();
            resetUI();
            attachTeacherDuplicateCheck();
        });
    </script>
   <script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-class-btn');
    if (!btn) return;

    const col = btn.closest('.col');
    const classId = col.dataset.classId;

    if (classId) {
        // پرانی کلاس — بیک اینڈ سے ڈیلیٹ
        Swal.fire({
            title: 'Delete Class?',
            text: 'This class will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) return;

            const date = document.getElementById('date-input').value;
            const formattedDate = date.split('-').reverse().join('-');
            const url = `{{ url('delete-general') }}/${classId}?date=${formattedDate}`;

            fetch(url, {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    col.style.transition = 'all 0.4s';
                    col.style.opacity = '0';
                    col.style.transform = 'scale(0.95)';
                    setTimeout(() => col.remove(), 400);
                    toastr.success('Class deleted!');
                } else {
                    toastr.error(data.message || 'Failed');
                }
            });
        });
    } else {
        // نئی کلاس — فوراً ہٹا دو
        Swal.fire({
            title: 'Remove?',
            text: 'This unsaved class will be removed.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then(result => {
            if (result.isConfirmed) {
                col.style.transition = 'all 0.4s';
                col.style.opacity = '0';
                col.style.transform = 'scale(0.9)';
                setTimeout(() => col.remove(), 400);
                toastr.info('Class removed');
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchSection = document.getElementById('search-section');
    const searchType = document.getElementById('searchType');
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearSearchBtn');
    const addBtn = document.getElementById('add-class-btn');
    const saveBtn = document.getElementById('save-classes-btn'); // Commented out button
    const slotSelect = document.getElementById('slot-select');
    const dateInput = document.getElementById('date-input');

    let searchActive = false;

    // Show search section only when date & slot selected
    slotSelect.addEventListener('change', function() {
        if (slotSelect.value && dateInput.value) {
            searchSection.style.display = 'block';
        } else {
            searchSection.style.display = 'none';
        }
    });

    function matches(text, keyword) {
        return text.toLowerCase().includes(keyword.toLowerCase());
    }

    function searchClasses() {
        const keyword = searchInput.value.trim().toLowerCase();
        if (!keyword) {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Search',
                text: 'Please enter something to search!'
            });
            return;
        }

        const type = searchType.value;
        const allCards = document.querySelectorAll('.class-card');
        let foundCount = 0;

        allCards.forEach(card => {
            let cardHasMatch = false;

            if (type === 'teacher') {
                const teacherSelect = card.querySelector('.teacher-select');
                const teacherName = teacherSelect?.options[teacherSelect.selectedIndex]?.text || '';
                if (matches(teacherName, keyword)) {
                    cardHasMatch = true;
                }
            } else {
                // Search within all rows of this class card
                const rows = card.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const id = row.querySelector('.id-input')?.value || '';
                    const name = row.querySelector('.name-input')?.value || '';

                    if (
                        (type === 'family' && matches(id, keyword)) ||
                        (type === 'student' && matches(name, keyword))
                    ) {
                        cardHasMatch = true;
                    }
                });
            }

            // Show or hide the card based on match
            card.style.display = cardHasMatch ? '' : 'none';
            if (cardHasMatch) foundCount++;
        });

        // 🟢 Show alert summary
        if (foundCount > 0) {
            Swal.fire({
                icon: 'success',
                title: 'Results Found',
                text: `${foundCount} matching class${foundCount > 1 ? 'es' : ''} found.`,
                timer: 1300,
                showConfirmButton: false
            });
            clearBtn.style.display = 'inline-block';
            searchActive = true;
        } else {
            Swal.fire({
                icon: 'info',
                title: 'No Results',
                text: 'No matching records found.'
            });
        }
    }

    function clearSearch(showAlert = true) {
        document.querySelectorAll('.class-card').forEach(card => {
            card.style.display = '';
        });
        searchInput.value = '';
        clearBtn.style.display = 'none';
        searchActive = false;
        if (showAlert) {
            Swal.fire({
                icon: 'info',
                title: 'Filters Cleared',
                text: 'All class tables are now visible again.',
                timer: 1200,
                showConfirmButton: false
            });
        }
    }

    // Event listeners
    searchBtn.addEventListener('click', searchClasses);
    clearBtn.addEventListener('click', () => clearSearch(true));

    // Prevent duplicate events when adding/saving while search active
    [addBtn, saveBtn].filter(btn => btn !== null).forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (searchActive) {
                e.preventDefault();
                clearSearch(false);
                Swal.fire({
                    icon: 'info',
                    title: 'Search Cleared',
                    text: 'Filters were cleared before continuing.',
                    confirmButtonText: 'Continue'
                }).then(() => btn.click());
            }
        });
    });

    // Pressing Enter triggers search
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') searchClasses();
    });
});
</script>

<script>
// Bulk Operations for Class Deletion
document.addEventListener('DOMContentLoaded', function() {
    const bulkOperations = document.getElementById('bulk-operations');
    const selectAllBtn = document.getElementById('select-all-btn');
    const deselectAllBtn = document.getElementById('deselect-all-btn');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const selectedCountSpan = document.getElementById('selected-count');
    const classesContainer = document.getElementById('classes-container');
    
    // Force hide on page load - must be hidden initially
    if (bulkOperations) {
        bulkOperations.style.display = 'none';
        bulkOperations.style.setProperty('display', 'none', 'important');
    }

    // Show/hide bulk operations based on checkbox selection
    function toggleBulkOperations() {
        const hasSelectedCheckboxes = document.querySelectorAll('.class-checkbox:checked').length > 0;
        const hasClasses = document.querySelectorAll('.class-card').length > 0;
        const isContainerVisible = classesContainer && classesContainer.style.display !== 'none';
        
        // Only show bulk operations when:
        // 1. At least one checkbox is selected
        // 2. Classes are loaded
        // 3. Container is visible
        if (hasSelectedCheckboxes && hasClasses && isContainerVisible) {
            bulkOperations.style.display = 'flex';
        } else {
            bulkOperations.style.display = 'none';
        }
    }

    // Update selected count and enable/disable delete button
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('.class-checkbox:checked');
        const count = checkboxes.length;
        selectedCountSpan.textContent = count;
        deleteSelectedBtn.disabled = count === 0;
        
        // Update button text color based on selection
        if (count > 0) {
            deleteSelectedBtn.classList.remove('btn-secondary');
            deleteSelectedBtn.classList.add('btn-danger');
        } else {
            deleteSelectedBtn.classList.remove('btn-danger');
            deleteSelectedBtn.classList.add('btn-secondary');
        }
        
        // Show/hide bulk operations based on selection
        // Show if at least one checkbox is checked, hide if none are checked
        const hasClasses = document.querySelectorAll('.class-card').length > 0;
        const isContainerVisible = classesContainer && classesContainer.style.display !== 'none';
        
        if (count > 0 && hasClasses && isContainerVisible) {
            bulkOperations.style.setProperty('display', 'flex', 'important');
        } else {
            bulkOperations.style.setProperty('display', 'none', 'important');
        }
    }

    // Select All functionality
    selectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.class-checkbox').forEach(checkbox => {
            checkbox.checked = true;
        });
        setTimeout(() => {
            updateSelectedCount();
        }, 10);
    });

    // Deselect All functionality
    deselectAllBtn.addEventListener('click', function() {
        document.querySelectorAll('.class-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        setTimeout(() => {
            updateSelectedCount();
        }, 10);
    });

    // Update count when checkbox is clicked
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('class-checkbox')) {
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                updateSelectedCount();
            }, 10);
        }
    });

    // Delete Selected functionality
    deleteSelectedBtn.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.class-checkbox:checked');
        const selectedClasses = Array.from(selectedCheckboxes)
            .map(cb => ({
                classId: cb.dataset.classId,
                card: cb.closest('.col')
            }))
            .filter(item => item.classId && item.classId.trim() !== ''); // Only include existing classes with valid IDs

        if (selectedClasses.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Classes Selected',
                text: 'Please select at least one existing class to delete.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Show confirmation dialog
        Swal.fire({
            title: 'Delete Selected Classes?',
            html: `<div style="text-align:left; font-size:14px;">
                <p><strong>Are you sure you want to delete ${selectedClasses.length} class(es)?</strong></p>
                <p style="color:#dc3545;">This action cannot be undone!</p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Yes, Delete ${selectedClasses.length} Class${selectedClasses.length > 1 ? 'es' : ''}`,
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteSelectedClasses(selectedClasses);
            }
        });
    });

    // Function to delete selected classes
    function deleteSelectedClasses(selectedClasses) {
        const date = document.getElementById('date-input').value;
        if (!date) {
            toastr.error('Date is required for deletion');
            return;
        }

        const formattedDate = date.split('-').reverse().join('-'); // Convert YYYY-MM-DD to DD-MM-YYYY
        
        // Show loading
        Swal.fire({
            title: 'Deleting Classes...',
            html: `Deleting ${selectedClasses.length} class(es). Please wait...`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Delete classes one by one
        let deletedCount = 0;
        let failedCount = 0;
        const totalClasses = selectedClasses.length;
        const deletePromises = [];

        selectedClasses.forEach((item, index) => {
            const deletePromise = fetch(`{{ url('delete-general') }}/${item.classId}?date=${formattedDate}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    deletedCount++;
                    // Animate card removal
                    if (item.card) {
                        item.card.style.transition = 'all 0.4s';
                        item.card.style.opacity = '0';
                        item.card.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            item.card.remove();
                            toggleBulkOperations();
                        }, 400);
                    }
                } else {
                    failedCount++;
                    console.error('Failed to delete class:', item.classId, data.message);
                }
            })
            .catch(err => {
                failedCount++;
                console.error('Error deleting class:', item.classId, err);
            });

            deletePromises.push(deletePromise);
        });

        // Wait for all deletions to complete
        Promise.all(deletePromises).then(() => {
            Swal.close();
            
            if (deletedCount === totalClasses) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: `${deletedCount} class(es) deleted successfully.`,
                    timer: 2000,
                    showConfirmButton: false
                });
                toastr.success(`${deletedCount} class(es) deleted successfully`);
                
                // Reload classes after a short delay
                setTimeout(() => {
                    const slotSelect = document.getElementById('slot-select');
                    if (slotSelect.value) {
                        fetchClasses();
                    }
                }, 2100);
            } else if (deletedCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Partial Success',
                    html: `<div style="text-align:left; font-size:14px;">
                        <p><strong>${deletedCount}</strong> class(es) deleted successfully.</p>
                        <p><strong>${failedCount}</strong> class(es) failed to delete.</p>
                    </div>`,
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Deletion Failed',
                    text: 'Failed to delete selected classes. Please try again.',
                    confirmButtonText: 'OK'
                });
            }

            // Reset checkboxes
            document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        });
    }

    // Initialize bulk operations when classes are loaded
    window.initializeBulkOperations = function() {
        // Force hide on initialization - only show when checkbox is selected
        bulkOperations.style.display = 'none';
        updateSelectedCount();
    };

            // Monitor for class changes
    const observer = new MutationObserver(function(mutations) {
        // Only update if classes are actually added/removed
        let shouldUpdate = false;
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0) {
                shouldUpdate = true;
            }
        });
        if (shouldUpdate) {
            // Check if any checkboxes are selected after class changes
            setTimeout(() => {
                const hasSelected = document.querySelectorAll('.class-checkbox:checked').length > 0;
                if (!hasSelected) {
                    bulkOperations.style.display = 'none';
                } else {
                    // If checkboxes are selected, show bulk operations
                    const hasClasses = document.querySelectorAll('.class-card').length > 0;
                    if (hasClasses) {
                        bulkOperations.style.display = 'flex';
                    }
                }
            }, 50);
        }
    });

    if (classesContainer) {
        observer.observe(classesContainer, {
            childList: true,
            subtree: false // Only watch direct children, not deep subtree
        });
    }

    // Initial setup - force hide by default (only show when checkbox is selected)
    if (bulkOperations) {
        bulkOperations.style.display = 'none';
        bulkOperations.style.setProperty('display', 'none', 'important');
    }
});
</script>


@endsection
