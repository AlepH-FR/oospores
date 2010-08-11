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
 * Managing CSS templates of an account
 * 
 * @package	Oos_CSS
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_CSS_Templates extends Oos_BaseClass
{
	/**	string	The account of the template */
	protected $_account; 
	/**	string	Name of the template */
	private $_name;
	/** string	Source (path) of that template */
	private $_source;
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$name		Name of our template
	 * @param	string	$name		Path to our template
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 */
	public function __construct($name, $source, $account = null)
	{
		$this->_account = $account;
		$this->_name 	= $name;
		$this->_source 	= $source;
	}
	
	/**
	 * Get template's name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getName() 	{ return $this->_name; }
	
	/**
	 * Get template's source
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getSource() { return $this->_source; }
	
	/**
	 * Check if that template is currently in used
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean
	 */
	public function isCurrentTemplate()
	{
		global $controller;
		return ($controller->session->getCurrentTemplate($this->_account) == $this->_source);  
	}
	
	/**
	 * Returns all templates available for this account
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	array
	 */
	static public function getAvailableTemplates($account)
	{
		$config = Oos_Config::getInstance($account);
		
		$rep = $config->getTemplatesDir();
		if(!$dir = @opendir($rep)) { return false; }
		
		$templates = array();
		while(false !== ($file = readdir($dir)))
		{
			if ($file == "." || $file == "..") 
			{
				continue;
			}
			
			$source = $rep . DS . $file . DS . 'css'  .  DS  .  'index.css';
			if(!file_exists($source))
			{	
				continue;
			}
			
			$src 		= $config->getTemplatesDir() . "/" . $file . "/css/index.css";
			$templates[] = new Oos_CSS_Templates($file, $src, $account);
		}
		
		return $templates;
	}
}