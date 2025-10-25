/**
 * Frontend Performance Monitoring Script
 * Tracks timing from page load to content visible
 */

(function() {
    'use strict';
    
    // Performance timing marks
    const marks = {
        pageLoadStart: 0,
        domReady: 0,
        contentRendered: 0,
        pageInteractive: 0,
        apiCallsStart: {},
        apiCallsEnd: {},
        apiCallsDuration: {}
    };
    
    let apiCallCount = 0;
    let totalApiTime = 0;
    let apiCallDetails = [];
    
    // Track when page starts loading
    marks.pageLoadStart = performance.timing.navigationStart || performance.now();
    
    // Mark DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        marks.domReady = performance.now();
        console.log('[PERF] DOM Ready:', marks.domReady - marks.pageLoadStart, 'ms');
    });
    
    // Mark when page is fully interactive
    window.addEventListener('load', function() {
        marks.pageInteractive = performance.now();
        console.log('[PERF] Page Fully Loaded:', marks.pageInteractive - marks.pageLoadStart, 'ms');
        
        // Calculate and log total performance metrics
        setTimeout(logPerformanceMetrics, 500);
    });
    
    /**
     * Override XMLHttpRequest to track AJAX calls
     */
    (function() {
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;
        
        XMLHttpRequest.prototype.open = function(method, url, async, user, password) {
            this._method = method;
            this._url = url;
            return originalOpen.apply(this, arguments);
        };
        
        XMLHttpRequest.prototype.send = function(data) {
            const requestId = `api_${++apiCallCount}_${Date.now()}`;
            const startTime = performance.now();
            marks.apiCallsStart[requestId] = startTime;
            
            // Track on completion
            this.addEventListener('loadend', function() {
                const endTime = performance.now();
                const duration = endTime - startTime;
                
                marks.apiCallsEnd[requestId] = endTime;
                marks.apiCallsDuration[requestId] = duration;
                totalApiTime += duration;
                
                const status = this.status;
                const url = this._url;
                
                apiCallDetails.push({
                    url: url,
                    method: this._method,
                    status: status,
                    duration: Math.round(duration),
                    timestamp: new Date().toISOString()
                });
                
                console.log(`[PERF] API Call: ${this._method} ${url} - ${Math.round(duration)}ms (Status: ${status})`);
            });
            
            return originalSend.apply(this, arguments);
        };
    })();
    
    /**
     * Override jQuery's ajax if available
     */
    if (typeof jQuery !== 'undefined') {
        $(document).ajaxSend(function(event, xhr, settings) {
            const requestId = `jquery_api_${++apiCallCount}_${Date.now()}`;
            const startTime = performance.now();
            marks.apiCallsStart[requestId] = startTime;
            
            xhr.addEventListener('loadend', function() {
                const endTime = performance.now();
                const duration = endTime - startTime;
                totalApiTime += duration;
                
                apiCallDetails.push({
                    url: settings.url,
                    method: settings.type,
                    status: xhr.status,
                    duration: Math.round(duration),
                    timestamp: new Date().toISOString()
                });
                
                console.log(`[PERF] jQuery AJAX: ${settings.type} ${settings.url} - ${Math.round(duration)}ms`);
            });
        });
    }
    
    /**
     * Log comprehensive performance metrics
     */
    function logPerformanceMetrics() {
        const navigation = performance.getEntriesByType('navigation')[0];
        const resourceTimings = performance.getEntriesByType('resource');
        
        // Calculate metrics
        const metrics = {
            timestamp: new Date().toISOString(),
            page: {
                url: window.location.href,
                path: window.location.pathname,
                title: document.title
            },
            timing: {
                totalLoadTime: navigation ? Math.round(navigation.loadEventEnd - navigation.fetchStart) : 0,
                domContentLoaded: navigation ? Math.round(navigation.domContentLoadedEventEnd - navigation.fetchStart) : 0,
                timeToFirstByte: navigation ? Math.round(navigation.responseStart - navigation.fetchStart) : 0,
                domInteractive: navigation ? Math.round(navigation.domInteractive - navigation.fetchStart) : 0,
                pageDownload: navigation ? Math.round(navigation.responseEnd - navigation.responseStart) : 0,
            },
            resources: {
                totalResources: resourceTimings.length,
                totalResourceSize: 0,
                slowestResources: []
            },
            api: {
                totalCalls: apiCallCount,
                totalTime: Math.round(totalApiTime),
                averageTime: apiCallCount > 0 ? Math.round(totalApiTime / apiCallCount) : 0,
                calls: apiCallDetails
            },
            performance: {
                memoryUsage: performance.memory ? {
                    usedJSHeapSize: Math.round(performance.memory.usedJSHeapSize / 1024 / 1024) + 'MB',
                    totalJSHeapSize: Math.round(performance.memory.totalJSHeapSize / 1024 / 1024) + 'MB',
                    jsHeapSizeLimit: Math.round(performance.memory.jsHeapSizeLimit / 1024 / 1024) + 'MB'
                } : 'Not available'
            }
        };
        
        // Analyze resources
        let totalSize = 0;
        const slowResources = [];
        
        resourceTimings.forEach(resource => {
            const size = resource.transferSize || 0;
            totalSize += size;
            
            const duration = resource.responseEnd - resource.startTime;
            if (duration > 500) { // Flag resources taking more than 500ms
                slowResources.push({
                    name: resource.name.split('/').pop(),
                    type: resource.initiatorType,
                    duration: Math.round(duration),
                    size: Math.round(size / 1024) + 'KB'
                });
            }
        });
        
        metrics.resources.totalResourceSize = Math.round(totalSize / 1024) + 'KB';
        metrics.resources.slowestResources = slowResources.slice(0, 5); // Top 5 slowest
        
        // Log to console
        console.group('[PERF] Frontend Performance Report');
        console.log('Page:', metrics.page.title);
        console.log('URL:', metrics.page.url);
        console.log('Total Load Time:', metrics.timing.totalLoadTime, 'ms');
        console.log('DOM Content Loaded:', metrics.timing.domContentLoaded, 'ms');
        console.log('Time to First Byte:', metrics.timing.timeToFirstByte, 'ms');
        console.log('Resources Loaded:', metrics.resources.totalResources);
        console.log('Total Resource Size:', metrics.resources.totalResourceSize);
        console.log('API Calls:', metrics.api.totalCalls);
        console.log('Total API Time:', metrics.api.totalTime, 'ms');
        console.log('Average API Time:', metrics.api.averageTime, 'ms');
        
        if (metrics.resources.slowestResources.length > 0) {
            console.log('Slow Resources:', metrics.resources.slowestResources);
        }
        
        console.log('Detailed Metrics:', metrics);
        console.groupEnd();
        
        // Send to backend for analysis (optional)
        if (typeof navigator.sendBeacon !== 'undefined') {
            try {
                navigator.sendBeacon('/api/log-performance', JSON.stringify(metrics));
            } catch (e) {
                console.warn('Could not send performance metrics to backend:', e);
            }
        }
        
        // Return metrics for further analysis
        window.__perfMetrics = metrics;
    }
    
    // Expose logPerformanceMetrics globally for manual invocation
    window.logPerformanceMetrics = logPerformanceMetrics;
    
    // Log immediately if window already loaded
    if (document.readyState === 'complete') {
        logPerformanceMetrics();
    }
})();

