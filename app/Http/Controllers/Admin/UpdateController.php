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
    public function doUpdate()
    {
        switch ($this->lastVersion) {
            case '2.4':
                $updated = \App\Models\Versions\Ver24::doUpdate();
                break;
            case '2.5':
                $updated = \App\Models\Versions\Ver25::doUpdate();
                break;
            default:
                $updated = \App\Models\Versions\Ver25::doUpdate();
                break;
        }

        if($updated){
            \App\Models\Option::where('option_name','app_verison')->delete();
            $option = \App\Models\Option::where('option_name','app_verison')->first();
            if($option){
                $option->option_value = $this->lastVersion;
                $option->save();
            }else{
                \App\Models\Option::create([
                    'option_value' => $this->lastVersion,
                    'option_name' => 'app_verison',
                    'option_label' => 'Phiên bản phần mềm',
                    'option_group' => 'system',
                    'option_group_name' => 'Hệ Thống',
                ]);
            }
            return redirect()->back()->with('success','Cập nhật thành công !');
        }else{
            return redirect()->back()->with('error','Cập nhật không thành công !');
        }
    }
}
