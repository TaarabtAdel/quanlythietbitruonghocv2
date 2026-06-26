<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\StoreBorrowRequest;
use App\Models\Borrow;
use App\Models\Room;
use App\Support\Api\BorrowPresenter;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BorrowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 20);
        $query = Borrow::query(true)->whereNull('deleted_at');

        if ($request->filled('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('status') && $request->status !== '' && (int) $request->status > -1) {
            $query->where('status', $request->status);
        }

        if ($request->filled('borrow_date')) {
            $query->whereDate('borrow_date', $request->borrow_date);
        }

        if ($request->filled('sw_start_week') && $request->filled('sw_end_week')) {
            $query->whereBetween('borrow_date', [$request->sw_start_week, $request->sw_end_week])
                ->orderBy('borrow_date', 'ASC');
        }

        if ($request->filled('school_years')) {
            $range = Borrow::getStartEndDateFromYear($request->school_years);
            if ($range['startDate'] && $range['endDate']) {
                $query->whereBetween('borrow_date', [$range['startDate'], $range['endDate']]);
            }
        }

        if ($request->filled('week')) {
            $week = $request->week;
            $year = substr($week, 0, 4);
            $weekNumber = substr($week, -2);
            $startDate = Carbon::now()->setISODate((int) $year, (int) $weekNumber)->startOfWeek();
            $endDate = Carbon::now()->setISODate((int) $year, (int) $weekNumber)->endOfWeek();
            $query->whereBetween('borrow_date', [$startDate, $endDate])
                ->orderBy('borrow_date', 'ASC');
        }

        $query->where('user_id', Auth::id())
            ->orderBy('borrow_date', 'DESC')
            ->orderBy('id', 'DESC');

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Borrow $item) => BorrowPresenter::listItem($item));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $borrow = Borrow::create([
                'user_id' => Auth::id(),
                'borrow_date' => date('Y-m-d', strtotime('+1 day')),
                'status' => Borrow::DRAFT,
            ]);

            return $this->success(
                BorrowPresenter::detail($borrow),
                __('sys.store_item_success'),
                201
            );
        } catch (QueryException $e) {
            Log::error('API borrow store: ' . $e->getMessage());

            return $this->error(__('sys.store_item_error'), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $borrow = $this->findOwnedBorrow($id);

        if (!$borrow) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success(BorrowPresenter::detail($borrow));
    }

    public function update(StoreBorrowRequest $request, int $id): JsonResponse
    {
        $borrow = $this->findOwnedBorrow($id);

        if (!$borrow) {
            return response()->json([
                'success' => false,
                'msg' => __('sys.item_not_found'),
            ], 404);
        }

        if (!$borrow->can_edit && !in_array($request->input('task'), ['show-devices', 'show-labs'], true)) {
            return response()->json([
                'success' => false,
                'msg' => 'Không có quyền chỉnh sửa phiếu mượn này.',
            ], 403);
        }

        try {
            $result = Borrow::updateItem($id, $request);

            if (($result['success'] ?? false) === false) {
                return response()->json([
                    'success' => false,
                    'msg' => strip_tags($result['message'] ?? 'Cập nhật thất bại'),
                ], 422);
            }

            $borrow->refresh();

            return response()->json([
                'success' => true,
                'msg' => __('sys.update_item_success'),
                'data' => BorrowPresenter::detail($borrow),
            ]);
        } catch (\Exception $e) {
            Log::error('API borrow update: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg' => __('sys.update_item_error'),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $borrow = $this->findOwnedBorrow($id);

        if (!$borrow) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        if (!$borrow->can_delete) {
            return $this->error('Không có quyền xóa phiếu mượn này.', 403);
        }

        try {
            Borrow::deleteItem($id);

            return $this->success(null, __('sys.destroy_item_success'));
        } catch (\Exception $e) {
            return $this->error(__('sys.destroy_item_error'), 500);
        }
    }

    public function copy(int $id): JsonResponse
    {
        $borrow = $this->findOwnedBorrow($id);

        if (!$borrow) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        try {
            Borrow::copyItem($id);

            return $this->success(null, __('sys.copy_item_success'));
        } catch (\Exception $e) {
            return $this->error(__('sys.copy_item_error'), 500);
        }
    }

    public function formData(int $id): JsonResponse
    {
        $borrow = $this->findOwnedBorrow($id);

        if (!$borrow) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success([
            'borrow' => BorrowPresenter::detail($borrow),
            'rooms' => Room::query()->whereNull('deleted_at')->orderBy('name')->get(['id', 'name']),
            'borrow_purposes' => Borrow::get_borrow_purposes(),
        ]);
    }

    protected function findOwnedBorrow(int $id): ?Borrow
    {
        $borrow = Borrow::query()->find($id);

        if (!$borrow || (int) $borrow->user_id !== (int) Auth::id()) {
            return null;
        }

        return $borrow;
    }
}
