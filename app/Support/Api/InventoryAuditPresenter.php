<?php

namespace App\Support\Api;

use App\Models\InventoryAudit;
use App\Models\InventoryRecord;

class InventoryAuditPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function listItem(InventoryAudit $audit): array
    {
        $audit->loadMissing(['user']);

        return [
            'id' => $audit->id,
            'name' => $audit->name,
            'school_year' => $audit->school_year,
            'audit_date' => $audit->audit_date,
            'status' => (int) $audit->status,
            'status_label' => self::statusLabel((int) $audit->status),
            'note' => $audit->note,
            'user_name' => $audit->user?->name,
            'created_at' => $audit->created_at?->format('d/m/Y H:i'),
            'updated_at' => $audit->updated_at?->format('d/m/Y H:i'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function detail(InventoryAudit $audit): array
    {
        $audit->loadMissing(['user', 'records.device']);

        return array_merge(self::listItem($audit), [
            'records' => $audit->records->map(fn (InventoryRecord $record) => self::recordItem($record))->values()->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function recordItem(InventoryRecord $record): array
    {
        $device = $record->device;

        return [
            'id' => $record->id,
            'device_id' => $record->device_id,
            'device_name' => $device?->name,
            'device_year' => $device?->year,
            'device_country' => $device?->country_name ?? $device?->country ?? '',
            'device_unit' => $device?->unit,
            'device_price' => $device?->price,
            'initial_total' => (int) $record->initial_total,
            'initial_damaged' => (int) $record->initial_damaged,
            'increase_quantity' => (int) $record->increase_quantity,
            'decrease_quantity' => (int) $record->decrease_quantity,
            'final_total' => (int) $record->final_total,
            'final_damaged' => (int) $record->final_damaged,
        ];
    }

    public static function statusLabel(int $status): string
    {
        return match ($status) {
            InventoryAudit::DRAFT => 'Nháp',
            InventoryAudit::INACTIVE => 'Chờ duyệt',
            InventoryAudit::ACTIVE => 'Đã duyệt',
            default => 'Không rõ',
        };
    }
}
