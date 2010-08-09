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
 * Oos_Autoload aim is quite obvious.
 * @see		__autoload
 * 
 * @package	OOS_Accounts
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @since	0.1.4
 */
class Oos_Autoload extends Oos_BaseClass
{
	/**
	 * Looking for a class file
	 * v1.0 : looking for class in specified directories. Controller & Model directories for core & current site account
	 * v2.0 : looking for class in a "Zend fashion" 
	 * 
	 * @version	2.0
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$class 	The class we are looking for
	 * @return 	string|false
	 * 
	 * @since	0.1.4
	 * 
	 * @return 	string|false
	 */
	public static function findFile($class) 
	{
		$reps = explode("_", $class);
		$account_code = array_shift($reps);
		
		// v1
		if($account_code != 'Oos')
		{
			$first_letter = substr($class, 0, 1);
			if($first_letter == "_"
			|| strpos($class, "_") === false
			|| $first_letter != strtoupper($first_letter))
			{
				// liste des répertoire de recherche par défauts
				$config 	= new Oos_Config(_OOS_ACCOUNT);
				$config_bo	= new Oos_Config($config->getParam('SITE', "BO_ACCOUNT"));
				$reps = array(
					1 => $config->getModelDir(),
					2 => $config->getControllerDir(),
					3 => $config->getModelDir(),
					4 => $config_bo->getModelDir(),
					5 => $config_bo->getControllerDir(),
					6 => $config_bo->getModelDir(),
				);
				
				// parcours des répertoires
				foreach($reps as $rep) 
				{
					$system = Oos_System::factory();
					$classFound =  $system->findFileInDirectory($class, $rep, array("php", "inc"));
					
					if($classFound) 
					{ 
						return $classFound;
					}
				}
				
				return false;
			}
		}
		
		// v2 
		if($account_code == 'Oos') 
		{ 
			$base_path = ROOT_DIR . DS . 'controller';
		}
		else
		{
			$config = Oos_Config::getInstance();
			$account = Oos_Accounts::codeToAccount($account_code);
			$base_path = $config->getAccountDir();	
		}
		
		foreach(array('php', 'inc') as $ext)
		{
			// module subclasses
			$file_path = $base_path . DS . implode(DS, $reps) . '.' . $ext;
			if(file_exists($file_path)) 
			{
				return $file_path;
			}
			
			// module's main 
			$last_rep = $reps[count($reps)-1];
			$file_path = $base_path . DS . implode(DS, $reps) . DS . $last_rep . '.' . $ext;
			if(file_exists($file_path)) 
			{
				return $file_path;
			}
		}		
		
		return false;
	}
}

/**
 * Looking for a class...
 * @see		http://php.net/manual/en/language.oop5.autoload.php 
 * 
 * @version	1.0
 * @since	0.1.4
  *@author	Antoine Berranger <antoine@oospores.net>
 * 
 * @param 	string	$class 	The class we are looking for
 * 
 * @throws	Oos_Exception	class not found
 */
function __autoload($class) 
{
	$file = Oos_Autoload::findFile($class);
	
	if($file) 
	{	
		require_once($file);
		return; 
	}
	
	throw new Oos_Exception('Required class "'.$class.'" not found', OOS_E_FATAL);
}