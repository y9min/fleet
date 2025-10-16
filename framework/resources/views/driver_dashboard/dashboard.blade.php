@extends('driver_dashboard.layouts.app')

@section('title')
    <title>Driver Dashboard | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/customer_dashboard/assets/main_css/dashboard.css') }}">
<style>
    /* PCO Flow Dashboard Enhancements */
    .card {
        border: none;
        box-shadow: 0 2px 10px rgba(3, 33, 39, 0.1);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 20px rgba(3, 33, 39, 0.15);
        transform: translateY(-2px);
    }
    
    /* Table styling */
    .table th {
        background-color: var(--pco-primary);
        color: var(--pco-text-light);
        border: none;
        font-weight: 600;
    }
    .table td {
        border-color: rgba(3, 33, 39, 0.1);
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background-color: rgba(127, 215, 225, 0.05);
    }
    
    /* Icon colors */
    .text-primary {
        color: var(--pco-primary) !important;
    }
    .text-warning {
        color: #ffc107 !important;
    }
    .text-info {
        color: var(--pco-secondary) !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    
    /* Welcome section enhancement */
    .driver-card {
        position: relative;
        overflow: hidden;
    }
    .driver-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }
    
    /* Stats card enhancement */
    .driver-stats {
        position: relative;
        overflow: hidden;
    }
    .driver-stats::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 80px;
        height: 80px;
        background: rgba(3, 33, 39, 0.1);
        border-radius: 50%;
        transform: translate(-20px, 20px);
    }
</style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item text-sm text-dark active"><a href="{{url('/driver-dashboard')}}" aria-current="page">Driver Dashboard</a></li>
@endsection

@section('content')

<div class="custom-alert-msg" style="color:white;"></div>

<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card driver-card shadow-sm">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center">
                    <div class="col-8 col-md-8">
                        <h4 class="text-white mb-2 fs-5 fs-md-4">Welcome back, {{ $user->name }}!</h4>
                        <p class="text-white-50 mb-1 small">Email: {{ $user->email }}</p>
                        <p class="text-white-50 mb-0 small">License: {{ $user->getMeta('license_number') ?? 'N/A' }}</p>
                    </div>
                    <div class="col-4 col-md-4 text-end">
                        <i class="fas fa-car fa-2x fa-md-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Prompt -->
@if($forcePasswordChange ?? false)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color:#dc3545;color:#ffffff;border-color:#dc3545;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3 text-white"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">Security Notice</h5>
                    <p class="mb-2">{{ $passwordChangeMessage ?? 'Please change your default password for security reasons.' }}</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#passwordChangeModal">
                            <i class="fas fa-key me-1"></i>
                            Change Password Now
                        </button>
                        <button type="button" class="btn btn-outline-light btn-sm" onclick="clearPasswordPrompt()">
                            <i class="fas fa-times me-1"></i>
                            Remind Me Later
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">

    <!-- Assigned Vehicle Card -->
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Assigned Vehicle</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-car text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                @if($assignedVehicle)
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-2">{{ $assignedVehicle->make_name ?? $assignedVehicle->make }} {{ $assignedVehicle->model_name ?? $assignedVehicle->model }}</h5>
                            <p class="mb-1"><strong>Plate:</strong> <span class="vehicle-plate">{{ $assignedVehicle->license_plate }}</span></p>
                            <p class="mb-1"><strong>Make:</strong> <span class="vehicle-make">{{ $assignedVehicle->make_name ?? $assignedVehicle->make }}</span></p>
                            <p class="mb-1"><strong>Model:</strong> <span class="vehicle-model">{{ $assignedVehicle->model_name ?? $assignedVehicle->model }}</span></p>
                            <p class="mb-1"><strong>Year:</strong> {{ $assignedVehicle->year }}</p>
                            <p class="mb-0"><strong>Status:</strong> 
                                @php
                                    $vehicleStatus = $assignedVehicle->getMeta('vehicle_status') ?: 'Available';
                                @endphp
                                @switch($vehicleStatus)
                                    @case('Available')
                                        <span class="badge bg-success vehicle-status-badge">Available</span>
                                        @break
                                    @case('Rented')
                                        <span class="badge bg-warning vehicle-status-badge">Rented</span>
                                        @break
                                    @case('Workshop')
                                        <span class="badge bg-info vehicle-status-badge">Workshop</span>
                                        @break
                                    @case('Disabled')
                                        <span class="badge bg-secondary vehicle-status-badge">Disabled</span>
                                        @break
                                    @default
                                        <span class="badge bg-success vehicle-status-badge">Available</span>
                                @endswitch
                            </p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-car fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No Vehicle Assigned</h6>
                        <p class="text-muted mb-0">Contact your administrator to get a vehicle assigned.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Controls -->
