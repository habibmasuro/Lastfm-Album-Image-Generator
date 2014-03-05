<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Last.fm Album Image Generator</title>
		
		<!-- Bootstrap -->
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
		
		<style>
		.container {
			margin-top: 40px;
		}
		</style>
		
		<!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css" rel="stylesheet"> -->
		
		<!-- <link href="cover.css" rel="stylesheet"> -->
		
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container">
			<div class="jumbotron text-center">
				<h1>Last.fm Album Image Generator</h1>
				
				<p>Brought to you by <strong>Dan Barrett</strong>.  Check out <a href="http://yesdevnull.net/lastfm" class="btn btn-danger">yesdevnull.net/lastfm</a> for more details.</p>
			</div>
			
			@yield( 'body' )
			
			<div class="row">
				<div class="col-sm-12">
					<p class="text-center"><em><small>&copy; {{ date ( 'Y' ) }} Dan Barrett.  This service is not official, nor endorsed by <span class="btn btn-danger btn-xs">Last.fm Ltd</span></small></em></p>
				</div>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	</body>
</html>