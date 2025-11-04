@extends('layouts.app')

@section('page_title')
PCOFlow | Vehicles
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

/* Header styles - EXACT match to onboarding page */
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

/* Breadcrumb styles - EXACT match to onboarding page */
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
        
/* Enhanced button styling to match onboarding page */
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

/* Table styling to match onboarding page exactly */
.vehicles-table {
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
        
        .vehicle-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        /* Fix modal z-index and pointer-events to prevent backdrop blocking interactions */
        .modal {
            overflow-y: auto !important;
            z-index: 1050 !important;
        }
        
        .modal.show {
            display: block !important;
            overflow-y: auto !important;
        }
        
        .modal-dialog {
            position: relative;
            z-index: 1060 !important;
            margin: 1.75rem auto;
        }
        
        .modal-content {
            pointer-events: auto !important;
            z-index: 1070 !important;
            position: relative;
        }
        
        .modal-backdrop {
            z-index: 1040 !important;
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none !important;
        }
        
        .modal-backdrop.show {
            pointer-events: auto !important;
        }
        
        /* Ensure all interactive elements in modal are clickable */
        .modal-content * {
            pointer-events: auto;
        }
        
        /* Allow scrolling in modal body */
        .modal-body {
            overflow-y: auto;
            max-height: calc(100vh - 200px);
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
        
/* Enhanced Modal Styling to match onboarding page */
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
        
/* Import Modal Specific - Enhanced to match onboarding page */
.file-upload-section {
    background: #f8f9fa;
    border: 2px dashed #7FD7E1;
    border-radius: 12px;
    padding: 40px 30px;
    text-align: center;
    margin-bottom: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
}
        
.file-upload-section:hover {
    border-color: #6BC5D2;
    background: #f0fdff;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(127, 215, 225, 0.15);
}
        
.file-upload-section.dragover {
    border-color: #6BC5D2;
    background: #e8f8fa;
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(127, 215, 225, 0.2);
}
        
.upload-icon {
    font-size: 3.5rem;
    color: #7FD7E1;
    margin-bottom: 20px;
    transition: transform 0.3s ease;
}
        
.file-upload-section:hover .upload-icon {
    transform: scale(1.1);
}
        
.upload-text {
    font-size: 1.2rem;
    color: #495057;
    margin-bottom: 10px;
    font-weight: 500;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
.upload-hint {
    color: #6c757d;
    font-size: 0.95rem;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
/* Enhanced form elements to match onboarding page */
.form-check-custom {
    background: #fff;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
        
.form-check-custom:hover {
    border-color: #7FD7E1;
    background: #f8fdff;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(127, 215, 225, 0.1);
}
        
.form-check-custom .form-check-input:checked {
    background-color: #7FD7E1;
    border-color: #7FD7E1;
}
        
.info-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}
        
.info-card-header {
    background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
    margin: -25px -25px 20px -25px;
    font-weight: 600;
    font-size: 16px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
        
        .required-column {
            color: #dc3545;
            font-weight: 600;
        }
        
        .optional-column {
            color: #6c757d;
        }
        
        /* Enhanced Alert */
        .alert-info-custom {
            background: linear-gradient(135deg, #e3f2fd, #f0f9ff);
            border: 1px solid #7FD7E1;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .alert-info-custom h6 {
            color: #0277bd;
            margin-bottom: 1rem;
        }
        
        /* Button Enhancements */
        .btn-primary-custom {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #6BC5D2, #5BB0BD);
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .btn-danger-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #c82333, #bd2130);
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #7FD7E1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .progress-bar-custom {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            border-radius: 4px;
        }
        
        /* Bulk Actions Toolbar */
        .bulk-actions-toolbar {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Enhanced Table Styling */
        .custom-control {
            padding-left: 1.5rem;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }
        
        /* Dropdown Styling */
        .dropdown-menu {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item.text-danger:hover {
            background-color: #f5c6cb;
            color: #721c24 !important;
        }
        
        .delete-confirmation {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 0.5rem;
            margin: 0.25rem 0;
            font-size: 0.85rem;
        }
        
        .delete-warning-icon {
            color: #f39c12;
            margin-right: 0.25rem;
        }
        
        .confirm-delete-row {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
        
        .vehicle-details {
            font-size: 0.9rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            animation: slideDown 0.3s ease-out;
        }
        
        .vehicle-details strong {
            color: #495057;
        }
        
        .details-section {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .details-section h6 {
            color: #7FD7E1;
            border-bottom: 2px solid #7FD7E1;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .details-expanded {
            background-color: #f0fdff !important;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        /* Checkbox Enhancements */
        .table .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #7FD7E1;
            border-color: #7FD7E1;
        }
        
        .row-selected {
            background-color: #f0fdff !important;
        }

        /* Additional enhancements to match onboarding page */
        .container-fluid {
            background-color: #f4f6f9;
            padding: 20px 20px 30px 20px;
        }

        /* Enhanced Toast Notification Styling */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
            animation: slideInDown 0.5s ease-out;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-success::before {
            background: linear-gradient(90deg, #28a745, #20c997);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-danger::before {
            background: linear-gradient(90deg, #dc3545, #e74c3c);
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .alert-warning::before {
            background: linear-gradient(90deg, #ffc107, #f39c12);
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .alert-info::before {
            background: linear-gradient(90deg, #17a2b8, #3498db);
        }

        /* Toast Animation */
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideOutUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }

        /* Enhanced close button */
        .alert .close {
            position: absolute;
            top: 0.75rem;
            right: 1rem;
            font-size: 1.5rem;
            font-weight: bold;
            line-height: 1;
            color: inherit;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .alert .close:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Icon styling */
        .alert i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Success icon */
        .alert-success i {
            color: #28a745;
        }

        /* Error icon */
        .alert-danger i {
            color: #dc3545;
        }

        /* Warning icon */
        .alert-warning i {
            color: #ffc107;
        }

        /* Info icon */
        .alert-info i {
            color: #17a2b8;
        }

        /* Badge styling to match onboarding page exactly */
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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

        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }

        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }

        .badge-yellow {
            color: #212529;
            background-color: #EABE14;
        }

        /* Custom dropdown styling - matching vehicle inspection table */
        .status-container {
            position: relative;
        }

        .custom-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 10000 !important;
            min-width: 120px !important;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 8px 12px !important;
            font-size: 12px !important;
            white-space: nowrap !important;
            display: block;
            color: #333;
            text-decoration: none;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #333;
            text-decoration: none;
        }

        .custom-dropdown-menu .dropdown-item {
            padding: 0.5rem 1rem;
        }

        /* Button styling to match vehicle inspection table */
        .btn-outline-success, .btn-outline-warning, .btn-outline-info, .btn-outline-secondary {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
        }

        .btn-outline-success:hover, .btn-outline-warning:hover, .btn-outline-info:hover, .btn-outline-secondary:hover {
            background: transparent !important;
        }

        /* Custom dropdown arrow */
        .dropdown-arrow {
            color: #000 !important;
            font-size: 10px;
            margin-left: 4px;
        }

        .custom-dropdown-toggle {
            cursor: pointer;
        }

        /* Enhanced dropdown styling */
        .dropdown-menu {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            padding: 8px 0;
        }

        .dropdown-item {
            padding: 10px 20px;
            font-size: 14px;
            transition: all 0.2s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .dropdown-item:hover {
            background-color: #f8fdff;
            color: #7FD7E1;
        }

        /* Enhanced form controls */
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 6px;
            padding: 10px 15px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .form-control:focus, .form-select:focus {
            border-color: #7FD7E1;
            box-shadow: 0 0 0 0.2rem rgba(127, 215, 225, 0.25);
        }

        /* Enhanced progress bar */
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background: linear-gradient(135deg, #7FD7E1, #6BC5D2);
            border-radius: 4px;
        }
    </style>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item active">@lang('fleet.vehicles')</li>
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
                <h1>@lang('fleet.manageVehicles')</h1>
                <p class="mb-0" style="opacity: 0.9; font-size: 16px; margin-top: 8px;">Manage your fleet vehicles with ease</p>
            </div>
            <div class="d-flex gap-3">
                <a href="{{ route('vehicles.create') }}" class="btn" style="background-color: #C1C1C1; color: black; border: 1px solid #C1C1C1;" title="Add Vehicle">
                    <i class="fas fa-plus"></i> Add Vehicle
                </a>
                <button type="button" class="btn" style="background-color: #7FD7E1; color: white; border: 1px solid #7FD7E1;" data-toggle="modal" data-target="#import" title="Import Vehicles">
                    <i class="fas fa-file-import"></i> Import Vehicles
                </button>
            </div>
        </div>
        
        <!-- Enhanced Bulk Actions Toolbar -->
        <div class="bulk-actions-toolbar" id="bulkToolbar" style="display: none;">
            <div class="d-flex align-items-center justify-content-between p-4" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: 1px solid #dee2e6; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #7FD7E1, #6BC5D2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0" style="font-weight: 600; color: #333;">Bulk Actions</h6>
                        <small class="text-muted" id="selectedCount">0</small> vehicle(s) selected
                    </div>
                </div>
                <div class="bulk-actions d-flex gap-2">
                    <button class="btn btn-outline-secondary" onclick="clearSelection()" style="border-radius: 6px; padding: 8px 16px;">
                        <i class="fas fa-times"></i> Clear Selection
                    </button>
                    <button class="btn btn-danger" onclick="bulkDeleteVehicles()" style="border-radius: 6px; padding: 8px 16px; background: linear-gradient(135deg, #dc3545, #c82333); border: none;">
                        <i class="fas fa-trash-alt"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-filter"></i> Filter Vehicles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="group_filter" class="form-label">Group</label>
                                <select class="form-control" id="group_filter">
                                    <option value="">All Groups</option>
                                    @foreach($groups ?? [] as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="type_filter" class="form-label">Vehicle Type</label>
                                <select class="form-control" id="type_filter">
                                    <option value="">All Types</option>
                                    <option value="1">Convertible</option>
                                    <option value="2">Coupe</option>
                                    <option value="3">Estate</option>
                                    <option value="4">Hatchback</option>
                                    <option value="5">MPV</option>
                                    <option value="6">Pickup</option>
                                    <option value="7">Saloon</option>
                                    <option value="8">SUV</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fuel_filter" class="form-label">Fuel Type</label>
                                <select class="form-control" id="fuel_filter">
                                    <option value="">All Fuel Types</option>
                                    <option value="Petrol">Petrol</option>
                                    <option value="Diesel">Diesel</option>
                                    <option value="Electric">Electric</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_filter" class="form-label">Status</label>
                                <select class="form-control" id="status_filter">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="rented">Rented</option>
                                    <option value="workshop">Workshop</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-search"></i> Apply Filters
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="vehicles-table">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="ajax_data_table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="chk_all">
                                            <label class="custom-control-label" for="chk_all"></label>
                                        </div>
                                    </th>
                                    <th style="width: 120px;">Registration Plate</th>
                                    <th style="width: 100px;">Make</th>
                                    <th style="width: 100px;">Model</th>
                                    <th style="width: 120px;">Vehicle Type</th>
                                    <th style="width: 100px;">Fuel Type</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 150px;">Assigned Driver</th>
                                    <th style="width: 80px;">Details</th>
                                    <th style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Import Modal -->
    <div id="import" class="modal fade" role="dialog" tabindex="-1" data-backdrop="false" data-keyboard="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-cloud-upload-alt"></i> Import Vehicles</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/import-vehicles', 'method' => 'POST', 'files' => true, 'id' => 'importForm', 'enctype' => 'multipart/form-data']) !!}
                    
                    <!-- File Upload Section -->
                    <div class="file-upload-section" id="fileDropZone">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <div class="upload-text">Drop your Excel/CSV file here or click to browse</div>
                        <div class="upload-hint">Maximum file size: 5MB • Supported formats: .xlsx, .xls, .csv</div>
                        {!! Form::file('excel', ['class' => 'form-control', 'required', 'accept' => '.xlsx,.xls,.csv', 'style' => 'display: none;', 'id' => 'fileInput']) !!}
                        <div id="fileName" class="mt-2" style="display: none;">
                            <i class="fas fa-file-excel text-success"></i> <span id="fileNameText"></span>
                            <button type="button" class="btn btn-sm btn-outline-danger ml-2" id="removeFile">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-cogs"></i> Import Options</label>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="skipDuplicates" name="skip_duplicates" value="1" checked>
                                        <label class="form-check-label" for="skipDuplicates">
                                            <strong>Skip Duplicate Registration Plates</strong><br>
                                            <small class="text-muted">Automatically skip vehicles with existing registration numbers</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="validateData" name="validate_data" value="1" checked>
                                        <label class="form-check-label" for="validateData">
                                            <strong>Validate Data Before Import</strong><br>
                                            <small class="text-muted">Check data integrity and show preview before importing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check-custom">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="createBackup" name="create_backup" value="1">
                                        <label class="form-check-label" for="createBackup">
                                            <strong>Create Backup Before Import</strong><br>
                                            <small class="text-muted">Automatically backup existing vehicle data</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-header">
                                    <i class="fas fa-list-check"></i> Column Requirements
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="text-danger mb-2">Required Fields</h6>
                                        <ul class="list-unstyled small">
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> Registration Plate</li>
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> Make</li>
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> Model</li>
                                            <li class="required-column"><i class="fas fa-asterisk fa-xs"></i> Year</li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="text-muted mb-2">Optional Fields</h6>
                                        <ul class="list-unstyled small">
                                            <li class="optional-column">Color</li>
                                            <li class="optional-column">Vehicle Type</li>
                                            <li class="optional-column">Fuel Type</li>
                                            <li class="optional-column">Mileage</li>
                                            <li class="optional-column">Price</li>
                                            <li class="optional-column">Price Period</li>
                                            <li class="optional-column">Initial Cost</li>
                                            <li class="optional-column">Vehicle Scheme</li>
                                            <li class="optional-column">Insurance Discount</li>
                                            <li class="optional-column">Available</li>
                                            <li class="optional-column">Vehicle Status</li>
                                            <li class="optional-column">Vehicle Group</li>
                                            <li class="optional-column">MOT Expiry Day</li>
                                            <li class="optional-column">MOT Expiry Month</li>
                                            <li class="optional-column">MOT Expiry Year</li>
                                            <li class="optional-column">Telematics Link</li>
                                            <li class="optional-column">Assigned Driver First Name</li>
                                            <li class="optional-column">Assigned Driver Last Name</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href="{{ route("download-vehicle-sample") }}" class="btn btn-outline-success btn-sm" target="_blank">
                                        <i class="fas fa-eye"></i> View Sample Template
                                    </a>
                                    <div class="dropdown ml-2 d-inline-block">
                                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" id="downloadTemplateDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-download"></i> Download Empty Template
                                        </button>
                                        <div class="dropdown-menu" id="templateDropdownMenu">
                                            <a class="dropdown-item" href="{{ route('download-empty-template', ['format' => 'xlsx']) }}">
                                                <i class="fas fa-file-excel"></i> Excel (.xlsx)
                                            </a>
                                            <a class="dropdown-item" href="{{ route('download-empty-template', ['format' => 'csv']) }}">
                                                <i class="fas fa-file-csv"></i> CSV (.csv)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert-info-custom mt-4">
                        <h6><i class="fas fa-info-circle"></i> Important Import Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Validation:</strong> All data is validated before import</li>
                                    <li><strong>Duplicates:</strong> System checks registration plates automatically</li>
                                    <li><strong>Preview:</strong> Review your data before final import</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Errors:</strong> Missing required fields will be highlighted</li>
                                    <li><strong>Progress:</strong> Real-time import progress tracking</li>
                                    <li><strong>Rollback:</strong> Failed imports can be rolled back</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar (Hidden by default) -->
                    <div id="importProgress" style="display: none;" class="mt-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: 0%">
                                <span id="progressText">0%</span>
                            </div>
                        </div>
                        <small class="text-muted">Importing vehicles... Please wait.</small>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="importBtn" style="border-radius: 8px; padding: 12px 24px; font-weight: 600;">
                        <i class="fas fa-upload"></i> Import Vehicles
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 12px 24px; font-weight: 500;">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal -->

    <!-- Modal -->


@endsection

@section('script')
<script type="text/javascript">
// Store vehicles data globally so other functions can access it
window.vehiclesGlobalData = @json($vehicles ?? []);
// Store drivers data globally for dropdown functionality
window.driversGlobalData = @json($drivers ?? []);

// Function to generate driver options for dropdown
function generateDriverOptions(vehicleId) {
    const drivers = window.driversGlobalData || [];
    let options = '';
    drivers.forEach(driver => {
        // Properly escape driver name to prevent JavaScript syntax errors
        const safeDriverName = (driver.name || 'Unknown').replace(/'/g, '&#39;').replace(/"/g, '&quot;').replace(/`/g, '&#96;');
        const safeDriverId = String(driver.id || '');
        options += `<a class="dropdown-item driver-change" href="#" data-driver-id="${safeDriverId}" data-vehicle-id="${vehicleId}">${safeDriverName}</a>`;
    });
    return options;
}

// Simple vanilla JavaScript approach to load vehicles
function loadVehiclesSimple(filteredData = null) {
    const vehiclesData = filteredData || window.vehiclesGlobalData; // Use filtered data if provided
    console.log('Vehicles data:', vehiclesData); // Debug log
    console.log('Number of vehicles:', vehiclesData ? vehiclesData.length : 0);
    
    if (vehiclesData && vehiclesData.length > 0) {
        console.log('First vehicle sample:', vehiclesData[0]);
        console.log('First vehicle ID:', vehiclesData[0].id, 'Type:', typeof vehiclesData[0].id);
    }
    
    const tbody = document.querySelector('#ajax_data_table tbody');
    
    if (!tbody) {
        console.error('Table tbody not found');
        return;
    }

    if (!vehiclesData || vehiclesData.length === 0) {
        console.log('No vehicles data found');
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">No vehicles found</td></tr>';
        return;
    }

    // Generate table rows with proper error handling
    let tableHTML = '';
    vehiclesData.forEach((vehicle, index) => {
        console.log(`Processing vehicle ${index + 1}:`, vehicle);
        
        // Validate vehicle data
        if (!vehicle || !vehicle.id) {
            console.error('Invalid vehicle data:', vehicle);
            return;
        }
        
        // Escape strings to prevent template injection
        const safeId = String(vehicle.id);
        const safePlate = (vehicle.license_plate || 'N/A').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        const safeMake = (vehicle.make_name || 'N/A').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        const safeModel = (vehicle.model_name || 'N/A').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
        const safeEngineType = vehicle.engine_type ? vehicle.engine_type.charAt(0).toUpperCase() + vehicle.engine_type.slice(1) : 'N/A';
        
        // Get vehicle type from relationship (UUID-based); fall back to Not Selected
        let vehicleType = 'Not Selected';
        if (vehicle.types && (vehicle.types.display_name || vehicle.types.name)) {
            vehicleType = vehicle.types.display_name || vehicle.types.name;
        }
        
        tableHTML += `
        <tr id="vehicle-row-${safeId}">
            <td>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input vehicle-checkbox" id="checkbox-${safeId}" name="ids[]" value="${safeId}" onchange="updateSelection()">
                    <label class="custom-control-label" for="checkbox-${safeId}"></label>
                </div>
            </td>
            <td><span class="badge badge-yellow">${safePlate}</span></td>
            <td>${safeMake}</td>
            <td>${safeModel}</td>
            <td>${vehicleType}</td>
            <td>${safeEngineType}</td>
            <td>
                <div class="status-container" data-vehicle-id="${safeId}">
                    <div class="status-display">
                        ${(() => {
                            const vehicleStatus = vehicle.vehicle_status || 'Available';
                            switch (vehicleStatus) {
                                case 'Available':
                                    return '<button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button"><span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span></button>';
                                case 'Rented':
                                    return '<button class="btn btn-sm btn-outline-warning custom-dropdown-toggle" type="button"><span class="badge badge-warning">Rented</span> <span class="dropdown-arrow">▼</span></button>';
                                case 'Workshop':
                                    return '<button class="btn btn-sm btn-outline-info custom-dropdown-toggle" type="button"><span class="badge badge-info">Workshop</span> <span class="dropdown-arrow">▼</span></button>';
                                case 'Disabled':
                                    return '<button class="btn btn-sm btn-outline-secondary custom-dropdown-toggle" type="button"><span class="badge badge-secondary">Disabled</span> <span class="dropdown-arrow">▼</span></button>';
                                default:
                                    return '<button class="btn btn-sm btn-outline-success custom-dropdown-toggle" type="button"><span class="badge badge-success">Available</span> <span class="dropdown-arrow">▼</span></button>';
                            }
                        })()}
                    </div>
                    <div class="custom-dropdown-menu" style="display: none;">
                        <a class="dropdown-item status-change" href="#" data-status="Available" data-vehicle-id="${safeId}">
                            <span class="badge badge-success">Available</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="Rented" data-vehicle-id="${safeId}">
                            <span class="badge badge-warning">Rented</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="Workshop" data-vehicle-id="${safeId}">
                            <span class="badge badge-info">Workshop</span>
                        </a>
                        <a class="dropdown-item status-change" href="#" data-status="Disabled" data-vehicle-id="${safeId}">
                            <span class="badge badge-secondary">Disabled</span>
                        </a>
                    </div>
                </div>
            </td>
            <td>
                <div class="driver-container position-relative">
                    <div class="driver-display">
                        <button class="btn btn-sm btn-outline-secondary custom-dropdown-toggle" type="button">
                            ${vehicle.driver_name ? (vehicle.driver_name.replace(/'/g, '&#39;').replace(/"/g, '&quot;').replace(/`/g, '&#96;')) : '<span class="text-muted">Not Assigned</span>'} <span class="dropdown-arrow">▼</span>
                        </button>
                    </div>
                    <div class="custom-dropdown-menu" style="display: none;">
                        <a class="dropdown-item driver-change" href="#" data-driver-id="" data-vehicle-id="${safeId}">
                            <span class="text-muted">Not Assigned</span>
                        </a>
                        ${generateDriverOptions(safeId)}
                    </div>
                </div>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-info btn-action" onclick="toggleVehicleDetails('${safeId}')" title="View Complete Details" id="details-btn-${safeId}">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ url('admin/vehicles') }}/${safeId}/edit" class="btn btn-sm btn-outline-primary" title="Edit Vehicle" style="padding: 6px 8px;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteVehicle('${safeId}', '${safePlate}', '${safeMake}', '${safeModel}'); return false;" title="Delete Vehicle" style="padding: 6px 8px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = tableHTML;
    
    console.log(`Loaded ${vehiclesData.length} vehicles`);
}

// Global functions for vehicle operations
window.toggleVehicleDetails = function(id, vehicleData = null) {
    console.log('Toggle details called for ID:', id);
    
    const row = document.getElementById(`vehicle-row-${id}`);
    const detailsBtn = document.getElementById(`details-btn-${id}`);
    
    console.log('Found row:', row);
    console.log('Found button:', detailsBtn);
    
    if (!row || !detailsBtn) {
        console.error('Row or button not found for ID:', id);
        alert('Error: Cannot find vehicle row or button');
        return;
    }
    
    // Check if details are currently open by looking for existing details row
    let existingDetails = null;
    const nextRow = row.nextElementSibling;
    if (nextRow && nextRow.classList.contains('vehicle-details-row')) {
        existingDetails = nextRow;
    }
    
    console.log('Existing details found:', existingDetails);
    
    if (existingDetails) {
        // Close details
        console.log('Closing details');
        existingDetails.remove();
        row.classList.remove('details-expanded');
        detailsBtn.innerHTML = '<i class="fas fa-eye"></i> View';
        detailsBtn.classList.remove('btn-info');
        detailsBtn.classList.add('btn-outline-info');
        return;
    }
    
    // Use passed vehicle data or get from global data source as fallback
    let vehicle = vehicleData;
    if (!vehicle) {
        console.log('No vehicle data passed, trying global data source');
        const vehiclesData = window.vehiclesGlobalData || [];
        vehicle = vehiclesData.find(v => v.id == id);
    }
    
    console.log('Found vehicle data:', vehicle);
    
    if (!vehicle) {
        console.error('Vehicle data not found for ID:', id);
        alert('Error: Cannot find vehicle data for ID ' + id);
        return;
    }
    
    // Open details
    console.log('Opening details for vehicle:', vehicle);
    row.classList.add('details-expanded');
    detailsBtn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
    detailsBtn.classList.remove('btn-outline-info');
    detailsBtn.classList.add('btn-info');
    
    // Create simplified details row for testing
    const detailsRow = document.createElement('tr');
    detailsRow.id = `vehicle-details-${id}`;
    detailsRow.className = 'vehicle-details-row';
    detailsRow.style.backgroundColor = '#f8f9fa';
    
    const detailsCell = document.createElement('td');
    detailsCell.setAttribute('colspan', '10');
    detailsCell.style.padding = '20px';
    
    // Render instantly from available row data
    detailsCell.innerHTML = generateInstantVehicleDetails(id, vehicle);

    // Fetch complete vehicle data including metadata in the background and enhance silently
    fetchCompleteVehicleData(id)
        .then(completeVehicle => {
            console.log('Complete vehicle data received:', completeVehicle);
            progressivelyEnhanceVehicleDetails(id, vehicle, completeVehicle);
        })
        .catch(error => {
            console.error('Error fetching complete vehicle data (silent):', error);
            // Do nothing; keep the instant-rendered content
        });
    
    detailsRow.appendChild(detailsCell);
    
    console.log('Created details row:', detailsRow);
    
    // Insert the details row directly after the current vehicle row
    try {
        row.parentNode.insertBefore(detailsRow, row.nextSibling);
        console.log('Details row inserted successfully');
    } catch (error) {
        console.error('Error inserting details row:', error);
        alert('Error inserting details row: ' + error.message);
    }
}

// Helper function to fetch complete vehicle data including metadata
async function fetchCompleteVehicleData(id) {
    try {
        const response = await fetch(`{{ url('admin/vehicles') }}/${id}/complete-data`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Error fetching complete vehicle data:', error);
        throw error;
    }
}

// Generate instant (non-blocking) vehicle details using fields already available in vehiclesGlobalData
function generateInstantVehicleDetails(id, vehicle) {
    const make = vehicle.make_name || vehicle.make || 'Unknown Make';
    const model = vehicle.model_name || vehicle.model || 'Unknown Model';
    const reg = vehicle.license_plate || vehicle.lic_plate || 'Not Set';
    const status = vehicle.vehicle_status || 'Available';
    const typeName = (vehicle.types && (vehicle.types.display_name || vehicle.types.name)) || vehicle.vehicle_type || 'Not Selected';
    const driverName = vehicle.driver_name || 'Not Assigned';
    const year = vehicle.year || '';
    const fuelType = vehicle.fuel_type || vehicle.fuel || '';
    const initialMileage = vehicle.initial_mileage || vehicle.mileage || '';
    const isActive = vehicle.is_active !== undefined ? (vehicle.is_active ? 'Yes' : 'No') : '';
    const scheme = vehicle.scheme || '';
    const telematicsLink = vehicle.telematics_link || '';
    
    // Format MOT date as DD/MM/YY
    const motExpiry = vehicle.mot_expiry ? (() => {
        const date = new Date(vehicle.mot_expiry);
        if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = String(date.getFullYear()).slice(-2);
            return `${day}/${month}/${year}`;
        }
        return '';
    })() : '';
    
    // Get price and insurance data instantly
    const vehiclePrice = vehicle.vehicle_price || vehicle.price || '';
    const insuranceDiscount = vehicle.insurance_discount || '';
    const initialCost = vehicle.initial_cost || '';
    const pricePeriod = vehicle.price_period || 'monthly';
    
    const imageUrl = vehicle.vehicle_image || vehicle.image || '';

    return `
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; max-width: 100%; overflow-x: auto;">
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                ${imageUrl ? `<img src="${imageUrl}" alt="Vehicle" style="width:72px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #eee; margin-bottom: 12px;" />` : ''}
                <div>
                    <h3 style="margin: 0; color: #495057; font-size: 1.5rem;">${make} ${model}${year ? ' (' + year + ')' : ''}</h3>
                    <p style="margin: 4px 0 0 0; color: #6c757d; font-size: 0.95rem;">
                        Vehicle ID: VEH-${id} | Registration: ${reg} | Status: ${status}
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 14px;">
                <div><strong>Vehicle Make:</strong> ${make}</div>
                <div><strong>Vehicle Model:</strong> ${model}</div>
                <div><strong>Vehicle Type:</strong> ${typeName}</div>
                <div><strong>Fuel Type:</strong> ${fuelType || '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Registration Plate:</strong> ${reg}</div>
                <div><strong>Vehicle Year:</strong> ${year || '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Price (with insurance included):</strong> ${vehiclePrice ? vehiclePrice : '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Price Interval:</strong> ${pricePeriod ? pricePeriod.charAt(0).toUpperCase() + pricePeriod.slice(1) : 'Monthly'}</div>
                <div><strong>Insurance Discount:</strong> ${insuranceDiscount ? insuranceDiscount : '<span class="text-muted">N/A</span>'}</div>
                
                <div><strong>Select Driver:</strong> <span id="details-driver-${id}">${driverName}</span></div>
                <div><strong>Initial Mileage (miles):</strong> ${initialMileage || '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Is Active?:</strong> ${isActive || '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Initial Cost:</strong> ${initialCost ? initialCost : '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Scheme:</strong> ${scheme || '<span class="text-muted">N/A</span>'}</div>
                <div><strong>Vehicle Status:</strong> ${status}</div>
                <div><strong>Telematics Link:</strong> ${telematicsLink ? `<a href="${telematicsLink}" target="_blank">View</a>` : '<span class="text-muted">N/A</span>'}</div>
                <div><strong>MOT Expiry Date:</strong> ${motExpiry || '<span class="text-muted">N/A</span>'}</div>
            </div>

            <div style="margin-top: 18px; display:flex; gap:10px; justify-content: center; display: none;">
                <a href="/admin/vehicles/${id}/edit" class="btn btn-warning" style="padding: 8px 14px;"><i class="fas fa-edit"></i> Edit Vehicle</a>
                <a href="/admin/vehicles/${id}" class="btn btn-info" style="padding: 8px 14px;"><i class="fas fa-eye"></i> View Full Details</a>
                <button class="btn btn-secondary" onclick="toggleVehicleDetails('${id}')" style="padding: 8px 14px;"><i class="fas fa-times"></i> Hide Details</button>
            </div>
        </div>
    `;
}

// Enhance the instant details with data from completeVehicle without showing any loading UI
function progressivelyEnhanceVehicleDetails(id, vehicle, completeVehicle) {
    // Group removed

    // Driver name (server may return authoritative value)
    if (completeVehicle && typeof completeVehicle.driver_name !== 'undefined') {
        const el = document.getElementById(`details-driver-${id}`);
        if (el) {
            el.textContent = completeVehicle.driver_name || 'Not Assigned';
        }
    }

    // Note: Price, insurance discount, and initial cost are now displayed instantly
    // from the vehicle data, so no progressive enhancement needed for these fields
}

// Generate comprehensive vehicle details HTML for inline display
function generateCompleteVehicleDetails(id, vehicle, completeVehicle) {
    const purchaseInfo = completeVehicle.purchase_info || [];
    
    // Enhanced price and cost information retrieval
    let vehiclePrice = 0;
    let initialCost = 0;
    let pricePeriod = 'monthly';
    
    // Try to get from metadata first
    if (completeVehicle.metadata) {
        vehiclePrice = parseFloat(completeVehicle.metadata.vehicle_price || completeVehicle.metadata.price || 0);
        initialCost = parseFloat(completeVehicle.metadata.initial_cost || 0);
        pricePeriod = completeVehicle.metadata.price_period || 'monthly';
    }
    
    // Fallback to purchase info
    if (vehiclePrice === 0 || initialCost === 0) {
        purchaseInfo.forEach(item => {
            if (item.exp_name && item.exp_amount) {
                const name = item.exp_name.toLowerCase();
                const amount = parseFloat(item.exp_amount) || 0;
                
                if (name.includes('price') || name.includes('purchase') || name.includes('total')) {
                    vehiclePrice = amount;
                } else if (name.includes('initial') || name.includes('down') || name.includes('deposit')) {
                    initialCost = amount;
                }
            }
        });
    }
    
    // Get metadata information
    const metadata = completeVehicle.metadata || {};
    const allMetadata = completeVehicle.all_metadata || [];
    
    return `
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; max-width: 100%; overflow-x: auto;">
            <!-- Header -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <h3 style="margin: 0; color: #495057; font-size: 1.8rem;">${vehicle.make_name || 'Unknown Make'} ${vehicle.model_name || 'Unknown Model'}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6c757d; font-size: 1.1rem;">
                    Vehicle ID: VEH-${String(id).padStart(4, '0')} | 
                    Registration: ${vehicle.license_plate || 'Not Set'} | 
                    Status: 
                    ${(() => {
                        const vehicleStatus = vehicle.vehicle_status || 'Available';
                        switch (vehicleStatus) {
                            case 'Available':
                                return '<span style="color: #28a745;"><i class="fas fa-check text-success"></i> Available</span>';
                            case 'Rented':
                                return '<span style="color: #ffc107;"><i class="fas fa-exclamation-triangle text-warning"></i> Rented</span>';
                            case 'Workshop':
                                return '<span style="color: #17a2b8;"><i class="fas fa-wrench text-info"></i> Workshop</span>';
                            case 'Disabled':
                                return '<span style="color: #6c757d;"><i class="fas fa-times text-secondary"></i> Disabled</span>';
                            default:
                                return '<span style="color: #28a745;"><i class="fas fa-check text-success"></i> Available</span>';
                        }
                    })()}
                </p>
            </div>
            
            <!-- Vehicle Creation Information -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;"><i class="fas fa-car"></i> Vehicle Creation Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>Vehicle Make:</strong> ${vehicle.make_name || 'Not Selected'}</div>
                    <div><strong>Vehicle Model:</strong> ${vehicle.model_name || 'Not Selected'}</div>
                    <div><strong>Vehicle Type:</strong> ${completeVehicle.vehicle_type || 'Not Selected'}</div>
                    <div><strong>Fuel Type:</strong> ${vehicle.engine_type || 'Not Selected'}</div>
                    <div><strong>Registration Plate:</strong> ${vehicle.license_plate || 'Not Set'}</div>
                    <div><strong>Vehicle Year:</strong> ${vehicle.year || 'Not Set'}</div>
                    
                    <div><strong>Assigned Driver:</strong> ${completeVehicle.driver_name || 'Not Assigned'}</div>
                    <div><strong>Initial Mileage:</strong> ${vehicle.int_mileage ? vehicle.int_mileage.toLocaleString() + ' miles' : 'Not Set'}</div>
                    <div><strong>Is Active:</strong> ${vehicle.in_service == 1 ? '<i class="fas fa-check text-success"></i> Yes' : '<i class="fas fa-times text-danger"></i> No'}</div>
                    <div><strong>Scheme:</strong> ${metadata.vehicle_scheme || 'Not Set'}</div>
                    <div><strong>Vehicle Status:</strong> ${(() => {
                        const vehicleStatus = vehicle.vehicle_status || 'Available';
                        switch (vehicleStatus) {
                            case 'Available': return 'Available';
                            case 'Rented': return 'Rented';
                            case 'Workshop': return 'Workshop';
                            case 'Disabled': return 'Disabled';
                            default: return 'Available';
                        }
                    })()}</div>
                    <div><strong>Telematics Link:</strong> ${metadata.telematics_link ? '<a href="' + metadata.telematics_link + '" target="_blank">View Link</a>' : 'Not Set'}</div>
                    <div><strong>Vehicle ID:</strong> ${id}</div>
                    
                </div>
            </div>
            
            <!-- Purchase & Pricing Information -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;"><i class="fas fa-pound-sign"></i> Purchase & Pricing Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>Vehicle Price (${pricePeriod}):</strong> ${vehiclePrice > 0 ? '£ ' + vehiclePrice.toFixed(2) : 'Not Set'}</div>
                    <div><strong>Insurance Discount:</strong> ${(() => {
                        const insuranceDiscount = parseFloat(completeVehicle.metadata?.insurance_discount || 0);
                        if (insuranceDiscount > 0) {
                            const priceWithoutInsurance = vehiclePrice - insuranceDiscount;
                            return '£ ' + insuranceDiscount.toFixed(2) + '<br><small style="color: #666;">Price without insurance: £ ' + priceWithoutInsurance.toFixed(2) + '</small>';
                        }
                        return 'Not Set';
                    })()}</div>
                    <div><strong>Initial Cost:</strong> ${initialCost > 0 ? '£ ' + initialCost.toFixed(2) : 'Not Set'}</div>
                </div>
            </div>
            
            
            
            <!-- Important Dates -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;"><i class="fas fa-calendar-alt"></i> Important Dates</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>MOT Expiry Date:</strong> ${(() => {
                        const motDate = completeVehicle.metadata?.mot_expiry_date || completeVehicle.metadata?.exp_date || vehicle.lic_exp_date;
                        if (motDate) {
                            const date = new Date(motDate);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = String(date.getFullYear()).slice(-2);
                            return `${day}/${month}/${year}`;
                        }
                        return 'Not Set';
                    })()}</div>
                    <div><strong>Created Date:</strong> ${vehicle.created_at ? new Date(vehicle.created_at).toLocaleString() : 'Not Available'}</div>
                    <div><strong>Last Updated:</strong> ${vehicle.updated_at ? new Date(vehicle.updated_at).toLocaleString() : 'Not Available'}</div>
                    <div><strong>Deleted At:</strong> ${vehicle.deleted_at || 'Not Deleted'}</div>
                </div>
            </div>
            
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; align-items: center; justify-content: space-between; padding-top: 20px; border-top: 1px solid #ddd;">
                <div>
                    <a href="/admin/vehicles/${vehicle.id}/edit" class="btn btn-warning" style="margin-right: 10px; padding: 10px 20px;">
                        <i class="fas fa-edit"></i> Edit Vehicle
                    </a>
                    <a href="/admin/vehicles/${vehicle.id}" class="btn btn-info" style="margin-right: 10px; padding: 10px 20px;">
                        <i class="fas fa-eye"></i> View Full Details
                    </a>
                </div>
                <button class="btn btn-secondary" onclick="toggleVehicleDetails('${id}')" style="padding: 10px 20px;">
                    <i class="fas fa-times"></i> Hide Details
                </button>
            </div>
        </div>
    `;
}

// Basic vehicle creation fields fallback when AJAX fails
function generateEnhancedBasicVehicleDetails(id, vehicle) {
    return `
        <div style="background: white; padding: 25px; border-radius: 8px; border: 1px solid #ddd; max-width: 100%; overflow-x: auto;">
            <!-- Header -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                <h3 style="margin: 0; color: #495057; font-size: 1.8rem;">${vehicle.make_name || 'Unknown Make'} ${vehicle.model_name || 'Unknown Model'}</h3>
                <p style="margin: 0.5rem 0 0 0; color: #6c757d; font-size: 1.1rem;">
                    Vehicle ID: VEH-${String(id).padStart(4, '0')} | 
                    Registration: ${vehicle.license_plate || 'Not Set'} | 
                    Status: 
                    ${(() => {
                        const vehicleStatus = vehicle.vehicle_status || 'Available';
                        switch (vehicleStatus) {
                            case 'Available':
                                return '<span style="color: #28a745;"><i class="fas fa-check text-success"></i> Available</span>';
                            case 'Rented':
                                return '<span style="color: #ffc107;"><i class="fas fa-exclamation-triangle text-warning"></i> Rented</span>';
                            case 'Workshop':
                                return '<span style="color: #17a2b8;"><i class="fas fa-wrench text-info"></i> Workshop</span>';
                            case 'Disabled':
                                return '<span style="color: #6c757d;"><i class="fas fa-times text-secondary"></i> Disabled</span>';
                            default:
                                return '<span style="color: #28a745;"><i class="fas fa-check text-success"></i> Available</span>';
                        }
                    })()}
                </p>
            </div>
            
            <!-- Vehicle Creation Information -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;"><i class="fas fa-car"></i> Vehicle Creation Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>Vehicle Make:</strong> ${vehicle.make_name || 'Not Selected'}</div>
                    <div><strong>Vehicle Model:</strong> ${vehicle.model_name || 'Not Selected'}</div>
                    <div><strong>Vehicle Type:</strong> ${(() => {
                        if (completeVehicle && completeVehicle.types && (completeVehicle.types.display_name || completeVehicle.types.name)) {
                            return completeVehicle.types.display_name || completeVehicle.types.name;
                        }
                        if (vehicle && vehicle.types && (vehicle.types.display_name || vehicle.types.name)) {
                            return vehicle.types.display_name || vehicle.types.name;
                        }
                        return 'Not Selected';
                    })()}</div>
                    <div><strong>Fuel Type:</strong> ${vehicle.engine_type || 'Not Selected'}</div>
                    <div><strong>Registration Plate:</strong> ${vehicle.license_plate || 'Not Set'}</div>
                    <div><strong>Vehicle Year:</strong> ${vehicle.year || 'Not Set'}</div>
                    <div><strong>Vehicle Group:</strong> Not Available (AJAX Failed)</div>
                    <div><strong>Assigned Driver:</strong> Not Available (AJAX Failed)</div>
                    <div><strong>Initial Mileage:</strong> ${vehicle.int_mileage ? vehicle.int_mileage.toLocaleString() + ' miles' : 'Not Set'}</div>
                    <div><strong>Is Active:</strong> ${vehicle.in_service == 1 ? '✅ Yes' : '❌ No'}</div>
                    <div><strong>Scheme:</strong> Not Available (AJAX Failed)</div>
                    <div><strong>Vehicle Status:</strong> ${(() => {
                        const vehicleStatus = vehicle.vehicle_status || 'Available';
                        switch (vehicleStatus) {
                            case 'Available': return 'Available';
                            case 'Rented': return 'Rented';
                            case 'Workshop': return 'Workshop';
                            case 'Disabled': return 'Disabled';
                            default: return 'Available';
                        }
                    })()}</div>
                    <div><strong>Telematics Link:</strong> Not Available (AJAX Failed)</div>
                    <div><strong>Vehicle ID:</strong> ${id}</div>
                    <div><strong>Group ID:</strong> ${vehicle.group_id || 'Not Set'}</div>
                    <div><strong>Type ID:</strong> ${vehicle.type_id || 'Not Set'}</div>
                    <div><strong>User ID:</strong> ${vehicle.user_id || 'Not Set'}</div>
                </div>
            </div>
            
            <!-- Technical Specifications -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;">🔧 Technical Specifications</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>Engine Type:</strong> ${vehicle.engine_type || 'Not Specified'}</div>
                    <div><strong>Horse Power:</strong> ${vehicle.horse_power || 'Not Specified'}</div>
                    <div><strong>Vehicle Color:</strong> ${vehicle.color_name || 'Not Specified'}</div>
                    <div><strong>VIN Number:</strong> ${vehicle.vin || 'Not Available'}</div>
                    <div><strong>Current Mileage:</strong> ${vehicle.mileage ? vehicle.mileage.toFixed(2) + ' miles' : 'Not Recorded'}</div>
                    <div><strong>Insurance Number:</strong> ${vehicle.insurance_number || 'Not Available'}</div>
                </div>
            </div>
            
            <!-- Important Dates -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="color: #7FD7E1; margin-bottom: 15px; border-bottom: 2px solid #7FD7E1; padding-bottom: 8px;"><i class="fas fa-calendar-alt"></i> Important Dates</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div><strong>MOT Expiry Date:</strong> ${(() => {
                        const motDate = vehicle.lic_exp_date;
                        if (motDate) {
                            const date = new Date(motDate);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = String(date.getFullYear()).slice(-2);
                            return `${day}/${month}/${year}`;
                        }
                        return 'Not Set';
                    })()}</div>
                    <div><strong>Created Date:</strong> ${vehicle.created_at ? new Date(vehicle.created_at).toLocaleString() : 'Not Available'}</div>
                    <div><strong>Last Updated:</strong> ${vehicle.updated_at ? new Date(vehicle.updated_at).toLocaleString() : 'Not Available'}</div>
                    <div><strong>Deleted At:</strong> ${vehicle.deleted_at || 'Not Deleted'}</div>
                </div>
            </div>
            
            <!-- Warning Message -->
            <div style="background: #fff3cd; padding: 20px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                <h4 style="color: #856404; margin-bottom: 15px;">⚠️ Limited Data Available</h4>
                <p style="color: #856404; margin: 0;">
                    This is basic vehicle information. Complete details including pricing, metadata, and relationships are not available. 
                    This may be due to network issues or incomplete data loading. Click "View Full Details" for complete information.
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 15px; align-items: center; justify-content: space-between; padding-top: 20px; border-top: 1px solid #ddd;">
                <div>
                    <a href="/admin/vehicles/${vehicle.id}/edit" class="btn btn-warning" style="margin-right: 10px; padding: 10px 20px;">
                        <i class="fas fa-edit"></i> Edit Vehicle
                    </a>
                    <a href="/admin/vehicles/${vehicle.id}" class="btn btn-info" style="margin-right: 10px; padding: 10px 20px;">
                        <i class="fas fa-eye"></i> View Full Details
                    </a>
                    <button class="btn btn-outline-info" onclick="location.reload()" style="margin-right: 10px; padding: 10px 20px;">
                        <i class="fas fa-sync-alt"></i> Refresh Page
                    </button>
                </div>
                <button class="btn btn-secondary" onclick="toggleVehicleDetails('${id}')" style="padding: 10px 20px;">
                    <i class="fas fa-times"></i> Hide Details
                </button>
            </div>
        </div>
    `;
}

window.confirmDeleteVehicle = function(id, plate, make, model) {
    console.log('confirmDeleteVehicle called with ID:', id, 'Plate:', plate, 'Make:', make, 'Model:', model);
    
    if (!id) {
        console.error('No ID provided to confirmDeleteVehicle');
        alert('Error: No vehicle ID provided');
        return;
    }
    
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) {
        console.error('Row not found for ID:', id);
        return;
    }
    
    // First, close ALL existing delete confirmations to prevent stacking
    const allExistingConfirms = document.querySelectorAll('.delete-confirmation-row');
    allExistingConfirms.forEach(confirmRow => {
        const vehicleRow = confirmRow.previousElementSibling;
        if (vehicleRow) {
            vehicleRow.classList.remove('confirm-delete-row');
        }
        confirmRow.remove();
    });
    
    // Check if this specific row already had a confirmation (toggle behavior)
    const existingConfirm = row.nextElementSibling;
    if (existingConfirm && existingConfirm.classList.contains('delete-confirmation-row')) {
        // If it already had a confirmation, we just removed it above, so we're done (toggle off)
        console.log('Delete confirmation toggled off for vehicle:', id);
        return;
    }
    
    // Highlight the row
    row.classList.add('confirm-delete-row');
    
    // Create confirmation row
    const confirmRow = document.createElement('tr');
    confirmRow.className = 'delete-confirmation-row';
    confirmRow.innerHTML = `
        <td colspan="11">
            <div class="delete-confirmation">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <i class="fas fa-exclamation-triangle delete-warning-icon"></i>
                        <strong>Confirm Deletion:</strong> Are you sure you want to delete <strong>${plate} (${make} ${model})</strong>?
                        <br><small class="text-muted">This will permanently delete the vehicle and all associated records.</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-danger mr-2" onclick="deleteVehicle('${id}')">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="cancelDelete('${id}')" type="button">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </td>
    `;
    
    row.parentNode.insertBefore(confirmRow, row.nextSibling);
    
    // Add event listener to cancel button as a fallback
    const cancelBtn = confirmRow.querySelector('.btn-secondary');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Cancel button clicked via event listener');
            cancelDelete(id);
        });
    }
    
    console.log('Delete confirmation toggled on for vehicle:', id);
}

window.cancelDelete = function(id) {
    console.log('cancelDelete called with ID:', id);
    
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) {
        console.error('Row not found for ID:', id);
        return;
    }
    
    // Find the confirmation row (it's a sibling, not a child)
    const confirmRow = row.nextElementSibling;
    if (confirmRow && confirmRow.classList.contains('delete-confirmation-row')) {
        console.log('Removing confirmation row');
        confirmRow.remove();
    } else {
        // Fallback: search for any confirmation row in the table
        const allConfirmRows = document.querySelectorAll('.delete-confirmation-row');
        allConfirmRows.forEach(row => row.remove());
    }
    
    // Remove the highlight class
    row.classList.remove('confirm-delete-row');
    console.log('Delete confirmation cancelled');
}

window.deleteVehicle = function(id) {
    console.log('Starting vehicle deletion for ID:', id, 'Type:', typeof id);
    
    if (!id) {
        console.error('No ID provided to deleteVehicle');
        alert('Error: No vehicle ID provided');
        return;
    }
    
    // Show loading state
    const row = document.getElementById(`vehicle-row-${id}`);
    if (row) {
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
        console.log('Row found and loading state applied');
    } else {
        console.warn('Row not found for ID:', id);
    }
    
    // Get CSRF token with multiple fallbacks
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value ||
                   document.querySelector('input[name="csrf_token"]')?.value ||
                   window.Laravel?.csrfToken;
    
    console.log('CSRF token found:', csrfToken ? 'Yes' : 'No');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showNotification('Security token not found. Please refresh the page.', 'error');
        if (row) {
            row.style.opacity = '1';
            row.style.pointerEvents = 'auto';
        }
        return;
    }
    
    // Submit via fetch for better control
    fetch('{{ url("admin/vehicles") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            '_method': 'DELETE',
            '_token': csrfToken,
            'id': id
        })
    })
    .then(response => {
        console.log('Delete response status:', response.status);
        console.log('Delete response headers:', response.headers);
        
        if (response.ok) {
            return response.json().then(data => {
                console.log('Delete response data:', data);
                
                if (data.success) {
                    // Remove the row from the table immediately
                    if (row) {
                        row.remove();
                    }
                    
                    // Also remove any details row if it exists
                    const detailsRow = document.getElementById(`vehicle-details-${id}`);
                    if (detailsRow) {
                        detailsRow.remove();
                    }
                    
                    // Remove any confirmation row if it exists
                    const confirmRow = document.querySelector('.delete-confirmation-row');
                    if (confirmRow) {
                        confirmRow.remove();
                    }
                    
                    // Show success message
                    showNotification(data.message || 'Vehicle deleted successfully!', 'success');
                    
                    // Verify deletion by checking if vehicle still exists
                    setTimeout(() => {
                        fetch(`{{ url('admin/vehicles') }}/${id}/complete-data`)
                            .then(response => {
                                if (response.ok) {
                                    console.warn('Vehicle still exists after deletion!');
                                    showNotification('Warning: Vehicle may not have been deleted properly. Please refresh the page.', 'warning');
                                } else {
                                    console.log('Vehicle successfully deleted and verified.');
                                }
                            })
                            .catch(error => {
                                console.log('Vehicle deletion verified (not found):', error);
                            });
                    }, 1000);
                    
                    // Update selection if needed
                    if (typeof updateSelection === 'function') {
                        updateSelection();
                    }
                } else {
                    throw new Error(data.error || 'Delete failed');
                }
            }).catch(jsonError => {
                console.error('JSON parsing error:', jsonError);
                // If JSON parsing fails, still try to remove the row as fallback
                if (row) {
                    row.remove();
                }
                showNotification('Vehicle deleted successfully!', 'success');
            });
        } else {
            return response.text().then(text => {
                console.error('Delete failed with response:', text);
                throw new Error('Delete failed with status: ' + response.status);
            });
        }
    })
    .catch(error => {
        console.error('Error deleting vehicle:', error);
        
        // Try fallback method with traditional form submission
        console.log('Trying fallback delete method...');
        const deleteForm = document.createElement('form');
        deleteForm.action = '{{ url("admin/vehicles") }}/' + id;
        deleteForm.method = 'POST';
        deleteForm.style.display = 'none';
        deleteForm.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="id" value="${id}">
        `;
        
        document.body.appendChild(deleteForm);
        deleteForm.submit();
    });
}

// Simple test function to verify deletion
window.testVehicleDeletion = function(id) {
    console.log('Testing vehicle deletion for ID:', id);
    
    // Check if vehicle exists in the table
    const row = document.getElementById(`vehicle-row-${id}`);
    if (row) {
        console.log('Vehicle found in table - ID:', id);
        return true;
    } else {
        console.log('Vehicle not found in table - ID:', id);
        return false;
    }
}

// Test function to verify all delete buttons are working
window.testAllDeleteButtons = function() {
    console.log('Testing all delete buttons...');
    const deleteButtons = document.querySelectorAll('button[onclick*="confirmDeleteVehicle"]');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach((button, index) => {
        const onclick = button.getAttribute('onclick');
        console.log(`Button ${index + 1} onclick:`, onclick);
        
        // Extract the ID from the onclick attribute
        const match = onclick.match(/confirmDeleteVehicle\('([^']+)'/);
        if (match) {
            const id = match[1];
            console.log(`Button ${index + 1} ID:`, id);
        } else {
            console.error(`Button ${index + 1} - Could not extract ID from:`, onclick);
        }
    });
}

// Test function to manually trigger delete for a specific ID
window.testDeleteById = function(id) {
    console.log('Manually testing delete for ID:', id);
    
    // Test if the row exists
    const row = document.getElementById(`vehicle-row-${id}`);
    if (!row) {
        console.error('Row not found for ID:', id);
        return false;
    }
    
    // Test if the delete button exists
    const deleteButton = row.querySelector('button[onclick*="confirmDeleteVehicle"]');
    if (!deleteButton) {
        console.error('Delete button not found for ID:', id);
        return false;
    }
    
    console.log('Row and delete button found, triggering delete...');
    
    // Manually call the confirmDeleteVehicle function
    const onclick = deleteButton.getAttribute('onclick');
    const match = onclick.match(/confirmDeleteVehicle\('([^']+)',\s*'([^']+)',\s*'([^']+)',\s*'([^']+)'/);
    if (match) {
        const [, vehicleId, plate, make, model] = match;
        console.log('Calling confirmDeleteVehicle with:', vehicleId, plate, make, model);
        confirmDeleteVehicle(vehicleId, plate, make, model);
        return true;
    } else {
        console.error('Could not parse onclick attribute:', onclick);
        return false;
    }
}

// Alternative deletion method that doesn't depend on external libraries
window.deleteVehicleSimple = function(id) {
    console.log('Using simple deletion method for ID:', id);
    
    if (confirm('Are you sure you want to delete this vehicle?')) {
        // Create a simple form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url("admin/vehicles") }}/' + id;
        form.style.display = 'none';
        
        // Add CSRF token
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Add method override
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        // Add ID
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(tokenInput);
        form.appendChild(methodInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}


window.bulkDeleteVehicles = function() {
    const selectedCheckboxes = document.querySelectorAll('.vehicle-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one vehicle to delete.');
        return;
    }
    
    const vehicleNames = Array.from(selectedCheckboxes).map(checkbox => {
        const row = checkbox.closest('tr');
        const plate = row.querySelector('.badge').textContent;
        return plate;
    }).join(', ');
    
    if (confirm(`Are you sure you want to delete ${selectedCheckboxes.length} vehicle(s)?\n\nVehicles: ${vehicleNames}\n\nThis action cannot be undone and will delete all associated records.`)) {
        // Create bulk delete form
        const form = document.createElement('form');
        form.action = '{{ url("admin/delete-vehicles") }}';
        form.method = 'POST';
        
        let formHtml = '@csrf';
        selectedCheckboxes.forEach(checkbox => {
            formHtml += `<input type="hidden" name="ids[]" value="${checkbox.value}">`;
        });
        
        form.innerHTML = formHtml;
        document.body.appendChild(form);
        form.submit();
    }
}

window.updateSelection = function() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const checkedBoxes = document.querySelectorAll('.vehicle-checkbox:checked');
    const selectAllCheckbox = document.getElementById('chk_all');
    const bulkToolbar = document.getElementById('bulkToolbar');
    const selectedCount = document.getElementById('selectedCount');
    
    // Update select all checkbox state
    if (checkedBoxes.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
    } else if (checkedBoxes.length === checkboxes.length) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
    }
    
    // Show/hide bulk toolbar
    if (checkedBoxes.length > 0) {
        bulkToolbar.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
        
        // Highlight selected rows
        checkboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) {
                row.classList.add('row-selected');
            } else {
                row.classList.remove('row-selected');
            }
        });
    } else {
        bulkToolbar.style.display = 'none';
        // Remove all row highlights
        checkboxes.forEach(cb => {
            cb.closest('tr').classList.remove('row-selected');
        });
    }
}

window.clearSelection = function() {
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const selectAllCheckbox = document.getElementById('chk_all');
    
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    selectAllCheckbox.checked = false;
    selectAllCheckbox.indeterminate = false;
    
    updateSelection();
}

// Enhanced functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - Starting vehicle initialization');
    
    // Wait a bit for jQuery to load if needed, then initialize
    setTimeout(() => {
        try {
            // Initialize drag and drop for import modal
            initializeDragAndDrop();
            
            // Initialize import form enhancements
            initializeImportForm();
            
            // Initialize delete confirmations
            initializeDeleteModals();
            
            // Load vehicles when page is ready
            console.log('About to load vehicles...');
            loadVehiclesSimple();
            console.log('Vehicle loading function called');
            
            // Initialize selection handlers
            setTimeout(() => {
                updateSelection();
            }, 500);
            
        } catch (error) {
            console.error('Error during initialization:', error);
            // Try loading vehicles without other initializations
            try {
                loadVehiclesSimple();
            } catch (vehicleError) {
                console.error('Error loading vehicles:', vehicleError);
            }
        }
    }, 100);
});

// Drag and Drop functionality
function initializeDragAndDrop() {
    const dropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const fileNameText = document.getElementById('fileNameText');
    const removeFile = document.getElementById('removeFile');
    
    if (!dropZone || !fileInput) return;
    
    // Click to browse files
    dropZone.addEventListener('click', function(e) {
        if (e.target.id !== 'removeFile') {
            fileInput.click();
        }
    });
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop zone when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    
    // Handle file input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(this.files);
    });
    
    // Remove file button
    if (removeFile) {
        removeFile.addEventListener('click', function(e) {
            e.stopPropagation();
            clearFile();
        });
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        dropZone.classList.add('dragover');
    }
    
    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }
    
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (validateFile(file)) {
                fileInput.files = files;
                displayFile(file);
            }
        }
    }
    
    function validateFile(file) {
        const allowedTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx?|csv)$/i)) {
            alert('Please select a valid Excel or CSV file (.xlsx, .xls, .csv)');
            return false;
        }
        
        if (file.size > maxSize) {
            alert('File size must be less than 5MB');
            return false;
        }
        
        return true;
    }
    
    function displayFile(file) {
        fileNameText.textContent = file.name;
        fileName.style.display = 'block';
        dropZone.querySelector('.upload-text').style.display = 'none';
        dropZone.querySelector('.upload-hint').style.display = 'none';
        dropZone.querySelector('.upload-icon').style.display = 'none';
    }
    
    function clearFile() {
        fileInput.value = '';
        fileName.style.display = 'none';
        dropZone.querySelector('.upload-text').style.display = 'block';
        dropZone.querySelector('.upload-hint').style.display = 'block';
        dropZone.querySelector('.upload-icon').style.display = 'block';
    }
}

// Import form enhancements
function initializeImportForm() {
    const importForm = document.getElementById('importForm');
    const importBtn = document.getElementById('importBtn');
    const progressDiv = document.getElementById('importProgress');
    const progressBar = progressDiv?.querySelector('.progress-bar');
    const progressText = document.getElementById('progressText');
    
    if (!importForm) return;
    
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const fileInput = document.getElementById('fileInput');
        
        if (!fileInput.files.length) {
            alert('Please select a file to import');
            return;
        }
        
        // Show progress
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        progressDiv.style.display = 'block';
        
        // Simulate progress (replace with actual upload progress)
        simulateProgress();
        
        // Submit form (you may want to use AJAX here for better UX)
        setTimeout(() => {
            this.submit();
        }, 1000);
    });
    
    function simulateProgress() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 95) {
                progress = 95;
                clearInterval(interval);
            }
            
            if (progressBar) {
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
            }
        }, 200);
    }
}

// Enhanced delete modals
function initializeDeleteModals() {
    // Add global click handler to close delete confirmations when clicking outside
    document.addEventListener('click', function(e) {
        // If clicking outside a delete confirmation, close any open confirmations
        if (!e.target.closest('.delete-confirmation') && !e.target.closest('[onclick*="confirmDeleteVehicle"]')) {
            const openConfirmations = document.querySelectorAll('.delete-confirmation-row');
            if (openConfirmations.length > 0) {
                console.log('Closing all delete confirmations due to outside click');
                openConfirmations.forEach(confirmRow => {
                    const vehicleRow = confirmRow.previousElementSibling;
                    if (vehicleRow) {
                        vehicleRow.classList.remove('confirm-delete-row');
                    }
                    confirmRow.remove();
                });
            }
        }
    });
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (window.currentDeleteId) {
                const form = document.getElementById('form_' + window.currentDeleteId);
                if (form) {
                    form.submit();
                } else {
                    // Create and submit a delete form
                    const deleteForm = document.createElement('form');
                    deleteForm.action = '{{ url("admin/vehicles") }}/' + window.currentDeleteId;
                    deleteForm.method = 'POST';
                    deleteForm.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(deleteForm);
                    deleteForm.submit();
                }
                
                // Hide modal using Bootstrap's modal methods if available
                const modal = document.getElementById('deleteModal');
                if (modal && typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }
            }
        });
    }
    
    // Check all functionality
    const checkAll = document.getElementById('chk_all');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.vehicle-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelection();
        });
    }
}

// Notification function
window.showNotification = function(message, type = 'info') {
    try {
        // Create notification element
        let alertClass = 'info';
        if (type === 'success') alertClass = 'success';
        else if (type === 'error') alertClass = 'danger';
        else if (type === 'warning') alertClass = 'warning';
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${alertClass} alert-dismissible fade show`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" onclick="this.parentElement.remove()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification && notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    } catch (error) {
        // Fallback to simple alert if notification system fails
        console.error('Notification system error:', error);
        alert(message);
    }
};

// Simple notification that doesn't depend on Bootstrap
window.showSimpleNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '5px';
    notification.style.color = 'white';
    notification.style.fontWeight = 'bold';
    notification.style.minWidth = '300px';
    notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    
    // Set background color based on type
    if (type === 'success') {
        notification.style.backgroundColor = '#28a745';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#dc3545';
    } else if (type === 'warning') {
        notification.style.backgroundColor = '#ffc107';
        notification.style.color = '#000';
    } else {
        notification.style.backgroundColor = '#17a2b8';
    }
    
    notification.innerHTML = `
        ${message}
        <button onclick="this.parentElement.remove()" style="float: right; background: none; border: none; color: inherit; font-size: 18px; cursor: pointer; margin-left: 10px;">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification && notification.parentNode) {
            notification.remove();
        }
    }, 5000);
};

// Function to reload and test the entire flow
window.reloadAndTestVehicles = function() {
    console.log('Reloading vehicles and testing...');
    
    // Clear existing data
    const tbody = document.querySelector('#ajax_data_table tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">Loading...</td></tr>';
    }
    
    // Reload vehicles
    loadVehiclesSimple();
    
    // Wait a bit then test
    setTimeout(() => {
        console.log('Testing delete buttons after reload...');
        testAllDeleteButtons();
    }, 500);
}

// Also try immediate execution as fallback
console.log('Vehicle script loaded - attempting immediate execution');
if (document.readyState === 'loading') {
    console.log('Document still loading, waiting for DOMContentLoaded');
} else {
    console.log('Document already loaded, executing immediately');
    setTimeout(loadVehiclesSimple, 100);
}

// Enhanced Toast Notification System
function initializeToastNotifications() {
    // Auto-dismiss success messages after 5 seconds
    $('.alert-success').each(function() {
        const $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    });

    // Auto-dismiss info messages after 4 seconds
    $('.alert-info').each(function() {
        const $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut(500, function() {
                $(this).remove();
            });
        }, 4000);
    });

    // Auto-dismiss warning messages after 6 seconds
    $('.alert-warning').each(function() {
        const $alert = $(this);
        setTimeout(function() {
            $alert.fadeOut(500, function() {
                $(this).remove();
            });
        }, 6000);
    });

    // Error messages stay until manually dismissed
    // But add a subtle pulse animation to draw attention
    $('.alert-danger').addClass('pulse-animation');
}

// Function to show custom toast notifications
function showToast(message, type = 'success', duration = 5000) {
    const iconMap = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-triangle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    };

    const alertClass = `alert-${type}`;
    const icon = iconMap[type] || iconMap['success'];
    const title = type.charAt(0).toUpperCase() + type.slice(1);

    const toastHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon}"></i> 
            <strong>${title}!</strong> ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Insert at the top of the container
    $('.container-fluid').prepend(toastHtml);

    // Auto-dismiss based on type
    if (type !== 'error') {
        setTimeout(function() {
            $(`.alert-${type}`).first().fadeOut(500, function() {
                $(this).remove();
            });
        }, duration);
    }
}

// Add pulse animation for error messages
$('<style>')
    .prop('type', 'text/css')
    .html(`
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
    `)
    .appendTo('head');

// Initialize toast notifications when page loads
$(document).ready(function() {
    initializeToastNotifications();
    
    // Handle custom dropdown functionality
    $(document).on('click', '.custom-dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const container = $(this).closest('.status-container, .driver-container');
        const dropdown = container.find('.custom-dropdown-menu');
        
        // Hide all other dropdowns
        $('.custom-dropdown-menu').not(dropdown).hide();
        
        // Toggle current dropdown
        dropdown.toggle();
        
        console.log('Dropdown toggled for vehicle:', container.data('vehicle-id'));
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.status-container, .driver-container').length) {
            $('.custom-dropdown-menu').hide();
        }
    });
    
    // Handle status change
    $(document).on('click', '.status-change', function(e) {
        e.preventDefault();
        const vehicleId = $(this).data('vehicle-id');
        const newStatus = $(this).data('status');
        const container = $(this).closest('.status-container');
        const display = container.find('.status-display');
        const dropdown = container.find('.custom-dropdown-menu');
        const displayButton = display.find('button');
        
        // Store original button HTML for restoration
        const originalHtml = displayButton.html();
        const originalDisabled = displayButton.prop('disabled');
        
        // Show loading state on display button
        displayButton.prop('disabled', true);
        displayButton.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        
        console.log('Updating vehicle status:', { vehicleId, newStatus });
        
        // Update status via AJAX
        $.ajax({
            url: '/admin/vehicles/update-status',
            method: 'POST',
            data: {
                vehicle_id: vehicleId,
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Status update response:', response);
                if (response.success) {
                    // Update the display button with new status
                    const buttonClass = getButtonClass(newStatus);
                    const badgeClass = getBadgeClass(newStatus);
                    display.html(`<button class="btn btn-sm ${buttonClass} custom-dropdown-toggle" type="button"><span class="badge ${badgeClass}">${newStatus}</span> <span class="dropdown-arrow">▼</span></button>`);
                    
                    // Hide dropdown
                    dropdown.hide();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Vehicle status updated successfully!');
                    } else {
                        alert('Vehicle status updated successfully!');
                    }
                } else {
                    console.error('Status update failed:', response.message);
                    // Restore original button state on error
                    displayButton.html(originalHtml).prop('disabled', originalDisabled);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to update status: ' + (response.message || 'Unknown error'));
                    } else {
                        alert('Failed to update status: ' + (response.message || 'Unknown error'));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', { xhr, status, error });
                // Restore original button state on error
                displayButton.html(originalHtml).prop('disabled', originalDisabled);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update status. Please try again.');
                } else {
                    alert('Failed to update status. Please try again.');
                }
            }
        });
    });
    
    // Handle driver change
    $(document).on('click', '.driver-change', function(e) {
        e.preventDefault();
        const vehicleId = $(this).data('vehicle-id');
        const driverId = $(this).data('driver-id');
        const container = $(this).closest('.driver-container');
        const display = container.find('.driver-display');
        const dropdown = container.find('.custom-dropdown-menu');
        const displayButton = display.find('button');
        
        // Store original button HTML for restoration
        const originalHtml = displayButton.html();
        const originalDisabled = displayButton.prop('disabled');
        
        // Show loading state on display button
        displayButton.prop('disabled', true);
        displayButton.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        
        console.log('Updating vehicle driver:', { vehicleId, driverId });
        
        // Update driver via AJAX
        $.ajax({
            url: '/admin/vehicles/update-driver',
            method: 'POST',
            data: {
                vehicle_id: vehicleId,
                driver_id: driverId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Driver update response:', response);
                if (response.success) {
                    // Update the display button with new driver
                    const driverName = response.driver_name || '<span class="text-muted">Not Assigned</span>';
                    display.html(`<button class="btn btn-sm btn-outline-secondary custom-dropdown-toggle" type="button">${driverName} <span class="dropdown-arrow">▼</span></button>`);
                    
                    // Update vehicle status display automatically
                    const newStatus = response.vehicle_status || (driverId ? 'Rented' : 'Available');
                    updateVehicleStatusDisplay(vehicleId, newStatus);
                    
                    // Hide dropdown
                    dropdown.hide();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Driver assignment updated successfully!');
                    } else {
                        alert('Driver assignment updated successfully!');
                    }
                } else {
                    // Restore original button state on error
                    displayButton.html(originalHtml).prop('disabled', originalDisabled);
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to update driver assignment');
                    } else {
                        alert(response.message || 'Failed to update driver assignment');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', { xhr, status, error });
                // Restore original button state on error
                displayButton.html(originalHtml).prop('disabled', originalDisabled);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to update driver assignment. Please try again.');
                } else {
                    alert('Failed to update driver assignment. Please try again.');
                }
            }
        });
    });
});

// Helper function to get badge class for status
function getBadgeClass(status) {
    switch (status) {
        case 'Available':
            return 'badge-success';
        case 'Rented':
            return 'badge-warning';
        case 'Workshop':
            return 'badge-info';
        case 'Disabled':
            return 'badge-secondary';
        default:
            return 'badge-success';
    }
}

// Helper function to get button class for status
function getButtonClass(status) {
    switch (status) {
        case 'Available':
            return 'btn-outline-success';
        case 'Rented':
            return 'btn-outline-warning';
        case 'Workshop':
            return 'btn-outline-info';
        case 'Disabled':
            return 'btn-outline-secondary';
        default:
            return 'btn-outline-success';
    }
}

// Filter functions - Client-side filtering to avoid AJAX issues
function applyFilters() {
    const groupFilter = document.getElementById('group_filter').value;
    const typeFilter = document.getElementById('type_filter').value;
    const fuelFilter = document.getElementById('fuel_filter').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    console.log('Applying filters:', { groupFilter, typeFilter, fuelFilter, statusFilter });
    
    // Show loading state
    const tbody = document.querySelector('#ajax_data_table tbody');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center">Filtering vehicles...</td></tr>';
    }
    
    // Filter the existing vehicles data
    const allVehicles = window.vehiclesGlobalData || [];
    let filteredVehicles = allVehicles.filter(vehicle => {
        // Group filter
        if (groupFilter && vehicle.group_id != groupFilter) {
            return false;
        }
        
        // Type filter
        if (typeFilter && vehicle.type_id != typeFilter) {
            return false;
        }
        
        // Fuel filter
        if (fuelFilter && vehicle.engine_type && vehicle.engine_type.toLowerCase() !== fuelFilter.toLowerCase()) {
            return false;
        }
        
        // Status filter
        if (statusFilter) {
            const vehicleStatus = getVehicleStatus(vehicle);
            if (statusFilter === 'available' && vehicleStatus !== 'Available') {
                return false;
            }
            if (statusFilter === 'rented' && vehicleStatus !== 'Rented') {
                return false;
            }
            if (statusFilter === 'workshop' && vehicleStatus !== 'Workshop') {
                return false;
            }
            if (statusFilter === 'disabled' && vehicleStatus !== 'Disabled') {
                return false;
            }
        }
        
        return true;
    });
    
    console.log(`Filtered ${filteredVehicles.length} vehicles from ${allVehicles.length} total`);
    
    // Debug: Show status distribution
    if (statusFilter) {
        const statusCounts = {};
        allVehicles.forEach(vehicle => {
            const status = getVehicleStatus(vehicle);
            statusCounts[status] = (statusCounts[status] || 0) + 1;
        });
        console.log('Status distribution:', statusCounts);
        console.log(`Looking for status: ${statusFilter}`);
    }
    
    // Load the filtered vehicles
    setTimeout(() => {
        loadVehiclesSimple(filteredVehicles);
    }, 100);
}

// Helper function to get vehicle status
function getVehicleStatus(vehicle) {
    // First check if there's a specific vehicle_status in metadata
    const vehicleStatus = vehicle.meta_data?.vehicle_status || 
                         (vehicle.metas && vehicle.metas.find(m => m.key === 'vehicle_status')?.value);
    
    if (vehicleStatus) {
        // Return the status from metadata (capitalize first letter)
        return vehicleStatus.charAt(0).toUpperCase() + vehicleStatus.slice(1).toLowerCase();
    }
    
    // Fallback logic if no specific status in metadata
    if (vehicle.in_service === 0) {
        return 'Disabled';
    }
    
    // Check if vehicle has assigned driver
    const assignedDriverId = vehicle.meta_data?.assign_driver_id || 
                            (vehicle.metas && vehicle.metas.find(m => m.key === 'assign_driver_id')?.value);
    
    if (assignedDriverId) {
        return 'Rented';
    }
    
    return 'Available';
}

// Function to update vehicle status display without page refresh
function updateVehicleStatusDisplay(vehicleId, newStatus) {
    const statusContainer = $(`.status-container[data-vehicle-id="${vehicleId}"]`);
    if (statusContainer.length) {
        const display = statusContainer.find('.status-display');
        const buttonClass = getButtonClass(newStatus);
        const badgeClass = getBadgeClass(newStatus);
        
        display.html(`<button class="btn btn-sm ${buttonClass} custom-dropdown-toggle" type="button"><span class="badge ${badgeClass}">${newStatus}</span> <span class="dropdown-arrow">▼</span></button>`);
        
        console.log(`Updated status display for vehicle ${vehicleId} to ${newStatus}`);
    }
}

function clearFilters() {
    document.getElementById('group_filter').value = '';
    document.getElementById('type_filter').value = '';
    document.getElementById('fuel_filter').value = '';
    document.getElementById('status_filter').value = '';
    
    // Reload all vehicles
    loadVehiclesSimple();
}

// Handle dropdown behavior for template download
document.addEventListener('DOMContentLoaded', function() {
    const dropdownButton = document.getElementById('downloadTemplateDropdown');
    const dropdownMenu = document.getElementById('templateDropdownMenu');
    
    if (dropdownButton && dropdownMenu) {
        // Hide dropdown initially
        dropdownMenu.style.display = 'none';
        
        // Toggle dropdown on button click
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (dropdownMenu.style.display === 'none') {
                dropdownMenu.style.display = 'block';
                dropdownButton.setAttribute('aria-expanded', 'true');
            } else {
                dropdownMenu.style.display = 'none';
                dropdownButton.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.style.display = 'none';
                dropdownButton.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Hide dropdown when selecting an option
        const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(function(item) {
            item.addEventListener('click', function() {
                dropdownMenu.style.display = 'none';
                dropdownButton.setAttribute('aria-expanded', 'false');
            });
        });
    }
});
</script>
@endsection
