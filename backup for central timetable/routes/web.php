<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Http\Request;



use App\Models\{User, AccessPermission};
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/svg-to-png', function () {
    return view('svg-to-png');
})->name('svg-to-png');

Route::get('/auth', function () {
    // Check if user is authenticated
    if (auth()->check()) {
        // Get all details of the authenticated user
        $user = auth()->user();

        // Dump and die with all user details
        dd($user->toArray());
    }

    return 'No authenticated user found.';
})->middleware('auth');


Route::get('/clear-config', function () {
    // Config cache clear karna
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');

//     DB::statement("
//     DELETE FROM general_timetables
//     WHERE date >= '2025-11-28'
//       AND date <= '2027-06-30'
//       AND WEEKDAY(date) = 4
// ");


    return "✅ Config cache cleared successfully!";
})->middleware('auth');

Route::get('/migrate', function () {

    $migrations = [
        // 'database/migrations/2025_08_07_123157_add_additional_info_to_attendance_table.php',
        // database\migrations\2025_08_07_123617_add_additional_info_to_general_timetables_table.php
        // database\migrations 'database/migrations/2025_08_19_133213_add_subject_sessions_grades_to_studentdata_table.php',
        //database\migrations\2025_08_19_164316_alter_studenthours_column_in_studentdata_table.php
        //2025_08_20_110023_add_performance_and_behaviour_to_attendance_table.php
        // database\migrations\2025_08_27_093920_add_terms_and_conditions_to_student_requests_table.php
        'database/migrations/2025_08_27_093920_add_terms_and_conditions_to_student_requests_table.php',
        'database/migrations/2025_08_27_100201_add_pdf_name_to_student_requests_table.php',
        'database/migrations/2026_06_10_000001_add_is_flag_to_studentdata_table.php',
        'database/migrations/2026_08_18_000001_add_block_columns_to_admission_table.php',
    ];

    foreach ($migrations as $migration) {
        Artisan::call('migrate', [
            '--path' => $migration,
            '--force' => true,
        ]);
    }

    return 'Selected migrations have been run successfully.';
});

Route::get('/migrate-flag', function () {
    Artisan::call('migrate', [
        '--path'  => 'database/migrations/2026_06_10_000001_add_is_flag_to_studentdata_table.php',
        '--force' => true,
    ]);

    return '✅ is_flag migration ran successfully!';
});


// Route::get('/checkk', function () {
//     // dd(User::all());
// });



Route::get('/logoutt', function () {
    Auth::logout();
    return redirect('/login'); // Redirect to login page after logout
})->name('logout');


Route::get('/', function () {
    return redirect('login');
});

