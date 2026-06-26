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
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $userId = Auth::id();

        $baseQuery = Borrow::query(true)
            ->where('user_id', $userId)
            ->whereMonth('borrow_date', $currentMonth)
            ->whereYear('borrow_date', $currentYear);

        $totalBorrow = (clone $baseQuery)
            ->whereIn('status', [Borrow::ACTIVE, Borrow::INACTIVE])
            ->count();

        $totalBorrowActive = (clone $baseQuery)
            ->where('status', Borrow::ACTIVE)
            ->count();

        $totalBorrowInactive = (clone $baseQuery)
            ->where('status', Borrow::INACTIVE)
            ->count();

        $events = $this->calendarEvents();

        return $this->success([
            'total_borrow' => $totalBorrow,
            'total_borrow_active' => $totalBorrowActive,
            'total_borrow_inactive' => $totalBorrowInactive,
            'events' => $events,
        ]);
    }

    protected function calendarEvents(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $userId = Auth::id();

        $startOfMonth = Carbon::create($currentYear, $currentMonth, 1);
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
