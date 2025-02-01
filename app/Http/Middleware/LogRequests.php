<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Determine Device Type
        $userAgent = $request->header('User-Agent');
        $deviceType =$userAgent;

        // Log the request in the database
        RequestLog::create([
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'request_data' => json_encode($request->all()), // Store all request data
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
        ]);

        return $next($request);
    }
}
