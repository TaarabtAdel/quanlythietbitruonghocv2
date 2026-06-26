<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\BorrowDevice;
use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 20);
        $query = Device::query()
            ->with(['devicetype', 'department'])
            ->orderBy('name', 'ASC')
            ->whereNull('deleted_at');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('device_type_id')) {
            $query->where('device_type_id', $request->device_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $addedIds = [];
        if ($request->filled('f_added_ids')) {
            $json = base64_decode($request->f_added_ids);
            $addedIds = json_decode($json, true) ?: [];
        }

        $items = $query->paginate($limit);

        return $this->success([
            'items' => $items->getCollection()->map(fn (Device $device) => [
                'id' => $device->id,
                'name' => $device->name,
                'quantity' => $device->quantity,
                'broken' => (int) ($device->broken ?? 0),
                'remaining' => max(0, (int) $device->quantity - (int) ($device->broken ?? 0)),
                'unit' => $device->unit,
                'device_type_id' => $device->device_type_id,
                'device_type_name' => $device->devicetype?->name,
                'department_id' => $device->department_id,
                'department_name' => $device->department?->name,
                'is_added' => in_array($device->id, $addedIds, true),
            ])->values(),
            'added_ids' => $addedIds,
            'filters' => [
                'device_types' => DeviceType::query()->orderBy('name')->get(['id', 'name']),
                'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
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
