<?php
class DiscriminantComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	var $serial = null;
	
	var $agents = array(
		'docomo' => '/^DoCoMo.+$/',
		'ezweb' => '/^KDDI.+UP.Browser.+$/',
		'softbank' => '/^(SoftBank|Vodafone|J-PHONE|MOT-C).+$/',
		'iphone' => '/^Mozilla.+iPhone.+$/',
		'willcom' => array(
			'/^Mozilla.+(WILLCOM|DDIPOCKET|MobilePhone).+$/',
			'/^PDXGW.+$/',
		),
		'emobile' => '/^emobile.+$/',
	);

	function __construct()
	{
		parent::__construct();
		
		$this->userAgent = env('HTTP_USER_AGENT');
		$this->_discrim();
		$this->_getSerial();
	}
	
	function getData()
	{
		return array(
			'carrier'=>$this->carrier,
			'serial'=>$this->serial,
		);
	}

	function _discrim()
	{
		foreach ($this->agents as $carrier=>$regix) {
			if (is_array($regix)) {
				foreach ($regix as $reg) {
					if (preg_match($reg, $this->userAgent)) {
						$this->carrier = $carrier;
						return true;
					}
				}
			}
			else {
				if (preg_match($regix, $this->userAgent)) {
					$this->carrier = $carrier;
					return true;
				}
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