<?php
/**
 * HTML_CSS_Mobile.php
 *
 * @author Daichi Kamemoto <daikame@gmail.com>
 */
/**
 * The MIT License
 *
 * Copyright (c) 2008 Daichi Kamemoto <daikame@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once 'lib/selectorToXPath.php';
require_once 'HTML/CSS.php';

/**
 * HTML_CSS_Mobile Mobile向けに外部参照/<style>タグののCSSをインラインのstyle要素に埋め込む
 *   PerlのHTML::DoCoMoCSS
 *   ( http://search.cpan.org/~tokuhirom/HTML-DoCoMoCSS-0.01/lib/HTML/DoCoMoCSS.pm )
 *   のPHP移殖版
 * 
 * @package 
 * @version 0.1.5
 * @copyright 2008 yudoufu
 * @author Daichi Kamemoto(a.k.a yudoufu) <daikame@gmail.com> 
 * @license MIT License
 */
class HTML_CSS_Mobile
{
	private $base_dir = './';
	private $mode = 'transit'; // mode-> transit: Exception抑制 strict: 例外発生
	private $dom;
	private $dom_xpath;
	private $css_files = array();
	private $html_css; 

	/**
	 * getInstance インスタンスを取得
	 * 
	 * @return class
	 */
	public static function getInstance()
	{
		return new HTML_CSS_Mobile();
	}

	/**
	 * setBaseDir CSSのベースディレクトリ(通常はDocumentRoot)を設定
	 * 
	 * @param string $base_dir 
	 * @return class
	 */
	public function setBaseDir($base_dir)
	{
		$this->base_dir = $base_dir;
		return $this;
	}

	/**
	 * setMode CSSのチェックモードを設定
	 * #TODO: もっとしっかりモード実装
	 * 
	 * @param string $mode 
	 * @return class
	 */
	public function setMode($mode)
	{
		$this->mode = $mode;
		return $this;
	}

	/**
	 * addCSSFiles CSSのファイルをプログラム側から読み込む
	 * 
	 * @param array $files 
	 * @return class
	 */
	public function addCSSFiles($files)
	{
		if (!is_array($files))
		{
			$files = array($files);
		}

		foreach ($files as $key => $file)
		{
			if (substr($file, 0, 1) != '/')
			{
				$file = $this->base_dir . $file;
			}

			if (file_exists($file) && is_file($file))
			{
				array_push($this->css_files, $file);
			}
		}

		return $this;
	}

	/**
	 * apply CSSをインライン化
	 *
	 * @param  string $document 変換を行うHTML文書
	 * @param  string $base_dir CSSのベースディレクトリ(setBaseDirより優先)
	 * @return string           変換されたHTML
	 */
	public function apply($document, $base_dir = '')
	{
		/****************************************
		 * 前処理
		 ****************************************/
		if ($base_dir)
		{
			$this->base_dir = $base_dir;
		}

		// loadHTML/saveHTMLのバグに対応。XML宣言の一時退避
		$declaration = '';
		if (preg_match('/^<\?xml\s[^>]+?\?>\s*/', $document, $e))
		{
			$declaration = $e[0];
			$document = substr($document, strlen($declaration));
		}

		// 文字参照をエスケープ
		$document = preg_replace('/&(#(?:\d+|x[0-9a-fA-F]+)|[A-Za-z0-9]+);/', 'HTMLCSSINLINERESCAPE%$1%::::::::', $document);

		// 機種依存文字がエラーになる問題を回避するため、UTF-8に変換して処理
		$doc_encoding = mb_detect_encoding($document, 'sjis-win, UTF-8, eucjp-win');

		switch (strtolower($doc_encoding))
		{
			case 'sjis-win':
				$html_encoding = 'Shift_JIS';
				break;
			case 'eucjp-win':
				$html_encoding = 'EUC-JP';
				break;
			default:
				$html_encoding = '';
				break;
		}

		/*
		if ($doc_encoding != 'UTF-8')
		{
			$document = str_replace(array('UTF-8', $html_encoding), array('@####UTF8####@', 'UTF-8'), $document);
			$document = mb_convert_encoding($document, 'UTF-8', $doc_encoding);
		}
		*/
		/****************************************
		 * 本処理
		 ****************************************/

		// XHTMLをパース
		$this->dom = new DOMDocument();
		$this->dom->loadHTML($document);

		$this->dom_xpath = new DOMXPath($this->dom);

		$this->loadCSS();

		// CSSをインライン化
		$css = $this->html_css->toArray();
		$add_style = array();
		foreach ($css as $selector => $style)
		{
			// 疑似要素は退避。@ルールはスルー(selectorToXPath的にバグでやすい)
			if (strpos($selector, '@') !== false) continue;
			if (strpos($selector, ':') !== false)
			{
				$add_style[] = $selector . '{' . $this->html_css->toInline($selector) . '}';
				continue;
			}

			$xpath = selectorToXPath::toXPath($selector);
			$elements = $this->dom_xpath->query($xpath);

			if ($elements->length == 0) continue;
			// inlineにするCSS文を構成(toInline($selector)だとh2, h3 などでうまくいかない問題があったため)
			$inline_style = '';
			foreach ($style as $k => $v)
			{
				$inline_style .= $k . ':' . $v . ';';
			}
			foreach ($elements as $element)
			{
				if ($attr_style = $element->attributes->getNamedItem('style'))
				{
					// style要素が存在する場合は前方追記
					#TODO: できれば、重複回避もしたい。少しロジックがまどろっこしい順序になってしまうのだが。。。
					$attr_style->nodeValue = $inline_style . $attr_style->nodeValue;
				}
				else
				{
					// style要素が存在しない場合は追加
					$element->setAttribute('style', $inline_style);
				}
			}
		}

		// 疑似クラスを<style>タグとして追加
		if (!empty($add_style))
		{
			$new_style = implode(PHP_EOL, $add_style);
			$new_style = str_replace(']]>', ']]]><![CDATA[]>', $new_style);
			$new_style = implode(PHP_EOL, array('<![CDATA[', $new_style, ']]>'));

			$head = $this->dom_xpath->query('//head');
			$new_style_node = new DOMElement('style', $new_style);
			$head->item(0)->appendChild($new_style_node)->setAttribute('type', 'text/css');
		}

		$result = $this->dom->saveHTML();

		/****************************************
		 * 後処理
		 ****************************************/

		// 文字コードを元に戻す
		if ($doc_encoding != 'UTF-8')
		{
			$result = mb_convert_encoding($result, $doc_encoding, 'UTF-8');
			$result = str_replace(array('UTF-8', '@####UTF8####@'), array($html_encoding, 'UTF-8'), $result);
		}

		// エスケープしていた参照を復元
		$result = preg_replace('/HTMLCSSINLINERESCAPE%(#(?:\d+|x[0-9a-fA-F]+)|[A-Za-z0-9]+)%::::::::/', '&$1;', $result);

		// 退避したXML宣言を復元
		if (!empty($declaration))
		{
			$result = $declaration . $result;
		}

		return $result;
	}

