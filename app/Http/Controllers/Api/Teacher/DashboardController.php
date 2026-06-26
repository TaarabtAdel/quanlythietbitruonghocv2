<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$month, $year] = $this->resolveMonthYear($request);
        $userId = Auth::id();

        $baseQuery = Borrow::query(true)
            ->where('user_id', $userId)
            ->whereMonth('borrow_date', $month)
            ->whereYear('borrow_date', $year);

        $totalBorrow = (clone $baseQuery)
            ->whereIn('status', [Borrow::ACTIVE, Borrow::INACTIVE])
            ->count();

        $totalBorrowActive = (clone $baseQuery)
            ->where('status', Borrow::ACTIVE)
            ->count();

        $totalBorrowInactive = (clone $baseQuery)
            ->where('status', Borrow::INACTIVE)
            ->count();

        $events = $this->calendarEvents($year, $month);

        return $this->success([
            'month' => $month,
            'year' => $year,
            'total_borrow' => $totalBorrow,
            'total_borrow_active' => $totalBorrowActive,
            'total_borrow_inactive' => $totalBorrowInactive,
            'events' => $events,
        ]);
    }

    /**
     * @return array{0: int, 1: int}
     */
    protected function resolveMonthYear(Request $request): array
    {
        $month = $request->integer('month', (int) Carbon::now()->format('m'));
        $year = $request->integer('year', (int) Carbon::now()->format('Y'));

        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));

        return [$month, $year];
    }

    protected function calendarEvents(int $year, int $month): array
    {
        $userId = Auth::id();

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startDate = $startOfMonth->copy()->subDays(7);
        $endDate = $endOfMonth->copy()->addDays(7);

        $borrows = Borrow::query()
            ->where('user_id', $userId)
            ->whereBetween('borrow_date', [$startDate, $endDate])
            ->where('status', Borrow::ACTIVE)
            ->get(['id', 'borrow_date']);

        return $borrows->map(fn (Borrow $borrow) => [
            'title' => '#' . $borrow->id,
            'start' => $borrow->borrow_date,
            'borrow_id' => $borrow->id,
        ])->values()->all();
    }
}
