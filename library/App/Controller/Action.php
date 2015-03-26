<?php

abstract class App_Controller_Action extends Zend_Controller_Action
{

    protected $_store;
    protected $_lang;
    protected $_auth;
    protected $_api;
    
    public function preDispatch()
    {
        if ($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout()->disableLayout();
    }
        
    public function init()
    {
        $this->_store = $this->view->store = App_Session::getInstance()->get('Store');
        $this->_api   = new App_Service_Rest($this->_store['StoreID']);
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_auth = $this->view->auth = Zend_Auth::getInstance()->getIdentity();
        } else {
            $this->_auth = $this->view->auth = null;
        }
        
        $this->_lang = $this->view->lang = Zend_Registry::get('LanguageSuffix');
    }
    
}