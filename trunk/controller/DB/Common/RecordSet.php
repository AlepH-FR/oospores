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
 * Abstract class to implement in order to manage record sets
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_DB_Common_RecordSet extends Oos_BaseClass
{
	/** ressource	The record set we wanna manipulate */
	protected $_rs = null;
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	ressource	$rs				The record set
 	 * @param	boolean		$is_ressource	(opt.) If $rs is not a valid ressource, then we do not make it a class attribute
	 */	
	public function __construct($rs, $is_ressource = true)
	{
		if($is_ressource)
		{
			$this->_rs = $rs;
		}
	}
		
	/**
	 * Class destructor.
	 * Forcing memory freeing.
	 * @see		Oos_DB_Common_RecordSet::free
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function __destruct()
	{
		if($this->_rs)
		{
			$this->free();
		}
	}
		
	/**
	 * Getting raw record set
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	ressource
	 */	
	public function getRaw()
	{
		return $this->_rs;
	}
		
	/**
	 * Fetching record set as an array
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	abstract public function fetchRow();
		
	/**
	 * Fetching record set as an associative array
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	abstract public function fetchAssoc();	
	
	/**
	 * Fetching record set as an object
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	stdClass
	 */
	abstract public function fetchObject();
	
	/**
	 * Freeing memory occuping by this record set
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean|null
	 */
	abstract public function free();
	
	/**
	 * Get the number of results in this record set
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	abstract public function numRows();
}