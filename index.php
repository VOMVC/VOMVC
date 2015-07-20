<?php
	/** Handle Required Root Defines **/
	define('FILEROOT','Include/');
	define('COREROOT',FILEROOT.'Core/');
	define('VIEWROOT',FILEROOT.'View/');
	define('CSSROOT',FILEROOT.'CSS/');
	define('JSROOT',FILEROOT.'JS/');

	/** Handle Other Required Defines **/
	session_start();
	define('DEV',true);
	define('DateTimeZone','America/Los_Angeles'); date_default_timezone_set(DateTimeZone);
	define('ROOT', getcwd());
	define('PROJECT', str_replace('/index.php', '', $_SERVER['PHP_SELF']));
	define("URL", "http://".$_SERVER['HTTP_HOST'].PROJECT);
	
	define('DBH','localhost');
	define('DBN','devposda_db');
	define('DBU','devposda_db');
	define('DBP','ListenDazesGrabsClefs88');
	
	define('EncryptionPassword','SierraBankruptcy Admin');
	define('EncryptionLength','256');

	
	/** Include the AMVC Core **/
	include(COREROOT.'Core.php');
	
	/** Define our dynamic pages, 'URL Routing', p function returns true if the regexp is satisfied against PAGE define **/
	defines([
		'AllPages'	=> p('/^.*?$/'),
		
		'Index'		=> p('/^\/?$/'),
		'AboutUs'	=> p('/^about-(us|casey|chris)?\/?$/'),
		
		'PostPage'	=> p('/^post\/(stuff|something)?\/?$/'),
		'GetPage'	=> p('/^get\/(stuff|something)?\/?$/'),
	]);
	
	/* This class will create the globals $HasPermissionTo, and $NoPermissionTo */
	/*new Model\Select\Permissions([
		'GroupsTable' => 'Groups',
		'GroupPermissionsTable' => 'GroupPermissions',
		'PermissionsTable' => 'Permissions',
		
		'GroupID' => '-1',//!isset($User->LoggedInAs['GroupID'])?'-1':$User->LoggedInAs['GroupID'],
	]);*/
	
	/** Handle our Project **/
	echo new Page(AllPages,function($p) {
		/* Handle any logic that needs to happen on all pages at the Setup level */
		
		/* Return our Setup view */
		return new View('Setup',[
			'Base' => URL,
			'Title' => 'AMVC: New Project',
			'Keywords' => 'axori,mvc',
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
	
?>
