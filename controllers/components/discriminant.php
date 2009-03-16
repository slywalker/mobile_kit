<?php
class DiscriminantComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	var $serial = null;
	
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
		$this->_getSerial();
	}

	function isMobile()
	{
		if ($this->carrier) {
			return true;
		}
		return false;
	}
	
	function getCarrier()
	{
		return $this->carrier;
	}
	
	function getSerial()
	{
		return $this->serial;
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
	
	// http://xxxxxxxx?guid=ON DoCoMo
	function _getSerial()
	{
		$serial = null;
		if ($this->carrier === 'docomo') {
			$serial = env('HTTP_X_UP_SUBNO');
		}
		elseif($this->carrier === 'softbank'){
			if (preg_match("/\/(SN[a-zA-Z0-9]+)\s/",
				$this->userAgent, $match)){
				$serial = $match[1];
			}
		}
		elseif ($this->carrier === 'ezweb') {
			$serial = env('HTTP_X_UP_SUBNO');
		}
		return $this->serial = $serial;
	}
}
?>