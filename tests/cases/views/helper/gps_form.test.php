<?php
App::import('Helper', array('MobileKit.GpsForm'));
App::import('Core', array('View', 'Controller'));

Mock::generate('Helper', 'MockBackendHelper', array('testMethod'));

class GpsFormHelperTestCase extends CakeTestCase {
/**
 * setUp
 *
 * @return void
 **/
	function startTest() {
		Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
		Router::parse('/');
		
		$this->GpsForm =& new GpsFormHelper;
		$this->GpsForm->MockBackend = new MockBackendHelper();
		
		$this->Controller =& ClassRegistry::init('Controller');
		if (isset($this->_debug)) {
			Configure::write('debug', $this->_debug);
		}
	}

/**
 * start Case - switch view paths
 *
 * @return void
 **/
	function startCase() {
		$this->_viewPaths = Configure::read('viewPaths');
		Configure::write('viewPaths', array(
			TEST_CAKE_CORE_INCLUDE_PATH . 'tests' . DS . 'test_app' . DS . 'views'. DS,
			APP . 'plugins' . DS . 'debug_kit' . DS . 'views'. DS, 
			ROOT . DS . LIBS . 'view' . DS
		));
		$this->_debug = Configure::read('debug');
	}
}
?>