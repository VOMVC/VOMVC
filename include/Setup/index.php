<?php
/** /Setup/index.php Controller is Included By Setup.php
	Provides           (string)$BodyHeader  - {BodyHeader} Tag Replace Data
	Provides & Expects (string)$BodyPage    - {BodyPage} Tag Replace Data
	Provides           (string)$BodyFooter  - {BodyFooter} Tag Replace Data
	Provides           (string)$PAGE        - Control & View File Name
**/

// Index PAGE Controller
new Controller(Index,function($p) use(&$BodyPage,$PAGE) { // <--- Note the use()
	$Example = '... Index Page..';

	// Return our main index view, using above data to output into the views
	$Page = new View('Body/'.$PAGE,[
		'Data' => $Example,
	]);
});
?>
