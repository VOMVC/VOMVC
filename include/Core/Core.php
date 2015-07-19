<?php

/** This function provides an easy way to see random debug data on the fly.
  */
function prent($var,$return=false) {
	$prent = '<pre>'.print_r($var,true).'</pre>';
	if($return) {
		return $prent; 
	} else {
		echo $prent;
	}
}

/** The autoloader makes creating and using a class a breeze
 */
spl_autoload_register(function($Class) {
	if(!strstr($Class,'\\')) {
		$Class = 'Core\\'.$Class;
	}
	$File = FILEROOT . str_replace('\\', '/', $Class) . '.php';
	
	if(file_exists($File)) {
		prent($File);
		include_once($File);
	}
});

/** FIRST, Handle URL to E and V and P Define, Variable & Function
 *  @updated 021520140608PM - This allows for
 *  switch(PAGE) {
 *  	case p($regexp): break;
 *  }, also
 *  switch(EXPERIMENT){
 *  	case 2: switch(VERSION) {
 *  		case 1: break;
 *  		default: case 2: break;
 *  	} break;
 *  }
 **/

$_p = isset($_GET['p']) ? $_GET['p'] : '/'; // Handle our page
unset($_GET['p']); // Get page out of our $_GET
$p = explode('/',$_p); // Explode our page by slash, and store in $p
$p['?'] = $_GET; // Set $p['?'] to the remaining $_GET items
// Handle Posts
define('ost','ost');
define('enc','enc');
$p[ost] = [];
$p[enc] = []; // Encrypted Page Data
// Handle Submit Specifically
if(isset($_POST['Submit'])) {
    $ubmit = $_POST['Submit'];
    //unset($_POST['Submit']);
    $p[ost][$ubmit] = $_POST; // This will let forms check if isset on $post and handle their own data or not right away
    foreach($_POST as $id=>$data) {
        $p[ost][$id] = $data; // This will give any ID that passed data with the Submit, the ability to qucikyl work with it
    }
    unset($ubmit);
}
// Handle Experiments
$_t = (isset($p[0]) && is_numeric($p[0]))?$p[0]:null;
define("E",$_t);
// And Versions of Experiments
$_t = (isset($p[1]) && is_numeric($p[1]) && is_numeric($_t))?$p[1]:null;
define("V",$_t);
if(is_numeric(E) && is_numeric(V)) {
    $_p = substr($_p,4);
} else if(is_numeric(E) && !is_numeric(V)) {
    $_p = substr($_p,2);
}
unset($_t); // Unset non-needed variables for future usability
define("EXPERIMENT",E);
define("VERSION",V);

// Turn all php errors into exception...
set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

/**
 * This powerful function helps in many places, it handles checking a regexp against the page (PAGE) url, and returns the url, or $return, or false
 * @param (string) $regexp // regexp to check against the page url
 * @param (anything) $return // (including closures, which the $return is then passed the regexp)
 * @author Casey Childers
*/
function p($regexp,$return=null) {
	if(is_string($regexp)) {
    	try {
            $pm = preg_match($regexp, PAGE, $m)?PAGE:null;
        } catch (ErrorException $e) {
            prent('Caught exception: ',  $e->getMessage(), "\n");
        }
		
	} else if(is_bool($regexp)) {
		$m = $regexp;
		$pm = $regexp?PAGE:null;
	}

	return ($return && $pm)?is_closure($return)?$return($m):$return:$pm;
}

// Updated 070120141137AM: This allows the system to reset itself each load, so that if the developer saves and updates across the board, and you visit / refresh a page, you will start fresh & not have the obfuscated data from the "previous session" (still the same session just being erased and re-written at the HTML drawstate [this may need to change to specifically happen once for developer load, and force all users to use the same, not sure yet, check on this later])
// Updated 070620140227AM: Moved this above the encryption data, so that the encrypted pages can adjust the session data without this resetting it.

if(isset($_SESSION)) {
    unset($_SESSION['_ReplaceTags_']);
    unset($_SESSION['_DontReplaceTags_']);
    unset($_SESSION['_UndoTags_']);
}

/**
 * This allows us to do Encrypted Pages
 */
$ENC = json_decode(AesCtr::decrypt(str_replace(array('%252F','%255C','%252B'), array('%2F','%5C','%2B'), $_p),EncryptionPassword,EncryptionLength), true);
if($ENC) {
    if(isset($ENC['p'])) {
        $_p = '';
        foreach($ENC['p'] as $k=>$c) {
            if(is_numeric($k)) {
                $_p .= '/'.$c;
            }
        }
        $_p = substr($_p,1);

        $post = $p[ost];
        $get = $p['?'];

        $p = $ENC['p']; // Replace $p data with our encrypted data, else use our current $p;

        $p[ost] = $post;
        $p['?'] = $get;
    } else { // Updated 031620150247PM: If an EncryptedPage comes in without p set, we assume they want the index page
        $_p = '/';
    }

    if(isset($ENC['session_id'])) { // Updated 032620150524PM: Change this from an inline if inside the function call, as it would break underscore control panel redirect doing it the other way
        session_id($ENC['session_id']); // Make sure we have the same session from when the encrypted page was created (that is, before the form was posted), if not use the current session
    }
    $GLOBALS = array_merge_recursive_distinct($GLOBALS,isset($ENC['GLOBALS'])?$ENC['GLOBALS']:[]);
}

define("PAGE",($_p != '')?$_p:'/');unset($_p);	// Then define our normalized PAGE

// If the session is not started, lets start it
if(!(session_status() === PHP_SESSION_ACTIVE)) {
    if(isset($_COOKIE['session_id'])) {
        session_id($_COOKIE['session_id']);
    }

    session_start();
    if(!isset($_COOKIE['session_id'])) {
        setcookie('session_id', session_id(), 0, '/', strstr(HOST,'.'));
    }
}

/** is_closure() is a helper function to solve the is_callable() issue on closures
 *
 *	@param (closure) $t // should be a closure if intending on this function returning true
 *	@return (boolean) // true if $t is a closure type of function, else false
 *  @author Casey Childers
 **/
function is_closure($t) {
	return is_object($t) && ($t instanceof Closure);
}


    
/**
 * This function will return a URL Encoded, AES Encrypted, JSON Encoded, Array String that can be passed to the Underscore domain as a page, and the system will understand it (look above for the code)
 * @param string $array represents an array with the following "special keys": 'p'=>[],'session_id'=>'','GLOBALS'=>[], which will define how the encrypted page will turn out
 */
function EncryptedPage($array) {
    return str_replace(array('%2F','%5C','%2B'),array('%252F','%255C','%252B'), urlencode(AesCtr::encrypt(json_encode($array), PASSWORD, ENCLENGTH)));
}

function defines($a) {foreach($a as $k => $c) {if(!defined($k)) {define($k,$c);}}}
?>
