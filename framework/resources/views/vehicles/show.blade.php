@extends('layouts.app')
@section('extra_css')
    <style type="text/css">
        .page-header {
            background: #7FD7E1;
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page-header h1 {
            color: white;
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .vehicle-details-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .vehicle-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .vehicle-content {
            padding: 2rem;
        }
        
        .details-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        
        .section-title {
            color: #7FD7E1;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            border-bottom: 2px solid #7FD7E1;
            padding-bottom: 0.5rem;
        }
        
        .field-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .field-item {
            display: flex;
            flex-direction: column;
        }
        
        .field-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .field-value {
            color: #212529;
            font-size: 1rem;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .purchase-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .purchase-item {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        .purchase-total {
            background: #e8f5e8;
            border: 2px solid #28a745;
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 6px;
            text-align: center;
        }
        
        .btn-toolbar {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        
        .vehicle-image {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('admin/') }}">@lang('menu.home')</a></li>
    <li class="breadcrumb-item"><a href="{{ url('admin/vehicles') }}">@lang('fleet.vehicles')</a></li>
    <li class="breadcrumb-item active">{{ $vehicle->license_plate ?? 'Vehicle Details' }}</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="page-header">
            <h1>🚗 Vehicle Details - {{ $vehicle->license_plate ?? 'N/A' }}</h1>
            <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Complete information for {{ $vehicle->make_name ?? 'Unknown' }} {{ $vehicle->model_name ?? '' }}</p>
        </div>
        
        <div class="vehicle-details-container">
            <div class="vehicle-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3 style="margin: 0; color: #495057;">{{ $vehicle->make_name ?? 'Unknown Make' }} {{ $vehicle->model_name ?? 'Unknown Model' }}</h3>
                        <p style="margin: 0.5rem 0 0 0; color: #6c757d;">
                            Vehicle ID: VEH-{{ str_pad($vehicle->id, 4, '0', STR_PAD_LEFT) }} | 
                            Registration: {{ $vehicle->license_plate ?? 'Not Set' }} |
                            Status: 
                            <span style="color: {{ $vehicle->in_service ? '#28a745' : '#dc3545' }};">
                                {{ $vehicle->in_service ? '✅ Active' : '❌ Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        @if($vehicle->vehicle_image)
                            <img src="{{ asset('uploads/' . $vehicle->vehicle_image) }}" class="vehicle-image" alt="Vehicle Image">
                        @else
                            <div style="width: 150px; height: 100px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                No Image
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="vehicle-content">
                <!-- Vehicle Creation Fields -->
                <div class="details-section">
                    <h4 class="section-title">🚗 Vehicle Creation Information</h4>
                    
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">Vehicle Make</label>
                            <div class="field-value">{{ $vehicle->make_name ?? 'Not Selected' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Model</label>
                            <div class="field-value">{{ $vehicle->model_name ?? 'Not Selected' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Type</label>
                            <div class="field-value">{{ $vehicle_type ?? 'Not Selected' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Fuel Type</label>
                            <div class="field-value">{{ $vehicle->engine_type ?? 'Not Selected' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Registration Plate</label>
                            <div class="field-value">{{ $vehicle->license_plate ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Year</label>
                            <div class="field-value">{{ $vehicle->year ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Group</label>
                            <div class="field-value">{{ $group_name ?? 'Not Selected' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Assigned Driver</label>
                            <div class="field-value">{{ $driver_name ?? 'Not Assigned' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Mileage</label>
                            <div class="field-value">
                                @if($vehicle->int_mileage)
                                    {{ number_format($vehicle->int_mileage, 2) }} miles
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Is Active?</label>
                            <div class="field-value" style="color: {{ $vehicle->in_service ? '#28a745' : '#dc3545' }};">
                                {{ $vehicle->in_service ? '✅ Yes' : '❌ No' }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Scheme</label>
                            <div class="field-value">{{ $additional_meta['scheme'] ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Status</label>
                            <div class="field-value">{{ $vehicle->in_service ? 'Active' : 'Inactive' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Telematics Link</label>
                            <div class="field-value">
                                @if(isset($additional_meta['telematics_link']) && $additional_meta['telematics_link'])
                                    <a href="{{ $additional_meta['telematics_link'] }}" target="_blank" style="color: #7FD7E1;">View Link</a>
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase & Pricing Information -->
                @if(!empty($purchase_info))
                <div class="details-section">
                    <h4 class="section-title">💰 Purchase & Pricing Information</h4>
                    
                    <div class="purchase-items">
                        @php
                            $totalPrice = 0;
                            $initialCost = 0;
                        @endphp
                        
                        @foreach($purchase_info as $item)
                            @php
                                $amount = floatval($item['exp_amount'] ?? 0);
                                $totalPrice += $amount;
                                
                                if (stripos($item['exp_name'] ?? '', 'initial') !== false) {
                                    $initialCost = $amount;
                                }
                            @endphp
                            
                            <div class="purchase-item">
                                <strong>{{ $item['exp_name'] ?? 'Unknown Item' }}</strong>
                                <div style="font-size: 1.1em; color: #28a745; font-weight: bold; margin-top: 0.5rem;">
                                    {{ Hyvikk::get('currency') }} {{ number_format($amount, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="field-group" style="margin-top: 1.5rem;">
                        <div class="field-item">
                            <label class="field-label">Price</label>
                            <div class="field-value" style="font-weight: bold; color: #28a745;">
                                {{ Hyvikk::get('currency') }} {{ number_format($totalPrice, 2) }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Cost</label>
                            <div class="field-value" style="font-weight: bold; color: #17a2b8;">
                                {{ Hyvikk::get('currency') }} {{ number_format($initialCost, 2) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="purchase-total">
                        <h5 style="margin: 0; color: #28a745;">
                            Total Acquisition Cost: {{ Hyvikk::get('currency') }} {{ number_format($totalPrice, 2) }}
                        </h5>
                    </div>
                </div>
                @else
                <div class="details-section">
                    <h4 class="section-title">💰 Purchase & Pricing Information</h4>
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">Price</label>
                            <div class="field-value">Not Set</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Cost</label>
                            <div class="field-value">Not Set</div>
                        </div>
                    </div>
                    <div style="background: #fff3cd; padding: 1rem; border-radius: 6px; border-left: 4px solid #ffc107; margin-top: 1rem;">
                        <p style="margin: 0; color: #856404;"><strong>💰 Purchase Information:</strong> No acquisition costs recorded for this vehicle.</p>
                    </div>
                </div>
                @endif
                
                <!-- Technical Specifications -->
                <div class="details-section">
                    <h4 class="section-title">🔧 Technical Specifications</h4>
                    
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">Engine Type</label>
                            <div class="field-value">{{ $vehicle->engine_type ?? 'Not Specified' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Horse Power</label>
                            <div class="field-value">{{ $vehicle->horse_power ?? 'Not Specified' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Color</label>
                            <div class="field-value">{{ $vehicle->color_name ?? 'Not Specified' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">VIN Number</label>
                            <div class="field-value">{{ $vehicle->vin ?? 'Not Available' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Current Mileage</label>
                            <div class="field-value">
                                @if($vehicle->mileage)
                                    {{ number_format($vehicle->mileage, 2) }} miles
                                @else
                                    Not Recorded
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Insurance Number</label>
                            <div class="field-value">{{ $vehicle->insurance_number ?? 'Not Available' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Metadata -->
                @if(!empty($additional_meta))
                <div class="details-section">
                    <h4 class="section-title">📋 Additional Information</h4>
                    
                    <div class="field-group">
                        @foreach($additional_meta as $key => $value)
                            @if($value && $key !== 'scheme' && $key !== 'telematics_link')
                            <div class="field-item">
                                <label class="field-label">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                <div class="field-value">{{ $value }}</div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Important Dates -->
                <div class="details-section">
                    <h4 class="section-title">📅 Important Dates</h4>
                    
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">Registration Expiry</label>
                            <div class="field-value">
                                @if($vehicle->reg_exp_date)
                                    {{ \Carbon\Carbon::parse($vehicle->reg_exp_date)->format('M d, Y') }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">License Expiry</label>
                            <div class="field-value">
                                @if($vehicle->lic_exp_date)
                                    {{ \Carbon\Carbon::parse($vehicle->lic_exp_date)->format('M d, Y') }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Insurance Expiry</label>
                            <div class="field-value">
                                @if($vehicle->exp_date)
                                    {{ \Carbon\Carbon::parse($vehicle->exp_date)->format('M d, Y') }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Created Date</label>
                            <div class="field-value">
                                {{ $vehicle->created_at ? $vehicle->created_at->format('M d, Y \a\t g:i A') : 'Not Available' }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Last Updated</label>
                            <div class="field-value">
                                {{ $vehicle->updated_at ? $vehicle->updated_at->format('M d, Y \a\t g:i A') : 'Not Available' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="btn-toolbar">
                    <a href="{{ url('admin/vehicles') }}" class="btn btn-secondary">
                        ← Back to Vehicles
                    </a>
                    
                    <a href="{{ url('admin/vehicles/' . $vehicle->id . '/edit') }}" class="btn btn-warning">
                        ✏️ Edit Vehicle
                    </a>
                    
                    @if($vehicle->in_service)
                        <a href="{{ url('admin/vehicles/disable/' . $vehicle->id) }}" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to disable this vehicle?')">
                            ❌ Disable Vehicle
                        </a>
                    @else
                        <a href="{{ url('admin/vehicles/enable/' . $vehicle->id) }}" class="btn btn-outline-success" onclick="return confirm('Are you sure you want to enable this vehicle?')">
                            ✅ Enable Vehicle
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection