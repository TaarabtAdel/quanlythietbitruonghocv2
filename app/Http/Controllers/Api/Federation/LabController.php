<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Lab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Lab::query()->with('department')->whereNull('deleted_at')->orderBy('name');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Lab $lab) => [
            'id' => $lab->id,
            'name' => $lab->name,
            'quantity' => $lab->quantity,
            'note' => $lab->note,
            'department_name' => $lab->department?->name,
        ]);
    }
}
