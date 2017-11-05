@extends('layouts.app')

@section('content')


<header class="masthead">
 <div class="container h-100">
   <div class="row h-100">
     <div class="col-lg-7 my-auto">
       <div class="header-content mx-auto">
         <h1 class="mb-5">Welcome back {{ Auth::user()->name }}!</h1>
         <div class="btn btn-outline btn-xl js-scroll-trigger">Locate My Bike</div>
         <p></p>
         <div class="bikeInfo">
          <div class="table-responsive">

            <table class="table">
              <tr>
               <td>Bike Name </td>
               <td> {{ DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)->value('bikeName') }} </td>
              </tr>
              <tr>
               <td>Bike Tracker ID </td>
               <td> {{ Auth::user()->bikeTrackerID }} </td>
              </tr>
              <tr>
               <td>Last Seen </td>
               <td>  </td>
              </tr>
              <tr>
               <td>Time Last Seen </td>
               <td> </td>
              </tr>
              <tr>
               <td>Bike Colour </td>
               <td> {{ DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)->value('bikeColour') }} </td>
              </tr>
              <tr>
               <td>Bike Manufacture </td>
               <td> {{ DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)->value('bikeMake') }} </td>
              </tr>
              <tr>
               <td>Bike Type </td>
               <td> {{ DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)->value('bikeType') }} </td>
              </tr>
              <tr>
               <td>Register Date </td>
               <td> {{ DB::table('bikes')->where('bikeTrackerID', Auth::user()->bikeTrackerID)->value('created_at') }} </td>
              </tr>
            </table>
           </div>

         </div>
         <p></p>
         <div class="btn btn-outline btn-xl js-scroll-trigger">Edit</div>
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
