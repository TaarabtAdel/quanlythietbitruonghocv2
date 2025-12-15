<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryRecord; // Import Model chi tiết
use App\Models\User; // Import Model User (Giả định có sẵn)
use Illuminate\Support\Facades\Auth;

class InventoryAudit extends Model
{
    use HasFactory;
    use SoftDeletes;
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    const DRAFT     = -1;

    /**
     * Tên bảng trong database.
     * @var string
     */
    protected $table = 'inventory_audits';

    /**
     * Các trường được phép gán dữ liệu hàng loạt (Mass Assignment).
     * @var array
     */
    protected $fillable = [
        'user_id',
        'school_year',
        'audit_date',
        'status',
        'name',
        'note'
    ];

    public function getStatusFmAttribute(){
        switch ($this->status) {
            case self::DRAFT:
                return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Phiếu Nháp</span>';
                break;
            case self::ACTIVE:
                return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Đã Duyệt</span>';
                break;
            case self::INACTIVE:
                return '<span class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">Chờ Duyệt</span>';
                break;
            case self::CANCELED:
                return '<span class="lable-table bg-dark-subtle text-warning rounded border border-dark-subtle font-text2 fw-bold">Đã Hủy</span>';
                break;
        }
    }

    // --- Mối quan hệ ---

    /**
     * Mối quan hệ: Một đợt kiểm duyệt thuộc về một người dùng (User).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ: Một đợt kiểm duyệt có nhiều bản ghi kiểm kê chi tiết (InventoryRecord).
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function records()
    {
        // 'inventory_audit_id' là khóa ngoại trong bảng inventory_records
        return $this->hasMany(InventoryRecord::class, 'inventory_audit_id'); 
    }

    public static function copyItem($id){
        // Tải bản ghi gốc cùng với các bản ghi chi tiết và thông tin Device liên quan
        $originalAudit = static::with('records.device')->findOrFail($id);
        // Tạo mảng để lưu thông tin kho hiện tại (quantity, broken)
        // Giảm N+1 Query Problem bằng cách lấy tất cả dữ liệu device liên quan một lần
        $deviceQuantities = $originalAudit->records->pluck('device', 'device_id')
                                                ->map(function ($device) {
                                                    return [
                                                        'quantity' => $device->quantity,
                                                        'broken' => $device->broken,
                                                    ];
                                                });

        DB::beginTransaction();
        try {
            // 1. SAO CHÉP BẢN GHI CHA (InventoryAudit)
            $newAudit = $originalAudit->replicate();
            
            // Cập nhật các trường cần thay đổi
            $newAudit->user_id = Auth::id(); 
            $newAudit->status = -1; 
            $newAudit->name = 'Bản sao của: ' . $originalAudit->name; 
            $newAudit->save();
            
            // 2. SAO CHÉP CÁC BẢN GHI CON (InventoryRecord)
            $recordsToInsert = [];
            foreach ($originalAudit->records as $originalRecord) {
                // Sao chép bản ghi con
                $newRecord = $originalRecord->replicate();
                // Liên kết với ID của Audit mới
                $newRecord->inventory_audit_id = $newAudit->id;
                // Lấy dữ liệu mảng, Laravel/Eloquent đã tự loại bỏ 'id'
                $recordData = $newRecord->toArray();
                $device_id = $recordData['device_id'];
                // Lấy thông tin tồn kho hiện tại (đã được tải trước đó)
                $currentStock = $deviceQuantities->get($device_id);
                // Thiết lập lại dữ liệu hiện tại của kho
                // Tồn đầu (initial_total) = Số lượng hiện tại trong kho
                $recordData['initial_total']        = $currentStock['quantity'] ?? 0;
                $recordData['initial_damaged']      = $currentStock['broken'] ?? 0;
                // Đặt lại các trường kiểm kê về 0
                $recordData['increase_quantity']    = 0;
                $recordData['decrease_quantity']    = 0;
                $recordData['final_total']          = 0;
                $recordData['final_damaged']        = 0;
                
                // Đảm bảo có timestamps (replicate() không tự động thêm)
                $recordData['created_at'] = now();
                $recordData['updated_at'] = now();
                // Bỏ ID cũ (replicate() thường đã làm, nhưng thêm vào để đảm bảo)
                unset($recordData['id']); 
                unset($recordData['device']); 
                $recordsToInsert[] = $recordData;
            }
            
            // 3. Lưu HÀNG LOẠT các bản ghi con mới
            if (!empty($recordsToInsert)) {
                // Sử dụng insert hàng loạt để tối ưu hóa hiệu năng
                DB::table('inventory_records')->insert($recordsToInsert);
            }
            DB::commit();
            return $newAudit;
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            throw $e; 
        }
    }
}