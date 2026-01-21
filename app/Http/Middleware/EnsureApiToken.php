<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class EnsureApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the Authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token d\'authentification manquant.',
            ], 401);
        }

        // Find the token
        $model = Sanctum::$personalAccessTokenModel;

        $accessToken = $model::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalide.',
            ], 401);
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Token expiré.',
            ], 401);
        }

        // Authenticate the user
        $user = $accessToken->tokenable;
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        // Add user to request for Sanctum
        app('auth')->setUser($user);

        return $next($request);
    }
}
