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
 * Accessing and connecting SQL daemons
 * 
 * @package	Oos_DB
 * @subpackage	SQL
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_DB_SQL_Access extends Oos_BaseClass 
{
	/** string	Name of the server host */
	private $_host;
	/** string 	Name of the database we'll try to access*/
	private $_dbname;
	/** string 	Username with which we'll try to access to database*/
	private $_user;
	/** string 	And its password*/
	private $_password;
	/** integer	Port to access MySQL daemon*/
	private $_port;
	/** boolean	Persistant connection or sleeping one ? */
	private $_persistent;
	
	/** ressource	Real connection handler */
	private $_sql_connexion;
	/** boolean		Are we connected to the database */
	private $_db_connexion;
	
	/**
	 * Class constructor.
	 * Connects to the specified host, then to the specified database.
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$host		(opt.) Name of the server host
	 * @param 	string	$dbname		Name of the database we'll try to access
	 * @param 	string	$user		(opt.) Username with which we'll try to access to database
	 * @param 	string	$password	(opt.) And its password
	 * @param 	integer	$port		(opt.) Port to access MySQL daemon
	 * @param 	boolean	$persistent	(opt.) Persistant connection or sleeping one ?
	 * 
	 * @throws	Oos_DB_Exception	MySQL is not supported on this server
	 * @throws	Oos_DB_Exception	Unable to connect to the MySQL daemon
	 * @throws	Oos_DB_Exception	Unable to connect tu the specified database
	 */
	public function __construct($host = 'localhost', $dbname, $user = 'root', $password = '', $port = 3306, $persistent = false)
	{
		$this->_host 	= $host;
		$this->_dbname 	= $dbname;
		$this->_user 	= $user;
		$this->_password = $password;
		$this->_port 	= $port;
		$this->_persistent = $persistent;
		
		if(!function_exists('mysql_connect')) 
		{
			throw new Oos_DB_Exception("MySQL is not supported on this server", OOS_E_FATAL);
		}
		
		if ($this->_persistent) 
		{
			$this->_sql_connexion = mysql_pconnect($this->_host.':'.$this->_port, $this->_user, $this->_password);
		} 
		else 
		{
			$this->_sql_connexion = mysql_connect($this->_host.':'.$this->_port, $this->_user, $this->_password);
		}
		
		if(!$this->_sql_connexion) 
		{
			throw new Oos_DB_Exception("Unable to connect to the MySQL daemon", OOS_E_FATAL);
		}
	
		if ($this->_dbname) 
		{
			$this->_db_connexion = mysql_select_db($this->_dbname);
		}
		
		if(!$this->_db_connexion) 
		{
			throw new Oos_DB_Exception("Unable to connect to the specified database '".$this->_db_name."'", OOS_E_FATAL);
		}
	}
	
	/**
	 * Class destructor.
	 * Forces instance to close the MySQL connection
	 * @see		Oos_DB_SQL_Access::close
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function __destruct() 
	{
		$this->close();
	}
	
	/**
	 * Close the MySQL connection
	 * 
	 * @version	1.0
 	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function close() 
	{
		mysql_close($this->_sql_connexion);
	}
}