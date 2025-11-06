@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header" style="margin-top: 0; padding-top: 0;">
        <div class="header-card" style="background: linear-gradient(135deg, #7ED6E0 0%, #6CC5D2 100%); border-radius: 15px; padding: 30px; margin-bottom: 0;">
            <div class="header-content d-flex justify-content-between align-items-center">
                <div>
                    <h1 style="color: white; font-size: 33px; font-weight: 700; margin: 0; line-height: 33px;">Fines Management</h1>
                    <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin: 5px 0 0 0;">Manage your fleet fines and penalties with ease.</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('fines.create') }}" class="btn" style="background: #C1C1C1; color: #151515; border: none; border-radius: 8px; padding: 12px 24px; font-weight: 600;">
                        <i class="fas fa-plus me-2"></i>Add Fine
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body" style="padding: 0;">
                    <!-- Filters Section -->
                    <div class="card" style="border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                        <div class="card-body" style="padding: 20px;">
                            <div class="d-flex align-items-center mb-3" style="background-color: #F8F9FA; padding: 12px 16px; border-radius: 8px;">
                                <i class="fas fa-filter me-2" style="color: #6c757d;"></i>
                                <h5 class="mb-0" style="font-weight: 600; color: #333;">Filter Fines</h5>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="d-flex flex-column">
                                        <label for="status-filter" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">Status</label>
                                        <select id="status-filter" class="form-select" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; height: 45px;">
                                            <option value="">All Statuses</option>
                                            <option value="pending">Pending</option>
                                            <option value="notified">Notified</option>
                                            <option value="disputed">Disputed</option>
                                            <option value="paid">Paid</option>
                                            <option value="escalated">Escalated</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="fine-type-filter" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">Fine Type</label>
                                    <select id="fine-type-filter" class="form-select" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; height: 45px;">
                                        <option value="">All Fine Types</option>
                                        <option value="01">Parked in restricted street</option>
                                        <option value="02">Parked/loading in restricted street</option>
                                        <option value="03">Parked after expiry</option>
                                        <option value="04">Parked without payment</option>
                                        <option value="05">Parked in loading place</option>
                                        <option value="06">Parked in suspended bay</option>
                                        <option value="07">Re-parked within one hour</option>
                                        <option value="08">Parked in wrong place</option>
                                        <option value="09">Parked with engine running</option>
                                        <option value="10">Parked without permit</option>
                                        <option value="11">Parked with invalid permit</option>
                                        <option value="12">Parked without payment (permit)</option>
                                        <option value="16">Parked in permit space</option>
                                        <option value="SP10">Exceeding speed limit (motorway)</option>
                                        <option value="SP20">Exceeding speed limit (road)</option>
                                        <option value="SP30">Exceeding statutory speed limit</option>
                                        <option value="SP40">Exceeding speed limit (road)</option>
                                        <option value="SP50">Exceeding speed limit (motorway)</option>
                                        <option value="CU80">Using mobile phone while driving</option>
                                        <option value="BUS_LANE">Bus lane contravention</option>
                                        <option value="RED_ROUTE">Red route contravention</option>
                                        <option value="YELLOW_BOX">Yellow box junction contravention</option>
                                        <option value="CYCLE_LANE">Cycle lane contravention</option>
                                        <option value="PEDESTRIAN">Pedestrian crossing contravention</option>
                                        <option value="NO_ENTRY">No entry contravention</option>
                                        <option value="ONE_WAY">One way street contravention</option>
                                        <option value="UTURN">U-turn contravention</option>
                                        <option value="NO_TURNING">No turning contravention</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="vehicle-filter" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">Vehicle</label>
                                    <input type="text" id="vehicle-filter" class="form-control" placeholder="Vehicle reg..." style="border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; height: 45px;">
                                </div>
                                <div class="col-md-2">
                                    <label for="date-from-filter" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">Date From</label>
                                    <input type="date" id="date-from-filter" class="form-control" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; height: 45px;">
                                </div>
                                <div class="col-md-2">
                                    <label for="date-to-filter" class="form-label" style="font-weight: 600; color: #333; margin-bottom: 8px;">Date To</label>
                                    <input type="date" id="date-to-filter" class="form-control" style="border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; height: 45px;">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" id="clear-filters" class="btn" style="background: #6c757d; color: white; border: none; border-radius: 8px; padding: 12px 16px; font-weight: 600; height: 50px; width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-size: 13px; display: flex; align-items: center; justify-content: center; white-space: nowrap;">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                </div>
                    
                    <table id="fines-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fine Type</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<style>
