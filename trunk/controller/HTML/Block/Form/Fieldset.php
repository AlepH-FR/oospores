<?php
/*
 * Mozilla Public License
 * 
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 * 
 * The Original Code is OOSpores content management framework, released August 2nd, 2010.
 * The Initial Developer of the Original Code is Antoine Berranger.
 * 
 * Portions created by Antoine Berranger are Copyright (C) 2010 Antoine Berranger
 * All Rights Reserved.
 * 
 * Contributor(s): ____________________.
 *
 * Alternatively, the contents of this file may be used under the terms
 * of the GPL license (the  "[GPL] License"), in which case the
 * provisions of [GPL] License are applicable instead of those
 * above.  If you wish to allow use of your version of this file only
 * under the terms of the [GPL] License and not to allow others to use
 * your version of this file under the MPL, indicate your decision by
 * deleting  the provisions above and replace  them with the notice and
 * other provisions required by the [GPL] License.  If you do not delete
 * the provisions above, a recipient may use your version of this file
 * under either the MPL or the [GPL] License.
 */

/**
 * Rendering fieldsets
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * @subpackage	Form
 * 
 * @since	0.1.5
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_HTML_Block_Form_Fieldset extends Oos_BaseClass
{
	private $_template;
	
	private $_id;
	private $_legend;
	private $_attributes;
	private $_elements = array();
	
	public function __construct($id, $legend = null, $attributes = array(), $elements = array())
	{
		$this->_id 			= $id;
		$this->_legend		= $legend;
		$this->_attributes	= $attributes;
		$this->_elements	= $elements;
		
		$this->_template = '
		<fieldset id="{id}" {attributes}>
			<legend>{legend}</legend>
			{elements}
		</fieldset>
		
		';
	}
	
	public function getId() 			{ return $this->_id; }
	public function getAttribute($key) 	{ return $this->_attributes[$key]; }
	public function getElements()		{ return $this->_elements; }
	
	public function setAttribute($key, $value)
	{
		$this->_attributes[$key] = $value;
	}
	
	public function setTemplate($template)
	{
		$this->_template = $template;
	}
	
	public function registerElements($element)
	{
		if(is_array($element))
		{
			foreach($element as $item) { $this->registerElements($item); }
		}
		$this->_elements[$element->getId()] = $element;
	}
	
	public function populate()
	{
		foreach($this->_elements as $element)
		{
			$elements_htm.= $element->populate();
		}
		
		if($this->_attributes['id'])
		{
			$this->_id = $this->_attributes['id'];
			unset($this->_attributes['id']);
		}
		
		$html = $this->_template;
		$html = str_replace('{id}', $this->_id, $html);
		$html = str_replace('{attributes}', Oos_Utils_String::array2Attributes($this->_attributes), $html);
		$html = str_replace('{legend}', $this->_legend, $html);
		$html = str_replace('{elements}', $elements_htm, $html);
		
		return $html;
	}
}