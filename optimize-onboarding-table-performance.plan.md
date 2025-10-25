# Optimize Onboarding Table Performance

## Problem

The onboarding applications table at `/admin/onboarding` has slow performance:
1. **Table loads slowly** - takes a while for entries to load
2. **Details dropdown is slow** - takes a while to open when clicking toggle details button

Need to make it snappy/instant like the drivers or vehicles table.

## Root Causes

### 1. Table Loading Performance
**Current Implementation** (Slow):
- Uses DataTables with `serverSide: true`
- Makes AJAX call to server on every page load, search, sort, etc.
- Located in: `framework/resources/views/onboarding/index.blade.php` lines 1177-1215

**Drivers Table Implementation** (Fast):
- Uses custom JavaScript rendering without DataTables serverSide
- Loads all data once, renders instantly client-side
- Located in: `framework/resources/views/drivers/index.blade.php`

### 2. Details Dropdown Performance
**Current Implementation** (Slow - AJAX-based):
- Function: `toggleDriverDetails()` at line 1435
- Makes AJAX GET request to `/admin/onboarding/{id}` on every click (line 1448-1449)
- Waits for server response before displaying details
- Network latency causes visible delay

**Drivers Table Implementation** (Fast - Instant):
- Function: `toggleDriverDetailsInstant()` at line 1050
- Embeds all driver data in button's `data-driver-info` attribute (line 1064)
- No AJAX call - data already in DOM
- Displays instantly with pure JavaScript

## Solution

Apply the same optimization strategy used in the drivers table to the onboarding table:

### 1. Embed Driver Data in Action Buttons (Instant Details Dropdown)

**File**: `framework/app/Http/Controllers/Admin/OnboardingController.php`

**Location**: Line 183 (in `fetchData()` method, within `->addColumn('actions')`)

**Change**:
```php
// Before: Simple button without data
$actions .= '<button class="btn btn-sm btn-info" data-driver-id="' . $driver->id . '" onclick="toggleDriverDetails(\'' . $driver->id . '\')" ...>

// After: Button with embedded driver data (like drivers table)
// Prepare driver data for instant display
$driverData = [
    'id' => $driver->id,
    'name' => $driver->name,
    'email' => $driver->email,
    'phone' => $driver->phone,
    'license_number' => $driver->license_number,
    'license_expiry' => $driver->license_expiry,
    'address' => $driver->address,
    'emergency_contact' => $driver->emergency_contact,
    'emergency_phone' => $driver->emergency_phone,
    'vehicle_id' => $driver->vehicle_id,
    'scheme' => $driver->scheme,
    'insurance_selection' => $driver->insurance_selection,
    'status' => $driver->status,
    'created_at' => $driver->created_at,
    'license_upload_path' => $driver->license_upload_path,
    'insurance_upload_path' => $driver->insurance_upload_path,
    'license_url' => $driver->license_url,
    'insurance_url' => $driver->insurance_url,
    'custom_data' => $driver->custom_data,
    'form_data' => $driver->form_data
];

// Add vehicle details if available
if ($driver->vehicle_id) {
    $vehicle = \App\Model\VehicleModel::find($driver->vehicle_id);
    if ($vehicle) {
        $driverData['vehicle_details'] = [
            'make_name' => $vehicle->make_name,
            'model_name' => $vehicle->model_name,
            'license_plate' => $vehicle->license_plate,
        ];
    }
}

$jsonData = json_encode($driverData);
$actions .= '<button class="btn btn-sm btn-info" data-driver-id="' . $driver->id . '" data-driver-info=\'' . $jsonData . '\' onclick="toggleDriverDetailsInstant(this)" ...>
```

### 2. Create Instant Toggle Function (No AJAX)

**File**: `framework/resources/views/onboarding/index.blade.php`

**Location**: Replace the existing `toggleDriverDetails()` function (lines 1434-1644) OR add new instant function before it