/* Reduce top spacing above header card by 50% */
.content-wrapper {
    padding-top: calc(0.5rem + 90px) !important;
}

/* Custom dropdown styling for fines table */
.status-container {
    position: relative;
}

.custom-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 10000 !important;
    min-width: 120px !important;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 8px 0;
}

.custom-dropdown-menu .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #333;
    text-decoration: none;
}

.custom-dropdown-menu .dropdown-item {
    padding: 0.5rem 1rem;
}

/* Button styling */
.btn-outline-warning, .btn-outline-info, .btn-outline-danger, .btn-outline-success, .btn-outline-dark, .btn-outline-secondary {
    border: none !important;
    background: transparent !important;
    padding: 0 !important;
}

.dropdown-arrow {
    color: #000 !important;
    font-size: 10px;
    margin-left: 4px;
}

.custom-dropdown-toggle {
    cursor: pointer;
}

/* Status column dimensions - more specific selectors */
#fines-table th:nth-child(6),
#fines-table td:nth-child(6),
#fines-table thead th:nth-child(6),
#fines-table tbody td:nth-child(6) {
    width: 124px !important;
    min-width: 124px !important;
    max-width: 124px !important;
    height: 50px !important;
    min-height: 50px !important;
    max-height: 50px !important;
    box-sizing: border-box !important;
}

/* Override DataTables column width */
#fines-table .status-column {
    width: 124px !important;
    min-width: 124px !important;
    max-width: 124px !important;
}

/* Force status column width with highest specificity */
table#fines-table th:nth-child(6),
table#fines-table td:nth-child(6) {
    width: 124px !important;
    min-width: 124px !important;
    max-width: 124px !important;
}

/* Status badge dimensions */
.status-container .badge {
    height: 19px !important;
    min-height: 19px !important;
    max-height: 19px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 10px !important;
    padding: 2px 6px !important;
    line-height: 1 !important;
    border-radius: 3px !important;
}

/* Center status container content horizontally */
.status-container {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    width: 100% !important;
    height: 100% !important;
}

.status-display {
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
}

/* Table header styling to match vehicles table */
#fines-table thead th {
    color: #333333 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    background-color: #f8f9fa !important;
    border-bottom: 2px solid #dee2e6 !important;
    padding: 12px 8px !important;
}

/* Remove clear button from date inputs */
input[type="date"]::-webkit-clear-button {
    display: none !important;
    -webkit-appearance: none !important;
}

input[type="date"]::-webkit-inner-spin-button {
    display: none !important;
    -webkit-appearance: none !important;
}

/* Fine details row styling */
.fine-details-row {
    background-color: #f8f9fa !important;
}

.fine-details-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin: 10px 0;
}

