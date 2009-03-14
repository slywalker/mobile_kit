<?php
class MbFormHelper extends AppHelper {
	var $helpers = array('Form');

	var $elements = array(
		'hiragana' => array(
			'istyle' => '1',
			'format' => '*M',
			'mode' => 'hiragana',
			'style' => '-wap-input-format:&quot;*&lt;ja:h&gt;&quot;;-wap-input-format:*M;',
		),
		'hankakukana' => array(
			'istyle' => '2',
			'format' => '*M',
			'mode' => 'hankakukana',
			'style' => '-wap-input-format:&quot;*&lt;ja:hk&gt;&quot;;-wap-input-format:*M;',
		),
		'alphabet' => array(
			'istyle' => '3',
			'format' => '*m',
			'mode' => 'alphabet',
			'style' => '-wap-input-format:&quot;*&lt;ja:en&gt;&quot;;-wap-input-format:*m;',
		),
		'numeric' => array(
			'istyle' => '4',
			'format' => '*N',
			'mode' => 'numeric',
			'style' => '-wap-input-format:&quot;*&lt;ja:n&gt;&quot;;-wap-input-format:*N;',
		),
	);

	function input($fieldName, $options = array())
	{
		if (isset($options['mode'])
		&& isset($this->elements[$options['mode']])) {
			$options = array_merge(
				$options, $this->elements[$options['mode']]);
		}
		return $this->Form->input($fieldName, $options);
	}

	function error($field, $text = null, $options = array())
	{
		$options = array_merge($options, array(
			'wrap' => 'span',
			'style' => 'font-size:xx-small;color:red;'
		));
		return $this->Form->error($field, $text, $options);;
	}

	function month($fieldName, $selected = null, $attributes = array(), $showEmpty = true)
	{
		return $this->Form->month($fieldName, $selected, $attributes, $showEmpty).'月';
	}

	function day($fieldName, $selected = null, $attributes = array(), $showEmpty = true)
	{
		return $this->Form->day($fieldName, $selected, $attributes, $showEmpty).'日';
	}
}
?>