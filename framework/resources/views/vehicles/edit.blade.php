@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  .vehicle-form-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  .vehicle-form-header {
    background: #7FD7E1;
    color: white;
    padding: 1rem 1.5rem;
    margin: 0;
  }
  
  .vehicle-form-header h4 {
    margin: 0;
    font-weight: 600;
  }
  
  .form-tabs {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 0;
  }
  
  .form-tab {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #7FD7E1;
    color: white;
    margin: 0;
    border: none;
    font-weight: 500;
  }
  
  .form-content {
    padding: 2rem;
    background: white;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .form-field {
    margin-bottom: 1rem;
  }
  
  .form-field label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
    font-size: 0.9rem;
  }
  
  .form-field .required::after {
    content: " *";
    color: #dc3545;
  }
  
  .form-field input,
  .form-field select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9rem;
    transition: border-color 0.2s;
  }
  
  .form-field input:focus,
  .form-field select:focus {
    outline: none;
    border-color: #7FD7E1;
    box-shadow: 0 0 0 2px rgba(127, 215, 225, 0.2);
  }
  
  .form-field .help-text {
    font-size: 0.8rem;
    color: #666;
    margin-top: 0.25rem;
  }
  
  .date-dropdowns {
    display: flex;
    gap: 5px;
    align-items: center;
  }
  
  .date-dropdowns select {
    min-width: 80px;
    text-align: center;
  }
  
  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 30px;
  }
  
  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  
  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 30px;
  }
  
  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }
  
  input:checked + .toggle-slider {
    background-color: #7FD7E1;
  }
  
  input:checked + .toggle-slider:before {
    transform: translateX(30px);
  }
  
  .file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 4px;
    padding: 1rem;
    text-align: center;
    background: #f9f9f9;
    cursor: pointer;
    transition: border-color 0.2s;
  }
  
  .file-upload-area:hover {
    border-color: #7FD7E1;
    background: #f0f9fa;
  }
  
  .file-upload-area input[type="file"] {
    display: none;
  }
  
  .user-defined-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 6px;
    margin-top: 2rem;
  }
  
  .user-defined-field {
    display: flex;
    gap: 0.5rem;
    align-items: end;
    margin-bottom: 0.5rem;
  }
  
  .user-defined-field input {
    flex: 1;
  }
  
  .add-field-btn {
    background: #7FD7E1;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
  }
  
  .add-field-btn:hover {
    background: #6bc4ce;
  }
  
  .submit-section {
    text-align: left;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
  }
  
  .submit-btn {
    background: #7FD7E1;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    font-size: 1rem;
  }
  
  .submit-btn:hover {
    background: #6bc4ce;
  }
  
  .cancel-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 4px;
    margin-right: 1rem;
    text-decoration: none;
    display: inline-block;
  }
  
  .cancel-btn:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
  }
  
  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
  }
  
  @media (max-width: 992px) {
    .form-grid {
      grid-template-columns: 1fr 1fr;
    }
  }
</style>
@endsection

