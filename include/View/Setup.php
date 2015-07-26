<?php
namespace View;
class Setup extends \View {
	public $HTTPCSS;
	public $HTTPJS;
	
	public function __construct($file, $data = null) {
		$this->HTTPCSS = $data['HTTPCSS'];
		$this->HTTPJS = $data['HTTPJS'];
		
		$data['HTTPCSS'] = $this->HTTPCSS($data['HTTPCSS']);
		$data['HTTPJS'] = $this->HTTPJS($data['HTTPJS']);
		
		parent::__construct($file, $data);
	}
	
	public function HTTPCSS($files) {
		$buffer = "\r\n\t\t";
		
		if(isset($files)) {
			foreach($files as $file) {
				if(stristr($file,'http')) {
					$buffer .= '<link rel="stylesheet" type="text/css" href="'.$file.'" />'."\r\n\t\t";
				} else {
					$buffer .= '<link rel="stylesheet" type="text/css" href="'.URL.'/css/'.$file.'" />'."\r\n\t\t";
				}
			}
		}
		
		return $buffer;
	}
	
	public function HTTPJS($files) {
		$buffer = '';
		
		if(isset($files)) {
			foreach($files as $file) {
				if(stristr($file,'http')) {
					$buffer .= '<script language="JavaScript" type="text/javascript" src="'.$file.'"></script>'."\r\n\t\t";
				} else {
					$buffer .= '<script language="JavaScript" type="text/javascript" src="'.URL.'/js/'.$file.'"></script>'."\r\n\t\t";
				}
			}
		}
		
		return $buffer;
	}
}
?>
