@extends('layouts.app')

@section('content')


<header class="masthead">
 <div class="container h-100">
   <div class="row h-100">
     <div class="col-lg-7 my-auto">
       <div class="header-content mx-auto">
         <p></p>
         <h2 class="mb-5">Welcome back {{ strtoupper(Auth::user()->name) }}!</h2>
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
               <td> Lat: {{ $bikeLocation->lat }}, Long {{ $bikeLocation->long }}  </td>
              </tr>
              <tr>
               <td>Time Last Seen </td>
               <td> {{ $bikeLocation->updated_at }} </td>
              </tr>
              <tr>
               <td>Total Distance Cycled </td>
               <td> {{ $bikeLocation->distance }} km </td>
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
               <td>Bike Pedal ID</td>
               <td> {{ $user->bikePedalID }} </td>
              </tr>
              <tr>
               <td>Second Bike Pedal ID</td>
               <td> {{ $user->bikePedalID_2 }} </td>
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
@if ($bikeLocation->updated_at == " ")
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
        var uluru = {lat: 52.6680, lng: -8.6305}; // Ireland Coords
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 6,
          center: uluru
        });
      }
      function locateBike() {
        var uluru = {lat: {{ $bikeLocation->lat }}, lng: {{ $bikeLocation->long }} };
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 16,
          center: uluru
        });
        var marker = new google.maps.Marker({
         position: uluru,
         map: map
       });
      }
 @endif
    </script>

@endsection
