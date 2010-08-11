<?php

class Oos_HTML_Block_Form_Element_Date extends Oos_HTML_Block_Form_Element
{
	public function doPopulate()
	{
		$this->_attributes['type'] 	= "date";
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['value'] = $this->_default_value;
		
		$html = '
		    	<input ' . Oos_Utils_String::array2Attributes($this->_attributes) . ' />
		';
		
		return $html;
	}
}