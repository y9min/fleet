@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.vehicle_inspection')</li>
@endsection
@section('extra_css')
<style type="text/css">
  .checkbox, #chk_all{
    width: 20px;
    height: 20px;
  }
  .table-warning {
    background-color: #ffffff !important;
  }
  .table tbody tr.table-warning {
    background-color: #ffffff !important;
  }
  .table tbody tr.table-warning td {
    background-color: #ffffff !important;
  }
  .table tbody tr {
    background-color: #ffffff !important;
  }
  .table tbody tr td {
    background-color: #ffffff !important;
  }
  
  /* Ensure table borders are grey instead of yellow */
  .table td, .table th {
    border-color: #DEE2E6 !important;
  }
  
  .table tbody tr td {
    border-top-color: #DEE2E6 !important;
    border-bottom-color: #DEE2E6 !important;
  }
  
  /* Ensure all status badges are exactly 19px tall */
  .badge {
    height: 19px !important;
    line-height: 19px !important;
    padding: 0 8px !important;
    font-size: 11px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  /* EXACT COPY FROM WORKING VEHICLES TABLE */
  .custom-dropdown-toggle {
    cursor: pointer;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
  }
  
  .custom-dropdown-toggle:hover,
  .custom-dropdown-toggle:focus,
  .custom-dropdown-toggle:active {
    box-shadow: none !important;
    outline: none !important;
    background-color: transparent !important;
    color: inherit !important;
    border-color: transparent !important;
  }
  
  .custom-dropdown-toggle:hover .badge,
  .custom-dropdown-toggle:focus .badge,
  .custom-dropdown-toggle:active .badge {
    background-color: inherit !important;
    color: inherit !important;
  }
  
  .custom-dropdown-toggle:hover .dropdown-arrow,
  .custom-dropdown-toggle:focus .dropdown-arrow,
  .custom-dropdown-toggle:active .dropdown-arrow {
    color: inherit !important;
  }

  /* Enhanced dropdown styling */
  .dropdown-menu {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 8px 0;
  }
  .workshop-badge {
    font-size: 0.8em;
    padding: 0.25em 0.5em;
  }
  .mot-expiry-badge {
    font-size: 0.8em;
    padding: 0.25em 0.5em;
  }
  .mot-table {
    margin-top: 20px;
  }
  
  /* Custom styling for MOT table header to match inspection table */
  .mot-table .card-header {
    background-color: #14A2B8 !important;
    color: white !important;
  }
  
  .mot-table .card-title {
    color: white !important;
  }
  .mot-row {
    background-color: #ffffff !important;
  }
  .mot-row-urgent {
    background-color: #ffffff !important;
  }
  .status-btn {
    border: none !important;
    padding: 0 !important;
    background: none !important;
    cursor: pointer !important;
  }
  
  .status-btn:focus {
    box-shadow: none !important;
    outline: none !important;
  }
  
  .status-btn:hover {
    background: none !important;
  }
  
  .status-btn .badge {
    transition: all 0.2s ease;
  }
  
  .status-btn:hover .badge {
    opacity: 0.8;
    transform: scale(1.05);
  }
  .status-dropdown .dropdown-item {
    padding: 0.5rem 1rem;
  }
  .notes-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .notes-text {
    flex: 1;
    font-size: 0.9rem;
    color: #6c757d;
  }
  .edit-notes {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
  .notes-input {
    width: 100%;
    padding: 0.25rem 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    font-size: 0.9rem;
  }
  
  /* Custom extra small button size */
  .btn-xs {
    padding: 0.125rem 0.375rem;
    font-size: 0.75rem;
    line-height: 1.25;
    border-radius: 0.2rem;
  }
  
  /* Inline notes editing container */
  .notes-container.editing {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .notes-container.editing .notes-input {
    flex: 1;
    margin: 0;
  }
  
  /* Custom dropdown styles - matching vehicles table */
  .status-container {
    position: relative;
    display: inline-block;
  }
  
  .custom-dropdown-toggle .badge {
    margin-right: 5px;
  }
  
  .dropdown-arrow {
    font-size: 10px;
    color: #6c757d;
  }
  
  .custom-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 9999 !important;
    min-width: 120px;
    background: white !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    padding: 0.5rem 0;
    display: none;
  }
  
  .custom-dropdown-menu.show {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
  }
  
  .custom-dropdown-menu .dropdown-item {
    display: block;
    width: 100%;
    padding: 0.5rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    cursor: pointer;
  }
  
  .custom-dropdown-menu .dropdown-item:hover {
    color: #16181b;
    background-color: #f8f9fa;
  }
  
  .custom-dropdown-menu .dropdown-item:focus {
    color: #16181b;
    background-color: #f8f9fa;
    outline: 0;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
        @lang('fleet.vehicle_inspection')
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table" style="padding-bottom: 25px">
          <thead class="thead-inverse">
            <tr>
              <th>Registration Plate</th>
              <th>@lang('fleet.vehicle')</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            {{-- @dd($reviews->vehicle) --}}
          @foreach($reviews as $r)
            <tr @if(isset($r->is_workshop_vehicle) && $r->is_workshop_vehicle) class="table-warning" @endif>
              <td>{{$r->reg_no}}</td>
              <td>
                {{$r->vehicle->make_name}} - {{$r->vehicle->model_name}}
                @if(isset($r->is_workshop_vehicle) && $r->is_workshop_vehicle)
                @endif
              </td>
              <td>
                @php
                  $vehicleStatus = $r->vehicle->getMeta('vehicle_status') ?: 'Available';
                @endphp
                <div class="status-container" data-vehicle-id="{{ $r->vehicle_id }}">
                  <div class="status-display">
                    @switch($vehicleStatus)
                      @case('Available')
                        <button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button">
                          <span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Rented')
                        <button class="btn btn-sm btn-outline-warning custom-dropdown-toggle" type="button">
                          <span class="badge badge-warning">Rented</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Workshop')
                        <button class="btn btn-sm btn-outline-info custom-dropdown-toggle" type="button">
                          <span class="badge badge-info">Workshop</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Disabled')
                        <button class="btn btn-sm btn-outline-secondary custom-dropdown-toggle" type="button">
                          <span class="badge badge-secondary">Disabled</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @default
                        <button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button">
                          <span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span>
                        </button>
                    @endswitch
                  </div>
                  <div class="custom-dropdown-menu" style="display: none;">
                    <a class="dropdown-item status-change" href="#" data-status="Available" data-vehicle-id="{{ $r->vehicle_id }}">
                      <span class="badge badge-success">Available</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Rented" data-vehicle-id="{{ $r->vehicle_id }}">
                      <span class="badge badge-warning">Rented</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Workshop" data-vehicle-id="{{ $r->vehicle_id }}">
                      <span class="badge badge-info">Workshop</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Disabled" data-vehicle-id="{{ $r->vehicle_id }}">
                      <span class="badge badge-secondary">Disabled</span>
                    </a>
                  </div>
                </div>
              </td>
              <td>
                <div class="notes-container">
                  <span class="notes-text" data-vehicle-id="{{ $r->vehicle_id }}">
                    @php
                      $notes = $r->vehicle->getMeta('inspection_notes');
                      \Log::info('Loading notes for vehicle ' . $r->vehicle_id, [
                        'vehicle_id' => $r->vehicle_id,
                        'notes' => $notes,
                        'vehicle_meta_loaded' => $r->vehicle->relationLoaded('metas'),
                        'all_metas' => $r->vehicle->metas->pluck('value', 'key')->toArray()
                      ]);
                    @endphp
                    {{ $notes ?: 'No notes' }}
                  </span>
                  <button type="button" class="btn btn-sm btn-outline-secondary edit-notes" data-vehicle-id="{{ $r->vehicle_id }}" title="Edit Notes">
                    <i class="fa fa-edit"></i>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- MOT Expiry Table -->
@if(isset($motExpiryVehicles) && $motExpiryVehicles->count() > 0)
<div class="row mot-table">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-8">
            <h3 class="card-title">
            MOT Expiry Alerts
            <span class="badge badge-danger ml-2" id="mot-count">{{ $motExpiryVehicles->count() }}</span>
            </h3>
          </div>
          <div class="col-md-4">
            <div class="form-group mb-0">
              <select class="form-control form-control-sm" id="mot-timeframe-filter">
                <option value="1">Next 1 Month</option>
                <option value="2" selected>Next 2 Months</option>
                <option value="3">Next 3 Months</option>
                <option value="6">Next 6 Months</option>
                <option value="12">Next 12 Months</option>
                <option value="all">All Upcoming</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="mot_data_table">
          <thead class="thead-inverse">
            <tr>
              <th>Registration Plate</th>
              <th>Vehicle</th>
              <th>MOT Expiry Date</th>
              <th>Days Until Expiry</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          @foreach($motExpiryVehicles as $vehicle)
            @php
                $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: $vehicle->getMeta('exp_date') ?: $vehicle->lic_exp_date;
                $expiryDate = \Carbon\Carbon::parse($motExpiryDate);
                $daysUntilExpiry = now()->diffInDays($expiryDate, false);
                $isUrgent = $daysUntilExpiry <= 7;
                $isExpired = $daysUntilExpiry < 0;
            @endphp
            <tr class="@if($isExpired) mot-row-urgent @elseif($isUrgent) mot-row @endif">
              <td>{{ $vehicle->license_plate }}</td>
              <td>
                {{ $vehicle->make_name }} - {{ $vehicle->model_name }}
              </td>
              <td>{{ $expiryDate->format('d/m/Y') }}</td>
              <td>
                @if($isExpired)
                  <span class="badge badge-danger">Expired {{ abs($daysUntilExpiry) }} days ago</span>
                @elseif($isUrgent)
                  <span class="badge badge-warning">{{ $daysUntilExpiry }} days</span>
                @else
                  {{ $daysUntilExpiry }} days
                @endif
              </td>
              <td>
                @php
                  $vehicleStatus = $vehicle->getMeta('vehicle_status') ?: 'Available';
                @endphp
                <div class="status-container" data-vehicle-id="{{ $vehicle->id }}">
                  <div class="status-display">
                    @switch($vehicleStatus)
                      @case('Available')
                        <button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button">
                          <span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Rented')
                        <button class="btn btn-sm btn-outline-warning custom-dropdown-toggle" type="button">
                          <span class="badge badge-warning">Rented</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Workshop')
                        <button class="btn btn-sm btn-outline-info custom-dropdown-toggle" type="button">
                          <span class="badge badge-info">Workshop</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @case('Disabled')
                        <button class="btn btn-sm btn-outline-secondary custom-dropdown-toggle" type="button">
                          <span class="badge badge-secondary">Disabled</span> <span class="dropdown-arrow">▼</span>
                        </button>
                        @break
                      @default
                        <button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button">
                          <span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span>
                        </button>
                    @endswitch
                  </div>
                  <div class="custom-dropdown-menu" style="display: none;">
                    <a class="dropdown-item status-change" href="#" data-status="Available" data-vehicle-id="{{ $vehicle->id }}">
                      <span class="badge badge-success">Available</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Rented" data-vehicle-id="{{ $vehicle->id }}">
                      <span class="badge badge-warning">Rented</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Workshop" data-vehicle-id="{{ $vehicle->id }}">
                      <span class="badge badge-info">Workshop</span>
                    </a>
                    <a class="dropdown-item status-change" href="#" data-status="Disabled" data-vehicle-id="{{ $vehicle->id }}">
                      <span class="badge badge-secondary">Disabled</span>
                    </a>
                  </div>
                </div>
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="{{ url('admin/vehicles/' . $vehicle->id . '/edit') }}" class="btn btn-sm btn-primary mr-2" title="Edit Vehicle">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                  <a href="{{ url('admin/vehicles') }}" class="btn btn-sm btn-info" title="View Vehicles">
                    <i class="fa fa-eye"></i> View
                  </a>
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

<!-- Load jQuery locally as fallback -->
<script src="{{ asset('assets/js/jquery.js') }}"></script>
<script>
console.log('SCRIPT LOADING - JavaScript file is being executed');

$(document).ready(function() {
    console.log('JQUERY READY - Document ready function executing');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Found buttons:', $('.custom-dropdown-toggle').length);
    
    // Handle custom dropdown functionality - Direct binding
    $('.custom-dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('BUTTON CLICKED!');
        
        const container = $(this).closest('.status-container');
        const dropdown = container.find('.custom-dropdown-menu');
        
        console.log('Container:', container.length);
        console.log('Dropdown:', dropdown.length);
        
        // Hide all other dropdowns
        $('.custom-dropdown-menu').not(dropdown).removeClass('show').hide();
        
        // Toggle current dropdown
        dropdown.toggleClass('show').toggle();
        
        console.log('Dropdown toggled for vehicle:', container.data('vehicle-id'));
        console.log('Dropdown display after toggle:', dropdown.css('display'));
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.status-container').length) {
            $('.custom-dropdown-menu').removeClass('show').hide();
        }
    });
    
    // Handle status change - Direct binding
    $('.status-change').on('click', function(e) {
        e.preventDefault();
        const vehicleId = $(this).data('vehicle-id');
        const newStatus = $(this).data('status');
        const container = $(this).closest('.status-container');
        const display = container.find('.status-display');
        const dropdown = container.find('.custom-dropdown-menu');
        
        console.log('Updating vehicle status:', { vehicleId, newStatus });
        
        // Update status via AJAX
        $.ajax({
            url: '/admin/vehicles/update-status',
            method: 'POST',
            data: {
                vehicle_id: vehicleId,
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Status update response:', response);
                if (response.success) {
                    // Update the display button with new status
                    const buttonClass = getButtonClass(newStatus);
                    const badgeClass = getBadgeClass(newStatus);
                    display.html('<button class="btn btn-sm ' + buttonClass + ' custom-dropdown-toggle" type="button"><span class="badge ' + badgeClass + '">' + newStatus + '</span> <span class="dropdown-arrow">▼</span></button>');
                    
                    // Clear inspection notes if vehicle is no longer in workshop
                    if (newStatus !== 'Workshop') {
                        const notesContainer = container.closest('tr').find('.notes-container');
                        const notesText = notesContainer.find('.notes-text');
                        notesText.text('No notes');
                    }
                    
                    // Hide dropdown
                    dropdown.removeClass('show').hide();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Vehicle status updated successfully!');
                    } else {
                        alert('Vehicle status updated successfully!');
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
                console.error('AJAX error:', { xhr, status, error });
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update status. Please try again.');
                } else {
                    alert('Failed to update status. Please try again.');
                }
            }
        });
    });
    
    // Edit Notes functionality
    $('.edit-notes').on('click', function(e) {
        e.preventDefault();
        const vehicleId = $(this).data('vehicle-id');
        const notesContainer = $(this).closest('.notes-container');
        const notesText = notesContainer.find('.notes-text');
        const currentNotes = notesText.text().trim();
        
        console.log('Edit notes clicked for vehicle:', vehicleId, 'Current notes:', currentNotes);
        
        // Create input field
        const inputField = $('<input>', {
            type: 'text',
            class: 'notes-input',
            value: currentNotes === 'No notes' ? '' : currentNotes,
            placeholder: 'Enter inspection notes...'
        });
        
        // Create save/cancel buttons
        const buttonContainer = $('<div>', {
            class: 'notes-buttons d-inline-flex align-items-center ml-2'
        });
        
        const saveButton = $('<button>', {
            type: 'button',
            class: 'btn btn-xs btn-success mr-1',
            text: 'Save'
        });
        
        const cancelButton = $('<button>', {
            type: 'button',
            class: 'btn btn-xs btn-secondary',
            text: 'Cancel'
        });
        
        buttonContainer.append(saveButton, cancelButton);
        
        // Replace notes text with input field
        notesText.hide();
        $(this).hide();
        notesContainer.addClass('editing');
        notesContainer.append(inputField);
        notesContainer.append(buttonContainer);
        
        // Focus on input
        inputField.focus();
        
        // Save button click handler
        saveButton.on('click', function() {
            const newNotes = inputField.val().trim();
            
            console.log('Saving notes for vehicle:', vehicleId, 'New notes:', newNotes);
            
            // Show loading state
            saveButton.prop('disabled', true).text('Saving...');
            
            // Send AJAX request to update notes
            $.ajax({
                url: '/admin/vehicles/update-notes',
                method: 'POST',
                data: {
                    vehicle_id: vehicleId,
                    notes: newNotes,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Notes update response:', response);
                    
                    if (response.success) {
                        // Update the notes text
                        notesText.text(response.notes || 'No notes');
                        
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Notes updated successfully!');
                        } else {
                            alert('Notes updated successfully!');
                        }
                    } else {
                        console.error('Notes update failed:', response.message);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Failed to update notes: ' + (response.message || 'Unknown error'));
                        } else {
                            alert('Failed to update notes: ' + (response.message || 'Unknown error'));
                        }
                    }
                    
                    // Restore original state
                    inputField.remove();
                    buttonContainer.remove();
                    notesContainer.removeClass('editing');
                    notesText.show();
                    $('.edit-notes[data-vehicle-id="' + vehicleId + '"]').show();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', { xhr, status, error });
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to update notes. Please try again.');
                    } else {
                        alert('Failed to update notes. Please try again.');
                    }
                    
                    // Restore original state
                    inputField.remove();
                    buttonContainer.remove();
                    notesContainer.removeClass('editing');
                    notesText.show();
                    $('.edit-notes[data-vehicle-id="' + vehicleId + '"]').show();
                }
            });
        });
        
        // Cancel button click handler
        cancelButton.on('click', function() {
            // Restore original state
            inputField.remove();
            buttonContainer.remove();
            notesContainer.removeClass('editing');
            notesText.show();
            $('.edit-notes[data-vehicle-id="' + vehicleId + '"]').show();
        });
        
        // Handle Enter key to save
        inputField.on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                saveButton.click();
            }
        });
        
        // Handle Escape key to cancel
        inputField.on('keydown', function(e) {
            if (e.which === 27) { // Escape key
                cancelButton.click();
            }
        });
    });
    
    // MOT Timeframe Filter functionality
    $('#mot-timeframe-filter').on('change', function() {
        const selectedTimeframe = $(this).val();
        const motTableRows = $('#mot_data_table tbody tr');
        let visibleCount = 0;
        
        console.log('MOT filter changed to:', selectedTimeframe);
        
        motTableRows.each(function() {
            const row = $(this);
            const daysUntilExpiryText = row.find('td:nth-child(4)').text().trim();
            
            // Extract days from the text (handle different formats)
            let daysUntilExpiry = 0;
            if (daysUntilExpiryText.includes('Expired')) {
                // Extract number from "Expired X days ago"
                const match = daysUntilExpiryText.match(/Expired (\d+) days ago/);
                if (match) {
                    daysUntilExpiry = -parseInt(match[1]); // Negative for expired
                }
            } else if (daysUntilExpiryText.includes('days')) {
                // Extract number from "X days" or badge with days
                const match = daysUntilExpiryText.match(/(\d+)\s*days/);
                if (match) {
                    daysUntilExpiry = parseInt(match[1]);
                }
            } else {
                // Try to parse as plain number
                const parsed = parseInt(daysUntilExpiryText);
                if (!isNaN(parsed)) {
                    daysUntilExpiry = parsed;
                }
            }
            
            let shouldShow = false;
            
            if (selectedTimeframe === 'all') {
                shouldShow = true; // Show all upcoming MOTs
            } else {
                const months = parseInt(selectedTimeframe);
                const daysInTimeframe = months * 30; // Approximate days in timeframe
                
                // Show vehicles expiring within the selected timeframe
                shouldShow = daysUntilExpiry >= 0 && daysUntilExpiry <= daysInTimeframe;
            }
            
            if (shouldShow) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });
        
        // Update the count badge
        $('#mot-count').text(visibleCount);
        
        console.log('MOT filter applied. Visible rows:', visibleCount);
    });
    
    // Initialize filter on page load (apply default "Next 2 Months" filter)
    $('#mot-timeframe-filter').trigger('change');
});

// Helper function to get badge class for status
function getBadgeClass(status) {
    switch (status) {
        case 'Available':
            return 'badge-success';
        case 'Rented':
            return 'badge-warning';
        case 'Workshop':
            return 'badge-info';
        case 'Disabled':
            return 'badge-secondary';
        default:
            return 'badge-success';
    }
}

// Helper function to get button class for status
function getButtonClass(status) {
    switch (status) {
        case 'Available':
            return 'btn-outline-success';
        case 'Rented':
            return 'btn-outline-warning';
        case 'Workshop':
            return 'btn-outline-info';
        case 'Disabled':
            return 'btn-outline-secondary';
        default:
            return 'btn-outline-success';
    }
}
</script>