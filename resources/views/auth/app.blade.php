<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;subset=vietnamese" rel="stylesheet">
    <link href="/css/client_login.css" rel="stylesheet">


    @yield('styles')
    <!-- Scripts -->

</head>
<body>
    <div id="app">
        <div class="container">
            <div class="notify">
                @include('notifications')
            </div>
        </div>
        <div class="container">
            @yield('content')
        </div>
    </div>
</body>
<script src="/js/app.js"></script>
<script type="text/javascript" charset="utf-8" >
    jQuery(document).ready(function($) {
        setTimeout(function(){
            $('.notify .alert-success, .notify .alert-danger, .booking-content .btn-flat').delay(3000).fadeOut(500);
        }, 6000);
    });
</script>
@yield('scripts')
</html>
