<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class Ver28 extends Model
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
        // 1. Nếu bảng chưa tồn tại -> Tạo mới hoàn toàn với lesson_number là string
        if (!Schema::hasTable('curriculum_details')) {
            Schema::create('curriculum_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_id'); 
                $table->integer('week')->nullable();            
                $table->string('lesson_number')->nullable();   // Đã đổi sang string
                $table->string('lesson_name');      
                $table->text('note')->nullable();   
                $table->timestamps();
            });
        } else {
            // 2. Nếu bảng đã tồn tại -> Cập nhật cột lesson_number sang string
            Schema::table('curriculum_details', function (Blueprint $table) {
                $table->string('lesson_number')->nullable()->change();
            });
        }
    }
}