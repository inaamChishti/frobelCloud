<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\{User,Session};

class CustomLoginController extends Controller
{
    public function customLogin(Request $request)
    {

        $request->validate([
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);


        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password))
        {

            Auth::login($user);

            $session = Session::where('status','active')->first();
            if($session)
            {
                session(['current_session' => $session['session_name']]);
            }
            // dd(Auth::user());
            return redirect()->route('home')->with('success', 'Logged in as admin!');
        }
        else
        {
            return back()->with('error', 'User not found.');
        }
    }

}
