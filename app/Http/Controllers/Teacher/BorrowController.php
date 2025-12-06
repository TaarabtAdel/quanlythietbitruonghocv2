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
use App\Models\BorrowDeviceFake;
use Illuminate\Support\Facades\DB;

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
            $borrow_fake_items = $item->borrow_fake_items;
            if( $item->user_id != Auth::id() ){
                abort(403);
            }
            $params = [
                'route_prefix'  => $this->route_prefix,
                'view_path'     => $this->view_path,
                'model'         => $this->model,
                'item'          => $item,
                'borrow_fake_items' => $borrow_fake_items,
                'rooms'         => $rooms,
            ];
            return view($this->view_path.'show', $params);
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
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
            $borrow_fake_items = $item->borrow_fake_items;
            $params = [
                'route_prefix'  => $this->route_prefix,
                'model'         => $this->model,
                'item'          => $item,
                'borrow_fake_items' => $borrow_fake_items,
                'rooms'         => $rooms
            ];
            return view($this->view_path.'edit', $params);
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
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
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
            if( $request->ajax() ){
                return response()->json([
                    'success' => false,
                    'msg' => __('sys.item_not_found'),
                ]);
            }
            // return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            //Log::error('Error in update method: ' . $e->getMessage());
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
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            //Log::error('Error in destroy method: ' . $e->getMessage());
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
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            //Log::error('Error in copy method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.copy_item_error'));
        }
    }

    public function saveFakeDevices(Request $request, $id)
    {
        $tiet_id = $request->tiet_id;
        $device_fakes = $request->device_fakes;

        if (empty($device_fakes) || !is_array($device_fakes)) {
            return response()->json(['message' => 'No device data provided'], 400);
        }
        DB::beginTransaction();
        try {
            // Xóa dữ liệu cũ cùng borrow_id + tiết (nếu cần làm mới)
            BorrowDeviceFake::where('borrow_id', $id)
                ->where('tiet', $tiet_id)
                ->delete();
            // Lưu danh sách mới
            foreach ($device_fakes as $fake) {
                if (!empty($fake['device_name']) && isset($fake['qty'])) {
                    BorrowDeviceFake::create([
                        'borrow_id'    => $id,
                        'device_name'  => $fake['device_name'],
                        'quantity'     => (int) $fake['qty'],
                        'tiet'         => $tiet_id,
                    ]);
                }
            }
            DB::commit();
            return response()->json(['success'=>true, 'message' => 'Fake devices saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success'=>false, 'message' => $e->getMessage()]);
        }
    }

    public function getFakeDevices(Request $request)
    {
        $id = $request->query('id');
        $tiet_id = $request->query('tiet_id', ''); // mặc định rỗng nếu không truyền
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Missing borrow_id (id)'
            ]);
        }
        // Base query
        $query = BorrowDeviceFake::where('borrow_id', $id);
        // Nếu có truyền tiet_id (kể cả = 0), lọc theo tiet_id
        if ($tiet_id !== '' && $tiet_id !== null) {
            $query->where('tiet', $tiet_id);
        }
        $devices = $query->orderBy('tiet')->get();
        // Nếu có tiet_id thì không cần nhóm, trả thẳng mảng thiết bị
        if ($tiet_id !== '' && $tiet_id !== null) {
            $data = $devices->map(function ($item) {
                return [
                    'id'          => $item->id,
                    'device_name' => $item->device_name,
                    'quantity'    => (int) $item->quantity,
                    'tiet_id'     => (int) $item->tiet,
                ];
            });
            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        }
        // Nếu không truyền tiet_id => nhóm theo 'tiet'
        $grouped = $devices->groupBy('tiet')->map(function ($items, $tiet) {
            return [
                'tiet_id' => (int) $tiet,
                'devices' => $items->map(function ($item) {
                    return [
                        'id'          => $item->id,
                        'device_name' => $item->device_name,
                        'quantity'    => (int) $item->quantity,
                    ];
                })->values(),
            ];
        })->values();
        return response()->json([
            'success' => true,
            'data'    => $grouped,
        ]);
    }

    // delete_fake_device
    public function delete_fake_device(Request $request)
    {
        $device_id = $request->input('fake_device_id');
        if (!$device_id) {
            return response()->json(['success' => false, 'message' => 'Missing device_id'], 400);
        }
        try {
            $device = BorrowDeviceFake::findOrFail($device_id);
            $device->delete();
            return response()->json(['success' => true, 'message' => 'Device deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting device'], 500);
        }
        
    }
    // update_qty_fake_device
    public function update_qty_fake_device(Request $request)
    {
        $device_id = $request->input('fake_device_id');
        $quantity = $request->input('quantity');
        if (!$device_id || !is_numeric($quantity) || $quantity < 0) {
            return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
        }
        try {
            $device = BorrowDeviceFake::findOrFail($device_id);
            $device->quantity = (int)$quantity;
            $device->save();
            return response()->json(['success' => true, 'message' => 'Quantity updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating quantity'], 500);
        }
    }

}