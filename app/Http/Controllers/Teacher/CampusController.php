<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Services\CampusService;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function select()
    {
        if (! CampusService::hasAffiliated()) {
            return redirect()->intended($this->homeUrl());
        }

        return view('teacher.auth.select-campus', [
            'campuses' => CampusService::selectable(),
            'currentKey' => session(CampusService::SESSION_KEY, CampusService::MAIN_KEY),
        ]);
    }

    public function choose(Request $request)
    {
        $validated = $request->validate([
            'campus_key' => ['required', 'string'],
        ]);

        if (CampusService::isMainAdmin()) {
            $error = CampusService::connectTo($validated['campus_key']);
            if (! $error) {
                CampusService::putSessionKey($validated['campus_key']);
            }
        } else {
            $error = CampusService::choose($validated['campus_key']);
        }

        if ($error) {
            return back()->with('error', $error);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->homeUrl());
    }

    protected function homeUrl(): string
    {
        $intended = session('url.intended');

        if (is_string($intended) && str_contains($intended, '/admin')) {
            return '/admin';
        }

        return '/';
    }
}
