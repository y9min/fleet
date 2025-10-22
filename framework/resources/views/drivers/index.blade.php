@extends('layouts.app')
@php($date_format_setting=(Hyvikk::get('date_format'))?Hyvikk::get('date_format'):'d-m-Y')
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

  

    

    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('menu.drivers') &nbsp;
          @can('Drivers add') <a href="{{ route('drivers.create') }}" class="btn btn-success"
            title="@lang('fleet.addDriver')"> <i class="fa fa-plus"></i> </a> @endcan
          @can('Drivers import') &nbsp;&nbsp;<button data-toggle="modal" data-target="#import"
            class="btn btn-warning">@lang('fleet.import')</button>@endcan
        </h3>
      </div>

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
              <th>Documents</th>
              <th>@lang('fleet.is_active')</th>
              <th>Assigned Vehicle</th>
              <th>@lang('fleet.action')</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="import" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">@lang('fleet.importDrivers')</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        {!! Form::open(['url'=>'admin/import-drivers','method'=>'POST','files'=>true]) !!}
        <div class="form-group">
          {!! Form::label('excel',__('fleet.importDrivers'),['class'=>"form-label"]) !!}
          {!! Form::file('excel',['class'=>"form-control",'required']) !!}
        </div>
        <div class="form-group">
          <a href="{{ asset('assets/samples/drivers.xlsx') }}">@lang('fleet.downloadSampleExcel')</a>
        </div>
        <div class="form-group">
          <h6 class="text-muted">@lang('fleet.note'):</h6>
          <ul class="text-muted">
            <li>@lang('fleet.driverImportNote1')</li>
            <li>@lang('fleet.driverImportNote2')</li>
            <li>@lang('fleet.driverImportNote3')</li>
            <li>@lang('fleet.excelNote')</li>
            <li>@lang('fleet.skipNote')</li>
          </ul>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-warning" type="submit">@lang('fleet.import')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fleet.close')</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
