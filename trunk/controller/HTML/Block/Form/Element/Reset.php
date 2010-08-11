<?php

class Oos_HTML_Block_Form_Element_Reset extends Oos_HTML_Block_Form_Element
{
	public function doPopulate()
	{
		$this->_attributes['type'] 	= "reset";
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['value'] = $this->_default_value;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		$html = '
		    	<input ' . Oos_Utils_String::array2Attributes($this->_attributes) . ' />
		';
		
		return $html;
	}
}