@extends('layouts.app')

@section('extra_css')
<link rel="stylesheet" href="{{ asset('assets/css/plugins-dataTables.bootstrap4.min.css') }}">
<style>
.tw-breadcrumb { display:flex; align-items:center; gap:8px; color:#6b7280; }
.tw-breadcrumb a { color:#2563eb; text-decoration:none; }
.tw-breadcrumb-sep { color:#9ca3af; }
.tw-text-2xl { font-size:1.5rem; line-height:2rem; }
.tw-font-semibold { font-weight:600; }
.tw-grid { display:grid; }
.tw-grid-cols-1 { grid-template-columns:repeat(1,minmax(0,1fr)); }
.tw-gap-6 { gap:1.5rem; }
@media (min-width: 640px) { /* sm */
    .sm\:tw-grid-cols-2 { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (min-width: 992px) { /* lg ~ bootstrap md/lg breakpoint */
    .lg\:tw-grid-cols-4 { grid-template-columns:repeat(4,minmax(0,1fr)); }
}
.tw-rounded-xl { border-radius:0.75rem; }
.tw-shadow-md { box-shadow:0 4px 6px rgba(0,0,0,0.1); }
.tw-p-6 { padding:1.5rem; }
.tw-center { text-align:center; }
.tw-circle { width:3rem; height:3rem; display:flex; align-items:center; justify-content:center; border-radius:9999px; color:#fff; font-size:1.25rem; margin:0 auto 0.75rem auto; }
.tw-text-xl { font-size:1.25rem; line-height:1.75rem; }
.tw-font-bold { font-weight:700; }
.tw-text-gray-600 { color:#4b5563; }
.tw-text-sm { font-size:0.875rem; }
.tw-dash-font { font-family:'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', sans-serif; }
.tw-stat-number { font-size:2rem; line-height:2.25rem; font-weight:700; margin-bottom:0.25rem; }
.tw-stat-label { text-transform:uppercase; letter-spacing:0.02em; font-size:0.875rem; color:#6b7280; margin:0; }
.form-builder-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    border: 2px dashed #dee2e6;
}

.stats-card-square {
    height: 180px;
    border: none;
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card-square:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.stats-card-square .card-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}

.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-card-square {
        height: 160px;
        margin-bottom: 1rem;
    }
    
    .icon-circle {
        width: 50px;
        height: 50px;
    }
    
    .icon-circle i {
        font-size: 20px !important;
    }
}

@media (min-width: 769px) {
    .stats-cards-row .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

.stats-cards-row { display: flex; flex-wrap: wrap; }
.stats-cards-row > [class*='col-'] { display: flex; }

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
.form-builder-box { border: 1px dashed #ccc; padding: 20px; border-radius: 6px; background: #f8f9fa; }
.gap-3 { gap: 1rem; }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="tw-breadcrumb">
                    <a href="{{ url('admin') }}">Dashboard</a>
                    <span class="tw-breadcrumb-sep">/</span>
                    <span>Driver Onboarding</span>
                </div>
                <h1 class="mt-2 mb-0 tw-text-2xl tw-font-semibold">Driver Onboarding</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card p-3">

            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" onclick="generateLink()">
                    <i class="fa fa-link"></i> Generate Onboarding Link
                </button>
            </div>

        <!-- Statistics Cards (Tailwind-like utilities scoped to this view) -->
        @php
            $pendingCount = $pending_count ?? 0;
            $approvedCount = $approved_count ?? 0;
            $rejectedCount = $rejected_count ?? 0;
            $totalCount = isset($total_count) ? $total_count : ($pendingCount + $approvedCount + $rejectedCount);
        @endphp
        <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 mb-4">
            <div class="tw-rounded-xl tw-shadow-md tw-p-6 tw-center" style="background:#ffffff;">
                <div class="tw-circle" style="background:#ffc107;">
                    <i class="fas fa-clock"></i>
                </div>
                <p class="tw-dash-font tw-stat-number">{{ $pendingCount }}</p>
                <p class="tw-dash-font tw-stat-label">Pending Applications</p>
            </div>
            <div class="tw-rounded-xl tw-shadow-md tw-p-6 tw-center" style="background:#ffffff;">
                <div class="tw-circle" style="background:#28a745;">
                    <i class="fas fa-check"></i>
                </div>
                <p class="tw-dash-font tw-stat-number">{{ $approvedCount }}</p>
                <p class="tw-dash-font tw-stat-label">Approved Drivers</p>
            </div>
            <div class="tw-rounded-xl tw-shadow-md tw-p-6 tw-center" style="background:#ffffff;">
                <div class="tw-circle" style="background:#dc3545;">
                    <i class="fas fa-times"></i>
                </div>
                <p class="tw-dash-font tw-stat-number">{{ $rejectedCount }}</p>
                <p class="tw-dash-font tw-stat-label">Rejected Applications</p>
            </div>
            <div class="tw-rounded-xl tw-shadow-md tw-p-6 tw-center" style="background:#ffffff;">
                <div class="tw-circle" style="background:#17a2b8;">
                    <i class="fas fa-users"></i>
                </div>
                <p class="tw-dash-font tw-stat-number">{{ $totalCount }}</p>
                <p class="tw-dash-font tw-stat-label">Total Applications</p>
            </div>
        </div>

        <div class="mb-4">
            <!-- Form Builder Section -->
            <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Form Builder</h3></div>
                    <div class="card-body">
                        <div class="form-builder-box">
                            <h5 class="mb-3">Add Custom Fields</h5>
                            <form id="customFieldForm">
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <div style="min-width:240px; flex:1 1 240px;">
                                        <input type="text" class="form-control" name="field_name" placeholder="Field Name" required>
                                    </div>
                                    <div style="min-width:200px;">
                                        <select class="form-control" name="field_type" required>
                                            @foreach($field_types as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-check ml-2">
                                        <input type="checkbox" class="form-check-input" id="is_required" name="is_required">
                                        <label class="form-check-label" for="is_required">Required</label>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-success">Add Field</button>
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
                                    <table class="table table-striped table-bordered">
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