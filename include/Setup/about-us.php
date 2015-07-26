<?php
/** /Setup/about-us.php Controller is Included By Setup.php
	Provides           (string)$BodyHeader  - {BodyHeader} Tag Replace Data
	Provides & Expects (string)$BodyPage    - {BodyPage} Tag Replace Data
	Provides           (string)$BodyFooter  - {BodyFooter} Tag Replace Data
	Provides           (string)$PAGE        - Control & View File Name
**/

// AboutUs PAGE Controller
new Controller(AboutUs,function($p) use(&$BodyPage,$PAGE) { // <--- Note the use()
	$AboutWho = str_replace('about-','',$p[0]);
	
	switch($AboutWho) {
		case 'casey':
			$Employee = new Model\Select\Employee([
				'Name' => 'Casey Childers',
			]);
			
			$BodyPage = $Employee->AddToView('Page/Employee');
		break;
		case 'chris':
			$Employee = new Model\Select\Employee([
				'Name' => 'Chris Childers',
			]);
			
			$BodyPage = $Employee->AddToView('Page/Employee');
		break;
		default:
			$Employees = new Model\Select\Employees();
			
			$EmplyoeeSection = '';
			foreach($Employees->Result as $i => $Employee) {
				$EmplyoeeSection .= new View('Page/AboutUs/Employee',$Employee);
			}

			$BodyPage = new View('Page/AboutUs',[
				'Employees' => $EmplyoeeSection
			]);
		break;
	}
});
?>
