<?php

class BookController extends Zend_Controller_Action
{
	
    private $model;
    
    public function init()
    {
        $this->model = new App_Model_Book();
    }
    
    public function listAction()
    {
        $category = $this->view->category = $this->getCategory($this->getParam('id', 0));
        
        if (!$category) {
            
            $model  = new App_Item();
            $params = array('order' => 'RAND()', 'limit' => 4);
            
            if ($this->getParam('ebook'))
                $params['ebook'] = true;
            
            $this->view->featured = $model->getBooks(array('featured' => 1) + $params);
            $this->view->specials = $model->getBooks(array('special'  => 1) + $params);
            
            goto view;  
             
        }     
        
        // set parameters for select
        
        $ids    = $this->getChildren($category['id']); 
        $ids[]  = $category['id'];
        
        $params = array('category' => $ids, 'order' => 'b.name ASC');
        
        if ($this->getParam('sort', null)) {
            
            $sort = $this->view->sort = $this->getParam('sort');
            
            switch ($sort) {
            	
            	case 'name-des':
            	    $params['order'] = 'b.name DESC';
            	    break;
            	    
            	case 'price-asc':
            	    $params['order'] = 'price';
            	    break;
            	    
            	case 'price-des':
            	    $params['order'] = 'price DESC';
            	    break;
            	    
            }
            
        }
        
        // end
        
        $model = new App_Item();
        $items = $model->getBooks($params);
        
        if ($items) {
             
            $paginator = Zend_Paginator::factory($items);
            $paginator->setDefaultItemCountPerPage(9);
            $paginator->setCurrentPageNumber($this->getParam('page', 1));
        
            $this->view->paginator = $paginator;
             
        } 

        view:
        
    }
    
    public function viewAction()
	{
        $category = $this->view->category = $this->getCategory($this->getParam('category-id', null));
        
        if (!$category)
            $this->_helper->redirector->gotoRoute(array(), 'home');        
        
        $book = $this->view->book = $this->model->getForParams(array('id' => $this->getParam('id'), 'active' => 1));
        
        if (!$book)
            $this->_helper->redirector->gotoRoute(array(), 'home');        
        
        $model = new App_Item();
	    $this->view->specials = $model->getBooks(array('special' => 1, 'order' => 'RAND()', 'limit' => 4));
	    
	    // set active category page
	    
	    $page = $this->view->navigation()->findOneById('nav-catalogue-' . $category['id']);
	    
	    if ($page)
	        $page->setActive(true);
	    
	}
	
	private function getCategory($id)
	{
	    $model = new App_Model_Category();
	    return $model->getById($id);
	}
	
	private function getChildren($id)
	{
	    $nav  = Zend_Registry::get('Zend_Navigation');
	    $page = $nav->findOneById('nav-catalogue' . ($id ? '-' . $id : null));
	
	    if (!$page->hasPages())
	        return array();
	     
	    $ids  = array();
	    $func = function($page) use (&$func, &$ids) {
	         
	        $ids[] = str_replace('nav-catalogue-', '', $page->id);
	         
	        if ($page->hasPages()) {
	            foreach ($page->getPages() as $_page)
	                $func($_page);
	        }
	
	    };
	     
	    foreach ($page->getPages() as $_page)
	        $func($_page);
	     
	    return $ids;
	}	
	
}