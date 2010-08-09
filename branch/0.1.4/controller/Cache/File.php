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
 * Caching data into files
 * 
 * @package	Oos_Cache
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo	finalize it and test it !
 */
class Oos_Cache_File extends Oos_Cache
{
	/**	string	Path to the cache folder */
	private $_path;
	/** string	Extension for cache files */
	private $_ext;
	/** integer	Lifetime of cached date */
	private $_default_expire;
	
	/**
	 * Class constructor.
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __construct()
	{
		$config = Oos_Config::getInstance();
		$this->_default_expire = $config->getParam("CACHE", 'DEFAULT_LIFETIME');
		
		$this->_path = $config->getCacheDir();
		if(!file_exists(self::$path))
		{
			mkdir(self::$path, 0777, true);	
		}
		
		$this->_ext  = '.cache';
	}
	
	/**
	 * Return the file path for a specified key
	 * 
	 * @version	0.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$key		Key for the cached data
	 * @return 	string
	 */
	public function getPath($key)
	{
		return $this->_path . DS . $key . $this->_ext;
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
		if(is_null($lifetime)) { $lifetime = $this->_default_expire; }
		file_put_contents($this->getPath($key), $content);
		
		return true;
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
		return file_exists($this->getPath($key));
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
		return file_get_contents($this->getPath($key));
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
		if(!$dir = @opendir($this->_path))
		{ 
			return;
		}
		
		while(false !== ($file = readdir($dir))) 
		{
			if ($file == "." || $file == "..") 
			{
				continue;
			}
			
			$path = $this->_path . DS . $file;
			
			if (is_dir($path)) 
			{ 
				continue;
			}
			
			unlink($path);
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
		unlink($this->getPath($key));
	}
}