<?php
App::import('Component', 'MobileKit.Discriminant');

class GpsFormHelper extends AppHelper {
	var $helpers = array('Form');
	var $mobile = null;
	
	function beforeRender()
	{
		$this->mobile =& ClassRegistry::init(
			'MobileKit.DiscriminantComponent', 'Component')->getData();
	}
}
?>