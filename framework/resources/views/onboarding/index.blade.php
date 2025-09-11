@extends('layouts.app')

@section('extra_css')
<link rel="stylesheet" href="{{ asset('assets/css/plugins-dataTables.bootstrap4.min.css') }}">
<style>
.form-builder-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    border: 2px dashed #dee2e6;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: center;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.field-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 10px;
    position: relative;
}

.field-item .delete-field {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #dc3545;
    cursor: pointer;
}

.onboarding-link {
    background: #e3f2fd;
    border: 1px solid #2196f3;
    border-radius: 5px;
    padding: 15px;
    margin-top: 15px;
}

.copy-button {
    background: #2196f3;
    color: white;
    border: none;
    padding: 5px 15px;
    border-radius: 3px;
    cursor: pointer;
    margin-left: 10px;
}

.dropdown-options-container {
    margin-top: 10px;
}

.dropdown-option {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.dropdown-option input {
    flex: 1;
    margin-right: 10px;
}
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Driver Onboarding</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Driver Onboarding</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $pending_count }}</div>
                    <div class="stats-label">Pending Applications</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $approved_count }}</div>
                    <div class="stats-label">Approved Drivers</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $rejected_count }}</div>
                    <div class="stats-label">Rejected Applications</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $total_count }}</div>
                    <div class="stats-label">Total Applications</div>
                </div>
            </div>
        </div>

        <!-- Form Builder Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Form Builder</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="generateLinkBtn">
                                <i class="fa fa-link"></i> Generate Onboarding Link
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-builder-section">
                            <h5>Add Custom Fields</h5>
                            <form id="customFieldForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Field Name</label>
                                            <input type="text" class="form-control" name="field_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Field Type</label>
                                            <select class="form-control" name="field_type" required>
                                                @foreach($field_types as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="is_required">
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-success btn-block">Add Field</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Dropdown Options (hidden by default) -->
                                <div id="dropdownOptionsSection" style="display: none;">
                                    <label>Dropdown Options</label>
                                    <div class="dropdown-options-container">
                                        <div class="dropdown-option">
                                            <input type="text" class="form-control" name="dropdown_options[]" placeholder="Option 1">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeDropdownOption(this)">×</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="addDropdownOption()">Add Option</button>
                                </div>
                            </form>
                        </div>

                        <!-- Generated Link Display -->
                        <div id="onboardingLinkSection" style="display: none;">
                            <div class="onboarding-link">
                                <h6>Onboarding Link Generated:</h6>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="generatedLink" readonly>
                                    <div class="input-group-append">
                                        <button class="copy-button" onclick="copyLink()">Copy</button>
                                    </div>
                                </div>
                                <small class="text-muted">Share this link with drivers to allow them to submit their onboarding information.</small>
                            </div>
                        </div>

                        <!-- Current Custom Fields -->
                        <div class="mt-4">
                            <h5>Current Custom Fields</h5>
                            <div id="customFieldsList">
                                @forelse($custom_fields as $field)
                                    <div class="field-item" data-field-id="{{ $field->id }}">
                                        <span class="delete-field" onclick="deleteField({{ $field->id }})">
                                            <i class="fa fa-trash"></i>
                                        </span>
                                        <strong>{{ $field->field_name }}</strong>
                                        <span class="badge badge-info">{{ $field_types[$field->field_type] ?? $field->field_type }}</span>
                                        @if($field->is_required)
                                            <span class="badge badge-warning">Required</span>
                                        @endif
                                        @if($field->field_type === 'dropdown' && $field->field_options)
                                            <br><small class="text-muted">Options: {{ implode(', ', $field->field_options['options'] ?? []) }}</small>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted">No custom fields added yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onboarding Drivers Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Onboarding Applications</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <select class="form-control" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="submitted">Submitted</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="onboardingTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>License No</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Applied Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Driver Details Modal -->
<div class="modal fade" id="driverDetailsModal" tabindex="-1" role="dialog" aria-labelledby="driverDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driverDetailsModalLabel">Driver Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="driverDetailsContent">
                <!-- Driver details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('assets/js/plugins-dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#onboardingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ url("admin/onboarding/fetch-data") }}',
            data: function(d) {
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'license_number', name: 'license_number'},
            {data: 'status_badge', name: 'status', orderable: false},
            {data: 'documents', name: 'documents', orderable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']]
    });

    // Status filter change
    $('#statusFilter').change(function() {
        table.ajax.reload();
    });

    // Custom field form submission
    $('#customFieldForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ url("admin/onboarding/store-field") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error adding field');
            }
        });
    });

    // Field type change handler
    $('select[name="field_type"]').change(function() {
        if ($(this).val() === 'dropdown') {
            $('#dropdownOptionsSection').show();
        } else {
            $('#dropdownOptionsSection').hide();
        }
    });
});

