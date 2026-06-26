<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Nest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Nest::query()
            ->withCount(['users' => fn ($q) => $q->whereNull('deleted_at')])
            ->whereNull('deleted_at')
            ->orderBy('name');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }

        return $this->paginated($query->paginate($limit), fn (Nest $nest) => [
            'id' => $nest->id,
            'name' => $nest->name,
            'user_count' => $nest->users_count,
        ]);
    }
}
