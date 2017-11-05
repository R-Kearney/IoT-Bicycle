@extends('layouts.app')
@section('bikeDetials')

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

  <p></p>
  <a class="btn btn-outline btn-xl js-scroll-trigger" href="{{ route('editBike') }}">Edit</a>

</div>
@endsection
