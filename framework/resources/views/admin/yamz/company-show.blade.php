@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building"></i> {{ $company->name }} — Details
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.yamz.companies') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="border p-3 rounded">
                            <div class="text-muted">Vehicles</div>
                            <div class="h4 mb-0">{{ $vehiclesCount }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded">
                            <div class="text-muted">Super Admins</div>
                            <div class="h4 mb-0">{{ $supers->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded">
                            <div class="text-muted">Office Admins</div>
                            <div class="h4 mb-0">{{ $offices->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border p-3 rounded">
                            <div class="text-muted">Drivers</div>
                            <div class="h4 mb-0">{{ $drivers->count() }}</div>
                        </div>
                    </div>
                </div>

                @if($supers->count() > 0)
                    <div class="alert alert-info mb-3 payment-setup-alert" style="position: relative;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-credit-card"></i> Payment Setup</strong>
                                <p class="mb-0">Send payment setup email to the company super admin.</p>
                                @if($company->subscription_status)
                                    <small>Subscription Status: <strong>{{ ucfirst($company->subscription_status) }}</strong></small>
                                @elseif($company->stripe_customer_id)
                                    <small class="text-muted">Stripe customer created. Subscription will be created when first vehicle is added.</small>
                                @else
                                    <small class="text-muted">Stripe customer will be created when you send the email.</small>
                                @endif
                            </div>
                            <form action="{{ route('admin.yamz.companies.send-payment-email', $company->id) }}" method="POST" style="display: inline;" id="payment-email-form">
                                @csrf
                                <button type="submit" class="btn btn-primary" id="send-payment-email-btn">
                                    <i class="fas fa-envelope"></i> Send Payment Setup Email
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <h5 class="mt-4">Super Admins</h5>
                <ul class="list-group mb-3">
                    @forelse($supers as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No super admins</li>
                    @endforelse
                </ul>

                <h5>Office Admins</h5>
                <ul class="list-group mb-3">
                    @forelse($offices as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No office admins</li>
                    @endforelse
                </ul>

                <h5>Drivers</h5>
                <ul class="list-group mb-3">
                    @forelse($drivers as $u)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $u->name }} <span class="text-muted">{{ $u->email }}</span>
                        </li>
                    @empty
                        <li class="list-group-item">No drivers</li>
                    @endforelse
                </ul>

                <h5>Recent Vehicles</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Plate</th>
                                <th>In Service</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $v)
                                <tr>
                                    <td>{{ $v->id }}</td>
                                    <td>{{ $v->make_name }}</td>
                                    <td>{{ $v->model_name }}</td>
                                    <td>{{ $v->license_plate }}</td>
                                    <td>{{ $v->in_service ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No vehicles found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
$(document).ready(function() {
    // Prevent payment setup alert from auto-hiding
    // Clear any existing fadeOut animations and ensure it stays visible
    var preventAutoHide = function() {
        $('.payment-setup-alert')
            .stop(true, true)
            .show()
            .css('opacity', '1')
            .css('display', 'block');
    };
    
    preventAutoHide();
    
    // Re-apply after a short delay to override admin-custom.js
    setTimeout(preventAutoHide, 100);
    setTimeout(preventAutoHide, 500);
    setTimeout(preventAutoHide, 1000);
    setTimeout(preventAutoHide, 6000); // After auto-hide would have fired
    
    // Monitor and prevent any fadeOut on payment setup alert
    var originalFadeOut = $.fn.fadeOut;
    $.fn.fadeOut = function(speed, callback) {
        if (this.hasClass('payment-setup-alert')) {
            return this; // Don't fade out payment setup alerts
        }
        return originalFadeOut.apply(this, arguments);
    };

    // Toast notification function
    function showToast(message, type = 'success') {
        const iconMap = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };

        const bgColorMap = {
            'success': '#28a745',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#17a2b8'
        };

        const icon = iconMap[type] || iconMap['success'];
        const bgColor = bgColorMap[type] || bgColorMap['success'];

        // Remove existing toasts
        $('.payment-toast-notification').remove();

        const toast = $(`
            <div class="payment-toast-notification" style="
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${bgColor};
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                z-index: 10000;
                min-width: 320px;
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
            ">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="${icon}" style="font-size: 20px;"></i>
                    <div style="flex: 1;">
                        <strong style="display: block; margin-bottom: 4px;">${type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Notice!'}</strong>
                        <div style="font-size: 14px;">${message}</div>
                    </div>
                    <button type="button" onclick="$(this).closest('.payment-toast-notification').fadeOut(300, function(){ $(this).remove(); });" style="
                        background: transparent;
                        border: none;
                        color: white;
                        font-size: 20px;
                        cursor: pointer;
                        padding: 0;
                        width: 24px;
                        height: 24px;
                        line-height: 20px;
                    ">&times;</button>
                </div>
            </div>
        `);

        $('body').append(toast);

        // Auto remove after 5 seconds
        setTimeout(function() {
            toast.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Check for session messages and show toast
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif

    // Add animation CSS
    if (!$('#payment-toast-styles').length) {
        $('<style id="payment-toast-styles">')
            .html(`
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `)
            .appendTo('head');
    }
});
</script>
@endsection
