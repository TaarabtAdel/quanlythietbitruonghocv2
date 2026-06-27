<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemUpdateService;

class UpdateController extends Controller
{
    public function index()
    {
        return view('admin.update.index', [
            'currentVersion' => SystemUpdateService::currentVersion(),
            'lastVersion' => SystemUpdateService::targetVersion(),
        ]);
    }

    public function doUpdate()
    {
        $result = SystemUpdateService::run();

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
