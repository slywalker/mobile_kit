<?php
App::import('Component', 'MobileKit.Render');

class TestRenderComponent extends RenderComponent {
}

class RenderTestCase extends CakeTestCase {
	
	function setUp()
	{
		$this->Controller =& ClassRegistry::init('Controller');
		$this->Controller->Component =& ClassRegistry::init('Component');
		$this->Controller->Render =&
			ClassRegistry::init('TestRenderComponent', 'Component');
	}
}
