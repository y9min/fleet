@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.vehicleGroup')</li>
@endsection
@section('extra_css')
<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/plugins-dataTables.bootstrap4.min.css') }}">
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

/* Header styles - EXACT match to vehicles page */
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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Breadcrumb styles - EXACT match to vehicles page */
.breadcrumb-text {
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: #6c757d;
}

.breadcrumb-text a {
    color: #007bff;
    text-decoration: none;
    font-weight: normal;
}

.breadcrumb-text a:hover {
    text-decoration: underline;
}

/* Main content */
.content {
    padding: 15px;
}

/* Card styles */
.card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 12px 20px;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

/* Enhanced page header with modern design */
.page-header {
    background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
    color: white;
    padding: 25px 30px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 12px rgba(127, 215, 225, 0.3);
    border: none;
}
        
.page-header h1 {
    color: white;
    margin: 0;
    font-weight: 600;
    font-size: 28px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
/* Enhanced button styling to match vehicles page */
.btn-toolbar {
    margin-bottom: 1.5rem;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.btn-primary {
    background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
    border: none;
    color: white;
    box-shadow: 0 2px 4px rgba(127, 215, 225, 0.3);
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(127, 215, 225, 0.4);
    background: linear-gradient(135deg, #6BC5D2, #5BB0BD);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #20c997, #17a2b8);
    color: white;
}

.btn-outline-primary {
    border: 2px solid #7FD7E1;
    color: #7FD7E1;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #7FD7E1;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(127, 215, 225, 0.3);
}

/* Table styling to match vehicles page exactly */
.vehicle-groups-table {
    background: white;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #dee2e6;
    margin-bottom: 0;
}
        
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #333;
    border-collapse: collapse;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
.table th,
.table td {
    padding: 12px;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
        
.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
    text-align: center;
}
        
.table td {
    text-align: center;
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.05);
}

.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.checkbox,
#chk_all {
    width: 20px;
    height: 20px;
}

/* Center the first-column checkbox in header and body */
#ajax_data_table thead th:first-child,
#ajax_data_table tbody td:first-child {
    padding-left: 0;
    padding-right: 0;
    text-align: center;
}

#ajax_data_table thead th:first-child {
    position: relative;
}

#ajax_data_table thead th:first-child #chk_all {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    margin: 0;
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
        
/* Enhanced Modal Styling to match vehicles page */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    overflow: hidden;
}
        
.modal-header {
    background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 25px 30px;
    border-bottom: none;
}
        
.modal-header h4 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
.modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 1.8rem;
    text-shadow: none;
    transition: opacity 0.3s ease;
}
        
.modal-header .close:hover {
    opacity: 1;
    color: white;
    transform: scale(1.1);
}
        
.modal-body {
    padding: 30px;
    background: #fff;
}
        
.modal-footer {
    background: #f8f9fa;
    padding: 20px 30px;
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 15px 15px;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
}

/* Enhanced Alert Styling */
.alert {
    border-radius: 8px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    color: #0c5460;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    color: #856404;
}

/* Enhanced Bulk Actions Toolbar */
.bulk-actions-toolbar {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 20px;
}

.bulk-actions-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bulk-actions-left {
    display: flex;
    align-items: center;
}

.bulk-actions-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.bulk-actions-text {
    font-weight: 600;
    color: #333;
    font-size: 16px;
}

