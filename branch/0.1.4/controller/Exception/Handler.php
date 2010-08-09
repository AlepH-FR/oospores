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

define('OOS_E_ALL', 	100);
define('OOS_E_VERBOSE', 103);
define('OOS_E_NOTICE', 	104);
define('OOS_E_WARNING', 109);
define('OOS_E_FATAL', 	110);
define('OOS_E_NONE', 	111);

/**
 * Handling Exceptions, routing their stack trace to differents levels depending on the configuration parameters
 * . screen
 * . file
 * . firebug
 * . mail to maintenance team
 * 
 * @package	Oos_Exception
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo	firebug output
 * @todo	file output
 */
class Oos_Exception_Handler
{
	/** Exception	The exception we wanna process */
	private $_e;

	/**
	 * Class constructor.
	 * . prints exception if needed
	 * . sends exception by email if needed
	 * . sends exception to firebug if needed
	 * . write exception in log files
	 * 
	 * Exiting on fatal exceptions.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	Exception	$e	The exception we wanna process
	 */
	public function __construct($e)
	{
		global $controller;
		$config = Oos_Config::getInstance();
		
		$this->_e = $e;
		
		ob_start();
		$level = $this->_e->getCode();
			
		// screen log
		if($level >= $config->getParam('ERRORS', 'SCREEN_LEVEL')) 
		{
			print $this->_e;
		}
		
		// email
		if($level >= $config->getParam('ERRORS', 'MAIL_LEVEL')) 
		{
			$this->sendMailToMaintenance();
		}
		
		// firebug
		if($controller->isAdminPage() && $level >= $config->getParam('ERRORS', 'FB_LEVEL')) 
		{
			;
		}
		
		// file
		
		ob_end_flush();

		if($level == OOS_E_FATAL) 
		{
			exit();	
		}	
	}
	
	/**
	 * Sends email to the maintenance team of the project
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	protected function sendMailToMaintenance() 
	{
		$config = Oos_Config::getInstance();
		$title = '[FATAL] ['.$config->getParam('URL', 'ROOT').'] '.$this->_e->getMessage();
		
		$txt = '
An error as occured on website '.$config->getParam('URL', 'ROOT').'

Date : '.date('Y-m-d').'
Hour : '.date('H:i').'
Url : '.$_SERVER['REQUEST_URI'].'
User IP : '.$_SERVER['REMOTE_ADDR'].'

File : '.$this->_e->getFile().'
Line : '.$this->_e->getLine().'

Message : '.$this->_e->getMessage().'

Stack trace :
'.$this->_e->getTraceAsString().'
		';	
		
		$dest_arr = $config->getParam('TEAM', 'MAILS_MAINTENANCE');
		mail(implode(",", $dest_arr), $title, $txt);
	}
	
	/**
	 * Transform exception's code into a readable information
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$code	Code of the exception
 	 * @return	string
	 */
	public static function getExceptionName($code) 
	{
		switch($code) 
		{
			case OOS_E_NONE : 		return "NONE";
			case OOS_E_VERBOSE : 	return "VERBOSE";
			case OOS_E_WARNING : 	return "WARNING";  
			case OOS_E_FATAL : 		return "FATAL";
			case OOS_E_ALL : 		return "ALL";
		}
		
		return "";
	}
	
	/**
	 * Getting a formatted debug stack trace
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean		$asArray	(opt.) returning the back trace as an array or as a string (by default)
 	 * @return	string|array
	 */	
	protected static function getDebugTrace($asArray = false) 
	{
	  	// read stack trace
		$stackArray = debug_backtrace();
		$caller2 = "";
		
		if ($stackArray[0]['class'] == __CLASS__) 
		{
			$caller2= "[".$stackArray[0]['file'].":".$stackArray[0]['line']."]";
			array_shift($stackArray); // don't report this method
		}
		
		if ($stackArray[0]['function'] == "log") 
		{
			$caller2= "[".$stackArray[0]['file'].":".$stackArray[0]['line']."]";
			array_shift($stackArray); // don't report caller method log (supposed log helper function)
		}
		
		$caller = $stackArray[0]['class'] . $stackArray[0]['type'] . $stackArray[0]['function'];
		$stack = array();
		foreach ($stackArray as $elt) 
		{
			if ($elt['class']) 
			{
				$str = $elt['class'].$elt['type'].$elt['function'];
			}
			else 
			{
				$str = $elt['function'];
			}
			$str.= " [".$elt['file'].":".$elt['line']."]";
			$stack[] = $str;
		}
		
		if($asArray) 
		{
			return $stack;
		}
		return implode("\n",$stack);
	}
}