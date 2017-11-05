@extends('layouts.app')

@section('content')


<header class="masthead">
 <div class="container h-100">
   <div class="row h-100">
     <div class="col-lg-7 my-auto">
       <div class="header-content mx-auto">
         <h2 class="mb-5">Welcome back {{ Auth::user()->name }}!</h2>
         <div class="btn btn-outline btn-xl js-scroll-trigger" onclick="locateBike()">Locate My Bike</div>
         <p></p>

         @yield('content')
         <div class="bikeInfo">
          <div class="table-responsive">

            <table class="table">
              <tr>
               <td>Bike Name </td>
               <td> {{ $bike->bikeName }} </td>
              </tr>
              <tr>
               <td>Bike Tracker ID </td>
               <td> {{ $bike->bikeTrackerID }} </td>
              </tr>
              <tr>
               <td>Last Seen </td>
               <td> Lat: {{ $bike->currentLat }}, Long {{ $bike->currentLong }}  </td>
              </tr>
              <tr>
               <td>Time Last Seen </td>
               <td> {{ $bike->updated_at }} </td>
              </tr>
              <tr>
               <td>Bike Colour </td>
               <td> {{ $bike->bikeColour }} </td>
              </tr>
              <tr>
               <td>Bike Manufacture </td>
               <td> {{ $bike->bikeMake }} </td>
              </tr>
              <tr>
               <td>Bike Type </td>
               <td> {{ $bike->bikeType }} </td>
              </tr>
              <tr>
               <td>Register Date </td>
               <td> {{ $bike->created_at }} </td>
              </tr>
            </table>
           </div>

           <p></p>
           <a class="btn btn-outline btn-xl js-scroll-trigger" href="{{ route('editBike') }}">Edit</a>
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
      function locateBike() {
        var uluru = {lat: {{ $bike->currentLat }}, lng: {{ $bike->currentLong }}};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: uluru
        });
        var marker = new google.maps.Marker({
         position: uluru,
         map: map
       });
      }
    </script>

@endsection
