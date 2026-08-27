<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CampusService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function logout(Request $request)
    {
        auth()->logout();
        CampusService::forget();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (CampusService::hasAffiliated()) {
            $request->validate([
                'campus_key' => ['required', 'string'],
            ]);
            $error = CampusService::connectTo($request->input('campus_key'));
            if ($error) {
                return back()->with('error', $error)->onlyInput('email', 'campus_key');
            }
        }

        if (auth()->attempt($credentials)) {
            $request->session()->regenerate();
            CampusService::putSessionKey($request->input('campus_key', CampusService::MAIN_KEY));
            CampusService::markMainAdmin(($request->input('campus_key', CampusService::MAIN_KEY) === CampusService::MAIN_KEY));
            CampusService::rememberLogin(auth()->user());

            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
