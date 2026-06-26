<?php

namespace App\Http\Controllers\Api\Federation;

use App\Http\Controllers\Api\Controller;
use App\Models\Borrow;
use App\Models\Device;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;

class MetaController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->success([
            'school' => [
                'company_name' => Option::get_option('general', 'company_name'),
                'company_parent' => Option::get_option('general', 'company_parent'),
                'company_sgd_code' => Option::get_option('general', 'company_sgd_code'),
                'company_address' => Option::get_option('general', 'company_address'),
            ],
            'stats' => $this->buildStats(),
            'synced_at' => now()->toIso8601String(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return $this->success($this->buildStats());
    }

    /**
     * @return array<string, int>
     */
    protected function buildStats(): array
    {
        $stats = [
            'devices' => (int) Device::query()->whereNull('deleted_at')->count(),
            'borrows' => (int) Borrow::query()->whereNull('deleted_at')->count(),
            'users' => (int) \App\Models\User::query()->whereNull('deleted_at')->count(),
            'assets' => $this->safeCount('assets'),
            'labs' => $this->safeCount('labs'),
            'rooms' => $this->safeCount('rooms'),
            'borrow_pending' => (int) Borrow::query()->whereNull('deleted_at')->where('status', Borrow::INACTIVE)->count(),
            'borrow_approved' => (int) Borrow::query()->whereNull('deleted_at')->where('status', Borrow::ACTIVE)->count(),
            'inventory_audits' => $this->safeCount('inventory_audits'),
        ];

        return $stats;
    }

    protected function safeCount(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) \Illuminate\Support\Facades\DB::table($table)->count();
    }
}
