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
 * Rendering form elements
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * @subpackage	Form
 * 
 * @since	0.1.5
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_HTML_Block_Form_Element extends Oos_BaseClass
{
	static protected $_tabindex	= 0;
	static protected $_accesskeys = array();
	
	protected $_tabindex;
	protected $_accesskey;
	
	protected $_template;
	protected $_template_required;
	
	protected $_id;
	protected $_label;
	protected $_default_value;
	protected $_attributes;
	protected $_p_attributes;
	
	protected $_message_error;
	protected $_message_success;
	
	final public function __construct($id, $attributes = array(), $p_attributes = array())
	{
		$this->_id = $id;
		
		$this->_template = '
			<p {p_attributes}>
		    	<label>{label} {required}</label>
		    	{element}
		    	<div class="error">{message_error}</div>
		    	<div class="success">{message_success}</div>
		    </p>	
		';
		
		$this->_template_required = '<span class="required">*</span>';
		
		// we are launching that in another method in order to launch factories and use call_user_func
		$this->init($id, $attributes, $p_attributes);
	}
		
	public function init($attributes = array(), $p_attributes = array())
	{
		$this->_attributes		= $attributes;
		$this->_p_attributes	= $p_attributes;
	}
	
	public function getId() 			{ return $this->_id; }
	public function getAttribute($key) 	{ return $this->_attributes[$key]; }
	
	public function setAttribute($key, $value)
	{
		$this->_attributes[$key] = $value;
	}
	public function setParaAttribute($key, $value)
	{
		$this->_attributes[$key] = $value;
	}
	
	public function setTemplate($template)
	{
		$this->_template = $template;
	}
	
	public function setTemplateRequired($template)
	{
		$this->_template_required = $template;
	}

	/**
	 * @todo
	 */
	public function setDefaultValue($value)
	{
		$this->_default_value = value;
	}
	
	/**
	 * @todo
	 */
	public function isRequired()
	{
		
	}
	
	abstract public function doPopulate();
	public function populate()
	{	
		// calculating additionnal values for para...
		if(!$this->_p_attributes["class"])
		{
			$this->_p_attributes["class"] = "para_standard";
		}
	
		if(!$this->_p_attributes['id'])
		{
			$this->_p_attributes["id"] = "para_" . $this->_id;
		}
		
		// ... and for input element
		$this->_tabindex 	= self::$_tabindex++;
		$this->_accesskey 	= self::getAccessKey($this->_id);
		
		// rendering
		$html = $this->_template;
		$html = str_replace('{p_attributes}', Oos_Utils_String::array2Attributes($this->_p_attributes), $html);
		$html = str_replace('{label}', $this->_label, $html);
		$html = str_replace('{element}', $this->doPopulate(), $html);
		
		$html = str_replace('{message_error}', $this->_message_error, $html);
		$html = str_replace('{message_success}', $this->_message_success, $html);
		
		if($this->isRequired())
		{
			$html = str_replace('{required}', $this->_template_required, $html);
		}
		return $html;
	}
	
	/**
	 * @todo
	 */
	static public function getAccessKey($element_id)
	{
		
	}
	
	/**
	 * Building elements
	 * The first 2 parameters are mandatory, others are optionnal and depends on the input type
	 * 
	 * @version	1.0
	 * @since	0.1.5
	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$type		Type of the new input
	 * @param 	string	$name		Name (and id) of that input
	 * 
	 * @return	Oos_HTML_Block_Form_Element		
	 * 
	 * @throws	Oos_HTML_Block_Form_Exception	Not enough arguments to build a form element
	 * @throws	Oos_HTML_Block_Form_Exception	Unknown type for a form element
	 */
	static public function factory()
	{
		$args = func_get_args();
		
		if(count($args) < 2)
		{
			throw new Oos_HTML_Exception("Not enough arguments to build a form element", OOS_E_FATAL);
		}
		
		$type = array_shift($args);
		$id = array_shift($args);
		
		$class = "Oos_HTML_Block_Form_Element_" . ucfirst($type);
		if(!class_exists($class))
		{
			throw new Oos_HTML_Exception("Unknown type '" . $type . "' for a form element", OOS_E_FATAL);
		}
		
		$o = new $class($id);
		call_user_func_array(array($o, 'init'), $args);
		
		return $o;
	}
}