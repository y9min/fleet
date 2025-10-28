@extends('layouts.app')
@section('extra_css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item "><a href="{{ route('invitations.index') }}">@lang('menu.bookings')</a></li>
    <li class="breadcrumb-item active">@lang('fleet.new_booking')</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        @lang('fleet.new_booking')
                    </h3>
                </div>

                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {!! Form::open(['route' => 'invitations.store', 'method' => 'post','class' => 'form-reset']) !!}
                    {!! Form::hidden('user_id', Auth::user()->id) !!}
                    {!! Form::hidden('status', 0) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                @if (Auth::user()->user_type == 'C')
                                    {!! Form::hidden('customer_id', Auth::user()->id) !!}
                                @else
                                    {!! Form::hidden('customer_id', Auth::user()->id) !!}
                                @endif
                                {!! Form::label('driver_id', __('fleet.selectDriver'), ['class' => 'form-label']) !!}
                                <select id="driver_id" name="driver_id" class="form-control" required>
                                    <option value="">-</option>
                                    
                                    @php
                                        $approvedDrivers = [];
                                        $onboardingDrivers = [];
                                        
                                        foreach ($drivers as $driver) {
                                            if (isset($driver->is_onboarding) && $driver->is_onboarding) {
                                                $onboardingDrivers[] = $driver;
                                            } else {
                                                $approvedDrivers[] = $driver;
                                            }
                                        }
                                    @endphp
                                    
                                    @if(count($onboardingDrivers) > 0)
                                        <optgroup label="Onboarding Drivers">
                                            @foreach ($onboardingDrivers as $driver)
                                                <option value="{{ $driver->id }}">
                                                    {{ $driver->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    
                                    @if(count($approvedDrivers) > 0)
                                        <optgroup label="Approved Drivers">
                                            @foreach ($approvedDrivers as $driver)
                                                <option value="{{ $driver->id }}">
                                                    {{ $driver->name }}
                                                    @if(Hyvikk::api('api') == "1")
                                                        @if ($driver && $driver->getMeta('is_available') == '1')
                                                            - (Online) @else - (Offline)
                                                        @endif
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                
                                <!-- Driver Details Section -->
                                <div id="driver-details" class="mt-3" style="display: none;">
                                    <div class="card" style="width: 1315px;">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">Driver Details</h6>
                                        </div>
                                        <div class="card-body" id="driver-details-content">
                                            <!-- Driver details will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                                
                                <style>
                                .inline-field {
                                    display: inline-block;
                                    margin-right: 20px;
                                    margin-bottom: 8px;
                                    vertical-align: top;
                                }
                                .inline-field strong {
                                    margin-right: 5px;
                                }
                                .inline-field .text-muted {
                                    color: #6c757d;
                                }
                                .inline-field .badge {
                                    margin-left: 5px;
                                }
                                .vehicle-selection-highlight {
                                    background-color: #fff3cd;
                                    border: 1px solid #ffeaa7;
                                    border-radius: 4px;
                                    padding: 8px 12px;
                                    margin: 4px 0;
                                    display: inline-block;
                                }
                                .vehicle-selection-highlight strong {
                                    color: #856404;
                                    font-weight: bold;
                                }
                                .vehicle-selection-highlight .text-muted {
                                    color: #856404;
                                    font-weight: 500;
                                }
                                </style>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                              {!! Form::label('vehicle_id',__('fleet.selectVehicle'), ['class' => 'form-label']) !!}
                              <select id="vehicle_id" name="vehicle_id" class="form-control" required>
                                
                                <option value="">-</option>

                                    @php
                                        // Group vehicles by vehicle type name
                                        $groupedVehicles = [];

                                        foreach ($vehicles as $vehicle) {
                                            $typeName = 'Other';
                                            if (isset($vehicle->type_id)) {
                                                $vt = \App\Model\VehicleTypeModel::find($vehicle->type_id);
                                                if ($vt) {
                                                    $typeName = $vt->vehicletype;
                                                }
                                            }
                                            $groupedVehicles[$typeName][] = $vehicle;
                                        }
                                    @endphp

                                    @foreach($groupedVehicles as $typeName => $vehiclesInGroup)
                                        <optgroup label="{{ $typeName }}">
                                            @foreach($vehiclesInGroup as $vehicle)
                                                @php
                                                    $assignDriverId = method_exists($vehicle, 'getMeta') ? $vehicle->getMeta('assign_driver_id') : null;
                                                @endphp
                                                <option value="{{ $vehicle->id }}" data-driver="{{ $assignDriverId }}">
                                                    {{ $vehicle->make_name }} {{ $vehicle->model_name }} {{ $vehicle->year }} ({{ $vehicle->license_plate }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                
                            
                              </select>
                              @php
                                $vehiclesForLog = collect($vehicles)->map(function($v){
                                  return [
                                    'id' => $v->id ?? null,
                                    'make' => $v->make_name ?? null,
                                    'model' => $v->model_name ?? null,
                                    'year' => $v->year ?? null,
                                    'plate' => $v->license_plate ?? null,
                                    'in_service' => $v->in_service ?? null,
                                    'group_id' => $v->group_id ?? null,
                                    'status_meta' => method_exists($v, 'getMeta') ? $v->getMeta('vehicle_status') : null
                                  ];
                                });
                              @endphp
                              <script>
                              (function() {
                                try {
                                  const vehiclesData = @json($vehiclesForLog);
                                  console.log('[Invitations] Vehicles payload count:', vehiclesData.length);
                                  console.table(vehiclesData);
                                  const selectEl = document.getElementById('vehicle_id');
                                  if (selectEl) {
                                    console.log('[Invitations] Dropdown option count:', selectEl.options.length);
                                    const optionTexts = Array.from(selectEl.options).map(o => o.textContent.trim());
                                    console.log('[Invitations] First 10 options:', optionTexts.slice(0,10));
                                  } else {
                                    console.warn('[Invitations] #vehicle_id select not found in DOM');
                                  }
                                } catch (e) {
                                  console.error('[Invitations] Logging error:', e);
                                }
                              })();
                              </script>
                            </div>
                          </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pickup', __('fleet.pickup'), ['class' => 'form-label']) !!}
                                <div class='input-group mb-2'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <span class="fa fa-calendar"></span></span>
                                    </div>
                                    {!! Form::date('pickup_date', date('Y-m-d'), ['class' => 'form-control', 'required','autocomplete' => 'off', 'id' => 'pickup_date']) !!}
                                </div>
                                <div class='input-group mb-2'>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <span class="fa fa-clock-o"></span></span>
                                    </div>
                                    {!! Form::time('pickup_time', date('H:i'), ['class' => 'form-control', 'required','autocomplete' => 'off', 'id' => 'pickup_time']) !!}
                                </div>
                                {!! Form::hidden('pickup', date('Y-m-d H:i'), ['id' => 'pickup']) !!}
                            </div>
                        </div>
                    </div>

                    

                    @if(Hyvikk::get('return_booking') == 1)
                    @endif


                    
                    @if (Auth::user()->user_type == 'C')
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('pickup_addr', __('fleet.pickup_addr'), ['class' => 'form-label']) !!}
                                {!! Form::textarea('pickup_addr', $company_address ?? null, ['class' => 'form-control', 'required', 'style' => 'height:100px']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('note', __('fleet.note'), ['class' => 'form-label']) !!}
                                {!! Form::textarea('note', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __('fleet.book_note'),
                                    'style' => 'height:100px',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    


                  

                    

                  

                    <div class="blank"></div>
                    <div class="col-md-12">
                        {!! Form::submit(__('fleet.save_booking'), ['class' => 'btn btn-info']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">@lang('fleet.new_customer')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                {!! Form::open(['route' => 'customers.ajax_store', 'method' => 'post', 'id' => 'create_customer_form']) !!}
                <div class="modal-body">
                    <div class="alert alert-danger print-error-msg" style="display:none">
                        <ul></ul>
                    </div>
                    <div class="form-group">
                        {!! Form::label('first_name', __('fleet.firstname'), ['class' => 'form-label']) !!}
                        {!! Form::text('first_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', __('fleet.lastname'), ['class' => 'form-label']) !!}
                        {!! Form::text('last_name', null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', __('fleet.gender'), ['class' => 'form-label']) !!}<br>
                        <input type="radio" name="gender" class="flat-red gender" value="1" checked>
                        @lang('fleet.male')<br>

                        <input type="radio" name="gender" class="flat-red gender" value="0"> @lang('fleet.female')
                    </div>

                    <div class="form-group">
                        {!! Form::label('phone', __('fleet.phone'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                            </div>
                            {!! Form::number('phone', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', __('fleet.email'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                            </div>
                            {!! Form::email('email', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('address', __('fleet.address'), ['class' => 'form-label']) !!}
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-address-book-o"></i></span>
                            </div>
                            {!! Form::textarea('address', null, ['class' => 'form-control', 'size' => '30x2','required']) !!}
                        </div>
                    </div>
                </div>

             

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
                    <button type="submit" class="btn btn-info">@lang('fleet.save_cust')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('script')

<script>







document.addEventListener("DOMContentLoaded", function() {
    let oneWayBtn = document.getElementById("oneWayBtn");
    let returnWayBtn = document.getElementById("returnWayBtn");
    let returnDateContainer = document.getElementById("returnDateContainer");

    // Toggle active button styles
    function activateButton(activeBtn, inactiveBtn) {
        activeBtn.classList.remove("btn-secondary");
        activeBtn.classList.add("btn-success");

        inactiveBtn.classList.remove("btn-success");
        inactiveBtn.classList.add("btn-secondary");
    }

    // One Way Click
    oneWayBtn.addEventListener("click", function() {
        returnDateContainer.classList.add("d-none");
        activateButton(oneWayBtn, returnWayBtn);
        document.querySelector(".booking_type").value = "one_way";

        get_driver($("#pickup").val(), $("#dropoff").val()); 
        get_vehicle($("#pickup").val(), $("#dropoff").val());
    });

    // Return Way Click
    returnWayBtn.addEventListener("click", function() {
        returnDateContainer.classList.remove("d-none");
        activateButton(returnWayBtn, oneWayBtn);
        document.querySelector(".booking_type").value = "return_way";

        get_driver($("#pickup").val(), $("#returnDropoff").val()); 
        get_vehicle($("#pickup").val(), $("#returnDropoff").val());
    });

    // Set default to One Way on page load
    returnDateContainer.classList.add("d-none");
    activateButton(oneWayBtn, returnWayBtn);
    document.querySelector(".booking_type").value = "one_way";
});
</script>

    </script>

    <script>
      var datet = "{{date('Y-m-d H:i:s')}}";
      var getDriverRoute='{{ url("admin/get_driver") }}';
      var getVehicleRoute='{{ url("admin/get_vehicle") }}';
      var prevAddress='{{ url("admin/prev-address") }}';
      var selectDriver="@lang('fleet.selectDriver')";
      var selectCustomer="@lang('fleet.selectCustomer')";
      var selectVehicle="@lang('fleet.selectVehicle')";
      var addCustomer="@lang('fleet.add_customer')";
      var prevAddressLang="@lang('fleet.prev_addr')";
     
      var fleet_email_already_taken="@lang('fleet.email_already_taken')";
    </script>
    <script src="{{asset('assets/js/bookings/create.js?2343453')}}"></script>   
     @if (Hyvikk::api('google_api') == '1')
        <script>
            function initMap() {
                $('#pickup_addr').attr("placeholder", "");
                var pickup_addr = document.getElementById('pickup_addr');
                new google.maps.places.Autocomplete(pickup_addr);
            }
        </script>
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ Hyvikk::api('api_key') }}&libraries=places&callback=initMap"
            async defer></script>
    @endif

    <script>
        $(document).ready(function() {
          $(".form-reset").on("submit", function(event) {
              $('input[type="submit"]').prop('disabled', true);
          });
          
          // Handle driver selection change
          $('#driver_id').on('change', function() {
              var driverId = $(this).val();
              var $driverDetails = $('#driver-details');
              var $driverDetailsContent = $('#driver-details-content');
              
              if (!driverId) {
                  $driverDetails.hide();
                  return;
              }
              
              // Show loading state
              $driverDetailsContent.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading driver details...</div>');
              $driverDetails.show();
              
              // Check if it's an onboarding driver
              if (driverId.startsWith('onboarding_')) {
                  var onboardingDriverId = driverId.replace('onboarding_', '');
                  loadOnboardingDriverDetails(onboardingDriverId);
              } else {
                  loadRegularDriverDetails(driverId);
              }
          });

          // Compose hidden pickup field from date/time parts
          function updatePickupCombined() {
              var d = $('#pickup_date').val();
              var t = $('#pickup_time').val();
              if (d && t) {
                  $('#pickup').val(d + ' ' + t);
              }
          }
          $('#pickup_date, #pickup_time').on('change keyup', updatePickupCombined);
          // initialize on load
          updatePickupCombined();
        });
        
        // Function to load onboarding driver details
        function loadOnboardingDriverDetails(driverId) {
            $.ajax({
                url: '{{ url("admin/onboarding") }}/' + driverId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        displayOnboardingDriverDetails(response.driver, response.customFields);
                    } else {
                        $('#driver-details-content').html('<div class="alert alert-danger">Error loading driver details</div>');
                    }
                },
                error: function(xhr) {
                    $('#driver-details-content').html('<div class="alert alert-danger">Error loading driver details</div>');
                }
            });
        }
        
        // Function to load regular driver details
        function loadRegularDriverDetails(driverId) {
            $.ajax({
                url: '{{ url("admin/drivers") }}/' + driverId + '/details',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        displayRegularDriverDetails(response.driver);
                    } else {
                        // Fallback to basic info if API fails
                        var driverName = $('#driver_id option:selected').text().split(' - ')[0];
                        var driverInfo = {
                            name: driverName,
                            type: 'regular'
                        };
                        displayRegularDriverDetails(driverInfo);
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                    // Fallback to basic info if API fails
                    var driverName = $('#driver_id option:selected').text().split(' - ')[0];
                    var driverInfo = {
                        name: driverName,
                        email: 'N/A',
                        phone: 'N/A',
                        license_number: 'N/A',
                        is_active: 1,
                        type: 'regular'
                    };
                    displayRegularDriverDetails(driverInfo);
                }
            });
        }
        
        // Function to display onboarding driver details
        function displayOnboardingDriverDetails(driver, customFields) {
            var html = '<div class="details-content">';
            
            // Basic Information - Inline layout similar to approved drivers
            html += '<div class="mb-3">';
            html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>License:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
            var statusClass = driver.status === 'approved' ? 'success' : (driver.status === 'rejected' ? 'danger' : 'warning');
            html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.status || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Submitted:</strong><span class="text-muted">' + (driver.created_at || 'N/A') + '</span></div>';
            html += '</div>';
            
            // Documents Section - Inline layout with proper spacing
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
            
            // Custom Fields Data - Inline layout
            if (driver.custom_data && Object.keys(driver.custom_data).length > 0) {
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Additional Information:</strong>';
                
                var fieldNameMap = {
                    'scheme_selection': 'Scheme Selection',
                    'vehicle_selection': 'Vehicle Selection',
                    'insurance_selection': 'Insurance Selection',
                    'ni_number': 'NI Number',
                    'address': 'Address',
                    'city': 'City',
                    'state': 'State',
                    'country': 'Country',
                    'postal_code': 'Postal Code',
                    'date_of_birth': 'Date of Birth',
                    'gender': 'Gender',
                    'emergency_contact_name': 'Emergency Contact Name',
                    'emergency_contact_phone': 'Emergency Contact Phone',
                    'driver_license_expiry': 'Driver License Expiry',
                    'insurance_expiry': 'Insurance Expiry'
                };
                
                for (var key in driver.custom_data) {
                    if (driver.custom_data.hasOwnProperty(key) && key !== 'token' && key !== 'terms' && !key.endsWith('_url')) {
                        var value = driver.custom_data[key];
                        var displayValue = '';
                        var fieldName = fieldNameMap[key] || key;
                        var isFileField = false;
                        
                        // Check if key starts with 'custom_' and extract the field ID
                        if (key.startsWith('custom_')) {
                            var fieldId = key.replace('custom_', '');
                            // Find the actual field name from customFields by matching field ID
                            customFields.forEach(function(field) {
                                if (field.id == fieldId) {
                                    fieldName = field.field_name;
                                    isFileField = (field.field_type === 'file');
                                }
                            });
                        }
                        
                        // Special handling for specific fields
                        if (key === 'vehicle_selection' && value) {
                            if (driver.vehicle_details) {
                                displayValue = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name + ' (' + driver.vehicle_details.license_plate + ')';
                            } else {
                                displayValue = 'Vehicle ID: ' + value;
                            }
                            // Add highlighting class for vehicle selection
                            html += '<div class="vehicle-selection-highlight"><strong>' + fieldName + ':</strong><span class="text-muted">' + displayValue + '</span></div>';
                            continue;
                        } else if (key === 'scheme_selection' && value) {
                            displayValue = value;
                        } else if (key === 'insurance_selection' && value) {
                            displayValue = value === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
                        } else if (isFileField && value && value.toString().trim() !== '') {
                            // For file fields, show a view link using the generated URL
                            var fileUrl = driver.custom_data[key + '_url'] || value;
                            displayValue = '<a href="' + fileUrl + '" class="btn btn-sm btn-outline-primary" target="_blank">';
                            displayValue += '<i class="fas fa-eye"></i> View Document';
                            displayValue += '</a>';
                        } else {
                            if (Array.isArray(value)) {
                                displayValue = value.length === 0 ? 'No data provided' : value.join(', ');
                            } else if (typeof value === 'object' && value !== null) {
                                displayValue = JSON.stringify(value);
                            } else if (value !== null && value !== undefined && value !== '' && value.toString().trim() !== '' && value.toString().trim() !== 'null' && value.toString().trim() !== 'undefined') {
                                displayValue = value.toString();
                            } else {
                                displayValue = 'No data provided';
                            }
                        }
                        
                        html += '<div class="inline-field"><strong>' + fieldName + ':</strong><span class="text-muted">' + displayValue + '</span></div>';
                    }
                }
                html += '</div>';
                html += '</div>';
            } else {
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Additional Information:</strong><span class="text-muted">No additional information provided.</span></div>';
                html += '</div>';
            }
            
            html += '</div>';
            $('#driver-details-content').html(html);
        }
        
        // Function to display regular driver details
        function displayRegularDriverDetails(driver) {
            console.log('Driver data received:', driver); // Debug log
            
            var html = '<div class="details-content">';
            
            // Basic Information - Inline layout similar to onboarding
            html += '<div class="mb-3">';
            html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>License Number:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
            html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + (driver.is_active == 1 ? 'success' : 'danger') + '">' + (driver.is_active == 1 ? 'Active' : 'Inactive') + '</span></div>';
            html += '</div>';
            
            // Assigned Vehicle Section
            if (driver.assigned_vehicle) {
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Assigned Vehicle:</strong><span class="text-muted">' + driver.assigned_vehicle.license_plate + ' (' + driver.assigned_vehicle.make_name + ' ' + driver.assigned_vehicle.model_name + ')</span></div>';
                html += '</div>';
            }
            
            // Documents Section - Inline layout with proper spacing
            html += '<div class="mb-3">';
            html += '<div class="inline-field"><strong>Documents:</strong>';
            if (driver.license_image || driver.license_upload_path) {
                var licenseUrl = driver.license_url || (driver.license_image ? '{{ asset("uploads/drivers/") }}/' + driver.license_image : '');
                html += '<a href="' + licenseUrl + '" class="btn btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
                html += '<i class="fas fa-eye"></i> License';
                html += '</a>';
            }
            if (driver.documents || driver.insurance_upload_path) {
                var insuranceUrl = driver.insurance_url || (driver.documents ? '{{ asset("uploads/drivers/") }}/' + driver.documents : '');
                html += '<a href="' + insuranceUrl + '" class="btn btn-outline-info" target="_blank" style="border: 1px solid #17a2b8; color: #17a2b8; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
                html += '<i class="fas fa-eye"></i> Insurance';
                html += '</a>';
            }
            html += '</div>';
            html += '</div>';
            
            // Additional Information from metadata - Show ALL available fields
            html += '<div class="mb-3">';
            html += '<div class="inline-field"><strong>Additional Information:</strong>';
            
            var hasAdditionalInfo = false;
            var processedFields = new Set(); // Track processed fields to avoid duplicates
            
            // First, process custom_data if it exists
            if (driver.custom_data) {
                try {
                    var customData = typeof driver.custom_data === 'string' ? JSON.parse(driver.custom_data) : driver.custom_data;
                    for (var customKey in customData) {
                        if (customData.hasOwnProperty(customKey) && customKey !== 'terms' && customKey !== 'token') {
                            var customValue = customData[customKey];
                            if (customValue && customValue !== null && customValue !== 'null' && customValue !== 'undefined' && customValue !== '') {
                                var customFieldName = customKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                var customDisplayValue = '';
                                
                                if (customKey === 'scheme_selection') {
                                    customDisplayValue = customValue;
                                } else if (customKey === 'vehicle_selection') {
                                    // Try to get vehicle details from the driver object or make an AJAX call
                                    if (driver.vehicle_details) {
                                        customDisplayValue = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name + ' (' + driver.vehicle_details.license_plate + ')';
                                    } else {
                                        customDisplayValue = 'Vehicle ID: ' + customValue;
                                    }
                                    // Add highlighting class for vehicle selection
                                    html += '<div class="vehicle-selection-highlight"><strong>' + customFieldName + ':</strong><span class="text-muted">' + customDisplayValue + '</span></div>';
                                    hasAdditionalInfo = true;
                                    processedFields.add(customKey);
                                    continue;
                                } else if (customKey === 'insurance_selection') {
                                    customDisplayValue = customValue === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
                                } else if (customKey.startsWith('custom_')) {
                                    // Handle custom file fields
                                    if (typeof customValue === 'string' && customValue.length > 0 && customValue.includes('/')) {
                                        var customFileName = customValue.split('/').pop();
                                        var customFileUrl = '{{ asset("storage/") }}/' + customValue;
                                        customDisplayValue = '<a href="' + customFileUrl + '" class="btn btn-sm btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 4px 8px; font-size: 12px; margin-left: 5px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">';
                                        customDisplayValue += '<i class="fas fa-eye"></i> View Document';
                                        customDisplayValue += '</a>';
                                    } else {
                                        customDisplayValue = customValue;
                                    }
                                } else {
                                    customDisplayValue = customValue;
                                }
                                
                                html += '<div class="inline-field"><strong>' + customFieldName + ':</strong><span class="text-muted">' + customDisplayValue + '</span></div>';
                                hasAdditionalInfo = true;
                                processedFields.add(customKey);
                            }
                        }
                    }
                } catch (e) {
                    // If JSON parsing fails, skip this field
                }
            }
            
            // Then process other fields, but skip those already processed from custom_data
            for (var key in driver) {
                if (driver.hasOwnProperty(key) && !processedFields.has(key)) {
                    var value = driver[key];
                    
                    // Skip system fields and already displayed fields
                    if (key !== 'id' && key !== 'user_id' && key !== 'created_at' && key !== 'updated_at' && key !== 'deleted_at' && 
                        key !== 'name' && key !== 'email' && key !== 'phone' && key !== 'license_number' && key !== 'is_active' && 
                        key !== 'assigned_vehicle' && key !== 'license_url' && key !== 'insurance_url' && 
                        key !== 'password' && key !== 'remember_token' && key !== 'api_token' && key !== 'user_type' && 
                        key !== 'group_id' && key !== 'company_id' && key !== 'email_verified_at' && key !== 'terms' && key !== 'token' &&
                        key !== 'custom_data' && key !== 'license_upload_path' && key !== 'insurance_upload_path') {
                        
                        if (value !== null && value !== undefined && value !== '' && value !== 'null' && value !== 'undefined') {
                            var displayValue = '';
                            var fieldName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            
                            // Special handling for specific fields
                            if (key === 'license_image' || key === 'documents') {
                                // For file fields, show as clickable buttons
                                if (typeof value === 'string' && value.length > 0) {
                                    var fileName = value.split('/').pop() || value;
                                    var fileUrl = value.startsWith('http') ? value : '{{ asset("storage/") }}/' + value;
                                    displayValue = '<a href="' + fileUrl + '" class="btn btn-sm btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 4px 8px; font-size: 12px; margin-left: 5px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">';
                                    displayValue += '<i class="fas fa-eye"></i> View Document';
                                    displayValue += '</a>';
                                } else {
                                    continue; // Skip if no value
                                }
                            } else {
                                if (Array.isArray(value)) {
                                    displayValue = value.length === 0 ? 'No data provided' : value.join(', ');
                                } else if (typeof value === 'object' && value !== null) {
                                    // Skip complex objects
                                    continue;
                                } else {
                                    displayValue = value.toString();
                                }
                            }
                            
                            html += '<div class="inline-field"><strong>' + fieldName + ':</strong><span class="text-muted">' + displayValue + '</span></div>';
                            hasAdditionalInfo = true;
                        }
                    }
                }
            }
            
            if (!hasAdditionalInfo) {
                html += '<span class="text-muted">No additional information provided.</span>';
            }
            
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            $('#driver-details-content').html(html);
        }
      </script>

@endsection




