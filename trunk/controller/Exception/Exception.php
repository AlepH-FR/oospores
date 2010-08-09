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
 * Managing exceptions
 * This class was widely inspired by the Zend_Exception class
 * 
 * @package	Oos_Exception
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Exception extends Exception
{
	/**	Exception|null	The previous exception */
	private $_previous = null;

	/**
	 * Class constructor.
	 * Adds the "previous" fonctionnlaity for PHP version inferior to 5.3
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string 		$msg		Exception's message
	 * @param 	integer 	$code		Exception's code (= severity)
	 * @param	Exception	$previous	The previous exception we caught
	 */
    public function __construct($msg = '', $code = 0, Exception $previous = null)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) 
        {
            parent::__construct($msg, (int) $code);
            $this->_previous = $previous;
        } 
        else 
        {
            parent::__construct($msg, (int) $code, $previous);
        }
    }

    /**
     * Overloading
     * For PHP < 5.3.0, provides access to the getPrevious() method.
     * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
     * @param	string		$method		The method called
     * @param  	array 		$args 		Arguments passed to that method
     * @return 	mixed
     */
    public function __call($method, array $args)
    {
        if ('getprevious' == strtolower($method)) 
        {
            return $this->_getPrevious();
        }
        return null;
    }

    /**
     * String representation of an exception.
     * If we are in an HTML context, we'll print it wrapped into HTML markups, else it'll be sent in the buffer output
     * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
     */
    public function __toString()
    {
    	if(defined('_OO_SPORES'))
    	{
			$result = '
<pre class="oos_error">
<strong><span class="oos_code">' . Oos_Exception_Handler::getExceptionName($this->getCode()) . '</span> - <span class="oos_type">' . get_called_class() . '</span> - <span class="oos_message">'.$this->getMessage().'</span></strong>
<strong>File :</strong> '.$this->getFile().'
<strong>Line :</strong> '.$this->getLine().'
<p class="oos_trace">'.$this->getTraceAsString().'</p>
</pre>	
			';
    	}
    	else
    	{
    		$result = '
Exception caught '.get_called_class().'
				
File : '.$this->getFile().'
Line : '.$this->getLine().'

Message : '.$this->getMessage().'

Stack trace :
'.$this->getTraceAsString().'
			';
    	}
		
		print $result;	
    }
    
    /**
     * Some exceptions may want to be handled ;)
     * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Oos_Exception_Handler
     */
    public function handle()
    {
    	return new Oos_Exception_Handler($this);
    }

    /**
     * Getting previous exception
     * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	Exception
     */
    protected function _getPrevious()
    {
        return $this->_previous;
    }
}