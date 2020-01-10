<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'C4PE Career Guidance') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" ></script>
@stack('scripts')
<!-- Fonts -->
    <!--link rel="dns-prefetch" href="//fonts.gstatic.com"-->
    <!--link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"-->

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @yield('stylesheets')
        <style>

            #tablecell {
                display: table-cell;
                vertical-align: middle;
                float: none;
                height: 200px;
                width: 100%;
            }

        </style>

        </head>
<body >
<div id="sizer">
    <div class="d-block d-sm-none d-md-none d-lg-none d-xl-none" data-size="xs"></div>
    <div class="d-none d-sm-block d-md-none d-lg-none d-xl-none" data-size="sm"></div>
    <div class="d-none d-sm-none d-md-block d-lg-none d-xl-none" data-size="md"></div>
    <div class="d-none d-sm-none d-md-none d-lg-block d-xl-none" data-size="lg"></div>
    <div class="d-none d-sm-none d-md-none d-lg-none d-xl-block" data-size="xl"></div>
</div>

<div id="app">
    <div class="container">
        <div class="row">
            <div class="col-md-12" style="text-align:right">
                <a href="{{route('home.impressum')}}" style="color:black">{{ __('c4pe.form.navi.impress')}}</a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">

            <div class="row">
                <div class="col-md-3">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{asset("images/InterregLogo.png")}}" class="img-fluid">
                    </a>
                </div>
                <div class="col-md-9 align-middle" style="text-align:center;padding-top:10px">
                    <span id="headline" style="font-size:36px">{!! __('c4pe.form.navi.title')!!}</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
</div>

<script>
    function viewSize() {
        return $('#sizer').find('div:visible').data('size');
    }

    $( document ).ready(function() {
        if(viewSize() == "xs")
            $('#headline').text("");
        if(viewSize() == "md")
            $('#headline').text("Self-Assessment-Center");
    });
</script>

</body>
</html>
