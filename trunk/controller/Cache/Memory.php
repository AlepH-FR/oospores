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
 * Caching data into memory via Memcache class
 * 
 * @package	Oos_Cache
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo	finalize it and test it !
 */
class Oos_Cache_Memory extends Oos_Cache
{
	/** Memcache	A memcache instance */
	private static $_handler;
	/** boolean		Wheher we are connected to the memcache servers or not */
	private static $_isConnected;

	/** array		Memcache servers */
	public $servers 	= array();
	/** integer		memcache port */
	public $memc_port 	= 11211;
	/**	integer		time we are waiting between two tries, in seconds */
	public $memc_wait 	= 1;
	/** integer		memcache lifetime for values */
	public $memc_default_expire = 43200;
	
	/**
	 * Class constructor.
	 *  . get data from the configuration files
	 *  . instantiate Memcache
	 *  . connect to the servers
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function __construct()
	{
		try
		{	
			class_exists('Memcache');
		}
		catch(Exception $e)
		{
			return ;
		}
		
		if(!self::$_handler)
		{
			$config = Oos_Config::getInstance();
			
			$this->servers 				= $config->getParam("CACHE", 'MEMCACHE_SERVERS');
			$this->memc_default_expire	= $config->getParam("CACHE", 'DEFAULT_LIFETIME');
			$this->memc_default_expire	= Oos_Utils_String::lifetime2seconds($this->memc_default_expire);
			
			
			self::$_handler = new Memcache();
		}
		
		if(!self::$_isConnected)
		{
			$this->connect();
		}
	}
	
	/**
	 * Returning the Memcache instance.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	Memcache
	 */		
	public function getHandler() { return self::$_handler; }
	
	/**
	 * Connect to the servers defined in configuration files
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function connect()
	{
		if(!is_array($this->servers)) { return; }
		
		foreach($this->servers as $server) 
		{
			self::$_handler->addServer($server, $this->memc_port, false, $this->memc_wait);
			self::$_isConnected = self::$_isConnected || self::$_handler->setServerParams($server, $this->memc_port, $this->memc_wait, 15, true);		
		}	
	}

	/**
	 * Stores some content in cache.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @param 	string 	$content	Content of the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 */	
	public function store($key, $content, $lifetime = null)
	{
		if(self::$_isConnected)
		{
			if(is_null($lifetime)) { $lifetime = $this->memc_default_expire; }		
			self::$_handler->set($key, $content, 0, $lifetime);	
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Look if an entry is stored in cache
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @return	boolean
	 */	
	public function isStored($key)
	{
		$value = $this->get($key);
		return isset($value);
	}
	
	/**
	 * Retrieving some data from the cache
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @return	string
	 */		
	public function get($key)
	{
		if(self::$_isConnected)
		{
			return self::$_handler->get($key);
		}
		
		return null;
	}
	
	/**
	 * Flushing the cache whatever the lifetime of each cached value. 
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 */	
	public function flush()
	{
		if(self::$_isConnected)
		{
			self::$_handler->flush();
		}
	}
	
	/**
	 * Flushing a specified cached value
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @return	boolean
	 */	
	public function delete($key)
	{
		if(self::$_isConnected)
		{
			self::$_handler->delete($key);
		}
	}
}