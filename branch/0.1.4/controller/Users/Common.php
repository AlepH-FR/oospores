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
 * User-oriented util methods
 * 
 * @package	Oos_Users
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Users_Common extends Oos_BaseClass
{
	/**
	 * Put users "offline" after 10 minutes without any activity on the website.
	 * Activity is stored in files in the Activity directory
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	static public function cleanUsersActivities()
	{
		$config = Oos_Config::getInstance();
		$rep = $config->getActivityAccountDir();
		if(!$dir = @opendir($rep))
		{ 
			return;
		}
		
		while(false !== ($file = readdir($dir))) 
		{
			if ($file == "." || $file == "..")  { continue; }
			
			$path = $rep . DS . $file;
			if (is_dir($path))  				{ continue; }
			
			$last_modified = filectime($path);
			if($last_modified < (time() - 300))
			{
				unlink($path);
			}
		}		
	}
	
	/**
	 * Adds activity for a user
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$user_id	Id of the user going active
 	 * @param	string	$page		Name of the page he went to
	 */
	static public function addUserActivity($user_id, $page)
	{
		if(!$user_id)
		{
			return;
		}
		
		$config = Oos_Config::getInstance();
		$path = $config->getActivityAccountDir();
		file_put_contents($path . DS . $user_id, $page);
	}
	
	
	/**
	 * Look whether a user is connected or not
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$user_id	Id of the user going active
 	 * @return	boolean
	 */
	static public function isUserConnected($user_id)
	{
		if(is_object($user_id)) { $user_id = $user_id->ID; }
		
		$config = Oos_Config::getInstance();
		$path = $config->getActivityAccountDir();
		return file_exists($path . DS . $user_id);
	}
}