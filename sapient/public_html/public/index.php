<?php

define('APPLICATION_ENV', 'production');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

switch (APPLICATION_ENV) 
{
	
	case 'production':
		$path = '../library';
		break;
		
    case 'staging':
    default:
		$path = '../../ZendFramework-1.12.7/library';
	
}

set_include_path(implode(PATH_SEPARATOR, array(realpath(dirname(__FILE__) . '/' . $path))));

require_once 'Zend/Application.php';

$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();