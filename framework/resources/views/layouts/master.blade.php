<!DOCTYPE html>
<html>
    <!--
    @copyright

  PCO Flow v7.1.2

  Copyright (C) 2017-2025 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
  Design and developed by Hyvikk Solutions <https://hyvikk.com/>  -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ trans('installer_messages.title') }}</title>
    <link rel="icon" href="{{ asset('assets/images/pco-flow-favicon.png') }}"  type="icon_img" sizes="32x32">
    <link rel="icon"  href="{{ asset('assets/images/pco-flow-favicon.png') }}" type="icon_img" sizes="32x32">
    <link rel="icon"  href="{{ asset('assets/images/pco-flow-favicon.png') }}" type="icon_img" sizes="32x32">


<meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('assets/css/installer-style.min.css') }}" rel="stylesheet"/>
    @yield('style')

</head>
<body>
<div class="master">
    <div class="box" style="width: 50% !important;">
        <div class="header">
            <img src="{{ asset('/assets/images/pco-flow-logo.png') }}" style="height: 120px; width: auto; object-fit: contain;" alt="PCO Flow">
            <h1 class="header__title">@yield('title')</h1>
        </div>

        <div class="main">
            @yield('container')
        </div>
    </div>
</div>
</body>
@yield('scripts')
</html>
