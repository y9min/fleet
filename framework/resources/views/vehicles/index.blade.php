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
                            </tfoot>
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
        $(document).ready(function() {
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
        // View Modal
        $(document).on('click', '.openBtn', function() {
            var id = $(this).attr("data-id");
            $('#viewModal .modal-body').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading vehicle details...</div>');
            $('#viewModal').modal({
                show: true
            });
            $.ajax({
                url: '{{ url('admin/vehicle/event') }}/' + id,
                type: 'GET',
                success: function(result) {
                    $('#viewModal .modal-body').html(result);
                },
                error: function() {
                    $('#viewModal .modal-body').html('<div class="alert alert-danger">Error loading vehicle details.</div>');
                }
            });
        });

        // Delete functionality
        var deleteId = null;
        function setDeleteId(id) {
            deleteId = id;
        }
        
        $(document).on('click', '#confirmDelete', function() {
            if (deleteId) {
                $('#form_' + deleteId).submit();
                $('#deleteModal').modal('hide');
            }
        });


            var table = $('#ajax_data_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ url('admin/vehicles-fetch') }}',
                    type: 'POST',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [
                    { data: 'check', name: 'check', orderable: false, searchable: false },
                    { data: 'vehicle_id', name: 'vehicle_id', orderable: true },
                    { data: 'license_plate', name: 'license_plate', orderable: true },
                    { data: 'make', name: 'make', orderable: true },
                    { data: 'model', name: 'model', orderable: true },
                    { data: 'fuel_type', name: 'fuel_type', orderable: true },
                    { data: 'status', name: 'status', orderable: true },
                    { data: 'assigned_driver', name: 'assigned_driver', orderable: false },
                    { data: 'telematics', name: 'telematics', orderable: false, searchable: false },
                    { data: 'view', name: 'view', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
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
        }); // End of document.ready
    </script>
@endsection
