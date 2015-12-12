<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>@yield('title')</title>
    <meta name="author" content="solutionportal">
    <meta name="keywords" content="appboard, webapp, admin, dashboard, template, ui">
    <meta name="description" content="Appboard - Admin Template with Angularjs">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{ asset('themes/admin/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/admin/css/main.css') }}">
    <script src="{{ asset('themes/admin/js/matchMedia.js') }}"></script>
</head>
<body class="app theme-one body-full">
<div class="main-container clearfix">
    <div class="content-container" id="content">@yield('content')</div>
    <footer id="site-foot" class="site-foot clearfix">
        <p class="left">&copy; Copyright 2015 <strong>iBenchu.net</strong>, All rights reserved.</p>
        <p class="right">v{{ app('version') }}</p>
    </footer>
</div>
<script src="{{ asset('themes/admin/js/jquery-2.1.3.min.js') }}"></script>
<script src="{{ asset('themes/admin/js/perfect-scrollbar.jquery.min.js') }}"></script>
<script src="{{ asset('themes/admin/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('themes/admin/js/app.js') }}"></script>
</body>
</html>