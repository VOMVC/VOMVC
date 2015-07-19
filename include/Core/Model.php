<?php
/**
  * Main AMVC Model
  *
  * Extend your models off this model.
  *
  * @author  Casey Childers <casey@axori.com>
  *
  * @version 1.0
  *
  * @param bool    			$IsRoute  Pass True to this param to trigger it. Use the defined Route's to route pages.
  * @param string|closure	$Function This is the name of the function on the object to trigger, or a closure function
  *
  * @example Extend a Model
  *	namespace Model\Select;
  *	class Employee extends \Model {
  *		public $Name = 'Full Name';
  *		public $Result = null;
  *		
  *		public function __construct($Param) {
  *			
  *			$this->Name = $Param['Name'];
  *
  *			// This is where the model handles its main business
  *			$this->Result = $this->DB()->prepare('
  *				SELECT * FROM `Employee` WHERE `Name` = :Name
  *			')->execute($Param)->fetchAll(PDO::FETCH_ASSOC);
  *		}
  *	}
  * 
  * @example Use A Model
  *	$Employee = new Model\Select\Employee([
  *		'Name' => 'Casey Childers',
  *	]);
  *	$AboutPage = $Employee->AddToView('Page/Employee');
  * 
  */
class Model extends PDO {
	public $CSS = '';
	public $JS = '';
	public $Template = '';
	
	public function DB() {
		try {
			$Model = new Model("mysql:dbname=".DBN.";host=".DBH, DBU, DBP);
			$Model->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			die('Error: ' . $e->getMessage());
		}
		
		return $Model;
	}
	
	public function AddToView($View) {
		// Before determining the view and output
		$this->Template = new View($View,$this->Result);
		$this->CSS .= $this->Template->CSS;
		$this->JS .= $this->Template->JS;
		return $this->Template;
	}
}


?>
