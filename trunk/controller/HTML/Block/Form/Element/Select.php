<?php

class Oos_HTML_Block_Form_Element_Select extends Oos_HTML_Block_Form_Element
{
	private $_options = array();
	
	public function init($options, $attributes = array(), $p_attributes = array())
	{
		$this->_options = $options;
		
		parent::init($attributes, $p_attributes);
	}
	
	public function populateOptions($options = null)
	{
		if(is_null($options))
		{
			$options = $this->_options;	
		}

		$options_htm = '';
		foreach($options as $value => $string)
		{
			if(is_array($string))
			{
				$options_htm = '
					<optgroup label="' . $value .'">
					' . $this->populationsOptions($string). '
					</optgroup>
				';
			}
			
			else
			{
				$selected = ($value == $this->_default_value) ? ' selected="selected' : '';
				$options_htm = '
					<option value="' . $value . '"' . $selected . '>' . $string . '</option>
				';
			}
		}
		
		return $options_htm;
	}
	
	public function doPopulate()
	{
		$this->_attributes['id'] 	= $this->_id;
		$this->_attributes['name'] 	= $this->_id;
		$this->_attributes['tabindex'] 	= $this->_tabindex;
		$this->_attributes['accesskey'] = $this->_accesskey;
		
		
		$html = '
		    	<select ' . Oos_Utils_String::array2Attributes($this->_attributes) . '>
		    	' . $this->populateOptions() . '
		    	</select>
		';
		
		return $html;
	}
}