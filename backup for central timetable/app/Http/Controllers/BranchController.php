<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\{TeacherAvailability, SubjectBookAssignment, IAGLearnerRequest, TermBreak, GeneralTimetable, AccessPermission, StaffAttendance, IagMeeting, Comment, StudentTest, Note, Payment, Role, Admission, Guardian, Kin, Student, medical_condition, TimeTable, Consent, StudentRequest, MockResult, Subject, Attendance, Book, Sale, Purchase, AssignBook, Activity, StudentGrade};
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log; // Moved to the top
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\Text\HtmlPart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Swift_Attachment;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Mime\Part\TextPart;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentRequestNotification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Client;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;
use Auth;
use FPDF;
use App\Helpers\AdmissionChangeTracker;





class BranchController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function addBranchUsers(Request $request)
    {
        try {
            $users = User::where('role', '!=', 'super_admin')->where('branch_id', session('branch_id'))
                ->select('id', 'name', 'email', 'usertype', 'login_session', 'visible_password')
                ->get();
            // dd($users);
            // Log query results for debugging
            Log::info('Users query results:', $users->toArray());

            $roles = Role::all();

            return view('branchFrontend.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in addBranchUsers: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'username' => 'required|unique:users,name,' . $request->user_id,
    //         'email' => 'required',
    //         'password' => $request->user_id ? 'nullable|min:8' : 'required|min:8',
    //         'role' => 'required',
    //     ]);

    //     $user = $request->user_id ? User::findOrFail($request->user_id) : new User;
    //     $user->name = $request->username;
    //     $user->email = $request->email;

    //     if ($request->filled('password')) {
    //         $user->password = Hash::make($request->password);
    //         $user->visible_password = $request->password;
    //     }

    //     $user->usertype = $request->role;
    //     $user->branch_id = session('branch_id');
    //     $user->role = 'user of ' . session('branch_id');


    //     $user->save();

    //     return response()->json(['success' => 'User saved successfully']);
    // }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                $request->user_id
                    ? 'unique:users,name,' . $request->user_id
                    : 'unique:users,name', // Ensure username is unique across all users
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                $request->user_id
                    ? 'unique:users,email,' . $request->user_id
                    : 'unique:users,email', // Ensure email is unique across all users
            ],
            'password' => $request->user_id ? 'nullable|min:8' : 'required|min:8',
            'role' => 'required',
        ], [
            'username.unique' => 'The username is already taken by another user in the system.',
            'email.unique' => 'The email is already taken by another user in the system.',
        ]);

        try {
            $user = $request->user_id ? User::findOrFail($request->user_id) : new User;
            $user->name = $request->username;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
                $user->visible_password = $request->password;
            }

            $user->usertype = $request->role;
            $user->branch_id = session('branch_id');
            $user->role = 'user of ' . session('branch_id');

            $user->save();

            return response()->json(['success' => 'User saved successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'errorMessage' => 'An error occurred while saving the user. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        // dd($user,$roles);
        return response()->json(['user' => $user, 'roles' => $roles]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => 'User deleted successfully']);
    }

    public function logoutSingle($id)
    {
        $user = User::findOrFail($id);
        $user->login_session = null;
        $user->save();
        return redirect()->back()->with('success', 'User logged out successfully');
    }

    public function logoutAll()
    {
        User::where('role', '!=', 'super_admin')->update(['login_session' => null]);
        return redirect()->back()->with('success', 'All users logged out successfully');
    }


    public function AddBranchRoles()
    {
        $roles = Role::where('branch_id', session('branch_id'))->get();

        return view('branchFrontend.users.roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|unique:role,name,' . $request->role_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;


        $role = $request->role_id ? Role::find($request->role_id) : new Role;
        $role->name = $request->role_name;
        $role->branch_name = $branch_name;
        $role->branch_id = $branch_id;

        $role->save();

        return response()->json([
            'message' => 'Role saved successfully!'
        ], 200);
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return response()->json([
            'role' => $role
        ], 200);
    }

    public function deleteRole($id)
    {

        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json([
            'message' => 'Role deleted successfully!'
        ], 200);
    }

    public function createBranchAdmission()
    {
        $lastAdmission = Admission::orderBy('admissionid', 'desc')->where('branch_id', session('branch_id'))->first();

        if (!$lastAdmission) {
            $family_id = 1001;
        } else {
            $lastFamilyNo = $lastAdmission->familyno;
            $family_id = $lastFamilyNo ? $lastFamilyNo + 1 : 1001;
        }

        $subjects = DB::table('subjects')
            ->select('name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->pluck('name');

        $student = Admission::where('familyno', $family_id)->where('branch_id', session('branch_id'))->first();
        // dd($family_id);
        if ($student) {
            return view('branchFrontend.admission.createAdmission')->withErrors([
                'message' => "Family Id already exists. You can't add any students",
            ]);
        } else {
            return view('branchFrontend.admission.createAdmission', compact('family_id', 'subjects'));
        }
    }



    // public function storeBranchAdmission(Request $request)
    // {
    //     dd($request->all());
    //     // Validate request data to ensure required fields are present
    //     $request->validate([
    //         'form_date' => 'required|date',
    //         'joining_date' => 'required|date',
    //         'dob' => 'required|array|min:1',
    //         'first_name' => 'required|array|min:1',
    //         'surname' => 'required|array|min:1',
    //         'gender' => 'required|array|min:1',
    //         'years_in_school' => 'nullable|array',
    //         'student_status' => 'nullable|array',
    //         'doctor_name' => 'nullable|array',
    //         'doctor_number' => 'nullable|array',
    //         'maddress' => 'nullable|array',
    //         'guardian_name' => 'required|string',
    //         'guardian_address' => 'required|string',
    //         'guardian_email' => 'required|email',
    //         'guardian_mobile' => 'required|string',
    //         'parent_relationship' => 'required|string',
    //         'kin_name' => 'required|string',
    //         'kin_address' => 'required|string',
    //         'kin_email' => 'required|email',
    //         'kin_mobile' => 'required|string',
    //         'family_id' => 'required',
    //         'family_status' => 'required',
    //         'payment_method' => 'required',
    //         'medical_condition' => 'nullable',
    //         'fee_detail' => 'nullable',
    //         'add_comment' => 'nullable',
    //     ]);

    //     // Format dates
    //     $request['form_date'] = (new \DateTime($request->form_date))->format('d/m/Y');
    //     $request['joining_date'] = (new \DateTime($request->joining_date))->format('d/m/Y');

    //     $formattedDob = [];
    //     foreach ($request->dob as $index => $date) {
    //         $formattedDob[$index] = (new \DateTime($date))->format('d/m/Y');
    //     }
    //     $request->merge(['dob' => $formattedDob]);

    //     try {
    //         DB::transaction(function () use ($request) {
    //             $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //             $branch_id = $branch->branch_id;
    //             $branch_name = $branch->branch_name;
    //             // $hours = json_decode($request->hours[0], true) ?? [];
    //             // dd($hours,$request->hours);
    //             // Guardian creation
    //             $guardianData = Guardian::create([
    //                 'guardianname' => $request->guardian_name,
    //                 'guardianaddress' => $request->guardian_address,
    //                 'guardiantel' => $request->guardian_email,
    //                 'guardianmob' => $request->guardian_mobile,
    //                 'parent_relationship' => $request->parent_relationship,
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //             ]);

    //             // Kin creation
    //             $kinDetails = Kin::create([
    //                 'kinname' => $request->kin_name,
    //                 'kinaddress' => $request->kin_address,
    //                 'kintel' => $request->kin_email,
    //                 'kinmob' => $request->kin_mobile,
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //             ]);

    //             // Admission creation
    //             $admission = new Admission();
    //             $admission->familyno = $request->family_id;
    //             $admission->formfilingdate = date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date)));
    //             $admission->joiningdate = date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date)));
    //             $admission->medicalcondition = $request->medical_condition;
    //             $admission->feedetail = $request->fee_detail;
    //             $admission->familystatus = $request->family_status;
    //             $admission->payment_method = $request->payment_method;
    //             $admission->add_comment = $request->add_comment;

    //             // Child fields
    //             $admission->child_name1 = $request->child_name1 ?? null;
    //             $admission->school_name1 = $request->school_name1 ?? null;
    //             $admission->child_name2 = $request->child_name2 ?? null;
    //             $admission->school_name2 = $request->school_name2 ?? null;
    //             $admission->child_name3 = $request->child_name3 ?? null;
    //             $admission->school_name3 = $request->school_name3 ?? null;
    //             $admission->child_name4 = $request->child_name4 ?? null;
    //             $admission->school_name4 = $request->school_name4 ?? null;
    //             $admission->child_name5 = $request->child_name5 ?? null;
    //             $admission->school_name5 = $request->school_name5 ?? null;

    //             $admission->branch_id = $branch_id ?? null;
    //             $admission->branch_name = $branch_name ?? null;

    //             $admission->save();

    //             $admission_id = $admission->admissionid;
    //             $kin_id = $kinDetails->kinid;
    //             $guardian_id = $guardianData->Guardianid;

    //             // Student data arrays
    //             $firstNames = $request->first_name ?? [];
    //             $surnames = $request->surname ?? [];
    //             $dobs = $request->dob ?? [];
    //             $genders = $request->gender ?? [];
    //             $yearsInSchool = $request->years_in_school ?? [];
    //             $studentStatuses = $request->student_status ?? [];

    //             $count = count($firstNames);


    //             $studentIds = [];
    //             $studentName = [];
    //             $doctorNames = [];
    //             $doctorNumbers = [];
    //             $medicalAddresses = [];

    //             if (!empty($request->medical_conditions)) {
    //                 foreach ($request->medical_conditions as $condition) {
    //                     $decoded = json_decode($condition, true);
    //                     if ($decoded) {
    //                         $doctorNames[] = $decoded['doctor_name'] ?? null;
    //                         $doctorNumbers[] = $decoded['doctor_number'] ?? null;
    //                         $medicalAddresses[] = $decoded['maddress'] ?? null;
    //                     }
    //                 }
    //             }
    //             // dd($doctorNames, $doctorNumbers, $medicalAddresses, $request->medical_conditions);

    //             // Create students
    //             for ($i = 0; $i < $count; $i++) {
    //                 $student = Student::create([
    //                     'student_status' => $studentStatuses[$i] ?? null,
    //                     'studentname' => $firstNames[$i] ?? null,
    //                     'studentsur' => $surnames[$i] ?? null,
    //                     'studentdob' => !empty($dobs[$i]) ? date('Y-m-d', strtotime(str_replace('/', '-', $dobs[$i]))) : null,
    //                     'studentgender' => $genders[$i] ?? null,
    //                     'studentyearinschool' => $yearsInSchool[$i] ?? null,
    //                     'admissionid' => $request->family_id,
    //                     'kinid' => $kin_id,
    //                     'studenthours' => $request->hours[$i] ?? null,
    //                     'guardianid' => $guardian_id,
    //                     'medical_condition' => (!empty($doctorNames[$i]) && !empty($doctorNumbers[$i])) ? 1 : null,
    //                     'branch_id' => $branch_id,
    //                     'branch_name' => $branch_name,
    //                 ]);
    //                 $studentIds[] = $student->studentid;
    //                 $studentName[] = ($firstNames[$i] ?? '') . ' ' . ($surnames[$i] ?? '');
    //             }

    //             // Create medical conditions
    //             $medicalCount = count($doctorNames);

    //             for ($i = 0; $i < $medicalCount; $i++) {
    //                 medical_condition::create([
    //                     'guardianid' => $guardian_id,
    //                     'family_id' => $request->family_id,
    //                     'student_id' => $studentIds[$i] ?? null,
    //                     'drName' => $doctorNames[$i] ?? null,
    //                     'drNumber' => $doctorNumbers[$i] ?? null,
    //                     'medicalDetails' => $medicalAddresses[$i] ?? null,
    //                     'branch_id' => $branch_id,
    //                     'branch_name' => $branch_name,
    //                 ]);
    //             }

    //             foreach ($studentName as $index => $studentNamez) {
    //                 Log::debug("Creating Timetable for Student:", ['student_name' => $studentNamez]);
    //                 for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
    //                     $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));
    //                     TimeTable::create([
    //                         'studentname' => $studentNamez,
    //                         'admissionid' => $request->family_id,
    //                         'day' => $day,
    //                         'branch_id' => $branch_id,
    //                         'branch_name' => $branch_name,
    //                     ]);
    //                     Log::debug("Timetable Entry Created:", [
    //                         'student_name' => $studentNamez,
    //                         'day' => $day,
    //                         'branch_id' => $branch_id,
    //                         'branch_name' => $branch_name,
    //                     ]);
    //                 }
    //             }
    //         });
    //     } catch (\Exception $e) {
    //         throw $e;
    //     }

    //     session()->flash('alert-success', 'Students registered successfully.');
    //     return redirect()->route('admin.admission.index');
    // }
    public function storeBranchAdmission(Request $request)
    {
        // dd($request->all());


        if ($request->filled('form_date')) {
            $request->merge([
                'form_date' => Carbon::createFromFormat('d/m/Y', $request->form_date)->format('Y-m-d')
            ]);
        }

        if ($request->filled('joining_date')) {
            $request->merge([
                'joining_date' => Carbon::createFromFormat('d/m/Y', $request->joining_date)->format('Y-m-d')
            ]);
        }

        if ($request->has('dob') && is_array($request->dob)) {

            $dob = collect($request->dob)->map(function ($date) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            })->toArray();

            $request->merge([
                'dob' => $dob
            ]);
        }
        // Validate request data
        $request->validate([
            'form_date' => 'required|date',
            'joining_date' => 'required|date',
            'dob' => 'required|array|min:1',
            'first_name' => 'required|array|min:1',
            'surname' => 'required|array|min:1',
            'gender' => 'required|array|min:1',
            'years_in_school' => 'nullable|array',
            'student_status' => 'nullable|array',
            'doctor_name' => 'nullable|array',
            'doctor_number' => 'nullable|array',
            'maddress' => 'nullable|array',
            'guardian_name' => 'required|string',
            'guardian_address' => 'required|string',
            'guardian_email' => 'required|email',
            // 'guardian_mobile' => 'required|string',
            // 'parent_relationship' => 'required|string',
            // 'kin_name' => 'required|string',
            // 'kin_address' => 'required|string',
            // 'kin_email' => 'required|email',
            // 'kin_mobile' => 'required|string',
            'family_id' => 'required',
            'family_status' => 'required',
            'payment_method' => 'required',
            'medical_condition' => 'nullable',
            'fee_detail' => 'nullable',
            'add_comment' => 'nullable',
            'subject_names' => 'nullable|array',
            'sessions' => 'nullable|array',
            'target_grades' => 'nullable|array',
        ]);

        // Format dates
        $request['form_date'] = (new \DateTime($request->form_date))->format('d/m/Y');
        $request['joining_date'] = (new \DateTime($request->joining_date))->format('d/m/Y');

        $formattedDob = [];
        foreach ($request->dob as $index => $date) {
            $formattedDob[$index] = (new \DateTime($date))->format('d/m/Y');
        }
        $request->merge(['dob' => $formattedDob]);

        try {
            DB::transaction(function () use ($request) {
                $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
                $branch_id = $branch->branch_id;
                $branch_name = $branch->branch_name;

                // Guardian creation
                $guardianData = Guardian::create([
                    'guardianname' => $request->guardian_name,
                    'guardianaddress' => $request->guardian_address,
                    'guardiantel' => $request->guardian_email,
                    'guardianmob' => $request->guardian_mobile,
                    'parent_relationship' => $request->parent_relationship,
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                ]);

                // Kin creation
                $kinDetails = Kin::create([
                    'kinname' => $request->kin_name,
                    'kinaddress' => $request->kin_address,
                    'kintel' => $request->kin_email,
                    'kinmob' => $request->kin_mobile,
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                ]);

                // Admission creation
                $admission = new Admission();
                $admission->familyno = $request->family_id;
                $admission->formfilingdate = date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date)));
                $admission->joiningdate = date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date)));
                $admission->medicalcondition = $request->medical_condition;
                // Combine package_amount and package_weeks into feedetail format: "280 for 5 weeks"
                if ($request->has('package_amount') && $request->has('package_weeks') && 
                    !empty($request->package_amount) && !empty($request->package_weeks)) {
                    $admission->feedetail = trim($request->package_amount) . ' for ' . strtolower(trim($request->package_weeks));
                } else {
                $admission->feedetail = $request->fee_detail;
                }
                $admission->familystatus = $request->family_status;
                $admission->payment_method = $request->payment_method;
                $admission->add_comment = $request->add_comment;

                // Child fields
                $admission->child_name1 = $request->child_name1 ?? null;
                $admission->school_name1 = $request->school_name1 ?? null;
                $admission->child_name2 = $request->child_name2 ?? null;
                $admission->school_name2 = $request->school_name2 ?? null;
                $admission->child_name3 = $request->child_name3 ?? null;
                $admission->school_name3 = $request->school_name3 ?? null;
                $admission->child_name4 = $request->child_name4 ?? null;
                $admission->school_name4 = $request->school_name4 ?? null;
                $admission->child_name5 = $request->child_name5 ?? null;
                $admission->school_name5 = $request->school_name5 ?? null;

                $admission->branch_id = $branch_id ?? null;
                $admission->branch_name = $branch_name ?? null;

                $admission->save();

                $admission_id = $admission->admissionid;
                $kin_id = $kinDetails->kinid;
                $guardian_id = $guardianData->Guardianid;

                // Student data arrays
                $firstNames = $request->first_name ?? [];
                $surnames = $request->surname ?? [];
                $dobs = $request->dob ?? [];
                $genders = $request->gender ?? [];
                $yearsInSchool = $request->years_in_school ?? [];
                $studentStatuses = $request->student_status ?? [];
                $subjectNames = $request->subject_names ?? [];
                $sessions = $request->sessions ?? [];
                $targetGrades = $request->target_grades ?? [];
                $curent_grade = $request->current_grades ?? [];
                $tiers =  $request->tiers ?? [];
                $qualifications =  $request->qualifications ?? [];

                // dd($curent_grade,$request->all());

                $count = count($firstNames);

                $studentIds = [];
                $studentName = [];
                $doctorNames = [];
                $doctorNumbers = [];
                $medicalAddresses = [];

                if (!empty($request->medical_conditions)) {
                    foreach ($request->medical_conditions as $condition) {
                        $decoded = json_decode($condition, true);
                        if ($decoded) {
                            $doctorNames[] = $decoded['doctor_name'] ?? null;
                            $doctorNumbers[] = $decoded['doctor_number'] ?? null;
                            $medicalAddresses[] = $decoded['maddress'] ?? null;
                        }
                    }
                }

                // Create students and store subjects data
                for ($i = 0; $i < $count; $i++) {
                    $student = Student::create([
                        'student_status' => $studentStatuses[$i] ?? null,
                        'studentname' => $firstNames[$i] ?? null,
                        'studentsur' => $surnames[$i] ?? null,
                        'studentdob' => !empty($dobs[$i]) ? date('Y-m-d', strtotime(str_replace('/', '-', $dobs[$i]))) : null,
                        'studentgender' => $genders[$i] ?? null,
                        'studentyearinschool' => $yearsInSchool[$i] ?? null,
                        'admissionid' => $request->family_id,
                        'kinid' => $kin_id,
                        'guardianid' => $guardian_id,
                        'medical_condition' => (!empty($doctorNames[$i]) && !empty($doctorNumbers[$i])) ? 1 : null,
                        'branch_id' => $branch_id,
                        'branch_name' => $branch_name,
                        'studenthours' => isset($sessions[$i]) ? json_encode($sessions[$i]) : null,
                        'subject_names' => isset($subjectNames[$i]) ? json_encode($subjectNames[$i]) : null,
                        'target_grades' => isset($targetGrades[$i]) ? json_encode($targetGrades[$i]) : null,
                        'tier' => isset($tiers[$i]) ? json_encode($tiers[$i]) : null,
                        'current_grades' => isset($curent_grade[$i]) ? json_encode($curent_grade[$i]) : null,
                        'qualifications' => isset($qualifications[$i]) ? json_encode($qualifications[$i]) : null,
                    ]);

                    $studentIds[] = $student->studentid;
                    $studentName[] = ($firstNames[$i] ?? '') . ' ' . ($surnames[$i] ?? '');
                }

                // Create medical conditions
                $medicalCount = count($doctorNames);
                for ($i = 0; $i < $medicalCount; $i++) {
                    medical_condition::create([
                        'guardianid' => $guardian_id,
                        'family_id' => $request->family_id,
                        'student_id' => $studentIds[$i] ?? null,
                        'drName' => $doctorNames[$i] ?? null,
                        'drNumber' => $doctorNumbers[$i] ?? null,
                        'medicalDetails' => $medicalAddresses[$i] ?? null,
                        'branch_id' => $branch_id,
                        'branch_name' => $branch_name,
                    ]);
                }

                // Create timetable entries
                foreach ($studentName as $index => $studentNamez) {
                    Log::debug("Creating Timetable for Student:", ['student_name' => $studentNamez]);
                    for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                        $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));
                        TimeTable::create([
                            'studentname' => $studentNamez,
                            'admissionid' => $request->family_id,
                            'day' => $day,
                            'branch_id' => $branch_id,
                            'branch_name' => $branch_name,
                        ]);
                        Log::debug("Timetable Entry Created:", [
                            'student_name' => $studentNamez,
                            'day' => $day,
                            'branch_id' => $branch_id,
                            'branch_name' => $branch_name,
                        ]);
                    }
                }
            });

            session()->flash('alert-success', 'Students registered successfully.');
            return redirect()->route('admin.admission.index');
        } catch (\Exception $e) {
            Log::error('Error storing admission: ' . $e->getMessage());
            session()->flash('alert-error', 'An error occurred while registering students. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function searchStudent()
    {
        // dd('');
        return view('branchFrontend.admission.searchStudent');
    }

    // public function getadmissions(Request $request)
    // {
    //     $keyword = $request->search;
    //     if ($request->option == 'Year') {
    //         $students =  Student::where('branch_id', session('branch_id'))->where(function ($query) use ($keyword) {
    //             $query->where('studentyearinschool', $keyword);
    //         })->get();
    //     }
    //     if ($request->option == 'student_name') {

    //         $students =  Student::where('branch_id', session('branch_id'))->where(function ($query) use ($keyword) {
    //             $query->where('studentname', 'LIKE', '%' . $keyword . '%')
    //                 ->orWhere('studentsur', 'LIKE', '%' . $keyword . '%');
    //         })->get();
    //         // dd($students);

    //     }

    //     if ($request->option == 'parent_name') {
    //         $parents = Guardian::where('branch_id', session('branch_id'))->where(function ($query) use ($keyword) {
    //             $query->where('guardianname', 'LIKE', '%' . $keyword . '%');
    //             //   ->orWhere('surname', 'LIKE', '%'.$keyword.'%');
    //         })->pluck('Guardianid');
    //         $students = Student::where('branch_id', session('branch_id'))->whereIn('guardianid', $parents)->get();
    //     }
    //     if ($request->option == 'phone') {
    //         $parents = Guardian::where('branch_id', session('branch_id'))->where('guardianmob', $keyword)->orWhere('guardiantel', $keyword)->pluck('Guardianid');
    //         $students = Student::where('branch_id', session('branch_id'))->whereIn('guardianid', $parents)->get();
    //     }
    //     if ($request->option == 'postCode') {
    //         $parents = Guardian::where('branch_id', session('branch_id'))->where('postal_code', $keyword)->pluck('Guardianid');
    //         $students = Student::where('branch_id', session('branch_id'))->whereIn('guardian_id', $parents)->get();
    //     }
    //     if ($request->option == 'family_id') {
    //         $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $request->search)->get();
    //     }
    //     // dd('');
    //     return view('branchFrontend.admission.searchStudent', compact('students'));
    // }
    public function getadmissions(Request $request)
    {
        $keyword = $request->search;
        $status = $request->status; // Get the status from the request
        $students = collect(); // Initialize an empty collection

        // Base query with branch_id filter
        $baseQuery = Student::where('branch_id', session('branch_id'));

        // Apply search filters based on the selected option
        if ($request->option == 'Year') {
            $students = $baseQuery->where('studentyearinschool', $keyword)->get();
        } elseif ($request->option == 'student_name') {
            $students = $baseQuery->where(function ($query) use ($keyword) {
                $query->where('studentname', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('studentsur', 'LIKE', '%' . $keyword . '%');
            })->get();
        } elseif ($request->option == 'parent_name') {
            $parents = Guardian::where('branch_id', session('branch_id'))
                ->where('guardianname', 'LIKE', '%' . $keyword . '%')
                ->pluck('Guardianid');
            $students = $baseQuery->whereIn('guardianid', $parents)->get();
        } elseif ($request->option == 'phone') {
            $parents = Guardian::where('branch_id', session('branch_id'))
                ->where(function ($query) use ($keyword) {
                    $query->where('guardianmob', $keyword)
                        ->orWhere('guardiantel', $keyword);
                })->pluck('Guardianid');
            $students = $baseQuery->whereIn('guardianid', $parents)->get();
        } elseif ($request->option == 'postCode') {
            $parents = Guardian::where('branch_id', session('branch_id'))
                ->where('postal_code', $keyword)
                ->pluck('Guardianid');
            $students = $baseQuery->whereIn('guardianid', $parents)->get();
        } elseif ($request->option == 'family_id') {
            $students = $baseQuery->where('admissionid', $keyword)->get();
        }

        // Apply status filter if provided
        if (!empty($status)) {
            $students = $students->filter(function ($student) use ($status) {
                return $student->student_status === $status;
            });
        }

        return view('branchFrontend.admission.searchStudent', compact('students'));
    }


    public function newAdmissionShow($studentid, $admissionid)
    {
        $student_id = $studentid;
        $family_id = $admissionid;

        $consent   = Consent::where('branch_id', session('branch_id'))->where('family_id', $family_id)->first();
        $students  = Student::where('branch_id', session('branch_id'))->where('studentid', $student_id)->where('admissionid', $family_id)->first();
        $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $family_id)->first();



        $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $students->kinid)->first();
        $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $students->guardianid)->first();
        $students->medical_condition_array = medical_condition::where('student_id', $students->studentid)->where('branch_id', session('branch_id'))->first();
        $medical_condition = $students->medical_condition_array;
        // dd($students);
        // dd($students,$medical_condition);
        return view('branchFrontend.admission.showAdmission', compact('consent', 'students', 'admission', 'kin', 'guardian', 'medical_condition'));
    }


    // public function admissionEditNew($id)
    // {
    //     $students  = Student::where('branch_id', session('branch_id'))->where('studentid', $id)->get();

    //     foreach ($students as $student) {

    //         $family_id =  $student->admissionid;
    //         $consent   = Consent::where('branch_id', session('branch_id'))->where('family_id', $family_id)->first();
    //         $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $family_id)->first();

    //         $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $student->kinid)->first();
    //         $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $student->guardianid)->first();
    //         // dd($guardian);
    //         $student->medical_condition_array = medical_condition::where('branch_id', session('branch_id'))->where('student_id', $student->studentid)->first();
    //     }
    //     $subjects = DB::table('subjects')
    //         ->select('name')
    //         ->where('branch_id', session('branch_id'))
    //         ->distinct()
    //         ->pluck('name')
    //         ->toArray();
    //     // dd($students);
    //     return view('branchFrontend.admission.editAdmission', compact('consent', 'family_id', 'students', 'admission', 'kin', 'guardian', 'subjects'));
    // }

    public function admissionEditNew($id)
    {
        $students = Student::where('branch_id', session('branch_id'))->where('studentid', $id)->get();

        $guardian = null;
        $consent = null;
        $admission = null;
        $kin = null;
        $family_id = null;

        foreach ($students as $student) {
            $family_id = $student->admissionid;
            $consent = Consent::where('branch_id', session('branch_id'))->where('family_id', $family_id)->first();
            $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $family_id)->first();
            $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $student->kinid)->first();
            $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $student->guardianid)->first();

            // Fetch medical condition
            $medical_condition = medical_condition::where('branch_id', session('branch_id'))
                ->where('student_id', $student->studentid)
                ->first();

            // Attach medical condition fields to the student
            $student->medical_condition = $medical_condition ? $medical_condition->medicalDetails : null;
            $student->medical_conditions_explanation = $medical_condition ? $medical_condition->medical_conditions_explanation : null;
            $student->allergies = $medical_condition ? $medical_condition->allergies : null;
            $student->allergies_explanation = $medical_condition ? $medical_condition->allergies_explanation : null;
            $student->additionalNeeds = $student ? $student->additionalNeeds : null; // If applicable
            $student->additional_needs_explanation = $student ? $student->additional_needs_explanation : null; // If applicable
            $student->medical_condition_array = $medical_condition; // For GP-related fields

            // Debugging: Log the medical_condition value to verify
            \Log::info('Student ID: ' . $student->studentid . ', Medical Condition: ' . ($student->medical_condition ?? 'null'));
        }

        $subjects = DB::table('subjects')
            ->select('name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->pluck('name')
            ->toArray();

        // Parse package to extract amount and weeks for split input
        $packageAmount = '';
        $packageWeeks = '';
        if ($admission && $admission->feedetail) {
            $package = $admission->feedetail;
            // Try new format: "280 for 2 weeks", "280 for 4 weeks", "280 for ucas session", "280 for per month", "280 for per session"
            if (preg_match('/(\d+)\s+for\s+(.+)/i', $package, $matches)) {
                $packageAmount = $matches[1];
                $packageWeeks = strtolower(trim($matches[2]));
            }
            // Try old format: "280for4weeks"
            elseif (preg_match('/(\d+)for(\d+)weeks?/i', $package, $matches)) {
                $packageAmount = $matches[1];
                $packageWeeks = strtolower($matches[2] . ' week' . ($matches[2] > 1 ? 's' : ''));
            }
            // Try to extract just numbers
            elseif (preg_match('/(\d+)/', $package, $matches)) {
                $packageAmount = $matches[1];
            }
        }

        // Parse guardian name to extract first and last name for auto-filling consent
        $guardianFirstName = '';
        $guardianLastName = '';
        if ($guardian && $guardian->guardianname) {
            $guardianName = trim($guardian->guardianname);
            // Split by last space to get last name as the last word
            $lastSpacePos = strrpos($guardianName, ' ');
            if ($lastSpacePos !== false) {
                $guardianFirstName = substr($guardianName, 0, $lastSpacePos);
                $guardianLastName = substr($guardianName, $lastSpacePos + 1);
            } else {
                // If no space found, use entire name as first name
                $guardianFirstName = $guardianName;
                $guardianLastName = '';
            }
        }

        // Debugging: Dump students to inspect data
        // dd($students);

        return view('branchFrontend.admission.editAdmission', compact('consent', 'family_id', 'students', 'admission', 'kin', 'guardian', 'subjects', 'packageAmount', 'packageWeeks', 'guardianFirstName', 'guardianLastName'));
    }


    // public function updateAdmissionForm(Request $request)
    // {

    //     // dd($request->all());
    //     // Update Consent
    //     // $consent = Consent::where('family_id', $request->family_id)->first();
    //     // if ($consent) {
    //     //     $consent->update([
    //     //         'consent_1_first_name' => $request->consent_1_first_name ?? null,
    //     //         'consent_1_last_name'  => $request->consent_1_last_name ?? null,
    //     //         'consent_1signature'   => $request->signature ?? null,
    //     //         'consent_1_date'       => $request->consent_1date ?? null,
    //     //         'how_did_you_hear'     => $request->how_did_you_hear ?? null,
    //     //     ]);
    //     // }

    //     $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //     $branch_id = $branch->branch_id;
    //     $branch_name = $branch->branch_name;



    //     $consent = Consent::where('branch_id', session('branch_id'))->firstOrNew(['family_id' => $request->family_id]);

    //     $consent->fill([
    //         'consent_1_first_name' => $request->consent_1_first_name ?? null,
    //         'consent_1_last_name'  => $request->consent_1_last_name ?? null,
    //         'consent_1signature'   => $request->signature ?? null,
    //         'consent_1_date'       => $request->consent_1date ?? null,
    //         'how_did_you_hear'     => $request->how_did_you_hear ?? null,
    //         'branch_name'          => $branch_name,
    //         'branch_id'            => $branch_id,
    //     ]);

    //     $consent->save();

    //     // Update Guardian
    //     $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $request->guardianid ?? null)->first();
    //     if ($guardian) {
    //         $guardian->update([
    //             'guardianname' => ($request->parent1_first_name ?? '') . ' ' . ($request->parent1_last_name ?? ''),
    //             'guardianaddress' => $request->parent1_Address ?? null,
    //             'address_line_2' => $request->parent1_Address_line2 ?? null,
    //             'city' => $request->parent1_city ?? null,
    //             'countyStateRegion' => $request->parent1_country_state_region ?? null,
    //             'zIPCode' => $request->parent1_zipCode ?? null,
    //             'country' => $request->parent1_country ?? null,
    //             'guardiantel' => $request->parent1_email ?? null,
    //             'guardianmob' => $request->parent1_mobile ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }

    //     // Update Kin
    //     $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $request->kinid ?? null)->first();
    //     if ($kin) {
    //         $kin->update([
    //             'kinname' => ($request->emergency_conatct1_first_name ?? '') . ' ' . ($request->emergency_conatct1_last_name ?? ''),
    //             'kinaddress' => $request->emergency_conatct1_Address ?? null,
    //             'emergency_conatct1_Address_line2' => $request->emergency_conatct1_Address_line2 ?? null,
    //             'emergency_conatct1_city' => $request->emergency_conatct1_city ?? null,
    //             'emergency_conatct1_country_state_region' => $request->emergency_conatct1_country_state_region ?? null,
    //             'emergency_conatct1_zipCode' => $request->emergency_conatct1_zipCode ?? null,
    //             'emergency_conatct1_country' => $request->emergency_conatct1_country ?? null,
    //             'kintel' => $request->emergency_conatct1_email ?? null,
    //             'kinmob' => $request->emergency_conatct1_mobile ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }
    //     // dd($request->child_name1,$request->school_name1);
    //     // Update Admission
    //     $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $request->family_id ?? null)->first();
    //     if ($admission) {
    //         $admission->update([
    //             'formfilingdate' => $request->form_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date))) : null,
    //             'joiningdate' => $request->joining_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date))) : null,
    //             'medicalcondition' => $request->medical_condition ?? null,
    //             'feedetail' => $request->fee_detail ?? null,
    //             'familystatus' => $request->family_status ?? null,
    //             'payment_method' => $request->payment_method ?? null,
    //             'add_comment' => $request->add_comment ?? null,
    //             'child_name1' => $request->child_name1 ?? null,
    //             'school_name1' => $request->school_name1 ?? null,
    //             'child_name2' => $request->child_name2 ?? null,
    //             'school_name2' => $request->school_name2 ?? null,
    //             'child_name3' => $request->child_name3 ?? null,
    //             'school_name3' => $request->school_name3 ?? null,
    //             'child_name4' => $request->child_name4 ?? null,
    //             'school_name4' => $request->school_name4 ?? null,
    //             'child_name5' => $request->child_name5 ?? null,
    //             'school_name5' => $request->school_name5 ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }

    //     // Update or Create Students and Medical Conditions
    //     $students = $request->input('student') ?? [];
    //     foreach ($students as $studentData) {
    //         // dd(($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));
    //         // Use updateOrCreate to handle both create and update scenarios
    //         $student = Student::updateOrCreate(
    //             ['studentid' => $studentData['studentid'] ?? null], // Check if studentid exists
    //             [
    //                 'studentname' => $studentData['firstName'] ?? null,
    //                 'studentsur' => $studentData['lastName'] ?? null,
    //                 'studentdob' => $studentData['dob'] ?? null,
    //                 'studentgender' => $studentData['gender'] ?? null,
    //                 'studentyearinschool' => isset($studentData['yearInSchool'])
    //                     ? preg_replace('/\D/', '', subject: trim($studentData['yearInSchool']))
    //                     : null,

    //                 'studenthours' => $studentData['tuitionHours'] ?? null,
    //                 'medical_condition' => (!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
    //                 'allergies' => $studentData['allergies'] ?? null,
    //                 'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
    //                 'additionalNeeds' => $studentData['additionalNeeds'] ?? null,
    //                 'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //                 'photoConsent' => json_encode($studentData['photoConsent'] ?? []),
    //                 'leaveAlone' => $studentData['leaveAlone'] ?? null,
    //                 'kinid' => $kin->kinid ?? null,
    //                 'guardianid' => $guardian->Guardianid ?? null,
    //                 'admissionid' => $request->family_id ?? null,
    //                 'student_status' => $studentData['student_status'] ?? null,
    //                 'branch_name'          => $branch_name,
    //                 'branch_id'            => $branch_id,
    //             ]
    //         );

    //         // Update or Create Medical Condition
    //         medical_condition::updateOrCreate(
    //             ['student_id' => $student->studentid], // Use 'student_id' as the primary key
    //             [
    //                 'guardianid' => $guardian->Guardianid ?? null,
    //                 'family_id' => $request->family_id ?? null,
    //                 'gpPrefix' => $studentData['gpPrefix'] ?? null,
    //                 'drName' => ($studentData['gpFirstName'] ?? '') . ' ' . ($studentData['gpLastName'] ?? ''),
    //                 'drNumber' => $studentData['GPPhone'] ?? null,
    //                 'medicalDetails' => $studentData['medicalConditions'] ?? null,
    //                 'allergies' => $studentData['allergies'] ?? null,
    //                 'gpAddress' => $studentData['gpAddress'] ?? null,
    //                 'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? null,
    //                 'gp_city' => $studentData['gp_city'] ?? null,
    //                 'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? null,
    //                 'gpzipCode' => $studentData['gpzipCode'] ?? null,
    //                 'gpcountry' => $studentData['gpcountry'] ?? null,
    //                 'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //                 'branch_name'          => $branch_name,
    //                 'branch_id'            => $branch_id,
    //             ]
    //         );


    //         /////////////////////////////////////////////////
    //         // Update TimeTable records if the name has changed
    //         $oldName = $studentData['oldName'] ?? '';
    //         $newName = ($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? '');
    //         // dd($oldName,$newName);
    //         if ($oldName && $oldName !== $newName) {
    //             TimeTable::where('branch_id', session('branch_id'))->where('admissionid', $request->family_id)
    //                 ->where('studentname', 'LIKE', '%' . $oldName . '%')
    //                 ->update([
    //                     'studentname' => $newName,
    //                 ]);
    //         }

    //         // If it's a new student, create TimeTable records
    //         if (empty($studentData['studentid'])) {
    //             for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
    //                 $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days"))); // Get day name (Monday to Sunday)

    //                 TimeTable::create([
    //                     'studentname' => $newName,
    //                     'admissionid' => $request->family_id,
    //                     'day' => $day,
    //                     'branch_name'          => $branch_name,
    //                     'branch_id'            => $branch_id,
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect('searchStudent')->with('success', 'Admission updated successfully!');
    // }
    // public function updateAdmissionForm(Request $request)
    // {
    //     // dd($request->all());
    //     $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //     $branch_id = $branch->branch_id;
    //     $branch_name = $branch->branch_name;

    //     $consent = Consent::where('branch_id', session('branch_id'))->firstOrNew(['family_id' => $request->family_id]);

    //     $consent->fill([
    //         'consent_1_first_name' => $request->consent_1_first_name ?? null,
    //         'consent_1_last_name'  => $request->consent_1_last_name ?? null,
    //         'consent_1signature'   => $request->signature ?? null,
    //         'consent_1_date'       => $request->consent_1date ?? null,
    //         'how_did_you_hear'     => $request->how_did_you_hear ?? null,
    //         'branch_name'          => $branch_name,
    //         'branch_id'            => $branch_id,
    //     ]);

    //     $consent->save();

    //     // Update Guardian
    //     $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $request->guardianid ?? null)->first();
    //     if ($guardian) {
    //         $guardian->update([
    //             'guardianname' => ($request->parent1_first_name ?? '') . ' ' . ($request->parent1_last_name ?? ''),
    //             'guardianaddress' => $request->parent1_Address ?? null,
    //             'address_line_2' => $request->parent1_Address_line2 ?? null,
    //             'city' => $request->parent1_city ?? null,
    //             'countyStateRegion' => $request->parent1_country_state_region ?? null,
    //             'zIPCode' => $request->parent1_zipCode ?? null,
    //             'country' => $request->parent1_country ?? null,
    //             'guardiantel' => $request->parent1_email ?? null,
    //             'guardianmob' => $request->parent1_mobile ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }

    //     // Update Kin
    //     $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $request->kinid ?? null)->first();
    //     if ($kin) {
    //         $kin->update([
    //             'kinname' => ($request->emergency_conatct1_first_name ?? '') . ' ' . ($request->emergency_conatct1_last_name ?? ''),
    //             'kinaddress' => $request->emergency_conatct1_Address ?? null,
    //             'emergency_conatct1_Address_line2' => $request->emergency_conatct1_Address_line2 ?? null,
    //             'emergency_conatct1_city' => $request->emergency_conatct1_city ?? null,
    //             'emergency_conatct1_country_state_region' => $request->emergency_conatct1_country_state_region ?? null,
    //             'emergency_conatct1_zipCode' => $request->emergency_conatct1_zipCode ?? null,
    //             'emergency_conatct1_country' => $request->emergency_conatct1_country ?? null,
    //             'kintel' => $request->emergency_conatct1_email ?? null,
    //             'kinmob' => $request->emergency_conatct1_mobile ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }

    //     // Update Admission
    //     $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $request->family_id ?? null)->first();
    //     if ($admission) {
    //         $admission->update([
    //             'formfilingdate' => $request->form_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date))) : null,
    //             'joiningdate' => $request->joining_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date))) : null,
    //             'medicalcondition' => $request->medical_condition ?? null,
    //             'feedetail' => $request->fee_detail ?? null,
    //             'familystatus' => $request->family_status ?? null,
    //             'payment_method' => $request->payment_method ?? null,
    //             'add_comment' => $request->add_comment ?? null,
    //             'child_name1' => $request->child_name1 ?? null,
    //             'school_name1' => $request->school_name1 ?? null,
    //             'child_name2' => $request->child_name2 ?? null,
    //             'school_name2' => $request->school_name2 ?? null,
    //             'child_name3' => $request->child_name3 ?? null,
    //             'school_name3' => $request->school_name3 ?? null,
    //             'child_name4' => $request->child_name4 ?? null,
    //             'school_name4' => $request->school_name4 ?? null,
    //             'child_name5' => $request->child_name5 ?? null,
    //             'school_name5' => $request->school_name5 ?? null,
    //             'branch_name'          => $branch_name,
    //             'branch_id'            => $branch_id,
    //         ]);
    //     }

    //     // Update or Create Students and Medical Conditions
    //     $students = $request->input('student') ?? [];
    //     foreach ($students as $studentData) {
    //         $student = Student::where('branch_id', session('branch_id'))
    //             ->updateOrCreate(
    //                 ['studentid' => $studentData['studentid'] ?? null],
    //                 [
    //                     'studentname' => $studentData['firstName'] ?? null,
    //                     'studentsur' => $studentData['lastName'] ?? null,
    //                     'studentdob' => $studentData['dob'] ?? null,
    //                     'studentgender' => $studentData['gender'] ?? null,
    //                     'studentyearinschool' => isset($studentData['yearInSchool'])
    //                         ? preg_replace('/\D/', '', subject: trim($studentData['yearInSchool']))
    //                         : null,
    //                     'studenthours' => $studentData['tuitionHours'] ?? null,
    //                     'medical_condition' => (!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
    //                     'allergies' => $studentData['allergies'] ?? null,
    //                     'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
    //                     'additionalNeeds' => $studentData['additionalNeeds'] ?? null,
    //                     'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //                     'photoConsent' => json_encode($studentData['photoConsent'] ?? []),
    //                     'leaveAlone' => $studentData['leaveAlone'] ?? null,
    //                     'kinid' => $kin->kinid ?? null,
    //                     'guardianid' => $guardian->Guardianid ?? null,
    //                     'admissionid' => $request->family_id ?? null,
    //                     'student_status' => $studentData['student_status'] ?? null,
    //                     'branch_name'          => $branch_name,
    //                     'branch_id'            => $branch_id,
    //                 ]
    //             );

    //         // Update or Create Medical Condition
    //         medical_condition::where('branch_id', session('branch_id'))
    //             ->updateOrCreate(
    //                 ['student_id' => $student->studentid],
    //                 [
    //                     'guardianid' => $guardian->Guardianid ?? null,
    //                     'family_id' => $request->family_id ?? null,
    //                     'gpPrefix' => $studentData['gpPrefix'] ?? null,
    //                     'drName' => ($studentData['gpFirstName'] ?? '') . ' ' . ($studentData['gpLastName'] ?? ''),
    //                     'drNumber' => $studentData['GPPhone'] ?? null,
    //                     'medicalDetails' => $studentData['medicalConditions'] ?? null,
    //                     'allergies' => $studentData['allergies'] ?? null,
    //                     'gpAddress' => $studentData['gpAddress'] ?? null,
    //                     'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? null,
    //                     'gp_city' => $studentData['gp_city'] ?? null,
    //                     'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? null,
    //                     'gpzipCode' => $studentData['gpzipCode'] ?? null,
    //                     'gpcountry' => $studentData['gpcountry'] ?? null,
    //                     'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //                     'branch_name'          => $branch_name,
    //                     'branch_id'            => $branch_id,
    //                 ]
    //             );

    //         // Update TimeTable records if the name has changed
    //         $oldName = $studentData['oldName'] ?? '';
    //         $newName = ($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? '');

    //         if ($oldName && $oldName !== $newName) {
    //             TimeTable::where('branch_id', session('branch_id'))
    //                 ->where('admissionid', $request->family_id)
    //                 ->where('studentname', 'LIKE', '%' . $oldName . '%')
    //                 ->update([
    //                     'studentname' => $newName,
    //                 ]);
    //         }

    //         // If it's a new student, create TimeTable records
    //         if (empty($studentData['studentid'])) {
    //             for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
    //                 $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));

    //                 TimeTable::create([
    //                     'studentname' => $newName,
    //                     'admissionid' => $request->family_id,
    //                     'day' => $day,
    //                     'branch_name' => $branch_name,
    //                     'branch_id' => $branch_id,
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect('searchStudent')->with('success', 'Admission updated successfully!');
    // }
    public function updateAdmissionForm(Request $request)
    {
        // dd($request->all());
        $dateFields = ['form_date', 'joining_date', 'consent_1date'];

        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $request->merge([
                        $field => Carbon::createFromFormat('d/m/Y', $request->$field)->format('Y-m-d')
                    ]);
                } catch (\Exception $e) {
                    // ignore agar already Y-m-d hai
                }
            }
        }

        // 2. Student dob fields
        if ($request->has('student')) {
            $students = $request->student;

            foreach ($students as $index => $student) {
                if (!empty($student['dob'])) {
                    try {
                        $students[$index]['dob'] = Carbon::createFromFormat('d/m/Y', $student['dob'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        // ignore agar already Y-m-d hai
                    }
                }
            }

            // update back into request
            $request->merge([
                'student' => $students
            ]);
        }

        // dd($request->all());
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        // ========== START: Capture old data for change tracking ==========
        $oldData = [];
        
        // Get old Consent data
        $oldConsent = Consent::where('branch_id', session('branch_id'))
            ->where('family_id', $request->family_id)
            ->first();
        if ($oldConsent) {
            $oldData['consent'] = $oldConsent->toArray();
        } else {
            $oldData['consent'] = [];
        }

        // Get old Guardian data
        $oldGuardian = Guardian::where('branch_id', session('branch_id'))
            ->where('Guardianid', $request->guardianid ?? null)
            ->first();
        if ($oldGuardian) {
            $oldData['guardian'] = $oldGuardian->toArray();
        } else {
            $oldData['guardian'] = [];
        }

        // Get old Kin data
        $oldKin = Kin::where('branch_id', session('branch_id'))
            ->where('kinid', $request->kinid ?? null)
            ->first();
        if ($oldKin) {
            $oldData['kin'] = $oldKin->toArray();
        } else {
            $oldData['kin'] = [];
        }

        // Get old Admission data
        $oldAdmission = Admission::where('branch_id', session('branch_id'))
            ->where('familyno', $request->family_id ?? null)
            ->first();
        if ($oldAdmission) {
            $oldData['admission'] = $oldAdmission->toArray();
        } else {
            $oldData['admission'] = [];
        }

        // Get old Students data with medical condition data
        $oldStudents = [];
        if ($request->has('student')) {
            foreach ($request->student as $studentData) {
                if (!empty($studentData['studentid'])) {
                    $oldStudent = Student::where('branch_id', session('branch_id'))
                        ->where('studentid', $studentData['studentid'])
                        ->first();
                    if ($oldStudent) {
                        $oldStudentArray = $oldStudent->toArray();
                        
                        // Get medical condition data for this student
                        // Try multiple ways to find the record
                        $oldMedicalCondition = null;
                        
                        // First try with branch_id
                        $oldMedicalCondition = medical_condition::where('student_id', $studentData['studentid'])
                            ->where('branch_id', session('branch_id'))
                            ->first();
                        
                        // If not found, try without branch_id
                        if (!$oldMedicalCondition) {
                            $oldMedicalCondition = medical_condition::where('student_id', $studentData['studentid'])
                                ->whereNull('branch_id')
                                ->first();
                        }
                        
                        // If still not found, try with any branch_id
                        if (!$oldMedicalCondition) {
                            $oldMedicalCondition = medical_condition::where('student_id', $studentData['studentid'])
                                ->first();
                        }
                        
                        // Merge medical condition fields into student data
                        if ($oldMedicalCondition) {
                            // Map medical_condition table fields to form field names
                            // Note: medicalDetails in DB = has_medical_conditions in form
                            // Note: allergies in DB = has_allergies in form
                            $oldStudentArray['has_medical_conditions'] = $oldMedicalCondition->medicalDetails ?? null;
                            $oldStudentArray['has_allergies'] = $oldMedicalCondition->allergies ?? null;
                            
                            // Only set explanation if parent field is 'yes' (form logic)
                            // But capture the actual value from database first
                            $oldMedicalExplanation = $oldMedicalCondition->medical_conditions_explanation ?? null;
                            $oldAllergiesExplanation = $oldMedicalCondition->allergies_explanation ?? null;
                            
                            // Trim and clean values
                            if ($oldMedicalExplanation) {
                                $oldMedicalExplanation = trim($oldMedicalExplanation);
                            }
                            if ($oldAllergiesExplanation) {
                                $oldAllergiesExplanation = trim($oldAllergiesExplanation);
                            }
                            
                            // Set explanation only if parent is 'yes', otherwise null
                            if (($oldMedicalCondition->medicalDetails ?? null) === 'yes') {
                                $oldStudentArray['medical_conditions_explanation'] = $oldMedicalExplanation ?: null;
                            } else {
                                $oldStudentArray['medical_conditions_explanation'] = null;
                            }
                            
                            if (($oldMedicalCondition->allergies ?? null) === 'yes') {
                                $oldStudentArray['allergies_explanation'] = $oldAllergiesExplanation ?: null;
                            } else {
                                $oldStudentArray['allergies_explanation'] = null;
                            }
                        } else {
                            // If no medical condition record, set to null
                            $oldStudentArray['medical_conditions_explanation'] = null;
                            $oldStudentArray['allergies_explanation'] = null;
                            $oldStudentArray['has_medical_conditions'] = null;
                            $oldStudentArray['has_allergies'] = null;
                        }
                        
                        $oldStudents[] = $oldStudentArray;
                    }
                }
            }
        }
        $oldData['students'] = $oldStudents;
        // ========== END: Capture old data for change tracking ==========

        // Update Consent
        $consent = Consent::where('branch_id', session('branch_id'))->firstOrNew(['family_id' => $request->family_id]);

        $consent->fill([
            'consent_1_first_name' => $request->consent_1_first_name ?? null,
            'consent_1_last_name'  => $request->consent_1_last_name ?? null,
            'consent_1signature'   => $request->signature ?? null,
            'consent_1_date'       => $request->consent_1date ?? null,
            'how_did_you_hear'     => $request->how_did_you_hear ?? null,
            'branch_name'          => $branch_name,
            'branch_id'            => $branch_id,
        ]);

        $consent->save();

        // Update Guardian
        $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $request->guardianid ?? null)->first();
        if ($guardian) {
            // Combine first and last name, or use parent1_first_name if last name not provided (backward compatibility)
            $guardianName = trim(($request->parent1_first_name ?? '') . ' ' . ($request->parent1_last_name ?? ''));
            if (empty($guardianName) && $request->parent1_first_name) {
                $guardianName = $request->parent1_first_name;
            }
            
            $guardian->update([
                'guardianname' => $guardianName,
                'guardianaddress' => $request->parent1_Address ?? null,
                'address_line_2' => $request->parent1_Address_line2 ?? null,
                'city' => $request->parent1_city ?? null,
                'countyStateRegion' => $request->parent1_country_state_region ?? null,
                'zIPCode' => $request->parent1_zipCode ?? null,
                'country' => $request->parent1_country ?? null,
                'guardiantel' => $request->parent1_email ?? null,
                'guardianmob' => $request->parent1_mobile ?? null,
                'branch_name'          => $branch_name,
                'branch_id'            => $branch_id,
            ]);
        }

        // Update Kin
        $kin = Kin::where('branch_id', session('branch_id'))->where('kinid', $request->kinid ?? null)->first();
        if ($kin) {
            $kin->update([
                'kinname' => ($request->emergency_conatct1_first_name ?? '') . ' ' . ($request->emergency_conatct1_last_name ?? ''),
                'kinaddress' => $request->emergency_conatct1_Address ?? null,
                'emergency_conatct1_Address_line2' => $request->emergency_conatct1_Address_line2 ?? null,
                'emergency_conatct1_city' => $request->emergency_conatct1_city ?? null,
                'emergency_conatct1_country_state_region' => $request->emergency_conatct1_country_state_region ?? null,
                'emergency_conatct1_zipCode' => $request->emergency_conatct1_zipCode ?? null,
                'emergency_conatct1_country' => $request->emergency_conatct1_country ?? null,
                'kintel' => $request->emergency_conatct1_email ?? null,
                'kinmob' => $request->emergency_conatct1_mobile ?? null,
                'branch_name'          => $branch_name,
                'branch_id'            => $branch_id,
            ]);
        }

        // Update Admission
        $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $request->family_id ?? null)->first();
        if ($admission) {
            $admission->update([
                'formfilingdate' => $request->form_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date))) : null,
                'joiningdate' => $request->joining_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date))) : null,
                'medicalcondition' => $request->medical_condition ?? null,
                'feedetail' => ($request->has('package_amount') && $request->has('package_weeks') && 
                    !empty($request->package_amount) && !empty($request->package_weeks)) 
                    ? trim($request->package_amount) . ' for ' . strtolower(trim($request->package_weeks))
                    : ($request->fee_detail ?? null),
                'familystatus' => $request->family_status ?? null,
                'payment_method' => $request->payment_method ?? null,
                'add_comment' => $request->add_comment ?? null,
                'child_name1' => $request->child_name1 ?? null,
                'school_name1' => $request->school_name1 ?? null,
                'child_name2' => $request->child_name2 ?? null,
                'school_name2' => $request->school_name2 ?? null,
                'child_name3' => $request->child_name3 ?? null,
                'school_name3' => $request->school_name3 ?? null,
                'child_name4' => $request->child_name4 ?? null,
                'school_name4' => $request->school_name4 ?? null,
                'child_name5' => $request->child_name5 ?? null,
                'school_name5' => $request->school_name5 ?? null,
                'branch_name'          => $branch_name,
                'branch_id'            => $branch_id,
            ]);
        }

        // Update or Create Students and Medical Conditions
        $students = $request->input('student') ?? [];
        foreach ($students as $index => $studentData) {
            // dd($studentData,$studentData['additional_needs_explanation'] );

            // Get new studenthours value
            $newStudenthours = isset($studentData['sessions']) ? json_encode($studentData['sessions']) : null;

            // Check if student exists and get old quota for comparison
            $existingStudent = null;
            $oldStudenthours = null;
            $oldTotalWeeklyQuota = 0;
            if (!empty($studentData['studentid'])) {
                $existingStudent = Student::where('branch_id', session('branch_id'))
                    ->where('studentid', $studentData['studentid'])
                    ->first();

                if ($existingStudent) {
                    $oldStudenthours = $existingStudent->studenthours;

                    // Calculate old total weekly quota
                    if ($oldStudenthours) {
                        $oldHoursArray = json_decode($oldStudenthours, true);
                        if (is_array($oldHoursArray)) {
                            foreach ($oldHoursArray as $hours) {
                                $oldTotalWeeklyQuota += (int)$hours;
                            }
                        }
                    }
                }
            }

            // Calculate new total weekly quota
            $newTotalWeeklyQuota = 0;
            if ($newStudenthours) {
                $newHoursArray = json_decode($newStudenthours, true);
                if (is_array($newHoursArray)) {
                    foreach ($newHoursArray as $hours) {
                        $newTotalWeeklyQuota += (int)$hours;
                    }
                }
            }

            // Record quota change if quota has changed
            if ($existingStudent && $oldStudenthours !== $newStudenthours && $oldTotalWeeklyQuota !== $newTotalWeeklyQuota) {
                $studentName = trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));

                DB::table('quota_history')->insert([
                    'studentid' => $existingStudent->studentid,
                    'family_id' => $request->family_id ?? $existingStudent->admissionid,
                    'student_name' => $studentName,
                    'old_studenthours' => $oldStudenthours,
                    'new_studenthours' => $newStudenthours,
                    'old_total_weekly_quota' => $oldTotalWeeklyQuota,
                    'new_total_weekly_quota' => $newTotalWeeklyQuota,
                    'change_date' => date('Y-m-d'), // Today's date when quota changed
                    'branch_id' => session('branch_id'),
                    'branch_name' => $branch_name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $student = Student::where('branch_id', session('branch_id'))
                ->updateOrCreate(
                    ['studentid' => $studentData['studentid'] ?? null],
                    [
                        'studentname' => $studentData['firstName'] ?? null,
                        'studentsur' => $studentData['lastName'] ?? null,
                        'studentdob' => $studentData['dob'] ?? null,
                        'studentgender' => $studentData['gender'] ?? null,
                        'studentyearinschool' => isset($studentData['yearInSchool'])
                            ? preg_replace('/\D/', '', subject: trim($studentData['yearInSchool']))
                            : null,
                        'studenthours' => $newStudenthours, //$studentData['tuitionHours'] ?? null,
                        'medical_condition' => $studentData['has_medical_conditions'] ?? null, //(!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
                        // 'medical_conditions_explanation' => $studentData['has_medical_conditions'] === 'yes' ? ($studentData['medical_conditions_explanation'] ?? null) : null,

                        // 'allergies' =>  $studentData['has_allergies'] ?? null, //$studentData['allergies'] ?? null,
                        // 'allergies_explanation' => $studentData['has_allergies'] === 'yes' ? ($studentData['allergies_explanation'] ?? null) : null,


                        // 'additionalNeeds' =>  $studentData['has_additional_needs'] ?? null, //$studentData['additionalNeeds'] ?? null,
                        // 'additional_needs_explanation' => $studentData['additional_needs_explanation'] ?? null,

                        'allergies' => isset($studentData['has_allergies'])
                            ? $studentData['has_allergies']
                            : null,

                        'allergies_explanation' => (
                            isset($studentData['has_allergies'])
                            && $studentData['has_allergies'] === 'yes'
                            && isset($studentData['allergies_explanation'])
                        )
                            ? $studentData['allergies_explanation']
                            : null,

                        'additionalNeeds' => isset($studentData['has_additional_needs'])
                            ? $studentData['has_additional_needs']
                            : null,

                        'additional_needs_explanation' => isset($studentData['additional_needs_explanation'])
                            ? $studentData['additional_needs_explanation']
                            : null,



                        // 'allergies' => $studentData['allergies'] ?? null,
                        'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
                        // 'medical_condition' => (!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
                        // 'allergies' => $studentData['allergies'] ?? null,
                        // 'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
                        // 'additionalNeeds' => $studentData['additionalNeeds'] ?? null,
                        'medicalConsent' => $studentData['medicalConsent'] ?? null,
                        'photoConsent' => json_encode($studentData['photoConsent'] ?? []),
                        'leaveAlone' => $studentData['leaveAlone'] ?? null,
                        'kinid' => $kin->kinid ?? null,
                        'guardianid' => $guardian->Guardianid ?? null,
                        'admissionid' => $request->family_id ?? null,
                        'student_status' => $studentData['student_status'] ?? null,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                        'subject_names' => isset($studentData['subject_names']) ? json_encode($studentData['subject_names']) : null,
                        'tier' => isset($studentData['tiers']) ? json_encode($studentData['tiers']) : null,
                        'sessions' =>  null, //isset($studentData['sessions']) ? json_encode($studentData['sessions']) : null,
                        'target_grades' => isset($studentData['target_grades']) ? json_encode($studentData['target_grades']) : null,
                        'current_grades' => isset($studentData['current_grades']) ? json_encode($studentData['current_grades']) : null,
                        'qualifications' => isset($studentData['qualifications']) ? json_encode($studentData['qualifications']) : null,
                    ]
                );

            // Update or Create Medical Condition
            medical_condition::where('branch_id', session('branch_id'))
                ->updateOrCreate(
                    ['student_id' => $student->studentid],
                    [
                        'guardianid' => $guardian->Guardianid ?? null,
                        'family_id' => $request->family_id ?? null,
                        'gpPrefix' => $studentData['gpPrefix'] ?? null,
                        'drName' => ($studentData['gpFirstName'] ?? '') . ' ' . ($studentData['gpLastName'] ?? ''),
                        'drNumber' => $studentData['GPPhone'] ?? null,
                        // 'medicalDetails' => $studentData['medicalConditions'] ?? null,
                        // 'allergies' => $studentData['allergies'] ?? null,
                        'gpAddress' => $studentData['gpAddress'] ?? null,
                        'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? null,
                        'gp_city' => $studentData['gp_city'] ?? null,
                        'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? null,
                        'gpzipCode' => $studentData['gpzipCode'] ?? null,
                        'gpcountry' => $studentData['gpcountry'] ?? null,
                        'medicalConsent' => $studentData['medicalConsent'] ?? null,



                        // 'medicalDetails' => $studentData['has_medical_conditions'] ?? null, //(!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
                        // 'medical_conditions_explanation' => $studentData['has_medical_conditions'] === 'yes' ? ($studentData['medical_conditions_explanation'] ?? null) : null,

                        // 'allergies' =>  $studentData['has_allergies'] ?? null, //$studentData['allergies'] ?? null,
                        // 'allergies_explanation' => $studentData['has_allergies'] === 'yes' ? ($studentData['allergies_explanation'] ?? null) : null,

                        'medicalDetails' => isset($studentData['has_medical_conditions'])
                            ? $studentData['has_medical_conditions']
                            : null,

                        'medical_conditions_explanation' => (
                            isset($studentData['has_medical_conditions']) &&
                            $studentData['has_medical_conditions'] === 'yes' &&
                            isset($studentData['medical_conditions_explanation'])
                        )
                            ? $studentData['medical_conditions_explanation']
                            : null,

                        'allergies' => isset($studentData['has_allergies'])
                            ? $studentData['has_allergies']
                            : null,

                        'allergies_explanation' => (
                            isset($studentData['has_allergies']) &&
                            $studentData['has_allergies'] === 'yes' &&
                            isset($studentData['allergies_explanation'])
                        )
                            ? $studentData['allergies_explanation']
                            : null,


                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                    ]
                );

            // Update TimeTable records if the name has changed
            $oldName = trim($studentData['oldName'] ?? '');
            $newName = trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));
            $familyId = $request->family_id ?? null;

            if ($oldName && $newName && $oldName !== $newName && $familyId) {
                // Update TimeTable records
                TimeTable::where('branch_id', session('branch_id'))
                    ->where('admissionid', $familyId)
                    ->where('studentname', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'studentname' => $newName,
                    ]);

                // Update general_timetables - Update student_names JSON array where student_ids contains family_id
                // Use same logic as inactive student removal, but just update the name instead of removing
                $timetables = GeneralTimetable::where('branch_id', session('branch_id'))
                    ->where('student_ids', 'LIKE', '%' . $familyId . '%')
                    ->get();

                // Filter timetables by student name in PHP (same as inactive logic)
                $matchingTimetables = $timetables->filter(function ($timetable) use ($oldName) {
                    $studentNames = json_decode($timetable->student_names, true) ?? [];
                    return in_array($oldName, $studentNames);
                });

                foreach ($matchingTimetables as $timetable) {
                    // Parse JSON fields (same as inactive logic)
                    $studentIds = json_decode($timetable->student_ids, true) ?? [];
                    $studentNames = json_decode($timetable->student_names, true) ?? [];

                    // Find the index of the student by family_id and name (same pattern as inactive)
                    $indexToUpdate = -1;
                    foreach ($studentIds as $index => $id) {
                        if ((string)$id === (string)$familyId && isset($studentNames[$index]) && $studentNames[$index] === $oldName) {
                            $indexToUpdate = $index;
                            break;
                        }
                    }

                    if ($indexToUpdate !== -1) {
                        // Update the student name in the array (instead of removing like inactive)
                        $studentNames[$indexToUpdate] = $newName;

                        // Update the record with modified student_names array (same pattern as inactive)
                        $timetable->update([
                            'student_names' => json_encode($studentNames),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Update attendance table - update student_name where family_id matches
                Attendance::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'student_name' => $newName,
                    ]);

                // Update assign_books table - update student_name where family_id matches
                AssignBook::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'student_name' => $newName,
                    ]);

                // Update mock_results table - update name where family_id matches
                MockResult::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'name' => $newName,
                    ]);

                // Update student_grades table - update full_name where family_id matches
                StudentGrade::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('full_name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'full_name' => $newName,
                    ]);

                // Update student_tests table - update student_name where family_id matches
                StudentTest::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'student_name' => $newName,
                    ]);

                // Update iag_meetings table - update learner_name where family_id matches
                IagMeeting::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('learner_name', 'LIKE', '%' . $oldName . '%')
                    ->update([
                        'learner_name' => $newName,
                    ]);
            }

            // If it's a new student, create TimeTable records
            if (empty($studentData['studentid'])) {
                for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                    $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));

                    TimeTable::create([
                        'studentname' => $newName,
                        'admissionid' => $request->family_id,
                        'day' => $day,
                        'branch_name' => $branch_name,
                        'branch_id' => $branch_id,
                    ]);
                }
            }
        }


        $student = Student::where('branch_id', session('branch_id'))
            ->where('studentid', $studentData['studentid'] ?? null)
            ->first();

        if ($student && $student->student_status === 'inactive') {
            // Get the student's full name and family_id
            $fullName = trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));
            $familyId = $student->admissionid ?? $request->family_id;

            // Log debugging information
            Log::info('Checking inactive student for timetable removal', [
                'student_name' => $fullName,
                'family_id' => $familyId,
                'branch_id' => session('branch_id'),
                'today_date' => Carbon::today()->toDateString(),
            ]);

            // Fetch future timetable records where family_id is included
            $timetables = GeneralTimetable::where('branch_id', session('branch_id'))
                ->where('date', '>=', Carbon::today()->toDateString())
                ->where('student_ids', 'LIKE', '%' . $familyId . '%')
                ->get();

            // Log the fetched timetables
            Log::info('Fetched timetables for removal', [
                'count' => $timetables->count(),
                'timetables' => $timetables->toArray(),
            ]);

            // Filter timetables by student name in PHP
            $matchingTimetables = $timetables->filter(function ($timetable) use ($fullName) {
                $studentNames = json_decode($timetable->student_names, true) ?? [];
                return in_array($fullName, $studentNames);
            });

            // Log the filtered timetables
            Log::info('Filtered timetables by student name', [
                'count' => $matchingTimetables->count(),
                'timetables' => $matchingTimetables->toArray(),
            ]);

            foreach ($matchingTimetables as $timetable) {
                // Parse JSON fields
                $studentIds = json_decode($timetable->student_ids, true) ?? [];
                $studentNames = json_decode($timetable->student_names, true) ?? [];
                $subjects = json_decode($timetable->subjects, true) ?? [];
                $yearInSchools = json_decode($timetable->year_in_schools, true) ?? [];
                $isAttendance = json_decode($timetable->is_attendance, true) ?? [];
                $permanent = json_decode($timetable->permanent, true) ?? [];

                // Find the index of the student by family_id and name
                $indexToRemove = -1;
                foreach ($studentIds as $index => $id) {
                    if ($id === (string) $familyId && $studentNames[$index] === $fullName) {
                        $indexToRemove = $index;
                        break;
                    }
                }

                if ($indexToRemove !== -1) {
                    // Remove the student from all arrays
                    unset($studentIds[$indexToRemove]);
                    unset($studentNames[$indexToRemove]);
                    unset($subjects[$indexToRemove]);
                    unset($yearInSchools[$indexToRemove]);
                    unset($isAttendance[$indexToRemove]);
                    unset($permanent[$indexToRemove]);

                    // Reindex arrays to maintain consistency
                    $studentIds = array_values($studentIds);
                    $studentNames = array_values($studentNames);
                    $subjects = array_values($subjects);
                    $yearInSchools = array_values($yearInSchools);
                    $isAttendance = array_values($isAttendance);
                    $permanent = array_values($permanent);

                    // Update or delete the timetable record
                    if (empty($studentIds)) {
                        // If no students remain, delete the record
                        $timetable->delete();
                        Log::info('Deleted timetable record', [
                            'timetable_id' => $timetable->id,
                            'reason' => 'No students remaining',
                        ]);
                    } else {
                        // Update the record with modified arrays
                        $timetable->update([
                            'student_ids' => json_encode($studentIds),
                            'student_names' => json_encode($studentNames),
                            'subjects' => json_encode($subjects),
                            'year_in_schools' => json_encode($yearInSchools),
                            'is_attendance' => json_encode($isAttendance),
                            'permanent' => json_encode($permanent),
                            'updated_at' => now(),
                        ]);
                        Log::info('Updated timetable record', [
                            'timetable_id' => $timetable->id,
                            'student_name_removed' => $fullName,
                            'family_id' => $familyId,
                        ]);
                    }
                }
            }
        }

        // ========== START: Track changes and log to activity_log ==========
        try {
            // Prepare new data from request
            $newData = [];
            
            // New Consent data
            $newData['consent'] = [
                'consent_1_first_name' => $request->consent_1_first_name ?? null,
                'consent_1_last_name' => $request->consent_1_last_name ?? null,
                'consent_1signature' => $request->signature ?? null,
                'consent_1_date' => $request->consent_1date ?? null,
                'how_did_you_hear' => $request->how_did_you_hear ?? null,
            ];

            // New Guardian data
            if ($guardian) {
                $newData['guardian'] = [
                    'guardianname' => ($request->parent1_first_name ?? '') . ' ' . ($request->parent1_last_name ?? ''),
                    'guardianaddress' => $request->parent1_Address ?? null,
                    'address_line_2' => $request->parent1_Address_line2 ?? null,
                    'city' => $request->parent1_city ?? null,
                    'countyStateRegion' => $request->parent1_country_state_region ?? null,
                    'zIPCode' => $request->parent1_zipCode ?? null,
                    'country' => $request->parent1_country ?? null,
                    'guardiantel' => $request->parent1_email ?? null,
                    'guardianmob' => $request->parent1_mobile ?? null,
                ];
            } else {
                $newData['guardian'] = [];
            }

            // New Kin data
            if ($kin) {
                $newData['kin'] = [
                    'kinname' => ($request->emergency_conatct1_first_name ?? '') . ' ' . ($request->emergency_conatct1_last_name ?? ''),
                    'kinaddress' => $request->emergency_conatct1_Address ?? null,
                    'emergency_conatct1_Address_line2' => $request->emergency_conatct1_Address_line2 ?? null,
                    'emergency_conatct1_city' => $request->emergency_conatct1_city ?? null,
                    'emergency_conatct1_country_state_region' => $request->emergency_conatct1_country_state_region ?? null,
                    'emergency_conatct1_zipCode' => $request->emergency_conatct1_zipCode ?? null,
                    'emergency_conatct1_country' => $request->emergency_conatct1_country ?? null,
                    'kintel' => $request->emergency_conatct1_email ?? null,
                    'kinmob' => $request->emergency_conatct1_mobile ?? null,
                ];
            } else {
                $newData['kin'] = [];
            }

            // New Admission data
            if ($admission) {
                $newData['admission'] = [
                    'formfilingdate' => $request->form_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->form_date))) : null,
                    'joiningdate' => $request->joining_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->joining_date))) : null,
                    'medicalcondition' => $request->medical_condition ?? null,
                    'feedetail' => ($request->has('package_amount') && $request->has('package_weeks') && 
                    !empty($request->package_amount) && !empty($request->package_weeks)) 
                    ? trim($request->package_amount) . ' for ' . strtolower(trim($request->package_weeks))
                    : ($request->fee_detail ?? null),
                    'familystatus' => $request->family_status ?? null,
                    'payment_method' => $request->payment_method ?? null,
                    'add_comment' => $request->add_comment ?? null,
                    'child_name1' => $request->child_name1 ?? null,
                    'school_name1' => $request->school_name1 ?? null,
                    'child_name2' => $request->child_name2 ?? null,
                    'school_name2' => $request->school_name2 ?? null,
                    'child_name3' => $request->child_name3 ?? null,
                    'school_name3' => $request->school_name3 ?? null,
                    'child_name4' => $request->child_name4 ?? null,
                    'school_name4' => $request->school_name4 ?? null,
                    'child_name5' => $request->child_name5 ?? null,
                    'school_name5' => $request->school_name5 ?? null,
                ];
            } else {
                $newData['admission'] = [];
            }

            // New Students data
            $newData['students'] = $request->input('student') ?? [];

            // Track changes using helper function
            AdmissionChangeTracker::trackAdmissionChanges(
                $request->family_id ?? '',
                $oldData,
                $newData,
                $branch_id,
                $branch_name
            );
        } catch (\Exception $e) {
            // Log error but don't break the flow
            Log::error('Error tracking admission changes: ' . $e->getMessage());
        }
        // ========== END: Track changes and log to activity_log ==========

        // ========== START: Post-save de-duplication (same family_id + branch_id + same first/last name) ==========
        // NOTE: As requested, this checks ONLY studentname + studentsur (no DOB check).
        // This can remove legitimately different students who share the exact same first/last name.
        try {
            $familyIdForDedupe = $request->family_id ?? null;
            $branchIdForDedupe = $branch_id ?? session('branch_id');

            if (!empty($familyIdForDedupe) && !empty($branchIdForDedupe)) {
                $familyStudents = Student::where('branch_id', $branchIdForDedupe)
                    ->where('admissionid', $familyIdForDedupe)
                    ->get(['studentid', 'studentname', 'studentsur']);

                $groups = $familyStudents->filter(function ($s) {
                        return trim((string) ($s->studentname ?? '')) !== '' && trim((string) ($s->studentsur ?? '')) !== '';
                    })
                    ->groupBy(function ($s) {
                        $first = mb_strtolower(trim((string) ($s->studentname ?? '')));
                        $last  = mb_strtolower(trim((string) ($s->studentsur ?? '')));
                        return $first . '|' . $last;
                    });

                foreach ($groups as $nameKey => $group) {
                    if ($group->count() <= 1) {
                        continue;
                    }

                    // Keep the oldest (lowest studentid), delete the rest
                    $sorted = $group->sortBy(function ($s) {
                        return (int) $s->studentid;
                    })->values();

                    $keepStudent = $sorted->first();
                    $deleteStudents = $sorted->slice(1);
                    $deleteIds = $deleteStudents->pluck('studentid')->filter()->values();

                    if ($deleteIds->isEmpty()) {
                        continue;
                    }

                    // Clean related medical_condition rows (to avoid orphans)
                    medical_condition::where('branch_id', $branchIdForDedupe)
                        ->whereIn('student_id', $deleteIds->all())
                        ->delete();

                    // Clean duplicate TimeTable rows for this full name (keep 7 days, delete extras)
                    $fullName = trim((string) ($keepStudent->studentname ?? '') . ' ' . (string) ($keepStudent->studentsur ?? ''));
                    if ($fullName !== '') {
                        $tt = TimeTable::where('branch_id', $branchIdForDedupe)
                            ->where('admissionid', $familyIdForDedupe)
                            ->where('studentname', $fullName)
                            ->orderBy('id', 'asc')
                            ->pluck('id');

                        if ($tt->count() > 7) {
                            $ttDelete = $tt->slice(7)->values()->all();
                            if (!empty($ttDelete)) {
                                TimeTable::whereIn('id', $ttDelete)->delete();
                            }
                        }
                    }

                    // Finally delete the duplicate student rows
                    Student::where('branch_id', $branchIdForDedupe)
                        ->where('admissionid', $familyIdForDedupe)
                        ->whereIn('studentid', $deleteIds->all())
                        ->delete();

                    Log::warning('Deduped duplicate students by name within family/branch', [
                        'branch_id' => $branchIdForDedupe,
                        'family_id' => $familyIdForDedupe,
                        'name_key' => $nameKey,
                        'kept_studentid' => $keepStudent->studentid ?? null,
                        'deleted_studentids' => $deleteIds->all(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error during post-save student de-duplication: ' . $e->getMessage());
        }
        // ========== END: Post-save de-duplication ==========

        return redirect('searchStudent')->with('success', 'Admission updated successfully!');
    }

    public function examRegRequest()
    {
        // $studentRequests = StudentRequest::where('branch_id', session('branch_id'))->orderBy('is_approved', 'asc')->get();
        $studentRequests = StudentRequest::where('branch_id', session('branch_id'))->where('is_approved', 0)->whereNull('is_archive')->orderBy('created_at', 'desc')->get();
        $decodedData = [];

        foreach ($studentRequests as $request) {
            $decoded = json_decode($request->base64_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $decodedData[] = $decoded;
            } else {
                $decodedData[] = ['error' => 'Invalid JSON data'];
            }
        }
        // dd($studentRequests);

        return view('branchFrontend.admission.examRegRequest', compact('studentRequests', 'decodedData'));
    }

    public function mockAddResult()
    {
        return view('branchFrontend.mock.index');
    }


    public function getFamilyRec(Request $request)
    {
        $family_id = $request->family_id;

        $students = Student::where('branch_id', session('branch_id'))
        ->where('student_status','active')
            ->whereBetween('studentyearinschool', [10, 13])
            ->where('admissionid', $family_id)
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No students found within year 10–13 for this family ID.',
                'data' => []
            ], 200); // ✅ Always return 200
        }

        $fullNames = $students->map(function ($student) {
            return $student->studentname . ' ' . $student->studentsur;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data loaded successfully!',
            'data' => $fullNames
        ], 200);
    }


    public function storeMockResult(Request $request)
    {

        // dd($request->all());
        if ($request->filled('exam_date')) {
            $request->merge([
                'exam_date' => Carbon::createFromFormat('d/m/Y', $request->exam_date)->format('Y-m-d')
            ]);
        }
        $request->validate([
            'family_id' => 'required',
            'name' => 'required',
            'subject' => 'required',
            'mock_type' => 'required',
            'exam_date' => 'required',
            'percentage' => 'required',
            'fine_grade' => 'required',
            'exam_marked_by' => 'required',
            'updated_by' => 'required',
            'qualification' => 'required',
            'tier' => 'required',
        ]);


        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $mockResult = MockResult::create([
            'family_id' => $request->input('family_id'),
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'mock_type' => $request->input('mock_type'),
            'exam_date' => $request->input('exam_date'),
            'percentage' => $request->input('percentage'),
            'qualifications' => $request->input('qualification'),
            'tier' => $request->input('tier'),
            'fine_grade' => $request->input('fine_grade'),
            'exam_marked_by' => $request->input('exam_marked_by'),
            'updated_by' => $request->input('updated_by'),
            'step_1' => $request->input('step1'),
            'step_2' => $request->input('step2'),
            'step_3' => $request->input('step3'),
            'branch_name' => $branch_name,
            'branch_id' => $branch_id,

        ]);

        return redirect()->back()->with('success', 'Mock result has been successfully saved.');
    }


    public function mockViewResult()
    {
        $mocks = MockResult::where('branch_id', session('branch_id'))->get();

        return view('branchFrontend.mock.viewMock', compact('mocks'));
    }

    public function exportToCSVMock()
    {
        // Retrieve all mock results
        $mocks = MockResult::where('branch_id', session('branch_id'))->get();

        // Define the CSV header
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=mock_results.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Define the CSV content
        $columns = [
            'ID',
            'Family ID',
            'Name',
            'Subject',
            'Mock Type',
            'Exam Date',
            'Percentage',
            'Qualifications',
            'Tier',
            'Fine Grade',
            'Marked By',
            'Updated By',
            'Step 1',
            'Step 2',
            'Step 3',
            // 'Created At',
            // 'Updated At'
        ];

        $callback = function () use ($mocks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($mocks as $mock) {
                fputcsv($file, [
                    $mock->id,
                    $mock->family_id,
                    $mock->name,
                    $mock->subject,
                    $mock->mock_type,
                    $mock->exam_date,
                    $mock->percentage,
                    $mock->qualifications,
                    $mock->tier,
                    $mock->fine_grade,
                    $mock->exam_marked_by,
                    $mock->updated_by,
                    $mock->step_1,
                    $mock->step_2,
                    $mock->step_3,
                    // $mock->created_at,
                    // $mock->updated_at
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function mockDelete($id)
    {
        $mockResult = MockResult::where('branch_id', session('branch_id'))
            ->where('id', $id)
            ->first();

        if (!$mockResult) {
            return redirect()->back()->with('error', 'Mock Result not found.');
        }

        $mockResult->delete();

        return redirect()->back()->with('success', 'Mock Result deleted successfully.');
    }

    public function editMock($id)
    {
        $mock = MockResult::where('branch_id', session('branch_id'))->where('id', $id)->first();

        return view('branchFrontend.mock.editMock', compact('mock'));
    }


    public function updatemock(Request $request)
    {
        // dd($request->all());
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $mockResult = MockResult::where('branch_id', session('branch_id'))
            ->findOrFail($request->id);

        $mockResult->family_id = $request->family_id;
        $mockResult->name = $request->name;
        $mockResult->subject = $request->subject;
        $mockResult->mock_type = $request->mock_type;
        $mockResult->exam_date = $request->exam_date;
        $mockResult->percentage = $request->percentage;

        $mockResult->qualifications = $request->qualification;
        $mockResult->tier = $request->tier;

        $mockResult->fine_grade = $request->fine_grade;
        $mockResult->exam_marked_by = $request->exam_marked_by;
        $mockResult->updated_by = $request->updated_by;
        $mockResult->step_1 = $request->step1;
        $mockResult->step_2 = $request->step2;
        $mockResult->step_3 = $request->step3;

        $mockResult->branch_id = $branch_id;
        $mockResult->branch_name = $branch_name;



        $mockResult->save();

        return redirect()->route('mock.view.result')->with('success', 'Mock result updated successfully!');
    }


    public function mockTestReport(Request $request)
    {
        // dd($request->all());
        if (!empty($request->input('family_id')) && !empty($request->input('name'))) {
            // Get the request data
            $family_id = $request->input('family_id');
            $name = $request->input('name');

            // Initialize the query
            $query = MockResult::query();

            // Apply filters if both 'family_id' and 'name' are not empty
            $results = $query->where('branch_id', session('branch_id'))->where('family_id', $family_id)
                ->where('name', 'like', '%' . $name . '%')
                ->get();
            $student_detail = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->first();
            $student_email =  Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $student_detail->guardianid)->first();
            $student_email = $student_email->guardiantel;
            // dd($student_email);
            // Return the results to the view

            return view('branchFrontend.mock.report', compact('results', 'name', 'family_id', 'student_email'));
        } else {
            // If no filters are applied, return null for results
            $results = null;
            $name = null;
            $family_id = null;
            $student_email = null;

            return view('branchFrontend.mock.report', compact('results', 'name', 'family_id', 'student_email'));
        }
    }

    public function sendMockEmail(Request $request)
    {
        $family_id = $request->input('family_id');
        $name = $request->name;

        $results = MockResult::where('branch_id', session('branch_id'))
            ->where('family_id', $family_id)
            ->where('name', 'like', '%' . $name . '%')
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'No results found'], 404);
        }

        // Generate PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        $pageWidth = $pdf->GetPageWidth();
        $logoWidth = 50 * 0.7;
        $logoHeight = 35 * 0.7;
        $logoX = ($pageWidth - $logoWidth) / 2;
        // $pdf->Image('https://frobelschoolsystemnew.frobel.co.uk/img/datesheetLogo.png', $logoX, 8, $logoWidth, $logoHeight);
        $pdf->Image(public_path('img/datesheetLogo.png'), $logoX, 8, $logoWidth, $logoHeight);
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'B', 16 * 0.7);
        $pdf->Cell(0, 6, 'Mock Exam Result', 0, 1, 'C');
        $pdf->Ln(3);

        $pdf->SetFont('Arial', '', 12 * 0.7);
        $pdf->SetX(10);
        $pdf->MultiCell(0, 6, "Thank you for attending your Mock exam/s with Frobel Education.
When students sit their Mock exams, it is not unusual for them to achieve a lower grade than that which they are able to achieve in the classroom, with their tutor and without the pressures that formal exams inevitably bring. Students must become good at their subject knowledge and academic skills, but they also need to be good at exam skills, and this sometimes needs significant practise.
In the grid below, you will see your Overall Mock grade for each subject.", 0, 'L');
        $pdf->Ln(3);

        $pdf->SetX(10);
        $pdf->Cell(0, 6, 'Student Family ID: ' . $family_id, 0, 1);
        $pdf->Cell(0, 6, 'Student Name: ' . $results->first()->name, 0, 1);
        $pdf->Cell(0, 6, 'Qualification: ' . ($results->first()->qualifications ?? 'N/A'), 0, 1);
        $pdf->Cell(0, 6, 'Report Type: Mock Exam Results', 0, 1);
        $pdf->Ln(3);

        $pdf->SetFont('Arial', '', 10 * 0.7);
        $pdf->SetFillColor(242, 242, 242);

        // Table headers
        $pdf->Cell(60, 6, 'Subject', 1, 0, 'C', true);
        $pdf->Cell(60, 6, 'Percentage', 1, 0, 'C', true);
        $pdf->Cell(60, 6, 'Grade', 1, 1, 'C', true);

        // Table rows
        foreach ($results as $result) {
            $pdf->Cell(60, 6, $result->subject, 1, 0, 'C');
            $pdf->Cell(60, 6, ($result->percentage ?? 'N/A') . '%', 1, 0, 'C');
            $pdf->Cell(60, 6, ($result->fine_grade ?? 'N/A'), 1, 1, 'C');
        }

        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 12 * 0.7);
        $pdf->Cell(0, 6, 'Steps Content:', 0, 1);
        $pdf->SetFont('Arial', '', 12 * 0.7);

        $pdf->SetX(10);
        $pdf->MultiCell(0, 6, 'Step 1: ' . ($results->first()->step_1 ?? 'N/A'), 0, 'L');
        $pdf->MultiCell(0, 6, 'Step 2: ' . ($results->first()->step_2 ?? 'N/A'), 0, 'L');
        $pdf->MultiCell(0, 6, 'Step 3: ' . ($results->first()->step_3 ?? 'N/A'), 0, 'L');
        $pdf->Ln(3);

        $pdf->MultiCell(0, 6, "On average, Frobel GCSE students attend 63 hours of tuition to progress one full GCSE grade.", 0, 'C');
        $pdf->MultiCell(0, 6, "*Progress rates may vary based on the learning attitude and ability of the individual student.", 0, 'C');

        $pdfPath = storage_path('app/public/mock_result_' . $family_id . '.pdf');
        $pdf->Output('F', $pdfPath);

        // Email HTML body
        $emailContent = "
        Hi,<br><br>
        Please find attached mock result of <strong>{$results->first()->name}</strong>.<br><br>
        Best Regards,<br>
        Frobel Exam Team<br>
        <a href='mailto:exams@frobel.co.uk'>exams@frobel.co.uk</a>
    ";

        // Send the email
        Mail::send([], [], function ($message) use ($emailContent, $pdfPath, $request) {
            $message->to($request->input('email'))
                ->subject('Mock Exam Result Card')
                ->html($emailContent) // ✅ Use html() instead of setBody()
                ->from('no-reply@frobel.co.uk', 'Frobel Education')
                ->attach($pdfPath);
        });

        // Delete the PDF after sending
        unlink($pdfPath);

        return response()->json(['message' => 'Mock result email sent successfully!']);
    }


    public function attendance()
    {
        $subjects = Subject::where('branch_id', session('branch_id'))->select('id', 'name')->get();

        if ($subjects->isEmpty()) {
            return redirect()->route('addSubjects')->with('error', 'Please add subjects first to proceed with attendance.');
        }

        return view('branchFrontend.attendance.index', compact('subjects'));
    }

    public function addSubjects()
    {
        $subjects = Subject::where('branch_id', session('branch_id'))->get();
        return view('branchFrontend.subejcts.index', compact('subjects'));
    }

    public function storesubejct(Request $request)
    {
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Subject::create([
            'name' => $validated['name'],
            'branch_id' => $branch->branch_id ?? $branch->branch_id,
            'branch_name' => $branch->branch_name ?? $branch->branch_name,
        ]);

        return redirect()->back()->with('success', 'Subject added successfully.');
    }

    public function updatesubject(Request $request)
    {
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        $validated = $request->validate([
            'id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $subject = Subject::findOrFail($request->id);
        $subject->name = $validated['name'];
        $subject->branch_id = $branch->branch_id ?? $branch->branch_id;
        $subject->branch_name = $branch->branch_name ?? $branch->branch_name;
        $subject->save();

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    public function destroysubject($id)
    {
        $subject = Subject::where('branch_id', session('branch_id'))->findOrFail($id);
        $subject->delete();
        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }


    public function findStudentsView(Request $request)
    {
        $family_id = $request->family_id;
        $students = Student::where('branch_id', session('branch_id'))
            ->where('admissionid', $family_id)
            ->where(function ($query) {
                // $query->where('student_status', '!=', 'inactive')
                //     ->orWhereNull('student_status');
            })
            ->get();
        // dd($students);
        $student_ids = array();

        foreach ($students as $student) {
            $student_ids[] = $student->admissionid;
        }

        if (count($student_ids) > 0) {
            // $students = Student::where('branch_id', session('branch_id'))->whereIn('admissionid', $student_ids)->where('student_status', 'active')->get();
            $students = Student::where('branch_id', session('branch_id'))
                ->whereIn('admissionid', $student_ids)
                ->where(function ($query) {
                    // $query->where('student_status', '!=', 'inactive')
                    //     ->orWhereNull('student_status');
                })
                ->get();
            return response()->json($students);
        } else {
            return response()->json([
                'error' => 'No record found corresponding to this family.'
            ], 404);
        }
    }

    public function findStudents(Request $request)
    {

        $family_id = $request->family_id;
        $students = Student::where('branch_id', session('branch_id'))
            ->where('admissionid', $family_id)
            ->where(function ($query) {
                $query->where('student_status', '!=', 'inactive')
                    ->orWhereNull('student_status');
            })
            ->get();
        // dd($students);
        $student_ids = array();

        foreach ($students as $student) {
            $student_ids[] = $student->admissionid;
        }

        if (count($student_ids) > 0) {
            // $students = Student::where('branch_id', session('branch_id'))->whereIn('admissionid', $student_ids)->where('student_status', 'active')->get();
            $students = Student::where('branch_id', session('branch_id'))
                ->whereIn('admissionid', $student_ids)
                ->where(function ($query) {
                    $query->where('student_status', '!=', 'inactive')
                        ->orWhereNull('student_status');
                })
                ->get();
            return response()->json($students);
        } else {
            return response()->json([
                'error' => 'No record found corresponding to this family.'
            ], 404);
        }
    }

    public function findSubjects(Request $request)
    {
        // dd($request->all());
        $student_name = $request->student_name;
        $subjects = Subject::where('branch_id', session('branch_id'))->get();

        return $subjects;
    }

    public function getTeacherName($name)
    {
        $teachers = DB::table('teachers_subject')->where('is_available', 'yes')->where('branch_id', session('branch_id'))->where('subject', $name)->distinct()->pluck('teacher_name');
        return response()->json($teachers);
    }


    public function teacherRoster()
    {
        $subjects = Subject::where('branch_id', session('branch_id'))->get();
        $rosters = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', session('branch_id'))
            ->get()
            ->groupBy('teacher_name')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'id' => $first->id,
                    'teacher_name' => $first->teacher_name,
                    'subject' => $group->pluck('subject')->toArray(),
                    'joining_date' => $first->joining_date,
                    'branch_id' => $first->branch_id,
                    'branch_name' => $first->branch_name,
                ];
            })->values();

        return view('branchFrontend.staff_management.index', compact('subjects', 'rosters'));
    }

    public function teacherRosterStore(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name' => 'required|string|max:50',
            'joining_date' => 'required|date',

        ];

        $messages = [
            // 'subjects.*.unique' => 'The combination of teacher name and subject already exists.',
        ];

        $request->validate($rules, $messages);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found.');
        }
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        // Process subjects - split comma-separated strings if needed
        $subjects = [];
        foreach ($request->subjects as $subjectGroup) {
            $splitSubjects = explode(',', $subjectGroup);
            foreach ($splitSubjects as $subject) {
                $subject = trim($subject);
                if (!empty($subject)) {
                    $subjects[] = $subject;
                }
            }
        }

        DB::beginTransaction();
        try {
            $insertedSubjects = [];
            foreach ($subjects as $subject) {
                $exists = DB::table('teachers_subject')
                    ->where('branch_id', $branch_id)
                    ->where('teacher_name', $request->name)
                    ->where('subject', $subject)
                    ->exists();

                if ($exists) {
                    throw new \Exception('The combination of teacher name and subject "' . $subject . '" already exists.');
                }

                DB::table('teachers_subject')->insert([
                    'teacher_name' => $request->name,
                    'subject' => $subject,
                    'joining_date' => $request->joining_date,
                    'is_available' => 'yes',
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                ]);

                $insertedSubjects[] = $subject;
            }

            // Manually log activity for teacher roster creation
            activity('TeacherRoster')
                ->causedBy(auth()->user())
                ->withProperties([
                    'teacher_name' => $request->name,
                    'subjects' => $insertedSubjects,
                    'joining_date' => $request->joining_date,
                    'subjects_count' => count($insertedSubjects),
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                ])
                ->log("Teacher roster created for {$request->name} with " . count($insertedSubjects) . " subject(s)");

            // Also set branch_id and branch_name on the activity log entry
            $latestActivity = \Spatie\Activitylog\Models\Activity::latest()->first();
            if ($latestActivity) {
                $latestActivity->branch_id = $branch_id;
                $latestActivity->branch_name = $branch_name;
                $latestActivity->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Teacher roster added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function searchAttendance(Request $request)
    {

        $time = $request->time;
        $date = date('Y-m-d', strtotime($request->date));
        $teacher = $request->teacher;
        $subject = $request->subject;
        // $student = Student::where('branch_id', session('branch_id'))->where('studentid', $request->student_name)->where('admissionid', $request->stu_idd)->first();
        $student = Student::where('branch_id', session('branch_id'))
            ->where(function ($query) use ($request) {
                $query->where('studentname', $request->student_name)
                    ->orWhereRaw("CONCAT(studentname, ' ', studentsur) = ?", [$request->student_name]);
            })
            ->where('admissionid', $request->stu_idd)
            ->first();
        // dd($student,$request->student_name,$request->stu_idd);
        // dd($student,session('branch_id'),$request->student_name,$request->stu_idd);
        $existingAttendance = Attendance::where([
            'family_id' => $request->stu_idd,
            'student_name' => '%' . $student->studentname . '%', // Adjust to match your needs
            'teacher_name' => $teacher,
            'subject' => $subject,
            'date' => $date,
            'time_slot' => $time,
            'branch_id' => session('branch_id'),
        ])->first();


        if ($existingAttendance) {
            return response()->json(['message' => 'Attendance entry already exists'], 203);
        }


        if ($student) {

            $years_in_school = $student->studentyearinschool;

            if ($request->stu_idd == 3184) {
                $timeTable = TimeTable::where('branch_id', session('branch_id'))->where('studentname', 'like', '%' . $student->studentname . ' ' . $student->studentsur . '%')
                    ->where('admissionid', $request->stu_idd)
                    ->first();
            } else {
                $timeTable = TimeTable::where('branch_id', session('branch_id'))->where('studentname', 'like', '%' . $student->studentname . '%')
                    ->where('admissionid', $request->stu_idd)
                    ->first();
            }
        }
        if (!$timeTable) {

            $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
            $branch_id = $branch->branch_id;
            $branch_name = $branch->branch_name;

            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            foreach ($daysOfWeek as $day) {
                $timeTable =  TimeTable::create([
                    'studentname' => $student->studentname,
                    'admissionid' => $request->stu_idd,
                    'day' => strtoupper($day),
                    'branch_name' => $branch_name,
                    'branch_id' => $branch_id,

                ]);
            }
        }
        return response()->json([
            'timetable' => $timeTable,
            'years_in_school' => $years_in_school
        ], 201);
    }

    public function teacherRosterUpdate(Request $request)
    {
        // dd($request->all());
        $rules = [
            'id' => 'required|integer',
            // 'name' => 'required|string|max:50',
            'joining_date' => 'required|date',

        ];

        $messages = [
            // 'subjects.*.exists' => 'One or more selected subjects are invalid.',
        ];

        $request->validate($rules, $messages);

        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found.');
        }

        DB::beginTransaction();
        try {
            // Get the original roster entry
            $originalRoster = DB::table('teachers_subject')
                ->where('branch_id', session('branch_id'))
                ->where('id', $request->id)
                ->first();

            if (!$originalRoster) {
                throw new \Exception('Roster entry not found.');
            }

            // Process subjects - split comma-separated strings if needed
            $subjects = [];
            foreach ($request->subjects as $subjectGroup) {
                $splitSubjects = explode(',', $subjectGroup);
                foreach ($splitSubjects as $subject) {
                    $subject = trim($subject);
                    if (!empty($subject)) {
                        $subjects[] = $subject;
                    }
                }
            }

            // Get current subjects for this teacher
            $currentSubjects = DB::table('teachers_subject')
                ->where('branch_id', session('branch_id'))
                ->where('teacher_name', $originalRoster->teacher_name)
                ->pluck('subject')
                ->toArray();

            // Identify subjects to delete (in current but not in new)
            $subjectsToDelete = array_diff($currentSubjects, $subjects);
            // Identify subjects to add (in new but not in current)
            $subjectsToAdd = array_diff($subjects, $currentSubjects);

            // Delete removed subjects
            if (!empty($subjectsToDelete)) {
                DB::table('teachers_subject')
                    ->where('branch_id', session('branch_id'))
                    ->where('teacher_name', $originalRoster->teacher_name)
                    ->whereIn('subject', $subjectsToDelete)
                    ->delete();
            }

            // Add new subjects
            foreach ($subjectsToAdd as $subject) {
                $exists = DB::table('teachers_subject')
                    ->where('branch_id', $branch->branch_id)
                    ->where('teacher_name', $request->name)
                    ->where('subject', $subject)
                    ->exists();

                if ($exists) {
                    throw new \Exception('The combination of teacher name and subject "' . $subject . '" already exists.');
                }

                DB::table('teachers_subject')->insert([
                    'teacher_name' => $request->name,
                    'subject' => $subject,
                    'joining_date' => $request->joining_date,
                    'is_available' => 'yes',
                    'branch_id' => $branch->branch_id,
                    'branch_name' => $branch->branch_name,
                ]);
            }

            // Update teacher_name and joining_date for all remaining subjects
            DB::table('teachers_subject')
                ->where('branch_id', session('branch_id'))
                ->where('teacher_name', $originalRoster->teacher_name)
                ->update([
                    'teacher_name' => $request->name,
                    'joining_date' => $request->joining_date,
                ]);

            // Manually log activity for teacher roster update
            activity('TeacherRoster')
                ->causedBy(auth()->user())
                ->withProperties([
                    'teacher_name' => $request->name,
                    'original_teacher_name' => $originalRoster->teacher_name,
                    'subjects_added' => array_values($subjectsToAdd),
                    'subjects_deleted' => array_values($subjectsToDelete),
                    'subjects_final' => $subjects,
                    'joining_date' => $request->joining_date,
                    'previous_joining_date' => $originalRoster->joining_date,
                    'branch_id' => $branch->branch_id,
                    'branch_name' => $branch->branch_name,
                ])
                ->log("Teacher roster updated for {$request->name}");

            // Also set branch_id and branch_name on the activity log entry
            $latestActivity = \Spatie\Activitylog\Models\Activity::latest()->first();
            if ($latestActivity) {
                $latestActivity->branch_id = $branch->branch_id;
                $latestActivity->branch_name = $branch->branch_name;
                $latestActivity->save();
            }

            DB::commit();
            return redirect()->route('teacherRoster')->with('success', 'Staff member updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
    public function teacherRosterDestroy($id)
    {
        $roster = DB::table('teachers_subject')
            ->where('branch_id', session('branch_id'))
            ->where('id', $id)
            ->first();

        if (!$roster) {
            return redirect()->back()->with('error', 'Roster entry not found.');
        }

        $deleted = DB::table('teachers_subject')
            ->where('branch_id', session('branch_id'))
            ->where('teacher_name', $roster->teacher_name)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Teacher roster deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete roster entry.');
        }
    }

    public function storeAttendance(Request $request)
    {
        // dd($request->all());
        $date = date('Y-m-d', strtotime($request->date));

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $existingAttendance = Attendance::where('branch_id', session('branch_id'))->where('family_id', $request->family_id)
            ->where('teacher_name', $request->teacher)
            ->where('subject', $request->subject)
            ->where('date', $date)
            ->where('time_slot', $request->time)
            ->where('student_name', 'like', '%' . $request->student_name . '%')
            ->where('branch_name', $branch_name)
            ->where('branch_id', $branch_id)
            ->first();


        if ($existingAttendance) {
            return response()->json(['message' => 'Attendance entry already exists'], 203);
        }





        if ($request->has('family_id') && !empty($request->family_id)) {
            $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
            $branch_id = $branch->branch_id;
            $branch_name = $branch->branch_name;

            // ✅ FIX: Always use studentdata.studentyearinschool as source of truth
            // Get student record to fetch current year
            $studentRecord = Student::where('admissionid', $request->family_id)
                ->whereRaw('LOWER(CONCAT(studentname, " ", COALESCE(studentsur, ""))) LIKE ?', ['%' . strtolower($request->student_name) . '%'])
                ->where('branch_id', session('branch_id'))
                ->first();
            
            // Use studentdata year first, fallback to request if student not found
            $yearInSchool = $studentRecord ? ($studentRecord->studentyearinschool ?? null) : ($request->years_in_school ?? null);

            $att = Attendance::create([
                'family_id' => $request->family_id,
                'student_name' => $request->student_name,
                'teacher_name' => $request->teacher,
                'subject' => $request->subject,
                'time_slot' => $request->time,
                'session_1' => $request->session,
                'student_year_in_school' => $yearInSchool,
                'date' => $date,
                'bk_ch' => $request->bk_ch,
                'status' => $request->status == 'completed' ? 'on' : '',
                'branch_id' => $branch->branch_id ?? $branch->branch_id,
                'branch_name' => $branch->branch_name ?? $branch->branch_name,
            ]);
        }

        return response()->json(['message' => 'Attendance entry created successfully'], 200);


        return response()->json('Success', 201);
    }



    public function viewAttendance(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        if ($request->ajax()) {
            $query = Attendance::query()
                ->where('branch_id', session('branch_id'))
                ->select([
                    'id',
                    'family_id',
                    'student_name',
                    'student_year_in_school as year',
                    'teacher_name',
                    'subject',
                    'time_slot',
                    'bk_ch',
                    'session_1',
                    'date',
                    'additional_info',
                    'adjustment'
                ])
                ->whereHas('student', function ($query) {
                    $query->where('student_status', '!=', 'inactive');
                })
                ->with('student'); // eager load student

            if ($fromDate && $toDate) {
                $query->whereBetween('date', [$fromDate, $toDate]);
            }

            $attendances = $query->orderBy('date', 'desc');

            return Datatables::of($attendances)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return $row->date ?? '';
                })
                ->addColumn('start_date', function ($row) {
                    // Make sure start_date is fetched from student table
                    return optional($row->student)->start_date ?? '';
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . url('admin/attendance/edit/' . $row->id) . '" class="edit btn btn-primary btn-sm">Edit</a>';
                    $deleteBtn = '<a href="' . url('attendance/delete/' . $row->id) . '" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $subjects = Subject::where('branch_id', session('branch_id'))->get();

        // Ensure start_date is set for all students in the current branch
$branchId = session('branch_id');

DB::statement("
    UPDATE studentdata sd
    JOIN (
        SELECT
            family_id,
            student_name,
            MIN(date) AS first_date
        FROM attendance
        WHERE branch_id = ?
        GROUP BY family_id, student_name
    ) att
    ON sd.admissionid = att.family_id
    AND CONCAT(sd.studentname, ' ', sd.studentsur) = att.student_name
    SET sd.start_date = att.first_date
    WHERE sd.branch_id = ?
", [$branchId, $branchId]);



        return view('branchFrontend.attendance.viewAttendance', compact('subjects'));
    }




    public function viewzView(Request $request)
    {
       // dd($request->all());
        ini_set('memory_limit', '99G'); // 99 Gigabytes
        ini_set('max_execution_time', '600000000000000'); // 600 seconds (10 minutes)

        $family_id = $family_id = $request->input('family_id'); //$request->columns[0]['search']['value'] ?? $request->family_id;
        $selectedValue = $request->input('selectedValue', 'Choose name');
        $fromDate = $request->input('from_date') ? Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d') : null;
        $toDate = $request->input('to_date') ? Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d') : null;
        $subject = $request->input('subject', 'Select subject');
        $timeSlot = preg_replace('/[a-zA-Z]+/', '', $request->input('timeSlot', ''));
        // Handle special case for Muhammad
        if ($selectedValue === 'Muhammad' && $family_id == 3184) {
            $selectedValue = 'Muhammad Ahmed';
        }
        //  dd($request->ssall(),$family_id);

        // Initialize query
        // $query = Attendance::query()
        //     ->where('branch_id', session('branch_id'))->select([
        //         'id',
        //         'family_id',
        //         'student_name',
        //         'student_year_in_school as year',
        //         'teacher_name',
        //         'subject',
        //         'time_slot',
        //         'bk_ch',
        //         'session_1',
        //         'date',
        //         'additional_info',
        //         'adjustment',
        //     ])

        //     // ->where(function ($query) {
        //     //     $query->whereHas('student', function ($subQuery) {
        //     //         $subQuery->where('student_status', '!=', 'inactive')
        //     //             ->orWhereNull('student_status');
        //     //     })->orWhereDoesntHave('student');
        //     // });
        //     ->whereHas('student', function ($query) {
        //         $query->whereIn('student_status', ['active', '', 'inactive']); // Include active, empty, or NULL statuses
        //     });
        $query = Attendance::query()
            ->select([
                'attendance.id',
                'attendance.family_id',
                'attendance.student_name',
                'attendance.student_year_in_school as year',
                'attendance.teacher_name',
                'attendance.subject',
                'attendance.time_slot',
                'attendance.bk_ch',
                'attendance.session_1',
                'attendance.date',
                'attendance.additional_info',
                'attendance.adjustment',
                'studentdata.start_date',
            ])
            ->join('studentdata', function ($join) {
                $join->on('attendance.family_id', '=', 'studentdata.admissionid')
                    ->whereRaw('attendance.student_name = CONCAT(studentdata.studentname, " ", studentdata.studentsur)')
                    ->where('studentdata.branch_id', '=', session('branch_id'));
            })
            ->where('attendance.branch_id', '=', session('branch_id'));
            // ->get();
        //  dd($query->count());

        // Apply filters conditionally
        if (isset($family_id) && is_numeric($family_id)) {
            // dd('1');
            $query->where('family_id', $family_id);
        }

        if ($selectedValue !== 'Choose name') {
            // dd('2');
            $selectedValue = preg_replace('/^[\p{Z}\s]+/u', '', $selectedValue);
            $query->whereRaw('LOWER(LTRIM(student_name)) LIKE ?', ['%' . strtolower($selectedValue) . '%']);
        }
        // dd($fromDate,$toDate);
        if (strpos($fromDate, '-') !== false && strpos($toDate, '-') !== false) {
            // dd('3');
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        if ($subject !== 'Select subject' && $subject !== 'null' && !empty($subject)) {
            // dd('4');
            // $query->whereRaw('LOWER(subject) LIKE ?', ['%' . strtolower($subject) . '%']);
            $query->whereRaw('LOWER(subject) = ?', [strtolower($subject)]);
        }

        if (strpos($timeSlot, ':') !== false) {
            // dd('5');
            $query->whereRaw("REPLACE(REPLACE(time_slot, 'am', ''), 'pm', '') = ?", [$timeSlot]);
        }

        // Execute query
        $attendances = $query;//->get();
        // dd($attendances,$family_id,$selectedValue,$fromDate,$toDate,$subject,$timeSlot);

        // Return DataTables response
        return DataTables::of($attendances)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row->date ?? '';
            })
            ->addColumn('start_date', function ($row) {
               return $row->start_date ?? '';
            })

            ->addColumn('action', function ($row) {
                $editUrl = url('admin/attendance/edit/' . $row->id);
                $deleteUrl = url('attendance/delete/' . $row->id);
                return <<<HTML
                    <a href="{$editUrl}" data-toggle="tooltip" data-id="{$row->id}" data-original-title="Edit" class="edit btn btn-primary btn-sm editAttendance">Edit</a>
                    <a href="{$deleteUrl}" class="delete btn btn-danger btn-sm deleteAttendance">Delete</a>
                HTML;
            })

            ->make(true);
    }




    public function viewz(Request $request)
    {
        // dd($request->all());
        ini_set('memory_limit', '99G'); // 99 Gigabytes
        ini_set('max_execution_time', '600000000000000'); // 600 seconds (10 minutes)

        $family_id = $family_id = $request->input('family_id'); //$request->columns[0]['search']['value'] ?? $request->family_id;
        $selectedValue = $request->input('selectedValue', 'Choose name');
        $fromDate = $request->input('from_date') ? Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d') : null;
        $toDate = $request->input('to_date') ? Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d') : null;
        $subject = $request->input('subject', 'Select subject');
        $timeSlot = preg_replace('/[a-zA-Z]+/', '', $request->input('timeSlot', ''));
        // Handle special case for Muhammad
        if ($selectedValue === 'Muhammad' && $family_id == 3184) {
            $selectedValue = 'Muhammad Ahmed';
        }
        //  dd($request->ssall(),$family_id);

        // Initialize query
        $query = Attendance::query()
            ->where('branch_id', session('branch_id'))->select([
                'id',
                'family_id',
                'student_name',
                'student_year_in_school as year',
                'teacher_name',
                'subject',
                'time_slot',
                'bk_ch',
                'session_1',
                'date',
                'additional_info',
                'adjustment'
            ])

            // ->where(function ($query) {
            //     $query->whereHas('student', function ($subQuery) {
            //         $subQuery->where('student_status', '!=', 'inactive')
            //             ->orWhereNull('student_status');
            //     })->orWhereDoesntHave('student');
            // });
            ->whereHas('student', function ($query) {
                $query->whereIn('student_status', ['active', '', null]); // Include active, empty, or NULL statuses
            });
        //  dd($query->count());

        // Apply filters conditionally
        if (isset($family_id) && is_numeric($family_id)) {
            // dd('1');
            $query->where('family_id', $family_id);
        }

        if ($selectedValue !== 'Choose name') {
            // dd('2');
            $selectedValue = preg_replace('/^[\p{Z}\s]+/u', '', $selectedValue);
            $query->whereRaw('LOWER(LTRIM(student_name)) LIKE ?', ['%' . strtolower($selectedValue) . '%']);
        }
        // dd($fromDate,$toDate);
        if (strpos($fromDate, '-') !== false && strpos($toDate, '-') !== false) {
            // dd('3');
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        if ($subject !== 'Select subject' && $subject !== 'null' && !empty($subject)) {
            // dd('4');
            $query->whereRaw('LOWER(subject) LIKE ?', ['%' . strtolower($subject) . '%']);
        }

        if (strpos($timeSlot, ':') !== false) {
            // dd('5');
            $query->whereRaw("REPLACE(REPLACE(time_slot, 'am', ''), 'pm', '') = ?", [$timeSlot]);
        }

        // Execute query
        $attendances = $query->get();
        // dd($attendances,$family_id,$selectedValue,$fromDate,$toDate,$subject,$timeSlot);

        // Return DataTables response
        return DataTables::of($attendances)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return $row->date ?? '';
            })
            ->addColumn('action', function ($row) {
                $editUrl = url('admin/attendance/edit/' . $row->id);
                $deleteUrl = url('attendance/delete/' . $row->id);
                return <<<HTML
                    <a href="{$editUrl}" data-toggle="tooltip" data-id="{$row->id}" data-original-title="Edit" class="edit btn btn-primary btn-sm editAttendance">Edit</a>
                    <a href="{$deleteUrl}" class="delete btn btn-danger btn-sm deleteAttendance">Delete</a>
                HTML;
            })
            ->make(true);
    }



    public function attendancesview(Request $request)
    {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $attendances = array();
        if ($request->ajax()) {
            $query = Attendance::query();
            $query->where('branch_id', session('branch_id'))
                ->select('id', 'family_id', 'student_name', 'student_year_in_school as year', 'teacher_name', 'subject', 'time_slot', 'bk_ch', 'session_1', 'date')
                ->whereHas('student', function ($query) {
                    $query->whereIn('student_status', ['active', '', null]); // Include active, empty, or NULL statuses
                })
                ->with('student'); // Eager load the student relationship
            $attendances = $query->orderBy('date', 'desc');
            return Datatables::of($attendances)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    if (!$row->date) {
                        return '';
                    } else {
                        $date = $row->date;
                        return $date;
                    }
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<a href="' . url('attendance/edit/' . $row->id) . '" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editAttendance">Edit</a>';
                    $deleteBtn = '<a href="' . url('attendance/delete/' . $row->id) . '" class="delete btn btn-danger btn-sm deleteAttendance">Delete</a>';
                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $subjects = Subject::where('branch_id', session('branch_id'))->get();

        return view('branchFrontend.attendance.viewAttendance', compact('attendances', 'subjects'));
    }


    public function deleteAtten($id)
    {

        try {


            $attendance = Attendance::where('branch_id', session('branch_id'))
                ->findOrFail($id);

            $attendance->delete();

            return redirect()->back()->with('success', 'Record deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting record: ' . $e->getMessage());
        }
    }



    // public function exportAttenView(Request $request)
    // {
    //     // Extract and validate inputs
    //     $family_id = $request->input('familyId');
    //     $selectedValue = $request->input('studentName', '');
    //     $fromDateInput = $request->input('fromDate');
    //     $toDateInput = $request->input('toDate');
    //     $subject = $request->input('subject', '');
    //     $timeSlot = preg_replace('/[a-zA-Z]+/', '', $request->input('timeSlot', ''));

    //     // Initialize variables for parsed dates
    //     $fromDate = null;
    //     $toDate = null;

    //     // Validate and parse fromDate
    //     if (!empty($fromDateInput)) {
    //         try {
    //             $fromDate = Carbon::createFromFormat('d/m/Y', $fromDateInput)->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             return response()->json(['error' => 'Invalid fromDate format. Please use dd/mm/yyyy.'], 400);
    //         }
    //     }

    //     // Validate and parse toDate
    //     if (!empty($toDateInput)) {
    //         try {
    //             $toDate = Carbon::createFromFormat('d/m/Y', $toDateInput)->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             return response()->json(['error' => 'Invalid toDate format. Please use dd/mm/yyyy.'], 400);
    //         }
    //     }

    //     // Initialize query
    //     $query = Attendance::query()
    //         ->where('branch_id', session('branch_id'))
    //         ->select([
    //             'id',
    //             'family_id',
    //             'student_name',
    //             'student_year_in_school as year',
    //             'teacher_name',
    //             'subject',
    //             'time_slot',
    //             'bk_ch',
    //             'session_1',
    //             'date',
    //             'adjustment',
    //         ])
    //         ->orderBy('family_id', 'asc');

    //     // Apply filters conditionally
    //     if (!empty($family_id) && is_numeric($family_id)) {
    //         $query->where('family_id', $family_id);
    //     }

    //     if (!empty($selectedValue)) {
    //         $selectedValue = preg_replace('/^[\p{Z}\s]+/u', '', $selectedValue);
    //         $query->whereRaw('LOWER(LTRIM(student_name)) LIKE ?', ['%' . strtolower($selectedValue) . '%']);
    //     }

    //     if (!empty($fromDate) && !empty($toDate)) {
    //         $query->whereBetween('date', [$fromDate, $toDate]);
    //     }

    //     if (!empty($subject) && $subject !== 'null') {
    //         $query->whereRaw('LOWER(subject) LIKE ?', ['%' . strtolower($subject) . '%']);
    //     }

    //     if (!empty($timeSlot) && strpos($timeSlot, ':') !== false) {
    //         $query->whereRaw("REPLACE(REPLACE(time_slot, 'am', ''), 'pm', '') = ?", [$timeSlot]);
    //     }

    //     // Execute query
    //     $attendances = $query->get();

    //     // Generate CSV content
    //     $filename = 'attendance_' . date('Ymd_His') . '.csv';
    //     // Updated header to include adjustment
    //     $csvContent = "id,family_id,student_name,year,teacher_name,subject,time_slot,bk_ch,session_1,date,adjustment\n";

    //     foreach ($attendances as $attendance) {
    //         // Escape CSV fields to handle commas and quotes, include adjustment
    //         $fields = [
    //             $attendance->id,
    //             $attendance->family_id,
    //             '"' . str_replace('"', '""', $attendance->student_name) . '"',
    //             $attendance->year,
    //             '"' . str_replace('"', '""', $attendance->teacher_name) . '"',
    //             '"' . str_replace('"', '""', $attendance->subject) . '"',
    //             $attendance->time_slot,
    //             $attendance->bk_ch,
    //             $attendance->session_1,
    //             $attendance->date,
    //             '"' . str_replace('"', '""', $attendance->adjustment) . '"', // Added adjustment
    //         ];
    //         $csvContent .= implode(',', $fields) . "\n";
    //     }

    //     // Set response headers
    //     return response($csvContent)
    //         ->header('Content-Type', 'text/csv')
    //         ->header('Content-Disposition', "attachment; filename=\"$filename\"")
    //         ->header('Cache-Control', 'max-age=0');
    // }

    public function exportAttenView(Request $request)
{
    // dd($request->all());
    $family_id = $request->input('familyId');
    $selectedValue = $request->input('studentName', '');
    $fromDateInput = $request->input('fromDate');
    $toDateInput = $request->input('toDate');
    $subject = $request->input('subject', '');
    $timeSlot = preg_replace('/[a-zA-Z]+/', '', $request->input('timeSlot', ''));

    $fromDate = $fromDateInput ? Carbon::createFromFormat('d/m/Y', $fromDateInput)->format('Y-m-d') : null;
    $toDate = $toDateInput ? Carbon::createFromFormat('d/m/Y', $toDateInput)->format('Y-m-d') : null;
    // dd($fromDate,$toDate);
    // Query with join to fetch start_date
    $query = Attendance::query()
        ->join('studentdata', function ($join) {
            $join->on('attendance.family_id', '=', 'studentdata.admissionid')
                 ->whereRaw('attendance.student_name = CONCAT(studentdata.studentname, " ", studentdata.studentsur)')
                 ->where('studentdata.branch_id', '=', session('branch_id'));
        })
        ->where('attendance.branch_id', session('branch_id'))
        ->select([
            'attendance.id',
            'attendance.family_id',
            'attendance.student_name',
            'attendance.student_year_in_school as year',
            'attendance.teacher_name',
            'attendance.subject',
            'attendance.time_slot',
            'attendance.bk_ch',
            'attendance.session_1',
            'attendance.date',
            'attendance.adjustment',
            'studentdata.start_date', // Include start_date
        ])
        ->orderBy('attendance.family_id', 'asc');

    if (!empty($family_id) && is_numeric($family_id)) {
        $query->where('attendance.family_id', $family_id);
    }

    if (!empty($selectedValue)) {
        $selectedValue = preg_replace('/^[\p{Z}\s]+/u', '', $selectedValue);
        $query->whereRaw('LOWER(LTRIM(attendance.student_name)) LIKE ?', ['%' . strtolower($selectedValue) . '%']);
    }

    if (!empty($fromDate) && !empty($toDate)) {
        $query->whereBetween('attendance.date', [$fromDate, $toDate]);
    }

    if (!empty($subject) && $subject !== 'null') {
        $query->whereRaw('LOWER(attendance.subject) = ?', [strtolower($subject)]);
    }

    if (!empty($timeSlot) && strpos($timeSlot, ':') !== false) {
        $query->whereRaw("REPLACE(REPLACE(attendance.time_slot, 'am', ''), 'pm', '') = ?", [$timeSlot]);
    }

    $attendances = $query->get();

    $filename = 'attendance_' . date('Ymd_His') . '.csv';
    $csvContent = "id,family_id,student_name,year,teacher_name,subject,time_slot,bk_ch,session_1,date,adjustment,start_date\n";

    foreach ($attendances as $attendance) {
        $fields = [
            $attendance->id,
            $attendance->family_id,
            '"' . str_replace('"', '""', $attendance->student_name) . '"',
            $attendance->year,
            '"' . str_replace('"', '""', $attendance->teacher_name) . '"',
            '"' . str_replace('"', '""', $attendance->subject) . '"',
            $attendance->time_slot,
            $attendance->bk_ch,
            $attendance->session_1,
            $attendance->date,
            '"' . str_replace('"', '""', $attendance->adjustment) . '"',
            $attendance->start_date, // Add start_date
        ];
        $csvContent .= implode(',', $fields) . "\n";
    }

    return response($csvContent)
        ->header('Content-Type', 'text/csv')
        ->header('Content-Disposition', "attachment; filename=\"$filename\"")
        ->header('Cache-Control', 'max-age=0');
}


    public function exportAtten(Request $request)
    {
        // Extract and validate inputs
        $family_id = $request->input('familyId');
        $selectedValue = $request->input('studentName', '');
        // $fromDate = $request->input('fromDate');
        // $toDate = $request->input('toDate');
        $fromDate = Carbon::createFromFormat('d/m/Y', $request->fromDate)->format('Y-m-d');
        $toDate = Carbon::createFromFormat('d/m/Y', $request->toDate)->format('Y-m-d');
        $subject = $request->input('subject', '');
        $timeSlot = preg_replace('/[a-zA-Z]+/', '', $request->input('timeSlot', ''));

        // Handle special case for Muhammad


        // Initialize query
        $query = Attendance::query()
            ->where('branch_id', session('branch_id'))->select([
                'id',
                'family_id',
                'student_name',
                'student_year_in_school as year',
                'teacher_name',
                'subject',
                'time_slot',
                'bk_ch',
                'session_1',
                'date'
            ])
            ->where(function ($query) {
                $query->whereHas('student', function ($subQuery) {
                    $subQuery->where('student_status', '!=', 'inactive')
                        ->orWhereNull('student_status');
                })->orWhereDoesntHave('student');
            })
            ->orderBy('family_id', 'asc');

        // Apply filters conditionally
        if (!empty($family_id) && is_numeric($family_id)) {
            $query->where('family_id', $family_id);
        }

        if (!empty($selectedValue)) {
            $selectedValue = preg_replace('/^[\p{Z}\s]+/u', '', $selectedValue);
            $query->whereRaw('LOWER(LTRIM(student_name)) LIKE ?', ['%' . strtolower($selectedValue) . '%']);
        }

        if (!empty($fromDate) && !empty($toDate) && strpos($fromDate, '-') !== false && strpos($toDate, '-') !== false) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        if (!empty($subject) && $subject !== 'null') {
            $query->whereRaw('LOWER(subject) LIKE ?', ['%' . strtolower($subject) . '%']);
        }

        if (!empty($timeSlot) && strpos($timeSlot, ':') !== false) {
            $query->whereRaw("REPLACE(REPLACE(time_slot, 'am', ''), 'pm', '') = ?", [$timeSlot]);
        }

        // Execute query
        $attendances = $query->get();

        // Generate CSV content
        $filename = 'attendance_' . date('Ymd_His') . '.csv';
        $csvContent = "id,family_id,student_name,year,teacher_name,subject,time_slot,bk_ch,session_1,date\n";

        foreach ($attendances as $attendance) {
            // Escape CSV fields to handle commas and quotes
            $fields = [
                $attendance->id,
                $attendance->family_id,
                '"' . str_replace('"', '""', $attendance->student_name) . '"',
                $attendance->year,
                '"' . str_replace('"', '""', $attendance->teacher_name) . '"',
                '"' . str_replace('"', '""', $attendance->subject) . '"',
                $attendance->time_slot,
                $attendance->bk_ch,
                $attendance->session_1,
                $attendance->date
            ];
            $csvContent .= implode(',', $fields) . "\n";
        }

        // Set response headers
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"")
            ->header('Cache-Control', 'max-age=0');
    }


    public function attendanceEdit($id)
    {
        $attendance = Attendance::where('branch_id', session('branch_id'))->where('id', $id)->first();
        $subjects = Subject::where('branch_id', session('branch_id'))->get();

        return view('branchFrontend.attendance.editAttendance', compact('attendance', 'subjects'));
    }

    public function updateAttendance(Request $request)
    {
        // dd($request->all());
        $id = $request->input('id');
        $attendance = Attendance::where('branch_id', session('branch_id'))->find($id);

        if (!$attendance) {
            return response()->json(['error' => 'Attendance not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'family_id' => 'required',
            'bk_ch' => 'required',
            'date' => 'required',
            'time_slot' => 'required',
            'subject' => 'sometimes',
            'teacher_name' => 'sometimes'
        ]);

        // Update the attendance record - use individual attribute assignment and save() to trigger activity log
        $attendance->family_id = $validatedData['family_id'];
        $attendance->bk_ch = $validatedData['bk_ch'];
        $attendance->date = $validatedData['date'];
        $attendance->time_slot = $validatedData['time_slot'];
        $attendance->subject = $validatedData['subject'] ?? $attendance->subject;
        $attendance->teacher_name = $validatedData['teacher_name'] ?? $attendance->teacher_name;

        $attendance->save();

        return back()->with('success', 'Attendance updated successfully');
    }

    public function books()
    {
        $books = Book::where('branch_id', session('branch_id'))->get();
        return view('branchFrontend.books.index', compact('books'));
    }

    public function bookStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:books',
            // 'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        Book::create([
            'name' => $request->name,
            'code' => $request->code,
            'quantity' => null, //$request->quantity,
            'price' => $request->price,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        return redirect()->route('books')->with('success', 'Book added successfully.');
    }

    public function deleteBooks($id)
    {
        $branch_id = session('branch_id');

        $deleted = Book::where('id', $id)
            ->where('branch_id', $branch_id)
            ->delete();

        if ($deleted) {
            return redirect()->route('books')->with('success', 'Book deleted successfully.');
        }

        return redirect()->route('books')->with('error', 'Book not found or you don\'t have permission to delete it.');
    }

    public function bookUpdate(Request $request)
    {
        $branch_id = session('branch_id');
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:books,code,' . $request->id,
            // 'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $book = Book::where('id', $request->id)
            ->where('branch_id', $branch_id)
            ->first();

        if (!$book) {
            return redirect()->route('books')->with('error', 'Book not found or you don\'t have permission to edit it.');
        }

        $book->update([
            'name' => $request->name,
            'code' => $request->code,
            // 'quantity' => $request->quantity,
            'price' => $request->price,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        return redirect()->route('books')->with('success', 'Book updated successfully.');
    }

    public function createsales()
    {
        $books = Book::where('branch_id', session('branch_id'))->get();
        return view('branchFrontend.books.createSales', compact('books'));
    }

    public function storeSales(Request $request)
    {

        $request->validate([
            'book_id' => 'required',
            'quantity' => 'required',
            'amount_received' => 'required',
            'receipt' => 'nullable|file',
        ]);

        $book = Book::where('branch_id', session('branch_id'))->find($request->book_id);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        if ($book->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock.');
        }

        $receiptPath = null;

        if ($request->hasFile('receipt')) {
            $receiptFile = $request->file('receipt');
            $destinationPath = public_path('receipts');

            // Make sure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Define the file name and move the file
            $fileName = time() . '_' . $receiptFile->getClientOriginalName();
            $receiptFile->move($destinationPath, $fileName);
            $receiptPath = 'receipts/' . $fileName;
        }

        Sale::create([
            'book_id' => $request->book_id,
            'quantity' => $request->quantity,
            'amount_received' => $request->amount_received,
            'receipt_path' => $receiptPath,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        $book->quantity -= $request->quantity;
        $book->save();

        return redirect()->route('sales.create')->with('success', 'Sale recorded successfully.');
    }


    public function purchaseSale()
    {
        $books = Book::where('branch_id', session('branch_id'))->get();
        return view('branchFrontend.books.purchaseSales', compact('books'));
    }


    public function storePurchase(Request $request)
    {
        $branch_id = session('branch_id');
        $branch = User::where('branch_id', $branch_id)->where('is_main_branch', 1)->first();

        $request->validate([
            'book_id' => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'amount_paid' => 'required|numeric|min:0',
            'payment_receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $book = Book::where('branch_id', $branch_id)->find($request->book_id);

        if (!$book) {
            return redirect()->back()->with('error', 'Book not found or you don\'t have permission to purchase it.');
        }

        $receiptPath = null;

        if ($request->hasFile('payment_receipt')) {
            $receiptFile = $request->file('payment_receipt');
            $destinationPath = public_path('receipts');

            // Make sure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Define the file name and move the file
            $fileName = time() . '_' . $receiptFile->getClientOriginalName();
            $receiptFile->move($destinationPath, $fileName);
            $receiptPath = 'receipts/' . $fileName;
        }

        Purchase::create([
            'book_id' => $request->book_id,
            'quantity' => $request->quantity,
            'amount_paid' => $request->amount_paid,
            'payment_receipt' => $receiptPath,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        $book->quantity += $request->quantity;
        $book->save();

        return redirect()->route('manage.purchases')->with('success', 'Purchase recorded successfully.');
    }


    public function booksRecord()
    {
        $books = Book::withSum('sales', 'quantity')
            ->withSum('purchases', 'quantity')
            ->withSum('sales', 'amount_received')
            ->where('branch_id', session('branch_id'))
            ->get()
            ->map(function ($book) {
                $book->sold = $book->sales_sum_quantity ?? 0;
                $book->purchased = $book->purchases_sum_quantity ?? 0;
                $book->balance = $book->quantity + $book->purchased - $book->sold;
                $book->opening_balance = $book->sales_sum_amount_received ?? 0;
                return $book;
            });

        $sales = Sale::where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $purchases = Purchase::where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('branchFrontend.books.bookRecord', compact('books', 'sales', 'purchases'));
    }



    public function assignBook()
    {
        $students = Student::where('branch_id', session('branch_id'))->get();
        $assignBooks = AssignBook::where('branch_id', session('branch_id'))->get();
        $teachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', session('branch_id'))
            ->select('teacher_name')
            ->distinct()
            ->get();

        $subjects  = Subject::where('branch_id', session('branch_id'))->get();
        // dd($students->take(5));

        return view('branchFrontend.assignBook.index', compact('students', 'assignBooks', 'teachers', 'subjects'));
    }

    // public function storeAssignBook(Request $request)
    // {
    //     // dd($request->all());
    //     $validatedData = $request->validate([
    //         'student_id' => 'required|string|max:255',
    //         'book_name' => 'required|string|max:255',
    //         'subject' => 'required|string',
    //         'teacher_name' => 'required',
    //     ]);

    //     [$studentName, $familyId] = explode('-', $request->input('student_id'));

    //     $existingRecord = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $familyId)
    //         ->where('student_name', $studentName)
    //         ->where('book', $request->input('book_name'))
    //         ->where('subject', $request->input('subject'))
    //         ->where('teacher', $request->input('teacher_name'))

    //         ->exists();

    //     if ($existingRecord) {
    //         return redirect()->back()->with('error', 'This record already exists.');
    //     }
    //     $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

    //     AssignBook::create([
    //         'family_id' => $familyId,
    //         'student_name' => $studentName,
    //         'book' => $request->input('book_name'),
    //         'subject' => $request->input('subject'),
    //         'teacher' => $request->input('teacher_name'),
    //         'price' => $request->book_price,
    //         'branch_id' => $branch->branch_id,
    //         'branch_name' => $branch->branch_name,
    //     ]);

    //     return redirect()->back()->with('success', 'Book assigned successfully!');
    // }
    public function storeAssignBook(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'student_id' => 'required|string|max:255',
            'book_name' => 'required|string|max:255',
            'subject' => 'required|string',
            'teacher_name' => 'required',
        ]);

        // Split student_id on the last hyphen
        $studentId = $request->input('student_id');
        $lastHyphenPos = strrpos($studentId, '-');
        if ($lastHyphenPos === false) {
            return redirect()->back()->with('error', 'Invalid student ID format.');
        }

        $studentName = substr($studentId, 0, $lastHyphenPos);
        $familyId = substr($studentId, $lastHyphenPos + 1);

        // Check for existing record
        $existingRecord = AssignBook::where('branch_id', session('branch_id'))
            ->where('family_id', $familyId)
            ->where('student_name', $studentName)
            ->where('book', $request->input('book_name'))
            ->where('subject', $request->input('subject'))
            ->where('teacher', $request->input('teacher_name'))
            ->exists();

        if ($existingRecord) {
            return redirect()->back()->with('error', 'This record already exists.');
        }

        // Fetch branch details
        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        if (!$branch) {
            return redirect()->back()->with('error', 'Branch not found.');
        }

        // Create new record
        AssignBook::create([
            'family_id' => $familyId,
            'student_name' => $studentName,
            'book' => $request->input('book_name'),
            'subject' => $request->input('subject'),
            'teacher' => $request->input('teacher_name'),
            'price' => $request->book_price,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        return redirect()->back()->with('success', 'Book assigned successfully!');
    }

    public function deleteAssignBook($id)
    {
        try {
            DB::beginTransaction();

            $assignBook = AssignBook::where('branch_id', session('branch_id'))->findOrFail($id);
            $assignBook->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Book assignment deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete book assignment');
        }
    }

    public function editAssignBook($id)
    {
        $assignBook = AssignBook::where('branch_id', session('branch_id'))->findOrFail($id);
        $teachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', session('branch_id'))
            ->select('teacher_name')
            ->distinct()
            ->get();
        // dd($assignBook,$teachers);
        return view('branchFrontend.assignBook.editAssignBook', compact('assignBook', 'teachers'));
    }

    public function updateAssignBook(Request $request, $id)
    {

        $assignBook = AssignBook::where('branch_id', session('branch_id'))->where('id', $id)->firstOrFail();
        // dd($request->all());
        $request->validate([
            'subject' => 'required',
            'book_name' => 'required|string|max:255',
            'teacher_name' => 'required',
        ]);
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $assignBook->student_name = $request->student;
        $assignBook->subject = $request->subject;
        $assignBook->book = $request->book_name;
        $assignBook->teacher = $request->teacher_name;
        $assignBook->price = $request->book_price;
        $assignBook->branch_id = $branch->branch_id;
        $assignBook->branch_name = $branch->branch_name;
        $assignBook->save();

        return redirect()->route('books.assign')->with('success', 'Assigned book updated successfully');
    }

    public function createPayment()
    {
        return view('branchFrontend.payment.createPayment');
    }

    public function showPayment(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'family_id' => 'required|numeric',
        ]);
        $family_id = $request->family_id;
        $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->get();

        // $latestAssignBook = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $family_id)
        //     ->orderBy('created_at', 'desc')
        //     ->get();
        $latestAssignBook = AssignBook::where('branch_id', session('branch_id'))
            ->where('family_id', $family_id)
            ->where(function ($q) {
                $q->whereNull('paid_status')
                    ->orWhere('paid_status', '!=', 1);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($latestAssignBook);



        $latestAssignBook = $latestAssignBook ?? null;


        $inactiveStudents = $students->filter(function ($student) {
            return $student->student_status === 'inactive';
        })->pluck('studentname')->toArray(); // Get only the names of inactive students
        // If there are inactive students, prepare the message
        $inactiveMessage = !empty($inactiveStudents)
            ? 'The following students are inactive-> ' . implode(', ', $inactiveStudents)
            : 'All students are active.';
        // dd($inactiveMessage);

        $students_ids = array();
        foreach ($students as $student) {
            $students_ids[] = $student->studentid;
        }
        if (count($students_ids) <= 0) {
            return redirect()->route('payment.create')->withErrors([
                'error' => 'No student found, corresponding this family id.'
            ]);
        }
        foreach ($students as $student) {
            $student_names[] = $student->studentname;
        }
        $student_names = implode(" + ", $student_names);
        $std = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->first();
        $std_familyId = $std->admissionid;

        $payment = Payment::where('branch_id', session('branch_id'))->where('paymentfamilyid', $std_familyId)->where(function ($query) {
            $query->where('deleted_by', null)

                ->orWhere('is_deleted', '');
        })->orderBy('created_at', 'desc')->first();
        // dd($payment);

        //for below tableshow paymentss start
        $previousPayments = Payment::where('branch_id', session('branch_id'))->where('paymentfamilyid', $family_id)
            ->where('deleted_by', null)
            ->orderBy('created_at', 'desc')
            ->select(
                '*',
                \DB::raw("COALESCE(DATE_FORMAT(STR_TO_DATE(paymentfrom, '%m/%d/%Y'), '%d/%m/%Y'), DATE_FORMAT(STR_TO_DATE(paymentfrom, '%Y-%m-%d'), '%d/%m/%Y')) as formatted_paymentfrom"),
                \DB::raw("COALESCE(DATE_FORMAT(STR_TO_DATE(paymentto, '%m/%d/%Y'), '%d/%m/%Y'), DATE_FORMAT(STR_TO_DATE(paymentto, '%Y-%m-%d'), '%d/%m/%Y')) as formatted_paymentto"),
                \DB::raw("COALESCE(DATE_FORMAT(STR_TO_DATE(paymentdate, '%m/%d/%Y'), '%d/%m/%Y'), DATE_FORMAT(STR_TO_DATE(paymentdate, '%Y-%m-%d'), '%d/%m/%Y')) as formatted_paymentdate")
            )
            ->get();

        //for below tableshow paymentss end


        if ($payment == null) {

            $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $std_familyId)->first();
            $last_package = $admission ? $admission->feedetail : null;
            $last_paid = null;
            $paid_up_to_date = null;
            $balance = null;

            $auth_user = null;
            $last_payment_date = null;

            $last_payment_date = null;

            $comments = \DB::table('payment_comments')->where('branch_id', session('branch_id'))
                ->where('family_id', $family_id)->first();
            if (!$comments) {
                $comments = null;
            }
            $package = $admission->feedetail;
            
            // Parse package to extract amount and weeks for split input
            $packageAmount = '';
            $packageWeeks = '';
            if ($package) {
                // Try new format: "280 for 2 weeks", "280 for 4 weeks", "280 for ucas session", "280 for per month", "280 for per session"
                if (preg_match('/(\d+)\s+for\s+(.+)/i', $package, $matches)) {
                    $packageAmount = $matches[1];
                    $packageWeeks = strtolower(trim($matches[2]));
                }
                // Try old format: "280for4weeks"
                elseif (preg_match('/(\d+)for(\d+)weeks?/i', $package, $matches)) {
                    $packageAmount = $matches[1];
                    $packageWeeks = strtolower($matches[2] . ' week' . ($matches[2] > 1 ? 's' : ''));
                }
                // Try to extract just numbers
                elseif (preg_match('/(\d+)/', $package, $matches)) {
                    $packageAmount = $matches[1];
                }
            }
            
            // dd('');
            return view('branchFrontend.payment.createPayment', compact('latestAssignBook', 'inactiveMessage', 'package', 'comments', 'last_package', 'last_paid', 'paid_up_to_date', 'auth_user', 'student_names', 'family_id', 'last_payment_date', 'students', 'balance', 'previousPayments', 'packageAmount', 'packageWeeks'));
        } else
        if ($payment) {
            // dd($payment);
            $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $std_familyId)->first();
            $last_package = $payment->package;
            $last_paid = $payment->paid;

            $paid_up_to_date = explode(' ', $payment->paymentto)[0]; //$payment->paymentto;
            // dd($paid_up_to_date);
            $dateObject = \DateTime::createFromFormat('d/m/Y', $paid_up_to_date);

            if (!$dateObject) {
                // If the first format fails, try the second format
                $dateObject = \DateTime::createFromFormat('Y-m-d', $paid_up_to_date);
            }

            if ($dateObject) {
                // Convert the date to the desired format (12 December 2015)
                $formattedDate = $dateObject->format('d F Y');

                // Now $formattedDate contains the date in the desired format
                $paid_up_to_date = $formattedDate;
            } else {
                // Handle invalid date formats
                $paid_up_to_date = 'Invalid Date';
            }


            // $paid_up_to_date =$paid_up_to_date ? \Carbon\Carbon::parse($paid_up_to_date)->format('d-F-Y') : null; //strtotime($paid_up_to_date);
            // $paid_up_to_date = date('d/m/Y' ,$paid_up_to_date);
            $balance = ($payment->balance < 0 ? '+' : '') . abs(floatval($payment->balance));
            // Always use admission feedetail for package display (not payment package)
            $package = $admission ? $admission->feedetail : null;
            
            // Parse package to extract amount and weeks for split input from admission feedetail
            $packageAmount = '';
            $packageWeeks = '';
            if ($package) {
                // Try new format: "280 for 2 weeks", "280 for 4 weeks", "280 for ucas session", "280 for per month", "280 for per session"
                if (preg_match('/(\d+)\s+for\s+(.+)/i', $package, $matches)) {
                    $packageAmount = $matches[1];
                    $packageWeeks = strtolower(trim($matches[2]));
                }
                // Try old format: "280for4weeks"
                elseif (preg_match('/(\d+)for(\d+)weeks?/i', $package, $matches)) {
                    $packageAmount = $matches[1];
                    $packageWeeks = strtolower($matches[2] . ' week' . ($matches[2] > 1 ? 's' : ''));
                }
                // Try to extract just numbers
                elseif (preg_match('/(\d+)/', $package, $matches)) {
                    $packageAmount = $matches[1];
                }
            }

            $auth_user = $payment->collector;
            $last_payment_date = $payment->paymentdate;

            $dateObject = \DateTime::createFromFormat('d/m/Y', $last_payment_date);

            if (!$dateObject) {
                // If the first format fails, try the second format
                $dateObject = \DateTime::createFromFormat('Y-m-d', $last_payment_date);
            }

            if ($dateObject) {
                // Convert the date to the desired format (12 December 2015)
                $last_payment_date = $dateObject->format('d F Y');

                // Now $formattedDate contains the date in the desired format
                $last_payment_date = $last_payment_date;
            } else {
                // Handle invalid date formats
                $last_payment_date = 'Invalid Date';
            }


            // $last_payment_date = \Carbon\Carbon::parse($last_payment_date)->format('d-F-Y');//date("d/m/Y", strtotime($last_payment_date));
            // $last_payment_date = $last_payment_date ? \Carbon\Carbon::parse($last_payment_date)->format('d-F-Y') : null;



            // dd( $payment->paymentto,$paid_up_to_date,'ok',$last_payment_date,$payment->paymentdate);

            $comments = \DB::table('payment_comments')->where('branch_id', session('branch_id'))
                ->where('family_id', $family_id)->first();
            // dd($comments->comments);
            $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->get();

            $inactiveStudents = $students->filter(function ($student) {
                return $student->student_status === 'inactive';
            })->pluck('studentname')->toArray(); // Get only the names of inactive students
            // If there are inactive students, prepare the message
            $inactiveMessage = !empty($inactiveStudents)
                ? 'The following students are inactive-> ' . implode(', ', $inactiveStudents)
                : 'All students are active.';
            // dd($inactiveMessage);

            // dd($last_paid,$paid_up_to_date,$last_payment_date);

            return view('branchFrontend.payment.createPayment', compact('latestAssignBook', 'inactiveMessage', 'package', 'comments', 'last_package', 'last_paid', 'paid_up_to_date', 'auth_user', 'student_names', 'family_id', 'last_payment_date', 'students', 'balance', 'previousPayments', 'packageAmount', 'packageWeeks'));
        } else {
            return redirect()->route('payment.create')->withErrors([
                'error' => 'No payment found, corresponding this family id.'
            ]);
        }
    }


    public function getPaymentBooks(Request $request)
    {
        $request->validate([
            'family_id' => 'required|integer',
        ]);

        $assignBooks = AssignBook::where('branch_id', session('branch_id'))
            ->where('family_id', $request->family_id)
            ->where(function ($q) {
                $q->whereNull('paid_status')
                    ->orWhere('paid_status', '!=', 1);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        // AssignBook::where('branch_id', session('branch_id'))->where('family_id', $request->family_id)
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        $assignBooks = $assignBooks->map(function ($book) {
            $book->paid = rand(0, 1);
            return $book;
        });

        return response()->json([
            'message' => 'Payment books retrieved successfully!',
            'data' => $assignBooks
        ]);
    }


    public function updateBookPaid(Request $request)
    {
        // dd($request->all());
        // Validate the input data
        $request->validate([
            'book_id' => 'required|integer|exists:assign_books,id',
            'paid' => 'required|boolean',

        ]);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();




        // Update the payment status in the assign_books table
        $updated = DB::table('assign_books')->where('branch_id', session('branch_id'))
            ->where('id', $request->input('book_id'))
            ->update([
                'paid_status' => $request->input('paid'),
                'payment_method' => $request->input('payment_method'),
                'updated_at' => now(),
            ]);

        $updated = AssignBook::where('branch_id', session('branch_id'))->where('id', $request->input('book_id'))
            ->first();
        // dd($updated);

        if ($updated) {
            // Retrieve the most recent payment record for the given family_id
            $lastPayment = Payment::where('branch_id', session('branch_id'))->where('paymentfamilyid', $updated->family_id)
                ->orderBy('created_at', 'desc')
                ->first();
            // dd($lastPayment);
            // Get the package from the last payment record if it exists
            $package = $lastPayment ? $lastPayment->package : null;

            // Get the balance from the last payment if it exists and add to it
            $balance = $lastPayment->balance ?? null;

            $receiptNo = $lastPayment ? $lastPayment->paymentid + 1 : 1;
            $date = date('d');   // Day (e.g., 04 for 4th day of the month)
            $month = date('m');  // Month (e.g., 10 for October)
            $year = date('y');
            $random = $date . $month . $year . $receiptNo;

            // Create the new payment record
            $payment = Payment::create([
                'paymentfrom' => now(), // Current date
                'paymentto' => now(), // Current date
                'paymentfamilyid' => $updated->family_id,
                'paymentdate' => now()->format('Y-m-d'), // Current date
                'paid' => 2, // Set to 2
                'package' => $package,
                'collector' => auth()->user()->name,
                'balance' => $balance,
                'payment_method' => $request->input('payment_method'),
                'payment_detail' => 2, // Set to 2
                'bank_transfer' => null,
                'adjustment' => null,
                'card_payment' => null,
                'cash_payment' => 2,
                'receipt_no' => $random, // Get from request
                'branch_id' => $branch->branch_id,
                'branch_name' => $branch->branch_name,
                'created_at' =>  now(), // Set created_at to null
                'updated_at' => now(), // Set updated_at to the current timestamp
            ]);
            // Return success response
            return response()->json(['success' => true, 'message' => 'Payment status updated and payment created successfully.']);
        } else {
            // Return failure response if update fails
            return response()->json(['success' => false, 'message' => 'Failed to update payment status.'], 500);
        }
    }


    public function storePayment(Request $request)
    {
        // dd($request->all());
        $request->payment_method = implode(',', array_keys(array_filter([
            'Cash Payment' => $request->input('cash_payment_amount'),
            'Card Payment' => $request->input('card_payment_amount'),
            'Bank Transfer' => $request->input('bank_transfer_amount'),
            'Adjustment' => $request->input('adjustment_amount'),
        ])));

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();




        // $request->payment_method = implode(', ', $request->payment_method);

        $request->balance = str_replace('+', '-', $request->balance);

        $family_id = $request->existing_family_id;

        $paymentz = Payment::where('branch_id', session('branch_id'))->where('paymentfamilyid', $family_id)->orderBy('paymentdate', 'desc')->first();
        // dd($payment);
        // dd($request->all(),$payment);
        $paid_up_to_date = 0;
        if ($paid_up_to_date == 0) {
            if ($request->paid_to == '') {
                $paid_up_to_date = $paymentz->paymentto;
                $paid_up_to_date = date('Y-m-d', strtotime(str_replace('/', '-',  $paid_up_to_date)));
            } else {
                $paid_up_to_date = date('Y-m-d', strtotime(str_replace('/', '-',  $request->paid_to)));
            }

            $paid_from = date('Y-m-d', strtotime(str_replace('/', '-',  $request->paid_from)));
            $paid_to = date('Y-m-d', strtotime(str_replace('/', '-',  $request->paid_to)));
            $crrDate = date('Y-m-d', strtotime(str_replace('/', '-',  $request->payment_date)));
            
            // Handle package: if separate amount and weeks provided, combine them
            $package = $request->package;
            if ($request->has('package_amount') && $request->has('package_weeks') && 
                !empty($request->package_amount) && !empty($request->package_weeks)) {
                // Combine as "amount for weeks" format (convert weeks to lowercase)
                $weeks = strtolower(trim($request->package_weeks));
                $package = trim($request->package_amount) . ' for ' . $weeks;
            }
            
            $extractedDigits = preg_replace('/[^\d ]*\b(\d+)\b.*/', '$1', $package);

            if (preg_match('/[a-zA-Z]/', $extractedDigits)) {
                $extractedDigits = preg_replace('/^.*?(\d+).*?$/', '$1', $extractedDigits);
            }

            $date = date('d');   // Day (e.g., 09 for 4th day of the month)
            $month = date('m');  // Month (e.g., 10 for October)
            $year = date('y');   // Last two digits of the year (e.g., 24 for 2024)

            $lastPaymentInfo = Payment::where('branch_id', session('branch_id'))->orderBy('paymentid', 'desc')->first();

            $receiptNo = $lastPaymentInfo ? $lastPaymentInfo->paymentid + 1 : 1;

            $receiptNumber = $date . $month . $year . $receiptNo;


            $existingPayment = Payment::where([
                'paymentfrom' => $paid_from,
                'paymentto' => $paid_to,
                'paymentfamilyid' => $family_id,
                'paymentdate' => date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date))),
                'paid' => $request->paid,
                'package' => $package,
                'collector' => auth()->user()->name,
                'balance' => $request->balance,
                'branch_id' => $branch->branch_id,
                'branch_name' => $branch->branch_name,
            ])->latest('created_at')->first();


            // my previous working logic start

            if ($existingPayment && now()->diffInMinutes($existingPayment->created_at) <= 2) {
            } else {
                $payment = Payment::create([
                    'paymentfrom' => $paid_from,
                    'paymentto' => $paid_to,
                    'paymentfamilyid' => $family_id,
                    'paymentdate' => date('Y-m-d', strtotime(str_replace('/', '-', $request->payment_date))),
                    'paid' => $request->paid,
                    'package' => $package,
                    'collector' => auth()->user()->name,
                    'balance' => $request->balance,
                    'payment_method' => $request['payment_method'],
                    'payment_detail' => $request->paid,
                    'bank_transfer' => $request->input('bank_transfer_amount'),
                    'adjustment' => $request->input('adjustment_amount'),
                    'card_payment' => $request->input('card_payment_amount'),
                    'cash_payment' => $request->input('cash_payment_amount'),
                    'receipt_no' => $receiptNumber,
                    'branch_id' => $branch->branch_id,
                    'branch_name' => $branch->branch_name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }




            DB::table('payment_comments')
                ->updateOrInsert(
                    ['family_id' => $family_id],
                    [
                        'comments' => $request->comment,
                        'branch_id' => $branch->branch_id,
                        'branch_name' => $branch->branch_name
                    ]
                );

            // Update admission table feedetail with the new package
            $admission = Admission::where('branch_id', session('branch_id'))
                ->where('familyno', $family_id)
                ->first();
            
            if ($admission) {
                $admission->feedetail = $package;
                $admission->save();
            }


            if ($request->signal == 1) {
                $pdf = new Dompdf();
                $pdf->setOptions(new Options([
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'margin-top' => '0mm',
                    'margin-right' => '0mm',
                    'margin-bottom' => '0mm',
                    'margin-left' => '0mm',
                ]));
                $data = [];

                $date = $currentDate = Carbon::now()->format('Y-m-d');

                $timestamp = time();
                $rand_num = rand(10, 999);
                $random = $timestamp . $rand_num;

                $date = date('d');   // Day (e.g., 04 for 4th day of the month)
                $month = date('m');  // Month (e.g., 10 for October)
                $year = date('y');   // Last two digits of the year (e.g., 24 for 2024)

                // You can generate a random or sequential receipt number, for example:
                $receipt_number = rand(1, 999);  // You can adjust this logic for actual receipt numbering

                // Combine the parts to form the receipt number
                $random = $date . $month . $year . $receiptNo;
                // dd($random);
                $from = $paid_from;
                $to = $paid_to;

                $receivedfrom = $family_id;
                $receivedto = auth()->user()->name;


                function numberToWords($number)
                {
                    $words = [
                        0 => 'zero',
                        1 => 'one',
                        2 => 'two',
                        3 => 'three',
                        4 => 'four',
                        5 => 'five',
                        6 => 'six',
                        7 => 'seven',
                        8 => 'eight',
                        9 => 'nine',
                        10 => 'ten',
                        11 => 'eleven',
                        12 => 'twelve',
                        13 => 'thirteen',
                        14 => 'fourteen',
                        15 => 'fifteen',
                        16 => 'sixteen',
                        17 => 'seventeen',
                        18 => 'eighteen',
                        19 => 'nineteen',
                        20 => 'twenty',
                        30 => 'thirty',
                        40 => 'forty',
                        50 => 'fifty',
                        60 => 'sixty',
                        70 => 'seventy',
                        80 => 'eighty',
                        90 => 'ninety'
                    ];

                    $number = str_replace(',', '', $number);
                    $number = (int) $number;

                    if ($number < 21) {
                        return $words[$number];
                    }

                    if ($number < 100) {
                        $tens = (int) ($number / 10) * 10;
                        $units = $number % 10;
                        return $words[$tens] . ($units ? '-' . $words[$units] : '');
                    }

                    if ($number < 1000) {
                        $hundreds = (int) ($number / 100);
                        $remainder = $number % 100;
                        return $words[$hundreds] . ' hundred' . ($remainder ? ' and ' . numberToWords($remainder) : '');
                    }

                    if ($number < 1000000) {
                        $thousands = (int) ($number / 1000);
                        $remainder = $number % 1000;
                        return numberToWords($thousands) . ' thousand' . ($remainder ? ' ' . numberToWords($remainder) : '');
                    }

                    if ($number < 1000000000) {
                        $millions = (int) ($number / 1000000);
                        $remainder = $number % 1000000;
                        return numberToWords($millions) . ' million' . ($remainder ? ' ' . numberToWords($remainder) : '');
                    }

                    throw 'Number is too large to convert to words';
                }

                $paid = $request->paid;
                $amount_in_words = numberToWords($paid);
                // dd($amount_in_words);
                $feeAmount = $package;
                $thisPayment = $request->paid;
                $thisbalance = $request->balance = str_replace('-', '+', $request->balance);
                $paymentMethod = $request->payment_method;

                $date = date('d F Y', strtotime($crrDate));
                $from = date('d F Y', strtotime($from));
                $to = date('d F Y', strtotime($to));
                // dd($date,$request->payment_date,$from);

                $cash = $request->cash_payment_amount ? 'Yes' : '';
                $adjustment = $request->adjustment_amount ? 'Yes' : '';
                $bank = $request->bank_transfer_amount ? 'Yes' : '';

                // $data = [
                //     'date' => $date,
                //     'random' => $random,
                //     'from' => $from,
                //     'to' => $to,
                //     'receivedfrom' => $receivedfrom,
                //     'receivedto' => $receivedto,
                //     'amount_in_words' => $amount_in_words,
                //     'feeAmount' => $feeAmount,
                //     'thisPayment' => $thisPayment,
                //     'thisbalance' => $thisbalance,
                //     'paymentMethod' => $paymentMethod,
                //     'paid' => $paid,
                //     'cash' => $cash,
                //     'adjustment' => $adjustment,
                //     'bank' => $bank
                // ];


                $student = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->first();
                $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $student->guardianid)->first();
                $city = $guardian->city;
                $state = $guardian->countyStateRegion;
                $addressOfGuardian = $guardian->guardianaddress;
                $mob = $guardian->guardianmob;
                $postcode = $guardian->zIPCode;

                $studentNames = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->get();
                $student_names = $studentNames->map(function ($student) {
                    return trim($student->studentname . ' ' . $student->studentsur);
                })->implode(', ');



                $data = [
                    'student_names' => $student_names,
                    'date' => $date,
                    'random' => $random,
                    'from' => $from,
                    'to' => $to,
                    'receivedfrom' => $receivedfrom,
                    'receivedto' => $receivedto,
                    'amount_in_words' => $amount_in_words,
                    'feeAmount' => $feeAmount,
                    'thisPayment' => $thisPayment,
                    'thisbalance' => $thisbalance,
                    'paymentMethod' => $paymentMethod,
                    // 'cash'=>$cash,'adjustment'=>$adjustment,
                    'cash' => $cash,
                    'adjustment' => $adjustment,
                    'bank' => $bank,
                    'paid' => $paid,
                    'city' => $city,
                    'state' => $state,
                    'addressOfGuardian' => $addressOfGuardian,
                    'mob' => $mob,
                    'family_id' => $receivedfrom,
                    'postcode' => $postcode,


                ];

                $pdf->loadHtml(view('reports.receipt', $data));
                $pdf->render();
                $pdfOutput = $pdf->output();

                // Generate a data URI for the PDF
                $pdfDataUri = 'data:application/pdf;base64,' . base64_encode($pdfOutput);

                // Create a unique ID for the new window or tab
                $windowId = 'pdf-window-' . uniqid();



                // $this->sendReceiptToWhatsApp($pdfOutput, '03068649342', $data);

                // Create a JavaScript function that opens the PDF in a new window or tab and redirects back to the previous page
                $url = '/payments/show?family_id=' . $family_id;
                $script = "
                        <script>
                            var pdfOpened = false;

                            function openPdf() {
                                if (!pdfOpened) {
                                    pdfOpened = true;
                                    var pdfWindow = window.open('', '$windowId', 'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600');
                                    pdfWindow.document.write('<iframe src=\"$pdfDataUri\" style=\"width:100%; height:100%;\"></iframe>');

                                    pdfWindow.onunload = function() {
                                        pdfOpened = false;
                                        showSuccessMessage();
                                    };
                                }
                            }
                            openPdf();

                            function showSuccessMessage() {
                                // Redirect directly when the PDF window is closed
                                window.location.href = '$url';
                            }
                        </script>
                    ";

                return response($script);
            }
        }

        return redirect()->back();
    }

    // private function sendReceiptToWhatsApp($pdfOutput, $phoneNumber, $data)
    // {
    //     $sid = env('TWILIO_SID');
    //     $token = env('TWILIO_AUTH_TOKEN');
    //     $from = env('TWILIO_WHATSAPP_FROM');
    //     $to = 'whatsapp:+923068649342';

    //     try {
    //         $twilio = new Client($sid, $token);

    //         // 1. Create public/receipts directory if it doesn't exist
    //         $receiptsDir = public_path('receipts');
    //         if (!file_exists($receiptsDir)) {
    //             mkdir($receiptsDir, 0755, true);
    //         }

    //         // 2. Save PDF to public/receipts
    //         $filename = 'receipt_' . time() . '_' . $data['random'] . '.pdf';
    //         $pdfPath = $receiptsDir . '/' . $filename;
    //         file_put_contents($pdfPath, $pdfOutput);

    //         // 3. Generate public URL
    //         $mediaUrl = url('receipts/' . $filename);

    //         \Log::debug('PDF stored at: ' . $pdfPath);
    //         \Log::debug('Public URL: ' . $mediaUrl);

    //         // 4. Verify URL is accessible
    //         if (!@get_headers($mediaUrl)) {
    //             throw new \Exception("Generated URL is not accessible");
    //         }

    //         // 5. Send WhatsApp message
    //         $message = $twilio->messages->create($to, [
    //             'from' => $from,
    //             'body' => "Payment Receipt\nDate: {$data['date']}\nReceipt No: {$data['random']}\nAmount: {$data['paid']}",
    //             'mediaUrl' => [$mediaUrl]
    //         ]);

    //         // 6. Schedule file deletion (optional)
    //         // You can use a scheduled job to clean up old receipts periodically

    //         \Log::info('WhatsApp message sent successfully: SID ' . $message->sid);
    //         return true;
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to send WhatsApp message: ' . $e->getMessage());
    //         return false;
    //     }
    // }


    public function updatePaymentComment(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'id' => 'required|numeric',
        ]);

        // Get the main branch information
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        if (!$branch) {
            return response()->json(['error' => 'Branch not found'], 404);
        }

        // Update the comments, branch_name, and branch_id fields
        $affectedRows = DB::table('payment_comments')
            ->where('branch_id', session('branch_id'))
            ->where('family_id', $request->id)
            ->update([
                'comments' => $request->comment,
                'branch_name' => $branch->name,
                'branch_id' => $branch->branch_id,
            ]);

        if ($affectedRows > 0) {
            return response()->json(['success' => true, 'message' => 'Comment updated successfully']);
        } else {
            // Check if the record exists, if not create a new one
            $inserted = DB::table('payment_comments')->insert([
                'family_id' => $request->id,
                'comments' => $request->comment,
                'branch_name' => $branch->name,
                'branch_id' => $branch->branch_id,
            ]);

            if ($inserted) {
                return response()->json(['success' => true, 'message' => 'New comment created successfully']);
            } else {
                return response()->json(['error' => 'Failed to update or create comment'], 500);
            }
        }
    }


    public function pdfGenerate(Request $request, $id)
    {

        $latestPayment = Payment::where('branch_id', session('branch_id'))->where('paymentfamilyid', $id)
            ->where(function ($query) {
                $query->whereNull('is_deleted')
                    ->orWhere('is_deleted', '');
            })
            ->orderByDesc('paymentid')
            ->first();




        $pdf = new Dompdf();
        $pdf->setOptions(new Options([
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'margin-top' => '0mm',
            'margin-right' => '0mm',
            'margin-bottom' => '0mm',
            'margin-left' => '0mm',
        ]));
        $date = $currentDate = Carbon::now()->format('Y-m-d');

        $timestamp = time();
        $rand_num = rand(10, 999);
        $random = $timestamp . $rand_num;

        $year = date('y');
        $day = date('d');
        $month = date('m');
        $hour = date('H');
        $minute = date('i');
        $random = rand(0, 999);
        $random = $latestPayment->receipt_no; //$year . $day . $month . $hour . $minute;

        $from = $latestPayment->paymentfrom;
        $to = $latestPayment->paymentto;

        $receivedfrom = $latestPayment->paymentfamilyid;
        $receivedto = $latestPayment->collector;


        function numberToWords($number)
        {
            $words = [
                0 => 'zero',
                1 => 'one',
                2 => 'two',
                3 => 'three',
                4 => 'four',
                5 => 'five',
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine',
                10 => 'ten',
                11 => 'eleven',
                12 => 'twelve',
                13 => 'thirteen',
                14 => 'fourteen',
                15 => 'fifteen',
                16 => 'sixteen',
                17 => 'seventeen',
                18 => 'eighteen',
                19 => 'nineteen',
                20 => 'twenty',
                30 => 'thirty',
                40 => 'forty',
                50 => 'fifty',
                60 => 'sixty',
                70 => 'seventy',
                80 => 'eighty',
                90 => 'ninety'
            ];

            $number = str_replace(',', '', $number);
            $number = (int) $number;

            if ($number < 21) {
                return $words[$number];
            }

            if ($number < 100) {
                $tens = (int) ($number / 10) * 10;
                $units = $number % 10;
                return $words[$tens] . ($units ? '-' . $words[$units] : '');
            }

            if ($number < 1000) {
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                return $words[$hundreds] . ' hundred' . ($remainder ? ' and ' . numberToWords($remainder) : '');
            }

            if ($number < 1000000) {
                $thousands = (int) ($number / 1000);
                $remainder = $number % 1000;
                return numberToWords($thousands) . ' thousand' . ($remainder ? ' ' . numberToWords($remainder) : '');
            }

            if ($number < 1000000000) {
                $millions = (int) ($number / 1000000);
                $remainder = $number % 1000000;
                return numberToWords($millions) . ' million' . ($remainder ? ' ' . numberToWords($remainder) : '');
            }

            throw 'Number is too large to convert to words';
        }

        $paid = $latestPayment->paid;
        $amount_in_words = numberToWords($paid);
        // dd($amount_in_words);
        $feeAmount = $latestPayment->package;
        $thisPayment = $latestPayment->paid;
        $thisbalance = $latestPayment->balance = str_replace('-', '+', $latestPayment->balance);
        $paymentMethod = $latestPayment->payment_method;

        $cash = $latestPayment->cash_payment ? 'Yes' : '';
        $adjustment = $latestPayment->adjustment ? 'Yes' : '';
        $bank = $latestPayment->bank_transfer ? 'Yes' : '';

        // $cash =  $latestPayment->payment_method == 'Cash Payment' ? 'Yes' : '';
        // $adjustment = $latestPayment->payment_method == 'Adjustment' ? 'Yes' : '';

        $date = date('d F Y', strtotime($latestPayment->paymentdate));
        $from = date('d F Y', strtotime($from));
        $to = date('d F Y', strtotime($to));


        $student = Student::where('branch_id', session('branch_id'))->where('admissionid', $id)->first();
        $guardian = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $student->guardianid)->first();
        $city = $guardian->city;
        $state = $guardian->countyStateRegion;
        $addressOfGuardian = $guardian->guardianaddress;
        $mob = $guardian->guardianmob;
        $postcode = $guardian->zIPCode;

        $studentNames = Student::where('branch_id', session('branch_id'))->where('admissionid', $id)->get();
        $student_names = $studentNames->map(function ($student) {
            return trim($student->studentname . ' ' . $student->studentsur);
        })->implode(', ');
        // dd($student_names);

        $data = [
            'student_names' => $student_names,
            'date' => $date,
            'random' => $random,
            'from' => $from,
            'to' => $to,
            'receivedfrom' => $receivedfrom,
            'receivedto' => $receivedto,
            'amount_in_words' => $amount_in_words,
            'feeAmount' => $feeAmount,
            'thisPayment' => $thisPayment,
            'thisbalance' => $thisbalance,
            'paymentMethod' => $paymentMethod,
            // 'cash'=>$cash,'adjustment'=>$adjustment,
            'cash' => $cash,
            'adjustment' => $adjustment,
            'bank' => $bank,
            'paid' => $paid,
            'city' => $city,
            'state' => $state,
            'addressOfGuardian' => $addressOfGuardian,
            'mob' => $mob,
            'family_id' => $receivedfrom,
            'postcode' => $postcode,


        ];



        $pdf->loadHtml(view('reports.receipt', $data));
        $pdf->render();
        $pdfOutput = $pdf->output();

        $pdfDataUri = 'data:application/pdf;base64,' . base64_encode($pdfOutput);

        return response()->json(['pdfDataUri' => $pdfDataUri]);
    }


    public function previousPaymentForm()
    {
        return view('branchFrontend.payment.showPayment');
    }

    public function previousPaymentShow(Request $request)
    {
        $family_id = $request->family_id;
        $paymentMethods = $request->input('payment_methods');
        $start_date = $request['start_date'] ?? null;
        $end_date = $request['end_date'] ?? null;

        $payments = Payment::where('branch_id', session('branch_id'))->
            // where('paymentfamilyid', $family_id)
            when($family_id && !empty($family_id), function ($query) use ($family_id) {
                $query->where('paymentfamilyid', $family_id);
            })
            ->when($paymentMethods && !in_array('all', $paymentMethods), function ($query) use ($paymentMethods) {
                // If payment_methods is not empty and "all" is not present, filter by payment_method
                $query->where(function ($query) use ($paymentMethods) {
                    $query->whereIn('payment_method', $paymentMethods);

                    // Check if "Cash Payment" is present in $paymentMethods
                    if (in_array('Cash Payment', $paymentMethods)) {
                        $query->orWhere('payment_method', 'like', '%Cash%');
                    }

                    // Check if "Card Payment" is present in $paymentMethods
                    if (in_array('Card Payment', $paymentMethods)) {
                        $query->orWhere('payment_method', 'like', '%Card%');
                    }

                    // Check if "Bank Transfer" is present in $paymentMethods
                    if (in_array('Bank Transfer', $paymentMethods)) {
                        $query->orWhere('payment_method', 'like', '%Bank%');
                    }

                    // Check if "Adjustment" is present in $paymentMethods
                    if (in_array('Adjustment', $paymentMethods)) {
                        $query->orWhere('payment_method', 'like', '%Adjustment%');
                    }
                });
            })
            ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                // If both start_date and end_date are present, filter by date range
                $query->whereBetween('paymentdate', [$start_date, $end_date]);
            })
            ->where(function ($query) {
                $query->whereNull('is_deleted')
                    ->orWhere('is_deleted', '');
            })
            // ->orderBy('paymentdate', 'desc')
            ->orderBy('paymentid', 'desc')

            ->select(
                '*',
                \DB::raw("COALESCE(
                    DATE_FORMAT(STR_TO_DATE(paymentfrom, '%m/%d/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentfrom, '%Y-%m-%d'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentfrom, '%d/%m/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentfrom, '%d-%m-%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(paymentfrom, '%d/%m/%Y')
                ) as formatted_paymentfrom"),
                \DB::raw("COALESCE(
                    DATE_FORMAT(STR_TO_DATE(paymentto, '%m/%d/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentto, '%Y-%m-%d'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentto, '%d/%m/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentto, '%d-%m-%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(paymentto, '%d/%m/%Y')
                ) as formatted_paymentto"),
                \DB::raw("COALESCE(
                    DATE_FORMAT(STR_TO_DATE(paymentdate, '%m/%d/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentdate, '%Y-%m-%d'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentdate, '%d/%m/%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(STR_TO_DATE(paymentdate, '%d-%m-%Y'), '%d/%m/%Y'),
                    DATE_FORMAT(paymentdate, '%d/%m/%Y')
                ) as formatted_paymentdate")
            )
            ->get();
        // dd($payments);
        $paymentMethodTotal = [];
        $total = 0; // Initialize total

        foreach ($payments as $payment) {
            $paymentMethod = $payment->payment_method;
            $paid = $payment->paid;

            if (!isset($paymentMethodTotal[$paymentMethod])) {
                $paymentMethodTotal[$paymentMethod] = 0;
            }

            $paymentMethodTotal[$paymentMethod] += $paid;
            $total += $paid; // Add to total
        }
        // dd($payments);
        $commentCount = DB::table('payment_comments')->where('branch_id', session('branch_id'))
            ->where('family_id', $family_id)
            ->count();
        if ($commentCount > 0) {
            $paymentComment = DB::table('payment_comments')->where('branch_id', session('branch_id'))
                ->where('family_id', $family_id)
                ->first();
        } else {
            $paymentComment = null;
        }


        return view('branchFrontend.payment.showPayment', compact('payments', 'family_id', 'paymentMethodTotal', 'total', 'start_date', 'end_date', 'paymentComment'));
    }

    public function updatePackage(Request $request)
    {
        $request->validate([
            'family_id' => 'required|numeric',
            'package_amount' => 'required|numeric|min:0',
            'package_weeks' => 'required|string',
        ]);

        try {
            $family_id = $request->family_id;
            $packageAmount = trim($request->package_amount);
            $packageWeeks = trim($request->package_weeks);
            
            // Combine as "amount for weeks" format (convert weeks to lowercase)
            $weeks = strtolower($packageWeeks);
            $package = $packageAmount . ' for ' . $weeks;

            // Update admission table
            $admission = Admission::where('branch_id', session('branch_id'))
                ->where('familyno', $family_id)
                ->first();

            if ($admission) {
                $admission->feedetail = $package;
                $admission->save();
                }

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully in admission table',
                'package' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating package: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentComments(Request $request)
    {

        $request->validate([
            'payment_comment' => 'required',
            'family_id' => 'required|numeric',
        ]);

        // Get the current comment details based on the provided family_id
        $currentComment = DB::table('payment_comments')->where('branch_id', session('branch_id'))
            ->where('family_id', $request->family_id)
            ->first();

        // Dump and die to see the current comment details
        // dd($currentComment);

        // Update the comments field
        $affectedRows = DB::table('payment_comments')->where('branch_id', session('branch_id'))
            ->where('family_id', $request->family_id)
            ->update(['comments' => $request->payment_comment]);

        if ($affectedRows > 0) {
            // Show a success notification using SweetAlert
            return response()->json(['success' => true, 'message' => 'Comment updated successfully']);
        } else {
            // No rows were affected, meaning the comment with the given family_id was not found
            return response()->json(['error' => 'Comment not found for the given family_id'], 404);
        }
    }


    public function getIndividualReceipt($id)
    {
        $latestPayment = Payment::where('branch_id', session('branch_id'))->where('paymentid', $id)

            ->first();


        $pdf = new Dompdf();
        $pdf->setOptions(new Options([
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'margin-top' => '0mm',
            'margin-right' => '0mm',
            'margin-bottom' => '0mm',
            'margin-left' => '0mm',
        ]));
        $date = $currentDate = Carbon::now()->format('Y-m-d');

        $timestamp = time();
        $rand_num = rand(10, 999);
        $random = $timestamp . $rand_num;

        $year = date('y');
        $day = date('d');
        $month = date('m');
        $hour = date('H');
        $minute = date('i');
        $random = rand(0, 999);
        $random = $latestPayment->receipt_no; // $year . $day . $month . $hour . $minute;

        $from = $latestPayment->paymentfrom;
        $to = $latestPayment->paymentto;

        $receivedfrom = $latestPayment->paymentfamilyid;
        $receivedto = $latestPayment->collector;


        function numberToWords($number)
        {
            $words = [
                0 => 'zero',
                1 => 'one',
                2 => 'two',
                3 => 'three',
                4 => 'four',
                5 => 'five',
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine',
                10 => 'ten',
                11 => 'eleven',
                12 => 'twelve',
                13 => 'thirteen',
                14 => 'fourteen',
                15 => 'fifteen',
                16 => 'sixteen',
                17 => 'seventeen',
                18 => 'eighteen',
                19 => 'nineteen',
                20 => 'twenty',
                30 => 'thirty',
                40 => 'forty',
                50 => 'fifty',
                60 => 'sixty',
                70 => 'seventy',
                80 => 'eighty',
                90 => 'ninety'
            ];

            $number = str_replace(',', '', $number);
            $number = (int) $number;

            if ($number < 21) {
                return $words[$number];
            }

            if ($number < 100) {
                $tens = (int) ($number / 10) * 10;
                $units = $number % 10;
                return $words[$tens] . ($units ? '-' . $words[$units] : '');
            }

            if ($number < 1000) {
                $hundreds = (int) ($number / 100);
                $remainder = $number % 100;
                return $words[$hundreds] . ' hundred' . ($remainder ? ' and ' . numberToWords($remainder) : '');
            }

            if ($number < 1000000) {
                $thousands = (int) ($number / 1000);
                $remainder = $number % 1000;
                return numberToWords($thousands) . ' thousand' . ($remainder ? ' ' . numberToWords($remainder) : '');
            }

            if ($number < 1000000000) {
                $millions = (int) ($number / 1000000);
                $remainder = $number % 1000000;
                return numberToWords($millions) . ' million' . ($remainder ? ' ' . numberToWords($remainder) : '');
            }

            throw 'Number is too large to convert to words';
        }

        $paid = $latestPayment->paid;
        $amount_in_words = numberToWords($paid);
        // dd($amount_in_words);
        $feeAmount = $latestPayment->package;
        $thisPayment = $latestPayment->paid;
        $thisbalance = $latestPayment->balance = str_replace('-', '+', $latestPayment->balance);
        $paymentMethod = $latestPayment->payment_method;

        $date = date('d F Y', strtotime($latestPayment->paymentdate));
        $from = date('d F Y', strtotime($from));
        $to = date('d F Y', strtotime($to));


        $cash = $latestPayment->cash_payment ? 'Yes' : '';
        $adjustment = $latestPayment->adjustment ? 'Yes' : '';
        $bank = $latestPayment->bank_transfer ? 'Yes' : '';
        // $cash =  $latestPayment->payment_method == 'Cash Payment' ? 'Yes' : '';
        // $adjustment = $latestPayment->payment_method == 'Adjustment' ? 'Yes' : '';

        $data = [
            'date' => $date,
            'random' => $random,
            'from' => $from,
            'to' => $to,
            'receivedfrom' => $receivedfrom,
            'receivedto' => $receivedto,
            'amount_in_words' => $amount_in_words,
            'feeAmount' => $feeAmount,
            'thisPayment' => $thisPayment,
            'thisbalance' => $thisbalance,
            'paymentMethod' => $paymentMethod,
            // 'cash'=>$cash,'adjustment'=>$adjustment,
            'cash' => $cash,
            'adjustment' => $adjustment,
            'bank' => $bank,
            'paid' => $paid,
        ];

        $pdf->loadHtml(view('reports.receipt', $data));
        $pdf->render();
        $pdfOutput = $pdf->output();

        $pdfDataUri = 'data:application/pdf;base64,' . base64_encode($pdfOutput);

        return response()->json(['pdfDataUri' => $pdfDataUri]);
    }

    public function PrevDelete($id)
    {
        $paymentData = Payment::where('branch_id', session('branch_id'))->where('paymentid', $id)->first();
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        DB::table('deleted_payments')->insert([
            'paymentid' => $paymentData->paymentid,
            'paymentfamilyid' => $paymentData->paymentfamilyid,
            'paymentfrom' => $paymentData->paymentfrom,
            'paymentto' => $paymentData->paymentto,
            'paymentdate' => $paymentData->paymentdate,
            'package' => $paymentData->package,
            'paid' => $paymentData->paid,
            'balance' => $paymentData->balance,
            'payment_method' => $paymentData->payment_method,
            'collector' => $paymentData->collector,
            'payment_detail' => $paymentData->payment_detail,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        // Update the is_deleted field
        $payment = Payment::where('branch_id', session('branch_id'))->where('paymentid', $id)->update(['is_deleted' => now(), 'deleted_by' => Auth::user()->name,]);

        // Manually log the deletion activity
        if ($payment) {
            activity('Payment')
                ->performedOn($paymentData)
                ->causedBy(auth()->user())
                ->withProperties(['paymentid' => $id])
                ->event('deleted')
                ->log('Payment has been marked as deleted');

            return back();
        } else {
            abort(404);
        }
    }

    public function paymentExportForm()
    {
        return view('branchFrontend.payment.exportPayment');
    }

    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_id'     => 'required|integer',
            'payment_method' => 'required|in:Cash Payment,Card Payment,Bank Transfer,Adjustment',
        ]);

        $payment = \App\Models\Payment::where('paymentid', $request->payment_id)
            ->where('branch_id', session('branch_id'))
            ->firstOrFail();

        $total = $payment->paid;

        // Move total to selected method column, wipe others
        $payment->cash_payment  = $request->payment_method === 'Cash Payment'  ? $total : null;
        $payment->card_payment  = $request->payment_method === 'Card Payment'  ? $total : null;
        $payment->bank_transfer = $request->payment_method === 'Bank Transfer' ? $total : null;
        $payment->adjustment    = $request->payment_method === 'Adjustment'    ? $total : null;
        $payment->payment_method = $request->payment_method;
        $payment->save();

        return response()->json(['success' => true, 'message' => 'Payment method updated successfully.']);
    }

    public function showExportPayments(Request $request)    {
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date)));
        // dd(Carbon::parse($from_date)->format('m/d/Y'));
        // dd($from_date,$to_date);
        $payment_method = $request->payment_method;
        // dd($request->all(),$from_date,$to_date,Carbon::parse($from_date)->format('m/d/Y'),Carbon::parse($to_date)->format('m/d/Y'));
        
        $query = Payment::where('branch_id', session('branch_id'))
            ->whereBetween('paymentdate', [$from_date, $to_date]);
        
        // Filter based on selected payment method
        if ($payment_method == 'Cash Payment') {
            $query->whereNotNull('cash_payment')
                  ->where('cash_payment', '>', 0);
        } else if ($payment_method == 'Card Payment') {
            $query->whereNotNull('card_payment')
                  ->where('card_payment', '>', 0);
        } else if ($payment_method == 'Bank Transfer') {
            $query->whereNotNull('bank_transfer')
                  ->where('bank_transfer', '>', 0);
        } else if ($payment_method == 'Adjustment') {
            $query->whereNotNull('adjustment')
                  ->where('adjustment', '>', 0);
        }
        // If 'all' or no method selected, show all payments
        
        $payments = $query->orderByDesc('paymentid')
            ->orderByDesc('paymentdate')
            ->get();

        // Total based on selected payment method
        if ($payment_method == 'Cash Payment') {
            $total_sum = $payments->sum('cash_payment');
        } elseif ($payment_method == 'Card Payment') {
            $total_sum = $payments->sum('card_payment');
        } elseif ($payment_method == 'Bank Transfer') {
            $total_sum = $payments->sum('bank_transfer');
        } elseif ($payment_method == 'Adjustment') {
            $total_sum = $payments->sum('adjustment');
        } else {
            $total_sum = $payments->sum('paid');
        }
        // dd($payments);


        if (count($payments) < 1) {
            return view('branchFrontend.payment.exportPayment')->withErrors('No record found corresponding to these details.');
        } else {
            return view('branchFrontend.payment.exportPayment', compact('payments', 'total_sum'));
        }
    }

    public function defaulterList()
    {
        $paymentRecords = DB::table('payment')->where('branch_id', session('branch_id'))
            ->select(
                'paymentfamilyid',
                DB::raw('MAX(paymentdate) AS last_payment_date'),
                DB::raw('MAX(paymentto) AS payment_expiry_date'),
                'balance'
            )
            ->whereIn('paymentfamilyid', function ($query) {
                $query->select('familyno')
                    ->from('admission')
                    ->where('familystatus', 'Active');
            })
            // ->where('paymentto', '<', now())
            ->where(function ($query) {
                $query->where('paymentto', '<', now())
                    ->orWhere('paymentto', '<', \Carbon\Carbon::createFromFormat('m/d/Y', '01/01/2020'));
            })
            ->groupBy('paymentfamilyid', 'balance')
            ->orderBy('last_payment_date', 'desc')
            ->get();

        // Format payment_expiry_date using Carbon or PHP date functions
        foreach ($paymentRecords as $record) {
            $record->payment_expiry_date = $record->payment_expiry_date; // Replace with formatting logic
        }

        foreach ($paymentRecords as $record) {
            // Format last_payment_date
            if (!empty($record->last_payment_date)) {
                $lastPaymentDate = \DateTime::createFromFormat('Y-m-d', $record->last_payment_date);
                if ($lastPaymentDate !== false) {
                    $record->last_payment_date = $lastPaymentDate->format('d F Y');
                }
            }

            // Format payment_expiry_date
            if (!empty($record->payment_expiry_date)) {
                $expiryDate = \DateTime::createFromFormat('Y-m-d', $record->payment_expiry_date);
                if ($expiryDate !== false) {
                    $record->payment_expiry_date = $expiryDate->format('d F Y');
                }
            }
        }

        // Dump the updated collection
        // dd($paymentRecords->orderBy('last_payment_date', 'desc'));

        return view('branchFrontend.payment.defaulterList', compact('paymentRecords'));
    }

    public function paymentLogForm()
    {
        $users = User::where('branch_id', session('branch_id'))->get();
        // dd($users);
        return view('branchFrontend.payment.paymentLog', compact('users'));
    }

    public function paymentLogShow(Request $request)
    {
        // Debugging: Uncomment to inspect the request data
        // dd($request->all());

        // Validate the request
        $request->validate([
            'users' => 'required|array',
            'date' => 'required|date_format:Y-m-d'
        ]);

        // Get all users for the branch (for the view)
        $users = User::where('branch_id', session('branch_id'))->get();

        // Format the date
        $date = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));

        // Initialize the payments array
        $allPayments = collect(); // Using a Laravel Collection for easier manipulation

        // Get selected users
        $selectedUsers = $request->users;
        // dd($selectedUsers);
        // Check if 'all' is selected or process individual users
        if (in_array('all', $selectedUsers)) {
            // Fetch all payments for the branch and date
            $allPayments = Payment::where('branch_id', session('branch_id'))
                ->where('paymentdate', $date)
                ->orderByDesc('paymentid')
                ->get();
        } else {
            // Fetch payments for each selected user and merge them
            foreach ($selectedUsers as $user) {
                $payments = Payment::where('branch_id', session('branch_id'))
                    ->where('collector', $user)
                    ->where('paymentdate', $date)
                    ->orderByDesc('paymentid')
                    ->get();
                // dd($payments);
                $allPayments = $allPayments->merge($payments);
            }
        }
        // dd($allPayments);
        // Remove duplicates if necessary (e.g., if payments could overlap)
        // $allPayments = $allPayments->unique('paymentid');

        // Debugging: Uncomment to inspect the payments
        // dd($allPayments->toArray());

        // Return the view with payments and usersss
        return view('branchFrontend.payment.paymentLog', compact('allPayments', 'users'));
    }

    public function systemLogs(Request $request)
    {
        // Get all logs for current branch with filters
        $query = DB::table('activity_log')
            ->where('branch_id', session('branch_id'));

        // Create a separate query for getting available users (based on logs with same filters)
        $userQuery = DB::table('activity_log')
            ->where('branch_id', session('branch_id'))
            ->whereNotNull('causer_id');

        // Apply filters if provided
        if ($request->has('event') && $request->event != '') {
            $query->where('event', $request->event);
            $userQuery->where('event', $request->event);
        }

        if ($request->has('log_name') && $request->log_name != '') {
            $query->where('log_name', 'like', '%' . $request->log_name . '%');
            $userQuery->where('log_name', 'like', '%' . $request->log_name . '%');
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $query->whereDate('created_at', '>=', $dateFrom);
            $userQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
            $query->whereDate('created_at', '<=', $dateTo);
            $userQuery->whereDate('created_at', '<=', $dateTo);
        }

        if ($request->has('user_id') && $request->user_id != '' && $request->user_id != 'all') {
            $query->where('causer_id', $request->user_id);
            $userQuery->where('causer_id', $request->user_id);
        }

        // Get users who have logs matching current filters
        $userIdsWithLogs = $userQuery->distinct()->pluck('causer_id')->toArray();

        // Get users dynamically based on available logs
        $users = User::where('branch_id', session('branch_id'))
            ->whereIn('id', $userIdsWithLogs)
            ->orderBy('name', 'asc')
            ->get();

        // Get sorting parameters
        $sortColumn = $request->get('sort_column', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedColumns = ['id', 'log_name', 'description', 'event', 'created_at', 'causer_id'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortColumn, $sortDirection);

        // Paginate logs to handle large datasets (500 per page)
        $perPage = 500;
        $allLogs = $query->select('id', 'log_name', 'description', 'event', 'subject_type', 'causer_id', 'properties', 'created_at', 'subject_id')
            ->paginate($perPage);

        // Get unique events for dropdown (based on current filters, excluding user_id filter)
        $eventQuery = DB::table('activity_log')
            ->where('branch_id', session('branch_id'));

        if ($request->has('log_name') && $request->log_name != '') {
            $eventQuery->where('log_name', 'like', '%' . $request->log_name . '%');
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $eventQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
            $eventQuery->whereDate('created_at', '<=', $dateTo);
        }

        $availableEvents = $eventQuery->distinct()
            ->pluck('event')
            ->filter()
            ->sort()
            ->values();

        // Get unique log names (based on current filters, excluding log_name filter)
        $logNameQuery = DB::table('activity_log')
            ->where('branch_id', session('branch_id'));

        if ($request->has('event') && $request->event != '') {
            $logNameQuery->where('event', $request->event);
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $logNameQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
            $logNameQuery->whereDate('created_at', '<=', $dateTo);
        }

        $availableLogNames = $logNameQuery->distinct()
            ->pluck('log_name')
            ->filter()
            ->sort()
            ->values();

        return view('branchFrontend.log.index', compact('users', 'allLogs', 'availableEvents', 'availableLogNames'));
    }

    public function getFilteredUsers(Request $request)
    {
        // Create query for getting users based on current filters
        $userQuery = DB::table('activity_log')
            ->where('branch_id', session('branch_id'))
            ->whereNotNull('causer_id');

        // Apply same filters as main query
        if ($request->has('event') && $request->event != '') {
            $userQuery->where('event', $request->event);
        }

        if ($request->has('log_name') && $request->log_name != '') {
            $userQuery->where('log_name', 'like', '%' . $request->log_name . '%');
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $userQuery->whereDate('created_at', '>=', $dateFrom);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
            $userQuery->whereDate('created_at', '<=', $dateTo);
        }

        // Get user IDs who have logs matching filters
        $userIdsWithLogs = $userQuery->distinct()->pluck('causer_id')->toArray();

        // Get users
        $users = User::where('branch_id', session('branch_id'))
            ->whereIn('id', $userIdsWithLogs)
            ->orderBy('name', 'asc')
            ->select('id', 'name')
            ->get();

        return response()->json($users);
    }

    public function getLogsData(Request $request)
    {
        // Get all logs for current branch with filters
        $query = DB::table('activity_log')
            ->where('branch_id', session('branch_id'));

        // Apply filters if provided
        if ($request->has('event') && $request->event != '') {
            $query->where('event', $request->event);
        }

        if ($request->has('log_name') && $request->log_name != '') {
            $query->where('log_name', 'like', '%' . $request->log_name . '%');
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $dateFrom = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_from)));
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $dateTo = date('Y-m-d', strtotime(str_replace('/', '-', $request->date_to)));
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($request->has('user_id') && $request->user_id != '' && $request->user_id != 'all') {
            $query->where('causer_id', $request->user_id);
        }

        // Get sorting parameters
        $sortColumn = $request->get('sort_column', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validate sort column to prevent SQL injection
        $allowedColumns = ['id', 'log_name', 'description', 'event', 'created_at', 'causer_id'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'created_at';
        }

        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $query->orderBy($sortColumn, $sortDirection);

        // Paginate logs to handle large datasets (500 per page)
        $perPage = $request->get('per_page', 500);
        $page = $request->get('page', 1);

        // Get total count before pagination
        $totalCount = $query->count();

        // Get paginated logs
        $allLogs = $query->select('id', 'log_name', 'description', 'event', 'subject_type', 'causer_id', 'properties', 'created_at', 'subject_id')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Get all unique user IDs from logs to batch load users (optimize N+1 query)
        $userIds = $allLogs->pluck('causer_id')->filter()->unique()->toArray();
        $users = [];
        if (!empty($userIds)) {
            $users = User::whereIn('id', $userIds)->pluck('name', 'id')->toArray();
        }

        // Process logs for response
        $processedLogs = [];
        $seenEntries = [];

        foreach ($allLogs as $log) {
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

            $userName = 'No User';
            if ($log->causer_id && isset($users[$log->causer_id])) {
                $userName = $users[$log->causer_id];
            }

            $timestamp = $log->created_at ?? '';
            $formattedDate = '';
            if (!empty($timestamp)) {
                $formattedDate = \Carbon\Carbon::parse($timestamp)->format('d M Y H:i');
            }

            $subjectType = $log->subject_type ?? 'N/A';
            $subjectTypeDisplay = $subjectType != 'N/A' ? class_basename($subjectType) : 'N/A';

            // Decode properties
            $properties = [];
            if ($log->properties) {
                $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
            }

            $processedLogs[] = [
                'id' => $log->id,
                'log_name' => $log->log_name ?? 'N/A',
                'description' => $log->description ?? 'N/A',
                'event' => $log->event ?? 'N/A',
                'subject_type' => $subjectTypeDisplay,
                'username' => $userName,
                'created_at' => $formattedDate,
                'properties' => $properties,
                'unique_id' => $log->log_name . '_' . $log->description . '_' . $log->event . '_' . $log->causer_id
            ];
        }

        return response()->json([
            'logs' => $processedLogs,
            'count' => count($processedLogs),
            'total' => $totalCount,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($totalCount / $perPage),
            'has_more' => ($page * $perPage) < $totalCount
        ]);
    }

    public function showSystemLogs(Request $request)
    {
        $users = User::where('branch_id', session('branch_id'))->get();
        $request->validate([
            'users' => 'required',
            'date' => 'required'
        ]);

        $date = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));
        $selectedUsers = $request->users;
        $allLogs = array();

        $count = 0;
        if (count($selectedUsers) > 0) {
            foreach ($selectedUsers as $key => $user) {
                if ($user == 'all') {
                    // $allLogs = Activity::whereDate('created_at',$date)->select('id', 'log_name', 'description', 'event', 'causer_id', 'properties' ,'created_at')->get();
                    $allLogs = DB::table('activity_log')
                        ->whereDate('created_at', $date)
                        ->where('branch_id', session('branch_id'))
                        ->select('id', 'log_name', 'description', 'event', 'causer_id', 'properties', 'created_at')
                        ->get();
                    $count++;
                } else {
                    if ($count == 0) {
                        // $allLogs = Activity::whereDate('causer_id', $user)->where('created_at', '>=' , $date)->get();
                        $allLogs = DB::table('activity_log')->where('branch_id', session('branch_id'))
                            ->whereDate('causer_id', $user)
                            ->where('created_at', '>=', $date)
                            ->get();
                    }
                }
            }
        }


        return view('branchFrontend.log.index', compact('users', 'allLogs'));
    }



    public function indexDiaries(Request $request)
    {
        $notes = Note::where('branch_id', session('branch_id'))
            ->select('id', 'ref_no', 'name', 'message', 'received_by', 'message_for', 'created_at')
            ->get();

        return view('branchFrontend.diaries.index', compact('notes'));
    }

    public function storeDiaries(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'ref_no' => 'required|numeric',
            'received_by' => 'required|string|max:255',
            'message_for' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        Note::create([
            'ref_no' => $request->ref_no,
            'name' => auth()->user()->name,
            'message' => $request->message,
            'received_by' => $request->received_by,
            'message_for' => $request->message_for,
            'branch_name' => $branch->branch_name,
            'branch_id' => session('branch_id'),
        ]);

        return redirect()->route('user.note.index')->with('success', 'Note saved successfully!');
    }



    public function destroydiaries($id)
    {
        $note = Note::where('branch_id', session('branch_id'))->findOrFail($id);
        $note->delete();

        return redirect()->route('user.note.index')->with('success', 'Note deleted successfully!');
    }


    public function StudentReport()
    {
        return view('branchFrontend.report.studentReport');
    }


    public function getReport(Request $request)
    {

        $activeStudents = Admission::where('branch_id', session('branch_id'))
            // ->where('familystatus', 'Active')
            ->get();

        foreach ($activeStudents as $activeStudent) {
            $students = Student::where('branch_id', session('branch_id'))->where('student_status', 'active')->where('admissionid', strtolower($activeStudent->familyno))->get();

            foreach ($students as $student) {
                // Check if studentdob is not empty or invalid before formatting
                if (!empty($student->studentdob) && Carbon::hasFormat($student->studentdob, 'Y-m-d')) {
                    $student->studentdob = Carbon::parse($student->studentdob)->format('d F Y');
                } else {
                    // Handle the case where the date is empty or invalid
                    $student->studentdob = 'N/A';
                }
            }

            $activeStudent->details = $students;
        }

        return response()->json([
            'response' => $activeStudents,
        ]);
    }

    // public function getMedicalReport(Request $request)
    // {
    //     $admissionIds = Admission::where('branch_id', session('branch_id'))
    //     // ->where('familystatus', 'Active')
    //     ->pluck('familyno');

    //     $studentsWithCondition = Student::where('branch_id', session('branch_id'))->where('student_status','active')->whereIn('medical_condition', ['yes', 1])
    //         ->whereIn('admissionid', $admissionIds)
    //         ->get();

    //     // dd($studentsWithCondition);

    //     // Format the studentdob column and include medicalcondition from Admission
    //     $studentsWithCondition->transform(function ($student) {
    //         $admission = Admission::where('branch_id', session('branch_id'))->where('familyno', $student->admissionid)->first();
    //         $student->studentdob = Carbon::parse($student->studentdob)->format('d F Y');
    //         $student->medicalcondition = $admission ? $admission->medicalcondition : null;
    //         return $student;
    //     });

    //     return response()->json([
    //         'response' => $studentsWithCondition,
    //     ]);
    // }
    public function getMedicalReport(Request $request)
    {
        $admissionIds = Admission::where('branch_id', session('branch_id'))
            ->pluck('familyno');

        $studentsWithCondition = Student::where('branch_id', session('branch_id'))
            ->where('student_status', 'active')
            ->whereIn('medical_condition', ['yes', 1])
            ->whereIn('admissionid', $admissionIds)
            ->get();

        // Format the studentdob column and include medicalcondition, additionalNeeds, and allergies
        $studentsWithCondition->transform(function ($student) {
            $admission = Admission::where('branch_id', session('branch_id'))
                ->where('familyno', $student->admissionid)
                ->first();

            $medicalCondition = medical_condition::where('branch_id', session('branch_id'))->where('student_id', $student->studentid)
                ->where('family_id', $student->admissionid)
                ->first();

            $student->studentdob = Carbon::parse($student->studentdob)->format('d F Y');
            $student->medicalcondition = $admission ? $admission->medicalcondition : null;
            $student->additionalNeeds = $student->additionalNeeds ?? null;
            $student->allergies = $medicalCondition ? $medicalCondition->allergies : null;

            return $student;
        });

        return response()->json([
            'response' => $studentsWithCondition,
        ]);
    }


    public function getfamilyReport(Request $request, $id)
    {
        $activeStudents = Admission::where('branch_id', session('branch_id'))->where('familyno', $id)->get();

        foreach ($activeStudents as $activeStudent) {
            $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $activeStudent->familyno)->get();

            // Format the studentdob column or set to null if it's null
            $students->transform(function ($student) {
                if ($student->studentdob) {
                    $student->studentdob = Carbon::parse($student->studentdob)->format('d F Y');
                } else {
                    $student->studentdob = null;
                }
                return $student;
            });

            $activeStudent->details = $students;
        }

        return response()->json([
            'response' => $activeStudents,
        ]);
    }


    public function teacherReport(Request $request)
    {
        // dd($request->all);
        $staffReport = null;
        if ($request->report_type == 'staffReport') {
            $staffReport = Attendance::where('branch_id', session('branch_id'))
                ->where('teacher_name', $request->teacher_name)
                ->whereBetween('date', [
                    \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromDate)->startOfDay()->toDateString(),
                    \Carbon\Carbon::createFromFormat('Y-m-d', $request->toDate)->endOfDay()->toDateString(),
                ])
                ->get();
            // dd($staffReport,$request->teacher_name);
        }

        $totalTeacherCount = null;
        $attendence = null;
        $condition = false;
        $teacher = null;
        if ($request->report_type == 'staffSession') {
            $teacher = $request->teacher_name; // Convert to uppercase
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate);
            $toDate = Carbon::createFromFormat('Y-m-d', $request->toDate);
            $daysDifference = $toDate->diffInDays($fromDate);
            $dayNames = [];

            for ($i = 0; $i <= $daysDifference; $i++) {
                $dayNames[] = strtoupper($fromDate->copy()->addDays($i)->format('l')); // Convert to uppercase
            }

            $result = DB::table('timetable')
                ->where('branch_id', session('branch_id'))
                ->select('teachers', 'day')
                ->selectRaw("CAST((LENGTH(teachers) - LENGTH(REPLACE(teachers, ?, ''))) / LENGTH(?) AS SIGNED) AS teacher_count", [$teacher, $teacher])
                ->distinct()
                ->where(function ($query) use ($teacher, $dayNames) {
                    $query->whereRaw('FIND_IN_SET(?, teachers) > 0', [$teacher])
                        ->whereIn(DB::raw('UPPER(day)'), $dayNames);
                })
                ->get();

            $totalTeacherCount = $result->sum('teacher_count');

            $attendence = Attendance::where('branch_id', session('branch_id'))
                ->where('teacher_name', $request->teacher_name)
                ->whereBetween('date', [
                    \Carbon\Carbon::createFromFormat('Y-m-d', $request->fromDate)->startOfDay()->toDateString(),
                    \Carbon\Carbon::createFromFormat('Y-m-d', $request->toDate)->endOfDay()->toDateString(),
                ])
                ->count();

            $condition = true;
        }

        $teacherNames = \DB::table('teachers_subject')
            ->where('branch_id', session('branch_id'))
            ->where('is_available', 'yes')
            ->pluck('teacher_name');

        return view('branchFrontend.report.teacherReport', compact('teacherNames', 'staffReport', 'totalTeacherCount', 'attendence', 'condition', 'teacher'));
    }

    public function ActiveInactive()
    {
        return view('branchFrontend.report.activeInactiveStudent');
    }

    // public function getActiveInactive(Request $request)
    // {
    //     dd($request->all());
    //     $status = $request->input('status');
    //     $branchId = session('branch_id');

    //     // Validate status input
    //     if (!in_array($status, ['active', 'inactive'])) {
    //         return response()->json(['error' => 'Invalid status'], 400);
    //     }

    //     // Subquery to get the latest attendance date per family_id and student_name
    //     $attendanceSubquery = DB::table('attendance')
    //         ->select('family_id', 'student_name', DB::raw('MAX(date) as latest_attendance_date'))
    //         ->where('branch_id', '=', $branchId)
    //         ->groupBy('family_id', 'student_name');

    //     // Main query
    //     $studentData = DB::table('studentdata')
    //         ->where('studentdata.branch_id', '=', $branchId)
    //         ->where('student_status', $status)
    //         ->join('admission', function ($join) use ($branchId) {
    //             $join->on('studentdata.admissionid', '=', 'admission.familyno')
    //                 ->where('admission.branch_id', '=', $branchId);
    //         })
    //         ->leftJoinSub($attendanceSubquery, 'attendance', function ($join) {
    //             $join->on('admission.familyno', '=', 'attendance.family_id')
    //                 ->on(DB::raw('CONCAT(studentdata.studentname, " ", studentdata.studentsur)'), '=', 'attendance.student_name');
    //         })
    //         ->select(
    //             'studentdata.*',
    //             'admission.joiningdate',
    //             'admission.familystatus',
    //             'attendance.latest_attendance_date',
    //             DB::raw('? as recordType')
    //         )
    //         ->addBinding(ucfirst($status), 'select');

    //     $students = $studentData->get();

    //     // Date validation and formatting
    //     foreach ($students as $student) {
    //         $invalidDateValues = ['//', 'N/A', null, '', '0000-00-00'];

    //         // Helper function to format dates
    //         $formatDate = function ($date) use ($invalidDateValues) {
    //             if (in_array($date, $invalidDateValues)) {
    //                 return null;
    //             }
    //             try {
    //                 return Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
    //             } catch (\Exception $e) {
    //                 try {
    //                     return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    //                 } catch (\Exception $e) {
    //                     return null;
    //                 }
    //             }
    //         };

    //         $student->studentdob = $formatDate($student->studentdob);
    //         $student->joiningdate = $formatDate($student->joiningdate);
    //         $student->latest_attendance_date = $formatDate($student->latest_attendance_date);
    //     }

    //     return response()->json(['paymentRecords' => $students]);
    // }
    // public function getActiveInactive(Request $request)
    // {
    //     $status = $request->input('status', '');
    //     $branchId = session('branch_id');

    //     // Subquery to get the latest attendance date per family_id and student_name
    //     $attendanceSubquery = DB::table('attendance')
    //         ->select('family_id', 'student_name', DB::raw('MAX(date) as latest_attendance_date'))
    //         ->where('branch_id', '=', $branchId)
    //         ->groupBy('family_id', 'student_name');

    //     // Main query
    //     $studentData = DB::table('studentdata')
    //         ->where('studentdata.branch_id', '=', $branchId)
    //         ->when($status, function ($query) use ($status) {
    //             if ($status === 'active') {
    //                 $query->whereIn(DB::raw('LOWER(student_status)'), ['active'])
    //                       ->orWhereNull('student_status')
    //                       ->orWhere('student_status', '');
    //             } else {
    //                 $query->whereIn(DB::raw('LOWER(student_status)'), ['inactive']);
    //             }
    //         })
    //         ->join('admission', function ($join) use ($branchId) {
    //             $join->on('studentdata.admissionid', '=', 'admission.familyno')
    //                  ->where('admission.branch_id', '=', $branchId);
    //         })
    //         ->leftJoinSub($attendanceSubquery, 'attendance', function ($join) {
    //             $join->on('admission.familyno', '=', 'attendance.family_id')
    //                  ->on(DB::raw('TRIM(CONCAT(COALESCE(studentdata.studentname, ""), " ", COALESCE(studentdata.studentsur, "")))'), '=', DB::raw('TRIM(attendance.student_name)'));
    //         })
    //         ->select(
    //             'studentdata.studentid',
    //             'studentdata.studentname',
    //             'studentdata.studentsur',
    //             'studentdata.studentdob',
    //             'studentdata.studentgender',
    //             'studentdata.studentyearinschool',
    //             'studentdata.admissionid',
    //             'studentdata.student_status as recordType',
    //             'admission.joiningdate',
    //             'admission.familystatus',
    //             'attendance.latest_attendance_date'
    //         );

    //     $students = $studentData->get();

    //     // Date validation and formatting
    //     foreach ($students as $student) {
    //         $invalidDateValues = ['//', 'N/A', null, '', '0000-00-00'];

    //         // Helper function to format dates
    //         $formatDate = function ($date) use ($invalidDateValues) {
    //             if (in_array($date, $invalidDateValues)) {
    //                 return null;
    //             }
    //             try {
    //                 return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    //             } catch (\Exception $e) {
    //                 try {
    //                     return Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
    //                 } catch (\Exception $e) {
    //                     return null;
    //                 }
    //             }
    //         };

    //         $student->studentdob = $formatDate($student->studentdob);
    //         $student->joiningdate = $formatDate($student->joiningdate);
    //         $student->latest_attendance_date = $formatDate($student->latest_attendance_date);
    //     }

    //     return response()->json(['paymentRecords' => $students]);
    // }
    //   public function getActiveInactive(Request $request)
    // {
    //     $status = $request->input('status', '');
    //     $branchId = session('branch_id');

    //     // Subquery to get the latest attendance date per family_id and student_name
    //     $attendanceSubquery = DB::table('attendance')
    //         ->select('family_id', 'student_name', DB::raw('MAX(date) as latest_attendance_date'))
    //         ->where('branch_id', '=', $branchId)
    //         ->groupBy('family_id', 'student_name');

    //     // Main query
    //     $studentData = DB::table('studentdata')
    //         ->where('studentdata.branch_id', '=', $branchId)
    //         ->when($status, function ($query) use ($status) {
    //             if ($status === 'active') {
    //                 $query->whereIn(DB::raw('LOWER(student_status)'), ['active'])
    //                       ->orWhereNull('student_status')
    //                       ->orWhere('student_status', '');
    //             } else {
    //                 $query->whereIn(DB::raw('LOWER(student_status)'), ['inactive']);
    //             }
    //         })
    //         ->join('admission', function ($join) use ($branchId) {
    //             $join->on('studentdata.admissionid', '=', 'admission.familyno')
    //                  ->where('admission.branch_id', '=', $branchId);
    //         })
    //         ->leftJoinSub($attendanceSubquery, 'attendance', function ($join) {
    //             $join->on('admission.familyno', '=', 'attendance.family_id')
    //                  ->on(DB::raw('TRIM(CONCAT(COALESCE(studentdata.studentname, ""), " ", COALESCE(studentdata.studentsur, "")))'), '=', DB::raw('TRIM(attendance.student_name)'));
    //         })
    //         ->select(
    //             'studentdata.studentid',
    //             'studentdata.studentname',
    //             'studentdata.studentsur',
    //             'studentdata.studentdob',
    //             'studentdata.studentgender',
    //             'studentdata.studentyearinschool',
    //             'studentdata.admissionid',
    //             'studentdata.student_status as recordType',
    //             'admission.joiningdate',
    //             'admission.familystatus',
    //             'attendance.latest_attendance_date'
    //         );

    //     $students = $studentData->get();

    //     // Date validation and formatting
    //     foreach ($students as $student) {
    //         $invalidDateValues = ['//', 'N/A', null, '', '0000-00-00'];

    //         // Helper function to format dates
    //         $formatDate = function ($date) use ($invalidDateValues) {
    //             if (in_array($date, $invalidDateValues)) {
    //                 return null;
    //             }
    //             try {
    //                 return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
    //             } catch (\Exception $e) {
    //                 try {
    //                     return Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
    //                 } catch (\Exception $e) {
    //                     return null;
    //                 }
    //             }
    //         };

    //         $student->studentdob = $formatDate($student->studentdob);
    //         $student->joiningdate = $formatDate($student->joiningdate);
    //         $student->latest_attendance_date = $formatDate($student->latest_attendance_date);
    //     }

    //     return response()->json(['paymentRecords' => $students]);
    // }
  public function getActiveInactive(Request $request)
{
    $status = strtolower(trim($request->input('status', '')));
    if ($status === 'all') {
        $status = '';
    }
    $search = $request->input('search', '');
    $branchId = session('branch_id');

    if (!$branchId) {
        return response()->json([
            'paymentRecords' => [],
            'counts' => ['active' => 0, 'inactive' => 0, 'unset' => 0],
            'message' => 'Branch ID missing in session',
        ], 422);
    }

    // 1. Attendance subquery – branch_id filter laga diya
    $attendanceSubquery = DB::table('attendance')
        ->select('family_id', 'student_name', DB::raw('MAX(date) as latest_attendance_date'))
        ->where('branch_id', '=', $branchId)
        ->groupBy('family_id', 'student_name');

    // 2. Admission subquery – branch_id filter yahan bhi daal diya
    $admissionSubquery = DB::table('admission')
        ->select(
            'familyno',
            'branch_id',
            DB::raw('MAX(joiningdate) as joiningdate'),
            DB::raw('MAX(familystatus) as familystatus')
        )
        ->where('branch_id', '=', $branchId)  // ← YEH ADD KIYA
        ->groupBy('familyno', 'branch_id');

    // 3. Main query – pehle se hi branch_id filter hai + baaki jagah bhi safe
    $studentData = DB::table('studentdata')
        ->where('studentdata.branch_id', '=', $branchId) // ← already tha
        ->when($status, function ($query) use ($status) {
            $query->where(function ($statusQuery) use ($status) {
                if ($status === 'active') {
                    $statusQuery->whereRaw('LOWER(student_status) = ?', ['active']);
                } elseif ($status === 'inactive') {
                    $statusQuery->whereRaw('LOWER(student_status) = ?', ['inactive']);
                } elseif (in_array($status, ['unset', 'not_set', 'empty'])) {
                    $statusQuery->where(function ($q) {
                        $q->whereNull('student_status')
                          ->orWhere('student_status', '');
                    });
                }
            });
        })
        ->when($search, function ($query) use ($search) {
            $searchTerm = '%' . strtolower(trim($search)) . '%';
            $exactSearchTerm = strtolower(trim($search));
            $query->where(function ($q) use ($searchTerm, $exactSearchTerm) {
                $q->whereRaw('LOWER(studentdata.studentid) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.studentname) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.studentsur) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.studentdob) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.studentgender) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.studentyearinschool) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.admissionid) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(attendance.latest_attendance_date) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(studentdata.student_status) = ?', [$exactSearchTerm]);
            });
        })
        ->joinSub($admissionSubquery, 'admission', function ($join) {
            // yahan branch_id filter subquery mein already laga hua hai, isliye join mein nahi daalna
            $join->on('studentdata.admissionid', '=', 'admission.familyno');
        })
        ->leftJoinSub($attendanceSubquery, 'attendance', function ($join) {
            $join->on('admission.familyno', '=', 'attendance.family_id')
                 ->on(DB::raw('TRIM(CONCAT(COALESCE(studentdata.studentname, ""), " ", COALESCE(studentdata.studentsur, "")))'), '=', DB::raw('TRIM(attendance.student_name)'));
        })
        ->select(
            'studentdata.studentid',
            'studentdata.studentname',
            'studentdata.studentsur',
            'studentdata.studentdob',
            'studentdata.subject_names',
            'studentdata.studentyearinschool',
            'studentdata.admissionid',
            'studentdata.student_status as recordType',
            'admission.joiningdate',
            'admission.familystatus',
            'attendance.latest_attendance_date'
        );

    $students = $studentData->get();

    // Date formatting (unchanged
    foreach ($students as $student) {
        $invalidDateValues = ['//', 'N/A', null, '', '0000-00-00'];

        $formatDate = function ($date) use ($invalidDateValues) {
            if (in_array($date, $invalidDateValues)) return null;
            try {
                return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
            } catch (\Exception $e) {
                try {
                    return Carbon::createFromFormat('d/m/Y', $date)->format('d/m/Y');
                } catch (\Exception $e) {
                    return null;
                }
            }
        };

        $student->studentdob = $formatDate($student->studentdob);
        $student->joiningdate = $formatDate($student->joiningdate);
        $student->latest_attendance_date = $formatDate($student->latest_attendance_date);
    }

    // 4. Counts query mein bhi branch_id filter (already tha lekin confirm)
    $counts = DB::table('studentdata')
        ->where('branch_id', '=', $branchId)
        ->selectRaw("SUM(CASE WHEN LOWER(COALESCE(student_status, '')) = 'active' THEN 1 ELSE 0 END) as active_count")
        ->selectRaw("SUM(CASE WHEN LOWER(COALESCE(student_status, '')) = 'inactive' THEN 1 ELSE 0 END) as inactive_count")
        ->first();

    return response()->json([
        'paymentRecords' => $students,
        'counts' => [
            'active' => (int) ($counts->active_count ?? 0),
            'inactive' => (int) ($counts->inactive_count ?? 0),
        ],
    ]);
}


  public function openManualCreatePage()
{
    $subjects = Subject::where('branch_id', session('branch_id'))->get();
    $students = Student::where('branch_id', session('branch_id'))->get();
   $all_teachers = DB::table('teachers_subject')
        ->select('teacher_name')
        ->where('branch_id', session('branch_id'))
        ->where('is_available', 'yes')
        ->distinct('teacher_name')
        ->get();
    return view('branchFrontend.tests.addManualTest', compact('students', 'subjects', 'all_teachers'));
}




public function openTestSubmissionTrackerPage(Request $request)
{
    $branch_id = session('branch_id');
    if (!$branch_id) {
        return redirect()->back()->with('error', 'Branch ID not found in session.');
    }

    $date_from = $request->input('date_from');
    $date_to = $request->input('date_to');
    $teacher_name = $request->input('teacher_name'); // Dropdown (exact match)
    $teacher_search = $request->input('teacher_search'); // Input box (partial match)

    // Get branch name
    $branch_name = DB::table('teachers_subject')
        ->where('branch_id', $branch_id)
        ->value('branch_name') ?? 'Unknown Branch';

    // Query to get teacher submission data
    $teacher_submissions_query = DB::table('student_tests')
        ->select(
            'tutor as teacher_name',
            DB::raw('COUNT(*) as test_count'),
            DB::raw('MAX(test_date) as last_submission_date')
        )
        ->where('branch_id', $branch_id);

    // Apply date filters
    if ($date_from) {
        $teacher_submissions_query->where('test_date', '>=', $date_from);
    }
    if ($date_to) {
        $teacher_submissions_query->where('test_date', '<=', $date_to);
    }

    // Apply teacher name filter: prioritize dropdown (exact match), then input box (partial match)
    if ($teacher_name) {
        $teacher_submissions_query->where('tutor', '=', trim($teacher_name));
    } elseif ($teacher_search) {
        $teacher_submissions_query->where('tutor', 'LIKE', '%' . trim($teacher_search) . '%');
    }

    // Group by teacher name and get results
    $teacher_submissions = $teacher_submissions_query
        ->groupBy('tutor')
        ->get();

    // Get all available teachers for dropdown and data merging
    $all_teachers = DB::table('teachers_subject')
        ->select('teacher_name')
        ->where('branch_id', $branch_id)
        ->where('is_available', 'yes')
        ->distinct()
        ->get()
        ->pluck('teacher_name')
        ->map(fn($name) => trim($name)) // Keep original case for dropdown
        ->unique()
        ->values();

    // Merge teachers with submission data
    $teacher_data = $all_teachers->map(function ($teacher_name) use ($teacher_submissions) {
        $submission = $teacher_submissions->firstWhere(
            fn($item) => strtolower(trim($item->teacher_name)) === strtolower(trim($teacher_name))
        );
        return (object) [
            'teacher_name' => ucwords($teacher_name),
            'test_count' => $submission ? $submission->test_count : 0,
            'last_submission_date' => $submission ? $submission->last_submission_date : null,
        ];
    })->filter(function ($teacher) use ($teacher_name, $teacher_search) {
        // Apply filter: dropdown (exact match) or input box (partial match)
        if ($teacher_name) {
            return strtolower($teacher->teacher_name) === strtolower(trim($teacher_name));
        } elseif ($teacher_search) {
            return stripos($teacher->teacher_name, trim($teacher_search)) !== false;
        }
        return true; // No filter applied
    })->values();

    return view(
        'branchFrontend.tests.openTestSubmissionTrackerPage',
        compact('teacher_data', 'branch_name', 'all_teachers')
    );
}

    public function storeManualTest(Request $request)
    {
        if ($request->filled('date')) {
            $request->merge([
                'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
            ]);
        }
        // dd($request->all());

        $validatedData = $request->validate([
            'family_id'   => 'required',
            'subject'     => 'required',
            'book'        => 'required',
            'test_no'     => 'required',
            'attempt'     => 'required',
            'date'        => 'required',
            'percentage'  => 'required',
            'status'      => 'required',
            'tutor'       => 'required',
            'updated_by'  => 'required',
        ]);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        try {
            StudentTest::create([
                'family_id'         => $request->family_id,
                'student_name'      => $request->student_name,
                'book'              => $request->book,
                'test_no'           => $request->test_no,
                'attempt'           => $request->attempt,
                'test_date'         => $request->date, // change 'date' to 'test_date'
                'percentage'        => $request->percentage,
                'status'            => $request->status,
                'tutor'             => $request->tutor,
                'tutor_updated_by'  => $request->updated_by,
                'subject'           => $request->subject,
                'branch_name'       => $branch->branch_name,
                'branch_id'       => $branch->branch_id,
            ]);

            $request->session()->flash('alert-success', 'Test added successfully');
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }

        return redirect()->back();
    }

    public function getFamilyStudents($familyId)
    {
        $students = Student::where('branch_id', session('branch_id'))
            ->where('admissionid', $familyId)
            // ->where('student_status','active')
            // ->orWhereNull('student_status')
            ->get();

        // dd($students);


        return $students;
    }

    public function createTest()
    {
        return view('branchFrontend.tests.importTests');
    }

    public function importtests(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        try {
            if ($file) {
                try {
                    $importedData = Excel::toArray(null, $file)[0];

                    $customDateFormat = 'm/d/Y'; // Adjust the format based on your Excel date format

                    foreach ($importedData as &$row) {
                        // Assuming the date column is at index 5
                        $dateValue = $row[5];

                        // Check if the value looks like a date
                        if (is_numeric($dateValue)) {
                            $row[5] = Carbon::createFromTimestamp(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateValue))->format($customDateFormat);
                        } else {
                            $row[5] = $dateValue;
                        }
                    }

                    // Retrieve branch information
                    $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
                    $branch_id = $branch->branch_id;
                    $branch_name = $branch->branch_name;

                    // Transform each row into a new array with the desired structure
                    $dataArray = [];

                    foreach ($importedData as $index => $row) {
                        // Skip the first row
                        if ($index === 0) {
                            continue;
                        }

                        // Skip empty rows
                        if (empty(array_filter($row))) {
                            continue;
                        }

                        // Check if the row has at least 10 columns
                        if (count($row) >= 10) {
                            if (!empty($row[3])) {
                                $dataArray[] = [
                                    'family_id' => intval($row[0] ?? null),
                                    'student_name' => $row[1] ?? null,
                                    'book' => $row[2] ?? null,
                                    'test_no' => $row[3] ?? null,
                                    'attempt' => $row[4] ?? null,
                                    'test_date' => $row[5] ?? null,
                                    'percentage' => is_numeric($row[6]) ? ($row[6] * 100) . '%' : null,
                                    'status' => $row[7] ?? null,
                                    'subject' => $row[8] ?? null,
                                    'tutor' => $row[9] ?? null,
                                    'tutor_updated_by' => $row[10] ?? null,
                                    'branch_id' => $branch_id, // Add branch_id
                                    'branch_name' => $branch_name, // Add branch_name
                                ];
                            }
                        }
                    }

                    // Insert data into the database in chunks to avoid memory issues
                    $chunkSize = 100; // Adjust this value based on your needs
                    $dataArrayChunks = array_chunk($dataArray, $chunkSize);

                    foreach ($dataArrayChunks as $chunk) {
                        DB::table('student_tests')->insert($chunk);
                    }

                    $request->session()->flash('alert-success', 'Import added successfully');
                } catch (\Exception $ex) {
                    Log::error($ex);
                    return redirect()->back()->withErrors('Please adjust your columns in the sheet');
                }
            }

            return response()->json(['message' => 'Import added successfully']);
        } catch (\Exception $ex) {
            // Log the exception for debugging purposes
            Log::error($ex);
            return response()->json(['error' => 'An error occurred during file processing.']);
        }
    }


    public function indexTests(Request $request)
    {
        // dd($request->all());
        // Check if branch_id exists in session
        if (!session()->has('branch_id')) {
            return redirect()->route('some.route')->with('error', 'Branch ID not found in session');
        }

        $branch_id = session('branch_id');

        $students = Student::where('branch_id', $branch_id)->get();
        $subjects = Subject::where('branch_id', $branch_id)->get();

        $student_tests = StudentTest::where('branch_id', $branch_id)
            ->select('id', 'family_id', 'student_name', 'subject', 'book', 'test_no', 'attempt', 'test_date', 'percentage', 'status', 'tutor', 'tutor_updated_by');

        // Apply filters
        if ($request->filled('family_id')) {
            $student_tests->where(function ($query) use ($request) {
                $query->where('family_id', $request->family_id)
                    ->orWhere('family_id', 0);
            });
        }

        if ($request->filled('student_name')) {
            // dd($request->student_name);
            $student_tests->where('student_name', 'LIKE', '%' . $request->student_name . '%');
        }

        if ($request->filled('subject_id')) {
            $student_tests->where('subject', $request->subject_id);
        }

        // Date filters
        if ($request->filled('date_from') || $request->filled('date_to')) {
            try {
                if ($request->filled('date_from')) {
                    $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                    $student_tests->where('test_date', '>=', $dateFrom);
                }

                if ($request->filled('date_to')) {
                    $dateTo = Carbon::parse($request->date_to)->endOfDay();
                    $student_tests->where('test_date', '<=', $dateTo);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Invalid date format');
            }
        }

        $student_tests = $student_tests->orderBy('test_date', 'asc')
            ->orderBy('test_no', 'asc')
            ->paginate(100);

        return view('branchFrontend.tests.showTest', compact('student_tests', 'students', 'subjects'));
    }

   public function editTest($id)
{
    $test = StudentTest::where('branch_id', session('branch_id'))->where('id', $id)->first();
    $subjects = Subject::where('branch_id', session('branch_id'))->get();
    $all_teachers = DB::table('teachers_subject')
        ->select('teacher_name')
        ->where('branch_id', session('branch_id'))
        ->where('is_available', 'yes')
        ->distinct()
        ->get();
    return view('branchFrontend.tests.editTest', compact('test', 'subjects', 'all_teachers'));
}

    public function updateTest(Request $request)
    {
        if ($request->filled('date')) {
            try {
                $request->merge([
                    'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                // agar already Y-m-d format hai to skip
            }
        }
        // dd($request->all());
        // Assuming 'id' is present in the form data
        $id = $request->id;

        // Find the record with the given ID
        $test = StudentTest::where('branch_id', session('branch_id'))->find($id);
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        if ($test) {
            // Update the fields with the new values
            $test->family_id = $request->family_id;
            $test->student_name = $request->student_name;
            $test->subject = $request->subject;
            $test->book = $request->book;
            $test->test_no = $request->test_no;
            $test->attempt = $request->attempt;
            $test->test_date = $request->date;
            $test->percentage = $request->percentage;
            $test->status = $request->status;
            $test->tutor = $request->tutor;
            $test->tutor_updated_by = $request->updated_by;
            $test->branch_name = $branch->branch_name;
            $test->branch_id = $branch->branch_id;

            // Save the updated record
            $test->save();

            return redirect('student-tests/view-records')->with('success', 'Test updated successfully');
        } else {
            // Handle the case where the record with the given ID is not found
            return  redirect('student-tests/view-records')->with('error', 'Test not found');
        }
    }

    public function deleteTest($id)
    {
        $test = StudentTest::where('branch_id', session('branch_id'))->find($id);

        // Check if the test exists
        if ($test) {
            // Delete the test
            $test->delete();

            // Optionally, you can return a response or redirect
            return redirect('student-tests/view-records')->with('success', 'Test updated successfully');
        } else {
            // Test not found, handle accordingly
            return  redirect('student-tests/view-records')->with('error', 'Test not found');
        }
    }


    public function exportTests(Request $request)
    {
        // Check if branch_id exists in session for consistency
        if (!session()->has('branch_id')) {
            return response()->json(['error' => 'Branch ID not found in session'], 400);
        }

        $students = Student::where('branch_id', session('branch_id'))->get();
        $student_tests = StudentTest::where('branch_id', session('branch_id'))
            ->select('id', 'family_id', 'student_name', 'subject', 'book', 'test_no', 'attempt', 'test_date', 'percentage', 'status', 'tutor', 'tutor_updated_by');

        // Apply filters with null checks
        if ($request->filled('family_id') && $request->family_id !== null && $request->family_id !== 'null') {
            $student_tests->where(function ($query) use ($request) {
                $query->where('family_id', $request->family_id)
                    ->orWhere('family_id', 0);
            });
        }

        if ($request->filled('student_name') && $request->student_name !== null && $request->student_name !== 'null') {
            $student_tests->where('student_name', $request->student_name); // Exact match
        }

        if ($request->filled('subject_id') && $request->subject_id !== null && $request->subject_id !== 'null') {
            $student_tests->where('subject', $request->subject_id); // Exact match
        }

        // Date filters with null checks
        if ($request->filled('date_from') || $request->filled('date_to')) {
            try {
                if ($request->filled('date_from') && $request->date_from !== null && $request->date_from !== 'null') {
                    $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                    $student_tests->where('test_date', '>=', $dateFrom);
                }

                if ($request->filled('date_to') && $request->date_to !== null && $request->date_to !== 'null') {
                    $dateTo = Carbon::parse($request->date_to)->endOfDay();
                    $student_tests->where('test_date', '<=', $dateTo);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid date format'], 400);
            }
        }

        // Retrieve the results
        $results = $student_tests->get();

        // Export logic
        $export = new class($results) implements FromCollection, WithHeadings {
            protected $studentTests;

            public function __construct($studentTests)
            {
                $this->studentTests = $studentTests;
            }

            public function collection()
            {
                return $this->studentTests->map(function ($test) {
                    return [
                        $test->family_id,
                        $test->student_name,
                        $test->subject,
                        $test->book,
                        $test->test_no,
                        $test->attempt,
                        !empty($test->test_date) && $test->test_date !== 'NM'
                            ? (function () use ($test) {
                                try {
                                    return Carbon::parse($test->test_date)->format('d/m/Y');
                                } catch (\Exception $e) {
                                    return $test->test_date; // Return original date on error
                                }
                            })()
                            : $test->test_date, // Return as is if empty or 'NM'
                        $test->percentage,
                        $test->status,
                        $test->tutor,
                        $test->tutor_updated_by,
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'Family ID',
                    'Student Name',
                    'Subject',
                    'Book',
                    'Test No',
                    'Attempt',
                    'Test Date',
                    'Percentage',
                    'Status',
                    'Tutor',
                    'Tutor Updated By',
                ];
            }
        };

        // Export the results to Excel
        return Excel::download($export, 'student_tests.xlsx');
    }

    public function teacherComment(Request $request)
    {
        $comments = Comment::where('branch_id', session('branch_id'))->select('id', 'family_id', 'student_name', 'comment')->get();

        if ($request->ajax()) {
            return Datatables::of($comments)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $deleteUrl = url('teacher/commentdestroy', $row->id);
                    $btn = '
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" title="Edit" class="btn btn-sm btn-primary edit editButton">Edit </a>
                    <a href="' . $deleteUrl . '" data-toggle="tooltip"  data-id="' . $row->id . '" title="Delete" class="btn btn-sm btn-danger del deleteButton">Delete </a>
                        ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }


        return view('branchFrontend.tests.teacherComment', compact('comments'));
    }


    public function TeacherStore(Request $request)
    {
        $comment = $request->comment;
        $family_id = $request->family_id;
        $student_name = $request->student;
        $commentor = auth()->user()->name;

        try {
            if ($family_id) {
                $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

                Comment::create([
                    'comment' => $comment,
                    'family_id' => $family_id,
                    'student_name' => $student_name,
                    'commentor' => $commentor,
                    'branch_name' => $branch->branch_name,
                    'branch_id' => $branch->branch_id,

                ]);



                return redirect()->route('comment.index')->with('success', 'Comment saved successfully.');
            }
        } catch (\Exception $ex) {
            return response()->json([
                'message' => $ex
            ], 201);
        }
    }

    public function getStudents(Request $request)
    {
        $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $request->family_id)->get();
        return $students;
    }

    public function commentstore(Request $request)
    {
        $commentId = $request->comment_id;

        // Find the comment by ID
        $comment = Comment::where('branch_id', session('branch_id'))->find($commentId);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        // Update the comment attributes
        $comment->family_id = $request->family_id;
        $comment->comment = $request->comment;
        $comment->branch_name = $branch->branch_name;
        $comment->branch_id = $branch->branch_id;

        // Save the updated comment
        $comment->save();


        return redirect()->route('comment.index')->with('success', 'Comment updated successfully');
    }

    public function commentDelete($id)
    {
        try {
            $comment = Comment::where('branch_id', session('branch_id'))->find($id);

            if (!$comment) {
                return redirect()->back()->with('error', 'Comment not found.');
            }

            $comment->delete();

            return redirect()->route('comment.index')->with('success', 'Comment deleted successfully.');
        } catch (\Exception $ex) {
            return redirect()->back()->with('error', 'Failed to delete comment: ' . $ex->getMessage());
        }
    }

    public function addLearner()
    {
        $currentMonth = date('n');
        $currentYear = date('Y');

        if ($currentMonth >= 9 && $currentMonth <= 12) {
            $session = "Autumn " . $currentYear;
        } else {
            $session = "Spring " . $currentYear;
        }
        return view('branchFrontend.IAG.AddLearner', compact('session'));
    }


    public function learnerRequest()
    {
        $learners = IAGLearnerRequest::where('branch_id', session('branch_id'))->where('is_approved', 0)->get();
        return view('branchFrontend.IAG.requestLEarner', compact('learners'));
    }

    public function getFamilyRecords(Request $request)
    {
        $family_id = $request->family_id;

        // Check if this family is blocked
        $admission = Admission::where('branch_id', session('branch_id'))
            ->where('familyno', $family_id)
            ->first();

        if ($admission && $admission->is_blocked) {
            return response()->json([
                'blocked' => true,
                'message' => 'Family ID Blocked. Please contact admin office for more details.',
            ], 403);
        }

        $students = Student::where('branch_id', session('branch_id'))->where('student_status','active')->whereBetween('studentyearinschool', [10, 13])->where('admissionid', $family_id)->get();

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found within year between 10 and 13 for the given family ID.'], 404);
        }

        $fullNames = $students->map(function ($student) {
            return $student->studentname . ' ' . $student->studentsur;
        });

        return response()->json($fullNames);
    }

    public function storeLearnerReq(Request $request)
    {
        $request->validate([
            'family_id' => 'required',
            'learner_name' => 'required',
            'learner_name_template' => 'required',
            'staff_lead_name' => 'required',
            'category' => 'required',
            'courses_subjects_being_studied' => 'required',
            'meeting_date' => 'required|date',
            'career_next_steps' => 'required',
            'interested_fields' => 'required',
            'researched_application_process_deadlines' => 'required',
            'clear_go_information' => 'required',
            'is_helpful_information' => 'required',
            'started_application' => 'required',
            'need_help_in_application' => 'required',
            'visited_our_resources_on_line' => 'required',
            'resources_in_career_library' => 'required',
            'IAG_lerner_plan_1' => 'required',
            'IAG_lerner_plan_2' => 'required',
            'IAG_lerner_plan_3' => 'required',
            'learner_on_secure_pathway' => 'required',
        ]);

        $existingLearner = IAGLearnerRequest::where('family_id', $request->family_id)->where('is_approved', 0)
            ->where('learner_name', $request->learner_name)
            ->first();

        if ($existingLearner) {
            return back()->withErrors(['learner_name' => 'Learner for this candidate has already been submitted.']);
        }
        $filePaths1 = [];
        $filePaths2 = [];
        $filePaths3 = [];

        $uploadDirectory = public_path('uploads');

        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        if ($request->hasFile('file_upload_one')) {
            foreach ($request->file('file_upload_one') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths1[] = 'uploads/' . $fileName;
            }
        }

        if ($request->hasFile('file_upload_two')) {
            foreach ($request->file('file_upload_two') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths2[] = 'uploads/' . $fileName;
            }
        }

        if ($request->hasFile('file_upload_three')) {
            foreach ($request->file('file_upload_three') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths3[] = 'uploads/' . $fileName;
            }
        }

        $filePaths1String = implode(',', $filePaths1);
        $filePaths2String = implode(',', $filePaths2);
        $filePaths3String = implode(',', $filePaths3);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        $learner = IAGLearnerRequest::create([
            'family_id' => $request->family_id,
            'learner_name' => $request->learner_name,
            'learner_name_template' => $request->learner_name_template,
            'staff_lead_name' => $request->staff_lead_name,
            'category' => $request->category,
            'courses_subjects_being_studied' => $request->courses_subjects_being_studied,
            'meeting_date' => $request->meeting_date,
            'career_next_steps' => $request->career_next_steps,
            'interested_fields' => $request->interested_fields,
            'researched_application_process_deadlines' => $request->researched_application_process_deadlines,
            'clear_go_information' => $request->clear_go_information,
            'is_helpful_information' => $request->is_helpful_information,
            'started_application' => $request->started_application,
            'need_help_in_application' => $request->need_help_in_application,
            'visited_our_resources_on_line' => $request->visited_our_resources_on_line,
            'resources_in_career_library' => $request->resources_in_career_library,
            'IAG_lerner_plan_1' => $request->IAG_lerner_plan_1,
            'IAG_lerner_plan_2' => $request->IAG_lerner_plan_2,
            'IAG_lerner_plan_3' => $request->IAG_lerner_plan_3,
            'learner_on_secure_pathway' => $request->learner_on_secure_pathway,
            'file_input_1' => $filePaths1String,
            'file_input_2' => $filePaths2String,
            'file_input_3' => $filePaths3String,
            'is_approved' => 0,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        return back()->with('success', 'Learner Request Received Successfully');
    }

    public function storeLearner(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'family_id' => 'required',
            'learner_name' => 'required',
            'current_session' => 'required',
            'learner_name_template' => 'required',
            'staff_lead_name' => 'required',
            'category' => 'required',
            'courses_subjects_being_studied' => 'required',
            'meeting_date' => 'required',
            'career_next_steps' => 'required',
            'interested_fields' => 'required',
            'researched_application_process_deadlines' => 'required',
            'clear_go_information' => 'required',
            'is_helpful_information' => 'required',
            'started_application' => 'required',
            'need_help_in_application' => 'required',
            'visited_our_resources_on_line' => 'required',
            'resources_in_career_library' => 'required',
            'IAG_lerner_plan_1' => 'required',
            'IAG_lerner_plan_2' => 'required',
            'IAG_lerner_plan_3' => 'required',
            'learner_on_secure_pathway' => 'required',
        ]);
        // dd($request->meeting_date);

        $existingLearner = IagMeeting::where('family_id', $request->family_id)
            ->where('learner_name', $request->learner_name)
            ->where('current_session', $request->current_session)
            ->first();

        if ($existingLearner) {
            return back()->withErrors(['learner_name' => 'Learner for this candidate has already been submitted for the current session.']);
        }
        $filePaths1 = [];
        $filePaths2 = [];
        $filePaths3 = [];

        $uploadDirectory = public_path('uploads');

        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        if ($request->hasFile('file_upload_one')) {
            foreach ($request->file('file_upload_one') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths1[] = 'uploads/' . $fileName;
            }
        }

        if ($request->hasFile('file_upload_two')) {
            foreach ($request->file('file_upload_two') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths2[] = 'uploads/' . $fileName;
            }
        }

        if ($request->hasFile('file_upload_three')) {
            foreach ($request->file('file_upload_three') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDirectory, $fileName);
                $filePaths3[] = 'uploads/' . $fileName;
            }
        }

        $filePaths1String = implode(',', $filePaths1);
        $filePaths2String = implode(',', $filePaths2);
        $filePaths3String = implode(',', $filePaths3);

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $meetingDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->meeting_date)->format('Y-m-d');

        $learner = IagMeeting::create([
            'family_id' => $request->family_id,
            'current_session' => $request->current_session,
            'learner_name' => $request->learner_name,
            'learner_name_template' => $request->learner_name_template,
            'staff_lead_name' => $request->staff_lead_name,
            'category' => $request->category,
            'courses_subjects_being_studied' => $request->courses_subjects_being_studied,
            'meeting_date' => $meetingDate, //$request->meeting_date,
            'career_next_steps' => $request->career_next_steps,
            'interested_fields' => $request->interested_fields,
            'researched_application_process_deadlines' => $request->researched_application_process_deadlines,
            'clear_go_information' => $request->clear_go_information,
            'is_helpful_information' => $request->is_helpful_information,
            'started_application' => $request->started_application,
            'need_help_in_application' => $request->need_help_in_application,
            'visited_our_resources_on_line' => $request->visited_our_resources_on_line,
            'resources_in_career_library' => $request->resources_in_career_library,
            'IAG_lerner_plan_1' => $request->IAG_lerner_plan_1,
            'IAG_lerner_plan_2' => $request->IAG_lerner_plan_2,
            'IAG_lerner_plan_3' => $request->IAG_lerner_plan_3,
            'learner_on_secure_pathway' => $request->learner_on_secure_pathway,
            'file_input_1' => $filePaths1String,
            'file_input_2' => $filePaths2String,
            'file_input_3' => $filePaths3String,
            'branch_id' => $branch->branch_id,
            'branch_name' => $branch->branch_name,
        ]);

        return back()->with('success', 'Learner Request Received Successfully');
    }

    public function viewLearner()
    {
        return view('branchFrontend.IAG.viewLearner');
    }

    public function getFamilyData(Request $request)
    {
        // dd($request->all());
        $familyId = $request->input('family_id');
        $learnerName = $request->input('learner_name');
        $session = $request->input('session');

        $data = IagMeeting::where('branch_id', session('branch_id'))->where('family_id', $familyId)->where('current_session', $session)
            ->where('learner_name', $learnerName)
            ->first();

        $data->term_name = json_decode($data->term_name, true);
        $data->staff_lead = json_decode($data->staff_lead, true);
        $data->date = json_decode($data->date, true);
        $data->meeting_notes = json_decode($data->meeting_notes, true);
        $data->secure_pathway = json_decode($data->secure_pathway, true);
        $data->iag_target1 = json_decode($data->iag_target1, true);
        $data->deadline = json_decode($data->deadline, true);
        $data->iag_target2 = json_decode($data->iag_target2, true);
        $data->iag_target3 = json_decode($data->iag_target3, true);
        $data->file = json_decode($data->file, true);
        // Check if data was found
        if ($data) {
            // Return the found data as JSON
            return response()->json($data);
        } else {
            // Return a message if no data was found
            return response()->json(['message' => 'Learner request not received'], 404);
        }
        return response()->json(['message' => 'Learner request not received'], 404);
    }

    // public function learnerReport(Request $request)
    // {
    //     // $meetings = IagMeeting::where('branch_id', session('branch_id'))->get();
    //     $meetings = IagMeeting::where('branch_id', session('branch_id'))
    //         ->whereIn('family_id', function ($query) {
    //             $query->select('admissionid')
    //                 ->from('studentdata')
    //                 ->whereBetween('studentyearinschool', [10, 13]);
    //         })
    //         ->get();

    //     $meetings = $meetings->map(function ($meeting) {
    //         // Fetch the student record
    //         $student = Student::where('branch_id', session('branch_id'))->whereBetween('studentyearinschool', [10, 13])->where('admissionid', $meeting->family_id)->first();

    //         if ($student) {
    //             // Fetch the guardian record
    //             $guardian = Guardian::where('branch_id', session('branch_id'))->where('guardianid', $student->guardianid)->first();

    //             // Embed the relevant student and guardian details into the meeting

    //             $meeting->guardian_email = $guardian->guardiantel ?? null;
    //         }

    //         return $meeting;
    //     });

    //     $currentSessions = IagMeeting::where('branch_id', session('branch_id'))
    //         ->distinct()
    //         ->pluck('current_session')
    //         ->filter() // remove null/empty
    //         ->values();
    //     // dd($meetings);

    //     return view('branchFrontend.IAG.reportLearner', compact('meetings','currentSessions'));
    // }

    // public function learnerReport(Request $request)
    // {
    //     // -----------------------------------------------------------------
    //     // 0. Determine current academic session (your original logic)
    //     // -----------------------------------------------------------------
    //     $currentMonth = now()->month;
    //     $currentYear  = now()->year;

    //     if ($currentMonth >= 9 && $currentMonth <= 12) {
    //         $currentSession = "Autumn $currentYear";
    //     } else {
    //         $currentSession = "Spring $currentYear";
    //     }

    //     // -----------------------------------------------------------------
    //     // 1. Families that have BOTH Autumn 2025 & Spring 2026
    //     // -----------------------------------------------------------------
    //     $validFamilies = DB::table('iag_meetings')
    //         ->select('family_id', 'learner_name')
    //         ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->groupBy('family_id', 'learner_name')
    //         ->havingRaw('COUNT(DISTINCT current_session) = 2')
    //         ->get();

    //     $validFamilyIds = $validFamilies->pluck('family_id')->unique();

    //     // -----------------------------------------------------------------
    //     // 2. BOTH‑SESSION records (with guardian email)
    //     // -----------------------------------------------------------------
    //     $bothComplete = IagMeeting::with(['student.guardian'])
    //         ->whereIn('family_id', $validFamilyIds)
    //         ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->whereHas(
    //             'student',
    //             fn($q) => $q
    //                 ->whereRaw('CAST(studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //                 ->whereRaw("TRIM(UPPER(CONCAT(COALESCE(studentname,''), ' ', COALESCE(studentsur,''))))
    //                         = TRIM(UPPER(iag_meetings.learner_name))")
    //         )
    //         ->get()
    //         ->map(function ($m) {
    //             $m->full_student_name = trim("{$m->student?->studentname} {$m->student?->studentsur}");
    //             $m->guardian_email    = $m->student?->guardian?->guardiantel ?? '';
    //             return $m;
    //         });

    //     // -----------------------------------------------------------------
    //     // 3. SINGLE‑SESSION (Autumn 2025) – families with only ONE session
    //     // -----------------------------------------------------------------
    //     $singleSessionAutumn = IagMeeting::with(['student.guardian'])
    //         ->where('current_session', 'Autumn 2025')
    //         ->whereHas(
    //             'student',
    //             fn($q) => $q
    //                 ->whereRaw('CAST(studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //                 ->whereRaw("TRIM(UPPER(CONCAT(COALESCE(studentname,''), ' ', COALESCE(studentsur,''))))
    //                         = TRIM(UPPER(iag_meetings.learner_name))")
    //         )
    //         ->whereIn('family_id', function ($q) {
    //             $q->select('family_id')
    //                 ->from('iag_meetings')
    //                 ->groupBy('family_id', 'learner_name')
    //                 ->havingRaw('COUNT(DISTINCT current_session) = 1');
    //         })
    //         ->get()
    //         ->map(function ($m) {
    //             $m->full_student_name = trim("{$m->student?->studentname} {$m->student?->studentsur}");
    //             $m->guardian_email    = $m->student?->guardian?->guardiantel ?? '';
    //             return $m;
    //         });

    //     // -----------------------------------------------------------------
    //     // 4. COUNTS (you already had these – keep them unchanged)
    //     // -----------------------------------------------------------------
    //     $bothCompleteCount = IagMeeting::whereIn('family_id', $validFamilyIds)
    //         ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //         ->whereRaw("TRIM(UPPER(CONCAT(COALESCE(sd.studentname,''), ' ', COALESCE(sd.studentsur,''))))
    //                     = TRIM(UPPER(iag_meetings.learner_name))")
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->distinct('iag_meetings.family_id')
    //         ->count('iag_meetings.family_id');

    //     $singleSessionAutumnCount = IagMeeting::where('current_session', 'Autumn 2025')
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //         ->whereRaw("TRIM(UPPER(CONCAT(COALESCE(sd.studentname,''), ' ', COALESCE(sd.studentsur,''))))
    //                     = TRIM(UPPER(iag_meetings.learner_name))")
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->whereIn('iag_meetings.family_id', function ($q) {
    //             $q->select('family_id')
    //                 ->from('iag_meetings')
    //                 ->groupBy('family_id', 'learner_name')
    //                 ->havingRaw('COUNT(DISTINCT current_session) = 1');
    //         })
    //         ->count();

    //     // -----------------------------------------------------------------
    //     // 5. Merge for the table (your Blade expects $meetings)
    //     // -----------------------------------------------------------------
    //     // dd($bothComplete, $singleSessionAutumn);
    //     $meetings = $bothComplete->merge($singleSessionAutumn);
    //     $totalCount = $bothCompleteCount + $singleSessionAutumnCount;

    //     // -----------------------------------------------------------------
    //     // 6. Return view
    //     // -----------------------------------------------------------------
    //     return view('branchFrontend.IAG.reportLearner', compact(
    //         'meetings',
    //         'bothComplete',
    //         'singleSessionAutumn',
    //         'bothCompleteCount',
    //         'singleSessionAutumnCount',
    //         'totalCount',
    //         'bothComplete',
    //         'singleSessionAutumn',
    //     ));
    // }




    //  public function learnerReport(Request $request)
    // {
    //     /////////////////////////////////////////////////////
    //     $currentMonth = now()->month;
    //     $currentYear = now()->year;

    //     if ($currentMonth >= 9 && $currentMonth <= 12) {
    //         // September → December
    //         $currentSession = "Autumn " . $currentYear;
    //     } else {
    //         // January → August
    //         // session year = current year (if before September) but part of the *next* academic cycle
    //         $currentSession = "Spring " . $currentYear;
    //     }



    //     ///////////////////////////////////////////both session completed start//////////////////////////////
    //     $validFamilies = DB::table('iag_meetings')
    //         ->select('family_id', 'learner_name')
    //         ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->groupBy('family_id', 'learner_name')
    //         ->havingRaw('COUNT(DISTINCT current_session) = 2')
    //         ->get();

    //     // Now extract just the family_ids (but only those with matching learner_name)
    //     $validFamilyIds = $validFamilies->pluck('family_id')->unique();

    //     // Main query
    //     $bothComplete = IagMeeting::query()
    //         ->whereIn('family_id', $validFamilyIds)
    //         ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //         ->join('guardian as g', 'sd.guardianid', '=', 'g.Guardianid') // Join guardian table
    //         ->select('iag_meetings.*')
    //         ->selectRaw('sd.studentyearinschool AS student_year_in_school')  // ADD THIS
    //         ->selectRaw("COALESCE(g.guardianmob, g.guardiantel, '') AS guardiantel") // Pick mobile first, then tel
    //         ->selectRaw("
    //     CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
    //     ")
    //         ->whereRaw("
    //         TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, ''))))
    //         = TRIM(UPPER(iag_meetings.learner_name))
    //     ")
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->get();

    //     // dd($bothComplete);


    //     ///////////////////////////////////////////both session completed end//////////////////////////////

    //     /////////////////////////////////////////////both session completed count start//////////////////////////////

    //     $bothCompleteCount = IagMeeting::query()
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //         ->whereIn('iag_meetings.current_session', ['Autumn 2025', 'Spring 2026'])
    //         ->whereRaw("
    //     TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, ''))))
    //     = TRIM(UPPER(iag_meetings.learner_name))
    // ")
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->whereIn('iag_meetings.family_id', function ($query) {
    //             $query->select('family_id')
    //                 ->from('iag_meetings')
    //                 ->whereIn('current_session', ['Autumn 2025', 'Spring 2026'])
    //                 ->groupBy('family_id', 'learner_name')
    //                 ->havingRaw('COUNT(DISTINCT current_session) = 2');
    //         })
    //         ->distinct('iag_meetings.family_id', 'iag_meetings.learner_name') // unique per family + name
    //         ->count();

    //     // dd($bothCompleteCount);

    //     /////////////////////////////////////////////both session completed count end//////////////////////////////


    //     ///////////////////////////////////////////single session completed start//////////////////////////////
    //     $singleSessionAutumn = IagMeeting::query()
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //          ->join('guardian as g', 'sd.guardianid', '=', 'g.Guardianid') // Join guardian table
    //         ->select(
    //            '*'
    //         )
    //         ->whereRaw("
    //     TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, ''))))
    //     = TRIM(UPPER(iag_meetings.learner_name))
    // ")
    //         ->where('iag_meetings.current_session', 'Autumn 2025')
    //         ->selectRaw('sd.studentyearinschool AS student_year_in_school')  // ADD THIS
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->selectRaw("COALESCE(g.guardianmob, g.guardiantel, '') AS guardiantel") // Pick mobile first, then tel

    //         ->whereIn('iag_meetings.family_id', function ($query) {
    //             $query->select('family_id')
    //                 ->from('iag_meetings')
    //                 ->groupBy('family_id', 'learner_name')
    //                 ->havingRaw('COUNT(DISTINCT current_session) = 1'); // only 1 session
    //         })
    //         ->get();

    //     // dd($singleSessionAutumn);


    //     ///////////////////////////////////////////single session completed end//////////////////////////////


    //     ///////////////////////////////////////////single session completed start//////////////////////////////
    //     $singleSessionAutumnCount = IagMeeting::query()
    //         ->join('studentdata as sd', 'iag_meetings.family_id', '=', 'sd.admissionid')
    //         ->select(
    //             'iag_meetings.id',
    //             'iag_meetings.family_id',
    //             'iag_meetings.learner_name',
    //             'iag_meetings.current_session',
    //             'sd.studentyearinschool'
    //         )
    //         ->whereRaw("
    //     TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, ''))))
    //     = TRIM(UPPER(iag_meetings.learner_name))
    // ")
    //         ->where('iag_meetings.current_session', 'Autumn 2025')
    //         ->whereRaw('CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13')
    //         ->whereIn('iag_meetings.family_id', function ($query) {
    //             $query->select('family_id')
    //                 ->from('iag_meetings')
    //                 ->groupBy('family_id', 'learner_name')
    //                 ->havingRaw('COUNT(DISTINCT current_session) = 1'); // only 1 session
    //         })
    //         ->count();

    //     // dd($singleSessionAutumnCount);
    //     // dd($bothComplete,$bothCompleteCount,$singleSessionAutumn->take(2),$singleSessionAutumnCount);



    //     ///////////////////////////////////////////single session completed end//////////////////////////////


    //         $greenRows =  $bothComplete;
    //         $orangeRows = $singleSessionAutumn;
    //         $completedBothTermCount = $bothCompleteCount;
    //         $completedSingleTermCount = $singleSessionAutumnCount;
    //         $totalCount = $completedSingleTermCount+$completedBothTermCount;


    //         // dd($greenRows);
    //     return view('branchFrontend.IAG.reportLearner', compact('greenRows','orangeRows','singleSessionAutumn','completedSingleTermCount','completedBothTermCount','totalCount'));
    // }

    public function learnerReport(Request $request)
    {
        $branchId = session('branch_id');

        // === Correct Academic Terms ===
        $now = now(); // e.g., 2025-11-01
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Academic year starts in September
        $academicYearStart = ($currentMonth >= 9) ? $currentYear : $currentYear - 1;
        $autumnTerm = "Autumn {$academicYearStart}";     // e.g., Autumn 2025
        $springTerm = "Spring " . ($academicYearStart + 1); // e.g., Spring 2026

        // ==================================================================
        // 1. GREEN COUNT: Unique students with BOTH terms
        // ==================================================================
        $completedBothTermCount = DB::selectOne("
        SELECT COUNT(DISTINCT CONCAT(m.family_id, '|', m.learner_name)) AS count
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'active'
        WHERE m.branch_id = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(sd.studentname, ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
          AND EXISTS (
              SELECT 1 FROM iag_meetings m3
              WHERE m3.family_id = m.family_id
                AND m3.learner_name = m.learner_name
                AND m3.branch_id = ?
                AND m3.current_session = ?
          )
    ", [$branchId, $branchId, $branchId, $autumnTerm, $branchId, $springTerm])->count ?? 0;

        // ==================================================================
        // 2. ORANGE COUNT: Unique students with ONLY Autumn
        // ==================================================================
        $completedSingleTermCount = DB::selectOne("
        SELECT COUNT(DISTINCT CONCAT(m.family_id, '|', m.learner_name)) AS count
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'active'
        WHERE m.branch_id = ?
          AND m.current_session = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(sd.studentname, ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND NOT EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
    ", [$branchId, $branchId, $autumnTerm, $branchId, $springTerm])->count ?? 0;

        $totalCount = $completedBothTermCount + $completedSingleTermCount;

        // ==================================================================
        // 3. GREEN ROWS: All meetings for students with BOTH (no JOIN guardian in rows if not needed)
        // ==================================================================
    //     $greenResult = DB::select("
    //     SELECT DISTINCT
    //         m.*,
    //         sd.studentyearinschool AS student_year_in_school,
    //         CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
    //     FROM iag_meetings m
    //     JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'active'
    //     WHERE m.branch_id = ?
    //       AND m.current_session IN (?, ?)
    //       AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
    //       AND TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
    //       AND EXISTS (
    //           SELECT 1 FROM iag_meetings m2
    //           WHERE m2.family_id = m.family_id
    //             AND m2.learner_name = m.learner_name
    //             AND m2.branch_id = ?
    //             AND m2.current_session = ?
    //       )
    //       AND EXISTS (
    //           SELECT 1 FROM iag_meetings m3
    //           WHERE m3.family_id = m.family_id
    //             AND m3.learner_name = m.learner_name
    //             AND m3.branch_id = ?
    //             AND m3.current_session = ?
    //       )
    //     ORDER BY m.learner_name, m.current_session
    // ", [$branchId, $branchId, $autumnTerm, $springTerm, $branchId, $autumnTerm, $branchId, $springTerm]);
    $greenResult = DB::select("
    SELECT DISTINCT
        m.*,
        sd.studentyearinschool AS student_year_in_school,
        CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
    FROM iag_meetings m
    JOIN studentdata sd 
        ON m.family_id = sd.admissionid 
        AND sd.branch_id = ? 
        AND sd.student_status = 'active'
    WHERE m.branch_id = ?
      AND m.current_session IN (?, ?)
      AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
      AND TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, '')))) 
          = TRIM(UPPER(m.learner_name))
    ORDER BY m.learner_name, m.current_session
", [$branchId, $branchId, $autumnTerm, $springTerm]);
        // ==================================================================
        // 4. ORANGE ROWS: Meetings for ONLY Autumn students
        // ==================================================================
        $orangeResult = DB::select("
        SELECT DISTINCT
            m.*,
            sd.studentyearinschool AS student_year_in_school,
            CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'active'
        WHERE m.branch_id = ?
          AND m.current_session = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND NOT EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
        ORDER BY m.learner_name
    ", [$branchId, $branchId, $autumnTerm, $branchId, $springTerm]);

        // ==================================================================
        // 5. Collections & Return
        // ==================================================================
        $greenRows  = collect($greenResult)->map(fn($row) => (object) $row);
        $orangeRows = collect($orangeResult)->map(fn($row) => (object) $row);
        // dd($greenRows);
        return view('branchFrontend.IAG.reportLearner', compact(
            'greenRows',
            'orangeRows',
            'completedBothTermCount',
            'completedSingleTermCount',
            'totalCount',
            'autumnTerm',
            'springTerm'
        ));
    }
      public function alumni(Request $request)
    {
        $branchId = session('branch_id');

        // === Correct Academic Terms ===
        $now = now(); // e.g., 2025-11-01
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Academic year starts in September
        $academicYearStart = ($currentMonth >= 9) ? $currentYear : $currentYear - 1;
        $autumnTerm = "Autumn {$academicYearStart}";     // e.g., Autumn 2025
        $springTerm = "Spring " . ($academicYearStart + 1); // e.g., Spring 2026

        // ==================================================================
        // 1. GREEN COUNT: Unique students with BOTH terms
        // ==================================================================
        $completedBothTermCount = DB::selectOne("
        SELECT COUNT(DISTINCT CONCAT(m.family_id, '|', m.learner_name)) AS count
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'inactive'
        WHERE m.branch_id = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(sd.studentname, ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
          AND EXISTS (
              SELECT 1 FROM iag_meetings m3
              WHERE m3.family_id = m.family_id
                AND m3.learner_name = m.learner_name
                AND m3.branch_id = ?
                AND m3.current_session = ?
          )
    ", [$branchId, $branchId, $branchId, $autumnTerm, $branchId, $springTerm])->count ?? 0;

        // ==================================================================
        // 2. ORANGE COUNT: Unique students with ONLY Autumn
        // ==================================================================
        $completedSingleTermCount = DB::selectOne("
        SELECT COUNT(DISTINCT CONCAT(m.family_id, '|', m.learner_name)) AS count
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'inactive'
        WHERE m.branch_id = ?
          AND m.current_session = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(sd.studentname, ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND NOT EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
    ", [$branchId, $branchId, $autumnTerm, $branchId, $springTerm])->count ?? 0;

        $totalCount = $completedBothTermCount + $completedSingleTermCount;

        // ==================================================================
        // 3. GREEN ROWS: All meetings for students with BOTH (no JOIN guardian in rows if not needed)
        // ==================================================================
        $greenResult = DB::select("
        SELECT DISTINCT
            m.*,
            sd.studentyearinschool AS student_year_in_school,
            CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'inactive'
        WHERE m.branch_id = ?
          AND m.current_session IN (?, ?)
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
          AND EXISTS (
              SELECT 1 FROM iag_meetings m3
              WHERE m3.family_id = m.family_id
                AND m3.learner_name = m.learner_name
                AND m3.branch_id = ?
                AND m3.current_session = ?
          )
        ORDER BY m.learner_name, m.current_session
    ", [$branchId, $branchId, $autumnTerm, $springTerm, $branchId, $autumnTerm, $branchId, $springTerm]);

        // ==================================================================
        // 4. ORANGE ROWS: Meetings for ONLY Autumn students
        // ==================================================================
        $orangeResult = DB::select("
        SELECT DISTINCT
            m.*,
            sd.studentyearinschool AS student_year_in_school,
            CONCAT(TRIM(COALESCE(sd.studentname, '')), ' ', TRIM(COALESCE(sd.studentsur, ''))) AS full_student_name
        FROM iag_meetings m
        JOIN studentdata sd ON m.family_id = sd.admissionid AND sd.branch_id = ? AND sd.student_status = 'inactive'
        WHERE m.branch_id = ?
          AND m.current_session = ?
          AND CAST(sd.studentyearinschool AS UNSIGNED) BETWEEN 10 AND 13
          AND TRIM(UPPER(CONCAT(COALESCE(sd.studentname, ''), ' ', COALESCE(sd.studentsur, '')))) = TRIM(UPPER(m.learner_name))
          AND NOT EXISTS (
              SELECT 1 FROM iag_meetings m2
              WHERE m2.family_id = m.family_id
                AND m2.learner_name = m.learner_name
                AND m2.branch_id = ?
                AND m2.current_session = ?
          )
        ORDER BY m.learner_name
    ", [$branchId, $branchId, $autumnTerm, $branchId, $springTerm]);

        // ==================================================================
        // 5. Collections & Return
        // ==================================================================
        $greenRows  = collect($greenResult)->map(fn($row) => (object) $row);
        $orangeRows = collect($orangeResult)->map(fn($row) => (object) $row);
        // dd($greenRows);
        return view('branchFrontend.IAG.alumni', compact(
            'greenRows',
            'orangeRows',
            'completedBothTermCount',
            'completedSingleTermCount',
            'totalCount',
            'autumnTerm',
            'springTerm'
        ));
    }

    public function sendLearnerEmail(Request $request)
    {
        // Retrieve input data
        $familyId = $request->input('family_id');
        $learnerName = $request->input('learner_name');

        // Find the learner in the IagMeeting model
        $learner = IagMeeting::where('branch_id', session('branch_id'))->where('family_id', $familyId)
            ->where('learner_name', 'like', '%' . $learnerName . '%')
            ->first();

        // Return error if learner not found
        if (!$learner) {
            return response()->json(['error' => 'Learner not found'], 404);
        }

        // Initialize FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Calculate logo position
        $pageWidth = $pdf->GetPageWidth();
        $logoWidth = 50 * 0.9;
        $logoHeight = 35 * 0.9;
        $logoX = ($pageWidth - $logoWidth) / 2;

        // Use local logo file
        $logoPath = public_path('img/datesheetLogo.png');

        // Verify logo file exists
        if (!file_exists($logoPath)) {
            return response()->json(['error' => 'Logo file not found at ' . $logoPath], 500);
        }

        // Add logo to PDF with error handling
        try {
            $pdf->Image($logoPath, $logoX, 8, $logoWidth, $logoHeight);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add logo to PDF: ' . $e->getMessage()], 500);
        }

        // Add spacing after logo
        $pdf->Ln(20);

        // Set title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'IAG Meeting Summary', 0, 1, 'C');

        // Set font for content
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 8);

        // Add learner details to PDF
        $pdf->Cell(50, 10, 'Family ID:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->family_id, 1, 'L');

        $pdf->Cell(50, 10, 'Learner Name:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->learner_name, 1, 'L');

        $pdf->Cell(50, 10, 'Destination:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->destination ?? 'N/A', 1, 'L');

        $pdf->Cell(50, 10, 'Learner Name Template:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->learner_name_template, 1, 'L');

        $pdf->Cell(50, 10, 'Staff Lead Name:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->staff_lead_name, 1, 'L');

        $pdf->Cell(50, 10, 'Category:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->category, 1, 'L');

        $pdf->Cell(50, 10, 'Courses/Subjects Being Studied:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->courses_subjects_being_studied, 1, 'L');

        $pdf->Cell(50, 10, 'Meeting Date:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, Carbon::parse($learner->meeting_date)->format('d/m/Y'), 1, 'L');

        $pdf->Cell(50, 10, 'Career Next Steps:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->career_next_steps, 1, 'L');

        $pdf->Cell(50, 10, 'Interested Fields:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->interested_fields, 1, 'L');

        $pdf->Cell(50, 10, 'Researched Application Deadlines:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->researched_application_process_deadlines, 1, 'L');

        $pdf->Cell(50, 10, 'Clear Go Information:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->clear_go_information, 1, 'L');

        $pdf->Cell(50, 10, 'Is Helpful Information:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->is_helpful_information, 1, 'L');

        $pdf->Cell(50, 10, 'Started Application:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->started_application, 1, 'L');

        $pdf->Cell(50, 10, 'Need Help in Application:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->need_help_in_application, 1, 'L');

        $pdf->Cell(50, 10, 'Visited Our Resources Online:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->visited_our_resources_on_line, 1, 'L');

        $pdf->Cell(50, 10, 'Resources in Career Library:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->resources_in_career_library, 1, 'L');

        $pdf->Cell(50, 10, 'IAG Learner Plan 1:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->IAG_lerner_plan_1, 1, 'L');

        $pdf->Cell(50, 10, 'IAG Learner Plan 2:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->IAG_lerner_plan_2, 1, 'L');

        $pdf->Cell(50, 10, 'IAG Learner Plan 3:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->IAG_lerner_plan_3, 1, 'L');

        $pdf->Cell(50, 10, 'Learner on Secure Pathway:', 1, 0, 'L');
        $pdf->MultiCell(0, 10, $learner->learner_on_secure_pathway, 1, 'L');

        // Save PDF to storage
        $pdfPath = storage_path('app/public/learner_meeting_' . $learner->id . '.pdf');
        try {
            $pdf->Output('F', $pdfPath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }

        // Prepare email content
        $guardianEmail = $request->input('guardian_email');
        $emailContent = "
            <p>Dear Guardian,</p>
            <p>Attached is the IAG meeting summary for {$learner->learner_name}. Please review the details carefully.</p>
            <p>If you have any questions, feel free to reach out.</p>
            <p>Best regards,<br>Frobel Administration</p>";

        // Send email with PDF attachment
        try {
            Mail::send([], [], function ($message) use ($emailContent, $pdfPath, $guardianEmail, $learner) {
                $message->to($guardianEmail)
                    ->subject('IAG Meeting Summary for ' . $learner->learner_name)
                    ->html($emailContent)
                    ->attach($pdfPath);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }

        // Set success message and redirect
        Session::flash('success', 'Email sent successfully!');
        return back();
    }

    public function showLearner($id)
    {
        $meetings = IagMeeting::where('branch_id', session('branch_id'))->where('id', $id)->first();
        $meetings->term_name = json_decode($meetings->term_name, true);
        $meetings->staff_lead = json_decode($meetings->staff_lead, true);
        $meetings->date = json_decode($meetings->date, true);
        $meetings->meeting_notes = json_decode($meetings->meeting_notes, true);
        $meetings->secure_pathway = json_decode($meetings->secure_pathway, true);
        $meetings->iag_target1 = json_decode($meetings->iag_target1, true);
        $meetings->deadline = json_decode($meetings->deadline, true);
        $meetings->iag_target2 = json_decode($meetings->iag_target2, true);
        $meetings->iag_target3 = json_decode($meetings->iag_target3, true);
        $meetings->file = json_decode($meetings->file, true);
        // dd($meetings);
        return view('branchFrontend.IAG.viewlearnerReport', compact('meetings'));
    }


    public function learnerEdit($id)
    {

        // Retrieve the learner record
        $learner = IagMeeting::where('branch_id', session('branch_id'))->where('id', $id)->first();
        // dd($learner);
        // if (!$learner) {
        //     // Handle case where learner is not found
        //     return redirect()->back()->with('error', 'Learner not found.');
        // }

        // Decode JSON fields
        $learner->term_name = json_decode($learner->term_name, true);
        $learner->staff_lead = json_decode($learner->staff_lead, true);
        $learner->date = json_decode($learner->date, true);
        $learner->meeting_notes = json_decode($learner->meeting_notes, true);
        $learner->secure_pathway = json_decode($learner->secure_pathway, true);
        $learner->iag_target1 = json_decode($learner->iag_target1, true);
        $learner->deadline = json_decode($learner->deadline, true);
        $learner->iag_target2 = json_decode($learner->iag_target2, true);
        $learner->iag_target3 = json_decode($learner->iag_target3, true);
        $learner->file = json_decode($learner->file, true);
        // dd($learner);
        // Pass the learner object to the view
        return view('branchFrontend.IAG.learnerEdit', compact('learner'));
    }

    public function updateLearner(Request $request)
    {
        if ($request->filled('meeting_date')) {
            $request->merge([
                'meeting_date' => Carbon::createFromFormat('d/m/Y', $request->meeting_date)->format('Y-m-d')
            ]);
        }

        // Ab request ke andar meeting_date format Y-m-d hai
        // dd($request->all());
        // Find the learner to update
        $learner = IagMeeting::where('branch_id', session('branch_id'))->find($request->id);

        if (!$learner) {
            return back()->withErrors(['learner' => 'Learner not found.']);
        }

        // Initialize the upload directory
        $uploadDirectory = public_path('uploads');

        // Create the upload directory if it doesn't exist
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Process for each file input field
        $fileInputs = ['file_upload_one', 'file_upload_two', 'file_upload_three'];
        $dbColumns = ['file_input_1', 'file_input_2', 'file_input_3'];

        // Loop through each file input field
        foreach ($fileInputs as $index => $field) {
            if ($request->hasFile($field)) {
                // Get the current db column for this file input
                $dbColumn = $dbColumns[$index];

                // Delete old files only if new ones are uploaded
                if ($learner->$dbColumn) {
                    $oldFiles = explode(',', $learner->$dbColumn);
                    foreach ($oldFiles as $oldFile) {
                        $fullPath = public_path($oldFile);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }

                // Process new files
                $filePaths = [];
                foreach ($request->file($field) as $file) {
                    $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($uploadDirectory, $fileName);
                    $filePaths[] = 'uploads/' . $fileName;
                }

                // Update the learner with new file paths
                $learner->$dbColumn = implode(',', $filePaths);
            }
        }

        // Update the learner with all other data (including file paths if available)
        $learner->update($request->except(['file_upload_one', 'file_upload_two', 'file_upload_three']));
        // ////////////////////

        // $filePaths = [];
        // $files = [];

        // foreach ($request->all() as $key => $value) {
        //     if (preg_match('/^file_\d+$/', $key)) {

        //         $files[$key] = $value;
        //     }
        // }

        // // Loop through each file to store it in the public/uploads directory
        // foreach ($files as $key => $file) {
        //     // Create a unique filename with the correct format
        //     $filename = 'uploads/' . $key . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        //     // Move the uploaded file to the public/uploads directory
        //     $file->move(public_path('uploads'), $filename);

        //     // Add the file path to the array
        //     $filePaths[] =  $filename;
        // }

        // $check =  IagMeeting::where('id',$request->id)->first();

        // dd($filePaths,$check);
        // // Debug: Dump the file paths and files
        // // dd($filePaths, $files,json_encode($filePaths));

        // // Save the file paths as a JSON-encoded string in the database

        // // $updateData['file'] = json_encode($filePaths);

        // $updateData['term_name'] = json_encode($request->term_name);
        // $updateData['staff_lead'] = json_encode($request->staff_lead);
        // $updateData['date'] = json_encode($request->date);
        // $updateData['meeting_notes'] = json_encode($request->meeting_notes);
        // $updateData['secure_pathway'] = json_encode($request->secure_pathway);
        // $updateData['iag_target1'] = json_encode($request->iag_target1);
        // $updateData['deadline'] = json_encode($request->deadline);
        // $updateData['iag_target2'] = json_encode($request->iag_target2);
        // $updateData['iag_target3'] = json_encode($request->iag_target3);
        // $updateData['file'] = json_encode($filePaths);

        // // Save the learner object
        // $learner->update($updateData);
        $filePaths = [];
        $files = [];

        // Extract uploaded files
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^file_\d+$/', $key)) {
                $files[$key] = $value;
            }
        }

        // Get existing record
        $check = IagMeeting::where('branch_id', session('branch_id'))->where('id', $request->id)->first();
        $existingFiles = $check ? json_decode($check->file, true) ?? [] : [];

        foreach ($files as $key => $file) {
            // Generate a unique filename
            $filename = 'uploads/' . $key . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Move the file to the uploads directory
            $file->move(public_path('uploads'), $filename);

            // Extract sequence number from file key (e.g., file_4 → 4)
            preg_match('/file_(\d+)/', $key, $matches);
            $sequenceNumber = $matches[1] ?? null;

            if ($sequenceNumber !== null) {
                $fileReplaced = false;

                // Find index of the file matching the sequence number
                foreach ($existingFiles as $index => $existingPath) {
                    if (preg_match("/file_$sequenceNumber(_\d+_)/", $existingPath)) {
                        // Delete the old file


                        $oldFile = public_path($existingPath);
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                        // dd($existingPath,$sequenceNumber,$filename);
                        $existingPath = $filename;
                        // Replace with new file path
                        $existingFiles[$index] = $filename;
                        $fileReplaced = true;
                        break;
                    }
                }

                // If sequence not found, add new entry
                if (!$fileReplaced) {
                    $existingFiles[] = $filename;
                }
            }
        }

        // Prepare data for updating
        $updateData = [
            'file' => json_encode($existingFiles),
            'term_name' => json_encode($request->term_name),
            'staff_lead' => json_encode($request->staff_lead),
            'date' => json_encode($request->date),
            'meeting_notes' => json_encode($request->meeting_notes),
            'secure_pathway' => json_encode($request->secure_pathway),
            'iag_target1' => json_encode($request->iag_target1),
            'deadline' => json_encode($request->deadline),
            'iag_target2' => json_encode($request->iag_target2),
            'iag_target3' => json_encode($request->iag_target3),
        ];

        // Update or create the record
        // if ($check) {
        $check->update($updateData);
        // } else {
        //     IagMeeting::create($updateData);
        // }

        // Redirect back with success message
        return redirect()->route('learner.report')->with('success', "{$learner->learner_name}'s learner has been updated successfully.");
    }


    public function learnerDelete($id)
    {
        $learner = IagMeeting::where('branch_id', session('branch_id'))->find($id);

        if ($learner) {
            // Check if there are any files to delete
            $files = json_decode($learner->file, true);  // Assuming files are stored as JSON in the 'file' field

            if ($files && is_array($files)) {
                foreach ($files as $filePath) {
                    // Remove 'uploads/' prefix if it exists
                    $filePathWithoutUploads = str_replace('uploads/', '', $filePath);
                    $fileFullPath = public_path('uploads/' . $filePathWithoutUploads);

                    // Check if the file exists and unlink it (delete the file)
                    if (file_exists($fileFullPath)) {
                        unlink($fileFullPath);
                        Log::debug("File deleted successfully: $fileFullPath");
                    } else {
                        Log::error("File not found for deletion: $fileFullPath");
                    }
                }
            }

            // Now delete the learner record
            $learner->delete();

            return redirect()->back()->with('success', 'Learner deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Learner not found.');
        }
    }

    public function storeDestination(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'id' => 'required',
            'destination' => 'required|string|max:40',
        ]);

        // Find the meeting record
        $meeting = IagMeeting::where('branch_id', session('branch_id'))->find($validated['id']);

        if (!$meeting) {
            return response()->json(['status' => 'error', 'message' => 'Meeting not found.'], 404);
        }

        // Check if this family is blocked
        // Use (string) cast to avoid type mismatch between int familyno and string family_id
        $admission = Admission::where('familyno', (string) $meeting->family_id)
            ->where('branch_id', session('branch_id'))
            ->first();

        // Also try without branch_id filter as fallback
        if (!$admission) {
            $admission = Admission::where('familyno', (string) $meeting->family_id)->first();
        }

        if ($admission && (int) $admission->is_blocked === 1) {
            return response()->json([
                'status'  => 'blocked',
                'message' => 'Family ID Blocked. Please contact admin office for more details.',
            ], 403);
        }

        $meeting->destination = $validated['destination'];
        $meeting->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Destination has been saved successfully.'
        ]);
    }


    public function learnerAnalysis()
    {
        $meetings = IagMeeting::where('branch_id', session('branch_id'))->get();
        $categories = ['KS4' => 0, 'KS5' => 0, 'Adult Learner' => 0];

        foreach ($meetings as $meeting) {
            if (isset($categories[$meeting->category])) {
                $categories[$meeting->category]++;
            }
        }

        $totalIagStudents = $meetings->count();

        // $categories['Total'] = $totalIagStudents;

        return view('branchFrontend.IAG.learnerFinalReport', [
            'meetings' => $meetings,
            'categoriesCount' => $categories,
        ]);
    }

    public function schedule()
    {
        $currentDate = now()->toDateString();

        $teachers = DB::table('teachers_subject')
            ->where('teachers_subject.branch_id', session('branch_id'))
            ->leftJoin('staff_attendances', 'teachers_subject.id', '=', 'staff_attendances.teacher_id')
            ->select(
                'teachers_subject.*',
                DB::raw('IF(staff_attendances.created_at IS NOT NULL AND DATE(staff_attendances.created_at) = ?, 1, 0) as is_attendance')
            )
            ->addBinding($currentDate, 'select')
            ->get();

        return view('branchFrontend.staff_management.teacherSchedule', compact('teachers'));
    }

    // public function getTeachers()
    // {
    //     // $currentDate = now()->toDateString();

    //     // $teachers = DB::table('teachers_subject')
    //     //     ->where('teachers_subject.branch_id', session('branch_id'))
    //     //     ->leftJoin('staff_attendances', 'teachers_subject.id', '=', 'staff_attendances.teacher_id')
    //     //     ->select(
    //     //         'teachers_subject.*',
    //     //         DB::raw('IF(staff_attendances.created_at IS NOT NULL AND DATE(staff_attendances.created_at) = ?, 1, 0) as is_attendance')
    //     //     )
    //     //     ->addBinding($currentDate, 'select')
    //     //     ->get();

    //     // return $teachers;


    // }

    public function getTeachers()
    {
        $currentDate = now()->toDateString();

        $teachers = DB::table('teachers_subject')
            ->where('teachers_subject.branch_id', session('branch_id'))
            ->select('id', 'teacher_name', 'subject', 'is_available')
            ->get();

        return $teachers;
    }


    //  public function markAttendance(Request $request)
    // {
    //     $request->validate([
    //         'teacher_id' => 'required|string'
    //     ]);

    //     // Normalize NULL to 'no'
    //     DB::table('teachers_subject')
    //         ->whereNull('is_available')
    //         ->orWhere('is_available', '')
    //         ->update(['is_available' => 'no']);

    //     $teacher = DB::table('teachers_subject')
    //         ->where('id', $request->teacher_id)
    //         ->first();

    //     if (!$teacher) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Teacher not found!'
    //         ]);
    //     }

    //     $teacherName = trim($teacher->teacher_name);

    //     // Get current status (normalize 'yes'/'no')
    //     $currentStatus = strtolower(trim($teacher->is_available)) === 'yes' ? 'yes' : 'no';
    //     $newStatus = $currentStatus === 'yes' ? 'no' : 'yes';

    //     // Update all rows with same teacher name (in case of duplicates)
    //     DB::table('teachers_subject')
    //         ->whereRaw('TRIM(teacher_name) = ?', [$teacherName])
    //         ->where('branch_id', session('branch_id'))
    //         ->update(['is_available' => $newStatus]);

    //     $statusMessage = $newStatus === 'yes'
    //         ? "$teacherName is now Available"
    //         : "$teacherName is now Not Available";

    //     return response()->json([
    //         'success' => true,
    //         'teacher' => $teacherName,
    //         'new_status' => $newStatus, // 'yes' or 'no'
    //         'message' => $statusMessage
    //     ]);
    // }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|string'
        ]);

        $teacher = DB::table('teachers_subject')
            ->where('id', $request->teacher_id)
            ->where('branch_id', session('branch_id'))
            ->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found!'
            ]);
        }

        $teacherName = trim($teacher->teacher_name);
        $currentStatus = strtolower(trim($teacher->is_available ?? '')) === 'yes' ? 'yes' : 'no';

        // If already "no", directly set to "yes"
        if ($currentStatus === 'no') {
            $newStatus = 'yes';
        } else {
            // Trying to set to "no", check future timetable for the next 7 days
            $today = now()->format('Y-m-d'); // Current date
            $nextSevenDays = now()->addDays(7)->format('Y-m-d'); // 7 days from today

            $futureDates = DB::table('general_timetables')
                ->where('teacher_id', $teacherName)
                ->where('branch_id', session('branch_id'))
                ->where('date', '>=', $today)
                ->where('date', '<=', $nextSevenDays)
                ->select('date')
                ->distinct()
                ->orderBy('date')
                ->get();

            if ($futureDates->isNotEmpty()) {
                $conflicts = $futureDates->map(function ($item) {
                    return "<strong>{$item->date}</strong>";
                })->implode('<br>');

                return response()->json([
                    'success' => false,
                    'conflict' => true,
                    'message' => "Cannot mark <strong>$teacherName</strong> as Not Available!<br><br>
                              booked dates:<br><br>$conflicts<br><br>
                              <small>remove from timetable first.</small>"
                ]);
            }

            // Safe to mark as "no" if no bookings in the next 7 days
            $newStatus = 'no';
        }

        // Get affected records count for activity log (before update)
        $affectedRecordsCount = DB::table('teachers_subject')
            ->whereRaw('TRIM(teacher_name) = ?', [$teacherName])
            ->where('branch_id', session('branch_id'))
            ->count();

        // Update the availability status
        DB::table('teachers_subject')
            ->whereRaw('TRIM(teacher_name) = ?', [$teacherName])
            ->where('branch_id', session('branch_id'))
            ->update(['is_available' => $newStatus]);

        // Manually log activity for teacher availability change
        $branch = User::where('branch_id', session('branch_id'))
            ->where('is_main_branch', 1)
            ->first();

        activity('TeacherAvailability')
            ->causedBy(auth()->user())
            ->withProperties([
                'teacher_name' => $teacherName,
                'is_available' => $newStatus,
                'previous_status' => $currentStatus,
                'affected_records_count' => $affectedRecordsCount,
                'branch_id' => $branch ? $branch->branch_id : null,
                'branch_name' => $branch ? $branch->branch_name : null,
            ])
            ->log("Teacher {$teacherName} availability changed to " . ($newStatus === 'yes' ? 'Available' : 'Not Available'));

        // Also set branch_id and branch_name on the activity log entry
        $latestActivity = \Spatie\Activitylog\Models\Activity::latest()->first();
        if ($latestActivity) {
            $latestActivity->branch_id = $branch ? $branch->branch_id : null;
            $latestActivity->branch_name = $branch ? $branch->branch_name : null;
            $latestActivity->save();
        }

        $message = $newStatus === 'yes'
            ? "$teacherName is now Available"
            : "$teacherName is now Not Available";

        return response()->json([
            'success' => true,
            'teacher' => $teacherName,
            'new_status' => $newStatus,
            'message' => $message
        ]);
    }
    // public function examReg()
    // {
    //     // Get all users where is_main_branch = 1
    //     $branches = DB::table('users')
    //         ->where('is_main_branch', 1)
    //         ->select('branch_id', 'branch_name')
    //         ->get();

    //     return view('branchFrontend.examReg.publicPage', compact('branches'));
    // }

    // public function studentRequest(Request $request)
    // {
    //     try {
    //         // $studentRequest = StudentRequest::create([
    //         //     'base64_data' => json_encode($request->except('_token')),
    //         //     'is_approved' => '0',
    //         // ]);

    //         // Get branch_id from request
    //         $branchId = $request->input('branch');

    //         // Fetch branch details
    //         $branch = User::where('branch_id', $branchId)
    //             ->where('is_main_branch', 1)
    //             ->select('branch_id', 'branch_name')
    //             ->first();

    //         // Create student request with branch info
    //         $studentRequest = StudentRequest::create([
    //             'base64_data' => json_encode($request->except('_token')),
    //             'is_approved' => '0',
    //             'branch_id' => $branch ? $branch->branch_id : null,
    //             'branch_name' => $branch ? $branch->branch_name : null,
    //         ]);

    //         $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
    //             ->pluck('user_id')
    //             ->toArray();

    //         $users = User::whereIn('id', $userIds)->get();

    //         if ($users->isEmpty()) {
    //             Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
    //         }

    //         $parentEmail = $request->input('parent1_email');

    //         if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
    //             $logoUrl = 'https://frobelschoolsystemnew.frobel.co.uk/img/datesheetLogo.png';
    //             $emailContent = '
    //             <html>
    //             <head>
    //                 <style>
    //                     body {
    //                         font-family: Arial, sans-serif;
    //                         color: #000000;
    //                         margin: 0;
    //                         padding: 0;
    //                         background-color: #f9f9f9;
    //                     }
    //                     .container {
    //                         max-width: 600px;
    //                         margin: 0 auto;
    //                         padding: 20px;
    //                         background-color: #ffffff;
    //                         border: 1px solid #e0e0e0;
    //                         border-radius: 8px;
    //                         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    //                     }
    //                     .logo {
    //                         text-align: center;
    //                         margin-bottom: 20px;
    //                     }
    //                     .logo img {
    //                         width: 100px;
    //                         height: auto;
    //                     }
    //                     .header {
    //                         color: #f17d7d;
    //                         font-size: 18px;
    //                         font-weight: bold;
    //                         text-align: center;
    //                         margin-bottom: 20px;
    //                     }
    //                     .content {
    //                         font-size: 16px;
    //                         line-height: 1.6;
    //                         color: #333333;
    //                         text-align: center;
    //                         margin-bottom: 20px;
    //                     }
    //                     .footer {
    //                         color: #bd9191;
    //                         font-size: 14px;
    //                         text-align: center;
    //                         margin-top: 20px;
    //                     }
    //                     a {
    //                         color: #f17d7d;
    //                         text-decoration: none;
    //                     }
    //                     a:hover {
    //                         text-decoration: underline;
    //                     }
    //                 </style>
    //             </head>
    //             <body>
    //                 <div class="container">
    //                     <div class="logo">
    //                         <img src="' . $logoUrl . '" alt="Frobel Education Logo">
    //                     </div>
    //                     <p class="header">Dear Learner,</p>
    //                     <div class="content">
    //                         <p>Thank you for submitting your admission form. We have received it successfully and will review your application shortly. If any further information is needed, we will be in touch.</p>
    //                         <p>If you have any questions in the meantime, feel free to contact us at <a href="mailto:admin@frobel.co.uk">admin@frobel.co.uk</a>.</p>
    //                     </div>
    //                     <p class="footer">Best regards,<br>Frobel Education</p>
    //                 </div>
    //             </body>
    //             </html>
    //         ';

    //             // Uncomment if you want to send the email
    //             // Mail::send([], [], function ($message) use ($parentEmail, $emailContent) {
    //             //     $message->to($parentEmail)
    //             //             ->subject('Thank You for Submitting the Form')
    //             //             ->from('admin@frobel.co.uk', 'Frobel Education')
    //             //             ->setBody($emailContent, 'text/html');
    //             // });
    //         } else {
    //             Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
    //         }

    //         Notification::send($users, new StudentRequestNotification($studentRequest));

    //         return response()->json(['message' => 'Data stored successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('Error in studentRequest: ' . $e->getMessage());
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
    //     }
    // }
    //   public function studentRequest(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         // Validate request data
    //         $validated = $request->validate([
    //             'parent1_email' => 'required|email',
    //             'branch' => 'required',
    //             'parent1_first_name' => 'required',
    //             'parent1_last_name' => 'required',
    //             'consent_1_checkbox' => 'required',
    //             'how_did_you_hear' => 'required',
    //         ]);

    //         // Get branch_id from request
    //         $branchId = $request->input('branch');

    //         // Fetch branch details
    //         $branch = User::where('branch_id', $branchId)
    //             ->where('is_main_branch', 1)
    //             ->select('branch_id', 'branch_name')
    //             ->first();

    //         if (!$branch) {
    //             Log::error('Branch not found for branch_id: ' . $branchId);
    //             return response()->json(['error' => 'Invalid branch selected'], 400);
    //         }

    //         // Create student request with branch info
    //         $studentRequest = StudentRequest::create([
    //             'base64_data' => json_encode($request->except('_token')),
    //             'is_approved' => '0',
    //             'branch_id' => $branch->branch_id,
    //             'branch_name' => $branch->branch_name,
    //         ]);

    //         // Determine branch admin email based on branch name
    //         // $branchEmails = [
    //         //     'Barking' => 'inaam.chishti2@gmail.com',
    //         //     'Stratford' => 'inaam.chishti2@gmail.com',
    //         //     'Grays' => 'inaam.chishti2@gmail.com',
    //         // ];

    //         // Determine branch admin email based on branch name
    //         $branchEmails = [
    //             'Barking' => 'barking@frobel.co.uk',
    //             'Stratford' => 'stratford@frobel.co.uk',
    //             'Grays' => 'grays@frobel.co.uk',
    //         ];

    //         $branchEmail = $branchEmails[$branch->branch_name] ?? null;
    //         if (!$branchEmail) {
    //             Log::error('No email defined for branch: ' . $branch->branch_name);
    //             return response()->json(['error' => 'No email configured for this branch'], 500);
    //         }

    //         // Generate PDF
    //         $options = new Options();
    //         $options->set('isHtml5ParserEnabled', true);
    //         $options->set('isRemoteEnabled', true);
    //         $dompdf = new Dompdf($options);

    //         // Prepare form data for PDF
    //         $formData = json_decode($studentRequest->base64_data, true);
    //         $students = [];
    //         foreach ($formData['firstName'] as $index => $firstName) {
    //             $students[] = [
    //                 'firstName' => $firstName,
    //                 'middleName' => $formData['middleName'][$index] ?? '',
    //                 'lastName' => $formData['lastName'][$index] ?? '',
    //                 'dob' => $formData['dob'][$index] ?? '',
    //                 'gender' => $formData['gender'][$index] ?? '',
    //                 'yearInSchool' => $formData['yearInSchool'][$index] ?? '',
    //                 'tuitionHours' => $formData['tuitionHours'][$index] ?? '',
    //                 'medicalConditions' => $formData['medicalConditions'][$index] ?? '',
    //                 'allergies' => $formData['allergies'][$index] ?? '',
    //                 'additionalNeeds' => $formData['additionalNeeds'][$index] ?? '',
    //                 'gpPrefix' => $formData['gpPrefix'][$index] ?? '',
    //                 'gpFirstName' => $formData['gpFirstName'][$index] ?? '',
    //                 'gpLastName' => $formData['gpLastName'][$index] ?? '',
    //                 'gpAddress' => $formData['gpAddress'][$index] ?? '',
    //                 'gpAddressLineTwo' => $formData['gpAddressLineTwo'][$index] ?? '',
    //                 'city' => $formData['city'][$index] ?? '',
    //                 'CountyStateRegion' => $formData['CountyStateRegion'][$index] ?? '',
    //                 'zipCode' => $formData['zipCode'][$index] ?? '',
    //                 'country' => $formData['country'][$index] ?? '',
    //                 'GPPhone' => $formData['GPPhone'][$index] ?? '',
    //                 'medicalConsent' => $formData['medicalConsent'][$index] ?? '',
    //                 'photoConsent' => is_array($formData['photoConsent'][$index]) ? implode(', ', $formData['photoConsent'][$index]) : $formData['photoConsent'][$index] ?? '',
    //                 'leaveAlone' => $formData['leaveAlone'][$index] ?? '',
    //             ];
    //         }

    //         // PDF HTML content
    //         $html = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <style>
    //                 body { font-family: Arial, sans-serif; font-size: 12px; }
    //                 h1, h2 { color: #67C0EA; }
    //                 table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    //                 th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //                 th { background-color: #67C0EA; color: white; }
    //                 .section { margin-bottom: 20px; }
    //                 .signature-img { max-width: 200px; height: auto; }
    //             </style>
    //         </head>
    //         <body>
    //             <h1>Frobel Education Form Submission</h1>
    //             <div class="section">
    //                 <h2>Branch Information</h2>
    //                 <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
    //             </div>';

    //         foreach ($students as $index => $student) {
    //             $html .= '
    //             <div class="section">
    //                 <h2>Student ' . ($index + 1) . ' Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($student['firstName']) . '</td></tr>
    //                     <tr><td>Middle Name</td><td>' . htmlspecialchars($student['middleName']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($student['lastName']) . '</td></tr>
    //                     <tr><td>Date of Birth</td><td>' . htmlspecialchars($student['dob']) . '</td></tr>
    //                     <tr><td>Gender</td><td>' . htmlspecialchars($student['gender']) . '</td></tr>
    //                     <tr><td>Year in School</td><td>' . htmlspecialchars($student['yearInSchool']) . '</td></tr>
    //                     <tr><td>Tuition Hours</td><td>' . htmlspecialchars($student['tuitionHours']) . '</td></tr>
    //                     <tr><td>Medical Conditions</td><td>' . htmlspecialchars($student['medicalConditions']) . '</td></tr>
    //                     <tr><td>Allergies</td><td>' . htmlspecialchars($student['allergies']) . '</td></tr>
    //                     <tr><td>Additional Needs</td><td>' . htmlspecialchars($student['additionalNeeds']) . '</td></tr>
    //                     <tr><td>GP Prefix</td><td>' . htmlspecialchars($student['gpPrefix']) . '</td></tr>
    //                     <tr><td>GP First Name</td><td>' . htmlspecialchars($student['gpFirstName']) . '</td></tr>
    //                     <tr><td>GP Last Name</td><td>' . htmlspecialchars($student['gpLastName']) . '</td></tr>
    //                     <tr><td>GP Address</td><td>' . htmlspecialchars($student['gpAddress']) . '</td></tr>
    //                     <tr><td>GP Address Line 2</td><td>' . htmlspecialchars($student['gpAddressLineTwo']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($student['city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($student['CountyStateRegion']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($student['zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($student['country']) . '</td></tr>
    //                     <tr><td>GP Phone</td><td>' . htmlspecialchars($student['GPPhone']) . '</td></tr>
    //                     <tr><td>Medical Consent</td><td>' . htmlspecialchars($student['medicalConsent']) . '</td></tr>
    //                     <tr><td>Photo Consent</td><td>' . htmlspecialchars($student['photoConsent']) . '</td></tr>
    //                     <tr><td>Leave Alone</td><td>' . htmlspecialchars($student['leaveAlone']) . '</td></tr>
    //                 </table>
    //             </div>';
    //         }

    //         $html .= '
    //             <div class="section">
    //                 <h2>Parent/Guardian Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['parent1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['parent1_last_name']) . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['parent1_Address']) . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['parent1_Address_line2']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['parent1_city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['parent1_country_state_region']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['parent1_zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['parent1_country']) . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['parent1_email']) . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['parent1_mobile']) . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Emergency Contact Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_last_name']) . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address']) . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address_line2']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['emergency_conatct1_city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['emergency_conatct1_country_state_region']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['emergency_conatct1_zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['emergency_conatct1_country']) . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['emergency_conatct1_email']) . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['emergency_conatct1_mobile']) . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Consent Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['consent_1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['consent_1_last_name']) . '</td></tr>
    //                     <tr><td>Date</td><td>' . htmlspecialchars($formData['consent_1date']) . '</td></tr>
    //                     <tr><td>Terms Agreed</td><td>' . htmlspecialchars($formData['consent_1_checkbox']) . '</td></tr>
    //                     <tr><td>Signature</td><td><img src="' . htmlspecialchars($formData['consent_1signature']) . '" class="signature-img" alt="Signature"></td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Additional Information</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>How did you hear about us?</td><td>' . htmlspecialchars($formData['how_did_you_hear']) . '</td></tr>
    //                 </table>
    //             </div>
    //         </body>
    //         </html>';

    //         $dompdf->loadHtml($html);
    //         $dompdf->setPaper('A4', 'portrait');
    //         $dompdf->render();
    //         $pdfOutput = $dompdf->output();
    //         $pdfPath = storage_path('app/public/student_form_' . $studentRequest->id . '.pdf');
    //         file_put_contents($pdfPath, $pdfOutput);

    //         // Email template base
    //         $emailTemplate = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <meta charset="UTF-8">
    //             <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //             <title>%s</title>
    //         </head>
    //         <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
    //             <!-- Logo -->
    //             <div style="text-align: center; margin-bottom: 20px;">
    //                 <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
    //             </div>
    //             <!-- Main Content -->
    //             <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    //                 <h2 style="color: #2e6da4;">Dear %s,</h2>
    //                 %s
    //                 <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
    //                 <p>Kind regards,<br>
    //                 <strong>Frobel Education Team</strong><br>
    //                 Frobel Education</p>
    //             </div>
    //             <!-- Footer -->
    //             <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
    //                 <p>
    //                     67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
    //                     Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
    //                     Email: <a href="mailto:exams@frobel.co.uk" style="color: #337ab7;">exams@frobel.co.uk</a>
    //                 </p>
    //                 <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
    //             </div>
    //         </body>
    //         </html>';

    //         // Send confirmation email to parent
    //         $parentEmail = $formData['parent1_email'];
    //         if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
    //             $parentEmailContent = sprintf(
    //                 $emailTemplate,
    //                 'Registration Confirmation',
    //                 'Learner',
    //                 '<p>Thank you for registering with Frobel Education. Your form has been successfully submitted to the ' . htmlspecialchars($branch->branch_name) . ' branch. We will be in touch shortly.</p>'
    //             );

    //             Mail::html($parentEmailContent, function ($message) use ($parentEmail) {
    //                 $message->to($parentEmail)
    //                         ->subject('Thank You for Your Registration - Frobel Education')
    //                         ->from('exams@frobel.co.uk', 'Frobel Education');
    //             });
    //         } else {
    //             Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
    //         }

    //         // Send notification email to branch admin with PDF attachment
    //         $adminEmailContent = sprintf(
    //             $emailTemplate,
    //             'New Student Registration',
    //             'Administrator',
    //             '<p>A new student registration form has been submitted for the ' . htmlspecialchars($branch->branch_name) . ' branch. Please find the details in the attached PDF.</p>'
    //         );

    //         Mail::html($adminEmailContent, function ($message) use ($branchEmail, $pdfPath, $studentRequest) {
    //             $message->to($branchEmail)
    //                     ->subject('New Student Registration - ' . $studentRequest->id)
    //                     ->from('exams@frobel.co.uk', 'Frobel Education')
    //                     ->attach($pdfPath, [
    //                         'as' => 'student_form_' . $studentRequest->id . '.pdf',
    //                         'mime' => 'application/pdf',
    //                     ]);
    //         });

    //         // Notify users with access permissions
    //         $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
    //             ->pluck('user_id')
    //             ->toArray();

    //         $users = User::whereIn('id', $userIds)->get();

    //         if ($users->isEmpty()) {
    //             Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
    //         } else {
    //             Notification::send($users, new StudentRequestNotification($studentRequest));
    //         }

    //         // Clean up PDF file
    //         if (file_exists($pdfPath)) {
    //             unlink($pdfPath);
    //         }

    //         return response()->json(['message' => 'Data stored and emails sent successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('Error in studentRequest: ' . $e->getMessage());
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
    //     }
    // }
    public function reviewRequest($id)
    {
        $data = StudentRequest::where('id', $id)->first();
        $request_id = $data->id;
        // dd(session('branch_id'));

        $data = json_decode($data->base64_data, true);
        $largestStudent = Student::where('branch_id', session('branch_id'))
            ->orderByRaw('CAST(admissionid AS SIGNED) DESC')
            ->first();
        $family_id = $largestStudent ? ((int)$largestStudent->admissionid + 1) : 1001;

        // dd($largestStudent,$family_id);

        $subjects = DB::table('subjects')
            ->select('name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->pluck('name')
            ->toArray();
        
        // Parse package to extract amount and weeks for split input
        $packageAmount = '';
        $packageWeeks = '';
        if (isset($data['fee_detail']) && $data['fee_detail']) {
            $package = $data['fee_detail'];
            // Try new format: "280 for 2 weeks", "280 for 4 weeks", "280 for ucas session", "280 for per month", "280 for per session"
            if (preg_match('/(\d+)\s+for\s+(.+)/i', $package, $matches)) {
                $packageAmount = $matches[1];
                $packageWeeks = strtolower(trim($matches[2]));
            }
            // Try old format: "280for4weeks"
            elseif (preg_match('/(\d+)for(\d+)weeks?/i', $package, $matches)) {
                $packageAmount = $matches[1];
                $packageWeeks = strtolower($matches[2] . ' week' . ($matches[2] > 1 ? 's' : ''));
            }
            // Try to extract just numbers
            elseif (preg_match('/(\d+)/', $package, $matches)) {
                $packageAmount = $matches[1];
            }
        }
        
        // dd($data);
        return view('branchFrontend.examReg.viewRequest', compact('data', 'family_id', 'request_id', 'subjects', 'packageAmount', 'packageWeeks'));
    }


    // public function approveRequest(Request $request)
    // {
    //     // dd($request->all());
    //     $studentIds = [];
    //     $studentName = [];

    //     $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //     $branch_id = $branch ? $branch->branch_id : null;
    //     $branch_name = $branch ? $branch->branch_name : null;

    //     $consent = Consent::create([
    //         'family_id' => $request->input('family_id', null),
    //         'consent_1_first_name' => $request->input('consent_1_first_name', null),
    //         'consent_1_last_name'  => $request->input('consent_1_last_name', null),
    //         'consent_1signature'   => $request->input('signature', null),
    //         'consent_1_date'       => $request->input('consent_1date', null),
    //         'how_did_you_hear'     => $request->input('how_did_you_hear', null),
    //         'branch_id' => $branch_id,
    //         'branch_name' => $branch_name,
    //     ]);

    //     ///////////////////// Guardian store start //////////////////////
    //     $guardian = new Guardian();
    //     $guardian->guardianname = trim(($request->input('parent1_first_name', '') . ' ' . $request->input('parent1_last_name', '')));
    //     $guardian->guardianaddress = $request->input('parent1_Address', null);
    //     $guardian->address_line_2 = $request->input('parent1_Address_line2', null);
    //     $guardian->city = $request->input('parent1_city', null);
    //     $guardian->countyStateRegion = $request->input('parent1_country_state_region', null);
    //     $guardian->zIPCode = $request->input('parent1_zipCode', null);
    //     $guardian->country = $request->input('parent1_country', null);
    //     $guardian->guardiantel = $request->input('parent1_email', null);
    //     $guardian->guardianmob = $request->input('parent1_mobile', null);
    //     $guardian->branch_id = $branch_id;
    //     $guardian->branch_name = $branch_name;
    //     $guardian->save();

    //     /////////////////// Next of Kin store Start //////////////////
    //     $kin = new Kin();
    //     $kin->kinname = trim(($request->input('emergency_conatct1_first_name', '') . ' ' . $request->input('emergency_conatct1_last_name', '')));
    //     $kin->kinaddress = $request->input('emergency_conatct1_Address', null);
    //     $kin->emergency_conatct1_Address_line2 = $request->input('emergency_conatct1_Address_line2', null);
    //     $kin->emergency_conatct1_city = $request->input('emergency_conatct1_city', null);
    //     $kin->emergency_conatct1_country_state_region = $request->input('emergency_conatct1_country_state_region', null);
    //     $kin->emergency_conatct1_zipCode = $request->input('emergency_conatct1_zipCode', null);
    //     $kin->emergency_conatct1_country = $request->input('emergency_conatct1_country', null);
    //     $kin->kintel = $request->input('emergency_conatct1_email', null);
    //     $kin->kinmob = $request->input('emergency_conatct1_mobile', null);
    //     $kin->branch_id = $branch_id;
    //     $kin->branch_name = $branch_name;
    //     $kin->save();

    //     ///////////////////// Admission store start //////////////////
    //     $admission = new Admission();
    //     $admission->familyno = $request->input('family_id', null);
    //     $admission->formfilingdate = $request->has('form_date') ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('form_date')))) : null;
    //     $admission->joiningdate = $request->has('joining_date') ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('joining_date')))) : null;
    //     $admission->medicalcondition = $request->input('medical_condition', null);
    //     $admission->feedetail = $request->input('fee_detail', null);
    //     $admission->familystatus = $request->input('family_status', null);
    //     $admission->payment_method = $request->input('payment_method', null);
    //     $admission->add_comment = $request->input('add_comment', null);
    //     $admission->branch_id = $branch_id;
    //     $admission->branch_name = $branch_name;

    //     for ($i = 1; $i <= 5; $i++) {
    //         $admission->{'child_name' . $i} = $request->input('child_name' . $i, null);
    //         $admission->{'school_name' . $i} = $request->input('school_name' . $i, null);
    //     }

    //     $admission->save();
    //     $admission_id = $admission->admissionid;

    //     ///////////////////// Student store start //////////////////
    //     $students = $request->input('student', []);
    //     foreach ($students as $studentData) {
    //         $student = Student::create([
    //             'studentname' => trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['middleName'] ?? '')),
    //             'studentsur' => $studentData['lastName'] ?? null,
    //             'studentdob' => $studentData['dob'] ?? null,
    //             'studentgender' => $studentData['gender'] ?? null,
    //             'studentyearinschool' => isset($studentData['yearInSchool'])
    //                 ? preg_replace('/\D/', '', trim($studentData['yearInSchool']))
    //                 : null,

    //             'studenthours' => $studentData['tuitionHours'] ?? null,
    //             'medical_condition' => (!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
    //             'allergies' => $studentData['allergies'] ?? null,
    //             'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
    //             'additionalNeeds' => $studentData['additionalNeeds'] ?? null,
    //             'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //             'photoConsent' => json_encode($studentData['photoConsent'] ?? null),
    //             'leaveAlone' => $studentData['leaveAlone'] ?? null,
    //             'kinid' => $kin->kinid,
    //             'guardianid' => $guardian->Guardianid,
    //             'admissionid' => $request->input('family_id', null),
    //             'student_status' => $studentData['student_status'] ?? null,
    //             'branch_id' => $branch_id,
    //             'branch_name' => $branch_name,
    //         ]);
    //         $studentIds[] = $student->studentid;
    //         $studentName[] = trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['middleName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));

    //         ////////////////////// Medical Condition Start /////////////////////
    //         medical_condition::create([
    //             'guardianid' => $guardian->Guardianid,
    //             'family_id' => $request->input('family_id', null),
    //             'student_id' => $student->studentid,
    //             'gpPrefix' => $studentData['gpPrefix'] ?? null,
    //             'drName' => trim(($studentData['gpFirstName'] ?? '') . ' ' . ($studentData['gpLastName'] ?? '')),
    //             'drNumber' => $studentData['GPPhone'] ?? null,
    //             'medicalDetails' => $studentData['medicalConditions'] ?? null,
    //             'allergies' => $studentData['allergies'] ?? null,
    //             'gpAddress' => $studentData['gpAddress'] ?? null,
    //             'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? null,
    //             'gp_city' => $studentData['gp_city'] ?? null,
    //             'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? null,
    //             'gpzipCode' => $studentData['gpzipCode'] ?? null,
    //             'gpcountry' => $studentData['gpcountry'] ?? null,
    //             'medicalConsent' => $studentData['medicalConsent'] ?? null,
    //             'branch_id' => $branch_id,
    //             'branch_name' => $branch_name,
    //         ]);
    //     }

    //     foreach ($studentName as $studentNamez) {
    //         for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
    //             $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));

    //             TimeTable::create([
    //                 'studentname' => $studentNamez,
    //                 'admissionid' => $request->input('family_id', null),
    //                 'day' => $day,
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //             ]);
    //         }
    //     }

    //     $studentRequest = StudentRequest::where('id', $request->input('request_id'))->first();
    //     if ($studentRequest) {
    //         $studentRequest->is_approved = 1;
    //         $studentRequest->save();
    //     }
    //     auth()->user()->notifications()
    //         ->whereRaw("JSON_EXTRACT(data, '$.request_id') = ?", [$request->request_id])
    //         ->delete();
    //     return redirect('fetch-student-registration')->with('success', 'Request approved successfully!');
    // }
    public function approveRequest(Request $request)
    {
        // dd($request->all());
        $dateFields = ['form_date', 'joining_date', 'dob', 'consent_1date'];

        foreach ($dateFields as $field) {
            if ($request->filled($field)) {
                try {
                    $request->merge([
                        $field => Carbon::createFromFormat('d/m/Y', $request->$field)->format('Y-m-d')
                    ]);
                } catch (\Exception $e) {
                    // agar date already Y-m-d format me ho ya invalid ho to skip kar do
                }
            }
        }
        if ($request->has('student')) {
            $students = $request->student;

            foreach ($students as $index => $student) {
                if (!empty($student['dob'])) {
                    try {
                        $students[$index]['dob'] = Carbon::createFromFormat('d/m/Y', $student['dob'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        // ignore if already in Y-m-d
                    }
                }
            }

            // update back into request
            $request->merge([
                'student' => $students
            ]);
        }
        // dd($request->all());
        // Retrieve branch information
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch ? $branch->branch_id : null;
        $branch_name = $branch ? $branch->branch_name : null;

        // Create Consent
        $consent = Consent::create([
            'family_id' => $request->input('family_id', null),
            'consent_1_first_name' => $request->input('consent_1_first_name', null),
            'consent_1_last_name' => $request->input('consent_1_last_name', null),
            'consent_1signature' => $request->input('signature', null),
            'consent_1_date' => $request->input('consent_1date', null),
            'how_did_you_hear' => $request->input('how_did_you_hear', null),
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
        ]);

        // Create Guardian
        $guardian = new Guardian();
        $guardian->guardianname = trim(($request->input('parent1_first_name', '') . ' ' . $request->input('parent1_last_name', '')));
        $guardian->guardianaddress = $request->input('parent1_Address', null);
        $guardian->address_line_2 = $request->input('parent1_Address_line2', null);
        $guardian->city = $request->input('parent1_city', null);
        $guardian->countyStateRegion = $request->input('parent1_country_state_region', null);
        $guardian->zIPCode = $request->input('parent1_zipCode', null);
        $guardian->country = $request->input('parent1_country', null);
        $guardian->guardiantel = $request->input('parent1_email', null);
        $guardian->guardianmob = $request->input('parent1_mobile', null);
        $guardian->branch_id = $branch_id;
        $guardian->branch_name = $branch_name;
        $guardian->save();

        // Create Kin
        $kin = new Kin();
        $kin->kinname = trim(($request->input('emergency_conatct1_first_name', '') . ' ' . $request->input('emergency_conatct1_last_name', '')));
        $kin->kinaddress = $request->input('emergency_conatct1_Address', null);
        $kin->emergency_conatct1_Address_line2 = $request->input('emergency_conatct1_Address_line2', null);
        $kin->emergency_conatct1_city = $request->input('emergency_conatct1_city', null);
        $kin->emergency_conatct1_country_state_region = $request->input('emergency_conatct1_country_state_region', null);
        $kin->emergency_conatct1_zipCode = $request->input('emergency_conatct1_zipCode', null);
        $kin->emergency_conatct1_country = $request->input('emergency_conatct1_country', null);
        $kin->kintel = $request->input('emergency_conatct1_email', null);
        $kin->kinmob = $request->input('emergency_conatct1_mobile', null);
        $kin->branch_id = $branch_id;
        $kin->branch_name = $branch_name;
        $kin->save();

        // Create Admission
        $admission = new Admission();
        $admission->familyno = $request->input('family_id', null);
        $admission->formfilingdate = $request->has('form_date') ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('form_date')))) : null;
        $admission->joiningdate = $request->has('joining_date') ? date('Y-m-d', strtotime(str_replace('/', '-', $request->input('joining_date')))) : null;
        $admission->medicalcondition = $request->input('medical_condition', null);
        // Combine package_amount and package_weeks into feedetail format: "280 for 5 weeks"
        if ($request->has('package_amount') && $request->has('package_weeks') && 
            !empty($request->package_amount) && !empty($request->package_weeks)) {
            $admission->feedetail = trim($request->package_amount) . ' for ' . strtolower(trim($request->package_weeks));
        } else {
        $admission->feedetail = $request->input('fee_detail', null);
        }
        $admission->familystatus = $request->input('family_status', null);
        $admission->payment_method = $request->input('payment_method', null);
        $admission->add_comment = $request->input('add_comment', null);
        $admission->branch_id = $branch_id;
        $admission->branch_name = $branch_name;

        for ($i = 1; $i <= 5; $i++) {
            $admission->{'child_name' . $i} = $request->input('child_name' . $i, null);
            $admission->{'school_name' . $i} = $request->input('school_name' . $i, null);
        }

        $admission->save();
        $admission_id = $admission->admissionid;

        // Create Students and Medical Conditions
        $studentIds = [];
        $studentName = [];
        $students = $request->input('student', []);
        foreach ($students as $studentData) {
            // dd($studentData,$studentData['has_additional_needs']);
            $student = Student::create([
                'studentname' => trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['middleName'] ?? '')),
                'studentsur' => $studentData['lastName'] ?? null,
                'studentdob' => $studentData['dob'] ?? null,
                'studentgender' => $studentData['gender'] ?? null,
                'studentyearinschool' => isset($studentData['yearInSchool'])
                    ? preg_replace('/\D/', '', trim($studentData['yearInSchool']))
                    : null,
                // 'studenthours' => $studentData['tuitionHours'] ?? null,

                'medical_condition' => $studentData['has_medical_conditions'] ?? null, //(!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
                // 'medical_conditions_explanation' => $studentData['has_medical_conditions'] === 'yes' ? ($studentData['medical_conditions_explanation'] ?? null) : null,

                'allergies' =>  $studentData['has_allergies'] ?? null, //$studentData['allergies'] ?? null,
                'allergies_explanation' => $studentData['has_allergies'] === 'yes' ? ($studentData['allergies_explanation'] ?? null) : null,


                'additionalNeeds' =>  $studentData['has_additional_needs'] ?? null, //$studentData['additionalNeeds'] ?? null,
                'additional_needs_explanation' => $studentData['has_additional_needs'] === 'yes' ? ($studentData['additional_needs_explanation'] ?? null) : null,



                // 'allergies' => $studentData['allergies'] ?? null,
                'medicalConditions_explanation' => $studentData['medicalConditions'] ?? null,
                // 'additionalNeeds' => $studentData['additionalNeeds'] ?? null,
                'medicalConsent' => $studentData['medicalConsent'] ?? null,
                'photoConsent' => json_encode($studentData['photoConsent'] ?? []),
                'leaveAlone' => $studentData['leaveAlone'] ?? null,
                'kinid' => $kin->kinid,
                'guardianid' => $guardian->Guardianid,
                'admissionid' => $request->input('family_id', null),
                'student_status' => $studentData['student_status'] ?? null,
                'branch_id' => $branch_id,
                'branch_name' => $branch_name,
                'subject_names' => isset($studentData['subject_names']) ? json_encode($studentData['subject_names']) : null,
                'studenthours' => isset($studentData['sessions']) ? json_encode($studentData['sessions']) : null,
                'target_grades' => isset($studentData['target_grades']) ? json_encode($studentData['target_grades']) : null,
                'current_grades' => isset($studentData['current_grades']) ? json_encode($studentData['current_grades']) : null,
                'tier' => isset($studentData['tiers']) ? json_encode($studentData['tiers']) : null,
                'qualifications' => isset($studentData['qualifications']) ? json_encode($studentData['qualifications']) : null,
            ]);

            $studentIds[] = $student->studentid;
            $studentName[] = trim(($studentData['firstName'] ?? '') . ' ' . ($studentData['middleName'] ?? '') . ' ' . ($studentData['lastName'] ?? ''));

            // Create Medical Condition
            medical_condition::create([
                'guardianid' => $guardian->Guardianid,
                'family_id' => $request->input('family_id', null),
                'student_id' => $student->studentid,
                'gpPrefix' => $studentData['gpPrefix'] ?? null,
                'drName' => trim(($studentData['gpFirstName'] ?? '') . ' ' . ($studentData['gpLastName'] ?? '')),
                'drNumber' => $studentData['GPPhone'] ?? null,
                // 'medicalDetails' => $studentData['medicalConditions'] ?? null,
                // 'allergies' => $studentData['allergies'] ?? null,
                'gpAddress' => $studentData['gpAddress'] ?? null,
                'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? null,
                'gp_city' => $studentData['gp_city'] ?? null,
                'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? null,
                'gpzipCode' => $studentData['gpzipCode'] ?? null,
                'gpcountry' => $studentData['gpcountry'] ?? null,
                'medicalConsent' => $studentData['medicalConsent'] ?? null,

                'medicalDetails' => $studentData['has_medical_conditions'] ?? null, //(!empty($studentData['medicalConditions']) && !empty($studentData['allergies'])) ? 'yes' : 'no',
                'medical_conditions_explanation' => $studentData['has_medical_conditions'] === 'yes' ? ($studentData['medical_conditions_explanation'] ?? null) : null,

                'allergies' =>  $studentData['has_allergies'] ?? null, //$studentData['allergies'] ?? null,
                'allergies_explanation' => $studentData['has_allergies'] === 'yes' ? ($studentData['allergies_explanation'] ?? null) : null,


                'branch_id' => $branch_id,
                'branch_name' => $branch_name,
            ]);
        }

        // Create TimeTable entries
        foreach ($studentName as $studentNamez) {
            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $day = strtoupper(date('l', strtotime("Sunday +{$dayIndex} days")));

                TimeTable::create([
                    'studentname' => $studentNamez,
                    'admissionid' => $request->input('family_id', null),
                    'day' => $day,
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                ]);
            }
        }

        // Update StudentRequest status
        $studentRequest = StudentRequest::where('id', $request->input('request_id'))->first();
        if ($studentRequest) {
            $studentRequest->is_approved = 1;
            $studentRequest->save();
        }
        try {
            // Retrieve PDF path from student_requests table
            $studentRequest = StudentRequest::where('id', $request->input('request_id'))->first();
            $attachPdf = false;
            $pdfPath = null;
            $pdfFileName = null;

            if ($studentRequest && $studentRequest->pdf_name) {
                $pdfPath = public_path($studentRequest->pdf_name);
                $pdfFileName = basename($studentRequest->pdf_name);
                if (file_exists($pdfPath)) {
                    $attachPdf = true;
                } else {
                    Log::error('PDF file not found at path: ' . $pdfPath);
                }
            } else {
                Log::error('No PDF found for request_id: ' . $request->input('request_id'));
            }

            // Determine branch admin email based on session('branch_id')
            $branchEmails = [
                'barking_centre_150' => 'admin@frobel.co.uk',
                'stratford_centre_272' => 'stratford@frobel.co.uk',
                'btec_qualifications_530' => 'grays@frobel.co.uk',
                'frobel_hayes_938' => 'hayes@frobel.co.uk',
            ];

            $branchEmail = $branchEmails[session('branch_id')] ?? null;
            if (!$branchEmail) {
                Log::error('No email defined for branch_id: ' . session('branch_id'));
                throw new \Exception('No email configured for this branch', 500);
            }

            // Email template
            $emailTemplate = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>%s</title>
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
                <!-- Logo -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
                </div>
                <!-- Main Content -->
                <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                    <h2 style="color: #2e6da4;">Dear %s,</h2>
                    %s
                    <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
                    <p>Kind regards,<br>
                    <strong>Frobel Education Team</strong><br>
                    Frobel Education</p>
                </div>
                <!-- Footer -->
                <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
                    <p>
                        67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
                        Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
                        Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a> |
                        Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a>
                    </p>
                    <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
                </div>
            </body>
            </html>';

            // Set email content based on whether PDF is available
            $emailContent = $attachPdf
                ? '<p>A new student registration form has been approved for the ' . htmlspecialchars($branch_name) . ' branch. Please find the details in the attached PDF.</p>'
                : '<p>A new student registration form has been approved for the ' . htmlspecialchars($branch_name) . ' branch. The PDF is not found. Please check the system for registration details.</p>';

            $adminEmailContent = sprintf(
                $emailTemplate,
                'New Student Registration',
                'Administrator',
                $emailContent
            );

            Mail::html($adminEmailContent, function ($message) use ($branchEmail, $attachPdf, $pdfPath, $pdfFileName, $studentRequest) {
                $message->to($branchEmail)
                    //   $message->to('inaam.chishti2@gmail.com')
                    ->subject('New Student Registration')
                    ->from('exams@frobel.co.uk', 'Frobel Learning');
                if ($attachPdf) {
                    $message->attach($pdfPath, [
                        'as' => $pdfFileName,
                        'mime' => 'application/pdf',
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error sending email to branch admin: ' . $e->getMessage());
            // Continue without interrupting the approval process
        }
        // Delete related notifications
        auth()->user()->notifications()
            ->whereRaw("JSON_EXTRACT(data, '$.request_id') = ?", [$request->request_id])
            ->delete();

        return redirect('fetch-student-registration')->with('success', 'Request approved successfully!');
    }

    public function deleteRequest($id)
    {

        $request = StudentRequest::find($id);

        if (!$request) {
            return redirect()->back()->with('error', 'Request not found.');
        }

        // Delete the request
        $request->delete();

        // Debugging: Check if notification exists
        $notification = auth()->user()->notifications()
            ->whereRaw("JSON_EXTRACT(data, '$.request_id') = ?", [$id])
            ->first();

        if ($notification) {
            $notification->delete();
        } else {
        }
        return redirect()->back()->with('success', 'Request deleted successfully.');
    }


    public function centralTimeTable()
    {
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $teachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->select('teacher_name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->get();

        $subjects = DB::table('subjects')
            ->select('name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->pluck('name');


        $termBreak = TermBreak::where('branch_id', session('branch_id'))->first();
        return view('branchFrontend.centralTimeTable.index', compact('teachers', 'subjects', 'termBreak'));
    }

    public function getGeneralName($id, Request $request)
    {
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $query = $request->query('query');

        // Get students based on the query
        $students = Student::where('branch_id', session('branch_id'))
            ->where(function ($q) {
                $q->whereNull('student_status')
                    ->orWhere('student_status', '')
                    ->orWhere('student_status', 'active');
            })
            ->where(function ($queryBuilder) use ($query) {
                // Check if the query is numeric (admission ID)
                if (is_numeric($query)) {
                    $queryBuilder->where('admissionid', 'like', '%' . $query . '%');
                } else {
                    // Search by student name or surname
                    $queryBuilder->where('studentname', 'like', '%' . $query . '%')
                        ->orWhere('studentsur', 'like', '%' . $query . '%');
                }
            })
            ->get();

        // Concatenate name and admission ID
        $students = $students->map(function ($student) {
            $student->full_name_with_admission = $student->studentname . ' ' . $student->studentsur . ' - ' . $student->admissionid;
            return $student;
        });
        // dd($students);
        return response()->json($students);
    }
     public function storeGeneralTimetable(Request $request)
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 60);
        // dd($request->all());
        // $record = GeneralTimetable::find($request->input('class-id'));

        // if ($record) {
        //     $record->additional_info = $request->additional_info ?? null;
        //     $success = $record->save();

        // }



        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $data = $request->all();
        $teacherId = $data['teacher'];
        $students = json_decode($data['students'], true);
        $yearinschools = json_decode($data['yearinschools'], true);
        $subjects = json_decode($data['subjects'], true);
        $attendance = json_decode($data['attendance'], true);
        $homeworks = json_decode($data['homeworks'], true); // Added to parse homeworks
        $books = json_decode($data['books'], true);
        $chapters = json_decode($data['chapters'], true);
        $permanents = json_decode($data['permanents'], true) ?? [];
        $attendanceTypes = json_decode($data['attendance_types'] ?? '[]', true) ?? [];
        $behaviours = json_decode($data['behaviours'] ?? '[]', true) ?? [];
        $performances = json_decode($data['performances'] ?? '[]', true) ?? [];

        $studentIds = [];
        $studentNames = [];
        foreach ($students as $student) {
            $parts = preg_split('/\s+/', $student);
            if (count($parts) >= 2) {
                $studentIds[] = trim($parts[0]);
                $studentNames[] = trim(implode(' ', array_slice($parts, 1)));
            }
        }

        $classId = isset($data['class-id']) ? $data['class-id'] : null;
        $existingMoveFromPage = '';
        $existingStudentIds = [];
        $existingStudentNames = [];
        $existingPermanent = [];
        if ($classId) {
            // ⚠️ MISSING branch_id filter - FIXED
            $existingRecord = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $classId)->first();
            if ($existingRecord) {
                // Get existing move_from_page value
                $existingMoveFromPage = $existingRecord->move_from_page ?? '';
                // Get existing students to compare with new ones (Eloquent may cast JSON columns to arrays)
                $existingStudentIds = is_array($existingRecord->student_ids)
                    ? $existingRecord->student_ids
                    : (is_string($existingRecord->student_ids) ? (json_decode($existingRecord->student_ids, true) ?? []) : []);
                $existingStudentNames = is_array($existingRecord->student_names)
                    ? $existingRecord->student_names
                    : (is_string($existingRecord->student_names) ? (json_decode($existingRecord->student_names, true) ?? []) : []);
                // Get existing permanent status to preserve students' status
                $existingPermanent = is_array($existingRecord->permanent)
                    ? $existingRecord->permanent
                    : (is_string($existingRecord->permanent) ? (json_decode($existingRecord->permanent, true) ?? []) : []);

                // Comprehensive deletion logic - ONLY delete records belonging to THIS specific class chain
                // Logic: Pick parent_id from existing class, if null then use main id (primary key)
                // Only delete records where parent_id == existing class's parent_id (or if null, then where parent_id == existing class's id)

                $existingRecordId = $existingRecord->id;
                $existingParentId = $existingRecord->parent_id;

                // Determine which parent_id to use for deletion
                // If existing record's parent_id is null, use its own id
                // If existing record has a parent_id, use that parent_id
                $parentIdForDeletion = ($existingParentId === null || $existingParentId == $existingRecordId)
                    ? $existingRecordId
                    : $existingParentId;

                // Delete ONLY records that belong to this specific class chain
                // Delete records where parent_id matches the parent_id we determined above
                $deletedCount = GeneralTimetable::where(function($query) use ($parentIdForDeletion, $classId) {
                    // Delete the existing record itself
                    $query->where('id', $classId);

                    // Delete all records where parent_id matches the parent_id for this class chain
                    $query->orWhere('parent_id', $parentIdForDeletion);
                })
                ->where('branch_id', session('branch_id'))
                ->whereDate('date', '>=', $data['selected-day'])
                ->delete();

                // Log deletion for debugging
                \Log::info('Deleted future records for class update', [
                    'class_id' => $classId,
                    'existing_record_id' => $existingRecordId,
                    'existing_parent_id' => $existingParentId,
                    'parent_id_for_deletion' => $parentIdForDeletion,
                    'selected_day' => $data['selected-day'],
                    'branch_id' => session('branch_id'),
                    'deleted_count' => $deletedCount
                ]);
            }
        }

        $selectedDay = $data['selected-day'];
        
        // ✅ FIX: Check for duplicate classes before creating (only for new classes, not updates)
        if (!$classId) {
            $selectedSlot = $data['selected-slot'];
            $existingDuplicate = GeneralTimetable::where('branch_id', session('branch_id'))
                ->where('date', $selectedDay)
                ->where('slot', $selectedSlot)
                ->where('teacher_id', $teacherId)
                ->where(function ($query) {
                    $query->whereNull('additional_student')
                        ->orWhere('additional_student', '');
                })
                ->first();
            
            if ($existingDuplicate) {
                // Duplicate found - delete it first (same logic as update)
                $existingRecordId = $existingDuplicate->id;
                $existingParentId = $existingDuplicate->parent_id;
                $parentIdForDeletion = ($existingParentId === null || $existingParentId == $existingRecordId)
                    ? $existingRecordId
                    : $existingParentId;
                
                $deletedCount = GeneralTimetable::where(function($query) use ($parentIdForDeletion, $existingRecordId) {
                    $query->where('id', $existingRecordId)
                        ->orWhere('parent_id', $parentIdForDeletion);
                })
                ->where('branch_id', session('branch_id'))
                ->whereDate('date', '>=', $selectedDay)
                ->delete();
                
                \Log::info('storeGeneralTimetable - Removed duplicate class before creating new one', [
                    'deleted_id' => $existingRecordId,
                    'teacher_id' => $teacherId,
                    'slot' => $selectedSlot,
                    'date' => $selectedDay,
                    'deleted_count' => $deletedCount
                ]);
            }
        }
        $dayName = strtolower(date('l', strtotime($selectedDay)));
        $currentDay = date('w', strtotime($selectedDay));
        $records = [];

        // ✅ FIRST: Preserve existing students' status (BOTH permanent AND temporary) when updating a class
        // This must happen BEFORE padding to ensure we don't overwrite existing statuses
        if ($classId && !empty($existingStudentIds) && !empty($existingPermanent)) {
            foreach ($studentIds as $index => $studentId) {
                if (isset($studentNames[$index]) && !empty($studentNames[$index])) {
                    // Find this student in existing record
                    foreach ($existingStudentIds as $j => $existingId) {
                        if (
                            $existingId == $studentId &&
                            isset($existingStudentNames[$j]) &&
                            trim($existingStudentNames[$j]) == trim($studentNames[$index]) &&
                            isset($existingPermanent[$j])
                        ) {
                            // Student exists in old record - preserve their permanent/temporary status
                            $existingPermanentStatus = $existingPermanent[$j];
                            $permanents[$index] = $existingPermanentStatus;
                            
                            // Also update attendance type to match
                            if ($existingPermanentStatus === 'No') {
                                // Temporary student - use 'Switch'
                                if (isset($attendanceTypes[$index])) {
                                    $currentType = $attendanceTypes[$index];
                                    $validTypes = ['Permanent', 'Switch', 'Compensation'];
                                    if (!in_array($currentType, $validTypes) || $currentType === 'Permanent') {
                                        $attendanceTypes[$index] = 'Switch';
                                    }
                                } else {
                                    $attendanceTypes[$index] = 'Switch';
                                }
                            } else {
                                // Permanent student - use 'Permanent'
                                if (isset($attendanceTypes[$index])) {
                                    $currentType = $attendanceTypes[$index];
                                    $validTypes = ['Permanent', 'Switch', 'Compensation'];
                                    if (!in_array($currentType, $validTypes)) {
                                        $attendanceTypes[$index] = 'Permanent';
                                    }
                                } else {
                                    $attendanceTypes[$index] = 'Permanent';
                                }
                            }
                            break;
                        }
                    }
                }
            }
        }

        // ✅ THEN: Pad arrays with defaults ONLY for NEW students (students not in existing record)
        $studentCount = count($studentIds);
        for ($index = 0; $index < $studentCount; $index++) {
            // Only pad if this index doesn't have a value AND student is NOT in existing record
            $isExistingStudent = false;
            if ($classId && !empty($existingStudentIds)) {
                if (isset($studentIds[$index]) && isset($studentNames[$index])) {
                    foreach ($existingStudentIds as $j => $existingId) {
                        if (
                            $existingId == $studentIds[$index] &&
                            isset($existingStudentNames[$j]) &&
                            trim($existingStudentNames[$j]) == trim($studentNames[$index])
                        ) {
                            $isExistingStudent = true;
                            break;
                        }
                    }
                }
            }
            
            // Only pad for NEW students (not in existing record)
            if (!$isExistingStudent) {
                if (!isset($permanents[$index]) || $permanents[$index] === '') {
                    $permanents[$index] = 'Yes'; // Default to permanent for new students
                }
                if (!isset($attendanceTypes[$index]) || $attendanceTypes[$index] === '') {
                    $attendanceTypes[$index] = 'Permanent'; // Default to permanent for new students
                }
            }
        }

        // Prepare arrays to separate permanent and non-permanent students
        $permanentStudentIds = [];
        $permanentStudentNames = [];
        $permanentSubjects = [];
        $permanentAttendance = [];
        $permanentBehaviours = [];
        $permanentPerformances = [];
        $permanentPermanents = [];
        $nonPermanentStudentIds = [];
        $nonPermanentStudentNames = [];
        $nonPermanentSubjects = [];
        $nonPermanentBehaviours = [];
        $nonPermanentPerformances = [];

        foreach ($studentIds as $index => $studentId) {
            $permanentValue = $permanents[$index] ?? 'Yes';
            if ($permanentValue === 'Yes') {
                $permanentStudentIds[] = $studentId;
                $permanentStudentNames[] = $studentNames[$index];
                $permanentSubjects[] = $subjects[$index];
                $permanentAttendance[] = $attendance[$index];
                $permanentPermanents[] = $permanentValue;
                $permanentBehaviours[] = $behaviours[$index] ?? 'null';
                $permanentPerformances[] = $performances[$index] ?? 'null';
            } else {
                $nonPermanentStudentIds[] = $studentId;
                $nonPermanentStudentNames[] = $studentNames[$index];
                $nonPermanentSubjects[] = $subjects[$index];
                $nonPermanentBehaviours[] = $behaviours[$index] ?? 'null';
                $nonPermanentPerformances[] = $performances[$index] ?? 'null';
            }
        }

        $encodedPermanentStudentIds = json_encode($permanentStudentIds);
        $encodedPermanentStudentNames = json_encode($permanentStudentNames);
        $encodedPermanentSubjects = json_encode($permanentSubjects);
        $encodedPermanentAttendance = json_encode($permanentAttendance);
        $encodedPermanentPermanents = json_encode($permanentPermanents);
        $encodedNonPermanentStudentIds = json_encode($nonPermanentStudentIds);
        $encodedNonPermanentStudentNames = json_encode($nonPermanentStudentNames);
        $encodedNonPermanentSubjects = json_encode($nonPermanentSubjects);

        // Prepare move_from_page data for updates - only add newly added/moved students
        $moveFromPageValue = null; // Initialize to null for new records
        if ($classId && !empty($existingStudentIds)) {
            $newStudentsEntries = [];

            // Build a map of existing students for quick lookup
            // Format: "family_id name" as key
            $existingStudentsMap = [];
            foreach ($existingStudentIds as $idx => $existingId) {
                if (isset($existingStudentNames[$idx])) {
                    $existingName = trim($existingStudentNames[$idx]);
                    if (!empty($existingName)) {
                        $key = trim($existingId) . ' ' . $existingName;
                        $existingStudentsMap[$key] = true;
                    }
                }
            }

            // Check which students are NEW (not in existing record)
            foreach ($studentIds as $index => $studentId) {
                if (isset($studentNames[$index]) && !empty($studentNames[$index])) {
                    $entry = trim($studentId) . ' ' . trim($studentNames[$index]);
                    // Only add if this student is not in the existing record
                    if (!isset($existingStudentsMap[$entry])) {
                        $newStudentsEntries[] = $entry;
                    }
                }
            }

            // Only update move_from_page if there are new students
            if (!empty($newStudentsEntries)) {
                // Start with existing move_from_page entries
                $allEntries = [];
                if (!empty($existingMoveFromPage)) {
                    $existingEntries = array_map('trim', explode(',', $existingMoveFromPage));
                    $existingEntries = array_filter($existingEntries); // Remove empty entries
                    $allEntries = array_merge($allEntries, $existingEntries);
                }

                // Add new student entries
                $allEntries = array_merge($allEntries, $newStudentsEntries);

                // Remove duplicates while preserving order
                $allEntries = array_unique($allEntries);
                $moveFromPageValue = implode(', ', $allEntries);
            } else {
                // No new students, keep existing move_from_page value
                $moveFromPageValue = $existingMoveFromPage;
            }
        } elseif ($classId && empty($existingStudentIds)) {
            // Existing record but no students - add all current students as new
            $newStudentsEntries = [];
            foreach ($studentIds as $index => $studentId) {
                if (isset($studentNames[$index]) && !empty($studentNames[$index])) {
                    $entry = trim($studentId) . ' ' . trim($studentNames[$index]);
                    $newStudentsEntries[] = $entry;
                }
            }

            if (!empty($newStudentsEntries)) {
                $allEntries = [];
                if (!empty($existingMoveFromPage)) {
                    $existingEntries = array_map('trim', explode(',', $existingMoveFromPage));
                    $existingEntries = array_filter($existingEntries);
                    $allEntries = array_merge($allEntries, $existingEntries);
                }
                $allEntries = array_merge($allEntries, $newStudentsEntries);
                $allEntries = array_unique($allEntries);
                $moveFromPageValue = implode(', ', $allEntries);
            } else {
                $moveFromPageValue = $existingMoveFromPage;
            }
        }

        // Create record for the selected day (all students + attendance + permanents)
        $isTermBreakSlot = in_array((string)($data['selected-slot'] ?? ''), ['7', '8'], true);
        $sessionTypeForSave = $isTermBreakSlot ? 'termbreak_scheduler' : null;
        $mainRecordData = [
            'date' => $selectedDay,
            'slot' => $data['selected-slot'],
            'teacher_id' => $teacherId,
            'time_slot' => json_encode($data['students']), // This will be updated below
            'student_ids' => json_encode($studentIds),
            'student_names' => json_encode($studentNames),
            'subjects' => json_encode($subjects),
            'parent_id' => null,
            'is_attendance' => json_encode($attendance),
            'permanent' => json_encode($permanents),
            'branch_id' => $branch_id,
            'branch_name' => $branch_name,
            'behaviours' => json_encode($behaviours),
            'performances' => json_encode($performances),
            'move_from_page' => $moveFromPageValue,
            'session_type' => $sessionTypeForSave,
        ];

        $records[] = $mainRecordData;

        // Create future records for permanent students
        $currentDate = strtotime($selectedDay . ' +1 week');
        $endDate = strtotime('+1 year', $currentDate);
        $slotIndex = $data['selected-slot'];

        // Handle term break sessions (slots 7 and 8)
        $isTermBreak = in_array($slotIndex, ['7', '8']);

        if ($isTermBreak) {
            // Term break sessions are scheduled daily
            while ($currentDate <= $endDate) {
                $records[] = [
                    'date' => date('Y-m-d', $currentDate),
                    'slot' => $data['selected-slot'],
                    'teacher_id' => $teacherId,
                    'time_slot' => json_encode($data['students']),
                    'student_ids' => $encodedPermanentStudentIds,
                    'student_names' => $encodedPermanentStudentNames,
                    'subjects' => $encodedPermanentSubjects,
                    'parent_id' => null,
                    'is_attendance' => '',
                    'permanent' => $encodedPermanentPermanents,
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                    'session_type' => $sessionTypeForSave,
                ];
                $currentDate = strtotime('+1 week', $currentDate);
            }
        } else {
            // Regular weekday or weekend sessions
            if (in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
                while ($currentDate <= $endDate) {
                    $dayOfWeek = strtolower(date('l', $currentDate));
                    if (in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
                        $records[] = [
                            'date' => date('Y-m-d', $currentDate),
                            'slot' => $data['selected-slot'],
                            'teacher_id' => $teacherId,
                            'time_slot' => json_encode($data['students']),
                            'student_ids' => $encodedPermanentStudentIds,
                            'student_names' => $encodedPermanentStudentNames,
                            'subjects' => $encodedPermanentSubjects,
                            'parent_id' => null,
                            'is_attendance' => '',
                            'permanent' => $encodedPermanentPermanents,
                            'branch_id' => $branch_id,
                            'branch_name' => $branch_name,
                            'session_type' => $sessionTypeForSave,
                        ];
                    }
                    $currentDate = strtotime('+1 week', $currentDate);
                }
            } elseif (in_array($dayName, ['saturday', 'sunday'])) {
                while ($currentDate <= $endDate) {
                    $dayOfWeek = strtolower(date('l', $currentDate));
                    if (in_array($dayOfWeek, ['saturday', 'sunday'])) {
                        $records[] = [
                            'date' => date('Y-m-d', $currentDate),
                            'slot' => $data['selected-slot'],
                            'teacher_id' => $teacherId,
                            'time_slot' => json_encode($data['students']),
                            'student_ids' => $encodedPermanentStudentIds,
                            'student_names' => $encodedPermanentStudentNames,
                            'subjects' => $encodedPermanentSubjects,
                            'parent_id' => null,
                            'is_attendance' => '',
                            'permanent' => $encodedPermanentPermanents,
                            'branch_id' => $branch_id,
                            'branch_name' => $branch_name,
                            'session_type' => $sessionTypeForSave,
                        ];
                    }
                    $currentDate = strtotime('+1 week', $currentDate);
                }
            }
        }

        // Attendance logic
        // Use selected-day instead of current date to allow marking attendance for past dates
        $attendanceDate = $selectedDay;
        $teacherName = $data['teacher'];
        $slotIndex = $data['selected-slot'];
        $timePeriod = '';
        $session_1 = '';

        // Map slot numbers to dropdown time values
        $timeSlotMapping = [
            '1' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '09:00 - 11:00am', 'label' => 'Lesson 1'],
            '2' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '11:20 - 01:20pm', 'label' => 'Lesson 2'],
            '3' => ['weekday' => '04:30 - 06:30pm', 'weekend' => '02:00 - 04:00pm', 'label' => 'Lesson 3'],
            '4' => ['weekday' => '06:45 - 08:45pm', 'weekend' => '', 'label' => 'Lesson 4'],
            '7' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '11:00 - 01:00pm', 'label' => 'Term Break Session 1'],
            '8' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '01:30 - 03:30pm', 'label' => 'Term Break Session 2'],
        ];

        // Determine time period and session based on slot and day
        if (isset($timeSlotMapping[$slotIndex])) {
            $timePeriod = ($currentDay >= 1 && $currentDay <= 5) ? $timeSlotMapping[$slotIndex]['weekday'] : $timeSlotMapping[$slotIndex]['weekend'];
            $session_1 = $timeSlotMapping[$slotIndex]['label'];
        } else {
            $timePeriod = 'Unknown Slot';
            $session_1 = 'Unknown Session';
        }

        $family_ids = [];
        $names = [];

        foreach ($students as $student) {
            if (preg_match('/^(\d+)\s+(.*)$/', $student, $matches)) {
                $family_ids[] = $matches[1];
                $names[] = trim($matches[2]);
            }
        }

        foreach ($family_ids as $index => $familyId) {

            $book = $books[$index] ?? '';
            $chapter = $chapters[$index] ?? '';
            $bk_ch = $book . '-' . $chapter;
            Log::info("Processing row $index: Book = $book, Chapter = $chapter, Combined = $bk_ch");
            // Prevent attendance marking if book is "Select Book" or empty, or chapter is empty

            if ($book === 'Select Book' || empty($book) || empty($chapter)) {
                continue; // Skip attendance recording
            }
            // dd($bk_ch);
            $studentRecord = Student::where('admissionid', $familyId)
                ->whereRaw('LOWER(CONCAT(studentname, " ", COALESCE(studentsur, ""))) LIKE ?', ['%' . strtolower($names[$index]) . '%'])
                ->where('branch_id', session('branch_id'))
                ->first();

            $attendanceRecord = Attendance::where('family_id', $familyId)
                ->where('date', $attendanceDate)
                ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                ->where('time_slot', $timePeriod)
                ->where('branch_id', session('branch_id'))
                ->first();

            if ($attendance[$index] === 'No' && $attendanceRecord) {
                $attendanceRecord->delete();
                continue;
            }

            if ($attendance[$index] === 'Yes') {
                // Check session limit for current week (for both new and existing attendance records)
                $subject = $subjects[$index] ?? '';
                if (!empty($subject)) {
                    // Get student data to check session limits
                    $studentData = DB::table('studentdata')
                        ->where('branch_id', session('branch_id'))
                        ->where('admissionid', $familyId)
                        ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), 'LIKE', '%' . $names[$index] . '%')
                        ->select('studentid', 'subject_names', 'studenthours')
                        ->first();

                    if ($studentData) {
                        $subject_names = $studentData->subject_names ? json_decode($studentData->subject_names, true) : [];
                        $studenthours = $studentData->studenthours ? json_decode($studentData->studenthours, true) : [];

                        // Calculate TOTAL weekly quota (sum of all subjects) - flexible usage across subjects
                        $totalWeeklyQuota = 0;
                        if (!empty($studenthours) && is_array($studenthours)) {
                            foreach ($studenthours as $hours) {
                                $totalWeeklyQuota += (int)$hours;
                            }
                        }

                        if ($totalWeeklyQuota > 0) {
                            // Calculate current week (Monday to Sunday)
                            $selectedDateTimestamp = strtotime($attendanceDate);
                            $dayOfWeek = date('w', $selectedDateTimestamp);
                            $mondayOffset = ($dayOfWeek == 0) ? -6 : (1 - $dayOfWeek);
                            $mondayOfWeek = date('Y-m-d', $selectedDateTimestamp + ($mondayOffset * 86400));
                            $sundayOfWeek = date('Y-m-d', strtotime($mondayOfWeek . ' +6 days'));

                            // Count current week attendance for this student across ALL subjects (not just current subject)
                            $currentWeekAttendanceQuery = Attendance::where('branch_id', session('branch_id'))
                                ->where('family_id', $familyId)
                                ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                                ->whereBetween('date', [$mondayOfWeek, $sundayOfWeek]);

                            // Exclude current attendance record if updating existing one
                            if ($attendanceRecord) {
                                $currentWeekAttendanceQuery->where('id', '!=', $attendanceRecord->id);
                            }

                            $currentWeekAttendanceCount = $currentWeekAttendanceQuery->count();

                            // Calculate carry forward from previous weeks
                            // Get first date from attendance where attendance_type is NOT NULL and date >= 2025-12-01
                            $globalResetDate = '2025-12-01';
                            $firstTimetableDateRaw = DB::table('attendance')
                                ->where('branch_id', session('branch_id'))
                                ->where('family_id', $familyId)
                                ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                                ->whereNotNull('attendance_type')
                                ->where('date', '>=', $globalResetDate)
                                ->orderBy('date', 'asc')
                                ->value('date');

                            $firstTimetableDate = null;
                            if ($firstTimetableDateRaw) {
                                // Use the first attendance date (already >= 2025-12-01)
                                $firstTimetableDate = $firstTimetableDateRaw;
                            } else {
                                // If no attendance found, check if there's any attendance before reset date
                                $oldestAttendance = DB::table('attendance')
                                    ->where('branch_id', session('branch_id'))
                                    ->where('family_id', $familyId)
                                    ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                                    ->whereNotNull('attendance_type')
                                    ->orderBy('date', 'asc')
                                    ->value('date');
                                
                                if ($oldestAttendance && $oldestAttendance < $globalResetDate) {
                                    // Existing student - use global reset date
                                    $firstTimetableDate = $globalResetDate;
                                }
                            }

                            // Calculate carry forward from ALL previous weeks (unused lessons accumulate)
                            // Example: Week 1 quota 2, took 1 → 1 unused carries forward
                            // Week 2 can use: Week 2 quota (2) + Carry forward (1) = 3 total
                            $carryForwardSessions = 0;
                            if ($firstTimetableDate && $totalWeeklyQuota > 0) {
                                // Calculate all previous weeks from first timetable date (when student was added) to current week
                                $firstDateTimestamp = strtotime($firstTimetableDate);
                                $firstDayOfWeek = date('w', $firstDateTimestamp);
                                $firstMondayOffset = ($firstDayOfWeek == 0) ? -6 : (1 - $firstDayOfWeek);
                                $firstMondayOfWeek = date('Y-m-d', $firstDateTimestamp + ($firstMondayOffset * 86400));

                                // Calculate previous weeks (before current week)
                                $previousWeekSunday = date('Y-m-d', strtotime($mondayOfWeek . ' -1 day'));

                                // Only calculate if there are previous weeks
                                if ($firstMondayOfWeek < $mondayOfWeek) {
                                    // Count ALL attendance in previous weeks (from first date to previous week Sunday)
                                    $previousWeeksAttendance = Attendance::where('branch_id', session('branch_id'))
                                        ->where('family_id', $familyId)
                                        ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                                        ->whereBetween('date', [$firstMondayOfWeek, $previousWeekSunday])
                                        ->count();

                                    // Calculate how many complete weeks have passed from first week to previous week
                                    $daysDiff = strtotime($previousWeekSunday) - strtotime($firstMondayOfWeek);
                                    $weeksPassed = floor($daysDiff / (7 * 24 * 60 * 60)) + 1; // +1 to include first week

                                    // Total quota available in all previous weeks
                                    $totalQuotaForPreviousWeeks = $weeksPassed * $totalWeeklyQuota;

                                    // Carry forward = Unused lessons from ALL previous weeks
                                    // If 3 weeks passed with quota 2 each = 6 total, and 5 lessons taken, carry forward = 1
                                    $carryForwardSessions = max(0, $totalQuotaForPreviousWeeks - $previousWeeksAttendance);
                                }
                            }

                            // Total available sessions = Current week quota + Carry forward from ALL previous weeks
                            // This allows student to use unused lessons from any previous week in current/future weeks
                            $totalAvailableSessions = $totalWeeklyQuota + $carryForwardSessions;

                            // Calculate total remaining lessons using SAME monthly breakdown logic as lesson tracking report
                            // This ensures consistency - if report shows remaining lessons, allow using them
                            $totalRemainingLessons = 0;
                            
                            if ($firstTimetableDate && $totalWeeklyQuota > 0) {
                                $today = date('Y-m-d');
                                
                                // Get all attendance from first date to today
                                $allAttendanceRecords = Attendance::where('branch_id', session('branch_id'))
                                    ->where('family_id', $familyId)
                                    ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
                                    ->whereBetween('date', [$firstTimetableDate, $today])
                                    ->select('date')
                                    ->get();
                                
                                // Use same monthly breakdown logic as getLessonTrackingData
                                $firstDateTimestamp = strtotime($firstTimetableDate);
                                $firstMonth = date('Y-m', $firstDateTimestamp);
                                $currentMonth = date('Y-m');
                                
                                $carryForwardUnusedQuota = 0;
                                $carryForwardIncompleteDays = 0;
                                $monthIterator = $firstMonth;
                                $isFirstMonth = true;
                                
                                // Get quota history for historical quota calculation
                                $quotaHistory = collect([]);
                                if ($studentData && isset($studentData->studentid)) {
                                    $quotaHistory = DB::table('quota_history')
                                        ->where('branch_id', session('branch_id'))
                                        ->where('studentid', $studentData->studentid)
                                        ->orderBy('change_date', 'desc')
                                        ->get();
                                }
                                
                                $getQuotaForDate = function($date) use ($quotaHistory, $totalWeeklyQuota, $firstTimetableDate) {
                                    if ($quotaHistory->isEmpty()) {
                                        return $totalWeeklyQuota;
                                    }
                                    $sortedHistory = $quotaHistory->sortBy('change_date');
                                    $applicableQuota = null;
                                    foreach ($sortedHistory as $change) {
                                        if ($change->change_date <= $date) {
                                            $applicableQuota = $change->new_total_weekly_quota;
                                        } else {
                                            break;
                                        }
                                    }
                                    if ($applicableQuota === null) {
                                        $firstChange = $sortedHistory->first();
                                        if ($firstChange) {
                                            $applicableQuota = $firstChange->old_total_weekly_quota;
                                        }
                                    }
                                    return $applicableQuota ?? $totalWeeklyQuota;
                                };
                                
                                while ($monthIterator <= $currentMonth) {
                                    $monthStartDate = $monthIterator . '-01';
                                    $monthEndDate = date('Y-m-t', strtotime($monthStartDate));
                                    
                                    // Count attendance in this month
                                    $attendanceStartDate = max($monthStartDate, $firstTimetableDate);
                                    $monthAttendance = 0;
                                    foreach ($allAttendanceRecords as $record) {
                                        $recordDate = $record->date;
                                        if ($recordDate >= $attendanceStartDate && $recordDate <= $monthEndDate) {
                                            $monthAttendance++;
                                        }
                                    }
                                    
                                    // Calculate period start date
                                    if ($isFirstMonth) {
                                        $periodStartDate = $firstTimetableDate;
                                        $isFirstMonth = false;
                                    } else {
                                        $periodStartDate = $monthStartDate;
                                    }
                                    
                                    if ($carryForwardIncompleteDays > 0) {
                                        $periodStartDate = date('Y-m-d', strtotime($periodStartDate . ' -' . $carryForwardIncompleteDays . ' days'));
                                    }
                                    
                                    $startTimestamp = strtotime($periodStartDate);
                                    $endTimestamp = strtotime($monthEndDate);
                                    $totalDays = floor(($endTimestamp - $startTimestamp) / (24 * 60 * 60)) + 1;
                                    
                                    $completeWeeks = floor($totalDays / 7);
                                    $remainingDays = $totalDays % 7;
                                    
                                    // Get quota for this month
                                    $monthQuota = $getQuotaForDate($monthEndDate);
                                    $monthlyQuota = $completeWeeks * $monthQuota;
                                    
                                    // Available = Monthly quota + Carry forward
                                    $availableThisMonth = $monthlyQuota + $carryForwardUnusedQuota;
                                    
                                    // Remaining = Available - Taken
                                    $remainingThisMonth = max(0, $availableThisMonth - $monthAttendance);
                                    
                                    // For current month, use the remaining
                                    if ($monthIterator == $currentMonth) {
                                        $totalRemainingLessons = $remainingThisMonth;
                                        break;
                                    }
                                    
                                    // Carry forward to next month = remaining
                                    $carryForwardUnusedQuota = $remainingThisMonth;
                                    $carryForwardIncompleteDays = $remainingDays;
                                    
                                    // Move to next month
                                    $monthIterator = date('Y-m', strtotime($monthIterator . '-01 +1 month'));
                                }
                            }

                            // Validate: Allow attendance if there are remaining lessons from monthly breakdown
                            // If monthly breakdown shows remaining lessons, allow using them even if weekly quota is exhausted
                            if ($totalRemainingLessons > 0) {
                                // Allow attendance - there are remaining lessons available
                            } elseif ($totalAvailableSessions > 0 && $currentWeekAttendanceCount >= $totalAvailableSessions) {
                                // No remaining lessons and weekly quota exhausted - skip attendance
                                continue; // Skip this attendance record - quota limit reached
                            }
                        }
                    }
                }

                // ✅ Check if student is temporary (permanent='No')
                $permanentValue = $permanents[$index] ?? 'Yes';
                $rawAttendanceType = $attendanceTypes[$index] ?? null;
                $validTypes = ['Permanent', 'Switch', 'Compensation'];
                if (!in_array($rawAttendanceType, $validTypes)) {
                    $rawAttendanceType = $permanentValue === 'Yes' ? 'Permanent' : 'Switch';
                }
                $isTemporaryStudent = $permanentValue === 'No';
                $adjustmentValue = $isTemporaryStudent ? 'yes' : null;

                if ($attendanceRecord) {
                    // dd($index,$behaviours,$performances,$behaviours[$index],$performances[$index],$attendance,$homeworks);

                    // ✅ FIX: Always use studentdata.studentyearinschool as source of truth
                    // Only fallback to form yearinschools if studentRecord doesn't exist
                    $yearInSchool = $studentRecord ? ($studentRecord->studentyearinschool ?? null) : ($yearinschools[$index] ?? null);

                    $attendanceRecord->update([
                        'family_id' => $familyId,
                        'student_name' => $names[$index],
                        'student_year_in_school' => $yearInSchool,
                        'bk_ch' => $bk_ch,
                        'status' => $homeworks[$index],
                        'date' => $attendanceDate,
                        'behaviour' => $behaviours[$index],
                        'performance' => $performances[$index],
                        'teacher_name' => $teacherName,
                        'subject' => $subjects[$index] ?? 'select subject',
                        'time_slot' => $timePeriod,
                        'session_1' => $session_1,
                        'branch_id' => $branch_id,
                        'branch_name' => $branch_name,
                        'additional_info' => $request->additional_info,
                        'adjustment' => $adjustmentValue, // ✅ Set adjustment based on permanent status
                        'attendance_type' => $rawAttendanceType,
                    ]);
                } else {
                    // ✅ FIX: Always use studentdata.studentyearinschool as source of truth
                    // Only fallback to form yearinschools if studentRecord doesn't exist
                    $yearInSchool = $studentRecord ? ($studentRecord->studentyearinschool ?? null) : ($yearinschools[$index] ?? null);

                    $attendanceRecord = new Attendance();
                    $attendanceRecord->family_id = $familyId;
                    $attendanceRecord->student_name = $names[$index];
                    $attendanceRecord->student_year_in_school = $yearInSchool;
                    $attendanceRecord->bk_ch = $bk_ch;
                    $attendanceRecord->status = $homeworks[$index];

                    $attendanceRecord->behaviour = $behaviours[$index];
                    $attendanceRecord->performance = $performances[$index];

                    $attendanceRecord->date = $attendanceDate;
                    $attendanceRecord->teacher_name = $teacherName;
                    $attendanceRecord->subject = $subjects[$index] ?? 'select subject';
                    $attendanceRecord->time_slot = $timePeriod;

                    $attendanceRecord->session_1 = $session_1;
                    $attendanceRecord->branch_id = $branch_id;
                    $attendanceRecord->branch_name = $branch_name;
                    $attendanceRecord->additional_info = $request->additional_info;
                    $attendanceRecord->adjustment = $adjustmentValue; // ✅ Set adjustment based on permanent status
                    $attendanceRecord->attendance_type = $rawAttendanceType;
                    $attendanceRecord->save();
                }
            }
        }

        // Update time_slot for all records to match dropdown format
        foreach ($records as &$record) {
            if (isset($timeSlotMapping[$record['slot']])) {
                $record['time_slot'] = ($currentDay >= 1 && $currentDay <= 5) ? $timeSlotMapping[$record['slot']]['weekday'] : $timeSlotMapping[$record['slot']]['weekend'];
            } else {
                $record['time_slot'] = 'Unknown Slot';
            }
        }

        // Save timetable records
        // Note: Removed the safety check that was deleting by teacher_id + slot
        // This was causing deletion of unrelated classes. Now we only delete by parent_id relationship.

        $mainRecord = GeneralTimetable::create(array_shift($records));
        foreach ($records as &$record) {
            $record['parent_id'] = $mainRecord->id;
            $record['branch_id'] = $branch_id;
            $record['branch_name'] = $branch_name;
        }
        // GeneralTimetable::insert($records);
        foreach ($records as $recordData) {
            GeneralTimetable::create($recordData);
        }

        $mainID = $mainRecord->id;
        $data = GeneralTimetable::where('id', $mainID)
            ->where('branch_id', session('branch_id'))
            ->first();
        $child = GeneralTimetable::where('parent_id', $mainID)
            ->where('branch_id', session('branch_id'))
            ->get();
        foreach ($child as $ch) {
            $ch->student_ids = $encodedPermanentStudentIds;
            $ch->student_names = $encodedPermanentStudentNames;
            $ch->subjects = $encodedPermanentSubjects;
            $ch->is_attendance = '';
            $ch->permanent = $encodedPermanentPermanents;
            $ch->branch_id = $branch_id;
            $ch->branch_name = $branch_name;
            $ch->time_slot = isset($timeSlotMapping[$ch->slot]) ? (($currentDay >= 1 && $currentDay <= 5) ? $timeSlotMapping[$ch->slot]['weekday'] : $timeSlotMapping[$ch->slot]['weekend']) : 'Unknown Slot';
            $ch->save();
        }
        $data->parent_id = $mainID;
        $data->branch_id = $branch_id;
        $data->branch_name = $branch_name;
        $data->time_slot = isset($timeSlotMapping[$data->slot]) ? (($currentDay >= 1 && $currentDay <= 5) ? $timeSlotMapping[$data->slot]['weekday'] : $timeSlotMapping[$data->slot]['weekend']) : 'Unknown Slot';
        $data->save();


        // ⚠️ MISSING branch_id filter - FIXED
        $record = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $mainID)->first();

        if ($record) {
            $record->additional_info = $request->additional_info ?? null;
            // Update move_from_page if it's an update
            if ($classId && !empty($moveFromPageValue)) {
                $record->move_from_page = $moveFromPageValue;
            }
            $success = $record->save();
        }

        return response()->json(['success' => true, 'message' => 'Timetable entry created successfully.']);
    }
    // public function storeGeneralTimetable(Request $request)
    // {
    //     $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //     $branch_id = $branch->branch_id;
    //     $branch_name = $branch->branch_name;

    //     $data = $request->all();
    //     $teacherId = $data['teacher'];
    //     $students = json_decode($data['students'], true);
    //     $yearinschools = json_decode($data['yearinschools'], true);
    //     $subjects = json_decode($data['subjects'], true);
    //     $attendance = json_decode($data['attendance'], true);
    //     $books = json_decode($data['books'], true);
    //     $chapters = json_decode($data['chapters'], true);
    //     $permanents = json_decode($data['permanents'], true);

    //     $studentIds = [];
    //     $studentNames = [];
    //     foreach ($students as $student) {
    //         $parts = preg_split('/\s+/', $student);
    //         if (count($parts) >= 2) {
    //             $studentIds[] = trim($parts[0]);
    //             $studentNames[] = trim(implode(' ', array_slice($parts, 1)));
    //         }
    //     }

    //     $classId = isset($data['class-id']) ? $data['class-id'] : null;
    //     if ($classId) {
    //         $existingTimetable = GeneralTimetable::find($classId);
    //         if ($existingTimetable) {
    //             GeneralTimetable::where('parent_id', $existingTimetable->parent_id)
    //                 ->whereDate('date', '>=', $data['selected-day'])
    //                 ->where('branch_id', session('branch_id'))
    //                 ->delete();
    //         }
    //     }

    //     $selectedDay = $data['selected-day'];
    //     $dayName = strtolower(date('l', strtotime($selectedDay)));
    //     $records = [];

    //     // Prepare arrays to separate permanent and non-permanent students
    //     $permanentStudentIds = [];
    //     $permanentStudentNames = [];
    //     $permanentSubjects = [];
    //     $permanentAttendance = [];
    //     $permanentPermanents = [];
    //     $nonPermanentStudentIds = [];
    //     $nonPermanentStudentNames = [];
    //     $nonPermanentSubjects = [];

    //     foreach ($studentIds as $index => $studentId) {
    //         if ($permanents[$index] === 'Yes') {
    //             $permanentStudentIds[] = $studentId;
    //             $permanentStudentNames[] = $studentNames[$index];
    //             $permanentSubjects[] = $subjects[$index];
    //             $permanentAttendance[] = $attendance[$index];
    //             $permanentPermanents[] = $permanents[$index];
    //         } else {
    //             $nonPermanentStudentIds[] = $studentId;
    //             $nonPermanentStudentNames[] = $studentNames[$index];
    //             $nonPermanentSubjects[] = $subjects[$index];
    //         }
    //     }

    //     $encodedPermanentStudentIds = json_encode($permanentStudentIds);
    //     $encodedPermanentStudentNames = json_encode($permanentStudentNames);
    //     $encodedPermanentSubjects = json_encode($permanentSubjects);
    //     $encodedPermanentAttendance = json_encode($permanentAttendance);
    //     $encodedPermanentPermanents = json_encode($permanentPermanents);
    //     $encodedNonPermanentStudentIds = json_encode($nonPermanentStudentIds);
    //     $encodedNonPermanentStudentNames = json_encode($nonPermanentStudentNames);
    //     $encodedNonPermanentSubjects = json_encode($nonPermanentSubjects);

    //     // Create record for the selected day (all students + attendance + permanents)
    //     $mainRecordData = [
    //         'date' => $selectedDay,
    //         'slot' => $data['selected-slot'],
    //         'teacher_id' => $teacherId,
    //         'time_slot' => json_encode($data['students']),
    //         'student_ids' => json_encode($studentIds),
    //         'student_names' => json_encode($studentNames),
    //         'subjects' => json_encode($subjects),
    //         'parent_id' => null,
    //         'is_attendance' => json_encode($attendance),
    //         'permanent' => json_encode($permanents),
    //         'branch_id' => $branch_id,
    //         'branch_name' => $branch_name,
    //     ];

    //     $records[] = $mainRecordData;

    //     // Create future records only for permanent students (starting from next week)
    //     if (in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
    //         $currentDate = strtotime($selectedDay . ' +1 week');
    //         $endDate = strtotime('+1 year', $currentDate);
    //         while ($currentDate <= $endDate) {
    //             $dayOfWeek = strtolower(date('l', $currentDate));
    //             if (in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
    //                 $records[] = [
    //                     'date' => date('Y-m-d', $currentDate),
    //                     'slot' => $data['selected-slot'],
    //                     'teacher_id' => $teacherId,
    //                     'time_slot' => json_encode($data['students']),
    //                     'student_ids' => $encodedPermanentStudentIds,
    //                     'student_names' => $encodedPermanentStudentNames,
    //                     'subjects' => $encodedPermanentSubjects,
    //                     'parent_id' => null,
    //                     'is_attendance' => '',
    //                     'permanent' => $encodedPermanentPermanents,
    //                     'branch_id' => $branch_id,
    //                     'branch_name' => $branch_name,
    //                 ];
    //             }
    //             $currentDate = strtotime('+1 week', $currentDate);
    //         }
    //     } elseif (in_array($dayName, ['saturday', 'sunday'])) {
    //         $currentDate = strtotime($selectedDay . ' +1 week');
    //         $endDate = strtotime('+1 year', $currentDate);
    //         while ($currentDate <= $endDate) {
    //             $dayOfWeek = strtolower(date('l', $currentDate));
    //             if (in_array($dayOfWeek, ['saturday', 'sunday'])) {
    //                 $records[] = [
    //                     'date' => date('Y-m-d', $currentDate),
    //                     'slot' => $data['selected-slot'],
    //                     'teacher_id' => $teacherId,
    //                     'time_slot' => json_encode($data['students']),
    //                     'student_ids' => $encodedPermanentStudentIds,
    //                     'student_names' => $encodedPermanentStudentNames,
    //                     'subjects' => $encodedPermanentSubjects,
    //                     'parent_id' => null,
    //                     'is_attendance' => '',
    //                     'permanent' => $encodedPermanentPermanents,
    //                     'branch_id' => $branch_id,
    //                     'branch_name' => $branch_name,
    //                 ];
    //             }
    //             $currentDate = strtotime('+1 week', $currentDate);
    //         }
    //     }

    //     // Attendance logic remains unchanged
    //     $currentDate = date('Y-m-d');
    //     $teacherName = $data['teacher'];
    //     $slotIndex = $data['selected-slot'];
    //     $currentDay = date('w', strtotime($selectedDay));
    //     $timePeriod = '';
    //     $session_1 = '';
    //     if ($currentDay >= 1 && $currentDay <= 5) {
    //         switch ($slotIndex) {
    //             case '1':
    //                 $timePeriod = '04:30 - 06:30pm';
    //                 $session_1 = 'Evening Lesson 1';
    //                 break;
    //             case '2':
    //                 $timePeriod = '06:45 - 08:45pm';
    //                 $session_1 = 'Evening Lesson 2';
    //                 break;
    //             default:
    //                 $timePeriod = 'Unknown Slot';
    //                 $session_1 = 'Unknown Session';
    //         }
    //     } else {
    //         switch ($slotIndex) {
    //             case '1':
    //                 $timePeriod = '09:00 - 11:00am';
    //                 $session_1 = 'Morning Lesson 1';
    //                 break;
    //             case '2':
    //                 $timePeriod = '11:20 - 01:20pm';
    //                 $session_1 = 'Evening Lesson 2';
    //                 break;
    //             case '3':
    //                 $timePeriod = '01:45 - 03:45pm';
    //                 $session_1 = 'Evening Lesson 3';
    //                 break;
    //             default:
    //                 $timePeriod = 'Unknown Slot';
    //                 $session_1 = 'Unknown Session';
    //         }
    //     }

    //     $family_ids = [];
    //     $names = [];
    //     foreach ($students as $student) {
    //         if (preg_match('/^(\d+)\s+(.*)$/', $student, $matches)) {
    //             $family_ids[] = $matches[1];
    //             $names[] = trim($matches[2]);
    //         }
    //     }

    //     foreach ($family_ids as $index => $familyId) {
    //         $bk_ch = ($books[$index] ?? '') . '-' . ($chapters[$index] ?? '');
    //         $studentRecord = Student::where('admissionid', $familyId)
    //             ->whereRaw('LOWER(CONCAT(studentname, " ", COALESCE(studentsur, ""))) LIKE ?', ['%' . strtolower($names[$index]) . '%'])
    //             ->where('branch_id', session('branch_id'))
    //             ->first();

    //         $attendanceRecord = Attendance::where('family_id', $familyId)
    //             ->where('date', $currentDate)
    //             ->where('student_name', 'LIKE', '%' . $names[$index] . '%')
    //             ->where('time_slot', $timePeriod)
    //             ->where('branch_id', session('branch_id'))
    //             ->first();

    //         if ($attendance[$index] === 'No' && $attendanceRecord) {
    //             $attendanceRecord->delete();
    //             continue;
    //         }

    //         if ($attendanceRecord && $attendance[$index] === 'Yes') {
    //             $attendanceRecord->update([
    //                 'family_id' => $familyId,
    //                 'student_name' => $names[$index],
    //                 'student_year_in_school' => $yearinschools[$index] ?? ($studentRecord->studentyearinschool ?? null),
    //                 'bk_ch' => $bk_ch,
    //                 'status' => $attendance[$index],
    //                 'date' => $currentDate,
    //                 'teacher_name' => $teacherName,
    //                 'subject' => $subjects[$index] ?? 'select subject',
    //                 'time_slot' => $timePeriod,
    //                 'session_1' => $session_1,
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //             ]);
    //         } elseif (!$attendanceRecord && $attendance[$index] === 'Yes' && $bk_ch !== 'Select Book-' && $bk_ch !== 'Book-') {
    //             $attendanceRecord = new Attendance();
    //             $attendanceRecord->family_id = $familyId;
    //             $attendanceRecord->student_name = $names[$index];
    //             $attendanceRecord->student_year_in_school = $yearinschools[$index] ?? ($studentRecord->studentyearinschool ?? null);
    //             $attendanceRecord->bk_ch = $bk_ch;
    //             $attendanceRecord->status = $attendance[$index];
    //             $attendanceRecord->date = $currentDate;
    //             $attendanceRecord->teacher_name = $teacherName;
    //             $attendanceRecord->subject = $subjects[$index] ?? 'select subject';
    //             $attendanceRecord->time_slot = $timePeriod;
    //             $attendanceRecord->session_1 = $session_1;
    //             $attendanceRecord->branch_id = $branch_id;
    //             $attendanceRecord->branch_name = $branch_name;
    //             $attendanceRecord->save();
    //         }
    //     }

    //     // Save timetable records
    //     $mainRecord = GeneralTimetable::create(array_shift($records));
    //     foreach ($records as &$record) {
    //         $record['parent_id'] = $mainRecord->id;
    //         $record['branch_id'] = $branch_id;
    //         $record['branch_name'] = $branch_name;
    //     }
    //     GeneralTimetable::insert($records);

    //     $mainID = $mainRecord->id;
    //     $data = GeneralTimetable::where('id', $mainID)
    //         ->where('branch_id', session('branch_id'))
    //         ->first();
    //     $child = GeneralTimetable::where('parent_id', $mainID)
    //         ->where('branch_id', session('branch_id'))
    //         ->get();
    //     foreach ($child as $ch) {
    //         $ch->student_ids = $encodedPermanentStudentIds;
    //         $ch->student_names = $encodedPermanentStudentNames;
    //         $ch->subjects = $encodedPermanentSubjects;
    //         $ch->is_attendance = '';
    //         $ch->permanent = $encodedPermanentPermanents;
    //         $ch->branch_id = $branch_id;
    //         $ch->branch_name = $branch_name;
    //         $ch->save();
    //     }
    //     $data->parent_id = $mainID;
    //     $data->branch_id = $branch_id;
    //     $data->branch_name = $branch_name;
    //     $data->save();

    //     return response()->json(['success' => true, 'message' => 'Timetable entry created successfully.']);
    // }

    /**
     * DB `general_timetables.slot`: regular lessons use 1–4; term-break sessions use 7–8 (same clock blocks as
     * lessons 1–2 — see timeSlotMapping keys 7/8 in centralTimeTable / termbreak blades). The central timetable
     * page only renders columns data-slot 1–4, so API responses must expose 7→1 and 8→2 for filter + dedupe.
     */
    private static function mapDbGeneralTimetableSlotToCentralColumn(int $dbSlot): int
    {
        if ($dbSlot === 7) {
            return 1;
        }
        if ($dbSlot === 8) {
            return 2;
        }
        return $dbSlot;
    }

    /**
     * Slot-isolated student dedupe (never compares lesson 1 vs lesson 2, etc.):
     * - For each slot number S, only timetable rows with slot === S are considered together.
     * - Identity for cross-class match: first 3+ digit run + letters-only from remainder of id+name (see studentSlotDedupeKey).
     * - Same student more than once in one class row: extra list entries removed.
     * - Same student in two classes (slot S): keep one using past-week teacher for slot S; other slots unchanged.
     */
    private function dedupeStudentsPerSlotUsingPriorWeek(
        $timetables,
        string $selectedDate,
        string $branchId,
        callable $normalizeJsonField,
        array &$skipDbPersistRowIds = []
    ) {
        $items = $timetables->values()->all();
        if (count($items) === 0) {
            return $timetables;
        }

        // Map DB slot to lesson number 1–4 (same as central timetable data-slot). Handles 2, "02", "2.0",
        // and labels like "Lesson 2" / "Slot 3". Does NOT treat clock text ("11:00") as lesson 11.
        $canonicalSlotKey = function ($slot) {
            if ($slot === null) {
                return null;
            }
            $s = trim((string) $slot);
            $s = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $s);
            if ($s === '') {
                return null;
            }
            if (is_numeric($s)) {
                $n = (int) $s;
            } elseif (preg_match('/(?:lesson|slot)\s*[:\#\-]?\s*(\d+)/i', $s, $m)) {
                $n = (int) $m[1];
            } else {
                return null;
            }
            if ($n < 1 || $n > 20) {
                return null;
            }
            $n = self::mapDbGeneralTimetableSlotToCentralColumn($n);
            return (string) $n;
        };

        $rawSlotForRow = function ($row) {
            $s = $row->slot ?? null;
            if ($s !== null && trim((string) $s) !== '') {
                return $s;
            }
            $ts = $row->time_slot ?? null;
            if ($ts === null || trim((string) $ts) === '') {
                return null;
            }
            $tstr = trim((string) $ts);
            if (preg_match('/lesson|slot/i', $tstr) || is_numeric($tstr)) {
                return $tstr;
            }
            return null;
        };

        // One key per pupil in a slot: first 3+ digit run (family/admission) + letters-only from the rest of id+name.
        // Merges "3673"+"LT T", "3673 LT T", "L T T" spacing, paths like 3673/5/LT T → same child; 3673 IT T vs LT T stay different.
        $studentSlotDedupeKey = function ($rawId, $rawName) {
            $id = trim((string) $rawId);
            $nm = trim(preg_replace('/\s+/u', ' ', str_ireplace('null', '', (string) ($rawName ?? ''))));
            if ($id === '' && $nm === '') {
                return null;
            }
            $id = preg_replace('/^(\d{3,})(\p{L})/u', '$1 $2', $id);
            $combined = trim(preg_replace('/\s+/u', ' ', $id . ' ' . $nm));
            if ($combined === '') {
                return null;
            }
            if (!preg_match('/^(\d{3,})/u', $combined, $m)) {
                return null;
            }
            $core = $m[1];
            $tail = trim(substr($combined, strlen($m[0])));
            $tail = preg_replace('#^[/\d.\-\s]+#u', '', $tail);
            $tail = trim($tail);
            $tail = preg_replace('/\s*-\s*[Yy]\s*\d+.*$/u', '', $tail);
            $tail = preg_replace('/\s*-\s*(MATH|ENGLISH|SCIENCE|BUSINESS)\s*$/iu', '', $tail);
            if ($tail === '') {
                return null;
            }
            $letters = preg_replace('/[^a-z\p{L}]/iu', '', $tail);
            if ($letters === '') {
                return null;
            }
            if (function_exists('mb_strtolower')) {
                $letters = mb_strtolower($letters, 'UTF-8');
            } else {
                $letters = strtolower($letters);
            }
            return $core . "\x1e" . $letters;
        };

        $pastDate = Carbon::parse($selectedDate)->subWeek()->format('Y-m-d');
        $pastRows = GeneralTimetable::where('date', $pastDate)
            ->where('branch_id', $branchId)
            ->where(function ($query) {
                $query->whereNull('additional_student')
                    ->orWhere('additional_student', '');
            })
            ->get();

        $pastUnique = [];
        $pastSeenKeys = [];
        foreach ($pastRows as $timetable) {
            $teacherId = trim((string)($timetable->teacher_id ?? ''));
            $slot = $canonicalSlotKey($rawSlotForRow($timetable));
            if ($slot === null) {
                continue;
            }
            $key = $teacherId . '|' . $slot;
            if (!isset($pastSeenKeys[$key])) {
                $pastUnique[] = $timetable;
                $pastSeenKeys[$key] = $timetable->id;
            } else {
                $existingIndex = null;
                foreach ($pastUnique as $idx => $existing) {
                    if ($existing->id == $pastSeenKeys[$key]) {
                        $existingIndex = $idx;
                        break;
                    }
                }
                if ($existingIndex !== null) {
                    $existingRecord = $pastUnique[$existingIndex];
                    $shouldReplace = false;
                    if ($timetable->parent_id === null && $existingRecord->parent_id !== null) {
                        $shouldReplace = true;
                    } elseif (($timetable->parent_id === null) === ($existingRecord->parent_id === null)) {
                        if ($timetable->id > $existingRecord->id) {
                            $shouldReplace = true;
                        }
                    }
                    if ($shouldReplace) {
                        $pastUnique[$existingIndex] = $timetable;
                        $pastSeenKeys[$key] = $timetable->id;
                    }
                }
            }
        }

        $preferredTeacherBySlotStudent = [];
        foreach ($pastUnique as $row) {
            $slotKey = $canonicalSlotKey($rawSlotForRow($row));
            if ($slotKey === null) {
                continue;
            }
            $teacherId = trim((string)($row->teacher_id ?? ''));
            $studentIds = $normalizeJsonField($row->student_ids);
            $studentNames = $normalizeJsonField($row->student_names);
            foreach ($studentIds as $i => $sid) {
                $sk = trim((string) $sid);
                if ($sk === '') {
                    continue;
                }
                $nm = $studentNames[$i] ?? '';
                $pairKey = $studentSlotDedupeKey($sk, $nm);
                if ($pairKey === null) {
                    continue;
                }
                if (!isset($preferredTeacherBySlotStudent[$slotKey][$pairKey])) {
                    $preferredTeacherBySlotStudent[$slotKey][$pairKey] = $teacherId;
                }
            }
        }

        $stripParallelByIndices = function (array $ids, array $names, array $subjects, array $att, array $perm, array $ages, array $removeIdx) {
            if (count($removeIdx) === 0) {
                return [$ids, $names, $subjects, $att, $perm, $ages];
            }
            $removeSet = array_flip($removeIdx);
            $newIds = [];
            $newNames = [];
            $newSubjects = [];
            $newAtt = [];
            $newPerm = [];
            $newAges = [];
            $n = count($ids);
            for ($i = 0; $i < $n; $i++) {
                if (isset($removeSet[$i])) {
                    continue;
                }
                $newIds[] = $ids[$i] ?? null;
                $newNames[] = $names[$i] ?? null;
                $newSubjects[] = $subjects[$i] ?? null;
                $newAtt[] = $att[$i] ?? null;
                $newPerm[] = $perm[$i] ?? null;
                $newAges[] = $ages[$i] ?? null;
            }
            return [$newIds, $newNames, $newSubjects, $newAtt, $newPerm, $newAges];
        };

        $intraClassLogLines = [];

        $dedupeWithinRow = function ($row) use ($normalizeJsonField, $stripParallelByIndices, $studentSlotDedupeKey, &$intraClassLogLines) {
            $ids = $normalizeJsonField($row->student_ids);
            $names = $normalizeJsonField($row->student_names);
            $subjects = $normalizeJsonField($row->subjects);
            $att = $normalizeJsonField($row->is_attendance);
            $perm = $normalizeJsonField($row->permanent);
            $ages = is_array($row->student_ages) ? $row->student_ages : [];
            while (count($ages) < count($ids)) {
                $ages[] = null;
            }
            $seen = [];
            $dupIdx = [];
            foreach ($ids as $i => $sid) {
                $sk = trim((string) $sid);
                if ($sk === '') {
                    continue;
                }
                $pairKey = $studentSlotDedupeKey($sk, $names[$i] ?? '');
                if ($pairKey === null) {
                    continue;
                }
                if (isset($seen[$pairKey])) {
                    $dupIdx[] = $i;
                } else {
                    $seen[$pairKey] = true;
                }
            }
            if (count($dupIdx) > 0) {
                $slotLabel = trim((string)($row->slot ?? '')) !== '' ? trim((string)($row->slot ?? '')) : '(unknown slot)';
                $teacherLabel = trim((string)($row->teacher_id ?? '')) !== '' ? trim((string)($row->teacher_id ?? '')) : '(unknown teacher)';
                $classRowId = (string)($row->id ?? '');
                foreach ($dupIdx as $di) {
                    $dupId = trim((string)($ids[$di] ?? ''));
                    $dupName = trim((string)($names[$di] ?? ''));
                    $intraClassLogLines[] =
                        '[Central Timetable | student dedupe] Same class list had duplicate (same id + name). Removed extra entry.'
                        . "\n  Slot: " . $slotLabel
                        . "\n  Class: timetable row id " . $classRowId . ', teacher id ' . $teacherLabel
                        . "\n  Duplicate student: admission id " . $dupId . ', name ' . ($dupName !== '' ? $dupName : '(empty)');
                }
                [$ids, $names, $subjects, $att, $perm, $ages] = $stripParallelByIndices($ids, $names, $subjects, $att, $perm, $ages, $dupIdx);
            }
            return [$ids, $names, $subjects, $att, $perm, $ages];
        };

        $working = [];
        foreach ($items as $idx => $row) {
            $working[$idx] = $dedupeWithinRow($row);
        }

        foreach ($intraClassLogLines as $intraLine) {
            Log::info($intraLine);
        }

        $slotToIndices = [];
        $skippedSlotCanonical = 0;
        $skippedSlotSamples = [];
        foreach ($items as $idx => $row) {
            $slotKey = $canonicalSlotKey($rawSlotForRow($row));
            if ($slotKey === null) {
                $skippedSlotCanonical++;
                if (count($skippedSlotSamples) < 20) {
                    $skippedSlotSamples[] = 'timetable_id=' . ($row->id ?? '')
                        . ' slot=' . json_encode($row->slot, JSON_UNESCAPED_UNICODE)
                        . ' time_slot=' . json_encode($row->time_slot ?? null, JSON_UNESCAPED_UNICODE);
                }
                continue;
            }
            $slotToIndices[$slotKey][] = $idx;
        }


        $removeByRow = [];
        $crossClassResolutionCount = 0;

        $maxLessonSlot = 4;
        $extraKeys = array_diff(array_keys($slotToIndices), array_map('strval', range(1, $maxLessonSlot)));
        foreach ($extraKeys as $ek) {
            if (is_numeric($ek) && (int) $ek > $maxLessonSlot) {
                $maxLessonSlot = (int) $ek;
            }
        }

        foreach (range(1, $maxLessonSlot) as $slotNum) {
            $slotKey = (string) $slotNum;
            $indices = $slotToIndices[$slotKey] ?? [];
            $indices = array_values(array_filter($indices, function ($idx) use ($items, $slotKey, $canonicalSlotKey, $rawSlotForRow) {
                $c = $canonicalSlotKey($rawSlotForRow($items[$idx]));
                return $c !== null && $c === $slotKey;
            }));

            $slotLog = [];
            $slotLog[] = '[Central Timetable | student dedupe] DATE ' . $selectedDate . ' | SLOT ' . $slotKey;
            $slotLog[] = '  Number of timetable rows (different teachers/classes) in slot ' . $slotKey . ': ' . count($indices);
            foreach ($indices as $idx) {
                $r = $items[$idx];
                $tid = trim((string)($r->teacher_id ?? ''));
                $tid = $tid !== '' ? $tid : '(unknown teacher)';
                $nStudents = count($working[$idx][0] ?? []);
                $slotLog[] = '  - timetable row id ' . ($r->id ?? '') . ' | teacher: ' . $tid . ' | student rows: ' . $nStudents;
            }

            if (count($indices) < 2) {
                continue;
            }

            $studentToIndices = [];
            foreach ($indices as $idx) {
                [$ids, $names] = $working[$idx];
                foreach ($ids as $i => $sid) {
                    $sk = trim((string) $sid);
                    if ($sk === '') {
                        continue;
                    }
                    $pairKey = $studentSlotDedupeKey($sk, $names[$i] ?? '');
                    if ($pairKey === null) {
                        continue;
                    }
                    $studentToIndices[$pairKey][] = ['row' => $idx, 'pos' => $i];
                }
            }

            $fixesThisSlot = 0;
            $slotDupBlocks = [];
            foreach ($studentToIndices as $studentKey => $occurrences) {
                if (count($occurrences) <= 1) {
                    continue;
                }

                $rowIndices = [];
                foreach ($occurrences as $oc) {
                    $rowIndices[$oc['row']] = true;
                }
                $uniqueRows = array_keys($rowIndices);
                if (count($uniqueRows) < 2) {
                    continue;
                }

                // Past-week hint is keyed by the same slot only (never another lesson's slot).
                $prefTeacher = $preferredTeacherBySlotStudent[$slotKey][$studentKey] ?? null;
                $prefTeacher = $prefTeacher !== null ? trim((string) $prefTeacher) : '';

                $firstOc = $occurrences[0];
                $dispIds = $working[$firstOc['row']][0];
                $dispNames = $working[$firstOc['row']][1];
                $studentAdmissionId = trim((string)($dispIds[$firstOc['pos']] ?? ''));
                $studentDisplayName = trim((string)($dispNames[$firstOc['pos']] ?? ''));
                if ($studentDisplayName === '') {
                    $studentDisplayName = '(name empty)';
                }

                $keepRowIdx = null;

                // Special rule: if same student appears in same slot as permanent in one class
                // and switch/temporary in another class, keep switch/temporary for this date only.
                $permYesRows = [];
                $nonPermRows = [];
                foreach ($occurrences as $oc) {
                    $ridx = $oc['row'];
                    $pos = $oc['pos'];
                    $permValues = $working[$ridx][4] ?? [];
                    $permRaw = trim((string)($permValues[$pos] ?? ''));
                    $isPermanentYes = in_array(strtolower($permRaw), ['yes', 'y', '1', 'true', 'permanent'], true);
                    if ($isPermanentYes) {
                        $permYesRows[$ridx] = true;
                    } else {
                        $nonPermRows[$ridx] = true;
                    }
                }
                $permRowIdxList = array_keys($permYesRows);
                $nonPermRowIdxList = array_keys($nonPermRows);
                $hasPermanentAndSwitch = count($permRowIdxList) > 0 && count($nonPermRowIdxList) > 0;
                if ($hasPermanentAndSwitch) {
                    // Keep one of non-permanent rows (switch/temporary) deterministically.
                    $minId = PHP_INT_MAX;
                    foreach ($nonPermRowIdxList as $ridx) {
                        $rid = (int)($items[$ridx]->id ?? 0);
                        if ($rid < $minId) {
                            $minId = $rid;
                            $keepRowIdx = $ridx;
                        }
                    }
                    // Important: for this special case do not persist DB removal for involved rows.
                    foreach ($uniqueRows as $ridx) {
                        $rowId = (int)($items[$ridx]->id ?? 0);
                        if ($rowId > 0) {
                            $skipDbPersistRowIds[$rowId] = true;
                        }
                    }

                    $permRowDetails = [];
                    foreach ($permRowIdxList as $ridx) {
                        $rowObj = $items[$ridx];
                        $permRowDetails[] = 'id ' . ($rowObj->id ?? '') . ' / teacher ' . trim((string)($rowObj->teacher_id ?? '(unknown)'));
                    }
                    $switchRowDetails = [];
                    foreach ($nonPermRowIdxList as $ridx) {
                        $rowObj = $items[$ridx];
                        $switchRowDetails[] = 'id ' . ($rowObj->id ?? '') . ' / teacher ' . trim((string)($rowObj->teacher_id ?? '(unknown)'));
                    }
                    $chosenRow = $items[$keepRowIdx] ?? null;
                    Log::info('[Central Timetable | duplicate special-case] Same-slot duplicate found with permanent + switch/temporary; kept switch for selected date and skipped DB persist for involved rows.', [
                        'date' => $selectedDate,
                        'slot' => $slotKey,
                        'student_admission_id' => $studentAdmissionId,
                        'student_name' => $studentDisplayName,
                        'permanent_rows' => $permRowDetails,
                        'switch_rows' => $switchRowDetails,
                        'kept_row_id' => $chosenRow->id ?? null,
                        'kept_teacher_id' => $chosenRow ? trim((string)($chosenRow->teacher_id ?? '')) : null,
                        'skip_db_persist_row_ids' => array_values(array_map('intval', array_keys($skipDbPersistRowIds))),
                    ]);
                }

                if ($keepRowIdx === null && $prefTeacher !== '') {
                    $matching = [];
                    foreach ($uniqueRows as $ridx) {
                        if (trim((string)($items[$ridx]->teacher_id ?? '')) === $prefTeacher) {
                            $matching[] = $ridx;
                        }
                    }
                    if (count($matching) === 1) {
                        $keepRowIdx = $matching[0];
                    } elseif (count($matching) > 1) {
                        $minId = PHP_INT_MAX;
                        foreach ($matching as $ridx) {
                            $rid = (int) $items[$ridx]->id;
                            if ($rid < $minId) {
                                $minId = $rid;
                                $keepRowIdx = $ridx;
                            }
                        }
                    }
                }

                if ($keepRowIdx === null) {
                    $minId = PHP_INT_MAX;
                    foreach ($uniqueRows as $ridx) {
                        $rid = (int) $items[$ridx]->id;
                        if ($rid < $minId) {
                            $minId = $rid;
                            $keepRowIdx = $ridx;
                        }
                    }
                }

                $resolutionSource = $hasPermanentAndSwitch
                    ? 'Special rule: same-slot duplicate had both permanent and switch/temporary. Kept switch/temporary for this date only (DB persist skipped for involved rows).'
                    : ($prefTeacher !== ''
                        ? 'Past week (' . $pastDate . ') had this student in slot ' . $slotKey . ' with teacher id ' . $prefTeacher . '.'
                        : 'No past-week teacher match for this student in slot ' . $slotKey . '. Used fallback: keep lowest timetable row id.');

                $keepRow = $items[$keepRowIdx];
                $keepTeacher = trim((string)($keepRow->teacher_id ?? ''));
                $keepTeacherLabel = $keepTeacher !== '' ? $keepTeacher : '(unknown)';
                $keepRowId = (string)($keepRow->id ?? '');

                $removedBullets = [];
                foreach ($occurrences as $oc) {
                    if ($oc['row'] === $keepRowIdx) {
                        continue;
                    }
                    $ridx = $oc['row'];
                    $other = $items[$ridx];
                    $otherTeacher = trim((string)($other->teacher_id ?? ''));
                    $otherTeacherLabel = $otherTeacher !== '' ? $otherTeacher : '(unknown)';
                    $removedBullets[] = '        • Row id ' . ($other->id ?? '') . '  |  Teacher: ' . $otherTeacherLabel;
                    $pos = $oc['pos'];
                    $removeByRow[$ridx][$pos] = true;
                }

                $crossClassResolutionCount++;
                $fixesThisSlot++;
                $dupNum = $fixesThisSlot;
                $slotDupBlocks[] =
                    "\n"
                    . "  >>> POINT #" . $dupNum . " — IS SLOT ME YE STUDENT DUPLICATE THA <<<\n"
                    . '      • Naam:           ' . $studentDisplayName . "\n"
                    . '      • Admission ID:   ' . $studentAdmissionId . "\n"
                    . '      • Dedupe key:     ' . str_replace("\x1e", ' + ', $studentKey) . "\n"
                    . "      • Rule / reason: " . $resolutionSource . "\n"
                    . "      • RAKHA (kept):   row id " . $keepRowId . '  |  Teacher: ' . $keepTeacherLabel . "\n"
                    . "      • HATAYA (removed from duplicate class rows):\n"
                    . implode("\n", $removedBullets) . "\n"
                    . "      ------------------------------------------------------------\n";
            }

            if ($fixesThisSlot === 0) {
            } else {
                $bigReport = "\n"
                    . "################################################################################\n"
                    . "#                                                                              #\n"
                    . "#     CENTRAL TIMETABLE — DUPLICATE STUDENTS REPORT (BARI HEADING)           #\n"
                    . "#     (Same slot = lesson column; same bacha 2+ timetable rows mein tha)     #\n"
                    . "#                                                                              #\n"
                    . "################################################################################\n"
                    . "##  DATE:              " . $selectedDate . "\n"
                    . "##  BRANCH ID:         " . $branchId . "\n"
                    . "##  SLOT (lesson):     " . $slotKey . "  <-- is slot mein neechay wale students duplicate mile\n"
                    . "##  TOTAL DUPLICATES:  " . $fixesThisSlot . " student(s)\n"
                    . "################################################################################\n"
                    . "##  LIST (point-wise har duplicate alag):\n"
                    . "################################################################################\n"
                    . implode('', $slotDupBlocks)
                    . "################################################################################\n"
                    . "##  END SLOT " . $slotKey . " DUPLICATE LIST\n"
                    . "################################################################################\n";
                Log::info($bigReport);
            }
        }

        foreach ($items as $idx => $row) {
            $removeIdx = isset($removeByRow[$idx]) ? array_keys($removeByRow[$idx]) : [];
            [$ids, $names, $subjects, $att, $perm, $ages] = $working[$idx];
            if (count($removeIdx) > 0) {
                [$ids, $names, $subjects, $att, $perm, $ages] = $stripParallelByIndices($ids, $names, $subjects, $att, $perm, $ages, $removeIdx);
            }
            $row->student_ids = json_encode(array_values($ids));
            $row->student_names = json_encode(array_values($names));
            $row->subjects = json_encode(array_values($subjects));
            $row->is_attendance = json_encode(array_values($att));
            $row->permanent = json_encode(array_values($perm));
            $row->student_ages = array_values($ages);
        }

        return collect($items);
    }

    /**
     * Central timetable: do not expose incomplete rows (missing admission or name), DB placeholders,
     * or inactive students. Keeps parallel arrays aligned (optional books/chapters).
     */
    private function filterCentralTimetableDisplayStudentRows(
        array $student_ids,
        array $student_names,
        array $subjects,
        array $is_attendance,
        array $permanent,
        array $books = [],
        array $chapters = [],
        bool $computeStudentAges = false
    ): array {
        $outIds = [];
        $outNames = [];
        $outSubjects = [];
        $outAtt = [];
        $outPerm = [];
        $outBooks = [];
        $outChapters = [];
        $ages = [];

        $rowCount = count($student_ids);
        while (count($books) < $rowCount) {
            $books[] = '';
        }
        while (count($chapters) < $rowCount) {
            $chapters[] = '';
        }

        for ($index = 0; $index < $rowCount; $index++) {
            $rawAdmission = $student_ids[$index] ?? '';
            $admissionId = trim((string) $rawAdmission);
            $nameRaw = isset($student_names[$index]) ? str_ireplace('null', '', (string) $student_names[$index]) : '';
            $nameForMatch = trim($nameRaw);
            $subjectRaw = isset($subjects[$index]) ? (string) $subjects[$index] : '';
            $subjectForCheck = strtolower(trim($subjectRaw));
            $admissionForCheck = strtolower(trim($admissionId));
            $nameForCheck = strtolower(trim($nameForMatch));

            $isPlaceholderAdmission = in_array($admissionForCheck, ['', 'no id', 'noid'], true);
            $isPlaceholderName = in_array($nameForCheck, ['', 'no name', 'noname'], true);
            $isPlaceholderSubject = in_array($subjectForCheck, ['', 'no subject', 'nosubject'], true);
            if ($isPlaceholderAdmission && $isPlaceholderName && $isPlaceholderSubject) {
                continue;
            }

            if ($admissionId === '' || $nameForMatch === '') {
                continue;
            }

            $student_data = Student::query()
                ->where('admissionid', $admissionId)
                ->where('branch_id', session('branch_id'))
                ->where(
                    DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"),
                    'LIKE',
                    '%' . $nameForMatch . '%'
                )
                ->select('studentyearinschool', 'student_status')
                ->first();

            if ($student_data && strtolower(trim((string) ($student_data->student_status ?? ''))) === 'inactive') {
                continue;
            }

            $outIds[] = $student_ids[$index] ?? '';
            $outNames[] = $student_names[$index] ?? '';
            $outSubjects[] = $subjects[$index] ?? '';
            $outAtt[] = $is_attendance[$index] ?? '';
            $outPerm[] = $permanent[$index] ?? '';
            $outBooks[] = $books[$index] ?? '';
            $outChapters[] = $chapters[$index] ?? '';
            if ($computeStudentAges) {
                $ages[] = $student_data ? $student_data->studentyearinschool : null;
            }
        }

        return [
            'student_ids' => array_values($outIds),
            'student_names' => array_values($outNames),
            'subjects' => array_values($outSubjects),
            'is_attendance' => array_values($outAtt),
            'permanent' => array_values($outPerm),
            'books' => array_values($outBooks),
            'chapters' => array_values($outChapters),
            'student_ages' => array_values($ages),
        ];
    }

    public function getGeneralTimetable(Request $request)
    {
        // dd($request->all());
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;
        $context = (string) $request->input('context', '');

        $selectedDate = $request->input('date');
        try {
            $selectedDate = Carbon::parse($selectedDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $selectedDate = date('Y-m-d');
        }

        $timetableQuery = GeneralTimetable::where('date', $selectedDate)
            ->where('branch_id', session('branch_id'))
            ->where(function ($query) {
                $query->whereNull('additional_student')
                    ->orWhere('additional_student', '');
            });

        if ($context === 'termbreak') {
            // Term break page must only show term break scheduler classes.
            $timetableQuery->where('session_type', 'termbreak_scheduler')
                ->whereIn('slot', ['7', '8']);
        } else {
            // Central timetable must never show term break classes.
            $timetableQuery->where(function ($query) {
                    $query->whereNull('session_type')
                        ->orWhere('session_type', '!=', 'termbreak_scheduler');
                })
                ->whereNotIn('slot', ['7', '8']);
        }

        $timetables = $timetableQuery->get();

        // Helper function to normalize JSON fields (handles arrays, JSON strings, comma-separated strings)
        $normalizeJsonField = function($value) {
            if (is_array($value)) {
                return $value;
            } elseif (is_string($value) && !empty(trim($value))) {
                // Try to decode as JSON first
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                } else {
                    // If not valid JSON, try to extract valid JSON part (handle cases with extra characters)
                    // Find the end of valid JSON by matching brackets/braces
                    $trimmed = trim($value);
                    if (($trimmed[0] ?? '') === '[' || ($trimmed[0] ?? '') === '{') {
                        $depth = 0;
                        $inString = false;
                        $escapeNext = false;
                        $endIndex = -1;
                        
                        for ($i = 0; $i < strlen($trimmed); $i++) {
                            $char = $trimmed[$i];
                            
                            if ($escapeNext) {
                                $escapeNext = false;
                                continue;
                            }
                            
                            if ($char === '\\') {
                                $escapeNext = true;
                                continue;
                            }
                            
                            if ($char === '"' && !$escapeNext) {
                                $inString = !$inString;
                                continue;
                            }
                            
                            if (!$inString) {
                                if ($char === '[' || $char === '{') {
                                    $depth++;
                                } elseif ($char === ']' || $char === '}') {
                                    $depth--;
                                    if ($depth === 0) {
                                        $endIndex = $i + 1;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if ($endIndex > 0) {
                            $jsonPart = substr($trimmed, 0, $endIndex);
                            $decoded = json_decode($jsonPart, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                return $decoded;
                            }
                        }
                    }
                    
                    // If not valid JSON, treat as comma-separated string
                    $parts = array_map('trim', explode(',', $value));
                    $parts = array_filter($parts); // Remove empty values
                    return array_values($parts); // Re-index array
                }
            }
            return [];
        };

        // DB cleanup (selected calendar date only):
        // If same teacher + same slot has duplicates, delete only empty duplicate rows.
        $canonicalSlotForCleanup = function ($row) {
            $slotValue = $row->slot;
            if ($slotValue === null || trim((string) $slotValue) === '') {
                $slotValue = $row->time_slot ?? null;
            }
            if ($slotValue === null || trim((string) $slotValue) === '') {
                return null;
            }
            $slotValue = trim((string) $slotValue);
            $slotValue = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $slotValue);
            if (is_numeric($slotValue)) {
                return (string) self::mapDbGeneralTimetableSlotToCentralColumn((int) $slotValue);
            }
            if (preg_match('/(?:lesson|slot)\s*[:\#\-]?\s*(\d+)/i', $slotValue, $m)) {
                $n = (int) $m[1];
                if ($n < 1 || $n > 20) {
                    return null;
                }
                return (string) self::mapDbGeneralTimetableSlotToCentralColumn($n);
            }
            return null;
        };

        $isEmptyClassRow = function ($row) use ($normalizeJsonField) {
            $ids = $normalizeJsonField($row->student_ids);
            $names = $normalizeJsonField($row->student_names);
            $subjects = $normalizeJsonField($row->subjects);
            $n = max(count($ids), count($names), count($subjects));
            for ($i = 0; $i < $n; $i++) {
                if (
                    trim((string) ($ids[$i] ?? '')) !== '' ||
                    trim((string) ($names[$i] ?? '')) !== '' ||
                    trim((string) ($subjects[$i] ?? '')) !== ''
                ) {
                    return false;
                }
            }
            return true;
        };

        $dupBuckets = [];
        foreach ($timetables as $row) {
            $teacherKey = trim((string) ($row->teacher_id ?? ''));
            $slotKey = $canonicalSlotForCleanup($row);
            if ($teacherKey === '' || $slotKey === null) {
                continue;
            }
            $dupBuckets[$teacherKey . '|' . $slotKey][] = $row;
        }

        $deleteIds = [];
        foreach ($dupBuckets as $bucketRows) {
            if (count($bucketRows) < 2) {
                continue;
            }

            $emptyRows = [];
            $nonEmptyRows = [];
            foreach ($bucketRows as $r) {
                if ($isEmptyClassRow($r)) {
                    $emptyRows[] = $r;
                } else {
                    $nonEmptyRows[] = $r;
                }
            }

            // If any non-empty row exists, delete all empty duplicates in this teacher+slot bucket.
            if (count($nonEmptyRows) > 0) {
                foreach ($emptyRows as $er) {
                    $deleteIds[] = (int) $er->id;
                }
                continue;
            }

            // If all are empty duplicates, keep one and delete extras.
            if (count($emptyRows) > 1) {
                usort($emptyRows, function ($a, $b) {
                    $aIsParent = ($a->parent_id === null);
                    $bIsParent = ($b->parent_id === null);
                    if ($aIsParent !== $bIsParent) {
                        return $aIsParent ? -1 : 1;
                    }
                    return (int)($b->id ?? 0) <=> (int)($a->id ?? 0);
                });
                for ($i = 1; $i < count($emptyRows); $i++) {
                    $deleteIds[] = (int) $emptyRows[$i]->id;
                }
            }
        }

        $deleteIds = array_values(array_unique(array_filter($deleteIds)));
        if (count($deleteIds) > 0) {
            GeneralTimetable::where('branch_id', session('branch_id'))
                ->whereDate('date', $selectedDate)
                ->whereIn('id', $deleteIds)
                ->delete();

            $timetables = $timetables->reject(function ($row) use ($deleteIds) {
                return in_array((int) $row->id, $deleteIds, true);
            })->values();
        }

        // Process each timetable entry to include studentyearinschool
        $timetables = $timetables->map(function ($timetable) use ($branch_id, $branch_name, $normalizeJsonField, $context) {
            // Normalize all JSON fields to ensure consistent format
            $student_ids = $normalizeJsonField($timetable->student_ids);
            $student_names = $normalizeJsonField($timetable->student_names);
            $subjects = $normalizeJsonField($timetable->subjects);
            $is_attendance = $normalizeJsonField($timetable->is_attendance);
            $permanent = $normalizeJsonField($timetable->permanent);

            $filtered = $this->filterCentralTimetableDisplayStudentRows(
                $student_ids,
                $student_names,
                $subjects,
                $is_attendance,
                $permanent,
                [],
                [],
                true
            );
            $timetable->student_ids = json_encode($filtered['student_ids']);
            $timetable->student_names = json_encode($filtered['student_names']);
            $timetable->subjects = json_encode($filtered['subjects']);
            $timetable->is_attendance = json_encode($filtered['is_attendance']);
            $timetable->permanent = json_encode($filtered['permanent']);
            $student_ages = $filtered['student_ages'];

            // Keep raw slots for term-break context (expects slots 7/8 in frontend).
            if ($context !== 'termbreak') {
                // Normalize slot to lesson number string (1–4) for central timetable filter + dedupe.
                // DB may store only time_slot ("Lesson 1 – ...") or non-numeric slot labels - extract first digit run.
                $slotValue = $timetable->slot;
                if ($slotValue === null || trim((string) $slotValue) === '') {
                    $slotValue = $timetable->time_slot ?? null;
                }
                if ($slotValue !== null && trim((string) $slotValue) !== '') {
                    $slotValue = trim((string) $slotValue);
                    $slotValue = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $slotValue);
                    if (is_numeric($slotValue)) {
                        $n = self::mapDbGeneralTimetableSlotToCentralColumn((int) $slotValue);
                        $timetable->slot = (string) $n;
                    } elseif (preg_match('/(?:lesson|slot)\s*[:\#\-]?\s*(\d+)/i', $slotValue, $m)) {
                        $n = (int) $m[1];
                        if ($n < 1 || $n > 20) {
                            $timetable->slot = null;
                        } else {
                            $n = self::mapDbGeneralTimetableSlotToCentralColumn($n);
                            $timetable->slot = (string) $n;
                        }
                    } else {
                        $timetable->slot = null;
                    }
                } else {
                    $timetable->slot = null;
                }
            }

            // Add student_ages to the timetable object
            $timetable->student_ages = $student_ages;
            $timetable->branch_id = $branch_id;
            $timetable->branch_name = $branch_name;
            return $timetable;
        });

        // Keep every timetable row (do not drop "duplicate" teacher+slot classes from the response).
        // Only dedupe students within a slot via dedupeStudentsPerSlotUsingPriorWeek.

        $skipDbPersistRowIds = [];
        $timetables = $this->dedupeStudentsPerSlotUsingPriorWeek(
            $timetables,
            $selectedDate,
            $branch_id,
            $normalizeJsonField,
            $skipDbPersistRowIds
        );

        // Persist deduped student arrays to DB for the selected calendar date only.
        // Safety: update by (id + branch_id + date), so unrelated rows are never touched.
        foreach ($timetables as $row) {
            if (!isset($row->id)) {
                continue;
            }
            if (isset($skipDbPersistRowIds[(int) $row->id])) {
                continue;
            }
            GeneralTimetable::where('id', (int) $row->id)
                ->where('branch_id', session('branch_id'))
                ->whereDate('date', $selectedDate)
                ->update([
                    'student_ids' => $row->student_ids,
                    'student_names' => $row->student_names,
                    'subjects' => $row->subjects,
                    'is_attendance' => $row->is_attendance,
                    'permanent' => $row->permanent,
                ]);
        }

        // Merge duplicate class rows for same teacher + same slot.
        // This prevents UI from showing two cards for one teacher in one slot.
        $groupedByTeacherSlot = [];
        foreach ($timetables as $row) {
            $teacherKey = trim((string) ($row->teacher_id ?? ''));
            $slotKey = trim((string) ($row->slot ?? ''));
            if ($teacherKey === '' || $slotKey === '') {
                $groupedByTeacherSlot[] = [$row];
                continue;
            }
            $bucketKey = $teacherKey . '|' . $slotKey;
            if (!isset($groupedByTeacherSlot[$bucketKey])) {
                $groupedByTeacherSlot[$bucketKey] = [];
            }
            $groupedByTeacherSlot[$bucketKey][] = $row;
        }

        $mergedTimetables = [];
        foreach ($groupedByTeacherSlot as $rows) {
            if (!is_array($rows) || count($rows) === 0) {
                continue;
            }
            if (count($rows) === 1) {
                $mergedTimetables[] = $rows[0];
                continue;
            }

            // Pick base row deterministically (prefer parent row, then latest id).
            usort($rows, function ($a, $b) {
                $aIsParent = ($a->parent_id === null);
                $bIsParent = ($b->parent_id === null);
                if ($aIsParent !== $bIsParent) {
                    return $aIsParent ? -1 : 1;
                }
                return (int)($b->id ?? 0) <=> (int)($a->id ?? 0);
            });
            $base = $rows[0];

            $mergedIds = [];
            $mergedNames = [];
            $mergedSubjects = [];
            $mergedAttendance = [];
            $mergedPermanent = [];
            $mergedAges = [];
            $seenStudentKeys = [];

            foreach ($rows as $r) {
                $ids = $normalizeJsonField($r->student_ids);
                $names = $normalizeJsonField($r->student_names);
                $subjects = $normalizeJsonField($r->subjects);
                $attendance = $normalizeJsonField($r->is_attendance);
                $permanent = $normalizeJsonField($r->permanent);
                $ages = is_array($r->student_ages ?? null) ? $r->student_ages : [];

                $max = max(count($ids), count($names), count($subjects));
                for ($i = 0; $i < $max; $i++) {
                    $sid = trim((string) ($ids[$i] ?? ''));
                    $sname = trim((string) ($names[$i] ?? ''));
                    if ($sid === '' && $sname === '') {
                        continue;
                    }

                    $studentKey = strtolower($sid . '|' . preg_replace('/\s+/u', ' ', $sname));
                    if (isset($seenStudentKeys[$studentKey])) {
                        continue;
                    }
                    $seenStudentKeys[$studentKey] = true;

                    $mergedIds[] = $ids[$i] ?? '';
                    $mergedNames[] = $names[$i] ?? '';
                    $mergedSubjects[] = $subjects[$i] ?? '';
                    $mergedAttendance[] = $attendance[$i] ?? '';
                    $mergedPermanent[] = $permanent[$i] ?? '';
                    $mergedAges[] = $ages[$i] ?? null;
                }
            }

            $base->student_ids = json_encode(array_values($mergedIds));
            $base->student_names = json_encode(array_values($mergedNames));
            $base->subjects = json_encode(array_values($mergedSubjects));
            $base->is_attendance = json_encode(array_values($mergedAttendance));
            $base->permanent = json_encode(array_values($mergedPermanent));
            $base->student_ages = array_values($mergedAges);

            $mergedTimetables[] = $base;
        }

        $timetables = collect($mergedTimetables)->values();

        return response()->json($timetables);
    }

    // public function deleteGeneral($id, Request $request)
    // {
    //     // dd($request->all(),$id);
    //     $record = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $id)->first();

    //     if (!$record) {
    //         return response()->json(['success' => false, 'message' => 'Record not found'], 404);
    //     }

    //     try {
    //         DB::beginTransaction();

    //         // Get the date from the request (passed from frontend)
    //         $selectedDate = $request->query('date');

    //         if (!$selectedDate) {
    //             return response()->json(['success' => false, 'message' => 'Date parameter is required'], 400);
    //         }

    //         // Delete records with the same parent_id where date is greater than or equal to the selected date
    //         $deleted = GeneralTimetable::where('branch_id', session('branch_id'))->where('parent_id', $record->parent_id)
    //             ->where('date', '>=', $selectedDate)
    //             ->delete();

    //         if ($deleted) {
    //             DB::commit();
    //             return response()->json(['success' => true]);
    //         }

    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => 'No records deleted'], 404);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    //     }
    // }


    public function deleteGeneral($id, Request $request)
    {
        $record = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $id)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Timetable record not found'], 404);
        }

        try {
            DB::beginTransaction();

            $selectedDate = $request->query('date');
            if (!$selectedDate) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Date parameter is required'], 400);
            }

            // Normalize date format (handle DD-MM-YYYY or YYYY-MM-DD)
            try {
                // Try to parse the date - it might be in DD-MM-YYYY format from frontend
                if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $selectedDate)) {
                    // Format: DD-MM-YYYY, convert to YYYY-MM-DD
                    $parts = explode('-', $selectedDate);
                    $selectedDate = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
                // Ensure date is in YYYY-MM-DD format
                $selectedDate = date('Y-m-d', strtotime($selectedDate));
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid date format: ' . $selectedDate], 400);
            }

            $recordId = $record->id;
            $recordParentId = $record->parent_id;

            // Comprehensive deletion logic - similar to storeGeneralTimetable
            // Delete ALL records from selected date onwards that belong to this class chain
            $deletedCount = 0;

            // Method 1: Delete by parent_id relationship
            $deletedCount += GeneralTimetable::where(function($query) use ($recordId, $recordParentId, $id) {
                // Always delete the existing record itself
                $query->where('id', $id);

                // Delete all children where parent_id = existing record id
                $query->orWhere('parent_id', $recordId);

                // If existing record has a parent_id (is a child), also delete all siblings
                if ($recordParentId !== null && $recordParentId != $recordId) {
                    $query->orWhere('parent_id', $recordParentId);
                }
            })
            ->where('branch_id', session('branch_id'))
            ->whereDate('date', '>=', $selectedDate)
            ->delete();

            if ($deletedCount > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount timetable record(s) deleted successfully"
                ]);
            }

            // If no records found with parent_id method, try deleting just the record itself
            $record->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Timetable record deleted successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ], 500);
        }
    }
    public function checkSessionLimit(Request $request)
    {
        $familyId = $request->input('family_id');
        $studentName = $request->input('student_name');
        $subject = $request->input('subject');
        $selectedDate = $request->input('date', date('Y-m-d'));

        Log::info('=== checkSessionLimit START ===', [
            'family_id' => $familyId,
            'student_name' => $studentName,
            'subject' => $subject,
            'selected_date' => $selectedDate,
            'branch_id' => session('branch_id')
        ]);

        // Get student data to check session limits
        $studentData = DB::table('studentdata')
            ->where('branch_id', session('branch_id'))
            ->where('admissionid', $familyId)
            ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), 'LIKE', '%' . $studentName . '%')
            ->select('studentid', 'subject_names', 'studenthours')
            ->first();

        if (!$studentData) {
            Log::info('checkSessionLimit: Student not found in studentdata', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'branch_id' => session('branch_id')
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $subject_names = $studentData->subject_names ? json_decode($studentData->subject_names, true) : [];
        $studenthours = $studentData->studenthours ? json_decode($studentData->studenthours, true) : [];

        // Calculate TOTAL weekly quota (sum of all subjects)
        $totalWeeklyQuota = 0;
        if (!empty($studenthours) && is_array($studenthours)) {
            foreach ($studenthours as $hours) {
                $totalWeeklyQuota += (int)$hours;
            }
        }

        // Calculate current week (Monday to Sunday)
        $selectedDateTimestamp = strtotime($selectedDate);
        $dayOfWeek = date('w', $selectedDateTimestamp);
        $mondayOffset = ($dayOfWeek == 0) ? -6 : (1 - $dayOfWeek);
        $mondayOfWeek = date('Y-m-d', $selectedDateTimestamp + ($mondayOffset * 86400));
        $sundayOfWeek = date('Y-m-d', strtotime($mondayOfWeek . ' +6 days'));

        Log::info('checkSessionLimit: Week calculation', [
            'monday_of_week' => $mondayOfWeek,
            'sunday_of_week' => $sundayOfWeek,
            'total_weekly_quota' => $totalWeeklyQuota
        ]);

        // Count current week attendance for this student across ALL subjects (not just current subject)
        $currentWeekAttendanceCount = 0;
        if ($totalWeeklyQuota > 0) {
            // First, let's check what attendance records exist for debugging
            $allAttendanceRecords = Attendance::where('branch_id', session('branch_id'))
                ->where('family_id', $familyId)
                ->where('student_name', 'LIKE', '%' . $studentName . '%')
                ->whereBetween('date', [$mondayOfWeek, $sundayOfWeek])
                ->select('id', 'date', 'student_name', 'subject', 'attendance_type')
                ->get();

            Log::info('checkSessionLimit: Attendance records found for current week', [
                'family_id' => $familyId,
                'student_name_search' => $studentName,
                'week_start' => $mondayOfWeek,
                'week_end' => $sundayOfWeek,
                'records_count' => $allAttendanceRecords->count(),
                'records' => $allAttendanceRecords->map(function($record) {
                    return [
                        'id' => $record->id,
                        'date' => $record->date,
                        'student_name' => $record->student_name,
                        'subject' => $record->subject,
                        'attendance_type' => $record->attendance_type
                    ];
                })->toArray()
            ]);

            // Also check ALL attendance records for this student to see name variations
            $allStudentAttendance = Attendance::where('branch_id', session('branch_id'))
                ->where('family_id', $familyId)
                ->where('student_name', 'LIKE', '%' . $studentName . '%')
                ->select('id', 'date', 'student_name', 'subject')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();

            Log::info('checkSessionLimit: Sample attendance records (last 10) for name matching', [
                'family_id' => $familyId,
                'student_name_search' => $studentName,
                'sample_records' => $allStudentAttendance->map(function($record) {
                    return [
                        'date' => $record->date,
                        'student_name_in_db' => $record->student_name,
                        'subject' => $record->subject
                    ];
                })->toArray()
            ]);

            $currentWeekAttendanceCount = $allAttendanceRecords->count();
        }

        // Calculate carry forward from previous weeks
        // Get first date from attendance where attendance_type is NOT NULL and date >= 2025-12-01
        $globalResetDate = '2025-12-01';
        $firstTimetableDateRaw = DB::table('attendance')
            ->where('branch_id', session('branch_id'))
            ->where('family_id', $familyId)
            ->where('student_name', 'LIKE', '%' . $studentName . '%')
            ->whereNotNull('attendance_type')
            ->where('date', '>=', $globalResetDate)
            ->orderBy('date', 'asc')
            ->value('date');

        $firstTimetableDate = null;
        if ($firstTimetableDateRaw) {
            // Use the first attendance date (already >= 2025-12-01)
            $firstTimetableDate = $firstTimetableDateRaw;
        } else {
            // If no attendance found, check if there's any attendance before reset date
            $oldestAttendance = DB::table('attendance')
                ->where('branch_id', session('branch_id'))
                ->where('family_id', $familyId)
                ->where('student_name', 'LIKE', '%' . $studentName . '%')
                ->whereNotNull('attendance_type')
                ->orderBy('date', 'asc')
                ->value('date');
            
            if ($oldestAttendance && $oldestAttendance < $globalResetDate) {
                // Existing student - use global reset date
                $firstTimetableDate = $globalResetDate;
            }
        }

        // Calculate carry forward from ALL previous weeks (unused lessons accumulate)
        // Example: Week 1 quota 2, took 1 → 1 unused carries forward
        // Week 2 can use: Week 2 quota (2) + Carry forward (1) = 3 total
        $carryForwardSessions = 0;
        if ($firstTimetableDate && $totalWeeklyQuota > 0) {
            // Calculate all previous weeks from first timetable date (when student was added) to current week
            $firstDateTimestamp = strtotime($firstTimetableDate);
            $firstDayOfWeek = date('w', $firstDateTimestamp);
            $firstMondayOffset = ($firstDayOfWeek == 0) ? -6 : (1 - $firstDayOfWeek);
            $firstMondayOfWeek = date('Y-m-d', $firstDateTimestamp + ($firstMondayOffset * 86400));

            // Calculate previous weeks (before current week)
            $previousWeekSunday = date('Y-m-d', strtotime($mondayOfWeek . ' -1 day'));

            // Only calculate if there are previous weeks
            if ($firstMondayOfWeek < $mondayOfWeek) {
                // Count ALL attendance in previous weeks (from first date to previous week Sunday)
                $previousWeeksAttendance = Attendance::where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $studentName . '%')
                    ->whereBetween('date', [$firstMondayOfWeek, $previousWeekSunday])
                    ->count();

                // Calculate how many complete weeks have passed from first week to previous week
                $daysDiff = strtotime($previousWeekSunday) - strtotime($firstMondayOfWeek);
                $weeksPassed = floor($daysDiff / (7 * 24 * 60 * 60)) + 1; // +1 to include first week

                // Total quota available in all previous weeks
                $totalQuotaForPreviousWeeks = $weeksPassed * $totalWeeklyQuota;

                // Carry forward = Unused lessons from ALL previous weeks
                // If 3 weeks passed with quota 2 each = 6 total, and 5 lessons taken, carry forward = 1
                $carryForwardSessions = max(0, $totalQuotaForPreviousWeeks - $previousWeeksAttendance);

                Log::info('checkSessionLimit: Carry forward calculation', [
                    'family_id' => $familyId,
                    'student_name' => $studentName,
                    'first_monday_of_week' => $firstMondayOfWeek,
                    'previous_week_sunday' => $previousWeekSunday,
                    'weeks_passed' => $weeksPassed,
                    'total_quota_for_previous_weeks' => $totalQuotaForPreviousWeeks,
                    'previous_weeks_attendance' => $previousWeeksAttendance,
                    'calculated_carry_forward' => $carryForwardSessions,
                    'raw_carry_forward_before_max' => ($totalQuotaForPreviousWeeks - $previousWeeksAttendance)
                ]);
            } else {
                Log::info('checkSessionLimit: No previous weeks to calculate carry forward', [
                    'family_id' => $familyId,
                    'student_name' => $studentName,
                    'first_monday_of_week' => $firstMondayOfWeek,
                    'current_monday_of_week' => $mondayOfWeek
                ]);
            }
        } else {
            Log::info('checkSessionLimit: Cannot calculate carry forward', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'first_timetable_date' => $firstTimetableDate,
                'total_weekly_quota' => $totalWeeklyQuota
            ]);
        }

        // Calculate total remaining lessons using SAME monthly breakdown logic as lesson tracking report
        // This ensures consistency - report shows March has 9 remaining, so we should allow using those
        $totalRemainingLessons = 0;
        
        Log::info('checkSessionLimit: Starting monthly breakdown calculation', [
            'family_id' => $familyId,
            'student_name' => $studentName,
            'first_timetable_date' => $firstTimetableDate,
            'total_weekly_quota' => $totalWeeklyQuota,
            'condition_met' => ($firstTimetableDate && $totalWeeklyQuota > 0)
        ]);
        
        if ($firstTimetableDate && $totalWeeklyQuota > 0) {
            $today = date('Y-m-d');
            
            // Get all attendance from first date to today
            $allAttendanceRecords = Attendance::where('branch_id', session('branch_id'))
                ->where('family_id', $familyId)
                ->where('student_name', 'LIKE', '%' . $studentName . '%')
                ->whereBetween('date', [$firstTimetableDate, $today])
                ->select('date')
                ->get();
            
            // Use same monthly breakdown logic as getLessonTrackingData
            $firstDateTimestamp = strtotime($firstTimetableDate);
            $firstMonth = date('Y-m', $firstDateTimestamp);
            $currentMonth = date('Y-m');
            
            $carryForwardUnusedQuota = 0;
            $carryForwardIncompleteDays = 0;
            $monthIterator = $firstMonth;
            $isFirstMonth = true;
            
            // Get quota history for historical quota calculation
            $quotaHistory = collect([]);
            if ($studentData && isset($studentData->studentid)) {
                $quotaHistory = DB::table('quota_history')
                    ->where('branch_id', session('branch_id'))
                    ->where('studentid', $studentData->studentid)
                    ->orderBy('change_date', 'desc')
                    ->get();
            }
            
            $getQuotaForDate = function($date) use ($quotaHistory, $totalWeeklyQuota, $firstTimetableDate) {
                if ($quotaHistory->isEmpty()) {
                    return $totalWeeklyQuota;
                }
                $sortedHistory = $quotaHistory->sortBy('change_date');
                $applicableQuota = null;
                foreach ($sortedHistory as $change) {
                    if ($change->change_date <= $date) {
                        $applicableQuota = $change->new_total_weekly_quota;
                    } else {
                        break;
                    }
                }
                if ($applicableQuota === null) {
                    $firstChange = $sortedHistory->first();
                    if ($firstChange) {
                        $applicableQuota = $firstChange->old_total_weekly_quota;
                    }
                }
                return $applicableQuota ?? $totalWeeklyQuota;
            };
            
            while ($monthIterator <= $currentMonth) {
                $monthStartDate = $monthIterator . '-01';
                $monthEndDate = date('Y-m-t', strtotime($monthStartDate));
                
                // Count attendance in this month
                $attendanceStartDate = max($monthStartDate, $firstTimetableDate);
                $monthAttendance = 0;
                foreach ($allAttendanceRecords as $record) {
                    $recordDate = $record->date;
                    if ($recordDate >= $attendanceStartDate && $recordDate <= $monthEndDate) {
                        $monthAttendance++;
                    }
                }
                
                // Calculate period start date
                if ($isFirstMonth) {
                    $periodStartDate = $firstTimetableDate;
                    $isFirstMonth = false;
                } else {
                    $periodStartDate = $monthStartDate;
                }
                
                if ($carryForwardIncompleteDays > 0) {
                    $periodStartDate = date('Y-m-d', strtotime($periodStartDate . ' -' . $carryForwardIncompleteDays . ' days'));
                }
                
                $startTimestamp = strtotime($periodStartDate);
                $endTimestamp = strtotime($monthEndDate);
                $totalDays = floor(($endTimestamp - $startTimestamp) / (24 * 60 * 60)) + 1;
                
                $completeWeeks = floor($totalDays / 7);
                $remainingDays = $totalDays % 7;
                
                // Get quota for this month
                $monthQuota = $getQuotaForDate($monthEndDate);
                $monthlyQuota = $completeWeeks * $monthQuota;
                
                // Available = Monthly quota + Carry forward
                $availableThisMonth = $monthlyQuota + $carryForwardUnusedQuota;
                
                // Remaining = Available - Taken
                $remainingThisMonth = max(0, $availableThisMonth - $monthAttendance);
                
                // For current month, use the remaining
                if ($monthIterator == $currentMonth) {
                    $totalRemainingLessons = $remainingThisMonth;
                    
                    Log::info('checkSessionLimit: Monthly breakdown - Current month calculation', [
                        'family_id' => $familyId,
                        'student_name' => $studentName,
                        'current_month' => $currentMonth,
                        'monthly_quota' => $monthlyQuota,
                        'carry_forward_unused_quota' => $carryForwardUnusedQuota,
                        'available_this_month' => $availableThisMonth,
                        'month_attendance' => $monthAttendance,
                        'remaining_this_month' => $remainingThisMonth,
                        'total_remaining_lessons' => $totalRemainingLessons,
                        'complete_weeks' => $completeWeeks,
                        'month_quota' => $monthQuota
                    ]);
                    
                    break;
                }
                
                // Carry forward to next month = remaining
                $carryForwardUnusedQuota = $remainingThisMonth;
                $carryForwardIncompleteDays = $remainingDays;
                
                // Move to next month
                $monthIterator = date('Y-m', strtotime($monthIterator . '-01 +1 month'));
            }
            
            Log::info('checkSessionLimit: Total remaining lessons calculation (monthly breakdown)', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'first_timetable_date' => $firstTimetableDate,
                'today' => $today,
                'current_month' => $currentMonth,
                'total_remaining_lessons' => $totalRemainingLessons
            ]);
        }

        // Total available sessions = Current week quota + Carry forward from ALL previous weeks
        // This allows student to use unused lessons from any previous week in current/future weeks
        $totalAvailableSessions = $totalWeeklyQuota + $carryForwardSessions;
        
        // Also consider total remaining lessons - if there are remaining lessons overall, allow using them
        // This ensures consistency with the lesson tracking report
        $effectiveAvailableSessions = max($totalAvailableSessions, $totalRemainingLessons);

        // Check if session limit is reached
        // IMPORTANT: If there are remaining lessons overall (as shown in lesson tracking report),
        // allow using them even if current week quota is exhausted
        // This ensures consistency with lesson tracking report which shows remaining lessons
        $sessionLimitReached = false;
        
        Log::info('checkSessionLimit: Before limit check', [
            'family_id' => $familyId,
            'student_name' => $studentName,
            'current_week_attendance_count' => $currentWeekAttendanceCount,
            'total_available_sessions' => $totalAvailableSessions,
            'total_remaining_lessons' => $totalRemainingLessons,
            'total_weekly_quota' => $totalWeeklyQuota,
            'carry_forward_sessions' => $carryForwardSessions
        ]);
        
        // ALWAYS allow if there are remaining lessons from monthly breakdown
        // This matches the report which shows remaining lessons can be used
        if ($totalRemainingLessons > 0) {
            // There are remaining lessons available (e.g., 9 remaining from monthly breakdown)
            // Allow using them - don't block the student
            $sessionLimitReached = false;
            
            Log::info('checkSessionLimit: Allowing use of remaining lessons', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'total_remaining_lessons' => $totalRemainingLessons,
                'current_week_attendance_count' => $currentWeekAttendanceCount,
                'total_available_sessions' => $totalAvailableSessions,
                'session_limit_reached' => false,
                'reason' => 'Remaining lessons available from monthly breakdown'
            ]);
        } else if ($totalAvailableSessions > 0 && $currentWeekAttendanceCount >= $totalAvailableSessions) {
            // No remaining lessons AND current week quota exhausted
            $sessionLimitReached = true;
            
            Log::info('checkSessionLimit: Session limit reached - no remaining lessons', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'current_week_attendance_count' => $currentWeekAttendanceCount,
                'total_available_sessions' => $totalAvailableSessions,
                'total_remaining_lessons' => $totalRemainingLessons
            ]);
        } else {
            Log::info('checkSessionLimit: No limit issue - week quota not exhausted', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'current_week_attendance_count' => $currentWeekAttendanceCount,
                'total_available_sessions' => $totalAvailableSessions
            ]);
        }

        // Also return per-subject limit for display purposes
        $sessionLimit = null;
        if (!empty($subject) && !empty($subject_names) && !empty($studenthours) && is_array($subject_names) && is_array($studenthours)) {
            $subjectIndex = array_search($subject, $subject_names);
            if ($subjectIndex !== false && isset($studenthours[$subjectIndex])) {
                $sessionLimit = (int)$studenthours[$subjectIndex];
            }
        }

        Log::info('checkSessionLimit: Final calculation', [
            'family_id' => $familyId,
            'student_name' => $studentName,
            'current_week_attendance_count' => $currentWeekAttendanceCount,
            'total_weekly_quota' => $totalWeeklyQuota,
            'carry_forward_sessions' => $carryForwardSessions,
            'total_available_sessions' => $totalAvailableSessions,
            'session_limit_reached' => $sessionLimitReached,
            'session_limit_per_subject' => $sessionLimit
        ]);

        Log::info('=== checkSessionLimit END ===');

        return response()->json([
            'success' => true,
            'session_data' => [
                'session_limit' => $sessionLimit, // Per subject (for display)
                'total_weekly_quota' => $totalWeeklyQuota, // Total across all subjects
                'current_week_attendance_count' => $currentWeekAttendanceCount,
                'carry_forward_sessions' => $carryForwardSessions,
                'total_available_sessions' => $totalAvailableSessions,
                'total_remaining_lessons' => $totalRemainingLessons, // Total remaining from overall quota (matches lesson tracking report)
                'session_limit_reached' => $sessionLimitReached
            ]
        ]);
    }

    public function getEditIndfo($id)
    {
        // Fetch the timetable record
        $fetch = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $id)->first();
        if (!$fetch) {
            return response()->json(['success' => false, 'message' => 'Timetable not found'], 404);
        }

        $selectedDay = $fetch->date;
        $slotIndex = $fetch->slot;
        $currentDay = date('w', strtotime($selectedDay));

        // Map slot index to time period
        $timePeriod = '';
        if ($currentDay >= 1 && $currentDay <= 5) {
            switch ($slotIndex) {
                case '1':
                    $timePeriod = '11:00 - 01:00pm';
                    break;
                case '2':
                    $timePeriod = '01:30 - 03:30pm';
                    break;
                case '3':
                    $timePeriod = '04:30 - 06:30pm';
                    break;
                case '4':
                    $timePeriod = '06:45 - 08:45pm';
                    break;
                case '7':
                    $timePeriod = '11:00 - 01:00pm';
                    break;
                case '8':
                    $timePeriod = '01:30 - 03:30pm';
                    break;
                default:
                    $timePeriod = 'Unknown Slot';
            }
        } else {
            switch ($slotIndex) {
                case '1':
                    $timePeriod = '09:00 - 11:00am';
                    break;
                case '2':
                    $timePeriod = '11:20 - 01:20pm';
                    break;
                case '3':
                    $timePeriod = '02:00 - 04:00pm';
                    break;
                case '7':
                    $timePeriod = '11:00 - 01:00pm';
                    break;
                case '8':
                    $timePeriod = '01:30 - 03:30pm';
                    break;
                default:
                    $timePeriod = 'Unknown Slot';
            }
        }

        // Decode JSON-like fields (Eloquent may already cast some columns to array — json_decode needs string).
        $decodeJsonArray = static function ($value): array {
            if (is_array($value)) {
                return $value;
            }
            if ($value === null || $value === '') {
                return [];
            }
            if (!is_string($value)) {
                return [];
            }
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }
            $decoded = json_decode($trimmed, true);
            return is_array($decoded) ? $decoded : [];
        };

        $student_ids = $decodeJsonArray($fetch->student_ids);
        $student_names = $decodeJsonArray($fetch->student_names);
        $subjects = $decodeJsonArray($fetch->subjects);
        $books = $decodeJsonArray($fetch->books);
        $chapters = $decodeJsonArray($fetch->chapters);
        $is_attendance = $decodeJsonArray($fetch->is_attendance);
        $permanent = $decodeJsonArray($fetch->permanent);



        // Trim arrays to match student_ids length
        $subjects = array_slice($subjects, 0, count($student_ids));
        $books = array_slice($books, 0, count($student_ids));
        $chapters = array_slice($chapters, 0, count($student_ids));
        $is_attendance = array_slice($is_attendance, 0, count($student_ids));
        $permanent = array_slice($permanent, 0, count($student_ids));

        $filteredSlots = $this->filterCentralTimetableDisplayStudentRows(
            $student_ids,
            $student_names,
            $subjects,
            $is_attendance,
            $permanent,
            $books,
            $chapters,
            false
        );
        $student_ids = $filteredSlots['student_ids'];
        $student_names = $filteredSlots['student_names'];
        $subjects = $filteredSlots['subjects'];
        $is_attendance = $filteredSlots['is_attendance'];
        $permanent = $filteredSlots['permanent'];
        $books = $filteredSlots['books'];
        $chapters = $filteredSlots['chapters'];

        $currentDate = $selectedDay; // Use selected day for attendance check
        $attendanceRecords = [];
        $selected_vals = [];
        $is_homework = []; // Initialize is_homework array
        $behaviours = []; // Initialize behaviours array
        $performances = []; // Initialize performances array

        foreach ($student_ids as $index => $student_id) {
            if (!isset($student_names[$index])) {
                $is_homework[] = 'No';
                $behaviours[] = null;
                $performances[] = null;
                continue;
            }


            $student_name = str_ireplace('null', '', $student_names[$index]);
            $subject = $subjects[$index] ?? '';
            $book = $books[$index] ?? '';
            $chapter = $chapters[$index] ?? '';
            $attendance = $is_attendance[$index] ?? 'No';
            $permanent_value = $permanent[$index] ?? 'Yes';

            // Fetch studentyearinschool from studentdata table
            $student_data = DB::table('studentdata')
                ->where('branch_id', session('branch_id'))
                ->where('admissionid', $student_id)
                ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), 'LIKE', '%' . $student_name . '%')
                ->select('studentid', 'studentyearinschool', 'subject_names', 'medical_condition', 'studenthours')
                ->first();

            $studentyearinschool = $student_data ? $student_data->studentyearinschool : null;
            $subject_names = $student_data && $student_data->subject_names !== null && $student_data->subject_names !== ''
                ? $decodeJsonArray($student_data->subject_names)
                : [];
            $studenthours = $student_data && $student_data->studenthours !== null && $student_data->studenthours !== ''
                ? $decodeJsonArray($student_data->studenthours)
                : [];
            $has_medical_condition = false;
            $medical_conditions_explanation = null;
            $allergies_explanation = null;
            if ($student_data) {
                $medicalValue = strtolower((string) ($student_data->medical_condition ?? ''));
                $has_medical_condition = in_array($medicalValue, ['yes', '1', 'true'], true);

                // Fetch medical condition details from medical_condition table
                if ($has_medical_condition && $student_data->studentid) {
                    $medical_condition_data = DB::table('medical_condition')
                        ->where('branch_id', session('branch_id'))
                        ->where('student_id', $student_data->studentid)
                        ->select('medical_conditions_explanation', 'allergies_explanation')
                        ->first();

                    if ($medical_condition_data) {
                        $medical_conditions_explanation = $medical_condition_data->medical_conditions_explanation;
                        $allergies_explanation = $medical_condition_data->allergies_explanation;
                    }
                }
            }

            // If subject is empty, use the first subject from subject_names as fallback
            if (empty($subject) && !empty($subject_names) && is_array($subject_names)) {
                $subject = $subject_names[0] ?? '';
            }

            // Calculate current week (Monday to Sunday) for session limit check
            $selectedDateTimestamp = strtotime($selectedDay);
            $dayOfWeek = date('w', $selectedDateTimestamp); // 0 (Sunday) to 6 (Saturday)

            // Calculate Monday of current week
            $mondayOffset = ($dayOfWeek == 0) ? -6 : (1 - $dayOfWeek);
            $mondayOfWeek = date('Y-m-d', $selectedDateTimestamp + ($mondayOffset * 86400));
            $sundayOfWeek = date('Y-m-d', strtotime($mondayOfWeek . ' +6 days'));

            // Calculate TOTAL weekly quota (sum of all subjects) - flexible usage across subjects
            $totalWeeklyQuota = 0;
            if (!empty($studenthours) && is_array($studenthours)) {
                foreach ($studenthours as $hours) {
                    $totalWeeklyQuota += (int)$hours;
                }
            }

            // Count current week attendance for this student across ALL subjects (not just current subject)
            $currentWeekAttendanceCount = 0;
            if ($totalWeeklyQuota > 0) {
                $currentWeekAttendanceCount = Attendance::where('branch_id', session('branch_id'))
                    ->where('family_id', $student_id)
                    ->where('student_name', 'LIKE', '%' . $student_name . '%')
                    ->whereBetween('date', [$mondayOfWeek, $sundayOfWeek])
                    ->count();
            }

            // Calculate carry forward from previous weeks
            // Get first date from attendance where attendance_type is NOT NULL and date >= 2025-12-01
            $globalResetDate = '2025-12-01';
            $firstTimetableDateRaw = DB::table('attendance')
                ->where('branch_id', session('branch_id'))
                ->where('family_id', $student_id)
                ->where('student_name', 'LIKE', '%' . $student_name . '%')
                ->whereNotNull('attendance_type')
                ->where('date', '>=', $globalResetDate)
                ->orderBy('date', 'asc')
                ->value('date');

            $firstTimetableDate = null;
            if ($firstTimetableDateRaw) {
                // Use the first attendance date (already >= 2025-12-01)
                $firstTimetableDate = $firstTimetableDateRaw;
            } else {
                // If no attendance found, check if there's any attendance before reset date
                $oldestAttendance = DB::table('attendance')
                    ->where('branch_id', session('branch_id'))
                    ->where('family_id', $student_id)
                    ->where('student_name', 'LIKE', '%' . $student_name . '%')
                    ->whereNotNull('attendance_type')
                    ->orderBy('date', 'asc')
                    ->value('date');
                
                if ($oldestAttendance && $oldestAttendance < $globalResetDate) {
                    // Existing student - use global reset date
                    $firstTimetableDate = $globalResetDate;
                }
            }

            // Calculate carry forward from ALL previous weeks (unused lessons accumulate)
            // Example: Week 1 quota 2, took 1 → 1 unused carries forward
            // Week 2 can use: Week 2 quota (2) + Carry forward (1) = 3 total
            $carryForwardSessions = 0;
            if ($firstTimetableDate && $totalWeeklyQuota > 0) {
                // Calculate all previous weeks from first timetable date (when student was added) to current week
                $firstDateTimestamp = strtotime($firstTimetableDate);
                $firstDayOfWeek = date('w', $firstDateTimestamp);
                $firstMondayOffset = ($firstDayOfWeek == 0) ? -6 : (1 - $firstDayOfWeek);
                $firstMondayOfWeek = date('Y-m-d', $firstDateTimestamp + ($firstMondayOffset * 86400));

                // Calculate previous weeks (before current week)
                $previousWeekSunday = date('Y-m-d', strtotime($mondayOfWeek . ' -1 day'));

                // Only calculate if there are previous weeks
                if ($firstMondayOfWeek < $mondayOfWeek) {
                    // Count ALL attendance in previous weeks (from first date to previous week Sunday)
                    $previousWeeksAttendance = Attendance::where('branch_id', session('branch_id'))
                        ->where('family_id', $student_id)
                        ->where('student_name', 'LIKE', '%' . $student_name . '%')
                        ->whereBetween('date', [$firstMondayOfWeek, $previousWeekSunday])
                        ->count();

                    // Calculate how many complete weeks have passed from first week to previous week
                    $daysDiff = strtotime($previousWeekSunday) - strtotime($firstMondayOfWeek);
                    $weeksPassed = floor($daysDiff / (7 * 24 * 60 * 60)) + 1; // +1 to include first week

                    // Total quota available in all previous weeks
                    $totalQuotaForPreviousWeeks = $weeksPassed * $totalWeeklyQuota;

                    // Carry forward = Unused lessons from ALL previous weeks
                    // If 3 weeks passed with quota 2 each = 6 total, and 5 lessons taken, carry forward = 1
                    $carryForwardSessions = max(0, $totalQuotaForPreviousWeeks - $previousWeeksAttendance);
                }
            }

            // Total available sessions = Current week quota + Carry forward from ALL previous weeks
            // This allows student to use unused lessons from any previous week in current/future weeks
            $totalAvailableSessions = $totalWeeklyQuota + $carryForwardSessions;

            // Calculate total remaining lessons using SAME monthly breakdown logic as lesson tracking report
            // This ensures consistency - report shows remaining lessons, so we should allow using those
            $totalRemainingLessons = 0;
            
            if ($firstTimetableDate && $totalWeeklyQuota > 0) {
                $today = date('Y-m-d');
                
                // Get all attendance from first date to today
                $allAttendanceRecords = Attendance::where('branch_id', session('branch_id'))
                    ->where('family_id', $student_id)
                    ->where('student_name', 'LIKE', '%' . $student_name . '%')
                    ->whereBetween('date', [$firstTimetableDate, $today])
                    ->select('date')
                    ->get();
                
                // Use same monthly breakdown logic as getLessonTrackingData
                $firstDateTimestamp = strtotime($firstTimetableDate);
                $firstMonth = date('Y-m', $firstDateTimestamp);
                $currentMonth = date('Y-m');
                
                $carryForwardUnusedQuota = 0;
                $carryForwardIncompleteDays = 0;
                $monthIterator = $firstMonth;
                $isFirstMonth = true;
                
                // Get quota history for historical quota calculation
                $quotaHistory = collect([]);
                if ($student_data && isset($student_data->studentid)) {
                    $quotaHistory = DB::table('quota_history')
                        ->where('branch_id', session('branch_id'))
                        ->where('studentid', $student_data->studentid)
                        ->orderBy('change_date', 'desc')
                        ->get();
                }
                
                $getQuotaForDate = function($date) use ($quotaHistory, $totalWeeklyQuota, $firstTimetableDate) {
                    if ($quotaHistory->isEmpty()) {
                        return $totalWeeklyQuota;
                    }
                    $sortedHistory = $quotaHistory->sortBy('change_date');
                    $applicableQuota = null;
                    foreach ($sortedHistory as $change) {
                        if ($change->change_date <= $date) {
                            $applicableQuota = $change->new_total_weekly_quota;
                        } else {
                            break;
                        }
                    }
                    if ($applicableQuota === null) {
                        $firstChange = $sortedHistory->first();
                        if ($firstChange) {
                            $applicableQuota = $firstChange->old_total_weekly_quota;
                        }
                    }
                    return $applicableQuota ?? $totalWeeklyQuota;
                };
                
                while ($monthIterator <= $currentMonth) {
                    $monthStartDate = $monthIterator . '-01';
                    $monthEndDate = date('Y-m-t', strtotime($monthStartDate));
                    
                    // Count attendance in this month
                    $attendanceStartDate = max($monthStartDate, $firstTimetableDate);
                    $monthAttendance = 0;
                    foreach ($allAttendanceRecords as $record) {
                        $recordDate = $record->date;
                        if ($recordDate >= $attendanceStartDate && $recordDate <= $monthEndDate) {
                            $monthAttendance++;
                        }
                    }
                    
                    // Calculate period start date
                    if ($isFirstMonth) {
                        $periodStartDate = $firstTimetableDate;
                        $isFirstMonth = false;
                    } else {
                        $periodStartDate = $monthStartDate;
                    }
                    
                    if ($carryForwardIncompleteDays > 0) {
                        $periodStartDate = date('Y-m-d', strtotime($periodStartDate . ' -' . $carryForwardIncompleteDays . ' days'));
                    }
                    
                    $startTimestamp = strtotime($periodStartDate);
                    $endTimestamp = strtotime($monthEndDate);
                    $totalDays = floor(($endTimestamp - $startTimestamp) / (24 * 60 * 60)) + 1;
                    
                    $completeWeeks = floor($totalDays / 7);
                    $remainingDays = $totalDays % 7;
                    
                    // Get quota for this month
                    $monthQuota = $getQuotaForDate($monthEndDate);
                    $monthlyQuota = $completeWeeks * $monthQuota;
                    
                    // Available = Monthly quota + Carry forward
                    $availableThisMonth = $monthlyQuota + $carryForwardUnusedQuota;
                    
                    // Remaining = Available - Taken
                    $remainingThisMonth = max(0, $availableThisMonth - $monthAttendance);
                    
                    // For current month, use the remaining
                    if ($monthIterator == $currentMonth) {
                        $totalRemainingLessons = $remainingThisMonth;
                        break;
                    }
                    
                    // Carry forward to next month = remaining
                    $carryForwardUnusedQuota = $remainingThisMonth;
                    $carryForwardIncompleteDays = $remainingDays;
                    
                    // Move to next month
                    $monthIterator = date('Y-m', strtotime($monthIterator . '-01 +1 month'));
                }
            }

            // Check if session limit is reached
            // If there are remaining lessons from monthly breakdown, allow student to use them
            if ($totalRemainingLessons > 0) {
                $sessionLimitReached = false; // Allow using remaining lessons
            } else {
                $sessionLimitReached = ($totalAvailableSessions > 0 && $currentWeekAttendanceCount >= $totalAvailableSessions);
            }

            // Also get per-subject limit for display purposes
            $sessionLimit = null;
            if (!empty($subject) && !empty($subject_names) && !empty($studenthours) && is_array($subject_names) && is_array($studenthours)) {
                $subjectIndex = array_search($subject, $subject_names);
                if ($subjectIndex !== false && isset($studenthours[$subjectIndex])) {
                    $sessionLimit = (int)$studenthours[$subjectIndex];
                }
            }

            // Fetch attendance record to get book and chapter if available
            $attendanceRecord = Attendance::where('branch_id', session('branch_id'))
                ->where('family_id', $student_id)
                ->where('date', $currentDate)
                ->where('student_name', 'LIKE', '%' . $student_name . '%')
                ->where('time_slot', $timePeriod)
                ->first();


            $homework = $attendanceRecord && in_array($attendanceRecord->status, ['Yes', 'No']) ? $attendanceRecord->status : 'No';
            $is_homework[] = $homework;


            // Fetch behaviour and performance from attendance record
            $behaviour = $attendanceRecord ? $attendanceRecord->behaviour : null;
            $performance = $attendanceRecord ? $attendanceRecord->performance : null;
            $behaviours[] = $behaviour;
            $performances[] = $performance;


            // If attendance record exists, use its book and chapter if not already set
            if ($attendanceRecord && !empty($attendanceRecord->bk_ch)) {
                $bk_ch_parts = explode('-', $attendanceRecord->bk_ch, 2);
                if (count($bk_ch_parts) === 2) {
                    list($att_book, $att_ch) = $bk_ch_parts;
                    $book = $book ?: $att_book;
                    $chapter = $chapter ?: $att_ch;
                } else {
                    // Log invalid bk_ch and set defaults
                    Log::warning("Invalid bk_ch format for family_id: $student_id, bk_ch: {$attendanceRecord->bk_ch}");
                    $book = $book ?: $bk_ch_parts[0];
                    $chapter = $chapter ?: '';
                }
            }

            // Fetch all available books for the student and subject
            $available_books = AssignBook::where('branch_id', session('branch_id'))
                ->where('family_id', $student_id)
                ->where('student_name', 'LIKE', '%' . $student_name . '%')
                ->where('subject', $subject)
                ->latest('id') // or ->latest('created_at')
                ->take(1)      // ✅ only one latest record
                ->get();

            // Add available books to attendanceRecords
            foreach ($available_books as $ab) {
                $attendanceRecords[] = [
                    'book_name' => $ab->book,
                    'ch' => '',
                    'family_id' => $ab->family_id,
                    'student_name' => $ab->student_name,
                    'subject' => $ab->subject,
                    'make_dropdown_selected' => ($ab->book === $book && $book !== '') ? 'yes' : 'no',
                    'studentyearinschool' => $studentyearinschool,
                    'permanent' => $permanent_value,
                    'subject_names' => $subject_names, // Include subject_names
                ];
            }

            // Add the selected book and chapter to selected_vals
            $selected_vals[] = [
                'book_name' => $book,
                'ch' => $chapter,
                'family_id' => $student_id,
                'student_name' => $student_name,
                'subject' => $subject,
                'make_dropdown_selected' => ($book !== '') ? 'yes' : 'no',
                'studentyearinschool' => $studentyearinschool,
                'permanent' => $permanent_value,
                'subject_names' => $subject_names, // Include subject_names
                'has_medical_condition' => $has_medical_condition,
                'medical_conditions_explanation' => $medical_conditions_explanation,
                'allergies_explanation' => $allergies_explanation,
                'session_limit' => $sessionLimit, // Per subject (for display)
                'total_weekly_quota' => $totalWeeklyQuota, // Total across all subjects
                'current_week_attendance_count' => $currentWeekAttendanceCount,
                'carry_forward_sessions' => $carryForwardSessions,
                'total_available_sessions' => $totalAvailableSessions,
                'session_limit_reached' => $sessionLimitReached,
                'total_remaining_lessons' => $totalRemainingLessons,
            ];
        }

        // Filter selected_vals to avoid duplicates
        $filtered_vals = [];
        foreach ($selected_vals as $item) {
            $key = $item['family_id'] . '|' . $item['student_name'] . '|' . $item['subject'];
            if (!isset($filtered_vals[$key])) {
                $filtered_vals[$key] = $item;
            } elseif (!empty($item['ch']) && empty($filtered_vals[$key]['ch'])) {
                $filtered_vals[$key] = $item;
            }
        }
        $filtered_vals = array_values($filtered_vals);

        // Group books by family_id and student_name
        $groupedBooks = collect($attendanceRecords)
            ->filter()
            ->groupBy(function ($item) {
                return $item['family_id'] . '-' . $item['student_name'];
            })
            ->map(function ($group) {
                return $group->toArray();
            });
        // dd($groupedBooks);
        // Format selected books and chapters
        $formatted_books = [];
        $ch = [];
        foreach ($filtered_vals as $key => $value) {
            $book_key = 'book-' . ($key + 1);
            $ch_key = 'ch-' . ($key + 1);
            $formatted_books[$book_key] = $value['book_name'] ?: '';
            $ch[$ch_key] = $value['ch'] ?: '';
        }

        return response()->json([
            'success' => true,
            'data' => $fetch,
            'attendanceRecords' => $attendanceRecords,
            'books' => $groupedBooks,
            'selected_books' => $formatted_books,
            'ch' => $ch,
            'students' => $filtered_vals,
            'is_homework' => $is_homework,
            'behaviours' => $behaviours,
            'performances' => $performances,
        ], 200);
    }
    // public function getEditIndfo($id)
    // {
    //     $fetch = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $id)->first();
    //     if (!$fetch) {
    //         return response()->json(['success' => false, 'message' => 'Timetable not found'], 404);
    //     }

    //     $selectedDay = $fetch->date;
    //     $slotIndex = $fetch->slot;
    //     $currentDay = date('w', strtotime($selectedDay));

    //     // Determine the time period based on the day and slot
    //     if ($currentDay >= 1 && $currentDay <= 5) {
    //         switch ($slotIndex) {
    //             case '1':
    //                 $timePeriod = '04:30 - 06:30';
    //                 break;
    //             case '2':
    //                 $timePeriod = '06:45 - 08:45';
    //                 break;
    //             default:
    //                 $timePeriod = 'Unknown Slot';
    //         }
    //     } else if ($currentDay == 0 || $currentDay == 6) {
    //         switch ($slotIndex) {
    //             case '1':
    //                 $timePeriod = '09:00 - 11:00';
    //                 break;
    //             case '2':
    //                 $timePeriod = '11:20 - 01:20';
    //                 break;
    //             case '3':
    //                 $timePeriod = '01:45 - 03:45';
    //                 break;
    //             default:
    //                 $timePeriod = 'Unknown Slot';
    //         }
    //     }

    //     $student_ids = json_decode($fetch->student_ids, true) ?? [];
    //     $student_names = json_decode($fetch->student_names, true) ?? [];
    //     $subjects = json_decode($fetch->subjects, true) ?? [];
    //     $permanent = json_decode($fetch->permanent, true) ?? [];

    //     // Trim subjects array to match student_ids length
    //     $subjects = array_slice($subjects, 0, count($student_ids));

    //     $currentDate = now()->format('Y-m-d');
    //     $attendanceRecords = [];
    //     $selected_vals = [];

    //     foreach ($student_ids as $index => $student_id) {
    //         if (!isset($student_names[$index])) {
    //             continue;
    //         }

    //         $student_name = str_ireplace('null', '', $student_names[$index]);

    //         // Fetch studentyearinschool from studentdata table
    //         $student_data = DB::table('studentdata')->where('branch_id', session('branch_id'))
    //             ->where('admissionid', $student_id)
    //             ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), 'LIKE', '%' . $student_name . '%')
    //             ->select('studentyearinschool')
    //             ->first();

    //         $studentyearinschool = $student_data ? $student_data->studentyearinschool : null;

    //         $attendanceRecord = Attendance::where('branch_id', session('branch_id'))->where('family_id', $student_id)
    //             ->where('date', $currentDate)
    //             ->where('student_name', 'LIKE', '%' . $student_name . '%')
    //             ->whereRaw("TRIM(TRAILING 'am' FROM TRIM(TRAILING 'pm' FROM time_slot)) = ?", [$timePeriod])
    //             ->first();

    //         $permanent_value = isset($permanent[$index]) ? $permanent[$index] : 'Yes';

    //         if ($attendanceRecord) {
    //             $bk_ch = $attendanceRecord->bk_ch;
    //             if ($bk_ch) {
    //                 list($book_name, $ch) = explode('-', $bk_ch, 2);
    //                 $latest = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $student_id)
    //                     ->where('student_name', 'LIKE', '%' . $student_name . '%')
    //                     ->where('subject', $subjects[$index])
    //                     ->latest()
    //                     ->first();

    //                 if ($latest) {
    //                     $selected_vals[] = [
    //                         'book_name' => $latest->book,
    //                         'ch' => $ch,
    //                         'family_id' => $latest->family_id,
    //                         'student_name' => $latest->student_name,
    //                         'subject' => $latest->subject,
    //                         'make_dropdown_selected' => 'yes',
    //                         'studentyearinschool' => $studentyearinschool,
    //                         'permanent' => $permanent_value,
    //                     ];
    //                 }

    //                 $books = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $student_id)
    //                     ->where('student_name', 'LIKE', '%' . $student_name . '%')
    //                     ->where('subject', $subjects[$index])
    //                     ->get();

    //                 foreach ($books as $book) {
    //                     $attendanceRecords[] = [
    //                         'book_name' => $book->book,
    //                         'ch' => '',
    //                         'family_id' => $book->family_id,
    //                         'student_name' => $book->student_name,
    //                         'subject' => $book->subject,
    //                         'make_dropdown_selected' => 'no',
    //                         'studentyearinschool' => $studentyearinschool,
    //                         'permanent' => $permanent_value,
    //                     ];
    //                 }
    //             } else {
    //                 $attendanceRecords[] = null;
    //             }
    //         } else {
    //             $books = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $student_id)
    //                 ->where('student_name', 'LIKE', '%' . $student_name . '%')
    //                 ->where('subject', $subjects[$index])
    //                 ->get();

    //             foreach ($books as $book) {
    //                 $attendanceRecords[] = [
    //                     'book_name' => $book->book,
    //                     'ch' => '',
    //                     'family_id' => $book->family_id,
    //                     'student_name' => $book->student_name,
    //                     'subject' => $book->subject,
    //                     'make_dropdown_selected' => 'no',
    //                     'studentyearinschool' => $studentyearinschool,
    //                     'permanent' => $permanent_value,
    //                 ];
    //             }

    //             $latest = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $student_id)
    //                 ->where('student_name', 'LIKE', '%' . $student_name . '%')
    //                 ->where('subject', $subjects[$index])
    //                 ->latest()
    //                 ->first();

    //             if ($latest) {
    //                 $selected_vals[] = [
    //                     'book_name' => $latest->book,
    //                     'ch' => '',
    //                     'family_id' => $latest->family_id,
    //                     'student_name' => $latest->student_name,
    //                     'subject' => $latest->subject,
    //                     'make_dropdown_selected' => 'yes',
    //                     'studentyearinschool' => $studentyearinschool,
    //                     'permanent' => $permanent_value,
    //                 ];
    //             } else {
    //                 $selected_vals[] = [
    //                     'book_name' => '',
    //                     'ch' => '',
    //                     'family_id' => $student_id,
    //                     'student_name' => $student_name,
    //                     'subject' => $subjects[$index] ?? '',
    //                     'make_dropdown_selected' => 'no',
    //                     'studentyearinschool' => $studentyearinschool,
    //                     'permanent' => $permanent_value,
    //                 ];
    //             }
    //         }
    //     }

    //     // Filter selected_vals to avoid duplicates
    //     $filtered_vals = [];
    //     foreach ($selected_vals as $item) {
    //         $key = $item['book_name'] . '|' . $item['family_id'] . '|' . $item['student_name'] . '|' . $item['subject'] . '|' . $item['make_dropdown_selected'];
    //         if (isset($filtered_vals[$key])) {
    //             if (!empty($item['ch']) && empty($filtered_vals[$key]['ch'])) {
    //                 $filtered_vals[$key] = $item;
    //             }
    //         } else {
    //             $filtered_vals[$key] = $item;
    //         }
    //     }
    //     $filtered_vals = array_values($filtered_vals);

    //     // Group books by family_id and student_name
    //     $groupedBooks = collect($attendanceRecords)
    //         ->filter()
    //         ->groupBy(function ($item) {
    //             return $item['family_id'] . '-' . $item['student_name'];
    //         });

    //     // Format selected books and chapters
    //     $formatted_books = [];
    //     $ch = [];
    //     foreach ($filtered_vals as $key => $value) {
    //         $book_key = 'book-' . ($key + 1);
    //         $ch_key = 'ch-' . ($key + 1);
    //         $formatted_books[$book_key] = $value['book_name'];
    //         $ch[$ch_key] = $value['ch'];
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $fetch,
    //         'attendanceRecords' => $attendanceRecords,
    //         'books' => $groupedBooks,
    //         'selected_books' => $formatted_books,
    //         'ch' => $ch,
    //         'students' => $filtered_vals,
    //     ], 200);
    // }



    public function fetchBkCh(Request $request)
    {
        $subject = $request->input('subject');
        $student = $request->input('student');
        // dd($subject,$student);
        preg_match('/^(\d+)\s+(.+)$/', $student, $matches);
        $family_id = $matches[1] ?? null;
        $name = $matches[2] ?? null;
        // dd($family_id,$name,$subject);
        if (!$family_id || !$name || !$subject) {
            return response()->json(['error' => 'Invalid input data'], 400);
        }

        $name = str_ireplace(['null', 'Null'], '', $name);


        $books = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $family_id)
            ->where('student_name', 'like', '%' . $name . '%')
            ->where('subject', $subject)
            ->latest('id') // or latest('created_at')
            ->take(1)      // ✅ only one latest record
            ->get();
        // dd($family_id,$name,$subject,$books);

        $latest = AssignBook::where('branch_id', session('branch_id'))->where('family_id', $family_id)
            ->where('student_name', $name)
            ->where('subject', $subject)
            ->orderBy('created_at', 'desc')
            ->first();
        $formattedBooks = $books->map(function ($book) use ($latest) {
            return [
                'name' => $book->book,
                'created_at' => $latest->created_at, // The latest book's created_at
                'is_latest' => ($book->created_at == $latest->created_at) // Check if it's the latest book
            ];
        });
        // dd($formattedBooks);
        return response()->json($formattedBooks);
    }

    public function generateStaffTimetable(Request $request)
    {
        // Get the selected teacher from the request
        $teacher = $request->input('teacher');

        // Get the current date and calculate the current week's Monday and Sunday dates
        $currentDate = \Carbon\Carbon::now();
        $monday = $currentDate->copy()->startOfWeek(); // Get Monday of the current week
        $sunday = $currentDate->copy()->endOfWeek();  // Get Sunday of the current week

        // Format the dates for easy comparison
        $fromDate = $monday->format('Y-m-d') ?? null;
        $toDate = $sunday->format('Y-m-d') ?? null;

        // If the teacher is selected, fetch the timetable for the selected teacher within the calculated date range
        if ($teacher) {
            $timetableRecords = GeneralTimetable::where('branch_id', session('branch_id'))->where('teacher_id', $teacher)
                ->whereBetween('date', [$fromDate, $toDate])
                ->get();

            $mergedRecords = [];

            foreach ($timetableRecords as $record) {
                $studentIds = json_decode($record->student_ids);
                $studentNames = json_decode($record->student_names);
                $subjects = json_decode($record->subjects);

                $mergedRecord = [
                    'id' => $record->id,
                    'date' => $record->date,
                    'slot' => $record->slot,
                    'teacher_id' => $record->teacher_id,
                    'student_ids' => $studentIds,
                    'student_names' => $studentNames,
                    'subjects' => $subjects,
                ];

                $mergedRecords[] = $mergedRecord;
            }
        } else {
            $mergedRecords = null;
        }
        if (!empty($mergedRecords)) {

            foreach ($mergedRecords as $key => $record) {
                // Count the number of students (based on student_ids or student_names, they are the same length)
                $studentCount = count($record['student_ids']);

                // Trim the subjects array to match the student count
                $mergedRecords[$key]['subjects'] = array_slice($record['subjects'], 0, $studentCount);
            }
        }

        // Dump the merged records array to see the result
        // dd($mergedRecords);


        if (!empty($mergedRecords)) {
            usort($mergedRecords, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
        }

        // Fetch the list of teachers
        $teachers = \DB::table('teachers_subject')->where('branch_id', session('branch_id'))
            ->where('is_available', 'yes')
            ->select('teacher_name')
            ->distinct()
            ->get();

        $prev = $request->input('teacher');
        // dd($mergedRecords);

        // Return the view with the generated timetable and teacher list
        return view('branchFrontend.centralTimeTable.generateTimetable', compact('teacher', 'prev', 'teachers', 'mergedRecords', 'fromDate', 'toDate'));
    }
    public function sendEmailTeacher(Request $request)
    {
        $timetableContent = json_decode($request->timetable_content, true);

        $pdf = new FPDF();
        $pdf->AddPage();

        $pageWidth = $pdf->GetPageWidth();
        $logoWidth = 50 * 0.9;
        $logoHeight = 35 * 0.9;
        $logoX = ($pageWidth - $logoWidth) / 2;

        // Use dynamic local logo path
        $logoPath = public_path('img/datesheetLogo.png');

        // Verify logo file exists
        if (!file_exists($logoPath)) {
            Session::flash('error', 'Logo file not found at ' . $logoPath);
            return back();
        }

        // Add logo to PDF with error handling
        try {
            $pdf->Image($logoPath, $logoX, 8, $logoWidth, $logoHeight);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to add logo to PDF: ' . $e->getMessage());
            return back();
        }

        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Teacher Timetable', 0, 1, 'C');

        $fromDate = Carbon::parse($request->from_date);
        $toDate = Carbon::parse($request->to_date);
        $formattedFromDate = $fromDate->format('d/m/Y');
        $formattedToDate = $toDate->format('d/m/Y');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFillColor(165, 201, 227);

        $columnWidths = [30, 30, 50, 30];
        $pdf->SetX(($pageWidth - array_sum($columnWidths)) / 2);
        $pdf->Cell($columnWidths[0], 8, 'Day', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[1], 8, 'Date', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[2], 8, 'Subjects', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[3], 8, 'Session', 1, 1, 'C', true);

        foreach ($timetableContent as $record) {
            $studentCount = count($record['student_ids']);
            $subjects = $record['subjects'];

            for ($i = 0; $i < $studentCount; $i++) {
                $subjectsText = implode(', ', $subjects);

                $pdf->SetX(($pageWidth - array_sum($columnWidths)) / 2);
                $pdf->Cell($columnWidths[0], 8, \Carbon\Carbon::parse($record['date'])->format('l'), 1, 0, 'C');
                $pdf->Cell($columnWidths[1], 8, \Carbon\Carbon::parse($record['date'])->format('d/m/Y'), 1, 0, 'C');
                $pdf->Cell($columnWidths[2], 8, $subjectsText, 1, 0, 'C');

                $dayOfWeek = \Carbon\Carbon::parse($record['date'])->format('l');
                if (in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                    switch ($record['slot']) {
                        case 1:
                            $slotText = '09:00 - 11:00';
                            break;
                        case 2:
                            $slotText = '11:20 - 01:20';
                            break;
                        case 3:
                            $slotText = '02:00 - 04:00';
                            break;
                        default:
                            $slotText = 'No Time Slot';
                            break;
                    }
                } else {
                    switch ($record['slot']) {
                        case 1:
                            $slotText = '11:00 - 01:00';
                            break;
                        case 2:
                            $slotText = '01:30 - 03:30';
                            break;
                        case 3:
                            $slotText = '04:30 - 06:30';
                            break;
                        case 4:
                            $slotText = '06:45 - 20:45';
                            break;
                        default:
                            $slotText = 'No Time Slot';
                            break;
                    }
                }
                $pdf->Cell($columnWidths[3], 8, $slotText, 1, 1, 'C');
            }
        }

        $pdfPath = storage_path('app/public/timetable_teacher_' . time() . '.pdf');
        try {
            $pdf->Output('F', $pdfPath);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to generate PDF: ' . $e->getMessage());
            return back();
        }

        $teacherName = $request->teacher_name;
        $emailContent = "
    <p>Dear {$teacherName},</p>
    <p>We hope this message finds you well.</p>
    <p>Please find attached your timetable for the upcoming term. Make sure to review it carefully and note down the important dates and timings for your classes.</p>
    <p>If you have any questions or need further assistance, feel free to reach out to admin.</p>
    <p>Wishing you a successful and productive term ahead!</p>
    <p>Best regards,<br>Frobel Administration</p>";

        try {
            Mail::send([], [], function ($message) use ($emailContent, $pdfPath, $request) {
                $message->to($request->email)
                    ->subject('Your Timetable is Here!')
                    ->html($emailContent)
                    ->attach($pdfPath, [
                        'as' => 'timetable_teacher.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to send email: ' . $e->getMessage());
            return back();
        }

        Session::flash('success', 'Email sent successfully!');
        return back();
    }


   public function generateStudentTimetable(Request $request)
    {
        $studentNameWithId = $request->input('student_name');
        $splitName = preg_split('/\s+/', $studentNameWithId, 2);
        $family_id = trim($splitName[0]);
        $studentName = isset($splitName[1]) ? trim($splitName[1]) : '';

        // Get date range from request, default to current week if not provided
        $currentDate = \Carbon\Carbon::now();
        $monday = $currentDate->copy()->startOfWeek(); // Get Monday of the current week
        $sunday = $currentDate->copy()->endOfWeek();  // Get Sunday of the current week

        // Use request dates if provided, otherwise use current week
        $fromDate = $request->input('from_date', $monday->format('Y-m-d'));
        $toDate = $request->input('to_date', $sunday->format('Y-m-d'));

        // Validate dates
        try {
            $fromDateCarbon = \Carbon\Carbon::parse($fromDate);
            $toDateCarbon = \Carbon\Carbon::parse($toDate);
        } catch (\Exception $e) {
            // If invalid dates, fall back to current week
        $fromDate = $monday->format('Y-m-d');
        $toDate = $sunday->format('Y-m-d');
        }
        // dd($fromDate,$toDate);


        // If the family_id and studentName are provided, fetch timetable records
        if ($family_id && $studentName) {
            $firstName = $studentName;  // Use full student name for display

            // Execute the query with the first name part and the family_id
            // $timetableRecords = \DB::where('branch_id', session('branch_id'))->select(
            //     'SELECT * FROM `general_timetables` WHERE JSON_UNQUOTE(JSON_EXTRACT(student_ids, "$[0]")) LIKE ? AND JSON_UNQUOTE(JSON_EXTRACT(student_names, "$[0]")) LIKE ? AND `date` BETWEEN ? AND ?',
            //     ['%' . $family_id . '%', '%' . $firstName . '%', $fromDate, $toDate]
            // );
            // Get all records in date range and filter in PHP
            // This is more reliable than SQL JSON queries which only check first element
            $timetableRecords = DB::select(
                'SELECT * FROM `general_timetables`
                WHERE `date` BETWEEN ? AND ?
                AND branch_id = ?',
                [$fromDate, $toDate, session('branch_id')]
            );
            // dd($family_id,$firstName,$timetableRecords);
            $mergedRecords = [];

            foreach ($timetableRecords as $record) {
                // Decode the outer string (double-encoded) for student_ids, student_names, and subjects
                // Handle both cases: if already array or if still JSON string (single or double encoded)
                $studentIds = $record->student_ids;
                if (is_array($studentIds)) {
                    // Already an array, use as is (no need to reassign)
                } else if (is_string($studentIds) && !empty($studentIds)) {
                    // First decode outer string
                    $decoded = json_decode($studentIds);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_string($decoded)) {
                            // Second decode the actual JSON
                            $studentIds = json_decode($decoded, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($studentIds)) {
                                $studentIds = [];
                            }
                        } else if (is_array($decoded)) {
                            // Already decoded in first step
                            $studentIds = $decoded;
                        } else {
                            $studentIds = [];
                        }
                    } else {
                        $studentIds = [];
                    }
                } else {
                    $studentIds = [];
                }

                $studentNames = $record->student_names;
                if (is_array($studentNames)) {
                    // Already an array, use as is (no need to reassign)
                } else if (is_string($studentNames) && !empty($studentNames)) {
                    // First decode outer string
                    $decoded = json_decode($studentNames);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_string($decoded)) {
                            // Second decode the actual JSON
                            $studentNames = json_decode($decoded, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($studentNames)) {
                                $studentNames = [];
                            }
                        } else if (is_array($decoded)) {
                            // Already decoded in first step
                            $studentNames = $decoded;
                        } else {
                            $studentNames = [];
                        }
                    } else {
                        $studentNames = [];
                    }
                } else {
                    $studentNames = [];
                }

                $subjects = $record->subjects;
                if (is_array($subjects)) {
                    // Already an array, use as is (no need to reassign)
                } else if (is_string($subjects) && !empty($subjects)) {
                    // First decode outer string
                    $decoded = json_decode($subjects);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_string($decoded)) {
                            // Second decode the actual JSON
                            $subjects = json_decode($decoded, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($subjects)) {
                                $subjects = [];
                            }
                        } else if (is_array($decoded)) {
                            // Already decoded in first step
                            $subjects = $decoded;
                        } else {
                            $subjects = [];
                        }
                    } else {
                        $subjects = [];
                    }
                } else {
                    $subjects = [];
                }

                // Decode permanent field - handle both string and array cases
                $permanent = $record->permanent;
                if (is_array($permanent)) {
                    // Already an array, use as is
                    $permanent = $permanent;
                } else if (is_string($permanent) && !empty($permanent)) {
                    // First decode outer string
                    $decoded = json_decode($permanent);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (is_string($decoded)) {
                            // Second decode the actual JSON
                            $permanent = json_decode($decoded, true);
                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($permanent)) {
                                $permanent = [];
                            }
                        } else if (is_array($decoded)) {
                            // Already decoded in first step
                            $permanent = $decoded;
                        } else {
                            $permanent = [];
                        }
                    } else {
                        $permanent = [];
                    }
                } else {
                    $permanent = [];
                }

                // Prepare the merged record
                $mergedRecord = [
                    'id' => $record->id,
                    'date' => $record->date,
                    'slot' => $record->slot,
                    'teacher_id' => $record->teacher_id,
                    'student_ids' => $studentIds,
                    'student_names' => $studentNames,
                    'subjects' => $subjects,
                    'permanent' => $permanent,
                ];

                // Add the merged record to the result array
                $mergedRecords[] = $mergedRecord;
            }
        } else {
            $mergedRecords = null;
        }
        if (!empty($mergedRecords)) {
            // Filter records to keep only those where the student exists, and extract only that student's data
            $mergedRecords = array_filter(array_map(function ($record) use ($family_id, $studentName) {
                $studentIds = $record['student_ids'];
                $studentNames = $record['student_names'];
                $subjects = $record['subjects'];
                $permanent = $record['permanent'] ?? [];

                // Find the index where both student_id and student_name match
                $matchingIndex = null;

                // Normalize the search name: trim, lowercase, normalize whitespace
                $searchName = strtolower(preg_replace('/\s+/', ' ', trim($studentName)));
                $searchNameParts = explode(' ', $searchName);
                $searchFirstName = $searchNameParts[0] ?? '';
                $searchLastName = end($searchNameParts) ?? '';
                $hasMultipleWords = count($searchNameParts) > 2; // Check if full name is provided (3+ words)

                foreach ($studentIds as $index => $studentId) {
                    if (!isset($studentNames[$index])) {
                        continue;
                    }

                    // Check if student_id matches
                    if ($studentId == $family_id) {
                        // Normalize the record name: trim, lowercase, normalize whitespace
                        $recordStudentName = strtolower(preg_replace('/\s+/', ' ', trim($studentNames[$index])));
                        $recordNameParts = explode(' ', $recordStudentName);
                        $recordFirstName = $recordNameParts[0] ?? '';
                        $recordLastName = end($recordNameParts) ?? '';

                        // First try exact match (this should catch most cases)
                        if ($recordStudentName === $searchName) {
                            $matchingIndex = $index;
                            break;
                        }

                        // If full name is provided (3+ words), require exact match only
                        // Don't use fuzzy matching for full names as it causes false positives
                        if ($hasMultipleWords) {
                            // Already checked exact match above, skip fuzzy matching
                            continue;
                        }

                        // Fallback: Only use fuzzy matching for incomplete names (2 words or less)
                        // This handles cases like "Fatma" vs "Fatima" (same person, spelling variation)
                        // but only when we don't have a complete full name
                        if ($recordLastName === $searchLastName && !empty($recordLastName)) {
                            // Check first name similarity (handles Fatma/Fatima variations)
                            $firstNameSimilarity = 0;
                            similar_text($recordFirstName, $searchFirstName, $firstNameSimilarity);

                            // If last name matches and first name is 80%+ similar, consider it a match
                            // But only if we don't have a full name to compare
                            if ($firstNameSimilarity >= 80) {
                                $matchingIndex = $index;
                                break;
                            }
                        }
                    }
                }

                // If no match found, return null to filter out this record
                if ($matchingIndex === null) {
                    return null;
                }

                // Check if permanent is "no" or "No" for this student at this index
                $permanentValue = isset($permanent[$matchingIndex]) ? trim(strtolower($permanent[$matchingIndex])) : '';
                if ($permanentValue === 'no') {
                    // Exclude this record if permanent is "no"
                    return null;
                }

                // Keep only the matching student's data
                return [
                    'id' => $record['id'],
                    'date' => $record['date'],
                    'slot' => $record['slot'],
                    'teacher_id' => $record['teacher_id'],
                    'student_ids' => [$studentIds[$matchingIndex]],
                    'student_names' => [$studentNames[$matchingIndex]],
                    'subjects' => isset($subjects[$matchingIndex]) ? [$subjects[$matchingIndex]] : [],
                ];
            }, $mergedRecords));

            // Reindex array after filtering
            $mergedRecords = array_values($mergedRecords);
        }

        // Use dd to check the filtered result
        // dd($mergedRecords, $family_id, $firstName);




        // dd($mergedRecords,$family_id,$firstName);
        if (!empty($mergedRecords)) {
            usort($mergedRecords, function ($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
        }

        if (!empty($mergedRecords)) {

            $stu = Student::where('branch_id', session('branch_id'))->where('admissionid', $family_id)->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(studentsur)) = ?", [$studentName])->first();
            // dd($stu);
             $yearinshcool = $stu->studentyearinschool ?? '-';

            $mail_data = Guardian::where('branch_id', session('branch_id'))->where('Guardianid', $stu->guardianid)->first();
            $mail = $mail_data->guardiantel;
        } else {
            $mail = null;
            $yearinshcool = null;
        }


        // Fetch the list of students for the selection
        $students = Student::where('branch_id', session('branch_id'))->selectRaw("CONCAT(admissionid, ' ', studentname, ' ', studentsur) AS full_name")
            ->get();
        // dd($students);
        $firstName = $firstName ?? null;
        $family_id = $family_id ?? null;

        //  dd($yearinshcool);

            // dd($mergedRecord);
        return view('branchFrontend.centralTimeTable.generateTimetableStduent', compact('family_id', 'mail', 'firstName', 'students', 'mergedRecords', 'fromDate', 'toDate','yearinshcool'));
    }

    public function sendEmailStudent(Request $request)
    {
        // Decode timetable content from JSON
        $timetableContent = json_decode($request->timetable_content, true);

        // Initialize FPDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set page width for centering
        $pageWidth = $pdf->GetPageWidth();

        // Add logo
        $logoWidth = 50 * 0.9; // Scaled logo size
        $logoHeight = 35 * 0.9;
        $logoX = ($pageWidth - $logoWidth) / 2;
        $logoPath = public_path('img/datesheetLogo.png');
        if (!file_exists($logoPath)) {
            Session::flash('error', 'Logo file not found at ' . $logoPath);
            return back();
        }
        try {
            $pdf->Image($logoPath, $logoX, 8, $logoWidth, $logoHeight);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to add logo to PDF: ' . $e->getMessage());
            return back();
        }

        // Reduced space after logo
        $pdf->Ln(18.53); // Changed from 40 to 10 to move content closer to logo
        $pdf->SetFont('Arial', 'B', 14);

        // Student Info
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $studentInfo = "Student Name: {$request->student_name} Year: {$request->year} Ref#: {$request->family_id}";
        $pdf->Cell(0, 10, $studentInfo, 0, 1, 'C');

        // Date Range Info
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        if ($fromDate && $toDate) {
            $fromDateFormatted = Carbon::parse($fromDate)->format('d M Y');
            $toDateFormatted = Carbon::parse($toDate)->format('d M Y');
            $pdf->SetFont('Arial', 'B', 9);
            $dateRangeInfo = "Report Period: {$fromDateFormatted} to {$toDateFormatted}";
            $pdf->Cell(0, 8, $dateRangeInfo, 0, 1, 'C');
        }

        // Prepare timetable data (same logic as Blade)
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weekdaySlotTimes = [1 => '11:00 - 01:00', 2 => '01:30 - 03:30', 3 => '04:30 - 06:30', 4 => '06:45 - 08:45'];
        $weekendSlotTimes = [1 => '09:00 - 11:00', 2 => '11:20 - 01:20', 3 => '02:00 - 04:00'];
        $timetable = [];
        foreach ($timetableContent as $record) {
            $day = Carbon::parse($record['date'])->format('l');
            $teacher = $record['teacher_id'] ?? '-';
            foreach ($record['subjects'] as $subject) {
                $slot = (int)$record['slot'];
                if (!isset($timetable[$day])) $timetable[$day] = [];
                if (isset($timetable[$day][$slot])) {
                    $timetable[$day][$slot] .= ', ' . $subject . ' (' . $teacher . ')';
                } else {
                    $timetable[$day][$slot] = $subject . ' (' . $teacher . ')';
                }
            }
        }
        $weekdayDays = array_slice($days, 0, 5);
        $weekendDays = array_slice($days, 5);

        // Weekday Table
        $pdf->Ln(5); // Minimal space before weekday table
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFillColor(165, 201, 227); // Light blue header
        $columnWidths = [30, 35, 35, 35, 35]; // Total: 170mm
        $pdf->SetX(($pageWidth - array_sum($columnWidths)) / 2);

        // Weekday Table Header
        $pdf->Cell($columnWidths[0], 8, 'DAY', 1, 0, 'C', true);
        foreach ($weekdaySlotTimes as $slot => $time) {
            $pdf->Cell($columnWidths[$slot], 8, $time, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Weekday Table Body
        foreach ($weekdayDays as $day) {
            $pdf->SetX(($pageWidth - array_sum($columnWidths)) / 2);
            $pdf->Cell($columnWidths[0], 8, $day, 1, 0, 'C');
            foreach ($weekdaySlotTimes as $slot => $time) {
                $cellText = isset($timetable[$day][$slot]) ? $timetable[$day][$slot] : '-';
                $pdf->Cell($columnWidths[$slot], 8, $cellText, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        // Weekend Table
        // No extra Ln() to keep tables close
        $weekendColumnWidths = [32, 46, 46, 46]; // Total: 32 + 46*3 = 170mm
        $pdf->SetX(($pageWidth - array_sum($weekendColumnWidths)) / 2);

        // Weekend Table Header
        $pdf->Cell($weekendColumnWidths[0], 8, 'DAY', 1, 0, 'C', true);
        foreach ($weekendSlotTimes as $slot => $time) {
            $pdf->Cell($weekendColumnWidths[$slot], 8, $time, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // Weekend Table Body
        foreach ($weekendDays as $day) {
            $pdf->SetX(($pageWidth - array_sum($weekendColumnWidths)) / 2);
            $pdf->Cell($weekendColumnWidths[0], 8, $day, 1, 0, 'C');
            foreach ($weekendSlotTimes as $slot => $time) {
                $cellText = isset($timetable[$day][$slot]) ? $timetable[$day][$slot] : '-';
                $pdf->Cell($weekendColumnWidths[$slot], 8, $cellText, 1, 0, 'C');
            }
            $pdf->Ln();
        }

        // Save PDF to storage
        $pdfPath = storage_path('app/public/timetable_student_' . time() . '.pdf');
        try {
            $pdf->Output('F', $pdfPath);
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to generate PDF: ' . $e->getMessage());
            return back();
        }

        // Prepare email content
        $studentName = $request->student_name;
        $emailContent = "
        <p>Dear {$studentName},</p>
        <p>We hope this message finds you well.</p>
        <p>Please find attached your timetable for the upcoming term. Make sure to review it carefully and note down the important dates and timings for your classes.</p>
        <p>If you have any questions or need further assistance, feel free to reach out to admin.</p>
        <p>Wishing you a successful and productive term ahead!</p>
        <p>Best regards,<br>Frobel Administration</p>";

        // Send email with PDF attachment
        try {
            Mail::send([], [], function ($message) use ($emailContent, $pdfPath, $request) {
                $message->to($request->email)
                    ->subject('Your Timetable is Here!')
                    ->html($emailContent)
                    ->attach($pdfPath, [
                        'as' => 'timetable_student.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Exception $e) {
            Session::flash('error', 'Failed to send email: ' . $e->getMessage());
            return back();
        }

        // Redirect back with success message
        Session::flash('success', 'Email sent successfully!');
        return back();
    }
    public function rearrangeStudent(Request $request)
    {
        // dd($request->all());
        $student = $request->get('student');
        $slot = $request->get('slot');

        if (empty($student) || empty($slot)) {
            return response()->json(['error' => 'Student and slot are required'], 400);
        }

        // Parse student input (e.g., "1001 ADAN1 KHAN")
        preg_match('/^(\d+)\s+(.*)$/', $student, $matches);
        $family_id = isset($matches[1]) ? trim($matches[1]) : null;
        $student_name = isset($matches[2]) ? trim($matches[2]) : null;

        if (!$family_id || !$student_name) {
            return response()->json(['error' => 'Invalid student format'], 400);
        }

        try {
            $today = $request->date;

            // Start a database transaction
            DB::beginTransaction();

            // Retrieve timetable for today and the given slot
            $timetables = DB::table('general_timetables')->where('branch_id', session('branch_id'))
                ->whereDate('date', $today)
                ->where('slot', $slot)
                ->get();

            if ($timetables->isEmpty()) {
                DB::rollBack();
                return response()->json(['error' => 'No timetable found for the specified date and slot'], 404);
            }

            $found = false;
            foreach ($timetables as $timetable) {
                // Decode JSON fields with robust handling
                $fixJson = function ($input) {
                    // If input is already an array, return it
                    if (is_array($input)) {
                        return $input;
                    }
                    // If input is a string, clean and decode it
                    if (is_string($input)) {
                        // Remove outer quotes and fix escaped quotes
                        $input = trim($input, '"');
                        $input = str_replace('\"', '"', $input);
                        // Try decoding as JSON
                        $decoded = json_decode($input, true);
                        if (is_array($decoded)) {
                            return $decoded;
                        }
                        // Handle double-encoded JSON (e.g., "[\"1001\"]" stored as string)
                        $doubleDecoded = json_decode(json_decode($input, true), true);
                        if (is_array($doubleDecoded)) {
                            return $doubleDecoded;
                        }
                        // Return empty array if decoding fails
                        return [];
                    }
                    return [];
                };

                $student_ids = $fixJson($timetable->student_ids);
                $student_names = $fixJson($timetable->student_names);
                $time_slot = $fixJson($timetable->time_slot);

                // Ensure decoded data is arrays
                if (!is_array($student_ids) || !is_array($student_names) || !is_array($time_slot)) {
                    continue; // Skip invalid entries
                }

                // Find the student in the arrays
                $index = array_search($family_id, $student_ids);
                if ($index !== false && isset($student_names[$index]) && $student_names[$index] === $student_name) {
                    // Remove the student from the arrays
                    unset($student_ids[$index]);
                    unset($student_names[$index]);
                    unset($time_slot[$index]);

                    // Reindex arrays to ensure clean JSON encoding
                    $student_ids = array_values($student_ids);
                    $student_names = array_values($student_names);
                    $time_slot = array_values($time_slot);

                    // Double-encode the arrays to match the desired format
                    $double_encoded_student_ids = json_encode(json_encode($student_ids));
                    $double_encoded_student_names = json_encode(json_encode($student_names));
                    $double_encoded_time_slot = json_encode(json_encode($time_slot));

                    // Update the specific timetable record with double-encoded JSON
                    DB::table('general_timetables')->where('branch_id', session('branch_id'))
                        ->where('id', $timetable->id)
                        ->update([
                            'student_ids' => $double_encoded_student_ids,
                            'student_names' => $double_encoded_student_names,
                            'time_slot' => $double_encoded_time_slot,
                            'updated_at' => now(),
                        ]);

                    $found = true;
                    break; // Exit loop after updating the relevant entry
                }
            }

            if (!$found) {
                DB::rollBack();
                return response()->json(['error' => 'Student not found in the timetable'], 404);
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['message' => 'Student removed from the timetable successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to remove student: ' . $e->getMessage()], 500);
        }
    }

    public function managePermission()
    {
        $users = User::where('branch_id', session('branch_id'))->get();
        // dd($users);
        return view('branchFrontend.permission.index', compact('users'));
    }
    public function allowPermission($id)
    {
        $user = User::where('branch_id', session('branch_id'))->where('id', $id)->first();
        $permission = AccessPermission::where('branch_id', session('branch_id'))->where('user_id', $id)->first();

        // Decode if it's a JSON string
        if ($permission && isset($permission->page_name)) {
            $permission->page_name = json_decode($permission->page_name, true);
        }

        // dd($permission);  // Check the structure of the permission object

        return view('branchFrontend.permission.managePermission', compact('user', 'permission'));
    }

    public function storePermission(Request $request)
    {
        $filteredData = $request->except(['user_id', '_token']);
        $jsonPermissions = json_encode($filteredData);
        $branchId = session('branch_id');

        // Get branch info for activity log
        $branch = User::where('branch_id', $branchId)->where('is_main_branch', 1)->first();

        // Check if permission already exists
        $existingPermission = AccessPermission::where('user_id', $request->input('user_id'))
            ->where('branch_id', $branchId)
            ->first();

        if ($existingPermission) {
            // Update existing permission - use save() to trigger activity log
            $existingPermission->page_name = $jsonPermissions;
            $existingPermission->can_access = true;
            $existingPermission->branch_name = $branch ? $branch->branch_name : null;
            $existingPermission->save();
        } else {
            // Create new permission
            AccessPermission::create([
                'user_id' => $request->input('user_id'),
                'branch_id' => $branchId,
                'page_name' => $jsonPermissions,
                'can_access' => true,
                'branch_name' => $branch ? $branch->branch_name : null,
            ]);
        }

        return redirect()->route('allow.permission', ['id' => $request->input('user_id')])
            ->with('success', 'Permissions stored successfully.');
    }

    public function deleteDoc(Request $request)
    {
        // Retrieve all the request data
        $filePath = $request->file_path;  // Path of the file to delete
        $fileType = $request->file_type;  // File input column type (e.g., 'file_input_1', 'file_input_2', 'file_input_3')
        $learnerId = $request->id;        // Learner's ID

        // Find the learner record
        $learner = IagMeeting::find($learnerId);

        if (!$learner) {
            return response()->json(['success' => false, 'message' => 'Learner not found']);
        }

        // Check if the learner has the specified file type
        if (isset($learner->$fileType)) {
            // Split the file paths into an array
            $existingFiles = explode(',', $learner->$fileType);

            // Remove the specified file path from the array
            $updatedFiles = array_filter($existingFiles, function ($file) use ($filePath) {
                return $file !== $filePath;
            });

            // Update the database with the remaining files
            $learner->$fileType = implode(',', $updatedFiles);
            $learner->save();

            // Delete the file from storage
            if (file_exists(public_path($filePath))) {
                unlink(public_path($filePath));
            }

            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'No files found for deletion']);
        }
    }


    public function storeTermbreak(Request $request)
    {
        // TermBreak::dd($request->all(), session('branch_id'));

        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;


        $branchId = session('branch_id');

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $termBreak = TermBreak::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'start_term_break' => $validated['start_date'],
                'end_term_break' => $validated['end_date'],
                'branch_name' => $branch_name,
                'branch_id' => $branch_id,
            ]
        );
        return response()->json(['success' => true, 'message' => 'Term break dates saved successfully!']);
    }
    public function getTermBreak(Request $request)
    {
        $date = $request->query('date');
        $branch_id = session('branch_id');

        $termBreak = TermBreak::where('branch_id', $branch_id)
            // ->where('start_term_break', '<=', $date)
            // ->where('end_term_break', '>=', $date)
            ->first();
        // dd($termBreak,$branch_id);
        if ($termBreak) {
            return response()->json([
                'success' => true,
                'is_term_break' => true,
                'start_date' => $termBreak->start_term_break,
                'end_date' => $termBreak->end_term_break
            ]);
        }

        return response()->json([
            'success' => true,
            'is_term_break' => false
        ]);
    }

    public function subjecBookAssigner()
    {
        $assignments = SubjectBookAssignment::where('branch_id', session('branch_id'))->get();
        $subjects = Subject::where('branch_id', session('branch_id'))->get();
        return view('branchFrontend.assignBook.assignerBook', compact('subjects', 'assignments'));
    }




    public function storeAssigner(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'book_name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $subject = Subject::findOrFail($request->subject_id);

        SubjectBookAssignment::create([
            'book_name' => $request->book_name,
            'subject_id' => $request->subject_id,
            'subject_name' => $subject->name,
            'price' => $request->price ?? null,
            'branch_id' => session('branch_id'),
            'branch_name' => $subject->branch_name,
        ]);

        return redirect()->route('subjecBookAssigner')->with('success', 'Assignment created successfully.');
    }

    public function updateAssigner(Request $request, $id)
    {
        $request->validate([
            'book_name' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $assignment = SubjectBookAssignment::where('branch_id', session('branch_id'))->findOrFail($id);
        $subject = Subject::where('branch_id', session('branch_id'))->findOrFail($request->subject_id);

        $assignment->update([
            'book_name' => $request->book_name,
            'subject_id' => $request->subject_id,
            'subject_name' => $subject->name,
            'price' => $request->price ?? null,
            'branch_name' => $subject->branch_name,
            'branch_id' => session('branch_id'),
        ]);

        return redirect()->route('subjecBookAssigner')->with('success', 'Assignment updated successfully.');
    }

    public function destroyAssigner($id)
    {
        $assignment = SubjectBookAssignment::where('branch_id', session('branch_id'))->findOrFail($id);
        $assignment->delete();

        return response()->json(['success' => true, 'message' => 'Assignment deleted successfully.']);
    }

    public function getBooksBySubject(Request $request)
    {
        $subject = $request->input('subject');
        $branch_id = $request->input('branch_id');

        $books = \App\Models\SubjectBookAssignment::where('branch_id', session('branch_id'))->where('subject_name', $subject)
            ->where('branch_id', $branch_id)
            ->select('book_name', 'price')
            ->get();

        return response()->json(['books' => $books]);
    }

    public function getLearnerDetails($id)
    {
        $learner = IAGLearnerRequest::where('branch_id', session('branch_id'))->findOrFail($id);
        $branches = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->get();
        return view('branchFrontend.IAG.approveLaernerReq', compact('learner', 'branches'));
    }

    public function approveLearnerRequest(Request $request, $id)
    {
        $learner = IAGLearnerRequest::where('branch_id', session('branch_id'))->findOrFail($id);

        // Replicate data to iag_meetings
        $meeting = IAGMeeting::create([
            'family_id' => $learner->family_id,
            'learner_name' => $learner->learner_name,
            'learner_name_template' => $learner->learner_name_template,
            'staff_lead_name' => $learner->staff_lead_name,
            'category' => $learner->category,
            'courses_subjects_being_studied' => $learner->courses_subjects_being_studied,
            'meeting_date' => $learner->meeting_date,
            'career_next_steps' => $learner->career_next_steps,
            'interested_fields' => $learner->interested_fields,
            'researched_application_process_deadlines' => $learner->researched_application_process_deadlines,
            'clear_go_information' => $learner->clear_go_information,
            'is_helpful_information' => $learner->is_helpful_information,
            'started_application' => $learner->started_application,
            'need_help_in_application' => $learner->need_help_in_application,
            'visited_our_resources_on_line' => $learner->visited_our_resources_on_line,
            'resources_in_career_library' => $learner->resources_in_career_library,
            'IAG_lerner_plan_1' => $learner->IAG_lerner_plan_1,
            'IAG_lerner_plan_2' => $learner->IAG_lerner_plan_2,
            'IAG_lerner_plan_3' => $learner->IAG_lerner_plan_3,
            'learner_on_secure_pathway' => $learner->learner_on_secure_pathway,
            'file_input_1' => $learner->file_input_1,
            'file_input_2' => $learner->file_input_2,
            'file_input_3' => $learner->file_input_3,
            'branch_name' => $learner->branch_name,
            'branch_id' => $learner->branch_id,
        ]);

        // Update is_approved status
        $learner->update(['is_approved' => 1]);

        return redirect()->route('learnerRequest')->with('success', 'Learner Request Approved Successfully');
    }



    public function getBaselineReport()
    {
        $guardianIds = Student::distinct()->where('branch_id', session('branch_id'))->pluck('admissionid')->filter()->sort()->values();

        return view('branchFrontend.report.getBaselineReport', compact('guardianIds'));
    }


    public function getProgressTrackingReport()
    {
        // $guardianIds = DB::table('studentdata')->where('branch_id', session('branch_id'))->distinct()->pluck('admissionid');
        $guardianIds = DB::table('studentdata')->where('branch_id', session('branch_id'))->distinct()->pluck('admissionid');
        return view('branchFrontend.report.getProgressTrackingReport', compact('guardianIds'));
    }

    public function getStudentsByGuardian(Request $request)
    {
        $guardianId = $request->input('guardian_id');

        $students = Student::where('branch_id', session('branch_id'))->where('admissionid', $guardianId)
            ->selectRaw('studentid, CONCAT(studentname, " ", COALESCE(studentsur, "")) as full_name')
            ->get();

        return response()->json($students);
    }


    public function getBaselineReportResult(Request $request)
    {
        try {
            $familyId = $request->input('family_id');
            $studentName = $request->input('student_name');

            if (!$familyId || !$studentName) {
                return response('<p class="text-danger">Invalid family ID or student name.</p>', 400);
            }

            // Fetch the baseline report data with guardian email
            $report = Student::selectRaw("
            CONCAT(studentname, ' ', COALESCE(studentsur, '')) AS full_name,
            admissionid AS familyid,
            target_grades,
            subject_names,
            studentyearinschool,

            studenthours,
            g.guardiantel AS email
        ")
                ->leftJoin('guardian as g', function ($join) {
                    $join->on('studentdata.guardianid', '=', 'g.Guardianid')
                        ->where('g.branch_id', session('branch_id')); // Apply branch_id filter to guardian table
                })
                ->where('studentdata.branch_id', session('branch_id')) // Apply branch_id filter to students table
                ->where('admissionid', $familyId)
                ->whereRaw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) = ?", [$studentName])
                ->get();

            if ($report->isEmpty()) {
                return response('<p class="text-info">No report found for the selected family ID and student name.</p>', 404);
            }

            // Decode the JSON strings in the collection
            $report->transform(function ($item) {
                $item->target_grades = json_decode($item->target_grades, true);
                $item->subject_names = json_decode($item->subject_names, true);
                $item->studenthours = json_decode($item->studenthours, true);
                return $item;
            });

            // Log the query for debugging
            \Log::info('Baseline report query executed', [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'email' => $report->first()->email ?? null,
                'studentyearinschool' => $report->first()->studentyearinschool ?? null,
                'branch_id' => session('branch_id')
            ]);

            return response()->json($report);
        } catch (\Exception $e) {
            \Log::error('Error fetching baseline report: ' . $e->getMessage());
            return response('<p class="text-danger">Error fetching report data: ' . $e->getMessage() . '</p>', 500);
        }
    }
    // public function getBaselineReportResult(Request $request)
    // {
    //     try {
    //         $familyId = $request->input('family_id');
    //         $studentName = $request->input('student_name');

    //         if (!$familyId || !$studentName) {
    //             return response('<p class="text-danger">Invalid family ID or student name.</p>', 400);
    //         }

    //         // Fetch the baseline report data with guardian email
    //         $report = Student::selectRaw("
    //         CONCAT(studentname, ' ', COALESCE(studentsur, '')) AS full_name,
    //         admissionid AS familyid,
    //         target_grades,
    //         subject_names,
    //         studenthours,
    //         g.guardiantel AS email
    //     ")
    //             ->leftJoin('guardian as g', 'studentdata.guardianid', '=', 'g.Guardianid')
    //             ->where('admissionid', $familyId)
    //             ->whereRaw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) = ?", [$studentName])
    //             ->get();

    //         if ($report->isEmpty()) {
    //             return response('<p class="text-info">No report found for the selected family ID and student name.</p>', 404);
    //         }

    //         // Decode the JSON strings in the collection
    //         $report->transform(function ($item) {
    //             $item->target_grades = json_decode($item->target_grades, true);
    //             $item->subject_names = json_decode($item->subject_names, true);
    //             $item->studenthours = json_decode($item->studenthours, true);
    //             return $item;
    //         });

    //         // Log the query for debugging
    //         \Log::info('Baseline report query executed', [
    //             'family_id' => $familyId,
    //             'student_name' => $studentName,
    //             'email' => $report->first()->email ?? null
    //         ]);

    //         return response()->json($report);
    //     } catch (\Exception $e) {
    //         \Log::error('Error fetching baseline report: ' . $e->getMessage());
    //         return response('<p class="text-danger">Error fetching report data: ' . $e->getMessage() . '</p>', 500);
    //     }
    // }

    public function sendBaselineReportEmail(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email',
                'report_html' => 'required|string',
            ]);

            $email = $request->input('email');
            $reportHtml = $request->input('report_html');

            // Define the Blade template content within the controller
            $emailTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baseline Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        .logo-container {
            background-color: #2048AC;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            margin: 0 auto 15px auto;
            width: fit-content;
        }
        .logo {
            max-width: 120px;
            width: 100%;
            height: auto;
            display: block;
        }
        h1 {
            color: #2048AC;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 15px;
        }
        table.report-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-info-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.95rem;
        }
        .report-info-table td:first-child {
            font-weight: bold;
            background-color: #AED6F1;
            color: #2048AC;
            width: 30%;
        }
        .report-info-table td:nth-child(2) {
            background-color: #fff;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 0.9rem;
        }
        .report-table th {
            background-color: #2048AC;
            color: white;
            text-transform: uppercase;
            font-weight: 600;
        }
        .report-table td {
            background-color: #fff;
        }
        .report-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #555;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        ' . $reportHtml . '
        <div class="footer">
            <p>Thank you for choosing Frobel Education</p>
            <p>Contact us at <a href="mailto:no-reply@frobel.co.uk">no-reply@frobel.co.uk</a></p>
        </div>
    </div>
</body>
</html>';

            // Send email using Laravel Mail
            Mail::html($emailTemplate, function ($message) use ($email) {
                $message->to($email)
                    ->subject('Baseline Report - Frobel Education');
                // ->from('no-reply@frobeleducation.com', 'Frobel Education');
            });

            return response()->json([
                'message' => 'Email sent successfully'
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send baseline report email: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function getProgressTrackingReportData(Request $request)
    // {
    //     // dd($request->all());
    //     $familyId = $request->query('family_id');
    //     $studentName = $request->query('student_name');
    //     $dateFrom = $request->query('date_from');
    //     $dateTo = $request->query('date_to');

    //     // Define scoring for behavior and performance
    //     $behaviorScores = [
    //         'Good' => 100,
    //         'Satisfactory' => 50,
    //         'Poor' => 0
    //     ];
    //     $performanceScores = [
    //         'Exceeding Target' => 100,
    //         'On Target' => 50,
    //         'Below Target' => 0
    //     ];

    //     // Query to aggregate attendance and test data by date
    //     $query = DB::table('attendance as att')
    //         ->select(
    //             'att.date',
    //             DB::raw('GROUP_CONCAT(DISTINCT att.subject ORDER BY att.subject) as subjects'),
    //             DB::raw('GROUP_CONCAT(DISTINCT st.percentage) as test_percentage'),
    //             DB::raw('GROUP_CONCAT(DISTINCT att.status) as attendance_status'),
    //             DB::raw('AVG(CASE
    //             WHEN att.behaviour = "Good" THEN 100
    //             WHEN att.behaviour = "Satisfactory" THEN 50
    //             WHEN att.behaviour = "Poor" THEN 0
    //             ELSE 0 END) as behaviour_score'),
    //             DB::raw('AVG(CASE
    //             WHEN att.performance = "Exceeding Target" THEN 100
    //             WHEN att.performance = "On Target" THEN 50
    //             WHEN att.performance = "Below Target" THEN 0
    //             ELSE 0 END) as performance_score'),
    //             'att.family_id',
    //             DB::raw("'$studentName' as student_name"),
    //             'g.guardiantel as email'
    //         )
    //         ->leftJoin('student_tests as st', function ($join) use ($studentName, $dateFrom, $dateTo) {
    //             $join->on('att.family_id', '=', 'st.family_id')
    //                 ->on('att.date', '=', 'st.test_date')
    //                 ->where('st.student_name', '=', $studentName)
    //                 ->whereBetween('st.test_date', [$dateFrom, $dateTo])
    //                 ->where('st.branch_id', session('branch_id')); // Apply branch_id filter to student_tests
    //         })
    //         ->leftJoin('studentdata as sd', function ($join) {
    //             $join->on('att.family_id', '=', 'sd.admissionid')
    //                 ->where('sd.branch_id', session('branch_id')); // Apply branch_id filter to studentdata
    //         })
    //         ->leftJoin('guardian as g', function ($join) {
    //             $join->on('sd.guardianid', '=', 'g.Guardianid')
    //                 ->where('g.branch_id', session('branch_id')); // Apply branch_id filter to guardian
    //         })
    //         ->where('att.family_id', $familyId)
    //         ->where('att.student_name', $studentName)
    //         ->whereBetween('att.date', [$dateFrom, $dateTo])
    //         ->where('att.branch_id', session('branch_id')) // Apply branch_id filter to attendance
    //         ->groupBy('att.date', 'att.family_id', 'g.guardiantel');

    //     // Log the query for debugging
    //     \Log::info($query->toSql(), array_merge($query->getBindings(), ['branch_id' => session('branch_id')]));
    //     $results = $query->orderBy('att.date', 'asc')->get();

    //     // Map results to include readable behavior and performance statuses
    //     $response = [
    //         'family_id' => $familyId,
    //         'student_name' => $studentName,
    //         'email' => $results->isNotEmpty() ? $results->first()->email : null,
    //         'data' => $results->map(function ($row) use ($behaviorScores, $performanceScores) {
    //             // Determine behavior status based on average score
    //             $avgBehaviour = $row->behaviour_score;
    //             $behaviour = $avgBehaviour >= 75 ? 'Good' : ($avgBehaviour >= 25 ? 'Satisfactory' : 'Poor');

    //             // Determine performance status based on average score
    //             $avgPerformance = $row->performance_score;
    //             $performance = $avgPerformance >= 75 ? 'Exceeding Target' : ($avgPerformance >= 25 ? 'On Target' : 'Below Target');

    //             // Calculate daily progress score
    //             $testScore = $row->test_percentage ? floatval(str_replace('%', '', $row->test_percentage)) : 0;
    //             $attendanceScore = $row->attendance_status === 'Yes' ? 100 : 0;
    //             $dailyProgress = (
    //                 ($testScore * 0.4) +
    //                 ($attendanceScore * 0.2) +
    //                 ($avgBehaviour * 0.2) +
    //                 ($avgPerformance * 0.2)
    //             );

    //             return [
    //                 'date' => $row->date,
    //                 'subjects' => $row->subjects,
    //                 'test_percentage' => $row->test_percentage,
    //                 'attendance_status' => $row->attendance_status,
    //                 'behaviour' => $behaviour,
    //                 'performance' => $performance,
    //                 'daily_progress' => round($dailyProgress, 2)
    //             ];
    //         })->toArray()
    //     ];

    //     return response()->json($response);
    // }


    // public function getProgressTrackingReportData(Request $request)
    // {
    //     $familyId = $request->query('family_id');
    //     $studentName = $request->query('student_name');
    //     $subject = $request->query('subject');
    //     $dateFrom = $request->query('date_from');
    //     $dateTo = $request->query('date_to');

    //     // Define scoring for behavior and performance
    //     $behaviorScores = [
    //         'Good' => 100,
    //         'Satisfactory' => 50,
    //         'Poor' => 0
    //     ];
    //     $performanceScores = [
    //         'Exceeding Target' => 100,
    //         'On Target' => 50,
    //         'Below Target' => 0
    //     ];

    //     // Get student data
    //     $studentData = DB::table('studentdata')
    //         ->where('admissionid', $familyId)
    //         ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), $studentName)
    //         ->where('branch_id', session('branch_id'))
    //         ->select('studentyearinschool', 'subject_names', 'target_grades', 'tier')
    //         ->first();

    //     if (!$studentData) {
    //         return response()->json(['data' => null], 404);
    //     }

    //     $subjects = json_decode($studentData->subject_names, true) ?? [];
    //     $targetGrades = json_decode($studentData->target_grades, true) ?? [];





    //     $targetGrade = '';
    //     foreach ($subjects as $index => $sub) {
    //         if (strtoupper($sub) === strtoupper($subject)) {
    //             $targetGrade = $targetGrades[$index] ?? 'N/A';
    //             break;
    //         }
    //     }

    //     // Get start date (first attendance date on or after session start)
    //     $sessionStartDate = date('Y') . '-08-18';

    //     $startDate = DB::table('attendance')
    //         ->where('family_id', $familyId)
    //         ->where('student_name', $studentName)
    //         ->where('subject', $subject)
    //         ->where('date', '>=', $sessionStartDate)
    //         ->where('branch_id', session('branch_id'))
    //         ->min('date');

    //     // Count sessions booked and attended



    //     $student = DB::table('studentdata')
    //         ->where('admissionid', $familyId)
    //         ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), $studentName)
    //         ->where('branch_id', session('branch_id'))
    //         ->select('studenthours', 'subject_names')
    //         ->first();



    //     $sessionsBooked = $query = DB::table('general_timetables')
    //         ->where('student_ids', 'LIKE', "%{$familyId}%")
    //         ->where('student_names', 'LIKE', "%{$studentName}%")
    //         ->where('subjects', 'LIKE', "%{$subject}%");

    //     // Add date range filter if provided
    //     if ($dateFrom && $dateTo) {
    //         $query->whereBetween('date', [$dateFrom, $dateTo]);
    //     } elseif ($dateFrom) {
    //         $query->where('date', '>=', $dateFrom);
    //     } elseif ($dateTo) {
    //         $query->where('date', '<=', $dateTo);
    //     }

    //     // Count the matching rows
    //     $sessionsBooked = $query->count();
    //     //    DB::table('general_timetables')
    //     // ->whereBetween('date', [$dateFrom, $dateTo])
    //     // ->where('branch_id', session('branch_id'))
    //     // ->whereRaw('JSON_CONTAINS(student_ids, ?)', [json_encode($familyId)])
    //     // ->count();
    //     // dd($sessionsBooked);

    //     $studentHour = null;

    //     if ($student) {
    //         $subjects = json_decode($student->subject_names, true);
    //         $hours    = json_decode($student->studenthours, true);

    //         // if (is_array($subjects) && is_array($hours)) {
    //         //     $index = array_search($subject, $subjects);
    //         //     if ($index !== false && isset($hours[$index])) {
    //         //         $sessionsBooked = $hours[$index]; // 🎯 sirf relevant subject ka hour
    //         //     }
    //         // }
    //     }

    //     $tiers = json_decode($studentData->tier, true) ?? [];
    //     // dd($tiers);

    //     $tier = 'N/A';
    //     if (is_array($subjects) && is_array($tiers)) {
    //         $index = array_search(strtoupper($subject), array_map('strtoupper', $subjects));
    //         if ($index !== false && isset($tiers[$index])) {
    //             $tier = $tiers[$index]; // 🎯 subject ke corresponding relevant tier
    //         }
    //     }
    //     // dd($tier);

    //     // DB::table('attendance')
    //     //     ->where('family_id', $familyId)
    //     //     ->where('student_name', $studentName)
    //     //     ->where('subject', $subject)
    //     //     ->whereBetween('date', [$dateFrom, $dateTo])
    //     //     ->where('branch_id', session('branch_id'))
    //     //     ->count();

    //     $sessionsAttended = DB::table('attendance')
    //         ->where('family_id', $familyId)
    //         ->where('student_name', $studentName)
    //         ->where('subject', $subject)
    //         ->whereBetween('date', [$dateFrom, $dateTo])
    //         // ->where('status', 'Yes')
    //         ->where('branch_id', session('branch_id'))
    //         ->count();
    //     // dd($sessionsAttended);

    //     // DB::table('attendance')
    //     //     ->where('family_id', $familyId)
    //     //     ->where('student_name', $studentName)
    //     //     ->where('subject', $subject)
    //     //     ->where('status', 'Yes')
    //     //     ->whereBetween('date', [$dateFrom, $dateTo])
    //     //     ->where('branch_id', session('branch_id'))
    //     //     ->count();

    //     // Calculate current grade (example logic, adjust as per actual grading system)
    //     // $currentGrade = 'N/A'; // Placeholder, replace with actual logic from attendance/timetable
    //     // $sessionsAttended = DB::table('attendance')
    //     //     ->where('family_id', $familyId)
    //     //     ->where('student_name', $studentName)
    //     //     ->where('subject', $subject)
    //     //     ->whereBetween('date', [$dateFrom, $dateTo])
    //     //     ->where('status', 'Yes')
    //     //     ->where('branch_id', session('branch_id'))
    //     //     ->count();

    //     // Calculate current grade from attendance percentage

    //     $currentGrade = 'N/A';
    //     // if ($sessionsBooked > 0) {
    //     //     $attendancePercentage = ($sessionsAttended / $sessionsBooked) * 100;

    //     //     if ($attendancePercentage >= 70) {
    //     //         $currentGrade = 'A';
    //     //     } elseif ($attendancePercentage >= 60) {
    //     //         $currentGrade = 'B';
    //     //     } elseif ($attendancePercentage >= 50) {
    //     //         $currentGrade = 'C';
    //     //     } elseif ($attendancePercentage >= 40) {
    //     //         $currentGrade = 'D';
    //     //     } else {
    //     //         $currentGrade = 'F';
    //     //     }
    //     // }



    //     // Calculate 30-day average behavior and performance
    //     $thirtyDaysAgo = date('Y-m-d', strtotime($dateTo . ' -30 days'));
    //     // Behaviour Average Calculation
    //     $behaviorData = DB::table('attendance')
    //         ->selectRaw('
    //     SUM(CASE WHEN behaviour = "Good" THEN 3
    //              WHEN behaviour = "Satisfactory" THEN 2
    //              WHEN behaviour = "Poor" THEN 1
    //              ELSE 0 END) as total_score,
    //     COUNT(CASE WHEN behaviour IN ("Good","Satisfactory","Poor") THEN 1 END) as total_count
    // ')
    //         ->where('family_id', $familyId)
    //         ->where('student_name', $studentName)
    //         ->where('subject', $subject)
    //         ->whereBetween('date', [$dateFrom, $dateTo])
    //         ->where('branch_id', session('branch_id'))
    //         ->first();

    //     $behaviorAvg = $behaviorData->total_count > 0
    //         ? $behaviorData->total_score / $behaviorData->total_count
    //         : 0;

    //     $behaviorPercentage = $behaviorData->total_count > 0
    //         ? ($behaviorData->total_score / ($behaviorData->total_count * 3)) * 100
    //         : 0;


    //     // Performance Average Calculation
    //     $performanceData = DB::table('attendance')
    //         ->selectRaw('
    //     SUM(CASE WHEN performance = "Exceeding Target" THEN 3
    //              WHEN performance = "On Target" THEN 2
    //              WHEN performance = "Below Target" THEN 1
    //              ELSE 0 END) as total_score,
    //     COUNT(CASE WHEN performance IN ("Exceeding Target","On Target","Below Target") THEN 1 END) as total_count
    // ')
    //         ->where('family_id', $familyId)
    //         ->where('student_name', $studentName)
    //         ->where('subject', $subject)
    //         ->whereBetween('date', [$dateFrom, $dateTo])
    //         ->where('branch_id', session('branch_id'))
    //         ->first();

    //     $performanceAvg = $performanceData->total_count > 0
    //         ? $performanceData->total_score / $performanceData->total_count
    //         : 0;

    //     $performancePercentage = $performanceData->total_count > 0
    //         ? ($performanceData->total_score / ($performanceData->total_count * 3)) * 100
    //         : 0;


    //     // Final Labels
    //     $behavior = $behaviorPercentage >= 75
    //         ? 'Good'
    //         : ($behaviorPercentage >= 25 ? 'Satisfactory' : 'Poor');

    //     $performance = $performancePercentage >= 75
    //         ? 'Exceeding Target'
    //         : ($performancePercentage >= 25 ? 'On Target' : 'Below Target');


    //     // Get test scores
    //     $testScores = DB::table('student_tests as st')
    //         ->select('st.test_date as date', DB::raw('GROUP_CONCAT(st.percentage) as test_percentage'))
    //         ->where('st.family_id', $familyId)
    //         ->where('st.student_name', $studentName)
    //         ->where('st.subject', $subject)
    //         ->whereBetween('st.test_date', [$dateFrom, $dateTo])
    //         ->where('st.branch_id', session('branch_id'))
    //         ->groupBy('st.test_date')
    //         ->orderBy('st.test_date', 'asc')
    //         ->get()
    //         ->map(function ($row) use ($behaviorScores, $performanceScores) {
    //             $testPercentage = $row->test_percentage ? explode(',', $row->test_percentage) : [];
    //             $testPercentage = array_map('floatval', $testPercentage);
    //             return [
    //                 'date' => \Carbon\Carbon::parse($row->date)->format('d/m/Y'),
    //                 'test_percentage' => implode(', ', $testPercentage),
    //                 'daily_progress' => array_sum($testPercentage) / max(1, count($testPercentage)) // Average test score as daily progress
    //             ];
    //         })->toArray();

    //     // Get guardian email
    //     $email = DB::table('studentdata as sd')
    //         ->leftJoin('guardian as g', 'sd.guardianid', '=', 'g.Guardianid')
    //         ->where('sd.admissionid', $familyId)
    //         ->where('sd.branch_id', session('branch_id'))
    //         ->pluck('g.guardiantel')
    //         ->first();

    //     $response = [
    //         'family_id' => $familyId,
    //         'student_name' => $studentName,
    //         'school_year' => $studentData->studentyearinschool ?? 'N/A',
    //         'start_date' => $startDate ? date('d/m/Y', strtotime($startDate)) : 'N/A',

    //         'subject' => $subject,
    //         'tier' => $tier, // Static as per request
    //         'sessions_booked' => $sessionsBooked,
    //         'sessions_attended' => $sessionsAttended,
    //         'target_grade' => $targetGrade,
    //         'current_grade' => $currentGrade,
    //         'behavior' => $behavior,
    //         'performance' => $performance,
    //         'email' => $email,
    //         'data' => [
    //             'test_scores' => $testScores
    //         ]
    //     ];

    //     return response()->json($response);
    // }
    public function getProgressTrackingReportData(Request $request)
    {
        $familyId = $request->query('family_id');
        $studentName = $request->query('student_name');
        $subject = $request->query('subject');
        $month = $request->query('month'); // Format: YYYY-MM

        if (!$familyId || !$studentName || !$subject || !$month) {
            return response()->json(['data' => null, 'error' => 'Missing required parameters'], 400);
        }

        // Validate month format
        try {
            $requestedMonth = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'error' => 'Invalid month format. Expected format: YYYY-MM'], 400);
        }

        // Validate that the selected month is not the current month
        $currentMonth = Carbon::now()->format('Y-m');
        if ($month === $currentMonth) {
            return response()->json(['data' => null, 'error' => 'Current month cannot be selected. Please select a previous month.'], 400);
        }

        // Validate that the selected month is not in the future
        if ($requestedMonth->isFuture()) {
            return response()->json(['data' => null, 'error' => 'Future months cannot be selected.'], 400);
        }

        // Parse month to get date range
        $monthStart = $requestedMonth->copy()->startOfMonth();
        $monthEnd = $requestedMonth->copy()->endOfMonth();
        $dateFrom = $monthStart->format('Y-m-d');
        $dateTo = $monthEnd->format('Y-m-d');

        // Get student data
        $studentData = DB::table('studentdata')
            ->where('admissionid', $familyId)
            ->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), $studentName)
            ->where('branch_id', session('branch_id'))
            ->select('studentid', 'studentyearinschool', 'subject_names', 'target_grades', 'tier', 'studenthours', 'current_grades', 'qualifications', 'admissionid', 'studentname', 'studentsur')
            ->first();

        if (!$studentData) {
            return response()->json(['data' => null], 404);
        }

        $subjects = json_decode($studentData->subject_names, true) ?? [];
        $targetGrades = json_decode($studentData->target_grades, true) ?? [];
        $tiers = json_decode($studentData->tier, true) ?? [];
        $studentHours = json_decode($studentData->studenthours, true) ?? [];
        $currentGrades = json_decode($studentData->current_grades, true) ?? [];
        
        // Parse qualifications
        $qualifications = [];
        if ($studentData->qualifications) {
            if (is_string($studentData->qualifications)) {
                $qualifications = json_decode($studentData->qualifications, true) ?? [];
            } else {
                $qualifications = $studentData->qualifications ?? [];
            }
        }
        
        // Convert month format from YYYY-MM to MonthName_YYYY (e.g., "2025-11" to "November_2025")
        $monthParts = explode('-', $month);
        $year = $monthParts[0] ?? date('Y');
        $monthNumber = $monthParts[1] ?? date('n');
        $monthTimestamp = strtotime($year . '-' . $monthNumber . '-01');
        $monthName = date('F', $monthTimestamp); // Full month name (e.g., "November")
        $monthFormatted = $monthName . '_' . $year; // Format: "November_2025"
        
        // Get start and end date of the month for attendance queries
        $startDateForAttendance = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth()->format('Y-m-d');
        $endDateForAttendance = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth()->format('Y-m-d');
        
        // Build student full name for attendance matching
        $studentFullName = trim(($studentData->studentname ?? '') . ' ' . ($studentData->studentsur ?? ''));

        // Determine which subjects to process
        $subjectsToProcess = [];
        if (strtolower($subject) === 'all') {
            $subjectsToProcess = $subjects;
        } else {
            $subjectsToProcess = [$subject];
        }

        // Get guardian email
        $email = DB::table('studentdata as sd')
            ->leftJoin('guardian as g', 'sd.guardianid', '=', 'g.Guardianid')
            ->where('sd.admissionid', $familyId)
            ->where('sd.branch_id', session('branch_id'))
            ->pluck('g.guardiantel')
            ->first();

        // Build Subject Overview Table
        $subjectOverview = [];
        $performanceSections = [];

        foreach ($subjectsToProcess as $subj) {
            $subjectIndex = array_search(strtoupper($subj), array_map('strtoupper', $subjects));
            if ($subjectIndex === false) {
                continue;
            }

            $targetGrade = isset($targetGrades[$subjectIndex]) ? $targetGrades[$subjectIndex] : 'N/A';
            $tier = isset($tiers[$subjectIndex]) ? $tiers[$subjectIndex] : 'N/A';

            // Get sessions per week from studentHours (same as old code)
            $sessionsBooked = isset($studentHours[$subjectIndex]) ? $studentHours[$subjectIndex] : 0;

        // Get start date (first attendance date on or after session start)
        $sessionStartDate = date('Y') . '-08-18';
        $startDate = DB::table('attendance')
            ->where('family_id', $familyId)
            ->where('student_name', $studentName)
                ->where('subject', $subj)
            ->where('date', '>=', $sessionStartDate)
            ->where('branch_id', session('branch_id'))
            ->min('date');

            // Calculate total weeks in the month (same formula as old code)
            $daysDiff = $monthStart->diffInDays($monthEnd) + 1;
            $totalWeeks = intdiv($daysDiff, 7);

            // Calculate sessions booked: sessions per week * total weeks (same as old code)
            $sessionsBooked = $sessionsBooked * $totalWeeks;

            // Count sessions attended in the month (same as old code - count all attendance records)
        $sessionsAttended = DB::table('attendance')
            ->where('family_id', $familyId)
            ->where('student_name', $studentName)
                ->where('subject', $subj)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('branch_id', session('branch_id'))
            ->count();

            // Calculate attendance percentage: (Sessions Attended ÷ Sessions Booked) × 100
            $attendancePercentage = $sessionsBooked > 0
                ? round(($sessionsAttended / $sessionsBooked) * 100, 0)
                : 0;

            // Categorize attendance based on percentage
            if ($attendancePercentage < 25) {
                $attendanceCategory = 'Poor';
            } elseif ($attendancePercentage < 50) {
                $attendanceCategory = 'Satisfactory';
            } elseif ($attendancePercentage < 75) {
                $attendanceCategory = 'Good';
            } else {
                $attendanceCategory = 'Excellent';
            }

            // Format attendance as "Category" only (percentage and count commented out)
            // $attendance = $attendanceCategory . ' (' . $attendancePercentage . '%) - ' . $sessionsAttended;
            $attendance = $attendanceCategory;

            // Calculate homework completion
            // status = 'Yes' means homework done, status = 'No' means no homework
            $homeworkTotal = DB::table('attendance')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subj)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->whereIn('status', ['Yes', 'No']) // Only count records with Yes or No status
                ->count();

            $homeworkCompleted = DB::table('attendance')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subj)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('status', 'Yes') // Yes = homework done
                ->where('branch_id', session('branch_id'))
                ->count();

            // Calculate homework percentage: (Homework Completed ÷ Homework Total) × 100
            $homeworkPercentage = $homeworkTotal > 0
                ? round(($homeworkCompleted / $homeworkTotal) * 100, 0)
                : 0;

            // Categorize homework based on percentage (same formula as attendance)
            if ($homeworkPercentage < 25) {
                $homeworkCategory = 'Poor';
            } elseif ($homeworkPercentage < 50) {
                $homeworkCategory = 'Satisfactory';
            } elseif ($homeworkPercentage < 75) {
                $homeworkCategory = 'Good';
            } else {
                $homeworkCategory = 'Excellent';
            }

            // Format homework as "Category (X%)"
            $homework = $homeworkCategory . ' (' . $homeworkPercentage . '%)';

            // Calculate behavior
        $behaviorData = DB::table('attendance')
            ->selectRaw('
            SUM(CASE WHEN behaviour = "Good" THEN 3
                     WHEN behaviour = "Satisfactory" THEN 2
                     WHEN behaviour = "Poor" THEN 1
                     ELSE 0 END) as total_score,
            COUNT(CASE WHEN behaviour IN ("Good","Satisfactory","Poor") THEN 1 END) as total_count
        ')
            ->where('family_id', $familyId)
            ->where('student_name', $studentName)
                ->where('subject', $subj)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('branch_id', session('branch_id'))
            ->first();

        $behaviorPercentage = $behaviorData->total_count > 0
            ? ($behaviorData->total_score / ($behaviorData->total_count * 3)) * 100
            : 0;

        $behavior = $behaviorPercentage >= 75
            ? 'Good'
            : ($behaviorPercentage >= 25 ? 'Satisfactory' : 'Poor');

            // Calculate performance
        $performanceData = DB::table('attendance')
            ->selectRaw('
            SUM(CASE WHEN performance = "Exceeding Target" THEN 3
                     WHEN performance = "On Target" THEN 2
                     WHEN performance = "Below Target" THEN 1
                     ELSE 0 END) as total_score,
            COUNT(CASE WHEN performance IN ("Exceeding Target","On Target","Below Target") THEN 1 END) as total_count
        ')
            ->where('family_id', $familyId)
            ->where('student_name', $studentName)
                ->where('subject', $subj)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('branch_id', session('branch_id'))
            ->first();

        $performancePercentage = $performanceData->total_count > 0
            ? ($performanceData->total_score / ($performanceData->total_count * 3)) * 100
            : 0;

        $performance = $performancePercentage >= 75
            ? 'Exceeding Target'
            : ($performancePercentage >= 25 ? 'On Target' : 'Below Target');

            // Get current grade using the same logic as grades module
            $currentGrade = 'Not entered';
            
            // Get key stage for this subject
            $keyStage = $qualifications[$subjectIndex] ?? null;
            
            // Check if key stage is KS1, KS2, or KS3
            if (in_array(strtoupper($keyStage ?? ''), ['KS1', 'KS2', 'KS3'])) {
                // For KS1/KS2/KS3 - get from attendance (bk_ch field)
                $lastAttendance = Attendance::where('branch_id', session('branch_id'))
                    ->where('family_id', $studentData->admissionid)
                    ->where('student_name', 'like', '%' . $studentFullName . '%')
                    ->where('subject', $subj)
                    ->whereBetween('date', [$startDateForAttendance, $endDateForAttendance])
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastAttendance && $lastAttendance->bk_ch) {
                    // Use full bk_ch value
                    $bkChValue = $lastAttendance->bk_ch;
                    $gradeValue = trim($bkChValue);
                    if (!empty($gradeValue)) {
                        $currentGrade = $gradeValue;
                    }
                }
            } else {
                // For KS4, KS5, Adult - get from StudentGrade table
                $existingGrade = StudentGrade::where('studentid', $studentData->studentid)
                    ->where('month', $monthFormatted)
                    ->where('subject_name', $subj)
                    ->where('branch_id', session('branch_id'))
                    ->first();

                if ($existingGrade && !empty($existingGrade->grade)) {
                    $currentGrade = $existingGrade->grade;
                }
            }

            // Add to Subject Overview
            $subjectOverview[] = [
                'subject_name' => $subj,
                'start_date' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : 'N/A',
                'tier' => $tier,
                'sessions_booked' => $sessionsBooked,
                'sessions_attended' => $sessionsAttended,
                'attendance' => $attendance,
                'homework' => $homework,
                'behaviour' => $behavior,
                'performance' => $performance,
            ];

            // Get test records (individual tests, not grouped)
            $testRecords = DB::table('student_tests')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subj)
                ->whereBetween('test_date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->orderBy('test_date', 'asc')
                ->orderBy('test_no', 'asc')
            ->get()
                ->map(function ($test) {
                    $testScore = $test->percentage ? str_replace('%', '', $test->percentage) : '0';
                return [
                        'test_date' => Carbon::parse($test->test_date)->format('d/m/Y'),
                        'book_name' => $test->book ?? '-',
                        'test_no' => $test->test_no ?? '-',
                        'test_score' => $test->percentage ?? '0%',
                ];
            })->toArray();

            // Add to Performance Sections
            $performanceSections[] = [
                'subject_name' => $subj,
                'current_grade' => $currentGrade,
                'target_grade' => $targetGrade,
                'test_records' => $testRecords,
            ];
        }

        $response = [
            'family_id' => $familyId,
            'student_name' => $studentName,
            'school_year' => $studentData->studentyearinschool ?? 'N/A',
            'email' => $email,
            'data' => [
                'subject_overview' => $subjectOverview,
                'performance_sections' => $performanceSections,
            ]
        ];

        return response()->json($response);
    }


    public function sendProgressReportEmail(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'email' => 'required|email',
                'report_html' => 'required|string',
                'teacher_comment' => 'nullable|string|max:1000',
            ]);

            $email = $request->input('email');
            $reportHtml = $request->input('report_html');
            $teacherComment = $request->input('teacher_comment') ?: 'No comments provided.';

            // Define the email template with header and footer matching the provided template
            $emailTemplate = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress Tracking Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 180px;
        }
        .container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-width: 700px;
            margin: 0 auto;
        }
        h1 {
            color: #2048AC;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 20px;
        }
        .report-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .report-info-table td {
            border: 1px solid #2048AC;
            padding: 8px;
        }
        .report-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 16.66%;
        }
        .report-info-table .info-content {
            background: #fff;
            width: 16.66%;
        }
        .subject-overview-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .subject-overview-table th,
        .subject-overview-table td {
            border: 1px solid #2048AC;
            padding: 10px;
            text-align: left;
            word-break: break-word;
        }
        .subject-overview-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
        }
        .subject-overview-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .performance-section {
            margin-bottom: 20px;
        }
        .performance-title {
            color: #FF8C00;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .subject-name {
            color: #28a745;
            font-weight: bold;
        }
        .performance-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .performance-info-table td {
            border: 1px solid #2048AC;
            padding: 8px;
        }
        .performance-info-table .info-header {
            font-weight: bold;
            background: #AED6F1;
            color: #2048AC;
            width: 30%;
        }
        .performance-info-table .info-content {
            background: #fff;
        }
        .test-records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .test-records-table th,
        .test-records-table td {
            border: 1px solid #2048AC;
            padding: 10px;
            text-align: left;
            word-break: break-word;
        }
        .test-records-table th {
            background-color: #e6f0ff;
            color: #2048AC;
            font-weight: 600;
        }
        .test-records-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .flag-pass {
            color: #28a745;
            font-weight: bold;
        }
        .flag-fail {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-top: 30px;
        }
        .footer a {
            color: #337ab7;
            text-decoration: none;
        }
        .footer img {
            max-width: 300px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo">
    </div>

    <div class="container">
        <h1>Progress Tracking Report</h1>
        ' . $reportHtml . '
    </div>

    <div class="footer">
        <p>
            67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
            Phone: <a href="tel:02089355931">020 8935 5931</a> |
            Email: <a href="mailto:admin@frobel.co.uk">admin@frobel.co.uk</a>
        </p>
        <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding">
    </div>
</body>
</html>';

            // Send email using Laravel Mail
            Mail::html($emailTemplate, function ($message) use ($email) {
    $message->to($email)
        ->subject('Progress Tracking Report - Frobel Education')
       ->from('exams@frobel.co.uk', 'Frobel Learning');
});


            return response()->json([
                'message' => 'Email sent successfully'
            ], 200);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to send progress report email: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function exportProgressReportPDF(Request $request)
    {
        $familyId = $request->query('family_id');
        $studentName = $request->query('student_name');
        $subject = $request->query('subject');
        $month = $request->query('month');

        if (!$familyId || !$studentName || !$subject || !$month) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        // Validate month format
        try {
            $requestedMonth = Carbon::createFromFormat('Y-m', $month);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid month format. Expected format: YYYY-MM'], 400);
        }

        // Validate that the selected month is not the current month
        $currentMonth = Carbon::now()->format('Y-m');
        if ($month === $currentMonth) {
            return response()->json(['error' => 'Current month cannot be selected. Please select a previous month.'], 400);
        }

        // Validate that the selected month is not in the future
        if ($requestedMonth->isFuture()) {
            return response()->json(['error' => 'Future months cannot be selected.'], 400);
        }

        // Get report data using the same method
        $reportRequest = new Request([
            'family_id' => $familyId,
            'student_name' => $studentName,
            'subject' => $subject,
            'month' => $month
        ]);

        $reportData = $this->getProgressTrackingReportData($reportRequest);
        $data = json_decode($reportData->getContent(), true);

        if (!$data || !isset($data['data'])) {
            return response()->json(['error' => 'No data found'], 404);
        }

        // Generate HTML for PDF
        $html = view('branchFrontend.report.progressReportPDF', $data)->render();

        // Configure Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Progress_Report_' . $familyId . '_' . str_replace(' ', '_', $studentName) . '_' . $month . '.pdf';

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function getStudentsByFamilyId(Request $request)
    {
        // dd($request->all());
        $familyId = $request->query('family_id');
        $students = DB::table('studentdata')->where('branch_id', session('branch_id'))
            ->where('admissionid', $familyId)
            ->select('studentname', 'studentsur')
            ->get();
        return response()->json($students);
    }
    public function updateStudentStatus(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'student_id' => 'required|exists:studentdata,studentid',
            'admission_id' => 'nullable|exists:studentdata,admissionid',
            'student_status' => 'required|in:active,inactive',
            'student_name' => 'nullable|string|max:255',
            'student_sur' => 'nullable|string|max:255',
        ]);

        $studentId = $request->input('student_id');
        $admissionId = $request->input('admission_id');
        $newStatus = $request->input('student_status');
        $studentName = $request->input('student_name');
        $studentSur = $request->input('student_sur');

        try {
            // Update student_status in the database based on studentid and branch_id
            $updated = DB::table('studentdata')
                ->where('studentid', $studentId)
                ->where('branch_id', $branchId)
                ->update(['student_status' => $newStatus]);

            if ($updated) {
                // Log the update for auditing
                Log::info('Student status updated', [
                    'student_id' => $studentId,
                    'admission_id' => $admissionId,
                    'student_status' => $newStatus,
                    'student_name' => $studentName,
                    'student_sur' => $studentSur,
                    'branch_id' => $branchId,
                ]);

                return response()->json(['message' => 'Student status updated successfully']);
            } else {
                Log::warning('No changes made to student status', [
                    'student_id' => $studentId,
                    'admission_id' => $admissionId,
                    'branch_id' => $branchId,
                ]);
                return response()->json(['error' => 'No changes made to student status'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error updating student status: ' . $e->getMessage(), [
                'student_id' => $studentId,
                'admission_id' => $admissionId,
                'branch_id' => $branchId,
            ]);
            return response()->json(['error' => 'Failed to update student status'], 500);
        }
    }


    public function approvedAdmission()
    {
        $studentRequests = StudentRequest::where('branch_id', session('branch_id'))->where('is_approved', 1)->orderBy('is_approved', 'desc')->get();
        $decodedData = [];
        // dd($studentRequests);
        foreach ($studentRequests as $request) {
            $decoded = json_decode($request->base64_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $decodedData[] = $decoded;
            } else {
                $decodedData[] = ['error' => 'Invalid JSON data'];
            }
        }

        return view('branchFrontend.admission.approved', compact('studentRequests', 'decodedData'));
    }


    public function Commentindex($studentRequestId)
    {
        $branchId = session('branch_id');
        $userId = Auth::id();

        $comments = DB::select("
            SELECT rc.id, rc.student_request_id, rc.user_id, rc.comment, rc.session, rc.created_at, rc.updated_at, u.name
            FROM request_comments rc
            JOIN users u ON rc.user_id = u.id
            WHERE rc.student_request_id = ? AND rc.session = ?
            ORDER BY rc.created_at DESC
        ", [$studentRequestId, $branchId]);

        return response()->json($comments);
    }

    public function Commentstorereq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_request_id' => 'required|integer|exists:student_requests,id',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $branchId = session('branch_id');
        $userId = Auth::id();

        DB::insert("
            INSERT INTO request_comments (student_request_id, user_id, comment, session, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ", [$request->student_request_id, $userId, $request->comment, $branchId]);

        $commentId = DB::getPdo()->lastInsertId();

        $comment = DB::selectOne("
            SELECT rc.id, rc.student_request_id, rc.user_id, rc.comment, rc.session, rc.created_at, rc.updated_at, u.name
            FROM request_comments rc
            JOIN users u ON rc.user_id = u.id
            WHERE rc.id = ?
        ", [$commentId]);

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comment added successfully'
        ]);
    }

    public function Commentupdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $userId = Auth::id();
        $comment = DB::selectOne("
            SELECT * FROM request_comments WHERE id = ? AND user_id = ?
        ", [$id, $userId]);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or comment not found'], 403);
        }

        DB::update("
            UPDATE request_comments
            SET comment = ?, updated_at = NOW()
            WHERE id = ?
        ", [$request->comment, $id]);

        $updatedComment = DB::selectOne("
            SELECT rc.id, rc.student_request_id, rc.user_id, rc.comment, rc.session, rc.created_at, rc.updated_at, u.name
            FROM request_comments rc
            JOIN users u ON rc.user_id = u.id
            WHERE rc.id = ?
        ", [$id]);

        return response()->json([
            'success' => true,
            'comment' => $updatedComment,
            'message' => 'Comment updated successfully'
        ]);
    }

    public function Commentdestroy($id)
    {
        $userId = Auth::id();
        $comment = DB::selectOne("
            SELECT * FROM request_comments WHERE id = ? AND user_id = ?
        ", [$id, $userId]);

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or comment not found'], 403);
        }

        DB::delete("
            DELETE FROM request_comments WHERE id = ?
        ", [$id]);

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }


    public function getSubjectsByStudent(Request $request)
    {
        $studentName = $request->query('student_name');
        // Fetch subjects from the database based on student_name
        $student = DB::table('studentdata')
            ->where(DB::raw("CONCAT(studentname, ' ', studentsur)"), 'like', '%' . $studentName . '%')
            ->where('branch_id', session('branch_id'))
            ->first();
        // dd($student,$studentName);
        $subjects = [];
        if ($student && $student->subject_names) {
            $subjects = json_decode($student->subject_names, true) ?: [];
        }
        return response()->json($subjects);
    }

    public function getAllStudentsProgressReport()
    {
        return view('branchFrontend.report.allStudentsProgressReport');
    }

    public function getAllSubjects()
    {
        $subjects = DB::table('subjects')
            ->where('branch_id', session('branch_id'))
            ->whereNotNull('name')
            ->pluck('name')      // get only the 'name' column
            ->unique()           // remove duplicates
            ->values()           // reset keys
            ->toArray();         // convert to array

        return response()->json($subjects);
    }

    // public function getAllStudentsProgressReportData(Request $request)
    // {
    //     $subject = $request->query('subject');
    //     $dateFrom = $request->query('date_from');
    //     $dateTo = $request->query('date_to');

    //     // Define scoring for behavior and performance
    //     $behaviorScores = [
    //         'Good' => 100,
    //         'Satisfactory' => 50,
    //         'Poor' => 0
    //     ];
    //     $performanceScores = [
    //         'Exceeding Target' => 100,
    //         'On Target' => 50,
    //         'Below Target' => 0
    //     ];

    //     // Get all students for the given subject
    //     $students = DB::table('studentdata')
    //         ->where('branch_id', session('branch_id'))
    //         ->whereJsonContains('subject_names', $subject)
    //         ->select('admissionid as family_id', 'studentname', 'studentsur', 'studentyearinschool', 'subject_names', 'tier')
    //         ->get();

    //     $responseData = [];
    //     $sessionStartDate = date('Y') . '-08-18';

    //     foreach ($students as $student) {
    //         $studentName = trim($student->studentname . ' ' . ($student->studentsur ?? ''));
    //         $familyId = $student->family_id;

    //         // Get start date
    //         $startDate = DB::table('attendance')
    //             ->where('family_id', $familyId)
    //             ->where('student_name', $studentName)
    //             ->where('subject', $subject)
    //             ->where('date', '>=', $sessionStartDate)
    //             ->where('branch_id', session('branch_id'))
    //             ->min('date');

    //         // Count sessions booked
    //         $sessionsBookedQuery = DB::table('general_timetables')
    //             ->where('student_ids', 'LIKE', "%{$familyId}%")
    //             ->where('student_names', 'LIKE', "%{$studentName}%")
    //             ->where('subjects', 'LIKE', "%{$subject}%")
    //             ->where('branch_id', session('branch_id'));

    //         if ($dateFrom && $dateTo) {
    //             $sessionsBookedQuery->whereBetween('date', [$dateFrom, $dateTo]);
    //         } elseif ($dateFrom) {
    //             $sessionsBookedQuery->where('date', '>=', $dateFrom);
    //         } elseif ($dateTo) {
    //             $sessionsBookedQuery->where('date', '<=', $dateTo);
    //         }

    //         $sessionsBooked = $sessionsBookedQuery->count();

    //         // Count sessions attended
    //         $sessionsAttended = DB::table('attendance')
    //             ->where('family_id', $familyId)
    //             ->where('student_name', $studentName)
    //             ->where('subject', $subject)
    //             ->whereBetween('date', [$dateFrom, $dateTo])
    //             ->where('branch_id', session('branch_id'))
    //             ->count();

    //         // Calculate current grade
    //         // $currentGrade = 'N/A';
    //         // if ($sessionsBooked > 0) {
    //         //     $attendancePercentage = ($sessionsAttended / $sessionsBooked) * 100;
    //         //     if ($attendancePercentage >= 70) {
    //         //         $currentGrade = 'A';
    //         //     } elseif ($attendancePercentage >= 60) {
    //         //         $currentGrade = 'B';
    //         //     } elseif ($attendancePercentage >= 50) {
    //         //         $currentGrade = 'C';
    //         //     } elseif ($attendancePercentage >= 40) {
    //         //         $currentGrade = 'D';
    //         //     } else {
    //         //         $currentGrade = 'F';
    //         //     }
    //         // }

    //         // Calculate behavior and performance
    //         $behaviorData = DB::table('attendance')
    //             ->selectRaw('
    //                 SUM(CASE WHEN behaviour = "Good" THEN 3
    //                          WHEN behaviour = "Satisfactory" THEN 2
    //                          WHEN behaviour = "Poor" THEN 1
    //                          ELSE 0 END) as total_score,
    //                 COUNT(CASE WHEN behaviour IN ("Good","Satisfactory","Poor") THEN 1 END) as total_count
    //             ')
    //             ->where('family_id', $familyId)
    //             ->where('student_name', $studentName)
    //             ->where('subject', $subject)
    //             ->whereBetween('date', [$dateFrom, $dateTo])
    //             ->where('branch_id', session('branch_id'))
    //             ->first();

    //         $behaviorPercentage = $behaviorData->total_count > 0
    //             ? ($behaviorData->total_score / ($behaviorData->total_count * 3)) * 100
    //             : 0;

    //         $behavior = $behaviorPercentage >= 75
    //             ? 'Good'
    //             : ($behaviorPercentage >= 25 ? 'Satisfactory' : 'Poor');

    //         $performanceData = DB::table('attendance')
    //             ->selectRaw('
    //                 SUM(CASE WHEN performance = "Exceeding Target" THEN 3
    //                          WHEN performance = "On Target" THEN 2
    //                          WHEN performance = "Below Target" THEN 1
    //                          ELSE 0 END) as total_score,
    //                 COUNT(CASE WHEN performance IN ("Exceeding Target","On Target","Below Target") THEN 1 END) as total_count
    //             ')
    //             ->where('family_id', $familyId)
    //             ->where('student_name', $studentName)
    //             ->where('subject', $subject)
    //             ->whereBetween('date', [$dateFrom, $dateTo])
    //             ->where('branch_id', session('branch_id'))
    //             ->first();

    //         $performancePercentage = $performanceData->total_count > 0
    //             ? ($performanceData->total_score / ($performanceData->total_count * 3)) * 100
    //             : 0;

    //         $performance = $performancePercentage >= 75
    //             ? 'Exceeding Target'
    //             : ($performancePercentage >= 25 ? 'On Target' : 'Below Target');

    //         // Get average test score
    //         $testScore = DB::table('student_tests')
    //             ->where('family_id', $familyId)
    //             ->where('student_name', $studentName)
    //             ->where('subject', $subject)
    //             ->whereBetween('test_date', [$dateFrom, $dateTo])
    //             ->where('branch_id', session('branch_id'))
    //             ->avg('percentage');

    //         $testScore = $testScore ? number_format($testScore, 2) : 'N/A';

    //         // Get tier
    //         $subjects = json_decode($student->subject_names, true) ?? [];
    //         $tiers = json_decode($student->tier, true) ?? [];
    //         $tier = 'N/A';
    //         if (is_array($subjects) && is_array($tiers)) {
    //             $index = array_search(strtoupper($subject), array_map('strtoupper', $subjects));
    //             if ($index !== false && isset($tiers[$index])) {
    //                 $tier = $tiers[$index];
    //             }
    //         }

    //         $responseData[] = [
    //             'family_id' => $familyId,
    //             'student_name' => $studentName,
    //             'school_year' => $student->studentyearinschool ?? 'N/A',
    //             'start_date' => $startDate ? date('d/m/Y', strtotime($startDate)) : 'N/A',
    //             'subject' => $subject,
    //             'tier' => $tier,
    //             'sessions_booked' => $sessionsBooked,
    //             'sessions_attended' => $sessionsAttended,
    //             // 'current_grade' => $currentGrade,
    //             'behavior' => $behavior,
    //             'performance' => $performance,
    //             'test_score' => $testScore
    //         ];
    //     }

    //     return response()->json(['data' => $responseData]);
    // }
    public function getAllStudentsProgressReportData(Request $request)
    {
        $subject = $request->query('subject');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Define scoring for behavior and performance
        $behaviorScores = [
            'Good' => 100,
            'Satisfactory' => 50,
            'Poor' => 0
        ];
        $performanceScores = [
            'Exceeding Target' => 100,
            'On Target' => 50,
            'Below Target' => 0
        ];

        // Get all students for the given subject
        $students = DB::table('studentdata')
            ->where('branch_id', session('branch_id'))
            ->whereJsonContains('subject_names', $subject)
            ->select('admissionid as family_id', 'studentname', 'studentsur', 'studentyearinschool', 'subject_names', 'tier', 'target_grades', 'current_grades')
            ->get();

        $responseData = [];
        $sessionStartDate = date('Y') . '-08-18';

        foreach ($students as $student) {
            $studentName = trim($student->studentname . ' ' . ($student->studentsur ?? ''));
            $familyId = $student->family_id;

            // Decode JSON fields
            $subjects = json_decode($student->subject_names, true) ?? [];
            $targetGrades = json_decode($student->target_grades, true) ?? [];
            $currentGrades = json_decode($student->current_grades, true) ?? [];
            $tiers = json_decode($student->tier, true) ?? [];

            // Find subject index and corresponding values
            $subjectIndex = array_search(strtoupper($subject), array_map('strtoupper', $subjects));
            $targetGrade = $subjectIndex !== false && isset($targetGrades[$subjectIndex]) ? $targetGrades[$subjectIndex] : 'N/A';
            $currentGrade = $subjectIndex !== false && isset($currentGrades[$subjectIndex]) ? $currentGrades[$subjectIndex] : 'N/A';
            $tier = $subjectIndex !== false && isset($tiers[$subjectIndex]) ? $tiers[$subjectIndex] : 'N/A';

            // Get start date
            $startDate = DB::table('attendance')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->where('date', '>=', $sessionStartDate)
                ->where('branch_id', session('branch_id'))
                ->min('date');

            // Count sessions booked
            $sessionsBookedQuery = DB::table('general_timetables')
                ->where('student_ids', 'LIKE', "%{$familyId}%")
                ->where('student_names', 'LIKE', "%{$studentName}%")
                ->where('subjects', 'LIKE', "%{$subject}%")
                ->where('branch_id', session('branch_id'));

            if ($dateFrom && $dateTo) {
                $sessionsBookedQuery->whereBetween('date', [$dateFrom, $dateTo]);
            } elseif ($dateFrom) {
                $sessionsBookedQuery->where('date', '>=', $dateFrom);
            } elseif ($dateTo) {
                $sessionsBookedQuery->where('date', '<=', $dateTo);
            }

            $sessionsBooked = $sessionsBookedQuery->count();

            // Count sessions attended
            $sessionsAttended = DB::table('attendance')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->count();

            // Calculate behavior
            $behaviorData = DB::table('attendance')
                ->selectRaw('
                SUM(CASE WHEN behaviour = "Good" THEN 3
                         WHEN behaviour = "Satisfactory" THEN 2
                         WHEN behaviour = "Poor" THEN 1
                         ELSE 0 END) as total_score,
                COUNT(CASE WHEN behaviour IN ("Good","Satisfactory","Poor") THEN 1 END) as total_count
            ')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->first();

            $behaviorPercentage = $behaviorData->total_count > 0
                ? ($behaviorData->total_score / ($behaviorData->total_count * 3)) * 100
                : 0;

            $behavior = $behaviorPercentage >= 75
                ? 'Good'
                : ($behaviorPercentage >= 25 ? 'Satisfactory' : 'Poor');

            // Calculate performance
            $performanceData = DB::table('attendance')
                ->selectRaw('
                SUM(CASE WHEN performance = "Exceeding Target" THEN 3
                         WHEN performance = "On Target" THEN 2
                         WHEN performance = "Below Target" THEN 1
                         ELSE 0 END) as total_score,
                COUNT(CASE WHEN performance IN ("Exceeding Target","On Target","Below Target") THEN 1 END) as total_count
            ')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->first();

            $performancePercentage = $performanceData->total_count > 0
                ? ($performanceData->total_score / ($performanceData->total_count * 3)) * 100
                : 0;

            $performance = $performancePercentage >= 75
                ? 'Exceeding Target'
                : ($performancePercentage >= 25 ? 'On Target' : 'Below Target');

            // Get average test score
            $testScore = DB::table('student_tests')
                ->where('family_id', $familyId)
                ->where('student_name', $studentName)
                ->where('subject', $subject)
                ->whereBetween('test_date', [$dateFrom, $dateTo])
                ->where('branch_id', session('branch_id'))
                ->avg('percentage');

            $testScore = $testScore ? number_format($testScore, 2) : 'N/A';

            $responseData[] = [
                'family_id' => $familyId,
                'student_name' => $studentName,
                'school_year' => $student->studentyearinschool ?? 'N/A',
                'start_date' => $startDate ? date('d/m/Y', strtotime($startDate)) : 'N/A',
                'subject' => $subject,
                'tier' => $tier,
                'sessions_booked' => $sessionsBooked,
                'sessions_attended' => $sessionsAttended,
                'target_grade' => $targetGrade,
                'current_grade' => $currentGrade,
                'behavior' => $behavior,
                'performance' => $performance,
                'test_score' => $testScore
            ];
        }

        return response()->json(['data' => $responseData]);
    }

    public function sendAllStudentsProgressReportEmail(Request $request)
    {
        // Implement email sending logic here
        // Use $request->email for recipient email
        // Use $request->report_html for the HTML content
        // Use $request->report_data for JSON data if needed
        // Example: Use Laravel Mail to send the report
        // For now, return a simple response
        return response()->json(['message' => 'Email sent successfully']);
    }

    public function getStudentDetails(Request $request)
    {
        // dd($request->all());
        // Extract parameters from the request
        $student = $request->query('student');
        $branchId = $request->query('branch_id');

        // Extract admissionid and name from the student string (e.g., "Fatimah Zahra Syed-3615")
        $parts = explode('-', $student);
        $admissionId = end($parts) ?? null;
        $fullName = implode('-', array_slice($parts, 0, -1)) ?? null;

        if (!$admissionId || !$branchId || !$fullName) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid student, name, or branch ID.'
            ], 400);
        }

        // Split fullName into studentname and studentsur (assuming last word is surname)
        // $nameParts = explode(' ', $fullName);
        // $studentsur = array_pop($nameParts); // Last part is surname
        // $studentname = implode(' ', $nameParts); // Remaining parts are first name
        // dd($studentsur,$studentname);

        $fullName = trim($fullName);
        $nameParts = explode(' ', $fullName);

        if (count($nameParts) >= 3) {
            // Handle names like "Wharisa Sultana Rahman"
            $studentname = $nameParts[0]; // First part only
            $studentsur = $nameParts[1] . ' ' . $nameParts[2]; // Combine last two parts
        } elseif (count($nameParts) == 2) {
            // Handle names like "Abyad Rahman"
            $studentname = $nameParts[0];
            $studentsur = $nameParts[1];
        } else {
            // Fallback (only one name given)
            $studentname = $fullName;
            $studentsur = '';
        }

        // dd($studentname,$studentsur);
        // Fetch student details from the studentdata table
        // $studentData = Student::where('admissionid', $admissionId)
        //     ->where('branch_id', $branchId)
        //     ->where('studentname', 'LIKE', '%' . $studentname . '%')
        //     ->where('studentsur', 'LIKE', '%' . $studentsur . '%')
        //     ->first();
        $studentData = Student::where('admissionid', $admissionId)
            ->where('branch_id', $branchId)
            ->whereRaw("CONCAT(studentname, ' ', studentsur) LIKE ?", ['%' . $studentname . ' ' . $studentsur . '%'])
            ->first();
        // dd($studentData);


        if (!$studentData) {
            return response()->json([
                'success' => false,
                'message' => 'No student found for the given admission ID, name, and branch.'
            ], 404);
        }

        // Decode the subject_names JSON column
        $subjects = json_decode($studentData->subject_names, true) ?? [];
        // dd($subjects);
        $subjects = array_unique($subjects);

        // Optionally reindex array (0,1,2,...)
        $subjects = array_values($subjects);

        return response()->json([
            'success' => true,
            'data' => [
                'student_name' => $studentData->studentname . ' ' . $studentData->studentsur,
                'admission_id' => $studentData->admissionid,
                'branch_id' => $studentData->branch_id,
                'subjects' => $subjects
            ]
        ]);
    }


    public function archive(Request $request, $id)
    {
        try {
            // Find the student request by ID
            $studentRequest = StudentRequest::findOrFail($id);

            // Update only the is_archive column to 1
            $studentRequest->update([
                'is_archive' => 1
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Request archived successfully'
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error archiving student request: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to archive request'
            ], 500);
        }
    }

    public function getArchive()
    {
        $studentRequests = StudentRequest::where('branch_id', session('branch_id'))->where('is_approved', 0)->whereNotNull('is_archive')->orderBy('created_at', 'desc')->get();
        $decodedData = [];

        foreach ($studentRequests as $request) {
            $decoded = json_decode($request->base64_data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $decodedData[] = $decoded;
            } else {
                $decodedData[] = ['error' => 'Invalid JSON data'];
            }
        }
        // dd($studentRequests);

        return view('branchFrontend.admission.archive', compact('studentRequests', 'decodedData'));
    }


    public function undoArchive(Request $request, $id)
    {
        try {
            $studentRequest = StudentRequest::findOrFail($id);
            $studentRequest->update(['is_archive' => null]);
            return response()->json([
                'success' => true,
                'message' => 'Request unarchived successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error unarchiving student request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to unarchive request'
            ], 500);
        }
    }


    public function getLearnerSessions(Request $request)
    {
        $familyId    = $request->input('family_id');
        $learnerName = trim($request->input('learner_name'));

        if (!$familyId || !$learnerName) {
            return response()->json([
                'sessions' => [],
                'message'  => 'Family ID and Learner Name are required.'
            ], 400);
        }

        $searchName = strtolower($learnerName);

        $sessions = IagMeeting::where('family_id', $familyId)
            ->whereRaw("LOWER(learner_name) = ?", [$searchName])
            ->whereNotNull('current_session')
            // ->where('current_session', '!=', '')
            ->pluck('current_session')
            ->unique()
            ->values();

        if ($sessions->isEmpty()) {
            return response()->json([
                'sessions' => [],
                'message'  => 'No sessions found for this learner.'
            ], 404);
        }

        return response()->json([
            'sessions' => $sessions,
            'message'  => 'Sessions loaded successfully.'
        ]);
    }

    public function timeTableSchedular()
    {
        return view('branchFrontend.schedular.TimetableSchedular');
    }

    public function termBreakSchedular()
    {
        return view('branchFrontend.schedular.TermBreakSchedular');
    }

    public function termBreakSchedularGet(Request $request)
    {
        $date = $request->query('date');
        $slot = (string) $request->query('slot');
        $branch_id = session('branch_id');

        if (!$date || !in_array($slot, ['7', '8'], true)) {
            return response()->json(['classes' => []]);
        }

        $classes = \App\Models\GeneralTimetable::where('date', $date)
            ->where('slot', $slot)
            ->where('session_type', 'termbreak_scheduler')
            ->where('branch_id', $branch_id)
            ->get();

        $allTeachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', $branch_id)
            ->distinct()
            ->pluck('teacher_name')
            ->toArray();

        foreach ($classes as $cls) {
            $studentIds = is_array($cls->student_ids) ? $cls->student_ids : (json_decode((string) $cls->student_ids, true) ?? []);
            $studentNames = is_array($cls->student_names) ? $cls->student_names : (json_decode((string) $cls->student_names, true) ?? []);
            $subjects = is_array($cls->subjects) ? $cls->subjects : (json_decode((string) $cls->subjects, true) ?? []);
            $years = is_array($cls->year_in_schools) ? $cls->year_in_schools : (json_decode((string) $cls->year_in_schools, true) ?? []);

            $subjectOptions = [];
            foreach ($studentIds as $index => $id) {
                $student = DB::table('studentdata')
                    ->where('branch_id', $branch_id)
                    ->where('admissionid', $id)
                    ->select('subject_names', 'studentyearinschool')
                    ->first();

                $studentSubjects = ($student && $student->subject_names)
                    ? (json_decode($student->subject_names, true) ?? [])
                    : [];

                if (empty($years[$index])) {
                    $years[$index] = $student->studentyearinschool ?? '';
                }

                if (isset($subjects[$index]) && !empty($subjects[$index]) && !in_array($subjects[$index], $studentSubjects, true)) {
                    $studentSubjects[] = $subjects[$index];
                }

                $subjectOptions[] = $studentSubjects;
            }

            $cls->student_ids = json_encode($studentIds);
            $cls->student_names = json_encode($studentNames);
            $cls->subjects = json_encode($subjects);
            $cls->year_in_schools = json_encode($years);
            $cls->teacher_options = json_encode([$allTeachers]);
            $cls->subject_options = json_encode($subjectOptions);
            $cls->class_id = $cls->id;
        }

        return response()->json(['classes' => $classes]);
    }

    public function termBreakSchedularGetStudentByFamilyId(Request $request)
    {
        // Reuse timetable-scheduler student search behavior
        return $this->getStudentByFamilyId($request);
    }

    public function termBreakSchedularGetTeachers(Request $request)
    {
        // Reuse timetable-scheduler teacher list behavior
        return $this->getScheduleTeachers($request);
    }

    public function termBreakSchedularSaveSingle(Request $request)
    {
        $slot = (string) $request->input('slot');
        if (!in_array($slot, ['7', '8'], true)) {
            return response()->json(['success' => false, 'error' => 'Only term break slots (7,8) are allowed.'], 422);
        }
        // Reuse timetable-scheduler save/update logic for exact behavior.
        return $this->timeTableSchedularSaveSingle($request);
    }

    public function termBreakSchedularDelete($id, Request $request)
    {
        // Reuse timetable-scheduler delete-chain behavior.
        return $this->deleteScheduleFromBackend($id, $request);
    }

    // public function timeTableSchedularGet(Request $request)
    // {
    //     $date = $request->query('date');
    //     $slot = $request->query('slot');
    //     $branch_id = session('branch_id');

    //     if (!$date || !$slot) {
    //         return response()->json(['classes' => []]);
    //     }

    //     $classes = \App\Models\GeneralTimetable::where('date', $date)
    //         ->where('slot', $slot)
    //         ->where('branch_id', $branch_id)
    //         ->get();

    //     // Fetch all distinct subjects for the branch
    //     $allSubjects = DB::table('teachers_subject')
    //         ->where('branch_id', $branch_id)
    //         ->distinct()
    //         ->pluck('subject')
    //         ->toArray();

    //     // Fetch all distinct teacher names for the branch
    //     $allTeachers = DB::table('teachers_subject')
    //         ->where('branch_id', $branch_id)
    //         ->distinct()
    //         ->pluck('teacher_name')
    //         ->toArray();

    //     foreach ($classes as $cls) {
    //         $studentIds = json_decode($cls->student_ids, true) ?? [];
    //         $studentNames = json_decode($cls->student_names, true) ?? [];
    //         $subjects = json_decode($cls->subjects, true) ?? [];
    //         $yearInSchools = [];

    //         foreach ($studentIds as $index => $id) {
    //             $student = \App\Models\Student::where('admissionid', $id)
    //                 ->where('branch_id', $branch_id)
    //                 ->whereRaw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) = ?", [$studentNames[$index]])
    //                 ->first();

    //             $yearInSchools[] = $student?->studentyearinschool ?? '';
    //         }

    //         // Subject options (dynamic from DB)
    //         $subjectOptions = array_fill(0, count($studentIds), $allSubjects);

    //         // Use all teachers for every class
    //         $teacherOptions = [$allTeachers]; // Wrap in array to maintain compatibility with frontend

    //         // Attach to class
    //         $cls->year_in_schools = json_encode($yearInSchools);
    //         $cls->teacher_options = json_encode($teacherOptions);
    //         $cls->subject_options = json_encode($subjectOptions);
    //     }

    //     return response()->json(['classes' => $classes]);
    // }

    // public function timeTableSchedularGet(Request $request)
    // {
    //     $date = $request->query('date');
    //     $slot = $request->query('slot');
    //     $branch_id = session('branch_id');

    //     if (!$date || !$slot) {
    //         return response()->json(['classes' => []]);
    //     }

    //     $classes = \App\Models\GeneralTimetable::where('date', $date)
    //         ->where('slot', $slot)
    //         ->where('branch_id', $branch_id)
    //         ->get();

    //     // Fetch all distinct subjects for the branch
    //     $allSubjects = DB::table('teachers_subject')
    //         ->where('branch_id', $branch_id)
    //         ->distinct()
    //         ->pluck('subject')
    //         ->toArray();

    //     // Fetch all distinct teacher names for the branch
    //     $allTeachers = DB::table('teachers_subject')
    //         ->where('branch_id', $branch_id)
    //         ->distinct()
    //         ->pluck('teacher_name')
    //         ->toArray();

    //     foreach ($classes as $cls) {
    //         $studentIds = json_decode($cls->student_ids, true) ?? [];
    //         $studentNames = json_decode($cls->student_names, true) ?? [];
    //         $subjects = json_decode($cls->subjects, true) ?? [];
    //         $yearInSchools = [];

    //         foreach ($studentIds as $index => $id) {
    //             $student = \App\Models\Student::where('admissionid', $id)
    //                 ->where('branch_id', $branch_id)
    //                 ->whereRaw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) = ?", [$studentNames[$index]])
    //                 ->first();

    //             $yearInSchools[] = $student?->studentyearinschool ?? '';
    //         }

    //         // Subject options (dynamic from DB)
    //         $subjectOptions = array_fill(0, count($studentIds), $allSubjects);

    //         // Use all teachers for every class
    //         $teacherOptions = [$allTeachers]; // Wrap in array to maintain compatibility with frontend

    //         // Attach to class
    //         $cls->year_in_schools = json_encode($yearInSchools);
    //         $cls->teacher_options = json_encode($teacherOptions);
    //         $cls->subject_options = json_encode($subjectOptions);
    //         // Include the class ID
    //         $cls->class_id = $cls->id; // Add the primary key as 'class_id'
    //     }

    //     return response()->json(['classes' => $classes]);
    // }

    public function timeTableSchedularGet(Request $request)
    {
        $date = $request->query('date');
        $slot = $request->query('slot');
        $branch_id = session('branch_id');

        if (!$date || !$slot) {
            return response()->json(['classes' => []]);
        }

        $classes = \App\Models\GeneralTimetable::where('date', $date)
            ->where('slot', $slot)
            ->where('branch_id', $branch_id)
            ->get();

        // ✅ FIX: Remove duplicate classes (same teacher_id + slot + date)
        // Group by teacher_id + slot and keep only one record (prefer parent_id = null or highest id)
        $uniqueClasses = [];
        $seenKeys = [];
        
        foreach ($classes as $class) {
            $teacherId = trim($class->teacher_id ?? '');
            $slotValue = trim($class->slot ?? '');
            $key = $teacherId . '|' . $slotValue;
            
            if (!isset($seenKeys[$key])) {
                // First occurrence - add it
                $uniqueClasses[] = $class;
                $seenKeys[$key] = $class->id;
            } else {
                // Duplicate found - keep the one with parent_id = null, or highest id
                $existingIndex = null;
                foreach ($uniqueClasses as $idx => $existing) {
                    if ($existing->id == $seenKeys[$key]) {
                        $existingIndex = $idx;
                        break;
                    }
                }
                
                if ($existingIndex !== null) {
                    $existingRecord = $uniqueClasses[$existingIndex];
                    // Prefer record with parent_id = null, otherwise prefer highest id
                    $shouldReplace = false;
                    if ($class->parent_id === null && $existingRecord->parent_id !== null) {
                        $shouldReplace = true;
                    } elseif (($class->parent_id === null) === ($existingRecord->parent_id === null)) {
                        // Both have same parent_id status, prefer higher id (newer record)
                        if ($class->id > $existingRecord->id) {
                            $shouldReplace = true;
                        }
                    }
                    
                    if ($shouldReplace) {
                        $uniqueClasses[$existingIndex] = $class;
                        $seenKeys[$key] = $class->id;
                        \Log::info("timeTableSchedularGet - Removed duplicate class", [
                            'removed_id' => $existingRecord->id,
                            'kept_id' => $class->id,
                            'teacher_id' => $teacherId,
                            'slot' => $slotValue,
                            'date' => $date
                        ]);
                    }
                }
            }
        }
        
        $classes = collect($uniqueClasses);

        // Fetch all distinct subjects for the branch (for new students)
        $allSubjects = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', $branch_id)
            ->distinct()
            ->pluck('subject')
            ->toArray();

        // Fetch all distinct teacher names
        $allTeachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', $branch_id)
            ->distinct()
            ->pluck('teacher_name')
            ->toArray();

        foreach ($classes as $cls) {
            // Check if already array (due to model cast) or decode from JSON string
            $studentIds = is_array($cls->student_ids) ? $cls->student_ids : (json_decode($cls->student_ids, true) ?? []);
            $studentNames = is_array($cls->student_names) ? $cls->student_names : (json_decode($cls->student_names, true) ?? []);
            $subjects = is_array($cls->subjects) ? $cls->subjects : (json_decode($cls->subjects, true) ?? []);
            $permanent = is_array($cls->permanent) ? $cls->permanent : (json_decode($cls->permanent, true) ?? []);

            // Parse move_from_page to get list of students that should be filtered out
            $movedStudents = [];
            if ($cls->move_from_page) {
                $movedStudentsList = array_map('trim', explode(',', $cls->move_from_page));
                foreach ($movedStudentsList as $movedStudent) {
                    if (!empty($movedStudent)) {
                        $movedStudents[] = trim($movedStudent);
                    }
                }
            }

            // ✅ Filter out students that are in move_from_page AND switch students (permanent='No')
            $filteredStudentIds = [];
            $filteredStudentNames = [];
            $filteredSubjects = [];

            foreach ($studentIds as $index => $id) {
                $studentName = isset($studentNames[$index]) ? $studentNames[$index] : '';
                $studentFullName = trim($id . ' ' . $studentName);

                // Check if this student is in move_from_page
                $shouldFilter = false;
                foreach ($movedStudents as $movedStudent) {
                    if (trim($movedStudent) === $studentFullName) {
                        $shouldFilter = true;
                        break;
                    }
                }

                // ✅ Also filter out switch students (permanent='No') - these should NOT show on timetable-scheduler
                if (!$shouldFilter) {
                    $isSwitchStudent = isset($permanent[$index]) && $permanent[$index] === 'No';
                    if ($isSwitchStudent) {
                        $shouldFilter = true; // Filter out switch students
                    }
                }

                // Only include students that are NOT in move_from_page AND NOT switch students
                if (!$shouldFilter) {
                    $filteredStudentIds[] = $id;
                    $filteredStudentNames[] = $studentName;
                    $filteredSubjects[] = isset($subjects[$index]) ? $subjects[$index] : '';
                }
            }

            // Update the arrays with filtered data
            $studentIds = $filteredStudentIds;
            $studentNames = $filteredStudentNames;
            $subjects = $filteredSubjects;

            // Update the class object with filtered data
            $cls->student_ids = json_encode($studentIds);
            $cls->student_names = json_encode($studentNames);
            $cls->subjects = json_encode($subjects);

            $yearInSchools = [];
            $subjectOptions = []; // اب per-student subjects ہوں گے
            $medicalFlags = [];

            foreach ($studentIds as $index => $id) {
                $studentName = isset($studentNames[$index]) ? $studentNames[$index] : '';
                $studentQuery = DB::table('studentdata')
                    ->where('branch_id', $branch_id)
                    ->where('admissionid', $id);

                // Only filter by name if it's provided
                if (!empty($studentName)) {
                    $studentQuery->where(DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, ''))"), 'LIKE', '%' . $studentName . '%');
                }

                $student = $studentQuery->select(
                        'admissionid',
                        DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) as full_name"),
                        'studentyearinschool',
                        'subject_names',
                        'medical_condition'
                    )
                    ->first();

                $yearInSchools[] = $student?->studentyearinschool ?? '';

                // Student کا اپنا subject list - only show student's relevant subjects
                $studentSubjects = $student && $student->subject_names
                    ? json_decode($student->subject_names, true)
                    : [];

                // Only use student's subjects, not all subjects by default
                // Start with empty array instead of $allSubjects
                $subjectOptions[] = !empty($studentSubjects) ? $studentSubjects : [];

                // اگر saved subject ہے لیکن options میں نہیں، تو شامل کرو
                if (isset($subjects[$index]) && $subjects[$index] && !in_array($subjects[$index], $subjectOptions[$index])) {
                    $subjectOptions[$index][] = $subjects[$index];
                }

                // Medical flag per student
                $mc = strtolower((string)($student->medical_condition ?? ''));
                $medicalFlags[] = in_array($mc, ['yes', '1', 'true'], true);
            }

            $teacherOptions = [$allTeachers]; // same as before

            $cls->year_in_schools = json_encode($yearInSchools);
            $cls->teacher_options = json_encode($teacherOptions);
            $cls->subject_options = json_encode($subjectOptions); // اب per-student
            $cls->student_medicals = json_encode($medicalFlags);
            $cls->class_id = $cls->id;

            // Hide move_from_page from response - should not appear in relevant class table
            unset($cls->move_from_page);
        }

        return response()->json(['classes' => $classes]);
    }

    public function getStudentByFamilyId(Request $request)
    {
        $familyId  = $request->query('family_id');
        $branch_id = session('branch_id');



        if (!$familyId) {
            return response()->json(['students' => []]);
        }

        $students = DB::table('studentdata')
            ->where('branch_id', $branch_id)
            ->where('admissionid', $familyId)               // <-- column you use
            ->where('student_status', 'active')
            ->select(
                'admissionid',
                DB::raw("CONCAT(studentname, ' ', COALESCE(studentsur, '')) as full_name"),
                'studentyearinschool',
                'subject_names'
            )
            ->get();


        $result = $students->map(function ($s) {
            // dd([
            //     'id'       => $s->admissionid,
            //     'name'     => trim($s->full_name),
            //     'year'     => $s->studentyearinschool,
            //     'subjects' => $s->subject_names ? json_decode($s->subject_names, true) : []
            // ]);
            return [
                'id'       => $s->admissionid,
                'name'     => trim($s->full_name),
                'year'     => $s->studentyearinschool,
                'subjects' => $s->subject_names ? json_decode($s->subject_names, true) : []
            ];
        });

        return response()->json(['students' => $result]);
    }

    public function checkStudentMedical(Request $request)
    {
        $branchId   = session('branch_id');
        $familyId   = $request->query('family_id');
        $studentRaw = trim((string) $request->query('student_name', ''));

        if (!$branchId || !$familyId || $studentRaw === '') {
            return response()->json(['has_condition' => false]);
        }

        // Normalize name comparison
        $studentName = strtolower(preg_replace('/\s+/', ' ', $studentRaw));

        // Fetch all students in this family, then match name in PHP (robust whitespace handling)
        $candidates = DB::table('studentdata')
            ->where('branch_id', $branchId)
            ->where('admissionid', $familyId)
            ->select('studentid', 'studentname', 'studentsur', 'medical_condition')
            ->get();

        if ($candidates->isEmpty()) {
            return response()->json(['has_condition' => false]);
        }

        $matched = null;
        foreach ($candidates as $cand) {
            $full = trim(($cand->studentname ?? '') . ' ' . ($cand->studentsur ?? ''));
            $norm = strtolower(preg_replace('/\s+/', ' ', $full));
            if ($norm === $studentName) {
                $matched = $cand;
                break;
            }
        }

        if (!$matched) {
            return response()->json(['has_condition' => false]);
        }

        // Prefer medical_condition from studentdata if available
        $value = $matched->medical_condition ?? null;
        if ($value === null || $value === '') {
            // Fallback: Look up medical condition table if exists
            try {
                $medical = DB::table('medical_condition')
                    ->where('branch_id', $branchId)
                    ->where('student_id', $matched->studentid)
                    ->select('medicalDetails')
                    ->first();
                if ($medical) {
                    $value = $medical->medicalDetails;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if ($value === null || $value === '') {
            return response()->json(['has_condition' => false]);
        }

        $value = strtolower((string) $value);
        $has   = ($value === 'yes') || ($value === '1') || ($value === 'true');

        return response()->json(['has_condition' => $has]);
    }

    public function getScheduleTeachers(Request $request)
    {
        // dd($request->all());
        // // Validate request parameters
        // $request->validate([
        //     'branch_id' => 'required|integer',
        //     'subject' => 'required|string'
        // ]);

        // ⚠️ SECURITY FIX: Use session branch_id instead of request parameter
        $branch_id = session('branch_id');
        $subject = $request->query('subject');

        // Fetch teachers
        $teachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->where('branch_id', $branch_id)
            ->distinct()
            ->pluck('teacher_name')
            ->map(fn($name) => ['id' => $name, 'name' => $name])
            ->toArray();



        return response()->json([
            'teachers' => $teachers
        ]);
    }

    // public function timeTableSchedularSave(Request $request)
    // {
    //     try {
    //         // Validate the incoming request data
    //         $data = $request->validate([
    //             'date' => 'required|date',
    //             'slot' => 'required|string',
    //             'classes' => 'required|array',
    //             'classes.*.class_id' => 'nullable|integer',
    //             'classes.*.teacher_id' => 'nullable|string',
    //             'classes.*.student_ids' => 'required|array',
    //             'classes.*.student_names' => 'required|array',
    //             'classes.*.subjects' => 'required|array',
    //             'classes.*.year_in_schools' => 'required|array',
    //             'classes.*.is_attendance' => 'nullable|array', // Allow is_attendance to be optional
    //         ]);

    //         $branch = \App\Models\User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //         $branch_id = $branch ? $branch->branch_id : session('branch_id');
    //         $branch_name = $branch ? $branch->branch_name : 'Unknown Branch';
    //         $date = $data['date'];
    //         $slot = $data['slot'];
    //         $currentDay = date('w', strtotime($date)); // 0 (Sunday) to 6 (Saturday)
    //         $dayName = strtolower(date('l', strtotime($date)));

    //         // Time slot mapping
    //         $timeSlotMapping = [
    //             '1' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '09:00 - 11:00am', 'label' => 'Lesson 1'],
    //             '2' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '11:20 - 01:20pm', 'label' => 'Lesson 2'],
    //             '3' => ['weekday' => '04:30 - 06:30pm', 'weekend' => '02:00 - 04:00pm', 'label' => 'Lesson 3'],
    //             '4' => ['weekday' => '06:45 - 08:45pm', 'weekend' => '', 'label' => 'Lesson 4'],
    //             '7' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '11:00 - 01:00pm', 'label' => 'Term Break Session 1'],
    //             '8' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '01:30 - 03:30pm', 'label' => 'Term Break Session 2'],
    //         ];

    //         // Determine time_slot based on slot and day
    //         $time_slot = isset($timeSlotMapping[$slot])
    //             ? ($currentDay >= 1 && $currentDay <= 5 ? $timeSlotMapping[$slot]['weekday'] : $timeSlotMapping[$slot]['weekend'])
    //             : 'Unknown Slot';

    //         $isTermBreak = in_array($slot, ['7', '8']);

    //         foreach ($data['classes'] as $classData) {
    //             // Initialize permanent flags
    //             $permanent = array_fill(0, count($classData['student_ids']), 'Yes');

    //             // Prepare main record data for the selected day
    //             $mainRecordData = [
    //                 'date' => $date,
    //                 'slot' => $slot,
    //                 'time_slot' => $time_slot,
    //                 'teacher_id' => $classData['teacher_id'] ?? null,
    //                 'student_ids' => json_encode($classData['student_ids']),
    //                 'student_names' => json_encode($classData['student_names']),
    //                 'subjects' => json_encode($classData['subjects']),
    //                 'year_in_schools' => json_encode($classData['year_in_schools']),
    //                 'permanent' => json_encode($permanent),
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //                 'parent_id' => null,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //                 'additional_student' => null,
    //                 'additional_info' => null,
    //                 'session_type' => null,
    //             ];

    //             // Update or create the main record, preserving existing is_attendance
    //             $mainRecord = null;
    //             if ($classData['class_id']) {
    //                 $class = \App\Models\GeneralTimetable::find($classData['class_id']);
    //                 if ($class) {
    //                     // Preserve existing is_attendance
    //                     $mainRecordData['is_attendance'] = $class->is_attendance; // Keep existing value
    //                     // Delete future records for this class (starting from next week)
    //                     \App\Models\GeneralTimetable::where('parent_id', $class->parent_id ?: $class->id)
    //                         ->whereDate('date', '>', $date) // Only delete future records
    //                         ->where('branch_id', $branch_id)
    //                         ->delete();
    //                     $class->update($mainRecordData);
    //                     $mainRecord = $class;
    //                 }
    //             }
    //             if (!$mainRecord) {
    //                 // Set is_attendance for new record
    //                 $mainRecordData['is_attendance'] = isset($classData['is_attendance'])
    //                     ? json_encode($classData['is_attendance'])
    //                     : json_encode(array_fill(0, count($classData['student_ids']), 'Yes'));
    //                 // Create a new record if no valid class_id or class not found
    //                 $mainRecord = \App\Models\GeneralTimetable::create($mainRecordData);
    //             }

    //             // Skip replication if no permanent students or empty student list
    //             if (empty($classData['student_ids']) || !in_array('Yes', $permanent)) {
    //                 continue;
    //             }

    //             // Create future records for permanent students
    //             $records = []; // Array for future records only
    //             $currentDate = strtotime($date . ' +1 week');
    //             $endDate = strtotime('+1 year', $currentDate);

    //             if ($isTermBreak) {
    //                 // Term break sessions are scheduled weekly
    //                 while ($currentDate <= $endDate) {
    //                     $records[] = [
    //                         'date' => date('Y-m-d', $currentDate),
    //                         'slot' => $slot,
    //                         'time_slot' => $time_slot,
    //                         'teacher_id' => $classData['teacher_id'] ?? null,
    // 'slot' => $slot,
    //                         'time_slot' => $time_slot,
    //                         'teacher_id' => $classData['teacher_id'] ?? null,
    //                         'student_ids' => json_encode($classData['student_ids']),
    //                         'student_names' => json_encode($classData['student_names']),
    //                         'subjects' => json_encode($classData['subjects']),
    //                         'year_in_schools' => json_encode($classData['year_in_schools']),
    //                         'permanent' => json_encode($permanent),
    //                         'is_attendance' => null, // No attendance for future records
    //                         'branch_id' => $branch_id,
    //                         'branch_name' => $branch_name,
    //                         'parent_id' => $mainRecord->id,
    //                         'created_at' => now(),
    //                         'updated_at' => now(),
    //                         'additional_student' => null,
    //                         'additional_info' => null,
    //                         'session_type' => null,
    //                     ];
    //                     $currentDate = strtotime('+1 week', $currentDate);
    //                 }
    //             } else {
    //                 // Regular weekday or weekend sessions
    //                 if (in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
    //                     while ($currentDate <= $endDate) {
    //                         $dayOfWeek = strtolower(date('l', $currentDate));
    //                         if (in_array($dayOfWeek, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) {
    //                             $records[] = [
    //                                 'date' => date('Y-m-d', $currentDate),
    //                                 'slot' => $slot,
    //                                 'time_slot' => $timeSlotMapping[$slot]['weekday'] ?? 'Unknown Slot',
    //                                 'teacher_id' => $classData['teacher_id'] ?? null,
    //                                 'student_ids' => json_encode($classData['student_ids']),
    //                                 'student_names' => json_encode($classData['student_names']),
    //                                 'subjects' => json_encode($classData['subjects']),
    //                                 'year_in_schools' => json_encode($classData['year_in_schools']),
    //                                 'permanent' => json_encode($permanent),
    //                                 'is_attendance' => null, // No attendance for future records
    //                                 'branch_id' => $branch_id,
    //                                 'branch_name' => $branch_name,
    //                                 'parent_id' => $mainRecord->id,
    //                                 'created_at' => now(),
    //                                 'updated_at' => now(),
    //                                 'additional_student' => null,
    //                                 'additional_info' => null,
    //                                 'session_type' => null,
    //                             ];
    //                         }
    //                         $currentDate = strtotime('+1 week', $currentDate);
    //                     }
    //                 } elseif (in_array($dayName, ['saturday', 'sunday'])) {
    //                     while ($currentDate <= $endDate) {
    //                         $dayOfWeek = strtolower(date('l', $currentDate));
    //                         if (in_array($dayOfWeek, ['saturday', 'sunday'])) {
    //                             $records[] = [
    //                                 'date' => date('Y-m-d', $currentDate),
    //                                 'slot' => $slot,
    //                                 'time_slot' => $timeSlotMapping[$slot]['weekend'] ?? 'Unknown Slot',
    //                                 'teacher_id' => $classData['teacher_id'] ?? null,
    //                                 'student_ids' => json_encode($classData['student_ids']),
    //                                 'student_names' => json_encode($classData['student_names']),
    //                                 'subjects' => json_encode($classData['subjects']),
    //                                 'year_in_schools' => json_encode($classData['year_in_schools']),
    //                                 'permanent' => json_encode($permanent),
    //                                 'is_attendance' => null, // No attendance for future records
    //                                 'branch_id' => $branch_id,
    //                                 'branch_name' => $branch_name,
    //                                 'parent_id' => $mainRecord->id,
    //                                 'created_at' => now(),
    //                                 'updated_at' => now(),
    //                                 'additional_student' => null,
    //                                 'additional_info' => null,
    //                                 'session_type' => null,
    //                             ];
    //                         }
    //                         $currentDate = strtotime('+1 week', $currentDate);
    //                     }
    //                 }
    //             }

    //             // Save future records
    //             foreach ($records as $recordData) {
    //                 \App\Models\GeneralTimetable::create($recordData);
    //             }
    //         }

    //         return response()->json(['success' => true, 'message' => 'Classes saved successfully']);
    //     } catch (\Exception $e) {
    //         \Log::error('Error saving timetable: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    //     }
    // }


    // backup
    // public function timeTableSchedularSave(Request $request)
    // {
    //     try {
    //         $data = $request->validate([
    //             'date' => 'required|date',
    //             'slot' => 'required|string',
    //             'classes' => 'required|array',
    //             'classes.*.class_id' => 'nullable|integer',
    //             'classes.*.teacher_id' => 'nullable|string',
    //             'classes.*.student_ids' => 'required|array',
    //             'classes.*.student_names' => 'required|array',
    //             'classes.*.subjects' => 'required|array',
    //             'classes.*.year_in_schools' => 'required|array',
    //             'temporary_moves' => 'nullable|array',
    //             'temporary_moves.*.family_id' => 'required',
    //             'temporary_moves.*.student_name' => 'required',
    //             'temporary_moves.*.old_class_id' => 'required',
    //             'temporary_moves.*.old_teacher' => 'required',
    //             'temporary_moves.*.old_subject' => 'nullable',
    //             'temporary_moves.*.permanent' => 'required|in:No',
    //         ]);

    //         $branch = \App\Models\User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
    //         $branch_id = $branch ? $branch->branch_id : session('branch_id');
    //         $branch_name = $branch ? $branch->branch_name : 'Unknown Branch';
    //         $date = $data['date'];
    //         $slot = $data['slot'];
    //         $dayName = strtolower(date('l', strtotime($date)));

    //         $timeSlotMapping = [
    //             '1' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '09:00 - 11:00am'],
    //             '2' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '11:20 - 01:20pm'],
    //             '3' => ['weekday' => '04:30 - 06:30pm', 'weekend' => '02:00 - 04:00pm'],
    //             '4' => ['weekday' => '06:45 - 08:45pm', 'weekend' => ''],
    //         ];

    //         $time_slot = ($dayName >= 1 && $dayName <= 5)
    //             ? ($timeSlotMapping[$slot]['weekday'] ?? 'Unknown')
    //             : ($timeSlotMapping[$slot]['weekend'] ?? 'Unknown');

    //         // Track which students are temporarily moved (for permanent = No)
    //         $tempMovedStudents = [];
    //         foreach ($data['temporary_moves'] ?? [] as $move) {
    //             $tempMovedStudents[] = [
    //                 'family_id' => $move['family_id'],
    //                 'name' => $move['student_name'],
    //                 'old_class_id' => $move['old_class_id'], // ✅ Store old class ID
    //             ];
    //         }

    //         foreach ($data['classes'] as $classData) {
    //             $studentCount = count($classData['student_ids']);
    //             $permanent = array_fill(0, $studentCount, 'Yes');

    //             $currentClassId = $classData['class_id'] ?? null;

    //             // ✅ First, preserve existing permanent='No' flags from database
    //             if (!empty($currentClassId)) {
    //                 $existingClass = \App\Models\GeneralTimetable::where('id', $currentClassId)
    //                     ->where('branch_id', session('branch_id'))
    //                     ->first();

    //                 if ($existingClass) {
    //                     $existingIds = json_decode($existingClass->student_ids, true) ?? [];
    //                     $existingNames = json_decode($existingClass->student_names, true) ?? [];
    //                     $existingPermanent = json_decode($existingClass->permanent, true) ?? [];

    //                     // Preserve permanent='No' for students that were already temporary
    //                     foreach ($classData['student_ids'] as $i => $id) {
    //                         $name = $classData['student_names'][$i];

    //                         // Find this student in existing record
    //                         foreach ($existingIds as $j => $existingId) {
    //                             if (
    //                                 $existingId == $id &&
    //                                 isset($existingNames[$j]) && $existingNames[$j] == $name &&
    //                                 isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No'
    //                             ) {
    //                                 // Student was already temporary, keep it temporary
    //                                 $permanent[$i] = 'No';
    //                                 break;
    //                             }
    //                         }
    //                     }
    //                 }
    //             }

    //             // ✅ Then, mark NEW temporary moved students as permanent = No ONLY in the NEW class (not in old class)
    //             foreach ($classData['student_ids'] as $i => $id) {
    //                 foreach ($tempMovedStudents as $moved) {
    //                     if ($id == $moved['family_id'] && $classData['student_names'][$i] == $moved['name']) {
    //                         // Only mark as permanent='No' if this is NOT the old class
    //                         if ($currentClassId != $moved['old_class_id']) {
    //                             $permanent[$i] = 'No';
    //                         }
    //                         break;
    //                     }
    //                 }
    //             }

    //             $mainRecordData = [
    //                 'date' => $date,
    //                 'slot' => $slot,
    //                 'time_slot' => $time_slot,
    //                 'teacher_id' => $classData['teacher_id'] ?? null,
    //                 'student_ids' => json_encode($classData['student_ids']),
    //                 'student_names' => json_encode($classData['student_names']),
    //                 'subjects' => json_encode($classData['subjects']),
    //                 'year_in_schools' => json_encode($classData['year_in_schools']),
    //                 'permanent' => json_encode($permanent),
    //                 'branch_id' => $branch_id,
    //                 'branch_name' => $branch_name,
    //                 'parent_id' => null,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];

    //             $mainRecord = null;
    //             if (!empty($classData['class_id'])) {
    //                 // $class = \App\Models\GeneralTimetable::find($classData['class_id']);
    //                 $class = \App\Models\GeneralTimetable::where('id', $classData['class_id'])
    //                     ->where('branch_id', session('branch_id'))
    //                     ->first();
    //                 if ($class) {
    //                     $mainRecordData['is_attendance'] = $class->is_attendance;
    //                     // \App\Models\GeneralTimetable::where('parent_id', $class->parent_id ?: $class->id)
    //                     //     ->whereDate('date', '>', $date)
    //                     //     ->delete();
    //                     \App\Models\GeneralTimetable::where('parent_id', $class->parent_id ?: $class->id)
    //                         ->where('branch_id', session('branch_id'))
    //                         ->whereDate('date', '>', $date)
    //                         ->delete();
    //                     $class->update($mainRecordData);
    //                     $mainRecord = $class;
    //                 }
    //             }

    //             if (!$mainRecord) {
    //                 $mainRecordData['is_attendance'] = json_encode(array_fill(0, $studentCount, 'No'));
    //                 $mainRecord = \App\Models\GeneralTimetable::create($mainRecordData);
    //             }

    //             if (empty($mainRecord->parent_id)) {
    //                 $mainRecord->update(['parent_id' => $mainRecord->id]);
    //             }

    //             // فیوچر ریکارڈز صرف تب بنائیں جب کوئی permanent student ہو
    //             if (!in_array('Yes', $permanent)) {
    //                 continue;
    //             }

    //             // فیوچر ریکارڈز بنائیں (صرف permanent والوں کے لیے)
    //             $currentDate = strtotime($date . ' +1 week');
    //             $endDate = strtotime('+1 year', $currentDate);
    //             $records = [];

    //             while ($currentDate <= $endDate) {
    //                 $futureDay = strtolower(date('l', $currentDate));
    //                 $isWeekday = in_array($futureDay, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
    //                 $isWeekend = in_array($futureDay, ['saturday', 'sunday']);

    //                 if (($isWeekday && in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) ||
    //                     ($isWeekend && in_array($dayName, ['saturday', 'sunday']))
    //                 ) {

    //                     // صرف permanent والے سٹوڈنٹس کو شامل کریں
    //                     $futureIds = [];
    //                     $futureNames = [];
    //                     $futureSubjects = [];
    //                     $futureYears = [];
    //                     $futurePermanent = [];

    //                     foreach ($classData['student_ids'] as $i => $id) {
    //                         if ($permanent[$i] === 'Yes') {
    //                             $futureIds[] = $id;
    //                             $futureNames[] = $classData['student_names'][$i];
    //                             $futureSubjects[] = $classData['subjects'][$i];
    //                             $futureYears[] = $classData['year_in_schools'][$i];
    //                             $futurePermanent[] = 'Yes';
    //                         }
    //                     }

    //                     if (!empty($futureIds)) {
    //                         $records[] = [
    //                             'date' => date('Y-m-d', $currentDate),
    //                             'slot' => $slot,
    //                             'time_slot' => $isWeekday ? ($timeSlotMapping[$slot]['weekday'] ?? '') : ($timeSlotMapping[$slot]['weekend'] ?? ''),
    //                             'teacher_id' => $classData['teacher_id'] ?? null,
    //                             'student_ids' => json_encode($futureIds),
    //                             'student_names' => json_encode($futureNames),
    //                             'subjects' => json_encode($futureSubjects),
    //                             'year_in_schools' => json_encode($futureYears),
    //                             'permanent' => json_encode($futurePermanent),
    //                             'is_attendance' => null,
    //                             'branch_id' => $branch_id,
    //                             'branch_name' => $branch_name,
    //                             'parent_id' => $mainRecord->id,
    //                             'created_at' => now(),
    //                             'updated_at' => now(),
    //                         ];
    //                     }
    //                 }
    //                 $currentDate = strtotime('+1 week', $currentDate);
    //             }

    //             foreach ($records as $rec) {
    //                 \App\Models\GeneralTimetable::create($rec);
    //             }
    //         }

    //         // ✅ پرانے فیوچر ریکارڈز میں سٹوڈنٹ واپس ڈالیں (with complete data)
    //         foreach ($data['temporary_moves'] ?? [] as $move) {
    //             $oldClass = \App\Models\GeneralTimetable::where('id', $move['old_class_id'])
    //                 ->where('branch_id', session('branch_id'))
    //                 ->first();
    //             if (!$oldClass) continue;

    //             $parentId = $oldClass->parent_id ?: $oldClass->id;

    //             // Get student's year_in_school from database
    //             $studentData = \DB::table('studentdata')
    //                 ->where('branch_id', session('branch_id'))
    //                 ->where('admissionid', $move['family_id'])
    //                 ->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(COALESCE(studentsur, ''))) = ?", [trim($move['student_name'])])
    //                 ->select('studentyearinschool')
    //                 ->first();

    //             $yearInSchool = $studentData ? $studentData->studentyearinschool : '';

    //             // Update all future records to include this student
    //             $futureRecords = \App\Models\GeneralTimetable::where('parent_id', $parentId)
    //                 ->where('branch_id', session('branch_id'))
    //                 ->where('date', '>', $date)
    //                 ->get();

    //             foreach ($futureRecords as $record) {
    //                 $ids = json_decode($record->student_ids, true) ?? [];
    //                 $names = json_decode($record->student_names, true) ?? [];
    //                 $subjects = json_decode($record->subjects, true) ?? [];
    //                 $years = json_decode($record->year_in_schools, true) ?? [];
    //                 $permanentFlags = json_decode($record->permanent, true) ?? [];

    //                 // Only add if student is not already in the record
    //                 if (!in_array($move['family_id'], $ids)) {
    //                     $ids[] = $move['family_id'];
    //                     $names[] = $move['student_name'];
    //                     $subjects[] = $move['old_subject'] ?? '';
    //                     $years[] = $yearInSchool;
    //                     $permanentFlags[] = 'Yes'; // Student is permanent in their original class

    //                     $record->update([
    //                         'student_ids' => json_encode($ids),
    //                         'student_names' => json_encode($names),
    //                         'subjects' => json_encode($subjects),
    //                         'year_in_schools' => json_encode($years),
    //                         'permanent' => json_encode($permanentFlags),
    //                         'updated_at' => now(),
    //                     ]);
    //                 }
    //             }
    //         }

    //         return response()->json(['success' => true, 'message' => 'Classes saved successfully']);
    //     } catch (\Exception $e) {
    //         \Log::error('Timetable Save Error: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    //     }
    // }


    public function timeTableSchedularSave(Request $request)
    {
        try {
            $data = $request->validate([
                'date' => 'required|date',
                'slot' => 'required|string',
                'classes' => 'required|array',
                'classes.*.class_id' => 'nullable|integer',
                'classes.*.teacher_id' => 'nullable|string',
                'classes.*.student_ids' => 'nullable|array',
                'classes.*.student_names' => 'nullable|array',
                'classes.*.subjects' => 'nullable|array',
                'classes.*.year_in_schools' => 'nullable|array',
                'temporary_moves' => 'nullable|array',
                'temporary_moves.*.family_id' => 'required',
                'temporary_moves.*.student_name' => 'required',
                'temporary_moves.*.old_class_id' => 'required',
                'temporary_moves.*.old_teacher' => 'required',
                'temporary_moves.*.old_subject' => 'nullable',
                'temporary_moves.*.permanent' => 'required|in:No',
            ]);

            $branch = \App\Models\User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
            $branch_id = $branch ? $branch->branch_id : session('branch_id');
            $branch_name = $branch ? $branch->branch_name : 'Unknown Branch';
            $date = $data['date'];
            $slot = $data['slot'];
            $dayName = strtolower(date('l', strtotime($date)));

            $timeSlotMapping = [
                '1' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '09:00 - 11:00am'],
                '2' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '11:20 - 01:20pm'],
                '3' => ['weekday' => '04:30 - 06:30pm', 'weekend' => '02:00 - 04:00pm'],
                '4' => ['weekday' => '06:45 - 08:45pm', 'weekend' => ''],
                // Term break dedicated slots
                '7' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '11:00 - 01:00pm'],
                '8' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '01:30 - 03:30pm'],
            ];
            $isTermBreakSlot = in_array((string)$slot, ['7', '8'], true);
            $sessionTypeForSave = $isTermBreakSlot ? 'termbreak_scheduler' : null;

            $time_slot = ($dayName >= 1 && $dayName <= 5)
                ? ($timeSlotMapping[$slot]['weekday'] ?? 'Unknown')
                : ($timeSlotMapping[$slot]['weekend'] ?? 'Unknown');

            // Track which students are temporarily moved (for permanent = No)
            $tempMovedStudents = [];
            foreach ($data['temporary_moves'] ?? [] as $move) {
                $tempMovedStudents[] = [
                    'family_id' => $move['family_id'],
                    'name' => $move['student_name'],
                    'old_class_id' => $move['old_class_id'], // ✅ Store old class ID
                ];
            }

            foreach ($data['classes'] as $classData) {
                // Ensure arrays are not null, default to empty arrays
                $studentIds = $classData['student_ids'] ?? [];
                $studentNames = $classData['student_names'] ?? [];
                $subjects = $classData['subjects'] ?? [];
                $yearInSchools = $classData['year_in_schools'] ?? [];

                $studentCount = count($studentIds);
                $permanent = $studentCount > 0 ? array_fill(0, $studentCount, 'Yes') : [];

                $currentClassId = $classData['class_id'] ?? null;

                // ✅ First, preserve existing permanent='No' flags and move_from_page students from database
                if (!empty($currentClassId)) {
                    $existingClass = \App\Models\GeneralTimetable::where('id', $currentClassId)
                        ->where('branch_id', session('branch_id'))
                        ->first();

                    if ($existingClass) {
                        $existingIds = is_array($existingClass->student_ids)
                            ? $existingClass->student_ids
                            : (is_string($existingClass->student_ids) ? (json_decode($existingClass->student_ids, true) ?? []) : []);
                        $existingNames = is_array($existingClass->student_names)
                            ? $existingClass->student_names
                            : (is_string($existingClass->student_names) ? (json_decode($existingClass->student_names, true) ?? []) : []);
                        $existingSubjects = is_array($existingClass->subjects)
                            ? $existingClass->subjects
                            : (is_string($existingClass->subjects) ? (json_decode($existingClass->subjects, true) ?? []) : []);
                        $existingYearInSchools = is_array($existingClass->year_in_schools)
                            ? $existingClass->year_in_schools
                            : (is_string($existingClass->year_in_schools) ? (json_decode($existingClass->year_in_schools, true) ?? []) : []);
                        $existingPermanent = is_array($existingClass->permanent)
                            ? $existingClass->permanent
                            : (is_string($existingClass->permanent) ? (json_decode($existingClass->permanent, true) ?? []) : []);

                        // Preserve move_from_page column
                        $preservedMoveFromPage = $existingClass->move_from_page;
                        $existingAttendance = is_array($existingClass->is_attendance)
                            ? $existingClass->is_attendance
                            : (is_string($existingClass->is_attendance) ? (json_decode($existingClass->is_attendance, true) ?? []) : []);

                        // Parse move_from_page to get students that should be restored
                        $movedStudentsToRestore = [];
                        if ($preservedMoveFromPage) {
                            $movedStudentsList = array_map('trim', explode(',', $preservedMoveFromPage));
                            foreach ($movedStudentsList as $movedStudent) {
                                if (!empty($movedStudent)) {
                                    $movedStudentsToRestore[] = trim($movedStudent);
                                }
                            }
                        }

                        // Add back students from move_from_page that are not in current save data
                        // Restore ALL their original details (subject, permanent status, attendance status, etc.)
                        foreach ($movedStudentsToRestore as $movedStudent) {
                            $movedParts = explode(' ', $movedStudent, 2);
                            if (count($movedParts) >= 2) {
                                $movedId = trim($movedParts[0]);
                                $movedName = trim($movedParts[1]);
                                $movedFullName = $movedId . ' ' . $movedName;

                                // Check if this student is already in current save data
                                $alreadyExists = false;
                                foreach ($studentIds as $idx => $currentId) {
                                    $currentName = $studentNames[$idx] ?? '';
                                    $currentFullName = $currentId . ' ' . $currentName;
                                    if ($currentFullName === $movedFullName) {
                                        $alreadyExists = true;
                                        break;
                                    }
                                }

                                // If not in current save data, restore from existing class with ALL details
                                if (!$alreadyExists) {
                                    $studentRestored = false;
                                    foreach ($existingIds as $j => $existingId) {
                                        $existingName = $existingNames[$j] ?? '';
                                        $existingFullName = $existingId . ' ' . $existingName;

                                        if ($existingFullName === $movedFullName) {
                                            // Restore this student with ALL original details
                                            $studentIds[] = $existingId;
                                            $studentNames[] = $existingName;
                                            $subjects[] = $existingSubjects[$j] ?? '';
                                            $yearInSchools[] = $existingYearInSchools[$j] ?? '';
                                            $permanent[] = $existingPermanent[$j] ?? 'Yes';
                                            // Restore attendance status if available
                                            if (isset($existingAttendance[$j])) {
                                                // We'll handle attendance later when creating the record
                                            }
                                            $studentCount++;
                                            $studentRestored = true;
                                            break;
                                        }
                                    }

                                    // If student not found in existing class, try to get basic info from database
                                    if (!$studentRestored) {
                                        $studentData = DB::table('studentdata')
                                            ->where('branch_id', session('branch_id'))
                                            ->where('admissionid', $movedId)
                                            ->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(COALESCE(studentsur, ''))) LIKE ?", ['%' . $movedName . '%'])
                                            ->select('studentyearinschool', 'subject_names')
                                            ->first();

                                        if ($studentData) {
                                            $studentIds[] = $movedId;
                                            $studentNames[] = $movedName;
                                            // Try to get subject from student's subject_names or use empty
                                            $studentSubjects = $studentData->subject_names ? json_decode($studentData->subject_names, true) : [];
                                            $subjects[] = !empty($studentSubjects) ? $studentSubjects[0] : '';
                                            $yearInSchools[] = $studentData->studentyearinschool ?? '';
                                            $permanent[] = 'Yes'; // Default to permanent when restoring
                                            $studentCount++;
                                        }
                                    }
                                }
                            }
                        }

                        // Preserve permanent='No' for students that were already temporary
                        if ($studentCount > 0) {
                            foreach ($studentIds as $i => $id) {
                                $name = $studentNames[$i] ?? '';

                                // Find this student in existing record
                                foreach ($existingIds as $j => $existingId) {
                                    if (
                                        $existingId == $id &&
                                        isset($existingNames[$j]) && $existingNames[$j] == $name &&
                                        isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No'
                                    ) {
                                        // Student was already temporary, keep it temporary
                                        $permanent[$i] = 'No';
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                // ✅ Then, mark NEW temporary moved students as permanent = No ONLY in the NEW class (not in old class)
                if ($studentCount > 0) {
                    foreach ($studentIds as $i => $id) {
                        foreach ($tempMovedStudents as $moved) {
                            if ($id == $moved['family_id'] && ($studentNames[$i] ?? '') == $moved['name']) {
                                // Only mark as permanent='No' if this is NOT the old class
                                if ($currentClassId != $moved['old_class_id']) {
                                    $permanent[$i] = 'No';
                                }
                                break;
                            }
                        }
                    }
                }

                // Preserve move_from_page if updating existing class - keep it intact
                $moveFromPage = null;
                $restoredAttendance = null;
                if (!empty($currentClassId)) {
                    $existingClassForMoveFromPage = \App\Models\GeneralTimetable::where('id', $currentClassId)
                        ->where('branch_id', session('branch_id'))
                        ->first();
                    if ($existingClassForMoveFromPage) {
                        // Keep move_from_page intact - don't remove students from it
                        if ($existingClassForMoveFromPage->move_from_page) {
                            $moveFromPage = $existingClassForMoveFromPage->move_from_page;
                        }

                        // Preserve attendance data for restored students
                        $existingAttendanceFull = is_array($existingClassForMoveFromPage->is_attendance)
                            ? $existingClassForMoveFromPage->is_attendance
                            : (is_string($existingClassForMoveFromPage->is_attendance) ? (json_decode($existingClassForMoveFromPage->is_attendance, true) ?? []) : []);
                        if (!empty($existingAttendanceFull)) {
                            // Build attendance array for all students (current + restored)
                            $restoredAttendance = [];
                            foreach ($studentIds as $idx => $studentId) {
                                $studentName = $studentNames[$idx] ?? '';
                                $studentFullName = $studentId . ' ' . $studentName;

                                // Try to find attendance from existing class
                                $foundAttendance = null;
                                $existingIdsForAtt = is_array($existingClassForMoveFromPage->student_ids)
                                    ? $existingClassForMoveFromPage->student_ids
                                    : (is_string($existingClassForMoveFromPage->student_ids) ? (json_decode($existingClassForMoveFromPage->student_ids, true) ?? []) : []);
                                $existingNamesForAtt = is_array($existingClassForMoveFromPage->student_names)
                                    ? $existingClassForMoveFromPage->student_names
                                    : (is_string($existingClassForMoveFromPage->student_names) ? (json_decode($existingClassForMoveFromPage->student_names, true) ?? []) : []);

                                foreach ($existingIdsForAtt as $attIdx => $existingIdForAtt) {
                                    $existingNameForAtt = $existingNamesForAtt[$attIdx] ?? '';
                                    $existingFullNameForAtt = $existingIdForAtt . ' ' . $existingNameForAtt;

                                    if ($studentFullName === $existingFullNameForAtt) {
                                        $foundAttendance = $existingAttendanceFull[$attIdx] ?? 'No';
                                        break;
                                    }
                                }

                                // If not found in existing, default to 'No'
                                $restoredAttendance[] = $foundAttendance ?? 'No';
                            }
                        }
                    }
                }

                $mainRecordData = [
                    'date' => $date,
                    'slot' => $slot,
                    'time_slot' => $time_slot,
                    'teacher_id' => $classData['teacher_id'] ?? null,
                    'student_ids' => json_encode($studentIds),
                    'student_names' => json_encode($studentNames),
                    'subjects' => json_encode($subjects),
                    'year_in_schools' => json_encode($yearInSchools),
                    'permanent' => json_encode($permanent),
                    'move_from_page' => $moveFromPage, // Preserve move_from_page column
                    'branch_id' => $branch_id,
                    'branch_name' => $branch_name,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $mainRecord = null;

                // ============================================================
                // NEW SOLUTION - DELETE OLD CLASSES AND CREATE NEW ONES
                // ============================================================
                // Always delete existing classes and create new ones instead of updating

                // Step 1: Delete existing class by class_id if provided
                if (!empty($classData['class_id'])) {
                    $existingClass = \App\Models\GeneralTimetable::where('id', $classData['class_id'])
                        ->where('branch_id', session('branch_id'))
                        ->first();

                    if ($existingClass) {
                        // Store attendance data if exists
                        $preservedAttendance = $existingClass->is_attendance;

                        // Delete future records for this class
                        \App\Models\GeneralTimetable::where('parent_id', $existingClass->parent_id ?: $existingClass->id)
                            ->where('branch_id', session('branch_id'))
                            ->whereDate('date', '>', $date)
                            ->delete();

                        // Delete the existing class itself
                        $existingClass->delete();

                        // Preserve attendance in mainRecordData if it existed
                        if ($preservedAttendance) {
                            $mainRecordData['is_attendance'] = $preservedAttendance;
                        }
                    }
                }

                // Step 2: Also delete any existing class with same teacher, date, slot
                // (in case class_id was not provided or different)
                if (!empty($classData['teacher_id'])) {
                    $existingClasses = \App\Models\GeneralTimetable::where('date', $date)
                        ->where('slot', $slot)
                        ->where('teacher_id', $classData['teacher_id'])
                        ->where('branch_id', $branch_id)
                        ->get();

                    foreach ($existingClasses as $existingClass) {
                        // Store attendance data if exists (only for first one found and not already set)
                        if (empty($mainRecordData['is_attendance']) && $existingClass->is_attendance) {
                            $mainRecordData['is_attendance'] = $existingClass->is_attendance;
                        }

                        // Delete future records for this existing class
                        \App\Models\GeneralTimetable::where('parent_id', $existingClass->parent_id ?: $existingClass->id)
                            ->where('branch_id', session('branch_id'))
                            ->whereDate('date', '>', $date)
                            ->delete();

                        // Delete the existing class itself
                        $existingClass->delete();
                    }
                }

                // Step 3: Always create new class (never update)
                // Use restored attendance if available, otherwise use preserved or default
                if ($restoredAttendance !== null) {
                    $mainRecordData['is_attendance'] = json_encode($restoredAttendance);
                } else {
                    $mainRecordData['is_attendance'] = $mainRecordData['is_attendance'] ??
                        ($studentCount > 0 ? json_encode(array_fill(0, $studentCount, 'No')) : null);
                }
                $mainRecord = \App\Models\GeneralTimetable::create($mainRecordData);
                // ============================================================

                if (empty($mainRecord->parent_id)) {
                    $mainRecord->update(['parent_id' => $mainRecord->id]);
                }

                // فیوچر ریکارڈز صرف تب بنائیں جب کوئی permanent student ہو
                if (!in_array('Yes', $permanent)) {
                    continue;
                }

                // فیوچر ریکارڈز بنائیں (صرف permanent والوں کے لیے)
                $currentDate = strtotime($date . ' +1 week');
                $endDate = strtotime('+1 year', $currentDate);
                $records = [];

                while ($currentDate <= $endDate) {
                    $futureDay = strtolower(date('l', $currentDate));
                    $isWeekday = in_array($futureDay, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                    $isWeekend = in_array($futureDay, ['saturday', 'sunday']);

                    if (($isWeekday && in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) ||
                        ($isWeekend && in_array($dayName, ['saturday', 'sunday']))
                    ) {

                        // صرف permanent والے سٹوڈنٹس کو شامل کریں
                        $futureIds = [];
                        $futureNames = [];
                        $futureSubjects = [];
                        $futureYears = [];
                        $futurePermanent = [];

                        foreach ($studentIds as $i => $id) {
                            if (isset($permanent[$i]) && $permanent[$i] === 'Yes') {
                                $futureIds[] = $id;
                                $futureNames[] = $studentNames[$i] ?? '';
                                $futureSubjects[] = $subjects[$i] ?? '';
                                $futureYears[] = $yearInSchools[$i] ?? '';
                                $futurePermanent[] = 'Yes';
                            }
                        }

                        if (!empty($futureIds)) {
                            $records[] = [
                                'date' => date('Y-m-d', $currentDate),
                                'slot' => $slot,
                                'time_slot' => $isWeekday ? ($timeSlotMapping[$slot]['weekday'] ?? '') : ($timeSlotMapping[$slot]['weekend'] ?? ''),
                                'teacher_id' => $classData['teacher_id'] ?? null,
                                'student_ids' => json_encode($futureIds),
                                'student_names' => json_encode($futureNames),
                                'subjects' => json_encode($futureSubjects),
                                'year_in_schools' => json_encode($futureYears),
                                'permanent' => json_encode($futurePermanent),
                                'is_attendance' => null,
                                'branch_id' => $branch_id,
                                'branch_name' => $branch_name,
                                'parent_id' => $mainRecord->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                    $currentDate = strtotime('+1 week', $currentDate);
                }

                foreach ($records as $rec) {
                    \App\Models\GeneralTimetable::create($rec);
                }
            }

            // ✅ پرانے فیوچر ریکارڈز میں سٹوڈنٹ واپس ڈالیں (with complete data)
            foreach ($data['temporary_moves'] ?? [] as $move) {
                $oldClass = \App\Models\GeneralTimetable::where('id', $move['old_class_id'])
                    ->where('branch_id', session('branch_id'))
                    ->first();
                if (!$oldClass) continue;

                $parentId = $oldClass->parent_id ?: $oldClass->id;

                // Get student's year_in_school from database
                $studentData = \DB::table('studentdata')
                    ->where('branch_id', session('branch_id'))
                    ->where('admissionid', $move['family_id'])
                    ->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(COALESCE(studentsur, ''))) = ?", [trim($move['student_name'])])
                    ->select('studentyearinschool')
                    ->first();

                $yearInSchool = $studentData ? $studentData->studentyearinschool : '';

                // Update all future records to include this student
                $futureRecords = \App\Models\GeneralTimetable::where('parent_id', $parentId)
                    ->where('branch_id', session('branch_id'))
                    ->where('date', '>', $date)
                    ->get();

                foreach ($futureRecords as $record) {
                    $ids = json_decode($record->student_ids, true) ?? [];
                    $names = json_decode($record->student_names, true) ?? [];
                    $subjects = json_decode($record->subjects, true) ?? [];
                    $years = json_decode($record->year_in_schools, true) ?? [];
                    $permanentFlags = json_decode($record->permanent, true) ?? [];

                    // Only add if student is not already in the record
                    if (!in_array($move['family_id'], $ids)) {
                        $ids[] = $move['family_id'];
                        $names[] = $move['student_name'];
                        $subjects[] = $move['old_subject'] ?? '';
                        $years[] = $yearInSchool;
                        $permanentFlags[] = 'Yes'; // Student is permanent in their original class

                        $record->update([
                            'student_ids' => json_encode($ids),
                            'student_names' => json_encode($names),
                            'subjects' => json_encode($subjects),
                            'year_in_schools' => json_encode($years),
                            'permanent' => json_encode($permanentFlags),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Classes saved successfully']);
        } catch (\Exception $e) {
            \Log::error('Timetable Save Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function timeTableSchedularSaveSingle(Request $request)
    {
       
        try {
            $data = $request->validate([
                'date' => 'required|date',
                'slot' => 'required|string',
                'class_id' => 'nullable|integer',
                'teacher_id' => 'nullable|string',
                'student_ids' => 'nullable|array',
                'student_names' => 'nullable|array',
                'subjects' => 'nullable|array',
                'year_in_schools' => 'nullable|array',
                'temporary_moves' => 'nullable|array',
                'temporary_moves.*.family_id' => 'required',
                'temporary_moves.*.student_name' => 'required',
                'temporary_moves.*.old_class_id' => 'required',
                'temporary_moves.*.old_teacher' => 'required',
                'temporary_moves.*.old_subject' => 'nullable',
                'temporary_moves.*.permanent' => 'required|in:No',
            ]);

            $branch = \App\Models\User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
            $branch_id = $branch ? $branch->branch_id : session('branch_id');
            $branch_name = $branch ? $branch->branch_name : 'Unknown Branch';
            $date = $data['date'];
            $slot = $data['slot'];
            $dayName = strtolower(date('l', strtotime($date)));

            $timeSlotMapping = [
                '1' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '09:00 - 11:00am'],
                '2' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '11:20 - 01:20pm'],
                '3' => ['weekday' => '04:30 - 06:30pm', 'weekend' => '02:00 - 04:00pm'],
                '4' => ['weekday' => '06:45 - 08:45pm', 'weekend' => ''],
                // term break scheduler slots
                '7' => ['weekday' => '11:00 - 01:00pm', 'weekend' => '11:00 - 01:00pm'],
                '8' => ['weekday' => '01:30 - 03:30pm', 'weekend' => '01:30 - 03:30pm'],
            ];
            $isTermBreakSlot = in_array((string)$slot, ['7', '8'], true);
            $sessionTypeForSave = $isTermBreakSlot ? 'termbreak_scheduler' : null;

            $time_slot = ($dayName >= 1 && $dayName <= 5)
                ? ($timeSlotMapping[$slot]['weekday'] ?? 'Unknown')
                : ($timeSlotMapping[$slot]['weekend'] ?? 'Unknown');

            // Track which students are temporarily moved (for permanent = No)
            $tempMovedStudents = [];
            foreach ($data['temporary_moves'] ?? [] as $move) {
                $tempMovedStudents[] = [
                    'family_id' => $move['family_id'],
                    'name' => $move['student_name'],
                    'old_class_id' => $move['old_class_id'],
                ];
            }

            // Ensure arrays are not null, default to empty arrays
            $studentIds = $data['student_ids'] ?? [];
            $studentNames = $data['student_names'] ?? [];
            $subjects = $data['subjects'] ?? [];
            $yearInSchools = $data['year_in_schools'] ?? [];

            $studentCount = count($studentIds);
            $permanent = $studentCount > 0 ? array_fill(0, $studentCount, 'Yes') : [];

            $currentClassId = $data['class_id'] ?? null;

            // ✅ First, preserve existing permanent='No' flags and move_from_page students from database
            if (!empty($currentClassId)) {
                $existingClass = \App\Models\GeneralTimetable::where('id', $currentClassId)
                    ->where('branch_id', session('branch_id'))
                    ->first();

                if ($existingClass) {
                    $existingIds = is_array($existingClass->student_ids)
                        ? $existingClass->student_ids
                        : (is_string($existingClass->student_ids) ? (json_decode($existingClass->student_ids, true) ?? []) : []);
                    $existingNames = is_array($existingClass->student_names)
                        ? $existingClass->student_names
                        : (is_string($existingClass->student_names) ? (json_decode($existingClass->student_names, true) ?? []) : []);
                    $existingSubjects = is_array($existingClass->subjects)
                        ? $existingClass->subjects
                        : (is_string($existingClass->subjects) ? (json_decode($existingClass->subjects, true) ?? []) : []);
                    $existingYearInSchools = is_array($existingClass->year_in_schools)
                        ? $existingClass->year_in_schools
                        : (is_string($existingClass->year_in_schools) ? (json_decode($existingClass->year_in_schools, true) ?? []) : []);
                    $existingPermanent = is_array($existingClass->permanent)
                        ? $existingClass->permanent
                        : (is_string($existingClass->permanent) ? (json_decode($existingClass->permanent, true) ?? []) : []);

                    // Preserve move_from_page column
                    $preservedMoveFromPage = $existingClass->move_from_page;
                    $existingAttendance = is_array($existingClass->is_attendance)
                        ? $existingClass->is_attendance
                        : (is_string($existingClass->is_attendance) ? (json_decode($existingClass->is_attendance, true) ?? []) : []);

                    // Parse move_from_page to get students that should be restored
                    $movedStudentsToRestore = [];
                    if ($preservedMoveFromPage) {
                        $movedStudentsList = array_map('trim', explode(',', $preservedMoveFromPage));
                        foreach ($movedStudentsList as $movedStudent) {
                            if (!empty($movedStudent)) {
                                $movedStudentsToRestore[] = trim($movedStudent);
                            }
                        }
                    }

                    // Add back students from move_from_page that are not in current save data
                    foreach ($movedStudentsToRestore as $movedStudent) {
                        $movedParts = explode(' ', $movedStudent, 2);
                        if (count($movedParts) >= 2) {
                            $movedId = trim($movedParts[0]);
                            $movedName = trim($movedParts[1]);
                            $movedFullName = $movedId . ' ' . $movedName;

                            // Check if this student is already in current save data
                            $alreadyExists = false;
                            foreach ($studentIds as $idx => $currentId) {
                                $currentName = $studentNames[$idx] ?? '';
                                $currentFullName = $currentId . ' ' . $currentName;
                                if ($currentFullName === $movedFullName) {
                                    $alreadyExists = true;
                                    break;
                                }
                            }

                            // If not in current save data, restore from existing class with ALL details
                            if (!$alreadyExists) {
                                $studentRestored = false;
                                foreach ($existingIds as $j => $existingId) {
                                    $existingName = $existingNames[$j] ?? '';
                                    $existingFullName = $existingId . ' ' . $existingName;

                                    if ($existingFullName === $movedFullName) {
                                        // Restore this student with ALL original details
                                        $studentIds[] = $existingId;
                                        $studentNames[] = $existingName;
                                        $subjects[] = $existingSubjects[$j] ?? '';
                                        $yearInSchools[] = $existingYearInSchools[$j] ?? '';
                                        $permanent[] = $existingPermanent[$j] ?? 'Yes';
                                        $studentCount++;
                                        $studentRestored = true;
                                        break;
                                    }
                                }

                                // If student not found in existing class, try to get basic info from database
                                if (!$studentRestored) {
                                    $studentData = DB::table('studentdata')
                                        ->where('branch_id', session('branch_id'))
                                        ->where('admissionid', $movedId)
                                        ->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(COALESCE(studentsur, ''))) LIKE ?", ['%' . $movedName . '%'])
                                        ->select('studentyearinschool', 'subject_names')
                                        ->first();

                                    if ($studentData) {
                                        $studentIds[] = $movedId;
                                        $studentNames[] = $movedName;
                                        $studentSubjects = $studentData->subject_names ? json_decode($studentData->subject_names, true) : [];
                                        $subjects[] = !empty($studentSubjects) ? $studentSubjects[0] : '';
                                        $yearInSchools[] = $studentData->studentyearinschool ?? '';
                                        $permanent[] = 'Yes';
                                        $studentCount++;
                                    }
                                }
                            }
                        }
                    }

                    // Preserve permanent='No' for students that were already temporary
                    if ($studentCount > 0) {
                        foreach ($studentIds as $i => $id) {
                            $name = $studentNames[$i] ?? '';

                            // Find this student in existing record
                            foreach ($existingIds as $j => $existingId) {
                                if (
                                    $existingId == $id &&
                                    isset($existingNames[$j]) && $existingNames[$j] == $name &&
                                    isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No'
                                ) {
                                    // Student was already temporary, keep it temporary
                                    $permanent[$i] = 'No';
                                    break;
                                }
                            }
                        }
                    }

                    // ✅ CRITICAL FIX: Add back ALL switch students (permanent='No') from existing class
                    // that are NOT in the current save data - these are from central-timetable
                    foreach ($existingIds as $j => $existingId) {
                        $existingName = $existingNames[$j] ?? '';
                        $existingFullName = $existingId . ' ' . $existingName;

                        // Check if this student is a switch student (permanent='No')
                        $isSwitchStudent = isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No';

                        if ($isSwitchStudent) {
                            // Check if this switch student is already in current save data
                            $alreadyInCurrentData = false;
                            foreach ($studentIds as $idx => $currentId) {
                                $currentName = $studentNames[$idx] ?? '';
                                $currentFullName = $currentId . ' ' . $currentName;
                                if ($currentFullName === $existingFullName) {
                                    $alreadyInCurrentData = true;
                                    break;
                                }
                            }

                            // If switch student is NOT in current save data, add them back
                            if (!$alreadyInCurrentData) {
                                $studentIds[] = $existingId;
                                $studentNames[] = $existingName;
                                $subjects[] = $existingSubjects[$j] ?? '';
                                $yearInSchools[] = $existingYearInSchools[$j] ?? '';
                                $permanent[] = 'No'; // Preserve switch status
                                $studentCount++;
                            }
                        }
                    }
                }
            }

            // ✅ Then, mark NEW temporary moved students as permanent = No ONLY in the NEW class (not in old class)
            // ✅ IMPORTANT: Do NOT overwrite existing switch students (permanent='No') - they should remain as 'No'
            if ($studentCount > 0) {
                foreach ($studentIds as $i => $id) {
                    // ✅ Check if this student is already a switch student - if so, preserve it and skip
                    $isAlreadySwitchStudent = isset($permanent[$i]) && $permanent[$i] === 'No';

                    // Only process if student is NOT already a switch student
                    if (!$isAlreadySwitchStudent) {
                        foreach ($tempMovedStudents as $moved) {
                            if ($id == $moved['family_id'] && ($studentNames[$i] ?? '') == $moved['name']) {
                                // Only mark as permanent='No' if this is NOT the old class
                                if ($currentClassId != $moved['old_class_id']) {
                                    $permanent[$i] = 'No';
                                }
                                break;
                            }
                        }
                    }
                }
            }

            // Preserve move_from_page if updating existing class - keep it intact
            $moveFromPage = null;
            $restoredAttendance = null;
            if (!empty($currentClassId)) {
                $existingClassForMoveFromPage = \App\Models\GeneralTimetable::where('id', $currentClassId)
                    ->where('branch_id', session('branch_id'))
                    ->first();
                if ($existingClassForMoveFromPage) {
                    // Keep move_from_page intact - don't remove students from it
                    if ($existingClassForMoveFromPage->move_from_page) {
                        $moveFromPage = $existingClassForMoveFromPage->move_from_page;
                    }

                    // Preserve attendance data for restored students
                    $existingAttendanceFull = is_array($existingClassForMoveFromPage->is_attendance)
                        ? $existingClassForMoveFromPage->is_attendance
                        : (is_string($existingClassForMoveFromPage->is_attendance) ? (json_decode($existingClassForMoveFromPage->is_attendance, true) ?? []) : []);
                    if (!empty($existingAttendanceFull)) {
                        // Build attendance array for all students (current + restored)
                        $restoredAttendance = [];
                        foreach ($studentIds as $idx => $studentId) {
                            $studentName = $studentNames[$idx] ?? '';
                            $studentFullName = $studentId . ' ' . $studentName;

                            // Try to find attendance from existing class
                            $foundAttendance = null;
                            $existingIdsForAtt = is_array($existingClassForMoveFromPage->student_ids)
                                ? $existingClassForMoveFromPage->student_ids
                                : (is_string($existingClassForMoveFromPage->student_ids) ? (json_decode($existingClassForMoveFromPage->student_ids, true) ?? []) : []);
                            $existingNamesForAtt = is_array($existingClassForMoveFromPage->student_names)
                                ? $existingClassForMoveFromPage->student_names
                                : (is_string($existingClassForMoveFromPage->student_names) ? (json_decode($existingClassForMoveFromPage->student_names, true) ?? []) : []);

                            foreach ($existingIdsForAtt as $attIdx => $existingIdForAtt) {
                                $existingNameForAtt = $existingNamesForAtt[$attIdx] ?? '';
                                $existingFullNameForAtt = $existingIdForAtt . ' ' . $existingNameForAtt;

                                if ($studentFullName === $existingFullNameForAtt) {
                                    $foundAttendance = $existingAttendanceFull[$attIdx] ?? 'No';
                                    break;
                                }
                            }

                            // If not found in existing, default to 'No'
                            $restoredAttendance[] = $foundAttendance ?? 'No';
                        }
                    }
                }
            }

            $mainRecordData = [
                'date' => $date,
                'slot' => $slot,
                'time_slot' => $time_slot,
                'teacher_id' => $data['teacher_id'] ?? null,
                'student_ids' => json_encode($studentIds),
                'student_names' => json_encode($studentNames),
                'subjects' => json_encode($subjects),
                'year_in_schools' => json_encode($yearInSchools),
                'permanent' => json_encode($permanent),
                'move_from_page' => $moveFromPage,
                'branch_id' => $branch_id,
                'branch_name' => $branch_name,
                'parent_id' => null,
                'session_type' => $sessionTypeForSave,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $mainRecord = null;

            // Delete existing class and create new one
            // Step 1: Delete existing class by class_id if provided
            if (!empty($data['class_id'])) {
                $existingClass = \App\Models\GeneralTimetable::where('id', $data['class_id'])
                    ->where('branch_id', session('branch_id'))
                    ->first();

                if ($existingClass) {
                    // Store attendance data if exists
                    $preservedAttendance = $existingClass->is_attendance;

                    // Delete future records for this class
                    \App\Models\GeneralTimetable::where('parent_id', $existingClass->parent_id ?: $existingClass->id)
                        ->where('branch_id', session('branch_id'))
                        ->whereDate('date', '>', $date)
                        ->delete();

                    // Delete the existing class itself
                    $existingClass->delete();

                    // Preserve attendance in mainRecordData if it existed
                    if ($preservedAttendance) {
                        $mainRecordData['is_attendance'] = $preservedAttendance;
                    }
                }
            }

            // Step 2: Also delete any existing class with same teacher, date, slot
            // ✅ CRITICAL FIX: Preserve switch students from these classes BEFORE deletion
            if (!empty($data['teacher_id'])) {
                $existingClasses = \App\Models\GeneralTimetable::where('date', $date)
                    ->where('slot', $slot)
                    ->where('teacher_id', $data['teacher_id'])
                    ->where('branch_id', $branch_id)
                    ->get();

                // First, preserve switch students from all existing classes before deletion
                foreach ($existingClasses as $existingClass) {
                    $existingIds = is_array($existingClass->student_ids)
                        ? $existingClass->student_ids
                        : (is_string($existingClass->student_ids) ? (json_decode($existingClass->student_ids, true) ?? []) : []);
                    $existingNames = is_array($existingClass->student_names)
                        ? $existingClass->student_names
                        : (is_string($existingClass->student_names) ? (json_decode($existingClass->student_names, true) ?? []) : []);
                    $existingSubjects = is_array($existingClass->subjects)
                        ? $existingClass->subjects
                        : (is_string($existingClass->subjects) ? (json_decode($existingClass->subjects, true) ?? []) : []);
                    $existingYearInSchools = is_array($existingClass->year_in_schools)
                        ? $existingClass->year_in_schools
                        : (is_string($existingClass->year_in_schools) ? (json_decode($existingClass->year_in_schools, true) ?? []) : []);
                    $existingPermanent = is_array($existingClass->permanent)
                        ? $existingClass->permanent
                        : (is_string($existingClass->permanent) ? (json_decode($existingClass->permanent, true) ?? []) : []);

                    // ✅ First, preserve switch status for students that ARE already in current save data
                    foreach ($studentIds as $idx => $currentId) {
                        $currentName = $studentNames[$idx] ?? '';

                        // Find this student in existing class
                        foreach ($existingIds as $j => $existingId) {
                            if (
                                $existingId == $currentId &&
                                isset($existingNames[$j]) && $existingNames[$j] == $currentName &&
                                isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No'
                            ) {
                                // Student was a switch student, preserve their switch status
                                $permanent[$idx] = 'No';
                                break;
                            }
                        }
                    }

                    // ✅ Then, add back ALL switch students (permanent='No') that are NOT in current save data
                    foreach ($existingIds as $j => $existingId) {
                        $existingName = $existingNames[$j] ?? '';
                        $existingFullName = $existingId . ' ' . $existingName;

                        // Check if this student is a switch student (permanent='No')
                        $isSwitchStudent = isset($existingPermanent[$j]) && $existingPermanent[$j] === 'No';

                        if ($isSwitchStudent) {
                            // Check if this switch student is already in current save data
                            $alreadyInCurrentData = false;
                            foreach ($studentIds as $idx => $currentId) {
                                $currentName = $studentNames[$idx] ?? '';
                                $currentFullName = $currentId . ' ' . $currentName;
                                if ($currentFullName === $existingFullName) {
                                    $alreadyInCurrentData = true;
                                    break;
                                }
                            }

                            // If switch student is NOT in current save data, add them back
                            if (!$alreadyInCurrentData) {
                                $studentIds[] = $existingId;
                                $studentNames[] = $existingName;
                                $subjects[] = $existingSubjects[$j] ?? '';
                                $yearInSchools[] = $existingYearInSchools[$j] ?? '';
                                $permanent[] = 'No'; // Preserve switch status - MUST remain 'No'
                                $studentCount++;
                            }
                        }
                    }
                }

                // Now delete the classes after preserving switch students
                foreach ($existingClasses as $existingClass) {
                    // Store attendance data if exists (only for first one found and not already set)
                    if (empty($mainRecordData['is_attendance']) && $existingClass->is_attendance) {
                        $mainRecordData['is_attendance'] = $existingClass->is_attendance;
                    }

                    // Delete future records for this existing class
                    \App\Models\GeneralTimetable::where('parent_id', $existingClass->parent_id ?: $existingClass->id)
                        ->where('branch_id', session('branch_id'))
                        ->whereDate('date', '>', $date)
                        ->delete();

                    // Delete the existing class itself
                    $existingClass->delete();
                }

                // ✅ Update mainRecordData with preserved switch students
                $mainRecordData['student_ids'] = json_encode($studentIds);
                $mainRecordData['student_names'] = json_encode($studentNames);
                $mainRecordData['subjects'] = json_encode($subjects);
                $mainRecordData['year_in_schools'] = json_encode($yearInSchools);
                $mainRecordData['permanent'] = json_encode($permanent);
            }

            // Step 3: Always create new class (never update)
            // Use restored attendance if available, otherwise use preserved or default
            if ($restoredAttendance !== null) {
                $mainRecordData['is_attendance'] = json_encode($restoredAttendance);
            } else {
                $mainRecordData['is_attendance'] = $mainRecordData['is_attendance'] ??
                    ($studentCount > 0 ? json_encode(array_fill(0, $studentCount, 'No')) : null);
            }
            $mainRecord = \App\Models\GeneralTimetable::create($mainRecordData);

            if (empty($mainRecord->parent_id)) {
                $mainRecord->update(['parent_id' => $mainRecord->id]);
            }

            // Create future records if there are permanent students OR if teacher is assigned (for new classes)
            $hasPermanentStudents = in_array('Yes', $permanent);
            $hasTeacher = !empty($data['teacher_id']);

            if ($hasPermanentStudents || ($hasTeacher && $studentCount == 0)) {
                $currentDate = strtotime($date . ' +1 week');
                $endDate = strtotime('+1 year', $currentDate);
                $records = [];

                while ($currentDate <= $endDate) {
                    $futureDay = strtolower(date('l', $currentDate));
                    $isWeekday = in_array($futureDay, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
                    $isWeekend = in_array($futureDay, ['saturday', 'sunday']);

                    if (($isWeekday && in_array($dayName, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])) ||
                        ($isWeekend && in_array($dayName, ['saturday', 'sunday']))
                    ) {
                        // Only include permanent students
                        $futureIds = [];
                        $futureNames = [];
                        $futureSubjects = [];
                        $futureYears = [];
                        $futurePermanent = [];

                        foreach ($studentIds as $i => $id) {
                            if (isset($permanent[$i]) && $permanent[$i] === 'Yes') {
                                $futureIds[] = $id;
                                $futureNames[] = $studentNames[$i] ?? '';
                                $futureSubjects[] = $subjects[$i] ?? '';
                                $futureYears[] = $yearInSchools[$i] ?? '';
                                $futurePermanent[] = 'Yes';
                            }
                        }

                        // Create future record even if no students (for new classes with just teacher)
                        // OR if there are permanent students
                        if (!empty($futureIds) || ($hasTeacher && $studentCount == 0)) {
                            $records[] = [
                                'date' => date('Y-m-d', $currentDate),
                                'slot' => $slot,
                                'time_slot' => $isWeekday ? ($timeSlotMapping[$slot]['weekday'] ?? '') : ($timeSlotMapping[$slot]['weekend'] ?? ''),
                                'teacher_id' => $data['teacher_id'] ?? null,
                                'student_ids' => json_encode($futureIds),
                                'student_names' => json_encode($futureNames),
                                'subjects' => json_encode($futureSubjects),
                                'year_in_schools' => json_encode($futureYears),
                                'permanent' => json_encode($futurePermanent),
                                'is_attendance' => null,
                                'branch_id' => $branch_id,
                                'branch_name' => $branch_name,
                                'session_type' => $sessionTypeForSave,
                                'parent_id' => $mainRecord->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                    $currentDate = strtotime('+1 week', $currentDate);
                }

                foreach ($records as $rec) {
                    \App\Models\GeneralTimetable::create($rec);
                }
            }

            // ✅ Restore students in old class future records (with complete data)
            foreach ($data['temporary_moves'] ?? [] as $move) {
                $oldClass = \App\Models\GeneralTimetable::where('id', $move['old_class_id'])
                    ->where('branch_id', session('branch_id'))
                    ->first();
                if (!$oldClass) continue;

                $parentId = $oldClass->parent_id ?: $oldClass->id;

                // Get student's year_in_school from database
                $studentData = \DB::table('studentdata')
                    ->where('branch_id', session('branch_id'))
                    ->where('admissionid', $move['family_id'])
                    ->whereRaw("CONCAT(TRIM(studentname), ' ', TRIM(COALESCE(studentsur, ''))) = ?", [trim($move['student_name'])])
                    ->select('studentyearinschool')
                    ->first();

                $yearInSchool = $studentData ? $studentData->studentyearinschool : '';

                // Update all future records to include this student
                $futureRecords = \App\Models\GeneralTimetable::where('parent_id', $parentId)
                    ->where('branch_id', session('branch_id'))
                    ->where('date', '>', $date)
                    ->get();

                foreach ($futureRecords as $record) {
                    $ids = json_decode($record->student_ids, true) ?? [];
                    $names = json_decode($record->student_names, true) ?? [];
                    $subjects = json_decode($record->subjects, true) ?? [];
                    $years = json_decode($record->year_in_schools, true) ?? [];
                    $permanentFlags = json_decode($record->permanent, true) ?? [];

                    // Only add if student is not already in the record
                    if (!in_array($move['family_id'], $ids)) {
                        $ids[] = $move['family_id'];
                        $names[] = $move['student_name'];
                        $subjects[] = $move['old_subject'] ?? '';
                        $years[] = $yearInSchool;
                        $permanentFlags[] = 'Yes'; // Student is permanent in their original class

                        $record->update([
                            'student_ids' => json_encode($ids),
                            'student_names' => json_encode($names),
                            'subjects' => json_encode($subjects),
                            'year_in_schools' => json_encode($years),
                            'permanent' => json_encode($permanentFlags),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Class saved successfully',
                'class_id' => $mainRecord->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Timetable Save Single Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteScheduleFromBackend($id, Request $request)
    {
        $record = GeneralTimetable::where('branch_id', session('branch_id'))->where('id', $id)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Timetable record not found'], 404);
        }

        try {
            DB::beginTransaction();

            $selectedDate = $request->query('date');
            if (!$selectedDate) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Date parameter is required'], 400);
            }

            // Fetch records to be deleted
            $records = GeneralTimetable::where('branch_id', session('branch_id'))
                ->where('parent_id', $record->parent_id)
                ->where('date', '>=', $selectedDate)
                ->get();

            if ($records->isEmpty()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No records found to delete'], 404);
            }

            // Delete each record individually to trigger the 'deleted' event
            $deletedCount = 0;
            foreach ($records as $timetableRecord) {
                if ($timetableRecord->delete()) {
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "$deletedCount timetable record(s) deleted successfully"
                ]);
            }

            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'No records deleted'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ], 500);
        }
    }


    public function switchBranch(Request $request)
    {
        // Ensure only super admin can switch branches
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $branch_id = $request->input('branch_id');

        // Validate branch_id exists
        $branch = User::where('role', 'branch_admin')->where('branch_id', $branch_id)->first();
        if (!$branch) {
            return redirect()->back()->with('error', 'Invalid branch selected.');
        }

        // Fetch super admin's current permissions
        $superAdmin = auth()->user();
        $superAdminPermissions = AccessPermission::where('user_id', $superAdmin->id)->first();

        // Store super admin's original page_name in session
        if ($superAdminPermissions) {
            \Log::info('Storing super admin permissions', ['user_id' => $superAdmin->id, 'page_name' => $superAdminPermissions->page_name]);
            Session::put('original_permissions', $superAdminPermissions->page_name);
        } else {
            \Log::info('No super admin permissions found, storing empty array', ['user_id' => $superAdmin->id]);
            Session::put('original_permissions', json_encode([]));
        }

        // Fetch permissions for user_id 51
        $referenceUserPermissions = AccessPermission::where('user_id', 51)->first();
        if (!$referenceUserPermissions) {
            return redirect()->back()->with('error', 'Reference user permissions not found.');
        }

        // Decode page_name JSON and loop through permissions
        $newPermissions = json_decode($referenceUserPermissions->page_name, true);
        if (!is_array($newPermissions)) {
            return redirect()->back()->with('error', 'Invalid permissions format for reference user.');
        }

        // Ensure all permissions are included
        $permissionsToApply = [];
        foreach ($newPermissions as $key => $value) {
            $permissionsToApply[$key] = $value;
        }

        // Update or create super admin's access_permissions record
        AccessPermission::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'page_name' => json_encode($permissionsToApply),
                'can_access' => 1,
                'branch_id' => $branch_id,
                'branch_name' => $branch->branch_name,
                'updated_at' => now(),
            ]
        );

        // Set branch_id and branch_name in session
        Session::put('branch_id', $branch_id);
        Session::put('branch_name', $branch->branch_name);

        return redirect()->route('branch.dashboard')->with('success', 'Switched to ' . $branch->branch_name . ' Dashboard');
    }
    public function clearBranch()
    {
        // Ensure only super admin can clear branch session
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Fetch super admin
        $superAdmin = auth()->user();

        // Retrieve original permissions from session
        $originalPermissions = Session::get('original_permissions', json_encode([]));
        \Log::info('Restoring super admin permissions', ['user_id' => $superAdmin->id, 'original_permissions' => $originalPermissions]);

        // Ensure original_permissions is a JSON string
        if (is_array($originalPermissions)) {
            $originalPermissions = json_encode($originalPermissions);
        }

        // Update or create super admin's access_permissions record
        AccessPermission::updateOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'page_name' => $originalPermissions,
                'can_access' => 1,
                'branch_id' => null,
                'branch_name' => null,
                'updated_at' => now(),
            ]
        );

        // Clear branch_id, branch_name, and original_permissions from session
        Session::forget(['branch_id', 'branch_name', 'original_permissions']);
        \Log::info('Cleared session for super admin', ['user_id' => $superAdmin->id]);

        return redirect()->route('home')->with('success', 'Returned to Super Admin Dashboard');
    }


    public function grades_module()
    {
         return view('branchFrontend.grades.index');
    }

    public function getExistingGrades(Request $request)
    {
        $branchId = session('branch_id');

        $request->validate([
            'student_id' => 'required|string',
            'month' => 'required|string',
        ]);

        $studentId = $request->input('student_id');
        $month = $request->input('month');

        try {
            // Get student data including qualifications and subject_names
            $student = DB::table('studentdata')
                ->select('studentid', 'studentname', 'studentsur', 'qualifications', 'subject_names', 'admissionid')
                ->where('studentid', $studentId)
                ->where('branch_id', $branchId)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Parse qualifications and subject_names (they are JSON arrays)
            $qualifications = [];
            if ($student->qualifications) {
                if (is_string($student->qualifications)) {
                    $qualifications = json_decode($student->qualifications, true) ?? [];
                } else {
                    $qualifications = $student->qualifications ?? [];
                }
            }

            $subjectNames = [];
            if ($student->subject_names) {
                if (is_string($student->subject_names)) {
                    $subjectNames = json_decode($student->subject_names, true) ?? [];
                } else {
                    $subjectNames = $student->subject_names ?? [];
                }
            }

            // Parse month to get year and month for attendance query
            // Format: "November_2025"
            $monthParts = explode('_', $month);
            $monthName = $monthParts[0] ?? '';
            $year = $monthParts[1] ?? date('Y');

            // Convert month name to number (1-12)
            $monthTimestamp = strtotime($monthName . ' 1, ' . $year);
            if ($monthTimestamp === false) {
                // Fallback to current month if parsing fails
                $monthNumber = date('n');
                $year = date('Y');
            } else {
                $monthNumber = date('n', $monthTimestamp);
            }

            // Get start and end date of the month
            $startDate = Carbon::createFromDate($year, $monthNumber, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::createFromDate($year, $monthNumber, 1)->endOfMonth()->format('Y-m-d');

            // Build student full name for attendance matching
            $studentFullName = trim(($student->studentname ?? '') . ' ' . ($student->studentsur ?? ''));

            $gradesMap = [];
            $keyStagesMap = []; // Track key stages for each subject
            $noDataFoundMap = []; // Track subjects with no attendance data

            // Process each subject
            foreach ($subjectNames as $index => $subject) {
                // Get key stage for this subject (qualifications array should match subject_names array)
                $keyStage = $qualifications[$index] ?? null;
                $keyStagesMap[$subject] = $keyStage;

                // Check if key stage is KS1, KS2, or KS3
                if (in_array(strtoupper($keyStage ?? ''), ['KS1', 'KS2', 'KS3'])) {
                    // Fetch last attendance for this subject in the selected month
                    $lastAttendance = Attendance::where('branch_id', $branchId)
                        ->where('family_id', $student->admissionid)
                        ->where('student_name', 'like', '%' . $studentFullName . '%')
                        ->where('subject', $subject)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($lastAttendance && $lastAttendance->bk_ch) {
                        // Use full bk_ch value
                        $bkChValue = $lastAttendance->bk_ch;
                        $gradesMap[$subject] = trim($bkChValue);
                    } else {
                        $gradesMap[$subject] = ''; // No attendance found
                        $noDataFoundMap[$subject] = true; // Mark as no data found
                    }
                } else {
                    // For KS4, KS5, Adult - get from StudentGrade table
                    $existingGrade = StudentGrade::where('studentid', $studentId)
                        ->where('month', $month)
                        ->where('subject_name', $subject)
                        ->where('branch_id', $branchId)
                        ->first();

                    if ($existingGrade) {
                        $gradesMap[$subject] = $existingGrade->grade;
                    } else {
                        $gradesMap[$subject] = ''; // No grade found
                    }
                }
            }

            // Check if any grades exist
            $hasGrades = !empty(array_filter($gradesMap));

            return response()->json([
                'success' => true,
                'hasGrades' => $hasGrades,
                'grades' => $gradesMap,
                'keyStages' => $keyStagesMap, // Return key stages for frontend
                'noDataFound' => $noDataFoundMap // Return subjects with no attendance data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch grades: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeGrades(Request $request)
    {
        // Get branch details from User table (is_main_branch = 1)
        $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $branchId = $branch->branch_id;
        // Use branch_name from User, fallback to session if null
        $branchName = $branch->branch_name ?? session('branch_name') ?? 'Unknown Branch';

        // Basic validation
        $request->validate([
            'family_id' => 'required|string',
            'student_id' => 'required|string',
            'student_name' => 'required|string',
            'month' => 'required|string',
            'grades' => 'required|array',
            'grades.*.subject' => 'required|string',
            'grades.*.grade' => 'required|string',
        ]);

        $familyId = $request->input('family_id');
        $studentId = $request->input('student_id');
        $studentName = $request->input('student_name');
        $month = $request->input('month');
        $grades = $request->input('grades');

        // Get student data to check key stages
        $student = DB::table('studentdata')
            ->select('qualifications', 'subject_names')
            ->where('studentid', $studentId)
            ->where('branch_id', $branchId)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Parse qualifications and subject_names
        $qualifications = [];
        if ($student->qualifications) {
            if (is_string($student->qualifications)) {
                $qualifications = json_decode($student->qualifications, true) ?? [];
            } else {
                $qualifications = $student->qualifications ?? [];
            }
        }

        $subjectNames = [];
        if ($student->subject_names) {
            if (is_string($student->subject_names)) {
                $subjectNames = json_decode($student->subject_names, true) ?? [];
            } else {
                $subjectNames = $student->subject_names ?? [];
            }
        }

        // Create subject to key stage mapping
        $subjectKeyStageMap = [];
        foreach ($subjectNames as $index => $subject) {
            $subjectKeyStageMap[$subject] = $qualifications[$index] ?? null;
        }

        // Validate each grade based on key stage and extract part before "-" for KS1/KS2/KS3
        foreach ($grades as $index => $gradeData) {
            $subject = $gradeData['subject'];
            $grade = $gradeData['grade'] ?? '';
            $keyStage = $subjectKeyStageMap[$subject] ?? null;

            // Check if key stage is KS1, KS2, or KS3
            $isKS123 = in_array(strtoupper($keyStage ?? ''), ['KS1', 'KS2', 'KS3']);

            if ($isKS123) {
                // For KS1, KS2, KS3: extract only the part before "-" (dash)
                if (!empty(trim($grade))) {
                    $parts = explode('-', $grade);
                    $grade = trim($parts[0]); // Get only the part before first "-"
                    $grades[$index]['grade'] = $grade; // Update the grade value
                }

                // Validate that grade is not empty after extraction
                if (empty(trim($grade))) {
                    return response()->json([
                        'success' => false,
                        'message' => "Grade is required for subject: {$subject}"
                    ], 422);
                }
            } else {
                // For KS4, KS5, Adult: only single A-Z or single 0-9
                if (!preg_match('/^[A-Za-z0-9]$/', $grade)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Grade for subject '{$subject}' must be a single digit (0-9) or single letter (A-Z)"
                    ], 422);
                }
            }
        }

        try {
            // Check if grades already exist for this student and month
            $existingGrades = StudentGrade::where('studentid', $studentId)
                ->where('month', $month)
                ->where('branch_id', $branchId)
                ->exists();

            if ($existingGrades) {
                // Update existing records and create new ones if they don't exist
                foreach ($grades as $gradeData) {
                    $gradeRecord = StudentGrade::where('studentid', $studentId)
                        ->where('month', $month)
                        ->where('subject_name', $gradeData['subject'])
                        ->where('branch_id', $branchId)
                        ->first();

                    if ($gradeRecord) {
                        // Update existing record
                        $gradeRecord->grade = $gradeData['grade'];
                        $gradeRecord->branch_name = $branchName;
                        $gradeRecord->save();
                    } else {
                        // Create new record if it doesn't exist
                        StudentGrade::create([
                            'studentid' => $studentId,
                            'family_id' => $familyId,
                            'full_name' => $studentName,
                            'subject_name' => $gradeData['subject'],
                            'grade' => $gradeData['grade'],
                            'month' => $month,
                            'branch_name' => $branchName,
                            'branch_id' => $branchId,
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Grades updated successfully!',
                    'action' => 'updated'
                ], 200);
            } else {
                // Create new records - use create() to trigger activity log
                foreach ($grades as $gradeData) {
                    StudentGrade::create([
                        'studentid' => $studentId,
                        'family_id' => $familyId,
                        'full_name' => $studentName,
                        'subject_name' => $gradeData['subject'],
                        'grade' => $gradeData['grade'],
                        'month' => $month,
                        'branch_name' => $branchName,
                        'branch_id' => $branchId,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Grades saved successfully!',
                    'action' => 'created'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save grades: ' . $e->getMessage()
            ], 500);
        }
    }

   public function getstudentsGrades(Request $request)
{
    $familyId = $request->input('family_id');

    if (!$familyId) {
        return response()->json([
            'success' => false,
            'message' => 'family_id is required'
        ], 400);
    }

    // Current logged in tutor's branch ID from session (must use this)
    $branchId = session('branch_id');

    if (!$branchId) {
        return response()->json([
            'success' => false,
            'message' => 'Branch not found in session'
        ], 403);
    }

    $students = DB::table('studentdata')
        ->select([
            'studentid',
            'admissionid',
            'subject_names',
            // Clean full name: "ADAN1 KHAN" — extra spaces bhi hata dega
            DB::raw("TRIM(CONCAT(COALESCE(studentname, ''), ' ', COALESCE(studentsur, ''))) AS full_name")
        ])
        ->where('admissionid', $familyId)
        ->where('branch_id', $branchId)                    // Yeh clause MUST hai
        ->whereRaw("LOWER(COALESCE(student_status, '')) != ?", ['inactive'])
        ->whereNotNull('studentname')                      // Optional: agar name null ho to skip
        ->orderBy('studentname')
        ->get();

    $total = $students->count();

    return response()->json([
        'success' => true,
        'family_id' => $familyId,
        'branch_id' => $branchId,
        'total_students' => $total,
        'students' => $students->map(function ($student) {
            return [
                'studentid'     => $student->studentid,
                'admissionid'   => $student->admissionid,
                'full_name'     => $student->full_name ?: 'Name Not Available',
                'subject_names' => $student->subject_names ?? [], // JSON/array safe
            ];
        })
    ]);
}

    public function lessonTrackingReport()
    {
        return view('branchFrontend.report.lessonTrackingReport');
    }

    public function getLessonTrackingData(Request $request)
    {
        try {
            $familyId = $request->input('family_id');

            Log::info('=== getLessonTrackingData START ===', [
                'family_id' => $familyId,
                'branch_id' => session('branch_id')
            ]);

            if (!$familyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Family ID is required'
                ], 400);
            }

            // Get all students for this family ID
            $students = DB::table('studentdata')
                ->where('branch_id', session('branch_id'))
                ->where('admissionid', $familyId)
                ->select('studentid', 'studentname', 'studentsur', 'studenthours', 'subject_names')
                ->get();

            Log::info('getLessonTrackingData: Students found', [
                'family_id' => $familyId,
                'students_count' => $students->count(),
                'students' => $students->map(function($s) {
                    return [
                        'studentid' => $s->studentid,
                        'studentname' => $s->studentname,
                        'studentsur' => $s->studentsur ?? 'null',
                        'full_name' => trim($s->studentname . ' ' . ($s->studentsur ?? ''))
                    ];
                })->toArray()
            ]);

            if ($students->isEmpty()) {
                Log::info('getLessonTrackingData: No students found', [
                    'family_id' => $familyId,
                    'branch_id' => session('branch_id')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for this Family ID'
                ], 404);
            }

            $reportData = [];
            $today = date('Y-m-d');

            foreach ($students as $student) {
                $studentName = trim($student->studentname . ' ' . ($student->studentsur ?? ''));
                $studenthours = $student->studenthours ? json_decode($student->studenthours, true) : [];
                $subject_names = $student->subject_names ? json_decode($student->subject_names, true) : [];

                Log::info('=== getLessonTrackingData: Processing student ===', [
                    'family_id' => $familyId,
                    'student_id' => $student->studentid,
                    'student_name_constructed' => $studentName,
                    'studentname_from_db' => $student->studentname,
                    'studentsur_from_db' => $student->studentsur ?? 'null',
                    'branch_id' => session('branch_id')
                ]);

                // Calculate current total weekly quota
                $currentTotalWeeklyQuota = 0;
                if (!empty($studenthours) && is_array($studenthours)) {
                    foreach ($studenthours as $hours) {
                        $currentTotalWeeklyQuota += (int)$hours;
                    }
                }

                // Get quota history for this student (ordered by change_date DESC - most recent first)
                $quotaHistory = DB::table('quota_history')
                    ->where('branch_id', session('branch_id'))
                    ->where('studentid', $student->studentid)
                    ->orderBy('change_date', 'desc')
                    ->get();

                // Get first date from attendance where attendance_type is NOT NULL and date >= 2025-12-01
                $globalResetDate = '2025-12-01';
                $firstTimetableDateRaw = DB::table('attendance')
                    ->where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $studentName . '%')
                    ->whereNotNull('attendance_type')
                    ->where('date', '>=', $globalResetDate)
                    ->orderBy('date', 'asc')
                    ->value('date');

                $firstTimetableDate = null;
                $isResetDate = false;

                if ($firstTimetableDateRaw) {
                    // Use the first attendance date (already >= 2025-12-01)
                    $firstTimetableDate = $firstTimetableDateRaw;
                    $isResetDate = false;
                } else {
                    // If no attendance found, check if there's any attendance before reset date
                    $oldestAttendance = DB::table('attendance')
                        ->where('branch_id', session('branch_id'))
                        ->where('family_id', $familyId)
                        ->where('student_name', 'LIKE', '%' . $studentName . '%')
                        ->whereNotNull('attendance_type')
                        ->orderBy('date', 'asc')
                        ->value('date');
                    
                    if ($oldestAttendance && $oldestAttendance < $globalResetDate) {
                        // Existing student - use global reset date
                        $firstTimetableDate = $globalResetDate;
                        $isResetDate = true;
                    }
                }

                if (!$firstTimetableDate) {
                    // Student not in timetable yet
                    $reportData[] = [
                        'student_name' => $studentName,
                        'first_timetable_date' => null,
                        'total_weekly_quota' => $currentTotalWeeklyQuota,
                        'weekly_breakdown' => [],
                        'total_lessons_taken' => 0,
                        'subjects' => $subject_names,
                        'studenthours' => $studenthours,
                        'quota_changes' => []
                    ];
                    continue;
                }

                // Helper function to get quota for a specific date based on history
                // If quota changed on a date, that date onwards uses the new quota
                $getQuotaForDate = function($date) use ($quotaHistory, $currentTotalWeeklyQuota, $firstTimetableDate) {
                    // If no history, use current quota
                    if ($quotaHistory->isEmpty()) {
                        return $currentTotalWeeklyQuota;
                    }

                    // Sort history by change_date ASC (oldest first) to find the correct quota period
                    $sortedHistory = $quotaHistory->sortBy('change_date');

                    // Find the most recent quota change that happened on or before this date
                    $applicableQuota = null;
                    foreach ($sortedHistory as $change) {
                        if ($change->change_date <= $date) {
                            // This date is on or after the quota change, so use the new quota
                            $applicableQuota = $change->new_total_weekly_quota;
                        } else {
                            // We've passed the change date, break
                            break;
                        }
                    }

                    // If no change applies (date is before all changes), use the old quota from the first change
                    if ($applicableQuota === null) {
                        $firstChange = $sortedHistory->first();
                        if ($firstChange) {
                            $applicableQuota = $firstChange->old_total_weekly_quota;
                        }
                    }

                    // If still null (shouldn't happen), use current quota
                    return $applicableQuota ?? $currentTotalWeeklyQuota;
                };

                // Get all attendance records from first timetable date to today
                // First, let's check what attendance records exist for debugging
                $allAttendanceForDebug = DB::table('attendance')
                    ->where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $studentName . '%')
                    ->whereBetween('date', [$firstTimetableDate, $today])
                    ->select('id', 'date', 'student_name', 'subject', 'attendance_type')
                    ->orderBy('date', 'asc')
                    ->get();

                Log::info('getLessonTrackingData: Attendance records found', [
                    'family_id' => $familyId,
                    'student_name_search' => $studentName,
                    'date_range' => [$firstTimetableDate, $today],
                    'records_count' => $allAttendanceForDebug->count(),
                    'records' => $allAttendanceForDebug->map(function($record) {
                        return [
                            'id' => $record->id,
                            'date' => $record->date,
                            'student_name_in_db' => $record->student_name,
                            'subject' => $record->subject,
                            'attendance_type' => $record->attendance_type
                        ];
                    })->toArray()
                ]);

                // Also check ALL attendance records for this family to see name variations
                $allFamilyAttendance = DB::table('attendance')
                    ->where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->select('id', 'date', 'student_name', 'subject')
                    ->orderBy('date', 'desc')
                    ->limit(20)
                    ->get();

                Log::info('getLessonTrackingData: Sample attendance records for family (last 20)', [
                    'family_id' => $familyId,
                    'student_name_search' => $studentName,
                    'sample_records' => $allFamilyAttendance->map(function($record) {
                        return [
                            'date' => $record->date,
                            'student_name_in_db' => $record->student_name,
                            'subject' => $record->subject
                        ];
                    })->toArray()
                ]);

                $attendanceRecords = DB::table('attendance')
                    ->where('branch_id', session('branch_id'))
                    ->where('family_id', $familyId)
                    ->where('student_name', 'LIKE', '%' . $studentName . '%')
                    ->whereBetween('date', [$firstTimetableDate, $today])
                    ->select('date', 'subject')
                    ->orderBy('date', 'asc')
                    ->get();

                Log::info('getLessonTrackingData: Final attendance count', [
                    'family_id' => $familyId,
                    'student_name' => $studentName,
                    'total_lessons_taken' => count($attendanceRecords),
                    'first_timetable_date' => $firstTimetableDate,
                    'today' => $today
                ]);

                // Calculate weekly breakdown - Show ALL weeks from first timetable date to today
                $weeklyBreakdown = [];

                // Get the week start of first timetable date
                $firstDateTimestamp = strtotime($firstTimetableDate);
                $firstDayOfWeek = date('w', $firstDateTimestamp);
                $firstMondayOffset = ($firstDayOfWeek == 0) ? -6 : (1 - $firstDayOfWeek);
                $firstWeekStart = date('Y-m-d', $firstDateTimestamp + ($firstMondayOffset * 86400));

                // Get the week start of today
                $todayTimestamp = strtotime($today);
                $todayDayOfWeek = date('w', $todayTimestamp);
                $todayMondayOffset = ($todayDayOfWeek == 0) ? -6 : (1 - $todayDayOfWeek);
                $currentWeekStartDate = date('Y-m-d', $todayTimestamp + ($todayMondayOffset * 86400));

                // Generate all weeks from first week start to current week start
                $weekStartIterator = $firstWeekStart;
                $attendanceByWeek = [];

                // Count attendance by week
                foreach ($attendanceRecords as $record) {
                    $recordDate = $record->date;
                    $dateTimestamp = strtotime($recordDate);
                    $dayOfWeek = date('w', $dateTimestamp);
                    $mondayOffset = ($dayOfWeek == 0) ? -6 : (1 - $dayOfWeek);
                    $weekStart = date('Y-m-d', $dateTimestamp + ($mondayOffset * 86400));

                    if (!isset($attendanceByWeek[$weekStart])) {
                        $attendanceByWeek[$weekStart] = 0;
                    }
                    $attendanceByWeek[$weekStart]++;
                }

                // Build weekly breakdown for all weeks from start to today
                while ($weekStartIterator <= $currentWeekStartDate) {
                    $weekEnd = date('Y-m-d', strtotime($weekStartIterator . ' +6 days'));
                    $lessonsTaken = $attendanceByWeek[$weekStartIterator] ?? 0;
                    $weekQuota = $getQuotaForDate($weekEnd);

                    $weeklyBreakdown[] = [
                        'week_start' => $weekStartIterator,
                        'week_end' => $weekEnd,
                        'lessons_taken' => $lessonsTaken,
                        'quota' => $weekQuota
                    ];

                    // Move to next week
                    $weekStartIterator = date('Y-m-d', strtotime($weekStartIterator . ' +7 days'));
                }

                $totalLessonsTaken = count($attendanceRecords);

                // =====================================================
                // PAYMENT DATA: Fetch all payment periods for this family
                // (needed for monthly & weekly payment coloring)
                // =====================================================
                $paymentRecords = DB::table('payment')
                    ->where('branch_id', session('branch_id'))
                    ->where('paymentfamilyid', $familyId)
                    ->whereNull('deleted_by')
                    ->whereNotNull('paymentfrom')
                    ->whereNotNull('paymentto')
                    ->select('paymentfrom', 'paymentto', 'paid', 'package')
                    ->orderBy('paymentfrom', 'asc')
                    ->get();

                // Build payment periods array (normalize dates to Y-m-d)
                $paymentPeriods = [];
                foreach ($paymentRecords as $pay) {
                    $pFrom = date('Y-m-d', strtotime($pay->paymentfrom));
                    $pTo   = date('Y-m-d', strtotime($pay->paymentto));
                    if ($pFrom && $pTo && $pFrom !== '1970-01-01') {
                        $paymentPeriods[] = [
                            'from' => $pFrom,
                            'to'   => $pTo,
                        ];
                    }
                }

                // Calculate monthly breakdown with carry forward and historical quota
                // Only count complete weeks per month, incomplete weeks carry forward to next month
                // Formula: Monthly Quota = Complete Weeks × Weekly Quota (e.g., 4 weeks × 2 = 8)
                $monthlyBreakdown = [];
                $firstDateTimestamp = strtotime($firstTimetableDate);
                $firstMonth = date('Y-m', $firstDateTimestamp);
                $currentMonth = date('Y-m');

                $carryForwardUnusedQuota = 0; // Unused quota from previous month
                $carryForwardIncompleteDays = 0; // Incomplete week days from previous month
                $monthIterator = $firstMonth;
                $isFirstMonth = true;

                while ($monthIterator <= $currentMonth) {
                    $monthStartDate = $monthIterator . '-01';
                    $monthEndDate = date('Y-m-t', strtotime($monthStartDate));

                    // Count attendance in this month from the already-fetched attendance records
                    // Use the later of monthStartDate or firstTimetableDate to ensure we don't count before reset
                    $attendanceStartDate = max($monthStartDate, $firstTimetableDate);
                    $monthAttendance = 0;
                    foreach ($attendanceRecords as $record) {
                        $recordDate = $record->date;
                        if ($recordDate >= $attendanceStartDate && $recordDate <= $monthEndDate) {
                            $monthAttendance++;
                        }
                    }

                    // Calculate days in this month period
                    if ($isFirstMonth) {
                        // First month: from actual start date to month end
                        $periodStartDate = $firstTimetableDate;
                        $isFirstMonth = false;
                    } else {
                        // Subsequent months: full month
                        $periodStartDate = $monthStartDate;
                    }

                    // Add incomplete days from previous month
                    if ($carryForwardIncompleteDays > 0) {
                        $periodStartDate = date('Y-m-d', strtotime($periodStartDate . ' -' . $carryForwardIncompleteDays . ' days'));
                    }

                    $startTimestamp = strtotime($periodStartDate);
                    $endTimestamp = strtotime($monthEndDate);
                    $totalDays = floor(($endTimestamp - $startTimestamp) / (24 * 60 * 60)) + 1;

                    // Calculate complete weeks and remaining days
                    $completeWeeks = floor($totalDays / 7);
                    $remainingDays = $totalDays % 7;

                    // Get quota for this month - use month end date to determine quota
                    // If quota changed during month, use the new quota for the entire month
                    $monthQuota = $getQuotaForDate($monthEndDate);

                    // Check if quota changed during this month
                    $quotaChangedInMonth = false;
                    $quotaChangeDate = null;
                    foreach ($quotaHistory as $change) {
                        if ($change->change_date >= $monthStartDate && $change->change_date <= $monthEndDate) {
                            $quotaChangedInMonth = true;
                            $quotaChangeDate = $change->change_date;
                            break;
                        }
                    }

                    // Monthly quota = Complete weeks only × Weekly quota (use quota at month end to reflect changes)
                    $monthlyQuota = $completeWeeks * $monthQuota;

                    // Carry forward incomplete week days to next month
                    $carryForwardIncompleteDays = $remainingDays;

                    // Available = Monthly quota + Carry forward unused quota from previous month
                    $carryForwardFromPrevious = $carryForwardUnusedQuota;
                    $availableThisMonth = $monthlyQuota + $carryForwardFromPrevious;

                    // Calculate remaining and carry forward for next month
                    $remaining = max(0, $availableThisMonth - $monthAttendance);
                    $carryForwardUnusedQuota = $remaining;

                    // Check if this month has payment coverage
                    // A month is "paid" if any payment period overlaps with this month
                    $isMonthPaid = false;
                    foreach ($paymentPeriods as $period) {
                        // Overlap: payment starts before month ends AND payment ends after month starts
                        if ($period['from'] <= $monthEndDate && $period['to'] >= $monthStartDate) {
                            $isMonthPaid = true;
                            break;
                        }
                    }

                    $monthlyBreakdown[] = [
                        'month' => $monthIterator,
                        'month_name' => date('F Y', strtotime($monthStartDate)),
                        'month_start' => $monthStartDate,
                        'month_end' => $monthEndDate,
                        'monthly_quota' => $monthlyQuota, // Only complete weeks quota
                        'weekly_quota_used' => $monthQuota, // Weekly quota used for this month
                        'complete_weeks' => $completeWeeks,
                        'quota_changed_in_month' => $quotaChangedInMonth,
                        'quota_change_date' => $quotaChangeDate,
                        'carry_forward_from_previous' => $carryForwardFromPrevious,
                        'available_this_month' => $availableThisMonth,
                        'lessons_taken' => $monthAttendance,
                        'remaining' => $remaining,
                        'carry_forward_to_next' => $carryForwardUnusedQuota,
                        'is_paid' => $isMonthPaid,
                    ];

                    // Move to next month
                    $monthIterator = date('Y-m', strtotime($monthIterator . '-01 +1 month'));
                }

                // Calculate total available lessons using historical quota
                // This is complex because quota may have changed, so we calculate week by week
                $firstDateTimestamp = strtotime($firstTimetableDate);
                $todayTimestamp = strtotime($today);
                $totalAvailableLessons = 0;

                // Calculate week by week to account for quota changes
                $currentDate = $firstTimetableDate;
                while ($currentDate <= $today) {
                    $weekQuota = $getQuotaForDate($currentDate);
                    $totalAvailableLessons += $weekQuota;

                    // Move to next week
                    $currentDate = date('Y-m-d', strtotime($currentDate . ' +7 days'));
                }

                // Format quota changes for display
                $quotaChanges = [];
                foreach ($quotaHistory as $change) {
                    $quotaChanges[] = [
                        'change_date' => $change->change_date,
                        'old_quota' => $change->old_total_weekly_quota,
                        'new_quota' => $change->new_total_weekly_quota,
                        'old_studenthours' => $change->old_studenthours ? json_decode($change->old_studenthours, true) : [],
                        'new_studenthours' => $change->new_studenthours ? json_decode($change->new_studenthours, true) : []
                    ];
                }

                // Helper: check if a week is covered by any payment period
                // A week is "paid" if its week_start falls within any payment period
                $isWeekPaid = function($weekStart, $weekEnd) use ($paymentPeriods) {
                    foreach ($paymentPeriods as $period) {
                        // Week is paid if week_start is within the payment period
                        if ($weekStart >= $period['from'] && $weekStart <= $period['to']) {
                            return true;
                        }
                    }
                    return false;
                };

                // Mark each week as paid/unpaid
                foreach ($weeklyBreakdown as &$week) {
                    $week['is_paid'] = $isWeekPaid($week['week_start'], $week['week_end']);
                }
                unset($week);

                // Calculate remaining lessons split: paid vs unpaid
                $totalRemaining = max(0, $totalAvailableLessons - $totalLessonsTaken);
                $paidRemaining   = 0;
                $unpaidRemaining = 0;

                // Walk through weekly breakdown in reverse (most recent first)
                $remainingToAllocate = $totalRemaining;
                $reversedWeeks = array_reverse($weeklyBreakdown);
                foreach ($reversedWeeks as $week) {
                    if ($remainingToAllocate <= 0) break;
                    $weekRemaining = max(0, $week['quota'] - $week['lessons_taken']);
                    $allocated = min($weekRemaining, $remainingToAllocate);
                    if ($week['is_paid']) {
                        $paidRemaining += $allocated;
                    } else {
                        $unpaidRemaining += $allocated;
                    }
                    $remainingToAllocate -= $allocated;
                }

                Log::info('getLessonTrackingData: Student report data prepared', [
                    'family_id' => $familyId,
                    'student_name' => $studentName,
                    'total_lessons_taken' => $totalLessonsTaken,
                    'total_available_lessons' => $totalAvailableLessons,
                    'total_remaining_lessons' => max(0, $totalAvailableLessons - $totalLessonsTaken),
                    'paid_remaining' => $paidRemaining,
                    'unpaid_remaining' => $unpaidRemaining,
                    'payment_periods_count' => count($paymentPeriods),
                    'first_timetable_date' => $firstTimetableDateRaw
                ]);

                $reportData[] = [
                    'student_name' => $studentName,
                    'first_timetable_date' => $firstTimetableDateRaw, // Original first date
                    'reset_date' => $isResetDate ? $globalResetDate : null, // Reset date if applied
                    'is_reset_date' => $isResetDate, // Flag to show if reset is applied
                    'current_weekly_quota' => $currentTotalWeeklyQuota,
                    'weekly_breakdown' => $weeklyBreakdown,
                    'monthly_breakdown' => $monthlyBreakdown,
                    'total_lessons_taken' => $totalLessonsTaken,
                    'total_available_lessons' => $totalAvailableLessons,
                    'total_remaining_lessons' => max(0, $totalAvailableLessons - $totalLessonsTaken),
                    'paid_remaining' => $paidRemaining,
                    'unpaid_remaining' => $unpaidRemaining,
                    'payment_periods' => $paymentPeriods,
                    'subjects' => $subject_names,
                    'studenthours' => $studenthours,
                    'quota_changes' => $quotaChanges
                ];
            }

            Log::info('=== getLessonTrackingData END ===', [
                'family_id' => $familyId,
                'students_processed' => count($reportData)
            ]);

            return response()->json([
                'success' => true,
                'data' => $reportData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching lesson tracking data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching report data: ' . $e->getMessage()
            ], 500);
        }
    }


    // =============================================
    // Family Block / Unblock Module
    // =============================================

    public function familyBlockIndex()
    {
        $admissions = Admission::where('branch_id', session('branch_id'))
            ->orderBy('familyno', 'asc')
            ->get(['admissionid', 'familyno', 'is_blocked', 'block_reason']);

        return view('branchFrontend.familyBlock.index', compact('admissions'));
    }

    public function familyBlockToggle(Request $request)
    {
        $request->validate([
            'admission_id' => 'required|integer',
            'action'       => 'required|in:block,unblock',
            'block_reason' => 'nullable|string|max:500',
        ]);

        try {
            $admission = Admission::where('admissionid', $request->admission_id)
                ->where('branch_id', session('branch_id'))
                ->firstOrFail();

            if ($request->action === 'block') {
                $admission->is_blocked   = 1;
                $admission->block_reason = $request->block_reason;
            } else {
                $admission->is_blocked   = 0;
                $admission->block_reason = null;
            }

            $admission->save();

            $msg = $request->action === 'block'
                ? 'Family ID ' . $admission->familyno . ' has been blocked successfully.'
                : 'Family ID ' . $admission->familyno . ' has been unblocked successfully.';

            return response()->json(['success' => $msg]);
        } catch (\Exception $e) {
            Log::error('Family block toggle error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function termBreak()
    {
         $branch = User::where('branch_id', session('branch_id'))->where('is_main_branch', 1)->first();
        $branch_id = $branch->branch_id;
        $branch_name = $branch->branch_name;

        $teachers = DB::table('teachers_subject')
            ->where('is_available', 'yes')
            ->select('teacher_name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->get();

        $subjects = DB::table('subjects')
            ->select('name')
            ->where('branch_id', session('branch_id'))
            ->distinct()
            ->pluck('name');


        $termBreak = TermBreak::where('branch_id', session('branch_id'))->first();
        return view('branchFrontend.centralTimeTable.termbreak', compact('teachers', 'subjects', 'termBreak'));

    }


    public function newListModule()
    {
        return view('branchFrontend.schedular.newListModule');
    }

    public function crashCourse()
    {
        // Get page permissions for dashboard - check both user-specific and branch-level permissions
        $permission = AccessPermission::where('branch_id', session('branch_id'))
            ->where('user_id', auth()->id())
            ->first();

        $pagePermissions = [];
        if ($permission && $permission->page_name) {
            $decoded = json_decode($permission->page_name, true);
            $pagePermissions = is_array($decoded) ? $decoded : [];
        }

        return view('branchFrontend.crashCourse.dashboard', compact('pagePermissions'));
    }

    public function crashCoursePackages()
    {
        // Get all packages for current branch
        $packages = DB::table('crash_course_packages')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('branchFrontend.crashCourse.packages', compact('packages'));
    }

    public function crashCourseRegistration()
    {
        // Get all students including inactive ones for dropdown
        $students = Student::where('branch_id', session('branch_id'))
            ->orderBy('studentname', 'asc')
            ->get();

        // Get all packages for current branch
        $packages = DB::table('crash_course_packages')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all registrations for current branch
        $registrations = DB::table('crash_course_registrations')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all payments for current branch (package_name is now stored in the table)
        $payments = DB::table('crash_course_payments')
            ->where('branch_id', session('branch_id'))
            ->orderBy('payment_date', 'desc')
            ->get();

            // dd($payments);


            $paid_payments = DB::table('crash_course_payments')
        ->where('branch_id', session('branch_id'))
        ->orderBy('payment_date', 'desc')
        ->get();

    // Extract unique candidates (in case of multiple payments)
    $paid_candidates = $paid_payments->map(function ($payment) {
        return [
            'candidate_name' => trim($payment->candidate_name),
            'family_id'      => $payment->family_id,
        ];
    })->unique(function ($item) {
        return $item['candidate_name'] . '-' . $item['family_id'];
    });

        return view('branchFrontend.crashCourse.registration', compact('students', 'packages', 'registrations', 'payments','paid_candidates'));
    }

    public function crashCourseAttendance()
    {
        // Get paid candidates (same as registration page)
        $paid_payments = DB::table('crash_course_payments')
            ->where('branch_id', session('branch_id'))
            ->orderBy('payment_date', 'desc')
            ->get();

        // Extract unique candidates (in case of multiple payments)
        $paid_candidates = $paid_payments->map(function ($payment) {
            return [
                'candidate_name' => trim($payment->candidate_name),
                'family_id'      => $payment->family_id,
            ];
        })->unique(function ($item) {
            return $item['candidate_name'] . '-' . $item['family_id'];
        });

        // Get all unique subjects from teachers_subject table (branch-wise, is_available = yes)
        $subjects = DB::table('teachers_subject')
            ->where('branch_id', session('branch_id'))
            ->where('is_available', 'yes')
            ->select('subject')
            ->distinct()
            ->orderBy('subject', 'asc')
            ->pluck('subject')
            ->unique()
            ->values();

        // Get all teachers from teachers_subject table (branch-wise, is_available = yes)
        $teachers = DB::table('teachers_subject')
            ->where('branch_id', session('branch_id'))
            ->where('is_available', 'yes')
            ->select('teacher_name')
            ->distinct()
            ->orderBy('teacher_name', 'asc')
            ->pluck('teacher_name')
            ->unique()
            ->values();

        // Static time slots
        $timeSlots = collect([
            // Weekday slots
            'Lesson 1 – 11:00 - 01:00',
            'Lesson 2 – 01:30 - 03:30',
            'Lesson 3 – 04:30 - 06:30',
            'Lesson 4 – 06:45 - 08:45',
            // Weekend slots
            'Lesson 1 – 09:00 - 11:00',
            'Lesson 2 – 11:20 - 01:20',
            'Lesson 3 – 02:00 - 04:00'
        ]);

        // Get all attendance records for current branch
        $attendanceRecords = DB::table('crash_course_attendance')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('branchFrontend.crashCourse.attendance', compact('paid_candidates', 'attendanceRecords', 'subjects', 'teachers', 'timeSlots'));
    }

    public function crashCourseRecords()
    {
        // Get all packages for current branch
        $packages = DB::table('crash_course_packages')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all registrations for current branch
        $registrations = DB::table('crash_course_registrations')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all payments for current branch
        $payments = DB::table('crash_course_payments')
            ->where('branch_id', session('branch_id'))
            ->orderBy('payment_date', 'desc')
            ->get();

        // Get all attendance records for current branch
        $attendanceRecords = DB::table('crash_course_attendance')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('branchFrontend.crashCourse.records', compact('packages', 'registrations', 'payments', 'attendanceRecords'));
    }

    public function crashCourseData()
    {
        // Get all packages for current branch
        $packages = DB::table('crash_course_packages')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all registrations for current branch
        $registrations = DB::table('crash_course_registrations')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all payments for current branch
        $payments = DB::table('crash_course_payments')
            ->where('branch_id', session('branch_id'))
            ->orderBy('payment_date', 'desc')
            ->get();

        // Get all attendance records for current branch
        $attendanceRecords = DB::table('crash_course_attendance')
            ->where('branch_id', session('branch_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'packages' => $packages,
            'registrations' => $registrations,
            'payments' => $payments,
            'attendanceRecords' => $attendanceRecords
        ]);
    }

    public function exportCrashCourseRecords(Request $request, $format)
    {
        try {
            $type = $request->query('type', 'all'); // 'registrations', 'payments', 'attendance', or 'all'
            $studentFilter = $request->query('student', '');
            $packageFilter = $request->query('package', '');
            $dateFilter = $request->query('date', '');

            // Get all data for current branch
            $packages = DB::table('crash_course_packages')
                ->where('branch_id', session('branch_id'))
                ->get();

            $registrations = DB::table('crash_course_registrations')
                ->where('branch_id', session('branch_id'))
                ->get();

            $payments = DB::table('crash_course_payments')
                ->where('branch_id', session('branch_id'))
                ->get();

            $attendanceRecords = DB::table('crash_course_attendance')
                ->where('branch_id', session('branch_id'))
                ->get();

            // Convert to collections for filtering
            $registrations = collect($registrations);
            $payments = collect($payments);
            $attendanceRecords = collect($attendanceRecords);
            $packages = collect($packages);

            // Apply filters
            if ($studentFilter) {
                $registrations = $registrations->filter(function($r) use ($studentFilter) {
                    return stripos($r->candidate_name ?? '', $studentFilter) !== false ||
                           stripos($r->family_id ?? '', $studentFilter) !== false;
                });
                $payments = $payments->filter(function($p) use ($studentFilter) {
                    return stripos($p->candidate_name ?? '', $studentFilter) !== false ||
                           stripos($p->family_id ?? '', $studentFilter) !== false;
                });
                $attendanceRecords = $attendanceRecords->filter(function($a) use ($studentFilter) {
                    return stripos($a->candidate_name ?? '', $studentFilter) !== false ||
                           stripos($a->family_id ?? '', $studentFilter) !== false;
                });
            }

            if ($packageFilter) {
                $registrations = $registrations->filter(function($r) use ($packageFilter) {
                    return $r->package_id == $packageFilter;
                });
            }

            if ($dateFilter) {
                $payments = $payments->filter(function($p) use ($dateFilter) {
                    return $p->payment_date == $dateFilter;
                });
                $attendanceRecords = $attendanceRecords->filter(function($a) use ($dateFilter) {
                    return date('Y-m-d', strtotime($a->created_at)) == $dateFilter;
                });
            }

            if ($format === 'excel') {
                // Excel Export based on type
                if ($type === 'registrations') {
                    $export = new class($registrations, $packages) implements FromCollection, WithHeadings {
                        private $registrations;
                        private $packages;

                        public function __construct($registrations, $packages)
                        {
                            $this->registrations = $registrations;
                            $this->packages = $packages;
                        }

                        public function collection()
                        {
                            $data = collect();
                            foreach ($this->registrations as $reg) {
                                $package = $this->packages->where('id', $reg->package_id)->first();
                                $data->push([
                                    'ID' => $reg->id,
                                    'Candidate Name' => $reg->candidate_name,
                                    'Family ID' => $reg->family_id,
                                    'Package Name' => $package ? $package->name : 'N/A',
                                    'Registration Date' => date('d/m/Y', strtotime($reg->created_at)),
                                ]);
                            }
                            return $data;
                        }

                        public function headings(): array
                        {
                            return ['ID', 'Candidate Name', 'Family ID', 'Package Name', 'Registration Date'];
                        }
                    };
                    $filename = 'crash_course_registrations_' . date('Y-m-d') . '.xlsx';
                } elseif ($type === 'payments') {
                    $export = new class($payments) implements FromCollection, WithHeadings {
                        private $payments;

                        public function __construct($payments)
                        {
                            $this->payments = $payments;
                        }

                        public function collection()
                        {
                            $data = collect();
                            foreach ($this->payments as $payment) {
                                $data->push([
                                    'ID' => $payment->id,
                                    'Candidate Name' => $payment->candidate_name,
                                    'Family ID' => $payment->family_id,
                                    'Amount Paid' => '£' . number_format($payment->amount_paid, 2),
                                    'Payment Method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
                                    'Payment Date' => date('d/m/Y', strtotime($payment->payment_date)),
                                ]);
                            }
                            return $data;
                        }

                        public function headings(): array
                        {
                            return ['ID', 'Candidate Name', 'Family ID', 'Amount Paid', 'Payment Method', 'Payment Date'];
                        }
                    };
                    $filename = 'crash_course_payments_' . date('Y-m-d') . '.xlsx';
                } elseif ($type === 'attendance') {
                    $export = new class($attendanceRecords) implements FromCollection, WithHeadings {
                        private $attendanceRecords;

                        public function __construct($attendanceRecords)
                        {
                            $this->attendanceRecords = $attendanceRecords;
                        }

                        public function collection()
                        {
                            $data = collect();
                            foreach ($this->attendanceRecords as $attendance) {
                                $data->push([
                                    'ID' => $attendance->id,
                                    'Candidate Name' => $attendance->candidate_name,
                                    'Family ID' => $attendance->family_id,
                                    'Subject' => $attendance->subject,
                                    'Teacher' => $attendance->teacher,
                                    'Time Slot' => $attendance->timeslot,
                                    'Date' => date('d/m/Y', strtotime($attendance->created_at)),
                                ]);
                            }
                            return $data;
                        }

                        public function headings(): array
                        {
                            return ['ID', 'Candidate Name', 'Family ID', 'Subject', 'Teacher', 'Time Slot', 'Date'];
                        }
                    };
                    $filename = 'crash_course_attendance_' . date('Y-m-d') . '.xlsx';
                } else {
                    // All records (original behavior)
                    $export = new class($registrations, $payments, $attendanceRecords, $packages) implements FromCollection, WithHeadings {
                        private $registrations;
                        private $payments;
                        private $attendanceRecords;
                        private $packages;

                        public function __construct($registrations, $payments, $attendanceRecords, $packages)
                        {
                            $this->registrations = $registrations;
                            $this->payments = $payments;
                            $this->attendanceRecords = $attendanceRecords;
                            $this->packages = $packages;
                        }

                        public function collection()
                        {
                            $data = collect();

                            // Add registrations
                            foreach ($this->registrations as $reg) {
                                $package = $this->packages->where('id', $reg->package_id)->first();
                                $data->push([
                                    'Type' => 'Registration',
                                    'ID' => $reg->id,
                                    'Candidate Name' => $reg->candidate_name,
                                    'Family ID' => $reg->family_id,
                                    'Package Name' => $package ? $package->name : 'N/A',
                                    'Date' => date('d/m/Y', strtotime($reg->created_at)),
                                    'Amount' => '',
                                    'Payment Method' => '',
                                    'Subject' => '',
                                    'Teacher' => '',
                                    'Time Slot' => ''
                                ]);
                            }

                            // Add payments
                            foreach ($this->payments as $payment) {
                                $data->push([
                                    'Type' => 'Payment',
                                    'ID' => $payment->id,
                                    'Candidate Name' => $payment->candidate_name,
                                    'Family ID' => $payment->family_id,
                                    'Package Name' => '',
                                    'Date' => date('d/m/Y', strtotime($payment->payment_date)),
                                    'Amount' => '£' . number_format($payment->amount_paid, 2),
                                    'Payment Method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
                                    'Subject' => '',
                                    'Teacher' => '',
                                    'Time Slot' => ''
                                ]);
                            }

                            // Add attendance
                            foreach ($this->attendanceRecords as $attendance) {
                                $data->push([
                                    'Type' => 'Attendance',
                                    'ID' => $attendance->id,
                                    'Candidate Name' => $attendance->candidate_name,
                                    'Family ID' => $attendance->family_id,
                                    'Package Name' => '',
                                    'Date' => date('d/m/Y', strtotime($attendance->created_at)),
                                    'Amount' => '',
                                    'Payment Method' => '',
                                    'Subject' => $attendance->subject,
                                    'Teacher' => $attendance->teacher,
                                    'Time Slot' => $attendance->timeslot
                                ]);
                            }

                            return $data;
                        }

                        public function headings(): array
                        {
                            return [
                                'Type',
                                'ID',
                                'Candidate Name',
                                'Family ID',
                                'Package Name',
                                'Date',
                                'Amount',
                                'Payment Method',
                                'Subject',
                                'Teacher',
                                'Time Slot'
                            ];
                        }
                    };
                    $filename = 'crash_course_records_' . date('Y-m-d') . '.xlsx';
                }

                return Excel::download($export, $filename);
            } else {
                // PDF Export based on type
                if ($type === 'registrations') {
                    $html = view('branchFrontend.crashCourse.exportPDFRegistrations', [
                        'registrations' => $registrations->values(),
                        'packages' => $packages->values()
                    ])->render();
                    $filename = 'crash_course_registrations_' . date('Y-m-d') . '.pdf';
                } elseif ($type === 'payments') {
                    $html = view('branchFrontend.crashCourse.exportPDFPayments', [
                        'payments' => $payments->values()
                    ])->render();
                    $filename = 'crash_course_payments_' . date('Y-m-d') . '.pdf';
                } elseif ($type === 'attendance') {
                    $html = view('branchFrontend.crashCourse.exportPDFAttendance', [
                        'attendanceRecords' => $attendanceRecords->values()
                    ])->render();
                    $filename = 'crash_course_attendance_' . date('Y-m-d') . '.pdf';
                } else {
                    // All records (original behavior)
                    $html = view('branchFrontend.crashCourse.exportPDF', [
                        'registrations' => $registrations->values(),
                        'payments' => $payments->values(),
                        'attendanceRecords' => $attendanceRecords->values(),
                        'packages' => $packages->values()
                    ])->render();
                    $filename = 'crash_course_records_' . date('Y-m-d') . '.pdf';
                }

                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('defaultFont', 'Arial');

                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                return $dompdf->stream($filename);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Package CRUD Methods
    public function storeCrashCoursePackage(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);

            $branch = User::where('branch_id', session('branch_id'))
                ->where('is_main_branch', 1)
                ->first();

            $packageId = DB::table('crash_course_packages')->insertGetId([
                'name' => $request->name,
                'price' => $request->price,
                'branch_id' => session('branch_id'),
                'branch_name' => $branch ? $branch->branch_name : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $package = DB::table('crash_course_packages')
                ->where('id', $packageId)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Package created successfully',
                'package' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateCrashCoursePackage(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|numeric',
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);

            DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'name' => $request->name,
                    'price' => $request->price,
                    'updated_at' => now(),
                ]);

            $package = DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Package updated successfully',
                'package' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCrashCoursePackage($id)
    {
        try {
            DB::table('crash_course_packages')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->delete();

            return response()->json(['success' => true, 'message' => 'Package deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Registration CRUD Methods
    public function storeCrashCourseRegistration(Request $request)
    {
        try {
            $request->validate([
                'candidate_value' => 'required|string',
                'package_id' => 'required|numeric',
            ]);

            // Extract candidate name and family_id from format: "name-family_id"
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            $branch = User::where('branch_id', session('branch_id'))
                ->where('is_main_branch', 1)
                ->first();

            $registrationId = DB::table('crash_course_registrations')->insertGetId([
                'candidate_name' => $candidateName,
                'family_id' => $familyId,
                'package_id' => $request->package_id,
                'branch_id' => session('branch_id'),
                'branch_name' => $branch ? $branch->branch_name : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $registration = DB::table('crash_course_registrations')
                ->where('id', $registrationId)
                ->where('branch_id', session('branch_id'))
                ->first();
            $package = DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Student registered successfully',
                'registration' => $registration,
                'package' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateCrashCourseRegistration(Request $request)
    {
        try {
            $request->validate([
                'registration_id' => 'required|numeric',
                'candidate_value' => 'required|string',
                'package_id' => 'required|numeric',
            ]);

            // Extract candidate name and family_id
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            DB::table('crash_course_registrations')
                ->where('id', $request->registration_id)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'candidate_name' => $candidateName,
                    'family_id' => $familyId,
                    'package_id' => $request->package_id,
                    'updated_at' => now(),
                ]);

            $registration = DB::table('crash_course_registrations')
                ->where('id', $request->registration_id)
                ->where('branch_id', session('branch_id'))
                ->first();
            $package = DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Registration updated successfully',
                'registration' => $registration,
                'package' => $package
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCrashCourseRegistration($id)
    {
        try {
            DB::table('crash_course_registrations')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->delete();

            return response()->json(['success' => true, 'message' => 'Registration deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Payment CRUD Methods
    public function storeCrashCoursePayment(Request $request)
    {
        try {
            $request->validate([
                'candidate_value' => 'required|string',
                'package_id' => 'required|numeric',
                'amount_paid' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
            ]);

            // Extract candidate name and family_id
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            $branch = User::where('branch_id', session('branch_id'))
                ->where('is_main_branch', 1)
                ->first();

            // Get package name from package_id
            $package = DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            $packageName = $package ? $package->name : 'N/A';

            $paymentId = DB::table('crash_course_payments')->insertGetId([
                'candidate_name' => $candidateName,
                'family_id' => $familyId,
                'package_name' => $packageName,
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'payment_date' => now()->toDateString(),
                'branch_id' => session('branch_id'),
                'branch_name' => $branch ? $branch->branch_name : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $payment = DB::table('crash_course_payments')
                ->where('id', $paymentId)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Payment saved successfully',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateCrashCoursePayment(Request $request)
    {
        try {
            $request->validate([
                'payment_id' => 'required|numeric',
                'candidate_value' => 'required|string',
                'package_id' => 'required|numeric',
                'amount_paid' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
            ]);

            // Extract candidate name and family_id
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            // Get package name from package_id
            $package = DB::table('crash_course_packages')
                ->where('id', $request->package_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            $packageName = $package ? $package->name : 'N/A';

            DB::table('crash_course_payments')
                ->where('id', $request->payment_id)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'candidate_name' => $candidateName,
                    'family_id' => $familyId,
                    'package_name' => $packageName,
                    'amount_paid' => $request->amount_paid,
                    'payment_method' => $request->payment_method,
                    'updated_at' => now(),
                ]);

            $payment = DB::table('crash_course_payments')
                ->where('id', $request->payment_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCrashCoursePayment($id)
    {
        try {
            DB::table('crash_course_payments')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->delete();

            return response()->json(['success' => true, 'message' => 'Payment deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function generateCrashCourseReceipt($id)
    {
        try {
            $payment = DB::table('crash_course_payments')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->first();

            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
            }

            // Get package name from payment table (already stored)
            $packageName = $payment->package_name ?? 'N/A';

            $pdf = new Dompdf();
            $pdf->setOptions(new Options([
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'margin-top' => '0mm',
                'margin-right' => '0mm',
                'margin-bottom' => '0mm',
                'margin-left' => '0mm',
            ]));

            // Generate receipt number from created_at timestamp
            $createdAt = strtotime($payment->created_at);
            $random = date('YmdHis', $createdAt) . $payment->id;

            // Number to words function
            function numberToWords($number)
            {
                $words = [
                    0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five',
                    6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten',
                    11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                    16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
                    20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty',
                    60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
                ];

                $number = str_replace(',', '', $number);
                $number = (int) $number;

                if ($number < 21) {
                    return $words[$number];
                }

                if ($number < 100) {
                    $tens = (int) ($number / 10) * 10;
                    $units = $number % 10;
                    return $words[$tens] . ($units ? '-' . $words[$units] : '');
                }

                if ($number < 1000) {
                    $hundreds = (int) ($number / 100);
                    $remainder = $number % 100;
                    return $words[$hundreds] . ' hundred' . ($remainder ? ' and ' . numberToWords($remainder) : '');
                }

                if ($number < 1000000) {
                    $thousands = (int) ($number / 1000);
                    $remainder = $number % 1000;
                    return numberToWords($thousands) . ' thousand' . ($remainder ? ' ' . numberToWords($remainder) : '');
                }

                if ($number < 1000000000) {
                    $millions = (int) ($number / 1000000);
                    $remainder = $number % 1000000;
                    return numberToWords($millions) . ' million' . ($remainder ? ' ' . numberToWords($remainder) : '');
                }

                throw new \Exception('Number is too large to convert to words');
            }

            $paid = $payment->amount_paid;
            $amount_in_words = numberToWords($paid);
            // Use payment method as stored in database (no conversion)
            $paymentMethod = $payment->payment_method;

            $date = date('d F Y', strtotime($payment->payment_date));

            $receivedBy = auth()->user()->name ?? 'Admin';

            // Set payment method flags
            $cash = ($payment->payment_method == 'cash') ? 'Yes' : '';
            $bank = (in_array($payment->payment_method, ['bank_transfer', 'card', 'online'])) ? 'Yes' : '';

            $data = [
                'date' => $date,
                'receipt_no' => $random,
                'candidate_name' => $payment->candidate_name,
                'family_id' => $payment->family_id,
                'package_name' => $packageName,
                'amount_paid' => '£' . number_format($paid, 2),
                'amount_in_words' => ucfirst($amount_in_words) . ' pounds only',
                'payment_method' => $paymentMethod,
                'received_by' => $receivedBy,
                'cash' => $cash,
                'bank' => $bank,
            ];

            $pdf->loadHtml(view('reports.crashCourseReceipt', $data));
            $pdf->render();
            $pdfOutput = $pdf->output();

            return response($pdfOutput, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="crash_course_receipt_' . $random . '.pdf"');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function getCrashCourseCandidates()
    {
        try {
            $paid_payments = DB::table('crash_course_payments')
                ->where('branch_id', session('branch_id'))
                ->orderBy('payment_date', 'desc')
                ->get();

            // Extract unique candidates (in case of multiple payments)
            $paid_candidates = $paid_payments->map(function ($payment) {
                return [
                    'candidate_name' => trim($payment->candidate_name),
                    'family_id'      => $payment->family_id,
                ];
            })->unique(function ($item) {
                return $item['candidate_name'] . '-' . $item['family_id'];
            })->values();

            return response()->json([
                'success' => true,
                'candidates' => $paid_candidates
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // Attendance CRUD Methods
    public function storeCrashCourseAttendance(Request $request)
    {
        try {
            $request->validate([
                'candidate_value' => 'required|string',
                'subject' => 'required|string',
                'teacher' => 'required|string',
                'timeslot' => 'required|string',
            ]);

            // Extract candidate name and family_id
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            $branch = User::where('branch_id', session('branch_id'))
                ->where('is_main_branch', 1)
                ->first();

            $attendanceId = DB::table('crash_course_attendance')->insertGetId([
                'candidate_name' => $candidateName,
                'family_id' => $familyId,
                'subject' => $request->subject,
                'teacher' => $request->teacher,
                'timeslot' => $request->timeslot,
                'branch_id' => session('branch_id'),
                'branch_name' => $branch ? $branch->branch_name : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $attendance = DB::table('crash_course_attendance')
                ->where('id', $attendanceId)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateCrashCourseAttendance(Request $request)
    {
        try {
            $request->validate([
                'attendance_id' => 'required|numeric',
                'candidate_value' => 'required|string',
                'subject' => 'required|string',
                'teacher' => 'required|string',
                'timeslot' => 'required|string',
            ]);

            // Extract candidate name and family_id
            $candidateValue = $request->candidate_value;
            $lastHyphenPos = strrpos($candidateValue, '-');
            if ($lastHyphenPos === false) {
                return response()->json(['success' => false, 'message' => 'Invalid candidate format'], 400);
            }
            $candidateName = substr($candidateValue, 0, $lastHyphenPos);
            $familyId = substr($candidateValue, $lastHyphenPos + 1);

            DB::table('crash_course_attendance')
                ->where('id', $request->attendance_id)
                ->where('branch_id', session('branch_id'))
                ->update([
                    'candidate_name' => $candidateName,
                    'family_id' => $familyId,
                    'subject' => $request->subject,
                    'teacher' => $request->teacher,
                    'timeslot' => $request->timeslot,
                    'updated_at' => now(),
                ]);

            $attendance = DB::table('crash_course_attendance')
                ->where('id', $request->attendance_id)
                ->where('branch_id', session('branch_id'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function deleteCrashCourseAttendance($id)
    {
        try {
            DB::table('crash_course_attendance')
                ->where('id', $id)
                ->where('branch_id', session('branch_id'))
                ->delete();

            return response()->json(['success' => true, 'message' => 'Attendance deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the Flag Candidate page.
     * User enters a family ID and sees all students under that family
     * with their active/inactive status. They can flag or undo flag.
     */
    public function flagCandidate(Request $request)
    {
        $branchId = session('branch_id');
        $familyId = $request->input('family_id');
        $students = collect();

        if ($familyId) {
            $students = \App\Models\Student::where('admissionid', $familyId)
                ->where('branch_id', $branchId)
                ->get();
        }

        return view('branchFrontend.flagCandidate.index', compact('students', 'familyId'));
    }

    /**
     * Toggle the is_flag value for a student.
     * flag action  → sets is_flag = 1
     * undo action  → sets is_flag = 0
     */
    public function toggleFlagCandidate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'action'     => 'required|in:flag,undo',
        ]);

        $branchId  = session('branch_id');
        $studentId = $request->input('student_id');
        $action    = $request->input('action');

        $student = \App\Models\Student::where('studentid', $studentId)
            ->where('branch_id', $branchId)
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $student->is_flag = ($action === 'flag') ? 1 : 0;
        $student->save();

        $message = ($action === 'flag')
            ? 'Student has been flagged successfully.'
            : 'Student flag has been removed successfully.';

        // Activity log
        $branch  = \App\Models\User::where('branch_id', $branchId)->where('is_main_branch', 1)->first();
        $logMsg  = ($action === 'flag')
            ? "Student {$student->studentname} {$student->studentsur} (ID: {$studentId}, Family: {$student->admissionid}) has been flagged"
            : "Student {$student->studentname} {$student->studentsur} (ID: {$studentId}, Family: {$student->admissionid}) flag has been removed";

        activity('Flag Candidate')
            ->performedOn($student)
            ->causedBy(auth()->user())
            ->withProperties([
                'student_id'   => $studentId,
                'family_id'    => $student->admissionid,
                'student_name' => $student->studentname . ' ' . $student->studentsur,
                'action'       => $action,
                'is_flag'      => $student->is_flag,
                'branch_id'    => $branchId,
                'branch_name'  => $branch->branch_name ?? null,
            ])
            ->event($action === 'flag' ? 'flagged' : 'unflagged')
            ->log($logMsg);

        // Set branch_id & branch_name on the log entry
        $latestActivity = \Spatie\Activitylog\Models\Activity::latest()->first();
        if ($latestActivity) {
            $latestActivity->branch_id   = $branchId;
            $latestActivity->branch_name = $branch->branch_name ?? null;
            $latestActivity->save();
        }

        return response()->json(['success' => true, 'message' => $message, 'is_flag' => $student->is_flag]);
    }

}


