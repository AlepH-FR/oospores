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
 * Class to instanciate to create an event listener
 * 
 * @package	Oos_Events
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Events_Listener extends Oos_BaseClass 
{
	/** array|string	function we gotta launch in a "call_user_func" format */
	private $_function;
	/** integer			priority of the listener, between 0 and 100 */
	private $_priority;
	/** array			arguments to give to the listening function */
	private $_args = array();
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	array|string	$function	function we gotta launch in a "call_user_func" format
	 * @param	integer			$priority	priority of the listener, between 0 and 100
	 * @param	array			$args		(opt.) arguments to give to the listening function
	 */
	public function __construct($function, $priority, $args)
	{
		 $this->_function 	= $function;
		 $this->setPriority($priority);
		 
		 if(is_null($args)) { return ; }
		 
		 if(!is_array($args)) 
		 {
		 	$args = array($args);
		 }
		 $this->_args		= $args;
	}
	
	/**
	 * Get listener's priority
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	integer
	 */
	public function getPriority()	{ return $this->_priority; }
	
	/**
	 * Set listener's priority to a new value
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$priority	The new priority we wanna set
 	 * @return 	integer
	 */
	public function setPriority($priority)	
	{ 
		 if($priority > 100) 	{ $priority = 100; }
		 if($priority < 0) 		{ $priority = 0; }
		 $this->_priority = $priority;
	}
	
	/**
	 * Transform function into a string so that we could use this key in an associative array
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return 	string
	 */
	public function getFuncString() 
	{
		if(is_array($this->_function)) 
		{
			return implode("::", $this->_function);
		} 
		else 
		{
			return (string) $this->_function;
		}
	}
	
	/**
	 * Execute listener
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$args	More args for the function. They will be merge before (!) the arguments given in the constructor
	 * @return 	mixed
	 */
	public function execute($args) 
	{
		
		$func_args = $this->_args;
		if(is_array($args)) 
		{
			$func_args = array_merge($args, $this->_args);
		}
		
		$func = $this->_function;
		
		if(!is_array($func) && strpos($this->_function, "::") !== false)
		{
			list($class, $method) = explode("::", $this->_function);
			$func = array($class, $method);
		}
		
		return call_user_func_array($func, $func_args);
	}
}