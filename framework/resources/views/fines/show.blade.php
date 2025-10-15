@extends('layouts.app')
@section('extra_css')
<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

/* Header styles */
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
}

/* Main content */
.content {
    padding: 0 15px 20px;
}

.main-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-bottom: 20px;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
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

/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 16px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.3s ease;
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

.btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 3px;
}

/* Info grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.info-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 15px;
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 16px;
    color: #333;
    word-break: break-word;
}

/* Badges */
.badge {
    display: inline-block;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 4px;
}

.badge-warning {
    color: #212529;
    background-color: #ffc107;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-dark {
    color: #fff;
    background-color: #343a40;
}

/* Amount display */
.amount-display {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    margin: 20px 0;
}

.amount-label {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 5px;
}

.amount-value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 10px;
}

.amount-breakdown {
    font-size: 12px;
    opacity: 0.8;
}

/* Status update section */
.status-update {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 4px;
    padding: 15px;
    margin: 20px 0;
}

.status-form {
    display: flex;
    gap: 10px;
    align-items: end;
    flex-wrap: wrap;
}

.status-form .form-group {
    margin-bottom: 0;
    min-width: 200px;
}

.status-form label {
    font-weight: 500;
    margin-bottom: 5px;
    color: #856404;
}

.form-control {
    display: block;
    width: 100%;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}

.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);
}

/* File display */
.file-display {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
}

.file-display a {
    color: #0066cc;
    text-decoration: none;
    font-weight: 500;
}

.file-display a:hover {
    text-decoration: underline;
}

/* Notes section */
.notes-section {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin: 20px 0;
}

.notes-content {
    font-style: italic;
    color: #6c757d;
    line-height: 1.6;
}

/* Timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-date {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}

.timeline-content {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .status-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .status-form .form-group {
        min-width: auto;
    }
    
    .header-content {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="header-card">
            <div class="header-content">
                <h1><i class="fas fa-gavel"></i> Fine Details</h1>
                <div>
                    <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Fine
                    </a>
                    <a href="{{ route('fines.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Fines
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <!-- Basic Information -->
        <div class="main-card">
            <div class="card-header">
                <h3 class="card-title">Basic Information</h3>
                <span class="badge {{ $fine->status_badge }}">{{ ucfirst($fine->status) }}</span>
            </div>
            
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Fine ID</div>
                        <div class="info-value">#{{ $fine->id }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Fine Type</div>
                        <div class="info-value">{{ $fine->fine_type }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Contravention Code</div>
                        <div class="info-value">{{ $fine->contravention_code ?: 'N/A' }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Reference Number</div>
                        <div class="info-value">{{ $fine->reference_number ?: 'N/A' }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Vehicle Registration</div>
                        <div class="info-value">{{ $fine->vehicle_reg }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Vehicle Details</div>
                        <div class="info-value">
                            @if($fine->vehicle)
                                {{ $fine->vehicle->make_name }} {{ $fine->vehicle->model_name }} ({{ $fine->vehicle->license_plate }})
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Assigned Driver</div>
                        <div class="info-value">
                            @if($fine->driver)
                                {{ $fine->driver->name }} ({{ $fine->driver->email }})
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Date Logged</div>
                        <div class="info-value">{{ $fine->date_logged->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amount Information -->
        <div class="main-card">
            <div class="card-header">
                <h3 class="card-title">Amount Information</h3>
            </div>
            
            <div class="card-body">
                <div class="amount-display">
                    <div class="amount-label">Current Amount Due</div>
                    <div class="amount-value">£{{ number_format($fine->current_amount, 2) }}</div>
                    <div class="amount-breakdown">
                        @if($fine->is_escalated)
                            <span class="badge badge-danger">ESCALATED</span>
                        @elseif($fine->is_in_discount_window)
                            <span class="badge badge-success">DISCOUNT APPLIED</span>
                        @endif
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Original Fine Amount</div>
                        <div class="info-value">£{{ number_format($fine->price, 2) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Admin Fee</div>
                        <div class="info-value">£{{ number_format($fine->admin_fee, 2) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value">£{{ number_format($fine->total_amount, 2) }}</div>
                    </div>
                    
                    @if($fine->discount_amount)
                        <div class="info-item">
                            <div class="info-label">Discount Amount</div>
                            <div class="info-value">£{{ number_format($fine->discount_amount, 2) }}</div>
                        </div>
                    @endif
                    
                    @if($fine->escalation_multiplier > 1)
                        <div class="info-item">
                            <div class="info-label">Escalation Multiplier</div>
                            <div class="info-value">{{ $fine->escalation_multiplier }}x</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Timeline and Dates -->
        <div class="main-card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $fine->date_logged->format('d/m/Y H:i') }}</div>
                        <div class="timeline-content">
                            <strong>Fine Logged</strong><br>
                            Fine was initially logged in the system
                        </div>
                    </div>
                    
                    @if($fine->due_date)
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $fine->due_date->format('d/m/Y') }}</div>
                            <div class="timeline-content">
                                <strong>Due Date</strong><br>
                                @if($fine->due_date < now() && !in_array($fine->status, ['paid', 'disputed']))
                                    <span class="badge badge-danger">OVERDUE</span>
                                @else
                                    Payment due date
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($fine->escalation_date)
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $fine->escalation_date->format('d/m/Y') }}</div>
                            <div class="timeline-content">
                                <strong>Escalation Date</strong><br>
                                @if($fine->is_escalated)
                                    <span class="badge badge-danger">ESCALATED</span>
                                @else
                                    Fine will escalate if not paid
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($fine->discount_window_days)
                        <div class="timeline-item">
                            <div class="timeline-date">{{ $fine->date_logged->addDays($fine->discount_window_days)->format('d/m/Y') }}</div>
                            <div class="timeline-content">
                                <strong>Discount Window Ends</strong><br>
                                @if($fine->is_in_discount_window)
                                    <span class="badge badge-success">ACTIVE</span>
                                @else
                                    Discount period has ended
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Update -->
        @if($fine->status !== 'paid')
            <div class="main-card">
                <div class="card-header">
                    <h3 class="card-title">Update Status</h3>
                </div>
                
                <div class="card-body">
                    <div class="status-update">
                        <form id="status-form" class="status-form">
                            <div class="form-group">
                                <label for="new_status">New Status</label>
                                <select name="status" id="new_status" class="form-control">
                                    <option value="pending" {{ $fine->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="notified" {{ $fine->status == 'notified' ? 'selected' : '' }}>Notified</option>
                                    <option value="paid" {{ $fine->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="disputed" {{ $fine->status == 'disputed' ? 'selected' : '' }}>Disputed</option>
                                    <option value="escalated" {{ $fine->status == 'escalated' ? 'selected' : '' }}>Escalated</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Additional Information -->
        <div class="main-card">
            <div class="card-header">
                <h3 class="card-title">Additional Information</h3>
            </div>
            
            <div class="card-body">
                @if($fine->evidence_file)
                    <div class="file-display">
                        <i class="fas fa-file"></i> 
                        <strong>Evidence File:</strong> 
                        <a href="{{ Storage::url($fine->evidence_file) }}" target="_blank">{{ basename($fine->evidence_file) }}</a>
                    </div>
                @endif
                
                @if($fine->notes)
                    <div class="notes-section">
                        <strong>Notes:</strong>
                        <div class="notes-content">{{ $fine->notes }}</div>
                    </div>
                @endif
                
                <div class="info-grid">
                    @if($fine->discount_window_days)
                        <div class="info-item">
                            <div class="info-label">Discount Window</div>
                            <div class="info-value">{{ $fine->discount_window_days }} days</div>
                        </div>
                    @endif
                    
                    @if($fine->escalation_days)
                        <div class="info-item">
                            <div class="info-label">Escalation Period</div>
                            <div class="info-value">{{ $fine->escalation_days }} days</div>
                        </div>
                    @endif
                    
                    <div class="info-item">
                        <div class="info-label">Created</div>
                        <div class="info-value">{{ $fine->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ $fine->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
$(document).ready(function() {
    // Status update form
    $('#status-form').submit(function(e) {
        e.preventDefault();
        
        var newStatus = $('#new_status').val();
        
        if (confirm('Are you sure you want to update the status to "' + newStatus + '"?')) {
            $.ajax({
                url: "{{ route('fines.update-status', $fine->id) }}",
                method: 'POST',
                data: {
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Status updated successfully.');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating status. Please try again.');
                }
            });
        }
    });
});
</script>
@endsection

