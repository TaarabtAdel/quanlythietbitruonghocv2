<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadFileTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class AdminModel extends Model
{
    use HasFactory;
    use UploadFileTrait;
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    const DRAFT     = -1;

    static $upload_dir = 'uploads';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'position',
        'deleted_at'
    ];

    public static function setUploadDir( $upload_dir ){
        self::$upload_dir = $upload_dir;
    }
    // Relations
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Methods
    // public static function getItems($request = null,$table = ''){
    //     $limit = $request->limit ? $request->limit : 20;
    //     if($table){
    //         $modelClass = '\App\Models\\' . $table;
    //         $query = $modelClass::query(true);
    //         $query = $modelClass::handleSearch($request,$query);
    //     }else{
    //         $query = self::query(true);
    //         $query = self::handleSearch($request,$query);
    //     }
    //     $items = $query->paginate($limit);
    //     return $items;
    // }
    // public static function handleSearch($request,$query){
    //     $model = new self;
    //     $tableName = $model->getTable();
    //     if($request->type && Schema::hasColumn($tableName, 'type')){
    //         $query->where('type',$request->type);
    //     }
    //     if($request->name){
    //         $query->where('name','LIKE','%'.$request->name.'%');
    //     }
    //     if($request->status !== NULL){
    //         if($request->status == 0){
    //             $query->whereNotNull('deleted_at');
    //         }
    //     }
    //     return $query;
    // }
    // public static function getAll($activeOnly = false){
    //     $query = self::query(true);
    //     if($activeOnly){
    //         $query->whereNull('deleted_at');
    //     }
    //     $query->orderBy('name');
    //     $items = $query->get();
    //     return $items;
    // }
    // public static function findItem($id,$table = ''){
    //     if($table){
    //         $model = '\App\Models\\' . $table;
    //     }else{
    //         $model = self::class;
    //     }
    //     $item = $model::findOrFail($id);
    //     $item->status = $item->deleted_at ? 0 : 1;
    //     return $item;
    // }
    // public static function saveItem($request,$table = ''){
    //     if($table){
    //         $model = '\App\Models\\' . $table;
    //     }else{
    //         $model = self::class;
    //     }
    //     $data = $request->except(['_token', '_method','type']);
    //     $data['deleted_at'] = $request->status == 1 ? NULL : date('Y-m-d H:i:s');
    //     unset($data['status']);
    //     if ($request->hasFile('image')) {
    //         $data['image'] = self::uploadFile($request->file('image'), self::$upload_dir);
    //     } 
    //     return $model::create($data);
    // }
    // public static function updateItem($id,$request,$table = ''){
    //     if($table){
    //         $model = '\App\Models\\' . $table;
    //     }else{
    //         $model = self::class;
    //     }

    //     $item = $model::findOrFail($id);
    //     $data = $request->all();
    //     $data = $request->except(['_token', '_method','type']);
    //     $data['deleted_at'] = $request->status == 1 ? NULL : date('Y-m-d H:i:s');
    //     unset($data['status']);
    //     if ($request->hasFile('image')) {
    //         $data['image'] = self::uploadFile($request->file('image'), self::$upload_dir);
    //     } 
    //     $item->update($data);
    //     return $item;
    // }
    // public static function deleteItem($id,$table = ''){
    //     if($table){
    //         $model = '\App\Models\\' . $table;
    //     }else{
    //         $model = self::class;
    //     }
    //     $item = $model::findItem($id,$table);
    //     unset($item->status);
    //     if($item->deleted_at){
    //         return $item->delete();
    //     }else{
    //         $item->deleted_at = date('Y-m-d H:i:s');
    //     }
    //     return $item->save();
    // }

    // Attributes
    public function getStatusFmAttribute(){
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }else{
            return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
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