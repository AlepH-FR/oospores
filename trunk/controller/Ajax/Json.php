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
 * Transform data into a json object and send it back
 * 
 * @package	Oos_Ajax
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Ajax_Json extends Oos_BaseClass 
{
	/** boolean	If true, the json will be automatically sent when the instance will be destructed */
	private $_auto_print;
	/** arra	The data to "jsonize" */
	private $_json = array();
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	boolean	$auto_print	(opt.) If true, the json will be automatically sent when the instance will be destructed. By default : false
	 */
	public function __construct($auto_print = false) 
	{
		$this->_auto_print = $auto_print;
	}
	
	/**
	 * Class destructor.
	 * Sends json to the buffer output if needed
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __destruct() 
	{
		if($this->_auto_print) 
		{
			print $this->encode();
		}
	}
	
	/**
	 * Cleans data to be "jsonized"
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value	The value to process
 	 * @return	string
	 */
	public function cleanValue($value) 
	{
		// strings
		if(is_string($value)) 
		{
			$value = htmlentities($value);
		} 
		
		// arrays 
		elseif(is_array($value)) 
		{
			foreach($value as $key => $item) 
			{
				$value[$key] = $this->cleanValue($item);
			}
		}
		
		return $value;
	}
	
	/**
	 * Adds data to our json attribute
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value	The value to process
	 */
	public function add($value) 
	{
		$this->_json[] = $this->cleanValue($value);
	}
	
	/**
	 * Encode our json array via the "json_encode" function
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function encode() 
	{
		return json_encode($this->_json);
	}
}