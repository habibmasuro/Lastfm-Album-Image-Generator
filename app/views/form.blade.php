<?php
$data = '';
 if ( isset ( $dataString ) ) {
$data = $dataString;
} ?>

{{ Form::open ( [ 'action' => 'LastfmController@formProcessor' ] ) }}

	{{ Form::label ( 'username' , 'Username' ) }}
	
	{{ Form::text ( 'user' , Input::get ( 'user' ) , [ 'id' => 'username' ] ) }}
	
	{{ Form::submit ( 'Generate!' ) }}
	
	{{ Form::textarea ( 'Code' , $data ) }}

{{ Form::close () }}