<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WebsiteModel extends Model
{
    use HasFactory;
    // use UploadFileTrait;
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    const DRAFT     = -1;

    static $upload_dir = 'uploads';

    public static function setUploadDir( $upload_dir ){
        self::$upload_dir = $upload_dir;
    }

    public static function getAll($request = null){
        $query = self::query(true);
        $query->whereNull('deleted_at');
        $query->orderBy('name');
        $items = $query->get();
        return $items;
    }
    public static function getItems($request = null,$user_id = 0){
        $limit = $request->limit ? $request->limit : 20;
        $query = self::query(true);
        $query->whereNull('deleted_at');
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->status){
            $query->where('status',$request->status);
        }
        if($user_id){
            $query->where('user_id',$user_id);
        }
        $query->orderBy('id','DESC');
        $items = $query->paginate($limit);
        return $items;
    }
    public static function findItem($id){
        return self::findOrFail($id);
    }
    public static function saveItem($request){
        $data = $request->all();
        if(!$request->slug && $request->name){
            $data['slug'] = Str::slug($request->name);
        }
        if ($request->hasFile('image')) {
            $data['image'] = self::uploadFile($request->file('image'), self::$upload_dir);
        } 
        return self::create($data);
    }
    public static function updateItem($id,$request){
        $item = self::findOrFail($id);
        $data = $request->all();
        if ($request->hasFile('image')) {
            //self::deleteFile($item->image);
            $data['image'] = self::uploadFile($request->file('image'), self::$upload_dir);
        } 
        $item->update($data);
        return $item;
    }
    public static function deleteItem($id){
        $item = self::findItem($id);
        //self::deleteFile($item->image);
        return $item->delete();
    }

    // Attributes
    public function getStatusFmAttribute(){
        switch ($this->status) {
            case self::DRAFT:
                return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.draf').'</span>';
                break;
            case self::ACTIVE:
                return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
                break;
            case self::INACTIVE:
                return '<span class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
                break;
        }
    }
    public function getCreatedAtFmAttribute(){
        return date('d-m-Y',strtotime($this->created_at));
    }
    public function getImageFmAttribute(){
        if( !$this->image ){
            return asset('admin-assets/images/default-image.png');
        }
        return asset($this->image);
    }
    public static function getStartEndDateFromWeek($week){
        $year           = substr($week, 0, 4);
        $weekNumber     = substr($week, -2);
        $startDate      = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate        = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
}