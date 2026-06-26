<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Room::query()->whereNull('deleted_at')->orderBy('name');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Room $room) => [
            'id' => $room->id,
            'name' => $room->name,
            'quantity' => $room->quantity,
            'note' => $room->note,
        ]);
    }
}
