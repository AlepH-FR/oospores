<?php

/**
 * Shortcut for Oos_Url::getUrl method
 * 
 * @version	1.0
 * @since	0.1.4
  * @author	Antoine Berranger <antoine@oospores.net>
  * 
 * @param 	string	$page 		Name of the page
 * @param 	mixed 	$object		(opt.) If it's a rewrited page, we need an object to build the proper uri
 * @param 	mixed 	$variables	(opt.) Variables to add in the query string
 * @param 	string	$account	(opt.) On what account are we looking for this url
 * 
 * @return 	string
 */
function url($page = null, $object = null, $variables = null, $account = null)
{
	return Oos_Url::getUrl($page, $object, $variables, $account);
}

/**
 * Shortcut for Oos_i18n::getLocalString
 * 
 * @version	1.0
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @param 	string	$group		Group of the wording 
 * @param 	string	$key		Key of this wording 
 * @param 	string	$account	(opt.) On what account are we looking for this wording ?
 * 
 * @return	string
 */
function i18n($group, $key, $account = null)
{
	return Oos_i18n::getLocalString($group, $key, $account);
}

/**
 * A dumb dump (funny to say, right ?) to avoid writing '<pre>' markup everytime we wanna watch a variable's content
 * 
 * @version	1.0
 * @since	0.1.4
 * @author	Antoine Berranger <antoine@oospores.net>
 * 
 * @param 	mixed	$o	Variable to dump
 */
function oos_dump($o)
{
	print '<pre class="oos_dump">';
	print_r($o);
	print '</pre>';
}