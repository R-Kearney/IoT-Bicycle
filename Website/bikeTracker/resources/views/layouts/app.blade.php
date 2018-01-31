<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IoT Bicycle</title>

    <!-- Styles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/simple-line-icons/css/simple-line-icons.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
    <!-- Plugin CSS -->
    <link href="{{ asset('css/device-mockups.min.css') }}" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/new-age.min.css') }}" rel="stylesheet">

</head>
<body id="page-top">
    <div id="app">

     <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="{{ route('welcome') }}">IoT Bicycle</a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        Menu
        <i class="fa fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">

         <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('welcome') }}">Home</a></li>
         <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('welcome') }}">How it works</a></li>

         <!-- Authentication Links -->
         @guest
             <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('login') }}">Login</a></li>
             <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('register') }}">Register</a></li>
         @else
             <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('home') }}">Track My Bike</a></li>
             <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('timeline') }}">My Timeline</a></li>
			 <li class="nav-item"><a class="nav-link js-scroll-trigger" href="{{ route('dynamics') }}">Cycling Dynamics</a></li>
             <li class="nav-item">
               <a class="nav-link js-scroll-trigger" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                   Logout</a>
                   <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                       {{ csrf_field() }}
                   </form>
             </li>
         @endguest
        </ul>
      </div>
    </div>
  </nav>

        @yield('content')

    </div>


    <footer>
      <div class="container">
        <p>&copy; Ricky Kearney. All Rights Reserved.</p>
        <ul class="list-inline">
          <li class="list-inline-item">
            <a href="#">Privacy</a>
          </li>
          <li class="list-inline-item">
            <a href="#">Terms</a>
          </li>
          <li class="list-inline-item">
            <a href="#">FAQ</a>
          </li>
        </ul>
      </div>
    </footer>

    <!-- Scripts -->
    <!-- <script src="{{ asset('js/app.js') }}"></script> -->
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo(env("GOOGLE_MAPS_KEY")); ?>&callback=initMap">  </script>
    <!-- Bootstrap core JavaScript -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/new-age.min.js') }}"></script>
    <script src="{{ asset('js/datetimepicker.js') }}"></script>
    <script src="{{ asset('js/Chart.js') }}"></script>

</body>
</html>
