<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use App\Support\Api\UserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->with(['nest', 'group'])
            ->whereNull('deleted_at')
            ->orderByGivenName();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('nest_id')) {
            $query->where('nest_id', $request->nest_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $items = $query->paginate($request->integer('limit', 20));

        return $this->paginated($items, fn (User $user) => UserPresenter::listItem($user));
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(UserPresenter::profile($request->user()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:Nam,Nữ'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = Auth::user();
        $user->update($data);

        return $this->success(
            UserPresenter::profile($user->fresh()),
            'Cập nhập thông tin thành công'
        );
    }
}