.fine-details-section {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.fine-details-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.fine-details-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.fine-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.fine-details-item {
    display: flex;
    flex-direction: column;
}

.fine-details-label {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 2px;
    font-weight: 500;
}

.fine-details-value {
    font-size: 13px;
    color: #333;
    font-weight: 500;
}

.fine-details-amount {
    text-align: center;
    background: linear-gradient(135deg, #7ED6E0 0%, #6CC5D2 100%);
    color: white;
    padding: 15px;
    border-radius: 6px;
    margin: 10px 0;
}

.fine-details-amount-label {
    font-size: 12px;
    opacity: 0.9;
    margin-bottom: 5px;
}

.fine-details-amount-value {
    font-size: 20px;
    font-weight: 700;
}

.loading-spinner {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 20px;
}
</style>

<script>
$(document).ready(function() {
    console.log('Starting fines table initialization...');
    
    // Initialize DataTable with performance optimizations
    var table = $('#fines-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        deferRender: true, // Defer rendering for better performance with large datasets
        pagingType: 'simple_numbers',
        pageLength: 10, // Reduced from 25 for faster initial load
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: "{{ route('fines.fetch') }}",
            type: 'GET',
            timeout: 30000, // 30 second timeout
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.status = $('#status-filter').val();
                d.fine_type = $('#fine-type-filter').val();
                d.vehicle = $('#vehicle-filter').val();
                d.date_from = $('#date-from-filter').val();
                d.date_to = $('#date-to-filter').val();
            },
            beforeSend: function(xhr, settings) {
                // Track AJAX request start
                if (window.trackAction) {
                    window.trackAction('datatable-ajax', settings.url);
                }
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', error, thrown);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                // Show user-friendly error message
                if ($('#dataTableError').length === 0) {
                    $('#fines-table').after('<div id="dataTableError" class="alert alert-danger">Failed to load fines data. Please refresh the page.</div>');
                }
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'fine_type_title', name: 'fine_type_title', orderable: false },
            { data: 'vehicle_info', name: 'vehicle_info', orderable: false },
            { data: 'driver_info', name: 'driver_info', orderable: false },
            { data: 'current_amount', name: 'current_amount', orderable: false },
            { data: 'status_badge', name: 'status', orderable: false, width: '124px' },
            { data: 'date_logged', name: 'date_logged' },
            { data: 'due_date_formatted', name: 'due_date', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        responsive: true,
        dom: 'rtip', // Remove length menu and search box
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading fines...',
            emptyTable: "No fines found",
            zeroRecords: "No matching fines found"
        }
    });
    
    console.log('DataTable initialized successfully');
    
    // Filter functionality (auto-apply on change for better UX)
    $('#status-filter, #fine-type-filter, #vehicle-filter, #date-from-filter, #date-to-filter').on('change keyup', function() {
        table.draw();
    });
    
    // Clear filters functionality
    $('#clear-filters').on('click', function() {
        $('#status-filter').val('');
        $('#fine-type-filter').val('');
        $('#vehicle-filter').val('');
        $('#date-from-filter').val('');
        $('#date-to-filter').val('');
        table.draw();
    });
    
    // Handle custom dropdown functionality
    $(document).on('click', '.custom-dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const container = $(this).closest('.status-container');
        const dropdown = container.find('.custom-dropdown-menu');
        
        // Hide all other dropdowns
        $('.custom-dropdown-menu').not(dropdown).hide();
        
        // Toggle current dropdown
        dropdown.toggle();
        
        console.log('Dropdown toggled for fine:', container.data('fine-id'));
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.status-container').length) {
            $('.custom-dropdown-menu').hide();
        }
    });
    
    // Handle status change
    $(document).on('click', '.status-change', function(e) {
        e.preventDefault();
        const fineId = $(this).data('fine-id');
        const newStatus = $(this).data('status');
        const container = $(this).closest('.status-container');
        const display = container.find('.status-display');
        const dropdown = container.find('.custom-dropdown-menu');
        
        console.log('Updating fine status:', { fineId, newStatus });
        
        // Update status via AJAX
        $.ajax({
            url: '/admin/fines/' + fineId + '/update-status',
            method: 'POST',
            data: {
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Status update response:', response);
                if (response.success) {
                    // Update the display button with new status
                    const buttonClass = getButtonClassForFineStatus(newStatus);
                    const badgeClass = getBadgeClassForFineStatus(newStatus);
                    display.html(`<button class="btn btn-sm ${buttonClass} custom-dropdown-toggle" type="button"><span class="badge ${badgeClass}">${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}</span> <span class="dropdown-arrow">▼</span></button>`);
                    
                    // Hide dropdown
                    dropdown.hide();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Fine status updated successfully!');
                    } else {
                        alert('Fine status updated successfully!');
                    }
                } else {
                    console.error('Status update failed:', response.message);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to update status: ' + (response.message || 'Unknown error'));
                    } else {
                        alert('Failed to update status: ' + (response.message || 'Unknown error'));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', { xhr, status, error, responseText: xhr.responseText });
                let errorMessage = 'Failed to update status. Please try again.';
                
                // Try to extract error message from response
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                }
                
                // Log full error details for debugging
                if (xhr.status === 403) {
                    errorMessage = 'You do not have permission to update fine status.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Fine not found.';
                } else if (xhr.status === 500) {
                    errorMessage = errorMessage || 'Server error occurred. Please try again.';
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    });
});

// Global function for fine details toggle
window.toggleFineDetails = function(id) {
    console.log('Toggle details called for fine ID:', id);
    
    const row = document.querySelector(`#fine-row-${id}`);
    const detailsBtn = document.getElementById(`details-btn-${id}`);
    
    console.log('Found row:', row);
    console.log('Found button:', detailsBtn);
    
    if (!row || !detailsBtn) {
        console.error('Row or button not found for fine ID:', id);
        alert('Error: Cannot find fine row or button');
        return;
    }
    
    // Check if details are currently open by looking for existing details row
    let existingDetails = null;
    const nextRow = row.nextElementSibling;
    if (nextRow && nextRow.classList.contains('fine-details-row')) {
        existingDetails = nextRow;
    }
    
    console.log('Existing details found:', existingDetails);
    
    if (existingDetails) {
        // Close details
        console.log('Closing details');
        existingDetails.remove();
        row.classList.remove('details-expanded');
        detailsBtn.innerHTML = '<i class="fas fa-eye"></i>';
        detailsBtn.className = 'btn btn-sm';
        detailsBtn.style.cssText = 'background: #14A2B8; color: white; border: none; border-radius: 4px;';
        detailsBtn.title = 'View Details';
        return;
    }
    
    // Open details
    console.log('Opening details for fine ID:', id);
    row.classList.add('details-expanded');
    detailsBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    detailsBtn.className = 'btn btn-sm';
    detailsBtn.style.cssText = 'background: #6CC5D2; color: white; border: none; border-radius: 4px;';
    detailsBtn.title = 'Hide Details';
    
    // Create details row
    const detailsRow = document.createElement('tr');
    detailsRow.id = `fine-details-${id}`;
    detailsRow.className = 'fine-details-row';
    detailsRow.style.backgroundColor = '#f8f9fa';
    
    const detailsCell = document.createElement('td');
    detailsCell.setAttribute('colspan', '9');
    detailsCell.style.padding = '20px';
    
    // Start with a loading indicator
    detailsCell.innerHTML = `
        <div class="fine-details-content">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i> Loading Fine Details...
            </div>
        </div>
    `;
    
    // Fetch fine details via AJAX
    fetchFineDetails(id).then(fineData => {
        console.log('Fine details received:', fineData);
        detailsCell.innerHTML = generateFineDetailsHTML(fineData);
    }).catch(error => {
        console.error('Error fetching fine details:', error);
        detailsCell.innerHTML = `
            <div class="fine-details-content">
                <div class="text-danger">Error loading fine details. Please try again.</div>
            </div>
        `;
    });
    
    detailsRow.appendChild(detailsCell);
    
    console.log('Created details row:', detailsRow);
    
    // Insert the details row directly after the current fine row
    row.parentNode.insertBefore(detailsRow, row.nextSibling);
    
    console.log('Details row inserted successfully');
};

// Function to fetch fine details via AJAX
function fetchFineDetails(fineId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/admin/fines/' + fineId + '/details',
            method: 'GET',
            success: function(data) {
                resolve(data);
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

// Function to generate fine details HTML
function generateFineDetailsHTML(fine) {
    let html = '<div class="fine-details-content">';
    
    // Basic Information Section
    html += '<div class="fine-details-section">';
    html += '<div class="fine-details-title">Basic Information</div>';
    html += '<div class="fine-details-grid">';
    html += '<div class="fine-details-item"><div class="fine-details-label">Fine ID</div><div class="fine-details-value">#' + fine.id + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Fine Type</div><div class="fine-details-value">' + fine.fine_type_title + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Contravention Code</div><div class="fine-details-value">' + fine.contravention_code + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Reference Number</div><div class="fine-details-value">' + fine.reference_number + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Vehicle Registration</div><div class="fine-details-value">' + fine.vehicle_reg + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Vehicle Details</div><div class="fine-details-value">' + fine.vehicle_details + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Assigned Driver</div><div class="fine-details-value">' + fine.driver_details + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Date Logged</div><div class="fine-details-value">' + fine.date_logged + '</div></div>';
    html += '</div>';
    html += '</div>';
    
    // Amount Information Section
    html += '<div class="fine-details-section">';
    html += '<div class="fine-details-title">Amount Information</div>';
    html += '<div class="fine-details-amount">';
    html += '<div class="fine-details-amount-label">Current Amount Due</div>';
    html += '<div class="fine-details-amount-value">' + fine.current_amount + '</div>';
    if (fine.is_escalated) {
        html += '<div style="font-size: 11px; opacity: 0.9; margin-top: 5px;">ESCALATED</div>';
    } else if (fine.is_in_discount_window) {
        html += '<div style="font-size: 11px; opacity: 0.9; margin-top: 5px;">DISCOUNT APPLIED</div>';
    }
    html += '</div>';
    html += '<div class="fine-details-grid">';
    html += '<div class="fine-details-item"><div class="fine-details-label">Original Fine</div><div class="fine-details-value">' + fine.price + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Admin Fee</div><div class="fine-details-value">' + fine.admin_fee + '</div></div>';
    html += '<div class="fine-details-item"><div class="fine-details-label">Total Amount</div><div class="fine-details-value">' + fine.total_amount + '</div></div>';
    if (fine.discount_amount !== 'N/A') {
        html += '<div class="fine-details-item"><div class="fine-details-label">Discount Amount</div><div class="fine-details-value">' + fine.discount_amount + '</div></div>';
    }
    if (fine.escalation_multiplier > 1) {
        html += '<div class="fine-details-item"><div class="fine-details-label">Escalation Multiplier</div><div class="fine-details-value">' + fine.escalation_multiplier + 'x</div></div>';
    }
    html += '</div>';
    html += '</div>';
    
    // Timeline Section
    html += '<div class="fine-details-section">';
    html += '<div class="fine-details-title">Timeline</div>';
    html += '<div class="fine-details-grid">';
    html += '<div class="fine-details-item"><div class="fine-details-label">Due Date</div><div class="fine-details-value">' + fine.due_date + (fine.is_overdue ? ' <span style="color: #dc3545;">(OVERDUE)</span>' : '') + '</div></div>';
    if (fine.escalation_date !== 'N/A') {
        html += '<div class="fine-details-item"><div class="fine-details-label">Escalation Date</div><div class="fine-details-value">' + fine.escalation_date + (fine.is_escalated ? ' <span style="color: #dc3545;">(ESCALATED)</span>' : '') + '</div></div>';
    }
    html += '</div>';
    html += '</div>';
    
    // Notes Section
    if (fine.notes && fine.notes !== 'No notes available') {
        html += '<div class="fine-details-section">';
        html += '<div class="fine-details-title">Notes</div>';
        html += '<div class="fine-details-value" style="font-style: italic; color: #6c757d; line-height: 1.6;">' + fine.notes + '</div>';
        html += '</div>';
    }
    
    // Action buttons
    html += '<div class="fine-details-section">';
    html += '<div class="d-flex justify-content-between align-items-center">';
    html += '<div>';
    html += '<a href="/admin/fines/' + fine.id + '/edit" class="btn btn-warning" style="margin-right: 10px; padding: 10px 20px;">';
    html += '<i class="fas fa-edit"></i> Edit Fine';
    html += '</a>';
    html += '<a href="/admin/fines/' + fine.id + '" class="btn btn-info" style="margin-right: 10px; padding: 10px 20px;">';
    html += '<i class="fas fa-eye"></i> View Full Details';
    html += '</a>';
    html += '</div>';
    html += '<button class="btn btn-secondary" onclick="toggleFineDetails(' + fine.id + ')" style="padding: 10px 20px;">';
    html += '<i class="fas fa-times"></i> Hide Details';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    
    html += '</div>';
    
    return html;
}

// Helper function to get badge class for fine status
function getBadgeClassForFineStatus(status) {
    switch (status) {
        case 'pending':
            return 'badge-warning';
        case 'notified':
            return 'badge-info';
        case 'disputed':
            return 'badge-danger';
        case 'paid':
            return 'badge-success';
        case 'escalated':
            return 'badge-dark';
        default:
            return 'badge-secondary';
    }
}

// Helper function to get button class for fine status
function getButtonClassForFineStatus(status) {
    switch (status) {
        case 'pending':
            return 'btn-outline-warning';
        case 'notified':
            return 'btn-outline-info';
        case 'disputed':
            return 'btn-outline-danger';
        case 'paid':
            return 'btn-outline-success';
        case 'escalated':
            return 'btn-outline-dark';
        default:
            return 'btn-outline-secondary';
    }
}

// Delete fine function
function deleteFine(id) {
    if (confirm('Are you sure you want to delete this fine?')) {
        $.ajax({
            url: '/admin/fines/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#fines-table').DataTable().ajax.reload();
                alert('Fine deleted successfully');
            },
            error: function(xhr) {
                alert('Error deleting fine: ' + xhr.responseText);
            }
        });
    }
}
</script>
@endsection