@extends('layouts.app')

@section('content')

<header class="masthead">
 <div class="container h-100">
   <div class="row h-100">
     <div class="col-lg-7 my-auto">
       <div class="header-content mx-auto">
         <h2 class="mb-5">Welcome back {{ strtoupper(Auth::user()->name) }}!</h2>
         <p></p>

         @yield('content')
         <div class="bikeInfo">
           <p>
            @if ($bikeLocation->isData == False)
             <h3>No Data Found</h3>
            @else
             Last reported location was at : {{ $bikeLocation->first()->updated_at }}
             <p>Distance Cycled: {{ $bikeLocation->distance }} km</p>
             <p>Avgerage Speed: {{ $bikeLocation->avgSpeed }} km/h</p>
            @endif
           </p>
           <p><i class="fa fa-stop"  style="color: #73f76a;" aria-hidden="true"></i> Start</p>
           <p><i class="fa fa-stop"  style="color: #6a6af7;" aria-hidden="true"></i> Finish</p>

          <h4> Select A Day to view your timline</h4>
          <span class=error>{{ $errors->first('date', ':message') }}</span>

          {{ Form::open(array('url' => 'timeline' , 'class' => 'form-horizontal', 'method' => 'get')) }}
          <div class="Form-group">
           <div class="col-sm-7">
            <div class="input-group date" data-provide="datepicker">
               <input type="date" name="date" class="form-control">
               <div class="input-group-addon">
                   <span class="glyphicon glyphicon-th"><i class="glyphicon glyphicon-th"></i></span>
               </div>
           </div>


            <p> Snap To Road {{ Form::checkbox('snapToRoad', '1', false, array('class' => 'name')) }} </p>
            <p> {{ Form::submit('View Timeline', array('class'=>'btn btn-large btn-primary btn-block'))}} </p>
          	{{ Form::close() }}
          </div>
         </div>

       </div>
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
function datePicker( jQuery ) {
  $(function () {
   var activeDays = ['<?php echo implode("', '", $bikeLocation->activeDays) ?>']

    $('.input-group.date').datepicker({
    format: "yyyy-mm-dd",
    todayBtn: "linked",
    autoclose: true,

    beforeShowDay: function (date) {
      var allDates = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
      if(activeDays.indexOf(allDates) != -1)
      return {
            classes: 'active'
          };
      else
       return true;
    }
   });

  });
}
</script>

<script>

@if ($bikeLocation->isData == False)
 //No Data Found
 function initMap() {
   var uluru = {lat: 52.6680, lng: -8.6305}; // Ireland Coords
   var map = new google.maps.Map(document.getElementById('map'), {
     zoom: 6,
     center: uluru
   });
 }
@else
      function initMap() {
        var uluru = {lat: {{ $bikeLocation->first()->lat }} , lng: {{$bikeLocation->first()->long }} };
        var map = new google.maps.Map(document.getElementById('map'), {
          maxZoom: 18,
          zoom: 12,
          center: uluru
        });

        bounds  = new google.maps.LatLngBounds(); // sets auto zoom bounds

       // Define a symbol using SVG path notation, with an opacity of 1.
       var lineSymbol = {
         path: 'M 0,-1 0,1',
         strokeOpacity: 1,
         scale: 3,
         strokeColor: '#f45642' // red
       };

       // Start Marker
       var marker = new google.maps.Marker({
         position: {lat: {{ $bikeLocation->last()->lat }} , lng: {{$bikeLocation->last()->long }} },
         icon: {
           path: google.maps.SymbolPath.CIRCLE,
           scale: 3,
           strokeColor: '#73f76a' // Green
         },
         draggable: false,
         map: map
       });

       // Finish Marker
       var marker = new google.maps.Marker({
         position: {lat: {{ $bikeLocation->first()->lat }} , lng: {{$bikeLocation->first()->long }} },
         icon: {
           path: google.maps.SymbolPath.CIRCLE,
           scale: 3,
           strokeColor: '#6a6af7' // Blue
         },
         draggable: false,
         map: map
       });

       // Create the polyline, passing the symbol in the 'icons' property.
       // Give the line an opacity of 0.
       // Repeat the symbol at intervals of 20 pixels to create the dashed effect.
       var line = new google.maps.Polyline({
        path: [
        @foreach ($bikeLocation as $bikeLocationTemp)
        {
         lat: {{ $bikeLocationTemp->lat }} , lng: {{$bikeLocationTemp->long }}
        },
         @endforeach
        ],
         strokeOpacity: 0,
         icons: [{
           icon: lineSymbol,
           offset: '0',
           repeat: '20px'
         }],
         map: map
       });

      @foreach ($bikeLocation as $bikeLocationTemp)
         loc = new google.maps.LatLng({{ $bikeLocationTemp->lat }}, {{ $bikeLocationTemp->long }});
         bounds.extend(loc);
      @endforeach

       map.fitBounds(bounds);       // auto-zoom
       map.panToBounds(bounds);     // auto-center
      }
    @endif
    </script>

@endsection
