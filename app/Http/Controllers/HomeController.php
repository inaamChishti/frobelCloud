<?php

namespace App\Http\Controllers;

use App\Models\{User, GeneralTimetable};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AccessPermission;
use Illuminate\Support\Facades\Log;



class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */




    // public function branchDashboard()
    // {
    //    return view('branchFrontend.branch_home.branchDashboard');
    // }
    //  public function branchDashboard()
    // {
    //     $today = date('Y-m-d'); // e.g., 2025-08-01
    //     $branchId = session('branch_id'); // Get current branch ID from session

    //     // Fetch branch details
    //     $branch = User::where('branch_id', $branchId)
    //         ->where('is_main_branch', 1)
    //         ->first(['branch_id', 'branch_name']);

    //     if (!$branch) {
    //         // Handle case where branch is not found
    //         return redirect()->back()->with('error', 'Branch not found.');
    //     }

    //     // Fetch today's timetable for the branch
    //     $todayTimetable = GeneralTimetable::whereDate('date', $today)
    //         ->where('branch_id', $branchId)
    //         ->get()
    //         ->map(function ($record) {
    //             // Decode JSON fields
    //             $jsonFields = ['student_ids', 'student_names', 'subjects', 'is_attendance'];
    //             foreach ($jsonFields as $field) {
    //                 $value = $record->$field;
    //                 if (is_string($value)) {
    //                     $cleaned = trim($value, '"');
    //                     $decoded = json_decode($cleaned, true);
    //                     $record->$field = is_array($decoded) ? array_map('strval', $decoded) : [];
    //                 }
    //             }
    //             return $record;
    //         });

    //     // Build student entries
    //     $studentEntries = [];
    //     foreach ($todayTimetable as $record) {
    //         if ($record->date === $today && $record->branch_id === $branchId) {
    //             foreach ($record->student_ids as $index => $id) {
    //                 $id = (string) $id; // Normalize to string
    //                 $studentEntries[] = [
    //                     'family_id' => $id,
    //                     'name' => $record->student_names[$index] ?? 'Unknown',
    //                     'time_slot' => $record->time_slot,
    //                     'teacher_id' => $record->teacher_id,
    //                     'subject' => $record->subjects[$index] ?? 'Unknown',
    //                 ];
    //             }
    //         }
    //     }
    //     $totalStudents = count($studentEntries);

    //     // Log student entries for debugging
    //     Log::info("Branch: {$branch->branch_name}, Student Entries: ", $studentEntries);

    //     // Fetch attendance records for today
    //     $attendanceRecords = Attendance::whereDate('date', $today)
    //         ->where('branch_id', $branchId)
    //         ->get()
    //         ->map(function ($record) {
    //             $record->family_id = (string) $record->family_id; // Normalize to string
    //             return $record;
    //         });

    //     // Log attendance records for debugging
    //     Log::info("Branch: {$branch->branch_name}, Attendance Records: ", $attendanceRecords->toArray());

    //     // Count attended instances
    //     $attendedCount = 0;
    //     foreach ($studentEntries as $entry) {
    //         $familyId = $entry['family_id'];
    //         $timeSlot = $entry['time_slot'];
    //         $teacherId = $entry['teacher_id'];
    //         $subject = $entry['subject'];

    //         // Check for matching attendance record
    //         $matchingAttendance = $attendanceRecords->first(function ($record) use ($familyId, $timeSlot, $teacherId, $subject, $today) {
    //             return $record->family_id === $familyId &&
    //                    $record->time_slot === $timeSlot &&
    //                    $record->teacher_name === $teacherId &&
    //                    $record->subject === $subject &&
    //                    $record->date === $today;
    //         });

    //         if ($matchingAttendance) {
    //             $attendedCount++;
    //         }
    //     }

    //     // Prepare branch stats
    //     $branchStats = [
    //         'branch_id' => $branch->branch_id,
    //         'branch_name' => $branch->branch_name,
    //         'totalStudents' => $totalStudents,
    //         'attendedCount' => $attendedCount,
    //     ];

    //     // Log final stats
    //     Log::info("Branch Stats: ", [$branchStats]);

    //     // Prepare view data
    //     $data = [
    //         'branchStats' => $branchStats,
    //         'today' => $today,
    //     ];

    //     return view('branchFrontend.branch_home.branchDashboard', $data);
    // }

     public function branchDashboard()
    {
        $today = date('Y-m-d');

        // Always derive branch_id from the authenticated user — session can be
        // lost after Auth::login() regenerates it on some server configurations.
        $branchId = session('branch_id');
        if (!$branchId && auth()->check()) {
            $branchId = auth()->user()->branch_id;
            if ($branchId) {
                session(['branch_id' => $branchId]);
            }
        }

        \Log::info('[DASHBOARD] branchDashboard called', [
            'session_branch_id' => session('branch_id'),
            'branchId_used' => $branchId,
            'auth_user_id' => auth()->id(),
            'auth_user_branch_id' => auth()->user()?->branch_id,
            'session_id' => session()->getId(),
        ]);

        $branch = User::where('branch_id', $branchId)
            ->where('is_main_branch', 1)
            ->first(['branch_id', 'branch_name']);

        \Log::info('[DASHBOARD] branch query result', [
            'branchId' => $branchId,
            'branch_found' => $branch ? true : false,
        ]);

        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found.');
        }

        try {

        // Use the same source/logic as central timetable for accurate matching.
        // This keeps branch-dashboard aligned with /central-timetable behavior.
        $apiRequest = new Request(['date' => $today]);
        $centralResponse = app(\App\Http\Controllers\BranchController::class)->getGeneralTimetable($apiRequest);
        $todayTimetable = collect($centralResponse->getData());

        $normalizeJsonField = function ($value) {
            if (is_array($value)) {
                return $value;
            }
            if (is_string($value) && trim($value) !== '') {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                return array_values(array_filter(array_map('trim', explode(',', $value))));
            }
            return [];
        };

        $todayTimetable = $todayTimetable->map(function ($record) use ($normalizeJsonField) {
            $record->student_ids = $normalizeJsonField($record->student_ids ?? []);
            $record->student_names = $normalizeJsonField($record->student_names ?? []);
            $record->subjects = $normalizeJsonField($record->subjects ?? []);
            $record->is_attendance = $normalizeJsonField($record->is_attendance ?? []);
            $record->permanent = $normalizeJsonField($record->permanent ?? []);
            return $record;
        });

        // Determine if today is weekday or weekend (needed for time slot mapping)
        $currentDay = date('N', strtotime($today)); // 1 (Monday) to 7 (Sunday)
        $isWeekday = ($currentDay >= 1 && $currentDay <= 5);
        
        // Time slot mapping for weekday and weekend
        $timeSlotMapping = [
            '1' => [
                'weekday' => '11:00 - 01:00pm',
                'weekend' => '09:00 - 11:00am',
                'label' => 'Lesson 1'
            ],
            '2' => [
                'weekday' => '01:30 - 03:30pm',
                'weekend' => '11:20 - 01:20pm',
                'label' => 'Lesson 2'
            ],
            '3' => [
                'weekday' => '04:30 - 06:30pm',
                'weekend' => '02:00 - 04:00pm',
                'label' => 'Lesson 3'
            ],
            '4' => [
                'weekday' => '06:45 - 08:45pm',
                'weekend' => '',
                'label' => 'Lesson 4'
            ],
        ];
        
        // Initialize arrays for grouping by slot
        $attendedStudentsBySlot = [
            '1' => [],
            '2' => [],
            '3' => [],
            '4' => []
        ];
        
        $absentStudentsBySlot = [
            '1' => [],
            '2' => [],
            '3' => [],
            '4' => []
        ];
        
        // Track processed students per slot+teacher to avoid duplicates within same class
        // Key format: slot|teacher_id|family_id|student_name
        $processedStudents = [];
        
        // Counters
        $totalStudents = 0;
        $attendedCount = 0;
        $absentCount = 0;
        $switchTotalCount = 0;
        $switchAttendedCount = 0;
        $switchAbsentCount = 0;
        $regularTotalCount = 0;
        $regularAttendedCount = 0;
        $regularAbsentCount = 0;
        
        // Build active student identity map (admissionid + full name) for accurate filtering.
        // Admission ID alone is not enough because siblings can share the same admission/family id.
        $activeStudentRows = DB::table('studentdata')
            ->where('branch_id', $branchId)
            ->whereRaw('LOWER(student_status) = ?', ['active'])
            ->select('admissionid', 'studentname', 'studentsur')
            ->get()
            ->toArray();
        $activeStudentIdentityMap = [];
        foreach ($activeStudentRows as $row) {
            $admissionId = trim((string)($row->admissionid ?? ''));
            $fullName = trim(preg_replace('/\s+/u', ' ', (string)(($row->studentname ?? '') . ' ' . ($row->studentsur ?? ''))));
            if ($admissionId === '' || $fullName === '') {
                continue;
            }
            $activeStudentIdentityMap[strtolower($admissionId . '|' . $fullName)] = true;
        }
        
        // Process each timetable record (now deduplicated by teacher+slot)
        foreach ($todayTimetable as $record) {
            if (empty($record->student_ids) || !is_array($record->student_ids)) {
                continue;
            }
            
            $studentIds = $record->student_ids ?? [];
            
            $slotNumber = (string)($record->slot ?? '');
            if (empty($slotNumber) || !isset($timeSlotMapping[$slotNumber])) {
                continue;
            }
            
            $studentNames = $record->student_names ?? [];
            $subjects = $record->subjects ?? [];
            $isAttendanceArray = $record->is_attendance ?? [];
            $permanentArray = $record->permanent ?? [];
            $teacherId = trim((string)($record->teacher_id ?? ''));
            
            // Get time slot display
            $timeSlotDisplay = $isWeekday 
                ? $timeSlotMapping[$slotNumber]['weekday'] 
                : $timeSlotMapping[$slotNumber]['weekend'];
            
            if (empty($timeSlotDisplay)) {
                continue; // Skip if no time slot for this day type
            }
            
            // Process each student in this record
            foreach ($studentIds as $index => $studentId) {
                if (empty($studentId)) {
                    continue;
                }
                
                $studentId = (string)$studentId;
                
                $studentName = isset($studentNames[$index]) ? trim((string)$studentNames[$index]) : '';
                
                if (empty($studentName) || $studentName === 'Unknown') {
                    continue;
                }

                // Check active status by admission id + name (accurate per student, not per family only).
                $normalizedStudentName = trim(preg_replace('/\s+/u', ' ', $studentName));
                $activeKey = strtolower($studentId . '|' . $normalizedStudentName);
                if (!isset($activeStudentIdentityMap[$activeKey])) {
                    continue; // Skip inactive or unmatched student
                }
                
                $subject = isset($subjects[$index]) ? trim((string)$subjects[$index]) : 'Unknown';
                $attendanceStatus = isset($isAttendanceArray[$index]) ? strtolower(trim((string)$isAttendanceArray[$index])) : 'no';
                $isSwitch = isset($permanentArray[$index]) && strtolower(trim((string)$permanentArray[$index])) === 'no';
                
                // Create unique key for this student in this class (slot + teacher + student + subject)
                // Include subject to handle cases where same student has multiple subjects with same teacher
                $studentKey = $slotNumber . '|' . $teacherId . '|' . $studentId . '|' . $studentName . '|' . $subject;
                
                // Skip exact duplicate for same class student/subject combination.
                if (isset($processedStudents[$studentKey])) {
                    continue; // Skip duplicate student entry in same class with same subject
                }
                
                // Mark this student+subject combination as processed
                $processedStudents[$studentKey] = true;
                
                // Student data
                $studentData = [
                    'family_id' => $studentId,
                    'student_name' => $studentName,
                    'subject' => $subject,
                    'time_slot' => $timeSlotDisplay,
                    'teacher_name' => $teacherId,
                    'is_switch' => $isSwitch,
                ];
                
                $totalStudents++;
                
                // Check attendance status from is_attendance field
                if ($attendanceStatus === 'yes') {
                    // Student attended
                    $attendedCount++;
                    $attendedStudentsBySlot[$slotNumber][] = $studentData;
                    
                    if ($isSwitch) {
                        $switchAttendedCount++;
                        $switchTotalCount++;
                    } else {
                        $regularAttendedCount++;
                        $regularTotalCount++;
                    }
                } else {
                    // Student absent
                    $absentCount++;
                    $absentStudentsBySlot[$slotNumber][] = $studentData;
                    
                    if ($isSwitch) {
                        $switchAbsentCount++;
                        $switchTotalCount++;
                    } else {
                        $regularAbsentCount++;
                        $regularTotalCount++;
                    }
                }
            }
        }
        
        // Prepare branch stats
        $branchStats = [
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
            'totalStudents' => $totalStudents,
            'attendedCount' => $attendedCount,
            'absentCount' => $absentCount,
            'switchTotalCount' => $switchTotalCount,
            'switchAttendedCount' => $switchAttendedCount,
            'switchAbsentCount' => $switchAbsentCount,
            'regularTotalCount' => $regularTotalCount,
            'regularAttendedCount' => $regularAttendedCount,
            'regularAbsentCount' => $regularAbsentCount,
        ];

        // Prepare slot information for view
        $slotInfo = [];
        foreach ($timeSlotMapping as $slotNumber => $slotData) {
            // Skip slot 4 on weekends
            if (!$isWeekday && $slotNumber == '4' && empty($slotData['weekend'])) {
                continue;
            }
            
            $students = $attendedStudentsBySlot[$slotNumber] ?? [];
            $switchCount = 0;
            foreach ($students as $student) {
                if ($student['is_switch'] ?? false) {
                    $switchCount++;
                }
            }
            
            $timeSlotDisplay = $isWeekday ? $slotData['weekday'] : $slotData['weekend'];
            
            $slotInfo[$slotNumber] = [
                'label' => $slotData['label'],
                'time_slot' => $timeSlotDisplay,
                'students' => $students,
                'switch_count' => $switchCount,
                'regular_count' => count($students) - $switchCount,
            ];
        }

        // Prepare absent slot information for view
        $absentSlotInfo = [];
        foreach ($timeSlotMapping as $slotNumber => $slotData) {
            // Skip slot 4 on weekends
            if (!$isWeekday && $slotNumber == '4' && empty($slotData['weekend'])) {
                continue;
            }
            
            $students = $absentStudentsBySlot[$slotNumber] ?? [];
            $switchCount = 0;
            foreach ($students as $student) {
                if ($student['is_switch'] ?? false) {
                    $switchCount++;
                }
            }
            
            $timeSlotDisplay = $isWeekday ? $slotData['weekday'] : $slotData['weekend'];
            
            $absentSlotInfo[$slotNumber] = [
                'label' => $slotData['label'],
                'time_slot' => $timeSlotDisplay,
                'students' => $students,
                'switch_count' => $switchCount,
                'regular_count' => count($students) - $switchCount,
            ];
        }

        // Prepare view data
        $data = [
            'branchStats' => $branchStats,
            'today' => $today,
            'slotInfo' => $slotInfo,
            'absentSlotInfo' => $absentSlotInfo,
            'isWeekday' => $isWeekday,
        ];

        return view('branchFrontend.branch_home.branchDashboard', $data);
        } catch (\Throwable $e) {
            \Log::error('[DASHBOARD] Exception in branchDashboard', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        }
    }
}