	/**
	 * loadCSS 各所で指定されているCSSファイルを読み込み、HTML_CSSのオブジェクト配列として格納する
	 * 
	 * @return void
	 */
	private function loadCSS()
	{
		// 外部参照のCSSファイルを抽出する
		$nodes = $this->dom_xpath->query('//link[@rel="stylesheet" or @type="text/css"] | //style[@type="text/css"]');

		foreach ($nodes as $node)
		{
			// CSSをパース
			#TODO: @importのサポート
			if ($node->tagName == 'link' && $href = $node->attributes->getNamedItem('href'))
			{
				// linkタグの場合
				/*
				if (!file_exists($this->base_dir . $href->nodeValue))
				{
					if ($this->mode !== 'strict') continue;
					throw new UnexpectedValueException('ERROR: ' . $this->base_dir . $href->nodeValue . ' file does not exist');
				}
				*/

				$css_string = file_get_contents($this->base_dir . $href->nodeValue);
			}
			else if ($node->tagName == 'style')
			{
				// styleタグの場合
				$css_string = $node->nodeValue;
			}

			$this->_loadCSS($css_string);

			// 読み込み終わったノードを削除。親ノードが取れない場合はスルー
			if ($parent = $node->parentNode)
			{
				$parent->removeChild($node);
			}

		}

		// メソッドで指定したCSSファイルを読み込む
		if (is_array($this->css_files))
		{
			foreach ($this->css_files as $file)
			{
				$css_string = '';
				if (substr($file, 0, 1) != '/')
				{
					$file = $this->base_dir . $file;
				}

				if (file_exists($file) && is_file($file))
				{
					$css_string = file_get_contents($file);
					$this->_loadCSS($css_string);
				}
			}
		}
	}

	/**
	 * _loadCSS 
	 * 
	 * @param string $css_string 
	 * @return void
	 */
	private function _loadCSS($css_string)
	{
		// 文字コードをDOM利用のためにUTF-8化
		/*
		$css_encoding = mb_detect_encoding($css_string, 'UTF-8, eucjp-win, sjis-win, iso-2022-jp');
		if ($css_encoding != 'UTF-8')
		{
			$css_string = mb_convert_encoding($css_string, 'UTF-8', $css_encoding);
		}
		*/

		if (is_null($this->html_css))
		{
			$this->html_css = new HTML_CSS();
		}

		// CSSをクラスへ追加
		$css_error = $this->html_css->parseString($css_string);
		if ($this->mode == 'strict' && $css_error)
		{
			throw new RuntimeException('ERROR: css parse error');
		}
	}

	/**
	 * atImportLoad
	 *
	 * @param HTML_CSS instance
	 * @return void
	 */
	private function atImportLoad($html_css)
	{
		#TODO: importの取得が上手く出来ない？
	}
}
