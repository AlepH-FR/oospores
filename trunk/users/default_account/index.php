<?php
define('_OOS_ACCOUNT', 'default_account');
require_once ('../../_init.php');

// building page
try 
{
	$controller->init();
} 

catch (Exception $e) 
{
	new Oos_Exception_Handler($e);
}