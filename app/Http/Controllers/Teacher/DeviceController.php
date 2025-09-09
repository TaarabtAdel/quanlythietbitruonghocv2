<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceType;
use App\Models\Department;

class DeviceController extends Controller
{
    protected $view_path    = 'device::';
    protected $route_prefix = 'device.';
    protected $model        = Device::class;
    public function index(Request $request)
    {
        $device_types = DeviceType::all();
        $departments = Department::all();
        $limit = $request->limit ? $request->limit : 20;
        $query = $this->model::orderBy('name','ASC');
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
        if( $request->ajax() ){
            return view('teacher.devices.index-ajax',$param);
        }
        return view('teacher.devices.index',$param);
    }
}
