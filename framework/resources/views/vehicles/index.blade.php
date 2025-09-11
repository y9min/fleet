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
        
        /* Delete Modal Warning */
        .delete-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .delete-warning-icon {
            color: #f39c12;
            font-size: 1.2rem;
            margin-right: 0.5rem;
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
        
        <div class="row">
            <div class="col-12">
                <div class="vehicles-table">
                    <div class="table-responsive">
                        <table class="table" id="ajax_data_table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" id="chk_all">
                                    </th>
                                    <th>Vehicle ID</th>
                                    <th>Registration Plate</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Fuel Type</th>
                                    <th>Status</th>
                                    <th>Assigned Driver</th>
                                    <th>Telematics</th>
                                    <th>View</th>
                                    <th>Actions</th>
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

    <!-- Enhanced Bulk Delete Modal -->
    <div id="bulkModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-trash-alt"></i> Bulk Delete Vehicles</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/delete-vehicles', 'method' => 'POST', 'id' => 'form_delete']) !!}
                    <div id="bulk_hidden"></div>
                    <div class="delete-warning">
                        <i class="fas fa-exclamation-triangle delete-warning-icon"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete <strong id="deleteCount">0</strong> selected vehicle(s)?</p>
                    <div id="selectedVehicles" class="mt-3">
                        <!-- Selected vehicles will be listed here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="bulk_action" class="btn btn-danger-custom" type="submit" data-submit="">
                        <i class="fas fa-trash-alt"></i> Delete Selected Vehicles
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

    <!-- Enhanced Single Delete Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-trash-alt"></i> Delete Vehicle</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="delete-warning">
                        <i class="fas fa-exclamation-triangle delete-warning-icon"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to permanently delete this vehicle?</p>
                    <div id="vehicleDetails" class="mt-3">
                        <!-- Vehicle details will be shown here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="del_btn" class="btn btn-danger-custom" type="button" data-submit="">
                        <i class="fas fa-trash-alt"></i> Delete Vehicle
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Enhanced View Modal -->
    <div id="viewModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-car"></i> Vehicle Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="loader">
                        <div class="text-center py-5">
                            <div class="loading-spinner mb-3"></div>
                            <h5 class="text-muted">Loading vehicle details...</h5>
                            <p class="text-muted">Please wait while we fetch the information</p>
                        </div>
                    </div>
                    <div id="vehicleContent" style="display: none;">
                        <!-- Vehicle details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary-custom" id="editVehicle" style="display: none;">
                        <i class="fas fa-edit"></i> Edit Vehicle
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Vehicle Deletion</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="delete-warning">
                        <i class="fas fa-exclamation-triangle delete-warning-icon"></i>
                        <strong>Permanent Action:</strong> This cannot be undone!
                    </div>
                    <p>Are you absolutely sure you want to delete this vehicle?</p>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-info-circle"></i> This will also delete:</h6>
                        <ul class="mb-0">
                            <li>All associated maintenance records</li>
                            <li>Booking history</li>
                            <li>Driver assignments</li>
                            <li>Vehicle documents</li>
                        </ul>
                    </div>
                    <div id="confirmVehicleDetails" class="mt-3">
                        <!-- Vehicle info will be displayed here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="confirmDelete" class="btn btn-danger-custom" type="button">
                        <i class="fas fa-trash-alt"></i> Yes, Delete Vehicle
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
// Simple vanilla JavaScript approach to load vehicles
function loadVehiclesSimple() {
    const vehiclesData = @json($vehicles ?? []); // Get vehicles from backend
    console.log('Vehicles data:', vehiclesData); // Debug log
    const tbody = document.querySelector('#ajax_data_table tbody');
    
    if (!tbody) {
        console.error('Table tbody not found');
        return;
    }

    if (!vehiclesData || vehiclesData.length === 0) {
        console.log('No vehicles data found');
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">No vehicles found</td></tr>';
        return;
    }

    tbody.innerHTML = vehiclesData.map(vehicle => `
        <tr>
            <td><input type="checkbox" name="ids[]" value="${vehicle.id}" class="checkbox"></td>
            <td>VEH-${String(vehicle.id).padStart(4, '0')}</td>
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
            <td><span class="text-muted">N/A</span></td>
            <td><button class="btn btn-sm btn-outline-primary" onclick="viewVehicle(${vehicle.id})"><i class="fa fa-eye"></i> View</button></td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{ url('admin/vehicles') }}/${vehicle.id}/edit" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger" onclick="setDeleteId(${vehicle.id})" data-toggle="modal" data-target="#deleteModal" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    console.log(`Loaded ${vehiclesData.length} vehicles`);
}

// Global functions for vehicle operations
window.setDeleteId = function(id) {
    window.currentDeleteId = id;
}

window.viewVehicle = function(id) {
    const modal = document.getElementById('viewModal');
    const loader = document.getElementById('loader');
    const content = document.getElementById('vehicleContent');
    const editBtn = document.getElementById('editVehicle');
    
    if (!modal) return;
    
    // Show modal and loader
    if (typeof $ !== 'undefined') {
        $(modal).modal('show');
    } else {
        modal.style.display = 'block';
        modal.classList.add('show');
    }
    loader.style.display = 'block';
    content.style.display = 'none';
    editBtn.style.display = 'none';
    
    // Simulate loading vehicle details
    setTimeout(() => {
        loader.style.display = 'none';
        content.style.display = 'block';
        editBtn.style.display = 'inline-block';
        
        // Mock vehicle content (replace with actual AJAX call)
        content.innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <h5 class="text-primary mb-3"><i class="fas fa-car"></i> Vehicle Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Vehicle ID:</strong> VEH-${String(id).padStart(4, '0')}</p>
                            <p><strong>Registration:</strong> <span class="badge badge-primary">ABC-123</span></p>
                            <p><strong>Make:</strong> Toyota</p>
                            <p><strong>Model:</strong> Camry</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fuel Type:</strong> Petrol</p>
                            <p><strong>Year:</strong> 2022</p>
                            <p><strong>Status:</strong> <span class="badge badge-success">Available</span></p>
                            <p><strong>Mileage:</strong> 25,000 miles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Quick Stats</h6>
                        </div>
                        <div class="card-body">
                            <p class="card-text small">
                                <strong>Last Service:</strong> 2 months ago<br>
                                <strong>Next Service:</strong> Due soon<br>
                                <strong>Insurance:</strong> Valid<br>
                                <strong>MOT:</strong> 8 months left
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Set edit button action
        editBtn.onclick = function() {
            window.location.href = '{{ url("admin/vehicles") }}/' + id + '/edit';
        };
    }, 1500);
}

// Enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Starting vehicle initialization');
    
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
    
    // Enhanced bulk delete
    const bulkActionBtn = document.getElementById('bulk_action');
    if (bulkActionBtn) {
        bulkActionBtn.addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('input[name="ids[]"]:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one vehicle to delete');
                return false;
            }
            
            // Update delete count
            const deleteCount = document.getElementById('deleteCount');
            if (deleteCount) {
                deleteCount.textContent = selectedCheckboxes.length;
            }
        });
    }
    
    // Check all functionality
    const checkAll = document.getElementById('chk_all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="ids[]"]');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
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