<!-- Modal -->

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
  $(function(){
    
    var table = $('#ajax_data_table').DataTable({
          dom: 'Bfrtip',
          pageLength: 10, // Start with 10 rows for faster initial load
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]], // Page size options
          deferRender: true, // Improve rendering performance for large datasets
          buttons: [
              {
            extend: 'print',
            text: '<i class="fa fa-print"></i> {{__("fleet.print")}}',

            exportOptions: {
              columns: ([1,2,3,4,5,6]),
            },
            customize: function ( win ) {
                 
                    $(win.document.body).find( 'table' )
                        .addClass( 'table-bordered' );
                    // $(win.document.body).find( 'td' ).css( 'font-size', '10pt' );

                },
                
              },
              {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6]
                }
            }
        ],
          "language": {
              "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('admin/drivers-fetch') }}",
          type: 'POST',
          data: function(d) {
            d._token = $('meta[name="csrf-token"]').attr('content');
          }
         },
         columns: [
            {data: 'check',name:'check', searchable:false, orderable:false},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'license_number', name: 'license_number'},
            {data: 'documents', name: 'documents', searchable:false, orderable:false},
            {data: 'is_active', name: 'is_active'},
            {data: 'assigned_vehicle', name: 'assigned_vehicle', orderable: false},
            {data: 'action',name:'action',  searchable:false, orderable:false}
        ],
        order: [[1, 'desc']],
        "initComplete": function() {
              table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                  // console.log($(this).parent().index());
                    that.search(this.value).draw();
                });
              });
            }
    });
  });
  $(document).on('click','input[type="checkbox"]',function(){
    if(this.checked){
      $('#bulk_delete').prop('disabled',false);
    }else { 
      if($("input[name='ids[]']:checked").length == 0){
        $('#bulk_delete').prop('disabled',true);
      } 
    } 
    
  });

  $('#bulk_delete').on('click',function(){
    // console.log($( "input[name='ids[]']:checked" ).length);
    if($( "input[name='ids[]']:checked" ).length == 0){
      $('#bulk_delete').prop('type','button');
        new PNotify({
            title: 'Failed!',
            text: "@lang('fleet.delete_error')",
            type: 'error'
          });
        $('#bulk_delete').attr('disabled',true);
    }
    if($("input[name='ids[]']:checked").length > 0){
      // var favorite = [];
      $.each($("input[name='ids[]']:checked"), function(){
          // favorite.push($(this).val());
          $("#bulk_hidden").append('<input type=hidden name=ids[] value='+$(this).val()+'>');
      });
      // console.log(favorite);
    }
  });


  $('#chk_all').on('click',function(){
    if(this.checked){
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",true);
      });
    }else{
      $('.checkbox').each(function(){
        $('.checkbox').prop("checked",false);
      });
      $('#bulk_delete').prop('disabled',true);
    }
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
    // Handle driver status toggle changes
    $(document).on('change', '.driver-status-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var driverId = $(this).data('driver-id');
        var isChecked = $(this).is(':checked');
        var action = isChecked ? 'enable' : 'disable';
        var toggle = $(this);
        
        console.log('Toggle changed:', driverId, action, isChecked);
        
        // Disable toggle during request
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
                
                // Reload the table to reflect changes
                $('#ajax_data_table').DataTable().ajax.reload();
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
            }
        });
        
        return false; // Prevent any default behavior
    });
    
    // Also handle click events as a fallback
    $(document).on('click', '.driver-status-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var driverId = $(this).data('driver-id');
        var isChecked = $(this).is(':checked');
        var action = isChecked ? 'enable' : 'disable';
        var toggle = $(this);
        
        console.log('Toggle clicked:', driverId, action, isChecked);
        
        // Disable toggle during request
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
                
                // Reload the table to reflect changes
                $('#ajax_data_table').DataTable().ajax.reload();
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
    
    // Get driver data from button's data attribute
    var driver = $button.data('driver-info');
    
    // Build HTML instantly (no AJAX delay)
    var html = '<div class="details-content">';
    
    // Basic Information
    html += '<div class="mb-3">';
    html += '<div class="inline-field"><strong>Name:</strong><span class="text-muted">' + (driver.name || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>Email:</strong><span class="text-muted">' + (driver.email || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>Phone:</strong><span class="text-muted">' + (driver.phone || 'N/A') + '</span></div>';
    html += '<div class="inline-field"><strong>License Number:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
    var statusClass = driver.is_active == 1 ? 'success' : 'secondary';
    html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.is_active == 1 ? 'Active' : 'Inactive') + '</span></div>';
    html += '</div>';
    
    // Assigned Vehicle
    if (driver.assigned_vehicle) {
        html += '<div class="mb-3">';
        html += '<div class="inline-field"><strong>Assigned Vehicle:</strong><span class="text-muted">' + 
                driver.assigned_vehicle.license_plate + ' (' + 
                driver.assigned_vehicle.make_name + ' ' + 
                driver.assigned_vehicle.model_name + ')</span></div>';
        html += '</div>';
    }
    
    // Additional Information (from meta fields)
    var hasAdditionalInfo = false;
    html += '<div class="mb-3">';
    html += '<h6><strong>Additional Information:</strong></h6>';
    
    // Display additional meta fields
    for (var key in driver) {
        if (driver.hasOwnProperty(key) && 
            !['id', 'name', 'email', 'phone', 'license_number', 'is_active', 'assigned_vehicle'].includes(key)) {
            var value = driver[key];
            if (value && value !== 'N/A' && value !== '') {
                hasAdditionalInfo = true;
                var displayName = key.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                html += '<div class="inline-field"><strong>' + displayName + ':</strong><span class="text-muted">' + value + '</span></div>';
            }
        }
    }
    
    if (!hasAdditionalInfo) {
        html += '<div class="text-muted">No additional information available</div>';
    }
    html += '</div>';
    
    html += '</div>';
    
    // Create and insert the details row instantly
    var $detailsRow = $('<tr class="details-row"><td colspan="9">' + html + '</td></tr>');
    $row.after($detailsRow);
    
    // Update button state
    $button.addClass('expanded').html('<i class="fas fa-eye-slash"></i>');
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
                var statusClass = driver.is_active == 1 ? 'success' : 'secondary';
                html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.is_active == 1 ? 'Active' : 'Inactive') + '</span></div>';
                html += '</div>';
                
                // Assigned Vehicle Information
                if (driver.assigned_vehicle) {
                    html += '<div class="mb-3">';
                    html += '<div class="inline-field"><strong>Assigned Vehicle:</strong><span class="text-muted">' + driver.assigned_vehicle.license_plate + ' (' + driver.assigned_vehicle.make_name + ' ' + driver.assigned_vehicle.model_name + ')</span></div>';
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
                    'license_upload_path', // Remove license upload path from display
                    'documents', 'id_proof_type', 'license', 'terms', 'token' // Remove unwanted fields
                ];
                
                // Display all driver metadata fields
                for (var key in driver) {
                    if (driver.hasOwnProperty(key) && !fieldsToExclude.includes(key)) {
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
                        
                        // Format the value
                        if (Array.isArray(value)) {
                            if (value.length === 0) {
                                displayValue = '<span class="text-muted">No data provided</span>';
                            } else {
                                displayValue = value.join(', ');
                            }
                        } else if (typeof value === 'object' && value !== null) {
                            displayValue = JSON.stringify(value);
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
                            lowerDisplayName !== 'token' && lowerKey !== 'documents' && 
                            lowerKey !== 'id_proof_type' && lowerKey !== 'license' && 
                            lowerKey !== 'terms' && lowerKey !== 'token') {
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
                    for (var customKey in driver.custom_data) {
                        if (driver.custom_data.hasOwnProperty(customKey) && 
                            customKey !== 'token' && customKey !== 'terms' && 
                            customKey !== 'documents' && customKey !== 'id_proof_type' && 
                            customKey !== 'license' && !customKey.endsWith('_url')) {
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
                                lowerCustomDisplayName !== 'token' && lowerCustomKey !== 'documents' && 
                                lowerCustomKey !== 'id_proof_type' && lowerCustomKey !== 'license' && 
                                lowerCustomKey !== 'terms' && lowerCustomKey !== 'token') {
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