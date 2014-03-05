<?php

class LastfmController extends BaseController {
	
	/**
	 * The text formatting settings.
	 *
	 * @var array
	 */
	public $text = [
		'colours' => [
			'border' => 'rgb(204,204,204)' ,
			'background' => 'rgb(245,245,245)' ,
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
	
	/**
	 * The Last.fm username
	 *
	 * @var string
	 */
	protected $username;
	
	/**
	 * The Top 10 number
	 *
	 * @var string
	 */
	protected $number;
	
	/**
	 * The request type, image or link
	 *
	 * @var string
	 */
	protected $type;
	
	/**
	 * The API url
	 *
	 * @var string
	 */
	protected $url;
	
	/**
	 * The result of the Last.fm API request
	 *
	 * @var array
	 */
	public $result;
	
	/**
	 * The filename of the generated image
	 *
	 * @var string
	 */
	public $filename;
	
	/**
	 * Follow the leader...
	 *
	 * @return void
	 */
	public function leader () {
		if ( count ( Input::all () ) == 0 ) {
			return Redirect::to ( '/generator' );
		}
		
		$result = Input::only ( [ 'user' , 'num' , 'type' , 'error' ] );
		
		// I use this for error testing...
		$forceError = ( isset ( $result['error'] ) ) ? true : false;
		
		// The validation rules
		$rules = [
			'user'	=> [
				'required' ,
				'regex:/^[a-z]([a-z0-9_-]){2,14}$/i' ,
				'min:2' ,
				'max:15' ,
			] ,
			'num'	=> 'required|integer|between:1,10' ,
			'type'	=> 'in:,link' ,
		];
		
		$messages = [
			'user.required' => 'Your Last.fm <code>user</code>name is required' ,
			'user.regex'	=> 'Your Last.fm <code>user</code>name contains invalid characters' ,
			'user.min'		=> 'Your Last.fm <code>user</code>name is too short' ,
			'user.max'		=> 'Your Last.fm <code>user</code>name is too long' ,
			'num.required'	=> 'You must supply an album number' ,
			'num.integer'	=> 'The var number must be a number' ,
			'num.min'		=> 'The number is too short' ,
			'type.in'		=> 'The <code>type</code> should be link, otherwise don\'t supply it' ,
		];
		
		$validator = Validator::make ( $result , $rules , $messages );
		
		if ( $validator->passes () ) {
			$this->username	= $result['user'];
			$this->number	= $result['num'];
			$this->type		= $result['type'];
		} else {
			return Redirect::to ( '/generator' )->withErrors ( $validator );
		}
		
		$client = new GuzzleHttp\Client( [
			'base_url' => [
				'http://ws.audioscrobbler.com/2.0/?method=library.getalbums&api_key=561e763a09d252d2bbf70beec4897d91&user={username}&limit=10&format=json' ,
				[ 'username' => $this->username ]
			] ,
			'headers' => [
				'User-Agent' => 'yesdevnull.net/lastfm Last.fm Album Image Generator' ,
			] ,
			'timeout' => 5 ,
		] );
		
		$response = $client->get();
		
		// Ruh-roh!
		if ( $response->getStatusCode () != 200 ) {
			Log::error ( 'Unable to connect to Last.fm' , [ 'message' => 'Guzzle Error: ' . $response->getReasonPhrase () , 'url' => $response->getEffectiveUrl () , 'code' => $response->getStatusCode () ] );
			
			// Unknown error connecting to Last.fm
			return $this->generateImage ( false , $response->getStatusCode () . ' Error when connecting to Last.fm' );
		}
		
		$this->result = $this->getApiResult ( $response->json () , $this->number );
		
		if ( $this->type == 'link' ) {
			Log::info ( 'Sending user away to Last.fm' , [ 'url' => $this->result['url'] , 'code' => 307 , 'user' => $this->username , 'number' => $this->number ] );
			
			// Redirect time, send them to Last.fm
			return Redirect::away ( $this->result['url'] , 307 );
		}
		
		$image	= $this->generateImage ( $this->imageUrl , $forceError );
		
		$responseImage = ( gettype ( $image ) == 'resource' ) ? $image->encoded : $image;
		
		$size	= strlen ( $responseImage );
		
		$headers = [
			'Content-Type'		=> 'image/png' ,
			'Content-Length'	=> $size ,
			'X-Powered-By'		=> 'yesdevnull.net/lastfm Last.fm Album Image Generator' ,
		];
		
		$response	= Response::make ( $responseImage , 200 , $headers );
		
		$filetime	= filemtime ( $this->filename );
		$etag		= md5 ( $filetime );
		$time		= Carbon::createFromTimeStamp ($filetime)->toRFC2822String ();
		$expires	= Carbon::createFromTimeStamp ($filetime)->addWeeks (1)->toRFC2822String ();
		
		$response->setEtag ( $etag );
		$response->setLastModified ( new DateTime ( $time ) );
		$response->setExpires ( new DateTime ( $expires ) );
		$response->setPublic ();
		
		return $response;
	}
	
	/**
	 * Return API result with supplied number
	 *
	 * @param	array	$results
	 * @param	integer	$number
	 * @return	array
	 */
	public function getApiResult ( $results , $number ) {
		// Minus 1 because the results array 
		$result = $results['albums']['album'][$number - 1];
		
		$this->imageUrl = $result['image'][3]['#text'];
		
		return $result;
	}
	
	/**
	 * Generate an album image from a Last.fm URL
	 *
	 * @param	string	$url
	 * @param	mixed	$error
	 * @return	mixed
	 */
	public function generateImage ( $url , $error = false ) {
		$this->filename = 'public/' . $this->username . '_' . $this->number . '_' . $this->result['playcount'] . '_' . $this->result['mbid'] . '.png';
		
		if ( Cache::has ( $this->filename ) ) {
			Log::info ( 'Loaded cached object' );
			
			$image = Image::open ( $this->filename );
			
			return $image->encode ( $this->filename , 90 );
		}
		
		if ( $error ) {
			// Normally, you'd only come down this path if the function was called by 
			Log::error ( 'There was an error, generate the error image' );
			
			// We had an error, we're a sad panda
			$image = Image::make ( 'public/resources/sadpanda.png' );
		} else {
			// Pull image from Last.fm and create the Image instance
			try {
				$image = Image::make ( $url );
			} catch ( Exception $e ) {
				Log::error ( 'Image path is invalid' , [ 'message' => 'Error: ' . $e->getMessage () , 'url' => $url , 'code' => $e->getCode () ] );
				
				// Go back and generate the error image
				return $this->generateImage ( false , 'Unable to locate image' );
			}
			
			// Resize image to 300x300 px, just in case the image is too large/small
			$image->resize ( 300 , 300 );
		}
		
		// Expand image up 36px with the background color with white
		$image->resizeCanvas ( 0 , 36 , 'bottom' , true , $this->text['colours']['white'] );
		
		// Larger rectangle that forms the border
		$image->rectangle ( $this->text['colours']['border'] , 0 , 0 , 299 , 24 , false );
		
		// Smaller rectangle with the background
		$image->rectangle ( $this->text['colours']['background'] , 1 , 1 , 298 , 23 );
		
		if ( $error ) {
			$image->text ( 'Error: ' . $error , 7 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['bold'] );
		} else {
			$x = 0;
			
			// 1. or 2. (etc)
			$image->text ( $this->number . '.' , $x += 7 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['bold'] );
			
			$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['bold'] , $this->number . '.' );
			// Artist Name
			$image->text ( $this->result['artist']['name'] , $x += $bbox[2] += 4 , 17 , $this->text['size'] , $this->text['colours']['link'] , 0 , $this->text['fonts']['normal'] );
			
			$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['normal'] , $this->result['artist']['name'] );
			// Chuck in a dash
			$image->text ( '-' , $x += $bbox[2] += 6 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['normal'] );
			
			$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['normal'] , '-' );
			// And the Album Title
			$image->text ( $this->result['name'] , $x += $bbox[2] += 4 , 17 , $this->text['size'] , $this->text['colours']['link'] , 0 , $this->text['fonts']['bold'] );
			
			$bbox = imagettfbbox ( $this->text['size'] , 0 , $this->text['fonts']['bold'] , $this->result['name'] );
			// And the Playcount in brackets
			$image->text ( '(' . $this->result['playcount'] . ')' , $x += $bbox[2] + 4 , 17 , $this->text['size'] , $this->text['colours']['black'] , 0 , $this->text['fonts']['normal'] );	
			// And we're done making the image
		}
		
