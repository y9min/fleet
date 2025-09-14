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
                            <button type="button" class="btn btn-primary btn-sm" onclick="generateLink()">
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

                        <!-- Saved Links Display -->
                        <div class="mt-4">
                            <h5>Generated Onboarding Links</h5>
                            @if($saved_links->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Link</th>
                                                <th>Created By</th>
                                                <th>Usage Count</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($saved_links as $link)
                                                <tr>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="text" class="form-control form-control-sm" value="{{ $link->link }}" readonly id="savedLink{{ $link->id }}">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary btn-sm" onclick="copySavedLink({{ $link->id }})">
                                                                    <i class="fa fa-copy"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $link->createdBy->name ?? 'Unknown' }}</td>
                                                    <td><span class="badge badge-info">{{ $link->usage_count }}</span></td>
                                                    <td>{{ $link->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-sm" onclick="deactivateLink({{ $link->id }})">
                                                            <i class="fa fa-trash"></i> Deactivate
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No onboarding links generated yet. Click "Generate Link" to create one.</p>
                            @endif
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
<!-- Ensure jQuery is loaded -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{ asset('assets/js/plugins-dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
// Wait for everything to load
$(document).ready(function() {
    console.log('DOM ready, initializing DataTables...');
    initializeOnboardingTable();
});

// Also try with a timeout as fallback
setTimeout(function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        console.log('Fallback initialization...');
        initializeOnboardingTable();
    }
}, 2000);

function initializeOnboardingTable() {
    // Prevent double initialization
    if ($.fn.DataTable.isDataTable('#onboardingTable')) {
        return;
    }
    
    console.log('Initializing DataTables...');
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
        order: [[0, 'desc']],
        language: {
            processing: "Loading driver applications..."
        }
    });

    // Status filter change
    $('#statusFilter').change(function() {
        table.ajax.reload();
    });
}

// Initialize other form elements after DataTables
$(document).ready(function() {
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
    if (typeof $ === 'undefined') {
        alert('System is still loading, please wait...');
        return;
    }
    
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
                
                // Refresh the page to show the new link in the saved links table
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        },
        error: function(xhr) {
            alert('Error generating link: ' + xhr.responseText);
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
                    alert('Driver approved successfully');
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                alert('Error approving driver');
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
                    alert('Driver rejected successfully');
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                alert('Error rejecting driver');
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
                // Populate modal with driver details
                $('#driverDetailsContent').html('<pre>' + JSON.stringify(response.driver, null, 2) + '</pre>');
                $('#driverDetailsModal').modal('show');
            }
        },
        error: function(xhr) {
            alert('Error loading driver details');
        }
    });
}

// Delete driver
function deleteDriver(driverId) {
    if (confirm('Are you sure you want to delete this driver application? This cannot be undone.')) {
        $.ajax({
            url: '{{ url("admin/onboarding") }}/' + driverId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Driver application deleted successfully');
                    $('#onboardingTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                alert('Error deleting driver application');
            }
        });
    }
}

// Copy link to clipboard
function copyLink() {
    var linkInput = document.getElementById('generatedLink');
    linkInput.select();
    document.execCommand('copy');
    alert('Link copied to clipboard!');
}

// Copy saved link to clipboard
function copySavedLink(linkId) {
    var linkInput = document.getElementById('savedLink' + linkId);
    linkInput.select();
    document.execCommand('copy');
    alert('Link copied to clipboard!');
}

// Deactivate saved link
function deactivateLink(linkId) {
    if (confirm('Are you sure you want to deactivate this link? This will prevent it from being used for new applications.')) {
        $.ajax({
            url: '{{ url("admin/onboarding/deactivate-link") }}/' + linkId,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Link deactivated successfully');
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error deactivating link');
            }
        });
    }
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

// Add missing functions
function approveDriver(driverId) {
    if (confirm('Are you sure you want to approve this driver?')) {
        $.ajax({
            url: '{{ url("admin/onboarding") }}/' + driverId + '/approve',
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

function rejectDriver(driverId) {
    if (confirm('Are you sure you want to reject this driver application?')) {
        $.ajax({
            url: '{{ url("admin/onboarding") }}/' + driverId + '/reject',
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

function viewDriver(driverId) {
    $.ajax({
        url: '{{ url("admin/onboarding") }}/' + driverId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Populate modal with driver details
                $('#driverDetailsContent').html(JSON.stringify(response.driver, null, 2));
                $('#driverDetailsModal').modal('show');
            }
        }
    });
}


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

// Generate onboarding link
function generateLink() {
    $.ajax({
        url: '{{ route("onboarding.generate_link") }}',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#generatedLink').val(response.link);
                $('#onboardingLinkSection').show();
                
                // Refresh the page to show the new link in the saved links table
                setTimeout(function() {
                    location.reload();
                }, 1000);
            }
        },
        error: function(xhr) {
            alert('Error generating link: ' + xhr.responseText);
        }
    });
}
</script>
@endsection