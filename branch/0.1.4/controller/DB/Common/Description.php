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
 * Abstract class to access database XML description
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_DB_Common_Description extends Oos_BaseClass
{
	/** Oos_XML_Collection_Database		Instance to access database properties */
	protected $_xml;
	/** array							XML Data transformed into an array */
	protected $_xml_data;
	
	/** array	List of fields described in the xml files */
	protected $_fields = array();
	/** array	List of tables described in the xml files  */
	protected $_tables = array();
	
	/**
	 * Class constructor.
	 * Instanciate Oos_XML_Collection_Database
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __construct() 
	{
		$config = Oos_Config::getInstance();
		
		$this->_xml = new Oos_XML_Collection_Database(_OOS_ACCOUNT);
		$this->_xml_data = $this->_xml->getData();
	}
	
	/**
	 * Normalizes identifiers.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$id		Id we want to normalize
 	 * @param	string	
	 */
	public function normalizeId($id) 
	{
		if(is_null($id)) { return null; }
		return strtolower(trim($id));
	}
	
	/**
	 * Checks if XML was loaded properly
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean	
	 */
	public function checkXML()
	{
		return is_array($this->_xml_data);
	}
	
	/**
	 * Checks if XML was loaded properly
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field_id	Identifier of the field (matching xml:id attributes in the xml files)
 	 * @param	string	$table_name	(opt.) Please try to specify the table, the research is much faster
 	 * @return	boolean	
	 */
	public function fieldExists($field_id, $table_name = null)
	{
		$field_id 	= $this->normalizeId($field_id);
		$table_name = $this->normalizeId($table_name);
		
		return !is_null($this->getFieldData($field_id, $table_name));
	}
	
	/**
	 * Get informations about a field
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field_id	Identifier of the field (matching xml:id attributes in the xml files)
 	 * @param	string	$table_name	(opt.) Please try to specify the table, the research is much faster	
 	 * @return	array|null	
	 */
	public function getFieldData($field_id, $table_name = null)
	{
		if(!is_null($table_name))
		{
			return $this->_xml_data['table'][$table_name]['field'][$field_id];
		}
		
		foreach($this->_xml_data['table'] as $table_id => $table_data)
		{
			if(!is_array($table_data)) 						{ continue; }
			if(!is_array($table_data['field'][$field_id])) 	{ continue; }
			
			return $table_data['field'][$field_id];
		}
		
		return null;		
	}
	
	/**
	 * Turn a field id into a Oos_DB_Common_Field object
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field_id	Identifier of the field (matching xml:id attributes in the xml files)
 	 * @param	string	$table_name	(opt.) Please try to specify the table, the research is much faster	
 	 * @return	Oos_DB_Common_Field
 	 * 
 	 * @throws	Oos_DB_Exception	No field matching the identifier 
	 */
	public function getField($field_id, $table_name = null) 
	{
		if($field_object = $this->_fields[$field_id]) 
		{
			return $field_object;
		}
		
		$options = array();
		$field_id 	= $this->normalizeId($field_id);
		$table_name = $this->normalizeId($table_name);
		
		// id fields
		if(substr($field_id, -3) == "_id") 
		{
			$field = array(
				"name" => 'ID',
				"type" => array("_value" => 'int')
			);
			$field = new Oos_DB_Common_Field($field_id, $field, $this->_db_type);
			$this->fields[$field_id] = $field;
			return $field;
		}
		
		// other fields
		$field 	= $this->getFieldData($field_id, $table_name);
		if(is_null($field)) 
		{
			throw new Oos_DB_Exception("No field matching the identifier '".$field_id."'", OOS_E_FATAL);
		}
		
		$field_object = new Oos_DB_Common_Field($field_id, $field, $this->_db_type);
		$this->_fields[$field_id] = $field_object;
		return $field_object;
	}

	/**
	 * Get every fields for a given table
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$table_id	Identifier of the table (matching xml:id attributes in the xml files)
 	 * @return	array(Oos_DB_Common_Field)
	 */
	public function getFieldsByTable($table_id)
	{
		$fields = $this->_xml_data['table'][$table_id]['field'];
		if(!is_array($fields)) { return array(); }
		
		$fields_desc = array();
		foreach($fields as $field_id => $field_data) 
		{
			$fields_desc[] = $this->getField($field_id);
		}
		
		return $fields_desc;
	}
	
	/**
	 * Turn a table id into a Oos_DB_Common_Table object
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$table_id	Identifier of the table (matching xml:id attributes in the xml files)
 	 * @return	Oos_DB_Common_Table
 	 * 
 	 * @throws	Oos_DB_Exception	No field matching the identifier 
	 */
	public function getTable($table_id) 
	{
		$table_id = $this->normalizeId($table_id);
		
		if($table_object = $this->_tables[$table_id]) 
		{
			return $table_object;
		}
		
		$table = $this->_xml_data['table'][$table_id];
		if(is_null($table)) 
		{
			return null;
		}
		
		$fields = $this->getFieldsByTable($table_id);
		
		$table_object = new Oos_DB_Common_Table($table['name'], $table_id, $fields, $this->_db_type, $table['libelle']);
		$this->_tables[$table_id] = $table_object;
		return $table_object;
	}
	
	/**
	 * Get every tables
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array(Oos_DB_Common_Table)
	 */
	public function getTables() 
	{
		$tables = $this->_xml_data['table'];
		if(!is_array($tables)) { return array(); }
		
		$tables_desc = array();
		foreach($tables as $table_id => $table_data)
		{	
			$tables_desc[] = $this->getTable($table_id);
		}
		
		return $tables_desc;	
	}
		
	/**
	 * Generates or updates tables and fields depending on XML Data.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract public function generateDatabases();	
}