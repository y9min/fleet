<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">

    @yield('title')
    <link rel="icon" href="{{ asset('assets/images/' . Hyvikk::get('icon_img')) }}" type="icon_img">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />









    @yield('script')









