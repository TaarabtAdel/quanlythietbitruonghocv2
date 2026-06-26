<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\BorrowDeviceFake;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FakeDeviceController extends Controller
{
    public function index(Request $request, int $borrowId): JsonResponse
    {
        if (!$this->ownsBorrow($borrowId)) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        $tietId = $request->query('tiet_id');

        $query = BorrowDeviceFake::where('borrow_id', $borrowId);

        if ($tietId !== null && $tietId !== '') {
            $query->where('tiet', $tietId);
            $devices = $query->orderBy('tiet')->get()->map(fn ($item) => [
                'id' => $item->id,
                'device_name' => $item->device_name,
                'quantity' => (int) $item->quantity,
                'tiet_id' => (int) $item->tiet,
            ]);

            return response()->json([
                'success' => true,
                'data' => $devices,
            ]);
        }

        $grouped = BorrowDeviceFake::where('borrow_id', $borrowId)
            ->orderBy('tiet')
            ->get()
            ->groupBy('tiet')
            ->map(function ($items, $tiet) {
                return [
                    'tiet_id' => (int) $tiet,
                    'devices' => $items->map(fn ($item) => [
                        'id' => $item->id,
                        'device_name' => $item->device_name,
                        'quantity' => (int) $item->quantity,
                    ])->values(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ]);
    }

    public function store(Request $request, int $borrowId): JsonResponse
    {
        if (!$this->ownsBorrow($borrowId)) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        $tietId = $request->input('tiet_id');
        $deviceFakes = $request->input('device_fakes', []);

        if (empty($deviceFakes) || !is_array($deviceFakes)) {
            return response()->json(['message' => 'No device data provided'], 400);
        }

        DB::beginTransaction();

        try {
            BorrowDeviceFake::where('borrow_id', $borrowId)
                ->where('tiet', $tietId)
                ->delete();

            foreach ($deviceFakes as $fake) {
                if (!empty($fake['device_name']) && isset($fake['qty'])) {
                    BorrowDeviceFake::create([
                        'borrow_id' => $borrowId,
                        'device_name' => $fake['device_name'],
                        'quantity' => (int) $fake['qty'],
                        'tiet' => $tietId,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fake devices saved successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $deviceId = $request->input('fake_device_id', $id);
        $quantity = $request->input('quantity');

        if (!$deviceId || !is_numeric($quantity) || $quantity < 0) {
            return response()->json(['success' => false, 'message' => 'Invalid input'], 400);
        }

        try {
            $device = BorrowDeviceFake::findOrFail($deviceId);

            if (!$this->ownsBorrow((int) $device->borrow_id)) {
                return $this->error(__('sys.item_not_found'), 404);
            }

            $device->quantity = (int) $quantity;
            $device->save();

            return response()->json(['success' => true, 'message' => 'Quantity updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $deviceId = $request->input('fake_device_id', $id);

        if (!$deviceId) {
            return response()->json(['success' => false, 'message' => 'Missing device_id'], 400);
        }

        try {
            $device = BorrowDeviceFake::findOrFail($deviceId);

            if (!$this->ownsBorrow((int) $device->borrow_id)) {
                return $this->error(__('sys.item_not_found'), 404);
            }

            $device->delete();

            return response()->json(['success' => true, 'message' => 'Device deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }
    }

    protected function ownsBorrow(int $borrowId): bool
    {
        return Borrow::query()
            ->where('id', $borrowId)
            ->where('user_id', Auth::id())
            ->exists();
    }
}
