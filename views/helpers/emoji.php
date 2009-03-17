<?php
class EmojiHelper extends AppHelper {
	var $helpers = array('Html');
	var $mobile = array();
	var $Emoji = null;
	
	function beforeRender()
	{
		$Discriminant = new DiscriminantComponent;
		$this->mobile =& ClassRegistry::init(
			'MobileKit.DiscriminantComponent', 'Component')->getData();
		$this->Emoji =& ClassRegistry::init('MobileKit.Emoji');
	}
	
	function _emoji($emoji)
	{
		if ($emoji) {
			if ($this->mobile['carrier']) {
				return sprintf('&#x%s;', $emoji[$this->mobile['carrier']]);
			} else {
				// PC用画像を用意しなければ・・・
				// $this->Html->image('/mobile_kit/img/hoge.gif');
				//debug('PCだよ');
			}
		}
		return null;
	}
	
	function docomo($unicode)
	{
		$emoji = $this->Emoji->findCode(array('docomo'=>$unicode));
		return $this->_emoji($emoji);
	}
	
	function kddi($unicode)
	{
		$emoji = $this->Emoji->findCode(array('kddi'=>$unicode));
		return $this->_emoji($emoji);
	}

	function softbank($unicode)
	{
		$emoji = $this->Emoji->findCode(array('softbank'=>$unicode));
		return $this->_emoji($emoji);
	}
}