<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\InventoryAudit;
use App\Support\Api\InventoryAuditPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InventoryAuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! Schema::hasTable('inventory_audits')) {
            return $this->success([
                'items' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                ],
            ]);
        }

        $limit = $request->integer('limit', 20);
        $items = InventoryAudit::query()
            ->with('user')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->paginate($limit);

        return $this->paginated($items, fn (InventoryAudit $audit) => InventoryAuditPresenter::listItem($audit));
    }

    public function show(int $id): JsonResponse
    {
        if (! Schema::hasTable('inventory_audits')) {
            return $this->error('Module kiểm kê chưa được cài đặt.', 404);
        }

        $audit = InventoryAudit::query()
            ->whereNull('deleted_at')
            ->find($id);

        if (! $audit) {
            return $this->error(__('sys.item_not_found'), 404);
        }

        return $this->success(InventoryAuditPresenter::detail($audit));
    }
}
