# DataTables Debugging Guide

## Changes Made

### 1. Fixed Performance Monitor Compatibility
**File**: `framework/public/assets/js/performance-monitor.js`

**Issue**: The performance monitor was trying to add event listeners to XHR objects that DataTables wraps, causing `xhr.addEventListener is not a function` errors.

**Fix**: Added safety check and fallback:
- Check if `xhr.addEventListener` exists before calling it
- If not available, use jQuery's `ajaxComplete` event as fallback

### 2. Added Comprehensive AJAX Logging
**File**: `framework/resources/views/onboarding/index.blade.php`

**Added logging for**:
- Before AJAX request is sent
- URL being requested
- XHR object details
- Response data received
- Record counts
- Error details with full response text

## What to Look For in Browser Console

After deployment (2-3 minutes), refresh `https://www.pcoflow.com/admin/onboarding` and check the console for these logs:

### ✅ Expected Success Flow:

```
[Log] DOM ready, checking DataTables availability...
[Log] Initializing DataTable, checking if already initialized...
[Log] Creating new DataTable instance...
[Log] DataTable function is available, initializing with AJAX URL: https://www.pcoflow.com/admin/onboarding/fetch-data
[Log] [DATATABLES] AJAX beforeSend triggered
[Log] [DATATABLES] URL: https://www.pcoflow.com/admin/onboarding/fetch-data
[Log] [DATATABLES] AJAX Response received
[Log] [DATATABLES] Response JSON: {...}
[Log] [DATATABLES] Records in response: X
[Log] [DATATABLES] Returning X records to DataTables
[Log] [DATATABLES] drawCallback triggered
[Log] [DATATABLES] Total records: X
[Log] [DATATABLES] Displayed records: X
```

### ❌ Error Scenarios:

#### Scenario 1: No AJAX Request
```
[Log] DataTable function is available, initializing...
[Log] [DATATABLES] AJAX beforeSend triggered  <-- Missing
```
**Cause**: Request not being sent
**Next Step**: Check DataTables initialization code

#### Scenario 2: AJAX Error
```
[Log] [DATATABLES] AJAX Error Details: {status: 500, ...}
[Log] [DATATABLES] Response Text: "SQLSTATE[...]"
```
**Cause**: Server-side error in controller
**Next Step**: Check Laravel logs

#### Scenario 3: Invalid Response Format
```
[Log] [DATATABLES] Invalid response format - no data array
```
**Cause**: Controller not returning proper DataTables format
**Next Step**: Check OnboardingController::fetchData() method

#### Scenario 4: Empty Response
```
[Log] [DATATABLES] Response contains 0 records
```
**Cause**: No data in database for current user/company
**Next Step**: Check database and user permissions

## Network Tab Checks

Open Network tab (F12 → Network) and look for:

1. **Request to `fetch-data`**:
   - Status: Should be 200 (not 403, 500, etc.)
   - Method: GET
   - Response: Should be JSON with `{draw, recordsTotal, recordsFiltered, data: [...]}`

2. **Response Preview**:
   - Click on the `fetch-data` request
   - Go to "Preview" tab
   - Should show JSON with array of driver objects

3. **Response Headers**:
   - Content-Type: application/json
   - No CORS errors

## Common Issues and Solutions

### Issue 1: "Loading driver applications..." Forever
**Logs show**: beforeSend triggered, but no response
**Fix**: Check if request is actually being sent in Network tab
**Possible causes**:
- Middleware blocking the request
- CSRF token issue
- Route not found

### Issue 2: AJAX Error 500
**Logs show**: `[DATATABLES] AJAX Error Details: {status: 500}`
**Fix**: Check Laravel logs at `framework/storage/logs/laravel.log`
**Possible causes**:
- Database query error
- Model relationship issue
- Permission denied

### Issue 3: No Records Displayed
**Logs show**: "Response contains 0 records"
**Fix**: Check if the user has access to onboarding data
**Possible causes**:
- Company filtering too strict
- No records in `onboarding_drivers` table
- User doesn't have permission

## Next Steps

1. Deploy and wait 2-3 minutes
2. Refresh the page (Ctrl+F5)
3. Open browser console (F12)
4. Copy ALL logs that start with `[DATATABLES]`
5. Open Network tab and check the `fetch-data` request:
   - Status code
   - Response preview
   - Request headers
6. Share the logs so we can diagnose the exact issue

