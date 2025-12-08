<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\GroupRoleModel;
use App\Models\AdminGroup;
use App\Models\AdminRole;
use App\Models\Device;

class Ver26 extends Model
{
    public static function doUpdate(){
        try {
            self::updateDatabase();
            self::optimizeDeviceData();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function optimizeDeviceData()
    {
        // Lấy tất cả các thiết bị cần tối ưu hóa giá
        $devices = Device::all();

        $count = 0;
        
        foreach ($devices as $device) {
            $originalPrice = $device->price;

            // 1. Kiểm tra xem giá có phải là một chuỗi (string) không
            if (!is_string($originalPrice)) {
                // Nếu giá đã là số hợp lệ, bỏ qua
                continue;
            }

            // 2. Loại bỏ các ký tự phân tách hàng nghìn (dấu phẩy, dấu chấm, khoảng trắng)
            // Lưu ý: Nếu bạn muốn giữ lại dấu thập phân (ví dụ: 7.5 triệu -> 7500000)
            // bạn phải loại bỏ tất cả dấu chấm và phẩy.
            // Ví dụ: "7.500.000" => "7500000"
            // Ví dụ: "7,500,000" => "7500000"
            
            $cleanedPrice = str_replace(['.', ',', ' '], '', $originalPrice);
            
            // 3. Chuyển đổi chuỗi đã làm sạch thành số nguyên (bigint/integer)
            $newPrice = (int) $cleanedPrice;

            // 4. Chỉ cập nhật nếu giá trị đã thay đổi
            if ($device->price !== $newPrice) {
                $device->price = $newPrice;
                $device->save();
                $count++;
            }
        }

        return "Đã tối ưu hóa thành công $count bản ghi giá thiết bị.";
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
                $table->date('deleted_at')->nullable();
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
            // Chỉ thêm cột 'broken' nếu nó chưa tồn tại
            if (!Schema::hasColumn('devices', 'broken')) {
                // Bạn nên sử dụng kiểu integer cho cột 'broken' để lưu trạng thái
                $table->integer('broken')->default(0)->nullable(false); 
                
                // Nếu bạn muốn giữ lại kiểu string như trong câu hỏi ban đầu:
                // $table->string('broken')->nullable();
            }
        });
    }

}