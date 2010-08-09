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
 * Abstract class for OS operations
 * 
 * @package	Oos_System
 * @subpackage	OS
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_System_OS_Common extends Oos_BaseClass
{
	/**
	 * Returns the memory print used by this thread
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	integer
	 */
	abstract public function getMemoryUsage();
	
	/**
	 * Looking recursively for a file in a directory.
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string 	$filename	The file to look for
	 * @param 	string 	$rep		The base directory to look in	
	 * @param	array	$extensions	The file extensions we are looking for
	 * @return	string|false
	 */
	public function findFileInDirectory($filename, $rep, $extensions = array()) 
	{	
		if(count($extensions) > 0)
		{
			foreach($extensions as $ext) 
			{
				$fname_w_ext = $filename . "." . $ext;
				$path = $rep . DS . $fname_w_ext;
				if(file_exists($path)) 
				{
					return $path;
				}						
			}
		}
		else
		{
			$path = $rep . DS . $filename;
			if(file_exists($path)) 
			{
				return $path;
			}			
		}
		
		if(!$dir = @opendir($rep)) { return false; }
		
		while(false !== ($file = readdir($dir)))
		{
			if ($file == "." || $file == "..") 
			{
				continue;
			}
		
			$path = $rep . DS . $file;
			if (!is_dir($path)) { continue; }
			
			$file_found = $this->findFileInDirectory($filename, $path, $extensions);
			if($file_found) 
			{ 
				return $file_found;
			}
		}		
		
		return false;
	}	
	
	/**
	 * Looking recursively for files in a directory.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 * 
	 * @param 	string 	$class		The file to look for
	 * @param 	string 	$rep		The base directory to look in	
	 * @param	array	$extensions	The file extensions we are looking for
	 * @return	array
	 */
	public function findFilesInDirectory($rep, $extensions = array()) 
	{	
		$files = array();
		if(!$dir = @opendir($rep)) { return false; }
		
		while(false !== ($file = readdir($dir)))
		{
			if ($file == "." || $file == "..") 
			{
				continue;
			}
		
			$path = $rep . DS . $file;
			$info = pathinfo($path);

			// recursive research
			if (is_dir($path)) 
			{
				$sub_dir_files = $this->findFilesInDirectory($path, $extensions);
				if(count($sub_dir_files) > 0) 
				{ 
					$files = array_merge($files, $sub_dir_files);
				}
				
				continue;
			}
			
			if(in_array($info['extension'], $extensions))
			{
				$files[] = $path;
			}
		}		
		
		return $files;
	}	
	
	/**
	 * Remove a directory recursively
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$dirname	Path to that directory
 	 * @return	boolean				Success/failure
	 */
	public function rmdir($dirname)
	{
		// Sanity check
	    if (!file_exists($dirname)) 
	    {
	        return false;
	    }
	 
	    // Simple delete for a file
	    if (is_file($dirname) || is_link($dirname)) 
	    {
	        return unlink($dirname);
	    }
	 
	    // Loop through the folder
	    $dir = dir($dirname);
	    while(false !== $entry = $dir->read()) 
	    {
	        // Skip pointers
	        if ($entry == '.' || $entry == '..') 
	        {
	            continue;
	        }
	 
	        // Recurse
	       	$this->rmdir($dirname . DS . $entry);
	    }
	
	   // Clean up
	   $dir->close();
	   
	   return rmdir($dirname);
	}
}