<?php

class ServiceController extends Zend_Controller_Action
{

    public function init()
    {
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    } 	
	
	private function handleWSDL($class)
	{
        $autodiscover = new Zend_Soap_AutoDiscover();
        $autodiscover->setClass($class);
        $autodiscover->handle();		
	}
    
    private function handleSOAP($class, $url)
    {
    	$soap = new Zend_Soap_Server($url);
        $soap->setClass($class);
        $soap->setObject(new $class());
        $soap->handle();    	
    }
	
    public function userAction()
    {
        if (isset($_GET['wsdl'])) {
        	
            $this->handleWSDL('App_Service_User');
        	
        } else {	            
        	
            $this->handleSOAP('App_Service_User', $this->view->url() . '?wsdl');
        	
        }

    }
    
}