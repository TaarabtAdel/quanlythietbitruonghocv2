<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Department;
use App\Models\Device;
use App\Models\DeviceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Device::query()
            ->with(['devicetype', 'department'])
            ->orderBy('name')
            ->whereNull('deleted_at');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%'.$request->name.'%');
        }
        if ($request->filled('device_type_id')) {
            $query->where('device_type_id', $request->device_type_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
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
                'year' => $device->year,
                'note' => $device->note,
                'device_type_name' => $device->devicetype?->name,
                'department_name' => $device->department?->name,
            ])->values(),
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
