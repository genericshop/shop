<?php

class IndexController extends App_Controller_Action
{
    
    public function indexAction()
    {
        //$this->view->specials = $this->_api->getAllSpecials();
        $this->view->terms = $this->_api->getStoreTerms();
        $store = App_Session::getInstance()->get('Store');
    }
}