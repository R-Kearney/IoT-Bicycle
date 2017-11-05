@extends('layouts.app')

@section('content')

<header class="masthead">
 <div class="container h-100">
   <div class="row h-100">
     <div class="col-lg-7 my-auto">
       <div class="header-content mx-auto">
         <h1 class="mb-5">Welcome back </h1>
         <div class="btn btn-outline btn-xl js-scroll-trigger">Locate My Bike</div>
         <p></p>
         <div class="bikeInfo">

	{{ Form::open(array('url' => 'updateBikeLocation' , 'class' => 'form-horizontal', 'method' => 'put')) }}

	<div class="Form-group">

		<div class="col-sm-7">
			<label class="control-label" for="textinput">device</label>
			{{ Form::text('device', 'test' , array('class' => 'form-control' )) }}
			<span class=error>{{ $errors->first('bikeName', ':message') }}</span>

		</div>


		<div class="col-sm-7">
			<label class="control-label" for="textinput">data</label>
			{{ Form::text('data', 'test', array('class' => 'form-control' )) }}
			<span class=error>{{ $errors->first('bikeColour', ':message') }}</span>

		</div>

	</div>



	<div class="Form-group">
		<div class="col-sm-4">
			<p></p>
			{{ Form::submit('Update', array('class'=>'btn btn-large btn-primary btn-block'))}}
		</div>
	</div>


	{{ Form::close() }}
</div>
<p></p>

</div>
</div>
<div class="col-lg-5 my-auto">
<div class="device-container">
<div class="device-mockup iphone6_plus portrait white">
	<div class="device">
			<div class="screen" id="map">
			</div>
			</div>
	</div>
</div>
</div>
</div>
</div>
</div>
</header>
<script>
      function initMap() {
        var uluru = {lat: 52.6680, lng: -8.6305};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 6,
          center: uluru
        });
        //var marker = new google.maps.Marker({
         // position: uluru,
        //  map: map
       // });
      }
    </script>
@endsection
