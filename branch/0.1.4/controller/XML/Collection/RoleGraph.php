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
 * Processing rolegraph xml files and transforming them into an associative array
 * 
 * @package	Oos_XML
 * @subpackage	Collection
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_XML_Collection_RoleGraph extends Oos_XML_Collection
{
	/**
	 * Class constructor.
	 * Defines xml scheme for the role files and launches parent constructor
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
			"roleAdmin" 		=> array("name"),
			"roleRegistered" 	=> array("name"),
			"roleAll" 			=> array("name"),
			"role" => array(
				"child",
			)
		);
		parent::__construct($account, 'role_graph', $schema);
	}
	
	/**
	 * Returns the role affected to be the base "admin" role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getRoleAdmin() 		{ return $this->_data['roleAdmin']['name']; }
	
	/**
	 * Returns the role affected to be the base "registered" role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getRoleRegistered() { return $this->_data['roleRegistered']['name']; }
	
	/**
	 * Returns the role affected to be the base "all" (not connected) role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	string
	 */
	public function getRoleAll() 		{ return $this->_data['roleAll']['name']; }
	
	/**
	 * Returns the child roles of a specified role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$role	The role on which we are looking for its children
	 * @return	array
	 */
	public function getAllowedRoles($role)
	{
		$role_info = $this->_data['role'][$role];
		if(!$role_info) { return false; }
		
		$allowed = $role_info['child'];
		$allowed[] = $role;
		
		return $allowed;
	}
}