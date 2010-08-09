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
 * Objects representing databases' record.
 * Provides methods to manage this records (creation, update, deletion)
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_DB_Common_Record extends Oos_BaseClass 
{
	/**	string	Id of our Record object */
	public $id;
	/** string	Main table of this record object. If the record associates many tables, the first one will be the main table */
	public $main_table;
	
	/** array	Tables associated with this record */
	protected $_tables;
	/** array	List of fields that have been chaned this the last update/load */
	protected $_changed_fields = array();
	
	/** array	List of fields in this record */
	protected $__fields = array();
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	mixed	$tables	Tables associated with this record 
	 * @param	array	$fields	(opt.) List of fields in this record
	 * @param 	string	$id		(opt.) Id of our Record object 
	 */
	public function __construct($tables, $fields = array(), $id = null) 
	{
		if(is_array($tables)) 
		{
			$this->_tables 		= $tables;
		}
		else 
		{
			$this->_tables[]	= $tables;
		}
		
		$this->main_table	= $this->_tables[0];
		$this->__fields		= $fields;
		
		$this->id			= $id;
	}
	
	/**
	 * Class destructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 */
	public function __destruct() 
	{
		;
	}
	
	/**
	 * Getting all fields of that record and their formatted values
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	array
 	 */
	public function getAllFields()
	{
		$result = array();
		foreach($this->__fields as $key => $value)
		{
			$result[$key] = $this->{$key};
		}	
		
		return $result;
	}
	
	/**
	 * Construct a field's key given it's name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
 	 * 
 	 * @todo	make it more clever => parse all table to find proper key ?
 	 */
	protected function getFieldKey($field) 
	{
		$key    = $field; 
		$result = $this->__fields[$key];
		
		if(!is_array($this->__fields) || !array_key_exists($key, $this->__fields)) 
		{
			$key = $this->main_table."_".$field;
		}
		
		if(is_array($this->__fields) && !array_key_exists($key, $this->__fields)) 
		{
			;
		}
		
		return $key;
	}
	
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
	abstract public function getField($field, $options = null);
	
	/**
	 * Magical method to access fields of this record
	 * @see		Oos_DB_Common_Record::getField
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field		Name of the field we are looking for
 	 * @return	string
	 */
	public function __get($field) 
	{
		return $this->getField($field);
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
	abstract public function setField($field, $value, $options = null); 
	
	/**
	 * Magical method to set new values for fields of this record
	 * @see		Oos_DB_Common_Record::setField
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field		Name of the field we are setting
 	 * @param	string	$value		Value we wanna set
 	 * @return	string
	 */
	public function __set($field, $value) 
	{
		return $this->setField($field, $value);
	}
	
	/**
	 * Get the id of this record
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */	
	public function getId() 
	{
		return $this->id;
	}

	/**
	 * Create new data for this record given a specified table
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$table	The table on which we wanna insert new data
 	 * @param	array	$fields	Fields we wanna create
 	 * @return	string			The id of the new entry
	 */	
	public function doCreate($table, $fields) 
	{
		$qh = Oos_DB::factory($this->_db_type, $table);
		$id = $qh->create($fields);
		
		return $id;
	}

	/**
	 * Creates data that has been set and that has been flag has "changed".
	 * It will launch a Oos_DB_Common_Record::doCreate method for each table concerned
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function create() 
	{
		// looking for tables
		foreach($this->_tables as $table) 
		{
			$table_changed_fields	= array();
			foreach($this->_changed_fields as $field => $cpt) 
			{
				if(strpos($field, $table) === 0) 
				{
					$table_changed_fields[$field] = $this->getField($field, array("raw" => true));
				}
			}
			
			// creation table after table
			$id = $this->doCreate($table, $table_changed_fields);
				
			$this->id = $id;
			$this->__fields[$table."_ID"] = $id;		
		}
		
		// emptying the list of changed fields
		$this->_changed_fields = array();
	}
	
	/**
	 * Update data for this record given a specified table
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$table			The table on which we wanna insert new data
 	 * @param	array	$fields			Fields we wanna create
 	 * @param	array	$whereClause	Array of criterias for this update
	 */		
	public function doUpdate($table, $fields, $whereClause) 
	{
		$qh = Oos_DB::factory($this->_db_type, $table);
		$qh->update($fields, $whereClause);
	}
	
	/**
	 * Updates data that has been set and that has been flag has "changed".
	 * It will launch a Oos_DB_Common_Record::doUpdate method for each table concerned
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @throws	Oos_DB_Exception	Why do you want to update an object that does not exist ?
	 */	
	public function update() 
	{
		// si on n'a pas d'identifiant, c'est qu'il fallait créer l'objet avant de le mettre à jour
		if(!$this->id) 
		{
			throw new Oos_DB_Exception("Why do you want to update an object that does not exist ?", OOS_E_FATAL);
		}
		
		// recherche des tables
		foreach($this->_tables as $table) 
		{
			$table_changed_fields	= array();
			foreach($this->_changed_fields as $field => $cpt) 
			{
				if(strpos($field, $table) === 0) 
				{
					$table_changed_fields[$field] = $this->getField($field, array("raw" => true));
				}
			}
			
			// update table par table
			$whereClause = array("ID:eq" => $this->__get($table."_ID"));
			$this->doUpdate($table, $table_changed_fields, $whereClause);			
		}
		
		// on vide la liste des champs modifiés
		$this->_changed_fields = array();
	}
	
	/**
	 * Delete entry for this record given a specified table
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$table			The table on which we wanna insert new data
 	 * @param	array	$whereClause	Array of criterias for this update
 	 * 
 	 * @todo	images & file deletion !
	 */	
	public function doDelete($table, $whereClause) 
	{
		$qh = Oos_DB::factory($this->_db_type, $table);
		$qh->delete($whereClause);
	}
	
	/**
	 * Delete entries of this record
	 * It will launch a Oos_DB_Common_Record::doDelete method for each table concerned
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function delete() 
	{
		foreach($this->_tables as $table) 
		{
			$whereClause = array("ID:eq" => $this->__get($table."_ID"));
			$this->doDelete($table, $whereClause);			
		}
	}
}