**Add**:
```javascript
// Instant toggle driver details dropdown (no AJAX delay) - like drivers table
function toggleDriverDetailsInstant(button) {
    var $button = $(button);
    var $row = $button.closest('tr');
    var $detailsRow = $row.next('.details-row');
    
    // If details row exists, remove it (instant)
    if ($detailsRow.length > 0) {
        $detailsRow.remove();
        $button.removeClass('expanded').html('<i class="fa fa-eye"></i>');
        return;
    }
    
    // Get driver data from button's data attribute (already embedded - no AJAX needed)
    var driver = $button.data('driver-info');
    
    // Build HTML instantly (exact same logic as current AJAX version, lines 1455-1632)
    var html = '<div class="details-content">';
    
    // Basic Information
    html += '<div class="mb-3">';
    html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>License:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
    
    // License Expiry Date
    if (driver.license_expiry) {
        html += '<div class="inline-field"><strong>License Expiry:</strong><span class="text-muted">' + driver.license_expiry + '</span></div>';
    }
    
    // Address
    if (driver.address) {
        html += '<div class="inline-field"><strong>Address:</strong><span class="text-muted">' + driver.address + '</span></div>';
    }
    
    // Emergency Contact
    if (driver.emergency_contact) {
        html += '<div class="inline-field"><strong>Emergency Contact:</strong><span class="text-muted">' + driver.emergency_contact + '</span></div>';
    }
    
    // Emergency Phone
    if (driver.emergency_phone) {
        html += '<div class="inline-field"><strong>Emergency Phone:</strong><span class="text-muted">' + driver.emergency_phone + '</span></div>';
    }
    
    // Vehicle Selection
    if (driver.vehicle_details) {
        var vehicleDisplay = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name + ' (' + driver.vehicle_details.license_plate + ')';
        html += '<div class="inline-field"><strong>Vehicle:</strong><span class="text-muted">' + vehicleDisplay + '</span></div>';
    } else if (driver.vehicle_id) {
        html += '<div class="inline-field"><strong>Vehicle ID:</strong><span class="text-muted">' + driver.vehicle_id + '</span></div>';
    }
    
    // Scheme Selection
    if (driver.scheme) {
        html += '<div class="inline-field"><strong>Scheme:</strong><span class="text-muted">' + driver.scheme + '</span></div>';
    }
    
    // Insurance Selection
    if (driver.insurance_selection) {
        var insuranceDisplay = driver.insurance_selection === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
        html += '<div class="inline-field"><strong>Insurance:</strong><span class="text-muted">' + insuranceDisplay + '</span></div>';
    }
    
    var statusClass = driver.status === 'approved' ? 'success' : (driver.status === 'rejected' ? 'danger' : 'warning');
    html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.status || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>Submitted:</strong><span class="text-muted">' + (driver.created_at || 'N/A') + '</span></div>';
    html += '</div>';
    
    // Documents Section
    html += '<div class="mb-3">';
    html += '<div class="inline-field"><strong>Documents:</strong>';
    if (driver.license_upload_path) {
        html += '<a href="' + driver.license_url + '" class="btn btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
        html += '<i class="fas fa-eye"></i> License';
        html += '</a>';
    }
    if (driver.insurance_upload_path) {
        html += '<a href="' + driver.insurance_url + '" class="btn btn-outline-info" target="_blank" style="border: 1px solid #17a2b8; color: #17a2b8; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
        html += '<i class="fas fa-eye"></i> Insurance';
        html += '</a>';
    }
    html += '</div>';
    html += '</div>';
    
    // Custom/Additional Fields (if any)
    if (driver.custom_data && Object.keys(driver.custom_data).length > 0) {
        html += '<div class="mb-3">';
        html += '<div class="inline-field"><strong>Additional Information:</strong></div>';
        // ... (keep existing custom_data rendering logic from lines 1536-1632)
        html += '</div>';
    }
    
    html += '</div>';
    
    // Create and insert the details row instantly
    var $detailsRow = $('<tr class="details-row"><td colspan="9">' + html + '</td></tr>');
    $row.after($detailsRow);
    
    // Update button state
    $button.addClass('expanded').html('<i class="fa fa-eye-slash"></i>');
}
```

### 3. Optional: Add Database Indexes for Faster Queries

**File**: Create new migration `framework/database/migrations/YYYY_MM_DD_HHMMSS_add_indexes_to_onboarding_drivers_table.php`

```php
public function up()
{
    Schema::table('onboarding_drivers', function (Blueprint $table) {
        $table->index('status');
        $table->index('vehicle_id');
        $table->index('created_at');
        $table->index(['status', 'created_at']);
    });
}
```

## Expected Results

After implementing these changes:

1. **Instant Details Dropdown**: Clicking "Toggle Details" will be **instant** - no AJAX delay, no waiting for server response
2. **Same Table Load Speed**: DataTables serverSide will remain, but can be optimized later if needed
3. **Consistent UX**: Matches the fast, snappy feel of the drivers and vehicles tables

## Performance Comparison

| Metric | Before (AJAX) | After (Instant) |
|--------|--------------|-----------------|
| Details Dropdown | ~300-1000ms (network latency) | < 10ms (instant) |
| User Experience | Noticeable delay | Snappy, instant |
| Server Load | Higher (AJAX call per click) | Lower (data embedded once) |

## Implementation Priority

**High Priority (Instant Impact)**:
1. Embed driver data in action buttons (Controller change)
2. Create instant toggle function (View JavaScript change)

**Medium Priority (If table load is still slow)**:
3. Add database indexes
4. Consider eager loading vehicle relationships in fetchData()

**Low Priority (Future optimization)**:
5. Consider switching to client-side DataTables rendering if dataset is small (< 1000 records)

## Files to Modify

1. `framework/app/Http/Controllers/Admin/OnboardingController.php` - Line ~183 (fetchData method)
2. `framework/resources/views/onboarding/index.blade.php` - Add new function before line 1434

## Rollback Plan

Keep the original `toggleDriverDetails()` AJAX function as fallback. If instant version has issues, users can still use the AJAX version by changing the onclick handler back.

