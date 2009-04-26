<?php
App::import('Vendor', 'MobileKit.simple_html_dom',
	array('file'=>'simplehtmldom/simple_html_dom.php'));

class RenderComponent extends Object {
	var $components = array('MobileKit.Mobile');
	var $layoutPath = 'mobile';
	var $viewPath = 'mobile';
	/**
	 * 強制的にCSSをインライン化
	 *
	 * @var boolen
	 */
	var $inlineCss = false;
	/**
	 * 強制的にSJIS出力
	 *
	 * @var boolen
	 */
	var $encodingSjis = false;

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
		if ($this->encodingSjis || $this->isMobile()) {
			if ($this->inlineCss || $this->Mobile->carrier === 'docomo') {
				$controller->output = $this->inlineCss($controller->output);
			}
			$controller->output = $this->_hankaku($controller->output);
			$controller->output =
				mb_convert_encoding($controller->output, 'SJIS-win', 'UTF-8');
			if ($this->isMobile()) {
				header("Content-type: application/xhtml+xml");
			}
		}
	}
	
	function _hankaku($output)
	{
		// 連続する半角スペースを半角スペース１としてカウント
		//$output = preg_replace('!\s+!', ' ', $output);
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

	function inlineCss($html)
	{
		// パースしやすいように無駄な空白や改行を取り除く
		$html = preg_replace('!\s+!', ' ', trim($html));
		// headからCSSファイルを取り出す
		preg_match('/<head>.*<\/head>/', $html, $match);
		$dom = new simple_html_dom;
		$dom->load($match[0], true);
		$css = '';
		foreach ($dom->find('link[type=text/css]') as $e) {
			if (is_object($e)) {
				$url = 'http://'.env('HTTP_HOST').$e->href;
				$css .= file_get_contents($url);
			}
		}
		$dom->clear();
		// CSSのパース
		$styles = $this->_parseCss($css);
		// a:*をヘッダ内に格納
		$css = '<style type="text/css"> <![CDATA['."\n";
		$links = array('a:link', 'a:hover', 'a:focus', 'a:visited');
		foreach ($links as $link) {
			if (isset($styles[$link])) {
				$css .= $link.'{'.$styles[$link].'}'."\n";
				unset($styles[$link]);
			}
		}
		$css .= ']]> </style>';
		$html = preg_replace('/<\/head>/', $css.' </head>', $html);
		
		// bodyを取り出す
		preg_match('/<body[^>]*>.*<\/body>/', $html, $match);
		$dom = new simple_html_dom;
		$dom->load($match[0], true);
		// インライン化
		foreach ($styles as $element=>$style) {
			foreach ($dom->find($element) as $e) {
				if (is_object($e)) {
					if (isset($e->attr['style'])) {
						$style .= str_replace('"', '', $e->attr['style']);
					}
					$e->attr =
						array_merge($e->attr, array('style'=>'"'.$style.'"'));
				}
			}
		}
		// html再構成
		$body = $dom->save();
		$dom->clear();
		$html = preg_replace("/<body>.*<\/body>/", $body, $html);
		$html = preg_replace("/> /", ">\n", $html);
		$html = preg_replace("/ </", "\n<", $html);
		return $html;
	}

	function _parseCss($string)
	{
		$string = preg_replace('!\s+!', ' ', $string);
		$string = preg_replace('/\/\*(?:(?!\*\/).)*\*\//', '', $string);
		$string = trim($string);
		$string = preg_replace('!\s*{\s*!', '{', $string);
		$string = preg_replace('!\s*:\s*!', ':', $string);
		$string = preg_replace('!\s*;\s*!', ';', $string);
		$string = preg_replace('!\s*}\s*!', '}', $string);
		preg_match_all('/([^{]+){([^}]+)}/', $string, $matchs);
		$results = array();
		foreach ($matchs[0] as $key=>$match) {
			$results[$matchs[1][$key]] = $matchs[2][$key];
		}
		return $results;
	}
}
?>