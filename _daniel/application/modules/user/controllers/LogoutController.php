<?php

class User_LogoutController extends Zend_Controller_Action
{
    
    public function init()
    {}
    
    public function indexAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		
		Zend_Session::forgetMe();
		
		$cartSession = new Zend_Session_Namespace('cart');
		$cartSession->unsetAll();
		
		$this->_helper->redirector('index', 'index', 'default');
    }

}