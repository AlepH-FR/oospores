<?php
define('_OOS_ACCOUNT', 'default_account');
require_once('../../_init.php');

try {
	// Add your custom listeners here
	
	// Trigger ajax call
	new Oos_Ajax_Call();
} 

catch(Oos_Exception $e) 
{
	new Oos_Exception_Handler($e);
}