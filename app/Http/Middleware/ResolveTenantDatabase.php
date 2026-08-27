<?php

namespace App\Http\Middleware;

use App\Services\CampusService;
use App\Support\TenantContext;
use App\Support\TenantDatabase;
use App\Support\TenantHostResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $baseDomain = config('tenant.base_domain');

        if (! config('tenant.tenant_resolve')) {
            $subdomain = TenantHostResolver::resolve($request, $baseDomain) ?? 'local';
            $databaseName = (string) config('database.connections.mysql.database');

            $this->rememberMain($subdomain, $databaseName);

            return $next($request);
        }

        $subdomain = TenantHostResolver::resolve($request, $baseDomain);

        if ($subdomain === null) {
            abort(403, 'Không xác định được trường từ tên miền. Truy cập qua subdomain, ví dụ: thptabc.'.config('tenant.base_domain'));
        }

        $databaseName = config('tenant.database_prefix').$subdomain;

        if (! TenantDatabase::exists($databaseName)) {
            abort(
                403,
                'Cơ sở dữ liệu trường không tồn tại hoặc tài khoản MySQL chưa có quyền truy cập: '.$databaseName
            );
        }

        TenantDatabase::connect($databaseName);
        $this->rememberMain($subdomain, $databaseName);

        return $next($request);
    }

    protected function rememberMain(string $subdomain, string $databaseName): void
    {
        TenantContext::set($subdomain, $databaseName);
        TenantContext::setMainDatabase($databaseName);
        TenantDatabase::configureMain($databaseName);
    }
}
