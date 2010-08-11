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
 * Rendering list easily.
 * . first item will have class "first"
 * . last item will have class "last"
 * . each even item will have class "even" and the same rule applies for odd items
 * . if "auto_indent" attribute is set to true, then each item will have class "li_item_" + item's number
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * 
 * @since	0.1.5
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_HTML_Block_List extends Oos_HTML_Block
{
	/** array	Array of items to render */
	private $_data;
	/** array	Calculated body of our "ul" markup process via the populate method */
	private $_body;
	
	/** string	Class of our list */
	public $class;
	/** boolean	Set to true identify each "li" item */
	public $auto_indent = false;
	
	/**
	 * Add an item to our data array
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string 	$item	Add an item to our list
	 */
	public function addDataItem($item)
	{
		$this->_data[] = $item;
	}
	
	/**
	 * Populates list items
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function populate()
	{
		if(is_null($this->_data)) { return ; }
		
		$html  = '';
		$count = 1;
		foreach($this->_data as $value)
		{
			$class = "";
			if($count == 1) 				{ $class = "first"; }
			if($count == $this->_nb_rows) 	{ $class = "last"; }
			if(($count % 2) == 0)
			{ 
				if(strlen($class) > 0) { $class.= " "; }
				$class.= "even"; 
			}
			else
			{
				if(strlen($class) > 0) { $class.= " "; }
				$class.= "odd"; 
			}
			
			if($this->auto_indent)
			{
				if(strlen($class) > 0) { $class.= " "; }
				$class.= "li_item_".$count; 
			}
			
			$html.= '
					<li class="' . $class . '">
						' . $value . '
					</li>
			';
			
			$count++;
		}
		
		$this->_body = $html;
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
	 * Implement this function to add items to your list.
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
		$this->populate();
		
		$block = '
			<ul class="' . $this->class . '">
				' . $this->_body . '
			</ul>
		';
		
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