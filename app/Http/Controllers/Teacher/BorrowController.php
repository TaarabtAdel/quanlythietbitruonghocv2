<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBorrowRequest;
use Illuminate\Http\Response;
use App\Models\Borrow;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class BorrowController extends Controller
{
    protected $view_path    = 'teacher.borrows.';
    protected $route_prefix = 'borrows.';
    protected $model        = Borrow::class;
    public function index(Request $request)
    {
        $limit = $request->limit ? $request->limit : 20;
        $query = Borrow::query(true);
        $query->whereNull('deleted_at');

        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->status > -1){
            $query->where('status',$request->status);
        }
        if( $request->borrow_date){
                $query->whereDate('borrow_date',$request->borrow_date);
            }

        if ($request && $request->school_years) {
            $yearRange = explode('-', $request->school_years);
            if (count($yearRange) == 2) {
                $startYear = trim($yearRange[0]);
                $endYear = trim($yearRange[1]);
                // Tính toán ngày bắt đầu và ngày kết thúc dựa vào năm học
                $startDate = $startYear . '-08-01'; // Năm học bắt đầu từ tháng 8
                $endDate = $endYear . '-07-01'; // Năm học kết thúc vào tháng 7 năm sau
                $query->whereBetween('borrow_date', [$startDate, $endDate]);
            }
        }
        $query->orderBy('borrow_date','DESC');
        if( $request->week ){
            $week = $request->week;
            $year = substr($week, 0, 4);
            $weekNumber = substr($week, -2);
            $startDate = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            $endDate = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();
            $query->whereBetween('borrow_date', [$startDate, $endDate]);
            $query->orderBy('borrow_date','ASC');
        }

        $query->where('user_id',Auth::id());
        $query->orderBy('id','DESC');
        $items = $query->paginate($limit);
        
        $params = [
            'route_prefix'  => $this->route_prefix,
            'view_path'     => $this->view_path,
            'model'         => $this->model,
            'items'         => $items
        ];
        return view($this->view_path.'index', $params);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $saved = $this->model::create([
                'user_id' => Auth::id(),
                'borrow_date' => date('Y-m-d',strtotime('+1day')),
                'status' => $this->model::DRAFT
            ]);
            return redirect()->route($this->route_prefix.'edit',$saved->id)->with('success', __('sys.store_item_success'));
        } catch (QueryException $e) {
            Log::error('Error in store method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.store_item_error'));
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $rooms = \App\Models\Room::all();
            $item = $this->model::find($id);
            if( $item->user_id != Auth::id() ){
                abort(403);
            }
            $params = [
                'route_prefix'  => $this->route_prefix,
                'view_path'     => $this->view_path,
                'model'         => $this->model,
                'item'          => $item,
                'rooms'         => $rooms,
            ];
            return view($this->view_path.'show', $params);
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            return redirect()->route($route_prefix.'index')->with('error', __('sys.item_not_found'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $rooms = \App\Models\Room::all();
            $item = $this->model::find($id);
            if( $item->user_id != Auth::id() ){
                abort(403);
            }
            if( !$item->can_edit  ){
                abort(403);
            }

            $params = [
                'route_prefix'  => $this->route_prefix,
                'model'         => $this->model,
                'item'          => $item,
                'rooms'         => $rooms
            ];
            return view($this->view_path.'edit', $params);
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBorrowRequest $request, $id)
    {
        try {
            $return = $this->model::updateItem($id,$request);
            if( $request->ajax() ){
                if( $return['success'] ){
                    return response()->json([
                        'success' => true,
                        'msg' => __('sys.update_item_success'),
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'msg' => $return['message'],
                    ]);
                }
            }
            return redirect()->route($this->route_prefix.'index')->with('success', __('sys.update_item_success'));
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            if( $request->ajax() ){
                return response()->json([
                    'success' => false,
                    'msg' => __('sys.item_not_found'),
                ]);
            }
            // return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            if( $request->ajax() ){
                return response()->json([
                    'success' => false,
                    'msg' => __('sys.update_item_error'),
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->model::deleteItem($id);
            return redirect()->route($this->route_prefix.'index')->with('success', __('sys.destroy_item_success'));
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            Log::error('Error in destroy method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.destroy_item_error'));
        }
    }
     /**
     * Copy the specified resource from storage.
     */
    public function copy($id)
    {
        try {
            $this->model::copyItem($id);
            return redirect()->route($this->route_prefix.'index')->with('success', __('sys.copy_item_success'));
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            Log::error('Error in copy method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.copy_item_error'));
        }
    }
}