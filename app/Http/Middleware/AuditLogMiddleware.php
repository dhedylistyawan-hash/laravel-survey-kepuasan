<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        
        // Log sensitive operations
        $sensitiveRoutes = [
            'login',
            'logout',
            'admin.backup',
            'survey.store',
            'admin.change-password'
        ];
        
        $currentRoute = $request->route() ? $request->route()->getName() : null;
        
        if ($currentRoute && in_array($currentRoute, $sensitiveRoutes)) {
            $logData = [
                'timestamp' => now()->toISOString(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => $currentRoute,
                'user_id' => auth()->id(),
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'request_size' => strlen($request->getContent()),
                'response_size' => strlen($response->getContent()),
            ];
            
            Log::channel('audit')->info('Security Event', $logData);
        }
        
        return $response;
    }
}