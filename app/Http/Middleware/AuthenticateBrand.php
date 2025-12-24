<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateBrand
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('API-KEY');

        if (!$apiKey) {
            abort(401, 'API key missing');
        }

        $key = ApiKey::with('brand')
            ->where('key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$key) {
            abort(401, 'Invalid API key');
        }

        $request->attributes->set('brand_id', $key->brand_id);
        $request->attributes->set('role', $key->role); 

        return $next($request);
    }
}
