<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebdavProxyDestination
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $proxyRoot = $request->header('X-WebDAV-Proxy-Root');

        if ($request->hasHeader('Destination') && $proxyRoot) {
            $originalDestination = $request->header('Destination');
            Log::info("Current Destination: " . $originalDestination);

            $appUrl = config('app.url');
            $appSubpath = parse_url($appUrl, PHP_URL_PATH) ?: '/';
            $backendRoot = rtrim($appSubpath, "/") . '/webdav';

            // Normalize trailing slashes (remove trailing slash for consistency)
            $proxyRoot = rtrim($proxyRoot, '/');
            $backendRoot = rtrim($backendRoot, '/');

            Log::info("Replacing $proxyRoot with $backendRoot in Destination header");

            $rewritten = Str::replaceFirst($proxyRoot, $backendRoot, $originalDestination);
            Log::info("Setting Rewritten Destination: " . $rewritten);

            // Replace the header in the Symfony request object
            $_SERVER['HTTP_DESTINATION'] = $rewritten;
        }

        return $next($request);
    }
}
