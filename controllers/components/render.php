<?php
App::import('Vendor', 'MobileKit.toInlineCSSDoCoMo',
	array('file' => 'toInlineCSSDoCoMo.php'));

class RenderComponent extends Object {
	var $components = array('MobileKit.Discriminant');
	var $layoutPath = 'mobile';
	var $viewPath = 'mobile';
	var $encoding = null;
	var $mobile = array();

	function initialize(&$controller)
	{
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
			if ($this->mobile['carrier'] === 'docomo') {
				$controller->output
					= $this->_toInlineCSSDoCoMo($controller->output);
			}
			
			if (!is_null($this->encoding)
			&& $this->encoding !== Configure::read('App.encoding')) {
				$controller->output
					= mb_convert_encoding(
						$controller->output,
						$this->encoding,
						Configure::read('App.encoding')
					);
			}
			header('Content-Type: application/xhtml+xml; charset='
				.$this->encoding);
		}
	}
	
	function _hankaku($output)
	{
		// 連続する半角スペースを半角スペース１としてカウント
		$output = preg_replace('!\s+!', " ", $output);
		// 全角を半角に変換
		return mb_convert_kana($output, 'rank');
	}
	
	function _toInlineCSSDoCoMo($output)
	{
		return toInlineCSSDoCoMo::getInstance()
			->setBaseDir('http://'.env('HTTP_HOST'))->apply($output);
	}
	
	function setEncoding($encoding)
	{
		return $this->encoding = $encoding;
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