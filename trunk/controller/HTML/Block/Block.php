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
 * Manipulate and render HTML blocks
 * 
 * @package	Oos_HTML
 * @subpackage	Block
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_HTML_Block extends Oos_BaseClass 
{
	/**	string			The account we are currently generating */
	protected $_account; 
	/** Oos_Config		The configuration handler */
	protected $_config;
	/** string			Template's name of this block */
	protected $_template;
	/** integer			On what zone we display that block */
	protected $_zone;
	/**	integer			Position of that block in the zone */
	protected $_position;
	/** Oos_HTML_Page	On what page we are */
	protected $_page;
	
	/**
	 * Get block's template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */
	public function getTemplate() 	{ return $this->_template; }
	
	/**
	 * Get block's zone
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	integer
	 */
	public function getZone() 		{ return $this->_zone; }
	
	/**
	 * Get block's position in our zone
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	integer
	 */
	public function getPosition() 	{ return $this->_position; }
		
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string			$template_name
	 * @param	string			$account
	 * @param	string			$page_name
	 * @param	Oos_HTML_Page	$page
	 * @param	integer			$zone
	 * @param	integer			$position
	 */
	public function __construct($template_name, $account = null, $page_name = null, $page = null, $zone = null, $position = null) 
	{
		if(!$account)
		{
			$account = _OOS_ACCOUNT;
		}

		$this->_account 	= $account;
		$this->_config	 	= Oos_Config::getInstance($this->_account);
		$this->_template  	= $template_name;
		
		$xmlData = Oos_XML_Collection_Sitemap::getInstance($this->_account);
		
		if($page) 
		{
			$this->_page	  = $page;	
		}	
		
		if($page_name)
		{
			$block = $xmlData->getBlock($template_name, $page_name);
			$this->_zone 		= $block['zone'];
			$this->_position	= $block['position'];
		}
	}
		
	/**
	 * This method must render an associative array, associating a value to each variables present on its template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract public function renderVariables();
	
	/**
	 * This method can return custom html to add to the template or make some calculation before the doRender method is called 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract public function render();
	
	/**
	 * Caching block's result
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return 	string
	 */	
	public function cache()
	{
		$config = Oos_Config::getInstance();
		
		$type 		= $config->getParam("CACHE", 'OUT');
		$lifetime 	= $config->getParam("CACHE", 'LIFETIME');
		
		$options = array(
			'lifetime'	=> Oos_Utils_String::lifetime2seconds($lifetime),
		);
		
		$cache = new Oos_Cache_Cache($type, $options);
		return $cache->cacheFuncResult(array(this, "doRender"));
	} 
	
	/**
	 * Rendering block
	 * 
	 * @version	1.2
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$tpl		(opt.) Rendering class's template or a specified one ?
 	 * @param	array	$variables	(opt.) Variables to pass to the html template. If not set, we'll look for rendered variables via renderVariables method
 	 * 
 	 * @return 	string
 	 * 
 	 * @throws	Oos_HTML_Exception		Template of block not found
	 */		
	public function doRender($tpl = null, $variables = null) 
	{
		global $controller;
	
		// looking for a template by default
		if(!$tpl) 
		{ 
			$tpl = $controller->getTemplateBlock($this->_template, $this->_account); 
			$htm = $this->render();
		}
		
		if(!$tpl) 
		{
			throw new Oos_HTML_Exception("Template of block '".$this->_template."' not found", OOS_E_FATAL);
		}
		
		// looking for variables
		if(!$variables) { $variables = $this->renderVariables(); }
		
		// removing line breakers
		$tpl = preg_replace("[\r\n]", $this->_config->getMarkup('_LINE_BREAK_'), $tpl);
		$tpl = preg_replace('/\n/', $this->_config->getMarkup('_LINE_BREAK_'), $tpl);
		
		// processing html
		// ... sub-blocks
		$reg = '/' . $this->_config->getMarkup('block:([\w_]*)') . '/';
		preg_match_all($reg, $tpl, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) 
		{
			$match = strtolower($match[1]);
			
			$inner_para = $controller->getBlock($match, $this->_account);
			$inner_html = $inner_para->doRender();
			
			$reg = '/' . $this->_config->getMarkup('block:'.strtoupper($match)) . '/';
			$tpl = preg_replace($reg, $inner_html, $tpl);
			$tpl = preg_replace("[\r\n]", $this->_config->getMarkup('_LINE_BREAK_'), $tpl);
			$tpl = preg_replace('/\n/', $this->_config->getMarkup('_LINE_BREAK_'), $tpl);
		}
		
		// ... conditionnal structures
		$reg = '/' . $this->_config->getMarkup('if:([\w_]*)').  '/';
		preg_match_all($reg, $tpl, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) 
		{
			$match = $match[1];
			
			$reg = '/' . $this->_config->getMarkup('loop:([\w_]+)') . '(.*?)' . $this->_config->getMarkup('if:'.$match) . '(.*?)' . $this->_config->getMarkup('endloop:\1') . '/';
		
			if(preg_match($reg, $tpl, $matchesTmp) >= 1) {continue; }
			
			// removing "if" if the condition is ok
			if($variables[$match]) 
			{
				$tpl = str_replace($this->_config->getMarkup('if:'.$match), '', $tpl);
				$tpl = str_replace($this->_config->getMarkup('endif:'.$match), '', $tpl);
				continue;
			}
			
			// else removing everything wrapper by this markup
			$reg = '/' . $this->_config->getMarkup('if:'.$match) . '(.*?)' . $this->_config->getMarkup('endif:'.$match) . '/';
			$tpl = preg_replace($reg, '', $tpl);
		}

		// ... internationalization
		$reg = '/' . $this->_config->getMarkup('i18n:([\w\._]*)').  '/';
		preg_match_all($reg, $tpl, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) 
		{
			$match = $match[1];
			list($category, $key) = explode('.', $match);
			if(!$key)
			{
				$key 		= $category;
				$categorie	= 'static';
			}
			
			$wording = i18n($category, $key, $this->_account);
			$tpl = str_replace($this->_config->getMarkup('i18n:' . $match), $wording, $tpl);
		}
		
		// ... urls
		$reg = '/' . $this->_config->getMarkup('url:([\w\._]*)').  '/';
		preg_match_all($reg, $tpl, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) 
		{
			$match = $match[1];
			
			$url = url($match, null, null, $this->_account);
			$tpl = str_replace($this->_config->getMarkup('url:' . $match), $url, $tpl);
		}
		
		// processing variables
		// ... if none, we stop there
		if(!$variables || !is_array($variables)) 
		{ 
			// on restitue les sauts de ligne	
			$tpl = preg_replace('/' . $this->_config->getMarkup('_LINE_BREAK_') . '/', "\n", $tpl);
			return $tpl; 
		}
		
		// ... processing each value
		foreach($variables as $key => $value) 
		{
			// simple ones
			if(!is_array($value)) 
			{
				$tpl = str_replace($this->_config->getMarkup($key), $value, $tpl);
				continue;
			}
				
			// loops
			if(is_array($value)) 
			{	
				$reg = '/' . $this->_config->getMarkup('loop:'.$key) . '(.*?)' . $this->_config->getMarkup('endloop:'.$key) . '/';
				
				if(count($value) == 0)
				{
					$tpl = preg_replace($reg, '', $tpl);
					continue;
				}
				
				// looking for this loop
				preg_match($reg, $tpl, $match);

				if(!$match[1]) 
				{
					continue;
				}

				// iterative for each value on this loop
				$list = array();
				foreach($value as $data) 
				{	
					$elt = $match[1];
					$elt = $this->doRender($elt, $data);
					$list[] = $elt;
				}
				
				$tpl = preg_replace($reg, implode("\n", $list), $tpl);
			}
		}
		
		// putting line breakers back
		$tpl = preg_replace('/' . $this->_config->getMarkup('_LINE_BREAK_') . '/', "\n", $tpl);
		
		$block = $htm.$tpl;
		if($controller->isAdminPage())
		{
			$block = '
	<div id="block-' . $this->_template . '" class="oos_block">
' . $block . '
	</div>
			';
		}
		
		return $block;
	}
}