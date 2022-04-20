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
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:url"                content="{{ request()->fullUrl() }}" />
    <meta property="og:title"              content="BigFast" />
    <meta property="og:description"        content="BigFast Logistic" />
    <meta property="og:image"              content="{{ asset('assets/img/favicon.png') }}" />
    <!-- Icons-->
    {{--<link href="{{ asset('css/free.min.css') }}" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/free.min.css">
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css') . '?v='.config('app.version') }}" rel="stylesheet">
    {{--<link href="{{ asset('css/font-awesome.min.css') . '?v='.config('app.version') }}" rel="stylesheet">--}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    @yield('css')

    {{--<link href="{{ asset('libs/select2/select2.min.css') }}" rel="stylesheet" />--}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('libs/toaster/toast.style.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/loading/css/HoldOn.css') }}" rel="stylesheet">

    <!-- Jquery -->
    <script src="{{ asset('libs/jquery/jquery.min.js') }}"></script>

    @yield('script-header')

  </head>

  <body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">

      @include('layouts.shared.nav-builder')

      @include('layouts.shared.header')

      <div class="c-body">

        <main class="c-main">

          @yield('content')

        </main>
        @include('layouts.shared.footer')
      </div>
    </div>

    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('js/coreui-utils.js') }}"></script>

    {{--<script src="{{ asset('libs/select2/select2.min.js') }}"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('libs/jquery/jquery.cookie.js') }}"></script>
    <script src="{{ asset('libs/toaster/toast.script.js') }}"></script>
    <script src="{{ asset('libs/loading/js/HoldOn.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/tooltips.js') }}"></script>

    <script src="{{ asset('js/global.js') . '?v=' . config('app.version') }}" defer></script>
    <script type="application/javascript">
      $(document).ready(function() {
        @if(Session::has('message'))
          $.Toast("{{ Session::get('title') }}", "{!! Session::get('message') !!}", "{{ Session::get('type') }}", {timeout: 5000});
        @endif
      });
    </script>

    @yield('javascript')

  </body>
</html>
