<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ver31 extends Model
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
        $schema = Schema::connection('school_main');

        if (! $schema->hasTable('campuses')) {
            $schema->create('campuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('database_name');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();
            });
        }
    }
}
