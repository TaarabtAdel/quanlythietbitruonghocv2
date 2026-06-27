<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Services\SystemUpdateService;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function update(): JsonResponse
    {
        if (! SystemUpdateService::needsUpdate()) {
            return $this->success([
                'previous_version' => SystemUpdateService::currentVersion(),
                'current_version' => SystemUpdateService::currentVersion(),
                'target_version' => SystemUpdateService::targetVersion(),
                'already_up_to_date' => true,
            ], 'Trường đang dùng phiên bản mới nhất.');
        }

        $result = SystemUpdateService::run();

        if (! $result['success']) {
            return $this->error($result['message'], 422, $result);
        }

        return $this->success($result, $result['message']);
    }

    public function status(): JsonResponse
    {
        return $this->success([
            'current_version' => SystemUpdateService::currentVersion(),
            'target_version' => SystemUpdateService::targetVersion(),
            'needs_update' => SystemUpdateService::needsUpdate(),
        ]);
    }
}
