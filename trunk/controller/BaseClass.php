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
 * Oos_BaseClass is OOSpores... base class.
 * This class provides some usefull methods for every classes of this project
 * 
 * @package	Oos_BaseClass
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @since	0.1.4
 */
abstract class Oos_BaseClass 
{
	/** array	Associative array of singletons */
	protected static $_instances = array();
	
	/**
	 * Building a single instance of this class.
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$account	Name of the account on which we are instanting this class
	 * @return 	Object
	 * 
	 * @throws	Oos_Exception	Unable to find the class name
	 * 
	 * @todo	get_called_class is PHP 5.3 ! Gotta provide a function for inferior versions
	 */
	public static function getInstance($account = null) 
	{		
		if(is_null($account)) { $account = _OOS_ACCOUNT; }

		$called_class = get_called_class();
				
		if(!$called_class || !class_exists($called_class))
		{
			throw new Oos_Exception("Unable to find the singleton class", OOS_E_FATAL);
		}
		
		// ajout à la table des singletons
		if(!isset(self::$_instances[$called_class.$account])) 
		{
			self::$_instances[$called_class.$account] = new $called_class($account);
		}
		return self::$_instances[$called_class.$account] ;
	}
}
?>