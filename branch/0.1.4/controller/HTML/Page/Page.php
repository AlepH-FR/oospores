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
 * Default Page implementation
 * 
 * @package	Oos_HTML
 * @subpackage	Page
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
abstract class Oos_HTML_Page extends Oos_BaseClass 
{
	/** string	Name of this page */
	public $name		= false;
	/** string	Template of this page */
	public $template	= false;
	/** string	Title of this page */
	public $title 		= false;
	/** string	CSS's template of this page */
	public $css_template	= false;
	/** array	Scripts that'll be rendered on this page */
	public $scripts 	= array();
	/** array	Styles that'll apply to this page */
	public $styles 		= array();
	/**	string	Path to ico favicon is set */
	public $favicon_ico;
	/**	string	Path to png favicon is set */
	public $favicon_png;
	
	/**	boolean	Let us know if that page was loaded properly ! */
	protected $_is_loaded 	= false;
	/**	string	Meta markup description of that page */
	protected $_description;
	/**	string	Meta markup keywords of that page */
	protected $_keywords;
	/**	string	Lang of this page */
	protected $_lang;
	/**	string	Charset used */
	protected $_charset;
	/**	string	Role associated with this page */
	protected $_role;
	/** array	Blocks that'll be rendered on this page */
	protected $_blocks	= array();
	
	/**	string		The account we are currently generating */
	protected $_account; 
	/** Oos_Config	The configuration handler */
	protected $_config;
	
	/**
	 * Class constructor.
	 * Get instance of Oos_XML_Collection_Sitemap and exctract informations about our page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$page_name	Name of our page
	 * @param 	string	$account	(opt.) Name of the account. By default it is set by the OOS_ACCOUNT variable
	 */
	public function __construct($page_name, $account = null)
	{
		$this->_account = $account;
		$this->_config	= Oos_Config::getInstance($this->_account);
		$xmlData = Oos_XML_Collection_Sitemap::getInstance($this->_account);
		
		$page = $xmlData->getPage($page_name);
		
		$this->name 		= $page['name'];
		$this->template		= $page['template'];
		$this->title 		= $xmlData->getTitle($this->name);
		$this->scripts 		= $xmlData->getScripts($this->name);
		$this->styles 		= $xmlData->getStyles($this->name);
		
		$this->_description = $page['description'];
		$this->_role 		= $page['role'];
		$this->_keywords 	= $xmlData->getKeywords($this->name);
		$this->_blocks  	= $xmlData->getBlocks($this->name);
		$this->_lang	  	= $xmlData->getLang();
		$this->_charset  	= $xmlData->getCharset();
		$this->_is_loaded 	= true;
	}
	
	/**
	 * Get page's role
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	string
	 */
	public function getRole()			{ return $this->_role; }
	
	/**
	 * See if this page was loaded properly
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	boolean
	 */
	public function isPageFound() 		{ return $this->_is_loaded; }
	
	/**
	 * Get page's name
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	boolean
	 */
	public function getName() 			{ return $this->_name; }
	
	/**
	 * Get page's css template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @return	boolean
	 */
	public function getCSSTemplate()	{ return ($this->css_template) ? $this->css_template : "default"; }
	
	/**
	 * Adds a script to the page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	script	Url of the script we wanna add.
 	 * 
	 * @return	boolean
	 */
	public function addScript($script) 	
	{ 
		if(strpos($script, "/") === false) 
		{
			$script_to_add = $this->_config->getScriptUrl() . '/';
		}
		
		$script_to_add .= $script;	
		
		if(!in_array($script_to_add, $this->scripts))
		{
			$this->scripts[].= $script_to_add;
		}
	}
	
	/**
	 * Checks for scripts unicity
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function checkScripts()
	{
		foreach($this->scripts as $index => $script)
		{
			if(strpos($script, "/") === false) 
			{
				$this->scripts[$index] = $this->_config->getScriptUrl() . '/' . $script;
			}			
		}
	}
	
	/**
	 * Adds a style to the page
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	string	$style		Name of the style file
 	 * @param	string	$template	(opt.) The template on which we'll find this file. If not set, we'll look for this page on the page's css template
 	 * @param	string	$media		(opt.) For which media we add this style. By default : all
	 */
	public function addStyle($style, $template = null, $media = "all") 
	{
		$this->styles[] = array(
			'src' 		=> $style,
			'template'	=> $template,
			'media'		=> $media,
		); 
	}
	
