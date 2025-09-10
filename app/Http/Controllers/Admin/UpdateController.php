<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function __construct(){
        $this->lastVersion = env('SYSTEM_VERSION',2.2);
    }
    public function index()
    {
        $option = \App\Models\Option::where('option_name','app_verison')->first();
        $currentVersion = $option->option_value ?? '1.0';
        $params = [
            'currentVersion' => $currentVersion,
            'lastVersion' => $this->lastVersion,
        ];
        return view('admin.update.index',$params);
    }
}
