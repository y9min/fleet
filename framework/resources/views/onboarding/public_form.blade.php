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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .onboarding-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .onboarding-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-bottom: 2px solid #667eea;
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
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
            border-color: #667eea;
            background: #e3f2fd;
        }
        
        .file-upload-label i {
            margin-right: 10px;
            color: #667eea;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
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
            background: #667eea;
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
        }
        
        .document-requirements {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .document-requirements h6 {
            color: #667eea;
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Driver's License Number</label>
                                <input type="text" class="form-control" name="license_number" value="{{ old('license_number') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Upload Section -->
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Driver's License</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="license_upload" id="license_upload" required accept=".pdf,.jpg,.jpeg,.png">
                                    <label for="license_upload" class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Click to upload driver's license</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required-label">Insurance Certificate</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" class="file-upload-input" name="insurance_upload" id="insurance_upload" required accept=".pdf,.jpg,.jpeg,.png">
                                    <label for="insurance_upload" class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Click to upload insurance certificate</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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

        // Form submission with loading state
        document.getElementById('onboardingForm').addEventListener('submit', function() {
            var submitBtn = document.querySelector('.submit-btn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>