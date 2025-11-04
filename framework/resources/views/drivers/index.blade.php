@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')

@section('page_title')
PCOFlow | Drivers
@endsection

@section('extra_css')
<style type="text/css">
.show-password-button{
    outline: none;
    border: 1px solid #ced4da;
  }
  .mybtn1 {
    padding-top: 4px;
    padding-right: 8px;
    padding-bottom: 4px;
    padding-left: 8px;
  }

  .checkbox,
  #chk_all {
    width: 20px;
    height: 20px;
  }

  td>img {
    border-radius: 50%;
  }

  /* Toggle Switch Styles */
  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  input:checked + .slider {
    background-color: #20B2AA;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(32, 178, 170, 0.2);
  }

  input:focus + .slider {
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(32, 178, 170, 0.3);
  }

  input:checked + .slider:before {
    transform: translateX(26px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
  }

  .slider.round {
    border-radius: 24px;
  }

  .slider.round:before {
    border-radius: 50%;
  }

  /* Hover effects for better interactivity */
  .slider:hover {
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15);
  }

  input:checked + .slider:hover {
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1), 0 0 0 2px rgba(32, 178, 170, 0.3);
  }

  /* Enhanced Import Modal Styles */
  .file-upload-section {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .file-upload-section:hover {
    border-color: #007bff;
    background-color: #e3f2fd;
  }

  .file-upload-section.dragover {
    border-color: #28a745;
    background-color: #d4edda;
  }

  .upload-icon {
    font-size: 48px;
    color: #6c757d;
    margin-bottom: 15px;
  }

  .upload-text {
    font-size: 16px;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
  }

  .upload-hint {
    font-size: 12px;
    color: #6c757d;
  }

  .modal-xl {
    max-width: 800px;
  }
  
  /* Fix modal z-index and pointer-events to prevent backdrop blocking interactions */
  .modal {
    overflow-y: auto !important;
    z-index: 1050 !important;
  }
  
  .modal.show {
    display: block !important;
    overflow-y: auto !important;
  }
  
  .modal-dialog {
    position: relative;
    z-index: 1060 !important;
    margin: 1.75rem auto;
  }
  
  .modal-content {
    pointer-events: auto !important;
    z-index: 1070 !important;
    position: relative;
  }
  
  .modal-backdrop {
    z-index: 1040 !important;
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    pointer-events: none !important;
  }
  
  .modal-backdrop.show {
    pointer-events: auto !important;
  }
  
  /* Ensure all interactive elements in modal are clickable */
  .modal-content * {
    pointer-events: auto;
  }
  
  /* Allow scrolling in modal body */
  .modal-body {
    overflow-y: auto;
    max-height: calc(100vh - 200px);
  }

  .progress {
    height: 20px;
    border-radius: 10px;
  }

  .progress-bar {
    border-radius: 10px;
  }

  /* Enhanced page header with modern design - matching vehicles page */
  .page-header {
      background: linear-gradient(135deg, #7ed6e1, #6dc6d2);
      color: white;
      padding: 25px 30px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 4px 12px rgba(126, 214, 225, 0.3);
      border: none;
  }

  .page-header h1 {
      color: white;
      margin: 0;
      font-weight: 600;
      font-size: 28px;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  }

  .page-header .btn {
      border-radius: 6px;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.3s ease;
  }

  /* Pagination Styles */
  #pagination-container {
      margin-top: 20px;
      padding: 10px 0;
  }

  #drivers-info {
      color: #666;
      font-size: 14px;
  }

  #drivers-pagination .pagination {
      margin: 0;
  }

  #drivers-pagination .page-item {
      margin: 0 2px;
  }

  #drivers-pagination .page-link {
      color: #007bff;
      padding: 6px 12px;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      transition: all 0.2s ease;
  }

  #drivers-pagination .page-link:hover {
      background-color: #e9ecef;
      border-color: #007bff;
  }

  #drivers-pagination .page-item.active .page-link {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
  }

  #drivers-pagination .page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      background-color: #fff;
      border-color: #dee2e6;
      cursor: not-allowed;
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

  .row-selected {
      background-color: #f0fdff !important;
  }
</style>
@endsection
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.drivers')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">

    @if (count($errors) > 0)
      <div class="alert alert-danger">
        @if (session('errors'))
            <p>{{ session('errors.error') }}</p>
            @if(session('errors.data'))
              <ul>
                  @foreach (session('errors.data') as $bookingId)
                      <li><a href="{{ route('invitations.edit', $bookingId) }}">Booking ID : {{ $bookingId }}</a></li>
                  @endforeach
              </ul>
            @endif
        @endif
        @if(!is_array($errors))
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    @endif

    @if (session('success'))
      <div class="alert alert-success">
        <p>{{ session('success') }}</p>
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger">
        <p>{{ session('error') }}</p>
      </div>
    @endif

  

    

    <!-- Enhanced Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>Drivers</h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 16px; margin-top: 8px;">Manage your drivers with ease</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('drivers.create') }}" class="btn" style="background-color: #C1C1C1; color: black; border: 1px solid #C1C1C1;" title="@lang('fleet.addDriver')">
                <i class="fas fa-plus"></i> Add Driver
            </a>
            <button type="button" class="btn" style="background-color: #7ed6e1; color: white; border: 1px solid #7ed6e1;" data-toggle="modal" data-target="#import" title="Import Drivers">
                <i class="fas fa-file-import"></i> Import Drivers
            </button>
        </div>
    </div>

    <!-- Enhanced Bulk Actions Toolbar -->
    <div class="bulk-actions-toolbar" id="bulkToolbar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between p-4" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7FD7E1, #6BC5D2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <h6 class="mb-0" style="font-weight: 600; color: #333;">Bulk Actions</h6>
                    <small class="text-muted" id="selectedCount">0</small> driver(s) selected
                </div>
            </div>
            <div class="bulk-actions d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="clearDriverSelection()" style="border-radius: 6px; padding: 8px 16px;">
                    <i class="fas fa-times"></i> Clear Selection
                </button>
                <button class="btn btn-danger" onclick="bulkDeleteDrivers()" style="border-radius: 6px; padding: 8px 16px; background: linear-gradient(135deg, #dc3545, #c82333); border: none;">
                    <i class="fas fa-trash-alt"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table" id="ajax_data_table" style="padding-bottom: 15px">
          <thead class="thead-inverse">
            <tr>
              <th>
                <input type="checkbox" id="chk_all">
              </th>
                <th>@lang('fleet.name')</th>
                <th>@lang('fleet.email')</th>
                <th>Phone</th>
              <th>License Number</th>
              <th>@lang('fleet.is_active')</th>
              <th>Assigned Vehicle</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        
        <!-- Pagination Controls -->
        <div class="d-flex justify-content-between align-items-center mt-3" id="pagination-container" style="display: none;">
            <div class="dataTables_info" id="drivers-info">Showing 0 to 0 of 0 entries</div>
            <div class="dataTables_paginate paging_simple_numbers" id="drivers-pagination">
                <ul class="pagination mb-0"></ul>
            </div>
        </div>
      </div>
    </div>
  </div>

