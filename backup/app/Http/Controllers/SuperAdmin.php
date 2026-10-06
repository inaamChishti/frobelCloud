<?php

namespace App\Http\Controllers;

use App\Models\{User, SuperadminRole};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\{AccessPermission, GeneralTimetable, Attendance};

class SuperAdmin extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

  public function index()
{
    // $user = auth()->user();
    //     if ($user->role === 'super_admin') {
    //         $superAdminPermissions = AccessPermission::where('user_id', $user->id)->first();
    //         $hasBranchContext = Session::has('branch_id') || ($superAdminPermissions && $superAdminPermissions->branch_id);

    //         // Restrict access if in branch context
    //         if ($hasBranchContext) {
    //             \Log::info('Super admin attempted to access /home while in branch context', [
    //                 'user_id' => $user->id,
    //                 'session_branch_id' => Session::get('branch_id'),
    //                 'permissions_branch_id' => $superAdminPermissions ? $superAdminPermissions->branch_id : null,
    //             ]);

    //             // Redirect to branch dashboard with error message
    //             return redirect()->route('branch.dashboard')->with('error', 'Please use "Back to Super Admin Dashboard" or log out to access the super admin dashboard.');
    //         }}

    $today = date('Y-m-d'); // e.g., 2025-08-01

    // Fetch main branches
    $mainBranches = User::where('is_main_branch', 1)
        ->whereNotNull('branch_id')
        ->pluck('branch_name', 'branch_id');

    $branchStats = [];

    foreach ($mainBranches as $branchId => $branchName) {
        // Fetch today's timetable for the branch
        $todayTimetable = GeneralTimetable::whereDate('date', $today)
            ->where('branch_id', $branchId)
            ->get()
            ->map(function ($record) {
                // Decode JSON fields
                $jsonFields = ['student_ids', 'student_names', 'subjects', 'is_attendance'];
                foreach ($jsonFields as $field) {
                    $value = $record->$field;
                    if (is_string($value)) {
                        $cleaned = trim($value, '"');
                        $decoded = json_decode($cleaned, true);
                        $record->$field = is_array($decoded) ? array_map('strval', $decoded) : [];
                    }
                }
                return $record;
            });

        // Build student entries (including duplicates)
        $studentEntries = [];
        foreach ($todayTimetable as $record) {
            if ($record->date === $today && $record->branch_id === $branchId) {
                foreach ($record->student_ids as $index => $id) {
                    $id = (string) $id; // Normalize to string
                    $studentEntries[] = [
                        'family_id' => $id,
                        'name' => $record->student_names[$index] ?? 'Unknown',
                        'time_slot' => $record->time_slot,
                        'teacher_id' => $record->teacher_id,
                        'subject' => $record->subjects[$index] ?? 'Unknown',
                    ];
                }
            }
        }
        $totalStudents = count($studentEntries);

        // Log student entries for debugging
        Log::info("Branch: $branchName, Student Entries: ", $studentEntries);

        // Fetch attendance records for today
        $attendanceRecords = Attendance::whereDate('date', $today)
            ->where('branch_id', $branchId)
            ->get()
            ->map(function ($record) {
                $record->family_id = (string) $record->family_id; // Normalize to string
                return $record;
            });

        // Log attendance records for debugging
        Log::info("Branch: $branchName, Attendance Records: ", $attendanceRecords->toArray());

        // Count attended instances
        $attendedCount = 0;
        foreach ($studentEntries as $entry) {
            $familyId = $entry['family_id'];
            $timeSlot = $entry['time_slot'];
            $teacherId = $entry['teacher_id'];
            $subject = $entry['subject'];

            // Check for matching attendance record
            $matchingAttendance = $attendanceRecords->first(function ($record) use ($familyId, $timeSlot, $teacherId, $subject, $today) {
                return $record->family_id === $familyId &&
                       $record->time_slot === $timeSlot &&
                       $record->teacher_name === $teacherId &&
                       $record->subject === $subject &&
                       $record->date === $today;
            });

            if ($matchingAttendance) {
                $attendedCount++;
            }
        }

        // Store stats
        $branchStats[] = [
            'branch_id' => $branchId,
            'branch_name' => $branchName,
            'totalStudents' => $totalStudents,
            'attendedCount' => $attendedCount,
        ];
    }

    // Log final stats
    Log::info("Branch Stats: ", $branchStats);

    // Prepare view data
    $data = [
        'branchStats' => $branchStats,
        'today' => $today,
    ];

    return view('home', $data);
}
    public function createBranch()
    {
        $users = User::all();
        return view('superAdmin.branch.createBranch', compact('users'));
    }

    public function storeBranch(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:50',
            // 'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'branch_name' => 'required_if:role,branch_admin|max:255',
            'branch_id' => 'required_if:role,branch_admin|string|unique:users,branch_id',
            'role' => 'required|in:super_admin,branch_admin',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Failed to create branch. Please check the errors below.');
        }



        try {
            $user = User::create([
                'name' => $request->username,
                'email' => $request->username . '@frobel.com',
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'branch_name' => $request->role === 'branch_admin' ? $request->branch_name : null,
                'branch_id' => $request->role === 'branch_admin' ? $request->branch_id : null,
                'visible_password' => $request->password,
                'is_main_branch' => $request->role === 'branch_admin' ? 1 : null,
                'usertype' => $request->role === 'branch_admin' ? 'Superadmin' : null,

            ]);

            if ($request->role === 'branch_admin') {
                AccessPermission::create([
                    'user_id' => $user->id,
                    'page_name' => json_encode(['manage_permissions' => 'on']),
                    'can_access' => 1,
                    'branch_id' => $request->branch_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->back()->with('success', 'Branch created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    public function listBranch()
    {
        $branches = User::where('role', '!=', 'super_admin')->where('is_main_branch', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superAdmin.branch.ListBranch', compact('branches'));
    }


    public function editBranch($id)
    {
        $users = User::where('id', $id)->first();
        $allUSers = User::all();
        // dd($users);
        return view('superAdmin.branch.edit', compact('users', 'allUSers'));
    }

    public function updateBranch(Request $request, $id)
    {

        $user = User::findOrFail($id);
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:super_admin,branch_admin',

        ]);

        $user->name = $request->username;
        $user->role = $request->role;
        $user->is_main_branch = ($request->role === 'branch_admin') ? 1 : null;
        $user->usertype = ($request->role === 'branch_admin') ? 'Superadmin' : null;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->visible_password = $request->password; // Store plain password (not recommended for production)
        }
        $user->save();

        return redirect()->route('listBranch')->with('success', 'Branch updated successfully.');
    }

    public function deleteBranch($id)
    {

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('listBranch')->with('success', 'Branch deleted successfully.');
    }

    // public function addUser(Request $request)
    // {
    //     try {
    //         $users = User::where('is_super_user', 1)
    //             ->select('id', 'name', 'email', 'usertype', 'login_session', 'visible_password')
    //             ->get();

    //         Log::info('Super Admin Users query results:', $users->toArray());

    //         $roles = SuperadminRole::all();

    //         return view('superAdmin.user.addUser', compact('users', 'roles'));
    //     } catch (\Exception $e) {
    //         Log::error('Error in addUser: ' . $e->getMessage());
    //         return response()->json(['error' => 'Server error occurred'], 500);
    //     }
    // }

    public function manageRoles()
    {
        $roles = SuperadminRole::all();
        return view('superAdmin.roles.manage-roles', compact('roles'));
    }

    // Store a new role
    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:superadmin_roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $role = SuperadminRole::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Role created successfully!',
            'role' => $role,
        ], 200);
    }

    // Show edit form for a role
    public function editRole($id)
    {
        $role = SuperadminRole::findOrFail($id);
        return response()->json([
            'role' => $role,
        ], 200);
    }

    // Update a role
    public function updateRole(Request $request, $id)
    {
        $role = SuperadminRole::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:superadmin_roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $role->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Role updated successfully!',
            'role' => $role,
        ], 200);
    }

    // Delete a role
    public function deleteRole($id)
    {
        $role = SuperadminRole::findOrFail($id);
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully!',
        ], 200);
    }



    public function manageSuperadminUsers()
    {
        try {
            $users = User::Where('role', 'super_admin')
                ->select('id', 'name', 'email', 'role', 'login_session', 'visible_password', 'usertype')
                ->get();
            $roles = SuperadminRole::select('id', 'name')->get();

            Log::info('Super Admin Users query results:', $users->toArray());

            return view('superAdmin.users.manage-superadmin-users', compact('users', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error in manageSuperadminUsers: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred'], 500);
        }
    }

    public function storeSuperadminUser(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                $request->user_id
                    ? 'unique:users,name,' . $request->user_id
                    : 'unique:users,name',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                $request->user_id
                    ? 'unique:users,email,' . $request->user_id
                    : 'unique:users,email',
            ],
            'password' => $request->user_id ? 'nullable|min:8' : 'required|min:8',
            'role' => 'nullable|exists:superadmin_roles,name',
        ], [
            'username.unique' => 'The username is already taken by another user in the system.',
            'email.unique' => 'The email is already taken by another user in the system.',
            'role.exists' => 'The selected role is invalid.',
        ]);

        try {
            $user = $request->user_id ? User::findOrFail($request->user_id) : new User;
            $user->name = $request->username;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
                $user->visible_password = $request->password;
            }

            $user->role = 'super_admin';
            $user->usertype = $request->role;
            $user->is_super_user = 1;
            $user->save();

            return response()->json(['success' => 'User saved successfully']);
        } catch (\Exception $e) {
            Log::error('Error in storeSuperadminUser: ' . $e->getMessage());
            return response()->json([
                'errorMessage' => 'An error occurred while saving the user. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editSuperadminUser($id)
    {
        try {
            $user = User::Where('role', 'super_admin')->findOrFail($id);
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            Log::error('Error in editSuperadminUser: ' . $e->getMessage());
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function deleteSuperadminUser($id)
    {
        try {
            $user = User::Where('role', 'super_admin')->findOrFail($id);
            $user->delete();
            return response()->json(['success' => 'User deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error in deleteSuperadminUser: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }

    public function logoutSingleSuperadminUser($id)
    {
        try {
            $user = User::Where('role', 'super_admin')->findOrFail($id);
            $user->login_session = 0;
            $user->save();
            return redirect()->route('manageSuperadminUsers')->with('success', 'User logged out successfully');
        } catch (\Exception $e) {
            Log::error('Error in logoutSingleSuperadminUser: ' . $e->getMessage());
            return redirect()->route('manageSuperadminUsers')->with('error', 'Failed to logout user');
        }
    }

    public function managePermission()
    {
        $users = User::Where('role', 'super_admin')->get();
        // dd($users);
        return view('superAdmin.permission.index', compact('users'));
    }

    public function allowPermission($id)
    {
        $user = User::Where('id', $id)->first();
        $permission = AccessPermission::where('user_id', $id)->first();

        if ($permission && isset($permission->page_name)) {
            $permission->page_name = json_decode($permission->page_name, true);
        }

        return view('superAdmin.permission.managePermission', compact('user', 'permission'));
    }
    public function storePermission(Request $request)
    {
        // dd($request->all());
        $filteredData = $request->except(['user_id', '_token']);
        $jsonPermissions = json_encode($filteredData);

        AccessPermission::updateOrCreate(
            [
                'user_id' => $request->input('user_id'),
            ],
            [
                'page_name' => $jsonPermissions,
                'can_access' => true
            ]
        );

        return redirect()->route('superadmin.allow.permission', ['id' => $request->input('user_id')])
            ->with('success', 'Permissions stored successfully.');
    }
}
