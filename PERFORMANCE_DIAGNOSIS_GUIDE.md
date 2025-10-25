# Performance Diagnosis Guide

## What Was Installed

This performance diagnosis system adds comprehensive instrumentation to measure exactly where time is spent in your application.

### 1. Backend Middleware (`PerformanceProfiler`)
- **Location**: `framework/app/Http/Middleware/PerformanceProfiler.php`
- **What it measures**:
  - Total request execution time
  - Backend processing time
  - Database query count and total query time
  - Average query time
  - Memory usage
  - Slow queries (>100ms)
- **Logs to**: `framework/storage/logs/performance.log`

### 2. Frontend Performance Monitor
- **Location**: `framework/public/assets/js/performance-monitor.js`
- **What it measures**:
  - Page load time
  - DOM content loaded time
  - Time to first byte (TTFB)
  - API call tracking
  - Resource loading times
  - Memory usage
- **Logs to**: Browser console

### 3. Analysis Command
- **Location**: `framework/app/Console/Commands/AnalyzePerformance.php`
- **Run with**: `php artisan performance:analyze`

## How to Use

### Step 1: Enable Performance Monitoring

The middleware is already enabled. Just make sure debugging is on:

```bash
# Check if APP_DEBUG is true
grep APP_DEBUG framework/.env
```

### Step 2: Trigger Requests to Generate Data

Simply use your application normally:
1. Login
2. Navigate to dashboard
3. Click on various pages
4. Logout

Each request will log performance metrics.

### Step 3: View Logs in Real-Time

**Backend logs** (terminal):
```bash
tail -f framework/storage/logs/performance.log
```

**Frontend logs** (browser):
1. Open browser developer console (F12)
2. Go to Console tab
3. Look for `[PERF]` logs

### Step 4: Generate Performance Report

Run the analysis command:
```bash
cd framework
php artisan performance:analyze
```

### Step 5: Analyze Specific Scenarios

#### Login Performance:
```bash
# Watch login specifically
tail -f framework/storage/logs/performance.log | grep "login"
```

#### Dashboard Load Performance:
```bash
# Watch dashboard loads
tail -f framework/storage/logs/performance.log | grep "admin"
```

## What the Metrics Mean

### Total Time vs Breakdown

When you see a log like:
```
[PERF] POST /login - Time: 1250.50ms (Queries: 15, 450.25ms) - Memory: 12.5MB - User: Admin
```

This breaks down as:
- **Total Time**: 1250.50ms (from request start to response end)
  - **Backend Time**: ~800ms (processing, logic, rendering)
  - **Query Time**: ~450ms (database access)
  - **Network**: ~unknown (request/response transfer time)

### Interpreting Results

**Good Performance:**
- Total: < 500ms
- Queries: < 10 per request
- Query time: < 100ms total

**Acceptable Performance:**
- Total: 500-1000ms
- Queries: 10-20 per request
- Query time: 100-300ms

**Slow Performance (⚠️):**
- Total: > 1000ms
- Queries: > 20 per request
- Query time: > 300ms

## Common Performance Issues Found

### 1. Too Many Database Queries
**Symptom**: High query count (20+ per page load)
**Cause**: N+1 queries, eager loading not used
**Solution**: Add eager loading relationships

### 2. Slow Database Queries
**Symptom**: Individual queries taking >100ms
**Cause**: Missing indexes, inefficient joins
**Solution**: Add database indexes

### 3. Large Payloads
**Symptom**: Frontend loads slowly despite fast backend
**Cause**: Too much data sent to frontend
**Solution**: Paginate data, limit fields

### 4. Heavy Rendering
**Symptom**: Good backend time but slow total time
**Cause**: Complex view rendering, too many DOM elements
**Solution**: Optimize blade templates, use caching

## Generating Your Performance Report

After using the application for a day:

```bash
cd framework
php artisan performance:analyze --hours=24
```

This will show:
- Average response times
- Slowest routes
- Most frequent routes
- Query patterns

## Next Steps

Once you have data, you can:

1. **Identify bottlenecks**: Routes taking >1s
2. **Find redundant queries**: Routes with 20+ queries
3. **Spot slow queries**: Queries taking >100ms
4. **Optimize**: Focus improvements on the worst offenders

## Example Output

```
═══════════════════════════════════════════════════
                    PERFORMANCE SUMMARY
═══════════════════════════════════════════════════

+--------------------+---------------+
| Metric             | Value         |
+--------------------+---------------+
| Total Requests     | 1,542         |
| Average Time       | 450.25 ms     |
| Slow Requests      | 45             |
| Total Queries      | 25,380        |
| Avg Queries/Req    | 16.46         |
+--------------------+---------------+

═══════════════════════════════════════════════════
                  SLOWEST REQUESTS
═══════════════════════════════════════════════════

⏱️  [GET] /admin/dashboard - 2,345.50 ms (Queries: 28, DB: 850.25 ms)
⏱️  [GET] /admin/vehicles - 1,890.30 ms (Queries: 22, DB: 620.15 ms)
⏱️  [POST] /login - 1,650.80 ms (Queries: 15, DB: 450.30 ms)
```

## Quick Checklist

- [ ] Performance profiler middleware installed
- [ ] Frontend performance monitor script added
- [ ] Logging configuration updated
- [ ] Test login and dashboard access
- [ ] Check performance logs
- [ ] Run performance analysis command
- [ ] Identify top 3 bottlenecks
- [ ] Document findings

## Troubleshooting

### No performance logs appearing
- Check that APP_DEBUG=true in .env
- Verify middleware is in Kernel.php
- Check file permissions on storage/logs/

### Frontend metrics not showing
- Check browser console for errors
- Verify performance-monitor.js is loaded
- Look for [PERF] prefix in console

### Analysis command fails
- Make sure log file exists for today
- Check that storage/logs/ is writable
- Verify artisan command is registered