<!-- Enhanced Import Modal -->
<div id="import" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-cloud-upload-alt"></i> Import Drivers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => 'admin/import-drivers', 'method' => 'POST', 'files' => true, 'id' => 'importForm', 'enctype' => 'multipart/form-data']) !!}
                
                <!-- File Upload Section -->
                <div class="file-upload-section" id="fileDropZone">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <div class="upload-text">Drop your Excel/CSV file here or click to browse</div>
                    <div class="upload-hint">Maximum file size: 5MB • Supported formats: .xlsx, .xls, .csv</div>
                    {!! Form::file('excel', ['class' => 'form-control', 'accept' => '.xlsx,.xls,.csv', 'style' => 'display: none;', 'id' => 'fileInput']) !!}
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
                            <h6 class="text-primary"><i class="fas fa-download"></i> Download Sample File</h6>
                            <a href="{{ asset('assets/samples/drivers.xlsx') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-excel"></i> Download Sample Excel
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <h6 class="text-info"><i class="fas fa-info-circle"></i> Import Guidelines</h6>
                            <ul class="text-muted small">
                                <li>Use the sample file format</li>
                                <li>Email addresses must be unique</li>
                                <li>Required fields: First Name, Last Name, Email, Password</li>
                                <li>Duplicate emails will be skipped</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Section -->
                <div id="importProgress" class="mt-3" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="text-center mt-2">
                        <span id="progressText">Preparing import...</span>
                    </div>
                </div>
                
                <!-- Import Results -->
                <div id="importResults" class="mt-3" style="display: none;">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Import Completed Successfully!</h6>
                        <div id="importStats"></div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="modal-footer">
                    <button class="btn btn-warning" type="submit" id="importBtn">
                        <i class="fas fa-upload"></i> Import Drivers
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<!-- Enhanced Import Modal -->

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
        {!! Form::open(['url'=>'admin/delete-drivers','method'=>'POST','id'=>'form_delete']) !!}
        <div id="bulk_hidden"></div>
        <p>@lang('fleet.confirm_bulk_delete')</p>
      </div>
      <div class="modal-footer">
        <button id="bulk_action" class="btn btn-danger" type="submit" data-submit="">@lang('fleet.delete')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
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
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="changepass" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.change_password')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>url('admin/change_password'),'id'=>'changepass_form']) !!}
        <form id="change" action="{{url('admin/change_password')}}" method="POST">
          {!! Form::hidden('driver_id',"",['id'=>'driver_id'])!!}
          <div class="form-group">
            {!! Form::label('passwd',__('fleet.password'),['class'=>"form-label"]) !!}
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-lock"></i></span>
              </div>
              {!! Form::password('passwd',['class'=>"form-control",'id'=>'passwd','required']) !!}
              <div class="input-group-prepend">
                <button type="button" id="show-password-button" class="show-password-button" >
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="password" class="btn btn-info" type="submit">@lang('fleet.change_password')</button>
        </form>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
@endsection

@section('script')
<script type="text/javascript">

// Embed drivers data in page for instant access
window.driversGlobalData = @json($drivers ?? []);

// Pagination state
let currentPage = 1;
const itemsPerPage = 10;

// Simple vanilla JavaScript approach to load drivers
function loadDriversSimple(filteredData = null, page = 1) {
    const driversData = filteredData || window.driversGlobalData;
    console.log('Drivers data:', driversData);
    console.log('Number of drivers:', driversData ? driversData.length : 0);
    
    const tbody = document.querySelector('#ajax_data_table tbody');
    
    if (!tbody) {
        console.error('Table tbody not found');
        return;
    }

    if (!driversData || driversData.length === 0) {
        console.log('No drivers data found');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No drivers found</td></tr>';
        document.getElementById('pagination-container').style.display = 'none';
        return;
    }

    // Calculate pagination
    const totalPages = Math.ceil(driversData.length / itemsPerPage);
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, driversData.length);
    const paginatedData = driversData.slice(startIndex, endIndex);
    
    currentPage = page;

    // Generate table rows
    let tableHTML = '';
    paginatedData.forEach((driver) => {
        const isActive = driver.is_active == 1;
        const checked = isActive ? 'checked' : '';
        
        // Phone display
        const phoneDisplay = driver.phone_display || 'N/A';
        
        // License Number
        const licenseNumber = driver.license_number || 'N/A';
        
        // Assigned vehicle
        let vehicleHTML = 'N/A';
        if (driver.vehicle && driver.vehicle.license_plate) {
            vehicleHTML = '<a href="{{ url("admin/vehicles") }}/' + driver.vehicle.id + '" class="badge badge-warning text-dark" style="background-color: #EABE14; color: #333; border-radius: 4px; padding: 6px 12px; text-decoration: none; font-weight: bold;" title="View Vehicle Details">' + driver.vehicle.license_plate + '</a>';
        }
        
        // Action buttons
        const actionButtons = '<div class="d-flex justify-content-center gap-2">' +
            '<button class="btn btn-sm btn-info" data-driver-id="' + driver.id + '" onclick="toggleDriverDetailsInstant(this)" title="View Details" style="padding: 6px 8px;"><i class="fas fa-eye"></i></button>' +
            '<button class="btn btn-sm btn-warning" data-id="' + driver.id + '" data-toggle="modal" data-target="#changepass" title="Change Password" style="padding: 6px 8px;"><i class="fas fa-key"></i></button>' +
            '<a href="{{ url("admin/drivers") }}/' + driver.id + '/edit" class="btn btn-sm btn-primary" title="Edit Driver" style="padding: 6px 8px;"><i class="fas fa-edit"></i></a>' +
            '<button class="btn btn-sm btn-danger" data-id="' + driver.id + '" data-toggle="modal" data-target="#myModal" title="Delete Driver" style="padding: 6px 8px;"><i class="fas fa-trash"></i></button>' +
            '</div>';
        
        tableHTML += '<tr>' +
            '<td><input type="checkbox" name="ids[]" value="' + driver.id + '" class="checkbox" id="chk' + driver.id + '" onclick="checkcheckbox();"></td>' +
            '<td><a href="{{ url("admin/drivers") }}/' + driver.id + '">' + driver.name + '</a></td>' +
            '<td>' + driver.email + '</td>' +
            '<td>' + phoneDisplay + '</td>' +
            '<td>' + licenseNumber + '</td>' +
            '<td><div class="d-flex justify-content-center"><label class="switch"><input type="checkbox" class="driver-status-toggle" data-driver-id="' + driver.id + '" ' + checked + '><span class="slider round"></span></label></div></td>' +
            '<td>' + vehicleHTML + '</td>' +
            '<td>' + actionButtons + '</td>' +
            '</tr>';
    });
    
    tbody.innerHTML = tableHTML;
    
    // Render pagination
    renderPagination(driversData.length, totalPages, page);
    
    // Re-attach event handlers
    attachEventHandlers();
}

