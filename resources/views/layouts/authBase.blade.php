<!DOCTYPE html>
<html lang="en">
  <head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="BigFast Logistic">
    <meta name="author" content="PAL Việt Nam">
    <meta name="keyword" content="BigFast Logistic,Logistic">
    <title>BigFast Logistic</title>
    <link rel="apple-touch-icon" href="assets/favicon/favicon.png">
    <link rel="icon" type="image/png" href="assets/favicon/favicon.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/favicon.png">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:url"                content="{{ request()->fullUrl() }}" />
    <meta property="og:title"              content="BigFast" />
    <meta property="og:description"        content="BigFast Logistic" />
    <meta property="og:image"              content="{{ asset('assets/img/favicon.png') }}" />
    {{--<link href="{{ asset('css/free.min.css') }}" rel="stylesheet"> <!-- icons -->--}}
    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/free.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css') . '?v='.config('app.version') }}" rel="stylesheet">

    <!-- Jquery -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>

    @yield('script-header')

  </head>
  <body class="c-app flex-row align-items-center">

    @yield('content')

    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>

    @yield('javascript')
  </body>
</html>
