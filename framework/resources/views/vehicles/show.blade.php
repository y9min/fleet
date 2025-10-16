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
            margin-bottom: 1rem;
        }
        
        .field-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .field-value {
            color: #212529;
            font-size: 1rem;
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
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
        <!-- New Header Layout -->
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="margin-bottom: 1rem;">
                <button onclick="goBack()" class="btn btn-secondary" style="background-color: #6c757d; border-color: #6c757d; color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </button>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h2 style="margin: 0; color: #495057; font-size: 2rem;">{{ $vehicle->make_name ?? 'Unknown Make' }} {{ $vehicle->model_name ?? 'Unknown Model' }}</h2>
                    <p style="margin: 0.5rem 0 0 0; color: #6c757d; font-size: 1.1rem;">
                        Vehicle ID: VEH-{{ str_pad($vehicle->id, 4, '0', STR_PAD_LEFT) }} | 
                        Registration: {{ $vehicle->license_plate ?? 'Not Set' }} | 
                        Status: 
                        @php
                            $vehicleStatus = $vehicle->getMeta('vehicle_status') ?: 'Available';
                        @endphp
                        @switch($vehicleStatus)
                            @case('Available')
                                <span style="color: #28a745;">✅ Available</span>
                                @break
                            @case('Rented')
                                <span style="color: #ffc107;">⚠️ Rented</span>
                                @break
                            @case('Workshop')
                                <span style="color: #17a2b8;">🔧 Workshop</span>
                                @break
                            @case('Disabled')
                                <span style="color: #6c757d;">❌ Disabled</span>
                                @break
                            @default
                                <span style="color: #28a745;">✅ Available</span>
                        @endswitch
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    @if($vehicle->vehicle_image)
                        <img src="{{ asset('uploads/' . $vehicle->vehicle_image) }}" style="max-width: 200px; max-height: 150px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" alt="Vehicle Image">
                    @else
                        <div style="width: 200px; height: 150px; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 1.1rem;">
                            No Image
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Debug Information (Remove in production) -->
        @if(config('app.debug'))
        <div style="background: #fff3cd; padding: 1rem; border-radius: 6px; border-left: 4px solid #ffc107; margin-bottom: 2rem;">
            <p style="margin: 0; color: #856404;"><strong>🔍 Debug Info:</strong> Vehicle ID: {{ $vehicle->id }} | Created: {{ $vehicle->created_at }} | Updated: {{ $vehicle->updated_at }}</p>
        </div>
        @endif
        
        <div class="vehicle-details-container">
            
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
                            <div class="field-value">
                                @if($vehicle->types && $vehicle->types->vehicletype)
                                    {{ $vehicle->types->vehicletype }}
                                @elseif($vehicle->type_id)
                                    @php
                                        // Fallback to type_id if relationship not loaded
                                        $displayVehicleType = 'Not Selected';
                                        switch($vehicle->type_id) {
                                            case 1: $displayVehicleType = 'Convertible'; break;
                                            case 2: $displayVehicleType = 'Coupe'; break;
                                            case 3: $displayVehicleType = 'Estate'; break;
                                            case 4: $displayVehicleType = 'Hatchback'; break;
                                            case 5: $displayVehicleType = 'MPV'; break;
                                            case 6: $displayVehicleType = 'Pickup'; break;
                                            case 7: $displayVehicleType = 'Saloon'; break;
                                            case 8: $displayVehicleType = 'SUV'; break;
                                            default: $displayVehicleType = 'Not Selected'; break;
                                        }
                                    @endphp
                                    {{ $displayVehicleType }}
                                @else
                                    Not Selected
                                @endif
                            </div>
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
                            <div class="field-value">
                                @php
                                    // Enhanced group name retrieval for both existing and new vehicles
                                    $displayGroupName = 'Not Selected';
                                    if ($vehicle->group_id) {
                                        if ($vehicle->group && $vehicle->group->name) {
                                            $displayGroupName = $vehicle->group->name;
                                        } else {
                                            // Fallback to direct database query for existing vehicles
                                            $group = DB::table('vehicle_group')
                                                ->where('id', $vehicle->group_id)
                                                ->where('deleted_at', null)
                                                ->first();
                                            if ($group && $group->name) {
                                                $displayGroupName = $group->name;
                                            }
                                        }
                                    }
                                @endphp
                                {{ $displayGroupName }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Assigned Driver</label>
                            <div class="field-value">
                                @php
                                    // Enhanced driver name retrieval for both existing and new vehicles
                                    $displayDriverName = 'Not Assigned';
                                    if ($vehicle->drivers && $vehicle->drivers->count() > 0) {
                                        $displayDriverName = $vehicle->drivers->first()->name;
                                    } elseif ($vehicle->getMeta('assign_driver_id')) {
                                        // Fallback for existing vehicles with driver in metadata
                                        $driverId = $vehicle->getMeta('assign_driver_id');
                                        $driver = DB::table('users')
                                            ->where('id', $driverId)
                                            ->where('deleted_at', null)
                                            ->first();
                                        if ($driver && $driver->name) {
                                            $displayDriverName = $driver->name;
                                        }
                                    }
                                @endphp
                                {{ $displayDriverName }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Mileage</label>
                            <div class="field-value">
                                @if($vehicle->int_mileage)
                                    {{ number_format((int)$vehicle->int_mileage) }} miles
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
                            <div class="field-value">
                                @php
                                    $scheme = $vehicle->getMeta('vehicle_scheme') ?: $vehicle->getMeta('scheme');
                                @endphp
                                {{ $scheme && $scheme !== '' ? $scheme : 'Not Set' }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Status</label>
                            <div class="field-value">
                                @php
                                    $vehicleStatus = $vehicle->getMeta('vehicle_status') ?: 'Available';
                                @endphp
                                @switch($vehicleStatus)
                                    @case('Available')
                                        <span class="badge badge-success">Available</span>
                                        @break
                                    @case('Rented')
                                        <span class="badge badge-warning">Rented</span>
                                        @break
                                    @case('Workshop')
                                        <span class="badge badge-info">Workshop</span>
                                        @break
                                    @case('Disabled')
                                        <span class="badge badge-secondary">Disabled</span>
                                        @break
                                    @default
                                        <span class="badge badge-success">Available</span>
                                @endswitch
                            </div>
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
                        
                        <!-- Additional comprehensive fields -->
                        <div class="field-item">
                            <label class="field-label">Vehicle ID</label>
                            <div class="field-value">{{ $vehicle->id }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Group ID</label>
                            <div class="field-value">{{ $vehicle->group_id ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Type ID</label>
                            <div class="field-value">{{ $vehicle->type_id ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">User ID</label>
                            <div class="field-value">{{ $vehicle->user_id ?? 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Price Period</label>
                            <div class="field-value">{{ $vehicle->getMeta('price_period') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Status (Metadata)</label>
                            <div class="field-value">{{ $vehicle->getMeta('vehicle_status') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Luggage Capacity</label>
                            <div class="field-value">{{ $vehicle->getMeta('luggage') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Assigned Driver ID (Meta)</label>
                            <div class="field-value">{{ $vehicle->getMeta('assign_driver_id') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Dimensions (H×L×W)</label>
                            <div class="field-value">
                                @php
                                    $height = $vehicle->height ?? 'N/A';
                                    $length = $vehicle->length ?? 'N/A';
                                    $breadth = $vehicle->breadth ?? 'N/A';
                                @endphp
                                {{ $height }} × {{ $length }} × {{ $breadth }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Weight</label>
                            <div class="field-value">{{ $vehicle->weight ? $vehicle->weight . ' kg' : 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Average Consumption</label>
                            <div class="field-value">{{ $vehicle->average ? $vehicle->average . ' L/100km' : 'Not Set' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase & Pricing Information -->
                <div class="details-section">
                    <h4 class="section-title">💰 Purchase & Pricing Information</h4>
                    
                    <div class="field-group">
                        @php
                            // Enhanced price information retrieval for both existing and new vehicles
                            $vehiclePrice = $vehicle->getMeta('vehicle_price') ?: $vehicle->getMeta('price');
                            $pricePeriod = $vehicle->getMeta('price_period') ?: 'weekly';
                            $initialCost = $vehicle->getMeta('initial_cost');
                            $totalPrice = 0;
                            
                            // For existing vehicles, try to extract from legacy purchase_info if metadata is missing
                            if (!$vehiclePrice && !$initialCost) {
                                $legacyPurchaseInfo = $vehicle->getMeta('purchase_info');
                                if ($legacyPurchaseInfo) {
                                    try {
                                        $legacyData = json_decode($legacyPurchaseInfo, true) ?: unserialize($legacyPurchaseInfo);
                                        if (is_array($legacyData)) {
                                            foreach ($legacyData as $item) {
                                                if (isset($item['exp_name']) && isset($item['exp_amount'])) {
                                                    if (stripos($item['exp_name'], 'Price') !== false) {
                                                        $vehiclePrice = $item['exp_amount'];
                                                    } elseif (stripos($item['exp_name'], 'Initial') !== false) {
                                                        $initialCost = $item['exp_amount'];
                                                    }
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Ignore errors in legacy data parsing
                                    }
                                }
                            }
                            
                            if ($vehiclePrice && $vehiclePrice !== '' && $vehiclePrice !== '0') $totalPrice += (float)$vehiclePrice;
                            if ($initialCost && $initialCost !== '' && $initialCost !== '0') $totalPrice += (float)$initialCost;
                        @endphp
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Price ({{ $pricePeriod ?: 'monthly' }})</label>
                            <div class="field-value" style="font-weight: bold; color: #28a745;">
                                @if($vehiclePrice && $vehiclePrice !== '' && $vehiclePrice !== '0')
                                    £ {{ number_format((float)$vehiclePrice, 2) }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Insurance Discount</label>
                            <div class="field-value" style="font-weight: bold; color: #dc3545;">
                                @php
                                    $insuranceDiscount = $vehicle->getMeta('insurance_discount');
                                @endphp
                                @if($insuranceDiscount && $insuranceDiscount !== '' && $insuranceDiscount !== '0')
                                    £ {{ number_format((float)$insuranceDiscount, 2) }}
                                    <small style="display: block; color: #666; font-weight: normal;">
                                        Price without insurance: £ {{ number_format((float)$vehiclePrice - (float)$insuranceDiscount, 2) }}
                                    </small>
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Cost</label>
                            <div class="field-value" style="font-weight: bold; color: #17a2b8;">
                                @if($initialCost && $initialCost !== '' && $initialCost !== '0')
                                    £ {{ number_format((float)$initialCost, 2) }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Price ({{ $pricePeriod ?: 'monthly' }})</label>
                            <div class="field-value" style="font-weight: bold; color: #28a745;">
                                @if($vehiclePrice && $vehiclePrice !== '' && $vehiclePrice !== '0')
                                    £ {{ number_format((float)$vehiclePrice, 2) }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Initial Cost</label>
                            <div class="field-value" style="font-weight: bold; color: #17a2b8;">
                                @if($initialCost && $initialCost !== '' && $initialCost !== '0')
                                    £ {{ number_format((float)$initialCost, 2) }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($totalPrice > 0)
                    <div class="purchase-total">
                        <h5 style="margin: 0; color: #28a745;">
                            Total Acquisition Cost: £ {{ number_format($totalPrice, 2) }}
                        </h5>
                    </div>
                    @endif
                </div>
                
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
                        
                        <!-- Additional technical details -->
                        <div class="field-item">
                            <label class="field-label">Insurance Number (Meta)</label>
                            <div class="field-value">{{ $vehicle->getMeta('ins_number') ?: 'Not Available' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Insurance Expiry (Meta)</label>
                            <div class="field-value">
                                @if($vehicle->getMeta('ins_exp_date'))
                                    {{ \Carbon\Carbon::parse($vehicle->getMeta('ins_exp_date'))->format('M d, Y') }}
                                @else
                                    Not Set
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Documents</label>
                            <div class="field-value">{{ $vehicle->documents ?? 'Not Available' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Documents (Meta)</label>
                            <div class="field-value">{{ $vehicle->getMeta('documents') ?: 'Not Available' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Traccar Device ID</label>
                            <div class="field-value">{{ $vehicle->getMeta('traccar_device_id') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Traccar Vehicle ID</label>
                            <div class="field-value">{{ $vehicle->getMeta('traccar_vehicle_id') ?: 'Not Set' }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Icon/Image Metadata</label>
                            <div class="field-value">
                                @if($vehicle->getMeta('icon'))
                                    <img src="{{ asset('uploads/' . $vehicle->getMeta('icon')) }}" style="max-width: 50px; max-height: 50px; border-radius: 4px;" alt="Vehicle Icon">
                                @else
                                    No Icon Set
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Complete Metadata Section -->
                <div class="details-section">
                    <h4 class="section-title">🗂️ Complete Vehicle Metadata</h4>
                    
                    <div class="field-group">
                        @php
                            // Get ALL metadata for comprehensive display with error handling
                            try {
                                $allMetadata = DB::table('vehicles_meta')
                                    ->where('vehicle_id', $vehicle->id)
                                    ->get()
                                    ->keyBy('key');
                            } catch (Exception $e) {
                                $allMetadata = collect();
                                // Log error for debugging
                                \Log::error('Vehicle metadata query failed: ' . $e->getMessage());
                            }
                        @endphp
                        
                        @if($allMetadata->count() > 0)
                            @foreach($allMetadata as $key => $metaItem)
                                <div class="field-item">
                                    <label class="field-label">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                    <div class="field-value">
                                        @if($metaItem->value && $metaItem->value !== '')
                                            {{ $metaItem->value }}
                                            <small style="color: #6c757d; display: block; font-size: 0.8em;">
                                                Type: {{ $metaItem->type }} | Updated: {{ \Carbon\Carbon::parse($metaItem->updated_at)->format('M d, Y g:i A') }}
                                            </small>
                                        @else
                                            Empty
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="field-item">
                                <label class="field-label">Metadata Status</label>
                                <div class="field-value">
                                    ⚠️ No metadata found for this vehicle
                                    <small style="color: #6c757d; display: block; font-size: 0.8em; margin-top: 0.5rem;">
                                        This could be a new vehicle or the metadata may need repair.
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Additional Metadata (Original) -->
                @if(!empty($additional_meta))
                <div class="details-section">
                    <h4 class="section-title">📋 Additional Information (Controller Data)</h4>
                    
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
                            <label class="field-label">MOT Expiry Date</label>
                            <div class="field-value">
                                @php
                                    $motExpiryDate = $vehicle->getMeta('mot_expiry_date') ?: $vehicle->getMeta('exp_date') ?: $vehicle->lic_exp_date;
                                @endphp
                                @if($motExpiryDate)
                                    {{ \Carbon\Carbon::parse($motExpiryDate)->format('d/m/y') }}
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
                        
                        <div class="field-item">
                            <label class="field-label">Deleted At</label>
                            <div class="field-value">
                                {{ $vehicle->deleted_at ? $vehicle->deleted_at->format('M d, Y \a\t g:i A') : 'Not Deleted' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Data Status -->
                <div class="details-section">
                    <h4 class="section-title">📊 Vehicle Data Status</h4>
                    
                    <div class="field-group">
                        @php
                            // Calculate data completeness for both existing and new vehicles
                            $totalFields = 0;
                            $filledFields = 0;
                            
                            // Check main vehicle fields
                            $mainFields = ['make_name', 'model_name', 'engine_type', 'license_plate', 'year', 'group_id', 'type_id'];
                            foreach ($mainFields as $field) {
                                $totalFields++;
                                if ($vehicle->$field && $vehicle->$field !== '') {
                                    $filledFields++;
                                }
                            }
                            
                            // Check metadata fields
                            $metaFields = ['vehicle_price', 'initial_cost', 'vehicle_scheme', 'price_period'];
                            foreach ($metaFields as $field) {
                                $totalFields++;
                                if ($vehicle->getMeta($field) && $vehicle->getMeta($field) !== '') {
                                    $filledFields++;
                                }
                            }
                            
                            $completenessPercentage = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
                            $isNewVehicle = $vehicle->created_at && $vehicle->created_at->diffInDays(now()) < 7;
                        @endphp
                        
                        <div class="field-item">
                            <label class="field-label">Data Completeness</label>
                            <div class="field-value">
                                {{ $filledFields }} / {{ $totalFields }} fields filled ({{ $completenessPercentage }}%)
                                <div style="width: 100%; background: #e9ecef; border-radius: 4px; margin-top: 0.5rem;">
                                    <div style="width: {{ $completenessPercentage }}%; background: {{ $completenessPercentage >= 80 ? '#28a745' : ($completenessPercentage >= 60 ? '#ffc107' : '#dc3545') }}; height: 8px; border-radius: 4px;"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Vehicle Age</label>
                            <div class="field-value">
                                @if($isNewVehicle)
                                    🆕 New Vehicle ({{ $vehicle->created_at->diffInDays(now()) }} days old)
                                @else
                                    📅 Existing Vehicle ({{ $vehicle->created_at->diffInDays(now()) }} days old)
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Metadata Status</label>
                            <div class="field-value">
                                @if($allMetadata->count() > 0)
                                    ✅ {{ $allMetadata->count() }} metadata records found
                                @else
                                    ⚠️ No metadata found - may need repair
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Data Quality</label>
                            <div class="field-value">
                                @if($completenessPercentage >= 80)
                                    ✅ Excellent
                                @elseif($completenessPercentage >= 60)
                                    ⚠️ Good
                                @else
                                    ❌ Needs Attention
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Database & System Information -->
                <div class="details-section">
                    <h4 class="section-title">💾 Database & System Information</h4>
                    
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">Primary Key (ID)</label>
                            <div class="field-value">{{ $vehicle->id }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Table Name</label>
                            <div class="field-value">{{ $vehicle->getTable() }}</div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Fillable Fields</label>
                            <div class="field-value">
                                <small style="color: #6c757d;">{{ implode(', ', $vehicle->getFillable()) }}</small>
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Has Metadata</label>
                            <div class="field-value">
                                @if($allMetadata->count() > 0)
                                    ✅ Yes ({{ $allMetadata->count() }} records)
                                @else
                                    ❌ No metadata found
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">UDF (User Defined Fields)</label>
                            <div class="field-value">
                                @if($vehicle->udf)
                                    {{ $vehicle->udf }}
                                @else
                                    No UDF data
                                @endif
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Relationships Loaded</label>
                            <div class="field-value">
                                @php
                                    $relations = $vehicle->getRelations();
                                    $relationNames = array_keys($relations);
                                @endphp
                                @if(count($relationNames) > 0)
                                    {{ implode(', ', $relationNames) }}
                                @else
                                    No relationships loaded
                                @endif
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
                    
                    <a href="{{ url('admin/vehicles/repair-metadata/' . $vehicle->id) }}" class="btn btn-info" onclick="return confirm('This will attempt to repair any missing price/initial cost metadata. Continue?')">
                        🔧 Repair Metadata
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
                
                <!-- Error Handling Section -->
                @if(config('app.debug'))
                <div class="details-section" style="background: #f8d7da; border: 1px solid #f5c6cb;">
                    <h4 class="section-title" style="color: #721c24;">🐛 Debug Information</h4>
                    <div class="field-group">
                        <div class="field-item">
                            <label class="field-label">View Status</label>
                            <div class="field-value" style="color: #721c24;">
                                ✅ Vehicle details view loaded successfully
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">Data Sources</label>
                            <div class="field-value" style="color: #721c24;">
                                Main Table: ✅ | Metadata: {{ $allMetadata->count() > 0 ? '✅' : '⚠️' }} | Relationships: {{ $vehicle->getRelations() ? '✅' : '⚠️' }}
                            </div>
                        </div>
                        
                        <div class="field-item">
                            <label class="field-label">View Sections</label>
                            <div class="field-value" style="color: #721c24;">
                                All sections loaded: ✅ Vehicle Info | ✅ Pricing | ✅ Technical | ✅ Metadata | ✅ Dates | ✅ System Info
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function goBack() {
    if (document.referrer && document.referrer.includes(window.location.origin)) {
        if (document.referrer.includes('/admin/drivers')) {
            window.location.href = document.referrer;
        } else if (document.referrer.includes('/admin/vehicles') && !document.referrer.includes('/admin/vehicles/' + {{ $vehicle->id }})) {
            window.location.href = document.referrer;
        } else {
            window.history.back();
        }
    } else {
        window.location.href = '{{ route("vehicles.index") }}';
    }
}
</script>
@endsection