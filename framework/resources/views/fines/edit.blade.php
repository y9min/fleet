@extends('layouts.app')
@section('extra_css')
<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* Reset and base styles */
* {
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #333;
    background-color: #f4f6f9;
}

/* Content wrapper */
.content-wrapper {
    background-color: #f4f6f9;
    min-height: 100vh;
}

/* Header styles */
.content-header {
    padding: 20px 15px;
    background: transparent;
}

.header-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px 25px;
    margin-bottom: 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.content-header h1 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

/* Main content */
.content {
    padding: 0 15px 20px;
}

.main-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.card-body {
    padding: 20px;
}

/* Form styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #555;
}

.form-control {
    display: block;
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}

.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 5px;
    font-size: 12px;
    color: #dc3545;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 16px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
}

/* Row layout */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0 10px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 0 10px;
}

.col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
    padding: 0 10px;
}

.col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding: 0 10px;
}

/* File upload */
.file-upload-wrapper {
    position: relative;
    display: inline-block;
    cursor: pointer;
    width: 100%;
}

.file-upload-input {
    position: absolute;
    left: -9999px;
}

.file-upload-label {
    display: block;
    padding: 8px 12px;
    background-color: #f8f9fa;
    border: 1px solid #ccc;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}

.file-upload-label:hover {
    background-color: #e9ecef;
}

.file-upload-label i {
    margin-right: 5px;
}

/* Current file display */
.current-file {
    background-color: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 8px 12px;
    margin-top: 5px;
    font-size: 13px;
}

.current-file a {
    color: #0066cc;
    text-decoration: none;
}

.current-file a:hover {
    text-decoration: underline;
}

/* Calculation display */
.calculation-display {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    margin-top: 10px;
    font-size: 13px;
}

.calculation-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.calculation-total {
    font-weight: 600;
    border-top: 1px solid #dee2e6;
    padding-top: 5px;
    margin-top: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .col-md-6,
    .col-md-4,
    .col-md-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .row {
        margin: 0;
    }
    
    .col-md-6,
    .col-md-4,
    .col-md-3,
    .col-md-12 {
        padding: 0;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-card">
            <div class="header-content">
                <h1><i class="fas fa-edit"></i> Edit Fine</h1>
                <div>
                    <a href="{{ route('fines.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Fines
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="main-card">
            <div class="card-header">
                <h3 class="card-title">Fine Details</h3>
            </div>
            
            <div class="card-body">
                <form action="{{ route('fines.update', $fine->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fine_type">Fine Type *</label>
                                <select name="fine_type" id="fine_type" class="form-control @error('fine_type') is-invalid @enderror" required>
                                    <option value="">Select Fine Type</option>
                                    @foreach($fine_types as $category => $types)
                                        <optgroup label="{{ $category }}">
                                            @foreach($types as $code => $description)
                                                <option value="{{ $code }}" {{ (old('fine_type', $fine->fine_type) == $code) ? 'selected' : '' }}>
                                                    {{ $code }} - {{ $description }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('fine_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contravention_code">Contravention Code</label>
                                <input type="text" name="contravention_code" id="contravention_code" class="form-control @error('contravention_code') is-invalid @enderror" value="{{ old('contravention_code', $fine->contravention_code) }}">
                                @error('contravention_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vehicle_id">Vehicle *</label>
                                <select name="vehicle_id" id="vehicle_id" class="form-control @error('vehicle_id') is-invalid @enderror" required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-reg="{{ $vehicle->license_plate }}" {{ (old('vehicle_id', $fine->vehicle_id) == $vehicle->id) ? 'selected' : '' }}>
                                            {{ $vehicle->make_name }} {{ $vehicle->model_name }} ({{ $vehicle->license_plate }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="driver_id">Assigned Driver</label>
                                <select name="driver_id" id="driver_id" class="form-control @error('driver_id') is-invalid @enderror">
                                    <option value="">Select Driver</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ (old('driver_id', $fine->driver_id) == $driver->id) ? 'selected' : '' }}>
                                            {{ $driver->name }} ({{ $driver->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('driver_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ (old('status', $fine->status) == 'pending') ? 'selected' : '' }}>Pending</option>
                                    <option value="notified" {{ (old('status', $fine->status) == 'notified') ? 'selected' : '' }}>Notified</option>
                                    <option value="paid" {{ (old('status', $fine->status) == 'paid') ? 'selected' : '' }}>Paid</option>
                                    <option value="disputed" {{ (old('status', $fine->status) == 'disputed') ? 'selected' : '' }}>Disputed</option>
                                    <option value="escalated" {{ (old('status', $fine->status) == 'escalated') ? 'selected' : '' }}>Escalated</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price">Fine Amount (£) *</label>
                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $fine->price) }}" step="0.01" min="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="admin_fee">Admin Fee (£)</label>
                                <input type="number" name="admin_fee" id="admin_fee" class="form-control @error('admin_fee') is-invalid @enderror" value="{{ old('admin_fee', $fine->admin_fee) }}" step="0.01" min="0">
                                @error('admin_fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_logged">Date Logged *</label>
                                <input type="datetime-local" name="date_logged" id="date_logged" class="form-control @error('date_logged') is-invalid @enderror" value="{{ old('date_logged', $fine->date_logged ? $fine->date_logged->format('Y-m-d\TH:i') : '') }}" required>
                                @error('date_logged')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount_window_days">Discount Window (days)</label>
                                <input type="number" name="discount_window_days" id="discount_window_days" class="form-control @error('discount_window_days') is-invalid @enderror" value="{{ old('discount_window_days', $fine->discount_window_days) }}" min="0">
                                @error('discount_window_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="escalation_days">Escalation Days</label>
                                <input type="number" name="escalation_days" id="escalation_days" class="form-control @error('escalation_days') is-invalid @enderror" value="{{ old('escalation_days', $fine->escalation_days) }}" min="0">
                                @error('escalation_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="escalation_multiplier">Escalation Multiplier</label>
                                <input type="number" name="escalation_multiplier" id="escalation_multiplier" class="form-control @error('escalation_multiplier') is-invalid @enderror" value="{{ old('escalation_multiplier', $fine->escalation_multiplier) }}" step="0.1" min="1">
                                @error('escalation_multiplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference_number">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" class="form-control @error('reference_number') is-invalid @enderror" value="{{ old('reference_number', $fine->reference_number) }}">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="evidence_file">Evidence File</label>
                                @if($fine->evidence_file)
                                    <div class="current-file">
                                        <i class="fas fa-file"></i> Current file: 
                                        <a href="{{ Storage::url($fine->evidence_file) }}" target="_blank">{{ basename($fine->evidence_file) }}</a>
                                    </div>
                                @endif
                                <div class="file-upload-wrapper">
                                    <input type="file" name="evidence_file" id="evidence_file" class="file-upload-input" accept=".pdf,.jpg,.jpeg,.png">
                                    <label for="evidence_file" class="file-upload-label">
                                        <i class="fas fa-upload"></i> {{ $fine->evidence_file ? 'Replace File' : 'Choose File' }} (PDF, JPG, PNG)
                                    </label>
                                </div>
                                @error('evidence_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="4">{{ old('notes', $fine->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Calculation Display -->
                    <div class="calculation-display">
                        <h6>Amount Calculation</h6>
                        <div class="calculation-item">
                            <span>Fine Amount:</span>
                            <span id="display-price">£{{ number_format($fine->price, 2) }}</span>
                        </div>
                        <div class="calculation-item">
                            <span>Admin Fee:</span>
                            <span id="display-admin-fee">£{{ number_format($fine->admin_fee, 2) }}</span>
                        </div>
                        <div class="calculation-item calculation-total">
                            <span>Total Amount:</span>
                            <span id="display-total">£{{ number_format($fine->total_amount, 2) }}</span>
                        </div>
                        @if($fine->discount_amount)
                            <div class="calculation-item">
                                <span>Discount Amount (50%):</span>
                                <span id="display-discount">£{{ number_format($fine->discount_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Fine
                        </button>
                        <a href="{{ route('fines.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
$(document).ready(function() {
    // Vehicle selection handling - no longer needed to sync with vehicle_reg field
    
    // Calculate amounts
    function calculateAmounts() {
        var price = parseFloat($('#price').val()) || 0;
        var adminFee = parseFloat($('#admin_fee').val()) || 0;
        var total = price + adminFee;
        var discount = total * 0.5;
        
        $('#display-price').text('£' + price.toFixed(2));
        $('#display-admin-fee').text('£' + adminFee.toFixed(2));
        $('#display-total').text('£' + total.toFixed(2));
        $('#display-discount').text('£' + discount.toFixed(2));
        
        // Show/hide discount display
        if ($('#discount_window_days').val()) {
            $('#discount-display').show();
        } else {
            $('#discount-display').hide();
        }
    }
    
    // Trigger calculation on input change
    $('#price, #admin_fee, #discount_window_days').on('input', calculateAmounts);
    
    // Initial calculation
    calculateAmounts();
    
    // File upload display
    $('#evidence_file').change(function() {
        var fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('.file-upload-label').html('<i class="fas fa-file"></i> ' + fileName);
        } else {
            $('.file-upload-label').html('<i class="fas fa-upload"></i> {{ $fine->evidence_file ? "Replace File" : "Choose File" }} (PDF, JPG, PNG)');
        }
    });
});
</script>
@endsection