// Render pagination controls
function renderPagination(totalItems, totalPages, currentPage) {
    const container = document.getElementById('pagination-container');
    const info = document.getElementById('drivers-info');
    const paginationUl = document.querySelector('#drivers-pagination ul');
    
    if (totalPages <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    // Update info text
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, totalItems);
    info.textContent = `Showing ${startIndex} to ${endIndex} of ${totalItems} entries`;
    
    // Clear previous pagination
    paginationUl.innerHTML = '';
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `paginate_button page-item previous ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a href="#" class="page-link" data-page="${currentPage - 1}">Previous</a>`;
    paginationUl.appendChild(prevLi);
    
    // Page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `paginate_button page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
        paginationUl.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `paginate_button page-item next ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a href="#" class="page-link" data-page="${currentPage + 1}">Next</a>`;
    paginationUl.appendChild(nextLi);
    
    // Attach click handlers
    paginationUl.querySelectorAll('a.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                loadDriversSimple(null, page);
            }
        });
    });
}

// Attach event handlers for interactive elements
function attachEventHandlers() {
    // Update selection when checkboxes change
    $('input[name="ids[]"]').off('change').on('change', function() {
        updateDriverSelection();
    });
    
    // Check all handler
    $('#chk_all').off('change').on('change', function() {
        $('.checkbox').prop("checked", this.checked);
        updateDriverSelection();
    });
    
    // Call update on page load
    updateDriverSelection();
}

// Update driver selection and show/hide bulk toolbar
function updateDriverSelection() {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const checkedBoxes = document.querySelectorAll('input[name="ids[]"]:checked');
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

// Clear driver selection
function clearDriverSelection() {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    const selectAllCheckbox = document.getElementById('chk_all');
    
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateDriverSelection();
}

// Bulk delete drivers
function bulkDeleteDrivers() {
    const selectedCheckboxes = document.querySelectorAll('input[name="ids[]"]:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one driver to delete.');
        return;
    }
    
    const driverNames = Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        const name = row.querySelector('td:nth-child(2) a').textContent;
        return name;
    }).join(', ');
    
    if (confirm(`Are you sure you want to delete ${selectedCheckboxes.length} driver(s)?\n\nDrivers: ${driverNames}\n\nThis action cannot be undone and will delete all associated records.`)) {
        // Create bulk delete form
        const form = document.createElement('form');
        form.action = '{{ url("admin/delete-drivers") }}';
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Starting driver initialization');
    loadDriversSimple();
    console.log('Driver loading function called');
});

  $(document).on("click", "#del_btn", function(){
    var id=$(this).data("submit");
    console.log("Delete button clicked for driver ID:", id);
    
    if (!id) {
      console.error("No driver ID found");
      return;
    }
    
    // Close the modal first
    $('#myModal').modal('hide');
    
    // Create a form dynamically and submit it
    setTimeout(function() {
      var form = $('<form>', {
        'method': 'POST',
        'action': '{{ url("admin/drivers") }}/' + id
      });
      
      form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
      }));
      
      form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content')
      }));
      
      form.append($('<input>', {
        'type': 'hidden',
        'name': 'id',
        'value': id
      }));
      
      $('body').append(form);
      console.log("Submitting dynamic form");
      form.submit();
    }, 300);
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    console.log("Modal opened for driver ID:", id);
    $("#del_btn").attr("data-submit",id);
  });

  $('#changepass').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
    $("#driver_id").val(id);
  });

  $("#changepass_form").on("submit",function(e){
    $.ajax({
      type: "POST",
      url: $(this).attr("action"),
      data: $(this).serialize(),
      success: function(data){
       new PNotify({
            title: 'Success!',
            text: "@lang('fleet.passwordChanged')",
            type: 'info'
        });
      },
      dataType: "html"
    });
    $('#changepass').modal("hide");
    e.preventDefault();
  });

    // Checkbox checked
  function checkcheckbox(){
    // Total checkboxes
    var length = $('.checkbox').length;
    // Total checked checkboxes
    var totalchecked = 0;
    $('.checkbox').each(function(){
        if($(this).is(':checked')){
            totalchecked+=1;
        }
    });
    // console.log(length+" "+totalchecked);
    // Checked unchecked checkbox
    if(totalchecked == length){
        $("#chk_all").prop('checked', true);
    }else{
        $('#chk_all').prop('checked', false);
    }
  }
</script>
{{-- show password script --}}
<script>
  $(document).ready(function() {
  $('#show-password-button').click(function() {
    $('#show-password-button').show();
    var passwordField = $('#passwd');
    var fieldType = passwordField.attr('type');
    if (fieldType === 'password') {
      passwordField.attr('type', 'text');
      $(this).attr('title', 'Hide password');
      $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      passwordField.attr('type', 'password');
      $(this).attr('title', 'Show password');
      $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });
});

</script>
{{-- show password script end --}}

{{-- Driver status toggle script --}}
<script>
$(document).ready(function() {
    // Handle driver status toggle changes (single handler; optimistic UI)
    $(document).on('change', '.driver-status-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var driverId = $(this).data('driver-id');
        var isChecked = $(this).is(':checked');
        var action = isChecked ? 'enable' : 'disable';
        var toggle = $(this);
        
        // Prevent duplicate requests for the same toggle until complete
        if (toggle.data('busy')) {
            return false;
        }
        toggle.data('busy', true);
        
        console.log('Toggle changed:', driverId, action, isChecked);
        
        // Optimistic UI: keep visual state as-is, disable interaction briefly
        toggle.prop('disabled', true);
        
        // Use the same logic as the original disable/enable buttons
        var url = '{{ url("admin/drivers") }}/' + action + '/' + driverId;
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('Success:', response);
                // Show success notification
                new PNotify({
                    title: 'Success!',
                    text: 'Driver status updated successfully',
                    type: 'success',
                    delay: 5000,
                    styling: 'bootstrap3',
                    addclass: 'alert-success',
                    icon: 'fa fa-check-circle'
                });
                // No table reload; optimistic state already reflected by the toggle
            },
            error: function(xhr) {
                console.log('Error:', xhr);
                var errorMessage = 'An error occurred while updating driver status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Revert the toggle state on error
                toggle.prop('checked', !isChecked);
                
                // Show error notification
                new PNotify({
                    title: 'Error!',
                    text: errorMessage,
                    type: 'error'
                });
            },
            complete: function() {
                // Re-enable toggle
                toggle.prop('disabled', false);
                toggle.data('busy', false);
            }
        });
        
        return false; // Prevent any default behavior
    });
});
</script>
{{-- Driver status toggle script end --}}

