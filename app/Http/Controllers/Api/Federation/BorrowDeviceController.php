<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\BorrowDevice;
use App\Models\Nest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowDeviceController extends Controller
{
    public function ledger(Request $request): JsonResponse
    {
        $request = $this->normalizeLedgerRequest($request);

        if ($request->boolean('require_user') && ! $request->filled('user_id')) {
            return $this->success([
                'items' => [],
                'range' => $this->resolveRange($request),
                'filters' => $this->filterPayload(),
            ]);
        }

        $items = BorrowDevice::getItems($request);

        return $this->success([
            'items' => $items,
            'range' => $this->resolveRange($request),
            'filters' => $this->filterPayload(),
        ]);
    }

    public function filters(): JsonResponse
    {
        return $this->success($this->filterPayload());
    }

    protected function normalizeLedgerRequest(Request $request): Request
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $request->merge([
                'sw_start_week' => $request->start_date,
                'sw_end_week' => $request->end_date,
            ]);
        } elseif (! $request->filled('week') && ! $request->filled('borrow_date') && ! $request->filled('sw_start_week')) {
            $currentWeek = Carbon::now()->format('Y-\WW');
            $request->merge(['week' => $currentWeek]);
        }

        if ($request->filled('session') && in_array($request->session, ['AM', 'PM'], true)) {
            // BorrowDevice::getItems maps AM/PM internally.
        }

        return $request;
    }

    /**
     * @return array{start: string, end: string, label: string}
     */
    protected function resolveRange(Request $request): array
    {
        if ($request->filled('sw_start_week') && $request->filled('sw_end_week')) {
            return [
                'start' => $request->sw_start_week,
                'end' => $request->sw_end_week,
                'label' => 'custom',
            ];
        }

        if ($request->filled('borrow_date')) {
            return [
                'start' => $request->borrow_date,
                'end' => $request->borrow_date,
                'label' => 'day',
            ];
        }

        if ($request->filled('week')) {
            $range = Borrow::getStartEndDateFromWeek($request->week);

            return [
                'start' => $range['startDate']->format('Y-m-d'),
                'end' => $range['endDate']->format('Y-m-d'),
                'label' => $request->week,
            ];
        }

        $currentWeek = Carbon::now()->format('Y-\WW');
        $range = Borrow::getStartEndDateFromWeek($currentWeek);

        return [
            'start' => $range['startDate']->format('Y-m-d'),
            'end' => $range['endDate']->format('Y-m-d'),
            'label' => $currentWeek,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function filterPayload(): array
    {
        return [
            'nests' => Nest::query()->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->whereNull('deleted_at')->orderBy('name')->limit(500)->get(['id', 'name']),
        ];
    }
}
