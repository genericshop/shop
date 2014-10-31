<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initConfig()
	{
	    Zend_Registry::set('config', $this->getOptions());
	}
	
	protected function _initRoutes()
	{
	    $this->bootstrap('frontController');
		
	    $front 	= $this->getResource('frontController');
		$router = $front->getRouter();
		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
		$router->addConfig($config, 'routes');
	}
	
	protected function _initCache()
	{
	    $frontend = array('lifetime' => 14400, 'automatic_serialization' => true);
	    $backend  = array('cache_dir' => APPLICATION_PATH . '/../data/cache');
	
	    Zend_Registry::set('Cache', Zend_Cache::factory('Core', 'File', $frontend, $backend));
	}
	
}