@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  .card-header {
    background: #7FD7E1;
    color: white;
  }
  
  .required {
    color: #dc3545;
  }
  
  .form-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
  }
  
  .section-title {
    color: #032127;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #7FD7E1;
  }
</style>
@endsection

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">Vehicles</a></li>
<li class="breadcrumb-item active">Add Vehicle</li>
@endsection

@section('content')
<div class="container-fluid">
  @if (count($errors) > 0)
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <div class="card">
    <div class="card-header">
      <h3 class="card-title mb-0">
        <i class="fa fa-plus"></i> Add New Vehicle
      </h3>
    </div>
    
    <div class="card-body">
      {!! Form::open(['route' => 'vehicles.store', 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'vehicleForm']) !!}
      {!! Form::hidden('user_id', Auth::user()->id) !!}
      
      <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6">
          <div class="form-section">
            <h5 class="section-title">Basic Information</h5>
            
            <!-- Registration Plate -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Registration Plate <span class="required">*</span></label>
              <div class="col-sm-8">
                <input type="text" name="license_plate" class="form-control" required 
                       placeholder="e.g., ABC-123" value="{{ old('license_plate') }}">
              </div>
            </div>
            
            <!-- Make -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Make <span class="required">*</span></label>
              <div class="col-sm-8">
                <input type="text" name="make_name" class="form-control" required 
                       placeholder="e.g., Toyota, Ford, BMW" value="{{ old('make_name') }}">
              </div>
            </div>
            
            <!-- Model -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Model <span class="required">*</span></label>
              <div class="col-sm-8">
                <input type="text" name="model_name" class="form-control" required 
                       placeholder="e.g., Camry, Focus, X3" value="{{ old('model_name') }}">
              </div>
            </div>
            
            <!-- Fuel Type -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Fuel Type <span class="required">*</span></label>
              <div class="col-sm-8">
                <select name="engine_type" class="form-control" required>
                  <option value="">Select Fuel Type</option>
                  <option value="Petrol" @if(old('engine_type')=='Petrol') selected @endif>Petrol</option>
                  <option value="Diesel" @if(old('engine_type')=='Diesel') selected @endif>Diesel</option>
                  <option value="Electric" @if(old('engine_type')=='Electric') selected @endif>Electric</option>
                  <option value="Hybrid" @if(old('engine_type')=='Hybrid') selected @endif>Hybrid</option>
                  <option value="CNG" @if(old('engine_type')=='CNG') selected @endif>CNG</option>
                  <option value="LPG" @if(old('engine_type')=='LPG') selected @endif>LPG</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Assignment & Settings -->
        <div class="col-md-6">
          <div class="form-section">
            <h5 class="section-title">Assignment & Settings</h5>
            
            <!-- Vehicle Status -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Status <span class="required">*</span></label>
              <div class="col-sm-8">
                <select name="vehicle_status" class="form-control" required>
                  <option value="Available" @if(old('vehicle_status')=='Available' || old('vehicle_status')==null) selected @endif>Available</option>
                  <option value="Rented" @if(old('vehicle_status')=='Rented') selected @endif>Rented</option>
                  <option value="Workshop" @if(old('vehicle_status')=='Workshop') selected @endif>Workshop</option>
                  <option value="Disabled" @if(old('vehicle_status')=='Disabled') selected @endif>Disabled</option>
                </select>
              </div>
            </div>
            
            <!-- Assigned Driver -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Assigned Driver</label>
              <div class="col-sm-8">
                <select id="driver_id" name="driver_id" class="form-control">
                  <option value="">No Driver Assigned</option>
                  @foreach($drivers as $driver)
                  <option value="{{$driver->id}}" @if(old('driver_id')==$driver->id) selected @endif>
                    {{$driver->name}}@if($driver->getMeta('is_active') != 1) (Inactive)@endif
                  </option>
                  @endforeach
                </select>
                <small class="text-muted">Optional: Assign a driver to this vehicle</small>
              </div>
            </div>
            
            <!-- Telematics Link -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Telematics</label>
              <div class="col-sm-8">
                <input type="url" name="telematics_link" class="form-control" 
                       placeholder="https://telematics.example.com/vehicle/123" value="{{ old('telematics_link') }}">
                <small class="text-muted">Optional: Link to vehicle's telematics dashboard</small>
              </div>
            </div>
            
            <!-- Vehicle Type -->
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Vehicle Type</label>
              <div class="col-sm-8">
                <select name="type_id" class="form-control" id="type_id">
                  <option value="">Select Vehicle Type</option>
                  @foreach($types as $type)
                  <option value="{{$type->id}}" @if(old('type_id')==$type->id) selected @endif>{{$type->displayname}}</option>
                  @endforeach
                </select>
                <small class="text-muted">Optional: Categorize vehicle type</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Additional Settings (Optional) -->
      <div class="row">
        <div class="col-md-12">
          <div class="form-section">
            <h5 class="section-title">Additional Settings (Optional)</h5>
            
            <div class="row">
              <div class="col-md-6">
                <!-- Vehicle Group -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Vehicle Group</label>
                  <div class="col-sm-8">
                    <select id="group_id" name="group_id" class="form-control">
                      <option value="">No Group</option>
                      @foreach($groups as $group)
                      <option value="{{$group->id}}" @if(old('group_id')==$group->id) selected @endif>{{$group->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <!-- Year -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Year</label>
                  <div class="col-sm-8">
                    <input type="number" name="year" class="form-control" 
                           placeholder="e.g., 2024" value="{{ old('year') }}" min="1900" max="2030">
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Financial Information -->
            <div class="row mt-3">
              <div class="col-md-6">
                <!-- Initial Cost -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Cost</label>
                  <div class="col-sm-8">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">£</span>
                      </div>
                      <input type="number" name="initial_cost" class="form-control" 
                             placeholder="e.g., 25000" value="{{ old('initial_cost') }}" min="0" step="0.01">
                    </div>
                    <small class="text-muted">Initial cost of the vehicle</small>
                  </div>
                </div>
                
                <!-- Initial Mileage -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Initial Mileage</label>
                  <div class="col-sm-8">
                    <div class="input-group">
                      <input type="number" name="int_mileage" class="form-control" 
                             placeholder="e.g., 15000" value="{{ old('int_mileage') }}" min="0">
                      <div class="input-group-append">
                        <span class="input-group-text">Miles</span>
                      </div>
                    </div>
                    <small class="text-muted">Vehicle mileage when acquired</small>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <!-- Scheme -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Scheme</label>
                  <div class="col-sm-8">
                    <select name="vehicle_scheme" class="form-control">
                      <option value="">Select Scheme</option>
                      <option value="Rental" @if(old('vehicle_scheme')=='Rental') selected @endif>Rental</option>
                      <option value="Rent To Buy" @if(old('vehicle_scheme')=='Rent To Buy') selected @endif>Rent To Buy</option>
                      <option value="Other" @if(old('vehicle_scheme')=='Other') selected @endif>Other</option>
                    </select>
                    <small class="text-muted">Vehicle acquisition scheme</small>
                  </div>
                </div>
                
                <!-- Price -->
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Price</label>
                  <div class="col-sm-8">
                    <div class="row">
                      <div class="col-8">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">£</span>
                          </div>
                          <input type="number" name="vehicle_price" class="form-control" 
                                 placeholder="e.g., 450" value="{{ old('vehicle_price') }}" min="0" step="0.01">
                        </div>
                      </div>
                      <div class="col-4">
                        <select name="price_period" class="form-control">
                          <option value="monthly" @if(old('price_period')=='monthly' || old('price_period')==null) selected @endif>Monthly</option>
                          <option value="weekly" @if(old('price_period')=='weekly') selected @endif>Weekly</option>
                        </select>
                      </div>
                    </div>
                    <small class="text-muted">Rental or lease price</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="row">
        <div class="col-12">
          <hr>
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
              <i class="fa fa-info-circle"></i> Vehicle ID will be automatically generated after saving
            </small>
            <div>
              <a href="{{ route('vehicles.index') }}" class="btn btn-secondary">
                <i class="fa fa-times"></i> Cancel
              </a>
              <button type="submit" class="btn" style="background: #7FD7E1; color: white; margin-left: 10px;">
                <i class="fa fa-save"></i> Create Vehicle
              </button>
            </div>
          </div>
        </div>
      </div>
      
      {!! Form::close() !!}
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function() {
    // Form validation
    if (typeof $ !== 'undefined') {
        $('#vehicleForm').on('submit', function(e) {
        var isValid = true;
        
        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove validation styling on change
    $('[required]').on('change keyup', function() {
        if ($(this).val()) {
            $(this).removeClass('is-invalid');
        }
    });
    } else {
        console.log('jQuery not available for vehicle form validation');
    }
});
</script>
@endsection