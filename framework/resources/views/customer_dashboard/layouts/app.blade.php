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

    <!-- Customer Dashboard CSS -->
    <link id="pagestyle" href="{{ asset('assets/customer_dashboard/assets/css/soft-ui-dashboard.css?v=345435') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/css/style.css?v=1.1234568') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/main_css/app.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/customer_dashboard/assets/main_css/app1.css') }}" rel="stylesheet" />

    <style>
        .custom-alerts {
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

    @if(Auth::check() && (request()->is('dashboard') || request()->is('dashboard/*')))
        <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
            @include('customer_dashboard.includes.sidebar')
        </aside>
        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
            <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
                @include('customer_dashboard.includes.header')
            </nav>
            <div class="container-fluid py-4">
                @yield('content')
                @include('customer_dashboard.includes.footer')
            </div>
        </main>
    @elseif(request()->is('login') || request()->is('forgot-password') || request()->is('log_in'))
        <div class="container position-sticky z-index-sticky top-0 p-0">
            <div class="row">
                <div class="col-12">
                    @include('customer_dashboard.includes.header')
                </div>
            </div>
        </div>
        <main class="main-content mt-0">
            @yield('content')
            @include('customer_dashboard.includes.footer')
        </main>
    @else
        <div class="container position-sticky z-index-sticky top-0 p-0">
            <div class="row">
                <div class="col-12">
                    @include('customer_dashboard.includes.header')
                </div>
            </div>
        </div>
        <main class="main-content mt-0">
            @yield('content')
            @include('customer_dashboard.includes.footer')
        </main>
    @endif

    <!-- Core JS Files -->
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