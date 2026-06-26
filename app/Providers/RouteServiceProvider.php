<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Live hosting: bật TENANT_RESOLVE=true — middleware ResolveTenantDatabase
 * tự đổi DB theo subdomain trường. Class này giữ helper tham chiếu.
 */
class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function getDatabaseName(): string
    {
        $host = request()->getHost();
        $baseDomain = config('tenant.base_domain');

        if (str_ends_with($host, '.'.$baseDomain)) {
            $subdomain = explode('.', substr($host, 0, -strlen('.'.$baseDomain)))[0];
        } else {
            $subdomain = explode('.', $host)[0];
        }

        return config('tenant.database_prefix').$subdomain;
    }

    public function checkDatabaseExist(string $databaseName): bool
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
