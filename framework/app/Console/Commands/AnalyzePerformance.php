<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AnalyzePerformance extends Command
{
    protected $signature = 'performance:analyze 
                            {--file= : Specific log file to analyze}
                            {--hours=24 : Hours of logs to analyze}
                            {--summary : Show summary only}';
    
    protected $description = 'Analyze performance logs and generate report';
    
    public function handle()
    {
        $this->info('🔍 Analyzing Performance Logs...');
        $this->newLine();
        
        // Get log file path
        $logPath = storage_path('logs/performance-' . date('Y-m-d') . '.log');
        
        if (!File::exists($logPath)) {
            $this->error('No performance log found for today: ' . $logPath);
            return 1;
        }
        
        $this->info("📄 Analyzing: {$logPath}");
        $this->newLine();
        
        // Read and parse log file
        $content = File::get($logPath);
        $lines = explode("\n", $content);
        
        $logs = [];
        $summary = [
            'total_requests' => 0,
            'slow_requests' => 0,
            'total_time' => 0,
            'average_time' => 0,
            'total_queries' => 0,
            'average_queries' => 0,
            'slowest_routes' => [],
            'most_called_routes' => [],
        ];
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            try {
                $data = json_decode($line, true);
                if (!$data) continue;
                
                // Extract metrics if present
                if (isset($data['timing'])) {
                    $logs[] = $data;
                    $summary['total_requests']++;
                    $summary['total_time'] += $data['timing']['total_time_ms'] ?? 0;
                    $summary['total_queries'] += $data['database']['total_queries'] ?? 0;
                    
                    // Track slow requests
                    if (($data['timing']['total_time_ms'] ?? 0) > 1000) {
                        $summary['slow_requests']++;
                    }
                    
                    // Track routes
                    $route = $data['request']['path'] ?? 'unknown';
                    if (!isset($summary['slowest_routes'][$route])) {
                        $summary['slowest_routes'][$route] = [];
                        $summary['most_called_routes'][$route] = 0;
                    }
                    $summary['slowest_routes'][$route][] = $data['timing']['total_time_ms'] ?? 0;
                    $summary['most_called_routes'][$route]++;
                }
            } catch (\Exception $e) {
                // Skip malformed lines
                continue;
            }
        }
        
        if ($summary['total_requests'] == 0) {
            $this->error('No performance data found in log');
            return 1;
        }
        
        $summary['average_time'] = round($summary['total_time'] / $summary['total_requests'], 2);
        $summary['average_queries'] = round($summary['total_queries'] / $summary['total_requests'], 2);
        
        // Display summary
        $this->displaySummary($summary);
        
        if (!$this->option('summary')) {
            $this->displayDetails($logs, $summary);
        }
        
        return 0;
    }
    
    private function displaySummary($summary)
    {
        $this->info('═══════════════════════════════════════════════════');
        $this->info('                    PERFORMANCE SUMMARY');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Requests', $summary['total_requests']],
                ['Average Response Time', number_format($summary['average_time'], 2) . ' ms'],
                ['Slow Requests (>1000ms)', $summary['slow_requests']],
                ['Total Queries Executed', $summary['total_queries']],
                ['Average Queries per Request', number_format($summary['average_queries'], 2)],
            ]
        );
    }
    
    private function displayDetails($logs, $summary)
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════');
        $this->info('                  SLOWEST REQUESTS');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();
        
        // Sort logs by total time
        usort($logs, function($a, $b) {
            return ($b['timing']['total_time_ms'] ?? 0) <=> ($a['timing']['total_time_ms'] ?? 0);
        });
        
        $slowest = array_slice($logs, 0, 10);
        
        foreach ($slowest as $log) {
            $route = $log['request']['path'] ?? 'Unknown';
            $method = $log['request']['method'] ?? 'Unknown';
            $totalTime = $log['timing']['total_time_ms'] ?? 0;
            $queryTime = $log['timing']['query_time_ms'] ?? 0;
            $queries = $log['database']['total_queries'] ?? 0;
            
            $this->line(sprintf(
                "⏱️  [%s] %s - %s ms (Queries: %d, DB: %s ms)",
                $method,
                $route,
                number_format($totalTime, 2),
                $queries,
                number_format($queryTime, 2)
            ));
        }
        
        $this->newLine();
        $this->info('═══════════════════════════════════════════════════');
        $this->info('              MOST FREQUENT ROUTES');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();
        
        arsort($summary['most_called_routes']);
        $topRoutes = array_slice($summary['most_called_routes'], 0, 10, true);
        
        foreach ($topRoutes as $route => $count) {
            $avgTime = isset($summary['slowest_routes'][$route]) 
                ? number_format(array_sum($summary['slowest_routes'][$route]) / count($summary['slowest_routes'][$route]), 2)
                : 'N/A';
            
            $this->line(sprintf(
                "📍 %s - Called: %d times | Avg Time: %s ms",
                $route,
                $count,
                $avgTime
            ));
        }
    }
}

