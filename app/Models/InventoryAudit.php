<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryRecord; // Import Model chi tiết
use App\Models\User; // Import Model User (Giả định có sẵn)
use Illuminate\Support\Facades\Auth;

class InventoryAudit extends Model
{
    use HasFactory;
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
        // Tải bản ghi gốc cùng với các bản ghi chi tiết
        $originalAudit = static::with('records')->findOrFail($id);
        DB::beginTransaction();
        try {
            // 1. SAO CHÉP BẢN GHI CHA (InventoryAudit)
            $newAudit = $originalAudit->replicate();
            // Cập nhật các trường cần thay đổi
            $newAudit->user_id = Auth::id(); // Người tạo phiếu mới
            $newAudit->status = 'Draft';     // Trạng thái ban đầu
            $newAudit->name = 'Bản sao của: ' . $originalAudit->name; // Đặt tên mới
            $newAudit->save();
            // 2. SAO CHÉP CÁC BẢN GHI CON (InventoryRecord)
            $recordsToInsert = [];
            foreach ($originalAudit->records as $originalRecord) {
                // Sao chép bản ghi con
                $newRecord = $originalRecord->replicate();
                // Liên kết với ID của Audit mới
                $newRecord->inventory_audit_id = $newAudit->id;
                // Chuẩn bị cho insert hàng loạt
                $recordData = $newRecord->toArray();
                // Đảm bảo không có ID cũ và có timestamps
                unset($recordData['id']);
                $recordData['created_at'] = now();
                $recordData['updated_at'] = now();
                
                $recordsToInsert[] = $recordData;
            }
            // 3. Lưu HÀNG LOẠT các bản ghi con mới
            if (!empty($recordsToInsert)) {
                DB::table('inventory_records')->insert($recordsToInsert);
            }
            DB::commit();
            return $newAudit;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; 
        }
    }
}