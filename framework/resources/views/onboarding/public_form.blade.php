<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Onboarding - {{ config('app.name', 'Fleet Manager') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .onboarding-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        
        .onboarding-header {
            background: #7FD7E1;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .onboarding-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .onboarding-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .onboarding-body {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h5 {
            color: #333;
            border-bottom: 2px solid #7FD7E1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }
        
        .required-label:after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #7FD7E1;
            box-shadow: 0 0 0 0.2rem rgba(127, 215, 225, 0.25);
        }
        
        .file-upload-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-input {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .file-upload-label:hover {
            border-color: #7FD7E1;
            background: #f0f9fa;
        }
        
        .file-upload-label i {
            margin-right: 10px;
            color: #7FD7E1;
        }
        
        .submit-btn {
            background: #7FD7E1;
            border: none;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 50px;
            font-weight: 600;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 215, 225, 0.4);
            background: #6bc5d1;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step-number {
            background: #e9ecef;
            color: #6c757d;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
            margin-bottom: 5px;
        }
        
        .step.active .step-number {
            background: #7FD7E1;
            color: white;
        }
        
        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert .btn-close {
            opacity: 0.5;
        }
        
        .alert .btn-close:hover {
            opacity: 1;
        }
        
        .document-requirements {
            background: #f8f9fa;
            border-left: 4px solid #7FD7E1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .document-requirements h6 {
            color: #7FD7E1;
            margin-bottom: 10px;
        }
        
        .document-requirements ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .document-requirements li {
            margin-bottom: 5px;
            color: #555;
        }
        
        .vehicle-selection-info {
            background: #f8f9fa;
            border-left: 4px solid #7FD7E1;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .vehicle-selection-info h6 {
            color: #7FD7E1;
            margin-bottom: 10px;
        }
        
        .vehicle-selection-info p {
            margin-bottom: 0;
            color: #555;
        }
        
        .vehicle-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .vehicle-select:focus {
            border-color: #7FD7E1;
            box-shadow: 0 0 0 0.2rem rgba(127, 215, 225, 0.25);
        }
        
        .vehicle-details .card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .vehicle-details .card-body {
            padding: 20px;
        }
        
        .vehicle-details .card-title {
            color: #7FD7E1;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .vehicle-details p {
            margin-bottom: 8px;
            color: #555;
        }
        
        .vehicle-details strong {
            color: #333;
        }
        
        /* Insurance Options Styles - Left Aligned */
        .insurance-options-left {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
        
        .insurance-options-left .form-check {
            margin-bottom: 0;
            padding: 0;
        }
        
        .insurance-options-left .form-check-input {
            margin-top: 0;
            margin-right: 12px;
            width: 18px;
            height: 18px;
            border: 2px solid #7FD7E1;
        }
        
        .insurance-options-left .form-check-input:checked {
            background-color: #7FD7E1;
            border-color: #7FD7E1;
        }
        
        .insurance-options-left .form-check-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            color: #333;
        }
        
        .insurance-options-left .form-check-label small {
            font-size: 14px;
            color: #666;
            font-weight: normal;
            margin-left: 5px;
        }
        
        .form-check-input:checked + .form-check-label {
            color: #7FD7E1;
        }
        
        .form-check-input:checked + .form-check-label small {
            color: #7FD7E1;
        }
    </style>
</head>
<body>
    <div class="onboarding-container">
        <div class="onboarding-header">
            <h1><i class="fas fa-id-card me-3"></i>Driver Onboarding</h1>
            <p>Join our fleet! Please complete the following form to begin your driver application.</p>
        </div>
        
        <div class="onboarding-body">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active">
                    <div class="step-number">1</div>
                    <div class="step-label">Personal Info</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-label">Documents</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-label">Review</div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ url('/driver-onboarding/submit') }}" method="POST" enctype="multipart/form-data" id="onboardingForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Personal Information Section -->
                <div class="form-section">
                    <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                    
                    <div class="row">
                        @foreach($fieldConfigs as $config)
                            @if($config->field_key === 'full_name')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'email')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'phone')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'license_number')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="text" class="form-control" name="license_number" value="{{ old('license_number') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'license_expiry')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="date" class="form-control" name="license_expiry" value="{{ old('license_expiry') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'address')
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <textarea class="form-control" name="address" rows="3" {{ $config->is_required ? 'required' : '' }}>{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'emergency_contact')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="text" class="form-control" name="emergency_contact" value="{{ old('emergency_contact') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'emergency_phone')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <input type="tel" class="form-control" name="emergency_phone" value="{{ old('emergency_phone') }}" {{ $config->is_required ? 'required' : '' }}>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Document Upload Section -->
                @if($fieldConfigs->where('field_key', 'license_file')->first() || $fieldConfigs->where('field_key', 'insurance_file')->first())
                <div class="form-section">
                    <h5><i class="fas fa-file-upload me-2"></i>Required Documents</h5>
                    
                    <div class="document-requirements">
                        <h6><i class="fas fa-info-circle me-2"></i>Document Requirements</h6>
                        <ul>
                            <li>All documents must be clear and legible</li>
                            <li>Accepted formats: PDF, JPG, PNG</li>
                            <li>Maximum file size: 2MB per document</li>
                            <li>Documents must be current and valid</li>
                        </ul>
                    </div>
                    
                    <div class="row">
                        @foreach($fieldConfigs as $config)
                            @if($config->field_key === 'license_file')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" class="file-upload-input" name="license_file" id="license_file" {{ $config->is_required ? 'required' : '' }} accept=".pdf,.jpg,.jpeg,.png">
                                            <label for="license_file" class="file-upload-label">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Click to upload driver's license</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @elseif($config->field_key === 'insurance_file')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" class="file-upload-input" name="insurance_file" id="insurance_file" {{ $config->is_required ? 'required' : '' }} accept=".pdf,.jpg,.jpeg,.png">
                                            <label for="insurance_file" class="file-upload-label">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Click to upload insurance certificate</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Vehicle Selection Section -->
                @if($fieldConfigs->where('field_key', 'vehicle_selection')->first())
                <div class="form-section">
                    <h5><i class="fas fa-car me-2"></i>Vehicle Selection</h5>
                    
                    <div class="vehicle-selection-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Available Vehicles</h6>
                        <p>Please select a vehicle from the available options below. Each vehicle shows make, model, year, pricing, and rental scheme information.</p>
                    </div>
                    
                    <div class="row">
                        @foreach($fieldConfigs as $config)
                            @if($config->field_key === 'vehicle_selection')
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                        <select class="form-control vehicle-select" name="vehicle_selection" id="vehicle_selection" {{ $config->is_required ? 'required' : '' }}>
                                            <option value="">Select a vehicle...</option>
                                            @foreach($availableVehicles as $vehicle)
                                                <option value="{{ $vehicle['id'] }}" 
                                                        data-fuel-type="{{ $vehicle['fuel_type'] }}"
                                                        data-price="{{ $vehicle['price'] }}" 
                                                        data-period="{{ $vehicle['price_period'] }}" 
                                                        data-scheme="{{ $vehicle['vehicle_scheme'] }}" 
                                                        data-initial-cost="{{ $vehicle['initial_cost'] }}"
                                                        data-insurance-discount="{{ $vehicle['insurance_discount'] }}"
                                                        {{ old('vehicle_selection') == $vehicle['id'] ? 'selected' : '' }}>
                                                    {{ $vehicle['display_text'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        
                                        <!-- Vehicle Details Display -->
                                        <div id="vehicle-details" class="vehicle-details" style="display: none; margin-top: -17px;">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Selected Vehicle Details</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Fuel Type:</strong> <span id="vehicle-fuel-type">-</span></p>
                                                            <p><strong>Price:</strong> <span id="vehicle-price">-</span></p>
                                                            <p><strong>Period:</strong> <span id="vehicle-period">-</span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Scheme:</strong> <span id="vehicle-scheme">-</span></p>
                                                            <p><strong>Initial Cost:</strong> <span id="vehicle-initial-cost">-</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Insurance Selection Section -->
                <div class="form-section">
                    <h5><i class="fas fa-shield-alt me-2"></i>Insurance Selection</h5>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label required-label">Choose your insurance option:</label>
                                
                                <div class="insurance-options-left">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="insurance_selection" id="with_insurance" value="with_insurance" {{ old('insurance_selection') == 'with_insurance' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="with_insurance">
                                            With Insurance <small>(Full coverage included)</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="insurance_selection" id="without_insurance" value="without_insurance" {{ old('insurance_selection') == 'without_insurance' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="without_insurance">
                                            Without Insurance <small>(No insurance coverage)</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scheme Selection Section -->
                @if($fieldConfigs->where('field_key', 'scheme_selection')->first())
                <div class="row">
                    @foreach($fieldConfigs as $config)
                        @if($config->field_key === 'scheme_selection')
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label {{ $config->is_required ? 'required-label' : '' }}">{{ $config->field_label }}</label>
                                    <select class="form-control vehicle-select" name="scheme_selection" id="scheme_selection" {{ $config->is_required ? 'required' : '' }}>
                                        <option value="">Select a scheme...</option>
                                        <option value="Rental" {{ old('scheme_selection') == 'Rental' ? 'selected' : '' }}>Rental</option>
                                        <option value="Rent to Buy" {{ old('scheme_selection') == 'Rent to Buy' ? 'selected' : '' }}>Rent to Buy</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @endif

                <!-- Custom Fields Section -->
                @if($custom_fields->count() > 0)
                <div class="form-section">
                    <h5><i class="fas fa-list-alt me-2"></i>Additional Information</h5>
                    
                    @foreach($custom_fields as $field)
                        <div class="form-group">
                            <label class="form-label {{ $field->is_required ? 'required-label' : '' }}">
                                {{ $field->field_name }}
                            </label>
                            
                            @switch($field->field_type)
                                @case('text')
                                    <input type="text" class="form-control" 
                                           name="custom_{{ $field->id }}" 
                                           value="{{ old('custom_' . $field->id) }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                    @break
                                
                                @case('email')
                                    <input type="email" class="form-control" 
                                           name="custom_{{ $field->id }}" 
                                           value="{{ old('custom_' . $field->id) }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                    @break
                                
                                @case('phone')
                                    <input type="tel" class="form-control" 
                                           name="custom_{{ $field->id }}" 
                                           value="{{ old('custom_' . $field->id) }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                    @break
                                
                                @case('date')
                                    <input type="date" class="form-control" 
                                           name="custom_{{ $field->id }}" 
                                           value="{{ old('custom_' . $field->id) }}"
                                           {{ $field->is_required ? 'required' : '' }}>
                                    @break
                                
                                @case('textarea')
                                    <textarea class="form-control" 
                                              name="custom_{{ $field->id }}" 
                                              rows="3"
                                              {{ $field->is_required ? 'required' : '' }}>{{ old('custom_' . $field->id) }}</textarea>
                                    @break
                                
                                @case('dropdown')
                                    <select class="form-select" 
                                            name="custom_{{ $field->id }}"
                                            {{ $field->is_required ? 'required' : '' }}>
                                        <option value="">Choose an option</option>
                                        @foreach($field->getDropdownOptions() as $option)
                                            <option value="{{ $option }}" 
                                                    {{ old('custom_' . $field->id) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @break
                                
                                @case('file')
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="file-upload-input" 
                                               name="custom_{{ $field->id }}" 
                                               id="custom_{{ $field->id }}"
                                               {{ $field->is_required ? 'required' : '' }}>
                                        <label for="custom_{{ $field->id }}" class="file-upload-label">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <span>Click to upload {{ strtolower($field->field_name) }}</span>
                                        </label>
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    @endforeach
                </div>
                @endif

                <!-- Terms and Conditions -->
                <div class="form-section">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" target="_blank">Terms and Conditions</a> and <a href="#" target="_blank">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane me-2"></i>Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // File upload label update
        document.querySelectorAll('.file-upload-input').forEach(function(input) {
            input.addEventListener('change', function() {
                var label = this.nextElementSibling;
                var fileName = this.files[0] ? this.files[0].name : label.querySelector('span').dataset.original;
                
                if (!label.querySelector('span').dataset.original) {
                    label.querySelector('span').dataset.original = label.querySelector('span').textContent;
                }
                
                if (this.files[0]) {
                    label.querySelector('span').textContent = fileName;
                    label.style.borderColor = '#28a745';
                    label.style.backgroundColor = '#d4edda';
                } else {
                    label.querySelector('span').textContent = label.querySelector('span').dataset.original;
                    label.style.borderColor = '#ccc';
                    label.style.backgroundColor = '#f8f9fa';
                }
            });
        });

        // Vehicle selection change handler
        document.getElementById('vehicle_selection').addEventListener('change', function() {
            var detailsDiv = document.getElementById('vehicle-details');
            
            // Always hide details regardless of selection
            detailsDiv.style.display = 'none';
        });

        // Insurance selection change handler for radio buttons
        function updateInsuranceSelection() {
            var selectedOption = document.querySelector('input[name="insurance_selection"]:checked');
            // No pricing updates needed - just selection tracking
        }

        // Add event listeners to radio buttons
        document.querySelectorAll('input[name="insurance_selection"]').forEach(function(radio) {
            radio.addEventListener('change', updateInsuranceSelection);
        });


        // Form submission with confirmation and loading state
        document.getElementById('onboardingForm').addEventListener('submit', function(e) {
            // Show confirmation dialog
            if (!confirm('Are you sure you want to submit your application? Please make sure all information is correct before proceeding.')) {
                e.preventDefault();
                return false;
            }
            
            var submitBtn = document.querySelector('.submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>