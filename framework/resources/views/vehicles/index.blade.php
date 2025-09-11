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
        
        .btn-toolbar {
            margin-bottom: 1rem;
        }
        
        .vehicles-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
        }
        
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .vehicle-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .modal {
            overflow: auto;
            overflow-y: hidden;
        }

        .custom_padding {
            padding: .3rem !important;
        }

        .checkbox,
        #chk_all {
            width: 20px;
            height: 20px;
        }

        #loader {
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            color: #555;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #7FD7E1 !important;
            border: 1px solid #7FD7E1 !important;
            color: white !important;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #6BC5D2 !important;
            border: 1px solid #6BC5D2 !important;
            color: white !important;
        }
        
        /* Enhanced Modal Styling */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .modal-header h4 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modal-header .close {
            color: white;
            opacity: 0.8;
            font-size: 1.5rem;
            text-shadow: none;
        }
        
        .modal-header .close:hover {
            opacity: 1;
            color: white;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .modal-footer {
            background: #f8f9fa;
            padding: 1.5rem;
            border-top: 1px solid #eee;
            border-radius: 0 0 12px 12px;
        }
        
        /* Import Modal Specific */
        .file-upload-section {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .file-upload-section:hover {
            border-color: #7FD7E1;
            background: #f0fdff;
        }
        
        .file-upload-section.dragover {
            border-color: #7FD7E1;
            background: #e8f8fa;
            transform: scale(1.02);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #7FD7E1;
            margin-bottom: 1rem;
        }
        
        .upload-text {
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .upload-hint {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .form-check-custom {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .form-check-custom:hover {
            border-color: #7FD7E1;
            background: #f8fdff;
        }
        
        .form-check-custom .form-check-input:checked {
            background-color: #7FD7E1;
            border-color: #7FD7E1;
        }
        
        .info-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .info-card-header {
            background: #7FD7E1;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 6px 6px 0 0;
            margin: -1.5rem -1.5rem 1rem -1.5rem;
            font-weight: 600;
        }
        
        .required-column {
            color: #dc3545;
            font-weight: 600;
        }
        
        .optional-column {
            color: #6c757d;
        }
        
        /* Enhanced Alert */
        .alert-info-custom {
            background: linear-gradient(135deg, #e3f2fd, #f0f9ff);
            border: 1px solid #7FD7E1;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .alert-info-custom h6 {
            color: #0277bd;
            margin-bottom: 1rem;
        }
        
        /* Button Enhancements */
        .btn-primary-custom {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #6BC5D2, #5BB0BD);
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-danger-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #c82333, #bd2130);
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #7FD7E1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .progress-bar-custom {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            border-radius: 4px;
        }
        
        /* Bulk Actions Toolbar */
        .bulk-actions-toolbar {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Enhanced Table Styling */
        .custom-control {
            padding-left: 1.5rem;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Dropdown Styling */
        .dropdown-menu {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item.text-danger:hover {
            background-color: #f5c6cb;
            color: #721c24 !important;
        }
        
        .delete-confirmation {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 0.5rem;
            margin: 0.25rem 0;
            font-size: 0.85rem;
        }
        
        .delete-warning-icon {
            color: #f39c12;
            margin-right: 0.25rem;
        }
        
        .confirm-delete-row {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
        
        .vehicle-details {
            font-size: 0.9rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            animation: slideDown 0.3s ease-out;
        }
        
        .vehicle-details strong {
            color: #495057;
        }
        
        .details-section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .details-section h6 {
            color: #7FD7E1;
            border-bottom: 2px solid #7FD7E1;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .details-expanded {
            background-color: #f0fdff !important;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        /* Checkbox Enhancements */
        .table .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #7FD7E1;
            border-color: #7FD7E1;
        }
        
        .row-selected {
            background-color: #f0fdff !important;
        }
    </style>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('fleet.vehicles')</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Success Message -->
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1>@lang('fleet.manageVehicles')</h1>
            <div class="d-flex gap-2">
                @can('Vehicles add')
                    <a href="{{ route('vehicles.create') }}" class="btn" style="background: #7FD7E1; color: white; border-radius: 6px; padding: 0.6rem 1.2rem; margin-right: 8px;" title="Add Vehicle">
                        <i class="fa fa-plus"></i> Add Vehicle
                    </a>
                @endcan
                @can('Vehicles import')
                    <button class="btn" style="background: #6BC5D2; color: white; border-radius: 6px; padding: 0.6rem 1.2rem;" data-toggle="modal" data-target="#import" title="Import Vehicles">
                        <i class="fa fa-upload"></i> Import
                    </button>
                @endcan
            </div>
        </div>
        
        <!-- Bulk Actions Toolbar -->
        <div class="bulk-actions-toolbar" id="bulkToolbar" style="display: none;">
            <div class="d-flex align-items-center justify-content-between p-3 bg-light border rounded mb-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success mr-2"></i>
                    <span id="selectedCount">0</span> vehicle(s) selected
                </div>
                <div class="bulk-actions">
                    <button class="btn btn-sm btn-outline-secondary mr-2" onclick="clearSelection()">
                        <i class="fas fa-times"></i> Clear Selection
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="bulkDeleteVehicles()">
                        <i class="fas fa-trash-alt"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="vehicles-table">
                    <div class="table-responsive">
                        <table class="table" id="ajax_data_table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="chk_all">
                                            <label class="custom-control-label" for="chk_all"></label>
                                        </div>
                                    </th>
                                    <th>Vehicle ID</th>
                                    <th>Registration Plate</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Fuel Type</th>
                                    <th>Status</th>
                                    <th>Assigned Driver</th>
                                    <th>Details</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Import Modal -->
    <div id="import" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-cloud-upload-alt"></i> Import Vehicles</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/import-vehicles', 'method' => 'POST', 'files' => true, 'id' => 'importForm', 'enctype' => 'multipart/form-data']) !!}
                    
                    <!-- File Upload Section -->
                    <div class="file-upload-section" id="fileDropZone">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Drop your Excel/CSV file here or click to browse</div>
                        <div class="upload-hint">Maximum file size: 5MB • Supported formats: .xlsx, .xls, .csv</div>
                        {!! Form::file('excel', ['class' => 'form-control', 'required', 'accept' => '.xlsx,.xls,.csv', 'style' => 'display: none;', 'id' => 'fileInput']) !!}
                        <div id="fileName" class="mt-2" style="display: none;">
                            <i class="fas fa-file-excel text-success"></i> <span id="fileNameText"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger ml-2" id="removeFile">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-cogs"></i> Import Options</label>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="skipDuplicates" name="skip_duplicates" value="1" checked>
                                        <label class="form-check-label" for="skipDuplicates">
                                            <strong>Skip Duplicate Registration Plates</strong><br>
                                            <small class="text-muted">Automatically skip vehicles with existing registration numbers</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="validateData" name="validate_data" value="1" checked>
                                        <label class="form-check-label" for="validateData">
                                            <strong>Validate Data Before Import</strong><br>
                                            <small class="text-muted">Check data integrity and show preview before importing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="createBackup" name="create_backup" value="1">
                                        <label class="form-check-label" for="createBackup">
                                            <strong>Create Backup Before Import</strong><br>
                                            <small class="text-muted">Automatically backup existing vehicle data</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-list-check"></i> Column Requirements
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="text-danger mb-2">Required Fields</h6>
                                        <ul class="list-unstyled small">
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> registration_plate</li>
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> make_name</li>
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> model_name</li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-2">Optional Fields</h6>
                                        <ul class="list-unstyled small">
                                            <li class="optional-column">engine_type</li>
                                            <li class="optional-column">year</li>
                                            <li class="optional-column">color_name</li>
                                            <li class="optional-column">vin</li>
                                            <li class="optional-column">mileage</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href="{{ asset('assets/samples/vehicles.xlsx') }}" class="btn btn-outline-success btn-sm" download>
                                        <i class="fas fa-download"></i> Download Sample Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert-info-custom mt-4">
                        <h6><i class="fas fa-info-circle"></i> Important Import Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Validation:</strong> All data is validated before import</li>
                                    <li><strong>Duplicates:</strong> System checks registration plates automatically</li>
                                    <li><strong>Preview:</strong> Review your data before final import</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Errors:</strong> Missing required fields will be highlighted</li>
                                    <li><strong>Progress:</strong> Real-time import progress tracking</li>
                                    <li><strong>Rollback:</strong> Failed imports can be rolled back</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar (Hidden by default) -->
                    <div id="importProgress" style="display: none;" class="mt-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: 0%">
                                <span id="progressText">0%</span>
                            </div>
                        </div>
                        <small class="text-muted">Importing vehicles... Please wait.</small>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary-custom" type="submit" id="importBtn">
                        <i class="fas fa-upload"></i> Import Vehicles
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal -->

    <!-- Modal -->


@endsection

@section('script')
<script type="text/javascript">
// Store vehicles data globally so other functions can access it
window.vehiclesGlobalData = @json($vehicles ?? []);

// Simple vanilla JavaScript approach to load vehicles
function loadVehiclesSimple() {
    const vehiclesData = window.vehiclesGlobalData; // Get vehicles from global
    console.log('Vehicles data:', vehiclesData); // Debug log
    const tbody = document.querySelector('#ajax_data_table tbody');
    
    if (!tbody) {
        console.error('Table tbody not found');
        return;
    }

    if (!vehiclesData || vehiclesData.length === 0) {
        console.log('No vehicles data found');
        tbody.innerHTML = '<tr><td colspan="10" class="text-center">No vehicles found</td></tr>';
        return;
    }

    tbody.innerHTML = vehiclesData.map(vehicle => `
        <tr id="vehicle-row-${vehicle.id}">
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input vehicle-checkbox" id="checkbox-${vehicle.id}" name="ids[]" value="${vehicle.id}" onchange="updateSelection()">
                    <label class="custom-control-label" for="checkbox-${vehicle.id}"></label>
                </div>
            </td>
            <td><strong>VEH-${String(vehicle.id).padStart(4, '0')}</strong></td>
            <td><span class="badge badge-primary">${vehicle.license_plate || 'N/A'}</span></td>
            <td>${vehicle.make_name || 'N/A'}</td>
            <td>${vehicle.model_name || 'N/A'}</td>
            <td>${vehicle.engine_type ? vehicle.engine_type.charAt(0).toUpperCase() + vehicle.engine_type.slice(1) : 'N/A'}</td>
            <td>
                ${vehicle.in_service == 1 
                    ? '<span class="badge badge-success">Available</span>' 
                    : '<span class="badge badge-secondary">Disabled</span>'}
            </td>
            <td><span class="text-muted">-</span></td>
            <td>
                <button class="btn btn-sm btn-outline-info btn-action" onclick="toggleVehicleDetails(${vehicle.id})" title="View Details" id="details-btn-${vehicle.id}">
                    ⓘ Details
                </button>
            </td>
            <td class="text-center">
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <button class="btn btn-sm btn-outline-secondary" type="button" onclick="toggleDropdown(${vehicle.id})" title="Vehicle Actions" style="font-size: 16px; padding: 8px 10px;">
                        ⚙
                    </button>
                    <div class="dropdown-menu" id="dropdown-${vehicle.id}" style="display: none; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); z-index: 1000; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-width: 130px; margin-top: 2px;">
                        <a class="dropdown-item" href="{{ url('admin/vehicles') }}/${vehicle.id}/edit" style="display: block; padding: 8px 12px; text-decoration: none; color: #333;">
                            Edit Vehicle
                        </a>
                        <div style="border-top: 1px solid #eee; margin: 4px 0;"></div>
                        <a class="dropdown-item text-danger" href="#" onclick="confirmDeleteVehicle(${vehicle.id}, '${vehicle.license_plate || 'N/A'}', '${vehicle.make_name || 'N/A'}', '${vehicle.model_name || 'N/A'}'); return false;" style="display: block; padding: 8px 12px; text-decoration: none; color: #dc3545;">
                            Delete Vehicle
                        </a>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
    
    console.log(`Loaded ${vehiclesData.length} vehicles`);
}

// Global functions for vehicle operations
window.toggleVehicleDetails = function(id) {
    console.log('Toggle details called for ID:', id);
    
    const row = document.getElementById(`vehicle-row-${id}`);
    const detailsBtn = document.getElementById(`details-btn-${id}`);
    
    console.log('Found row:', row);
    console.log('Found button:', detailsBtn);
    
    if (!row || !detailsBtn) {
        console.error('Row or button not found for ID:', id);
        alert('Error: Cannot find vehicle row or button');
        return;
    }
    
    // Check if details are currently open by looking for existing details row
    let existingDetails = null;
    const nextRow = row.nextElementSibling;
    if (nextRow && nextRow.classList.contains('vehicle-details-row')) {
        existingDetails = nextRow;
    }
    
    console.log('Existing details found:', existingDetails);
    
    if (existingDetails) {
        // Close details
        console.log('Closing details');
        existingDetails.remove();
        row.classList.remove('details-expanded');
        detailsBtn.innerHTML = 'ⓘ Details';
        detailsBtn.classList.remove('btn-info');
        detailsBtn.classList.add('btn-outline-info');
        return;
    }
    
    // Get vehicle data from the global data source
    console.log('Global vehicles data:', window.vehiclesGlobalData);
    const vehiclesData = window.vehiclesGlobalData || [];
    const vehicle = vehiclesData.find(v => v.id == id);
    
    console.log('Found vehicle data:', vehicle);
    
    if (!vehicle) {
        console.error('Vehicle data not found for ID:', id, 'Available vehicles:', vehiclesData.length);
        alert('Error: Cannot find vehicle data for ID ' + id);
        return;
    }
    
    // Open details
    console.log('Opening details for vehicle:', vehicle);
    row.classList.add('details-expanded');
    detailsBtn.innerHTML = '✕ Hide';
    detailsBtn.classList.remove('btn-outline-info');
    detailsBtn.classList.add('btn-info');
    
    // Create simplified details row for testing
    const detailsRow = document.createElement('tr');
    detailsRow.id = `vehicle-details-${id}`;
    detailsRow.className = 'vehicle-details-row';
    detailsRow.style.backgroundColor = '#f8f9fa';
    
    const detailsCell = document.createElement('td');
    detailsCell.setAttribute('colspan', '10');
    detailsCell.style.padding = '20px';
    
    detailsCell.innerHTML = `
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h5 style="color: #7FD7E1; margin-bottom: 15px;">🚗 Vehicle Details</h5>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div>
                    <h6>Basic Information</h6>
                    <p><strong>Vehicle ID:</strong> VEH-${String(id).padStart(4, '0')}</p>
                    <p><strong>Registration:</strong> ${vehicle.license_plate || 'N/A'}</p>
                    <p><strong>Make:</strong> ${vehicle.make_name || 'N/A'}</p>
                    <p><strong>Model:</strong> ${vehicle.model_name || 'N/A'}</p>
                    <p><strong>Year:</strong> ${vehicle.year || 'N/A'}</p>
                </div>
                <div>
                    <h6>Status & Service</h6>
                    <p><strong>Status:</strong> ${vehicle.in_service == 1 ? 'Available' : 'Disabled'}</p>
                    <p><strong>Fuel Type:</strong> ${vehicle.engine_type || 'N/A'}</p>
                    <p><strong>Color:</strong> ${vehicle.color_name || 'N/A'}</p>
                    <p><strong>VIN:</strong> ${vehicle.vin || 'Not Available'}</p>
                </div>
                <div>
                    <h6>Actions</h6>
                    <a href="/admin/vehicles/${vehicle.id}/edit" class="btn btn-sm btn-warning" style="margin-right: 10px;">✏️ Edit</a>
                    <button class="btn btn-sm btn-secondary" onclick="toggleVehicleDetails(${id})">✕ Hide Details</button>
                </div>
            </div>
        </div>
    `;
    
    detailsRow.appendChild(detailsCell);
    
    console.log('Created details row:', detailsRow);
    
    // Insert the details row directly after the current vehicle row
    try {
        row.parentNode.insertBefore(detailsRow, row.nextSibling);
        console.log('Details row inserted successfully');
    } catch (error) {
        console.error('Error inserting details row:', error);
        alert('Error inserting details row: ' + error.message);
    }
}

window.confirmDeleteVehicle = function(id, plate, make, model) {
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) return;
    
    const existingConfirm = row.querySelector('.delete-confirmation-row');
    if (existingConfirm) {
        existingConfirm.remove();
        row.classList.remove('confirm-delete-row');
        return;
    }
    
    // Highlight the row
    row.classList.add('confirm-delete-row');
    
    // Create confirmation row
    const confirmRow = document.createElement('tr');
    confirmRow.className = 'delete-confirmation-row';
    confirmRow.innerHTML = `
        <td colspan="10">
            <div class="delete-confirmation">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-exclamation-triangle delete-warning-icon"></i>
                        <strong>Confirm Deletion:</strong> Are you sure you want to delete <strong>${plate} (${make} ${model})</strong>?
                        <br><small class="text-muted">This will permanently delete the vehicle and all associated records.</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-danger mr-2" onclick="deleteVehicle(${id})">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="cancelDelete(${id})">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </td>
    `;
    
    row.parentNode.insertBefore(confirmRow, row.nextSibling);
}

window.cancelDelete = function(id) {
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) return;
    
    const confirmRow = row.querySelector('.delete-confirmation-row');
    if (confirmRow) {
        confirmRow.remove();
    }
    row.classList.remove('confirm-delete-row');
}

window.deleteVehicle = function(id) {
    // Create and submit delete form
    const deleteForm = document.createElement('form');
    deleteForm.action = '{{ url("admin/vehicles") }}/' + id;
    deleteForm.method = 'POST';
    deleteForm.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    document.body.appendChild(deleteForm);
    deleteForm.submit();
}

window.toggleDropdown = function(vehicleId) {
    const dropdown = document.getElementById(`dropdown-${vehicleId}`);
    const isVisible = dropdown.style.display === 'block';
    
    // Close all other dropdowns first
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
        d.style.display = 'none';
    });
    
    // Toggle the current dropdown
    dropdown.style.display = isVisible ? 'none' : 'block';
    
    // Close dropdown when clicking outside
    if (dropdown.style.display === 'block') {
        document.addEventListener('click', function closeDropdown(e) {
            if (!dropdown.contains(e.target) && !e.target.closest(`[onclick="toggleDropdown(${vehicleId})"]`)) {
                dropdown.style.display = 'none';
                document.removeEventListener('click', closeDropdown);
            }
        });
    }
}

window.bulkDeleteVehicles = function() {
    const selectedCheckboxes = document.querySelectorAll('.vehicle-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one vehicle to delete.');
        return;
    }
    
    const vehicleNames = Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        const plate = row.querySelector('.badge').textContent;
        return plate;
    }).join(', ');
    
    if (confirm(`Are you sure you want to delete ${selectedCheckboxes.length} vehicle(s)?\n\nVehicles: ${vehicleNames}\n\nThis action cannot be undone and will delete all associated records.`)) {
        // Create bulk delete form
        const form = document.createElement('form');
        form.action = '{{ url("admin/delete-vehicles") }}';
        form.method = 'POST';
        
        let formHtml = '@csrf';
        selectedCheckboxes.forEach(checkbox => {
            formHtml += `<input type="hidden" name="ids[]" value="${checkbox.value}">`;
        });
        
        form.innerHTML = formHtml;
        document.body.appendChild(form);
        form.submit();
    }
}

window.updateSelection = function() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const checkedBoxes = document.querySelectorAll('.vehicle-checkbox:checked');
    const selectAllCheckbox = document.getElementById('chk_all');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    
    // Update select all checkbox state
    if (checkedBoxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
    
    // Show/hide bulk toolbar
    if (checkedBoxes.length > 0) {
        bulkToolbar.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
        
        // Highlight selected rows
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) {
                row.classList.add('row-selected');
            } else {
                row.classList.remove('row-selected');
            }
        });
    } else {
        bulkToolbar.style.display = 'none';
        // Remove all row highlights
        checkboxes.forEach(cb => {
            cb.closest('tr').classList.remove('row-selected');
        });
    }
}

window.clearSelection = function() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const selectAllCheckbox = document.getElementById('chk_all');
    
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateSelection();
}

// Enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Starting vehicle initialization');
    
    // Wait a bit for jQuery to load if needed, then initialize
    setTimeout(() => {
        try {
            // Initialize drag and drop for import modal
            initializeDragAndDrop();
            
            // Initialize import form enhancements
            initializeImportForm();
            
            // Initialize delete confirmations
            initializeDeleteModals();
            
            // Load vehicles when page is ready
            console.log('About to load vehicles...');
            loadVehiclesSimple();
            console.log('Vehicle loading function called');
            
            // Initialize selection handlers
            setTimeout(() => {
                updateSelection();
            }, 500);
            
        } catch (error) {
            console.error('Error during initialization:', error);
            // Try loading vehicles without other initializations
            try {
                loadVehiclesSimple();
            } catch (vehicleError) {
                console.error('Error loading vehicles:', vehicleError);
            }
        }
    }, 100);
});

// Drag and Drop functionality
function initializeDragAndDrop() {
    const dropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');
    const removeFile = document.getElementById('removeFile');
    
    if (!dropZone || !fileInput) return;
    
    // Click to browse files
    dropZone.addEventListener('click', function(e) {
        if (e.target.id !== 'removeFile') {
            fileInput.click();
        }
    });
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    
    // Handle file input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(this.files);
    });
    
    // Remove file button
    if (removeFile) {
        removeFile.addEventListener('click', function(e) {
            e.stopPropagation();
            clearFile();
        });
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        dropZone.classList.add('dragover');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (validateFile(file)) {
                fileInput.files = files;
                displayFile(file);
            }
        }
    }
    
    function validateFile(file) {
        const allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx?|csv)$/i)) {
            alert('Please select a valid Excel or CSV file (.xlsx, .xls, .csv)');
            return false;
        }
        
        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            return false;
        }
        
        return true;
    }
    
    function displayFile(file) {
        fileNameText.textContent = file.name;
        fileName.style.display = 'block';
        dropZone.querySelector('.upload-text').style.display = 'none';
        dropZone.querySelector('.upload-hint').style.display = 'none';
        dropZone.querySelector('.upload-icon').style.display = 'none';
    }
    
    function clearFile() {
        fileInput.value = '';
        fileName.style.display = 'none';
        dropZone.querySelector('.upload-text').style.display = 'block';
        dropZone.querySelector('.upload-hint').style.display = 'block';
        dropZone.querySelector('.upload-icon').style.display = 'block';
    }
}

// Import form enhancements
function initializeImportForm() {
    const importForm = document.getElementById('importForm');
    const importBtn = document.getElementById('importBtn');
    const progressDiv = document.getElementById('importProgress');
    const progressBar = progressDiv?.querySelector('.progress-bar');
    const progressText = document.getElementById('progressText');
    
    if (!importForm) return;
    
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const fileInput = document.getElementById('fileInput');
        
        if (!fileInput.files.length) {
            alert('Please select a file to import');
            return;
        }
        
        // Show progress
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        progressDiv.style.display = 'block';
        
        // Simulate progress (replace with actual upload progress)
        simulateProgress();
        
        // Submit form (you may want to use AJAX here for better UX)
        setTimeout(() => {
            this.submit();
        }, 1000);
    });
    
    function simulateProgress() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 95) {
                progress = 95;
                clearInterval(interval);
            }
            
            if (progressBar) {
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
            }
        }, 200);
    }
}

// Enhanced delete modals
function initializeDeleteModals() {
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (window.currentDeleteId) {
                const form = document.getElementById('form_' + window.currentDeleteId);
                if (form) {
                    form.submit();
                } else {
                    // Create and submit a delete form
                    const deleteForm = document.createElement('form');
                    deleteForm.action = '{{ url("admin/vehicles") }}/' + window.currentDeleteId;
                    deleteForm.method = 'POST';
                    deleteForm.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
                
                // Hide modal using Bootstrap's modal methods if available
                const modal = document.getElementById('deleteModal');
                if (modal && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }
            }
        });
    }
    
    // Check all functionality
    const checkAll = document.getElementById('chk_all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.vehicle-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelection();
        });
    }
}

// Also try immediate execution as fallback
console.log('Vehicle script loaded - attempting immediate execution');
if (document.readyState === 'loading') {
    console.log('Document still loading, waiting for DOMContentLoaded');
} else {
    console.log('Document already loaded, executing immediately');
    setTimeout(loadVehiclesSimple, 100);
}
</script>
@endsection
