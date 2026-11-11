<!-- resources/views/frontend/layouts/minimal.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Secure Checkout</title>

    <!-- Favicon (Optional) -->
    <link rel="icon" href="{{ asset('frontend/assets/img/favicon.ico') }}" type="image/x-icon">

    <!-- Prevent unwanted behavior -->
    <meta name="theme-color" content="#0056b3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Disable cache to prevent sensitive data retention -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Optional: Basic font/style for consistency -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            overflow-x: hidden;
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#fff;">
    @yield('content')

    @stack('script')
</body>
</html>