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
 * Managing SQL Records
 * 
 * @package	Oos_DB
 * @subpackage	SQL
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_SQL_Record extends Oos_DB_Common_Record 
{
	/**	string	On what type of database we are. Maybe idiot, but it's saving time and ressources */
	protected $_db_type = "SQL";
	
	/**
	 * This method is called by __get magical method
	 * @see		Oos_DB_Common_Record::__get
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field		Name of the field we are looking for
 	 * @param	array	$options	(opt.) Options for this request ("raw" to get raw values)
 	 * @return	string
	 */
	public function getField($field, $options = array()) 
	{
		$database = Oos_DB::description($this->_db_type);
		
		$key = $this->getFieldKey($field);
		$db_field = $database->getField($key);
		
		$raw_value = $this->__fields[$key];
		if($options["raw"]) 
		{
			return $raw_value;
		}
		
		$formatted_value = $db_field->getGetFormattedValue($raw_value);
		return $formatted_value;		
	}
	
	/**
	 * This method is called by __set magical method.
	 * If a new value is set, then the field will be flagged as changed, so that the next update request will update that field
	 * @see		Oos_DB_Common_Record::__set
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field		Name of the field we are setting
 	 * @param	string	$value		Value we wanna set
 	 * @param	array	$options	(opt.) Options for this request ("raw" to get raw values)
 	 * @return	string
	 */
	public function setField($field, $value, $options = null) 
	{
		$database = Oos_DB::description($this->_db_type);
		
		$key = $this->getFieldKey($field);
		$db_field = $database->getField($key);
		
		$previous_value = $this->getField($field, array("raw" => true));
		
		$value = $db_field->getSetFormattedValue($value, $previous_value);
		
		if(!is_null($value))
		{
			$this->__fields[$key] = $value;
			$this->_changed_fields[$key]++;	
		}		
	}
}