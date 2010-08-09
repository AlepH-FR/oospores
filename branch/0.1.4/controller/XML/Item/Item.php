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
 * Managing XML Markups as Item objects
 * @warning	This class isn't used in OOSpores any more, but we choose to let it into the source code : it can be useful for some website developments
 * 
 * @package	Oos_XML
 * @subpackage	Item
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_XML_Item extends Oos_BaseClass
{
	/** DOMNode	A XML Node generated with DOMDoucment library */
	protected $_xml;
	/** array	Child nodes of our node that we will looking at */
	protected $_fields;
	/** boolean	Returning raw or formated values ? */
	protected $_raw;

	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	DOMNode	$xml_item	A XML Node generated with DOMDoucment library
	 * @param	boolean	$raw		Returning raw or formated values ?
	 * 
	 * @throws	Oos_XML_Exception	No xml item defined
	 */	
	public function __construct($xml_item, $raw = false)
	{
		if(!$xml_item) 
		{
			throw new Oos_XML_Exception("No xml item defined", OOS_E_FATAL);	
		}
		$this->_xml = $xml_item;
		$this->_raw	= $raw;
	}
	
	/**
	 * Genuine setter
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$field	The field we are looking for
	 * @return 	string
	 * 
	 * @throws	Oos_XML_Exception	No xml item defined
	 */
	public function __get($field) 
	{
		if(!$this->_xml) 
		{
			throw new Oos_XML_Exception("No xml item defined", OOS_E_FATAL);
		}		
		
		if(!$this->_fields[$field])
		{	
			$xml_data = $this->_xml->getElementsByTagName($field);
			if($xml_data->item(0)
			&& $xml_data->item(0)->parentNode
			&& !$xml_data->item(0)->parentNode->isSameNode($this->_xml)) { return; }
			
			$data = $xml_data->item(0)->nodeValue;
			
			if(!$data)
			{
				$data = $this->_xml->getAttribute($field);	
			}
			
			if(!$this->_raw)
			{
				$data = $this->refineData($data, $field);
			}
			$this->_fields[$field] = $data;
		}
		
		return $this->_fields[$field];
	}
	
	/**
	 * Getting raw value for a field
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$field	The field we are looking for
	 * @return 	string
	 * 
	 * @throws	Oos_XML_Exception	No xml item defined
	 */
	public function getRaw($field) 
	{
		if(!$this->_xml) 
		{
			throw new Oos_XML_Exception("No xml item defined", OOS_E_FATAL);	
		}		
		
		if(!$this->_fields[$field])
		{	
			$xml_data = $this->_xml->getElementsByTagName($field);
			if($xml_data->item(0)
			&& $xml_data->item(0)->parentNode
			&& !$xml_data->item(0)->parentNode->isSameNode($this->_xml)) { return; }
			
			$data = $xml_data->item(0)->nodeValue;
			
			if(!$data)
			{
				$data = $this->_xml->getAttribute($field);	
			}
			
			if(!$this->_raw)
			{
				$data = $this->refineData($data, $field, true);
			}
			$this->_fields[$field] = $data;
		}
		
		return $this->_fields[$field];
	}
	
	/**
	 * Refine and process some data
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string	$data	The data to refine
	 * @param 	string	$field	Name of the field concerned
	 * @return	string
	 */	
	abstract public function doRefineData($data, $field);
}