	/**
	 * Caches page's html code
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function cache()
	{
		$config = Oos_Config::getInstance();
		
		$type 		= $config->getParam("CACHE", 'OUT');
		$lifetime 	= $config->getParam("CACHE", 'LIFETIME');
		
		$options = array(
			'lifetime'	=> Oos_Utils_String::lifetime2seconds($lifetime),
		);
		
		$cache = new Oos_Cache_Cache($type, $options);
		return $cache->cacheFuncPrint(array($this, "doRender"));
	} 
	
	/**
	 * Buildings html code for scripts
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getScripts()
	{
		foreach($this->scripts as $script) 
		{
			if(!$script) { continue; }
			
			$scripts.= '
		<script charset="iso-8859-1" type="text/javascript" src="' . $script . '"></script>
			';
			$scripts = trim($scripts);
		}

		return $scripts;	
	}
	
	/**
	 * Buildings html code for styles
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getStyles()
	{
		foreach($this->styles as $style) 
		{
			if(!$style) { continue; }
			
			$styles.= '
		<link type="text/css" rel="stylesheet" href="' . $this->getStyleDir($style['template']) . '/' . $style['src'] . '" media="' . $style['media'] . '" />
			';
		}	
		
		return $styles;
	}
	
	/**
	 * Adds a google verification markup if specified in configuration files
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getGoogleVerification()
	{
		$code = $this->_config->getParam("GOOGLE", 'VERIFICATION_KEY');
		if(!$code) { return ''; }
		
		return '
		<meta name="google-site-verification" content="' . $code . '" />
		';
	}

	/**
	 * Buildings html code for title
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getTitre()
	{
		return '
		<title>' . $this->title . '</title>
		';	
	}
	
	/**
	 * Buildings html code for rss and atom files
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
 	 * 
 	 * @todo	Clean that
	 */
	public function getFlux()
	{
		$flux = '';
		
		if(file_exists($this->_config->getAccountDir().'/atom.xml'))
		{
			$flux.= '
		<link rel="alternate" type="application/atom+xml" title="' . $this->_config->getParam("ACCOUNT", 'NAME') . '" href="' . $this->_config->getParam("ACCOUNT", 'URL') . '/atom.xml" />
			';
		}
		
		return $flux;
	}
	
	/**
	 * Buildings html code for meta markups
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
	 */
	public function getMeta()
	{
		$meta = '
		<meta 
			name="keywords" 
			content="' . utf8_decode($this->_keywords) . '" 
			lang="' . $this->_lang . '"
		/>
		<meta 
			name="description"
			content="' . utf8_decode($this->_description) . '" 
			lang="' . $this->_lang . '"
		/>
		<meta 
			equiv="Content-Type"
			content="text/html; charset=' . $this->_charset . '" 
		/>
		<meta 
			name="Content-Language"
			content="' . $this->_lang . '" 
		/>
		';		
		
		return $meta;	
	}

	/**
	 * Get path of a specified css template
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param 	string 	$css_template	The css template we are looking for
	 * @return	string
	 */
	final public function getStyleDir($css_template = null) 
	{
		if(!$css_template) 
		{
			$css_template = $this->getCSSTemplate();
		}
		
		return $this->_config->getStyleUrl($css_template);
	}
	
	/**
	 * Get the oospores markup for zones in page templates
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	integer	$num_zone	Identifier of that zone
 	 * @return	string
	 */
	final public function getZonePattern($num_zone) 
	{
		$pattern = $this->_config->getMarkup('zone:'.$num_zone);
		return $pattern;
	}
	
