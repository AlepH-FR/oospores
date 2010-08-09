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
 * Launches an account generation
 * 
 * @package	Oos_Generation
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Generation extends Oos_BaseClass
{
	/**	string					The account we are currently generating */
	private $_account; 
	/** Oos_Generation_HTAccess	The htaccess handler */
	private $_htaccess;
	/** Oos_Config				The configuration handler */
	private $_config;
	
	/**
	 * Class constructor.
	 * Instanciate Oos_Config et Oos_Generation_HTAccess instances
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$account	(opt.) The account we want to generate
	 */
	public function __construct($account = null)
	{
		if(is_null($account))
		{
			$account = _OOS_ACCOUNT;
		}
		$this->_account 	= $account;
		$this->_htaccess 	= new Oos_Generation_HTAccess($this->_account);
		$this->_config 		= Oos_Config::getInstance($this->_account);
	}	
	
	/**
	 * Main method for account genration
	 * Will generate pages statically and rewriting rules via the htaccess handler
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function run()
	{
		ini_set('max_execution_time', 120);
		// preparing www directory
		$this->prepareWwwDir();
		
		// parsing pages
		$pages = Oos_XML_Collection_Sitemap::getInstance($this->account)->getPages();

		foreach($pages as $page)
		{
			$rewrite = $page['rewriting'];
			
			// getting needed xml informations
			$pageName 		= $page['name'];
			$templateName	= $page['template'];
			
			// generation page
			$this->generatePage($pageName);	
			if(!$rewrite)
			{
				continue;
			}
			
			// rewriting rules
			$from 	= $pageName.".php?isrew=1";
			$to		= $rewrite;
			
			preg_match_all('/' . $this->_config->getMarkup('([^#]*)') . '/', $to, $matches);
			$cpt = 0;
			foreach($matches[1] as $match)
			{
				$cpt++;
				
				list($table, $field) = explode(".", $match);
				$to 	= preg_replace('/' . $this->_config->getMarkup($match) . '/', '([^-]*)', $to);
				$from  .= '&'.strtolower($table).'_'.strtolower($field).'=$'.$cpt;	
			}
			
			$to .= "\.htm";
			$flags	= array("QSA", "L");
			$this->_htaccess->addRewriteRule($from, $to, $flags);
		}
		
		// special pages
		$indexName 	= Oos_XML_Collection_Sitemap::getInstance($this->_account)->getPageRoot();
		$this->generatePage($indexName, 'index');
		
		// generation htaccess
		$this->_htaccess->populate();	
	}
	
	/**
	 * Generates a page statically
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$page_name			Name of the page we wanna generate
 	 * @param	string	$template_name		Name of the template of that page
 	 * @param	string	$forced_page_name	(opt.) If set, the page will be generated if a file named this way 
	 */
	public function generatePage($page_name, $forced_page_name = null)
	{
		global $controller;
		
		// creation page object
		$page = $controller->getPage($page_name, $this->_account);
		$html = $page->doRender(true); 
		
		// creating file name
		$file_name = !is_null($forced_page_name) ? $forced_page_name : $page_name;
		
		//copying
		$file = $this->_config->getWwwDir() . DS . $file_name . '.php';
		file_put_contents($file, $html);
		
		// for security issues
		chmod($file, 0604);
	}
	
	/**
	 * Prepare the wwwroot directory
	 * . clean it
	 * . create it if needed and update chmod
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function prepareWwwDir()
	{
		$config = Oos_Config::getInstance();
		
		$www_dir = $this->_config->getWwwDir();
		
		// cleaning
		$system = Oos_System::factory();
		$system->rmdir($www_dir);
		
		// creating directory if needed
		if(!file_exists($www_dir))
		{
			mkdir($www_dir);
			chmod($www_dir, 0705);
		}
	}
}