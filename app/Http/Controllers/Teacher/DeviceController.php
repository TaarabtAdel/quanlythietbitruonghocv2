<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Department;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $device_types = DeviceType::all();
        $departments = Department::all();
        $limit = $request->limit ? $request->limit : 20;
        $query = Device::orderBy('name','ASC');
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->device_type_id){
            $query->where('device_type_id',$request->device_type_id);
        }
        if($request->department_id){
            $query->where('department_id',$request->department_id);
        }
        $items = $query->paginate($limit);
        $param = [
            'items' => $items,
            'device_types' => $device_types,
            'departments' => $departments,
            'request' => $request
        ];
        return view('teacher.devices.index', $param );
    }
}
