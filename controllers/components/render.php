<?php
App::import('Vendor', 'Mobile.toInlineCSSDoCoMo',
	array('file' => 'toInlineCSSDoCoMo.php'));

class RenderComponent extends Object {
	var $components = array('MobileKit.Discriminant');
	var $layoutPath = 'mobile';
	var $viewPath = 'mobile';
	
	function beforeRender(&$controller)
	{
		if ($this->Discriminant->isMobile()) {
			$controller->layoutPath = $this->layoutPath;
			if ($controller->viewPath !== 'errors') {
				$controller->viewPath =
					$controller->viewPath.DS.$this->viewPath;
			}
		}
	}
	
	function shutdown(&$controller)
	{
		if ($this->Discriminant->isMobile()) {
			//$controller->output = $this->emoji($controller->output);
			$controller->output = $this->_hankaku($controller->output);
			if ($this->Discriminant->carrier === 'docomo') {
				$controller->output
					= $this->_toInlineCSSDoCoMo($controller->output);
			}
			header('Content-Type: application/xhtml+xml; charset=UTF-8');
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
	
	function isMobile()
	{
		return $this->Discriminant->isMobile();
	}

	function getCarrier()
	{
		return $this->Discriminant->getCarrier();
	}

	function getSerial()
	{
		return $this->Discriminant->getSerial();
	}
	
	function emoji($output)
	{
		// 絵文字変換
		App::import(
			'Vendor',
			'MobilePictogramConverter',
			array(
				'file' =>
				 'MobilePictogramConverter/MobilePictogramConverter.php'
			)
		);
		$Mpc = MobilePictogramConverter::factory(
			$output,
			MPC_FROM_FOMA,
			MPC_FROM_CHARSET_UTF8,
			MPC_FROM_OPTION_WEB
		);
		$Mpc->setImagePath(Router::url('/img'));

		if ($this->mobile->isDoCoMo()) {
			$to = MPC_TO_FOMA;
			$option = MPC_TO_OPTION_RAW;
		} elseif ($this->mobile->isSoftBank()) {
			$to = MPC_TO_SOFTBANK;
			$option = MPC_TO_OPTION_WEB;
		} elseif ($this->mobile->isEZweb()) {
			$to = MPC_TO_EZWEB;
			$option = MPC_TO_OPTION_WEB;
		} else {
			$to = str_replace('MPC_', '', strtoupper(get_class($Mpc)));
			$option = MPC_TO_OPTION_IMG;
		}
		return $Mpc->Convert($to, $option, MPC_TO_CHARSET_UTF8);
	}
}
?>