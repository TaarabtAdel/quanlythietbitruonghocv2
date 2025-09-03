<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Borrow;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowLabController extends Controller
{
    protected $view_path    = 'teacher.borrow_labs.';
    protected $route_prefix = 'borrows.';
    protected $model        = Borrow::class;
    public function index(Request $request)
    {
        if( $request->lab_id ){
            return $this->show($request);
        }
        if( !$request->week ){
            $currentWeek    = Carbon::now()->format('Y-\WW');
            $request->merge([
                'week' => $currentWeek
            ]);
        }

        $items = $this->model::getBorrowedLabs($request);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'items'         => $items
        ];
        return view($this->view_path.'index', $params);
    }

    public function show(Request $request){
        if( !$request->week ){
            $currentWeek    = Carbon::now()->format('Y-\WW');
            $startDateEndDate = $this->model::getStartEndDateFromWeek($currentWeek);
            $request->merge([
                'week' => $currentWeek
            ]);
        }else{
            $startDateEndDate = $this->model::getStartEndDateFromWeek($request->week);
        }
        $lab_id = $request->lab_id;
        $lab_name = \App\Models\Lab::find($lab_id)->name ?? '';

        $items = $this->model::getBorrowedLab($request);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'lab_name'         => $lab_name,
            'items'         => $items
        ];
        $params = array_merge($params,$startDateEndDate);
        return view($this->view_path.'show', $params);
    }
}