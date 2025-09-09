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
        <!-- Page Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h1>@lang('fleet.manageVehicles')</h1>
            @can('Vehicles add')
                <a href="{{ route('vehicles.create') }}" class="btn" style="background: #32CD32; color: white; border-radius: 50px; padding: 0.5rem 1rem;" title="@lang('fleet.addNew')">
                    <i class="fa fa-plus"></i>
                </a>
            @endcan
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
                                    <th>#</th>
                                    <th>@lang('fleet.vehicleImage')</th>
                                    <th>@lang('fleet.make')</th>
                                    <th>@lang('fleet.model')</th>
                                    <th>@lang('fleet.type')</th>
                                    <th>@lang('fleet.color')</th>
                                    <th>@lang('fleet.licensePlate')</th>
                                    <th>@lang('fleet.group')</th>
                                    <th>@lang('fleet.service')</th>
                                    <th>@lang('fleet.assigned_driver')</th> 
                                    <th>@lang('fleet.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>
                                        @can('Vehicles delete')
                                            <button class="btn btn-danger btn-sm" id="bulk_delete" data-toggle="modal"
                                                data-target="#bulkModal" disabled title="@lang('fleet.delete')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endcan
                                    </th>
                                    <th>#</th>
                                    <th>@lang('fleet.vehicleImage')</th>
                                    <th>@lang('fleet.make')</th>
                                    <th>@lang('fleet.model')</th>
                                    <th>@lang('fleet.type')</th>
                                    <th>@lang('fleet.color')</th>
                                    <th>@lang('fleet.licensePlate')</th>
                                    <th>@lang('fleet.group')</th>
                                    <th>@lang('fleet.service')</th>
                                    <th>@lang('fleet.assigned_driver')</th>
                                    <th>@lang('fleet.action')</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
                    <h4 class="modal-title">@lang('fleet.importVehicles')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/import-vehicles', 'method' => 'POST', 'files' => true]) !!}
                    <div class="form-group">
                        {!! Form::label('excel', __('fleet.importVehicles'), ['class' => 'form-label']) !!}
                        {!! Form::file('excel', ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        <a href="{{ asset('assets/samples/vehicles.xlsx') }}">@lang('fleet.downloadSampleExcel')</a>
                    </div>
                    <div class="form-group">
                        <h6 class="text-muted">@lang('fleet.note'):</h6>
                        <ul class="text-muted">
                            <li>@lang('fleet.vehicleImportNote1')</li>
                            <li>@lang('fleet.vehicleImportNote2')</li>
                            <li>@lang('fleet.excelNote')</li>
                            <li>@lang('fleet.fileTypeNote')</li>
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

    <!--model 2 -->
    <div id="myModal2" class="modal  fade" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('fleet.vehicle')</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div id="loader">
                        Loading data...
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        @lang('fleet.close')
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--model 2 -->
@endsection

@section('script')
    <script type="text/javascript">
        $("#del_btn").on("click", function() {
            var id = $(this).data("submit");
            $("#form_" + id).submit();
        });

        $('#myModal').on('show.bs.modal', function(e) {
            var id = e.relatedTarget.dataset.id;
            $("#del_btn").attr("data-submit", id);
        });

        // $(document).on('click', '.openBtn', function() {
        //     // alert($(this).data("id"));
        //     var id = $(this).attr("data-id");
        //     $('#myModal2 .modal-body').load('{{ url('admin/vehicle/event') }}/' + id, function(result) {
        //         $('#myModal2').modal({
        //             show: true
        //         });
        //     });
        // });
        $(document).on('click', '.openBtn', function() {
            var id = $(this).attr("data-id");
            $('#myModal2 .modal-body').html('<div id="loader">Loading data...</div>');
            $('#myModal2').modal({
                show: true
            });
            $.ajax({
                url: '{{ url('admin/vehicle/event') }}/' + id,
                type: 'GET',
                success: function(result) {
                    $('#myModal2 .modal-body').html(result);
                },
                error: function() {
                    $('#myModal2 .modal-body').html('Error loading data.');
                },
                complete: function() {
                    $('#loader').hide();
                }
            });
        });


        $(function() {

            var table = $('#ajax_data_table').DataTable({
                dom: 'Bfrtip',
      buttons: [
          {
        extend: 'print',
        text: '<i class="fa fa-print"></i> {{__("fleet.print")}}',

        exportOptions: {
           columns: ([1,2,3,4,5,6,7,8,9]),
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
                columns: [1, 2, 3, 4, 5, 6, 7,8,9]
            }
        }
    ],

                "language": {
                    "url": '{{ asset('assets/datatables/') . '/' . __('fleet.datatable_lang') }}',
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/vehicles-fetch') }}",
                    type: 'POST',
                    data: {}
                },
                columns: [{
                        data: 'check',
                        name: 'check',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'vehicle_image',
                        name: 'vehicle_image',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'make',
                        name: 'vehicles.make_name'
                    },
                    {
                        data: 'model',
                        name: 'vehicles.model_name'
                    },
                    {
                        data: 'displayname',
                        name: 'types.displayname'
                    },
                    {
                        data: 'color',
                        name: 'vehicles.color_name'
                    },
                    {
                        data: 'license_plate',
                        name: 'license_plate'
                    },
                    {
                        data: 'group',
                        name: 'group.name'
                    },
                    {
                        data: 'in_service',
                        name: 'in_service'
                    },
                    {data: 'assigned_driver', name: 'assigned_driver'},
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        orderable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                "initComplete": function() {
                    table.columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change', function() {
                            // console.log($(this).parent().index());
                            that.search(this.value).draw();
                        });
                    });
                }
            });
        });
        $(document).on('click', 'input[type="checkbox"]', function() {
            if (this.checked) {
                $('#bulk_delete').prop('disabled', false);

            } else {
                if ($("input[name='ids[]']:checked").length == 0) {
                    $('#bulk_delete').prop('disabled', true);
                }
            }

        });
        $('#bulk_delete').on('click', function() {
            // console.log($( "input[name='ids[]']:checked" ).length);
            if ($("input[name='ids[]']:checked").length == 0) {
                $('#bulk_delete').prop('type', 'button');
                new PNotify({
                    title: 'Failed!',
                    text: "@lang('fleet.delete_error')",
                    type: 'error'
                });
                $('#bulk_delete').attr('disabled', true);
            }
            if ($("input[name='ids[]']:checked").length > 0) {
                // var favorite = [];
                $.each($("input[name='ids[]']:checked"), function() {
                    // favorite.push($(this).val());
                    $("#bulk_hidden").append('<input type=hidden name=ids[] value=' + $(this).val() + '>');
                });
                // console.log(favorite);
            }
        });

        $('#chk_all').on('click', function() {
            if (this.checked) {
                $('.checkbox').each(function() {
                    $('.checkbox').prop("checked", true);
                });
            } else {
                $('.checkbox').each(function() {
                    $('.checkbox').prop("checked", false);
                });
                $('#bulk_delete').prop('disabled', true);
            }
        });

        // Checkbox checked
        function checkcheckbox() {
            // Total checkboxes
            var length = $('.checkbox').length;
            // Total checked checkboxes
            var totalchecked = 0;
            $('.checkbox').each(function() {
                if ($(this).is(':checked')) {
                    totalchecked += 1;
                }
            });
            // console.log(length+" "+totalchecked);
            // Checked unchecked checkbox
            if (totalchecked == length) {
                $("#chk_all").prop('checked', true);
            } else {
                $('#chk_all').prop('checked', false);
            }
        }

        $(document).ready(function() {
            $('#myTable tfoot th').each(function() {
                if ($(this).index() != 0 && $(this).index() != $('#data_table tfoot th').length - 1) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="' + title + '" />');
                }
            });
            var myTable = $('#myTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'collection',
                    text: 'Export',
                    buttons: [{
                            extend: 'excel',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9]
                            },
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9]
                            },
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [2, 3, 4, 5, 6, 7, 8, 9]
                            },
                        }
                    ]
                }],
                "language": {
                    "url": '{{ asset('assets/datatables/') . '/' . __('fleet.datatable_lang') }}',
                },
                // individual column search
                "initComplete": function() {
                    myTable.columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change', function() {
                            that.search(this.value).draw();
                        });
                    });
                }
            });
        });
    </script>
@endsection
