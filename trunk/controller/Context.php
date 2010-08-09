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
 * Oos_Context is a work in progress... or just a bad idea
 * Don't use it !
 * 
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Context extends BaseClass
{
	public $db;
	public $config;
	public $session;
	
	private static $_instances = array();
	
	public static function getInstance($account = null) 
	{	
		if(!isset(self::$_instances[$account])) 
		{
			self::$_instances[$account] = new Oos_Context($account);
		}
		return self::$_instances[$account];
	}

	public function __construct($account)
	{
		// initialize configuration
		$this->config = new Oos_Config($account);

		// managing sessions
		$lifetime = $this->config->getParam("SESSION", "LIFETIME");
		if(!$lifetime) { $lifetime = $this->config->getParam("SESSION", "LIFETIME"); }
		
		$time = Oos_Utils_String::lifetime2seconds($lifetime);
		if(!$time)
		{
			throw new Oos_Users_Exception('Lifetime syntax is not properly formatted', OOS_E_FATAL);
		}
	
		$tracking 	= $this->config->getParam("SESSION", "TRACKING");
		$method 	= $this->config->getParam("SESSION", "METHOD");
		$server		= $this->config->getParam("SESSION", "MEMCACHE_SERVER");
		$options = array(
			'method'	=> $method,
			'server'	=> $server,
		);
		$this->session = new Oos_Users_Session($time, $tracking, $options);		
	}
	
	public function __set($field, $value)
	{
		return ;
	}
}

function ctx($account)
{
	return Oos_Context::getContext($account);
}
?>