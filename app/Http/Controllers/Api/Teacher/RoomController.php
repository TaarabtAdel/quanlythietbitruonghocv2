<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::query()
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        $items = $query->paginate($request->integer('limit', 20));

        return $this->paginated($items, fn (Room $room) => [
            'id' => $room->id,
            'name' => $room->name,
        ]);
    }
}
