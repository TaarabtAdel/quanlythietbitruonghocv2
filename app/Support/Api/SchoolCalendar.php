<?php

namespace App\Support\Api;

use App\Models\Borrow;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

class SchoolCalendar
{
    private const CACHE_FIRST_BORROW = 'school_calendar_first_borrow_v2';

    private const CACHE_FIRST_CREATED = 'school_calendar_first_created_v2';

    private const YEAR_FLOOR = 2000;

    public static function schoolYears(): array
    {
        $startYear = self::earliestBorrowYear();
        $currentYear = (int) date('Y');
        $items = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $value = self::formatSchoolYear($year);
            $items[] = ['id' => $value, 'name' => $value];
        }

        return $items;
    }

    public static function schoolWeekConfig(): array
    {
        $startYear = self::earliestBorrowCreatedYear();
        $currentYear = (int) date('Y');
        $configs = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            $key = self::formatSchoolYear($year);
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
            return self::formatSchoolYear($year);
        }

        return self::formatSchoolYear($year - 1);
    }

    public static function currentIsoWeek(): string
    {
        return date('Y-\WW');
    }

    public static function formatSchoolYear(int $startYear): string
    {
        $startYear = self::normalizeYear($startYear);

        return sprintf('%04d-%04d', $startYear, $startYear + 1);
    }

    public static function earliestBorrowYear(): int
    {
        $firstBorrow = Cache::remember(self::CACHE_FIRST_BORROW, 1440, function () {
            return Borrow::query()
                ->orderBy('created_at')
                ->first(['borrow_date', 'created_at']);
        });

        if (! $firstBorrow) {
            return self::normalizeYear((int) date('Y'));
        }

        $candidates = array_filter([
            self::parseYear($firstBorrow->created_at),
            self::parseYear($firstBorrow->borrow_date),
        ]);

        if ($candidates === []) {
            return self::normalizeYear((int) date('Y'));
        }

        return min($candidates);
    }

    public static function parseYear(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return self::normalizeYear((int) $value->format('Y'));
        }

        $str = trim((string) $value);

        if (preg_match('/^(\d{4})-\d{2}-\d{2}/', $str, $matches)) {
            return self::normalizeYear((int) $matches[1]);
        }

        if (preg_match('/^(\d{2})-\d{2}-\d{2}$/', $str, $matches)) {
            return self::normalizeYear(self::expandTwoDigitYear((int) $matches[1]));
        }

        $timestamp = strtotime($str);
        if ($timestamp !== false) {
            return self::normalizeYear((int) date('Y', $timestamp));
        }

        return null;
    }

    public static function normalizeYear(int $year): int
    {
        if ($year < 100) {
            $year = self::expandTwoDigitYear($year);
        }

        $current = (int) date('Y');

        if ($year < self::YEAR_FLOOR) {
            return self::YEAR_FLOOR;
        }

        if ($year > $current + 1) {
            return $current;
        }

        return $year;
    }

    protected static function earliestBorrowCreatedYear(): int
    {
        $firstBorrow = Cache::remember(self::CACHE_FIRST_CREATED, 1440, function () {
            return Borrow::query()->orderBy('created_at')->value('created_at');
        });

        return self::parseYear($firstBorrow) ?? self::normalizeYear((int) date('Y'));
    }

    protected static function expandTwoDigitYear(int $yy): int
    {
        return $yy <= 69 ? 2000 + $yy : 1900 + $yy;
    }
}