.bulk-actions-count {
    background: #7FD7E1;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-left: 10px;
}
</style>
@endsection
@section('content')
<div class="container-fluid">
    <!-- Enhanced Success Message -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> 
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Enhanced Error Messages -->
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Error!</strong> 
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Enhanced Info Messages -->
    @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle"></i> 
        <strong>Info!</strong> {{ session('info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Enhanced Warning Messages -->
    @if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Warning!</strong> {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    
    <!-- Enhanced Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1>@lang('fleet.vehicleGroup')</h1>
            <p class="mb-0" style="opacity: 0.9; font-size: 16px; margin-top: 8px;">Organize your fleet with vehicle groups</p>
        </div>
        <div class="d-flex gap-3">
            <button type="button" id="btn_open_create_group" class="btn" style="background-color: #C1C1C1; color: black; border: 1px solid #C1C1C1;" title="@lang('fleet.createGroup')" data-toggle="modal" data-target="#createGroupModal">
                <i class="fas fa-plus"></i> @lang('fleet.createGroup')
            </button>
        </div>
    </div>
    
    <!-- Bulk Actions Toolbar (match vehicles page) -->
    <div class="bulk-actions-toolbar" id="bulkToolbar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between p-4" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="d-flex align-items-center">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7FD7E1, #6BC5D2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                    <h6 class="mb-0" style="font-weight: 600; color: #333;">Bulk Actions</h6>
                    <small class="text-muted" id="selectedCount">0</small> group(s) selected
                </div>
            </div>
            <div class="bulk-actions d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="btn_clear_selection" style="border-radius: 6px; padding: 8px 16px;">
                    <i class="fas fa-times"></i> Clear Selection
                </button>
                @can('VehicleGroup delete')
                    <button type="button" class="btn btn-danger" id="bulk_delete" data-toggle="modal" data-target="#bulkModal" disabled style="border-radius: 6px; padding: 8px 16px; background: linear-gradient(135deg, #dc3545, #c82333); border: none;">
                        <i class="fas fa-trash-alt"></i> Delete Selected
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Enhanced Table Card -->
    <div class="card vehicle-groups-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="ajax_data_table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="chk_all" class="form-check-input">
                            </th>
                            <th>@lang('fleet.groupName')</th>
                            <th>@lang('fleet.description')</th>
                            <th>@lang('fleet.vehicles')</th>
                            <th style="width: 120px;">@lang('fleet.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
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
        {!! Form::open(['url'=>'admin/delete-vehicle-groups','method'=>'POST','id'=>'form_delete']) !!}
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
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Create Vehicle Group Modal -->
<div id="createGroupModal" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas fa-object-group"></i> Create Vehicle Group</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="createGroupStepSelect" style="display: block;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div style="font-weight: 600;">Select Vehicles</div>
            <div>
              <input type="text" id="vehicleSearchInput" class="form-control" placeholder="Search reg/make/model/year/fuel" style="min-width: 320px;">
            </div>
          </div>
          <div class="table-responsive" style="max-height: 60vh; overflow: auto; border: 1px solid #e9ecef; border-radius: 8px;">
            <table class="table table-striped table-bordered mb-0" id="vehiclePickTable">
              <thead>
                <tr>
                  <th style="width: 50px; text-align: center;"><input type="checkbox" id="select_all_vehicles"></th>
                  <th>Reg</th>
                  <th>Make</th>
                  <th>Model</th>
                  <th>Year</th>
                  <th>Fuel Type</th>
                </tr>
              </thead>
              <tbody>
                <!-- populated by JS -->
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted"><span id="selectedVehiclesCount">0</span> selected</small>
            <div>
              <button type="button" class="btn btn-outline-secondary" id="clearVehicleSelection">Clear</button>
              <button type="button" class="btn btn-primary" id="goToDetails" disabled>Next</button>
            </div>
          </div>
        </div>

        <div id="createGroupStepDetails" style="display: none;">
          <div class="form-group">
            <label for="groupTitle">Group Title</label>
            <input type="text" id="groupTitle" class="form-control" placeholder="Enter group title" required>
          </div>
          <div class="form-group">
            <label for="groupDescription">Description (optional)</label>
            <textarea id="groupDescription" class="form-control" rows="3" placeholder="Enter description"></textarea>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Vehicles selected: <span id="summarySelectedVehiclesCount">0</span></small>
            <div>
              <button type="button" class="btn btn-outline-secondary" id="backToSelect">Back</button>
              <button type="button" class="btn btn-success" id="submitCreateGroup" disabled>Create Group</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="vehiclePickLoading" style="display:none; position: fixed; inset: 0; background: rgba(255,255,255,0.6); align-items: center; justify-content: center; z-index: 1052;">
    <div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>
  </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
  // Store selected IDs to persist across DataTable redraws
  var selectedGroupIds = new Set();
  // Guard flag to suppress unintended DataTables reloads from checkbox interactions
  var suppressNextAjaxReload = false;

  // Enhanced delete functionality
  $("#del_btn").on("click", function () {
    var id = $(this).data("submit");
    $("#form_" + id).submit();
  });

  $('#myModal').on('show.bs.modal', function (e) {
    var id = e.relatedTarget.dataset.id;
    $("#del_btn").attr("data-submit", id);
  });

  // Enhanced checkbox functionality with bulk actions toolbar
  function updateBulkActions() {
    // Count checkboxes from both current page and stored selections
    var visibleChecked = $('#ajax_data_table').find("input[name='ids[]']:checked").length;
    var checkedCount = selectedGroupIds.size;
    
    var bulkToolbar = $('#bulkToolbar');
    var selectedCount = $('#selectedCount');
    var bulkDeleteBtn = $('#bulk_delete');
    var bulkDeleteFooterBtn = $('#bulk_delete_footer');

    if (checkedCount > 0) {
      bulkToolbar.show();
      selectedCount.text(checkedCount);
      bulkDeleteBtn.prop('disabled', false);
      if (bulkDeleteFooterBtn.length) { bulkDeleteFooterBtn.prop('disabled', false); }
    } else {
      bulkToolbar.hide();
      selectedCount.text('0');
      bulkDeleteBtn.prop('disabled', true);
      if (bulkDeleteFooterBtn.length) { bulkDeleteFooterBtn.prop('disabled', true); }
    }
  }

  // Enhanced DataTable initialization
  var table;
  $(function () {
    table = $('#ajax_data_table').DataTable({
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'print',
          text: '<i class="fas fa-print"></i> {{__("fleet.print")}}',
          className: 'btn btn-outline-primary',
          exportOptions: {
            columns: [1, 2, 3],
          },
          customize: function (win) {
            $(win.document.body).find('table').addClass('table-bordered');
            $(win.document.body).find('h1').css('color', '#333');
          },
        },
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success',
          exportOptions: {
            columns: [1, 2, 3]
          }
        }
      ],
      "language": {
        "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
      },
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ url('admin/vehicle-group-fetch') }}",
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        data: function(d) {
          // keep default DataTables params; no extra payload needed
          try { console.debug('[VehicleGroups] DT request params', d); } catch(err) {}
          // Always derive the search term from the visible DataTables filter input only,
          // to avoid checkbox interactions injecting values like 'on' into the search parameter.
          try {
            var visibleSearch = $('#ajax_data_table_filter input[type="search"]').val() || '';
            if (d.search && typeof d.search.value !== 'undefined') {
              d.search.value = visibleSearch;
            }
          } catch (e) {}

          // Clear per-column search values unless you add explicit column filters in the footer
          if (Array.isArray(d.columns)) {
            for (var i = 0; i < d.columns.length; i++) {
              if (d.columns[i] && d.columns[i].search) {
                d.columns[i].search.value = '';
              }
            }
          }
          return d;
        },
        dataSrc: function(json) {
          try { console.debug('[VehicleGroups] DT response', json); } catch(err) {}
          // Defensive: DataTables expects {data: [...]} shape
          if (json && Array.isArray(json.data)) { return json.data; }
          // Some servers return the array directly
          if (Array.isArray(json)) { return json; }
          return [];
        },
        error: function(xhr, error, thrown) {
          console.error('DataTable AJAX Error:', error, thrown);
          console.error('Status:', xhr.status);
          console.error('Response:', xhr.responseText);
          alert('Failed to load data. Check console for details.');
        }
      },
      columns: [
        { data: 'check', name: 'check', searchable: false, orderable: false },
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description' },
        { data: 'vehicle_count', name: 'vehicle_count' },       
        { data: 'action', name: 'action', searchable: false, orderable: false }
      ],
      order: [[1, 'desc']],
      "drawCallback": function(settings) {
        // Restore checkbox states after DataTable redraw
        $('#ajax_data_table tbody input[name="ids[]"]').each(function() {
          var checkboxId = $(this).val();
          if (selectedGroupIds.has(checkboxId)) {
            $(this).prop('checked', true);
          } else {
            $(this).prop('checked', false);
          }
        });
        
        // Update select all checkbox state
        checkcheckbox();
        updateBulkActions();
      },
      "initComplete": function () {
        table.columns().every(function () {
          var that = this;
          $('input', this.footer()).on('keyup change', function () {
            that.search(this.value).draw();
          });
        });
      }
    });

    // Prevent checkbox interactions from triggering a server reload
    $('#ajax_data_table').on('preXhr.dt', function(e, settings, data) {
      if (suppressNextAjaxReload) {
        suppressNextAjaxReload = false;
        try { console.debug('[VehicleGroups] Suppressed unintended DataTables reload'); } catch(err) {}
        e.preventDefault();
        return false;
      }
      return true;
    });

    // Handle checkbox clicks within DataTable (delegated event)
    $('#ajax_data_table').on('change', 'input[name="ids[]"]', function(e) {
      try {
        // Prevent any default behavior or bubbling that could trigger DataTables redraws
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
        if (e && typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();

        // Suppress any immediate DT ajax reload potentially triggered by plugins
        suppressNextAjaxReload = true;

        var checkboxId = $(this).val();

        if ($(this).is(':checked')) {
          selectedGroupIds.add(checkboxId);
        } else {
          selectedGroupIds.delete(checkboxId);
        }

        updateBulkActions();
        checkcheckbox();
      } catch (err) {
        console.error('Checkbox change handler error:', err);
      }
    });

    // Handle select all checkbox
    $('#chk_all').on('change', function (e) {
      if (e && typeof e.preventDefault === 'function') e.preventDefault();
      if (e && typeof e.stopPropagation === 'function') e.stopPropagation();
      if (e && typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();

      // Suppress any immediate DT ajax reload potentially triggered by plugins
      suppressNextAjaxReload = true;

      var isChecked = this.checked;

      // Update all visible checkboxes
      $('#ajax_data_table tbody input[name="ids[]"]').each(function() {
        $(this).prop('checked', isChecked);
        var checkboxId = $(this).val();

        if (isChecked) {
          selectedGroupIds.add(checkboxId);
        } else {
          selectedGroupIds.delete(checkboxId);
        }
      });

      checkcheckbox();
      updateBulkActions();
      return false;
    });
  });

  // ===== Create Vehicle Group Modal Logic =====
  var allVehicles = [];
  var filteredVehicles = [];
  var selectedVehicleIds = new Set();
  var vehiclePickInitialized = false;

  function renderVehiclePickTable() {
    var tbody = $('#vehiclePickTable tbody');
    tbody.empty();
    filteredVehicles.forEach(function(v) {
      var id = v.id;
      var reg = v.license_plate || '';
      var make = v.make_name || v.make || '';
      var model = v.model_name || v.model || '';
      var year = v.year || v.made_year || '';
      var fuel = v.engine_type || v.fuel || '';
      var checked = selectedVehicleIds.has(String(id)) ? 'checked' : '';
      var row = '<tr>' +
        '<td style="text-align:center;"><input type="checkbox" class="veh_chk" data-id="'+ id +'" '+ checked +'></td>' +
        '<td>'+ escapeHtml(reg) +'</td>' +
        '<td>'+ escapeHtml(make) +'</td>' +
        '<td>'+ escapeHtml(model) +'</td>' +
        '<td>'+ escapeHtml(String(year)) +'</td>' +
        '<td>'+ escapeHtml(fuel) +'</td>' +
      '</tr>';
      tbody.append(row);
    });
    updateVehicleSelectionUI();
  }

  function escapeHtml(str){
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function applyVehicleFilter(query) {
    var q = (query || '').toLowerCase();
    if (!q) {
      filteredVehicles = allVehicles.slice(0);
      return;
    }
    filteredVehicles = allVehicles.filter(function(v){
      var fields = [
        v.license_plate, v.make_name, v.model_name, v.year,
        v.engine_type, v.make, v.model, v.made_year
      ];
      return fields.some(function(f){
        return (f !== null && f !== undefined && String(f).toLowerCase().indexOf(q) !== -1);
      });
    });
  }

  function updateVehicleSelectionUI() {
    $('#selectedVehiclesCount').text(selectedVehicleIds.size);
    $('#summarySelectedVehiclesCount').text(selectedVehicleIds.size);
    $('#goToDetails').prop('disabled', selectedVehicleIds.size === 0);
    var allIdsInView = filteredVehicles.map(function(v){ return String(v.id); });
    var allChecked = allIdsInView.length > 0 && allIdsInView.every(function(id){ return selectedVehicleIds.has(id); });
    $('#select_all_vehicles').prop('checked', allChecked);
    $('#submitCreateGroup').prop('disabled', selectedVehicleIds.size === 0 || !$('#groupTitle').val());
  }

  function fetchVehiclesForPicker() {
    if (vehiclePickInitialized) return;
    vehiclePickInitialized = true;
    $('#vehiclePickLoading').css('display','flex');
    $.ajax({
      url: "{{ url('admin/vehicles-fetch') }}",
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      data: {},
      success: function(resp){
        try {
          var data = resp && resp.data ? resp.data : (Array.isArray(resp) ? resp : []);
          allVehicles = Array.isArray(data) ? data : [];
          applyVehicleFilter($('#vehicleSearchInput').val());
          renderVehiclePickTable();
        } catch (e) { console.error(e); }
      },
      error: function(xhr){
        alert('Failed to load vehicles');
      },
      complete: function(){
        $('#vehiclePickLoading').hide();
      }
    });
  }

  // Open modal -> load vehicles once
  $('#createGroupModal').on('shown.bs.modal', function(){
    fetchVehiclesForPicker();
  });

  // Search input (debounced)
  var vehicleSearchTimer;
  $('#vehicleSearchInput').on('input', function(){
    clearTimeout(vehicleSearchTimer);
    var val = $(this).val();
    vehicleSearchTimer = setTimeout(function(){
      applyVehicleFilter(val);
      renderVehiclePickTable();
    }, 200);
  });

  // Select all in current filtered view
  $(document).on('change', '#select_all_vehicles', function(){
    var check = $(this).is(':checked');
    filteredVehicles.forEach(function(v){
      var id = String(v.id);
      if (check) { selectedVehicleIds.add(id); } else { selectedVehicleIds.delete(id); }
    });
    renderVehiclePickTable();
  });

  // Row checkbox toggle
  $(document).on('change', '.veh_chk', function(){
    var id = String($(this).data('id'));
    if ($(this).is(':checked')) { selectedVehicleIds.add(id); } else { selectedVehicleIds.delete(id); }
    updateVehicleSelectionUI();
  });

  // Clear selection
  $('#clearVehicleSelection').on('click', function(){
    selectedVehicleIds.clear();
    renderVehiclePickTable();
  });

  // Step navigation
  $('#goToDetails').on('click', function(){
    $('#createGroupStepSelect').hide();
    $('#createGroupStepDetails').show();
    updateVehicleSelectionUI();
  });
  $('#backToSelect').on('click', function(){
    $('#createGroupStepDetails').hide();
    $('#createGroupStepSelect').show();
  });

  // Validation for title
  $('#groupTitle').on('input', function(){
    updateVehicleSelectionUI();
  });

  // Submit create group
  $('#submitCreateGroup').on('click', function(){
    var title = ($('#groupTitle').val() || '').trim();
    if (!title) { return; }
    var description = ($('#groupDescription').val() || '').trim();
    var ids = Array.from(selectedVehicleIds);

    $('#submitCreateGroup').prop('disabled', true).text('Creating...');

    $.ajax({
      url: "{{ route('vehicle_group.store') }}",
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      data: { name: title, description: description, vehicleIds: ids },
      success: function(){
        try {
          $('#createGroupModal').modal('hide');
          // Reload the main DataTable
          if (table) { table.ajax.reload(null, false); }
          // Toast
          if (window.PNotify) {
            new PNotify({ title: 'Success', text: 'Vehicle group created successfully!', type: 'success' });
          } else {
            alert('Vehicle group created successfully!');
          }
        } catch (e) {}
      },
      error: function(xhr){
        var msg = 'Failed to create group';
        try { msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : msg; } catch(e) {}
        if (window.PNotify) { new PNotify({ title: 'Error', text: msg, type: 'error' }); } else { alert(msg); }
      },
      complete: function(){
        $('#submitCreateGroup').prop('disabled', false).text('Create Group');
      }
    });
  });

  // Checkbox checked function
  function checkcheckbox() {
    var totalCheckboxes = $('#ajax_data_table tbody .checkbox').length;
    var checkedCheckboxes = $('#ajax_data_table tbody .checkbox:checked').length;
    
    if (checkedCheckboxes == totalCheckboxes && totalCheckboxes > 0) {
      $("#chk_all").prop('checked', true);
    } else {
      $('#chk_all').prop('checked', false);
    }
  }

  // Enhanced bulk delete functionality
  $('#bulk_delete, #bulk_delete_footer').on('click', function (e) {
    if (selectedGroupIds.size == 0) {
      e.preventDefault();
      e.stopPropagation();
      new PNotify({
        title: 'Failed!',
        text: "@lang('fleet.delete_error')",
        type: 'error'
      });
      return false;
    }
    
    // Populate hidden form fields with selected IDs before modal opens
    $("#bulk_hidden").empty();
    selectedGroupIds.forEach(function(id) {
      $("#bulk_hidden").append('<input type=hidden name=ids[] value=' + id + '>');
    });
    
    // Allow modal to open via Bootstrap's data-toggle
  });

  // Clear selections after successful form submission (on page reload, Set will be empty anyway)
  $('#form_delete').on('submit', function() {
    // Form will submit normally, selections will be cleared on redirect
    return true;
  });

  // Initialize bulk actions on page load
  $(document).ready(function() {
    updateBulkActions();
  });

  // Clear Selection button behavior (match vehicles page)
  $('#btn_clear_selection').on('click', function() {
    try {
      // Uncheck all visible checkboxes and clear Set
      $('#ajax_data_table tbody input[name="ids[]"]').each(function() {
        $(this).prop('checked', false);
      });
      selectedGroupIds.clear();
      $('#chk_all').prop('checked', false).prop('indeterminate', false);
      updateBulkActions();
    } catch (err) { console.error('Clear selection failed:', err); }
  });
</script>
@endsection