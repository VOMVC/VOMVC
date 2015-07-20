<?php
/**
  * Main AMVC View
  *
  * Extend your views off this view.
  *
  * @author  Casey Childers <casey@axori.com>
  *
  * @version 1.0
  *
  * @param string   $file  The file to pull from the view folder
  * @param string	$folder The view folder to use, default is VIEWROOT
  * @param string	$ext The extension used inside the view folder
  * @param bool		$dev Enable comments or not on this view
  * @param string	$stag Start Comment Tag
  * @param string	$etag End Comment Tag
  *
  * @example Page Controller
  *	return new View('Body',[
  *		'Header' => $Header,
  *		'Body' => $Body,
  *		'Footer' => $Footer,
  *	]);
  */
class View {
	public $CSS = '';
	public $JS = '';
	
	public function __construct($file, $data = null, $folder = VIEWROOT, $ext = 'html', $dev = DEV,$stag = '<!-- ',$etag = ' -->') {
		//global $Obfuscation;
		if($folder == VIEWROOT) {
			$this->CSS = new View($file,$data,CSSROOT,'css');
			$this->CSS = !isset($this->CSS->Template)?'':$this->CSS->Template;
			$this->JS = new View($file,$data,JSROOT,'js');
			$this->JS = !isset($this->JS->Template)?'':$this->JS->Template;
		}
		
		foreach($data as $k => $c) {
			if(is_object($c)) {
				$this->CSS .= $c->CSS;
				$this->JS .= $c->JS;
			}
		}
		
		
		$file = "$folder$file.$ext";
		 
		if(!file_exists($file)) {
			if($folder == VIEWROOT) {
				prent('ERROR: View File '.$file.' Does Not Exist.'); 
			}
			return false;
		}
		
		$tpl = '';
		$tpl .= !DEV?'':$stag.'START /'.$file.$etag;
		$tpl .= file_get_contents($file);
		$tpl .= !DEV?'':$stag.'END /'.$file.$etag;

		$data = self::KeysToTags($data);
		
		/*foreach($GLOBALS as $key => $val) {
			if(substr($key,0,5) == 'VIEW_') {
				global $$key;
				$tpl = $$key->ReplaceTags($tpl,$file);
			}
		}*/
		
		$tpl = $this->ReplacePTags($tpl,$file);

		// Replace our normal tags
		$tpl = self::ViewReplaceTags($tpl,$data);

		// Replace all Obfuscation tags in this view
		if(class_exists('Obfuscation')) {	
			$tpl = Obfuscation::ReplaceTags($tpl);
		}
		
		$this->Template = $tpl;
	}
	
	public function __toString() {
		try {
			if(isset($this->Template)) {
				return (string)$this->Template;
			}
		} catch(Exception $e) {
			die('Error: ' . $e->getMessage());
		}
		return '';
	}

	public function ReplacePTags($string,$view) {

		// Find all {P_Tags} and \{P_Tags}, backslashing a tag will simply replace it with itself minus the slash on output
		preg_match_all('/({P_[a-zA-Z0-9 _&,\.\/:\?\-]+?})|(\\\\{P_[a-zA-Z0-9 _&,\.\/:\?\-]+?})/',$string,$matches);

		$specials = [['&',',','.','/',':','?'],['And','_','.','/','_','_']];
		$ReplaceTags = array_flip(array_filter(array_values(array_unique($matches[1]))));
		$DontReplaceTags = array_flip(array_filter(array_values(array_unique($matches[2]))));

		// For each ReplaceTags
		foreach($ReplaceTags as $tag => &$num) {

			if(!is_string($num)) {
				$page = str_replace(['{P_','}',' '],['','','-'],$tag);

				$page = str_replace($specials[0],$specials[1],$page);

				if(defined($page)) {
					$num = constant($page)?'{_Page_}':'';
				} else {
					$num = '';
				}
			}
		}

		// For each DontReplaceTags (slash before the tag)
		foreach($DontReplaceTags as $k=>&$c) {
			$c = substr($k,1); // Remove the slash before the tag and return the tag itself
		}

		// Handle replacing our "ReplaceTags" with their new outputs, which should be letters, or the inner {_Tag_} details if DEBUG is on
		$Keys = array_keys($ReplaceTags);
		$Vals = array_values($ReplaceTags);
		$string = str_replace($Keys,$Vals,$string);

		// Handle replacing our "DontReplaceTags" (tags with slashes at the start) with their non-slashed counterparts
		$Keys = array_keys($DontReplaceTags);
		$Vals = array_values($DontReplaceTags);
		$string = str_replace($Keys,$Vals,$string);

		return $string;
	}

	public function RemoveDATATags($string,$type) {

		// Find all {'.$type.'_Tags} and \{'.$type.'_Tags}, backslashing a tag will simply replace it with itself minus the slash on output
		preg_match_all('/({'.$type.'_[a-zA-Z0-9 _&,\.\/:\?\-]+?})|(\\\\{'.$type.'_[a-zA-Z0-9 _&,\.\/:\?\-]+?})/',$string,$matches);

		$specials = [['&',',','.','/',':','?'],['And','_','.','/','_','_']];
		$ReplaceTags = array_flip(array_filter(array_values(array_unique($matches[1]))));
		$DontReplaceTags = array_flip(array_filter(array_values(array_unique($matches[2]))));

		// For each ReplaceTags
		foreach($ReplaceTags as $tag => &$num) {
			$num = '';
		}

		// For each DontReplaceTags (slash before the tag)
		foreach($DontReplaceTags as $k=>&$c) {
			$c = substr($k,1); // Remove the slash before the tag and return the tag itself
		}

		// Handle replacing our "ReplaceTags" with their new outputs, which should be letters, or the inner {_Tag_} details if DEBUG is on
		$Keys = array_keys($ReplaceTags);
		$Vals = array_values($ReplaceTags);
		$string = str_replace($Keys,$Vals,$string);

		// Handle replacing our "DontReplaceTags" (tags with slashes at the start) with their non-slashed counterparts
		$Keys = array_keys($DontReplaceTags);
		$Vals = array_values($DontReplaceTags);
		$string = str_replace($Keys,$Vals,$string);

		return $string;
	}

	static function ViewReplaceTags($tpl,$data) {
		if($data) {
			foreach($data AS $r => $k)
				if($k !== true && $k !== false) {
					$tpl = str_replace($r, $k, $tpl);
				}
		}
		return $tpl;
	}

	static function KeysToTags($array=null) {
		if(is_array($array)) {
			foreach($array as $k=>$c) {
				$array['{'.$k.'}'] = $c;
				unset($array[$k]);
			}
		}
		return $array;
	}
}
?>
