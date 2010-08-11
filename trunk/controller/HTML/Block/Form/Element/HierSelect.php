<?php

class Oos_HTML_Block_Form_Element_HierSelect extends Oos_HTML_Block_Form_Element
{
	private $_options = array();
	
	public function init($values, $attributes = array(), $p_attributes = array())
	{
		$this->_values = $values;
		parent::init($attributes, $p_attributes);
	}
	
	public function populateOptions($options = null)
	{
		
	}
	
	public function doPopulate()
	{
		
	}
}