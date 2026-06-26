<?php

namespace App\Support\Api;

use App\Models\Borrow;
use Illuminate\Support\Facades\Cache;

class SchoolCalendar
{
    public static function schoolYears(): array
    {
        $firstBorrow = Cache::remember('api_first_borrow_record', 1440, function () {
            return Borrow::orderBy('borrow_date', 'ASC')->first();
        });

        $startYear = $firstBorrow
            ? (int) date('Y', strtotime($firstBorrow->borrow_date))
            : (int) date('Y');

        $items = [];
        for ($year = $startYear; $year <= (int) date('Y'); $year++) {
            $value = $year . '-' . ($year + 1);
            $items[] = ['id' => $value, 'name' => $value];
        }

        return $items;
    }

    public static function schoolWeekConfig(): array
    {
        $firstBorrow = Cache::remember('api_first_borrow_created', 1440, function () {
            return Borrow::orderBy('created_at', 'ASC')->first();
        });

        $startYear = $firstBorrow
            ? (int) date('Y', strtotime($firstBorrow->created_at))
            : (int) date('Y');

        $configs = [];
        for ($year = $startYear; $year <= (int) date('Y'); $year++) {
            $key = $year . '-' . ($year + 1);
            $configs[$key] = [
                'startWeek1' => "{$year}-09-05",
                'numberWeek' => 38,
            ];
        }

        return $configs;
    }

    public static function currentSchoolYear(): string
    {
        $month = (int) date('n');
        $year = (int) date('Y');

        if ($month >= 9) {
            return $year . '-' . ($year + 1);
        }

        return ($year - 1) . '-' . $year;
    }

    public static function currentIsoWeek(): string
    {
        return date('Y-\WW');
    }
}
