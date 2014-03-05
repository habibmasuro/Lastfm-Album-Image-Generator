<?php

class LastfmTest extends TestCase {

	/**
	 * Test variable array
	 *
	 * @var array
	 */
	private $testVars = [
		'user' => 'yesdevnull' ,
		'num' => 1
	];
	
	public function testHomeNoVarsRedirectsToGenerator () {
		$this->call ( 'GET' , '/' );
		
		$this->assertRedirectedTo ( '/generator' );
	}
	
	/**
	 * I'm commenting this out because it slows down the unit test dramatically
	 */
	/*
	public function testHomeUsernameAcceptsOnlyAlphaNum () {		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertResponseOK();
	}
	*/
	
	public function testGetGenerator () {
		$this->call ( 'GET' , '/generator' );

		$this->assertResponseOK ();
	}
	
	public function testPostGeneratorAllOK () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'yesdevnull'] );
		
		$this->assertViewHas ( 'dataString' );
	}
	
	public function testPostGeneratorUsernameFailsNoUsername () {
		$this->call ( 'POST' , '/generator' );
		
		$this->assertSessionHas ( 'errors' );
	}
	
	public function testPostGeneratorUsernameAcceptsOnlyAlphaNum () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'yesdevnull27' ] );
		
		$this->assertResponseOK ();
	}
	
	public function testPostGeneratorUsernameFirstLetterAlpha () {
		$this->call ( 'POST' , '/generator' , [ 'user' => '2yesdevnull' ] );
		
		$this->assertSessionHas ( 'errors' );
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