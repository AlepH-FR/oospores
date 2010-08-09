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
 * Oos_Url enables developpers to construct urls easily
 * 
 * @package	Oos_Url
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Url extends Oos_BaseClass
{
	/**
	 * Returns the url of a given page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$page 		Name of the page
	 * @param 	mixed 	$object		(opt.) If it's a rewrited page, we need an object to build the proper uri
	 * @param 	mixed 	$variables	(opt.) Variables to add in the query string
 	 * @param 	string	$account	(opt.) On what account are we looking for this url
	 * 
	 * @return 	string
	 */
	static public function getUrl($page = null, $object = null, $variables = null, $account = null) 
	{		
		$config 	= Oos_Config::getInstance($account);
		$xml_data 	= Oos_XML_Collection_Sitemap::getInstance($account);
			
		// asking for the current page
		if(is_null($page))
		{
			list($page, $ext) = explode(".", $_SERVER['REQUEST_URI']);
			if(substr($page, 0, 1) == "/")
			{
				$page = substr($page, 1);	
			}
				
			if(substr($page, 0, 1) == "?")
			{
				$page = false;	
			}
			
			if(strpos($ext, "?") !== false)
			{
				list($ext) = explode("?", $ext);
			}
		}
		
		if($page)
		{
			$page_info = $xml_data->getPage($page);
			$rw	= $page_info['rewriting'];
		} 
		
		// rewriting
		if($rw)
		{
			preg_match_all('/' . $config->getMarkup("([^#]*)") . '/', $rw, $matches);
			$cpt = 0;
			foreach($matches[1] as $match)
			{
				$cpt++;
				
				list($table, $field) = explode(".", $match);
				
				// traitement des données
				if($field != "ID") { $field = strtolower($field); }
				
				if(is_object($object))
				{
					$field_to_uri = Oos_Utils_String::toUri($object->getField($field));
				}
				elseif(is_array($object))
				{
					$key = strtolower($table . "_" . $field);
					$field_to_uri = Oos_Utils_String::toUri($object[$key]);
				}
				
				$rw = preg_replace('/' . $config->getMarkup($match) . '/', $field_to_uri, $rw);
			}
			
			$page = $rw;
		}
		
		// page's extension
		if($page)
		{
			if(!$ext) { $ext = "htm"; }
			$page .= "." . $ext;
		}
		
		// adding querystring
		if($variables) 
		{
			$first = true;
			foreach($variables as $key => $value) 
			{
				$sep = $first? '?' : '&' ;
				$page.= $sep . $key . '=' . $value;
				
				$first = false;
			}
		}
		
		return $page;
	}
}