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
            
            // Check if xhr has addEventListener (some wrapped xhr objects don't)
            if (xhr && typeof xhr.addEventListener === 'function') {
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
            } else {
                // Fallback: track via ajaxComplete event
                $(document).one('ajaxComplete', function(event, xhr, settings) {
                    if (settings && settings.url) {
                        const endTime = performance.now();
                        const duration = endTime - startTime;
                        totalApiTime += duration;
                        
                        apiCallDetails.push({
                            url: settings.url,
                            method: settings.type,
                            status: xhr && xhr.status ? xhr.status : 0,
                            duration: Math.round(duration),
                            timestamp: new Date().toISOString()
                        });
                        
                        console.log(`[PERF] jQuery AJAX Complete: ${settings.type} ${settings.url} - ${Math.round(duration)}ms`);
                    }
                });
            }
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
    
    // Action-specific performance tracking
    window.trackAction = function(actionType, actionName) {
        var actionId = actionType + '_' + Date.now();
        var startTime = performance.now();
        
        // Store action tracking data
        if (!window.__actionMetrics) {
            window.__actionMetrics = [];
        }
        
        window.__actionMetrics.push({
            id: actionId,
            type: actionType,
            name: actionName,
            startTime: startTime,
            duration: null,
            status: 'pending'
        });
        
        // Return function to mark action as complete
        return function(status) {
            var endTime = performance.now();
            var duration = Math.round(endTime - startTime);
            
            var action = window.__actionMetrics.find(function(a) {
                return a.id === actionId;
            });
            
            if (action) {
                action.duration = duration;
                action.status = status || 'completed';
                action.endTime = endTime;
            }
            
            console.log('[PERF] Action:', actionType, actionName, '-', duration, 'ms', '(' + (status || 'completed') + ')');
            
            // Warn if action takes too long
            if (duration > 1000) {
                console.warn('[PERF] Slow Action Detected:', actionType, actionName, '-', duration, 'ms');
            }
            
            return duration;
        };
    };
    
    // Track form submissions
    $(document).on('submit', 'form', function() {
        var form = this;
        var action = form.action || window.location.pathname;
        var actionName = action.split('/').pop() || 'form-submit';
        var completeAction = window.trackAction('submit', actionName);
        
        // Mark as complete when form submit completes (via AJAX or redirect)
        $(form).one('ajaxComplete', function() {
            completeAction('ajax-complete');
        });
        
        // If no AJAX, mark as complete after a delay (redirect)
        setTimeout(function() {
            if (window.__actionMetrics) {
                var pending = window.__actionMetrics.find(function(a) {
                    return a.name === actionName && a.status === 'pending';
                });
                if (pending) {
                    completeAction('redirect');
                }
            }
        }, 100);
    });
    
    // Track button clicks for add/edit/delete actions
    $(document).on('click', 'a[href*="/create"], a[href*="/add"], button[data-action="add"], a.btn-success', function() {
        var href = $(this).attr('href') || '';
        var actionName = href.split('/').pop() || 'add';
        window.trackAction('add', actionName);
    });
    
    $(document).on('click', 'a[href*="/edit"], button[data-action="edit"], a.btn-primary[href*="edit"]', function() {
        var href = $(this).attr('href') || '';
        var actionName = href.split('/').pop() || 'edit';
        window.trackAction('edit', actionName);
    });
    
    $(document).on('click', 'button[data-action="delete"], button.btn-danger, a[data-method="delete"]', function() {
        var actionName = $(this).data('id') || $(this).closest('tr').find('td:first').text() || 'delete';
        window.trackAction('delete', actionName);
    });
    
    // Track redirects via link clicks
    $(document).on('click', 'a', function() {
        var href = $(this).attr('href');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            var completeAction = window.trackAction('redirect', href);
            // Mark complete after navigation (won't execute if navigation happens)
            setTimeout(function() {
                completeAction('navigation-complete');
            }, 100);
        }
    });
    
    // Log immediately if window already loaded
    if (document.readyState === 'complete') {
        logPerformanceMetrics();
    }
})();

