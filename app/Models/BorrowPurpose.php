<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class BorrowPurpose extends AdminModel
{
    use HasFactory;
    protected $table = "borrow_purposes";
    protected $fillable = [
        'name','slug','deleted_at'
    ];
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }

    // Ghi đè phương thức create
    public static function create($data){
        $data['slug'] = Str::slug($data['name'],'_');
        DB::table('borrow_purposes')->insert($data);
    }

    // Ghi đè phương thức update
    public function update(array $attributes = [], array $options = []){
        $id = $this->id;
        $old_slug = $this->slug;
        $data = array_merge($this->toArray(), $attributes);
        $new_slug = Str::slug($data['name'],'_');
        $data['slug'] = $new_slug;
        
        // Fix datetime format
        if (isset($data['created_at'])) {
            $data['created_at'] = \Carbon\Carbon::parse($data['created_at'])->format('Y-m-d H:i:s');
        }
        if (isset($data['updated_at'])) {
            $data['updated_at'] = \Carbon\Carbon::parse($data['updated_at'])->format('Y-m-d H:i:s');
        }

        if($old_slug != $new_slug){
            // Nếu slug cũ và mới khác nhau, cập nhật trong bảng `borrows`
            DB::table('borrows')
            ->where('borrow_purpose', $old_slug)
            ->update(['borrow_purpose' => $new_slug]);
        }
        DB::table('borrow_purposes')->where('id',$id)->update($data);
    }
}
