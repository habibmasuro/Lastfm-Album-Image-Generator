@extends( 'base' )

@section( 'body' )

@if ( $errors->first ( 'num' ) || $errors->first ( 'type' ) )
<div class="row">

	<div class="col-sm-12">

		{{ $errors->first ( 'num' , '<div class="alert alert-danger"><strong>Error:</strong> :message</div>' ) }}
		
		{{ $errors->first ( 'type' , '<div class="alert alert-danger"><strong>Error:</strong> :message</div>' ) }}
	
	</div>

</div>
@endif

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

{{ Form::close () }}

	@if ( isset ( $dataString ) )
		<div class="col-sm-offset-2 col-sm-10">
			<pre id="generated-code">{{{ $dataString }}}</pre>
		</div>
	@endif

@stop