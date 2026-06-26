<?php

namespace App\Support\Api;

use App\Models\Borrow;
use App\Models\BorrowDevice;
use App\Models\Device;
use Illuminate\Support\Collection;

class BorrowPresenter
{
    public static function statusLabel(int $status): string
    {
        return match ($status) {
            Borrow::DRAFT => 'Phiếu Nháp',
            Borrow::ACTIVE => 'Đã Duyệt',
            Borrow::INACTIVE => 'Chờ Duyệt',
            Borrow::CANCELED => 'Đã Hủy',
            default => '',
        };
    }

    public static function listItem(Borrow $borrow): array
    {
        $borrow->loadMissing(['user.nest', 'borrow_devices.device', 'borrow_devices.lab', 'borrow_fake_devices']);

        return [
            'id' => $borrow->id,
            'user_id' => $borrow->user_id,
            'user_name' => $borrow->user_name,
            'nest_name' => $borrow->user?->nest?->name ?? '',
            'borrow_date' => $borrow->borrow_date,
            'borrow_date_formatted' => $borrow->borrow_date_fm,
            'borrow_purpose' => $borrow->borrow_purpose,
            'status' => $borrow->status,
            'status_label' => self::statusLabel((int) $borrow->status),
            'created_at' => $borrow->created_at?->toIso8601String(),
            'created_at_formatted' => $borrow->created_at_fm,
            'device_summary' => self::plainDeviceNames($borrow),
            'lab_summary' => self::plainLabNames($borrow),
            'lesson_summary' => self::plainLessonInfo($borrow),
            'can_edit' => $borrow->can_edit,
            'can_delete' => $borrow->can_delete,
        ];
    }

    public static function detail(Borrow $borrow): array
    {
        $borrow->loadMissing([
            'user',
            'borrow_devices.device.devicetype',
            'borrow_devices.device.department',
            'borrow_devices.room',
            'borrow_devices.lab',
            'borrow_fake_devices',
        ]);

        return array_merge(self::listItem($borrow), [
            'borrow_note' => $borrow->borrow_note,
            'is_returned' => $borrow->is_returned,
            'updated_at' => $borrow->updated_at?->toIso8601String(),
            'borrow_purposes' => Borrow::get_borrow_purposes(),
            'lessons' => self::formatLessons($borrow),
        ]);
    }

    public static function formatLessons(Borrow $borrow): array
    {
        $grouped = [];
        $fakeByTiet = $borrow->borrow_fake_items;

        foreach ($borrow->borrow_items as $tiet => $items) {
            $first = $items[0] ?? null;
            $devices = [];

            foreach ($items as $row) {
                if (!$row->device_id) {
                    continue;
                }

                $devices[] = self::formatDeviceRow($row);
            }

            $grouped[] = [
                'tiet' => (int) $tiet,
                'session' => $first?->session,
                'lecture_number' => $first ? (int) $first->lecture_number : null,
                'room_id' => $first ? (int) $first->room_id : null,
                'room_name' => $first?->room?->name,
                'lecture_name' => $first?->lecture_name,
                'lesson_name' => $first?->lesson_name,
                'lab_id' => $first ? (int) ($first->lab_id ?: 0) : 0,
                'lab_name' => $first?->lab?->name,
                'devices' => $devices,
                'fake_devices' => collect($fakeByTiet[$tiet] ?? [])->map(fn ($item) => [
                    'id' => $item->id,
                    'device_name' => $item->device_name,
                    'quantity' => (int) $item->quantity,
                    'tiet' => (int) $item->tiet,
                ])->values()->all(),
            ];
        }

        if (empty($grouped)) {
            $grouped[] = [
                'tiet' => 0,
                'session' => 'Sáng',
                'lecture_number' => 1,
                'room_id' => null,
                'room_name' => null,
                'lecture_name' => '',
                'lesson_name' => '',
                'lab_id' => 0,
                'lab_name' => null,
                'devices' => [],
                'fake_devices' => [],
            ];
        }

        return $grouped;
    }

    protected static function formatDeviceRow(BorrowDevice $row): array
    {
        return [
            'device_id' => (int) $row->device_id,
            'device_name' => $row->device?->name,
            'quantity' => (int) $row->quantity,
            'device_type_name' => $row->device?->devicetype?->name,
            'department_name' => $row->device?->department?->name,
        ];
    }

    protected static function plainDeviceNames(Borrow $borrow): string
    {
        if (!$borrow->borrow_devices || !$borrow->borrow_devices->count()) {
            return '';
        }

        $parts = $borrow->borrow_devices->map(function ($row) {
            if (!$row->device_id) {
                return null;
            }
            $device = Device::find($row->device_id);

            return $device
                ? $device->name . ' (x' . $row->quantity . ')'
                : null;
        })->filter()->unique()->values();

        $text = $parts->implode(', ');

        if ($borrow->borrow_fake_devices && $borrow->borrow_fake_devices->count()) {
            $text .= ($text ? ', ' : '') . 'Thiết bị tự chuẩn bị';
        }

        return $text;
    }

    protected static function plainLabNames(Borrow $borrow): string
    {
        $labIds = $borrow->borrow_devices?->pluck('lab_id')->filter(fn ($id) => $id > 0)->unique() ?? collect();

        if ($labIds->isEmpty()) {
            return '';
        }

        return \App\Models\Lab::whereIn('id', $labIds)->pluck('name')->implode(', ');
    }

    protected static function plainLessonInfo(Borrow $borrow): string
    {
        if (!$borrow->borrow_devices || !$borrow->borrow_devices->count()) {
            return '';
        }

        return $borrow->borrow_devices
            ->map(fn ($row) => 'Buổi: ' . $row->session . ', Tiết: ' . $row->lecture_number)
            ->unique()
            ->implode(' | ');
    }
}
