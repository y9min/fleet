<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LoginPerformanceLogger
{
    /**
     * Track login performance specifically
     */
    public function handle(Request $request, Closure $next)
    {
        // Only track login and post-login redirects
        if (!$this->shouldTrack($request)) {
            return $next($request);
        }
        
        $loginStartTime = microtime(true);
        $loginStartMemory = memory_get_usage();
        
        DB::enableQueryLog();
        
        $response = $next($request);
        
        // Calculate metrics
        $executionTime = (microtime(true) - $loginStartTime) * 1000; // milliseconds
        $memoryUsage = memory_get_usage() - $loginStartMemory;
        $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
        
        // Get query information
        $queries = DB::getQueryLog();
        $totalQueries = count($queries);
        $totalQueryTime = array_sum(array_column($queries, 'time'));
        
        // Capture route and user info
        $route = $request->route()->getName();
        $userType = 'Guest';
        $userId = 'N/A';
        
        if (auth()->check()) {
            $userType = auth()->user()->user_type ?? 'Unknown';
            $userId = auth()->id() ?? 'N/A';
        }
        
        $loginMetrics = [
            'timestamp' => now()->toDateTimeString(),
            'action' => 'Login Process',
            'route' => $route,
            'method' => $request->method(),
            'path' => $request->path(),
            'user' => [
                'type' => $userType,
                'id' => $userId,
            ],
            'timing' => [
                'total_time_ms' => round($executionTime, 2),
                'query_time_ms' => round($totalQueryTime, 2),
                'backend_time_ms' => round($executionTime - $totalQueryTime, 2),
            ],
            'database' => [
                'total_queries' => $totalQueries,
                'total_query_time_ms' => round($totalQueryTime, 2),
                'average_query_time_ms' => $totalQueries > 0 ? round($totalQueryTime / $totalQueries, 2) : 0,
            ],
            'memory' => [
                'memory_used_mb' => $memoryUsageMB,
            ],
            'status' => 'success'
        ];
        
        // Log to dedicated login performance log
        Log::channel('performance')->info('LOGIN_PERFORMANCE', $loginMetrics);
        
        // Detailed console output
        if (config('app.debug')) {
            error_log("🔐 [LOGIN] {$route} - Total: {$loginMetrics['timing']['total_time_ms']}ms | Queries: {$totalQueries}, {$loginMetrics['database']['total_query_time_ms']}ms | User: {$userType}");
            
            if ($executionTime > 500) {
                error_log("⚠️  [SLOW LOGIN] {$route} took {$loginMetrics['timing']['total_time_ms']}ms");
            }
        }
        
        return $response;
    }
    
    /**
     * Determine if we should track this request
     */
    private function shouldTrack(Request $request): bool
    {
        $path = $request->path();
        
        // Track login related routes
        return in_array($path, [
            'login',
            'admin',
            'admin/dashboard',
        ]) || $request->routeIs('login', 'logout', 'admin.dashboard');
    }
}

