<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'type', 'action', 'is_read', 'item_id'];

    static function deleteNotification($notiid){
        $noti = self::find($notiid);
        if($noti){
            $noti->delete();
        }
    }
    static function addNotification($user_id, $type, $action, $item_id){
        self::create([
            'user_id' => $user_id,
            'type'    => $type,
            'action' => $action,
            'item_id' => $item_id
        ]);
    }
    //Feature 
    function getUserNameAttribute(){
        return isset($this->user) ? $this->user->name : '';
    }
    function getItemNameAttribute(){
        $item_name = '';
        switch ($this->type) {
            case 'new_borrow':
                $item_name = 'Phiếu báo mượn mới';
                break;
            
            default:
                # code...
                break;
        }
        return $item_name;
    }
    function getItemDescriptionAttribute(){
        $item_name = '';
        switch ($this->type) {
            case 'new_borrow':
                $borrow = \App\Models\Borrow::find($this->item_id);
                $item_name = "<span class='fw-bold'>{$borrow->user->name}</span> vừa tạo phiếu mượn";
                break;
            
            default:
                # code...
                break;
        }
        return $item_name;
    }

    function getItemIconAttribute(){
        $item_icon = '';
        switch ($this->type) {
            case 'new_borrow':
                $item_icon = '<span class="material-symbols-outlined"> forum </span>';
                break;
            case 'new_order':
                $item_icon = '<i class="ti-heart bg_danger"></i>';
                break;
            
            default:
                # code...
                break;
        }
        return $item_icon;
    }
    public function getItemLinkAttribute()
{
    $itemLink = '';
    switch ($this->type) {
        case 'new_borrow':
            $itemLink = route('adminborrow.index', ['notiid' => $this->id]);
            break;
        default:
         
            break;
    }
    return $itemLink;
}
    
}
