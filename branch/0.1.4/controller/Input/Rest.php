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
 * Exception of the Oos_Input package
 * 
 * @package	Oos_Input
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Input_Rest extends Oos_BaseClass
{
	/**	boolean	Allows us to know if the static class was init already */
	static private $_is_init = false;
	/** array	Data captured via the GET method */
	static private $_data_get;
	/** array	Data captured via the POST method */
	static private $_data_post;
	
	/** boolean	Do we received data via the GET method ? */
	static public $is_get;
	/** boolean	Do we received data via the POST method ? */
	static public $is_post;
	
	/**
	 * Initialization of the static class
	 * Copy all GET and POST parameters into our array and clean them
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @todo	unset $_GET and $_POST data
	 */
	static private function init() 
	{
		if(self::$_is_init) { return ; }
		
		if($_SERVER['REQUEST_METHOD'] == "GET") 
		{
			self::$is_get == true;
		}
		
		if($_SERVER['REQUEST_METHOD'] == "POST") 
		{
			self::$is_post == true;
		}
		
		// copie des données entrantes
		self::$_data_get	= array();
		self::$_data_post 	= array();
		
		foreach($_GET as $key => $val)   
		{ 
			$val = self::processParam($val);
			self::$_data_get[$key] = $val; 
		}
		foreach($_POST as $key => $val) 
		{ 
			$val = self::processParam($val);
			self::$_data_post[$key] = $val;
		}
		
		self::$_is_init = true;
	}
	
	/**
	 * Returns all paramaters given via POST or GET
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	static public function getAllParams() 
	{
		self::init();
		return array_merge(self::getAllGetParams(), self::getAllPostParams());
	}
	
	/**
	 * Returns all paramaters given via GET
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	static public function getAllGetParams() 	
	{ 
		self::init();
		return self::$_data_get; 
	}
	
	/**
	 * Returns all paramaters given via POST
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	static public function getAllPostParams() 	
	{ 
		self::init();
		return self::$_data_post; 
	}
	
	/**
	 * Returns a parameters given via the GET or the POST method.
	 * If the same key was used both in POST and GET, then the POST data will be returned.
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$key	The parameter we are looking for
 	 * @return	string
	 */
	static public function getParam($key) 
	{
		self::init();
		
		$post_value = self::getPostParam($key);
		$get_value 	= self::getGetParam($key);	
		
		if($post_value && !$get_value) 
		{
			return $post_value;
		}
		
		if(!$post_value && $get_value)
		{
			return $get_value;
		}		
		
		if($post_value && $get_value) 
		{
			return $post_value;
		}
		
		return null;
	}
	
	/**
	 * Returns a parameters given via the GET method
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$key	The parameter we are looking for
 	 * @return	string
	 */
	static public function getGetParam($key) 
	{
		self::init();
		return self::$_data_get[$key];
	}
	
	/**
	 * Returns a parameters given via the POST method
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$key	The parameter we are looking for
 	 * @return	string
	 */
	static public function getPostParam($key) 
	{
		self::init();
		return self::$_data_post[$key];
	}
	
	/**
	 * Process a parameters for it to be safe
	 * 
 	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$data	The data to process
 	 * @return	string
	 */
	static public function processParam($data) 
	{
		// remove some special comments
		$data = html_entity_decode($data);
		$data = strip_tags($data, '<br><p><a><b><strong><i><em><u><ul><li><span>');	
		
		if(!get_magic_quotes_gpc()) { $data = addslashes($data); }
		return $data;
	}
}