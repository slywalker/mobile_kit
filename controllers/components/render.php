<?php
App::import('Vendor', 'MobileKit.selectorToXPath',
	array('file' => 'selectorToXPath.php'));

class RenderComponent extends Object {
	var $Controller = null;
	var $components = array('MobileKit.Discriminant');
	var $layoutPath = 'mobile';
	var $viewPath = 'mobile';
	var $mobile = array();
	var $xhtml = null;

	function initialize(&$controller)
	{
		$this->Controller = $controller;
		$this->mobile = $this->Discriminant->getData();
	}
	
	function beforeRender(&$controller)
	{
		if ($this->isMobile()) {
			$controller->layoutPath = $this->layoutPath;
			if ($controller->viewPath !== 'errors') {
				$controller->viewPath =
					$controller->viewPath.DS.$this->viewPath;
			}
		}
	}
	
	function shutdown(&$controller)
	{
		if ($this->isMobile()) {
			$controller->output = $this->_hankaku($controller->output);
			header("Content-type: application/xhtml+xml");
		}
	}
	
	function _hankaku($output)
	{
		// 連続する半角スペースを半角スペース１としてカウント
		$output = preg_replace('!\s+!', " ", $output);
		// 全角を半角に変換
		return mb_convert_kana($output, 'rank');
	}
	
	function isMobile()
	{
		return !is_null($this->mobile['carrier']);
	}

	function getCarrier()
	{
		return $this->mobile['carrier'];
	}

	function getSerial()
	{
		return $this->mobile['serial'];
	}
}
?>