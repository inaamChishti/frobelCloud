@extends('layouts.branchDashboardApp')

@section('content')
{{-- Loading Overlay --}}
<div id="dataProcessingLoader" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 shadow-2xl flex flex-col items-center border border-gray-200">
        <div class="loader-spinner border-4 border-blue-200 border-t-blue-600 rounded-full w-16 h-16 animate-spin mb-4"></div>
        <p class="text-gray-700 text-lg font-semibold">Please wait...</p>
        <p class="text-gray-500 text-sm mt-2">Processing attendance data</p>
    </div>
</div>

<div class="main-content max-w-7xl mx-auto p-6 font-sans">
    <h1 class="text-3xl font-bold text-center mb-8 text-gray-800" id="title">
        Today's Summary for {{ $branchStats['branch_name'] }} ({{ \Carbon\Carbon::parse($today)->format('d/m/Y') }})
    </h1>

    <div class="branch-card bg-white rounded-lg shadow-lg p-6 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">{{ $branchStats['branch_name'] }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="space-y-3">
                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-1">Overall Statistics</h4>
                <p class="text-gray-600">
                    <strong>Total Students Scheduled:</strong>
                    <span class="count font-medium text-blue-600" data-target="{{ $branchStats['totalStudents'] }}">0</span>
                </p>
                <p class="text-gray-600">
                    <strong>Students Attended:</strong>
                    <span class="count font-medium text-green-600" data-target="{{ $branchStats['attendedCount'] }}">0</span>
                </p>
                <p class="text-gray-600">
                    <strong>Students Absent:</strong>
                    <span class="count font-medium text-red-600" data-target="{{ $branchStats['absentCount'] }}">0</span>
                </p>
            </div>
            {{-- <div class="space-y-3">
                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-1">Switch/Temporary Students</h4>
                <p class="text-gray-600">
                    <strong>Total Switch Students:</strong>
                    <span class="count font-medium text-purple-600" data-target="{{ $branchStats['switchTotalCount'] }}">0</span>
                </p>
                <p class="text-gray-600">
                    <strong>Switch Attended:</strong>
                    <span class="count font-medium text-green-600" data-target="{{ $branchStats['switchAttendedCount'] }}">0</span>
                </p>
                <p class="text-gray-600">
                    <strong>Switch Absent:</strong>
                    <span class="count font-medium text-red-600" data-target="{{ $branchStats['switchAbsentCount'] }}">0</span>
                </p>
            </div> --}}
        </div>
        {{-- <div class="mt-4">
            <canvas id="chart-{{ $branchStats['branch_name'] }}" class="w-full h-40"></canvas>
        </div> --}}
    </div>

    {{-- Attended Students by Time Slot --}}
    <div class="attended-students-by-slot bg-white rounded-lg shadow-lg p-6 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl mb-8">
        <h3 class="text-xl font-semibold mb-6 text-gray-700">Attendance: {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($slotInfo as $slotNumber => $slot)
                @php
                    $colors = [
                        '1' => ['bg' => '#B3D9FF', 'text' => '#1976D2'], // Very Light Blue
                        '2' => ['bg' => '#C8E6C9', 'text' => '#388E3C'], // Very Light Green
                        '3' => ['bg' => '#FF9999', 'text' => '#8B0000'], // Light Red (more distinct)
                        '4' => ['bg' => '#FFC0CB', 'text' => '#C2185B'], // Light Pink (more distinct)
                    ];
                    $color = $colors[$slotNumber] ?? $colors['1'];
                    $studentCount = count($slot['students'] ?? []);
                @endphp
                <div class="slot-section bg-gray-50 rounded-lg p-4 border-2 border-gray-200 min-h-[200px]">
                    <div class="slot-header mb-4 pb-3 border-b border-gray-300" style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 10px; border-radius: 5px; margin: -16px -16px 16px -16px; border: none;">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="text-lg font-semibold" style="color: {{ $color['text'] }};">{{ $slot['label'] }}</h4>
                            <span class="text-sm font-bold" style="color: {{ $color['text'] }};">({{ $studentCount }})</span>
                        </div>
                        @if (isset($slot['switch_count']) && $slot['switch_count'] > 0)
                            <div class="text-xs mt-1" style="color: {{ $color['text'] }};">
                                <span class="opacity-75">Switch: {{ $slot['switch_count'] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="slot-students max-h-96 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                        @if (!empty($slot['students']))
                            <div class="space-y-2">
                                @foreach ($slot['students'] as $student)
                                    <div class="student-card bg-white rounded-md p-3 border {{ ($student['is_switch'] ?? false) ? 'border-purple-300 bg-purple-50' : 'border-gray-200' }} hover:bg-green-50 hover:border-green-300 transition-all duration-200">
                                        @if ($student['is_switch'] ?? false)
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold text-purple-700 bg-purple-200 rounded mb-2">Switch</span>
                                        @endif
                                        <p class="text-xs font-medium text-gray-700 mb-1">
                                            <strong>ID:</strong> {{ $student['family_id'] }}
                                        </p>
                                        <p class="text-xs text-gray-600 mb-1">
                                            <strong>Name:</strong> {{ $student['student_name'] }}
                                        </p>
                                        <p class="text-xs text-gray-600 mb-1">
                                            <strong>Subject:</strong> {{ $student['subject'] }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            <strong>Teacher:</strong> {{ $student['teacher_name'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-400 py-8">
                                <p class="text-sm">No attendance marked</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Absent Students by Time Slot --}}
    <div class="absent-students bg-white rounded-lg shadow-lg p-6 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <h3 class="text-xl font-semibold mb-6 text-gray-700">Absent Students</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($absentSlotInfo as $slotNumber => $slot)
                @php
                    $colors = [
                        '1' => ['bg' => '#B3D9FF', 'text' => '#1976D2'], // Very Light Blue
                        '2' => ['bg' => '#C8E6C9', 'text' => '#388E3C'], // Very Light Green
                        '3' => ['bg' => '#FF9999', 'text' => '#8B0000'], // Light Red (more distinct)
                        '4' => ['bg' => '#FFC0CB', 'text' => '#C2185B'], // Light Pink (more distinct)
                    ];
                    $color = $colors[$slotNumber] ?? $colors['1'];
                    $studentCount = count($slot['students'] ?? []);
                @endphp
                <div class="slot-section bg-gray-50 rounded-lg p-4 border-2 border-gray-200 min-h-[200px]">
                    <div class="slot-header mb-4 pb-3 border-b border-gray-300" style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 10px; border-radius: 5px; margin: -16px -16px 16px -16px; border: none;">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="text-lg font-semibold" style="color: {{ $color['text'] }};">{{ $slot['label'] }}</h4>
                            <span class="text-sm font-bold" style="color: {{ $color['text'] }};">({{ $studentCount }})</span>
                        </div>
                        @if (isset($slot['switch_count']) && $slot['switch_count'] > 0)
                            <div class="text-xs mt-1" style="color: {{ $color['text'] }};">
                                <span class="opacity-75">Switch: {{ $slot['switch_count'] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="slot-students max-h-96 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                        @if (!empty($slot['students']))
                            <div class="space-y-2">
                                    @foreach ($slot['students'] as $student)
                                        <div class="student-card bg-white rounded-md p-3 border {{ ($student['is_switch'] ?? false) ? 'border-purple-300 bg-purple-50' : 'border-red-200' }} hover:bg-red-50 hover:border-red-400 transition-all duration-200">
                                            @if ($student['is_switch'] ?? false)
                                                <span class="inline-block px-2 py-0.5 text-xs font-semibold text-purple-700 bg-purple-200 rounded mb-2">Switch</span>
                                            @endif
                                            <p class="text-xs font-medium text-gray-700 mb-1">
                                                <strong>ID:</strong> {{ $student['family_id'] }}
                                            </p>
                                            <p class="text-xs text-gray-600 mb-1">
                                                <strong>Name:</strong> {{ $student['student_name'] }}
                                            </p>
                                            <p class="text-xs text-gray-600 mb-1">
                                                <strong>Subject:</strong> {{ $student['subject'] }}
                                            </p>
                                            <p class="text-xs text-gray-600">
                                                <strong>Teacher:</strong> {{ $student['teacher_name'] }}
                                            </p>
                                        </div>
                                    @endforeach
                            </div>
                        @else
                            <div class="text-center text-gray-400 py-8">
                                <p class="text-sm">No absent students</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .branch-card, .absent-students, .attended-students-by-slot {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    #title {
        animation: fadeInUp 0.5s ease-out;
    }

    /* Custom scrollbar styles */
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    .scrollbar-thin::-webkit-scrollbar {
        width: 8px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 4px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .student-card {
            font-size: 0.875rem;
            padding: 0.75rem;
        }
    }

    /* Loader styles */
    #dataProcessingLoader {
        display: flex;
    }

    #dataProcessingLoader.hidden {
        display: none;
    }

    .loader-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    $(document).ready(function() {
        // Show loader
        $('#dataProcessingLoader').removeClass('hidden');
        
        // Get total and attended counts
        const totalStudents = {{ $branchStats['totalStudents'] }};
        const attendedCount = {{ $branchStats['attendedCount'] }};
        let absentCount = {{ $branchStats['absentCount'] }};

        // Count-up animation for numbers (will be updated after filtering)
        function animateCount($element, targetValue) {
            $element.prop('Counter', parseInt($element.text()) || 0).animate({
                Counter: targetValue
            }, {
                duration: 800,
                easing: 'swing',
                step: function(now) {
                    $element.text(Math.ceil(now));
                },
                complete: function() {
                    $element.text(targetValue);
                }
            });
        }
        
        // Initial count animation
        $('.count').each(function() {
            const target = parseInt($(this).data('target')) || 0;
            animateCount($(this), target);
        });

        // Update counts for a slot
        function updateSlotCounts($slot) {
            const visibleCount = $slot.find('.student-card:visible').length;
            const switchCount = $slot.find('.student-card:visible').filter(function() {
                return $(this).find('span:contains("Switch")').length > 0;
            }).length;
            
            // Update total count - find the span with count inside the flex div
            const $countSpan = $slot.find('.slot-header .flex span.text-sm.font-bold');
            if ($countSpan.length > 0) {
                $countSpan.text('(' + visibleCount + ')');
            }
            
            // Update switch count
            let $switchInfo = $slot.find('.slot-header .text-xs');
            if (switchCount > 0) {
                if ($switchInfo.length > 0) {
                    $switchInfo.find('span').text('Switch: ' + switchCount);
                } else {
                    // Get color from existing header for consistency
                    const headerColor = $slot.find('.slot-header h4').css('color') || '#1976D2';
                    $slot.find('.slot-header .flex').after('<div class="text-xs mt-1" style="color: ' + headerColor + ';"><span class="opacity-75">Switch: ' + switchCount + '</span></div>');
                }
            } else if ($switchInfo.length > 0) {
                $switchInfo.remove();
            }
        }
        
        // Filter duplicate students: if same student appears in both attendance and absent,
        // and one is switch and one is not, keep switch and hide non-switch
        // Match by family_id + subject + teacher to avoid hiding legitimate different subjects
        function filterDuplicateStudents(callback) {
            // Build map of attendance slots by label
            const attendanceSlotsMap = new Map();
            $('.attended-students-by-slot .slot-section').each(function() {
                const $slot = $(this);
                const label = $slot.find('.slot-header h4').text().trim();
                attendanceSlotsMap.set(label, $slot);
            });
            
            // Process each absent slot and match with attendance slot by label
            $('.absent-students .slot-section').each(function() {
                const $absentSlot = $(this);
                const label = $absentSlot.find('.slot-header h4').text().trim();
                const $attendanceSlot = attendanceSlotsMap.get(label);
                
                if (!$attendanceSlot || $attendanceSlot.length === 0) {
                    // Update absent slot count even if no matching attendance slot
                    updateSlotCounts($absentSlot);
                    return;
                }
                
                // Build maps: family_id|subject|teacher -> {isSwitch, $card}
                // This ensures we only hide true duplicates (same student, same subject, same teacher)
                const attendanceMap = new Map();
                $attendanceSlot.find('.student-card').each(function() {
                    const $card = $(this);
                    const familyId = $card.find('p:contains("ID:")').text().replace('ID:', '').trim();
                    const subject = $card.find('p:contains("Subject:")').text().replace('Subject:', '').trim();
                    const teacher = $card.find('p:contains("Teacher:")').text().replace('Teacher:', '').trim();
                    const isSwitch = $card.find('span:contains("Switch")').length > 0;
                    if (familyId && subject && teacher) {
                        const key = familyId + '|' + subject + '|' + teacher;
                        attendanceMap.set(key, { isSwitch: isSwitch, $card: $card });
                    }
                });
                
                const absentMap = new Map();
                $absentSlot.find('.student-card').each(function() {
                    const $card = $(this);
                    const familyId = $card.find('p:contains("ID:")').text().replace('ID:', '').trim();
                    const subject = $card.find('p:contains("Subject:")').text().replace('Subject:', '').trim();
                    const teacher = $card.find('p:contains("Teacher:")').text().replace('Teacher:', '').trim();
                    const isSwitch = $card.find('span:contains("Switch")').length > 0;
                    if (familyId && subject && teacher) {
                        const key = familyId + '|' + subject + '|' + teacher;
                        absentMap.set(key, { isSwitch: isSwitch, $card: $card });
                    }
                });
                
                // Compare and hide non-switch duplicates (only if exact match: same student, subject, teacher)
                attendanceMap.forEach((attStudent, key) => {
                    if (absentMap.has(key)) {
                        const absStudent = absentMap.get(key);
                        // If one is switch and one is not
                        if (attStudent.isSwitch && !absStudent.isSwitch) {
                            // Switch in attendance, hide non-switch from absent
                            absStudent.$card.hide();
                        } else if (!attStudent.isSwitch && absStudent.isSwitch) {
                            // Switch in absent, hide non-switch from attendance
                            attStudent.$card.hide();
                        } else if (!attStudent.isSwitch && !absStudent.isSwitch) {
                            // Both are non-switch - this shouldn't happen (same student can't be both attended and absent)
                            // But if it does, hide the absent one since attendance takes precedence
                            absStudent.$card.hide();
                        }
                    }
                });
                
                // Update counts for both slots after filtering
                updateSlotCounts($attendanceSlot);
                updateSlotCounts($absentSlot);
            });
            
            // Update counts for attendance slots that don't have matching absent slots
            $('.attended-students-by-slot .slot-section').each(function() {
                const $slot = $(this);
                const label = $slot.find('.slot-header h4').text().trim();
                let hasMatch = false;
                $('.absent-students .slot-section').each(function() {
                    if ($(this).find('.slot-header h4').text().trim() === label) {
                        hasMatch = true;
                        return false;
                    }
                });
                if (!hasMatch) {
                    updateSlotCounts($slot);
                }
            });
            
            // Recalculate total absent count after filtering
            let totalVisibleAbsent = 0;
            $('.absent-students .slot-section').each(function() {
                totalVisibleAbsent += $(this).find('.student-card:visible').length;
            });
            
            // Update the overall absent count display
            // Find the absent count element (the one showing "Students Absent")
            const $absentCountElements = $('.count').filter(function() {
                const $parent = $(this).closest('p');
                return $parent.find('strong').text().includes('Students Absent') || 
                       $parent.find('strong').text().includes('Absent');
            });
            
            if ($absentCountElements.length > 0) {
                const $absentCountElement = $absentCountElements.first();
                $absentCountElement.attr('data-target', totalVisibleAbsent);
                // Update the count with animation
                animateCount($absentCountElement, totalVisibleAbsent);
                // Update the global variable
                absentCount = totalVisibleAbsent;
            }
            
            // Execute callback if provided
            if (typeof callback === 'function') {
                callback();
            }
        }
        
        // Initialize chart variable (will be updated after filtering)
        let attendanceChart = null;
        
        // Function to initialize or update chart
        function initializeChart(finalAbsentCount) {
            const chartElement = document.getElementById('chart-{{ $branchStats['branch_name'] }}');
            if (chartElement) {
                if (attendanceChart) {
                    // Update existing chart
                    attendanceChart.data.datasets[0].data = [totalStudents, attendedCount, finalAbsentCount];
                    attendanceChart.update();
                } else {
                    // Create new chart
                    attendanceChart = new Chart(chartElement.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Scheduled', 'Attended', 'Absent'],
                            datasets: [{
                                label: '{{ $branchStats['branch_name'] }} Stats',
                                data: [totalStudents, attendedCount, finalAbsentCount],
                                backgroundColor: ['#3b82f6', '#10b981', '#ef4444'], // Blue, Green, Red
                                borderColor: ['#2563eb', '#059669', '#dc2626'], // Darker shades for borders
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { backgroundColor: '#1f2937' }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { color: '#4b5563' }
                                },
                                x: {
                                    ticks: { color: '#4b5563' }
                                }
                            }
                        }
                    });
                }
            }
        }
        
        // Initialize chart with original count first
        initializeChart(absentCount);
        
        // Run filter after page loads
        filterDuplicateStudents(function() {
            // Update chart with corrected absent count
            initializeChart(absentCount);
            
            // Hide loader after processing completes
            setTimeout(function() {
                $('#dataProcessingLoader').addClass('hidden');
            }, 300);
        });
    });
</script>
@endsection
