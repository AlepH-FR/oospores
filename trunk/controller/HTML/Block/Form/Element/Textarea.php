<?php

class Oos_HTML_Block_Form_Element_Textarea extends Oos_HTML_Block_Form_Element
{
	public function doPopulate()
	{
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		$html = '
		    	<textarea ' . Oos_Utils_String::array2Attributes($this->_attributes) . '>' . $this->_default_value . '</textarea>
		';
		
		return $html;
	}
}