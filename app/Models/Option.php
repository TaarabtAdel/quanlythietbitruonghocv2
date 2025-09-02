<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $table ='options';
    protected $fillable = [
        'option_value',
        'option_name',
        'option_label',
        'option_group'
    ];

    public $timestamps = false;

    public static function get_option($group,$name,$default = ''){
        $option_value = self::where('option_group',$group)
                ->where('option_name',$name)
                ->value('option_value');
        return $option_value ?? $default;
    }
    public static function update_option($group,$name,$value = ''){
        return self::where('option_group',$group)
                ->where('option_name',$name)
                ->update([
                    'option_value' => $value
                ]);
    }

}
