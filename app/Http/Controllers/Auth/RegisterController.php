<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'visible_password' => $data['password'],
        ]);
    }

    public function customlogin(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $user = User::where('name', $credentials['username'])
                    ->where('visible_password', $credentials['password'])
                    ->first();

        if ($user) {
            if ($user->role !== 'super_admin') {
                session(['branch_id' => $user->branch_id]);
            }
            $user->login_session = 1;
            $user->save();
            Auth::login($user);

            if ($user->role === 'super_admin') {
                return redirect()->route('home');
            } else {
                return redirect()->route('branch.dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }
}
