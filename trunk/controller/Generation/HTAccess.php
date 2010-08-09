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
 * Generation of HTAccess file with proper rewriting rules
 * 
 * @package	Oos_Generation
 * 
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 */
class Oos_Generation_HTAccess extends Oos_BaseClass
{
	/**	string		The account we are currently generating */
	private $_account; 
	/** Oos_Config	The configuration handler */
	private $_config;
	
	/** array	Rules added to this instances */
	private $_rewrite_rules = array();
	/** string	Calculated output */
	private $_output 		= array();
	
	/**
	 * Class constructor.
	 * Instanciate Oos_Config
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$account	The account we want to generate
	 */	
	public function __construct($account)	
	{
		$this->_account = $account;
		$this->_config 	= Oos_Config::getInstance($this->_account);
	}
		
	/**
	 * Generates output for the htaccess file
	 * . calculating error documents
	 * . populating rewrite rules for all rules added to this instance
	 * 
	 * @version	1.1
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
	 */
	public function populate()
	{
		$xml_data = Oos_XML_Collection_Sitemap::getInstance($this->_account);
		// gestion des droits
		// 401 et 405 : accès refusé
		$this->_output[] = "ErrorDocument 401 /_public/www/".$xml_data->getPageAccessDenied().".htm";
		$this->_output[] = "ErrorDocument 405 /_public/www/".$xml_data->getPageAccessDenied().".htm";

		// 404 : fichier non trouvé
		$this->_output[] = "ErrorDocument 404 /_public/www/".$xml_data->getPageNotFound().".htm";
	
		$this->_output[] = "";
		$this->_output[] = "RewriteEngine On";
		
		// écriture des règles de rewriting
		if(count($this->_rewrite_rules) > 0)
		{	
			foreach($this->_rewrite_rules as $rule)
			{
				$this->_output[] = 'RewriteRule '."\t".'^'.$rule->to."\t".' /_public/www/'.$rule->from.' ['.implode(",",$rule->flags).']';
			}
		}
		
		// alias des pages générés
		$this->_output[] = 'RewriteRule ' . "\t" . '^([^/]*)\.htm' . "\t" . '/_public/www/$1.php [QSA]';	  	
		
		// écriture du fichier
		$file_name = $this->_config->getAccountDir() . DS . '.htaccess';
		file_put_contents($file_name, implode("\n", $this->_output));
	}
		
	/**
	 * Creates an instance of Oos_Generation_RewriteRule and adds it to the rewrite rule list
	 * 
	 * @version	1.0
	 * @since	0.1.4
 	 * @author	Antoine Berranger <antoine@oospores.net>
 	 * 
	 * @param	string	$from	Source of the rule
	 * @param	string	$to		Destination of the rule
	 * @param	array	$flags	Flags for this rule
	 */
	public function addRewriteRule($from, $to, $flags)
	{
		$this->_rewrite_rules[] = new Oos_Generation_RewriteRule($from, $to, $flags);
	}
}