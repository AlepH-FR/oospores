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
 * Oos_Config provides maaaany usefull methods and mainly
 *  . getting dirs of this account
 *  . getting params in this account configuration files
 * 
 * @package	Oos_Config
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Config extends Oos_BaseClass 
{
	/** string	Name of the current account */
	private $_account;
	
	/** array	Hash table with every configuration variables */
	private $_config = array();
	
	/**
	 * Constructor, parsing OOS config files & account config files
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$account	Name of the account
	 * 
	 * @todo	protect oos non-rewritable config variables
	 */
	public function __construct($account) 
	{
		$this->_account = $account;
	
		$rep = $this->getConfigDir(true);
		$this->parseForIniFile($rep, true);
		
		$rep = $this->getConfigDir();
		$this->parseForIniFile($rep);
	}
		
	/**
	 * Checking for .ini files in the specified directory and adding configuration parameters to the config object
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$rep	Path to the directory to parse
	 * @param	boolean	$oos	(opt.) Is this an OOS config file ?
	 * 
	 * @throws	Oos_Exception	Empty ini file
	 */
	public function parseForIniFile($rep, $oos = false)
	{
		if(!$dir = opendir($rep)) { return false; }
		
		while(false !== ($file = readdir($dir)))
		{
			// only files...
			if ($file == "." || $file == "..")  { continue; }
			if (is_dir($path)) 					{ continue; }
			
			$path = $rep . DS . $file;
			$path_parts = pathinfo($path);
			if($path_parts['extension'] != 'ini') { continue; }
	
			// adding to config
			$ini_array = parse_ini_file($path, true);
			
			if(count($ini_array) == 0)
			{
				throw new Oos_Exception("Empty ini file '".$file."' on path '".$rep, OOS_E_WARNING);
			}
			
			foreach($ini_array as $key => $value)
			{
				if($oos && array_key_exists($key, $this->_config)) { continue; }
				$this->_config[$key] = $value;
			}
		}		
	}
	
	/**
	 * Accessing any configuration variable
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$categorie	The category where to find the variable
	 * @param 	string	$key		Name of the variable
	 * @return	string	
	 */
	public function getParam($categorie, $key) 
	{
		$categorie 	= strtolower($categorie);
		$key 		= strtolower($key);
		
		switch($categorie) 
		{
			case "DIR":	
				return realpath($this->_config[$categorie][$key]);
			default:
				return $this->_config[$categorie][$key];
		}
	}
	
	/**
	 * Getting OOS version
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return string
	 */
	public function getVersion() 
	{
		$version 	= $this->getParam('INFO', "version");
		$mode 		= $this->getParam('INFO', "mode");
		return $version." ".$mode;
	}
	
	/**
	 * Getting the left separator for variables insertion in templates
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getVarsLeftSep() 	
	{ 
		$seps = $this->getParam('DEV', "VARIABLES_SEPARATOR"); 
		return $seps[0]; 
	}
	
	/**
	 * Getting the right separator for variables insertion in templates
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getVarsRightSep() 	
	{ 
		$seps = $this->getParam('DEV', "VARIABLES_SEPARATOR"); 
		return $seps[1]; 
	}
	
	/**
	 * Return a normalize string from a markup name or markup pattern
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$string	The string of the markup
	 * @return	string
	 */
	public function getMarkup($string)
	{
		$markup = $this->getVarsLeftSep() . $string . $this->getVarsRightSep();
		return $markup;
	}
	
	/**
	 * Returns the "users" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getUsersDir() 		{ return ROOT_DIR . DS. 'users'; }
	
	/**
	 * Returns the "controller" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getControllerDir()	{ return ROOT_DIR . DS . 'controller'; }
	
	/**
	 * Returns the "config" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	boolean	$oos	(opt.) Config directory for the cms differs slightly
	 * @return	string
	 */
	public function getConfigDir($oos = false)	
	{ 
		if($oos) { return ROOT_DIR . DS . 'config'; }
		
		return $this->getAccountDir() . DS . 'config';
	}

	/**
	 * Returns the "account" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getAccountDir()  	{ return $this->getUsersDir() . DS . $this->_account; }

	/**
	 * Returns the "model" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getModelDir() 		{ return $this->getAccountDir() . DS . 'model'; }
	
	/**
	 * Returns the "view" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getViewDir() 		{ return $this->getAccountDir() . DS . 'view'; }
	
	/**
	 * Returns the "cron" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getCronDir() 		{ return $this->getAccountDir() . DS . 'cron'; }

	
	/**
	 * Returns the "www" dir where we store generated pages
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getWwwDir() 		{ return $this->getPublicDir() . DS . 'www'; }

	/**
	 * Returns the "oos" dir, where oospores adds data to the account
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getOosDir() 		{ return $this->getAccountDir() . DS . '_oos'; }
	
	/**
	 * Returns the "log" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getLogDir() 		{ return $this->getOosDir() . DS . 'log'; }
	
	/**
	 * Returns the "cache" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getCacheDir() 		{ return $this->getOosDir() . DS . 'cache'; }

	/**
	 * Returns the "generation" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getGenerationDir() 	{ return $this->getOosDir() . DS . 'generated'; }
	
	/**
	 * Returns the "public" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPublicDir() 		{ return $this->getAccountDir() . DS . '_public'; }
	
	
	/**
	 * Returns the "architecture" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getArchitectureDir(){ return $this->getAccountDir() . DS . 'architecture'; }
	
	/**
	 * Returns the "script" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getScriptDir() 		{ return $this->getAccountDir() . DS . 'js'; }
	
	/**
	 * Returns the "pages" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPagesDir() 		{ return $this->getViewDir() . DS . 'pages'; }
	
	/**
	 * Returns the "blocks" dir
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getBlocksDir() 		
	{ 
		$dir = $this->getViewDir() . DS . 'blocks'; 
		if(file_exists($dir)) { return $dir; }
		
		$dir = $this->getViewDir() . DS . 'paragraphes'; 
		if(file_exists($dir)) { return $dir; }
		
		return null;
	}
	
	/**
	 * Returns the "templates" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getTemplatesDir()	{ return $this->getViewDir() . DS . 'templates'; }
	
	/**
	 * Returns the "model" dir for blocks
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getBlockModelDir()	{ return $this->getModelDir() . DS . 'paragraphes'; }
	
	/**
	 * Returns the "model" dir for pages
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPageModelDir()	{ return $this->getModelDir() . DS . 'pages'; }
	
	/**
	 * Returns the "internationalization" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getI18nDir()		{ return $this->getModelDir() . DS . 'i18n'; }
	
	/**
	 * Returns the "style" dir of a given template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$tpl	The template's name
	 * @return	string
	 */
	public function getStyleDir($tpl)	{ return $this->getTemplatesDir() . DS . $tpl . DS . 'css'; }
	
	/**
	 * Returns the "account's activity" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getActivityAccountDir() { return $this->getLogDir() . DS . 'activity'; }
	
	/**
	 * Returns the "oos backoffice uploads" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getUploadAccountDir() 	{ return $this->getPublicDir() . DS . 'upload' . DS . 'account'; }
	
	/**
	 * Returns the "web uploads" dir
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getUploadWebDir() 		{ return $this->getPublicDir() . DS . 'upload' . DS . 'web'; }	

	/**
	 * Returns the "script" url
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getScriptUrl() 		{ return './view/js'; }
	
	/**
	 * Returns the "style" url of a given template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$tpl	The template's name
	 * @return	string
	 */
	public function getStyleUrl($tpl)	{ return './view/templates/'.$tpl.'/css'; }
	
	/**
	 * Looking if current user is an admin
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	boolean
	 * 
	 * @todo	We gotta move this function elsewhere
	 */
	public function isAdmin() 
	{
		if($_SERVER['SERVER_NAME'] == "localhost")
		{
			return true;
		}
		
		$ips = $this->getParam("TEAM", "ALLOWED_IPS");
		
		if(!is_array($ips) || count($ips) == 0) { return false; }
		return (in_array($_SERVER['REMOTE_ADDR'], $ips));
	}
}