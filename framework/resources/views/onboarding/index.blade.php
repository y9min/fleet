@extends('layouts.app')

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

/* Header styles - EXACT match to screenshot */
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

/* Breadcrumb styles - EXACT match to screenshot */
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

/* Form builder box - simplified and themed */
.form-builder-box {
    background: #ffffff;
    border: 1px solid #7FD7E1;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(127, 215, 225, 0.1);
}

.form-builder-box h5 {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    border-bottom: 2px solid #7FD7E1;
    padding-bottom: 8px;
}

.form-row {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-control:focus {
    border-color: #7FD7E1;
    box-shadow: 0 0 0 0.2rem rgba(127, 215, 225, 0.25);
}

.btn-success {
    background-color: #7FD7E1;
    border-color: #7FD7E1;
    color: #333;
    font-weight: 500;
}

.btn-success:hover {
    background-color: #6bc5d1;
    border-color: #6bc5d1;
    color: #333;
}

/* User instructions */
.user-instructions {
    background: #e8f4f8;
    border: 1px solid #7FD7E1;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #2c5aa0;
}

.user-instructions i {
    color: #7FD7E1;
    margin-right: 8px;
}

/* Field configuration styles */
.field-configs-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.field-config-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.field-config-item:hover {
    background: #e9ecef;
    border-color: #7FD7E1;
}

.field-info {
    flex: 1;
}

.field-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.field-label strong {
    font-size: 16px;
    color: #333;
}

.field-type-badge {
    background: #7FD7E1;
    color: #333;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.field-controls {
    display: flex;
    gap: 20px;
    align-items: center;
}

.field-controls .form-check {
    margin: 0;
}

.field-controls .form-check-input {
    margin-right: 5px;
}

.field-controls .form-check-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.field-controls .form-check-label {
    font-size: 13px;
    color: #666;
    cursor: pointer;
}

.form-group {
    flex: 1;
    min-width: 200px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
}

.form-control:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.form-check {
    display: flex;
    align-items: center;
    margin-left: 15px;
}

.form-check-input {
    margin-right: 8px;
}

.form-check-label {
    font-size: 14px;
    color: #333;
    margin: 0;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.btn-success {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #1e7e34;
    border-color: #1c7430;
}

.btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-info {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

/* Document buttons - EXACT styling from screenshot */
.btn-outline-primary {
    color: #007bff;
    background-color: transparent;
    border: 1px solid #007bff;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 12px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    text-decoration: none;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.btn-outline-info {
    color: #17a2b8;
    background-color: transparent;
    border: 1px solid #17a2b8;
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 12px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    text-decoration: none;
}

.btn-outline-info:hover {
    color: #fff;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

/* Action buttons - Enhanced styling with solid colors */
.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 1px;
    text-decoration: none;
    transition: all 0.15s ease-in-out;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.action-btn.approve {
    background-color: #28a745;
    color: white;
}

.action-btn.approve:hover {
    background-color: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
}

.action-btn.reject {
    background-color: #ffc107;
    color: #212529;
}

.action-btn.reject:hover {
    background-color: #e0a800;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
}

.action-btn.view {
    background-color: #17a2b8;
    color: white;
}

.action-btn.view:hover {
    background-color: #138496;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(23, 162, 184, 0.3);
}

.action-btn.delete {
    background-color: #dc3545;
    color: white;
}

.action-btn.delete:hover {
    background-color: #c82333;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

/* Enhanced button styling for table actions */
.table .btn {
    border: none;
    font-weight: 500;
    text-shadow: none;
}

.table .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.table .btn-success:hover {
    background-color: #1e7e34;
    border-color: #1c7430;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
}

.table .btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.table .btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #212529;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
}

.table .btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.table .btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(23, 162, 184, 0.3);
}

.table .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.table .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

/* Document buttons styling - Enhanced with solid colors */
.table .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.table .btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 123, 255, 0.3);
}

/* Ensure document buttons have consistent styling */
.table td[data-column="documents"] {
    white-space: nowrap;
}

.table td[data-column="documents"] .btn {
    display: inline-flex;
    vertical-align: middle;
    margin: 0 1px;
}

/* Tables */
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #333;
    border-collapse: collapse;
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

/* Center buttons in table cells */
.table td {
    text-align: center;
    vertical-align: middle;
}

.table th {
    text-align: center;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 4px;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}

.badge-warning {
    color: #212529;
    background-color: #ffc107;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

/* Input groups */
.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    margin-bottom: 0;
}

.input-group-append {
    margin-left: -1px;
    display: flex;
}

.input-group-append .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Section headings */
.section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

/* Field items */
.field-item {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
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
    font-size: 14px;
}

/* DataTables styling - EXACT match to first screenshot */
.dataTables_wrapper {
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #333;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 4px 8px;
    margin-left: 8px;
    font-size: 14px;
}

/* DataTables pagination - EXACT match to first screenshot with inline layout */
.dataTables_wrapper .dataTables_paginate {
    display: inline-block;
    margin: 0;
    padding: 0;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    display: inline-block;
    padding: 6px 12px;
    margin: 0 2px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 14px;
    color: #007bff;
    text-decoration: none;
    background: #fff;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #0056b3;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #0056b3;
    border-color: #004085;
    color: #fff;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #6c757d;
    background: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    background: #fff;
    border-color: #dee2e6;
    color: #6c757d;
}

/* DataTables info - EXACT match to first screenshot */
.dataTables_wrapper .dataTables_info {
    color: #6c757d;
    font-size: 14px;
    display: inline-block;
    margin: 0;
    padding: 0;
}

/* DataTables bottom row - inline layout */
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    display: inline-block;
    vertical-align: middle;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-group {
        min-width: auto;
    }
    
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}

