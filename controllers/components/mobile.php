<?php
class MobileComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	var $serial = null;
	
	var $_agents = array(
		'docomo' => '/^DoCoMo.+$/',
		'kddi' => '/(^KDDI.+UP.Browser.+$|^UP.Browser.+$)/',
		'softbank' => '/^(SoftBank|Vodafone|J-PHONE|MOT-C).+$/',
		'iphone' => '/^Mozilla.+iPhone.+$/',
		'willcom' => array(
			'/^Mozilla.+(WILLCOM|DDIPOCKET|MobilePhone).+$/',
			'/^PDXGW.+$/',
		),
		'emobile' => '/^emobile.+$/',
	);

	function initialize(&$controller)
	{
		$this->userAgent = env('HTTP_USER_AGENT');
		$this->setCarrier($this->userAgent);
		$this->setSerial();
	}
	
	function setCarrier($userAgent = null)
	{
		if (is_null($userAgent)) {
			$userAgent = $this->userAgent = env('HTTP_USER_AGENT');
		}

		foreach ($this->_agents as $carrier=>$regix) {
			if (is_array($regix)) {
				foreach ($regix as $reg) {
					if (preg_match($reg, $userAgent)) {
						return $this->carrier = $carrier;
					}
				}
			}
			else {
				if (preg_match($regix, $userAgent)) {
					return $this->carrier = $carrier;
				}
			}
		}
		return $this->carrier = null;
	}
	
	// http://xxxxxxxx?guid=ON DoCoMo
	function setSerial()
	{
		$serial = null;
		if ($this->carrier === 'docomo') {
			$serial = env('HTTP_X_UP_SUBNO');
		}
		elseif ($this->carrier === 'softbank') {
			if (preg_match("/\/(SN[a-zA-Z0-9]+)\s/",
				$this->userAgent, $match)){
				$serial = $match[1];
			}
		}
		elseif ($this->carrier === 'kddi') {
			$serial = env('HTTP_X_UP_SUBNO');
		}
		return $this->serial = $serial;
	}
}
?>