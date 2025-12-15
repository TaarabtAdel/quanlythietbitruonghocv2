<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

use App\Models\InventoryAudit;
use App\Models\InventoryRecord; 
use App\Models\Device; 

class InventoryAuditController extends Controller
{
    protected $view_path    = 'admin.inventory_audits';
    protected $route_prefix = 'admin.inventory_audits';
    protected $model        = InventoryAudit::class;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->model::orderBy('created_at','DESC');

        if ($request->filled('name')) {
            $name = trim($request->name);
            $query->where('name', 'like', "%{$name}%");
        }
        if ($request->status) {
            $query->where('status',$request->status);
        }
        if ($request->school_year) {
            $query->where('school_year',$request->school_year);
        }

        $items = $query->paginate(20)->appends($request->except(['_token', '_method']));

        if( $request->ajax() ){
            return response()->json([
                'success' => true,
                'data' => $items,
            ]);
        }

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'items'         => $items,
        ];

        return view($this->view_path.'.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $saved = $this->model::create([
                'name' => 'Kiểm kê ngày '.date('d/m/Y'),
                'user_id' => Auth::id(),
                'date' => date('Y-m-d'),
                'status' => $this->model::DRAFT
            ]);
            return redirect()->route($this->route_prefix.'.edit',$saved->id)->with('success', __('sys.store_item_success'));
        } catch (QueryException $e) {
            //Log::error('Error in store method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.store_item_error'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->model::findOrFail($id);

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => $item,
        ];

        return view($this->view_path.'.show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = $this->model::findOrFail($id);

        $params = [
            'route_prefix'  => $this->route_prefix,
            'model'         => $this->model,
            'view_path'     => $this->view_path,
            'item'          => $item,
        ];
        return view($this->view_path.'.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Validate dữ liệu cần thiết
        $request->validate([
            'name' => 'required|string|max:255',
            'audit_date' => 'nullable|date',
            'devices' => 'required|array',
            'devices.*.device_id' => 'required_with:devices|integer|exists:devices,id',
            'devices.*.initial_total' => 'nullable|integer',
            // Thêm các rules validation khác cho các trường còn lại...
        ]);

        DB::beginTransaction();
        try {
            $audit = InventoryAudit::findOrFail($id);
            $inventoryAuditId = $audit->id;
            
            // Cập nhật InventoryAudit (giữ nguyên logic của phần trước)
            $audit->update([
                'name' => $request->input('name'),
                'audit_date' => $request->input('audit_date'),
                'school_year' => $request->input('school_year'),
                'status' => $request->input('status'),
                'note' => $request->input('note'),
                // ... (các trường khác)
            ]);

            // --- PHẦN 2: ĐỒNG BỘ BẢNG INVENTORY_RECORDS BẰNG UPSERT ---

            // 2. Xóa các bản ghi chi tiết bị loại bỏ khỏi Request
            InventoryRecord::where('inventory_audit_id', $inventoryAuditId)->delete();
            
            $devices = $request->input('devices', []);
            $recordsToUpsert = [];
            $deviceIdsInRequest = []; // Dùng để theo dõi các ID thiết bị được gửi lên

            foreach ($devices as $deviceRecord) {
                $deviceId = (int) $deviceRecord['device_id'];
                $deviceIdsInRequest[] = $deviceId;
                
                $recordsToUpsert[] = [
                    // Khóa duy nhất để xác định bản ghi (thiết bị + lần kiểm kê)
                    'device_id' => $deviceId, 
                    'inventory_audit_id' => $inventoryAuditId,

                    // Dữ liệu cần cập nhật hoặc tạo mới
                    'initial_total' => (int) ($deviceRecord['initial_total'] ?? 0),
                    'initial_damaged' => (int) ($deviceRecord['initial_broken'] ?? 0),
                    'increase_quantity' => (int) ($deviceRecord['increase'] ?? 0),
                    'decrease_quantity' => (int) ($deviceRecord['decrease'] ?? 0),
                    'final_total' => (int) ($deviceRecord['final_total'] ?? 0),
                    'final_damaged' => (int) ($deviceRecord['final_broken'] ?? 0),
                    'updated_at' => now(), // Đảm bảo trường updated_at được cập nhật
                ];
            }
            
            // 1. Thực hiện Upsert (Insert Or Update)
            if (!empty($recordsToUpsert)) {
                // Trường hợp 1: Sử dụng Upsert (Yêu cầu Laravel >= 8)
                InventoryRecord::upsert(
                    $recordsToUpsert,
                    ['device_id', 'inventory_audit_id'], // Các cột tạo nên khóa duy nhất (để xác định cần UPDATE hay INSERT)
                    [
                        'initial_total', 'initial_damaged', 'increase_quantity', 
                        'decrease_quantity', 'final_total', 'final_damaged', 'updated_at' // Các cột cần cập nhật nếu bản ghi tồn tại
                    ]
                );
            }

            // --- PHẦN 3: CẬP NHẬT SỐ LƯỢNG KHO CHÍNH (BẢNG DEVICES) ---
            if ($request->task == 'submit_request_update') {
                // Cập nhật từng thiết bị dựa trên dữ liệu cuối cùng của kiểm kê
                foreach ($recordsToUpsert as $record) {
                    // Lấy các giá trị cần cập nhật
                    $deviceId = $record['device_id'];
                    // final_total: Tổng số lượng còn lại cuối năm (bao gồm cả Tốt và Hỏng)
                    $quantity = $record['final_total']; 
                    // final_damaged: Số lượng hỏng cuối năm
                    $broken = $record['final_damaged'];   
                    // Thực hiện cập nhật trong bảng devices
                    Device::where('id', $deviceId)->update([
                        'quantity' => $quantity, // Cập nhật tổng số lượng
                        'broken' => $broken,     // Cập nhật số lượng hỏng
                    ]);
                }
            }
            // --- KẾT THÚC CẬP NHẬT KHO ---

            DB::commit();
            return response()->json(['msg' => 'Cập nhật kiểm kê thành công!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['msg' => 'Đã xảy ra lỗi.', 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $item = $this->model::findOrFail($id);
        if($item->deleted_at){
            $item->delete();
        }else{
            $item->deleted_at = now();
            $item->save();
        }
        return redirect()
            ->route($this->route_prefix.'.index', ['page' => $request->page])
            ->with('success', 'Phiếu đã được xóa.');
    }
    // copy
    public function copy(Request $request, string $id){
        try {
            $this->model::copyItem($id);
            return redirect()->route($this->route_prefix.'.index')->with('success', __('sys.copy_item_success'));
        } catch (\Exception $e) {
            //Log::error('Item not found: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.item_not_found'));
        } catch (QueryException $e) {
            //Log::error('Error in copy method: ' . $e->getMessage());
            return redirect()->back()->with('error', __('sys.copy_item_error'));
        }
    }
}
