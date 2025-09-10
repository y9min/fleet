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

    <!-- Import Modal -->
    <div id="import" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-upload"></i> Import Vehicles</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/import-vehicles', 'method' => 'POST', 'files' => true, 'id' => 'importForm']) !!}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label"><i class="fa fa-file-excel"></i> Select File (Excel/CSV)</label>
                                {!! Form::file('excel', ['class' => 'form-control', 'required', 'accept' => '.xlsx,.xls,.csv']) !!}
                                <small class="text-muted">Supported formats: .xlsx, .xls, .csv</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="skipDuplicates" name="skip_duplicates" value="1" checked>
                                    <label class="form-check-label" for="skipDuplicates">
                                        Skip vehicles with duplicate registration plates
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="validateData" name="validate_data" value="1" checked>
                                    <label class="form-check-label" for="validateData">
                                        Validate data before importing
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6><i class="fa fa-info-circle"></i> Required Columns</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled small">
                                        <li><strong>registration_plate</strong> (Required)</li>
                                        <li><strong>make_name</strong> (Required)</li>
                                        <li><strong>model_name</strong> (Required)</li>
                                        <li>engine_type (Fuel Type)</li>
                                        <li>year</li>
                                        <li>color_name</li>
                                        <li>vin</li>
                                        <li>mileage</li>
                                    </ul>
                                    <a href="{{ asset('assets/samples/vehicles.xlsx') }}" class="btn btn-sm btn-outline-success">
                                        <i class="fa fa-download"></i> Sample File
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fa fa-info-circle"></i> Import Notes:</h6>
                        <ul class="mb-0">
                            <li>System automatically validates duplicate registration plates</li>
                            <li>Missing required fields will be highlighted in preview</li>
                            <li>You can preview data before final import</li>
                            <li>Maximum file size: 5MB</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn" style="background: #7FD7E1; color: white;" type="submit">
                        <i class="fa fa-upload"></i> Import Vehicles
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="bulkModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('fleet.delete')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/delete-vehicles', 'method' => 'POST', 'id' => 'form_delete']) !!}
                    <div id="bulk_hidden"></div>
                    <p>@lang('fleet.confirm_bulk_delete')</p>
                </div>
                <div class="modal-footer">
                    <button id="bulk_action" class="btn btn-danger" type="submit"
                        data-submit="">@lang('fleet.delete')</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('fleet.delete')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>@lang('fleet.confirm_delete')</p>
                </div>
                <div class="modal-footer">
                    <button id="del_btn" class="btn btn-danger" type="button" data-submit="">@lang('fleet.delete')</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- View Modal -->
    <div id="viewModal" class="modal fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Vehicle Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="loader">
                        <div class="text-center">
                            <i class="fa fa-spinner fa-spin"></i> Loading vehicle details...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Confirm Delete</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this vehicle? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button id="confirmDelete" class="btn btn-danger" type="button">Delete Vehicle</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
    // Simple view implementation
    alert('View vehicle ID: ' + id + ' (Implementation pending)');
}

// Enhanced delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Starting vehicle initialization');
    
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
    
    // Load vehicles when page is ready
    console.log('About to load vehicles...');
    loadVehiclesSimple();
    console.log('Vehicle loading function called');
});

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
