<?php
App::import('Component', 'MobileKit.Discriminant');

class GpsFormHelper extends AppHelper {
	var $helpers = array('Form');
	var $mobile = null;
	
	function beforeRender()
	{
		$this->mobile = DiscriminantComponent::getData();
	}
}
?>