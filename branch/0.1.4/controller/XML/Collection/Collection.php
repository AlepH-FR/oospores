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
 * 
 * 
 * @package	Oos_XML
 * @subpackage	Collection
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_XML_Collection extends Oos_BaseClass
{
	protected $_schema;
	protected $_data;
	protected $_files;
	
	private $_generated_file_name;
	private $_generated_function;
	
	public function __construct($account, $type, $schema = array())
	{
		$config = Oos_Config::getInstance($account);
		$directory = $config->getArchitectureDir();
		
		$this->_schema = $schema;
		$this->_generated_file_name = $directory . DS . '_generated' . DS . $type . '.inc';

		if(file_exists($this->_generated_file_name))
		{
			require_once($this->_generated_file_name);
		}
		$this->_generated_function 		= 'get_collection_'.$type;
	
		$system = Oos_System::factory();
		if(file_exists($directory . DS . $type . ".xml"))
		{
			$this->_files = array(
				$directory . DS . $type . ".xml"
			);
		}
		else
		{
			$this->_files = $system->findFilesInDirectory($directory, array("xml"));
		}
		
		$this->load();
	}
	
	public function load()
	{
		// files updated... loading from beginning
		if($this->filesUpdated())
		{
			foreach($this->_files as $file)
			{
				$this->parseFile($file);
			}
			$this->generateStaticFile();
			return;
		}
		
		// no update, required generated file
		require_once($this->_generated_file_name);
		$function = $this->_generated_function;
		$this->_data = $function();
	}
	
	public function parseFile($file)
	{
		$xml = new DOMDocument();
		$xml->load($file);	
		$this->_current_file = pathinfo($file);
			
		$xml_data = $this->parseXML($xml);
		
		if(!is_array($this->_data))
		{
			$this->_data = array();
		}
		$this->_data = array_merge_recursive($xml_data, $this->_data);
	}
	
	public function parseXML($xml, $schema = null)
	{
		if(is_null($schema))
		{
			$schema = $this->_schema;
		}
		
		$xml_data = array();
		
		foreach($schema as $key => $attr)
		{
			if(is_array($attr))
			{
				$tags = $xml->getElementsByTagName($key);
				
				foreach($tags as $tag)
				{
					$tag_id = $tag->getAttribute("id");
					if(!$tag_id) { $tag_id = $tag->getAttribute("xml:id"); }
					
					$data = $this->parseXML($tag, $attr);
					
					if($tag_id)
					{
						$xml_data[$tag->tagName][$tag_id] = $data;
					}
					else
					{
						$xml_data[$tag->tagName] = $data;
					}
				}
				
				continue; 
			}
			
			if($attr == "_value")
			{
				$xml_data[$attr] = $xml->nodeValue;
				continue;
			}
			
			if($attr == "_file")
			{
				$xml_data[$attr] = $this->_current_file['filename'];
				continue;
			}
			
			$xml_item = $xml->getElementsByTagName($attr);
			if($xml_item->length > 1)
			{
				$data = array();
				foreach($xml_item as $item) 
				{ 
					if($item->parentNode && !$item->parentNode->isSameNode($xml)) { continue; }
					$data[] = $item->nodeValue; 
				}
				
				if(count($data) == 1) { list($data) = $data; }
			}
			else
			{
				$data = $xml_item->item(0)->nodeValue;
				if($xml_item->parentNode && !$xml_item->parentNode->isSameNode($xml)) { continue; }
			}
			
			if(!$data)
			{
				$data = $xml->getAttribute($attr);	
			}
			
			$xml_data[$attr] = $data;
		}
		
		return $xml_data;
	}
	
	public function filesUpdated()
	{
		// deleting stat cache to update mtime data
		clearstatcache();
		$generation_date = filemtime($this->_generated_file_name);
		
		foreach($this->_files as $file)
		{
			if(filemtime($file) > $generation_date)
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function generateStaticFile()
	{
		$php = '
/**
 * This file was automatically generated by OOSpores.
 * Last update '.date('Y-m-d h:i:s').'
 *
 * Please do not modify !
 */
 
function '.$this->_generated_function.'()
{
	return '.var_export($this->_data, true).';
}
		';
		file_put_contents($this->_generated_file_name,'<?php'.$php);
	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function refineData($data, $raw = false)
	{
		$config = Oos_Config::getInstance();
		
		if(!preg_match('/'.$config->getMarkup(".*").'/e', $data))
		{
			return $data;	
		}	
		
		// refine it
		preg_match_all('/'.$config->getMarkup("(.*)").'/e', $data, $matches, PREG_PATTERN_ORDER);
		
		foreach($matches[1] as $match)
		{
			list($table, $field) = explode(".", $match);
			
			$field_key = strtolower($table).'_'.strtolower($field);
			$value = Oos_Input_Rest::getParam($field_key);
			
			if($value)
			{
				$value = str_replace("_", '__SPACE__', ucfirst($value));
			}
			else
			{
				$id_key = strtolower($table).'_id';
				$id = Oos_Input_Rest::getParam($id_key);
				if(!$id) 
				{ 
					$data = preg_replace('/'.$config->getMarkup($match).'/e', '', $data);
					continue; 
				}
				
				$qh = Oos_DB::factory("SQL", strtolower($table));
				$object = $qh->findById($id);
				if(!$object) 
				{ 
					$data = preg_replace('/'.$config->getMarkup($match).'/e', '', $data);
					continue; 
				}
				
				if($raw)
				{
					$value = $object->getField(strtolower($field));
					$value = Oos_Utils_String::text2ascii($value);
				}
				else
				{
					$value = $object->getField(strtolower($field));
					$value = Oos_Utils_String::html2text($value);
				}
				$value = str_replace(" ", '__SPACE__', $value); 
			}
			
			$data = preg_replace('/'.$config->getMarkup($match).'/e', '__SPACE__'.$value, $data);
		}
		
		$data = str_replace('__SPACE__', " ", $data);
		
		return $data;
	}
}