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
 * Getting informations about the browser and its version
 * 
 * @package	Oos_Utils
 * @subpackage	Browser
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Utils_Browser_UA 
{
	public $ff  = false;		/**< boolean	True if browser is Firefox */
	public $ff1 = false;		/**< boolean	True if browser is Firefox v.1 */
	public $ff2 = false;		/**< boolean	True if browser is Firefox v.2 */
	public $ff3 = false;		/**< boolean	True if browser is Firefox v.3 */
	public $ff4 = false;		/**< boolean	True if browser is Firefox v.4 */

	public $ie 		= false;	/**< boolean	True if browser is Internet Explorer */
	public $ie4 	= false;	/**< boolean	True if browser is Internet Explorer v.4 */
	public $ie5 	= false;	/**< boolean	True if browser is Internet Explorer v.5 */
	public $ie55 	= false;	/**< boolean	True if browser is Internet Explorer v.5.5 */
	public $ie6 	= false;	/**< boolean	True if browser is Internet Explorer v.6 */
	public $ie7 	= false;	/**< boolean	True if browser is Internet Explorer v.7 */
	public $ie8 	= false;	/**< boolean	True if browser is Internet Explorer v.8 */
	public $ie9 	= false;	/**< boolean	True if browser is Internet Explorer v.9 */

	// Opera
	public $opera 	= false;	/**< boolean	True if browser is Opera */
	public $opera5 	= false;	/**< boolean	True if browser is Opera v.5 */
	public $opera6 	= false;	/**< boolean	True if browser is Opera v.6 */
	public $opera7 	= false;	/**< boolean	True if browser is Opera v.7 */
	public $opera8 	= false;	/**< boolean	True if browser is Opera v.8 */
	public $opera9	= false;	/**< boolean	True if browser is Opera v.9 */
	public $opera10	= false;	/**< boolean	True if browser is Opera v.10 */

	// Netscape
	public $nn 	= false;		/**< boolean	True if browser is Netscape */
	public $nn2 = false;		/**< boolean	True if browser is Netscape v.2 */
	public $nn3 = false;		/**< boolean	True if browser is Netscape v.3 */
	public $nn4 = false;		/**< boolean	True if browser is Netscape v.4 */
	public $nn6 = false;		/**< boolean	True if browser is Netscape v.6 */
	public $nn8 = false;		/**< boolean	True if browser is Netscape v.8 */

	// Chrome
	public $chrome = false;		/**< boolean	True if browser is Chrome */
	
	// Konqueror
	public $konq = false;		/**< boolean	True if browser is Konqueror */

	// robots
	public $botgoogle    = false;	/**< boolean	Is that specified bot */
	public $botgoogletb  = false;	/**< boolean	Is that specified bot */
	public $botffg       = false;	/**< boolean	Is that specified bot */
	public $botgooglebot = false;	/**< boolean	Is that specified bot */
	public $botnewsgator = false;	/**< boolean	Is that specified bot */
	public $botmagpierss = false;	/**< boolean	Is that specified bot */
	public $botplanetphp = false;	/**< boolean	Is that specified bot */
	public $botbloglines = false;	/**< boolean	Is that specified bot */
	public $botdoubanbot = false;	/**< boolean	Is that specified bot */
	public $bottopix     = false;	/**< boolean	Is that specified bot */
	public $botrssreader = false;	/**< boolean	Is that specified bot */
	public $botnnw       = false;	/**< boolean	Is that specified bot */
	public $botngo       = false;	/**< boolean	Is that specified bot */
	public $botgn        = false;	/**< boolean	Is that specified bot */
	public $botrb        = false;	/**< boolean	Is that specified bot */

	/**
	 * Class constructor.
	 * Calculates the browser type and version depending on the user agent string
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$uas	The user agent string
	 */
	public function __construct($uas, $version) 
	{
		$this->ff = strpos($uas, 'Firefox') !== false;
		$this->ie = (
			strpos($uas, 'MSIE') !== false && 
			strpos($uas, 'Opera') === false
		);
		$this->opera = strpos($uas, 'Opera') !== false;
		$this->nn = (
			strpos($uas, 'Mozilla') !== false &&
			strpos(strtolower($uas), 'spoofer') === false &&
			strpos(strtolower($uas), 'webtv')   === false &&
			strpos(strtolower($uas), 'hotjava') === false &&
			$this->opera === false &&
			$this->ie === false &&
			$this->ff === false
		);
		$this->konq 	= strpos($uas, 'Konqueror') !== false;
		$this->chrome 	= strpos($uas, 'Chrome') !== false;

		// Internet explorer
		if ($this->ie) 
		{
			$this->ie4  = strpos($uas, 'MSIE 4.') !== false;
			$this->ie5  = strpos($uas, 'MSIE 5.') !== false;
			$this->ie55 = strpos($uas, 'MSIE 5.5') !== false;
			$this->ie6  = strpos($uas, 'MSIE 6.') !== false;
			$this->ie7  = strpos($uas, 'MSIE 7.') !== false;
			$this->ie8  = strpos($uas, 'MSIE 8.') !== false;
			$this->ie9  = strpos($uas, 'MSIE 9.') !== false;
		}
		
		// Firefox
		elseif ($this->ff) 
		{
			$this->ff1 = strpos($uas, 'Firefox/1') !== false;
			$this->ff2 = strpos($uas, 'Firefox/2') !== false;
			$this->ff3 = strpos($uas, 'Firefox/3') !== false;
			$this->ff4 = strpos($uas, 'Firefox/4') !== false;
		}

		// Netscape Navigator
		elseif ($this->nn) 
		{
			$this->nn2   = $version->major === 2;
			$this->nn3   = $version->major === 3;
			$this->nn4   = $version->major === 4;
			$this->nn6   = $version->major === 5;
			$this->nn8   = (bool) strpos($uas, 'Netscape/8');
		}
		
		// Opera
		elseif ($this->opera) 
		{
			$this->opera5 = strpos($uas, 'Opera/5') !== false;
			$this->opera6 = strpos($uas, 'Opera/6') !== false;
			$this->opera7 = strpos($uas, 'Opera/7') !== false;
			$this->opera8 = strpos($uas, 'Opera/8') !== false;
			$this->opera9 = strpos($uas, 'Opera/9') !== false;
			$this->opera10 = strpos($uas, 'Opera/10') !== false;
		}
		
		// robots
		else 
		{
			$this->botgoogle    = strpos($uas, 'Mediapartners-Google') !== false;
			$this->botgoogletb  = strpos($uas, 'GoogleToolbar') !== false;
			$this->botffg       = strpos($uas, 'Feedfetcher-Google') !== false;
			$this->botgooglebot = strpos($uas, 'Googlebot') !== false;
			$this->botnewsgator = strpos($uas, 'NewsGatorOnline/') !== false;
			$this->botmagpierss = strpos($uas, 'MagpieRSS') !== false;
			$this->botplanetphp = strpos($uas, 'PlanetPHPAggregator') !== false;
			$this->botbloglines = strpos($uas, 'Bloglines') !== false;
			$this->botdoubanbot = strpos($uas, 'Doubanbot') !== false;
			$this->bottopix     = strpos($uas, 'Topix.net') !== false;
			$this->botrssreader = strpos($uas, 'RssReader') !== false;
			$this->botnnw       = strpos($uas, 'NetNewsWire') !== false;
			$this->botngo       = strpos($uas, 'NewsGatorOnline') !== false;
			$this->botgn        = strpos($uas, 'GreatNews') !== false;
			$this->botrb        = strpos($uas, 'RssBar') !== false;
		}

	}

	/**
	 * Is our visitor a robot ?
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	boolean
	 */	
	public function isRobot() 
	{
		if ($this->botgoogle) 		{ return true; }
		if ($this->botgoogletb) 	{ return true; }
		if ($this->botffg) 			{ return true; }
		if ($this->botgooglebot) 	{ return true; }
		if ($this->botnewsgator) 	{ return true; }
		if ($this->botmagpierss) 	{ return true; }
		if ($this->botplanetphp) 	{ return true; }
		if ($this->botbloglines) 	{ return true; }
		if ($this->botdoubanbot) 	{ return true; }
		if ($this->bottopix) 		{ return true; }
		if ($this->botrssreader) 	{ return true; }
		if ($this->botnnw) 			{ return true; }
		if ($this->botngo) 			{ return true; }
		if ($this->botgn) 			{ return true; }
		if ($this->botrb) 			{ return true; }
		
		return false;
	}
}