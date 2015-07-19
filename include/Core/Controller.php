<?php
/**
  * Main AMVC Controller
  *
  * Extend your controllers off this controller.
  *
  * @author  Casey Childers <casey@axori.com>
  *
  * @version 1.0
  *
  * @param bool    			$IsRoute  Pass True to this param to trigger it. Use the defined Route's to route pages.
  * @param string|closure	$Function This is the name of the function on the object to trigger, or a closure function
  *
  * @example Page Controller
  *	define('AboutUs','/^about-us\/?$/')
  *	$Page = new Controller(AboutUs,function($p) {
  *		return 'About Us';
  *	});
  * 
  * @example Permission Controller
  *	$UserForm = new Controller($HasPermissionTo['Do Something'],function($p){
  *		return 'User Form';
  *	});
  */ 
class Controller {
	public $CSS = '';
	public $JS = '';
	
	public function __construct($IsRoute,$Function = null) {
		global $p;
		
		if($IsRoute) {
			if(is_string($Function)) {
				$this->Result = $this->$Function($p);
			}
			if(is_closure($Function)) {
				$this->Result = $Function($p);
			}
			
			if(isset($this->Result) && !is_string($this->Result)) {
				$this->CSS .= $this->Result->CSS;
				$this->JS .= $this->Result->JS;
			}
		}
	}
	
	public function __toString() {
		try {
			if(isset($this->Result)) {
				return (string)$this->Result;
			}
		} catch(Exception $e) {
			die('Error: ' . $e->getMessage());
		}
		return '';
	}
}
?>
