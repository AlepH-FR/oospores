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
 * Managing queries on a SQL database.
 * 
 * @package	Oos_DB
 * @subpackage	SQL
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_SQL_Query extends Oos_DB_Common_Query 
{
	/**	string	On what type of database we are. Maybe idiot, but it's saving time and ressources */
	protected $_db_type = "SQL";
	
	/** array	Identifiers of the tables of this query object */
	protected $_tables_ids = array();
	
	/**
	 * Getting a table code given its name. Each table is being identified in the SQL request so that we can use join structures.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$name	Name of the table we are looking for
 	 * @return	string
 	 * 
 	 * @throws	Oos_DB_Exception	No such table defined for this request
	 */	
	private function getTableCode($name) 
	{
		$code = $this->_tables_ids[$name];
		
		if(!$code) 
		{
			$code = $this->_tables_ids[$name];
		}
		
		if(!$code) 
		{
			throw new Oos_DB_Exception("No such table '" . $name ."' defined for this request", OOS_E_FATAL);
			return false;
		}
		
		return $code;
	}
	
	/**
	 * Get tables string for the request, like 
	 * <code>"SELECT * FROM ".$table_string</code>
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Oos_DB_Exception	No table defined for this request
	 */	
	private function getTablesString() 
	{
		switch(count($this->_tables)) 
		{
			case 0:
				throw new Oos_DB_Exception("No table defined for this request", OOS_E_FATAL);
				break;
				
			case 1:
				$this->_tables_ids[$this->tables[0]] = 1;
				return $this->_tables[0].' T1';
				break;
				
			default:
				$cpt 	= 1;
				$tables	= array();
				foreach($this->_tables as $table) 
				{
					$this->tables_ids[$table] = $cpt;
					$tables[] = $table.' T'.$cpt;
					$cpt++;
				}
				
				return implode(',', $tables);
		}
	}
	
	/**
	 * Construct "WHERE" clauses
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array|null	$sub_fields		(opt.) Use for iterative purposes
 	 * @param	boolean		$mode_no_table	(opt.) If true, we won't use table's identifiers
 	 * @return	string
 	 * 
 	 * @throws	Oos_DB_Exception	A sub criteria must be an array
 	 * @throws	Oos_DB_Exception	Wrong operator type AND or OR permitted
	 */	
	private function getWhereClause($sub_fields = null, $mode_no_table = false) 
	{
		$database = Oos_DB::description($this->_db_type);
		
		$where = array();
		$operande = "AND";
		
		$fields = ($sub_fields)? $sub_fields : $this->_values;
		
		if(!$fields)
		{
			$where[] = "1=1";
		}
		 
		foreach($fields as $field => $value) 
		{			
			list($field_sep, $op) = explode(":", $field);
			
			if($field_sep) {
				$field = $field_sep;
			}
			
			// sub criterias
			if($op == "sub") 
			{
				if(!is_array($value)) 
				{
					throw new Oos_DB_Exception("A sub criteria must be an array", OOS_E_FATAL);
				}
				$where[] = " (". $this->getWhereClause($value). ") ";
				continue;
			}
			
			// logical operatifs
			if($op == "op") 
			{
				$operande = strtoupper($value);
				if($operande != "OR" && $operande != "AND") 
				{
					throw new Oos_DB_Exception("Wrong operator type '".$operande."'. AND or OR permitted", OOS_E_FATAL);
					$operande = "AND";
				}
				continue;
			}
			
			// key
			if(strpos($field, ".") !== false) 
			{
				try {
					$field_object = $database->getField($field);
				} catch(Exception $e) {
					list($tableName, $field) = explode('.', $field);
					$field = $tableName.'_'.$field;
					$field_object = $database->getField($field, $table_name);
				}
				
				$where_key = 'T'.$this->getTableCode($tableName).'.'.$field;
			} 
			else 
			{
				try {
					$field_object = $database->getField($field);
				} catch(Exception $e) {
					$field = $this->_main_table.'_'.$field;
					$field_object = $database->getField($field, $this->_main_table);
				}
				
				if($mode_no_table) 	{ $where_key = $field; }
				else 				{ $where_key = 'T1.'.$field; }
			}
						
			
			// value
			if(strpos($value, ".") !== false) 
			{
				list($tableName, $valueName) = explode('.', $value);
				
				try {
					$tableCode = $this->getTableCode($tableName);
					$where_value = 'T'.$tableCode.'.'.$tableName.'_'.$valueName;
				} catch(Exception $e) {
					$where_value = $field_object->getWhereClauseValue($value);
				}
				
			} 
			else 
			{
				$where_value = $field_object->getWhereClauseValue($value);
			}		

			$clause = $this->buildWhereClause($field_object, $where_key, $value, $where_value, $op);
			
			$where[] = $clause;
		}
		
		return implode(" ".$operande." ", $where);
	}
	
	/**
	 * Builds "WHERE" clause on a specified field depending on its type
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	Oos_DB_Common_Field	$field_object	Object of this field
 	 * @param	boolean				$where_key		Key for this field object
 	 * @param	boolean				$value			Value that we are looking for
 	 * @param	string				$where_value	The value formatted given the field's type
 	 * @param	boolean				$op				Logical operator
 	 * 
 	 * @return	string
	 */	
	private function buildWhereClause($field_object, $where_key, $value, $where_value, $op)
	{		
		switch($field_object->getType())
		{
			case "int":
			case "float":
			case "timestamp":
			case "date":
				$clause = $this->buildWhereClauseInt($where_key, $value, $where_value, $op);
				break;
			case "joint":
			case "multijoint":
				
				if (count(split('\.',$where_value)) > 1)
				{
					$clause = $this->buildWhereClauseJoint($where_key, $value, $where_value, $op);
					break;
				}
			default:
				$clause = $this->buildWhereClauseCommon($where_key, $value, $where_value, $op);
				break;
		}
		
		return $clause;
	}

	/**
	 * Builds "WHERE" for "int" fields
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean				$where_key		Key for this field object
 	 * @param	boolean				$value			Value that we are looking for
 	 * @param	string				$where_value	The value formatted given the field's type
 	 * @param	boolean				$op				Logical operator (like | eq | ne | lt | le | gt | ge | reg | nreg)
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Wrong operand type 
	 */	
	private function buildWhereClauseInt($where_key, $value, $where_value, $op)
	{
		switch($op) 
		{
			case null:
			case "":
				$where_value = '%' . $where_value . '%';
			case "like":
				$clause = $where_key . " LIKE '" . $where_value . "' ";
				break;	
			case "eq": 
				if($value == "" || $value == null)
				{
					$clause = "(" . $where_key . " = '' OR " . $where_key . " IS NULL)";
				}
				else
				{
					$clause = $where_key . " = '" . $where_value . "' ";
				}
				break;
			case "ne":
				if($value == "" || $value == null)
				{
					$clause = "(" . $where_key . " != '' OR " . $where_key . " IS NOT NULL)";
				}
				else
				{
					$clause = $where_key . " != '" . $where_value . "' ";
				}
				break;
			case "lt" :
				$clause = $where_key . " < '" . $where_value . "' ";
				break;
			case "le" :
				$clause = $where_key . " <= '" . $where_value . "' ";
				break;
			case "gt" :
				$clause = $where_key . " > '" . $where_value . "' ";
				break;
			case "ge" :
				$clause = $where_key . " >= '" . $where_value . "' ";
				break;
			case "reg" :
				$clause = $where_key . " REGEXP '" . $where_value . "'";
				break;
			case "nreg" :
				$clause = $where_key . " NOT REGEXP '" . $where_value . "'";
				break;
			default:
				throw new Oos_DB_Exception("Wrong operand type '" . $op . "'", OOS_E_FATAL);
				break;
		}
		
		return $clause;
	}

	/**
	 * Builds "WHERE" for "joint" fields
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean				$where_key		Key for this field object
 	 * @param	boolean				$value			Value that we are looking for
 	 * @param	string				$where_value	The value formatted given the field's type
 	 * @param	boolean				$op				Logical operator (eq | ne)
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Wrong operand type 
	 */	
	private function buildWhereClauseJoint($where_key, $value, $where_value, $op)
	{
		switch($op) 
		{
			case null:
			case "":
			case "eq":
				$clause = $where_key . " = " . $where_value . " ";
				break;
				
			case "ne":
				$clause = $where_key . " != " . $where_value . " ";
				break;
				
			default:
				throw new Oos_DB_Exception("Wrong operand type '" . $op . "'", OOS_E_FATAL);
				break;
		}
		
		return $clause;
	}

	/**
	 * Builds "WHERE" for other fields
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean				$where_key		Key for this field object
 	 * @param	boolean				$value			Value that we are looking for
 	 * @param	string				$where_value	The value formatted given the field's type
 	 * @param	boolean				$op				Logical operator (like | eq | ne | lt | le | gt | ge | reg | nreg)
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Wrong operand type 
	 */
	private function buildWhereClauseCommon($where_key, $value, $where_value, $op)
	{
		switch($op) 
		{
			case null:
			case "":
				$where_value = '%' . $where_value . '%';
			case "like":
				$clause = $where_key . " LIKE '" . $where_value . "' ";
				break;	
			case "eq": 
				if($value == "" || $value == null)
				{
					$clause = "(" . $where_key . " = '' OR " . $where_key . " IS NULL)";
				}
				else
				{
					$clause = $where_key . " = '" . $where_value . "' ";
				}
				break;
			case "ne":
				if($value == "" || $value == null)
				{
					$clause = "(" . $where_key . " != '' OR " . $where_key . " IS NOT NULL)";
				}
				else
				{
					$clause = $where_key . " != '" . $where_value . "' ";
				}
				break;
			case "lt" :
				$clause = "CONVERT(" . $where_key.",signed) < " . $value;
				break;
			case "le" :
				$clause = "CONVERT(" . $where_key.",signed) <= " . $value;
				break;
			case "gt" :
				$clause = "CONVERT(" . $where_key.",signed) > " . $value;
				break;
			case "ge" :
				$clause = "CONVERT(" . $where_key.",signed) >= " . $value;
				break;
			case "reg" :
				$clause = $where_key . " REGEXP '" .  $value . "'";
				break;
			case "nreg" :
				$clause = $where_key . " NOT REGEXP '" .  $value . "'";
				break;
			default:
				throw new Oos_DB_Exception("Wrong operand type '" . $op . "'", OOS_E_FATAL);
				break;
		}
		
		return $clause;
	}

	/**
	 * Builds "ORDER BY" string for our SQL request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	private function getOrderBy() 
	{
		if(count($this->_orders) == 0) 
		{
			return "";
		}
		
		$processed_orders = array();
		foreach($this->_orders as $order)
		{
			list($order_field, $mode) = explode(":", $order);
			if(!$order_field) 
			{
				$order_field = $order;
			}
			
			if(strpos($order_field, ".") !== false) 
			{
				list($tableName, $order_field) = explode('.', $order_field);
				$order_str = $tableName.'_'.$order_field;
			} 
			else 
			{
				$db = Oos_DB::description($this->_db_type);
				if($db->fieldExists($order_field, $this->_main_table))
				{
					$order_str = $order_field;
				}
				else
				{
					$order_str = $this->_main_table.'_'.$order_field;
				}
			}
			
			switch(strtoupper($mode))
			{
				case "ASC":
				case "DESC":
					break;
				default:
					$mode = "DESC";
			}
			
			$order_str.= " ".$mode;
			
			$processed_orders[] = $order_str;
		}
		
		return "ORDER BY ".implode(",", $processed_orders);
	}

	/**
	 * Builds "LIMIT" string for our SQL request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	private function getLimit() 
	{
		if($this->_limit_begin && $this->_limit_end) 
		{
			$limit = "LIMIT " . $this->_limit_begin . "," . $this->_limit_end;
		} 
		elseif($this->_limit_end) 
		{
			$limit = "LIMIT " . $this->_limit_end;
		}
		
		return $limit;
	}

	/**
	 * Get selected fields string for the request, like 
	 * <code>"SELECT ".$selected_fields." FROM"</code>
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getSelectedFields() 
	{
		// if no fields are set, we select everything
		if(count($this->_fields) == 0)
		{
			return "*";
		}
		
		// else we process specified fields
		$fields = $this->_fields;
		foreach($fields as $key => $field)
		{
			if(strpos($field, ".") !== false) 
			{
				list($tableName, $field) = explode('.', $field);
				$field = $tableName . '_' . $field;
				$fields[$key] = 'T' . $this->getTableCode($tableName) . '.' . $field;
			} 
			else 
			{
				$fields[$key] = $this->_main_table . '_' . $field;
			}			
		}
		
		return implode(",", $fields);
	}
	
	/**
	 * Launches the SQL "SELECT" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array(Oos_DB_Common_Record)
	 */
	public function doSelect() 
	{
		$fields 		= $this->getSelectedFields();
		$tables 		= $this->getTablesString();
		$whereClause	= $this->getWhereClause();
		$order_by		= $this->getOrderBy();
		$limit			= $this->getLimit();
		
		$request = "SELECT " . $fields . " FROM " . $tables . " WHERE " . $whereClause . " " . $order_by . " " . $limit;
		return $this->request($request);
	}
	
	/**
	 * Launches the SQL "COUNT" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	public function doCount() 
	{
		$tables 		= $this->getTablesString();
		$whereClause	= $this->getWhereClause();
		$order_by		= $this->getOrderBy();
		$limit			= $this->getLimit();
		
		$request = "SELECT COUNT(*) FROM " . $tables . " WHERE " . $whereClause;
				
		$rs = $this->simpleRequest($request);
		$resultat = $rs->fetchRow();
		return $resultat[0];
	}
	
	/**
	 * Returns the id of the last entry inserted in the database
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	public function getInsertId() 
	{
		return mysql_insert_id();
	}
	
	/**
	 * Launches the SQL "UPDATE" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
	 */
	public function doUpdate() 
	{
		list($table) 	= $this->_tables;
		$whereClause	= $this->getWhereClause();
		
		$values = array();
		foreach($this->_fields as $field => $value) 
		{
			$values[] = $field . " = '" . $value . "'";
		}		
		
		$values_str = implode(",", $values);
		
		$request = "UPDATE " . $table . " T1 SET " . $values_str . " WHERE " . $whereClause;
		$result = mysql_query($request);
		
		return $this->simpleRequest($request, false);
	}
	
	/**
	 * Launches the SQL "DELETE" request
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_DB_Common_RecordSet
	 */
	public function doDelete() 
	{
		list($table) 	= $this->_tables;
		$whereClause	= $this->getWhereClause(null, true);
		
		$request = "DELETE FROM " . $table . " WHERE " . $whereClause;
		return $this->simpleRequest($request, false);
	}
	
	/**
	 * Launches the SQL "CREATE" request and return the id of this new entry
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	public function doCreate() 
	{
		list($table) 	= $this->_tables;
		
		$fields = array();
		$values = array();
		
		foreach($this->_values as $field => $value) 
		{
			$fields[] = "`".$field."`";
			$values[] = "'".$value."'";
		}
		
		$fields_str = implode(",", $fields);
		$values_str = implode(",", $values);
		
		$request = "INSERT INTO " . $table . " (" . $fields_str . ") VALUES (" . $values_str . ")";
		$this->simpleRequest($request, false);

		$id = $this->getInsertId();	
		return $id;
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
	public function doQuery($request)
	{
		return mysql_query($request);
	}
}