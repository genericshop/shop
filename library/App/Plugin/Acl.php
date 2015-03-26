<?php

class App_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	
	protected $_acl;
	
	public function __construct()
	{
		$this->_acl = new App_Acl();
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$auth = Zend_Auth::getInstance();
		
		if (!$auth->hasIdentity())
			$role = 'Guest';
		else
			$role = isset($auth->getIdentity()->AccountType) ? $auth->getIdentity()->AccountType : 'Guest';    	

		Zend_Registry::set('Zend_Acl', $this->_acl);
		Zend_Registry::set('Zend_Role', $role);
		
    	$module 	= $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$action     = $this->getRequest()->getActionName();

		if ($this->_acl->has($module . ':' . $controller . ':' . $action)) 
		    $resource = $module . ':' . $controller . ':' . $action;
		
		elseif ($this->_acl->has($module . ':' . $controller))
            $resource = $module . ':' . $controller;
		
		else 
		    $resource = $module;
		
		if ($resource && !$this->_acl->isAllowed($role, $resource)) {
		    
			$request
				->setModuleName('default')
				->setControllerName('error')
				->setActionName('denied');
			
			return;
		    
		}
    	
    }
    
}