<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Borrow;
use App\Models\Notification;
use App\Policies\BorrowPolicy;


class BorrowController extends Controller
{
    protected $view_path    = 'admin.borrows.';
    protected $route_prefix = 'admin.borrows.';
    protected $model        = Borrow::class;
    public function index(Request $request)
    {
        $this->authorize('viewAny', $this->model);
        try {
            $notiid = $request->notiid;
            if($notiid){
                Notification::deleteNotification($notiid);
            }
            if($request->task && $request->task == 'approve'){
                $id = $request->id;
                $borrow = $this->model::findItem($id);
                $borrow->status = 1;
                $borrow->save();
                if( $request->redirect){
                    return redirect()->back()->with('success', __('sys.update_item_success'));
                }
            }

            $query = Borrow::query(true);
            // $query->whereNull('deleted_at');
            if( $request->status === NULL ){
                $query->whereIn('status',[
                    Borrow::ACTIVE,
                    Borrow::INACTIVE,
                    Borrow::CANCELED
                ]);
            }else{
                $query->where('status',$request->status);
            }
            if( $request->user_id){
                $query->where('user_id',$request->user_id);
            }
            if( $request->borrow_date){
                $query->whereDate('borrow_date',$request->borrow_date);
            }
            if($request->nest_id){
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('nest_id', $request->nest_id );
                });
            }

            $startDateEndDate = [];
            if( $request->week ){
                $startDateEndDate = $this->model::getStartEndDateFromWeek($request->week);
                $query->whereBetween('borrow_date', $startDateEndDate);
            }
            if ($request->school_years) {
                $startDateEndDate = $this->model::getStartEndDateFromYear($request->school_years);
                $query->whereBetween('borrow_date', $startDateEndDate);
            }
            $query->orderBy('id','DESC');
            $items = $query->paginate(20);

            $borrow_purposes = \App\Models\Borrow::get_borrow_purposes();
            

            $params = [
                'route_prefix'  => $this->route_prefix,
                'model'         => $this->model,
                'items'         => $items,
                'borrow_purposes' => $borrow_purposes,
            ];
            $params = array_merge($params,$startDateEndDate);

            return view($this->view_path.'index', $params);
        } catch (QueryException $e) {
            Log::error('Error in index method: ' . $e->getMessage());
            return redirect()->back()->with('error',  __('sys.get_items_error'));
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $rooms = \App\Models\Room::getAll();
            $item = $this->model::findItem($id);
            $this->authorize('view', $item);
            $params = [
                'route_prefix'  => $this->route_prefix,
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
            $rooms = \App\Models\Room::getAll();
            $item = $this->model::findItem($id);
            $this->authorize('update', $item);
            $params = [
                'route_prefix'  => $this->route_prefix,
                'model'         => $this->model,
                'item'          => $item,
                'rooms'         => $rooms,
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
    public function update(Request $request, $id)
    {
        try {
            $this->model::updateItem($id,$request);
            return redirect()->route($this->route_prefix.'index')->with('success', __('sys.update_item_success'));
        } catch (ModelNotFoundException $e) {
            Log::error('Item not found: ' . $e->getMessage());
            if( $request->ajax() ){
                return response()->json([
                    'success' => false,
                    'msg' => __('sys.item_not_found'),
                ]);
            }
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            Log::error('Error in update method: ' . $e->getMessage());
            if( $request->ajax() ){
                return response()->json([
                    'success' => false,
                    'msg' => __('sys.update_item_error'),
                ]);
            }
            return redirect()->back()->with('error', __('sys.update_item_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $item = $this->model::findItem($id);
            $this->authorize('delete', $item);
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
}