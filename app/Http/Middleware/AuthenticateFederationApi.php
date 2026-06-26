<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateFederationApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('federation.enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => 'Federation API is disabled.',
            ], 503);
        }

        $configuredKey = config('federation.api_key');
        if ($configuredKey === null || $configuredKey === '') {
            return response()->json([
                'success' => false,
                'message' => 'Federation API key is not configured.',
            ], 503);
        }

        $providedKey = $request->header('X-SGD-Key') ?? $request->bearerToken();
        if (! is_string($providedKey) || ! hash_equals($configuredKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid federation API key.',
            ], 401);
        }

        return $next($request);
    }
}
