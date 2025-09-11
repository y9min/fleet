
@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Driver Onboarding</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Generate Onboarding Link</h3>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" id="generateLink">Generate New Onboarding Link</button>
                        <div id="linkResult" class="mt-3" style="display: none;">
                            <div class="alert alert-success">
                                <strong>Onboarding Link Generated:</strong><br>
                                <input type="text" id="onboardingLink" class="form-control mt-2" readonly>
                                <button class="btn btn-sm btn-info mt-2" onclick="copyLink()">Copy Link</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Onboarding Applications</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="onboardingTable">
                                <thead>
                                    <tr>
                                        <th>Driver ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>License No</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Driver Documents</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="documentsContent">
                <!-- Documents will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    var table = $('#onboardingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.onboarding.data') }}",
            type: 'GET'
        },
        columns: [
            {data: 'onboarding_id', name: 'onboarding_id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'license_number', name: 'license_number'},
            {data: 'status_badge', name: 'status', searchable: false},
            {data: 'documents', name: 'documents', orderable: false, searchable: false},
            {data: 'submitted_at', name: 'submitted_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[7, 'desc']]
    });

    // Generate link
    $('#generateLink').click(function() {
        $.post('{{ route("admin.onboarding.generate-link") }}', {
            _token: '{{ csrf_token() }}'
        }).done(function(response) {
            $('#onboardingLink').val(response.link);
            $('#linkResult').show();
        });
    });

    // Approve driver
    $(document).on('click', '.approve-driver', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to approve this driver?')) {
            $.post('{{ url("admin/onboarding") }}/' + id + '/approve', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                if (response.success) {
                    alert(response.message);
                    table.ajax.reload();
                }
            });
        }
    });

    // Reject driver
    $(document).on('click', '.reject-driver', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to reject this driver?')) {
            $.post('{{ url("admin/onboarding") }}/' + id + '/reject', {
                _token: '{{ csrf_token() }}'
            }).done(function(response) {
                if (response.success) {
                    alert(response.message);
                    table.ajax.reload();
                }
            });
        }
    });

    // Delete driver
    $(document).on('click', '.delete-driver', function() {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this application?')) {
            $.ajax({
                url: '{{ url("admin/onboarding") }}/' + id,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'}
            }).done(function(response) {
                if (response.success) {
                    alert(response.message);
                    table.ajax.reload();
                }
            });
        }
    });

    // View documents
    $(document).on('click', '.view-docs', function() {
        var id = $(this).data('id');
        $.get('{{ url("admin/onboarding") }}/' + id + '/documents').done(function(data) {
            var html = '<div class="row">';
            if (data.drivers_license_file) {
                html += '<div class="col-md-4"><h6>Drivers License</h6><a href="/storage/' + data.drivers_license_file + '" target="_blank" class="btn btn-info btn-sm">View/Download</a></div>';
            }
            if (data.pco_license_file) {
                html += '<div class="col-md-4"><h6>PCO License</h6><a href="/storage/' + data.pco_license_file + '" target="_blank" class="btn btn-info btn-sm">View/Download</a></div>';
            }
            if (data.insurance_file) {
                html += '<div class="col-md-4"><h6>Insurance</h6><a href="/storage/' + data.insurance_file + '" target="_blank" class="btn btn-info btn-sm">View/Download</a></div>';
            }
            html += '</div>';
            $('#documentsContent').html(html);
            $('#documentsModal').modal('show');
        });
    });
});

function copyLink() {
    var copyText = document.getElementById("onboardingLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Link copied to clipboard!");
}
</script>
@endsection
