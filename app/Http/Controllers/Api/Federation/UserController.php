<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Nest;
use App\Models\User;
use App\Support\Api\UserPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()
            ->with(['nest', 'group'])
            ->whereNull('deleted_at')
            ->orderByGivenName();

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }
        if ($request->filled('nest_id')) {
            $query->where('nest_id', $request->nest_id);
        }

        $items = $query->paginate($request->integer('limit', 30));

        return $this->success([
            'items' => $items->getCollection()->map(fn (User $user) => UserPresenter::listItem($user))->values(),
            'filters' => [
                'nests' => Nest::query()->orderBy('name')->get(['id', 'name']),
            ],
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }
}
