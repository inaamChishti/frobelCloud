@extends('layouts.branchDashboardApp')

@section('content')
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Activity Logs</h1>
                <p>View and manage system activity logs with advanced filtering and sorting.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="clearFiltersBtn">
                    <i class="fas fa-redo"></i> Clear Filters
                </button>
                <button type="button" class="btn btn-info" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="toast-error mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Advanced Filters Section -->
        <div class="ui-bordered px-4 pt-4 mb-4 bg-white">
            <h5 class="mb-3" style="color: #2046A8; font-weight: 600;">
                <i class="fas fa-filter"></i> Advanced Filters
            </h5>
            <form method="GET" action="{{ route('system.log') }}" id="filterForm">
                <div class="row g-3">
                    <!-- User Filter -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="user_id" style="font-size: 14px; color: black; font-weight: bold;">User</label>
                        <select name="user_id" id="user_id" class="form-control select2-single">
                            <option value="all">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="fas fa-info-circle"></i> Only users with logs are shown
                        </small>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="date_from" style="font-size: 14px; color: black; font-weight: bold;">Date From</label>
                        <input type="text" name="date_from" id="date_from" class="form-control datepicker"
                               value="{{ request('date_from') }}" placeholder="dd/mm/yyyy" autocomplete="off" 
                               inputmode="none" readonly onfocus="this.removeAttribute('readonly');">
            </div>

                    <!-- Date To -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="date_to" style="font-size: 14px; color: black; font-weight: bold;">Date To</label>
                        <input type="text" name="date_to" id="date_to" class="form-control datepicker"
                               value="{{ request('date_to') }}" placeholder="dd/mm/yyyy" autocomplete="off"
                               inputmode="none" readonly onfocus="this.removeAttribute('readonly');">
        </div>

                    <!-- Log Name Filter -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="log_name" style="font-size: 14px; color: black; font-weight: bold;">Log Name</label>
                        <select name="log_name" id="log_name" class="form-control select2-single">
                            <option value="">All Log Names</option>
                            @foreach($availableLogNames ?? [] as $logName)
                                <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>
                                    {{ $logName }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                    <!-- Event Filter -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="event" style="font-size: 14px; color: black; font-weight: bold;">
                            Event <span class="text-muted">(Realtime)</span>
                            </label>
                        <select name="event" id="event" class="form-control select2-single">
                            <option value="">All Events</option>
                            @foreach($availableEvents ?? [] as $event)
                                <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>
                                    {{ ucfirst($event) }}
                                </option>
                            @endforeach
                        </select>
                        </div>

                    <!-- Sort Column -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="sort_column" style="font-size: 14px; color: black; font-weight: bold;">Sort By</label>
                        <select name="sort_column" id="sort_column" class="form-control">
                            <option value="created_at" {{ request('sort_column', 'created_at') == 'created_at' ? 'selected' : '' }}>Created At</option>
                            <option value="log_name" {{ request('sort_column') == 'log_name' ? 'selected' : '' }}>Log Name</option>
                            <option value="event" {{ request('sort_column') == 'event' ? 'selected' : '' }}>Event</option>
                        </select>
                        </div>

                    <!-- Sort Direction -->
                    <div class="col-md-3 col-lg-3 col-xs-12 mb-3">
                        <label for="sort_direction" style="font-size: 14px; color: black; font-weight: bold;">Sort Direction</label>
                        <select name="sort_direction" id="sort_direction" class="form-control">
                            <option value="desc" {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                            <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        </select>
                    </div>
                </div>
            </form>
            </div>

        <!-- Logs Table -->
        <div id="logsContainer">
            <div class="table-responsive" id="logsTableWrapper" @if(!isset($allLogs) || $allLogs->count() == 0) style="display: none;" @endif>
                    <table class="table table-striped" id="logsTable">
                        <thead>
                            <tr>
                            <th class="sortable" data-column="log_name">
                                Model <i class="fas fa-sort"></i>
                            </th>
                            <th class="sortable" data-column="description">
                                Description <i class="fas fa-sort"></i>
                            </th>
                            <th class="sortable" data-column="event">
                                Event <i class="fas fa-sort"></i>
                            </th>
                            <th class="sortable" data-column="causer_id">
                                Username <i class="fas fa-sort"></i>
                            </th>
                            <th class="sortable" data-column="created_at">
                                Created At <i class="fas fa-sort"></i>
                            </th>
                            <th>Properties</th>
                            </tr>
                        </thead>
                    <tbody id="logsTableBody">
                        @if(isset($allLogs) && $allLogs->count() > 0)
                            @php
                                $uniqueLogs = [];
                                $seenEntries = [];
                            @endphp

                            @foreach ($allLogs as $log)
                                @php
                                    // Only apply uniqueness filter for GeneralTimetable, show all others
                                    $shouldFilterUnique = ($log->log_name === 'GeneralTimetable');
                                    
                                    if ($shouldFilterUnique) {
                                        // Create a unique identifier for GeneralTimetable entries
                                    $logIdentifier = $log->log_name . '|' . $log->description . '|' . $log->event . '|' . $log->causer_id;

                                    // Check if we've already seen this entry
                                        if (in_array($logIdentifier, $seenEntries)) {
                                            continue; // Skip duplicate GeneralTimetable entries
                                        }
                                        $seenEntries[] = $logIdentifier;
                                    }
                                    
                                    $uniqueLogs[] = $log;
                                @endphp
                            @endforeach

                            @foreach ($uniqueLogs as $log)
                                        @php
                                    $userName = 'No User';
                                            $user = \App\Models\User::find($log->causer_id);
                                            if ($user) {
                                                $userName = $user->name;
                                    }
                                    
                                    $timestamp = $log->created_at ?? '';
                                    $formattedDate = '';
                                    if (!empty($timestamp)) {
                                        $formattedDate = \Carbon\Carbon::parse($timestamp)->format('d M Y H:i');
                                    }
                                        @endphp
                                <tr class="log-entry" 
                                    data-unique-id="{{ $log->log_name }}_{{ $log->description }}_{{ $log->event }}_{{ $log->causer_id }}"
                                    data-model="{{ strtolower($log->log_name ?? '') }}"
                                    data-description="{{ strtolower($log->description ?? '') }}"
                                    data-event="{{ strtolower($log->event ?? '') }}"
                                    data-username="{{ strtolower($userName) }}">
                                    <td>{{ $log->log_name ?? 'N/A' }}</td>
                                    <td>{{ $log->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->event == 'created' ? 'success' : ($log->event == 'updated' ? 'warning' : ($log->event == 'deleted' ? 'danger' : 'info')) }}">
                                            {{ ucfirst($log->event ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>{{ $userName }}</td>
                                    <td>{{ $formattedDate }}</td>
                                    <td>
                                        @php
                                            $properties = [];
                                            if ($log->properties) {
                                                $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
                                            }
                                            
                                            // Check if this is Admission log with changes
                                            $isAdmissionWithChanges = false;
                                            if ($log->log_name === 'Admission' && isset($properties['changes'])) {
                                                $isAdmissionWithChanges = true;
                                            }
                                            
                                            // Get attributes for display
                                            $displayData = [];
                                            if (!empty($properties) && !$isAdmissionWithChanges) {
                                                if (isset($properties['attributes'])) {
                                                    $displayData = $properties['attributes'];
                                                } elseif (isset($properties['old'])) {
                                                    $displayData = $properties['old'];
                                                } else {
                                                    $displayData = $properties;
                                                }
                                            }
                                        @endphp
                                        @if($isAdmissionWithChanges)
                                            <div class="properties-preview">
                                                <div class="alert alert-info mb-2 p-2">
                                                    <i class="fas fa-info-circle"></i> 
                                                    <strong>Changed Fields:</strong>
                                                    @php
                                                        $changeCount = 0;
                                                        foreach($properties['changes'] as $section => $sectionChanges) {
                                                            if ($section === 'Students') {
                                                                foreach($sectionChanges as $studentChange) {
                                                                    if (isset($studentChange['changes'])) {
                                                                        $changeCount += count($studentChange['changes']);
                                                                    } else {
                                                                        $changeCount += 1; // For new student added
                                                                    }
                                                                }
                                                            } else {
                                                                $changeCount += count($sectionChanges);
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $changeCount }} field(s)
                                                </div>
                                                <button type="button" class="btn btn-sm btn-info w-100 view-properties-btn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#propertiesModal"
                                                        data-properties='@json($properties)'
                                                        data-log-name="{{ $log->log_name }}"
                                                        data-description="{{ $log->description }}">
                                                    <i class="fas fa-eye"></i> View Changed Fields
                                                </button>
                                            </div>
                                        @elseif(!empty($displayData))
                                            <div class="properties-preview">
                                                <div class="properties-list">
                                                    @php
                                                        $displayCount = 0;
                                                        $maxDisplay = 4;
                                                    @endphp
                                                    @foreach($displayData as $key => $value)
                                                        @if($key !== 'branch_id' && $key !== 'branch_name' && $displayCount < $maxDisplay)
                                                            <div class="property-item mb-2 p-2 bg-light rounded">
                                                                <div class="property-key fw-bold text-primary" style="font-size: 11px;">
                                                                    {{ ucwords(str_replace('_', ' ', $key)) }}:
                                                                </div>
                                                                <div class="property-value" style="font-size: 11px; margin-top: 2px;">
                                                                    @if(is_bool($value))
                                                                        <span class="badge bg-{{ $value ? 'success' : 'danger' }}">{{ $value ? 'Yes' : 'No' }}</span>
                                                                    @elseif(is_array($value) || is_object($value))
                                                                        <span class="badge bg-secondary">Complex Data</span>
                                                                    @else
                                                                        <span class="text-dark">{{ Str::limit($value, 40) }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @php $displayCount++; @endphp
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @if(count(array_filter(array_keys($displayData), function($k) { return $k !== 'branch_id' && $k !== 'branch_name'; })) > $maxDisplay)
                                                    <button type="button" class="btn btn-sm btn-info mt-2 w-100 view-properties-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#propertiesModal"
                                                            data-properties='@json($properties)'
                                                            data-log-name="{{ $log->log_name }}"
                                                            data-description="{{ $log->description }}">
                                                        <i class="fas fa-eye"></i> View All ({{ count(array_filter(array_keys($displayData), function($k) { return $k !== 'branch_id' && $k !== 'branch_name'; })) }} fields)
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-info mt-2 w-100 view-properties-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#propertiesModal"
                                                            data-properties='@json($properties)'
                                                            data-log-name="{{ $log->log_name }}"
                                                            data-description="{{ $log->description }}">
                                                        <i class="fas fa-eye"></i> View Details
                                                    </button>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No properties</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            <div id="noLogsMessage" class="alert alert-info text-center mt-5" @if(isset($allLogs) && $allLogs->count() > 0) style="display: none;" @endif>
                <i class="fas fa-info-circle"></i> No logs found. Try adjusting your filters.
            </div>
            <div id="loadingMessage" class="alert alert-secondary text-center mt-5" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Loading logs...
            </div>
            
            <!-- Pagination Container -->
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-4" style="display: none !important;">
                <div class="pagination-info">
                    <span id="paginationInfo" class="text-muted"></span>
                </div>
                <nav>
                    <ul class="pagination mb-0" id="paginationLinks">
                        <!-- Pagination links will be generated here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Properties Modal -->
    <div class="modal fade" id="propertiesModal" tabindex="-1" aria-labelledby="propertiesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: #2046A8; color: white;">
                    <h5 class="modal-title" id="propertiesModalLabel">
                        <i class="fas fa-info-circle"></i> Activity Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Log Name:</strong> <span id="modalLogName" class="badge bg-info ms-2"></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Description:</strong> <span id="modalDescription" class="ms-2"></span>
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3"><strong>Properties:</strong></h6>
                    <div id="propertiesContent" style="overflow: visible;">
                        <!-- Properties will be displayed here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .main-content {
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #2046A8;
        }

        .main-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #2046A8;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #2046A8;
            margin-bottom: 20px;
        }

        .ui-bordered {
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #2046A8;
        }

        .form-control {
            border-color: #2046A8;
            color: #2046A8;
            background: transparent;
        }

        .form-control:focus {
            border-color: #4ba8d2;
            box-shadow: 0 0 8px rgba(103, 192, 234, 0.5);
        }

        .table-responsive {
            border-radius: 8px;
            overflow: visible;
        }

        .table.table-striped {
            --bs-table-bg: transparent;
            --bs-table-color: #2046A8;
            --bs-table-striped-bg: rgba(103, 192, 234, 0.1);
            --bs-table-striped-color: #2046A8;
            color: var(--bs-table-color);
            background: var(--bs-table-bg);
            border-color: #2046A8;
            border-radius: 8px;
            margin-bottom: 0;
            width: 100%;
        }

        .table.table-striped thead th {
            background: #2046A8;
            color: #ffffff;
            border-bottom: 2px solid #2046A8;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            cursor: pointer;
        }

        .table.table-striped thead th.sortable {
            user-select: none;
        }

        .table.table-striped thead th i {
            margin-left: 5px;
            opacity: 0.5;
        }

        .table.table-striped thead th.sort-asc i::before {
            content: "\f0de";
            opacity: 1;
        }

        .table.table-striped thead th.sort-desc i::before {
            content: "\f0dd";
            opacity: 1;
        }

        .table.table-striped tbody {
            background: transparent;
        }

        .table.table-striped tbody tr {
        }

        .table.table-striped tbody td {
            vertical-align: middle;
            border-color: #2046A8;
            padding: 12px;
            color: #2046A8;
            font-size: 14px;
            font-weight: 500;
        }

        .table.table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-color-type: #2046A8 !important;
            --bs-table-bg-type: rgba(103, 192, 234, 0.1) !important;
            color: var(--bs-table-color-type) !important;
            background-color: var(--bs-table-bg-type) !important;
        }

        .btn-primary, .btn-secondary, .btn-info {
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background: #2046A8;
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-info {
            background: #17a2b8;
        }

        .toast-error {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border: 2px solid #a71d2a;
            border-radius: 8px;
            font-weight: 500;
            padding: 15px;
        }


        .badge {
            padding: 8px 12px;
            font-size: 14px;
        }

        /* Select2 Styling */
        .select2-container .select2-selection--single {
            border-color: #2046A8;
            background: transparent;
            color: #2046A8;
            border-radius: 8px;
            height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #2046A8;
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2046A8;
            color: #ffffff;
        }

        /* Flatpickr Styling */
        .flatpickr-calendar {
            background: #ffffff;
            border: 1px solid #2046A8;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .flatpickr-day.selected {
            background: #2046A8;
            color: #ffffff;
            border-color: #2046A8;
        }


        .properties-table-container {
            width: 100%;
        }

        .properties-table-container table {
            margin-bottom: 0;
        }

        .properties-table-container .table th {
            background-color: #f8f9fa;
            color: #2046A8;
            font-weight: 600;
            border-bottom: 2px solid #2046A8;
        }

        .properties-table-container .table td {
            vertical-align: middle;
            word-wrap: break-word;
        }

        .json-display pre {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            font-size: 11px;
        }

        #propertiesContent {
            overflow: visible !important;
        }

        #propertiesContent .properties-table-container {
            overflow: visible;
        }

        .long-text {
            word-break: break-word;
        }

        .modal-xl {
            max-width: 90%;
        }

        .properties-preview {
            min-width: 250px;
        }

        .properties-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .property-item {
            border-left: 3px solid #2046A8;
        }

        .property-key {
            color: #2046A8;
        }

        .property-value {
            color: #333;
        }


        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .table-filter-input {
                min-width: 100%;
                max-width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for single selects
            $('.select2-single').select2({
                placeholder: "Select...",
                allowClear: true,
                width: '100%'
            });

            // Initialize Flatpickr for date inputs
            $('.datepicker').flatpickr({
                dateFormat: "d/m/Y",
                allowInput: true,
                disableMobile: true,
                clickOpens: true
            });

            // Function to update user dropdown dynamically (optimized)
            function updateUserDropdown() {
                const formData = {
                    event: $('#event').val(),
                    log_name: $('#log_name').val(),
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val()
                };

                // Show loading state
                $('#user_id').prop('disabled', true);
                const currentValue = $('#user_id').val();

                $.ajax({
                    url: "{{ route('system.log.get.users') }}",
                    method: 'GET',
                    data: formData,
                    cache: false,
                    async: true,
                    success: function(response) {
                        // Clear and repopulate user dropdown
                        $('#user_id').empty();
                        $('#user_id').append('<option value="all">All Users</option>');
                        
                        if (response && response.length > 0) {
                            $.each(response, function(index, user) {
                                const selected = (currentValue == user.id) ? 'selected' : '';
                                $('#user_id').append(`<option value="${user.id}" ${selected}>${user.name}</option>`);
                            });
                } else {
                            // No users found with logs matching filters
                            $('#user_id').append('<option value="">No users found</option>');
                        }
                        
                        // Reinitialize Select2
                        $('#user_id').select2({
                            placeholder: response && response.length > 0 ? "Select User..." : "No users available",
                            allowClear: true,
                            width: '100%'
                        });
                        
                        // Restore previous selection if it still exists
                        if (currentValue && currentValue !== 'all') {
                            const optionExists = $('#user_id option[value="' + currentValue + '"]').length > 0;
                            if (optionExists) {
                                $('#user_id').val(currentValue).trigger('change');
                            } else {
                                // Previous selection no longer valid, reset to 'all'
                                $('#user_id').val('all').trigger('change');
                            }
                        }
                        
                        $('#user_id').prop('disabled', false);
                    },
                    error: function() {
                        $('#user_id').prop('disabled', false);
                        console.error('Error loading users');
                    }
                });
            }

            // Function to load logs via AJAX
            let currentPage = 1;
            
            function loadLogs(page = 1) {
                currentPage = page;
                const formData = {
                    event: $('#event').val(),
                    log_name: $('#log_name').val(),
                    user_id: $('#user_id').val(),
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    sort_column: $('#sort_column').val(),
                    sort_direction: $('#sort_direction').val(),
                    page: page,
                    per_page: 500
                };

                // Show loading
                $('#logsTableWrapper').hide();
                $('#noLogsMessage').hide();
                $('#loadingMessage').show();

                $.ajax({
                    url: "{{ route('system.log.get.logs') }}",
                    method: 'GET',
                    data: formData,
                    cache: false,
                    async: true,
                    success: function(response) {
                        $('#loadingMessage').hide();
                        
                        if (response.logs && response.logs.length > 0) {
                            let html = '';
                            response.logs.forEach(function(log) {
                                const eventClass = log.event == 'created' ? 'success' : (log.event == 'updated' ? 'warning' : (log.event == 'deleted' ? 'danger' : 'info'));
                                html += `
                                    <tr class="log-entry" 
                                        data-unique-id="${log.unique_id}"
                                        data-model="${log.log_name.toLowerCase()}"
                                        data-description="${log.description.toLowerCase()}"
                                        data-event="${log.event.toLowerCase()}"
                                        data-username="${log.username.toLowerCase()}">
                                        <td>${log.log_name}</td>
                                        <td>${log.description}</td>
                                        <td><span class="badge bg-${eventClass}">${log.event.charAt(0).toUpperCase() + log.event.slice(1)}</span></td>
                                        <td>${log.username}</td>
                                        <td>${log.created_at}</td>
                                        <td>
                                            ${formatPropertiesPreview(log.properties || {}, log.log_name, log.description)}
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            $('#logsTableBody').html(html);
                            $('#logsTableWrapper').show();
                            
                            // Update pagination
                            updatePagination(response);
                        } else {
                            $('#noLogsMessage').show();
                            $('#paginationContainer').hide();
                        }
                    },
                    error: function() {
                        $('#loadingMessage').hide();
                        $('#noLogsMessage').html('<i class="fas fa-exclamation-triangle"></i> Error loading logs. Please try again.').show();
                    }
                });
            }

            // Update user dropdown when filters change (except user_id itself) - fast
            let userUpdateTimeout;
            $('#event, #log_name, #date_from, #date_to').on('change input', function() {
                clearTimeout(userUpdateTimeout);
                userUpdateTimeout = setTimeout(function() {
                    currentPage = 1; // Reset to first page on filter change
                    updateUserDropdown();
                    loadLogs(1);
                }, 100);
            });

            // Real-time filter updates via AJAX (fast)
            let filterTimeout;
            $('#event, #log_name, #user_id, #sort_column, #sort_direction').on('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(function() {
                    currentPage = 1; // Reset to first page on filter change
                    loadLogs(1);
                }, 50);
            });

            // Debounced search for text inputs (fast)
            let searchTimeout;
            $('#date_from, #date_to').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1; // Reset to first page on filter change
                    updateUserDropdown();
                    loadLogs(1);
                }, 200);
            });

            // Table column sorting
            $(document).on('click', '.sortable', function() {
                const column = $(this).data('column');
                const currentSort = $('#sort_column').val();
                const currentDirection = $('#sort_direction').val();

                // Update sort column
                $('#sort_column').val(column);

                // Toggle direction if same column, otherwise default to desc
                if (currentSort === column) {
                    $('#sort_direction').val(currentDirection === 'desc' ? 'asc' : 'desc');
                } else {
                    $('#sort_direction').val('desc');
                }

                // Remove sort indicators
                $('.sortable').removeClass('sort-asc sort-desc');
                
                // Add sort indicator
                const newDirection = $('#sort_direction').val();
                $(this).addClass('sort-' + newDirection);

                // Load logs via AJAX
                currentPage = 1; // Reset to first page on sort change
                loadLogs(1);
            });

            // Set initial sort indicators
            const currentSort = $('#sort_column').val();
            const currentDirection = $('#sort_direction').val();
            $(`.sortable[data-column="${currentSort}"]`).addClass('sort-' + currentDirection);

            // Clear filters button
            $('#clearFiltersBtn').on('click', function() {
                $('#filterForm')[0].reset();
                $('.select2-single').val(null).trigger('change');
                $('#sort_column').val('created_at');
                $('#sort_direction').val('desc');
                currentPage = 1; // Reset to first page
                loadLogs(1);
            });

            // Refresh button
            $('#refreshBtn').on('click', function() {
                currentPage = 1; // Reset to first page
                loadLogs(1);
            });

            // Function to update pagination
            function updatePagination(response) {
                if (response.total && response.total > response.per_page) {
                    $('#paginationContainer').show();
                    const start = ((response.current_page - 1) * response.per_page) + 1;
                    const end = Math.min(response.current_page * response.per_page, response.total);
                    $('#paginationInfo').text(`Showing ${start} to ${end} of ${response.total} logs`);
                    
                    let paginationHtml = '';
                    
                    // Previous button
                    if (response.current_page > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page - 1}">Previous</a></li>`;
                    } else {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">Previous</span></li>`;
                    }
                    
                    // Page numbers
                    const startPage = Math.max(1, response.current_page - 2);
                    const endPage = Math.min(response.last_page, response.current_page + 2);
                    
                    if (startPage > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                        if (startPage > 2) {
                            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }
                    
                    for (let i = startPage; i <= endPage; i++) {
                        if (i === response.current_page) {
                            paginationHtml += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
                        } else {
                            paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                        }
                    }
                    
                    if (endPage < response.last_page) {
                        if (endPage < response.last_page - 1) {
                            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${response.last_page}">${response.last_page}</a></li>`;
                    }
                    
                    // Next button
                    if (response.has_more) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${response.current_page + 1}">Next</a></li>`;
                    } else {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
                    }
                    
                    $('#paginationLinks').html(paginationHtml);
                } else {
                    $('#paginationContainer').hide();
                }
            }
            
            // Handle pagination clicks
            $(document).on('click', '.page-link[data-page]', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                loadLogs(page);
            });

            // Initial load - show table if logs exist
            @if(isset($allLogs) && $allLogs->count() > 0)
                $('#logsTableWrapper').show();
                $('#noLogsMessage').hide();
            @else
                $('#logsTableWrapper').hide();
                $('#noLogsMessage').show();
            @endif

            // Handle properties modal
            $(document).on('click', '.view-properties-btn', function() {
                const properties = $(this).data('properties');
                const logName = $(this).data('log-name');
                const description = $(this).data('description');
                
                $('#modalLogName').text(logName);
                $('#modalDescription').text(description);
                
                // Format and display properties
                let propertiesHtml = '';
                if (properties && typeof properties === 'object') {
                    // Check if this is an Admission log with changes tracking
                    if (logName === 'Admission' && properties.changes) {
                        propertiesHtml = formatAdmissionChanges(properties);
                    } else {
                        // Check if it's attributes (for created/updated events)
                        const dataToShow = properties.attributes || properties.old || properties;
                        
                        if (properties.attributes && properties.old) {
                            // Updated event - show both old and new
                            propertiesHtml += '<div class="mb-4"><h6 class="text-success"><i class="fas fa-plus-circle"></i> New Values:</h6>';
                            propertiesHtml += formatProperties(properties.attributes);
                            propertiesHtml += '</div>';
                            propertiesHtml += '<div class="mb-4"><h6 class="text-danger"><i class="fas fa-minus-circle"></i> Old Values:</h6>';
                            propertiesHtml += formatProperties(properties.old);
                            propertiesHtml += '</div>';
                        } else {
                            propertiesHtml = formatProperties(dataToShow);
                        }
                    }
                } else {
                    propertiesHtml = '<p class="text-muted">No properties available</p>';
                }
                
                $('#propertiesContent').html(propertiesHtml);
            });

            // Function to format properties nicely
            function formatProperties(data) {
                if (!data || typeof data !== 'object') {
                    return '<p class="text-muted">No data available</p>';
                }
                
                let html = '<div class="properties-table-container"><table class="table table-bordered table-sm table-hover">';
                html += '<thead class="table-light"><tr><th style="width: 35%;">Field</th><th>Value</th></tr></thead><tbody>';
                
                for (const [key, value] of Object.entries(data)) {
                    // Skip branch_id and branch_name as they're already shown
                    if (key === 'branch_id' || key === 'branch_name') continue;
                    
                    let displayValue = value;
                    if (value === null || value === '') {
                        displayValue = '<span class="text-muted fst-italic">(empty)</span>';
                    } else if (typeof value === 'object') {
                        displayValue = '<div class="json-display"><pre class="mb-0 p-2 bg-light rounded" style="font-size: 11px; white-space: pre-wrap; word-wrap: break-word;">' + JSON.stringify(value, null, 2) + '</pre></div>';
                    } else if (typeof value === 'boolean') {
                        displayValue = value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>';
                    } else {
                        const strValue = String(value);
                        if (strValue.length > 150) {
                            displayValue = '<div class="long-text" style="max-width: 500px;"><span class="d-inline-block" title="' + strValue.replace(/"/g, '&quot;') + '">' + strValue.substring(0, 150) + '...</span></div>';
                        } else {
                            displayValue = '<span>' + strValue + '</span>';
                        }
                    }
                    
                    html += `<tr>
                        <td class="fw-bold text-primary">${formatFieldName(key)}</td>
                        <td>${displayValue}</td>
                    </tr>`;
                }
                
                html += '</tbody></table></div>';
                return html;
            }

            // Function to format field names (convert snake_case to Title Case)
            function formatFieldName(name) {
                return name
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }

            // Function to format Admission changes in a user-friendly way - ONLY CHANGED FIELDS
            function formatAdmissionChanges(properties) {
                if (!properties.changes || Object.keys(properties.changes).length === 0) {
                    return '<p class="text-muted">No changes recorded</p>';
                }

                let html = '';
                
                // Show Family ID if available
                if (properties.family_id) {
                    html += '<div class="alert alert-info mb-4">';
                    html += '<strong><i class="fas fa-id-card"></i> Family ID:</strong> ' + properties.family_id;
                    html += '</div>';
                }

                const changes = properties.changes;
                const sections = ['Consent', 'Guardian', 'Kin', 'Admission', 'Students'];
                let hasAnyChanges = false;

                sections.forEach(section => {
                    if (changes[section] && changes[section].length > 0) {
                        hasAnyChanges = true;
                        html += '<div class="card mb-4 border-primary">';
                        html += '<div class="card-header bg-primary text-white">';
                        html += '<h5 class="mb-0"><i class="fas fa-edit"></i> ' + section + ' - Changed Fields</h5>';
                        html += '</div>';
                        html += '<div class="card-body">';

                        if (section === 'Students') {
                            // Handle student changes differently
                            changes[section].forEach((studentChange, index) => {
                                if (index > 0) html += '<hr class="my-3">';
                                
                                html += '<div class="student-change-section">';
                                html += '<h6 class="text-primary mb-3">';
                                html += '<i class="fas fa-user-graduate"></i> ';
                                html += studentChange.student_name || 'Student #' + (index + 1);
                                if (studentChange.student_id) {
                                    html += ' <small class="text-muted">(ID: ' + studentChange.student_id + ')</small>';
                                }
                                html += '</h6>';

                                if (studentChange.action === 'added') {
                                    html += '<div class="alert alert-success">';
                                    html += '<i class="fas fa-plus-circle"></i> <strong>New Student Added</strong>';
                                    html += '</div>';
                                } else if (studentChange.changes && studentChange.changes.length > 0) {
                                    // Only show changed fields
                                    html += '<div class="alert alert-warning mb-3">';
                                    html += '<i class="fas fa-info-circle"></i> <strong>' + studentChange.changes.length + ' field(s) changed</strong>';
                                    html += '</div>';
                                    html += '<table class="table table-bordered table-sm table-hover">';
                                    html += '<thead class="table-light">';
                                    html += '<tr><th style="width: 30%;">Changed Field</th><th style="width: 35%;">Old Value</th><th style="width: 35%;">New Value</th></tr>';
                                    html += '</thead><tbody>';

                                    studentChange.changes.forEach(change => {
                                        html += '<tr>';
                                        html += '<td class="fw-bold text-primary">' + change.field + '</td>';
                                        html += '<td class="text-danger"><del>' + (change.old_value || '(empty)') + '</del></td>';
                                        html += '<td class="text-success"><strong>' + (change.new_value || '(empty)') + '</strong></td>';
                                        html += '</tr>';
                                    });

                                    html += '</tbody></table>';
                                }
                                html += '</div>';
                            });
                        } else {
                            // Handle other sections (Consent, Guardian, Kin, Admission) - ONLY CHANGED FIELDS
                            html += '<div class="alert alert-warning mb-3">';
                            html += '<i class="fas fa-info-circle"></i> <strong>' + changes[section].length + ' field(s) changed</strong>';
                            html += '</div>';
                            html += '<table class="table table-bordered table-sm table-hover">';
                            html += '<thead class="table-light">';
                            html += '<tr><th style="width: 30%;">Changed Field</th><th style="width: 35%;">Old Value</th><th style="width: 35%;">New Value</th></tr>';
                            html += '</thead><tbody>';

                            changes[section].forEach(change => {
                                html += '<tr>';
                                html += '<td class="fw-bold text-primary">' + change.field + '</td>';
                                html += '<td class="text-danger"><del>' + (change.old_value || '(empty)') + '</del></td>';
                                html += '<td class="text-success"><strong>' + (change.new_value || '(empty)') + '</strong></td>';
                                html += '</tr>';
                            });

                            html += '</tbody></table>';
                        }

                        html += '</div></div>';
                    }
                });

                if (!hasAnyChanges || html === '') {
                    html = '<p class="text-muted">No changes recorded</p>';
                }

                return html;
            }

            // Function to format properties preview for table
            function formatPropertiesPreview(properties, logName, description) {
                if (!properties || Object.keys(properties).length === 0) {
                    return '<span class="text-muted">No properties</span>';
                }

                // Check if this is Admission log with changes
                if (logName === 'Admission' && properties.changes) {
                    let changeCount = 0;
                    for (const section in properties.changes) {
                        if (properties.changes[section] && Array.isArray(properties.changes[section])) {
                            if (section === 'Students') {
                                properties.changes[section].forEach(studentChange => {
                                    if (studentChange.changes) {
                                        changeCount += studentChange.changes.length;
                                    } else {
                                        changeCount += 1; // For new student added
                                    }
                                });
                            } else {
                                changeCount += properties.changes[section].length;
                            }
                        }
                    }
                    
                    let html = '<div class="properties-preview">';
                    html += '<div class="alert alert-info mb-2 p-2">';
                    html += '<i class="fas fa-info-circle"></i> ';
                    html += '<strong>Changed Fields:</strong> ' + changeCount + ' field(s)';
                    html += '</div>';
                    html += `<button type="button" class="btn btn-sm btn-info w-100 view-properties-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#propertiesModal"
                        data-properties='${JSON.stringify(properties)}'
                        data-log-name="${logName}"
                        data-description="${description}">
                        <i class="fas fa-eye"></i> View Changed Fields
                    </button>`;
                    html += '</div>';
                    return html;
                }

                // Get display data
                let displayData = {};
                if (properties.attributes) {
                    displayData = properties.attributes;
                } else if (properties.old) {
                    displayData = properties.old;
                } else {
                    displayData = properties;
                }

                if (Object.keys(displayData).length === 0) {
                    return '<span class="text-muted">No properties</span>';
                }

                let html = '<div class="properties-preview">';
                let htmlList = '<div class="properties-list">';
                let count = 0;
                const maxPreview = 4;

                for (const [key, value] of Object.entries(displayData)) {
                    if (key === 'branch_id' || key === 'branch_name') continue;
                    if (count >= maxPreview) break;

                    let displayValue = '';
                    if (value === null || value === '') {
                        displayValue = '<span class="text-muted fst-italic">(empty)</span>';
                    } else if (typeof value === 'boolean') {
                        displayValue = `<span class="badge bg-${value ? 'success' : 'danger'}">${value ? 'Yes' : 'No'}</span>`;
                    } else if (typeof value === 'object') {
                        displayValue = '<span class="badge bg-secondary">Complex Data</span>';
                    } else {
                        const strValue = String(value);
                        displayValue = strValue.length > 40 ? strValue.substring(0, 40) + '...' : strValue;
                    }

                    const fieldName = formatFieldName(key);
                    htmlList += `<div class="property-item mb-2 p-2 bg-light rounded">
                        <div class="property-key fw-bold text-primary" style="font-size: 11px;">${fieldName}:</div>
                        <div class="property-value" style="font-size: 11px; margin-top: 2px;">${displayValue}</div>
                    </div>`;
                    count++;
                }

                htmlList += '</div>';
                html += htmlList;

                const totalKeys = Object.keys(displayData).filter(k => k !== 'branch_id' && k !== 'branch_name').length;
                if (totalKeys > maxPreview) {
                    html += `<button type="button" class="btn btn-sm btn-info mt-2 w-100 view-properties-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#propertiesModal"
                        data-properties='${JSON.stringify(properties)}'
                        data-log-name="${logName}"
                        data-description="${description}">
                        <i class="fas fa-eye"></i> View All (${totalKeys} fields)
                    </button>`;
                } else {
                    html += `<button type="button" class="btn btn-sm btn-info mt-2 w-100 view-properties-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#propertiesModal"
                        data-properties='${JSON.stringify(properties)}'
                        data-log-name="${logName}"
                        data-description="${description}">
                        <i class="fas fa-eye"></i> View Details
                    </button>`;
                }

                html += '</div>';
                return html;
            }
        });
    </script>
@endsection
