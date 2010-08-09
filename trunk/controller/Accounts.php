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
 * Oos_Accounts provides some utilities to parse accounts
 * 
 * @package	Oos_Accounts
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Accounts extends Oos_BaseClass
{
	/** array	Associative table mapping code to their proper account */
	static private $_code_to_account = array();
	
	/**
	 * Getting all accounts on this oos installation
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return 	array
	 */
	static public function getAllAccounts()
	{
		$config = Oos_Config::getInstance();
		$rep = $config->getUsersDir();
		
		if(!$dir = @opendir($rep)) { return; }
		
		$results = array();
		
		while(false !== ($file = readdir($dir))) 
		{
			$path = $rep . DS . $file;
			
			if ($file == "." || $file == "..") 	{ continue; }
			if (!is_dir($path)) 				{ continue; }
			
			$results[] = $file;
		}
		
		return $results;
	}

	/**
	 * Getting the name of an account by its code
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$code	Code of the account we are looking for
	 * @return 	string
	 */
	static public function codeToAccount($code)
	{
		if(count(self::$_code_to_account) == 0)
		{
			$accounts = self::getAllAccounts();
			foreach($accounts as $account)
			{
				$config = Oos_Config::getInstance($account);
				$rep = $config->getConfigDir();
				
				$file = $rep . DS . 'config.ini';
				if(!file_exists($file)) { continue; }
				
				$ini = parse_ini_file($file);
				$account_code = $ini["account"]["code"];
				
				self::$_code_to_account[$account_code] = $account;
			}
		}
		
		return $_code_to_account[$code];
	}
}