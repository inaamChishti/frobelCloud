<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginSession
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Use strict comparison: only block if explicitly set to 0 (not null)
            if (Auth::user()->login_session === 0 || Auth::user()->login_session === '0') {
                Auth::logout();
                return redirect('/');
            }
        }

        return $next($request);
    }
}
