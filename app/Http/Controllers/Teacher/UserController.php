<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;

class UserController extends Controller
{
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
