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
 * Represents database fields and provides method to access or update their values
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_Common_Field extends Oos_BaseClass 
{
	/**	string	On what type of database we are. Maybe idiot, but it's saving time and ressources */
	private $_db_type;
	
	/** string	Name of the field */
	private $_name;
	/** string	Id of the field */
	private $_code;
	/** string	Type of the field */
	private $_type;
	/** array	Miscellanous options depending on field's type */
	private $_options;
	
	/**
	 * Get field's name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
	 */
	public function getName() 	{ return utf8_decode($this->_name); }
	
	/**
	 * Get field's id (or code)
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
	 */
	public function getCode() 	{ return utf8_decode($this->_code); }
	
	/**
	 * Get field's type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
	 */
	public function getType() 	{ return utf8_decode($this->_type); }
	
	/**
	 * Get field's option
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$key	Key of the option we are looking for
 	 * @return 	string
	 */
	public function getOption($key) { return $this->_options[$key]; }
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$field_id	Id (or code) of the field
 	 * @param	array	$field_data	Data of the field provided by Oos_XML_Database_Collection
 	 * @param	string	$db_type	Type of database we are accessing
	 */
	public function __construct($field_id, $field_data, $db_type) 
	{
		$this->_db_type = $db_type;
		
		$this->_name 	= $field_data['name'];
		$this->_code	= $field_id;
		$this->_type 	= $field_data['type']['_value'];
		$this->_options = $field_data['type'];
		
		$this->checkType();
	}
	
	/**
	 * Checks if the field has a valid type between (joint | multijoint | password | date | timestamp | bool | int | float | file | image | texte | memo | email | url)
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	boolean
 	 * 
 	 * @throws	Oos_XML_Exception	Invalid type for field 
	 */
	private function checkType() 
	{
		switch($this->_type) 
		{
			case "joint":	
			case "multijoint":
			case "password":
			case "date":			
			case "timestamp":
			case "bool":		
			case "int":
			case "float":
			case "image":
			case "file":
			case "text":		
			case "memo":	
			case "email":
			case "url":
				return true;
		}
		
		throw new Oos_DB_Exception("Invalid type '".$this->_type."' for field '".$this->_name."'", OOS_E_FATAL);
		return false;
	}
	
	/**
	 * Maps XML types to SQL types
	 * . int, joint : int
	 * . float : float
	 * . password, text, memo, email, image, url, file, timestamp, multijoint : text
	 * . date : date
	 * . bool : bool
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
	 */
	public function getSqlType() 
	{
		switch($this->_type) 
		{
			case "int":
			case "joint":		return "int";
			case "multijoint": 	return "text";
			case "float": 		return "float";
			case "password":
			case "text":		
			case "memo":
			case "email":
			case "image":
			case "url":
			case "file": 	 	return "text";
			case "date": 		return "date";
			case "bool": 		return "bool";
			case "timestamp":	return "text";
		}
		
		return null;
	}
	
	/**
	 * Throws an exception when the value we wanna set isn't well formatted
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value_expected	Some description of the type of value we excepted
 	 * 
 	 * @throws	Oos_DB_Exception 	Wrong format for field's criteria
	 */
	private function wrongFormat($value_expected = "") 
	{
		if($value_expected) 
		{
			$text = 'Value expected : '.$value_expected;
		}
		
		throw new Oos_DB_Exception("Wrong format for field's criteria '".$this->_name."' (type '".$this->_type."'). ".$text."", OOS_E_FATAL);
	}
	
	/**
	 * Get a "WHERE" clause for requests on this field depending on the value, and the field's type
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value	The value we are trying to match
 	 * @return	string
	 */
	public function getWhereClauseValue($value) 
	{
		$config = Oos_Config::getInstance();
		
		switch($this->_type) 
		{
			case "joint":
				return $this->getWhereClauseValueJoint($value);
				
			case "multijoint":
				return $this->getWhereClauseValueMultiJoint($value);
				
			case "password":
				if(strlen($value) == 32) { return $value; }
				$salt_1 = $config->getParam("SECURITY", "SALT_1");
				$salt_2 = $config->getParam("SECURITY", "SALT_2");
				return md5($salt_1.$value.$salt_2);
				
			case "date":
				return $this->getWhereClauseValueDate($value);	
						
			case "timestamp":
				if(is_null($value)) { return 0; }
				return intval($value) - DATE_DIFFERENCIAL;
				
			case "bool":
				return ($value);	
					
			case "int":
				return intval($value);
				
			case "float":
				return floatval($value);
				
			case "image":
			case "file":
				return "".$value;	
				
			case "text":		
			case "email":		
			case "memo":
			case "url":
				return "".$value;
		}
	}
	
	/**
	 * Get a "WHERE" clause for requests on "joint" fields
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string|integer|object	$value	The value we are trying to match
 	 * @return	string
	 */	
	private function getWhereClauseValueJoint($value) 
	{
		if(is_null($value))
		{
			return null;
		}
		
		if(is_string($value) && $value == intval($value)) 
		{
			$value = intval($value);
		}
		
		if(!is_object($value) && !is_int($value)) 
		{
			$this->wrongFormat("Objet Record or Integer. Given : '".$value."'");
		}
		
		if(is_object($value)) 	{ return $value->ID; }
		if(is_int($value)) 		{ return $value; }
	}
	
	/**
	 * Get a "WHERE" clause for requests on "multijoint" fields
	 * Recursively calling for Oos_DB_Common_Field::getWhereClauseValueJoint
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$value	The value we are trying to match
 	 * @return	string
	 */	
	private function getWhereClauseValueMultiJoint($value) 
	{
		$values = array();
		
		if(!is_array($value)) 
		{
			$value = array($value);
		}	
		
		foreach($value as $value_unit) 
		{
			$joint_value = $this->getWhereClauseValueJoint($value_unit);
			if(is_null($value)) { return null; }
			$values[] = $joint_value;
		}
		
		if(count($values) == 0) 
		{
			return null;
		}
		
		$result = ",".implode(",", $values).",";
		return $result;
	}
	
	/**
	 * Get a "WHERE" clause for requests on "date" fields
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value	The value we are trying to match
 	 * @return	string
	 */	
	private function getWhereClauseValueDate($value) 
	{
		$reg = "/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/";
		$is_user = preg_match($reg, $value);
		
		if($is_user) 
		{
			list($day, $month, $year) = explode("/", $value);
			$value = $year."-".$month."-".$day;
		}
		
		$reg = "/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/";
		$is_unix = preg_match($reg, $value);
		
		if(!$is_unix) 
		{
			$this->wrongFormat("yyyy-mm-dd OR dd-mm-yyyy");
		}
		
		return $value;
	}
	
	/**
	 * Launches proper "accessor" to format field value
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value	The current value of the field
 	 * @return	mixed
	 */
	public function getGetFormattedValue($value) 
	{
		switch($this->_type) 
		{
			case "joint":
				return $this->getGetFormattedValueJoint($value);
				
			case "multijoint":
				return $this->getGetFormattedValueMultiJoint($value);	
				
			case "timestamp":
				if(!is_null($value)) { $value += DATE_DIFFERENCIAL; }
				return $value;
				
			case "bool":
				return (intval($value) == 1);
						
			case "int":
				return intval($value);
				
			case "float":
				return floatval($value);
				
			case "password":
			case "date":
			case "image":
			case "file":		
			case "email":
			case "url":
				return $value;
					
			case "text":		
			case "memo":
				return stripslashes($value);
		}		
	}
	
	/**
	 * Accessing "joint" field's value
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value	The value we have to format
 	 * @return	Oos_DB_Common_Record
	 */		
	private function getGetFormattedValueJoint($value) 
	{
		$qh = Oos_DB::factory($this->_db_type, $this->_options["table"]);
		$record = $qh->findById(intval($value));
		return $record;
	}
	
	/**
	 * Accessing "multijoint" field's value
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$value	The value we have to format
 	 * @return	array(Oos_DB_Common_Record)
	 */	
	private function getGetFormattedValueMultiJoint($values) 
	{
		$values_array = explode(",", $values);
		
		$result = array();
		foreach($values_array as $value) 
		{
			if(!$value)		{ continue; }
			$record = $this->getGetFormattedValueJoint($value);
			if(!$record)	{ continue; }
			
			$result[] = $record;
		}
		
		return $result;
	}
	
	/**
	 * Launches proper "setter" to format a new field's value to be stored in our databases
	 *
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value			The new value we wanna set
 	 * @param	mixed	$previous_value	The current value of the field
 	 * @return	string
	 */
	public function getSetFormattedValue($value, $previous_value) 
	{
		switch($this->_type) 
		{
			case "int":
				if(!is_int($value)) { return null; }
				return ($value != $previous_value)?$value:null;
				
			case "float":
				if(!is_float($value)) { return null; }
				return ($value != $previous_value)?$value:null;
				
			case "joint":
			case "multijoint":
				$new_value = $this->getWhereClauseValue($value);
				return ($new_value != $previous_value)?$new_value:null;
				
			case "timestamp":
			case "password":
			case "date":			
			case "text":		
			case "memo":
				$new_value = $this->getWhereClauseValue($value);
				return ($new_value != $previous_value)?$new_value:null;
				
			case "bool":
				return ($value)?1:0;
				
			case "image":
			case "file":
				return $this->getSetFormattedValueFile($value, $previous_value);
				
			case "email":
				return $this->getSetFormattedValueMail($value);
				
			case "url":
				return $this->getSetFormattedValueUrl($value);
		}	
	}
	
	/**
	 * Setting "file" and "image" field's value.
	 * It tries to generate a random id for this file.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value			The new value we wanna set
 	 * @param	mixed	$previous_value	The current value of the field
 	 * @param	integer	$nb_attempts	(opt.) Number of attemps we already try
 	 * @return	string					new file path for the file
 	 * 
 	 * @throws	Oos_DB_Exception	Unable to find a valid key to create a temporary file
 	 * @throws	Oos_DB_Exception	Specified file does not exists
 	 * @throws	Oos_DB_Exception	An error occured while transfering file
 	 * @throws	Oos_DB_Exception	An error as occured while copying file
	 */	
	public function getSetFormattedValueFile($value, $previous_value, $nb_attempts = 0) 
	{
		$config = Oos_Config::getInstance();
		
		if($nb_attempts > 10) 
		{
			throw new Oos_DB_Exception("Unable to find a valid key to create a temporary file", OOS_E_FATAL);
			return null;			
		}
		
		if($value == $previous_value) 
		{
			return null;
		}
		
		// testing type
		if(!is_array($value) && !is_string($value)) 
		{
			$this->wrongFormat("Array or String");
			return null;
		}
		
		// file deletion	
		if($previous_value && file_exists($config->getUploadAccountDir().DS.$previous_value))
		{
			$filepath = $config->getUploadAccountDir().DS.$previous_value;
			unlink($filepath);
		}	
		
		// if it's a string value, we just copy it since it's a path
		if(is_string($value)) 
		{
			$account_path = $config->getAccountDir();
			
			$file_path = $value;
			if(substr($file_path, 0, 1) == ".")
			{
				$file_path = substr($file_path, 1);
			}
			if(substr($file_path, 0, 1) == "/")
			{
				$file_path = substr($file_path, 1);
			}
			
			$file_path = str_replace('/', DS, $file_path);
			if(file_exists($account_path.DS.$file_path))
			{
				return $value;
			}
			
			throw new Oos_DB_Exception("Specified file does not exists", OOS_E_WARNING);
			return null;
		}
		
		// else, value is a $_FILES array
		if($value["error"] != UPLOAD_ERR_OK) 
		{
			throw new Oos_DB_Exception("An error occured while transfering file", OOS_E_WARNING);
			return null;
		}
		
		if(!$value['name'] || !$value['tmp_name']) 
		{
			$this->wrongFormat("Wrong array format");
			return null;
		}
		
		// file path
		$filepath = $config->getUploadAccountDir();
		
		// constructing random file name
		$filename.= strtolower($this->code);
		$filename.= "_";

		$between = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';		
		for($i = 0; $i < 20; $i++) { $filename.= $between[rand(0, 36)]; }
		
		$ext = strtolower(array_pop(explode(".",$value['name'])));
		$filename.= ".";
		$filename.= $ext;

		// watching if file already exists or not
		$new_file = $config->getUploadAccountDir().DS.$filename;
		if(file_exists($new_file)) 
		{
			$nb_attempts++;
			return $this->getSetFormattedValueFile($value, $nb_attempts); 
		}
		
		// copying file
		$cpOk = move_uploaded_file($value['tmp_name'], $new_file);
		
		if(!$cpOk) 
		{
			throw new Oos_DB_Exception("An error as occured while copying file", OOS_E_WARNING);
		}
		
		return $filename;
	}	
	
	/**
	 * Setting "mail" field's value.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value			The new value we wanna set
	 */	
	public function getSetFormattedValueMail($value) 
	{
		if ($value === '')							{ return $value; }
		if(!Oos_Utils_String::isValidEmail($value)) { return null; }
		return $value;
	}
		
	/**
	 * Setting "url" field's value.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$value			The new value we wanna set
	 */
	public function getSetFormattedValueUrl($value) 
	{
		if(!$value) 						{ return ""; }
		if(strpos($value, ".") === false) 	{ return ""; }
		if(strpos($value, " ") !== false) 	{ return ""; }
		
		if(substr($value, 0, 7) != "http://") 
		{
			$value = "http://".$value;
		}
		return $value;
	}
}