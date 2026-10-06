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

            // Reset redirect count — user is authenticated and authorized
            Session::forget('super_admin_redirect_count');

            // All good → allow request to proceed
            return $next($request);
        }

        // If not super admin → track redirect count and redirect to branch dashboard
        $redirectCount = Session::get('super_admin_redirect_count', 0);
        $redirectCount++;
        Session::put('super_admin_redirect_count', $redirectCount);

        if ($redirectCount >= 3) {
            Auth::logout();
            Session::forget('super_admin_redirect_count');
            return redirect('/login')->with('error', 'You were logged out due to multiple unauthorized access attempts.');
        }

        return redirect('/branch-dashboard');
    }
}
