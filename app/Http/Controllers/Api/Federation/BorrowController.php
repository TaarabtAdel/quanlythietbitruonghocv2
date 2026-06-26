<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\Nest;
use App\Models\User;
use App\Support\Api\BorrowPresenter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 30);
        $query = Borrow::query(true)
            ->with(['user'])
            ->whereNull('deleted_at')
            ->orderByDesc('borrow_date')
            ->orderByDesc('id');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('borrow_date')) {
            $query->whereDate('borrow_date', $request->borrow_date);
        }
        if ($request->filled('nest_id')) {
            $query->whereHas('user', fn ($q) => $q->where('nest_id', $request->nest_id));
        }
        if ($request->filled('week')) {
            $week = $request->week;
            $year = substr($week, 0, 4);
            $weekNumber = substr($week, -2);
            $startDate = Carbon::now()->setISODate((int) $year, (int) $weekNumber)->startOfWeek();
            $endDate = Carbon::now()->setISODate((int) $year, (int) $weekNumber)->endOfWeek();
            $query->whereBetween('borrow_date', [$startDate, $endDate]);
        }

        $items = $query->paginate($limit);

        return $this->paginated($items, fn (Borrow $borrow) => BorrowPresenter::listItem($borrow));
    }

    public function show(int $id): JsonResponse
    {
        $borrow = Borrow::query(true)->whereNull('deleted_at')->find($id);

        if (! $borrow) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success(BorrowPresenter::detail($borrow));
    }

    public function filters(): JsonResponse
    {
        return $this->success([
            'nests' => Nest::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->whereNull('deleted_at')->orderBy('name')->limit(500)->get(['id', 'name']),
        ]);
    }
}
