<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\BorrowDevice;
use App\Models\Borrow;
use Carbon\Carbon;

class BorrowDeviceController extends Controller
{
    protected $view_path    = 'admin.borrow_devices.';
    protected $route_prefix = 'admin.borrows.';
    protected $model        = BorrowDevice::class;
    public function index(Request $request)
    {
        if(auth()->user()->hasPermission('BorrowDevice_viewAny')){
            if( !$request->week ){
                $currentWeek    = Carbon::now()->format('Y-\WW');
                $startDateEndDate = Borrow::getStartEndDateFromWeek($currentWeek);
                $request->merge([
                    'week' => $currentWeek
                ]);
            }else{
                $startDateEndDate = Borrow::getStartEndDateFromWeek($request->week);
            }
            $items = $this->model::getItems($request);
            $params = [
                'route_prefix'  => $this->route_prefix,
                'model'         => $this->model,
                'items'         => $items
            ];
            $params = array_merge($params,$startDateEndDate);
            return view($this->view_path.'index', $params);
        }else{
            abort(403);
        }
        
    }
}
