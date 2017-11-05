@extends('layouts.app')

@section('content')

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100">

      <div class="col-lg-7 my-auto">
        <div class="header-content mx-auto">
          <h1 class="mb-5">The Invisible GPS</h1>
          <h1 class="mb-5">Anti Theft Tracker</h1>
          <h1 class="mb-5">For Your Bike</h1>
          <a href="{{ route('register') }}" class="btn btn-outline btn-xl js-scroll-trigger">Track Now!</a>
        </div>
      </div>
      <div class="col-lg-5 my-auto">
        <div class="device-container">
          <div class="device-mockup iphone6_plus portrait white">
            <div class="device">
              <div class="screen">
                <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                <img src="img/appScreen.jpg" class="img-fluid" alt="">
              </div>
              <div class="button">
                <!-- You can hook the "home button" to some JavaScript events or just remove it -->
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</header>

<section class="technology bg-primary text-center" id="technology">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <h2 class="section-heading">PEACE OF MIND FOR YOUR BIKE</h2>
        <p>Keep your bike safe, the invisible GPS anti-theft device for bicycles designed to give you the peace of mind you are looking for</p>
        <p>&nbsp;</p>

        <div class="row">
         <div class="col-md-4 col-sm-6">
          <div class="h2 ">
           <i class="fa fa-shield" aria-hidden="true"></i> SECURE</div>
           <div class="panel-body">GPS based, precise up to 5m</div>
           </div>

           <div class="col-md-4 col-sm-6">
            <div class="h2"><i class="fa fa-check" aria-hidden="true"></i> SIMPLE</div>
            <div class="panel-body">Interaction via mobile web app</div>
           </div>

           <div class="col-md-4 col-sm-6">
            <div class="h2"><i class="fa fa-eye-slash" aria-hidden="true"></i> STEALTH</div>
            <div class="panel-body">Invisible from the outside</div>
         </div>
        </div>

      </div>
    </div>
  </div>
</section>

<section class="features" id="features">
  <div class="container">
    <div class="section-heading text-center">
      <h2>Hide It. Lock It. Track It.</h2>
      <p class="text-muted">Stays hidden until you need it most!</p>
      <hr>
    </div>
    <div class="row">
      <div class="col-lg-4 my-auto">
        <div class="device-container">
          <div class="device-mockup iphone6_plus portrait white">
            <div class="device">
              <div class="screen">
                <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                <img src="img/demo-screen-1.jpg" class="img-fluid" alt="">
              </div>
              <div class="button">
                <!-- You can hook the "home button" to some JavaScript events or just remove it -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-8 my-auto">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-6">
              <div class="feature-item">
                <i class="icon-screen-smartphone text-primary"></i>
                <h3>Any Device</h3>
                <p class="text-muted">Works with any device, straight out of the box</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="feature-item">
                <i class="icon-camera text-primary"></i>
                <h3>Exterme Battery Saving</h3>
                <p class="text-muted">Always charged when you need it most</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="feature-item">
                <i class="icon-present text-primary"></i>
                <h3>Friend In Need?</h3>
                <p class="text-muted">Loan the bike to a friend with one click!</p>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="feature-item">
                <i class="icon-lock-open text-primary"></i>
                <h3>Secure</h3>
                <p class="text-muted">We take security seriously!</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta">
  <div class="cta-content">
    <div class="container">
      <h2>Stop Theft.<br>Start Tracking.</h2>
      <a href="{{route ('register')}}" class="btn btn-outline btn-xl js-scroll-trigger">Register Your Tracker Now!</a>
    </div>
  </div>
  <div class="overlay"></div>
</section>

<section class="contact bg-primary" id="contact">
  <div class="container">
    <h2>We
      <i class="fa fa-heart"></i>
      new friends!</h2>
    <ul class="list-inline list-social">
      <li class="list-inline-item social-twitter">
        <a href="#">
          <i class="fa fa-twitter"></i>
        </a>
      </li>
      <li class="list-inline-item social-facebook">
        <a href="#">
          <i class="fa fa-facebook"></i>
        </a>
      </li>
      <li class="list-inline-item social-google-plus">
        <a href="#">
          <i class="fa fa-google-plus"></i>
        </a>
      </li>
    </ul>
  </div>
</section>





@endsection
