<?php
class RenderComponent extends Object {
	var $components = array('MobileKit.Mobile');
	var $layoutPath = 'mobile';
	var $viewPath = 'mobile';

	function initialize(&$controller)
	{
		if ($this->isMobile() && $controller->data) {
			mb_convert_variables('UTF-8', 'SJIS-win', $controller->data);
		}
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
			$controller->output =
				mb_convert_encoding($controller->output, 'SJIS-win', 'UTF-8');
			header("Content-type: application/xhtml+xml");
		}
	}
	
	function _hankaku($output)
	{
		// 連続する半角スペースを半角スペース１としてカウント
		$output = preg_replace('!\s+!', " ", $output);
		// 全角を半角に変換
		$output = mb_convert_kana($output, 'rank');
		return $output;
	}
	
	function isMobile()
	{
		return !is_null($this->Mobile->carrier);
	}

	function getCarrier()
	{
		return $this->Mobile->carrier;
	}

	function getSerial()
	{
		return $this->Mobile->serial;
	}
}
?>