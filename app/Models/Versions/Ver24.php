<?php

namespace App\Models\Versions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\Models\GroupRoleModel;
use App\Models\AdminGroup;
use App\Models\AdminRole;

class Ver24 extends Model
{
    public static function doUpdate(){
        try {
            self::addDocumentsTable();
            self::updateGroupRoleData();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function addDocumentsTable()
    {
        /*
        Tạo bảng borrow_purposes gồm id, name, slug, created_at, updated_at, deleted_at
        */
        if (!Schema::hasTable('documents')) {
            /*
            Tạo cho tôi bảng documents gồm id, name, image, description, created_at, updated_at, deleted_at
            */
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable(false);
                $table->string('slug')->nullable();
                $table->string('image')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public static function updateGroupRoleData(){
        $all_roles = AdminGroup::$roles;
        $role_ids = [];
        foreach ($all_roles as $group_name => $roles) {
            foreach($roles as $name){
                $check = DB::table('roles')->where('group_name',$group_name)
                ->where('name',$name)->limit(1)->first();
                if($check){
                    $role_ids[] = $check->id;
                }else{
                    $role = new AdminRole;
                    $role->group_name = $group_name;
                    $role->name = $name;
                    $role->save();
                    $role_ids[] = $role->id;
                }
            }
        }
        if( count($role_ids) > 0 ){
            // Cập nhật cho tất cả các nhóm
            $items = AdminGroup::all();
            foreach( $items as $item ){
                $item->roles()->detach();
                $item->roles()->attach($role_ids);
            }
        }
        
    }

    public static function seedBorrowPurposeData(){
        $borrow_purposes = [
            [
                'name' => 'ĐK Phòng Bộ Môn',
                'slug' => 'phong_bo_mon'
            ],
            [
                'name' => 'ĐK Dạy CNTT',
                'slug' => 'day_cntt'
            ],
            [
                'name' => 'ĐK Sử Dụng TB Thực Hành',
                'slug' => 'sd_tbth'
            ],
            [
                'name' => 'ĐK Phòng Đa Chức Năng',
                'slug' => 'phong_dcn'
            ],
            [
                'name' => 'Khác',
                'slug' => 'other'
            ]
        ];
        foreach( $borrow_purposes as $borrow_purpose ){
            \App\Models\BorrowPurpose::updateOrCreate([
                'name' => $borrow_purpose['name']
            ],$borrow_purpose);
        }
       
    }
}