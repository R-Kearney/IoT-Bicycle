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
            @if ($bikeDynamics->isData == False)
             <h3>No Data Found</h3>
            @else
            <p> Last reported data was at: {{ $bikeDynamics->first()->updated_at }} </p>
            <p> Selected Pedal ID: {{ $bikeDynamics->bikePedalID }} </p>
            @endif
           </p>

          <h4> Select A Day to view your Cycling Dynamics </h4>
          <span class=error>{{ $errors->first('date', ':message') }}</span>

          {{ Form::open(array('url' => 'dynamics' , 'class' => 'form-horizontal', 'method' => 'get')) }}
          <div class="Form-group">
           <div class="col-sm-7">
             <div class="input-group date" data-provide="datepicker">
                <input type="date" name="date" class="form-control">
                <div class="input-group-addon">
                    <span class="glyphicon glyphicon-th"><i class="glyphicon glyphicon-th"></i></span>
                </div>
            </div>
            <p> {{ Form::select('Pedal', ['1' => 'Pedal 1', '2' => 'Pedal 2'], '1') }} </p>
            <p> {{ Form::submit('View Cycling Dynamics', array('class'=>'btn btn-large btn-primary btn-block'))}} </p>
          	{{ Form::close() }}
          </div>
         </div>
         <sub>*Overall Efficency is calculated from power distribution over pedal surface. </sub>
       </div>
     </div>
    </div>
     <div class="col-lg-5 my-auto">
      <div class="device-container">
        <div class="device-mockup iphone6_plus portrait white">
          <div class="device">
            <div class="screen" >
              <h4>Power Phase</h4>
              <div id="dynamics">
                <div id="chartPowerLeft">
                  {!! $chartPowerLeft->render() !!}
                  Left
                </div>
                <div id="chartPowerRight">
                  {!! $chartPowerRight->render() !!}
                  Right
                </div>
              </div>
              <h4>Total Force {{ $bikeDynamics->calculatedNewtons }}N </h4>
              <div id="dynamicsBorder"></div>
              <h4>Power Location per Pedal</h4>
              <div id="dynamics">
                {!! $chartPowerLocation->render() !!}
              </div>
              <div id="dynamicsBorder"></div>
              <h4>Overall Efficency: {{ $bikeDynamics->calculatedEfficency }}%</h4>
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
   var activeDays = ['<?php echo implode("', '", $bikeDynamics->activeDays) ?>']

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

@endsection
