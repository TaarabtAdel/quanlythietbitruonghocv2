<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Ver29 extends Model
{
    public static function doUpdate()
    {
        try {
            self::updateDatabase();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateDatabase()
    {
        // Bảng token API cho app Giáo viên (Expo web + mobile)
        if (!Schema::hasTable('api_tokens')) {
            Schema::create('api_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->default('teacher-app');
                $table->string('token', 64)->unique();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
            });
        }
    }
}
