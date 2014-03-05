<?php
$data = '';
 if ( isset ( $dataString ) ) {
$data = $dataString;
} ?>

{{ Form::open ( [ 'action' => 'LastfmController@formProcessor' ] ) }}

	<ul class="errors">
	
		@foreach ( $errors->all () as $message )
		
			<li>{{ $message }}</li>
		
		@endforeach
	
	</ul>

	{{ Form::label ( 'username' , 'Username' ) }}
	
	{{ Form::text ( 'user' , Input::get ( 'user' ) , [ 'id' => 'username' ] ) }}
	
	{{ Form::submit ( 'Generate!' ) }}
	
	{{ Form::textarea ( 'Code' , $data ) }}

{{ Form::close () }}