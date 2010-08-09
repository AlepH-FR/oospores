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
 * A factory of system subclasses depending on the environment of the server
 * 
 * @package	Oos_System
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_System extends Oos_BaseClass
{
	/**
	 * Reads what is the current OS
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Oos_System_Exception	Unable to detect the operating system
	 */
	static public function getSystem()
	{
		$system_string = php_uname();
		list($system) = explode(" ", $system_string);
		if(!$system)
		{
			throw new Oos_System_Exception('Unable to detect the operating system', OOS_E_FATAL);
		}
		return ucfirst($system);
	}
	
	/**
	 * Builds a singleton of the right class, extending Oos_System_OS_Common, depending on the environment of the server
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_System_OS_Common
	 */
	static public function factory()
	{
		if(!self::$_instances["system"])
		{
			$system = self::getSystem();
			$system_class = "Oos_System_OS_".$system;	
			self::$_instances["system"] = new $system_class;
		}
		
		return self::$_instances["system"]; 
	}	
}