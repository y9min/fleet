@extends('layouts.app')
@section('extra_css')
<style type="text/css">
  .vehicle-header {
    background: #28a745;
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
  }
  
  .vehicle-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
  }
  
  .tab-container {
    border: 1px solid #e0e0e0;
    border-top: none;
    border-radius: 0 0 8px 8px;
    overflow: hidden;
  }
  
  .nav-tabs-custom {
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    margin: 0;
  }
  
  .nav-tabs-custom .nav-link {
    background: #f8f9fa;
    border: none;
    color: #495057;
    padding: 12px 20px;
    font-weight: 500;
    border-radius: 0;
  }
  
  .nav-tabs-custom .nav-link.active {
    background: #7FD7E1;
    color: white;
    border-bottom: 3px solid #5bc0de;
  }
  
  .form-container {
    padding: 30px;
    background: white;
  }
  
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
  }
  
  .form-field {
    display: flex;
    flex-direction: column;
  }
  
  .form-field label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
  }
  
  .form-field .help-icon {
    color: #7FD7E1;
    margin-left: 5px;
    font-size: 12px;
  }
  
  .form-control-modern {
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
  }
  
  .form-control-modern:focus {
    border-color: #7FD7E1;
    box-shadow: 0 0 0 2px rgba(127, 215, 225, 0.2);
    outline: none;
  }
  
  .toggle-switch {
    position: relative;
    width: 50px;
    height: 25px;
    background: #ccc;
    border-radius: 25px;
    cursor: pointer;
    transition: background 0.3s;
  }
  
  .toggle-switch.active {
    background: #7FD7E1;
  }
  
  .toggle-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 21px;
    height: 21px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s;
  }
  
  .toggle-switch.active .toggle-slider {
    transform: translateX(25px);
  }
  
  .udf-section {
    border-top: 1px solid #e0e0e0;
    padding-top: 25px;
    margin-top: 25px;
  }
  
  .udf-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }
  
  .udf-item {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 15px;
    align-items: end;
    margin-bottom: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
  }
  
  .btn-add-udf {
    background: #7FD7E1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
  }
  
  .btn-add-udf:hover {
    background: #5bc0de;
  }
  
  .btn-remove-udf {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
  }
  
  .btn-submit {
    background: #28a745;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 20px;
  }
  
  .btn-submit:hover {
    background: #218838;
  }
  
  .required {
    color: #dc3545;
  }
  
  @media (max-width: 768px) {
    .form-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    
    .udf-item {
      grid-template-columns: 1fr;
      gap: 10px;
    }
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

  <div class="vehicle-header">
    <h3><i class="fa fa-plus"></i> Add Vehicle</h3>
  </div>
  
  <div class="tab-container">
    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#general-info" role="tab">
          General Information
        </a>
      </li>
    </ul>
    
    <div class="tab-content">
      <div class="tab-pane fade show active" id="general-info" role="tabpanel">
        <div class="form-container">
          {!! Form::open(['route' => 'vehicles.store', 'method' => 'post', 'class' => 'form-horizontal', 'id' => 'vehicleForm', 'enctype' => 'multipart/form-data']) !!}
          {!! Form::hidden('user_id', Auth::user()->id) !!}
          
          <div class="form-grid">
            <!-- Select Vehicle Make -->
            <div class="form-field">
              <label>Select Vehicle Make <i class="fa fa-question-circle help-icon" title="Vehicle manufacturer"></i></label>
              <input type="text" name="make_name" class="form-control-modern" 
                     placeholder="Select Vehicle Make" value="{{ old('make_name') }}" required>
            </div>
            
            
            <!-- Select Vehicle Model -->
            <div class="form-field">
              <label>Select Vehicle Model <i class="fa fa-question-circle help-icon" title="Vehicle model"></i></label>
              <input type="text" name="model_name" class="form-control-modern" 
                     placeholder="Select Vehicle Model" value="{{ old('model_name') }}" required>
            </div>
            
            <!-- Vehicle Type -->
            <div class="form-field">
              <label>Vehicle Type</label>
              <select name="type_id" class="form-control-modern">
                <option value="">Vehicle Type</option>
                @foreach($types as $type)
                <option value="{{$type->id}}" @if(old('type_id')==$type->id) selected @endif>{{$type->displayname}}</option>
                @endforeach
              </select>
            </div>
            
            
            
            
            <!-- License Plate -->
            <div class="form-field">
              <label>License Plate</label>
              <input type="text" name="license_plate" class="form-control-modern" 
                     placeholder="" value="{{ old('license_plate') }}" required>
            </div>
            
            
            <!-- Vehicle Year -->
            <div class="form-field">
              <label>Vehicle Year</label>
              <input type="number" name="year" class="form-control-modern" 
                     placeholder="" value="{{ old('year') }}" min="1900" max="2030">
            </div>
            
            <!-- Price -->
            <div class="form-field">
              <label>Price</label>
              <input type="number" name="vehicle_price" class="form-control-modern" 
                     placeholder="" value="{{ old('vehicle_price') }}" step="0.01">
            </div>
            
            <!-- Select Vehicle Group -->
            <div class="form-field">
              <label>Select Vehicle Group</label>
              <select name="group_id" class="form-control-modern">
                <option value="">Default</option>
                @foreach($groups as $group)
                <option value="{{$group->id}}" @if(old('group_id')==$group->id) selected @endif>{{$group->name}}</option>
                @endforeach
              </select>
            </div>
            
            
            <!-- Select Driver -->
            <div class="form-field">
              <label>Select Driver</label>
              <select name="driver_id" class="form-control-modern">
                <option value="">Mariah Bahringer ( Inactive )</option>
                @foreach($drivers as $driver)
                <option value="{{$driver->id}}" @if(old('driver_id')==$driver->id) selected @endif>
                  {{$driver->name}}@if($driver->getMeta('is_active') != 1) ( Inactive )@endif
                </option>
                @endforeach
              </select>
            </div>
            
            
            <!-- Initial Mileage(km) -->
            <div class="form-field">
              <label>Initial Mileage(km)</label>
              <input type="number" name="int_mileage" class="form-control-modern" 
                     placeholder="" value="{{ old('int_mileage') }}">
            </div>
            
            
            
            
            
          </div>
          
          <!-- User Defined Fields -->
          <div class="udf-section">
            <div class="udf-header">
              <h4 style="margin: 0; color: #495057;">Add User defined field</h4>
              <button type="button" class="btn-add-udf" onclick="addUDFField()">Add</button>
            </div>
            
            <div id="udf-container">
              <!-- Dynamic UDF fields will be added here -->
            </div>
          </div>
          
          <div style="text-align: left; margin-top: 30px;">
            <button type="submit" class="btn-submit">Submit</button>
          </div>
          
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
// Toggle switch functionality
function toggleActive(element) {
    element.classList.toggle('active');
    const input = element.querySelector('input[name="in_service"]');
    input.value = element.classList.contains('active') ? '1' : '0';
}

// User Defined Fields functionality
let udfCounter = 0;

function addUDFField() {
    udfCounter++;
    const container = document.getElementById('udf-container');
    const udfItem = document.createElement('div');
    udfItem.className = 'udf-item';
    udfItem.innerHTML = `
        <div class="form-field">
            <label>Field Name</label>
            <input type="text" name="udf[${udfCounter}][label]" class="form-control-modern" 
                   placeholder="Enter field name" required>
        </div>
        <div class="form-field">
            <label>Field Value</label>
            <input type="text" name="udf[${udfCounter}][value]" class="form-control-modern" 
                   placeholder="Enter field value">
        </div>
        <button type="button" class="btn-remove-udf" onclick="removeUDFField(this)">
            <i class="fa fa-trash"></i>
        </button>
    `;
    container.appendChild(udfItem);
}

function removeUDFField(button) {
    button.parentElement.remove();
}

// Initialize with one UDF field if needed
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('vehicleForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '#e0e0e0';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
    
    // Remove validation styling on input
    const allInputs = document.querySelectorAll('.form-control-modern');
    allInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.style.borderColor = '#e0e0e0';
            }
        });
    });
    
    // Set default toggle state
    const toggleSwitch = document.querySelector('.toggle-switch');
    if (toggleSwitch) {
        toggleSwitch.classList.add('active'); // Default to active
    }
});
</script>
@endsection