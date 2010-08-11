<?php

class Oos_HTML_Block_Form_Element_File extends Oos_HTML_Block_Form_Element
{
	public function doPopulate()
	{
		$this->_attributes['type'] 	= "file";
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		$html = '
		    	<input ' . Oos_Utils_String::array2Attributes($this->_attributes) . ' />
		';
		
		return $html;
	}
}