@section("breadcrumb")
<li class="breadcrumb-item"><a href="{{ route('vehicles.index')}}">Vehicles</a></li>
<li class="breadcrumb-item active">Edit Vehicle</li>
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

  <div class="vehicle-form-container">
    <div class="vehicle-form-header">
      <h4>Edit Vehicle</h4>
    </div>
    
    
    <div class="form-content">
      {!! Form::open(['route' => ['vehicles.update', $vehicle->id], 'method' => 'PATCH', 'enctype' => 'multipart/form-data', 'id' => 'vehicleForm']) !!}
      {!! Form::hidden('user_id', Auth::user()->id) !!}
      {!! Form::hidden('id', $vehicle->id) !!}
      
      <div class="form-grid">
        <!-- Column 1 -->
        <div class="form-field">
          <label class="required">Select Vehicle Make</label>
          <input type="text" name="make_name" class="form-control" required value="{{ $vehicle->make_name }}" placeholder="Enter vehicle make">
        </div>
        
        
        <div class="form-field">
          <label class="required">Select Vehicle Model</label>
          <input type="text" name="model_name" class="form-control" required value="{{ $vehicle->model_name }}" placeholder="Enter vehicle model">
        </div>
        
        <div class="form-field">
          <label>Vehicle Type</label>
          <select name="type_id" class="form-control">
            @foreach($types as $type)
              <option value="{{ $type->id }}" @if($vehicle->type_id == $type->id) selected @endif>{{ $type->displayname }}</option>
            @endforeach
          </select>
        </div>
        
        <div class="form-field">
          <label class="required">Fuel Type</label>
          <select name="engine_type" class="form-control" required>
            <option value="Petrol" @if($vehicle->engine_type == 'Petrol') selected @endif>Petrol</option>
            <option value="Diesel" @if($vehicle->engine_type == 'Diesel') selected @endif>Diesel</option>
            <option value="Electric" @if($vehicle->engine_type == 'Electric') selected @endif>Electric</option>
            <option value="Hybrid" @if($vehicle->engine_type == 'Hybrid') selected @endif>Hybrid</option>
            <option value="CNG" @if($vehicle->engine_type == 'CNG') selected @endif>CNG</option>
            <option value="LPG" @if($vehicle->engine_type == 'LPG') selected @endif>LPG</option>
          </select>
        </div>
        
        
        
        <div class="form-field">
          <label class="required">Registration Plate</label>
          <input type="text" name="license_plate" class="form-control" required value="{{ $vehicle->license_plate }}" placeholder="License Plate Number">
        </div>
        
        
        <div class="form-field">
          <label>Vehicle Year</label>
          <input type="number" name="year" class="form-control" value="{{ $vehicle->year }}" min="1900" max="2030" placeholder="e.g., 2024">
        </div>
        
        <div class="form-field">
          <label>Price (with insurance included)</label>
          <div style="display: flex; gap: 0.5rem;">
            <input type="number" name="vehicle_price" class="form-control" value="{{ $vehicle->getMeta('price') ?? 0 }}" min="0" step="0.01" placeholder="0">
            <select name="price_period" class="form-control" style="max-width: 100px;">
              <option value="monthly" @if($vehicle->getMeta('price_period') == 'monthly') selected @endif>Monthly</option>
              <option value="weekly" @if($vehicle->getMeta('price_period') == 'weekly' || $vehicle->getMeta('price_period') == null) selected @endif>Weekly</option>
            </select>
          </div>
          <div class="help-text">This is the price with insurance included</div>
        </div>
        
        <div class="form-field">
          <label>Insurance Discount</label>
          <input type="number" name="insurance_discount" class="form-control" value="{{ $vehicle->getMeta('insurance_discount') ?? 0 }}" min="0" step="0.01" placeholder="0">
          <div class="help-text">Discount amount when driver chooses without insurance</div>
        </div>
        
        <div class="form-field">
          <label>Select Vehicle Group</label>
          <select name="group_id" class="form-control">
            <option value="" @if($vehicle->group_id == null) selected @endif>No Group</option>
            @foreach($groups as $group)
            <option value="{{$group->id}}" @if($vehicle->group_id == $group->id) selected @endif>{{$group->name}}</option>
            @endforeach
          </select>
        </div>
        
        
        <div class="form-field">
          <label>Select Driver</label>
          <select name="driver_id" class="form-control">
            <option value="">No Driver Assigned</option>
            @foreach($drivers as $driver)
            <option value="{{$driver->id}}" @if($vehicle->getMeta('assign_driver_id') == $driver->id) selected @endif>
              {{$driver->name}}@if($driver->getMeta('is_active') != 1) (Inactive)@endif
            </option>
            @endforeach
          </select>
        </div>
        
        
        <div class="form-field">
          <label>Initial Mileage(miles)</label>
          <input type="number" name="int_mileage" class="form-control" value="{{ $vehicle->int_mileage }}" min="0" placeholder="0">
        </div>
        
        <div class="form-field">
          <label>Is Active?</label>
          <div class="toggle-switch">
            <input type="checkbox" name="in_service" id="in_service" value="1" @if($vehicle->in_service == 1) checked @endif>
            <span class="toggle-slider"></span>
          </div>
        </div>
        
        

        <!-- Additional existing fields to preserve -->
        <div class="form-field">
          <label>Initial Cost</label>
          <input type="number" name="initial_cost" class="form-control" value="{{ $vehicle->getMeta('initial_cost') }}" min="0" step="0.01" placeholder="Initial cost">
        </div>
        
        <div class="form-field">
          <label>Scheme</label>
          <select name="vehicle_scheme" class="form-control">
            <option value="Rental" @if($vehicle->getMeta('vehicle_scheme') == 'Rental' || $vehicle->getMeta('vehicle_scheme') == null) selected @endif>Rental</option>
            <option value="Rent To Buy" @if($vehicle->getMeta('vehicle_scheme') == 'Rent To Buy') selected @endif>Rent To Buy</option>
            <option value="Other" @if($vehicle->getMeta('vehicle_scheme') == 'Other') selected @endif>Other</option>
          </select>
        </div>
        
        <div class="form-field">
          <label>Vehicle Status</label>
          <select name="vehicle_status" class="form-control">
            <option value="Available" @if($vehicle->getMeta('vehicle_status') == 'Available' || $vehicle->getMeta('vehicle_status') == null) selected @endif>Available</option>
            <option value="Rented" @if($vehicle->getMeta('vehicle_status') == 'Rented') selected @endif>Rented</option>
            <option value="Workshop" @if($vehicle->getMeta('vehicle_status') == 'Workshop') selected @endif>Workshop</option>
            <option value="Disabled" @if($vehicle->getMeta('vehicle_status') == 'Disabled') selected @endif>Disabled</option>
          </select>
        </div>
        
        <div class="form-field">
          <label>Telematics Link</label>
          <input type="url" name="telematics_link" class="form-control" value="{{ $vehicle->getMeta('telematics_link') }}" placeholder="Telematics dashboard URL">
        </div>
        
        <div class="form-field">
          <label>MOT Expiry Date</label>
          <div class="date-dropdowns">
            @php
                $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: $vehicle->getMeta('exp_date') ?: $vehicle->lic_exp_date;
            @endphp
            <select name="mot_exp_day" class="form-control" style="width: 80px; display: inline-block;">
              <option value="">DD</option>
              @for($i = 1; $i <= 31; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" @if($motExpiryDate && date('d', strtotime($motExpiryDate)) == str_pad($i, 2, '0', STR_PAD_LEFT)) selected @endif>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
              @endfor
            </select>
            <select name="mot_exp_month" class="form-control" style="width: 80px; display: inline-block; margin-left: 5px;">
              <option value="">MM</option>
              @for($i = 1; $i <= 12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" @if($motExpiryDate && date('m', strtotime($motExpiryDate)) == str_pad($i, 2, '0', STR_PAD_LEFT)) selected @endif>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
              @endfor
            </select>
            <select name="mot_exp_year" class="form-control" style="width: 80px; display: inline-block; margin-left: 5px;">
              <option value="">YY</option>
              @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                <option value="{{ substr($i, -2) }}" @if($motExpiryDate && date('y', strtotime($motExpiryDate)) == substr($i, -2)) selected @endif>{{ substr($i, -2) }}</option>
              @endfor
            </select>
          </div>
          <div class="help-text">MOT test expiration date</div>
        </div>
        
      </div>
      
      <!-- User Defined Fields Section -->
      <div class="user-defined-section">
        <h6 style="margin-bottom: 1rem; color: #333; font-weight: 600;">Add User defined field</h6>
        <div id="user-defined-fields">
          @if($udfs != null)
            @foreach($udfs as $key => $value)
              <div class="user-defined-field">
                <input type="text" name="udf[{{$key}}]" value="{{$value}}" class="form-control" placeholder="{{$key}}">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeUserField(this)" style="padding: 0.5rem;">Remove</button>
              </div>
            @endforeach
          @endif
        </div>
        <div class="user-defined-field">
          <input type="text" id="new-field-name" placeholder="Field name" class="form-control">
          <input type="text" id="new-field-value" placeholder="Field value" class="form-control">
          <button type="button" class="add-field-btn" onclick="addUserField()">Add</button>
        </div>
      </div>
      
      <div class="submit-section">
        <a href="{{ route('vehicles.index') }}" class="cancel-btn">Cancel</a>
        <button type="submit" class="submit-btn">Update Vehicle</button>
      </div>
      
      {!! Form::close() !!}
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
// User defined fields functionality
let userFieldIndex = {{ $udfs ? count($udfs) : 0 }};

function addUserField() {
    const fieldName = document.getElementById('new-field-name').value;
    const fieldValue = document.getElementById('new-field-value').value;
    
    if (!fieldName.trim()) {
        alert('Please enter a field name');
        return;
    }
    
    const container = document.getElementById('user-defined-fields');
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'user-defined-field';
    fieldDiv.style.marginBottom = '0.5rem';
    
    fieldDiv.innerHTML = `
        <input type="text" name="udf[${userFieldIndex}][name]" value="${fieldName}" readonly class="form-control" style="background: #f8f9fa;">
        <input type="text" name="udf[${userFieldIndex}][value]" value="${fieldValue}" class="form-control">
        <button type="button" class="btn btn-sm btn-danger" onclick="removeUserField(this)" style="padding: 0.5rem;">Remove</button>
    `;
    
    container.appendChild(fieldDiv);
    
    // Clear input fields
    document.getElementById('new-field-name').value = '';
    document.getElementById('new-field-value').value = '';
    
    userFieldIndex++;
}

function removeUserField(button) {
    button.parentElement.remove();
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    document.getElementById('vehicleForm').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = this.querySelectorAll('[required]');
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#dc3545';
            } else {
                field.style.borderColor = '#ddd';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove validation styling on change
    const allInputs = document.querySelectorAll('input, select');
    allInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.value.trim()) {
                this.style.borderColor = '#ddd';
            }
        });
    });
});
</script>
@endsection