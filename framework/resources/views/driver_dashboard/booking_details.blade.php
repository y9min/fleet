@extends('driver_dashboard.layouts.app')

@section('title')
    <title>Booking Details | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item text-sm"><a href="{{url('/driver-bookings')}}" class="opacity-5 text-dark">My Bookings</a></li>
    <li class="breadcrumb-item text-sm text-dark active">Booking Details</li>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">Booking Details - #{{ $booking->id }}</h6>
                    </div>
                    <div class="col-6 text-end">
                        <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'ongoing' ? 'warning' : 'secondary') }} fs-6">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <!-- Booking Information -->
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Booking Information
                        </h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><strong>Booking ID:</strong></p>
                                <p class="mb-2"><strong>Date Created:</strong></p>
                                <p class="mb-2"><strong>Status:</strong></p>
                                <p class="mb-2"><strong>Payment Status:</strong></p>
                            </div>
                            <div class="col-6">
                                <p class="mb-2">#{{ $booking->id }}</p>
                                <p class="mb-2">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                                <p class="mb-2">
                                    <span class="badge bg-{{ $booking->status == 'completed' ? 'success' : ($booking->status == 'ongoing' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </p>
                                <p class="mb-2">
                                    <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($booking->payment_status ?? 'Pending') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h6>
                        @if($booking->customer)
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2"><strong>Name:</strong></p>
                                    <p class="mb-2"><strong>Email:</strong></p>
                                    <p class="mb-2"><strong>Phone:</strong></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">{{ $booking->customer->name }}</p>
                                    <p class="mb-2">{{ $booking->customer->email }}</p>
                                    <p class="mb-2">{{ $booking->customer->getMeta('mobno') ?? 'N/A' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Customer information not available</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <div class="row">
                    <!-- Trip Details -->
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-route me-2"></i>Trip Details
                        </h6>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <p class="mb-1"><strong>Pickup Location:</strong></p>
                                <p class="mb-2">{{ $booking->pickup_addr ?? 'N/A' }}</p>
                                @if($booking->pickup_date)
                                    <small class="text-muted">Scheduled: {{ \Carbon\Carbon::parse($booking->pickup_date)->format('M d, Y h:i A') }}</small>
                                @endif
                            </div>
                            <div class="col-12 mb-3">
                                <p class="mb-1"><strong>Drop Location:</strong></p>
                                <p class="mb-2">{{ $booking->dest_addr ?? 'N/A' }}</p>
                                @if($booking->drop_date)
                                    <small class="text-muted">Scheduled: {{ \Carbon\Carbon::parse($booking->drop_date)->format('M d, Y h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="col-md-6 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-car me-2"></i>Vehicle Information
                        </h6>
                        @if($booking->vehicle)
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-2"><strong>Make/Model:</strong></p>
                                    <p class="mb-2"><strong>License Plate:</strong></p>
                                    <p class="mb-2"><strong>Year:</strong></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-2">{{ $booking->vehicle->make }} {{ $booking->vehicle->model }}</p>
                                    <p class="mb-2">{{ $booking->vehicle->license_plate }}</p>
                                    <p class="mb-2">{{ $booking->vehicle->year }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Vehicle information not available</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <!-- Pricing Information -->
                <div class="row">
                    <div class="col-md-8 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-pound-sign me-2"></i>Pricing Information
                        </h6>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2"><strong>Base Fare:</strong></p>
                                <p class="mb-2"><strong>Distance Fare:</strong></p>
                                <p class="mb-2"><strong>Time Fare:</strong></p>
                                <p class="mb-2"><strong>Additional Charges:</strong></p>
                                <hr>
                                <p class="mb-0"><strong>Total Amount:</strong></p>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-2">£{{ number_format($booking->base_fare ?? 0, 2) }}</p>
                                <p class="mb-2">£{{ number_format($booking->distance_fare ?? 0, 2) }}</p>
                                <p class="mb-2">£{{ number_format($booking->time_fare ?? 0, 2) }}</p>
                                <p class="mb-2">£{{ number_format($booking->additional_charges ?? 0, 2) }}</p>
                                <hr>
                                <p class="mb-0"><strong>£{{ number_format($booking->total_amount ?? 0, 2) }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-md-4 mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-cogs me-2"></i>Actions
                        </h6>
                        <div class="d-grid gap-2">
                            @if($booking->status == 'ongoing')
                                <button type="button" class="btn btn-success" onclick="completeBooking({{ $booking->id }})">
                                    <i class="fas fa-check me-2"></i>Complete Booking
                                </button>
                            @endif
                            
                            @if($booking->status == 'confirmed')
                                <button type="button" class="btn btn-warning" onclick="startBooking({{ $booking->id }})">
                                    <i class="fas fa-play me-2"></i>Start Trip
                                </button>
                            @endif

                            <a href="{{ url('/driver-bookings') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($booking->note || $booking->special_instructions)
                    <hr class="my-4">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Information
                            </h6>
                            @if($booking->note)
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Notes:</strong></p>
                                    <p class="mb-0">{{ $booking->note }}</p>
                                </div>
                            @endif
                            @if($booking->special_instructions)
                                <div class="mb-3">
                                    <p class="mb-1"><strong>Special Instructions:</strong></p>
                                    <p class="mb-0">{{ $booking->special_instructions }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function completeBooking(bookingId) {
        if (confirm('Are you sure you want to mark this booking as completed?')) {
            fetch(`/driver-booking-complete/${bookingId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error completing booking: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while completing the booking.');
            });
        }
    }

    function startBooking(bookingId) {
        if (confirm('Are you sure you want to start this trip?')) {
            fetch(`/driver-booking-start/${bookingId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error starting booking: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while starting the booking.');
            });
        }
    }
</script>
@endsection