	/**
	 * Process the <body> of the page.
	 * Rendering each block of that page.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @return	string
 	 * 
 	 * @throws	Oos_HTML_Exception	Page's template not found for page
 	 * @throws	Oos_HTML_Exception	Zone not found in template
	 */
	final public function getInnerBody() 
	{
		global $controller;
		
		$known_zones = array();
		$innerBody = $controller->getTemplatePage($this->template, $this->_account);
		if(!$innerBody) 
		{
			throw new Oos_HTML_Exception("Page's template '" . $this->template . "' not found for page '" . $this->name . "'", OOS_E_FATAL);
		}
		
		if(is_array($this->_blocks))
		{ 
			foreach($this->_blocks as $block) 
			{	
				$zone 		= $block['zone'];
				$template 	= $block['template'];
				$known_zones[$zone] = true;
				
				$pattern = $this->getZonePattern($zone);
				$pos = strpos($innerBody, $pattern);

				if($pos === false) 
				{
					throw new Oos_HTML_Exception("Zone '" . $zone . "' not found in template '" . $this->template . "'", OOS_E_WARNING);
				}
			
				$before = substr($innerBody, 0, $pos);
				$after  = substr($innerBody, $pos, strlen($innerBody));
				
				$block = $controller->getBlock($template, $this->_account, $this->name, $this);
				$innerBody = $before . "\n" . $block->doRender() . "\n" . $after;
			}
		}
		
		foreach($known_zones as $key => $boolean)
		{
			$innerBody = str_replace($this->getZonePattern($key), '', $innerBody);
		}
		
		return $innerBody;
	}
	
	/**
	 * This method can process some stuff or make some calculation before the doRender method is called 
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */	
	abstract function render();
	
	/**
	 * Render html code of our page.
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
 	 * @param	boolean	$dynamic	(opt.) If set to true, it will render a PHP and HTML code that'll generate this page later on
	 */	
	final public function doRender($dynamic = false) 
	{
		global $controller;
		$this->checkScripts();
		$this->render();
		
		if($dynamic)
		{
			// header php
			$html = '<?php
	define("_OOS_ACCOUNT", "' . $this->_account . '");
	require_once("' . realpath(ROOT_DIR) .'" . DIRECTORY_SEPARATOR . "_init.php");
	$page = $controller->init("' . $this->name . '", true);
	$page->render();

	ob_start();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->_lang . '" lang="' . $this->_lang . '">
	<head>
		<?php print $page->getGoogleVerification(); ?>
		<?php print $page->getTitre(); ?>
		<?php print $page->getMeta(); ?>
		<?php print $page->getFlux(); ?>

 		<link rel="shortcut icon" type="image/x-icon" href="' . $this->favincon_ico . '" />
		<link rel="icon" type="image/png" href="' . $this->favicon_png . '" />

		<?php print $page->getStyles(); ?>
		
		<script type="text/javascript">
			oos_onload = {};
		</script>
	</head>
	<body>
<?php
	if($controller->isAdminPage())
	{
		print $controller->inner_bo;
	}
	print $page->getInnerBody();
?>
<?php print $page->getScripts(); ?>
		<script type="text/javascript" defer="defer">
			for(var key in oos_onload)
			{
				var fn = oos_onload[key];
				fn();
			}
		</script>
	</body>
</html>
<?php
	ob_end_flush();
?>
			';	
		}
		else
		{		
			// ajout des contrôles CMS si besoin
			if($controller->isAdminPage()) 
			{
				$inner_bo = $controller->inner_bo;
			}
			
			// construction du corps de la page
			$innerBody = $this->getInnerBody();
		
			$scripts 	= $this->getScripts();
			$styles		= $this->getStyles();
			$meta 		= $this->getMeta();
			$titre		= $this->getTitre();
			$google		= $this->getGoogleVerification();
			$flux		= $this->getFlux();
			
			$html.= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->_lang . '" lang="' . $this->_lang . '">
	<head>
		' . $google . '
		' . $titre . '
		' . $meta . '
		
		' . $flux . '

 		<link rel="shortcut icon" type="image/x-icon" href="' . $this->favicon_ico . '" />
		<link rel="icon" type="image/png" href="' . $this->favicon_png . '" />
		' . $styles . '
		
		<script type="text/javascript" defer="defer">
			oos_onload = {};
		</script>
	</head>
	<body>
' . $inner_bo . '
' . $innerBody . '
' . $scripts . '
		<script type="text/javascript">
			for(var key in oos_onload)
			{
				var fn = oos_onload[key];
				fn();
			}
		</script>
	</body>
</html>
		';	
		}
		
		return $html;
	}
}