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
 * Main class for crons.
 * Basically it'll look for all files in the "cron" directory and launch them depending on their time tags
 * 
 * @package	Oos_Cron
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Cron extends Oos_BaseClass
{
	/** array	Items to launch */
	private $_cron_items = array();
	
	/**	integer	Current hour */
	private $_hour;
	/**	integer	Current minute */
	private $_min;
	/**	integer	Current month */
	private $_month;
	/**	integer	Current day in week */
	private $_day_in_week;
	/**	integer	Current day in month */
	private $_day_in_month;
	
	/**
	 * Class constructor.
	 *  . getting local datetime
	 *  . parsing cron directory of each account looking for items
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __construct()
	{
		// getting local time informations
		$this->_hour 			= date("H");
		$this->_min  			= intval(date("i"));
		$this->_day_in_week 	= date("w");
		$this->_day_in_month	= date("j");
		$this->_month			= date("n");
		
		// catching all accounts
		$accounts = Oos_Accounts::getAllAccounts();
		
		// and parsing each...
		foreach($accounts as $account)
		{
			$config = Oos_Config::getInstance($account);
			$rep = $config->getCronDir();
			
			if(!file_exists($rep)) 		{ continue; }
			if(!$dir = @opendir($rep)) 	{ continue; }
		
			while(false !== ($file = readdir($dir))) 
			{
				if ($file == "." || $file == "..") { continue; }
			
				$ext = substr($file, -3);
				if($ext != 'php' && $ext != 'inc') { continue; }
				
				$file_name = substr($file, 0, strlen($file)-4);
				$path = $rep . DS . $file;
				
				$item = new CronItem($file_name, $path, $account);
				$this->_cron_items[] = $item;
			}	
		}
	}
	
	/**
	 * Execute cron items if we have to
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @todo	Add execution time control
	 */
	public function execute()
	{
		global $CRON_INFO;
		
		foreach($this->_cron_items as $item)
		{
			// making needed manipulations
			define('_OOS_ACCOUNT', $item->getAccount());
		
			// requiring
			require_once($item->getPath());
			
			// looking for script frequency
			$freq = $CRON_INFO[$item->getName()];
			if(!isset($freq["minute"])) 		{ $freq["minute"] 		= '*'; }
			if(!isset($freq["hour"])) 			{ $freq["hour"] 		= '*'; }
			if(!isset($freq["day_in_week"])) 	{ $freq["day_in_week"] 	= '*'; }
			if(!isset($freq["day_in_month"])) 	{ $freq["day_in_month"] = '*'; }
			if(!isset($freq["month"])) 			{ $freq["month"] 		= '*'; }
			
			$logger = new Oos_Cron_Log($item->getName(), _OOS_ACCOUNT, true);
			
			// continuing is not valid
			if(!$this->checkFrequency($freq))
			{
				continue;
			}
			
			// launching
			$function = $item->getName();
			
			try
			{
				if(function_exists($function))
				{
					$function();	
				}
				elseif(class_exists($function) && method_exists($function, 'main'))
				{
					call_user_func(array($function, 'main'));
				}
			}
			
			catch(Exception $e)
			{
				$msg = $e->toString();
				$logger->addEvent($msg);
				$logger->sendReport();
				
				continue;
			}
		}
	}	
	
	/**
	 * Checks is a cron's frequency is matching current datetime
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	array	$freq	Array of frequencies matching the usual cron informations
 	 * @return	boolean
	 */	
	public function checkFrequency($freq)
	{
		// creating valid moment with user data
		$valid_minutes 			= $this->processFrequency($freq["minute"], 60);
		$valid_hours 			= $this->processFrequency($freq["hour"], 23);
		$valid_days_in_weak 	= $this->processFrequency($freq["day_in_week"], 6);
		$valid_days_in_month 	= $this->processFrequency($freq["day_in_month"], 31, 1);
		$valid_monthes 			= $this->processFrequency($freq["month"], 12, 1);

		// calculating
		$ok = (
			$valid_minutes[$this->_min] &&
			$valid_hours[$this->_hour] &&	
			$valid_days_in_weak[$this->_day_in_week] &&
			$valid_days_in_month[$this->_day_in_month] &&
			$valid_monthes[$this->_month]
		);
		
		return $ok;
	}
	
	/**
	 * Interprets cron's vocabulary and returns an array of dates to execute this item
	 *  . "*" means execute it every time
	 *  . "a-b" means execute it from a to b
	 *  . "a/b" means execute it at a then every b
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string		$item	Some string written in the cron's vocabulary
 	 * @param	integer		$end	End point. 60 for minutes, 12 for monthes, etc.
 	 * @param	integer		$start	(opt.)	Starting point (0 or 1 ? there is no january, 0th ;)
 	 * @return	array
	 */	
	public function processFrequency($item, $end, $start = 0)
	{
		$result = array();
		
		// all
		if($item === '*')
		{
			foreach(range($start, $end) as $i) { $result[$i] = true; }
			return $result;
		}
		
		// from... to...
		if(strpos($item, "-") !== false)
		{
			list($begin, $stop) = explode("-", $item);
			foreach(range($begin, $stop) as $i)
			{
				if($i >= $end) { continue; }
				$result[$i] = true;
			}
			
			return $result;
		}
		
		// at.. then every...
		if(strpos($item, "/") !== false)
		{
			list($begin, $freq) = explode("/", $item);
			
			if($begin === '*') { $begin = $start; }
			
			$result[$begin] = true;
			
			$cpt = $begin + $freq;
			while($cpt < $end)
			{
				$result[$cpt] = true;
				$cpt += $freq;

				// watch dog
				if($cpt > 100000) { break; }
			}
			return $result;
		}
		
		// specific value
		$result[intval($item)] = true;
		return $result;
	}
}

// launching crons
define('DS', DIRECTORY_SEPARATOR);
require_once(dirname(__FILE__). DS . '..' . DS . '..' . DS . '_init.php');

$OOS_CRON_INFO = array();
$cron = new Oos_Cron();
$cron->execute();

?>