<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryAudit; // Import Model đợt kiểm duyệt
use App\Models\Device; // Import Model Device (Thiết bị)

class InventoryRecord extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database.
     * @var string
     */
    protected $table = 'inventory_records';

    /**
     * Các trường được phép gán dữ liệu hàng loạt (Mass Assignment).
     * @var array
     */
    protected $fillable = [
        'inventory_audit_id',
        'device_id',
        'school_year',
        'initial_total',
        'initial_damaged',
        'increase_quantity',
        'decrease_quantity',
        'final_total',
        'final_damaged',
    ];

    // --- Mối quan hệ ---

    /**
     * Mối quan hệ: Một bản ghi chi tiết thuộc về một đợt kiểm duyệt (InventoryAudit).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function audit()
    {
        // 'inventory_audit_id' là khóa ngoại tham chiếu đến bảng inventory_audits
        return $this->belongsTo(InventoryAudit::class, 'inventory_audit_id'); 
    }

    /**
     * Mối quan hệ: Một bản ghi chi tiết là của một thiết bị (Device).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo(Device::class); 
    }
}