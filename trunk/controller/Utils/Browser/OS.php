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
 * Getting informations about the operating system
 * 
 * @package	Oos_Utils
 * @subpackage	Browser
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Utils_Browser_OS 
{
	public $win 		= false;	/**< boolean	True if OS is a Windows OS */
	public $win98 		= false;	/**< boolean	True if OS is Windows 98 */
	public $winnt 		= false;	/**< boolean	True if OS is Windows NT */
	public $win2000 	= false;	/**< boolean	True if OS is Windows 2000 */
	public $winxp 		= false;	/**< boolean	True if OS is Windows XP */
	public $win2003 	= false;	/**< boolean	True if OS is Windows 2003 */
	public $winvista 	= false;	/**< boolean	True if OS is Windows Vista */
	public $winseven 	= false;	/**< boolean	True if OS is Windows Seven */

	public $mac 		= false;	/**< boolean	True if OS is a Mac OS */
	public $macosx 		= false;	/**< boolean	True if OS is Mac OS X */

	public $unix 		= false;	/**< boolean	True if OS is Unix OS */
	public $unixfedora 	= false;	/**< boolean	True if OS is Fedora */
	public $unixubuntu 	= false;	/**< boolean	True if OS is Ubuntu */

	/**
	 * Class constructor.
	 * Calculates the operating systems depending on the user agent string
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$uas	The user agent string
	 */
	public function __construct($uas) 
	{
		$this->win  = strpos($uas, 'Win') !== false;
		$this->mac  = strpos($uas, 'Mac') !== false;
		$this->unix = strpos($uas, 'X11') !== false;

		// windows
		if ($this->win) 
		{
			$this->win98      = strpos($uas, 'Windows 98')     !== false;
			$this->winnt      = strpos($uas, 'Windows NT 4.0') !== false;
			$this->win2000    = strpos($uas, 'Windows NT 5.0') !== false;
			$this->winxp      = strpos($uas, 'Windows NT 5.1') !== false;
			$this->win2003    = strpos($uas, 'Windows NT 5.2') !== false;
			$this->winvista   = strpos($uas, 'Windows NT 6.0') !== false;
			$this->winseven   = strpos($uas, 'Windows NT 6.1') !== false;
		}

		// mac
		elseif ($this->mac) 
		{
			$this->macosx = strpos($uas, 'OS X') !== false;
		}

		// unix
		elseif ($this->unix) 
		{
			$this->unixfedora = strpos($uas, 'Fedora/') !== false;
			$this->unixubuntu = strpos($uas, 'Ubuntu')  !== false;
		}
	}
}