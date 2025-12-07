<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\GroupRoleModel;
use App\Models\AdminGroup;
use App\Models\AdminRole;

class Ver26 extends Model
{
    public static function doUpdate(){
        try {
            self::updateDatabase();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateDatabase()
    {
        if (!Schema::hasTable('inventory_audits')) {
            Schema::create('inventory_audits', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('user_id'); // Người phụ trách
                $table->string('school_year', 9)->nullable(); 
                $table->date('audit_date')->nullable(); // Ngày kiểm duyệt thực tế
                $table->string('status')->default('Draft'); // Trạng thái
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('inventory_records')) {
            Schema::create('inventory_records', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('device_id'); // Khóa ngoại tới bảng 'devices'
                $table->unsignedBigInteger('inventory_audit_id');
                // Số lượng thiết bị khi nhập sổ (Đầu năm)
                $table->integer('initial_total')->default(0)->comment('Tổng số lượng đầu năm');
                $table->integer('initial_damaged')->default(0)->comment('Số lượng hỏng đầu năm');
                // Biến động trong năm
                $table->integer('increase_quantity')->default(0)->comment('Số lượng tăng thêm trong năm');
                $table->integer('decrease_quantity')->default(0)->comment('Số lượng giảm đi trong năm');
                // Số còn lại sau năm học (Cuối năm)
                $table->integer('final_total')->default(0)->comment('Tổng số lượng cuối năm');
                $table->integer('final_damaged')->default(0)->comment('Số lượng hỏng cuối năm');
                // Thiết lập khóa duy nhất (Đảm bảo 1 thiết bị chỉ có 1 bản ghi trong 1 năm học)
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::table('devices', function (Blueprint $table) {
            // Thêm cột 'broken' kiểu integer, mặc định là 0
            $table->string('broken')->nullable();
        });
    }

}