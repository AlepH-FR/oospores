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
 * Objects representing databases' tables
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_Common_Table extends Oos_BaseClass 
{
	/**	string	On what type of database we are. Maybe idiot, but it's saving time and ressources */
	protected $_db_type; 
	
	/**	string	Name of this table */
	protected $_name;
	/** string	Code of this table */
	protected $_code;
	/** array	Array of Oos_DB_Common_Field objects that are associated with this table */
	protected $_fields;
	/** string	Libelle of this table (used in backoffices) */
	protected $_libelle;
	
	/**
	 * Get table's name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */	
	public function getName() 	{ return utf8_decode($this->_name); }
	
	/**
	 * Get table's code
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */
	public function getCode() 	{ return utf8_decode($this->_code); }
	
	/**
	 * Get table's fields
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	array(Oos_DB_Common_Field)
	 */
	public function getFields() { return $this->_fields; }
	
	/** Get table's libelle
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */
	public function getLibelle(){ return utf8_decode($this->_libelle); }
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$name		Name of this table
	 * @param	string	$code		Code of this table
	 * @param	array	$fields		Array of Oos_DB_Common_Field objects that are associated with this table
	 * @param	string	$db_type	On what type of database we are
	 * @param	string	$libelle	Libelle of this table (used in backoffices)
	 */
	public function __construct($name, $code, $fields, $db_type, $libelle) 
	{
		$this->_db_type = $db_type;
		
		$this->_name 	= $name;
		$this->_code	= $code;
		$this->_fields 	= $fields;
		$this->_libelle	= $libelle;
	}
	
	/**
	 * Looking if the specified field is associated with this table given its code
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$field	The code of the field we are looking for
	 * @return	boolean
	 */	
	public function hasField($field) 
	{
		if($field == strtolower($this->code).'_ID') 
		{
			return true;
		} 
		
		foreach($this->fields as $table_field) 
		{
			if($table_field->getCode() == $field) 
			{
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Sorts two tables between them.
	 * Used in bubble sort functions.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	Oos_DB_Common_Table	$a	The first item to compare
	 * @param	Oos_DB_Common_Table	$b	The second item to compare
	 * @return	integer
	 */	
	public static function sortByName($a, $b)
	{
		return strcmp($a->getName(), $b->getName());
	}
}