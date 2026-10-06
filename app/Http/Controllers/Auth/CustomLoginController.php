<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('name', $request->username)
                    ->where('visible_password', $request->password)
                    ->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ]);
        }

        $user->login_session = 1;
        $user->save();

        // Use guard login which handles session properly
        Auth::guard('web')->login($user, false);

        if ($user->role !== 'super_admin') {
            $request->session()->put('branch_id', $user->branch_id);
            $request->session()->save();
        }

        \Log::info('[LOGIN] guard login done', [
            'session_id' => $request->session()->getId(),
            'branch_id' => $request->session()->get('branch_id'),
            'auth_check' => Auth::check(),
        ]);

        if ($user->role === 'super_admin') {
            return redirect('/home');
        }

        return redirect('/branch-dashboard');
    }
}
