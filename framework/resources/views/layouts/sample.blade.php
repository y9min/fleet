<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>@yield('title', 'Vehicle Import Sample Template')</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-bootstrap.min.css') }}" />
    <link href="{{ asset('assets/css/cdn-ionicons.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/cdn-font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-jquery-ui.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/cdn-dataTables.bootstrap.min.css') }}">
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/AdminLTE.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/skins-all-skins.min.css') }}" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
        }
        .sample-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body class="hold-transition skin-black-light sidebar-mini">
    <div class="wrapper">
        <section class="content">
            <div class="sample-container">
                @yield('content')
            </div>
        </section>
    </div>
    
    <script src="{{ asset('assets/js/cdn-jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/cdn-bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/cdn-jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/cdn-dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/cdn-dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/AdminLTE.min.js') }}"></script>
</body>
</html>

