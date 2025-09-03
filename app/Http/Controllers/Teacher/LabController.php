<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Lab;
use App\Models\Room;
use App\Models\Department;
use App\Models\BorrowDevice;

class LabController extends Controller
{
    public function index(Request $request)
    {
        $departments    = Department::all();
        $rooms          = Room::all();

        $limit = $request->limit ? $request->limit : 20;
        $query = Lab::orderBy('name','ASC');
        $borrow_lab_ids = [];
        if($request->item_id && $request->tiet !== ''){
            $borrow_device = BorrowDevice::where('borrow_id',$request->item_id)
            ->where('tiet',$request->tiet)
            ->first();
            if($borrow_device){
                $borrow_date    = $borrow_device->borrow_date;
                $session        = $borrow_device->session;
                $lecture_number = $borrow_device->lecture_number;
                $request->merge([
                    'borrow_date' => $borrow_date,
                    'session' => $session,
                    'lecture_number' => $lecture_number
                ]);
    
                // Find labs based on borrow_date, session, lecture_number
                $queryLab = BorrowDevice::where('lab_id','>',0)
                ->whereHas('borrow',function($query){
                    $query->where('status','>=',0);
                });
                if($request->borrow_date){
                    $queryLab->where('borrow_date',$borrow_date);
                }
                if($request->session){
                    $queryLab->where('session',$session);
                }
                if($request->lecture_number){
                    $queryLab->where('lecture_number',$lecture_number);
                }
                $borrow_labs = $queryLab->get();
                
                if($borrow_labs){
                    foreach($borrow_labs as $borrow_lab){
                        $borrow_lab_ids[$borrow_lab->lab_id] = $borrow_lab->borrow->user->name;
                    }
                }
            }
            
        }
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->lab_type_id){
            $query->where('lab_type_id',$request->lab_type_id);
        }
        if($request->department_id){
            $query->where('department_id',$request->department_id);
        }
        $items = $query->paginate($limit);
        $param = [
            'items' => $items,
            'rooms' => $rooms,
            'departments' => $departments,
            'borrow_lab_ids' => $borrow_lab_ids,
        ];
        if( $request->ajax() ){
            return view('teacher.labs.index-ajax',$param);
        }
        return view('teacher.labs.index',$param);
    }
}