Route::get('/check-sid', function () {
    try {
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $message = $twilio->messages('MMa4b809011b6084b0d50c776a1b090d1f')->fetch();

        return response()->json([
            'status' => $message->status,
            'error_code' => $message->errorCode,
            'error_message' => $message->errorMessage,
            'to' => $message->to,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-whatsapp-pdf', function () {
    $sid = env('TWILIO_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from = env('TWILIO_WHATSAPP_FROM');
    $to = 'whatsapp:+923068649342';

    // Path to your existing PDF file
    $pdfPath = public_path('receipts/receipt_1755535825_18082511.pdf');

    // Verify file exists
    if (!file_exists($pdfPath)) {
        return response()->json([
            'success' => false,
            'message' => 'PDF file not found',
            'path' => $pdfPath
        ]);
    }

    try {
        $twilio = new Client($sid, $token);

        // Generate public URL to the PDF
        $mediaUrl = url('receipts/receipt_1755535825_18082511.pdf');

        // Verify URL is accessible
        if (!@get_headers($mediaUrl)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF URL not accessible',
                'url' => $mediaUrl
            ]);
        }

        // Send the message
        $message = $twilio->messages->create($to, [
            'from' => $from,
            'body' => 'Test PDF Attachment from Live Server',
            'mediaUrl' => [$mediaUrl]
        ]);

        return response()->json([
            'success' => true,
            'message_sid' => $message->sid,
            'pdf_url' => $mediaUrl,
            'file_size' => filesize($pdfPath) . ' bytes'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});
Route::get('/test', function () {
    $sid    = env('TWILIO_SID');
    $token  = env('TWILIO_AUTH_TOKEN');
    $from   = env('TWILIO_WHATSAPP_FROM');

    // yahan static number rakha gaya hai
    $to     = "whatsapp:+923068649342";

    $twilio = new Client($sid, $token);

    $message = $twilio->messages->create(
        $to,
        [
            "from" => $from,
            "body" => "Dev Test Message via Laravel Twilio WhatsApp ✅"
        ]
    );

    return "Message Sent Successfully! SID: " . $message->sid;
});


Auth::routes();

Route::get('public-page', [App\Http\Controllers\PublicController::class, 'examReg'])->name('exam.registration');
Route::get('public-page-hayes', [App\Http\Controllers\PublicController::class, 'examRegHayes'])->name('exam.registration');
Route::get('public-page-application-forms', [App\Http\Controllers\PublicController::class, 'examRegApplicationForm'])->name('exam.registration');

Route::post('send-student', [App\Http\Controllers\PublicController::class, 'studentRequest'])->name('studentRequest');

Route::get('public-learner', [App\Http\Controllers\PublicController::class, 'publicLearner'])->name('publicLearner');


Route::post('/custom-login', [App\Http\Controllers\Auth\RegisterController::class, 'customlogin'])->name('custom.login');



Route::middleware(['checkLoginSession'])->group(function () {
    Route::get('/branch-dashboard', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))->where('user_id', auth()->id())->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // if (($pages['dashboard'] ?? '') !== 'on') {
        //     abort(403, 'You are not authorized to access branch dashboard');
        // }

        return app(App\Http\Controllers\HomeController::class)->branchDashboard(request());
    })->name('branch.dashboard');

    /**
     * Count distinct students (unique family_id) who attended at least 1 lesson in the centre
     * for Year 10 and Year 11 within the requested academic-year windows.
     *
     * Windows:
     * - Sep 2024 - Aug 2025  => 2024-09-01 .. 2025-08-31
     * - Sep 2025 - Aug 2026  => 2025-09-01 .. 2026-08-31
     *
     * NOTE: All logic intentionally kept inside the route closure per request.
     */
    Route::get('/branch-dashboard/year10-11-centre-counts', function () {
        // As requested: use attendance table only, for a specific centre
        $branchId = 'barking_centre_150';
    
        $periods = [
            'sep_2024_aug_2025' => ['start' => '2024-09-01', 'end' => '2025-08-31'],
            'sep_2025_aug_2026' => ['start' => '2025-09-01', 'end' => '2026-08-31'],
        ];

        // Extract year 10/11 from the year string; ignore alphabets/symbols
        $extractYearNum = function ($raw) {
            if ($raw === null) return null;
            $s = strtolower(trim((string) $raw));
            if ($s === '') return null;

            // Handles: "Year 10", "year 10", "y-10", "y10", "10" (and same for 11)
            // Prefer an explicit 10/11 match anywhere in the string.
            if (preg_match('/\b(10|11)\b/', $s, $m)) {
                return (int) $m[1];
            }

            // Fallback: keep digits only (ignore alphabets/symbols), e.g. "Y-10" -> "10"
            $digits = preg_replace('/\D+/', '', $s);
            if (!$digits) return null;

            // If digits are exactly 10 or 11, accept; otherwise ignore.
            return ($digits === '10' || $digits === '11') ? (int) $digits : null;
        };

        /**
         * For each period, count unique students (family_id + student_name) who have
         * at least 1 attendance record in that period.
         * Also, only count Year 10 / Year 11 by extracting numeric year from student_year_in_school.
         */
        $getAttendanceRows = function (string $start, string $end) use ($branchId) {
            return DB::table('attendance')
                ->select('family_id', 'student_name', 'student_year_in_school', 'status')
                ->where('branch_id', $branchId)
                ->whereNotNull('family_id')
                ->where('family_id', '!=', '')
                ->whereNotNull('student_name')
                ->where('student_name', '!=', '')
                ->whereBetween('date', [$start, $end])
                ->get();
        };
    
        // SQL-style count like:
        // SELECT COUNT(DISTINCT CONCAT(family_id,'_',student_name)) ...
        // We use REGEXP so student_year_in_school can be "Year 10", "y-10", "y10", "10" (and same for 11).
        $countStudentsLikeSql = function (string $start, string $end, int $year) use ($branchId) {
            $yearRegex = '(^|[^0-9])' . $year . '([^0-9]|$)';

            $row = DB::table('attendance')
                ->selectRaw("COUNT(DISTINCT CONCAT(family_id, '_', student_name)) AS total_students")
                ->where('branch_id', $branchId)
                ->whereNotNull('family_id')
                ->where('family_id', '!=', '')
                ->whereNotNull('student_name')
                ->where('student_name', '!=', '')
                ->whereBetween('date', [$start, $end])
                ->whereRaw("LOWER(TRIM(student_year_in_school)) REGEXP ?", [$yearRegex])
                ->first();

            return (int) ($row->total_students ?? 0);
        };

        $results = [];
        foreach ($periods as $key => $range) {
            $y10 = $countStudentsLikeSql($range['start'], $range['end'], 10);
            $y11 = $countStudentsLikeSql($range['start'], $range['end'], 11);

            $results[$key] = [
                'start' => $range['start'],
                'end' => $range['end'],
                'year_10_students' => $y10,
                'year_11_students' => $y11,
                'unique_attended_students_total' => ($y10 + $y11),
            ];
        }
    
        // HTML Table/Grid generate
        $html = '<h2>Branch ID: ' . $branchId . '</h2>';
        $html .= '<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
        $html .= '<thead style="background-color: #f2f2f2;"><tr>';
        $html .= '<th>Period</th><th>Start Date</th><th>End Date</th><th>Year 10 Students</th><th>Year 11 Students</th><th>Total Unique Attended</th>';
        $html .= '</tr></thead><tbody>';
    
        foreach ($results as $period => $data) {
            $html .= '<tr>';
            $html .= '<td>' . $period . '</td>';
            $html .= '<td>' . $data['start'] . '</td>';
            $html .= '<td>' . $data['end'] . '</td>';
            $html .= '<td>' . $data['year_10_students'] . '</td>';
            $html .= '<td>' . $data['year_11_students'] . '</td>';
            $html .= '<td>' . ($data['unique_attended_students_total'] ?? '') . '</td>';
            $html .= '</tr>';
        }
    
        $html .= '</tbody></table>';
        // $html .= '<p>Generated at: ' . \Carbon\Carbon::now()->toDateTimeString() . '</p>';
    
        return response($html);
    })->middleware('auth')
      ->name('branch.dashboard.year10_11_centre_counts');
    // Route::get('/branch-dashboard', [App\Http\Controllers\HomeController::class, 'branchDashboard'])->name('branch.dashboard');
    // Route::get('/add-branch-users', [App\Http\Controllers\BranchController::class, 'addBranchUsers'])->name('add.branch.users');
    Route::get('/add-branch-users', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['Add_Users'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to add branch users');
        }

        return app(App\Http\Controllers\BranchController::class)->addBranchUsers(request());
    })->name('add.branch.users');
    Route::post('/admin/users/store', [App\Http\Controllers\BranchController::class, 'store'])->name('admin.user.store');
    Route::get('/admin/users/{id}/edit', [App\Http\Controllers\BranchController::class, 'edit']);
    Route::delete('/admin/users/delete/{id}', [App\Http\Controllers\BranchController::class, 'destroy']);
    // Route::get('/add-branch-roles', [App\Http\Controllers\BranchController::class, 'AddBranchRoles']);
    Route::get('/add-branch-roles', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['add_roles'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to add branch roles');
        }

        return app(App\Http\Controllers\BranchController::class)->AddBranchRoles();
    })->name('branch.roles.index');
    // Route::get('/add-branch-roles', [App\Http\Controllers\BranchController::class, 'AddBranchRoles'])->name('branch.roles.index');
    Route::post('/branch/roles/store', [App\Http\Controllers\BranchController::class, 'storeRole'])->name('branch.roles.store');
    Route::get('/branch/roles/{id}/edit', [App\Http\Controllers\BranchController::class, 'editRole'])->name('branch.roles.edit');
    Route::delete('/branch/roles/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteRole'])->name('branch.roles.delete');
    // Route::get('/create-admission', [App\Http\Controllers\BranchController::class, 'createBranchAdmission']);
    Route::get('/create-admission', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['admission'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to create admissions');
        }

        return app(App\Http\Controllers\BranchController::class)->createBranchAdmission(request());
    })->name('create.admission');
    Route::post('admissions', [App\Http\Controllers\BranchController::class, 'storeBranchAdmission'])->name('admin.admission.store');
    // Route::get('searchStudent', [App\Http\Controllers\BranchController::class, 'searchStudent'])->name('admin.admission.index');
    Route::get('searchStudent', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['search_student'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to search students');
        }

        return app(App\Http\Controllers\BranchController::class)->searchStudent(request());
    })->name('admin.admission.index');
    // Route::get('getadmissions', [App\Http\Controllers\BranchController::class, 'getadmissions'])->name('admission.getadmissions');\
    Route::get('getadmissions', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['search_student'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access admissions');
        }

        return app(App\Http\Controllers\BranchController::class)->getadmissions(request());
    })->name('admission.getadmissions');
    // Route::get('new-admission-show/{studentid}/{admissionid}', [App\Http\Controllers\BranchController::class, 'newAdmissionShow'])->name('new.admission.show');
    Route::get('new-admission-show/{studentid}/{admissionid}', function ($studentid, $admissionid) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['search_student'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view admission details');
        }

        $controller = app(App\Http\Controllers\BranchController::class);
        return $controller->newAdmissionShow($studentid, $admissionid);
    })->name('new.admission.show');
    // Route::get('admission-editNew/{id}', [App\Http\Controllers\BranchController::class, 'admissionEditNew'])->name('admissionEditNew');
    Route::get('admission-editNew/{id}', function ($id) {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if admission_edit permission is enabled
        if (($pages['search_student'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to edit admissions');
        }

        // Call controller method - passing the ID directly
        $controller = app(App\Http\Controllers\BranchController::class);
        return $controller->admissionEditNew($id);
    })->name('admissionEditNew');
    Route::post('update-admission-form', [App\Http\Controllers\BranchController::class, 'updateAdmissionForm'])->name('updateAdmissionForm');

    // Route::get('fetch-student-registration', [App\Http\Controllers\BranchController::class, 'examRegRequest'])->name('examRegRequest');
    Route::post('archive-application/{id}', [App\Http\Controllers\BranchController::class, 'archive'])->name('archive-application');
    Route::get('archived-admissions', [App\Http\Controllers\BranchController::class, 'getArchive'])->name('getArchive');
    Route::post('undo-archive/{id}', [App\Http\Controllers\BranchController::class, 'undoArchive'])->name('undo-archive');
    Route::get('fetch-student-registration', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if fetch-student-registration access is enabled
        if (($pages['new_admission'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to fetch student registrations');
        }

        return app(App\Http\Controllers\BranchController::class)->examRegRequest(request());
    })->name('examRegRequest');


    Route::get('approved-admissions', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if approved-admissions access is enabled
        if (($pages['new_admission'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to fetch student registrations');
        }

        return app(App\Http\Controllers\BranchController::class)->approvedAdmission(request());
    })->name('approvedAdmission');

    Route::get('mocks/add-result', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if mock-add-result access is enabled
        if (($pages['add_mock_result'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access mock add results');
        }

        return app(App\Http\Controllers\BranchController::class)->mockAddResult(request());
    })->name('mockAddResult');
    // Route::get('mocks/add-result', [App\Http\Controllers\BranchController::class, 'mockAddResult'])->name('mockAddResult');
    Route::get('get/family/rec', [App\Http\Controllers\BranchController::class, 'getFamilyRec'])->name('get.family.rec');
    Route::post('store/mock-results', [App\Http\Controllers\BranchController::class, 'storeMockResult'])->name('storeMockResult');
    // Route::get('mocks/view-result', [App\Http\Controllers\BranchController::class, 'mockViewResult'])->name('mock.view.result');
    Route::get('mocks/view-result', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if mock-view-result access is enabled
        if (($pages['view_mock_result'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view mock test results');
        }

        return app(App\Http\Controllers\BranchController::class)->mockViewResult(request());
    })->name('mock.view.result');
    Route::get('mock-results/export', [App\Http\Controllers\BranchController::class, 'exportToCSVMock'])->name('export.mock.results');
    Route::get('mock/delete/{id}', [App\Http\Controllers\BranchController::class, 'mockDelete'])->name('mockDelete');
    // Route::get('mock/edit/{id}', [App\Http\Controllers\BranchController::class, 'editMock'])->name('editMock');
    Route::get('mock/edit/{id}', function ($id) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if mock-edit access is enabled
        if (($pages['view_mock_result'] ?? '') !== 'on') {
            abort(403, 'You arse not authorized to edit mock tests');
        }

        return app(App\Http\Controllers\BranchController::class)->editMock($id);
    })->name('editMock');
    Route::post('update/mock-results', [App\Http\Controllers\BranchController::class, 'updatemock'])->name('updatemock');
    // Route::get('mock/test/report', [App\Http\Controllers\BranchController::class, 'mockTestReport'])->name('mock.test.report');
    Route::get('mock/test/report', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if mock-test-report access is enabled
        if (($pages['mock_test_report'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access mock test reports');
        }

        return app(App\Http\Controllers\BranchController::class)->mockTestReport(request());
    })->name('mock.test.report');
    Route::post('send-mock-email', [App\Http\Controllers\BranchController::class, 'sendMockEmail'])->name('sendMockEmail');
    Route::get('attendance', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if attendance access is enabled
        if (($pages['attendance'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access attendance records');
        }

        return app(App\Http\Controllers\BranchController::class)->attendance(request());
    })->name('attendance');
    // Route::get('attendance', [App\Http\Controllers\BranchController::class, 'attendance'])->name('attendance');
    // Route::get('add-subject', [App\Http\Controllers\BranchController::class, 'addSubjects'])->name('addSubjects');
    Route::get('add-subject', function () {
        // Get user permissions
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if add-subject permission is enabled
        if (($pages['add_subjects'] ?? '') !== 'on') {  // Note: Using 'add_subject' as permission key
            abort(403, 'You are not authorized to add subjects');
        }

        // Call controller method
        return app(App\Http\Controllers\BranchController::class)->addSubjects(request());
    })->name('addSubjects');
    Route::post('store-subject', [App\Http\Controllers\BranchController::class, 'storesubejct'])->name('store-subject');
    Route::put('update-subject', [App\Http\Controllers\BranchController::class, 'updatesubject'])->name('update-subject');
    Route::delete('delete-subject/{id}', [App\Http\Controllers\BranchController::class, 'destroysubject'])->name('delete-subject');

    // Route::get('teacher-roster', [App\Http\Controllers\BranchController::class, 'teacherRoster'])->name('teacherRoster');
    Route::get('teacher-roster', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if teacher_roster permission is enabled
        if (($pages['teacher_roster'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access teacher roster');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->teacherRoster(request());
    })->name('teacherRoster');
    Route::post('teacher/roster/store', [App\Http\Controllers\BranchController::class, 'teacherRosterStore'])->name('teacher.roster.store');
    Route::put('/teacher-roster-update', [App\Http\Controllers\BranchController::class, 'teacherRosterupdate'])->name('teacher.roster.update');
    Route::delete('teacher/roster/delete/{id}', [App\Http\Controllers\BranchController::class, 'teacherRosterdestroy'])->name('teacher.roster.delete');


    Route::get('/whatsapp', [WhatsAppController::class, 'sendWhatsApp'])->name('whatsapp.index');
    Route::post('/whatsapp/send', [WhatsAppController::class, 'sendMessage'])->name('whatsapp.send');
    Route::post('/whatsapp/webhook', [WhatsAppController::class, 'handleWebhook'])->name('whatsapp.webhook');

    Route::get('getTeacherName/{name}', [App\Http\Controllers\BranchController::class, 'getTeacherName'])->name('getTeacherName');
    Route::post('search/family', [App\Http\Controllers\BranchController::class, 'findStudents'])->name('search.family');
    Route::post('search/family/view', [App\Http\Controllers\BranchController::class, 'findStudentsView'])->name('search.family.view');

    Route::get('attendances/search', [App\Http\Controllers\BranchController::class, 'searchAttendance'])->name('attendance.search');
    Route::post('attendances', [App\Http\Controllers\BranchController::class, 'store'])->name('attendance.store');
    Route::get('search/subject', [App\Http\Controllers\BranchController::class, 'findSubjects'])->name('search.subject');
    Route::post('attendances', [App\Http\Controllers\BranchController::class, 'storeAttendance'])->name('attendance.store');


    Route::get('view-attendance', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if view-attendance access is enabled
        if (($pages['view_attendance'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view attendance records');
        }

        return app(App\Http\Controllers\BranchController::class)->viewAttendance(request());
    })->name('view.attendance');
    // Route::get('view-attendance', [App\Http\Controllers\BranchController::class, 'viewAttendance'])->name('view.attendance');
    Route::get('attendances/viewz', [App\Http\Controllers\BranchController::class, 'viewz'])->name('attendance.viewz');
    Route::get('attendances/viewz/view', [App\Http\Controllers\BranchController::class, 'viewzView'])->name('attendance.viewz.view');


    Route::get('attendances/view', [App\Http\Controllers\BranchController::class, 'attendancesview'])->name('attendance.view');
    Route::get('attendance/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteAtten'])->name('attendance.deleteAtten');
    Route::get('/exportAtten', [App\Http\Controllers\BranchController::class, 'exportAtten'])->name('exportAtten');
    Route::get('/exportAttenView', [App\Http\Controllers\BranchController::class, 'exportAttenView'])->name('exportAttenView');


    Route::get('/admin/attendance/edit/{id}', [App\Http\Controllers\BranchController::class, 'attendanceEdit'])->name('attendanceEdit');
    Route::get('subject-book-assigner', [App\Http\Controllers\BranchController::class, 'subjecBookAssigner'])->name('subjecBookAssigner');


    // Route::get('subject-book-assigner', [App\Http\Controllers\BranchController::class, 'subjectBookAssigner'])->name('subjecBookAssigner');
    Route::post('subjectBookAssignments/store', [App\Http\Controllers\BranchController::class, 'storeAssigner'])->name('subjectBookAssignments.store');
    Route::put('subjectBookAssignments/{id}', [App\Http\Controllers\BranchController::class, 'updateAssigner'])->name('subjectBookAssignments.update');
    Route::delete('subjectBookAssignments/{id}', [App\Http\Controllers\BranchController::class, 'destroyAssigner'])->name('subjectBookAssignments.destroy');





    Route::post('attendance/update', [App\Http\Controllers\BranchController::class, 'updateAttendance'])->name('attendance.update');

    Route::get('/books', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if books access is enabled
        if (($pages['manage_books'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access books');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->books(request());
    })->name('books');
    // Route::get('/books', [App\Http\Controllers\BranchController::class, 'books'])->name('books');
    Route::post('/books', [App\Http\Controllers\BranchController::class, 'bookStore'])->name('booksStore');
    Route::delete('/books-destroy/{id}', [App\Http\Controllers\BranchController::class, 'deleteBooks'])->name('deleteBooks');
    Route::put('/books-update', [App\Http\Controllers\BranchController::class, 'bookUpdate'])->name('booksUpdate');
    // Route::get('sales/create', [App\Http\Controllers\BranchController::class, 'createsales'])->name('sales.create');
    Route::get('sales/create', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if sales_create permission is enabled
        if (($pages['manage_sales'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to create sales records');
        }

        // Call controller method
        return app(App\Http\Controllers\BranchController::class)->createsales(request());
    })->name('sales.create');
    Route::post('sales/store', [App\Http\Controllers\BranchController::class, 'storeSales'])->name('storeSales');
    // Route::get('manage-purchases', [App\Http\Controllers\BranchController::class, 'purchaseSale'])->name('manage.purchases');
    Route::get('manage-purchases', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if manage_purchases permission is enabled
        if (($pages['manage_purchases'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to manage purchases');
        }

        // Call controller method
        return app(App\Http\Controllers\BranchController::class)->purchaseSale(request());
    })->name('manage.purchases');
    Route::post('purchases/store', [App\Http\Controllers\BranchController::class, 'storePurchase'])->name('storePurchase');
    Route::get('books-report', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if books_report permission is enabled
        if (($pages['books_report'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access books reports');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->booksRecord(request());
    })->name('books.record');
    // Route::get('books-report', [App\Http\Controllers\BranchController::class, 'booksRecord'])->name('books.record');
    Route::get('assign-book', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if book_assignment permission is enabled
        if (($pages['assign_books'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to assign books');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->assignBook(request());
    })->name('books.assign');

    Route::get('/get-student-details', [App\Http\Controllers\BranchController::class, 'getStudentDetails'])->name('get-student-details');
    // Route::get('assign-book', [App\Http\Controllers\BranchController::class, 'assignBook'])->name('books.assign');
    Route::post('/store-assign-book', [App\Http\Controllers\BranchController::class, 'storeAssignBook'])->name('store.assign.book');
    Route::get('/delete-assign-book/{id}', [App\Http\Controllers\BranchController::class, 'deleteAssignBook'])->name('delete.assign.book');
    Route::get('/edit-assign-book/{id}', [App\Http\Controllers\BranchController::class, 'editAssignBook'])->name('edit.assign.book');
    Route::post('update-assign-book/{id}', [App\Http\Controllers\BranchController::class, 'updateAssignBook']);
    Route::post('get-books-by-subject', [App\Http\Controllers\BranchController::class, 'getBooksBySubject'])->name('get.books.by.subject');

    Route::get('pay-student-fee', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if fee_payment permission is enabled
        if (($pages['pay_student_fee'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to process student fee payments');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->createPayment(request());
    })->name('payment.create');
    // Route::get('pay-student-fee', [App\Http\Controllers\BranchController::class, 'createPayment'])->name('payment.create');
    Route::post('payments/store', [App\Http\Controllers\BranchController::class, 'storePayment'])->name('payment.store');
    Route::get('payments/show', [App\Http\Controllers\BranchController::class, 'showPayment'])->name('payment.show');

    Route::post('/payment-books', [App\Http\Controllers\BranchController::class, 'getPaymentBooks']);
    Route::post('/update-book-paid', [App\Http\Controllers\BranchController::class, 'updateBookPaid']);
    Route::get('update/payment/comment', [App\Http\Controllers\BranchController::class, 'updatePaymentComment']);
    Route::get('pdfGenerate/{id}', [App\Http\Controllers\BranchController::class, 'pdfGenerate']);
    // Route::get('previous-payments', [App\Http\Controllers\BranchController::class, 'previousPaymentForm'])->name('payment.previous');
    Route::get('previous-payments', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if view_payment_history permission is enabled
        if (($pages['previous_payment'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view previous payments');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->previousPaymentForm(request());
    })->name('payment.previous');
    Route::get('previous/payments/show', [App\Http\Controllers\BranchController::class, 'previousPaymentShow'])->name('payment.previous.show');
    Route::post('updatePaymentComment', [App\Http\Controllers\BranchController::class, 'updatePaymentComments'])->name('updatePaymentComment');
    Route::post('updatePackage', [App\Http\Controllers\BranchController::class, 'updatePackage'])->name('updatePackage');
    Route::get('getIndividualReceipt/{id}', [App\Http\Controllers\BranchController::class, 'getIndividualReceipt'])->name('getIndividualReceipt');
    Route::delete('PrevDelete/{id}', [App\Http\Controllers\BranchController::class, 'PrevDelete']);

    // Route::get('payment-export', [App\Http\Controllers\BranchController::class, 'paymentExportForm'])->name('payment.export');
    Route::get('payment-export', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if payment_export permission is enabled
        if (($pages['payment_export'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to export payment records');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->paymentExportForm(request());
    })->name('payment.export');
    Route::get('payments/export/view', [App\Http\Controllers\BranchController::class, 'showExportPayments'])->name('payment.export.show');
    Route::post('payments/update-method', [App\Http\Controllers\BranchController::class, 'updatePaymentMethod'])->name('payment.update.method');
    // Route::get('defaulter-list', [App\Http\Controllers\BranchController::class, 'defaulterList'])->name('defaulter.list');
    Route::get('defaulter-list', function () {
        // Verify user permissions for the current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if 'defaulter_list' permission is enabled
        if (($pages['defaulter_list'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view the defaulter list');
        }

        // Call the controller method
        return app(App\Http\Controllers\BranchController::class)->defaulterList(request());
    })->name('defaulter.list');
    // Route::get('payment-logs', [App\Http\Controllers\BranchController::class, 'paymentLogForm']);
    Route::get('payment-logs', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if payment_logs permission is enabled
        if (($pages['payment_logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view payment logs');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->paymentLogForm(request());
    })->name('payment.logs');  // Added route name for consistency
    // Route::get('payments/logs/show', [App\Http\Controllers\BranchController::class, 'paymentLogShow'])->name('payment.log.show');
    Route::get('payments/logs/show', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if payment_logs permission is enabled
        if (($pages['payment_logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view payment logs');
        }

        // Call controller method with request
        return app(App\Http\Controllers\BranchController::class)->paymentLogShow(request());
    })->name('payment.log.show');

    // Route::get('activity-log', [App\Http\Controllers\BranchController::class, 'systemLogs'])->name('system.log');
    Route::get('activity-log', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if activity log access is enabled
        if (($pages['logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access activity logs');
        }

        return app(App\Http\Controllers\BranchController::class)->systemLogs(request());
    })->name('system.log');

    Route::get('activity-log/get-users', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if activity log access is enabled
        if (($pages['logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access activity logs');
        }

        return app(App\Http\Controllers\BranchController::class)->getFilteredUsers(request());
    })->name('system.log.get.users');

    Route::get('activity-log/get-logs', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if activity log access is enabled
        if (($pages['logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access activity logs');
        }

        return app(App\Http\Controllers\BranchController::class)->getLogsData(request());
    })->name('system.log.get.logs');

    // Route::get('logs/show', [App\Http\Controllers\BranchController::class, 'showSystemLogs'])->name('system.log.show');
    Route::get('logs/show', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if system_logs permission is enabled
        if (($pages['logs'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view system logs');
        }

        // Call controller method with request
        return app(App\Http\Controllers\BranchController::class)->showSystemLogs(request());
    })->name('system.log.show');


    Route::get('users/note', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if user notes access is enabled
        if (($pages['diaries'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access user notes');
        }

        return app(App\Http\Controllers\BranchController::class)->indexDiaries(request());
    })->name('user.note.index');
    // Route::get('users/note', [App\Http\Controllers\BranchController::class, 'indexDiaries'])->name('user.note.index');
    Route::post('users/note/store', [App\Http\Controllers\BranchController::class, 'storeDiaries'])->name('user.note.store');
    Route::delete('users/note/delete/{id}', [App\Http\Controllers\BranchController::class, 'destroydiaries'])->name('user.note.delete');

    Route::get('get-subjects-by-student', [App\Http\Controllers\BranchController::class, 'getSubjectsByStudent'])->name('getSubjectsByStudent');

    Route::get('baseline-report', [App\Http\Controllers\BranchController::class, 'getBaselineReport'])->name('getBaselineReport');
    Route::get('progress-tracking-report', [App\Http\Controllers\BranchController::class, 'getProgressTrackingReport'])->name('getProgressTrackingReport');
    Route::get('get-students-by-guardian', [App\Http\Controllers\BranchController::class, 'getStudentsByGuardian'])->name('getStudentsByGuardian');
    Route::get('/get-baseline-report', [App\Http\Controllers\BranchController::class, 'getBaselineReportResult'])->name('getBaselineReportResult');
    Route::post('/send-baseline-report-email', [App\Http\Controllers\BranchController::class, 'sendBaselineReportEmail'])->name('sendBaselineReportEmail');

    Route::get('progress-tracking-report-data', [App\Http\Controllers\BranchController::class, 'getProgressTrackingReportData'])->name('getProgressTrackingReportData');
    Route::post('send-progress-report-email', [App\Http\Controllers\BranchController::class, 'sendProgressReportEmail'])->name('sendProgressReportEmail');
    Route::get('export-progress-report-pdf', [App\Http\Controllers\BranchController::class, 'exportProgressReportPDF'])->name('exportProgressReportPDF');
    Route::get('get-students-by-family-id', [App\Http\Controllers\BranchController::class, 'getStudentsByFamilyId'])->name('getStudentsByFamilyId');

    Route::get('students-reports', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if student reports access is enabled
        if (($pages['student_reports'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access student reports');
        }

        return app(App\Http\Controllers\BranchController::class)->StudentReport(request());
    })->name('student.report');
    // Route::get('students-reports', [App\Http\Controllers\BranchController::class, 'StudentReport'])->name('student.report');
    Route::get('getReport', [App\Http\Controllers\BranchController::class, 'getReport']);
    Route::get('getMedicalReport', [App\Http\Controllers\BranchController::class, 'getMedicalReport']);
    Route::get('getfamilyReport/{id}', [App\Http\Controllers\BranchController::class, 'getfamilyReport']);
    // Route::get('teacher-report', [App\Http\Controllers\BranchController::class, 'teacherReport'])->name('teacher.report');
    Route::get('teacher-report', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if teacher report access is enabled
        if (($pages['teacher_reports'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access teacher reports');
        }

        return app(App\Http\Controllers\BranchController::class)->teacherReport(request());
    })->name('teacher.report');


    Route::get('active-inactive-students', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if active/inactive students access is enabled
        if (($pages['active_inactive_students'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access active/inactive student records');
        }

        return app(App\Http\Controllers\BranchController::class)->ActiveInactive(request());
    })->name('student.ActiveInactive');
    // Route::get('active-inactive-students', [App\Http\Controllers\BranchController::class, 'ActiveInactive'])->name('student.ActiveInactive');
    Route::get('get-active-inactive', [App\Http\Controllers\BranchController::class, 'getActiveInactive'])->name('getActiveInactive');

    Route::post('/update-student-status', [App\Http\Controllers\BranchController::class, 'updateStudentStatus'])->name('update-student-status');





    Route::get('all-students-progress-report', [App\Http\Controllers\BranchController::class, 'getAllStudentsProgressReport'])->name('getAllStudentsProgressReport');
    Route::get('get-all-subjects', [App\Http\Controllers\BranchController::class, 'getAllSubjects'])->name('getAllSubjects');
    Route::get('all-students-progress-report-data', [App\Http\Controllers\BranchController::class, 'getAllStudentsProgressReportData'])->name('getAllStudentsProgressReportData');
    Route::post('send-all-students-progress-report-email', [App\Http\Controllers\BranchController::class, 'sendAllStudentsProgressReportEmail'])->name('sendAllStudentsProgressReportEmail');


    Route::get('student-tests/test-submission-tracker', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['test_submission_tracker'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access the test submission tracker');
        }

        return app(App\Http\Controllers\BranchController::class)->openTestSubmissionTrackerPage(request());
    })->name('test.submission.tracker');


    Route::get('student-tests/add-record', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['add_tests_record'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to manually add test records');
        }

        return app(App\Http\Controllers\BranchController::class)->openManualCreatePage(request());
    })->name('student.test.manual.create');
    // Route::get('student-tests/add-record', [App\Http\Controllers\BranchController::class, 'openManualCreatePage'])->name('student.test.manual.create');
    Route::post('student/tests/manual/store', [App\Http\Controllers\BranchController::class, 'storeManualTest'])->name('student.test.manual.store');
    Route::post('get-students/{family_id}', [App\Http\Controllers\BranchController::class, 'getFamilyStudents'])->name('get-family-students');

    Route::get('student-tests/add-records-excel', function () {
        // Verify user permissions
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if excel-upload permission is enabled
        if (($pages['add_tests_record_excel'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to upload test records via Excel');
        }

        // Call controller method with request object
        return app(App\Http\Controllers\BranchController::class)->createTest(request());
    })->name('student.test.create');
    // Route::get('student-tests/add-records-excel', [App\Http\Controllers\BranchController::class, 'createTest'])->name('student.test.create');
    Route::post('student/tests', [App\Http\Controllers\BranchController::class, 'importtests'])->name('student-test.import');
    Route::get('student-tests/view-records', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if view-test-records permission is enabled
        if (($pages['view_tests_records'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view student test records');
        }

        // Call controller method (with request if needed)
        return app(App\Http\Controllers\BranchController::class)->indexTests(request());
    })->name('student.test.index');

    // Route::get('student-tests/view-records', [App\Http\Controllers\BranchController::class,  'indexTests'])->name('student.test.index');
    Route::get('student/test/edit/{id}', [App\Http\Controllers\BranchController::class, 'editTest'])->name('student.test.edit');
    Route::post('student/test/update', [App\Http\Controllers\BranchController::class, 'updateTest'])->name('student.test.update');
    Route::delete('student/test/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteTest'])->name('student.test.delete');
    Route::get('/export-tests', [App\Http\Controllers\BranchController::class, 'exportTests'])->name('exportTests');

    Route::get('student-tests/teacher-comments', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if teacher-comments permission is enabled
        if (($pages['teacher_comments'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access teacher comments');
        }

        // Call controller method (pass request if needed)
        return app(App\Http\Controllers\BranchController::class)->teacherComment(request());
    })->name('comment.index');
    // Route::get('student-tests/teacher-comments', [App\Http\Controllers\BranchController::class, 'teacherComment'])->name('comment.index');
    Route::post('teacher/comment', [App\Http\Controllers\BranchController::class, 'TeacherStore'])->name('comment.store');
    Route::get('teacher/getStudents', [App\Http\Controllers\BranchController::class, 'getStudents']);
    Route::put('teacher/commentstore', [App\Http\Controllers\BranchController::class, 'commentstore']);
    Route::delete('delete-comment/{id}', [App\Http\Controllers\BranchController::class, 'commentDelete']);


    // Route::get('add-learner', [App\Http\Controllers\BranchController::class, 'addLearner']);
    Route::get('add-learner', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))->where('user_id', auth()->id())->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if add-learner access is enabled
        if (($pages['add_learner'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access add learner page');
        }

        return app(App\Http\Controllers\BranchController::class)->addLearner(request());
    })->name('add-learner');
    Route::get('get/family/rec', [App\Http\Controllers\BranchController::class, 'getFamilyRecords'])->name('get.family.rec');

    Route::get('learner-requests', [App\Http\Controllers\BranchController::class, 'learnerRequest'])->name('learnerRequest');
    Route::post('store/learner', [App\Http\Controllers\BranchController::class, 'storeLearner'])->name('store.learner');
    Route::post('store/learner-request', [App\Http\Controllers\BranchController::class, 'storeLearnerReq'])->name('store.learner.req');
    Route::get('learner-details/{id}', [App\Http\Controllers\BranchController::class, 'getLearnerDetails'])->name('getLearnerDetails');
    Route::post('approve-learner-request/{id}', [App\Http\Controllers\BranchController::class, 'approveLearnerRequest'])->name('approveLearnerRequest');
    // Route::get('view-learner', [App\Http\Controllers\BranchController::class, 'viewLearner'])->name('view.learner');
    Route::get('view-learner', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if view-learner access is enabled
        if (($pages['view_learner'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access view learner page');
        }

        return app(App\Http\Controllers\BranchController::class)->viewLearner(request());
    })->name('view.learner');
    Route::get('get/family/data', [App\Http\Controllers\BranchController::class, 'getFamilyData'])->name('get.family.data');
    Route::get('/get/learner/sessions', [App\Http\Controllers\BranchController::class, 'getLearnerSessions']);
    // Route::get('learner-report', [App\Http\Controllers\BranchController::class, 'learnerReport'])->name('learner.report');
    Route::get('learner-report', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if learner-report access is enabled
        if (($pages['report'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access learner reports');
        }

        return app(App\Http\Controllers\BranchController::class)->learnerReport(request());
    })->name('learner.report');


    Route::get('alumni', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if alumni access is enabled
        if (($pages['alumni'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access learner reports');
        }

        return app(App\Http\Controllers\BranchController::class)->alumni(request());
    })->name('alumni');



    Route::post('/send-learner-email', [App\Http\Controllers\BranchController::class, 'sendLearnerEmail']);
    // Route::get('learner-view/{id}', [App\Http\Controllers\BranchController::class, 'showLearner'])->name('viewLearner');
    Route::get('learner-view/{id}', function ($id) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if learner-view access is enabled
        if (($pages['report'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view learner details');
        }

        return app(App\Http\Controllers\BranchController::class)->showLearner($id);
    })->name('viewLearner');
    // Route::get('learner-edit/{id}', [App\Http\Controllers\BranchController::class, 'learnerEdit'])->name('learnerEdit');
    Route::get('learner-edit/{id}', function ($id) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if learner-edit access is enabled
        if (($pages['report'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to edit learner details');
        }

        // Pass the request and id separately
        $controller = app(App\Http\Controllers\BranchController::class);
        return $controller->learnerEdit($id);
    })->name('learnerEdit');
    Route::POST('learner/Update', [App\Http\Controllers\BranchController::class, 'updateLearner'])->name('updateLearner');
    Route::get('learner-delete/{id}', [App\Http\Controllers\BranchController::class, 'learnerDelete'])->name('learnerDelete');
    Route::get('store-destination', [App\Http\Controllers\BranchController::class, 'storeDestination'])->name('storeDestination');
    // Route::get('learner-analysis', [App\Http\Controllers\BranchController::class, 'learnerAnalysis'])->name('learnerAnalysis');
    Route::get('learner-analysis', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if learner-analysis access is enabled
        if (($pages['iag_analysis'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access learner analysis');
        }

        return app(App\Http\Controllers\BranchController::class)->learnerAnalysis(request());
    })->name('learnerAnalysis');
    // Route::get('schedule', [App\Http\Controllers\BranchController::class, 'schedule'])->name('teacherAttendance');
    Route::get('schedule', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))->where('user_id', auth()->id())->first();
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if schedule access is enabled
        if (($pages['teacher_availability'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access teacher attendance');
        }

        return app(App\Http\Controllers\BranchController::class)->schedule(request());
    })->name('teacherAttendance');
    Route::get('teacher/getTeachers', [App\Http\Controllers\BranchController::class, 'getTeachers']);
    Route::post('teacher/markAttendance', [App\Http\Controllers\BranchController::class, 'markAttendance']);
    Route::post('teacher/attendancemartk', [App\Http\Controllers\BranchController::class, 'attendancemark']);

    Route::get('review-request/{id}', [App\Http\Controllers\BranchController::class, 'reviewRequest'])->name('reviewRequest');
    Route::post('approve-reuest', [App\Http\Controllers\BranchController::class, 'approveRequest'])->name('approveRequest');
    Route::delete('delete-request/{id}', [App\Http\Controllers\BranchController::class, 'deleteRequest'])->name('deleteRequest');

    Route::post('delete-doc', [App\Http\Controllers\BranchController::class, 'deleteDoc'])->name('deleteDoc');
    Route::get('central-timetable', function () {
        // Get user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permission pages
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if central-timetable access is enabled
        if (($pages['manage_central_timetable'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access central timetable');
        }

        // Call the controller method
        return app(App\Http\Controllers\BranchController::class)->centralTimeTable(request());
    })->name('centralTimeTable');
    // Route::get('central-timetable', [App\Http\Controllers\BranchController::class, 'centralTimeTable'])->name('centralTimeTable');
    Route::get('get-general-student-name/{id}', [App\Http\Controllers\BranchController::class, 'getGeneralName'])->name('getGeneralName');
    Route::get('/store-generalTimetable', [App\Http\Controllers\BranchController::class, 'storeGeneralTimetable']);
    Route::get('/get-general-timetable', [App\Http\Controllers\BranchController::class, 'getGeneralTimetable']);
    Route::get('/delete-general/{id}', [App\Http\Controllers\BranchController::class, 'deleteGeneral']);
    Route::get('/get-edit-info/{id}', [App\Http\Controllers\BranchController::class, 'getEditIndfo']);
    Route::post('/get-edit-info/session-limit-check', [App\Http\Controllers\BranchController::class, 'checkSessionLimit']);
    Route::get('/fetch-bk-ch', [App\Http\Controllers\BranchController::class, 'fetchBkCh']);
    Route::get('/generate-staff-timetable', [App\Http\Controllers\BranchController::class, 'generateStaffTimetable']);
    Route::post('/send-teacher-email', [App\Http\Controllers\BranchController::class, 'sendEmailTeacher']);
    Route::get('/generate-student-timetable', [App\Http\Controllers\BranchController::class, 'generateStudentTimetable']);
    Route::post('/send-student-email', [App\Http\Controllers\BranchController::class, 'sendEmailStudent']);
    Route::get('/re-arrange-student', [App\Http\Controllers\BranchController::class, 'rearrangeStudent'])->name('re-arrange-student');
    Route::post('/store-term-break', [App\Http\Controllers\BranchController::class, 'storeTermbreak']);
    Route::get('/get-term-break', [App\Http\Controllers\BranchController::class, 'getTermBreak'])->name('get.term.break');


    Route::get('/term-break', [App\Http\Controllers\BranchController::class, 'termBreak']);
    Route::get('/new-list-module', [App\Http\Controllers\BranchController::class, 'newListModule']);

    // Route::get('manage-permissions', [App\Http\Controllers\BranchController::class, 'managePermission'])->name('manage.permission');
    // Route::get('allow/permission/{id}', [App\Http\Controllers\BranchController::class, 'allowPermission'])->name('allow.permission');
    Route::get('manage-permissions', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if manage-permissions access is enabled
        // if (($pages['manage_permissions'] ?? '') !== 'on') {
        //     abort(403, 'You are not authorized to manage permissions');
        // }

        $controller = app(App\Http\Controllers\BranchController::class);
        return $controller->managePermission();
    })->name('manage.permission');

    Route::get('allow/permission/{id}', function ($id) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if allow/permission access is enabled
        if (($pages['manage_permissions'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to allow permissions');
        }

        $controller = app(App\Http\Controllers\BranchController::class);
        return $controller->allowPermission($id);
    })->name('allow.permission');
    Route::post('store/permission/access', [App\Http\Controllers\BranchController::class, 'storePermission'])->name('storePermission');


    Route::get('/Comment-request-comments/{studentRequestId}', [App\Http\Controllers\BranchController::class, 'Commentindex'])->name('Comment.request-comments.index');
    Route::post('/Comment-request-comments', [App\Http\Controllers\BranchController::class, 'Commentstorereq'])->name('Comment.request-comments.store');
    Route::put('/Comment-request-comments/{id}', [App\Http\Controllers\BranchController::class, 'Commentupdate'])->name('Comment.request-comments.update');
    Route::delete('/Comment-request-comments/{id}', [App\Http\Controllers\BranchController::class, 'Commentdestroy'])->name('Comment.request-comments.destroy');


    Route::get('/timetable-scheduler', [App\Http\Controllers\BranchController::class, 'timeTableSchedular'])->name('timeTableSchedular');

    Route::get('/timetable-scheduler-get', [App\Http\Controllers\BranchController::class, 'timeTableSchedularGet'])->name('timeTableSchedularGet');
    Route::get('/get-student-by-family-id', [App\Http\Controllers\BranchController::class, 'getStudentByFamilyId'])
        ->name('get.students.by.family');
    Route::get('/lesson-tracking-report', [App\Http\Controllers\BranchController::class, 'lessonTrackingReport'])->name('lesson.tracking.report');
    Route::post('/get-lesson-tracking-data', [App\Http\Controllers\BranchController::class, 'getLessonTrackingData'])->name('get.lesson.tracking.data');
    Route::get('/get-teachers', [App\Http\Controllers\BranchController::class, 'getScheduleTeachers'])
        ->name('get.teachers');
    Route::post('timetable-schedular-save', [App\Http\Controllers\BranchController::class, 'timeTableSchedularSave'])->name('timeTableSchedularSave');
    Route::post('timetable-schedular-save-single', [App\Http\Controllers\BranchController::class, 'timeTableSchedularSaveSingle'])->name('timeTableSchedularSaveSingle');
    Route::get('/termbreak-scheduler', [App\Http\Controllers\BranchController::class, 'termBreakSchedular'])->name('termBreakSchedular');
    Route::get('/termbreak-scheduler-get', [App\Http\Controllers\BranchController::class, 'termBreakSchedularGet'])->name('termBreakSchedularGet');
    Route::get('/termbreak-scheduler/students', [App\Http\Controllers\BranchController::class, 'termBreakSchedularGetStudentByFamilyId'])->name('termBreakSchedularGetStudents');
    Route::get('/termbreak-scheduler/teachers', [App\Http\Controllers\BranchController::class, 'termBreakSchedularGetTeachers'])->name('termBreakSchedularGetTeachers');
    Route::post('/termbreak-scheduler-save-single', [App\Http\Controllers\BranchController::class, 'termBreakSchedularSaveSingle'])->name('termBreakSchedularSaveSingle');
    Route::delete('/termbreak-scheduler-delete/{id}', [App\Http\Controllers\BranchController::class, 'termBreakSchedularDelete'])->name('termBreakSchedularDelete');

         Route::get('grades-module', [App\Http\Controllers\BranchController::class, 'grades_module'])->name('grades.module');
         Route::post('grades-module/store', [App\Http\Controllers\BranchController::class, 'storeGrades'])->name('grades.store');
         Route::get('grades-module/get-existing', [App\Http\Controllers\BranchController::class, 'getExistingGrades'])->name('grades.get.existing');
        Route::get('getstudentsGrades', [App\Http\Controllers\BranchController::class, 'getstudentsGrades'])->name('get.students.by.family.grades');





    Route::get('/deleteScheduleFromBackend', [App\Http\Controllers\BranchController::class, 'deleteScheduleFromBackend'])
        ->name('deleteScheduleFromBackend');





    Route::post('/switch-branch', [App\Http\Controllers\BranchController::class, 'switchBranch'])->name('switch-branch');
    Route::get('/clear-branch', [App\Http\Controllers\BranchController::class, 'clearBranch'])->name('clear-branch');

    Route::get('/crash-course', [App\Http\Controllers\BranchController::class, 'crashCourse'])->name('crash.course');
    Route::get('/crash-course/packages', [App\Http\Controllers\BranchController::class, 'crashCoursePackages'])->name('crash.course.packages');
    Route::get('/crash-course/registration', [App\Http\Controllers\BranchController::class, 'crashCourseRegistration'])->name('crash.course.registration');
    Route::get('/crash-course/attendance', [App\Http\Controllers\BranchController::class, 'crashCourseAttendance'])->name('crash.course.attendance');
    Route::get('/crash-course/records', [App\Http\Controllers\BranchController::class, 'crashCourseRecords'])->name('crash.course.records');
    Route::get('/crash-course/data', [App\Http\Controllers\BranchController::class, 'crashCourseData'])->name('crash.course.data');
    Route::get('/crash-course/export/{format}', [App\Http\Controllers\BranchController::class, 'exportCrashCourseRecords'])->name('crash.course.export');

    // Crash Course Package Routes
    Route::post('/crash-course/package/store', [App\Http\Controllers\BranchController::class, 'storeCrashCoursePackage'])->name('crash.course.package.store');
    Route::put('/crash-course/package/update', [App\Http\Controllers\BranchController::class, 'updateCrashCoursePackage'])->name('crash.course.package.update');
    Route::delete('/crash-course/package/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteCrashCoursePackage'])->name('crash.course.package.delete');

    // Crash Course Registration Routes
    Route::post('/crash-course/registration/store', [App\Http\Controllers\BranchController::class, 'storeCrashCourseRegistration'])->name('crash.course.registration.store');
    Route::put('/crash-course/registration/update', [App\Http\Controllers\BranchController::class, 'updateCrashCourseRegistration'])->name('crash.course.registration.update');
    Route::delete('/crash-course/registration/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteCrashCourseRegistration'])->name('crash.course.registration.delete');

    // Crash Course Payment Routes
    Route::post('/crash-course/payment/store', [App\Http\Controllers\BranchController::class, 'storeCrashCoursePayment'])->name('crash.course.payment.store');
    Route::put('/crash-course/payment/update', [App\Http\Controllers\BranchController::class, 'updateCrashCoursePayment'])->name('crash.course.payment.update');
    Route::delete('/crash-course/payment/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteCrashCoursePayment'])->name('crash.course.payment.delete');
    Route::get('/crash-course/payment/receipt/{id}', [App\Http\Controllers\BranchController::class, 'generateCrashCourseReceipt'])->name('crash.course.payment.receipt');
    Route::get('/crash-course/candidates', [App\Http\Controllers\BranchController::class, 'getCrashCourseCandidates'])->name('crash.course.candidates');

    // Crash Course Attendance Routes
    Route::post('/crash-course/attendance/store', [App\Http\Controllers\BranchController::class, 'storeCrashCourseAttendance'])->name('crash.course.attendance.store');
    Route::put('/crash-course/attendance/update', [App\Http\Controllers\BranchController::class, 'updateCrashCourseAttendance'])->name('crash.course.attendance.update');
    Route::delete('/crash-course/attendance/delete/{id}', [App\Http\Controllers\BranchController::class, 'deleteCrashCourseAttendance'])->name('crash.course.attendance.delete');

    // Flag Candidate Routes
    Route::get('flag-candidate', function () {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['flag_candidate'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access Flag Candidate');
        }

        return app(App\Http\Controllers\BranchController::class)->flagCandidate(request());
    })->name('flag.candidate');

    Route::post('flag-candidate/toggle', function (Illuminate\Http\Request $request) {
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pages = json_decode($permission->page_name ?? '{}', true);

        if (($pages['flag_candidate'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to flag candidates');
        }

        return app(App\Http\Controllers\BranchController::class)->toggleFlagCandidate($request);
    })->name('flag.candidate.toggle');

    // =============================================
    // Family Block / Unblock Routes
    // =============================================
    Route::get('family-block', [App\Http\Controllers\BranchController::class, 'familyBlockIndex'])
        ->name('family.block.index');

    Route::post('family-block/toggle', [App\Http\Controllers\BranchController::class, 'familyBlockToggle'])
        ->name('family.block.toggle');
});



Route::middleware(['superadmin'])->group(function () {

    Route::get('/home', function () {
        // Get the permission record for current branch and user
        $permission = AccessPermission::firstOrNew(['id' => 6]);

        // Default permissions array
        $defaultPages = [
            'dashboard' => 'on',
            'create_branch' => 'on',
            'list_branch' => 'on',
            'users' => 'on',
            'roles' => 'on',
            'grant_permission' => 'on',
        ];

        // Update properties
        $permission->page_name = json_encode($defaultPages);
        $permission->branch_name = null;
        $permission->branch_id = null;
        $permission->save();

        // Decode permissions
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if dashboard access is allowed
        if (($pages['dashboard'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to access the dashboard');
        }

        // Call controller method
        return app(App\Http\Controllers\SuperAdmin::class)->index();
    })->name('home');

    // Route::get('/home', [App\Http\Controllers\SuperAdmin::class, 'index'])->name('home');
    Route::get('/create-branch', function () {
        // Verify user permissions for current branch (or global permissions for super admin)
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if branch_creation permission is enabled
        if (($pages['create_branch'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to create new branches');
        }

        // Call controller method
        return app(App\Http\Controllers\SuperAdmin::class)->createBranch();
    })->name('createBranch');
    // Route::get('/create-branch', [App\Http\Controllers\SuperAdmin::class, 'createBranch'])->name('createBranch');
    Route::post('/store-branch', [App\Http\Controllers\SuperAdmin::class, 'storeBranch'])->name('store-branch');
    // Route::get('/list-branch', [App\Http\Controllers\SuperAdmin::class, 'listBranch'])->name('listBranch');
    // List Branches Route
    Route::get('/list-branch', function () {
        // Verify user permissions
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if branch_listing permission is enabled
        if (($pages['list_branch'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to view branch listings');
        }

        // Call controller method
        return app(App\Http\Controllers\SuperAdmin::class)->listBranch();
    })->name('listBranch');

    // Edit Branch Route
    Route::get('/edit-branch/{id}', function ($id) {
        // Verify user permissions
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if branch_editing permission is enabled
        if (($pages['list_branch'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to edit branches');
        }

        // Call controller method with ID parameter
        return app(App\Http\Controllers\SuperAdmin::class)->editBranch($id);
    })->name('editBranch');
    // Route::get('/edit-branch/{id}', [App\Http\Controllers\SuperAdmin::class, 'editBranch'])->name('editBranch');
    Route::post('/update-branch/{id}', [App\Http\Controllers\SuperAdmin::class, 'updateBranch'])->name('updateBranch');
    Route::delete('/delete-branch/{id}', [App\Http\Controllers\SuperAdmin::class, 'deleteBranch'])->name('deleteBranch');
    // Route::get('/add-user', [App\Http\Controllers\SuperAdmin::class, 'addUser'])->name('addUser');

    // Route::get('/manage-roles', [App\Http\Controllers\SuperAdmin::class, 'manageRoles'])->name('manageRoles');
    Route::get('/manage-roles', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if manage_roles permission is enabled
        if (($pages['roles'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to manage roles');
        }

        // Call controller method
        $controller = app(App\Http\Controllers\SuperAdmin::class);
        return $controller->manageRoles();
    })->name('manageRoles');
    Route::post('/roles/store', [App\Http\Controllers\SuperAdmin::class, 'storeRole'])->name('admin.role.store');
    Route::get('/roles/{id}/edit', [App\Http\Controllers\SuperAdmin::class, 'editRole'])->name('admin.role.edit');
    Route::put('/roles/{id}', [App\Http\Controllers\SuperAdmin::class, 'updateRole'])->name('admin.role.update');
    Route::delete('/roles/delete/{id}', [App\Http\Controllers\SuperAdmin::class, 'deleteRole'])->name('admin.role.delete');

    Route::get('/manage-superadmin-users', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if manage_superadmin_users permission is enabled
        if (($pages['users'] ?? '') !== 'on') {
            abort(403, 'You are not authorized to manage superadmin users');
        }

        // Call controller method
        $controller = app(App\Http\Controllers\SuperAdmin::class);
        return $controller->manageSuperadminUsers();
    })->name('manageSuperadminUsers');
    // Route::get('/manage-superadmin-users', [App\Http\Controllers\SuperAdmin::class, 'manageSuperadminUsers'])->name('manageSuperadminUsers');
    Route::post('/superadmin-users/store', [App\Http\Controllers\SuperAdmin::class, 'storeSuperadminUser'])->name('admin.superadmin-user.store');
    Route::get('/superadmin-users/{id}/edit', [App\Http\Controllers\SuperAdmin::class, 'editSuperadminUser'])->name('admin.superadmin-user.edit');
    Route::put('/superadmin-users/{id}', [App\Http\Controllers\SuperAdmin::class, 'storeSuperadminUser'])->name('admin.superadmin-user.update'); // Added PUT route
    Route::delete('/superadmin-users/delete/{id}', [App\Http\Controllers\SuperAdmin::class, 'deleteSuperadminUser'])->name('admin.superadmin-user.delete');
    Route::get('/superadmin-users/logout/{id}', [App\Http\Controllers\SuperAdmin::class, 'logoutSingleSuperadminUser'])->name('admin.superadmin-user.logout');
    // Route::get('grant-permission', [App\Http\Controllers\SuperAdmin::class, 'managePermission'])->name('superadmin.manage.permission');
    Route::get('/grant-permission', function () {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if grant_permission permission is enabled
        // if (($pages['grant_permission'] ?? '') !== 'on') {
        //     abort(403, 'You are not authorized to grant permissions');
        // }

        // Call controller method
        $controller = app(App\Http\Controllers\SuperAdmin::class);
        return $controller->managePermission();
    })->name('superadmin.manage.permission');

    Route::get('/super-admin/allow/permission/{id}', function ($id) {
        // Verify user permissions for current branch
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        // Decode permissions with empty array fallback
        $pages = json_decode($permission->page_name ?? '{}', true);

        // Check if allow_permission permission is enabled
        // if (($pages['grant_permission'] ?? '') !== 'on') {
        //     abort(403, 'You are not authorized to allow permissions');
        // }

        // Call controller method with parameter
        $controller = app(App\Http\Controllers\SuperAdmin::class);
        return $controller->allowPermission($id);
    })->name('superadmin.allow.permission');
    // Route::get('super-admin/allow/permission/{id}', [App\Http\Controllers\SuperAdmin::class, 'allowPermission'])->name('superadmin.allow.permission');
    Route::post('super-admin/store/permission/access', [App\Http\Controllers\SuperAdmin::class, 'storePermission'])->name('superadmin.storePermission');
});





Route::get('/check', function () {
    dd(session('branch_id'));
});

Route::get('/customLogout', function () {
    $user = Auth::user();
    if ($user) {
        $user->login_session = 0;
        $user->save();
    }
    Session::flush();
    Auth::logout();

    return redirect('/login')->with('message', 'You have been logged out.');
});

Route::get('/logout-all', function () {
    $currentUserId = Auth::id();
    User::where('id', '!=', $currentUserId)->update(['login_session' => 0]);
    return redirect('/');
});


Route::get('/logout-single/{id}', function ($id) {
    $user = User::find($id);
    // dd($user);
    if ($user) {
        $user->update(['login_session' => 0]);

        return redirect()->back()->with('success', 'User has been logged out successfully!');
    } else {
        return redirect()->back()->with('error', 'User not found.');
    }
})->name('logout.single');




// import code for db is below
Route::get('import', [App\Http\Controllers\ImportDatabase::class, 'showUploadForm'])->name('import.form');
Route::post('import/upload', [App\Http\Controllers\ImportDatabase::class, 'upload'])->name('import.upload');

Route::get('/file/view/{encoded}', [App\Http\Controllers\ImportDatabase::class, 'viewDcos'])->name('file.view');



Route::get('/tempImportStudents', function () {
    return view('tempImportStudents');
});





Route::get('/export-students-branchwise', function () {
    $export = new class implements FromCollection, WithHeadings, WithMapping {
        public function collection()
        {
            return DB::table('studentdata')
                ->select('studentid', 'branch_id', 'branch_name', 'target_grades', 'tier', 'subject_names', 'studenthours', 'studentsur', 'studentname', 'admissionid', 'studentyearinschool')
                ->where('student_status', 'active')
                ->where('branch_id', 'barking_centre_150')
                ->get();
        }

        public function headings(): array
        {
            return [
                'Student ID',
                'Branch ID',
                'Branch Name',
                'Target Grades',
                'Tier',
                'Subject Names',
                'Student Hours',
                'Year in School',
                'Student Surname',
                'Student Name',
                'Admission ID',
            ];
        }

        public function map($student): array
        {
            return [
                $student->studentid,
                $student->branch_id,
                $student->branch_name,
                is_array($student->target_grades) ? implode(', ', $student->target_grades) : $student->target_grades,
                is_array($student->tier) ? implode(', ', $student->tier) : $student->tier,
                is_array($student->subject_names) ? implode(', ', $student->subject_names) : $student->subject_names,
                is_array($student->studenthours) ? implode(', ', $student->studenthours) : $student->studenthours,
                is_array($student->studentyearinschool) ? implode(', ', $student->studentyearinschool) : $student->studentyearinschool,

                $student->studentsur,
                $student->studentname,
                $student->admissionid,
            ];
        }
    };

    return Excel::download($export, 'students_barking_centre_150.xlsx');
})->name('exportstudents');




Route::post('/import-students-branchwise', function (Request $request) {
    if (!$request->hasFile('file')) {
        return redirect()->back()->with('error', 'Please upload an Excel file.');
    }

    $import = new class implements ToCollection, WithHeadingRow {
        public function collection($rows)
        {
            // dd($rows);
            foreach ($rows as $row) {
                $studentId = $row['student_id'];
                if (!$studentId) {
                    continue; // Skip rows without a student ID
                }

                $data = [
                    'branch_id' => $row['branch_id'],
                    'branch_name' => $row['branch_name'],
                    'target_grades' => $row['target_grades'],
                    'tier' => $row['tier'],
                    'subject_names' => $row['subject_names'],
                    'studenthours' => $row['student_hours'],
                    'studentyearinschool' => $row['year_in_school'],
                    'studentsur' => $row['student_surname'],
                    'studentname' => $row['student_name'],
                    'admissionid' => $row['admission_id'],

                ];

                // Update the record in the studentdata table based on studentid
                DB::table('studentdata')
                    ->where('studentid', $studentId)
                    ->update($data);
            }
        }
    };

    Excel::import($import, $request->file('file'));

    return redirect()->back()->with('success', 'Student data updated successfully.');
})->name('importstudents');


Route::get('/checkk', function () {
    $permission = AccessPermission::take(5)->get();

    $today = Carbon::today()->toDateString(); // sirf date (YYYY-MM-DD)
    $now   = Carbon::now()->toDateTimeString(); // full date & time (YYYY-MM-DD HH:MM:SS)

    $records = DB::table('general_timetables')
        ->where('date', '2025-08-24')
        ->get();

    dd([
        'current_date' => $today,
        'current_datetime' => $now,
        'records' => $records,
    ]);
});





Route::get('/debug-times', function () {

    // 1) App / PHP timezone + time
    $appTz = config('app.timezone') ?: date_default_timezone_get();
    $appNow = Carbon::now($appTz)->toDateTimeString();

    $phpTz = date_default_timezone_get();
    $phpNow = Carbon::now($phpTz)->toDateTimeString();

    // Helper: detect country code from timezone name
    $tzToCountry = function (?string $tz) {
        if (!$tz) return null;
        try {
            $dtz = new DateTimeZone($tz);
            $loc = $dtz->getLocation();
            return $loc['country_code'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    };

    $appCountry = $tzToCountry($appTz);
    $phpCountry = $tzToCountry($phpTz);

    // 2) Query DB (corrected: no collect(), direct object)
    $row = DB::selectOne("
        SELECT
            NOW() AS db_now,
            UTC_TIMESTAMP() AS db_utc,
            @@global.time_zone AS db_global_tz,
            @@session.time_zone AS db_session_tz,
            @@system_time_zone AS db_system_tz
    ");

    $dbNow       = $row->db_now ?? null;
    $dbUtc       = $row->db_utc ?? null;
    $dbGlobalTz  = $row->db_global_tz ?? null;
    $dbSessionTz = $row->db_session_tz ?? null;
    $dbSystemTz  = $row->db_system_tz ?? null;

    // 3) Compute DB offset
    $dbOffsetSeconds = null;
    if ($dbNow && $dbUtc) {
        $dbOffsetSeconds = strtotime($dbNow) - strtotime($dbUtc);
    }

    // 4) infer timezone name from offset
    $inferredDbTzName = null;
    if (!is_null($dbOffsetSeconds)) {
        $inferredDbTzName = timezone_name_from_abbr('', $dbOffsetSeconds, false);
        if ($inferredDbTzName === false) {
            $inferredDbTzName = timezone_name_from_abbr('', $dbOffsetSeconds, true) ?: null;
        }
    }

    $inferredDbCountry = $tzToCountry($inferredDbTzName);

    // 6) best guess of DB timezone
    $dbBestGuessTz =
        ($dbSessionTz && $dbSessionTz !== 'SYSTEM') ? $dbSessionTz :
        (($dbGlobalTz && $dbGlobalTz !== 'SYSTEM') ? $dbGlobalTz :
        ($inferredDbTzName ?: $dbSystemTz));

    $dbBestGuessCountry = $tzToCountry($dbBestGuessTz);

    // Final Response
    return response()->json([
        'app' => [
            'config_app_timezone' => $appTz,
            'app_now' => $appNow,
            'app_country_code' => $appCountry,
        ],
        'php' => [
            'php_default_timezone' => $phpTz,
            'php_now' => $phpNow,
            'php_country_code' => $phpCountry,
        ],
        'database' => [
            'db_now_raw' => $dbNow,
            'db_utc_raw' => $dbUtc,
            'db_offset_seconds' => $dbOffsetSeconds,
            'db_global_time_zone' => $dbGlobalTz,
            'db_session_time_zone' => $dbSessionTz,
            'db_system_time_zone' => $dbSystemTz,
            'inferred_db_tz_from_offset' => $inferredDbTzName,
            'inferred_db_country_code' => $inferredDbCountry,
            'db_best_guess_timezone' => $dbBestGuessTz,
            'db_best_guess_country_code' => $dbBestGuessCountry,
        ],
        'notes' => [
            "If MySQL returns 'SYSTEM', offset-based guess is used.",
            "Offset → timezone mapping is approximate (multiple zones share offsets).",
            "Best practice: always set MySQL timezone to a named IANA zone."
        ]
    ]);
});
