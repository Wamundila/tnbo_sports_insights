<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInsightsServiceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredApiKey = (string) config('insights.api_key', '');

        if ($configuredApiKey === '') {
            if (app()->environment(['local', 'testing'])) {
                return $next($request);
            }

            return new JsonResponse([
                'message' => 'Insights API key is not configured.',
                'code' => 'API_KEY_NOT_CONFIGURED',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $providedApiKey = $request->header(config('insights.api_key_header'));

        if (! is_string($providedApiKey) || ! hash_equals($configuredApiKey, $providedApiKey)) {
            return new JsonResponse([
                'message' => 'Unauthorized.',
                'code' => 'UNAUTHORIZED',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
