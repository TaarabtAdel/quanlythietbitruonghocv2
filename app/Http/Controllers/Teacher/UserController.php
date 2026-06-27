<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    protected $view_path    = 'teacher.users';
    protected $route_prefix = 'users.';
    protected $model        = User::class;
    public function index(Request $request)
    {
        $query = $this->model::whereNull('deleted_at')->orderByGivenName();
        if($request->name){
            $query->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->nest_id){
            $query->where('nest_id',$request->nest_id);
        }
        if($request->group_id){
            $query->where('group_id',$request->group_id);
        }

        $items = $query->paginate(20);
        $param = [
            'items' => $items,
            'route_prefix' => $this->route_prefix,
        ];
        return view($this->view_path.'.index', $param );
    }

    // Profile
    public function profile(){
        $item = Auth::user();
        $param = [
            'item' => $item
        ];
        return view('teacher.users.profile',$param);
    }
    public function profileEdit()
    {
        
        $item = Auth::user();
        $param = [
            'item' => $item
        ];
        return view('teacher.users.profileEdit',$param);
    }
    public function postProfileEdit(Request $request)
    {
        $data = $request->except('_method','_token');
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']); // Loại bỏ trường password khỏi mảng data
        }
        $item = Auth::user();
        if ($item->update($data)) {
            return redirect()->route('users.profile')->with('success','Cập nhập thông tin thành công');
        }
        return redirect()->route('users.profile')->with('success','Cập nhập thông tin thành công');
    }
}
