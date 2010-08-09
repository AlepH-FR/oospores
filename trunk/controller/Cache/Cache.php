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
 * Abstract class to extends for any cache handler.
 * 
 * @package	Oos_Cache
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo	!!! this all package is still a work in process and far away from being stable
 * @todo	link cache handler to databases ? Maybe we'll need a factory to construct thoses handlers
 * @todo	manage cache keys
 * @todo	implement some methods for pages'n blocks
 */
abstract class Oos_Cache extends Oos_BaseClass
{
	/**	string	the current key used for caching */
	private $_stamp_key 		= null;
	/** integer	life time of the cache data in seconds */
	private $_stamp_life_time 	= null;
	
	/**
	 * Class destructor.
	 * If a cache operation is still in process, then end it
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 */
	public function __destruct()
	{
		if(!is_null($this->_stamp_key))
		{
			$this->end();
		}
	}
	
	/**
	 * Alias for the store method
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @param 	string 	$content	Content of the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 */
	public function set($key, $content, $lifetime = null)
	{
		$this->store($key, $content, $lifetime);
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
	abstract public function store($key, $content, $lifetime = null);
	
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
	abstract public function isStored($key);
	
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
	abstract public function get($key);
	
	/**
	 * Flushing the cache whatever the lifetime of each cached value. 
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 */
	abstract public function flush();
	
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
	abstract public function delete($key);

	/**
	 * Start buffering output
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 */
	public function start($key, $lifetime = null)
	{
		if(!is_null($this->_stamp_key))
		{
			throw new Oos_Cache_Exception("We cannot open 2 buffers simultaneously", OOS_E_FATAL);
		}
		$this->_stamp_life_time = $lifetime;
		$this->_stamp_key 		= $key;
		ob_start();
	}
	
	/**
	 * Stop buffering.
	 * Store data collected and reset stamp data.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function end() 
	{
		$content = ob_get_contents();
		ob_end_clean();
		$this->store($this->_stamp_key, $content, $this->_stamp_life_time);
		$this->_stamp_key 			= null;
		$this->_stamp_life_time 	= null;
	}

	/**
	 * Launches a function and store it's data into cache.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$function	Array or string for the function to be called via call_user_func_array
	 * @param 	array	$args		(opt.) Key for the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 * @return	mixed
	 */
	public function cacheFuncResult($function, $args = null, $lifetime = null)
	{
		if(is_array($function))
		{
			$key = implode("_", $function);
		}
		else
		{
			$key = (string) $function;
		}
		
		if(is_array($args))
		{
			$key.= implode("_", $args);
		}
		$key.= "_result";
		
		if(!($res = $this->isStored($key)))
		{
			$res = call_user_func_array($function, $args);
			$this->set($key, $res, $lifetime);
		}
		
		return $this->get($key);
	}

	/**
	 * Launches a function and store the output this function has written out.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$function	Array or string for the function to be called via call_user_func_array
	 * @param 	array	$args		(opt.) Key for the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 * @return	string
	 */	
	public function cacheFuncOutput($function, $args = null, $lifetime = null)
	{
		if(is_array($function))
		{
			$key = implode("_", $function);
		}
		else
		{
			$key = (string) $function;
		}
		
		if(is_array($args))
		{
			$key.= implode("_", $args);
		}
		$key.= "_print";
		
		if(!($res = $this->isStored($key)))
		{
			$this->start($key, $lifetime);
			call_user_func_array($function, $args);
			$this->end();
		}
		
		$data = $this->get($key);
		return $data;
	}
	
	/**
	 * Instanciates a class and store the output the instance has written out.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$class_name	String of the class' name to instanciate
	 * @param 	array	$args		(opt.) Key for the cached data
	 * @param 	integer	$lifetime	(opt.) Lifetime of this cached value
	 * @return	string
	 */
	public function cacheClass($class_name, $args = null, $lifetime = null)
	{
		$key = $class_name;
		
		if(!($res = $this->isStored($key)))
		{
			$this->start($key, $lifetime);
			new $class_name($args);
			$this->end();
		}
		
		return $this->get($key);
	}
}