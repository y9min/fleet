<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PerformanceProfiler
{
    // Track performance metrics for each request
    protected $startTime;
    protected $startMemory;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
        
        // Start query logging if enabled
        DB::enableQueryLog();
        
        // Capture request details
        $requestMethod = $request->method();
        $requestPath = $request->path();
        $requestUrl = $request->fullUrl();
        
        $response = $next($request);
        
        // Calculate metrics
        $executionTime = (microtime(true) - $this->startTime) * 1000; // in milliseconds
        $memoryUsage = memory_get_usage() - $this->startMemory;
        $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
        $peakMemory = memory_get_peak_usage(true);
        $peakMemoryMB = round($peakMemory / 1024 / 1024, 2);
        
        // Get query information
        $queries = DB::getQueryLog();
        $totalQueries = count($queries);
        $slowQueries = [];
        $totalQueryTime = 0;
        
        foreach ($queries as $query) {
            $time = $query['time'] ?? 0;
            $totalQueryTime += $time;
            
            // Flag queries taking more than 100ms
            if ($time > 100) {
                $slowQueries[] = [
                    'query' => $query['query'] ?? 'N/A',
                    'time' => $time,
                    'bindings' => $query['bindings'] ?? []
                ];
            }
        }
        
        // Determine user type if authenticated
        $userType = auth()->check() ? auth()->user()->user_type ?? 'Unknown' : 'Guest';
        $userId = auth()->id() ?? 'N/A';
        
        // Compile performance report
        $performanceReport = [
            'timestamp' => now()->toDateTimeString(),
            'request' => [
                'method' => $requestMethod,
                'path' => $requestPath,
                'url' => $requestUrl,
                'user_type' => $userType,
                'user_id' => $userId,
            ],
            'timing' => [
                'total_time_ms' => round($executionTime, 2),
                'backend_time_ms' => round($executionTime - ($totalQueryTime), 2),
                'query_time_ms' => round($totalQueryTime, 2),
                'slow_request' => $executionTime > 1000, // Flag requests > 1 second
            ],
            'database' => [
                'total_queries' => $totalQueries,
                'total_query_time_ms' => round($totalQueryTime, 2),
                'average_query_time_ms' => $totalQueries > 0 ? round($totalQueryTime / $totalQueries, 2) : 0,
                'slow_query_count' => count($slowQueries),
            ],
            'memory' => [
                'memory_used_mb' => $memoryUsageMB,
                'peak_memory_mb' => $peakMemoryMB,
            ],
            'slow_queries' => array_slice($slowQueries, 0, 5), // Top 5 slowest queries
        ];
        
        // Log the performance metrics
        Log::channel('performance')->info('PERFORMANCE_METRICS', $performanceReport);
        
        // Log to console for immediate visibility
        if (config('app.debug')) {
            $logMessage = sprintf(
                "[PERF] %s %s - Time: %.2fms (Queries: %d, %.2fms) - Memory: %.2fMB - User: %s",
                $requestMethod,
                $requestPath,
                $executionTime,
                $totalQueries,
                $totalQueryTime,
                $memoryUsageMB,
                $userType
            );
            
            // Log warning if request is slow
            if ($executionTime > 1000) {
                Log::channel('performance')->warning("SLOW_REQUEST_DETECTED: {$logMessage}");
                error_log("⚠️  {$logMessage}");
            } else {
                error_log("✓ {$logMessage}");
            }
        }
        
        // Add performance headers to response for debugging
        if (config('app.debug')) {
            $response->headers->set('X-Performance-Total-Time', round($executionTime, 2) . 'ms');
            $response->headers->set('X-Performance-Query-Count', $totalQueries);
            $response->headers->set('X-Performance-Query-Time', round($totalQueryTime, 2) . 'ms');
            $response->headers->set('X-Performance-Memory', $memoryUsageMB . 'MB');
        }
        
        return $response;
    }
}

