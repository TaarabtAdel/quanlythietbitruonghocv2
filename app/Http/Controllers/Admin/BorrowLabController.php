<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\BorrowLab;
use App\Models\Borrow;
use Carbon\Carbon;

class BorrowLabController extends Controller
{
    protected $view_path    = 'admin.borrow_labs';
    protected $route_prefix = 'admin.borrows.';
    protected $model        = BorrowLab::class;
    public function index(Request $request)
    {
        if( !auth()->user()->hasPermission('BorrowLab_viewAny')){
            abort(403);
        }
        if( !$request->week ){
            $currentWeek    = Carbon::now()->format('Y-\WW');
            $startDateEndDate = Borrow::getStartEndDateFromWeek($currentWeek);
            $request->merge([
                'week' => $currentWeek
            ]);
        }else{
            $startDateEndDate = Borrow::getStartEndDateFromWeek($request->week);
        }
        // Nếu chọn theo tổ thì hiển thị các phòng theo tổ
        if( $request->nest_id ){
            return $this->showNest($request);
        }
        // Nếu chọn theo phòng thì hiển thị chi tiết một phòng
        if( $request->lab_id ){
            return $this->showLab($request);
        }
        $items = $this->model::getItems($request);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'items'         => $items
        ];
        $params = array_merge($params,$startDateEndDate);
        return view($this->view_path.'.index', $params);
    }

    public function showNest(Request $request){
        if( !$request->week ){
            $currentWeek    = Carbon::now()->format('Y-\WW');
            $startDateEndDate = Borrow::getStartEndDateFromWeek($currentWeek);
            $request->merge([
                'week' => $currentWeek
            ]);
        }else{
            $startDateEndDate = Borrow::getStartEndDateFromWeek($request->week);
        }

        $nest_id = $request->nest_id;
        $query = $this->model::query(true);

        $query->whereBetween('borrow_date', array_values($startDateEndDate));

        if ($request->sw_start_week && $request->sw_end_week) {
            $query->whereBetween('borrow_date', [$request->sw_start_week,$request->sw_end_week]);
        }

        if($request->nest_id){
            $query->whereHas('borrow.user', function ($query) use ($request) {
                $query->where('nest_id', $request->nest_id );
            });
        }
        // Nếu chọn thêm phòng
        if( $request->lab_id ){
            $query->where('lab_id', $request->lab_id );
        }
        $lab_ids = $query->pluck('lab_id')->toArray() ?? [];

        $lab_items = [];
        if( count($lab_ids) ){
            $lab_ids = array_unique($lab_ids);
            $lab_ids = \App\Models\Lab::whereIn('id',$lab_ids)->pluck('id')->toArray() ?? [];
            $lab_items = [];
            foreach( $lab_ids as $lab_id ){
                $request->merge([
                    'lab_id' => $lab_id
                ]);
                $lab_name = \App\Models\Lab::find($lab_id)->name ?? '';
                $lab_items[$lab_name] = $this->model::getBorrowedLab($request);
            }
        }
        
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'lab_items'     => $lab_items
        ];
        $params = array_merge($params,$startDateEndDate);
        return view($this->view_path.'.showNest', $params);
    }
    public function showLab(Request $request){
        if( !$request->week ){
            $currentWeek    = Carbon::now()->format('Y-\WW');
            $startDateEndDate = Borrow::getStartEndDateFromWeek($currentWeek);
            $request->merge([
                'week' => $currentWeek
            ]);
        }else{
            $startDateEndDate = Borrow::getStartEndDateFromWeek($request->week);
        }
        $lab_id = $request->lab_id;
        $lab_name = \App\Models\Lab::find($lab_id)->name ?? '';

        $items = $this->model::getBorrowedLab($request);
        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'lab_name'         => $lab_name,
            'items'         => $items
        ];
        $params = array_merge($params,$startDateEndDate);
        return view($this->view_path.'.showLab', $params);
    }
}
