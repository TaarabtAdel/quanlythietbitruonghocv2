<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryRecord; // Import Model chi tiết
use App\Models\User; // Import Model User (Giả định có sẵn)

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
}