{{-- Driver details toggle script --}}
<script>
// Instant toggle driver details dropdown (no AJAX delay)
function toggleDriverDetailsInstant(button) {
    var $button = $(button);
    var $row = $button.closest('tr');
    var $detailsRow = $row.next('.details-row');
    
    // If details row exists, remove it (instant)
    if ($detailsRow.length > 0) {
        $detailsRow.remove();
        $button.removeClass('expanded').html('<i class="fas fa-eye"></i>');
        return;
    }
    
    // Get driver ID from button
    var driverId = $button.data('driver-id');
    
    // Fetch full driver details via AJAX
    $.ajax({
        url: '{{ url("admin/drivers") }}/' + driverId + '/details',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var driver = response.driver;
                var customFields = response.customFields || [];
                var customFieldsMap = response.customFieldsMap || {};
                var html = '<div class="details-content">';
    
                // Create a map of custom field names for better display
                var customFieldMap = {};
                customFields.forEach(function(field) {
                    customFieldMap[field.field_name.toLowerCase().replace(/\s+/g, '_')] = field.field_name;
                });
                
                // Basic Information - Inline layout
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>License Number:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
                
                // License Expiry Date (if available)
                if (driver.license_expiry || driver.license_expiry_date || driver.driver_license_expiry) {
                    var licenseExpiry = driver.license_expiry || driver.license_expiry_date || driver.driver_license_expiry;
                    var formattedDate = '';
                    try {
                        var date = new Date(licenseExpiry);
                        if (!isNaN(date.getTime())) {
                            var day = String(date.getDate()).padStart(2, '0');
                            var month = String(date.getMonth() + 1).padStart(2, '0');
                            var year = date.getFullYear();
                            formattedDate = day + '/' + month + '/' + year;
                        } else {
                            formattedDate = licenseExpiry;
                        }
                    } catch (e) {
                        formattedDate = licenseExpiry;
                    }
                    html += '<div class="inline-field"><strong>License Expiry:</strong><span class="text-muted">' + formattedDate + '</span></div>';
                }
                
                // Address (if available)
                if (driver.address) {
                    html += '<div class="inline-field"><strong>Address:</strong><span class="text-muted">' + driver.address + '</span></div>';
                }
                
                // Emergency Contact (if available)
                if (driver.emergency_contact || driver.emergency_contact_name) {
                    var emergencyName = driver.emergency_contact || driver.emergency_contact_name;
                    html += '<div class="inline-field"><strong>Emergency Contact:</strong><span class="text-muted">' + emergencyName + '</span></div>';
                }
                
                // Emergency Phone (if available)
                if (driver.emergency_phone || driver.emergency_contact_phone || driver.emergency_contact_number) {
                    var emergencyPhone = driver.emergency_phone || driver.emergency_contact_phone || driver.emergency_contact_number;
                    html += '<div class="inline-field"><strong>Emergency Phone:</strong><span class="text-muted">' + emergencyPhone + '</span></div>';
                }
                
                // Vehicle Selection (if available)
                if (driver.vehicle_details) {
                    var vehicleDisplay = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name;
                    html += '<div class="inline-field"><strong>Vehicle Selection:</strong><span class="text-muted">' + vehicleDisplay + '</span></div>';
                } else if (driver.vehicle_selection) {
                    html += '<div class="inline-field"><strong>Vehicle Selection:</strong><span class="text-muted">' + driver.vehicle_selection + '</span></div>';
                }
                
                // Scheme Selection (if available)
                if (driver.scheme || driver.scheme_selection) {
                    var scheme = driver.scheme || driver.scheme_selection;
                    html += '<div class="inline-field"><strong>Scheme Selection:</strong><span class="text-muted">' + scheme + '</span></div>';
                }
                
                // Insurance Selection (if available)
                if (driver.insurance_selection) {
                    var insuranceDisplay = driver.insurance_selection === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
                    html += '<div class="inline-field"><strong>Insurance Selection:</strong><span class="text-muted">' + insuranceDisplay + '</span></div>';
                }
                
                var statusClass = driver.is_active == 1 ? 'success' : 'secondary';
                html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.is_active == 1 ? 'Active' : 'Inactive') + '</span></div>';
                html += '</div>';
    
                // Assigned Vehicle Information
                if (driver.assigned_vehicle) {
                    html += '<div class="mb-3">';
                    html += '<div class="inline-field"><strong>Assigned Vehicle:</strong><span class="text-muted">' + driver.assigned_vehicle.license_plate + ' (' + driver.assigned_vehicle.make_name + ' ' + driver.assigned_vehicle.model_name + ')</span></div>';
                    html += '</div>';
                }
                
                // Documents Section - License and Insurance Images
                if (driver.license_image || driver.license_upload_path || driver.insurance_image || driver.insurance_upload_path || driver.documents) {
                    html += '<div class="mb-3">';
                    html += '<h6><strong>Documents:</strong></h6>';
                    
                    // License Image Button
                    if (driver.license_image || driver.license_upload_path) {
                        var licenseFile = driver.license_upload_path || driver.license_image;
                        var licenseUrl = driver.license_url || ('{{ asset("uploads") }}' + '/' + licenseFile);
                        html += '<div class="inline-field">';
                        html += '<strong>License Image:</strong>';
                        html += '<a href="' + licenseUrl + '" target="_blank" class="btn btn-sm btn-primary ml-2">';
                        html += '<i class="fas fa-eye"></i> View License';
                        html += '</a>';
                        html += '</div>';
                    }
                    
                    // Insurance Image Button
                    if (driver.insurance_image || driver.insurance_upload_path || driver.documents) {
                        var insuranceFile = driver.insurance_upload_path || driver.insurance_image || driver.documents;
                        var insuranceUrl = driver.insurance_url || ('{{ asset("uploads") }}' + '/' + insuranceFile);
                        html += '<div class="inline-field">';
                        html += '<strong>Insurance Image:</strong>';
                        html += '<a href="' + insuranceUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                        html += '<i class="fas fa-eye"></i> View Insurance';
                        html += '</a>';
                        html += '</div>';
                    }
                    
                    html += '</div>';
                }
                
                // Additional Information (All Custom Fields and Onboarding Data)
                var hasAdditionalInfo = false;
                html += '<div class="mb-3">';
                html += '<h6><strong>Additional Information:</strong></h6>';
                
                // List of fields to display (excluding basic info already shown)
                var fieldsToExclude = [
                    'id', 'name', 'email', 'phone', 'license_number', 'is_active', 'user_type', 'group_id', 
                    'api_token', 'password', 'remember_token', 'created_at', 'updated_at', 'user_id',
                    'assigned_vehicle', 'vehicle_details', 'custom_data',
                    'license_upload_path', 'insurance_upload_path', 'license_image', 'insurance_image', 'documents',
                    'id_proof_type', 'is_available', 'first_name', 'last_name', 'phone_code', 'method', 
                    'edit', 'emp_id', 'contract_number', 'driver_image', 'license', 'terms', 'token',
                    'detail_id', 'license_url', 'insurance_url',
                    'license_expiry', 'license_expiry_date', 'driver_license_expiry',
                    'address', 'emergency_contact', 'emergency_phone', 'emergency_contact_name', 
                    'emergency_contact_phone', 'emergency_contact_number',
                    'vehicle_selection', 'scheme', 'scheme_selection', 'insurance_selection',
                    'company_id', 'is_verified', 'vehicle_id'
                ];
                
                // Display all driver metadata fields
                for (var key in driver) {
                    var keyLower = key.toLowerCase();
                    // Skip fields that end with '_url' (they're technical metadata, shown via buttons)
                    var isUrlField = key.endsWith('_url');
                    if (driver.hasOwnProperty(key) && !fieldsToExclude.includes(key) && !fieldsToExclude.includes(keyLower) && !isUrlField) {
                        var value = driver[key];
                        var displayName = '';
                        var displayValue = '';
                        var isFileField = false;
                        
                        // Skip object values to avoid "[object Object]" display
                        if (typeof value === 'object' && value !== null) {
                            continue;
                        }
                        
                        // Check if this is a custom field with an ID (custom_1, custom_2, etc.)
                        if (key.startsWith('custom_')) {
                            var fieldId = key.replace('custom_', '');
                            // Try to get the proper field name from customFieldsMap (enhanced with backend data)
                            if (customFieldsMap[key]) {
                                displayName = customFieldsMap[key].field_label || customFieldsMap[key].field_name;
                                isFileField = (customFieldsMap[key].field_type === 'file');
                            } else if (response.customFields) {
                                // Fallback to original logic
                                response.customFields.forEach(function(field) {
                                    if (field.id == fieldId) {
                                        displayName = field.field_name;
                                        isFileField = (field.field_type === 'file');
                                    }
                                });
                            }
                            if (!displayName) {
                                displayName = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                            }
                        } else {
                            displayName = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                        }
                        
                        if (value !== null && value !== undefined && value !== '' && value.toString().trim() !== '' && value.toString().trim() !== 'null' && value.toString().trim() !== 'undefined') {
                            displayValue = value.toString();
                            
                            // Check if it's a file field and use the URL from backend if available
                            var fileUrlKey = key + '_url';
                            var useBackendUrl = isFileField && value && driver[fileUrlKey];
                            
                            if (useBackendUrl) {
                                // Use the URL generated by the backend
                                hasAdditionalInfo = true;
                                html += '<div class="inline-field"><strong>' + displayName + ':</strong> ';
                                html += '<a href="' + driver[fileUrlKey] + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                                html += '<i class="fas fa-eye"></i> View File';
                                html += '</a>';
                                html += '</div>';
                            } else if (isFileField && value) {
                                // Fallback to local asset URL construction
                                hasAdditionalInfo = true;
                                var fileUrl = '{{ asset("uploads/onboarding/") }}/' + value;
                                html += '<div class="inline-field"><strong>' + displayName + ':</strong> ';
                                html += '<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                                html += '<i class="fas fa-eye"></i> View File';
                                html += '</a>';
                                html += '</div>';
                            } else {
                                hasAdditionalInfo = true;
                                html += '<div class="inline-field"><strong>' + displayName + ':</strong><span class="text-muted">' + displayValue + '</span></div>';
                            }
                        }
                    }
                }
                
                if (!hasAdditionalInfo) {
                    html += '<div class="text-muted">No additional information available</div>';
                }
                html += '</div>';
                
                html += '</div>';
                
                // Create and insert the details row
                var $detailsRow = $('<tr class="details-row"><td colspan="9">' + html + '</td></tr>');
                $row.after($detailsRow);
                
                // Update button state
                $button.addClass('expanded').html('<i class="fas fa-eye-slash"></i>');
            }
        },
        error: function(xhr) {
            console.error('Error loading driver details:', xhr);
            alert('Error loading driver details. Please try again.');
        }
    });
}

