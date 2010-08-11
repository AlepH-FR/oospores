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
 * Oos_Controller is the main class of OOSpores.
 * It initializes a context of execution (session, configuration, ...) and run an infinite loop to find the proper state that
 * he is asked for.
 * 
 * @package	Oos_Controller
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Controller extends Oos_BaseClass 
{
	/** Oos_Users_Session	An instance to manipulate session's date */
	public $session;
	/** string				Content of the page */
	public $page;
	/** string				Content of the backoffice */
	public $inner_bo;
	
	/** Oos_XML_Collection_Sitemap	Data about the website */
	private $_xml_data;
	/** Oos_XML_Collection_Sitemap	Data about the website's backoffice */
	private $_xml_data_bo;
	
	/** Oos_Config	Configuration of the website */
	public $_config_site;
	/** Oos_Config	Configuration of the website's backoffice */
	public $_config_bo;
	
	/**	boolean		Whether it is an admin page or not */
	private $_admin_page = false;
		
	/**
	 * Class constructor. Building context :
	 *  . configuration objectifs
	 *  . session
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @throws	Oos_Exception			_OOS_ACCOUNT was not defined
 	 * @throws	Oos_Session_Exception	Lifetime syntax is not properly formatted
	 */
	public function __construct() 
	{
		if(!defined('_OOS_ACCOUNT'))
		{
			throw new Oos_Exception('_OOS_ACCOUNT was not defined', OOS_E_FATAL);
		}
		
		// initialize configuration
		$this->_config_site = Oos_Config::getInstance(_OOS_ACCOUNT);
		$this->_config_bo	= Oos_Config::getInstance($this->_config_site->getParam('SITE', "BO_ACCOUNT"));

		// managing sessions
		$lifetime = $this->_config_site->getParam("SESSION", "LIFETIME");
		if(!$lifetime) { $lifetime = $this->_config_site->getParam("SESSION", "LIFETIME"); }
		
		$time = Oos_Utils_String::lifetime2seconds($lifetime);
		if(!$time)
		{
			throw new Oos_Session_Exception('Lifetime syntax is not properly formatted', OOS_E_FATAL);
		}
	
		$tracking 	= $this->_config_site->getParam("SESSION", "TRACKING");
		$method 	= $this->_config_site->getParam("SESSION", "METHOD");
		$server		= $this->_config_site->getParam("SESSION", "MEMCACHE_SERVER");
		$options = array(
			'method'	=> $method,
			'server'	=> $server,
		);
		$this->session = new Oos_Users_Session($time, $tracking, $options);
	}
		
	/**
	 * Is this really an admin page ?
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	boolean
	 */
	public function isAdminPage() 				{ return $this->_admin_page; }	
		
	/**
	 * Forcing admin display
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean	$bool	(opt.) Admin or not admin ? That is the question.
	 */
	public function setAdminPage($bool = false) { $this->_admin_page = ($bool); }
	
	/**
	 * Getting XML data information about an account
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$account	(opt.) Name of the account
 	 * @return	Oos_XML_Collection_Sitemap
	 */
	public function getXMLData($account = null)
	{ 
		if(is_null($account) || $account == _OOS_ACCOUNT)
		{
			return $this->_xml_data;
		}
		elseif($account == $this->_config_site->getParam('SITE', "BO_ACCOUNT"))
		{
			return $this->_xml_data_bo;
		} 
		
		return Oos_XML_Collection_Sitemap::getInstance($account);
	}
	
	/**
	 * This method is the main loop of the controller. It parses XML Data and then, tries to find the proper page to display
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$page_name 	(opt.) The name of the initial page requested
	 * @param 	boolean	$is_dynamic (opt.) If false, this prints the rendering of the page found, else it returns the page object
	 * @return 	mixed
	 * 
	 * @throws	Oos_XML_Exception	Impossible to find a proper page
	 */
	public function init($page_name = null, $is_dynamic = false)
	{
		if($this->session->getAccessRole() == "admin") 
		{
			$this->setAdminPage(true);
		}
		
		$this->session->setValidSession();
		
		// parsing data
		$this->_xml_data 	= new Oos_XML_Collection_Sitemap(_OOS_ACCOUNT);	
		
		// initial user demand
		if(!$page_name)
		{
			$page_name = Oos_Input_Rest::getGetParam('page');
			$page = null;
		}
		
		$previous_page_asked = null;
		while(true) 
		{
			if(!is_null($previous_page_asked) && $previous_page_asked == $page_name)
			{
				throw new Oos_XML_Exception("Impossible to find a proper page. Latest : ".$page_name, OOS_E_FATAL);
			}
			$previous_page_asked = $page_name;
			
			if($this->_config_site->getParam('SITE', "MAINTENANCE") == true)
			{
				$page_name = $this->_xml_data->getPageMaintenance();
			}
			
			if(!$page_name) { $page_name = $this->_xml_data->getPageRoot(); }

			// getting page object
			$page = $this->getPage($page_name, _OOS_ACCOUNT);
			
			if(is_null($page)) 
			{
				$page_name = $this->_xml_data->getPageNotFound();
				continue;
			}
			
			// if not found
			if(!$page->isPageFound() && $page_name != $this->_xml_data->getPageNotFound()) 
			{
				$page_name = $this->_xml_data->getPageNotFound();
				continue;
			}
		
			// dog watcher
			if(!$page->isPageFound() && $page_name == $this->_xml_data->getPageNotFound()) 
			{
				print '404 not found';
				exit();
			}
			
			// if access is restricted
			$access_controller = new Oos_Access_Control($page->getRole(), $this->session->getAccessRole());
			if(!$access_controller->hasPermission()) 
			{
				$page_name = $this->_xml_data->getPageAccessDenied();
				continue;
			}
			
			$this->page = $page;
			break;
		}
		
		// affichage du CMS
		if($this->isAdminPage())
		{
			$this->_xml_data_bo = new Oos_XML_Collection_Sitemap($this->_config_site->getParam('SITE', "BO_ACCOUNT"));
			$ac = new Oos_Access_Control("admin", $this->session->getAccessRole());
		
			if(!$ac->hasPermission()) 
			{
				$page_name = $this->_xml_data_bo->getPageAccessDenied();
			} 
			else 
			{
				$page_name = $this->_xml_data_bo->getPageRoot();		
			} 
			
			$mainmenu = $this->getPage($page_name, $this->_config_site->getParam('SITE', "BO_ACCOUNT"));
			$mainmenu->render();
			
			$this->inner_bo = $mainmenu->getInnerBody();
			
			foreach($mainmenu->scripts as $script) 
			{
				$page->addScript($script);
			}
		}
		
		// tracking
		$this->session->track($page->name);
		
		// process output
		if(!$is_dynamic)
		{
			header('Content-type: text/html; charset='.$this->_xml_data->getCharset());
			print $page->doRender();
		}
		else
		{
			return $this->page;
		}
	}
	
	/**
	 * Prints a block directly as it was a popover
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$template_name 	Template name for this block
	 */
	public function getPopover($template_name)
	{
		$para = $this->getBlock($template_name);
		print $para->doRender();
	}
	
	/**
	 * This function tells if a block is whether static or dynamic
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string 	$model 	Model of the block
	 * @param 	string 	$account	(opt.) The account in use
	 * @return 	boolean 	
	 */
	public function isBlockImplemented($model, $account = null) 
	{
		$config = Oos_Config::getInstance($account);
		$path = $config->getBlockModelDir() . DS;
		
		$system = Oos_System::factory();
		$file = $system->findFileInDirectory($model, $path, array("php", "inc"));
		return $file;
	}
	
	/**
	 * Returns the proper "block" object depending on its implemtentation
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string			$template_name 	Template name for this block
	 * @param 	string 			$account		(opt.) The account in use
	 * @param 	string 			$page_name		(opt.) The page's name
	 * @param 	Oos_HTML_Page 	$page			(opt.) The page object
	 * 
	 * @return 	Oos_HTML_Block 	
	 */
	public function getBlock($template_name, $account = null, $page_name = null, $page = null) 
	{
		if(!$this->isBlockImplemented($template_name, $account)) 
		{
			$templateClass = "Oos_HTML_Block_Default";
		} 
		else 
		{
			$templateClass = $template_name;
		}
		
		$para = new $templateClass($template_name, $account, $page_name, $page);
		return $para;
	}
	
	/**
	 * Returns the block's template
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string			$template_name 	Template name for this block
	 * @param 	string 			$account		The account in use
	 * 
	 * @return 	string|false 	
	 * 
	 * @todo 	organizing blocks in directories !
	 */
	public function getTemplateBlock($template_name, $account) 
	{
		$system = Oos_System::factory();
		$config = Oos_Config::getInstance($account);
		
		$file = $system->findFileInDirectory($template_name, $config->getBlocksDir(), array("htm", "html", "tpl"));
		if($file && file_exists($file))
		{
			return file_get_contents($file);
		}
		return $file;
	}
	
	/**
	 * This function tells if a page is whether static or dynamic
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string 	$model 		Model of the page
	 * @param 	string 	$account	(opt.) The account in use
	 * @return 	boolean 	
	 */
	public function isPageImpleted($model, $account = null) 
	{
		$config = Oos_Config::getInstance($account);
		$path = $config->getPageModelDir(). DS .$model . ".php";
		return file_exists($path);
	}
	
	/**
	 * Returns the proper "page" object depending on its implemtentation
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string 			$page_name		(opt.) The page's name
	 * @param 	string 			$account		(opt.) The account in use
	 * 
	 * @return 	Oos_HTML_Page
	 */	
	public function getPage($page_name, $account = null) 
	{
		// looking for page data in sitemap
		$page = $this->getXMLData($account)->getPage($page_name);
		$templateClass = $page['template'];
		
		if(!$templateClass)
		{
			return null;
		}
		
		if(!$this->isPageImpleted($templateClass, $account)) 
		{
			$templateClass = "Oos_HTML_Page_Default";
		}
		
		// looking for page to process
		$page = new $templateClass($page_name, $account);
		return $page;
	}
	
	/**
	 * Returns the page's template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string			$template_name 	Template name for this block
	 * @param 	string 			$account		The account in use
	 * 
	 * @return 	string|false 	
	 */
	public function getTemplatePage($template_name, $account) 
	{
		$config = Oos_Config::getInstance($account);
		$file_prefix 	= $config->getPagesDir() . DS . strtolower($template_name);
		$extensions 	= array("htm", "html", "tpl");
		
		foreach($extensions as $ext) 
		{
			$file = $file_prefix.".".$ext;
			if(file_exists($file)) 
			{
				return file_get_contents($file);
			}
		}
		
		return false;
	}
}