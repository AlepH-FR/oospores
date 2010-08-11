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
 * Rendering tables easily.
 * . first row will have class "first"
 * . last row will have class "last"
 * . each even item row have class "even" and the same rule applies for odd rows
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * 
 * @since	0.1.5
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_HTML_Block_Table extends Oos_HTML_Block
{
	/** array	Columns of the thead markup */
	private $_header_data;
	/** array	Columns of the tfoot markup */
	private $_footer_data;
	/** array	Matrix of tbody cells */
	private $_body_data;
	
	/** integer	Number of rows of that table */
	private $_nb_rows = 0;
	/** integer	Number of colls of that table */
	private $_nb_cols = 0;
	
	/** string	Processed thead markup */
	private $_t_header;
	/** string	Processed tfoot markup */
	private $_t_footer;
	/** string	Processed tbody markup */
	private $_t_body;
	
	/** string	The class you want to give to the table */
	public $table_class;
	/** string	A caption for your table */
	public $caption;
	
	/** 
 	 * Add a row or multiple rows to your table
	 * 
	 * @version	1.0
 	 * @author 	ANB
	 * 
	 * @param	array	$data	a data matrix that will be merge with previous data, or a data array a row that will be pushed at the end of the previous data matrix
	 */
	public function addData($data)
	{
		if(!is_array($data))
		{
			return; 
		}

		if(!is_array(current($data)))
		{
			$this->_body_data[] = $data;
			$this->_nb_rows++;
		}
		
		if(is_null($this->_body_data)) { $this->_body_data = array(); }
		
		$this->_body_data 	= array_merge($this->_body_data, $data);
		$this->_nb_cols 	= count(current($data));
		
		$this->_nb_rows += count($data);	 
	}
	
	/**
	 * Sets a new header for your table
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	array	$header		data for the thead markup
	 */ 
	public function setHeader($header)
	{
		$this->_header_data = $header;
	}
	
	/**
	 * Sets a new footer for your table
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	array	$footer		data for the tfoot markup
	 */
	public function setFooter($footer)
	{
		$this->_footer_data = $header;
	}
	
	/**
	 * Populate the HTML header of the table
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @throws	Oos_HTML_Exception	Header's data don't match the number of rows of you table's data
	 */
	public function populateHeader()
	{
		if(!$this->header_data) { return ; }
		if($this->_nb_rows > 0 && (count($this->header_data) != $this->_nb_cols))
		{
			throw new Oos_HTML_Exception("Header's data don't match the number of rows of you table's data", OOS_E_WARNING);
		}
		
		$cells = '';
		$first = true;
		foreach($this->header_data as $value)
		{
			$class = "";
			if($first) 								{ $class = "first"; $first = false; }
			if($value == end($this->header_data)) 	{ $class = "last"; }
			
			$cells.= '
						<th class="'.$class.'">
							'.$value.'
						</th>
			';
		}
		
		$html = '
				<thead>
					<tr>
						'.$cells.'
					</tr>
				</thead>
		';
		
		$this->_t_header = $html;
	}
	
	/**
	 * Populate the HTML footer of the table
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @throws	Oos_HTML_Exception	Footer's data don't match the number of rows of you table's data
	 */
	public function populateFooter()
	{
		if(!$this->footer_data) { return ; }
		if($this->_nb_rows > 0 && (count($this->footer_data) != $this->_nb_cols))
		{
			throw new Exception("Footer's data don't match the number of rows of you table's data");
		}
		
		$cells = '';
		$first = true;
		foreach($this->footer_data as $value)
		{
			$class = "";
			if($first) 								{ $class = "first"; $first = false; }
			if($value == end($this->footer_data)) 	{ $class = "last"; }
			
			$cells.= '
						<td class="'.$class.'">
							'.$value.'
						</td>
			';
		}
		
		$html = '
				<tfoot>
					<tr>
						'.$cells.'
					</tr>
				</tfoot>
		';
		
		$this->_t_footer = $html;
	}
	
	/**
	 * Populate the HTML body of the table
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function populateBody()
	{
		if(is_null($this->body_data)) { return ; }
		
		$rows  = '';
		$count = 1;
		foreach($this->body_data as $value)
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
			
			$cols 		= '';
			$first_col 	= true;
			
			// calcul du colspan sur la dernière cellule
			$colspan 	= false;
			$nb_cells = count($value);
			if($nb_cells != $this->_nb_cols)
			{
				$colspan = $this->_nb_cols - $nb_cells + 1;
			}
			foreach($value as $cell_value)
			{
				$class_col = "";
				if($first_col) 					{ $class_col = "first"; $first_col = false; }
				if($cell_value == end($value)) 	
				{ 
					$class_col = "last";  
					if($colspan) 	{ $colspan_string = 'colspan="'.$colspan.'"'; } 
				}
				else
				{
					$colspan_string = '';
				}
				
				$cols.= '
						<td class="'.$class_col.'" '.$colspan_string.'>
						 	'.$cell_value.'
						</td>
				';			
			}
			
			$rows.= '
					<tr class="'.$class.'">
						'.$cols.'
					</tr>
			';
			
			$count++;
		}
		
		$html = '
				<tbody>
					'.$rows.'
				</tbody>
		';
		
		$this->_t_body = $html;
	}
	
	/**
	 * We don't want to use it there.
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	final public function renderVariables() { ; }
	
	/**
	 * Implement this function to add items to your list.
	 * 
	 * @version	1.0
	 * @since	0.1.5
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract public function render();
	
	/**
	 * Returns formatted & valid HTML code for this table.
	 * It calls your "render" method on which you can add items to your table, 
	 * then it's populates the html code and finally it returns it.
	 * 
	 * @version	1.0
 	 * @author 	ANB
 	 * 
	 * @return	string	HTML Code
	 */
	public function doRender()
	{	
		$this->render();
		$this->populateHeader();
		$this->populateBody();
		$this->populateFooter();
			
		$block = '
			<table class="' . $this->table_class . '">
				<caption>' . $this->caption . '</caption>
				' . $this->_t_header . '
				' . $this->_t_footer . '
				' . $this->_t_body . '
			</table>
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