// Legacy AJAX-based toggle function (kept as fallback)
function toggleDriverDetails(driverId) {
    var $button = $('button[data-driver-id="' + driverId + '"]');
    var $row = $button.closest('tr');
    var $detailsRow = $row.next('.details-row');
    
    // If details row exists, remove it
    if ($detailsRow.length > 0) {
        $detailsRow.remove();
        $button.removeClass('expanded').html('<i class="fas fa-eye"></i>');
        return;
    }
    
    // Otherwise, fetch and show details
    $.ajax({
        url: '{{ url("admin/drivers") }}/' + driverId + '/details',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var driver = response.driver;
                var customFields = response.customFields || [];
                var html = '<div class="details-content">';
                
                // Create a map of custom field names for better display
                var customFieldMap = {};
                customFields.forEach(function(field) {
                    customFieldMap[field.field_name.toLowerCase().replace(/\s+/g, '_')] = field.field_name;
                });
                
                // Basic Information - Inline layout
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>License Number:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
                
                // License Expiry Date (if available)
                if (driver.license_expiry || driver.license_expiry_date || driver.driver_license_expiry) {
                    var licenseExpiry = driver.license_expiry || driver.license_expiry_date || driver.driver_license_expiry;
                    var formattedDate = '';
                    try {
                        var date = new Date(licenseExpiry);
                        if (!isNaN(date.getTime())) {
                            var day = String(date.getDate()).padStart(2, '0');
                            var month = String(date.getMonth() + 1).padStart(2, '0');
                            var year = date.getFullYear();
                            formattedDate = day + '/' + month + '/' + year;
                        } else {
                            formattedDate = licenseExpiry;
                        }
                    } catch (e) {
                        formattedDate = licenseExpiry;
                    }
                    html += '<div class="inline-field"><strong>License Expiry:</strong><span class="text-muted">' + formattedDate + '</span></div>';
                }
                
                // Address (if available)
                if (driver.address) {
                    html += '<div class="inline-field"><strong>Address:</strong><span class="text-muted">' + driver.address + '</span></div>';
                }
                
                // Emergency Contact (if available)
                if (driver.emergency_contact || driver.emergency_contact_name) {
                    var emergencyName = driver.emergency_contact || driver.emergency_contact_name;
                    html += '<div class="inline-field"><strong>Emergency Contact:</strong><span class="text-muted">' + emergencyName + '</span></div>';
                }
                
                // Emergency Phone (if available)
                if (driver.emergency_phone || driver.emergency_contact_phone || driver.emergency_contact_number) {
                    var emergencyPhone = driver.emergency_phone || driver.emergency_contact_phone || driver.emergency_contact_number;
                    html += '<div class="inline-field"><strong>Emergency Phone:</strong><span class="text-muted">' + emergencyPhone + '</span></div>';
                }
                
                // Vehicle Selection (if available)
                if (driver.vehicle_details) {
                    var vehicleDisplay = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name;
                    html += '<div class="inline-field"><strong>Vehicle Selection:</strong><span class="text-muted">' + vehicleDisplay + '</span></div>';
                } else if (driver.vehicle_selection) {
                    html += '<div class="inline-field"><strong>Vehicle Selection:</strong><span class="text-muted">' + driver.vehicle_selection + '</span></div>';
                }
                
                // Scheme Selection (if available)
                if (driver.scheme || driver.scheme_selection) {
                    var scheme = driver.scheme || driver.scheme_selection;
                    html += '<div class="inline-field"><strong>Scheme Selection:</strong><span class="text-muted">' + scheme + '</span></div>';
                }
                
                // Insurance Selection (if available)
                if (driver.insurance_selection) {
                    var insuranceDisplay = driver.insurance_selection === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
                    html += '<div class="inline-field"><strong>Insurance Selection:</strong><span class="text-muted">' + insuranceDisplay + '</span></div>';
                }
                
                var statusClass = driver.is_active == 1 ? 'success' : 'secondary';
                html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.is_active == 1 ? 'Active' : 'Inactive') + '</span></div>';
                html += '</div>';
                
                // Assigned Vehicle Information
                if (driver.assigned_vehicle) {
                    html += '<div class="mb-3">';
                    html += '<div class="inline-field"><strong>Assigned Vehicle:</strong><span class="text-muted">' + driver.assigned_vehicle.license_plate + ' (' + driver.assigned_vehicle.make_name + ' ' + driver.assigned_vehicle.model_name + ')</span></div>';
                    html += '</div>';
                }
                
                // Documents Section - License and Insurance Images
                if (driver.license_image || driver.license_upload_path || driver.insurance_image || driver.insurance_upload_path || driver.documents) {
                    html += '<div class="mb-3">';
                    html += '<h6><strong>Documents:</strong></h6>';
                    
                    // License Image Button
                    if (driver.license_image || driver.license_upload_path) {
                        var licenseFile = driver.license_upload_path || driver.license_image;
                        var licenseUrl = driver.license_url || ('{{ asset("uploads") }}' + '/' + licenseFile);
                        html += '<div class="inline-field">';
                        html += '<strong>License Image:</strong>';
                        html += '<a href="' + licenseUrl + '" target="_blank" class="btn btn-sm btn-primary ml-2">';
                        html += '<i class="fas fa-eye"></i> View License';
                        html += '</a>';
                        html += '</div>';
                    }
                    
                    // Insurance Image Button
                    if (driver.insurance_image || driver.insurance_upload_path || driver.documents) {
                        var insuranceFile = driver.insurance_upload_path || driver.insurance_image || driver.documents;
                        var insuranceUrl = driver.insurance_url || ('{{ asset("uploads") }}' + '/' + insuranceFile);
                        html += '<div class="inline-field">';
                        html += '<strong>Insurance Image:</strong>';
                        html += '<a href="' + insuranceUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                        html += '<i class="fas fa-eye"></i> View Insurance';
                        html += '</a>';
                        html += '</div>';
                    }
                    
                    html += '</div>';
                }
                
                // Additional Information (All Custom Fields and Onboarding Data)
                var hasAdditionalInfo = false;
                html += '<div class="mb-3">';
                html += '<h6><strong>Additional Information:</strong></h6>';
                
                // Get all custom fields for proper display names
                var customFieldMap = {};
                if (response.customFields) {
                    response.customFields.forEach(function(field) {
                        customFieldMap['custom_' + field.id] = field.field_name;
                    });
                }
                
                // List of fields to display (excluding basic info already shown)
                var fieldsToExclude = [
                    'id', 'name', 'email', 'phone', 'license_number', 'is_active', 'user_type', 'group_id', 
                    'api_token', 'password', 'remember_token', 'created_at', 'updated_at', 'user_id',
                    'assigned_vehicle', 'vehicle_details', 'custom_data', // custom_data will be handled separately
                    'license_upload_path', 'insurance_upload_path', 'license_image', 'insurance_image', 'documents',
                    'id_proof_type', 'is_available', 'first_name', 'last_name', 'phone_code', 'method', 
                    'edit', 'emp_id', 'contract_number', 'driver_image', 'license', 'terms', 'token',
                    'detail_id', 'license_url', 'insurance_url',
                    // Exclude fields that are now shown in basic info section
                    'license_expiry', 'license_expiry_date', 'driver_license_expiry',
                    'address', 'emergency_contact', 'emergency_phone', 'emergency_contact_name', 
                    'emergency_contact_phone', 'emergency_contact_number',
                    'vehicle_selection', 'scheme', 'scheme_selection', 'insurance_selection'
                ];
                
                // Display all driver metadata fields
                for (var key in driver) {
                    var keyLower = key.toLowerCase();
                    if (driver.hasOwnProperty(key) && !fieldsToExclude.includes(key) && !fieldsToExclude.includes(keyLower)) {
                        var value = driver[key];
                        var displayName = '';
                        var displayValue = '';
                        var isFileField = false;
                        
                        // Get proper display name
                        if (customFieldMap[key]) {
                            displayName = customFieldMap[key];
                        } else if (key.startsWith('custom_')) {
                            // Handle custom fields with IDs
                            var fieldId = key.replace('custom_', '');
                            if (response.customFields) {
                                response.customFields.forEach(function(field) {
                                    if (field.id == fieldId) {
                                        displayName = field.field_name;
                                        isFileField = (field.field_type === 'file');
                                    }
                                });
                            }
                            if (!displayName) {
                                displayName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            }
                        } else {
                            displayName = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        }
                        
                        // Rename specific fields
                        if (key.toLowerCase() === 'exp_date' || key === 'Exp Date') {
                            displayName = 'License Expiry Date';
                        }
                        if (key.toLowerCase() === 'econtact') {
                            displayName = 'Emergency Contact';
                        }
                        
                        // Check if it's a file field
                        if (key.includes('_upload_path') || key.includes('_image') || isFileField) {
                            isFileField = true;
                        }
                        
                        // Additional check: if the value looks like a file path, treat it as a file field
                        if (!isFileField && value && typeof value === 'string') {
                            var fileExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx', '.txt', '.zip', '.rar', '.xlsx', '.xls'];
                            var hasFileExtension = fileExtensions.some(function(ext) {
                                return value.toLowerCase().includes(ext.toLowerCase());
                            });
                            
                            // Also check for common file path patterns
                            var hasFilePathPattern = value.includes('/') || value.includes('\\') || 
                                                   value.includes('onboarding/documents/') || 
                                                   value.includes('uploads/');
                            
                            if (hasFileExtension || hasFilePathPattern) {
                                isFileField = true;
                                console.log('Detected file field by pattern:', key, value);
                            }
                        }
                        
                        // Format all date fields to dd/mm/yyyy
                        if (key.toLowerCase().includes('date') || key.toLowerCase().includes('expiry') || key.toLowerCase().includes('exp')) {
                            if (value && typeof value === 'string') {
                                try {
                                    var date = new Date(value);
                                    if (!isNaN(date.getTime())) {
                                        var day = String(date.getDate()).padStart(2, '0');
                                        var month = String(date.getMonth() + 1).padStart(2, '0');
                                        var year = date.getFullYear();
                                        value = day + '/' + month + '/' + year;
                                    }
                                } catch (e) {
                                    // Keep original value if date parsing fails
                                }
                            }
                        }
                        
                        // Format the value
                        if (Array.isArray(value)) {
                            if (value.length === 0) {
                                displayValue = '<span class="text-muted">No data provided</span>';
                            } else {
                                displayValue = value.join(', ');
                            }
                        } else if (typeof value === 'object' && value !== null) {
                            // Skip object values to avoid "[object Object]" display
                            // These are likely complex data structures that should be handled elsewhere
                            continue;
                        } else if (value !== null && value !== undefined && value !== '' && value.toString().trim() !== '' && value.toString().trim() !== 'null' && value.toString().trim() !== 'undefined') {
                            displayValue = value.toString();
                        } else {
                            displayValue = '<span class="text-muted">No data provided</span>';
                        }
                        
                        // Only show fields that have meaningful data and exclude unwanted fields
                        var lowerDisplayName = displayName.toLowerCase();
                        var lowerKey = key.toLowerCase();
                        if (displayValue !== '<span class="text-muted">No data provided</span>' && displayValue !== '' && 
                            lowerDisplayName !== 'meta data' && lowerDisplayName !== 'license upload path' &&
                            lowerDisplayName !== 'documents' && lowerDisplayName !== 'id proof type' &&
                            lowerDisplayName !== 'license' && lowerDisplayName !== 'terms' &&
                            lowerDisplayName !== 'token' && lowerDisplayName !== 'method' &&
                            lowerKey !== 'documents' && lowerKey !== 'id_proof_type' && lowerKey !== 'license' && 
                            lowerKey !== 'terms' && lowerKey !== 'token' && lowerKey !== 'method' &&
                            lowerKey !== 'all_metas' && key !== 'all_metas') {
                            hasAdditionalInfo = true;
                            html += '<div class="inline-field">';
                            html += '<strong>' + displayName + ':</strong>';
                            
                            if (isFileField && value && value.toString().trim() !== '') {
                                // Handle file uploads
                                var fileUrl;
                                if (value.indexOf('onboarding/documents/') === 0) {
                                    // New format: onboarding/documents/filename
                                    fileUrl = '{{ asset("storage/") }}/' + value;
                                } else if (value.indexOf('uploads/') === 0) {
                                    // Already has uploads/ prefix
                                    fileUrl = '{{ asset("") }}/' + value;
                                } else {
                                    // Old format: filename (stored in uploads/onboarding/)
                                    fileUrl = '{{ asset("uploads/onboarding/") }}/' + value;
                                }
                                
                                html += '<a href="' + fileUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                                html += '<i class="fas fa-eye"></i> View File';
                                html += '</a>';
                            } else {
                                html += '<span class="text-muted">' + displayValue + '</span>';
                            }
                            
                            html += '</div>';
                        }
                    }
                }
                
                // Handle custom_data if it exists (for legacy data)
                if (driver.custom_data && typeof driver.custom_data === 'object') {
                    // Fields already displayed in basic info section - exclude from additional info
                    var excludedCustomFields = ['token', 'terms', 'documents', 'id_proof_type', 'license',
                        'license_expiry', 'license_expiry_date', 'driver_license_expiry',
                        'address', 'emergency_contact', 'emergency_phone', 'emergency_contact_name',
                        'emergency_contact_phone', 'emergency_contact_number',
                        'vehicle_selection', 'scheme', 'scheme_selection', 'insurance_selection'];
                    
                    for (var customKey in driver.custom_data) {
                        if (driver.custom_data.hasOwnProperty(customKey) && 
                            !excludedCustomFields.includes(customKey) && !customKey.endsWith('_url')) {
                            var customValue = driver.custom_data[customKey];
                            var customDisplayName = '';
                            var customDisplayValue = '';
                            var customIsFileField = false;
                            
                            // Get proper display name for custom fields
                            if (customKey.startsWith('custom_')) {
                                var customFieldId = customKey.replace('custom_', '');
                                if (response.customFields) {
                                    response.customFields.forEach(function(field) {
                                        if (field.id == customFieldId) {
                                            customDisplayName = field.field_name;
                                            customIsFileField = (field.field_type === 'file');
                                        }
                                    });
                                }
                            }
                            
                            if (!customDisplayName) {
                                customDisplayName = customKey.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            }
                            
                            // Rename specific fields
                            if (customKey.toLowerCase() === 'exp_date' || customKey === 'Exp Date') {
                                customDisplayName = 'License Expiry Date';
                            }
                            if (customKey.toLowerCase() === 'econtact') {
                                customDisplayName = 'Emergency Contact';
                            }
                            
                            // Additional check: if the value looks like a file path, treat it as a file field
                            if (!customIsFileField && customValue && typeof customValue === 'string') {
                                var fileExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx', '.txt', '.zip', '.rar', '.xlsx', '.xls'];
                                var hasFileExtension = fileExtensions.some(function(ext) {
                                    return customValue.toLowerCase().includes(ext.toLowerCase());
                                });
                                
                                // Also check for common file path patterns
                                var hasFilePathPattern = customValue.includes('/') || customValue.includes('\\') || 
                                                       customValue.includes('onboarding/documents/') || 
                                                       customValue.includes('uploads/');
                                
                                if (hasFileExtension || hasFilePathPattern) {
                                    customIsFileField = true;
                                    console.log('Detected custom file field by pattern:', customKey, customValue);
                                }
                            }
                            
                            // Format all date fields to dd/mm/yyyy for custom fields
                            if (customKey.toLowerCase().includes('date') || customKey.toLowerCase().includes('expiry') || customKey.toLowerCase().includes('exp')) {
                                if (customValue && typeof customValue === 'string') {
                                    try {
                                        var date = new Date(customValue);
                                        if (!isNaN(date.getTime())) {
                                            var day = String(date.getDate()).padStart(2, '0');
                                            var month = String(date.getMonth() + 1).padStart(2, '0');
                                            var year = date.getFullYear();
                                            customValue = day + '/' + month + '/' + year;
                                        }
                                    } catch (e) {
                                        // Keep original value if date parsing fails
                                    }
                                }
                            }
                            
                            // Format the custom value
                            if (Array.isArray(customValue)) {
                                if (customValue.length === 0) {
                                    customDisplayValue = '<span class="text-muted">No data provided</span>';
                                } else {
                                    customDisplayValue = customValue.join(', ');
                                }
                            } else if (typeof customValue === 'object' && customValue !== null) {
                                customDisplayValue = JSON.stringify(customValue);
                            } else if (customValue !== null && customValue !== undefined && customValue !== '' && customValue.toString().trim() !== '' && customValue.toString().trim() !== 'null' && customValue.toString().trim() !== 'undefined') {
                                customDisplayValue = customValue.toString();
                            } else {
                                customDisplayValue = '<span class="text-muted">No data provided</span>';
                            }
                            
                            // Only show custom fields that have meaningful data and exclude unwanted fields
                            var lowerCustomDisplayName = customDisplayName.toLowerCase();
                            var lowerCustomKey = customKey.toLowerCase();
                            if (customDisplayValue !== '<span class="text-muted">No data provided</span>' && customDisplayValue !== '' && 
                                lowerCustomDisplayName !== 'meta data' && lowerCustomDisplayName !== 'license upload path' &&
                                lowerCustomDisplayName !== 'documents' && lowerCustomDisplayName !== 'id proof type' &&
                                lowerCustomDisplayName !== 'license' && lowerCustomDisplayName !== 'terms' &&
                                lowerCustomDisplayName !== 'token' && lowerCustomDisplayName !== 'method' &&
                                lowerCustomKey !== 'documents' && lowerCustomKey !== 'id_proof_type' && lowerCustomKey !== 'license' && 
                                lowerCustomKey !== 'terms' && lowerCustomKey !== 'token' && lowerCustomKey !== 'method') {
                                hasAdditionalInfo = true;
                                html += '<div class="inline-field">';
                                html += '<strong>' + customDisplayName + ':</strong>';
                                
                                if (customIsFileField && customValue && customValue.toString().trim() !== '') {
                                    // Handle custom file uploads
                                    var customFileUrl;
                                    if (customValue.indexOf('onboarding/documents/') === 0) {
                                        customFileUrl = '{{ asset("storage/") }}/' + customValue;
                                    } else if (customValue.indexOf('uploads/') === 0) {
                                        // Already has uploads/ prefix
                                        customFileUrl = '{{ asset("") }}/' + customValue;
                                    } else {
                                        customFileUrl = '{{ asset("uploads/onboarding/") }}/' + customValue;
                                    }
                                    
                                    html += '<a href="' + customFileUrl + '" target="_blank" class="btn btn-sm btn-info ml-2">';
                                    html += '<i class="fas fa-eye"></i> View File';
                                    html += '</a>';
                                } else {
                                    html += '<span class="text-muted">' + customDisplayValue + '</span>';
                                }
                                
                                html += '</div>';
                            }
                        }
                    }
                }
                
                if (!hasAdditionalInfo) {
                    html += '<div class="inline-field"><strong>Additional Information:</strong><span class="text-muted">No additional information provided.</span></div>';
                }
                
                html += '</div>';
                html += '</div>';
                
                // Create and insert the details row
                var $detailsRow = $('<tr class="details-row"><td colspan="9">' + html + '</td></tr>');
                $row.after($detailsRow);
                
                // Update button state
                $button.addClass('expanded').html('<i class="fas fa-eye-slash"></i>');
            }
        },
        error: function(xhr) {
            console.log('Error details:', xhr);
            var errorMessage = 'Error loading driver details';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                errorMessage = 'Error: ' + xhr.responseText;
            }
            alert(errorMessage);
        }
    });
}
</script>
{{-- Driver details toggle script end --}}

