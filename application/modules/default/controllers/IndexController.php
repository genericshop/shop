<?php

class IndexController extends App_Controller_Action
{
    
    public function indexAction()
    {
        //$this->view->specials = $this->_api->getAllSpecials();
        $this->view->terms = $this->_api->getStoreTerms();
        $store = App_Session::getInstance()->get('Store');

        if (!empty($store["layout"])) {
        	$this->_helper->layout->setLayout($store["layout"]);
        }
        
        $books = $this->_api->getBooks();

        $this->view->books = array_slice($books, 0, 10);
    }
}