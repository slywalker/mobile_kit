<?php
/*
 * http://code.google.com/p/emoji4unicode/
 * emoji4unicodeのemoji4unicode.xmlファイルをVendorsに設置
*/
uses('Xml');

class Emoji extends MobileKitAppModel {
	var $name = 'Emoji';
	var $useTable = false;

	function __construct()
	{
		parent::__construct();
		
		$this->data = unserialize(Cache::read('MobileKitEmoji'));
		if (!$this->data) {
			$this->importXml();
		}
	}
	
	function importXml()
	{
		$file = dirname(dirname(__FILE__)).'/vendors/emoji4unicode.xml';
		$xml = new Xml($file);
		$this->data = Set::reverse($xml);
		Cache::write('MobileKitEmoji', serialize($this->data));
		return;
	}
	
	function findCode($conditions = array())
	{
		$condition = null;
		foreach ($conditions as $key=>$code) {
			$condition = $key.'='.$code;
		}
		$result = Set::extract(
			'/Emoji4unicode/Category/Subcategory/E['.$condition.']',
			$this->data);
		if ($result) {
			return $result[0]['E'];
		}
		return array();
	}
}

?>