<?php

class Oos_HTML_Block_Form_Element_Checkbox extends Oos_HTML_Block_Form_Element
{
	private $_value;
	
	public function init($value, $attributes = array(), $p_attributes = array())
	{
		$this->_value = $value;
		parent::init($attributes, $p_attributes);
	}
	
	public function doPopulate()
	{
		$this->_attributes['type'] 	= "checkbox";
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['value'] = $this->_value;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		if($this->_value == $this->_default_value)
		{
			$this->_attributes['checked'] = "checked";
		}
		$html = '
		    	<input ' . Oos_Utils_String::array2Attributes($this->_attributes) . ' />
		';
		
		return $html;
	}
}