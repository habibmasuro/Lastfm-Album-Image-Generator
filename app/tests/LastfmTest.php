<?php

class LastfmTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
	public function testAccessGenerator () {
		$this->call ( 'GET' , '/generator' );

		$this->assertResponseOK ();
	}
	
	public function testPostGeneratorUsernameIsset () {
		$this->call ( 'POST' , '/generator' );
		
		$this->assertSessionHas ( 'errors' );
	}
	
	public function testPostGeneratorUsernameAcceptsOnlyAlphaNum () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'yesdevnull' ] );
		
		$this->assertResponseOK ();
	}
	
	public function testPostGeneratorUsernameFailsSpecialChars () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'yesdevnull+' ] );
		
		$this->assertSessionHas ( 'errors' );
	}
	
	public function testPostGeneratorUsernameFailsTooLong () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'LorumIpsumDolorSitAmet'] );
		
		$this->assertSessionHas ( 'errors' );
	}
	
	public function testPostGeneratorUsernameFailsTooShort () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'D' ] );
		
		$this->assertSessionHas ( 'errors' );
	}
}