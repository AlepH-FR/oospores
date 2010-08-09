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
 * Managing sessions
 * 
 * @package	Oos_Users
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Users_Session extends Oos_BaseClass 
{
	const USE_MEMCACHE 	= 1;
	const USE_FILES 	= 2;
	
	/** array	Tracking user activity */	
	public $tracking;
	
	/** array	Data stored after connection */
	private $_login_data;

	/**
	 * Class constructor.
	 * Intializes session.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$lifetime	(opt.) Lifetime of sessions. By default : 2 days
 	 * @param	boolean	$trackling	(opt.) Do we track user activity ? By default : false
 	 * @param	array	$options	(opt.) Misc. options depending on the session handler
 	 * 
 	 * @todo	debug memcache
	 */	
	public function __construct($lifetime = 172800, $tracking = false, $options = array()) 
	{		
		// configuration
		ini_set('session.gc_maxlifetime', $lifetime); 	
		ini_set('session.use_cookies', '1'); 
		ini_set('session.use_only_cookies', '1');  	
		
		if($options['method'] == self::USE_MEMCACHE)
		{	
			try
			{	
				class_exists('Memcache');
				ini_set('session.save_handler', 'memcache');
				ini_set('session.save_path', 'tcp://'.$options['server'].':11211?retry_interval=15');
			}
			catch(Exception $e)
			{
				;
			}
		}
		
		$this->tracking = $tracking;
 
		// session start
		session_start();

		// initializing new sessions
		if (!isset($this->id)) 
		{
			$this->id 		= session_id();
			$this->_start  	= time();
			$this->tracker	= array();
		}
		
		$this->_end = time();
	}
	
	/**
	 * Genuine setter.
	 * Puts attribute into $_SESSION
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$key	Attributes to add to session
	 * @param	mixed	$value	Its value
	 */
	public function __set($key, $value) 
	{ 
		$_SESSION[$key] = $value;	
	}
	
	/**
	 * Genuine getter, getting attribute in $_SESSION
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$key	Attributes to get
	 */
	public function __get($key) 
	{ 
		return $_SESSION[$key];
	}
	
	/**
	 * Flag this session as valid
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function setValidSession() 
	{
		$this->_oos_session = "1";
	}
	
	/**
	 * Checks if this session is valid
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean
	 */
	public function isValidSession() 
	{
		$config = Oos_Config::getInstance();
		if($config->isAdmin())
		{
			return true;
		}
		
		return ($this->_oos_session == "1");
	}
	
	/**
	 * Returns the template choosen by the user
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$account	The account on which we are
 	 * @return	string
	 */
	public function getCurrentTemplate($account)
	{
		$key = "template_".$account;
		return $this->{$key};
	}
	
	/**
	 * Changes the current template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$name		Name of the new template
 	 * @param	string	$account	The account on which we are
	 */
	public function setCurrentTemplate($name, $account)
	{
		$key = "template_".$account;
		$this->{$key} = $name;
	}
	
	/**
	 * Tracks users activity
	 * . refresh his activity "counter"
	 * . adds tracking informations if needed
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$page_name		(opt.) The page that the user is currently visiting
	 */
	public function track($page_name = "") 
	{
		// storing activity
		Oos_Users_Common::addUserActivity($this->userId, $page_name);
		
		// tracking the page that the user is currently visiting
		if($this->tracking)
		{
			$this->tracker[time()] = $page_name;
		}
	}
	
	/**
	 * Get the user's role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return string
	 */
	public function getAccessRole() 
	{
		return $this->role;
	}
	
	/**
	 * Logs user in
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$user		User information (his id or an Oos_DB_Common_Record object)
 	 * @param	array	$login_data	Data to add to the session while we log the user in
 	 * @return	boolean
 	 * 
 	 * @throws	Oos_Users_Exception	User data must be an integer or an object
 	 * @throws	Oos_Users_Exception	Additionnal data must be formatted as an array
	 */
	public function login($user, $login_data = null) 
	{
		// si on a objet à un objet record
		if(is_object($user) && $user->ID) 
		{
			$user = $user->ID;
		}
		
		// si a ce moment là on n'a pas un entier, il y a une erreur
		if(!is_int($user)) 
		{
			throw new Oos_Users_Exception("Data must be an integer or an object", OOS_E_WARNING);
			return false;
		}
		
		// on indique l'identifiant de l'utilisateur
		$this->userId = $user;
		
		// et on renseigne les autres champs
		if(!is_null($login_data) && !is_array($login_data))
		{
			throw new Oos_Users_Exception("Additionnal data must be formatted as an array", OOS_E_WARNING);

		}
		else 
		{
			$this->_login_data = $login_data;
			foreach($login_data as $key => $value) 
			{
				$this->{$key} = $value;
			}
		}
		
		return true;
	}

	/**
	 * Logs user out
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function logout() 
	{
		$this->userId = null;
		$this->role = null;
		
		if($this->_login_data) 
		{
			foreach($this->_login_data as $key => $value) 
			{
				$this->{$key} = null;
			}
		}
	}
	
	/**
	 * Checks if the user is connected to the website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean
	 */
	public function isConnected()
	{
		return !is_null($this->userId);
	}

	/**
	 * Get the id of the connected user of this session
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	public function getConnectedUserId() 
	{
		return $this->userId;
	}
}