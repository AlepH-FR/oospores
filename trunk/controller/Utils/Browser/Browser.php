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
 * Getting informations about the browser
 * 
 * @package	Oos_Utils
 * @subpackage	Browser
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Utils_Browser 
{
	/** string						The user agent string of the client */
	private $_user_agent_string;
	/** Oos_Utils_Browser_Version	Version of the browser */
	public $version;
	/** Oos_Utils_Browser_OS		Version of the client's os */
	public $os;
	/** Oos_Utils_Browser_UA		Version of user agent */
	public $ua;
	
	/**
	 * Class constructor.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param	string	$uas	(opt.) If specified, it will replace the php default user agent string
	 */
	public function __construct($uas = '') 
	{
		if (!$uas) { $uas = $_SERVER['HTTP_USER_AGENT']; }
		$this->userAgentString = $uas;
		
		$this->version = new Oos_Utils_Browser_Version($this->_user_agent_string);
		
		$this->os = new Oos_Utils_Browser_OS($this->_user_agent_string);
		$this->ua = new Oos_Utils_Browser_UA($this->_user_agent_string, $this->version);
	}

	/**
	 * Returns the user agent string
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @return 	string
	 */
	public function getUserAgentString() 
	{
		return $this->_user_agent_string;
	}
}