@if(config('app.show_upcoming_payments', false))
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-outline-primary btn-sm" id="toggle-payments-btn" onclick="toggleUpcomingPayments()">
                <i class="fas fa-credit-card me-1"></i>
                Show Upcoming Payments
            </button>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Upcoming Payments -->
    @if(config('app.show_upcoming_payments', false))
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-4" id="upcoming-payments-card" style="display: none;">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Upcoming Payments</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-credit-card text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                @if(count($upcomingPayments) > 0)
                    @foreach($upcomingPayments as $payment)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">{{ $payment['description'] }}</h6>
                                <p class="text-sm text-muted mb-0">Due: {{ $payment['due_date'] }}</p>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-warning">£{{ number_format($payment['amount'], 2) }}</h6>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr class="my-2">
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="text-muted">All payments up to date</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Service Reminders -->
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Service Reminders</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-tools text-info"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="service-reminders-container">
                    @if(count($serviceReminders) > 0)
                        @foreach($serviceReminders as $reminder)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $reminder['type'] }}</h6>
                                    <p class="text-sm text-muted mb-0">{{ $reminder['description'] }}</p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-info">{{ $reminder['due_date'] }}</span>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">No upcoming services</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tickets & Fines -->
    <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Tickets & Fines</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="tickets-container">
                    @if(count($tickets) > 0)
                        @foreach($tickets as $ticket)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $ticket['type'] }}</h6>
                                    <p class="text-sm text-muted mb-0">{{ $ticket['description'] }}</p>
                                    <p class="text-sm text-muted mb-0">{{ $ticket['date'] }}</p>
                                </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-danger">£{{ number_format($ticket['amount'], 2) }}</h6>
                                @php
                                    $statusClass = 'badge-secondary';
                                    switch(strtolower($ticket['status'])) {
                                        case 'paid':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'notified':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'disputed':
                                            $statusClass = 'badge-danger';
                                            break;
                                        case 'escalated':
                                            $statusClass = 'badge-dark';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $ticket['status'] }}
                                </span>
                                @if($ticket['status'] != 'Paid')
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-success mark-paid-btn" 
                                                data-fine-id="{{ $ticket['id'] }}" 
                                                data-fine-type="{{ $ticket['type'] }}"
                                                data-fine-amount="£{{ number_format($ticket['amount'], 2) }}">
                                            <i class="fas fa-check"></i> Mark as Paid
                                        </button>
                                    </div>
                                @endif
                            </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h6 class="text-muted">No outstanding fines</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-pco-primary text-white">
                <h5 class="modal-title" id="passwordChangeModalLabel">
                    <i class="fas fa-key me-2"></i>
                    Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordChangeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-pco-primary" onclick="changePassword()">
                    <i class="fas fa-save me-1"></i>
                    Update Password
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        // You can add AJAX call here to refresh data
        console.log('Dashboard data refresh...');
    }, 300000);

    // Clear password prompt
    function clearPasswordPrompt() {
        fetch('{{ route("driver.clear.password.prompt") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Change password
    function changePassword() {
        const form = document.getElementById('passwordChangeForm');
        const formData = new FormData(form);
        
        // Validate passwords match
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('new_password_confirmation');
        
        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }
        
        if (newPassword.length < 8) {
            alert('Password must be at least 8 characters long!');
            return;
        }

        fetch('{{ route("driver.change.password.profile") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'An error occurred while changing password.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while changing password.');
        });
    }

    // Auto-refresh vehicle status every 30 seconds
    function refreshVehicleStatus() {
        fetch('{{ route("driver.getinfo") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.assignedVehicle) {
                const vehicle = data.assignedVehicle;
                const vehicleStatus = vehicle.meta_data?.vehicle_status || 'Available';
                
                // Update vehicle status badge
                const statusElement = document.querySelector('.vehicle-status-badge');
                if (statusElement) {
                    let badgeClass = 'bg-success';
                    let statusText = 'Available';
                    
                    switch(vehicleStatus) {
                        case 'Available':
                            badgeClass = 'bg-success';
                            statusText = 'Available';
                            break;
                        case 'Rented':
                            badgeClass = 'bg-warning';
                            statusText = 'Rented';
                            break;
                        case 'Workshop':
                            badgeClass = 'bg-info';
                            statusText = 'Workshop';
                            break;
                        case 'Disabled':
                            badgeClass = 'bg-secondary';
                            statusText = 'Disabled';
                            break;
                    }
                    
                    statusElement.className = `badge ${badgeClass}`;
                    statusElement.textContent = statusText;
                }
                
                // Update vehicle plate
                const plateElement = document.querySelector('.vehicle-plate');
                if (plateElement && vehicle.license_plate) {
                    plateElement.textContent = vehicle.license_plate;
                }
                
                // Update vehicle make
                const makeElement = document.querySelector('.vehicle-make');
                if (makeElement) {
                    const make = vehicle.make_name || vehicle.make || '';
                    makeElement.textContent = make;
                }
                
                // Update vehicle model
                const modelElement = document.querySelector('.vehicle-model');
                if (modelElement) {
                    const model = vehicle.model_name || vehicle.model || '';
                    modelElement.textContent = model;
                }
                
                // Update service reminders if changed
                if (data.serviceReminders && data.serviceReminders.length > 0) {
                    const serviceRemindersContainer = document.querySelector('.service-reminders-container');
                    if (serviceRemindersContainer) {
                        let remindersHtml = '';
                        data.serviceReminders.forEach((reminder, index) => {
                            remindersHtml += `
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">${reminder.type}</h6>
                                        <p class="text-sm text-muted mb-0">${reminder.description}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info">${reminder.due_date}</span>
                                    </div>
                                </div>
                                ${index < data.serviceReminders.length - 1 ? '<hr class="my-2">' : ''}
                            `;
                        });
                        serviceRemindersContainer.innerHTML = remindersHtml;
                    }
                }
                
                // Update fines/tickets if changed
                if (data.tickets && data.tickets.length > 0) {
                    const ticketsContainer = document.querySelector('.tickets-container');
                    if (ticketsContainer) {
                        let ticketsHtml = '';
                        data.tickets.forEach((ticket, index) => {
                            let statusClass = 'bg-secondary';
                            switch(ticket.status.toLowerCase()) {
                                case 'paid':
                                    statusClass = 'badge-success';
                                    break;
                                case 'pending':
                                    statusClass = 'badge-warning';
                                    break;
                                case 'notified':
                                    statusClass = 'badge-info';
                                    break;
                                case 'disputed':
                                    statusClass = 'badge-danger';
                                    break;
                                case 'escalated':
                                    statusClass = 'badge-dark';
                                    break;
                                default:
                                    statusClass = 'badge-secondary';
                            }
                            const markPaidButton = ticket.status.toLowerCase() !== 'paid' ? 
                                `<div class="mt-2">
                                    <button class="btn btn-sm btn-success mark-paid-btn" 
                                            data-fine-id="${ticket.id}" 
                                            data-fine-type="${ticket.type}"
                                            data-fine-amount="£${parseFloat(ticket.amount).toFixed(2)}">
                                        <i class="fas fa-check"></i> Mark as Paid
                                    </button>
                                </div>` : '';
                            
                            ticketsHtml += `
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-1">${ticket.type}</h6>
                                        <p class="text-sm text-muted mb-0">${ticket.description}</p>
                                        <small class="text-muted">${ticket.date}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge ${statusClass}">${ticket.status}</span>
                                        </div>
                                        <div class="text-sm font-weight-bold">£${parseFloat(ticket.amount).toFixed(2)}</div>
                                        ${markPaidButton}
                                    </div>
                                </div>
                                ${index < data.tickets.length - 1 ? '<hr class="my-2">' : ''}
                            `;
                        });
                        ticketsContainer.innerHTML = ticketsHtml;
                    }
                } else {
                    // Show no fines message
                    const ticketsContainer = document.querySelector('.tickets-container');
                    if (ticketsContainer) {
                        ticketsContainer.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h6 class="text-muted">No outstanding fines</h6>
                            </div>
                        `;
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error refreshing vehicle status:', error);
        });
    }

    // Handle mark as paid button clicks
    function handleMarkAsPaid() {
        $(document).on('click', '.mark-paid-btn', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const fineId = button.data('fine-id');
            const fineType = button.data('fine-type');
            const fineAmount = button.data('fine-amount');
            
            // Confirm action
            if (!confirm(`Are you sure you want to mark this fine as paid?\n\nFine: ${fineType}\nAmount: ${fineAmount}`)) {
                return;
            }
            
            // Disable button and show loading
            button.prop('disabled', true);
            button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // Make AJAX request
            fetch('{{ route("driver.mark.fine.paid") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fine_id: fineId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    
                    // Refresh the page to show updated status
                    location.reload();
                } else {
                    // Show error message
                    alert(data.message || 'An error occurred while marking the fine as paid.');
                    
                    // Re-enable button
                    button.prop('disabled', false);
                    button.html('<i class="fas fa-check"></i> Mark as Paid');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking the fine as paid.');
                
                // Re-enable button
                button.prop('disabled', false);
                button.html('<i class="fas fa-check"></i> Mark as Paid');
            });
        });
    }

    // Toggle upcoming payments card visibility
    function toggleUpcomingPayments() {
        const card = document.getElementById('upcoming-payments-card');
        const button = document.getElementById('toggle-payments-btn');
        
        if (card.style.display === 'none') {
            card.style.display = 'block';
            button.innerHTML = '<i class="fas fa-eye-slash me-1"></i> Hide Upcoming Payments';
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-outline-secondary');
        } else {
            card.style.display = 'none';
            button.innerHTML = '<i class="fas fa-credit-card me-1"></i> Show Upcoming Payments';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-outline-primary');
        }
    }

    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize mark as paid functionality
        handleMarkAsPaid();
        
        // Refresh immediately
        refreshVehicleStatus();
        
        // Then refresh every 30 seconds
        setInterval(refreshVehicleStatus, 30000);
    });
</script>
@endsection
