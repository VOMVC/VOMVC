<?php
	/** Handle Required Root Defines **/
	define('FILEROOT','include/');
	define('COREROOT',FILEROOT.'Core/');
	define('HTMLROOT',FILEROOT.'HTML/');
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
	define('DBN','Name');
	define('DBU','User');
	define('DBP','Pass');
	
	define('EncryptionPassword','EncryptionPassword');
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
	
	/** Setup our Project Code **/
	include(FILEROOT.'Setup.php');	
?>
