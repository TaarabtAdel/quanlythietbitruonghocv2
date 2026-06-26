<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\Lab;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BorrowLabController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('lab_id')) {
            return $this->show($request);
        }

        if (!$request->filled('week')) {
            $request->merge(['week' => Carbon::now()->format('Y-\WW')]);
        }

        $items = Borrow::getBorrowedLabs($request);
        $weekMeta = $this->weekMeta($request);

        return $this->success([
            'week' => $request->week,
            'start_date' => $weekMeta['startDate']?->format('Y-m-d'),
            'end_date' => $weekMeta['endDate']?->format('Y-m-d'),
            'schedule' => $items,
        ]);
    }

    public function show(Request $request, ?int $labId = null): JsonResponse
    {
        if ($labId) {
            $request->merge(['lab_id' => $labId]);
        }

        if (!$request->filled('week')) {
            $request->merge(['week' => Carbon::now()->format('Y-\WW')]);
        }

        $weekMeta = $this->weekMeta($request);
        $lab = Lab::find($request->lab_id);

        $items = Borrow::getBorrowedLab($request);

        return $this->success([
            'lab_id' => (int) $request->lab_id,
            'lab_name' => $lab?->name ?? '',
            'week' => $request->week,
            'start_date' => $weekMeta['startDate']?->format('Y-m-d'),
            'end_date' => $weekMeta['endDate']?->format('Y-m-d'),
            'schedule' => $items,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        if (!$request->filled('week')) {
            $request->merge(['week' => Carbon::now()->format('Y-\WW')]);
        }

        return $this->success([
            'week' => $request->week,
            'items' => Borrow::getBorrowedLabsSummary($request),
        ]);
    }

    protected function weekMeta(Request $request): array
    {
        if ($request->filled('sw_start_week') && $request->filled('sw_end_week')) {
            return [
                'startDate' => Carbon::parse($request->sw_start_week),
                'endDate' => Carbon::parse($request->sw_end_week),
            ];
        }

        if ($request->filled('week')) {
            return Borrow::getStartEndDateFromWeek($request->week);
        }

        return ['startDate' => null, 'endDate' => null];
    }
}
