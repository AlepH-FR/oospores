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
 * Oos_Access_Control is the centerpiece of the rights manangement in OOSpores.
 * This class implements a RBAC(3) access control.
 * 
 * @package	Oos_Access
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @since	0.1.4
 */
class Oos_Access_Control extends Oos_BaseClass
{
	/**	string	Role of the subject on which we are asking for permissions */
	private $_subject_role;
	/** string	Role of the accessor trying to process the subject */
	private $_accessor_role;
	
	/** string	Name of the base role for all users */
	private $_role_all;
	/** string	Name of the base role for registered users */
	private $_role_registered;
	/** string	Name of the role for admins (mandatory in order to access backoffice) */
	private $_role_admin;
	
	/**	Oos_XML_Collection_RoleGraph	Object to access the roles tree */
	private $_xml;
	
	/**
	 * Class constructor.
	 * Instanciates Oos_XML_Collection_RoleGraph in order to build base roles.
	 * 
	 * @version	1.0
	 * @since	0.1.4
	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$subject_role	(opt.) Role of the subject on which we are asking for permissions.
	 * 									If this role is set to "all" or "*", then it'll match the "ALL" role
	 * 	
	 * @param 	string	$accessor_role	(opt.) Role of the accessor trying to process the subject
	 * 									If this role is set to "all" or "*", then it'll look for some session informations
	 * 
	 */
	public function __construct($subject_role = "all", $accessor_role = "all")
	{
		global $controller;
		$config = Oos_Config::getInstance();
		
		$this->_xml = new Oos_XML_Collection_RoleGraph(_OOS_ACCOUNT);
		
		$this->_role_all 		= $this->_xml->getRoleAll();
		$this->_role_registered = $this->_xml->getRoleRegistered();
		$this->_role_admin 		= $this->_xml->getRoleAdmin();
		
		if(!$subject_role || $subject_role == 'all' || $subject_role == '*')  
		{ 
			$subject_role = $this->_role_all; 
		}
		
		if(!$accessor_role || $accessor_role == 'all' || $accessor_role == '*') 
		{ 
			if($config->isAdmin())
			{
				$accessor_role = $this->_role_admin;
			}
			
			if($controller->session->isConnected())
			{
				$accessor_role = $this->_role_registered;
			} 
			
			else 
			{
				$accessor_role = $this->_role_all;
			}	
		}
		
		$this->_subject_role  = $subject_role;
		$this->_accessor_role = $accessor_role;	
	}
	
	/**
	 * Processing persmissions algorithm.
	 * Will return true if the accessor as the right to access the subject
	 * 
	 * @version	1.0
	 * @since	0.1.4
	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return	boolean
	 * 
	 * @throws	Oos_Access_Exception	Accessor's role is unknown
	 */
	public function hasPermission()
	{	
		$allowed_roles = $this->_xml->getAllowedRoles($this->_accessor_role);	
		if(!$allowed_roles)
		{
			throw new Oos_Access_Exception("Role not found '".$this->_accessor_role."'", OOS_E_FATAL);
		}	
		
		return in_array($this->_subject_role, $allowed_roles);
	}
}