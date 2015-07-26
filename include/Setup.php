<?php
/** Echo out our whole project at this point  **/
echo new Controller\Setup(AllPages,function($p) {
	/* Handle any logic that needs to happen on all pages at the Setup level, such as Ajax Calls */
	switch(PAGE) {
		case 'PostPage':
			return json_encode($data);
		break;
		case 'GetPage':
			return 'Some Get String';
		break;
	}

	/* Return our Setup view */
	return new View\Setup('Setup',[
		'Base' => URL,
		'Title' => 'New AMVC Project',
		'Keywords' => '',
		'Description' => 'New Axori MVC Project',
		'FavIcon' => '/imgs/favicon.png',
		
		// Load our External CSS
		'HTTPCSS' => [
			'http://necolas.github.io/normalize.css/3.0.2/normalize.css',
		],
		
		// Load our External JS
		'HTTPJS' => [
			'https://code.jquery.com/jquery-1.11.1.min.js',
		],
		
		// Create a Body Controller and connect it to any set of pages
		'Body' => new Controller(AllPages,function($p) {
		
			// Setup some cross body variables
			$BodyHeader = 'Default Data';
			$BodyPage = new View('Body/Page/404');
			$BodyFooter = new View('Body/Footer');

			// Handle dynamic page routing actions
			switch(PAGE) {
				case AboutUs: // about-us, about-casey, about-chris
					$PAGE = 'about-us';	
				break;
			}

			// Include our PAGE Controller
			$PAGE = (PAGE == '/'?'index':PAGE);
			include(FILEROOT.'Setup/'.$PAGE.'.php');
			
			// If these things are not defined, then define them as false
			defined('SomeBoolean')?:define('SomeBoolean',false);
		
			// You can have deeper controllers define things (and set to default value like above), then have the higher controller adjust things based on those defines
			$Slideshow = '';
			if(SomeBoolean) {
				$Slideshow = new View('Body/Header/Slideshow');
			}
			
			$Header = new View('Body/Header',[
				'Slideshow' => $Slideshow,
			]);
		
			/* Return our Page view */
			return new View('Body',[
				'Header' => $BodyHeader,
				'Page' => $BodyPage,
				'Footer' => $BodyFooter,
			]);
		}),
	]);
});
?>