{{-- Enhanced Import Modal JavaScript --}}
<script>
$(document).ready(function() {
    // File upload drag and drop functionality
    const fileDropZone = $('#fileDropZone');
    const fileInput = $('#fileInput');
    const fileName = $('#fileName');
    const fileNameText = $('#fileNameText');
    const removeFile = $('#removeFile');
    const importBtn = $('#importBtn');
    const importForm = $('#importForm');
    const importProgress = $('#importProgress');
    const importResults = $('#importResults');
    const progressBar = $('.progress-bar');
    const progressText = $('#progressText');
    const importStats = $('#importStats');

    // Click to browse files
    fileDropZone.on('click', function() {
        fileInput.click();
    });

    // File input change
    fileInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            handleFileSelection(file);
        }
    });

    // Drag and drop events
    fileDropZone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    fileDropZone.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    fileDropZone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            // Create a new FileList and assign to the input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput[0].files = dataTransfer.files;
            handleFileSelection(file);
        }
    });

    // Remove file
    removeFile.on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        fileName.hide();
        fileDropZone.show();
        importBtn.prop('disabled', true);
    });

    function handleFileSelection(file) {
        // Validate file type
        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            new PNotify({
                title: 'Invalid File Type',
                text: 'Please select an Excel (.xlsx, .xls) or CSV file.',
                type: 'error'
            });
            return;
        }

        // Validate file size (5MB max)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            new PNotify({
                title: 'File Too Large',
                text: 'File size must be less than 5MB.',
                type: 'error'
            });
            return;
        }

        // Show file name
        fileNameText.text(file.name);
        fileName.show();
        fileDropZone.hide();
        importBtn.prop('disabled', false);
    }

    // Form submission with progress
    importForm.on('submit', function(e) {
        e.preventDefault();
        
        // Check if file is selected
        if (!fileInput[0].files.length) {
            new PNotify({
                title: 'No File Selected',
                text: 'Please select a file to import.',
                type: 'error'
            });
            return;
        }
        
        const formData = new FormData(this);
        
        // Show progress
        importProgress.show();
        importResults.hide();
        importBtn.prop('disabled', true);
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.css('width', progress + '%');
            progressText.text('Processing... ' + Math.round(progress) + '%');
        }, 200);

        // Submit form via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                clearInterval(progressInterval);
                progressBar.css('width', '100%');
                progressText.text('Import completed!');
                
                setTimeout(function() {
                    importProgress.hide();
                    
                    // Show results
                    if (response.success) {
                        importStats.html(`
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-success">${response.stats.successfully_imported || 0}</h5>
                                        <small>Successfully Imported</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-warning">${response.stats.duplicates_skipped || 0}</h5>
                                        <small>Duplicates Skipped</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-danger">${response.stats.validation_failed || 0}</h5>
                                        <small>Validation Errors</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-info">${response.stats.total_rows || 0}</h5>
                                        <small>Total Rows</small>
                                    </div>
                                </div>
                            </div>
                        `);
                        importResults.show();
                        
                        // Reload the table data from server
                        loadDriversSimple();
                        
                        new PNotify({
                            title: 'Import Successful!',
                            text: `Successfully imported ${response.stats.successfully_imported || 0} drivers.`,
                            type: 'success'
                        });
                    }
                }, 1000);
            },
            error: function(xhr) {
                clearInterval(progressInterval);
                importProgress.hide();
                
                let errorMessage = 'An error occurred during import.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // Use default error message
                    }
                }
                
                new PNotify({
                    title: 'Import Failed',
                    text: errorMessage,
                    type: 'error'
                });
            },
            complete: function() {
                importBtn.prop('disabled', false);
            }
        });
    });

    // Reset modal when closed
    $('#import').on('hidden.bs.modal', function() {
        fileInput.val('');
        fileName.hide();
        fileDropZone.show();
        importProgress.hide();
        importResults.hide();
        progressBar.css('width', '0%');
        importBtn.prop('disabled', true);
    });
});
</script>
{{-- Enhanced Import Modal JavaScript end --}}

<style>
/* Driver details styling */
.details-content {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin: 10px 0;
}

.inline-field {
    display: inline-block;
    margin-right: 30px;
    margin-bottom: 10px;
    min-width: 200px;
}

.inline-field strong {
    color: #495057;
    margin-right: 8px;
}

.inline-field .text-muted {
    color: #6c757d;
}

.details-row td {
    padding: 0 !important;
    border-top: none !important;
}
</style>

@endsection