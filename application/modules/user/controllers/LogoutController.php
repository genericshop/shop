<?php

class User_LogoutController extends Zend_Controller_Action
{
    
    public function init()
    {}
    
    public function indexAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::forgetMe();
		
		$cart = new Zend_Session_Namespace('Cart');
		$cart->unsetAll();
		
		$this->_helper->flashMessenger(array('success' => $this->view->translate('You have been logged out successfully.')));
		$this->_helper->redirector->gotoRoute(array(), 'login');
    }

}