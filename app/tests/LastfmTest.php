<?php

class LastfmTest extends TestCase {

	/**
	 * Test variable array
	 *
	 * @var array
	 */
	private $testVars = [
		'user' => 'yesdevnull' ,
		'num' => 1 ,
		'type' => '' ,
	];
	
	public function testHomeNoVarsRedirectsToGenerator () {
		$this->call ( 'GET' , '/' );
		
		$this->assertRedirectedTo ( '/generator' );
		// Make sure the Error MessageBag is empty
		$this->assertSessionHas ( 'errors' , '' );
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
	
	public function testHomeUsernameFailsFirstLetterAlpha () {
		$this->testVars['user'] = '2yesdevnull';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors' ] );
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testHomeUsernameFailsDoesNotExist () {
		$this->testVars['user'] = '';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors' ] );
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testHomeUsernameFailsSpecialChars () {
		$this->testVars['user'] = 'yesdevnull+';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors' ] );
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testHomeUsernameFailsTooLong () {
		$this->testVars['user'] = 'LorumIpsumDolorSitAmet';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testHomeUsernameFailsTooShort () {
		$this->testVars['user'] = 'D';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testHomeNumberFailsSpecialChars () {
		$this->testVars['num'] = '1!';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeNumberFailsAlphaChars () {
		$this->testVars['num'] = 'D';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeNumberFailsDoesNotExist () {
		$this->testVars['num'] = '';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeNumberFailsTooLowOutOfBounds () {
		$this->testVars['num'] = 0;
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeNumberFailsTooHighOutOfBounds () {
		$this->testVars['num'] = 13;
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeNumberFailsNegativeNumber () {
		$this->testVars['num'] = -13;
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors'] );
		$this->assertSessionHasErrors ( 'num' );
	}
	
	public function testHomeTypeFailsNotValidVar () {
		$this->testVars['type'] = 'doggy';
		
		$this->call ( 'GET' , '/' , $this->testVars );
		
		$this->assertRedirectedTo ( '/generator' , [ 'errors' ] );
		$this->assertSessionHasErrors ( 'type' );
	}
	
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
		
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testPostGeneratorUsernameAcceptsOnlyAlphaNum () {
		$crawler = $this->client->request ( 'POST' , '/generator' , [ 'user' => 'yesdevnull27' ] );
		
		$this->assertTrue ( $this->client->getResponse ()->isOk () );
		
		$this->assertCount ( 1 , $crawler->filter ( '#generated-code' ) );
	}
	
	public function testPostGeneratorUsernameFirstLetterAlpha () {
		$this->call ( 'POST' , '/generator' , [ 'user' => '2yesdevnull' ] );
		
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testPostGeneratorUsernameFailsSpecialChars () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'yesdevnull+' ] );
		
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testPostGeneratorUsernameFailsTooLong () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'LorumIpsumDolorSitAmet'] );
		
		$this->assertSessionHasErrors ( 'user' );
	}
	
	public function testPostGeneratorUsernameFailsTooShort () {
		$this->call ( 'POST' , '/generator' , [ 'user' => 'D' ] );
		
		$this->assertSessionHasErrors ( 'user' );
	}
}