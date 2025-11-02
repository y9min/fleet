@extends('layouts.app')

@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.users')@lang('fleet.managers')</li>
@endsection
@section('extra_css')
<style type="text/css">
  .show-password-button{
    outline: none;
    border: 1px solid #ced4da;
  }
</style>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">@lang('fleet.manageUsers')@lang('fleet.managers') &nbsp;
          @can('Users add')<a href="{{route('users.create')}}" class="btn btn-success" title="@lang('fleet.addUser')"><i
              class="fa fa-plus"></i></a>@endcan
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table table-bordered table-striped" id="ajax_data_table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Company</th>
              <th>Created</th>
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
  $("#del_btn").on("click",function(){
    var id=$(this).data("submit");
    $("#form_"+id).submit();
  });

  $('#myModal').on('show.bs.modal', function(e) {
    var id = e.relatedTarget.dataset.id;
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
      buttons: [
          {
        extend: 'print',
        text: '<i class="fa fa-print"></i> {{__("fleet.print")}}',

        exportOptions: {
           columns: [0, 1, 2, 3],
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
                columns: [0, 1, 2, 3]
            }
        }
    ],
          "language": {
              "url": '{{ asset("assets/datatables/")."/".__("fleet.datatable_lang") }}',
          },
         processing: true,
         serverSide: true,
         ajax: {
          url: "{{ url('admin/users-fetch') }}",
          type: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          data: function(d) {
            // Return default DataTables params
            return d;
          },
          error: function(xhr, error, thrown) {
            console.error('DataTables AJAX Error:', error, thrown);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            
            // Show user-friendly error message
            if ($('#usersDataTableError').length === 0) {
              $('#ajax_data_table').after('<div id="usersDataTableError" class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error loading users data!</strong> Please refresh the page or contact support if the problem persists.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
            }
          }
         },
         columns: [
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'company', name: 'company', searchable: false, orderable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', searchable: false, orderable: false}
        ],
        order: [[3, 'desc']]
    });
  });
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
@endsection