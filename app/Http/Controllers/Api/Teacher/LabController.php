<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\BorrowDevice;
use App\Models\Department;
use App\Models\Lab;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 20);
        $query = Lab::query()->with('department')->orderBy('name', 'ASC');
        $borrowLabIds = [];

        if ($request->filled('item_id') && $request->has('tiet')) {
            $borrowDevice = BorrowDevice::query()
                ->where('borrow_id', $request->item_id)
                ->where('tiet', $request->tiet)
                ->first();

            if ($borrowDevice) {
                $request->merge([
                    'borrow_date' => $borrowDevice->borrow_date,
                    'session' => $borrowDevice->session,
                    'lecture_number' => $borrowDevice->lecture_number,
                ]);

                $borrowed = BorrowDevice::query()
                    ->where('lab_id', '>', 0)
                    ->whereHas('borrow', fn ($q) => $q->where('status', '>=', 0))
                    ->where('borrow_date', $borrowDevice->borrow_date)
                    ->where('session', $borrowDevice->session)
                    ->where('lecture_number', $borrowDevice->lecture_number)
                    ->with('borrow.user')
                    ->get();

                foreach ($borrowed as $row) {
                    $borrowLabIds[$row->lab_id] = $row->borrow->user->name ?? '';
                }
            }
        }

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->filled('lab_type_id')) {
            $query->where('lab_type_id', $request->lab_type_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $items = $query->paginate($limit);

        return $this->success([
            'items' => $items->getCollection()->map(fn (Lab $lab) => [
                'id' => $lab->id,
                'name' => $lab->name,
                'department_id' => $lab->department_id,
                'department_name' => $lab->department?->name,
                'borrowed_by' => $borrowLabIds[$lab->id] ?? null,
                'is_busy' => isset($borrowLabIds[$lab->id]),
            ])->values(),
            'borrow_lab_ids' => $borrowLabIds,
            'filters' => [
                'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
                'rooms' => Room::query()->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
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
