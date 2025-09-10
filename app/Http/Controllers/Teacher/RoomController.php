<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    protected $view_path    = 'teacher.rooms';
    protected $route_prefix = 'rooms.';
    protected $model        = Room::class;
    public function index(Request $request)
    {
        $query = $this->model::whereNull('deleted_at')->orderBy('name','asc');
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        $items = $query->paginate(20);
        $param = [
            'items' => $items,
            'route_prefix' => $this->route_prefix,
        ];
        return view($this->view_path.'.index', $param );
    }
}
