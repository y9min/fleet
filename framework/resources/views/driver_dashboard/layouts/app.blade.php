<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/customer_dashboard/assets/img/apple-icon.png') }}">

    @yield('title')
    <link rel="icon" href="{{ asset('assets/customer_dashboard/assets/img/favicon.png') }}" type="image/png">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <!-- Driver Dashboard CSS -->
    <link id="pagestyle" href="{{ asset('assets/customer_dashboard/assets/css/soft-ui-dashboard.css?v=345435') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/css/style.css?v=1.1234568') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/main_css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/main_css/app1.css') }}" rel="stylesheet" />

    <style>
        .custom-alerts {
            margin-bottom: 20px;
        }
        
        /* PCO Flow Brand Colors */
        :root {
            --pco-primary: #032127;
            --pco-secondary: #7FD7E1;
            --pco-button: #6B7280;
            --pco-text-light: #ffffff;
            --pco-text-dark: #032127;
        }
        
        /* Full Width Header */
        .pco-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 117px;
            background-color: var(--pco-primary);
            z-index: 1030;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(3, 33, 39, 0.3);
        }
        
        .pco-header .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .pco-header .header-logo {
            height: 40px;
            width: auto;
            margin-bottom: 5px;
        }
        
        .pco-header .page-title {
            color: var(--pco-text-light);
            font-size: 28px;
            font-weight: 700;
            font-family: sans-serif;
            margin: 0;
        }
        
        .pco-header .header-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .pco-header .header-btn {
            background: var(--pco-secondary);
            color: var(--pco-text-dark);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .pco-header .header-btn:hover {
            background: #5bc5d1;
            color: var(--pco-text-dark);
            text-decoration: none;
        }
        
        .pco-header .header-btn.logout {
            background: #dc3545;
            color: white;
        }
        
        .pco-header .header-btn.logout:hover {
            background: #c82333;
            color: white;
        }
        
        .driver-theme {
            --primary-color: var(--pco-primary);
            --secondary-color: var(--pco-secondary);
            --accent-color: var(--pco-button);
        }
        
        /* Welcome Card with PCO Flow branding */
        .driver-card {
            background: linear-gradient(135deg, var(--pco-primary) 0%, #054a52 100%);
            color: var(--pco-text-light);
            border: none;
            box-shadow: 0 4px 20px rgba(3, 33, 39, 0.3);
        }
        .driver-card .card-body {
            color: var(--pco-text-light);
        }
        .driver-card h4, .driver-card h5, .driver-card h6 {
            color: var(--pco-text-light);
        }
        .driver-card .text-white-50 {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        /* Stats Card with PCO Flow secondary color */
        .driver-stats {
            background: linear-gradient(135deg, var(--pco-secondary) 0%, #5bc5d1 100%);
            color: var(--pco-text-dark);
            border: none;
            box-shadow: 0 4px 20px rgba(127, 215, 225, 0.3);
        }
        .driver-stats .card-body {
            color: var(--pco-text-dark);
        }
        .driver-stats h4, .driver-stats h5, .driver-stats h6 {
            color: var(--pco-text-dark);
        }
        .driver-stats .text-white {
            color: var(--pco-text-dark) !important;
        }
        .driver-stats .text-white-50 {
            color: rgba(3, 33, 39, 0.7) !important;
        }
        
        /* PCO Flow Button Styling */
        .btn-pco {
            background-color: var(--pco-button);
            border-color: var(--pco-button);
            color: var(--pco-text-light);
        }
        .btn-pco:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: var(--pco-text-light);
        }
        .btn-outline-pco {
            color: var(--pco-button);
            border-color: var(--pco-button);
        }
        .btn-outline-pco:hover {
            background-color: var(--pco-button);
            border-color: var(--pco-button);
            color: var(--pco-text-light);
        }
        
        /* Card Headers with PCO Flow styling */
        .card-header {
            background-color: var(--pco-primary);
            color: var(--pco-text-light);
            border-bottom: 2px solid var(--pco-secondary);
            display: flex !important;
            align-items: center !important;
            min-height: 60px !important;
            padding-bottom: calc(0.75rem + 7px) !important;
        }
        .card-header .row {
            width: 100% !important;
            margin: 0 !important;
            align-items: center !important;
        }
        .card-header h6 {
            color: var(--pco-text-light);
            margin: 0 !important;
            font-size: 1.2em !important;
        }
        .card-header i {
            color: var(--pco-secondary);
        }
        
        /* Badge styling */
        .badge.bg-success {
            background-color: #28a745 !important;
        }
        .badge.bg-danger {
            background-color: #dc3545 !important;
        }
        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: var(--pco-text-dark);
        }
        .badge.bg-info {
            background-color: var(--pco-secondary) !important;
            color: var(--pco-text-dark);
        }
        .badge.bg-secondary {
            background-color: var(--pco-button) !important;
        }
        
        /* Admin portal badge colors */
        .badge.badge-success {
            background-color: #28a745 !important;
            color: #fff !important;
        }
        .badge.badge-danger {
            background-color: #dc3545 !important;
            color: #fff !important;
        }
        .badge.badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
        .badge.badge-info {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }
        .badge.badge-dark {
            background-color: #424242 !important;
            color: #fff !important;
        }
        .badge.badge-secondary {
            background-color: #6c757d !important;
            color: #fff !important;
        }
        
        /* Uniform button sizing */
        .mark-paid-btn {
            min-width: 120px !important;
            height: 32px !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        
        /* Main content always full width */
        .main-content {
            margin-left: 0 !important;
            padding-top: 140px !important; /* Extra space for fixed header */
        }
        
        /* Ensure container has proper spacing */
        .main-content .container-fluid {
            padding-top: 0 !important;
        }
        
        /* Ensure first content element has proper spacing */
        .main-content .container-fluid > *:first-child {
            margin-top: 0 !important;
        }
        
        /* Ensure cards have proper spacing from header */
        .main-content .card:first-child {
            margin-top: 0 !important;
        }
        
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .main-content {
                padding-left: 20px;
                padding-top: 130px !important; /* Slightly less padding on mobile */
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .fs-5 {
                font-size: 1.1rem !important;
            }
            
            .fs-md-4 {
                font-size: 1.5rem !important;
            }
            
            .fa-2x {
                font-size: 1.5em !important;
            }
            
            .fa-md-3x {
                font-size: 2em !important;
            }
        }
        
        @media (max-width: 576px) {
            .card-header h6 {
                font-size: 0.9rem;
            }
            
            .card-body h4, .card-body h5 {
                font-size: 1.1rem;
            }
            
            .badge {
                font-size: 0.7rem;
            }
            
            .table th, .table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

    @if(Auth::check() && (request()->is('driver-dashboard') || request()->is('driver-dashboard/*') || request()->is('driver-*')))
        <!-- PCO Flow Header -->
        <header class="pco-header">
            <div class="header-content">
                <div>
                    <h1 class="page-title">
                        @if(Route::currentRouteName() == "driver.dashboard")
                            Dashboard
                        @elseif(Route::currentRouteName() == "driver.bookings")
                            My Bookings
                        @elseif(Route::currentRouteName() == "driver.profile")
                            Driver Profile
                        @elseif(Route::currentRouteName() == "driver.change.password")
                            Change Password
                        @elseif(Route::currentRouteName() == "driver.booking.details")
                            Booking Details
                        @else
                            Driver Portal
                        @endif
                    </h1>
                </div>
            </div>
            <div class="header-buttons">
                @if(Route::currentRouteName() == "driver.profile")
                    <a href="{{ url('/driver-dashboard') }}" class="header-btn" style="background: #6c757d; color: white; border: none;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                        </svg>
                        Back
                    </a>
                @endif
                <a href="{{ url('/driver-profile') }}" class="header-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    Profile
                </a>
                <a href="#" class="header-btn logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Logout
                </a>
            </div>
        </header>
        
        <form id="logout-form" action="{{ route('unified.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
            <div class="container-fluid" style="padding-top: 20px; {{ request()->is('driver-profile') ? 'padding-bottom: 0px;' : 'padding-bottom: 20px;' }}">
                @yield('content')
                @if(!request()->is('driver-profile'))
                    @include('driver_dashboard.includes.footer')
                @endif
            </div>
        </main>
    @elseif(request()->is('login') || request()->is('forgot-password') || request()->is('log_in'))
        <div class="container position-sticky z-index-sticky top-0 p-0">
            <div class="row">
                <div class="col-12">
                    @include('driver_dashboard.includes.header')
                </div>
            </div>
        </div>
        <main class="main-content mt-0">
            @yield('content')
            @include('driver_dashboard.includes.footer')
        </main>
    @else
        <div class="container position-sticky z-index-sticky top-0 p-0">
            <div class="row">
                <div class="col-12">
                    @include('driver_dashboard.includes.header')
                </div>
            </div>
        </div>
        <main class="main-content mt-0">
            @yield('content')
            @include('driver_dashboard.includes.footer')
        </main>
    @endif

    <!-- Core JS Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        // Fallback to local jQuery if CDN fails
        if (!window.jQuery) {
            console.warn('jQuery CDN failed, loading local fallback...');
            document.write('<script src="{{ asset("assets/customer_dashboard/assets/js/core/jquery.min.js") }}"><\/script>');
        }
        // Ensure $ is available globally
        window.$ = window.jQuery;
    </script>
    <script src="{{ asset('assets/customer_dashboard/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/customer_dashboard/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/customer_dashboard/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/customer_dashboard/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/customer_dashboard/assets/js/plugins/chartjs.min.js') }}"></script>

    <!-- Alert auto-hide functionality (vanilla JavaScript) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success alerts
            const successAlert = document.getElementById('successAlert');
            if (successAlert) {
                setTimeout(function() {
                    successAlert.style.transition = 'opacity 0.5s ease-in-out';
                    successAlert.style.opacity = '0';
                    setTimeout(function() {
                        if (successAlert.parentNode) {
                            successAlert.parentNode.removeChild(successAlert);
                        }
                    }, 500);
                }, 5000);
            }

            // Auto-hide error alerts
            const errorAlert = document.getElementById('errorAlert');
            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.style.transition = 'opacity 0.5s ease-in-out';
                    errorAlert.style.opacity = '0';
                    setTimeout(function() {
                        if (errorAlert.parentNode) {
                            errorAlert.parentNode.removeChild(errorAlert);
                        }
                    }, 500);
                }, 5000);
            }
        });
    </script>

    @yield('script')

    <!-- Global URL Variables -->
    <script>
        var Home = "{{ url('/') }}";
        var login = "{{ url('user-login') }}";
        var register = "{{ url('user-register') }}";
        var forgot_password = "{{ url('forgot-password') }}";
        var reset_password_email = "{{ url('reset-password-email') }}";
        var reset_password = "{{ url('reset-password') }}";
        var booking_alert = "{{ url('save-booking-alert') }}";
        var driver_dashboard = "{{ url('driver-dashboard') }}";
    </script>

    <!-- Utility Functions -->
    <script>
        function mouseover() {
            const img1 = document.getElementById("img1");
            const img2 = document.getElementById("img2");
            if (img1) img1.style.display = "none";
            if (img2) img2.style.display = "block";
        }

        function mouseout() {
            const img1 = document.getElementById("img1");
            const img2 = document.getElementById("img2");
            if (img1) img1.style.display = "block";
            if (img2) img2.style.display = "none";
        }
        
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            if (typeof Scrollbar !== 'undefined') {
                Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
            }
        }
        
    </script>

</body>
</html>
