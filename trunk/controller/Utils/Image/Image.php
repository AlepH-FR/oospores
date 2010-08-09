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
 * This is quite a simple library that allows you to add several images, change their dimensions, opacity, quality to build a new image 
 * 
 * @package	Oos_Utils
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Utils_Image extends Oos_BaseClass {
	
	private $_has_src;
	private $_sources;
	private $_sources_fetch = array();
	private $_dest = null;
	private $_quality;
	
	private $_colors;
	private $_color_transparency	= 255;	// transparence par défaut : blanc
	private $_color_background 		= 255;	// background par défaut : blanc
	
	private $_img_handler;
	
	/**
	 * Class constructor.
	 * You can give this function as many arguments as u want to. Each of them being a path to an image.
	 * The first one will be the destination of the new image created by this class if no destination is further defined via the setPath method.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	public function __construct() 
	{
		// no source image
		if(func_num_args() == 0) 
		{
			$this->_dest = new Oos_Utils_Image_Info(null, null, null, null, null);
			return;
		}
		
		// hey, there are some sources !
		$this->_has_src = true;
		
		foreach(func_get_args() as $path) 
		{
			$this->addImage($path);
		}
	}
	
	/**
	 * Class destructor.
	 * Be sure that memory used by the image handler has been free up. 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function __destruct() 
	{
		if($this->_img_handler) 
		{
			imagedestroy($this->_img_handler);
		}
	}
	
	/**
	 * Adds an image 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$page	Path to the image
 	 * @param	boolean	$onTop	(opt.) Put this image on top of all other images
 	 * 
 	 * @throws	Oos_Image_Exception		File does not exists
	 */	
	public function addImage($path, $onTop = false) 
	{
		if(!file_exists($path)) 
		{
			throw new Oos_Image_Exception("File " . $path . " does not exists", OOS_E_WARNING);
			continue;
		}
		
		// getting some data
		list($format, $name) 	= self::getImageInfo($path);
		list($width, $height) 	= getimagesize($path);
		
		// creating an image object
		$image_object = new Oos_Utils_Image_Info($name, $path, $format, $width, $height);
		if($onTop)
		{
			array_unshift($this->_sources, $image_object);
		}
		else 
		{
			$this->_sources[] = $image_object;
		}
		
		// l'image de destination par défaut est la première image
		if(!$this->_dest) 
		{
			$image_object = new Oos_Utils_Image_Info($name, $path, $format, $width, $height);
			$this->_dest = $image_object;
		}
	}
	
	/**
	 * Whenever you want to remove an image from this object. 
	 * Maybe to create multiple images with differents masks within a single source.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$page	Path to the image
	 */	
	public function removeImage($path)  
	{
		$cleaned_source = $this->_sources;
		
		foreach($this->_sources as $src)
		{
			if($src != $path)
			{
				$cleaned_source[] = $src;
			}
		}
		
		$this->_sources = $cleaned_source;
	}
	
	/**
	 * Transformations : apply an homotethy to the destination image
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$ratio	Ratio to apply
 	 * 
 	 * @throws	Oos_Image_Exception		Unable to apply an homothety : no image defined
	 */
	public function homotethy(int $ratio) 
	{
		if(!$this->_has_src)
		{
			throw new Oos_Image_Exception("Unable to apply an homothety : no image defined", OOS_E_WARNING);
		}
		
		$this->_dest->width 	= round($this->_dest->width * $ratio);
		$this->_dest->height 	= round($this->_dest->height * $ratio);
	}
	
	/**
	 * Transformations : sets a new size for the destination file.
	 * If one of the argument is set to false, then it'll be calculated to keep the ratio with the source image.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer|boolean	$width	(opt.) The new width to apply to the destination file
 	 * @param	integer|boolean	$height	(opt.) The new height to apply to the destination file
	 */
	public function setSize($width = false, $height = false) 
	{
		$this->_dest->width = $width;
		$this->_dest->height = $height;
	}
	
	/**
	 * Transformations : sets a new quality for destination file.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$quality	An integer between 0 and 100. 0 for the lowest quality, 100 for the highest one.
 	 * 
 	 * @throws	Oos_Image_Exception	Quality of an image must be an integer between 0 and 100
	 */
	public function setQuality($quality = null)
	{
		if(!is_int($quality) || $quality < 0 || $quality > 100) 
		{
			throw new Oos_Image_Exception("Quality of an image must be an integer between 0 and 100", OOS_E_WARNING);
			$quality = 100;
		}
		
		$this->_quality = $quality;
	}
	
	/**
	 * Transformations : sets format of the destination file to PNG
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function setFormatPNG() 
	{
		$this->_dest->format = "png";
	}
	
	/**
	 * Transformations : sets format of the destination file to JPG
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function setFormatJPG() 
	{
		$this->_dest->format = "jpg";
	}
	
	/**
	 * Transformations : sets format of the destination file to GIF
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function setFormatGIF() 
	{
		$this->_dest->format = "gif";
	}
	
	/**
	 * Transformations : sets a new path for the destination file, if you don't want to erase one of your images
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$path	Path of the destination file
	 */
	public function setPath($path) 
	{
		$this->_dest->path = $path;
	}
	
	/**
	 * Transformations : sets the background color
	 * @see		Oos_Utils_Image::colorFiler
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer|array(3)	$rgb	The rgb color you want to apply
	 */
	public function setBackgroundColor($rgb) 
	{
		$rgb = $this->colorFilter($rgb);
		$this->_color_background 	= $rgb;
	}
	
	/**
	 * Transformations : sets the transparency color
	 * @see		Oos_Utils_Image::colorFiler
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer|array(3)	$rgb	The rgb color you want to apply
	 */
	public function setTransparancyColor($rgb) 
	{
		if($rgb)
		{
			$rgb = $this->colorFilter($rgb);
		}
		
		$this->_color_transparency = $rgb;
	}
	
	/**
	 * Transformations : Fetch a specified image 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$img	Image concerned by the feching or non-fetching
 	 * @param	boolean	$bool	Fetching this image ?
	 */
	public function setFetch($img_path, $bool) 
	{
		$this->_sources_fetch[$img_path] = ($bool);
	}

	/**
	 * Colors : Get a LibGD color handler by its name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$name	Name of that color
 	 * @return	integer
	 */
	private function getColor($name) 
	{
		return $this->_colors[$name];
	}
	
	/**
	 * Colors : Allocates a new LibGD color
	 * @see		Oos_Utils_Image::colorFiler
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string				$name	Name of that color
 	 * @param	integer|array(3)	$rgb	The rgb color you want to apply
	 */
	private function allocateColor($name, $rgb) 
	{
		$rgb = $this->colorFilter($rgb);
		
		list($r, $g, $b) = $rgb;
		$this->_colors[$name] = imagecolorallocate($this->_img_handler, intval($r), intval($g), intval($b));
	}
	
	/**
	 * Colors : Filters a color.
	 * If an integer is given, then it will build an array(3) repeating each time the value.
	 * If an array is given, it must have exactly 3 values in it.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	mixed	$rgb	The rgb color you want to apply
 	 * 
 	 * @throws	Oos_Image_Exception		Wrong format for color allocation : int or array(3)
	 */
	private function colorFilter($rgb) 
	{
		if(is_int($rgb)) 
		{
			$rgb = array($rgb, $rgb, $rgb);
		}
		
		if(count($rgb) != 3) 
		{ 
			throw new Oos_Image_Exception("Wrong format for color allocation : int or array(3)", OOS_E_WARNING);		
		}
		
		return $rgb;
	}
	
	/**
	 * Creating a new image thanks to all the transformations that have been applied before..
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string	Path to the new image
 	 * 
 	 * @throws	Oos_Image_Exception		The size of the image is not defined
 	 * @throws	Oos_Image_Exception		Unable to find the LibGD library
 	 * @throws	Oos_Image_Exception		Format of the source image is not supported
	 */
	public function create() 
	{
		if($this->_dest->width === false && $this->_dest->height === false) 
		{ 
			throw new Oos_Image_Exception("The size of the image is not defined", OOS_E_FATAL);
		}
		
		$sources = array_reverse($this->_sources);
		list($main_source) = $sources;
		
		// calculating destination image dimensions if needed
		if($this->_dest->width === 0 && $this->_dest->height === 0 ) 
		{ 
			$this->_dest->width = $main_source->width; 
			$this->_dest->height = $main_source->height;
		}
		
		if(!$this->_dest->width) 
		{
			$this->_dest->width = round( $main_source->width * ($this->_dest->height / $main_source->height));
		}
		
		if(!$this->_dest->height) 
		{
			$this->_dest->height = round( $main_source->height * ($this->_dest->width / $main_source->width));
		}

		// creating handler
		$this->_img_handler = imagecreatetruecolor($this->_dest->width, $this->_dest->height);
		if(!$this->_img_handler) 
		{
			throw new Oos_Image_Exception("Unable to find the LibGD library", OOS_E_FATAL);
		}
			
		// base image, background and transparency
		$this->allocateColor("background", $this->_color_background);
		
		imagefilledrectangle($this->_img_handler, 0, 0, $this->_dest->width, $this->_dest->height, $this->getColor("background"));
		
		if($this->_color_transparency)
		{
			$this->allocateColor("transparency", $this->_color_transparency);
			imagecolortransparent($this->_img_handler, $this->getColor("transparency"));
		}			
		
		// copying source images
		if($this->_has_src) 
		{
			$sources = array_reverse($this->_sources);
			
			// foreach source...
			foreach($sources as $source) 
			{
				// creating image handler
				switch($source->_format) 
				{
					case "gif":
						$imgSrc = imagecreatefromgif($source->path);
						break;
					case "jpg":
					case "jpeg":
						$imgSrc = imagecreatefromjpeg($source->path);
						break;
					case "png":
		 				$imgSrc = imagecreatefrompng($source->path);
		 				break;
	 				case "bmp":
	 					$imgSrc = self::imageCreateFromBMP($source->path);
	 					break;
		 			default:
	 					throw new Oos_Image_Exception("Format of the source image is not supported", OOS_E_FATAL);
		 				break;
				}
	
				// calculating new dimensions
				$dst_x = 0;
				$dst_y = 0;
				
				$ratio_dest = $this->_dest->width / $this->_dest->height;
				$ratio_src 	= $source->width / $source->height;
				
				// keeping source image ratio...
				if(!$this->_sources_fetch[$source->path]) 
				{
					if($ratio_src > $ratio_dest)
					{
						$tempHeight	= $this->_dest->height;
						$tempWidth	= round(($source->width * $tempHeight) / $source->height);
					}
					elseif($ratio_src < $ratio_dest)
					{
						$tempWidth	= $this->_dest->width;
						$tempHeight	= round(($source->height * $tempWidth) / $source->width);
					}
					else
					{
						$tempHeight = $this->_dest->height;
						$tempWidth	= $this->_dest->width;
					}
					
					$dst_x = round(($this->_dest->width - $tempWidth)/2); 
					$dst_y = round(($this->_dest->height - $tempHeight)/2);   
				} 
				
				// ... or fetch it
				else 
				{
					if ($ratio_src > 1) 
					{
						$tempWidth	= $this->_dest->width;
						$tempHeight	= round(($source->height * $tempWidth) / $source->width);
						$dst_y = round(($this->_dest->height - $tempHeight)/2);
						
					} 
					elseif ($ratio_src < 1) 
					{
						$tempHeight	= $this->_dest->height;
						$tempWidth	= round(($source->width * $tempHeight) / $source->height);
						$dst_x = round(($this->_dest->width - $tempWidth)/2);
						
					}  
					else 
					{
						$tempHeight = $this->_dest->height;
						$tempWidth	= $this->_dest->width;
					}
				}
				
				// copying and destroying handler
				imagecopyresampled($this->_img_handler, $imgSrc, $dst_x, $dst_y, 0, 0, $tempWidth, $tempHeight, $source->width, $source->height);
				imagedestroy($imgSrc);	
			}
		}
		
		// creating image
		switch($this->_dest->format) 
		{
			case "gif":
				imagegif($this->_img_handler, $this->_dest->path);
				return $this->_dest->path;
				
			case "jpg":
				imagejpeg($this->_img_handler, $this->_dest->path, $this->_quality);
				return $this->_dest->path;
				
			case "png":
				$quality = round($this->_quality / 10);
				$compression = 10 - $quality;
				if($compression > 9) { $compression = 9; }
				
				imagepng($this->_img_handler, $this->_dest->path, $compression);
				return $this->_dest->path;
				
 			default:
 				return false;	
		}	
	}
	
	/**
	 * Get some image path informations.
	 * Quite a dumb method but we keep it for compatibily issues
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$path	Path to the new image
 	 * @return 	array
	 */
	static public function getImageInfo($path)
	{
		$image_info = path_info($path);
		return array($image_info['extension'], $image_info['filename']);
	}
	
	/**
	 * Creating a image ressource from a bitmap file
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$filename	Path to the bitmap image
 	 * @return 	ressource
	 */
	static public function imageCreateFromBMP($filename) 
	{
		// Ouverture du fichier en mode binaire
		if (! $f1 = fopen($filename,"rb"))
		{ 
			return false;
		}
	
	 	// Chargement des entêtes FICHIER
		$file = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($file['file_type'] != 19778) 
		{
	   		return false;
		}
	
		// Chargement des entêtes BMP
		$bmp = unpack(	'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
						'/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
						'/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
	
		$bmp['colors'] = pow(2,$bmp['bits_per_pixel']);
		if ($bmp['size_bitmap'] == 0) 
			$bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
			
		$bmp['bytes_per_pixel'] 	= $bmp['bits_per_pixel']/8;
		$bmp['bytes_per_pixel2'] 	= ceil($bmp['bytes_per_pixel']);
		
		$bmp['decal'] 	= ($bmp['width']*$bmp['bytes_per_pixel']/4);
		$bmp['decal'] 	-= floor($bmp['width']*$bmp['bytes_per_pixel']/4);
		$bmp['decal'] 	= 4 - (4*$bmp['decal']);
		
		if ($bmp['decal'] == 4) $bmp['decal'] = 0;
	
	 	// Chargement des couleurs de la palette
		$palette = array();
		if ($bmp['colors'] < 16777216) {
			$palette = unpack('V'.$bmp['colors'], fread($f1,$bmp['colors']*4));
		}
	
	 	// Création de l'image
		$image = fread($f1,$bmp['size_bitmap']);
		$vide = chr(0);
	
		$res = imagecreatetruecolor($bmp['width'],$bmp['height']);
		$p = 0;
		$y = $bmp['height']-1;
		
		while ($y >= 0) 
		{
			$x = 0;
			while ($x < $bmp['width']) 
			{
	     		if ($bmp['bits_per_pixel'] == 24) 
	     		{
	        		$color = unpack("V",substr($image,$p,3).$vide);
				} 
				
				elseif ($bmp['bits_per_pixel'] == 16) 
				{ 
	        		$color = unpack("n",substr($image,$p,2));
	        		$color[1] = $palette[$color[1]+1];
	     		} 
	     		
	     		elseif ($bmp['bits_per_pixel'] == 8) 
	     		{ 
	        		$color = unpack("n",$vide.substr($image,$p,1));
	        		$color[1] = $palette[$color[1]+1];
	     		} 
	     		
	     		elseif ($bmp['bits_per_pixel'] == 4) 
	     		{
					$color = unpack("n",$vide.substr($image,floor($p),1));
					if (($p*2)%2 == 0) 	$color[1] = ($color[1] >> 4) ; 
					else 				$color[1] = ($color[1] & 0x0F);
					
	        		$color[1] = $palette[$color[1]+1];
	     		} 
	     		
	     		elseif ($bmp['bits_per_pixel'] == 1) 
	     		{
			        $color = unpack("n",$vide.substr($image,floor($p),1));
			        if     (($p*8)%8 == 0) $color[1] =  $color[1]        >>7;
			        elseif (($p*8)%8 == 1) $color[1] = ($color[1] & 0x40)>>6;
			        elseif (($p*8)%8 == 2) $color[1] = ($color[1] & 0x20)>>5;
			        elseif (($p*8)%8 == 3) $color[1] = ($color[1] & 0x10)>>4;
			        elseif (($p*8)%8 == 4) $color[1] = ($color[1] & 0x8)>>3;
			        elseif (($p*8)%8 == 5) $color[1] = ($color[1] & 0x4)>>2;
			        elseif (($p*8)%8 == 6) $color[1] = ($color[1] & 0x2)>>1;
			        elseif (($p*8)%8 == 7) $color[1] = ($color[1] & 0x1);
			        $color[1] = $palette[$color[1]+1];
	     		} 
	     		
	     		else 
	     		{
	        		return false;
	     		}
	     		
	     		imagesetpixel($res,$x,$y,$color[1]);
	     		$x++;
	     		$p += $bmp['bytes_per_pixel'];
	    	}
	    	
			$y--;
			$p += $bmp['decal'];
		}
	
	 	// Fermeture du fichier
		fclose($f1);
	
	 	return $res;
	}	
}