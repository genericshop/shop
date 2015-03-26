<?php

class App_Plugin_Redir extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if ($request->getParam('redir', null)) {
    		$ns = new Zend_Session_Namespace('Redir');
    		$ns->url = $request->getParam('redir');
		}
    }
    
}