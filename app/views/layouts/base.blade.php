<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ $title }} &middot; eTrack</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('assets/css/application.css') }}">
</head>
<body>
<header id="global-header" role="banner">
    <div class="header-wrapper">
        <div class="header-logo">
            <a href="#" title="Go to the eTrack homepage" id="logo">
                <img src="{{ asset('assets/images/logo.png') }}" alt="eTrack logo" height="33">
                eTrack
            </a>
        </div>

        <nav class="main">
            <a href="#" class="toggle-nav">Menu</a>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Courses</a></li>
                <li><a href="#">Units</a></li>
                <li><a href="#">Users</a></li>
                <li><a href="#">Maintenance</a></li>
            </ul>
        </nav>

        <nav class="minor">
            <ul>
                <li class="text">Hello Jake Moreman</li>
<!--                <li><a href="#">Your details</a></li>-->
                <li><a href="#">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div id="wrapper">
    <section id="content" role="main">
        @yield('main')
    </section>

    <div id="layout_footer"></div>
</div>

<footer id="footer">
    <div class="wrapper">
        <p>&copy; Copyright 2014 City College Plymouth</p>

        <p>Version {{ Config::get('app.version') }}</p>
    </div>
</footer>
</body>
</html>