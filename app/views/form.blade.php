@extends( 'base' )

@section( 'body' )

<div class="row">

	<div class="col-sm-12 text-center">
	
		<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Enter your Last.fm username below to generate the BBCode required for your Last.fm profile page.</div>
	
	</div>

</div>

@if ( $errors->first ('num') || $errors->first ('type') || $errors->first('token') )
<div class="row">

	<div class="col-sm-12 text-center">

		{{ $errors->first ( 'num' , '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-sign"></span> <strong>Error:</strong> :message</div>' ) }}
		
		{{ $errors->first ( 'type' , '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-sign"></span> <strong>Error:</strong> :message</div>' ) }}
		
		{{ $errors->first ( 'token' , '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove-sign"></span> <strong>Error:</strong> :message</div>' ) }}
	
	</div>

</div>
@endif

{{ Form::open ( [ 'action' => 'LastfmController@formProcessor' , 'role' => 'form' , 'class' => 'form-horizontal' ] ) }}

	<?php $usernameErrors = ( $errors->has ( 'user' ) ) ? true : false; ?>
	
	<div class="form-group {{ $errors->first ( 'user' , 'has-error has-feedback' ) }}">
	
		{{ Form::label ( 'username' , 'Username' , [ 'class' => 'col-sm-1 control-label' ] ) }}
			
		<div class="col-sm-11">
		
			{{ Form::text ( 'user' , Input::get ( 'user' ) , [ 'id' => 'username' , 'class' => 'form-control' ] ) }}
			
			@if ( $usernameErrors )
				<span class="glyphicon glyphicon-remove-sign form-control-feedback"></span>
				
				{{ $errors->first ( 'user' , '<span class="help-block">:message</span>' ) }}
			@endif
		
		</div>
	
	</div>
	
	<div class="form-group">
	
		<div class="col-sm-offset-1 col-sm-11">
	
			<label>{{ Form::submit ( 'Generate!' , [ 'class' => 'btn btn-primary btn-lg' ] ) }}</label>
		
		</div>
	
	</div>

{{ Form::close () }}

	@if ( isset ( $dataString ) )
		<div class="col-sm-12">
			<pre id="generated-code">{{{ $dataString }}}</pre>
		</div>
	@endif

@stop