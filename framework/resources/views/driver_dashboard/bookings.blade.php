@extends('driver_dashboard.layouts.app')

@section('title')
    <title>My Bookings | {{ Hyvikk::get('app_name') }}</title>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item text-sm text-dark active"><a href="{{url('/driver-bookings')}}" aria-current="page">My Bookings</a></li>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0 p-3">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <h6 class="mb-0">My Bookings</h6>
                    </div>
                    <div class="col-6 text-end">
                        <i class="fas fa-calendar text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                @if($bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Pickup Location</th>
                                    <th>Drop Location</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>#{{ $booking->id }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $booking->created_at->format('M d, Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $booking->created_at->format('h:i A') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($booking->customer)
                                                <div>
                                                    <strong>{{ $booking->customer->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $booking->customer->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $booking->pickup_addr ?? 'N/A' }}</strong>
                                                @if($booking->pickup_date)
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($booking->pickup_date)->format('M d, Y h:i A') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $booking->dest_addr ?? 'N/A' }}</strong>
                                                @if($booking->drop_date)
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($booking->drop_date)->format('M d, Y h:i A') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'secondary';
                                                switch($booking->status) {
                                                    case 'completed':
                                                        $statusClass = 'success';
                                                        break;
                                                    case 'ongoing':
                                                        $statusClass = 'warning';
                                                        break;
                                                    case 'cancelled':
                                                        $statusClass = 'danger';
                                                        break;
                                                    case 'confirmed':
                                                        $statusClass = 'info';
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>£{{ number_format($booking->total_amount ?? 0, 2) }}</strong>
                                            @if($booking->payment_status)
                                                <br>
                                                <small class="text-muted">
                                                    Payment: 
                                                    <span class="badge bg-{{ $booking->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($booking->payment_status) }}
                                                    </span>
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ url('/driver-booking-details/' . $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                @if($booking->status == 'ongoing')
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="completeBooking({{ $booking->id }})">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($bookings->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">No bookings found</h5>
                        <p class="text-muted">You don't have any bookings yet. Bookings will appear here once they are assigned to you.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Booking Statistics -->
<div class="row mt-4">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card driver-stats shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-calendar-check fa-2x text-white mb-3"></i>
                <h4 class="text-white">{{ $bookings->where('status', 'completed')->count() }}</h4>
                <p class="text-white-50 mb-0">Completed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card driver-card shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-white mb-3"></i>
                <h4 class="text-white">{{ $bookings->where('status', 'ongoing')->count() }}</h4>
                <p class="text-white-50 mb-0">Ongoing</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                <h4 class="text-success">{{ $bookings->where('status', 'confirmed')->count() }}</h4>
                <p class="text-muted mb-0">Confirmed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-pound-sign fa-2x text-primary mb-3"></i>
                <h4 class="text-primary">£{{ number_format($bookings->where('status', 'completed')->sum('total_amount'), 2) }}</h4>
                <p class="text-muted mb-0">Total Earnings</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function completeBooking(bookingId) {
        if (confirm('Are you sure you want to mark this booking as completed?')) {
            // You can implement AJAX call here to complete the booking
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
</script>
@endsection
