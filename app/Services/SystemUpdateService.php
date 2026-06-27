<?php

namespace App\Services;

use App\Models\Option;

class SystemUpdateService
{
    /** @return array<string, mixed> */
    public static function run(?string $targetVersion = null): array
    {
        $targetVersion = (string) ($targetVersion ?? env('SYSTEM_VERSION', '3.0'));
        $currentVersion = (string) Option::get_option_name('app_verison', '1.0');
        $versionToUpdate = null;
        $updated = true;

        $allAvailableUpdates = [
            '2.5' => \App\Models\Versions\Ver25::class,
            '2.6' => \App\Models\Versions\Ver26::class,
            '2.7' => \App\Models\Versions\Ver27::class,
            '2.8' => \App\Models\Versions\Ver28::class,
            '2.9' => \App\Models\Versions\Ver29::class,
            '3.0' => \App\Models\Versions\Ver30::class,
        ];

        $updateVersions = array_keys($allAvailableUpdates);
        usort($updateVersions, 'version_compare');

        foreach ($updateVersions as $versionKey) {
            $versionToUpdate = $versionKey;
            $updaterClass = $allAvailableUpdates[$versionKey];

            if (version_compare($versionToUpdate, $currentVersion, '<=')) {
                continue;
            }

            if (version_compare($versionToUpdate, $targetVersion, '>')) {
                break;
            }

            try {
                $updated = $updaterClass::doUpdate();

                if (! $updated) {
                    break;
                }
            } catch (\Exception $e) {
                $updated = false;
                break;
            }
        }

        if ($updated) {
            Option::query()->updateOrCreate(
                ['option_name' => 'app_verison'],
                [
                    'option_value' => $targetVersion,
                    'option_label' => 'Phiên bản phần mềm',
                    'option_group' => 'system',
                    'option_group_name' => 'Hệ Thống',
                ]
            );

            return [
                'success' => true,
                'message' => 'Cập nhật thành công lên phiên bản '.$targetVersion.'.',
                'previous_version' => $currentVersion,
                'current_version' => $targetVersion,
                'target_version' => $targetVersion,
                'stopped_at' => null,
            ];
        }

        $message = 'Cập nhật không thành công.';
        if ($versionToUpdate) {
            $message .= ' Đã dừng lại ở bước: '.$versionToUpdate.'.';
        }

        return [
            'success' => false,
            'message' => $message,
            'previous_version' => $currentVersion,
            'current_version' => $currentVersion,
            'target_version' => $targetVersion,
            'stopped_at' => $versionToUpdate,
        ];
    }

    public static function targetVersion(): string
    {
        return (string) env('SYSTEM_VERSION', '3.0');
    }

    public static function currentVersion(): string
    {
        return (string) Option::get_option_name('app_verison', '1.0');
    }

    public static function needsUpdate(): bool
    {
        return version_compare(self::currentVersion(), self::targetVersion(), '<');
    }
}
