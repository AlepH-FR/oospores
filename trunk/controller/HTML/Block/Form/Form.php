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
 * Rendering forms
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * @subpackage	Form
 * 
 * @since	0.1.5
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo 	id;
 */
abstract class Oos_HTML_Block_Form extends Oos_HTML_Block
{
	public $method 	= "get";
	public $action	= "";
	public $target	= "_self";

	protected $_fieldsets = array();
	protected $_elements  = array();
	
	public function registerFieldset($fieldset)
	{
		if(is_array($fieldset))
		{
			foreach($fieldset as $item) { $this->registerFieldset($item); }
		}
		$this->_fieldsets[$fieldset->getId()] = $fieldset;
		
		if(!is_array($this->_elements))
		{
			$this->_elements = array();
		}
		
		$this->_elements = array_merge($this->_elements, $fieldset->getElements());
	}
	
	public function setDefaults($defaults)
	{
		if(!is_array($defaults)) { return ; }
		
		foreach($defaults as $e_name => $default)
		{
			$this->_elements[$e_name]->setDefaultValue($default);
		}
	}
	
	public function populate()
	{
		foreach($this->_elements as $element)
		{
			if(get_class($element) == "Oos_HTML_Block_Form_Element_File")
			{
				// todo enctype
			}
		}
		
		foreach($this->_fieldsets as $fieldset)
		{
			$fieldsets_htm.= $fieldset->populate();
		}
		
		$html = '
	<form action="' . $this->action .'" method="' . $this->method . '" target="' . $this->target . '">
		' . $fieldsets_htm . '
	</form>
		';
		
		return $html;
	}
	
	/**
	 * We don't want to use it there.
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	final public function renderVariables()
	{
		;
	}
	
	/**
	 * Implement this function to add items to your form
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract public function render();
	
	/**
	 * Renders block. It calls your "render" method on which you can add items to your list, then it's populates the body of the list
	 * and finally it returns it.
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */
	public function doRender()
	{	
		$this->render();
		$block = $this->populate();
		
		global $controller;
		if($controller->isAdminPage())
		{
			$block = '
	<div id="block-' . $this->_template . '" class="oos_block">
' . $block . '
	</div>
			';
		}
		
		return $block;
	}
}