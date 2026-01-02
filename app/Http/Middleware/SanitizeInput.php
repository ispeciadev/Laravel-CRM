<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize Input Middleware
 * 
 * Provides additional input sanitization for security.
 * Removes potentially dangerous characters and patterns from input.
 */
class SanitizeInput
{
    /**
     * Keys to exclude from sanitization (for fields like passwords, etc.)
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
        'content', // Rich text editor content
        'body',    // Email body
        'html',    // HTML content
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        array_walk_recursive($input, function (&$value, $key) {
            if (is_string($value) && !in_array($key, $this->except)) {
                // Remove null bytes
                $value = str_replace(chr(0), '', $value);
                
                // Remove non-printable characters (except for newlines and tabs)
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
