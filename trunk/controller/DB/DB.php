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
 * Main factory class to access database singletons
 * 
 * @package	Oos_DB
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB extends Oos_BaseClass 
{
	/** array	List of queries objects */
	static protected $_known_items = array();
	/** array	List of connection handlers */
	static protected $_known_conns = array();
	/** array	List of knowns database descriptions */
	static protected $_known_descs = array();
	
	/**
	 * We really don't wanna plural instances of this class
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __clone() { ; }
	
	/**
	 * Factory for queries objects.
	 * It automatically connects to the database if needed.
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$type	Type of the database
 	 * @param	array	$tables	Tables concerns by the query object
 	 * 
 	 * @return 	Oos_DB_Common_Query
	 */	
	static function factory($type, $tables)
	{
		// creating DB Key
		if(is_array($tables)) 
		{ 
			$tables_str = implode("_", $tables); 
		}
		else 
		{
			$tables_str = (string) $tables;
		}
		
		$key = $type."_".$tables_str;
		 
		if(!self::$_known_items[$key])
		{
			$db_class = "Oos_DB_" . $type . "_Query";
			$db = new $db_class($tables);
			self::$_known_items[$key] = $db;
		} 
		
		self::connect($type);
		return self::$_known_items[$key];
	}
	
	/**
	 * Factory for database descriptions
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$type	Type of the database
 	 * 
 	 * @return 	Oos_DB_Common_Description
	 */	
	static function description($type)
	{
		if(!self::$_known_descs[$type])
		{
			$desc_class = "Oos_DB_" . $type . "_Description";
			$desc = new $desc_class();
			
			self::$_known_descs[$type] = $desc;
		}
		
		return self::$_known_descs[$type]; 		
	}
	
	/**
	 * Factory for connection handlers
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$type	Type of the database
 	 * 
 	 * @return 	Oos_DB_Common_Access
	 */	
	static function connect($type)
	{
		if(!self::$_known_conns[$type])
		{
			$config = Oos_Config::getInstance();
			
			// connexion
			$host 		= $config->getParam('DB_'.$type, 'HOST');
			$name 		= $config->getParam('DB_'.$type, 'NAME');
			$user 		= $config->getParam('DB_'.$type, 'USER');
			$password 	= $config->getParam('DB_'.$type, 'PASSWORD');
			$port 		= $config->getParam('DB_'.$type, 'PORT');
			$persist 	= $config->getParam('DB_'.$type, 'PERSISTENT');
			
			$connect_class = "Oos_DB_" . $type . "_Access";
			$conn = new $connect_class($host, $name, $user, $password, $port, $persist);
			
			self::$_known_conns[$type] = $conn;
		}
		
		return self::$_known_conns[$type]; 
	}
}