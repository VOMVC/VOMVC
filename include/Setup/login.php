<?php
/** /Setup/index.php Controller is Included By Setup.php
	Provides           (string)$BodyHeader  - {BodyHeader} Tag Replace Data
	Provides & Expects (string)$BodyPage    - {BodyPage} Tag Replace Data
	Provides           (string)$BodyFooter  - {BodyFooter} Tag Replace Data
	Provides           (string)$PAGE        - Control & View File Name
**/

// Index PAGE Controller
new Controller(Login,function($p) use(&$BodyPage,$PAGE) { // <--- Note the use()
	if(isset($_POST['Submit'])) {
		switch($_POST['Submit']) {
			case 'Login':
				prent($_POST);
				break;
		}
	}
	
	// Return our main index view, using above data to output into the views
	$BodyPage = new View('Body/Page/'.$PAGE,[]);
});
?>
