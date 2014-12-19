<?php

class App_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $config 	= new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        $navigation = new Zend_Navigation($config);
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->navigation($navigation)->setAcl(Zend_Registry::get('Zend_Acl'))->setRole(Zend_Registry::get('Zend_Role'));
                
        Zend_Registry::set('Zend_Navigation', $navigation);
    }
    
}