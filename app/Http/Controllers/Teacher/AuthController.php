<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\CampusService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('teacher.auth.login', [
            'campuses' => CampusService::selectable(),
            'showCampusSelect' => true,
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        CampusService::forget();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email' => ['required'],
            'password' => ['required'],
            'campus_key' => ['required', 'string'],
        ];

        $credentials = $request->validate($rules);

        $campusKey = $credentials['campus_key'] ?? CampusService::MAIN_KEY;
        $error = CampusService::connectTo($campusKey);

        if ($error) {
            return back()->with('error', $error)->onlyInput('email', 'campus_key');
        }

        $login_type = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $attempt = [
            $login_type => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (auth()->attempt($attempt, $request->boolean('remember'))) {
            $request->session()->regenerate();
            CampusService::putSessionKey($campusKey);
            CampusService::markMainAdmin($campusKey === CampusService::MAIN_KEY);
            CampusService::rememberLogin(auth()->user());

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email', 'campus_key');
    }
}
