<?php

namespace App\Models\Versions;

use App\Models\Option;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Ver30 extends Model
{
    public static function doUpdate()
    {
        try {
            self::updateDocumentsTable();
            self::seedSgdOptions();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function updateDocumentsTable()
    {
        if (! Schema::hasTable('documents')) {
            return;
        }

        if (! Schema::hasColumn('documents', 'source')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('source', 20)->default('internal')->after('description');
            });
        }

        if (! Schema::hasColumn('documents', 'sgd_document_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedBigInteger('sgd_document_id')->nullable()->after('source');
            });
        }

        \Illuminate\Support\Facades\DB::table('documents')
            ->whereNull('source')
            ->orWhere('source', '')
            ->update(['source' => 'internal']);
    }

    public static function seedSgdOptions()
    {
        $options = [
            [
                'option_group' => 'general',
                'option_name' => 'company_sgd_code',
                'option_label' => 'Mã Sở (subdomain)',
                'option_value' => '',
            ],
            [
                'option_group' => 'general',
                'option_name' => 'sgd_portal_url',
                'option_label' => 'URL portal Sở',
                'option_value' => '',
            ],
            [
                'option_group' => 'general',
                'option_name' => 'sgd_api_key',
                'option_label' => 'API key kết nối Sở',
                'option_value' => '',
            ],
        ];

        foreach ($options as $row) {
            Option::query()->firstOrCreate(
                [
                    'option_group' => $row['option_group'],
                    'option_name' => $row['option_name'],
                ],
                $row
            );
        }
    }
}
