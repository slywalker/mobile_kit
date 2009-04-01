<?php
App::import('Component', 'MobileKit.Mobile');

class GpsFormHelper extends AppHelper {
	var $helpers = array('Form');
	var $Mobile = null;
	
	function beforeRender()
	{
		$this->Mobile =& ClassRegistry::init(
			'MobileKit.MobileComponent', 'Component')
	}
}
?>