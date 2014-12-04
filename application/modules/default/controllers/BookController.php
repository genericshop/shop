<?php

class BookController extends App_Controller_Action
{
	
    /*
    public function init()
    {
        parent::init();
        $this->view->nav = $this->getNavigation();
    }
    */
    
    public function listAction()
    {
        $type = $this->getParam('type', 'print');
        
        if ($type === 'print') {
            
            $this->view->heading = _('Books (Print)');
            $items = $this->_api->getBooks();
            
        } elseif ($type === 'digital') {
            
            $this->view->heading = _('Books (Digital)');
            $items = $this->_api->getEbooks();
            
        }
        
        if ($items && is_array($items)) {
            
            usort($items, function($a, $b) {
                return $a->Name > $b->Name;
            }); // could probably do this client side
            
            $this->view->items = $items;
            
        }

        
    }
    
    public function viewAction()
	{
        $result = $this->_api->getBook($this->getParam('id', null)); 
        
        if (true !== $result->Status) {
            $this->_helper->flashMessenger(array('error' => 'The item you are trying to view does not exist.'));
            $this->_helper->redirector('index', 'index', 'default');
        }
            
        $this->view->book = $result;
	}
	
}