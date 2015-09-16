# VOMVC - VO MVC

This is a straight forward MVC that follows the below flow, and is extendable on all levels quite easily.

###### Please note that this project has been filed with the Copyright Office of the United States and is currently an open case [1-2708466311], the LICENSE allowing you to use it must be followed. Comments in the Core Files for the Author of Casey Childers MUST REMAIN in your Code per this License & Copyright Agreement.

![](https://docs.google.com/drawings/d/1vSqLWgyM9uoJrh39qsfDERNoREVq22UEq66Z4Qq66OI/pub?w=960&h=720)
![](https://docs.google.com/drawings/d/1SVt-vS-P2gJy2zockFyWOkNPV2R_gwx8YlL1BDCheWU/pub?w=961&h=2923)

Lets walk through an index.php file using the AMVC

## 1. Handle Required Root Defines
    define('FILEROOT','Include/');
    define('COREROOT',FILEROOT.'Core/');
    define('VIEWROOT',FILEROOT.'View/');
    define('CSSROOT',FILEROOT.'CSS/');
    define('JSROOT',FILEROOT.'JS/');


## 2. Handle Other Required Defines
    session_start();
    define('DateTimeZone','America/Los_Angeles'); date_default_timezone_set(DateTimeZone);
    define('ROOT', getcwd());
    define('PROJECT', str_replace('/index.php', '', $_SERVER['PHP_SELF']));
    define("URL", "http://".$_SERVER['HTTP_HOST'].PROJECT);
    	
    define('DBH','localhost');
    define('DBN','DBName');
    define('DBU','DBUser');
    define('DBP','DBPassword');
    
    define('EncryptionPassword','AReallyGoodPassword');
    define('EncryptionLength','256');
	  
	  
## 3. Include the AMVC Core
    include(COREROOT.'Core.php');
    
    
## 4. Define our dynamic pages, 'URL Routing Triggers', p function returns true if the regexp is satisfied against PAGE define
    defines([
        'AllPages'	=> p('/^.*?$/'),
  		
        'Index'		=> p('/^\/?$/'),
        'AboutUs'	=> p('/^about-(us|casey|chris)?\/?$/'),
  		
        'PostPage'	=> p('/^post\/(stuff|something)?\/?$/'),
        'GetPage'	=> p('/^get\/(stuff|something)?\/?$/'),
    ]);
	  
	  
## 5. Setup our Page

This will handle connecting all 'allowed' pages to this Page controller. In this example, we allow all pages to pass through. The Page Controller, and all Controllers, expect the first parameter to be a true / false, this is the 'Trigger' which fires the closure function or function on the extended object (pass a string of the name of the function to the 2nd param instead of a closure). Our Page class simply extends a Controller, and outputs the Views CSS & JS files to the proper places in the Setup.html if they exist, that is it's only job. This means that the returned string to the Controller expects a {CSS} amd {JS} tag to be available.

	echo new Page(AllPages,function($p) {
		return 'All Pages Will Output This.';
	});

## 6. Understand the View class

The View class is straight forward, there is a file located in the VIEWROOT folder with a name.html, such as Setup.html. The same file name, with a different extension exists in CSSROOT and JSROOT as well. If you had a Setup.html file, you could also 'optionally' create a Setup.css and Setup.js files at the same path, but in the different ROOT folders within FILEROOT folder.

The View Class, when turned into a string fires the __toString method on the class, which outputs the ->Template string on the View class

	echo new View('Setup',[
	    	// Key => Val Pairs refer to a {Key} tag inside the View File, which is replaced with the Val.
	        'Base' => URL,
	        'Title' => 'AMVC: New Project',
	        'Keywords' => 'visual-optics,mvc',
	        'Description' => 'This is a new project amvc description',
	        'FavIcon' => 'imgs/favicon.png',
	    
	        // Outputs a string to the {Body} tag located in the Setup.html, Setup.css, and Setup.js views.
	        'Body' => 'All Pages Will Output This Inside Body Element, Or Where Ever The {Body} Tag Is Located.'
    	]);
    
Note that the above will not output our CSS & JS files automatically, which makes you wonder why the above was mentioned?

## 7. Return our Setup View to the Page Controller

Here we will return the same Setup View above for all pages using our AMVC View Class to a closure function of the Page Controller, this allows the ability to easily copy the root setup of any html document over to other projects, and keep that setup file up to date and normalized across all project pages. From here you may choose to setup your across project template as well, but I advise against it, as that is what the next step is for.

Note that at this point, if the Setup.html file has a {CSS} and {JS} tags, then the Page class will automatically load in the CSSROOT.'Setup.css' and JSROOT.'Setup.js' files inline in the html.

	    echo new Page(AllPages,function($p) {
	        return new View('Setup',[
	            'Base' => URL,
	            'Title' => 'AMVC: New Project',
	            'Keywords' => 'visual-optics,mvc',
	            'Description' => 'This is a new project amvc description',
	            'FavIcon' => '',
	            
	            // Create a Body Controller and connect it to any set of pages
	            'Body' => 'All Pages Will Output This Inside Body Element, Or Where Ever The {Body} Tag Is Located.'
	        ]);
	    });

## 8. What is a Controller? How do I use it?

Just like in the above, where we use the Page Controller class which extends off the Core Controller, we do the same in this example. Except, a normal Controller will not automatically output the CSS and JS to the returned View, it 'will' how-ever pass along the view data as it is returned to it, keeping it in order as it was setup. The Core Controller expects only that the returned Class has a Template, or a __toString method which will output the Template of the class. The View Class sets its final data up in the ->Template variable. In the end, the Controller is expected to return an object that can be further manipulated, before being turned into a string elsewhere in the code.

The below example will only fire the body controller when we are Routed to an AboutUs page, which according to the above code would be about-us, about-casey, or about-chris pages. You can see the repetetive syntax starts to show Page Controller <- View <- KeyTags <- AnyValueOr Controller <- View <- KeyTags <- Value / Controller <- View <- Tags <- ...

	echo new Page(AllPages,function($p) {
		return new View('Setup',[
			'Base' => URL,
			'Title' => 'AMVC: New Project',
			'Keywords' => 'visual-optics,mvc',
			'Description' => 'This is a new project amvc description',
			'FavIcon' => '',
			
			// Create a Body Controller and connect it to any set of pages
			'Body' => new Controller(AboutUs,function($p) {
				/* Handle any logic that needs to happen on pages with this body controller */
				$Header = 'Header Data';
				$Body = 'Body Data';
				$Footer = 'Footer Data';
				
				/* Return our Body view */
				return new View('Body',[
					'Header' => $Header,
					'Body' => $Body,
					'Footer' => $Footer,
				]);
			}),
		]);
	});
    
## 9. What are 'Controller Actions' aka the Action Switch? 
    
Controller Actions, or the Action Switch simply refers to a switch on the Controller function which allows the controller to handle different 'actions'. An example of this, adding onto out above code, would be as follows..

        echo new Page(AllPages,function($p) {
		/* Handle any logic that needs to happen on all pages at the Setup level */
		
		/* Return our Setup view */
		return new View('Setup',[
			'Base' => URL,
			'Title' => 'AMVC: New Project',
			'Keywords' => 'visual-optics,mvc',
			'Description' => 'This is a new project amvc description',
			'FavIcon' => '',
			
			// Create a Body Controller and connect it to any set of pages
			'Body' => new Controller(AllPages,function($p) {
				/* Handle any logic that needs to happen on pages with this body controller */
				$Header = 'Header Data';
				$Footer = 'Footer Data';
				
				/* Switch based on an action, in this case the PAGE, this is the Body Controller's possible 'Actions' it can take, and is not limited to the PAGE, but can refer to items part of the $p, $_POST, $_GET, or anything that refers to an interaction from the user with this controller */
				switch(PAGE) {
					case Index:
						$Body = 'Index Header';
					break;
					case AboutUs:
						$Body = 'About Us Header';
					break;
				}
				
				/* Return our Body view */
				return new View('Body',[
					'Header' => $Header,
					'Body' => $Body,
					'Footer' => $Footer,
				]);
			}),
		]);
	});
	
	
## 10. This is too easy, is that all I have to do to make dynamic pages come to life?

The short answer is... No.. but dont be scurred, only 1 more step to success. These pages can be dynamic if the data is being given to the controller in some way, and since the Controller's only objective (like an xbox controller) is to figure out what buttons the user is pressing, and what events are happening, and pass that data off to the Game Console, and tell it to do stuff to update, before showing the user the next frame of the game. This 'dynamic changing of data' is the last step in order to have mastered this simple MVC. And you 'dont' want to do this in your Controller, as the controller doesn't like to work, it just likes to have its buttons pushed and tell about it.

This last step refers to the M in the accronym, and is the Model. A Core Model in the AMVC extends off the PHP PDO Class, and provides 2 new methods. One method is DB() which automatically sets up your PDO and connects it to the DB, and is intended to be used with the classes that extend the Core Model class. The other added method is the 'AddToView()' method, who's first parameter takes a View Path, and automagically applies the ASSOC Array to the View, replacing the {Key} tags with the Values given by the Model->Results. Any key => value array the model class adds to its ->Results will be applied to a view with the AddToView method. This allows the Model to interact with databases, api's, or anywhere where data is stored and needs to be updated, and the controller simply tells the Model to do its job.

Lets take a look at this in action..

	namespace Model\Select;
	class Employee extends \Model {
		public $Name = 'Full Name';
		public $Result = null;
		
		public function __construct($Param) {
			$this->Name = $Param['Name'];
			
			// The model handles its main business, notice the use of the ->DB() method extended from the Core Model
			$this->Result = $this->DB()->prepare('
				SELECT * FROM `Employee` WHERE `Name` = :Name
			')->execute($Param)->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	// The Model will store data on the ->Result, which will be available as $Employee->Result in the below case
	$Employee = new Model\Select\Employee([
		'Name' => 'Casey Childers',
	]);
	
	prent($Employee->Result);
	
	// Once this method is called, the $Employee variable then has the ->Result as well as a processed ->Template
	echo $Employee->AddToView('Page/Employee');


## 11. You can see a Controller with a fully working Action set, connecting to Model's which add their data to the views and load the view css and js before returning it all back to the user on echo.


	$Header = new Controller(AboutUs,function($p) { // About Us Controller (would allow about-us, about-casey, or about-chris
		// Do some controller logic here, which is just processing the user events
		$AboutWho = str_replace('about-','',$p[0]); // Grab the us, casey, or chris from the $p array (page array)
		$AboutPage = ''; // start a string
		
		// Switch based on the different available actions for this controller
		switch($AboutWho) { // switch based on our who action
			case 'casey':
				// return a model to a variable, we pass an array, the model returns a ->Result
				$Employee = new Model\Select\Employee([
					'Name' => 'Casey Childers',
				]);
				
				$AboutPage = $Employee->AddToView('Page/Employee');
			break;
			case 'chris':
				$Employee = new Model\Select\Employee([
					'Name' => 'Chris Childers',
				]);
				$AboutPage = $Employee->AddToView('Page/Employee');
			break;
			default:
				$Employees = new Model\Select\Employees();
				foreach($Employees->Result as $i => $Employee) {
					$AboutPage .= new View('Page/Employees/Employee',$Employee);
				}
				
				$AboutPage = new View('Page/AboutUs',[
					'Employees' => $AboutPage
				]);
			break;
		}
	
		// Return a string, the controller will output it
		return $AboutPage;
	});
	
	echo $Header;

## 12. If we put this all together in a simple project, you can see this all working. I have gone ahead and also added a few Post / Get Ajax examples in the Page Controller Action Switch, which return any type of data before the setup view is reached.

	echo new Page(AllPages,function($p) {
		/* Handle any logic that needs to happen on all pages before ajax calls */
		
		/* Return Ajax Post / Get Pages BEFORE Setup, means Setup is Never Reached for Ajax Calls */
		switch(PAGE) {
			// Ajax Post Example
			case PostPage:
				$Employee = new Model\Select\Employee([
					'Name' => $_POST['Name'],
				]);
									
				return json_encode($Employee->Result);
			break;
			
			// Ajax Get Example
			case GetPage:
				switch($p[1]) { // This can be off the page array or $_GET, or what ever...
					case 'something': return 'data is something'; break;
					case 'stuff': return 'data is stuff'; break;
				}
			break;
		}
		
		/* Handle any logic that needs to happen on all pages at the Setup level */
		$Title = 'AMVC: New Project';
		
		/* Return our Setup view */
		return new View('Setup',[
			'Base' => URL,
			'Title' => $Title,
			'Keywords' => 'visual-optics,mvc',
			'Description' => 'This is a new project amvc description',
			'FavIcon' => '',
			
			// Create a Body Controller and connect it to any set of pages
			'Body' => new Controller(AllPages,function($p) {
				/* Handle any logic that needs to happen on pages with this body controller */
				$Header = 'Header Data';
				$Footer = 'Footer Data';
				
				/* Switch based on Page, this is the Body Controller's possible 'Actions' */
				switch(PAGE) {
					case Index:
						$Body = new Controller(Index,function($p) { // Index Controller
							$Example = '... Hi!';
						
							// Return our main index view, using above data to output into the views
							return new View('Page/Index',[
								'Body' => $Example,
							]);
						});
					break;
					case AboutUs:
						$Body = new Controller(AboutUs,function($p) { // About Us Controller
							// Do some controller logic here, which is just processing the user events
							$AboutWho = str_replace('about-','',$p[0]);
							$AboutPage = '';
							
							// Switch based on the different available actions for this controller
							switch($AboutWho) {
								case 'casey':
									// return a model to the controller
									$Employee = new Model\Select\Employee([
										'Name' => 'Casey Childers',
									]);
									
									$AboutPage = $Employee->AddToView('Page/Employee');
								break;
								case 'chris':
									$Employee = new Model\Select\Employee([
										'Name' => 'Chris Childers',
									]);
									$AboutPage = $Employee->AddToView('Page/Employee');
								break;
								default:
									$Employees = new Model\Select\Employees();
									foreach($Employees->Result as $i => $Employee) {
										$AboutPage .= new View('Page/Employees/Employee',$Employee);
									}
									
									$AboutPage = new View('Page/AboutUs',[
										'Employees' => $AboutPage
									]);
								break;
							}

							// Return a string, the controller will output it
							return $AboutPage;
						});
						
					break;
				}
				
				/* Return our Body view */
				return new View('Body',[
					'Header' => $Header,
					'Body' => $Body,
					'Footer' => $Footer,
				]);
			}),
		]);
	});

# Core Files

## Aes.php && AesCtr.php

For Encryption & Encrypted Page Purposes

https://github.com/VOMVC/VOMVC/blob/master/include/Core/Aes.php
https://github.com/VOMVC/VOMVC/blob/master/include/Core/AesCtr.php

## Core.php

Some Core Functionality Needed On Most If Not On All Projects

https://github.com/VOMVC/VOMVC/blob/master/include/Core/Core.php
	
## Controller.php

Main AMVC Controller

https://github.com/VOMVC/VOMVC/blob/master/include/Core/Controller.php
	
## Page.php

Main AMVC Page Controller

https://github.com/VOMVC/VOMVC/blob/master/include/Core/Page.php
	
## Model.php

Main AMVC Model

https://github.com/VOMVC/VOMVC/blob/master/include/Core/Model.php
	
## View.php

Main AMVC View

https://github.com/VOMVC/VOMVC/blob/master/include/Core/View.php
