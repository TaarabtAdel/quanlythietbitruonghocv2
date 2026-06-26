<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use App\Support\TenantHostResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        $baseDomain = config('tenant.base_domain');

        if (! config('tenant.tenant_resolve')) {
            // Local: DB cố định trong .env; slug lấy từ domain nếu có (vd. thptabc.test)
            $subdomain = TenantHostResolver::resolve($request, $baseDomain) ?? 'local';

            TenantContext::set(
                $subdomain,
                (string) config('database.connections.mysql.database')
            );

            return $next($request);
        }

        // Live: mỗi request tự nhận tenant từ subdomain — không dùng TENANT_SUBDOMAIN
        $subdomain = TenantHostResolver::resolve($request, $baseDomain);

        if ($subdomain === null) {
            abort(403, 'Không xác định được trường từ tên miền. Truy cập qua subdomain, ví dụ: thptabc.'.config('tenant.base_domain'));
        }

        $databaseName = config('tenant.database_prefix').$subdomain;

        if (! $this->databaseExists($databaseName)) {
            abort(403, 'Cơ sở dữ liệu trường không tồn tại.');
        }

        config(['database.connections.mysql.database' => $databaseName]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        TenantContext::set($subdomain, $databaseName);

        return $next($request);
    }

    protected function databaseExists(string $databaseName): bool
    {
        try {
            $connection = DB::connection('mysql')->getPdo();
            $databases = $connection->query('SHOW DATABASES')->fetchAll(\PDO::FETCH_COLUMN);

            return in_array($databaseName, $databases, true);
        } catch (\Throwable) {
            return false;
        }
    }
}
