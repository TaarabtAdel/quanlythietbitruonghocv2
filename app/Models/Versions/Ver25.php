<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\GroupRoleModel;
use App\Models\AdminGroup;
use App\Models\AdminRole;

class Ver25 extends Model
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
        \App\Models\Option::updateOrCreate(
            ['option_name' => 'enable_fake_device'], // điều kiện tìm
            [
                'option_value' => 0,
                'option_label' => 'Cho phép giáo viên đưa lên thiết thị tự chuẩn bị trong phiếu mượn',
                'option_group' => 'borrow_device',
                'option_group_name' => 'Mượn thiết bị',
            ]
        );

        if (!Schema::hasTable('borrow_device_fakes')) {
            Schema::create('borrow_device_fakes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('borrow_id');
                $table->string('device_name');
                $table->unsignedBigInteger('room_id')->default(0);
                $table->integer('quantity')->nullable();
                $table->date('borrow_date')->nullable();
                $table->date('return_date')->nullable();
                $table->string('lecture_name')->nullable();
                $table->string('lesson_name')->nullable();
                $table->string('session')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->integer('lecture_number')->nullable();
                $table->integer('tiet')->default(0);
                $table->unsignedBigInteger('lab_id')->default(0);
            });
        }
    }

}