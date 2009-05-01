<?php
class MobileComponent extends Object {
	var $userAgent = null;
	var $carrier = null;
	var $uid = null;
	var $displayWidth = null;
	var $displayHeight = null;
	
	var $_agents = array(
		'docomo' => array('/^DoCoMo.+$/'),
		'kddi' => array(
			'/^KDDI.+UP.Browser.+$/',
			'/^UP.Browser.+$/',
		),
		'softbank' => array('/^(SoftBank|Vodafone|J-PHONE|MOT-C).+$/'),
		'iphone' => array('/^Mozilla.+iPhone.+$/'),
		'willcom' => array(
			'/^Mozilla.+(WILLCOM|DDIPOCKET|MobilePhone).+$/',
			'/^PDXGW.+$/',
		),
		'emobile' => array('/^emobile.+$/'),
	);

	function __construct() {
		$this->userAgent = env('HTTP_USER_AGENT');
		$this->setCarrier();
		$this->setUid();
		$this->setDisplay();
	}

	function setCarrier($userAgent = null)
	{
		if (is_null($userAgent)) {
			$userAgent = $this->userAgent;
		}
		if (is_null($userAgent)) {
			return $this->carrier = null;
		}

		foreach ($this->_agents as $carrier=>$regix) {
			foreach ($regix as $reg) {
				if (preg_match($reg, $userAgent)) {
					return $this->carrier = $carrier;
				}
			}
		}
		return $this->carrier = null;
	}
	
	// http://xxxxxxxx?guid=ON DoCoMo
	function setUid($carrier = null)
	{
		if (is_null($carrier)) {
			$carrier = $this->carrier;
		}
		if (is_null($carrier)) {
			return $this->uid = null;
		}

		$uid = null;
		if ($carrier === 'docomo') {
			$uid = env('HTTP_X_DCMGUID');
		}
		elseif ($carrier === 'softbank') {
			$uid = env('HTTP_X_JPHONE_UID');
		}
		elseif ($carrier === 'kddi') {
			$uid = env('HTTP_X_UP_SUBNO');
		}
		return $this->uid = $uid;
	}
	
	function setDisplay($carrier = null)
	{
		if (is_null($carrier)) {
			$carrier = $this->carrier;
		}
		if (is_null($carrier)) {
			return $this->uid = null;
		}

		$uid = null;
		if ($carrier === 'docomo') {
			$display = array();
		}
		elseif ($carrier === 'softbank') {
			$display = env('HTTP_X_JPHONE_DISPLAY');
			$display = explode("*", $display);
		}
		elseif ($carrier === 'kddi') {
			$display = env('HTTP_X_UP_DEVCAP_SCREENPIXELS');
			$display = explode(",", $display);
		}
		if (2 === count($display)) {
			$this->displayWidth = $display[0];
			$this->displayHeight = $display[1];
		} else {
			$this->displayWidth = 240;
			$this->displayHeight = 320;
		}
		return $display;
	}
}
?>