@extends( 'base' )

@section( 'body' )

<?php

//dd($errors->isEmpty());

$data = '';

if ( isset ( $dataString ) ) {
	$data = $dataString;
}

?>

{{ Form::open ( [ 'action' => 'LastfmController@formProcessor' , 'role' => 'form' , 'class' => 'form-horizontal' ] ) }}

	<?php $usernameErrors = ( $errors->has ( 'user' ) ) ? true : false; ?>
	
	<div class="form-group {{ $errors->first ( 'user' , 'has-error has-feedback' ) }}">
	
		{{ Form::label ( 'username' , 'Username' , [ 'class' => 'col-sm-2 control-label' ] ) }}
			
		<div class="col-sm-10">
		
			{{ Form::text ( 'user' , Input::get ( 'user' ) , [ 'id' => 'username' , 'class' => 'form-control' ] ) }}
			
			@if ( $usernameErrors )
				<span class="glyphicon glyphicon-remove form-control-feedback"></span>
				
				{{ $errors->first ( 'user' , '<span class="help-block">:message</span>' ) }}
			@endif
		
		</div>
	
	</div>
	
	<div class="form-group">
	
		<div class="col-sm-offset-2 col-sm-10">
	
			<label>{{ Form::submit ( 'Generate!' , [ 'class' => 'btn btn-primary btn-lg' ] ) }}</label>
		
		</div>
	
	</div>
	
	<div class="form-group">
	
		{{ Form::label ( 'code' , 'Your Code' , [ 'class' => 'col-sm-2 control-label'] ) }}
		
		<div class="col-sm-10">
	
		{{ Form::textarea ( 'code' , $data , [ 'class' => 'form-control' ] ) }}
		
		</div>
	
	</div>

{{ Form::close () }}

	@if ( isset ( $dataString ) )
		<pre>{{{ $dataString }}}</pre>
	@endif

@stop