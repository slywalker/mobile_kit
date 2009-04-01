<?php
App::import('Component', 'MobileKit.Render');
App::import('Component', 'MobileKit.Mobile');

class TestRenderComponent extends RenderComponent {
}

class RenderTestCase extends CakeTestCase {
	
	function setUp()
	{
		$this->Controller =& ClassRegistry::init('Controller');
		$this->Controller->Component =& ClassRegistry::init('Component');
		$this->Controller->Render =&
			ClassRegistry::init('TestRenderComponent', 'Component');
		$this->Controller->Render->Mobile =&
			ClassRegistry::init('MobileComponent', 'Component');
	}
	
	function testIsMobile()
	{
		$userAgent = 'Mozilla/5.0';
		$this->Controller->Render->Mobile->setCarrier($userAgent);
		$this->assertFalse($this->Controller->Render->isMobile());

		$userAgent = 'DoCoMo/2.0 P903i';
		$this->Controller->Render->Mobile->setCarrier($userAgent);
		$this->assertTrue($this->Controller->Render->isMobile());

		$userAgent = 'KDDI-SA31 UP.Browser/6.2.0.7.3.129 (GUI) MMP/2.0';
		$this->Controller->Render->Mobile->setCarrier($userAgent);
		$this->assertTrue($this->Controller->Render->isMobile());

		$userAgent = 'Vodafone/1.0/V903SH/SHJ001[/Serial] Browser/UP.Browser/7.0.2.1 Profile/MIDP-2.0';
		$this->Controller->Render->Mobile->setCarrier($userAgent);
		$this->assertTrue($this->Controller->Render->isMobile());

		$userAgent = 'IE';
		$this->Controller->Render->Mobile->setCarrier($userAgent);
		$this->assertFalse($this->Controller->Render->isMobile());
	}
	
	function test_ParseCss()
	{
		$string = '
		body {
			background-color: #fff;
			color: #000;
		}
		a:hover {
			color: #000;
		}
		h1 span {
			font-size: xx-small;
		}
		h2 {
			font-weight: normal;
			margin: 0 0 0 0;
			padding: 0 0 0 0;
			font-size: xx-small;
			color: #fff;
		}
		#foo {
			font-size: xx-small;
		}
		.bar {
			font-size: xx-small;
		}
		';
		$result = $this->Controller->Render->_parseCss($string);
		
		$expect = array(
			'body' => 'background-color:#fff;color:#000;',
			'a:hover' => 'color:#000;',
			'h1 span' => 'font-size:xx-small;',
			'h2' => 'font-weight:normal;margin:0 0 0 0;padding:0 0 0 0;font-size:xx-small;color:#fff;',
			'#foo' => 'font-size:xx-small;',
			'.bar' => 'font-size:xx-small;',
		);
		$this->assertEqual($expect, $result); 
	}
	
	function testParseHtml()
	{
		$html = '
		<?xml version="1.0" encoding="Shift_JIS"?>
		<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
		<head>
			<meta http-equiv="content-type" content="application/xhtml+xml; charset=Shift_JIS" />
			<meta content="width=320, minimum-scale=0.5" name="viewport" />
			<title>Test</title>
			<link href="/cake/fseek/css/cake.generic.css" type="text/css" rel="stylesheet">
		</head>
		<body>
			<h1>
				<span>
					風を求めて。風俗GPS検索
				</span>
			</h1>
			<span>
				Copyrights(C)2009<br />
				fseek.jp<br />
				All rights reserved
			</span>
		</body>
		</html>
		';
		$result = $this->Controller->Render->inlineCss($html);
	}
}
