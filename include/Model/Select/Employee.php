<?php 
/**
  * Select Employee Example
  *
  * Use this as an example of how to extend off a Model
  *
  * @author  Casey Childers <casey@axori.com>
  *
  * @version 1.0
  *
  * @param array $Param  This is a list of params by key => value pairs that this model will use, we
  *                      do an array so data can easily be passed around.
  *
  * @example Page Controller
  *	return new View('Body',[
  *		'Header' => $Header,
  *		'Body' => $Body,
  *		'Footer' => $Footer,
  *	]);
  */
namespace Model\Select;
class Employee extends \Model {
	public $Name = 'Full Name';
	public $Title = 'Employee Title';
	public $Result = null;
	
	public function __construct($Param) {
		
		$this->Name = $Param['Name'];
		$Param['Title'] = 'Blah';
		
		// This is where the model handles its main business
		$this->Result = $this->DB()->prepare('
			SELECT * FROM `Employee` WHERE `Name` = :Name
		')->execute($Param)->fetchAll(PDO::FETCH_ASSOC);
		
		$this->Title = $this->Result['Title'];
	}
}
?>
