<?php
/**
  * Main AMVC Page Controller
  *
  * Extend your pages off this page controller. All this does is takes the CSS and JS
  * from across the Project Page, and outputs it onto the template at {CSS} and {JS}
  *
  * @author  Casey Childers <casey@axori.com>
  *
  * @version 1.0
  *
  * @param bool    			$IsRoute  Pass True to this param to trigger it. Use the defined Route's to route pages.
  * @param string|closure	$Function This is the name of the function on the object to trigger, or a closure function
  *
  * @example Refer To index.php For Complete Example
  */
class Page extends Controller {
	public function __toString() {
		try {
			
			if(isset($this->Result) && !is_string($this->Result)) {
				return View::ViewReplaceTags($this->Result->Template,View::KeysToTags([
					//'HTTPCSS' => '',//[],
					'CSS' => '<style>'.$this->CSS.'</style>',
					
					//'HTTPJS' => '',//[],
					'JS' => '<script type="text/javascript">'.$this->JS.'</script>',
				]));
			} else {
				return $this->Result;
			}
		} catch(Exception $e) {
			die('Error: ' . $e->getMessage());
		}
		return '';
	}
}
?>
