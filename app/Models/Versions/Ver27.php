<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class Ver27 extends Model
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
        // Bảng danh mục chương trình (Năm học, Môn học)
        if (!Schema::hasTable('curriculums')) {
            Schema::create('curriculums', function (Blueprint $table) {
                $table->id();
                $table->string('academic_year'); // Năm học
                $table->unsignedBigInteger('department_id'); // Bộ môn
                $table->string('grade')->nullable(); // Khối lớp (tùy chọn)
                $table->string('subject_type')->nullable(); // Phân môn
                $table->text('note')->nullable(); // Ghi chú
                $table->timestamps();
                $table->softDeletes();
                // status
                $table->integer('status')->default(1);
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            });
        }

        // Bảng chi tiết phân phối chương trình
        if (!Schema::hasTable('curriculum_details')) {
            Schema::create('curriculum_details', function (Blueprint $table) {
                $table->id();
                // Liên kết logic với bảng curriculums qua ID (không dùng khóa ngoại)
                $table->unsignedBigInteger('curriculum_id'); 
                
                $table->integer('week')->nullable();            // Tuần PPCT
                $table->integer('lesson_number')->nullable();   // Tiết PPCT
                $table->string('lesson_name');      // Tên Bài
                $table->text('note')->nullable();   // Ghi Chú
                
                $table->timestamps();
            });
        }
    }
}