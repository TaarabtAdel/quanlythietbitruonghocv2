<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        $this->routes(function () {
			Route::middleware('web')
				// ->domain('{subdomain}.' . config('app.url'))
				->namespace($this->namespace)
				->group(function () {
					$databaseName = $this->getDatabaseName();
					config(['database.connections.mysql.database' => $databaseName]);
					\DB::reconnect('mysql');
						
					$databaseExists = $this->checkDatabaseExist($databaseName);
					if (!$databaseExists) {
						abort(403);
					}else{
						require base_path('routes/web.php');
					}
				});
		});

    }

    public function getDatabaseName(){
        $url = request()->url();
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        $exploded = explode('.', $host);
        $subdomain = $exploded[0];
        $databaseName = $subdomain;
        return 'jzxzyfjmhosting_'.$databaseName;
    }
    public function checkDatabaseExist($databaseName){
        try {
            $connection = DB::connection()->getPdo();
            $databases = $connection->query("SHOW DATABASES")->fetchAll(\PDO::FETCH_COLUMN);
            return in_array($databaseName,$databases);
        } catch (\Exception $e) {
            return false;
        }
    }
}