// Generate onboarding link
function generateLink() {
    $.ajax({
        url: '{{ url("admin/onboarding/generate-link") }}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#generatedLink').val(response.link);
                $('#onboardingLinkSection').show();
            }
        }
    });
}

// Copy link to clipboard
function copyLink() {
    var linkInput = document.getElementById('generatedLink');
    linkInput.select();
    document.execCommand('copy');
    alert('Link copied to clipboard!');
}

// Delete custom field
function deleteField(fieldId) {
    if (confirm('Are you sure you want to delete this field?')) {
        $.ajax({
            url: '{{ url("admin/onboarding/delete-field") }}/' + fieldId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}

// View driver details
function viewDriver(driverId) {
    $.ajax({
        url: '{{ url("admin/onboarding") }}/' + driverId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var content = '<div class="row">';
                content += '<div class="col-md-6"><strong>Name:</strong> ' + response.driver.name + '</div>';
                content += '<div class="col-md-6"><strong>Email:</strong> ' + response.driver.email + '</div>';
                content += '<div class="col-md-6"><strong>Phone:</strong> ' + response.driver.phone + '</div>';
                content += '<div class="col-md-6"><strong>License Number:</strong> ' + response.driver.license_number + '</div>';
                content += '<div class="col-md-6"><strong>Status:</strong> ' + response.driver.status + '</div>';
                content += '<div class="col-md-6"><strong>Applied:</strong> ' + new Date(response.driver.created_at).toLocaleDateString() + '</div>';
                
                if (response.driver.custom_data) {
                    content += '<div class="col-md-12"><hr><h6>Custom Fields:</h6>';
                    for (var key in response.driver.custom_data) {
                        content += '<div><strong>' + key + ':</strong> ' + response.driver.custom_data[key] + '</div>';
                    }
                    content += '</div>';
                }
                
                content += '</div>';
                
                $('#driverDetailsContent').html(content);
                $('#driverDetailsModal').modal('show');
            }
        }
    });
}

// Approve driver
function approveDriver(driverId) {
    if (confirm('Are you sure you want to approve this driver?')) {
        $.ajax({
            url: '{{ url("admin/onboarding/approve") }}/' + driverId,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            }
        });
    }
}

// Reject driver
function rejectDriver(driverId) {
    if (confirm('Are you sure you want to reject this driver?')) {
        $.ajax({
            url: '{{ url("admin/onboarding/reject") }}/' + driverId,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            }
        });
    }
}

// Delete driver
function deleteDriver(driverId) {
    if (confirm('Are you sure you want to delete this driver record?')) {
        $.ajax({
            url: '{{ url("admin/onboarding") }}/' + driverId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            }
        });
    }
}

// Generate link button click
$('#generateLinkBtn').click(function() {
    generateLink();
});

// Dropdown options management
function addDropdownOption() {
    var container = $('.dropdown-options-container');
    var optionHtml = '<div class="dropdown-option">' +
        '<input type="text" class="form-control" name="dropdown_options[]" placeholder="New Option">' +
        '<button type="button" class="btn btn-sm btn-danger" onclick="removeDropdownOption(this)">×</button>' +
        '</div>';
    container.append(optionHtml);
}

function removeDropdownOption(button) {
    $(button).closest('.dropdown-option').remove();
}
</script>
@endsection