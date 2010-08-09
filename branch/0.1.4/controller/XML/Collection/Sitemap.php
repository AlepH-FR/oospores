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
 * Processing sitemap xml files and transforming them into an associative array
 * 
 * @package	Oos_XML
 * @subpackage	Collection
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_XML_Collection_Sitemap extends Oos_XML_Collection
{	
	/**
	 * Class constructor.
	 * Defines xml scheme for the sitemap files and launches parent constructor
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$account	Account concerned
	 */
	public function __construct($account)
	{
		$schema = array(
			"metadata" => array(
				"prefix_title",
				"base_keywords",
				"doctype",
				"encodage",
				"lang",
				"favicon" 	=> array("png", "ico"),
				"script",
				"style" 	=> array("src", "template", "media"),
			),
			
			"pageNotFound"		=> array("name"),
			"pageAccessDenied"	=> array("name"),
			"pageRoot"			=> array("name"),
			"pageMaintenance"	=> array("name"),
			
			"modele" => array(
				"name",
				"parent",
				"block" => array(
					"name",
					"template",
					"zone",
					"position",
				),	
			),
			
			"page" => array(
				"_file",
				"name",
				"modele",
				"template",
				"titre",
				"description",
				"keywords",
				"rewriting",
				"script",
				"style" => array(
					"src", 
					"template", 
					"media"
				),	
				"role",
				"backoffice",
				"block" => array(
					"name",
					"template",
					"zone",
					"position",
				),	
			),
		);
		parent::__construct($account, 'sitemap', $schema);
	}
	
	/**
	 * Returns the page associated with the Error 404
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPageNotFound() 		{ return $this->_data['pageNotFound']['name']; }
	
	/**
	 * Returns the page associated with the Error 403
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPageAccessDenied()	{ return $this->_data['pageAccessDenied']['name']; }
	
	/**
	 * Returns the page associated with the index page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPageRoot()			{ return $this->_data['pageRoot']['name']; }
	
	/**
	 * Returns the page that is the "maintenance" page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getPageMaintenance()	{ return $this->_data['pageMaintenance']['name']; }
	
	/**
	 * Returns the doctype of the website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getDocType() 			{ return $this->_data['metadata']['doctype']; }
	
	/**
	 * Returns the lang of the website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getLang() 				{ return $this->_data['metadata']['lang']; }
	
	/**
	 * Returns the common keywords of all pages for this website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getCommonKeyWords()	 	{ return $this->_data['metadata']['base_keywords']; }
	
	/**
	 * Returns the title prefix that appends to all pages titles for this website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getTitlePrefix() 		{ return $this->_data['metadata']['prefix_title']; }
	
	/**
	 * Returns the charset of the website
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getCharset()			{ return $this->_data['metadata']['charset']; }
	
	/**
	 * Returns the .ico favicon of the website 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getFavIconIco()	
	{
		$ico = $this->_data['metadata']['favicon']['ico'];

		return $ico;
	}
	
	/**
	 * Returns the .png favicon of the website 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getFavIconPng()	
	{
		$ico = $this->_data['metadata']['favicon']['png'];

		return $ico;
	}
	
	/**
	 * Get the data of a specified page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getPage($page_name)
	{
		return $this->_data['page'][$page_name];
	}
	
	/**
	 * Get scripts of a specified page and global website'scripts merged all together
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getScripts($page_name)
	{
		$scripts = $this->_data['page'][$page_name]['script'];
		if(!is_array($scripts) && !is_null($scripts)) { $scripts = array($scripts); }
		
		$common_scripts = $this->_data['metadata']['script'];
		if(!is_array($common_scripts) && !is_null($common_scripts)) { $common_scripts = array($common_scripts); }
		
		$scripts = array_merge_recursive($scripts, $common_scripts);
		return $scripts;			
	}
	
	/**
	 * Get styles of a specified page and global website'styles merged all together
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getStyles($page_name)
	{
		$styles = $this->_data['page'][$page_name]['style'];
		if(!is_array($styles) && !is_null($styles)) { $styles = array($styles); }
		if(!is_null($styles['src'])) { $styles = array($styles); }
		if(is_null($styles)) { $styles = array(); }
		
		$common_styles = $this->_data['metadata']['style'];
		if(!is_array($common_styles) && !is_null($common_styles)) { $common_styles = array($common_styles); }
		if(!is_null($common_styles['src'])) { $common_styles = array($common_styles); }
		if(is_null($common_styles)) { $common_styles = array(); }

		$styles = array_merge($styles, $common_styles);
			
		return $styles;		
	}
	
	/**
	 * Get the title of a specified page, prefixed with the global website prefix
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getTitle($page_name)
	{
		$title = $this->_data['page'][$page_name]['titre'];
		$prefix_title = $this->_data['metadata']['prefix_title'];
		
		return $this->refineData(utf8_decode($prefix_title.$title));
	}
	
	/**
	 * Get keywords of a specified page, prefixed with the global website keywords
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getKeywords($page_name)
	{
		$kw = $this->_data['page'][$page_name]['keywords'];
		$base_kw = $this->_data['metadata']['base_keywords'];

		return $base_kw.', '.$kw;
	}
	
	/**
	 * Get a pages blocks informations, merge with the blocks of its modele
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getBlocks($page_name)
	{
		$blocks = $this->_data['page'][$page_name]['block'];
		$modele = $this->_data['page'][$page_name]['modele'];
		
		if($modele)
		{
			$modele_blocks = $this->getModeleBlocks($modele);
			$blocks = array_merge_recursive($blocks, $modele_blocks);
		}
		
		return $blocks;
	}
	
	/**
	 * Get blocks of a modele
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$modele		Name of the modele identified in xml via the "xml:id" attribute
	 * @return	array
	 */
	public function getModeleBlocks($modele)
	{
		$modele = $this->_data['modele'][$modele];
		
		$blocks = $modele['block'];
		if($parent = $modele['parent'])
		{
			$blocks = array_merge($blocks, $this->getModeleBlocks($parent));
		}
		
		return $blocks;
	}
	
	/**
	 * Get a block informations
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$block_name	Name of the block identified in xml via the "xml:id" attribute
	 * @param	string	$page_name	Name of the page identified in xml via the "xml:id" attribute
	 * @return	array|null
	 */
	public function getBlock($block_name, $page_name)
	{
		$blocks = $this->getBlocks($page_name);
		
		foreach($blocks as $block)
		{
			if($block['template'] == $block_name)
			{
				return $block;
			}
		}
		
		return null;
	}
	
	/**
	 * Get the list of all categories of pages, those categories being the name of the XML files
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	array
	 */
	public function getCategories()
	{
		$categories = array();
		foreach($this->_files as $file)
		{
			$info = pathinfo($file);
			$categories[] = strtolower($info['filename']);
		}
		
		return $categories;
	}
	
	
	/**
	 * Get the list of all pages in a specified category
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$category_name	(opt.) Name of the category on which we are looking for pages	
	 * @return	array
	 */
	public function getPages($category_name = null)
	{
		if(is_null($category_name))
		{
			return $this->_data['page'];
		}
	
		$pages = $this->_data['page'];
		$result = array();
		foreach($pages as $page)
		{
			if(strtolower($page['_file']) == strtolower($category_name))
			{
				$result[] = $page;
			}
		}
		
		return $result;
	}
}