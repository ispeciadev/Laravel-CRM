<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Force HTTPS Middleware
 * 
 * Redirects all HTTP requests to HTTPS in production environment.
 * This is critical for security as it prevents man-in-the-middle attacks
 * and ensures all data is encrypted in transit.
 */
class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production and not for localhost/127.0.0.1
        if (config('app.env') === 'production' && !in_array($request->getHost(), ['localhost', '127.0.0.1', '0.0.0.0'])) {
            // Check if the request is not secure
            if (!$request->secure()) {
                // Redirect to HTTPS version of the URL
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }
}
