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
 * Objects representing databases' queries
 * Provides util methods in order to expose those simple methods
 *  . request
 *  . simpleRequest (no Oos_DB_Common_Record created)
 *  . delete
 *  . update
 *  . create
 * 
 * @package	Oos_DB
 * @subpackage	Common
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_DB_Common_Query extends Oos_BaseClass 
{
	/** array	Storing queries results */
	static private $_queries = array();
	
	/** string	Name of the main table of the query object */
	protected $_main_table = null;
	/**	array	Tables concerned by this query */
	protected $_tables 	= array();
	/**	array	Fields to update on this query and their values */
	protected $_fields 	= array();
	/**	array	Values to match for this query */
	protected $_values 	= array();
	/**	array	Array of "order" instructions */
	protected $_orders 	= array();
	/**	integer|null	Select entries after the ...th one */
	protected $_limit_begin = null;
	/**	integer|null	Select up to ... entries */
	protected $_limit_end 	= null;
	
	/** Oos_Cache	Cache handler */
	protected $_cache;
	
	/**
	 * Class constructor.
	 * Initialize tables and cache
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	mixed	$tables		(opt.) Tables concerned by this query (null if no table, string for a single table, or an array
	 */
	public function __construct($tables = null) 
	{
		if(is_null($tables)) { return; }
		
		if(is_array($tables)) 
		{
			$this->_tables 		= $tables;
			$this->_main_table	= $tables[0];
		} 
		else 
		{
			$this->_tables[] 	= $tables;
			$this->_main_table	= $tables;
		}
		
		$this->_cache = new Oos_Cache_Memory();
	}
	
	/**
	 * Reset data that was set before for this query, except tables, so that we don't have to build plenty of Query objects
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function reset() 
	{
		$this->_fields = array();
		$this->_values = array();
		$this->_orders = array();
		$this->_limit_begin = null;
		$this->_limit_begin = null;
	}
	
	/**
	 * Add fields to the query
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$fields		Fields to add
	 */
	public function addFields($fields) 
	{ 
		if(!$fields || !is_array($fields)) { return; }
		$this->_fields = array_merge($this->_fields, $fields);
	}
	
	/**
	 * Add news values to the query
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$values		Values to add
	 */
	public function addValues($values) 
	{ 
		if(!$values || !is_array($values)) { return; }
		$this->_values = array_merge($this->_values, $values);
	}
	
	/**
	 * Add an order to the order list
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$order		An order to add
	 */
	public function addOrder($order) 
	{ 
		$this->_orders[] = $order;
	}
	
	/**
	 * Add orders to the order list
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array|string	$orders		Orders to add
	 */
	public function addOrders($orders) 
	{ 
		if(!$orders) { return; }
		
		if(!is_array($orders)) 
		{ 
			$this->addOrder($orders); 
		}
		$this->_orders = array_merge($this->_orders, $orders);
	}
	
	/**
	 * Add limit to the query
	 *  . If the $limit is null, then the limits will be reset
	 *  . If the $limit is an array, then the first value will fill the _limit_begin attribute, the second one the _limit_end attribute
	 *  . If the $limit is an integer, then it will fill the _limit_end attribute and _limit_begin will be 0
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	null|array|integer	$limit		Limit to add
	 */
	public function addLimit($limit) 
	{ 
		if(!$limit) 
		{ 
			$this->_limit_begin = null;
			$this->_limit_end 	= null;
		}
		
		else if(is_array($limit)) 
		{
			list($begin, $end) = $limit;
			
			$this->_limit_begin = $begin;
			$this->_limit_end 	= $end;
		} 
		
		else 
		{
			$this->_limit_begin = 0;
			$this->_limit_end	= intval($limit);
		}
	}
	
	/**
	 * Transform a database result array into an instance of Oos_DB_Common_Record
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$record		A db result fetched as an associative array
 	 * @return	Oos_DB_Common_Record
	 */
	public function loadRecord($record) 
	{
		$fields = $record;
		
		if(count($this->_tables) == 1) 
		{
			$id	= $record[$this->_tables[0].'_ID'];
		} 
		else 
		{
			$id_array = array();
			foreach($this->_tables as $table) 
			{
				$id_array[] = $table.$record[$table.'_ID'];
			}
			
			$id = implode("-", $id_array);
		}
		
		$record_class_name = "Oos_DB_".$this->_db_type."_Record";
		return new $record_class_name($this->_tables, $fields, $id);
	}
	
	/**
	 * Query the database to find a record given its id 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$record		A db result fetched as an associative array
 	 * @return	null|Oos_DB_Common_Record
	 */
	public function findById($value) 
	{
		if(is_object($value)) 
		{ 
			$value = $value->ID; 
		}
		$value = intval($value);
			
		$criteria = array(
			"ID:eq" => $value,
		);
		
		list($result) = $this->select($criteria, null, null);
		return $result;
	}
	
	/**
	 * Alias for the "select" method
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array			$criterias	(opt.) List of where clauses	
 	 * @param	array|string	$order_by	(opt.) List (or lone) order clause(s)
 	 * @param	array|integer	$limit		(opt.) Limit instruction
 	 * @param	array			$fields		(opt.) Fields concerned by the query
 	 * @return	null|Oos_DB_Common_Record
	 */
	public function find($criterias = null, $order_by = null, $limit = null, $fields = null) 
	{
		return $this->select($criterias, $order_by, $limit, $fields);
	}
	
	/**
	 * Launches the proper "SELECT" request depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array(Oos_DB_Common_Record)
	 */
	abstract function doSelect();
	
	/**
	 * Builds and executes a "select" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array			$criterias	(opt.) List of where clauses	
 	 * @param	array|string	$order_by	(opt.) List (or lone) order clause(s)
 	 * @param	array|integer	$limit		(opt.) Limit instruction
 	 * @param	array			$fields		(opt.) Fields concerned by the query
 	 * @return	null|Oos_DB_Common_Record
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */
	public function select($criterias = null, $order_by = null, $limit = null, $fields = null) 
	{
		$this->reset();
		
		if(count($this->_tables) == 0)
		{
			throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
		}
		
		$this->addFields($fields);
		$this->addValues($criterias);
		$this->addOrders($order_by);
		$this->addLimit($limit);
		
		return $this->doSelect();
	}
	
	/**
	 * Launches the proper "COUNT" request depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	abstract function doCount();
	
	/**
	 * Builds and executes a "count" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$criterias	(opt.) List of where clauses	
 	 * @return	integer
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */
	public function count($criterias = null) 
	{
		$this->reset();
		
		if(count($this->_tables) == 0)
		{
			throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
		}
				
		$this->addValues($criterias);
		
		return $this->doCount();		
	}
	
	/**
	 * Returns the id of the last entry inserted  depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	abstract function getInsertId();
	
	/**
	 * Launches the proper "CREATE" request depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	abstract function doCreate();
	
	/**
	 * Builds and executes a "create" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$values		(opt.) Field values concerned by the query
 	 * @return	integer
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */
	public function create($values = null) 
	{
		$this->reset();
		
		if(count($this->_tables) == 0)
		{
			throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
		}
		
		$this->addValues($values);
		
		return $this->doCreate();	
	}
	
	/**
	 * Launches the proper "UPDATE" request depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
	 */
	abstract function doUpdate();
	
	/**
	 * Builds and executes an "update" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array		$fields		(opt.) Field concerned by the query
 	 * @param	array		$criterias	(opt.) Criterias for this update
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */
	public function update($fields = null, $criterias = null) 
	{
		$this->reset();
		
		if(count($this->_tables) == 0)
		{
			throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
		}
		
		$this->addFields($fields);
		$this->addValues($criterias);
		
		return $this->doUpdate();	
	}
	
	/**
	 * Launches the proper "DELETE" request depending on our DB type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
	 */
	abstract function doDelete();	
	
	/**
	 * Builds and executes an "delete" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array		$criterias	(opt.) Criterias for this update
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */
	public function delete($criterias = null) 
	{
		$this->reset();

		if(count($this->_tables) == 0)
		{
			throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
		}
		
		$this->addValues($criterias);
		
		return $this->doDelete();
	}
	
	/**
	 * simpleRequest enables you to send your custom request to your database
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$request		Your request
 	 * @param	boolean	$save_ressource	Saving ressource sent back by your db handler ? (not usefull for updates, delete, etc.)
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
	 */
	public function simpleRequest($request, $save_ressource = true)
	{
		$rs = $this->doQuery($request);
		$rs_class_name = "Oos_DB_".$this->_db_type."_RecordSet";
		$oos_rs = new $rs_class_name($rs, $save_ressource);
		
		return $oos_rs;
	}
	
	/**
	 * Launches a request to the database
	 * 
	 * @version	1.2
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$request		The request that has been built
 	 * 
 	 * @return	array(Oos_DB_Common_Record)
 	 * 
 	 * @todo	Caching data
	 */
	public function request($request) 
	{
		$key = serialize($request);
		if(!self::$_queries[$key])
		{
			$rs = $this->simpleRequest($request);
			if(!$rs->getRaw()) 
			{
				return null;
			}
			
			$result = array();
			while($record = $rs->fetchAssoc()) 
			{
				$result[] = $this->loadRecord($record);
			}
			self::$_queries[$key] = $result;
		}
			
		return self::$_queries[$key];
	}
	
	/**
	 * Do launch a query and return its result
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$request		The request that has been built
 	 * @return	mixed
	 */
	abstract public function doQuery($request);
	
}