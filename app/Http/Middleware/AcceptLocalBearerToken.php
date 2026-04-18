<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptLocalBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        // For local development, accept any bearer token that starts with 'local-'
        $authHeader = $request->header('Authorization');
        
        if ($authHeader && str_starts_with($authHeader, 'Bearer local-')) {
            // Token is valid for local development
            // Store it in the request for reference if needed
            $request->attributes->set('local_auth_token', substr($authHeader, 7)); // Remove 'Bearer '
        }
        
        return $next($request);
    }
}
