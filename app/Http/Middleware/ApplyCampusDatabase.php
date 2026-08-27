<?php

namespace App\Http\Middleware;

use App\Services\CampusService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyCampusDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        view()->share('currentCampusName', null);
        view()->share('hasMultipleCampuses', false);

        try {
            CampusService::ensureReady();
        } catch (\Throwable) {
            return $next($request);
        }

        CampusService::applySelected($request->header('X-Campus-Id'));

        if (auth()->check()) {
            CampusService::rememberLogin(auth()->user());
        }

        view()->share('currentCampusName', \App\Support\TenantContext::campusName());
        view()->share('hasMultipleCampuses', CampusService::hasAffiliated());
        view()->share('canBrowseCampuses', CampusService::canBrowseCampuses());
        view()->share('canManageCampuses', CampusService::canManageCampuses());
        view()->share('isMainCampus', \App\Support\TenantContext::isMainCampus());
        view()->share('campusList', CampusService::canBrowseCampuses() ? CampusService::selectable() : []);
        view()->share('currentCampusKey', \App\Support\TenantContext::campusKey());

        if ($this->shouldSelectCampus($request)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn cơ sở.',
                    'requires_campus_select' => true,
                    'campuses' => CampusService::selectable(),
                ], 409);
            }

            return redirect()->route('campuses.select');
        }

        return $next($request);
    }

    protected function shouldSelectCampus(Request $request): bool
    {
        if ($this->isExempt($request)) {
            return false;
        }

        if (! auth()->check()) {
            return false;
        }

        return CampusService::needsSelection();
    }

    protected function isExempt(Request $request): bool
    {
        if ($request->is('api/teacher/login')) {
            return true;
        }

        return $request->routeIs(
            'login',
            'auth.postLogin',
            'auth.logout',
            'admin.login',
            'admin.authenticate',
            'admin.logout',
            'campuses.select',
            'campuses.choose',
            'admin.campuses.switch'
        );
    }
}
