<?php
class DiscriminantComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	
	var $agents = array(
		'docomo' => array('DoCoMo'),
		'ezweb' => array('UP.Browser'),
		'softbank' => array('SoftBank', 'Vodafone', 'J-PHONE'),
		'willcom' => array('WILLCOM', 'DDIPOCKET'),
	);

	function initialize(&$controller)
	{
		$this->userAgent = env('HTTP_USER_AGENT');
		$this->_discrim();
	}

	function isMobile()
	{
		if ($this->carrier) {
			return true;
		}
		return false;
	}

	function _discrim()
	{
		foreach ($this->agents as $carrier=>$agents) {
			foreach ($agents as $agent) {
				if (strpos($this->userAgent, $agent) !== false) {
					$this->carrier = $carrier;
					return true;
				}
			}
		}
		return false;
	}
}
?>