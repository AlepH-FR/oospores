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
 * Strings manipulation
 * 
 * @package	Oos_Utils
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Utils_String extends Oos_BaseClass 
{
	/**	array	list of html entites */
	static private $_html_entities = null;
	/** array 	list of text entites */
	static private $_text_entities = null;

	/**
	 * Veryfing if the value is a valid email address
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$address	The address to test
	 * @return	boolean
	 */
	static public function isValidEmail($address) 
	{
		if(eregi("\r",$address) || eregi("\n",$address)) { return false; }
		$bool = ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9\-]+\$", $address);
		return $bool;
	}

	/**
	 * Transform a string from text to ascii
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$string		The string to process
	 * @return	string
	 */
   	static public function text2ascii($string) 
	{
        $string = strtr($string,
            "ŠŒšœŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖØÙÚÛÜİßàáâãäåæçèéêëìíîïğñòóôõöøùúûüıÿ,;.:/",
            "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy     ");
        $string = strtolower($string);

        $string = preg_replace("/[^(a-z)(0-9)_-\s\@]/", "", $string);
        $string = preg_replace("/\s+/", " ", $string);
        return $string;
    } 

	/**
	 * Transform a string from html to ascii
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$string		The string to process
	 * @return	string
	 */
    static public function html2ascii($string) 
    {
        $string = self::html2text($string);
        $string = self::text2ascii($string);
        return $string;
    } 

	/**
	 * Initialize static attributes _html_entites and _text_entites
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	static public function init_entities()
	{
		if(!is_null($_html_entities)) { return ; }
		
   		$codes = array_merge(array(34,38,60,62), range(160,255)); 
    	$chars = array_map("chr", $codes); 
    	$html  = array_map("htmlentities", $chars);
    	
    	$chars[] = "\n";
    	$html[]  = "<br />";
    	$chars[] = "\n";
    	$html[]  = "<br/>";
    	$chars[] = "\n";
    	$html[]  = "<br>";
    	
    	self::$_text_entities = $chars;
    	self::$_html_entities = $html;
	}

	/**
	 * Transform a string from html to text
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$string		The string to process
	 * @return	string
	 */
    static public function html2text($string) 
    {
    	self::init_entities();
        $string = str_replace(self::$_html_entities, self::$_text_entities, $string);
        return $string;
    } 

	/**
	 * Transform a string from text to html
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$string		The string to process
	 * @return	string
	 */
    static public function text2html($string) 
    {
    	self::init_entities();
        $string = str_replace(self::$_text_entities, self::$_html_entities, $string);
        return $string;
    } 
	
	/**
	 * Transform a string to be uri compliant
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$string		The string to process
	 * @return	string
	 */
	static public function toUri($string)
	{
		$string = self::html2ascii($string);
		$string = preg_replace("/[^\w]/", "_", $string);
		$string = preg_replace("/_+/", "_", $string);
		
		if(substr($string, 0, 1) == "_")
		{
			$string = substr($string, 1);	
		}
		
		$string = strtr($string, "àäâêëéèöôïîìùüû", "aaaeeeeooiiiuuu");
		$string = strtolower($string);
		return $string;	
	}

	/**
	 * A small util function converting datetime parameters into seconds
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string	$lifetime	The time to convert
	 * @return	int|null
	 */
	static public function lifetime2seconds($lifetime)
	{
		if(preg_match("/([0-9]+)([\w]+)/", $lifetime, $match))
		{
			$int = intval($match[1]);
			
			switch($match[2])
			{
				case "M":
					$time = 31 * 24 * 60 * 60 * $int;
					break;
				case "j":
					$time = 24 * 60 * 60 * $int;
					break;
				case "h":
					$time = 60 * 60 * $int;
					break;
				case "m":
					$time = 60 * $int;
					break;
				case "s":
					$time = $int;
					break;
				default:
			}
		}
		
		return $time;
	}
}