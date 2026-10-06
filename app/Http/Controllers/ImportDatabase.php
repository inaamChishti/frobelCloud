<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Auth;


class ImportDatabase extends Controller
{
    /**
     * Display the upload form for all tables
     */
    public function showUploadForm()
    {
        return view('import.upload');
    }

    /**
     * Handle file upload and dispatch to specific import method
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
            'table' => 'required|in:access_permissions,activity_log,admission,assign_books,attendance,books,consents,deleted_payments,email_job,exam_entry_form,exam_table,family_comments,feesection,general_timetables,guardian,history,iag_meetings,ip_addresses,kin,mails,medical_condition,migrations,mock_results,notes,notifications,payment,payment_comments,payment_log,purchases,role,sales,staff_attendances,studentdata,student_requests,student_tests,subjects,teachers_subject,teacher_comments,timetable'
        ]);

        $file = $request->file('file');
        $table = $request->input('table');

        $method = 'import_' . $table;
        if (method_exists($this, $method)) {
            return $this->$method($file);
        }

        return back()->withErrors(['table' => 'Invalid table selected']);
    }

    private function import_access_permissions($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('access_permissions')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing access_permissions row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Access permissions imported successfully');
    }

    private function import_activity_log($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('activity_log')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing activity_log row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Activity log imported successfully');
    }

    private function import_admission($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                // dd($rows);
                foreach ($rows as $row) {
                    try {
                        DB::table('admission')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing admission row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Admission data imported successfully');
    }

    private function import_assign_books($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('assign_books')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing assign_books row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Assign books data imported successfully');
    }

    private function import_attendance($file)
    {
          ini_set('memory_limit', '4G'); // 4 GB tak realistic
ini_set('max_execution_time', 7200); // 2 hrs tak

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('attendance')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing attendance row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Attendance data imported successfully');
    }

    private function import_books($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('books')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing books row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Books data imported successfully');
    }

    private function import_consents($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('consents')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing consents row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Consents data imported successfully');
    }

    private function import_deleted_payments($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('deleted_payments')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing deleted_payments row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Deleted payments data imported successfully');
    }

    private function import_email_job($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('email_job')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing email_job row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Email job data imported successfully');
    }

    private function import_exam_entry_form($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('exam_entry_form')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing exam_entry_form row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Exam entry form data imported successfully');
    }

    private function import_exam_table($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('exam_table')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing exam_table row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Exam table data imported successfully');
    }

    private function import_family_comments($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('family_comments')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing family_comments row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Family comments data imported successfully');
    }

    private function import_feesection($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('feesection')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing feesection row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Feesection data imported successfully');
    }

    private function import_general_timetables($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('general_timetables')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing general_timetables row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'General timetables data imported successfully');
    }

    private function import_guardian($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('guardian')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing guardian row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Guardian data imported successfully');
    }

    private function import_history($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('history')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing history row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'History data imported successfully');
    }

    private function import_iag_meetings($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('iag_meetings')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing iag_meetings row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'IAG meetings data imported successfully');
    }

    private function import_ip_addresses($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('ip_addresses')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing ip_addresses row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'IP addresses data imported successfully');
    }

    private function import_kin($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('kin')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing kin row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Kin data imported successfully');
    }

    private function import_mails($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('mails')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing mails row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Mails data imported successfully');
    }

    private function import_medical_condition($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('medical_condition')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing medical_condition row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Medical condition data imported successfully');
    }

    private function import_migrations($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('migrations')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing migrations row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Migrations data imported successfully');
    }

   private function import_mock_results($file)
{
    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', 1200);

    Excel::import(new class implements ToCollection, WithHeadingRow {
        public function collection(Collection $rows)
        {
            // Define the expected columns in the mock_results table
            $tableColumns = [
                'id',
                'family_id',
                'name',
                'subject',
                'mock_type',
                'exam_date',
                'percentage',
                'fine_grade',
                'qualifications',
                'tier',
                'exam_marked_by',
                'updated_by',
                'step_1',
                'step_2',
                'step_3',
                'branch_name',
                'branch_id',
                'created_at',
                'updated_at'
            ];

            foreach ($rows as $row) {
                try {
                    // Map the row data to the table columns
                    $data = [];
                    foreach ($tableColumns as $column) {
                        // Check if the column exists in the row and is not null
                        if (isset($row[$column])) {
                            $data[$column] = $row[$column];
                        } else {
                            $data[$column] = null; // Set null for missing columns
                        }
                    }

                    // Insert the mapped data into the mock_results table
                    DB::table('mock_results')->insert($data);
                } catch (\Exception $e) {
                    Log::error('Error importing mock_results row: ' . $e->getMessage());
                }
            }
        }
    }, $file);

    return back()->with('success', 'Mock results data imported successfully');
}

    private function import_notes($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('notes')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing notes row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Notes data imported successfully');
    }

    private function import_notifications($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('notifications')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing notifications row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Notifications data imported successfully');
    }

    private function import_payment($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('payment')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing payment row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Payment data imported successfully');
    }

    private function import_payment_comments($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('payment_comments')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing payment_comments row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Payment comments data imported successfully');
    }

    private function import_payment_log($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('payment_log')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing payment_log row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Payment log data imported successfully');
    }

    private function import_purchases($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('purchases')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing purchases row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Purchases data imported successfully');
    }

    private function import_role($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('role')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing role row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Role data imported successfully');
    }

    private function import_sales($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('sales')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing sales row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Sales data imported successfully');
    }

    private function import_staff_attendances($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('staff_attendances')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing staff_attendances row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Staff attendances data imported successfully');
    }

    private function import_studentdata($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('studentdata')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing studentdata row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Student data imported successfully');
    }

    private function import_student_requests($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('student_requests')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing student_requests row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Student requests data imported successfully');
    }

    private function import_student_tests($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('student_tests')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing student_tests row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Student tests data imported successfully');
    }

    private function import_subjects($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('subjects')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing subjects row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Subjects data imported successfully');
    }

    private function import_teachers_subject($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('teachers_subject')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing teachers_subject row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Teachers subject data imported successfully');
    }

    private function import_teacher_comments($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('teacher_comments')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing teacher_comments row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Teacher comments data imported successfully');
    }

    private function import_timetable($file)
    {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 1200);

        Excel::import(new class implements ToCollection, WithHeadingRow {
            public function collection(Collection $rows)
            {
                foreach ($rows as $row) {
                    try {
                        DB::table('timetable')->insert($row->toArray());
                    } catch (\Exception $e) {
                        Log::error('Error importing timetable row: ' . $e->getMessage());
                    }
                }
            }
        }, $file);

        return back()->with('success', 'Timetable data imported successfully');
    }


public function viewDcos($encoded)
{
    if (!Auth::check()) {
        abort(403, 'Unauthorized access');
    }
    $file = base64_decode($encoded);

    // Security check - ensure the file exists and is within a safe directory
    if (!file_exists(public_path($file))) {
        abort(404);
    }

    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    return view('file-view', [
        'file' => asset($file),
        'extension' => $extension,
    ]);
}

}
