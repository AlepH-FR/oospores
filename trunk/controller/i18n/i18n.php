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
 * Managing internationalization.
 * Wordings are stored in .txt files.
 * There is one file for each category, and those files are created in a "language" folder.
 * In those files, wordings are identified by a "key => value" structure.
 * 
 * @package	Oos_i18n
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */ 
class Oos_i18n extends Oos_BaseClass
{
	/**
	 * Returns current language in use on the website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 * @return	string
	 */
	static public function getCurrentLanguage($account = null) 
	{
		global $controller;
		
		$lang =  $controller->session->LANG;
		if(!$lang) 
		{
			$config = Oos_Config::getInstance($account);
			$lang = $config->getParam('I18N', 'DEFAULT_LOCALE');
		}
		
		return $lang;
	}

	/**
	 * Set a new language
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$lang	The new language
	 */	
	static public function setCurrentLanguage($lang) 
	{
		global $controller;
		$controller->session->LANG = $lang;
	}

	/**
	 * Get the path of a category. 
	 * If that file does not exists, then this will create it. 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$category	The category we are looking for
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 * @return 	string
	 * 
	 * @throws	Oos_i18n_Exception	No language defined
	 */		
	static public function getI18nFilePath($category, $account = null) 
	{
		$config = Oos_Config::getInstance($account);
		
		$lang = self::getCurrentLanguage($account);
		if(!$lang) 
		{
			throw new Oos_i18n_Exception("No language defined", OOS_E_FATAL);
		}
		
		$file = $config->getI18nDir() . DS . strtolower($lang) . DS . strtolower($category).'.txt';
		if(!file_exists($file)) 
		{
			file_put_contents($file, '');
		}
		
		return $file;
	}

	/**
	 * Returns all categories for an account
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 * @return 	array
	 * 
	 * @throws	Oos_i18n_Exception	No language defined
	 */		
	static public function getCategories($account = null)
	{
		$config = Oos_Config::getInstance($account);
		
		$lang = self::getCurrentLanguage($account);
		if(!$lang) 
		{
			throw new Oos_i18n_Exception("No language defined", OOS_E_FATAL);
		}
		
		$rep = $config->getI18nDir() . DS . strtolower($lang) . DS;	
		if(!file_exists($rep)) 		{ return array(); }	
		if(!$dir = @opendir($rep)) 	{ return array(); }

		$result = array();
		while(false !== ($file = readdir($dir))) 
		{
			if ($file == "." || $file == "..") { continue; }
		
			$path = $rep.DS.$file;
			if (is_dir($path)) { continue; }
			
			$file = substr($file, 0, strlen($file)-4);
			$result[] = $file;
		}	
		
		return $result;
	}

	/**
	 * Returns all wordings for a category
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$category	The category we are looking for
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 * @return 	array
	 */		
	static public function getItemsByCategory($category, $account = null)
	{
		$file 	= self::getI18nFilePath($category, $account);
		$lines 	= file($file);
		$result = array();
		
		foreach($lines as $line)
		{
			if(strpos($line, "=>") === false) { continue; }
			
			list($key, $value) = explode("=>", $line);
			$key 	= trim($key);
			$value	= trim($value);
			
			$result[$key] = $value;
		}
		
		return $result;
	}

	/**
	 * Write new content in a category
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$category	The category we are looking for
	 * @param 	array	$items		An associative array of items to write
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 */			
	static public function writeCategorie($categorie, $items, $account = null)
	{
		$file 	= self::getI18nFilePath($categorie, $account);
		
		$lines = array();
		foreach($items as $key => $value)
		{
			$lines[] = $key.' => '.$value;
		}
		
		$content = implode("\n", $lines);

		file_put_contents($file, $content);
	}

	/**
	 * Get a string identified by its category and its key.
	 * If no value was found, then the key passed in arguments will be returned
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$category	The category we are looking for
	 * @param 	string	$key		Key of this wording
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 * @return	string
	 */	
	static public function getLocalString($category, $key, $account = null) 
	{
		// on va cherche le bon fichier
		$i18n_file = self::getI18nFilePath($category, $account);
		$data = file_get_contents($i18n_file);
		
		// on supprime les lignes
		$data = preg_replace("[\r\n]", "|", $data);
		$data = preg_replace('/\n/', "|", $data);
		
		// on gère les espaces
		$data = preg_replace("[\t]", " ", $data);
		$data = preg_replace('/\s+/', " ", $data);
		$data = '|'.$data.'|';
		
		// on lance la recherche sur la boucle
		$reg = '/\|'.$key.'\s*=>([^\|]*)\|/i';
		preg_match($reg, $data, $match);
		
		$value = $match[1];
		$value = stripslashes($value);
		
		$result = trim($value);
		if(!$result) { $result = $key; }
		
		return $result;
	}
}