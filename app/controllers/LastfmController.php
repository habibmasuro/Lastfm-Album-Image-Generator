<?php

class LastfmException extends Exception {}

App::error( function ( LastfmException $e , $errorCode , $fromConsole ) {
	if ( $fromConsole ) {
		return 'Error (' . $errorCode . '): ' . $e->getMessage() . "\n";
	}
	
	return '<h1>Error: ' . $e->getMessage() . '</h1>';
} );

class LastfmController extends BaseController {
	public $text = [
		'colours' => [
			'border' => [ 204 , 204 , 204 ] ,
			'background' => [ 245 , 245 , 245 ] ,
			'white' => '#fff' ,
			'link' => 'rgb(1,135,197)' ,
			'black' => '#000' ,
		] ,
		'size' => 8 ,
		'fonts' => [
			'normal' => 'public/fonts/Arial.ttf' ,
			'bold' => 'public/fonts/Arial-Bold.ttf' ,
		] ,
	];
	
	protected $username;
	
	protected $number;
	
	protected $type;
	
	protected $url;
	
	public $imageUrl;
	
	public $result;
	
	public function leader ( ) {
		$result = Input::only( [ 'user' , 'num' , 'type' ] );
		
		$rules = [
			'user'	=> [
				'required' ,
				'regex:/^([a-z0-9_-]){1,30}$/i' ,
			] ,
			'num'	=> 'required|integer|between:1,10' ,
			'type'	=> 'alpha' ,
		];
		
		$validator = Validator::make( $result , $rules );
		
		if ( $validator->passes() ) {
			$this->username = $result['user'];
			$this->number = $result['num'];
			$this->type = $result['type'];
		} else {
			var_dump( $validator->messages() );
		}
				
		$url = 'http://ws.audioscrobbler.com/2.0/?method=library.getalbums&api_key=561e763a09d252d2bbf70beec4897d91&user=' . $this->username . '&limit=10&format=json';
		
		$ch = curl_init ( $url );
		
		curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , true );
		
		$curlResult = json_decode ( curl_exec ( $ch ) , true );
		
		$this->result = $curlResult['albums']['album'][ $this->number - 1 ];
		
		$this->imageUrl = $this->result['image'][3]['#text'];
		
		$this->generateImage ( $this->result['image'][3]['#text'] );
	}
	
	public function getAlbumNumber () {
		
	}
	 
	public function generateImage( $url ) {
		$name = $this->username . '_' . $this->number . '_' . md5( time() ) . '.png';
		
		// Pull image from Last.fm and create the Image instance
		try {
			$image = Image::make( $url );
		} catch ( Exception $e ) {
			Log::error ( 'Image path is invalid' , [ 'message' => 'Error: ' . $e->getMessage() , 'url' => $url , 'code' => $e->getCode() ] );
			throw new \Exception( );
		}
		
		// Resize image to 300x300 px
		$image->resize ( 300 , 300 );
		
		// Expand image up 41px with the background color with white
		$image->resizeCanvas ( 0 , 36 , 'bottom' , true , '#fff' );
		
		// Larger rectangle that forms the border
		$image->rectangle ( 'rgb(204,204,204)' , 0 , 0 , 299 , 24 , false );
		
		// Smaller rectangle with the background
		$image->rectangle ( 'rgb(245,245,245)' , 1 , 1 , 298 , 23 );
		
		$x = 0;
		
		// 1. or 2. (etc)
		$image->text( $this->number . '.' , $x += 5 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['bold'] );
		
		$bbox = imagettfbbox( $this->text['size'] , 0 , $this->text['fonts']['bold'] , $this->number . '.' );
		// Artist Name
		$image->text( $this->result['artist']['name'] , $x += $bbox[2] += 4 , 17 , $this->text['size'] , $this->text['colours']['link'] , 0 , $this->text['fonts']['normal'] );
		
		$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['normal'] , $this->result['artist']['name'] );
		// Chuck in a dash
		$image->text ( '-' , $x += $bbox[2] += 6 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['normal'] );
		
		$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['normal'] , '-' );
		// And the Album Title
		$image->text ( $this->result['name'] , $x += $bbox[2] += 4 , 17 , $this->text['size'] , $this->text['colours']['link'] , 0 , $this->text['fonts']['bold'] );
		
		$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['bold'] , $this->result['name'] );
		// And the Playcount in brackets
		$image->text ( '(' . $this->result['playcount'] . ')' , $x += $bbox[2] + 4 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['normal'] );
		
		// Save the image as a .png with a quality of 90
		$image->save ( $name , 90 );
		
		Log::info ( 'Finished generating image non-optimised image' );
		
		// Get a random number of seconds between 10 and 99 for the queue - I don't really want to 
		// start generating smaller images straight away
		$seconds = mt_rand ( 10 , 99 );
		
		Log::info ( 'Sent image to optimiser queue, starting in ' . $seconds . ' seconds' );
		// Send the image to the optimiser for the next requests...
		Queue::later ( $seconds , 'LastfmController@optimiseImage' , [ 'image' => $name , 'level' => 2 ] );
	}
	
	public function optimiseImage ( $job , $data ) {
		Log::info ( 'Started Job ID #' . $job->getJobId() . ' - optimising image...' );
		
		$start = Carbon::now();
		
		// Optimise this image!
		exec ( './app/commands/optipng -o' . $data['level'] . ' -quiet ' . $data['image'] ); //. ' -out ' . $name );
		
		$diff = ( $start->diffInSeconds() == 1 ) ? $start->diffInSeconds() . ' second' : $start->diffInSeconds() . ' seconds';
		
		Log::info ( 'Finished Job ID #' . $job->getJobId() . ' - optimising image, took ' . $diff );
		
		// Remove job from queue
		$job->delete();
	}
}
