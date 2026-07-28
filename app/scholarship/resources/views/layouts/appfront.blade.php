@extends('adminlte::master')

@section('adminlte_css')
    <link rel="stylesheet"
          href="{{ asset('vendor/adminlte/dist/css/skins/skin-' .'green.min.css')}} ">
    @stack('css')
    @yield('css')
@stop

@section('body_class', 'skin-green layout-top-nav sidebar-collapse')

@section('body')
    <div class="wrapper">

        <!-- Main Header -->
        <header class="main-header">
            <nav class="navbar navbar-static-top" style="border-bottom: solid 2px #545852;">
                <div class="container">
                    <div class="navbar-header">
                        <a href="{{ url(config('adminlte.dashboard_url', 'home')) }}" class="navbar-brand">
                            <img src="{{ asset('images/peo_logo.png') }}" alt="" class="img-responsive" style="padding-bottom:25px;">
                        </a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">

                        <ul class="nav navbar-nav">
                            <li>
                                <a href="{{ url(config('adminlte.login_url', 'auth/login')) }}">
                                    <i class="fa fa-fw fa-power-off"></i> Login
                                </a>
                             </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @if(config('adminlte.layout') == 'top-nav')
            <div class="container">
            @endif

            <!-- Content Header (Page header) -->
            <div class="content-wrapper">
                    <section class="content-header">
                          @yield('content_header')
                    </section>

                    <!-- Main content -->
                    <section class="content" >
                        <div class="content-wrapper">

                        @yield('content')
                        </div>
                    </section>
            <!-- /.content -->
             </div>
        <!-- /.content-wrapper -->
        @if(config('adminlte.layout') == 'top-nav')
        </div>
        <!-- /.container -->
        @endif
    </div>
    <!-- ./wrapper -->
@stop

@section('adminlte_js')
<footer class="main-footer" style="text-align:center">
    <strong>Copyright © People Fondation <span>2018</span> .</strong> All rights
    reserved.
</footer>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
@stop


