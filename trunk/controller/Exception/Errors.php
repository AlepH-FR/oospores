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
 * Managing errors as Exception
 * 
 * @package	Oos_Exception
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Exception_Errors
{
	/**
	 * Handle errors to generate exceptions for fatal errors.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string 	$errno		Error code
	 * @param 	string	$errstr		Error message
	 * @param 	string	$errfile	Error file
	 * @param 	integer	$errline	Error line
	 * @return	true
	 * 
	 * @throws	Oos_Exception
	 * 
	 * @todo	Manage "warning" errors
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline) 
	{
		switch ($errno) 
		{
			case E_ERROR:
			case E_PARSE:
			case E_USER_ERROR:
			    try {
	    			throw new Oos_Exception($errstr, OOS_E_FATAL);
	    		} catch(Oos_Exception $e) {
	    			$e->handle();
	    		}
				break;
				
			case E_WARNING:
		    case E_USER_WARNING:
		        break;
		
		    default:
		        break;
	    }
	
	    // don't execute internal handler
	    return true;
	}
}