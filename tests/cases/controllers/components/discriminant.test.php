<?php
App::import('Component', 'MobileKit.Discriminant');

class TestDiscriminantComponent extends DiscriminantComponent {
}

class DiscriminantTestCase extends CakeTestCase {
	
	function setUp()
	{
		$this->Controller =& ClassRegistry::init('Controller');
		$this->Controller->Component =& ClassRegistry::init('Component');
		$this->Controller->Discriminant =&
			ClassRegistry::init('TestDiscriminantComponent', 'Component');
	}
	
	function testDiscrim()
	{
		$userAgent = 'DoCoMo/2.0 P903i';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'docomo');

		$userAgent = 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'iphone');

		$userAgent = 'Vodafone/1.0/V903SH/SHJ001[/Serial] Browser/UP.Browser/7.0.2.1 Profile/MIDP-2.0 ';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'softbank');

		$userAgent = 'MOT-C980/80.2F.2E. MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'softbank');

		$userAgent = 'J-PHONE/4.3/V604T[/Serial] TS/2.00 Profile/MIDP-1.0 Configuration/CLDC-1.0　Ext-Profile/JSCL-1.3.2';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'softbank');

		$userAgent = 'KDDI-SA31 UP.Browser/6.2.0.7.3.129 (GUI) MMP/2.0';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'ezweb');

		$userAgent = 'Mozilla/3.0(WILLCOM;SANYO/WX310SA/2;1/1/C128) NetFront/3.3';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'willcom');

		$userAgent = 'Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'willcom');

		$userAgent = 'PDXGW/1.0 (TX=8;TY=6;GX=96;GY=64;C=G2;G=B2;GI=0)';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'willcom');

		$userAgent = 'emobile/1.0.0 (H11T; like Gecko; Wireless) NetFront/3.4 ';
		$this->Controller->Discriminant->userAgent = $userAgent;
		$this->assertTrue($this->Controller->Discriminant->_discrim());
		$carrier = $this->Controller->Discriminant->carrier;
		$this->assertEqual($carrier, 'emobile');
	}
}
?>