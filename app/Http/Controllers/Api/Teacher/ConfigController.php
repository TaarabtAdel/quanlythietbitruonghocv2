<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\Department;
use App\Models\DeviceType;
use App\Models\Group;
use App\Models\Nest;
use App\Models\Option;
use App\Support\Api\SchoolCalendar;
use Illuminate\Http\JsonResponse;

class ConfigController extends Controller
{
    public function index(): JsonResponse
    {
        $appVersion = Option::get_option_name('app_verison', '1.0');

        return $this->success([
            'app_name' => config('app.name'),
            'app_version' => $appVersion,
            'features' => [
                'curricula' => version_compare($appVersion, '2.7', '>='),
            ],
            'borrow_purposes' => Borrow::get_borrow_purposes(),
            'borrow_statuses' => [
                ['value' => Borrow::ACTIVE, 'label' => 'Duyệt'],
                ['value' => Borrow::INACTIVE, 'label' => 'Chờ'],
                ['value' => Borrow::CANCELED, 'label' => 'Hủy'],
                ['value' => Borrow::DRAFT, 'label' => 'Nháp'],
            ],
            'return_statuses' => collect(Borrow::RETURN_STATUS)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values(),
        ]);
    }

    public function departments(): JsonResponse
    {
        return $this->success(
            Department::query()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function deviceTypes(): JsonResponse
    {
        return $this->success(
            DeviceType::query()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function schoolYears(): JsonResponse
    {
        return $this->success(SchoolCalendar::schoolYears());
    }

    public function schoolWeeks(): JsonResponse
    {
        return $this->success([
            'current_school_year' => SchoolCalendar::currentSchoolYear(),
            'current_week' => SchoolCalendar::currentIsoWeek(),
            'config' => SchoolCalendar::schoolWeekConfig(),
        ]);
    }

    public function borrowPurposes(): JsonResponse
    {
        $purposes = Borrow::get_borrow_purposes();

        return $this->success(
            collect($purposes)->map(fn ($label, $slug) => [
                'slug' => $slug,
                'name' => $label,
            ])->values()
        );
    }

    public function nests(): JsonResponse
    {
        return $this->success(
            Nest::query()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function groups(): JsonResponse
    {
        return $this->success(
            Group::query()->orderBy('name')->get(['id', 'name'])
        );
    }

    public function grades(): JsonResponse
    {
        return $this->success([
            ['value' => '6', 'label' => '6'],
            ['value' => '7', 'label' => '7'],
            ['value' => '8', 'label' => '8'],
            ['value' => '9', 'label' => '9'],
            ['value' => '10', 'label' => '10'],
            ['value' => '11', 'label' => '11'],
            ['value' => '12', 'label' => '12'],
        ]);
    }
}
