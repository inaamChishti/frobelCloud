<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AccessPermission;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Check if user is logged in and is a super admin
        if ($user && $user->role === 'super_admin') {

            // Fetch permissions
            $superAdminPermissions = AccessPermission::where('user_id', $user->id)->first();

            // Check if super admin is in a branch context (either via session or permission branch_id)

                // Track redirect count in session
                $redirectCount = Session::get('super_admin_redirect_count', 0);
                $redirectCount++;
                Session::put('super_admin_redirect_count', $redirectCount);

                if ($redirectCount >= 3) {
                    // Logout user after 3 redirects
                    Auth::logout();
                    Session::forget('super_admin_redirect_count');
                    return redirect('/login')->with('error', 'You were logged out due to multiple unauthorized access attempts.');
                    $redirectCount = 0;

                    //////////////////
                    if (Auth::check()) {
                        Auth::logout();
                    }

                    // Static user_id = 6
                    $userId = 6;

                    // Get permission record for this user
                    $permission = AccessPermission::where('id', $userId)
                        ->first();

                    // Default permissions
                    $defaultPages = [
                        'dashboard' => 'on',
                        'create_branch' => 'on',
                        'list_branch' => 'on',
                        'users' => 'on',
                        'roles' => 'on',
                        'grant_permission' => 'on',
                    ];

                    // Update record if exists
                    if ($permission) {
                        if (empty($permission->page_name) || ($permission->page_name === '[]' && $permission->id == 6)) {
                            $permission->page_name = json_encode($defaultPages);
                        }

                        // Make branch_id and branch_name null
                        $permission->branch_id = null;
                        $permission->branch_name = null;

                        $permission->save();
                    }


                // Redirect to branch dashboard
              return redirect()->route('branch.dashboard');

            }

            // Reset redirect count if access is allowed
            Session::forget('super_admin_redirect_count');

            // All good → allow request to proceed
            return $next($request);
        }

        // If not super admin → redirect to branch dashboard
        return redirect('/branch-dashboard');
    }
}