		// Save the image as a .png with a quality of 90
		$image->save ( $this->filename );
		
		// I don't want to optimise the error message images
		if ( !$error ) {
			Log::info ( 'Finished generating image non-optimised image' );
			
			if ( Cache::add ( $this->filename , true , Carbon::now ()->addWeeks(1) ) ) {
				Log::info ( 'Added item to cache, will go stale in ' . Carbon::now ()->addWeeks(1)->diffForHumans () );	
			}
			
			// Get a random number of seconds between 10 and 99 for the queue - I don't really want to 
			// start generating smaller images straight away
			$seconds = mt_rand ( 10 , 99 );
			
			Log::info ( 'Sent image to optimiser queue, starting in ' . $seconds . ' seconds' );
			// Send the image to the optimiser for the next requests...
			Queue::later ( $seconds , 'LastfmController@optimiseImage' , [ 'image' => $this->filename , 'level' => 2 ] );
		}
		
		return $image;
	}
	
	/**
	 * Optimise the image with Beanstalk and OptiPNG (supplied)
	 *
	 * @param	resource	\Illuminate\Queues\Jobs
	 * @param	array		$data
	 * @return	void
	 */
	public function optimiseImage ( $job , $data ) {
		Log::info ( 'Started Job ID #' . $job->getJobId () . ' - optimising image...' );
		
		$start = Carbon::now ();
		
		// Optimise this image!
		exec ( './app/commands/optipng -o' . $data['level'] . ' -quiet ' . $data['image'] );
		
		// The difference of time in seconds between 
		$diff = ( $start->diffInSeconds () == 1 ) ? $start->diffInSeconds () . ' second' : $start->diffInSeconds () . ' seconds';
		
		Log::info ( 'Finished Job ID #' . $job->getJobId () . ' - optimising image, took ' . $diff );
		
		// Remove job from queue
		$job->delete ();
	}
	
	/**
	 * Process the form request
	 *
	 * @return void
	 */
	public function formProcessor () {
		$result = Input::only ( [ 'user' ] );
		
		// The validation rules
		$rules = [
			'user'	=> [
				'required' ,
				'regex:/^[a-z]([a-z0-9_-]){1,14}$/i' ,
			] ,
		];
		
		$messages = [
			'user.required' => 'Your Last.fm username is required' ,
			'user.regex'	=> 'Your Last.fm username contains invalid characters' ,
			'user.min'		=> 'Your Last.fm username is too short' ,
			'user.max'		=> 'Your Last.fm username is too long' ,
		];
		
		$validator = Validator::make ( $result , $rules , $messages );
		
		if ( $validator->passes () ) {
			$this->username	= $result['user'];
		} else {
			return Redirect::to ( '/generator' )->withInput ()->withErrors ( $validator );
		}
		
		$string = '';
		
		for ( $i = 1 ; $i <= 10 ; $i++ ) {
			$newLine = ( $i < 10 ) ? "\n\n" : '';
			
			$string .= '[url=' . URL::action ( 'LastfmController@leader' , [ 'user' => $this->username , 'num' => $i , 'type' => 'link' ] ) . '][img]' . URL::action ( 'LastfmController@leader' , [ 'user' => $this->username , 'num' => $i ] ) . '[/img][/url]' . $newLine;
		}
		
		return View::make ( 'form' )->with ( 'dataString' , $string );
	}
}