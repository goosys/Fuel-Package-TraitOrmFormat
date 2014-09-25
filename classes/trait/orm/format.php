<?php

trait Trait_Orm_Format {
	
	/*
	public static function __callStatic($method, $args)
	{
		if (substr($method, 0, 10) == 'formatted_')
		{
			$value = call_user_func_array(substr($method, 10),$args);
			$result = $this->_format(substr($method, 10));
			return $result;
		}
		else
		{
			return call_user_func_array($method,$args);
		}
	}
	*/
	
	/**
	 * Allow for getter, setter and unset methods
	 *
	 */
	public function & __get($property)
	{
		if (substr($property, 0, 10) == 'formatted_')
		{
			$result = $this->_format(substr($property, 10));
			return $result;
		}
		else
		{
			return $this->get($property);
		}
	}
	
	protected function _format($property,$format='')
	{
		$result = '';
		if( empty($format) ){
			$format = $this->_get_format($property,$this->property($property));
		}
		
		return static::format($this->get($property),$format);
	}
	
	/**
	 *
	 * @param $value
	 * @param $format: '%d' | 'common.jpn' | 'selector.method' | callable
	 */
	public function format($value,$format='')
	{
		$result = $value;
		if( !empty($format) && !is_callable($format) ){
			$format = static::_get_format('',array('format'=>$format));
		}
		
		if( !is_null($value) && is_callable($format) )
		{
			$result = $format($value);
		}
		
		return $result;
	}
	
	protected static function _get_format($property,$p=array())
	{
		//$class = get_called_class();
		$class = get_class();
		$model = strtolower(substr($class, 6));//cut 'Model_'
		$format= '';
		
		if( isset($p['format']) ){
			$format = $p['format'];
		}
		else if( __('trait-orm-format.model.'.$model) ){
			$lng = __('trait-orm-format.model.'.$model);
			$format = \Arr::get($lng, $property,'%s');
		}
		
		if( substr($format, 0, 7) == 'common.')
		{
			$lng = __('trait-orm-format.common');
			$format = \Arr::get( $lng, substr($format,7), false);
		}
		
		if( !is_callable($format) && substr($format, 0, 9) == 'selector.')
		{
			if( __($format) ){
				$selector = __($format);
				$format = function($val) use($selector) { return \Arr::get($selector,$val); };
			}
		}
		
		if( !is_null($format) && !is_callable($format) ){
			$format2= $format;
			$format = function($val) use($format2) { return sprintf($format2,$val); };
		}
		
		return ($format)?: false;
	}
}


/**
Example:
lang/ja/trait-orm-format.php
	'common' => array(
		'jpy'       => function($val){ return number_format($val).'Yen'; },
		'percent'   => '%d%%',
	),
	'model' => array(
		'item' => array(
			'price'     => 'common.jpy',
			'tax'       => 'common.percent',
			'num'       => '%dko',
			
			'payment_method' => 'selector.payment_method',
		),
	)
lang/ja/selector.php
	'payment_method' => array(
		'' => '--'
		'1'=> 'cache',
		'2'=> 'card',
	)

Example:
<?php echo $item->formatted_price; ?>
<?php echo $item->format($item->price,'common.jpy'); ?>

<?php echo $item->formatted_tax; ?>
<?php echo $item->formatted_num; ?>
<?php echo $item->formatted_payment_method; ?>

*/