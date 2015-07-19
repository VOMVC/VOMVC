# AMVC - Axori MVC

This is a straight forward MVC that follows the below flow, and is extendable on all levels quite easily.


                                             [  MODEL (O)  ]
                                           /   /¯         \  ¯\
           [RESULT (A,R), FILE (R,O)]¯¯¯¯¯/_  /¯[TPL (A)]-_\   \¯¯¯¯¯[ACTION (O), FILE (O), DATA (R,O)]
                                    [VIEW (O)]<--------->[CONTROLLER (R)]
                                     /¯    ¯\            /   /¯
                              [CSS (O)]    [JS (O)]     /_  /¯¯¯¯¯[ROUTER (R)]              
                                                    [USER (A,R)]

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
	  define('EncryptionPassword','SierraBankruptcy Admin');
	  define('EncryptionLength','256');
	  
	  
## 3. Include the AMVC Core
    include(COREROOT.'Core.php');
    
    
## 4. Define our dynamic pages, 'URL Routing', p function returns true if the regexp is satisfied against PAGE define
    defines([
  		'AllPages'	=> p('/^.*?$/'),
  		
  		'Index'		=> p('/^\/?$/'),
  		'AboutUs'	=> p('/^about-(us|casey|chris)?\/?$/'),
  		
  		'PostPage'	=> p('/^post\/(stuff|something)?\/?$/'),
  		'GetPage'	=> p('/^get\/(stuff|something)?\/?$/'),
	  ]);
	  
	  
## 5. Setup our Page

This will handle connecting all 'allowed' pages to this Page controller. In this example, we allow all pages to pass through.

    echo new Page(AllPages,function($p) {
      return 'All Pages Will Output This.';
    });
