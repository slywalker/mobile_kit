<?php
class DiscriminantComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	
	var $agents = array(
		'docomo' => '/^DoCoMo.+$/',
		'ezweb' => '/^.+UP.Browser.+$/',
		'softbank' => '/^(SoftBank|Vodafone|J-PHONE).+$/',
		'willcom' => '/^Mozilla.+(WILLCOM|DDIPOCKET|MobilePhone).+$/',
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
		foreach ($this->agents as $carrier=>$regix) {
				if (preg_match($regix, $this->userAgent)) {
					$this->carrier = $carrier;
					return true;
				}
		}
		return false;
	}
}
?>