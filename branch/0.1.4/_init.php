<?php

if (version_compare(phpversion(), '5.1', '<')) die ('OOSpores require PHP 5.1 or higher.'); 
error_reporting(E_ERROR | E_WARNING);

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__FILE__));
define('_OO_SPORES', true);

// time hack
$cur_time = time();
date_default_timezone_set('Europe/Paris');
define('DATE_DIFFERENCIAL', date('Z', $cur_time));

require_once(ROOT_DIR . DS . 'controller' . DS . '_shortcuts.inc');

require_once(ROOT_DIR . DS . 'controller' . DS . 'BaseClass.php');
require_once(ROOT_DIR . DS . 'controller' . DS . 'Autoload.php');
require_once(ROOT_DIR . DS . 'controller' . DS . 'Exception' . DS. 'Handler.php');

set_error_handler(array("Oos_Exception_Errors", "errorHandler"));

try 
{
	$controller = new Oos_Controller();
} 

catch(Oos_Exception $e) 
{
	new Oos_Exception_Handler($e);
}