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
 * A small util to profile your PHP scripts.
 * It will write some information about your script's execution instead of rendering your usual page.
 * 
 * @package	Oos_System
 * @subpackage	Profiler
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @todo	Multiple occurences of a mark
 * @todo	Loops
 * @todo	Reporting
 * @todo	Start / stops > Cascade marks
 * @todo	Integrate to MySQL requests
 * @todo	oos_dump styles
 * @todo	i18n
 */
class Oos_System_Profiler extends Oos_BaseClass
{
	/**	array	Types of marks */
	private $_types		= array();
	/** array 	All the markers that have been declared */
	private $_markers	= array();
	/** array 	Childs markers of other markers */
	private $_childs 	= array();
	/** array 	Timers */
	private $_timers 	= array();
	/** array 	Time elapsed for each marker */
	private $_elapsed	= array();
	/** array 	Knowing if a marker has already been rendered or not */
	private $_rendered	= array();
	
	/**
	 * Class constructor.
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __construct()
	{
		ob_start();	
	}
	
	/**
	 * Starts a new timer for a given mark
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$mark	Name of that mark. 
 	 * @param	string	$type	(opt.) Type of this mark. If set, the global time elapsed for each mark of this type will be calculated and rendered 
	 */
	public function startMark($mark, $type = null)
	{
		$now = microtime(true);
		
		$latest_timers = array_reverse($this->_timers);
		foreach($latest_timers as $old_mark => $value)
		{
			if(is_null($value)) { continue; }
			if(!is_array($this->_childs[$old_mark]))
			{
				$this->_childs[$old_mark] = array();
			}
			$this->_childs[$old_mark][] = $mark;
		}
		
		$this->_timers[$mark] 	= $now;
		
		if(!is_null($type))
		{
			$this->_markers[$mark]	= $type;
		}
	}
	
	/**
	 * Stops the timer of a mark
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$mark	Name of that mark. 
	 */
	public function stopMark($mark)
	{
		$now = microtime(true);
		if(is_null($this->_timers[$mark])) { return; }
	
		$elapsed = $now - $this->_timers[$mark];
		
		$this->_elapsed[$mark] 	= $elapsed;
		$this->_timers[$mark]	= null;
		
		if($type = $this->_markers[$mark])
		{
			$this->_types[$type] += $elapsed;
		}
	}
	
	/**
	 * Renders a mark and its childs marks
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$mark	Name of that mark. 
 	 * @param	integer	$depth	(opt.) To render nicely marks in "pre" markups
 	 * 
 	 * @return	string
	 */
	public function renderMark($mark, $depth = 1)
	{
		if($this->_rendered[$mark] == true) { return; }
		
		$offset = $depth * 4;
		foreach(range(1, $offset) as $i) { $off_str.= ' '; }
		
		$mark_str = $off_str. '<strong>' . $mark . '</strong> - ' . $this->_elapsed[$mark] . "\n";
		
		
		foreach($this->_childs[$mark] as $child_mark)
		{
			$mark_str.= $this->renderMark($child_mark, $depth+1);
		}
		
		$this->_rendered[$mark] = true;
		return $mark_str;
	}
	
	/**
	 * Renders the 5 slowest marks 
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function renderSlowests()
	{	
		arsort($this->_elapsed, SORT_NUMERIC);
		$slowests = array_slice($this->_elapsed, 0, 5, true);
		foreach($slowests as $slowest => $time) 
		{
			$mark_str = '<strong>' . $slowest . '</strong> - ' . $time . "\n";
		}
	
		return $mark_str;
	}
	
	/**
	 * Reports the profiling result to the screen
	 *  
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @todo	non html rendering for batch scripts
	 */
	public function report()
	{
		ob_end_clean();
		$this->_types = array_reverse($this->_types, true);
		foreach($this->_types as $type => $time)
		{
			$account_time += $time;
			$type_times.= '<strong>' . $type . '</strong> - ' . $time . "\n";
		};
				
		$result = '
<pre class="oos_dump">
<h1>Profiling...</h1>
<h2>Global stats</h2>
<strong>Total time</strong> - ' . $this->_elapsed['global timer'] . '
' . $type_times . '
<strong>oo</strong>spores <strong>time</strong> - ' . ($this->_elapsed['global timer'] - $this->_types['{variables}']) . '
<hr />
<h2>5 slowests marks</h2>
' . $this->renderSlowests() . '
<hr />
<h2>Stack</h2>
' . $this->renderMark('global timer') . '
</pre>
		';
		
		print $result;
	}
}