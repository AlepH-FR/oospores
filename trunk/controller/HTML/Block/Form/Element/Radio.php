<?php

class Oos_HTML_Block_Form_Element_Radio extends Oos_HTML_Block_Form_Element
{
	private $_group;
	
	public function init($group, $attributes = array(), $p_attributes = array())
	{
		$this->_group = $group;
		parent::init($attributes, $p_attributes);
	}
	
	public function doPopulate()
	{
		$this->_attributes['type'] 	= "radio";
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_group;
		$this->_attributes['value'] = $this->_default_value;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		
		$html = '
		    	<input ' . Oos_Utils_String::array2Attributes($this->_attributes) . ' />
		';
		
		return $html;
	}
}