/* Text colors */
.text-muted {
    color: #6c757d !important;
}

/* Spacing utilities */
.mb-3 { margin-bottom: 1rem !important; }
.mb-4 { margin-bottom: 1.5rem !important; }
.mt-4 { margin-top: 1.5rem !important; }

/* Row expansion styles */
.details-row {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.details-content {
    padding: 20px;
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.details-content .row {
    margin-bottom: 15px;
}

.details-content .col-md-2,
.details-content .col-md-4 {
    margin-bottom: 10px;
}

.details-content strong {
    color: #333;
    font-weight: 600;
}

.details-content .text-muted {
    color: #6c757d;
}

.details-content .btn {
    margin: 2px;
}

/* Inline field layout */
.inline-field {
    display: inline-block;
    margin-right: 20px;
    margin-bottom: 8px;
    font-size: 14px;
}

.inline-field strong {
    color: #333;
    font-weight: 600;
    margin-right: 5px;
}

.inline-field .text-muted {
    color: #6c757d;
}

.inline-field .badge {
    margin-left: 5px;
}

.inline-field .btn {
    margin-left: 5px;
    margin-right: 0;
}

/* Toggle button styles - 28x28px like other action buttons */
.toggle-btn {
    width: 28px;
    height: 28px;
    background-color: #17a2b8;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 1px;
    text-decoration: none;
    transition: background-color 0.15s ease-in-out;
}

/* Ensure action buttons stay in one row */
.table td[data-column="actions"] {
    white-space: nowrap;
}

.table td[data-column="actions"] .action-btn,
.table td[data-column="actions"] .toggle-btn {
    display: inline-flex;
    vertical-align: middle;
}

.toggle-btn:hover {
    background-color: #138496;
}

.toggle-btn.expanded {
    background-color: #dc3545;
}

.toggle-btn.expanded:hover {
    background-color: #c82333;
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="header-card">
                <div class="header-content">
                    <h1>Driver Onboarding</h1>
                    <div class="breadcrumb-text">
                        <a href="{{ url('admin') }}">Dashboard</a> / Driver Onboarding
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <!-- Onboarding Applications -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Onboarding Applications</h3>
                </div>
                <div class="card-body">
                    <table id="onboardTable" class="table table-striped table-bordered">
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
                        <tbody>
                            <!-- Data will be loaded via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Builder Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Builder</h3>
                    <button type="button" class="btn btn-primary" onclick="generateLink()">
                        <i class="fa fa-link"></i> Generate Onboarding Link
                    </button>
                </div>
                <div class="card-body">
                    <!-- Preexisting Fields Configuration -->
                    <div class="form-builder-box">
                        <h5>Configure Preexisting Fields</h5>
                        
                        <!-- User Instructions -->
                        <div class="user-instructions">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Toggle fields on/off and set whether they are mandatory for the onboarding form.
                        </div>
                        
                        <div class="field-configs-container">
                            @foreach($field_configs as $config)
                                <div class="field-config-item" data-field-id="{{ $config->id }}">
                                    <div class="field-info">
                                        <div class="field-label">
                                            <strong>{{ $config->field_label }}</strong>
                                            <span class="field-type-badge">{{ ucfirst($config->field_type) }}</span>
                                        </div>
                                    </div>
                                    <div class="field-controls">
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input field-visibility-toggle" 
                                                   id="visible_{{ $config->id }}" 
                                                   {{ $config->is_visible ? 'checked' : '' }}
                                                   data-field-id="{{ $config->id }}"
                                                   data-field-type="visibility">
                                            <label class="form-check-label" for="visible_{{ $config->id }}">Visible</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" class="form-check-input field-required-toggle" 
                                                   id="required_{{ $config->id }}" 
                                                   {{ $config->is_required ? 'checked' : '' }}
                                                   data-field-id="{{ $config->id }}"
                                                   data-field-type="required"
                                                   {{ !$config->is_visible ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="required_{{ $config->id }}">Required</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add Custom Fields -->
                    <div class="form-builder-box">
                        <h5>Add Custom Fields</h5>
                        
                        <!-- User Instructions -->
                        <div class="user-instructions">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> After adding new custom fields, please refresh the page or generate a new onboarding link to see the changes in the driver form.
                        </div>
                        
                        <form id="customFieldForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="field_name" placeholder="Field Name" required>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" name="field_type" required>
                                        @foreach($field_types as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_required" name="is_required">
                                    <label class="form-check-label" for="is_required">Required</label>
                                </div>
                                <button type="submit" class="btn btn-success">Add Field</button>
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
                                    <button class="btn btn-primary" onclick="copyLink()">Copy</button>
                                </div>
                            </div>
                            <small class="text-muted">Share this link with drivers to allow them to submit their onboarding information.</small>
                        </div>
                    </div>

                    <!-- Generated Onboarding Links -->
                    <div class="mt-4">
                        <h5 class="section-title">Generated Onboarding Links</h5>
                        @if($saved_links->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
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
                        <h5 class="section-title">Current Custom Fields</h5>
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
    </section>
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
    if ($.fn.DataTable.isDataTable('#onboardTable')) {
        return;
    }
    
    console.log('Initializing DataTables...');
    // Initialize DataTable with simple_numbers pagination and inline layout
    var table = $('#onboardTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pagingType: 'simple_numbers',
        ajax: {
            url: '{{ url("admin/onboarding/fetch-data") }}'
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
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12"<"d-flex justify-content-between align-items-center"<"dataTables_info"i><"dataTables_paginate"p>>>>',
        drawCallback: function(settings) {
            // Ensure pagination buttons are inline
            $('.dataTables_paginate').css('display', 'inline-block');
            $('.dataTables_paginate .paginate_button').css('display', 'inline-block');
            
            // Ensure info text is inline
            $('.dataTables_info').css('display', 'inline-block');
            
            // Remove any existing detail rows
            $('.details-row').remove();
        }
    });
}

// Initialize other form elements after DataTables
$(document).ready(function() {
    // Field configuration toggles
    $(document).on('change', '.field-visibility-toggle', function() {
        console.log('Visibility toggle changed');
        var fieldId = $(this).data('field-id');
        var isVisible = $(this).is(':checked');
        var requiredToggle = $('#required_' + fieldId);
        
        console.log('Field ID:', fieldId, 'Is Visible:', isVisible);
        
        // Enable/disable required toggle based on visibility
        requiredToggle.prop('disabled', !isVisible);
        
        // If field becomes invisible, uncheck required
        if (!isVisible) {
            requiredToggle.prop('checked', false);
        }
        
        // Update field configuration
        updateFieldConfig(fieldId, 'visibility', isVisible);
    });
    
    $(document).on('change', '.field-required-toggle', function() {
        console.log('Required toggle changed');
        var fieldId = $(this).data('field-id');
        var isRequired = $(this).is(':checked');
        
        console.log('Field ID:', fieldId, 'Is Required:', isRequired);
        
        // Update field configuration
        updateFieldConfig(fieldId, 'required', isRequired);
    });
    
    // Custom field form submission
    $('#customFieldForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

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
                    // Show success message
                    alert('Custom field added successfully! The page will refresh to show the new field.');
                    location.reload();
                } else {
                    // Show validation errors
                    var errors = response.errors || {};
                    var errorMsg = 'Please fix the following errors:\n';
                    for (var field in errors) {
                        errorMsg += '- ' + errors[field][0] + '\n';
                    }
                    alert(errorMsg);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Error adding field. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += xhr.responseJSON.message;
                } else {
                    errorMsg += 'Please try again.';
                }
                alert(errorMsg);
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false).html(originalText);
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

// Refresh CSRF token
function refreshCSRFToken() {
    return $.get('{{ url("admin/onboarding/refresh-token") }}').then(function(response) {
        // Update the meta tag with the new token
        $('meta[name="csrf-token"]').attr('content', response.csrf_token);
        return response.csrf_token;
    });
}

// Approve driver
function approveDriver(driverId) {
    if (confirm('Are you sure you want to approve this driver?')) {
        // Get fresh CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: '{{ url("admin/onboarding/approve") }}/' + driverId,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message || 'Driver approved successfully and added to drivers list');
                    $('#onboardTable').DataTable().ajax.reload();
                } else {
                    alert('Error: ' + (response.message || 'Failed to approve driver'));
                }
            },
            error: function(xhr, status, error) {
                console.log('XHR Response:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Error approving driver: ' + error;
                if (xhr.status === 419) {
                    // Try to refresh CSRF token and retry once
                    refreshCSRFToken().then(function(newToken) {
                        $.ajax({
                            url: '{{ url("admin/onboarding/approve") }}/' + driverId,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': newToken
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message || 'Driver approved successfully and added to drivers list');
                                    $('#onboardTable').DataTable().ajax.reload();
                                } else {
                                    alert('Error: ' + (response.message || 'Failed to approve driver'));
                                }
                            },
                            error: function(xhr2, status2, error2) {
                                alert('CSRF token mismatch. Please refresh the page and try again.');
                            }
                        });
                    }).catch(function() {
                        alert('CSRF token mismatch. Please refresh the page and try again.');
                    });
                    return;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = 'Error: ' + xhr.responseJSON.message;
                }
                alert(errorMessage);
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
                    $('#onboardTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                alert('Error rejecting driver');
            }
        });
    }
}

// Toggle driver details dropdown
function toggleDriverDetails(driverId) {
    var $button = $('button[data-driver-id="' + driverId + '"]');
    var $row = $button.closest('tr');
    var $detailsRow = $row.next('.details-row');
    
    // If details row exists, remove it
    if ($detailsRow.length > 0) {
        $detailsRow.remove();
        $button.removeClass('expanded').html('<i class="fa fa-eye"></i>');
        return;
    }
    
    // Otherwise, fetch and show details
    $.ajax({
        url: '{{ url("admin/onboarding") }}/' + driverId,
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
                html += '<div class="inline-field"><strong>License:</strong><span class="text-muted">' + (driver.license_number || 'N/A') + '</span></div>';
                var statusClass = driver.status === 'approved' ? 'success' : (driver.status === 'rejected' ? 'danger' : 'warning');
                html += '<div class="inline-field"><strong>Status:</strong><span class="badge badge-' + statusClass + '">' + (driver.status || 'N/A') + '</span></div>';
                html += '<div class="inline-field"><strong>Submitted:</strong><span class="text-muted">' + (driver.created_at || 'N/A') + '</span></div>';
                html += '</div>';
                
                // Documents Section - Inline layout with proper spacing
                html += '<div class="mb-3">';
                html += '<div class="inline-field"><strong>Documents:</strong>';
                if (driver.license_upload_path) {
                    html += '<a href="' + driver.license_url + '" class="btn btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
                    html += '<i class="fas fa-eye"></i> License';
                    html += '</a>';
                }
                if (driver.insurance_upload_path) {
                    html += '<a href="' + driver.insurance_url + '" class="btn btn-outline-info" target="_blank" style="border: 1px solid #17a2b8; color: #17a2b8; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
                    html += '<i class="fas fa-eye"></i> Insurance';
                    html += '</a>';
                }
                html += '</div>';
                html += '</div>';
                
                // Custom Fields Data - Inline layout
                console.log('Driver custom_data:', driver.custom_data);
                if (driver.custom_data && Object.keys(driver.custom_data).length > 0) {
                    html += '<div class="mb-3">';
                    html += '<div class="inline-field"><strong>Additional Information:</strong>';
                    
                    // Create field name mapping for better display
                    var fieldNameMap = {
                        'scheme_selection': 'Scheme Selection',
                        'vehicle_selection': 'Vehicle Selection',
                        'insurance_selection': 'Insurance Selection',
                        'ni_number': 'NI Number',
                        'address': 'Address',
                        'city': 'City',
                        'state': 'State',
                        'country': 'Country',
                        'postal_code': 'Postal Code',
                        'date_of_birth': 'Date of Birth',
                        'gender': 'Gender',
                        'emergency_contact_name': 'Emergency Contact Name',
                        'emergency_contact_phone': 'Emergency Contact Phone',
                        'driver_license_expiry': 'Driver License Expiry',
                        'insurance_expiry': 'Insurance Expiry'
                    };
                    
                    for (var key in driver.custom_data) {
                        if (driver.custom_data.hasOwnProperty(key) && key !== 'token' && key !== 'terms' && !key.endsWith('_url')) {
                            var value = driver.custom_data[key];
                            var displayValue = '';
                            
                            // Find the actual field name from customFields by matching field ID
                            var fieldName = key;
                            var isFileField = false;
                            
                            // Check if key starts with 'custom_' and extract the field ID
                            if (key.startsWith('custom_')) {
                                var fieldId = key.replace('custom_', '');
                                customFields.forEach(function(field) {
                                    if (field.id == fieldId) {
                                        fieldName = field.field_name;
                                        isFileField = (field.field_type === 'file');
                                    }
                                });
                            } else {
                                // Use mapped field name if available
                                fieldName = fieldNameMap[key] || fieldName;
                            }
                            
                            // Special handling for specific fields
                            if (key === 'vehicle_selection' && value) {
                                // Try to get vehicle details from the driver object or make an AJAX call
                                if (driver.vehicle_details) {
                                    displayValue = driver.vehicle_details.make_name + ' ' + driver.vehicle_details.model_name + ' (' + driver.vehicle_details.license_plate + ')';
                                } else {
                                    displayValue = 'Vehicle ID: ' + value;
                                }
                            } else if (key === 'scheme_selection' && value) {
                                displayValue = value;
                            } else if (key === 'insurance_selection' && value) {
                                displayValue = value === 'with_insurance' ? 'With Insurance' : 'Without Insurance';
                            } else {
                                // Debug: Log the value to see what we're getting
                                console.log('Field:', fieldName, 'Key:', key, 'Value:', value, 'Type:', typeof value);
                                
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
                            }
                            
                            html += '<div class="inline-field"><strong>' + fieldName + ':</strong>';
                            
                            if (isFileField && value && value.toString().trim() !== '') {
                                // For file fields, show a view link using the generated URL
                                var fileUrl = driver.custom_data[key + '_url'] || value;
                                html += '<a href="' + fileUrl + '" class="btn btn-outline-primary" target="_blank" style="border: 1px solid #007bff; color: #007bff; padding: 8px 16px; font-size: 14px; margin-left: 8px; margin-right: 8px; min-width: 100px; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center;">';
                                html += '<i class="fas fa-eye"></i> View';
                                html += '</a>';
                            } else {
                                html += '<span class="text-muted">' + displayValue + '</span>';
                            }
                            
                            html += '</div>';
                        }
                    }
                    
                    html += '</div>';
                    html += '</div>';
                } else {
                    html += '<div class="mb-3">';
                    html += '<div class="inline-field"><strong>Additional Information:</strong><span class="text-muted">No additional information provided.</span></div>';
                    html += '</div>';
                }
                
                html += '</div>';
                
                // Create and insert the details row
                var $detailsRow = $('<tr class="details-row"><td colspan="9">' + html + '</td></tr>');
                $row.after($detailsRow);
                
                // Update button state
                $button.addClass('expanded').html('<i class="fa fa-eye-slash"></i>');
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
                    $('#onboardTable').DataTable().ajax.reload();
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

// Update field configuration
function updateFieldConfig(fieldId, fieldType, value) {
    console.log('Updating field config:', {fieldId, fieldType, value});
    
    var data = {};
    if (fieldType === 'visibility') {
        data.is_visible = value;
    } else if (fieldType === 'required') {
        data.is_required = value;
    }
    
    console.log('Sending data:', data);
    console.log('URL:', '{{ url("admin/onboarding/update-field-config") }}/' + fieldId);
    
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log('CSRF Token:', csrfToken);
    
    // Get field label for user feedback
    var fieldLabel = $('#visible_' + fieldId).closest('.field-config-item').find('.field-label strong').text();
    
    $.ajax({
        url: '{{ url("admin/onboarding/update-field-config") }}/' + fieldId,
        type: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            console.log('Response:', response);
            if (response.success) {
                console.log('Field configuration updated successfully');
                // Show success toast
                showToast('success', fieldLabel + ' configuration updated successfully');
            } else {
                console.error('Error updating field configuration:', response);
                // Revert the toggle state
                if (fieldType === 'visibility') {
                    $('#visible_' + fieldId).prop('checked', !value);
                } else if (fieldType === 'required') {
                    $('#required_' + fieldId).prop('checked', !value);
                }
                showToast('error', 'Failed to update ' + fieldLabel + ' configuration');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error:', xhr);
            console.error('Response text:', xhr.responseText);
            console.error('Status:', xhr.status);
            // Revert the toggle state
            if (fieldType === 'visibility') {
                $('#visible_' + fieldId).prop('checked', !value);
            } else if (fieldType === 'required') {
                $('#required_' + fieldId).prop('checked', !value);
            }
            var errorMsg = 'Failed to update ' + fieldLabel + ' configuration';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            showToast('error', errorMsg);
        }
    });
}

// Simple toast notification function
function showToast(type, message) {
    // Remove existing toasts
    $('.toast-notification').remove();
    
    var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    var bgColor = type === 'success' ? '#28a745' : '#dc3545';
    
    var toast = $('<div class="toast-notification" style="position: fixed; top: 20px; right: 20px; background: ' + bgColor + '; color: white; padding: 15px 20px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 9999; min-width: 300px;">' +
        '<i class="fas fa-' + icon + '"></i> ' + message +
        '</div>');
    
    $('body').append(toast);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}
</script>
@endsection
