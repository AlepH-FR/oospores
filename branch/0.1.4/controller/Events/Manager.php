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
 * This class fills 2 roles :
 * . factorying and managing listener
 * . dispatching events
 * 
 * @package	Oos_Events
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Events_Manager extends Oos_BaseClass 
{
	/** array	List of listeners added */
	private static $__listeners = array();
	
	/**
	 * Creates a Oos_Events_Listener and adds it to our list of listeners
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string			$event		name of the event we will listen at
	 * @param	array|string	$function	function we gotta launch in a "call_user_func" format
	 * @param	integer			$priority	priority of the listener, between 0 and 100
	 * @param	array			$args		(opt.) arguments to give to the listening function
	 */	
	public static function addListener($event, $function, $priority = 50, $args = null) 
	{
		if(!self::$__listeners[$event]) 
		{
			self::$__listeners[$event] = array();
		}
		
		$listener = new Oos_Events_Listener($function, $priority, $args);
		self::$__listeners[$event][$listener->getFuncString()] = $listener;
	}
	
	/**
	 * Removes a listener to the events queue
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string			$event		name of the event concerned
	 * @param	array|string	$function	function we had to launch in a "call_user_func" format
	 */	
	public static function removeListener($event, $function) 
	{
		if(self::$__listeners[$event][$function]) 
		{
			unset(self::$__listeners[$event][$function]);
		}
	}
	
	/**
	 * Triggers an event and launches every listeners of this function order by descending priority
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string			$event		name of the event concerned
	 * @param	array			$args		(opt.) arguments to give to the listening function
	 * @return	array(mixed)
	 */
	public static function triggerEvent($event, $args = null) 
	{
		$listeners = self::$__listeners[$event];
		if(!$listeners || !is_array($listeners)) 
		{
			return;
		}
		uksort($listeners, array("Oos_Events_Manager", "sortListeners"));
		
		$results = array();
		foreach($listeners as $function_string => $listener) 
		{
			$results[] = $listener->execute($args);
		}
		
		return $results;
	}
	
	/**
	 * Sorting listeners by priority
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	Oos_Events_Listener		$a		The first listener
 	 * @param	Oos_Events_Listener		$b		The second listener
	 * @return	integer
	 */
	public static function sortListeners($a, $b) 
	{
		$diff = $a->getPriority() - $b->getPriority();
		
		if($diff == 0) 
		{ 
			return 0; 
		}
		return ($diff > 0) ? -1 : 1; 
	}
}