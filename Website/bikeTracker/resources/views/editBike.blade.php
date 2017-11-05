@extends('layout.main')

@section('content')

<div class="container">



	{{ Form::open(array('url' => 'updateBike')) }}

	<div class="form-group">

		<div class="col-sm-6">
			<label class="control-label" for="textinput">bike Name</label>
			{{ Form::text('bike Name', bikeName) }}

		</div>


		<div class="col-sm-6">
			<label class="control-label" for="textinput">Bike Colour</label>
			{{ Form::text('bikeColour', $bikeColour) }}

		</div>

	</div>


	<!-- Text input-->
	<div class="form-group">

		<div class="col-sm-6">
			<label class="control-label" for="textinput">Bike Type</label>
			{{ Form::text('bikeType', $bikeType) }}

		</div>


		<div class="col-sm-6">
			<label class="control-label" for="textinput">Bike Make</label>
			{{ Form::text('bikeMake', $bikeMake) }}

		</div>

	</div>


	{{ Form::hidden('id', $customer->id) }}


	<div class="form-group">
		<div class="col-sm-12">
			{{ Form::submit('Update', array('class'=>'btn btn-large btn-primary btn-block'))}}
		</div>
	</div>


	{{ Form::close() }}
</div>
@stop
