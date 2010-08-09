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
 * Compressing a CSS file and it's @import instructions in a single one
 * 
 * @package	Oos_CSS
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_CSS_Compressor
{
	/** string	The css code */
	private $_css;
	
	/**
	 * Get CSS code
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return string
	 */
	public function getCSS() { return $this->_css; }
	
	/**
	 * Prints CSS code with a "text/css" header
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function printCSS()
	{
		header("Content-type:text/css");
		print $this->_css;	
	}
	
	/**
	 * Class constructor.
	 * Minimalize a CSS file and imports its "@import" dependencies in the same file.
	 * 
	 * @warning I was inspired by a method I found some years ago but i can't remember the author's name. 
	 * If u know him, please tell me, so I can credit him !
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$base_file		Name of the base CSS file
	 * @param	string	$base_path		Path to access this file
	 */
	public function __construct($base_file, $base_path)
	{
		if(!file_exists($base_path . DS . $base_file))
		{
			return;
		}	

  		$css = file_get_contents($base_path . DS . $base_file);

		$other_css_files = array();

		// replacing imports by file contents
  		$pattern = '/\@import url\((.*\.css)\);/e';
  		preg_match_all($pattern, $css, $matches, PREG_PATTERN_ORDER);
  		$other_css_files = array_merge($other_css_files, $matches[1]);
  		
  		$pattern = '/\@import "(.*\.css)";/e';
  		preg_match_all($pattern, $css, $matches, PREG_PATTERN_ORDER);
  		$other_css_files = array_merge($other_css_files, $matches[1]);

		foreach($other_css_files as $match)
		{
			$content = file_get_contents($base_path . DS . $match);
			
			if(preg_match($pattern, $css))
			{
				$css = str_replace('@import url\('.$match.'\);', $content, $css);
			}

			if(preg_match($pattern, $css))
			{
				$css = str_replace('@import "'.$match.'";', $content, $css);
			}
		}

	  	// windows to unix linebreakers
  		$css = str_replace(chr(13),chr(10), $css);

  		// deleting comments
  		$com_begin = strpos($css,'/*');
 		$in_ie_hack = false; 
 		
  		while($com_begin !== false) 
  		{
    		$com_end = strpos($css,'*/', $com_begin + 2);
    		if($com_end === false) { break; }

    		// ie hacks
   			if(substr($css, $com_end-1, 1) == '\\') 
   			{
      			$css = substr($css, 0, $com_begin).'/*\*/'.substr($css, $com_end+2);
	      		$in_ie_hack = true;
   			} 
   			else 
   			{
     			if($in_ie_hack) 
     			{
        			$css = substr($css, 0, $com_begin).'/**/'.substr($css, $com_end+2);
      			} 
      			else 
      			{
      				// deleting comment
        			$css = substr($css, 0, $com_begin).substr($css, $com_end+2);
      			}
     
	      		$in_ie_hack = false;
    		}

    		$com_begin = strpos($css, '/*', $com_begin+1);
  		}

  		// linebreakers to white spaces
  		$css = preg_replace('/'.chr(10).'+/', ' ', $css);

  		// multiple white spaces deletion
  		$css = preg_replace('/[ '.chr(9).']+/', ' ',  $css );

  		// useless white spaces deletion
  		$css = preg_replace('/[ '.chr(9).']*([:;{},])[ '.chr(9).']*/', '\\1',  $css );

 		// useless ponctuation
  		$css = str_replace(';}','}', $css);

  		$this->_css = $css;
	}
}