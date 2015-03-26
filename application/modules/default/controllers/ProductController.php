<?php

class ProductController extends App_Controller_Action
{
	
    public function init()
    {
        parent::init();
        $this->view->nav = $this->getNavigation();
    }
    
    public function listAction()
    {
        $category_id     = $this->getParam('category', null);
        $category_sub_id = $this->getParam('sub_category', null);
        
        if ($category_id || $category_sub_id) {
            
            if ($category_sub_id) {
                
                $category = $this->view->category = $this->_api->getSubCategory($category_sub_id);
                
                /*
                if (true !== $category->Status) {
                    $this->_helper->flashMessenger(array('error' => 'The category you are trying to browse does not exist.'));
                    $this->_helper->redirector->gotoRoute(array(), 'product');
                }
                */
                
                $items = $this->view->items = $this->_api->getAllProductsBySubCategory($category->_ID);
                
            } else {
                
                $category = $this->view->category = $this->_api->getCategory($category_id);
                
                if (true !== $category->Status) {
                    $this->_helper->flashMessenger(array('error' => 'The category you are trying to browse does not exist.'));
                    $this->_helper->redirector->gotoRoute(array(), 'product');
                }
                
                $result = $this->_api->getAllProductsByCategory($category->_ID);
                
                if (is_array($result))
                    $items = $this->view->items = $result;
                
            }

        }
        
    }
    
    public function viewAction()
	{
        $result = $this->_api->getProduct($this->getParam('id', null)); 
        
        if (true !== $result->Status) {
            $this->_helper->flashMessenger(array('error' => 'The item you are trying to view does not exist.'));
            $this->_helper->redirector('index', 'index', 'default');
        }
            
        $this->view->product = $result;
	}
	
    private function getNavigation()
    {
        $nav   = new Zend_Navigation();
        $items = $this->getNavigationItems();
            
        $key_name = 'Name_' . $this->view->lang;
        
        foreach ($items as $item) {
        
            $page = array(
                'label'  => $item->{$key_name},
                'route'  => 'product-list',
                'params' => array('category' => $item->_ID)
            );
            
            if (isset($item->Items)) {
            
                $pages = array();
                
                foreach ($item->Items as $sub) {
                    
                    $pages[] = array(
                        'label'  => $sub->{$key_name},
                        'route'  => 'product-list',
                        'params' => array('category' => $item->_ID, 'sub_category' => $sub->_ID)
                    );
                    
                }
                
                $page['pages'] = $pages;
                
            }
            
            $nav->addPage($page);
         
        }
        
        return $nav;
    }
	
    private function getNavigationItems()
    {
        $cache      = Zend_Registry::get('Cache');
        $cache_key  = $this->_store['id'] . '_nav_product';
        
        //$categories = APPLICATION_ENV === 'production' ? $cache->load($cache_key) : null;
        $categories = null;
        
        if (!$categories) {
        
            $categories = $this->_api->getCategories();
        
//             Zend_Debug::dump($categories);
//             exit;
            
            foreach ($categories as &$category) {
        
                $sub_categories = $this->_api->getSubCategories($category->_ID);
        
                if (!is_array($sub_categories) || !count($sub_categories))
                    continue;
        
                $category->Items = $sub_categories;
        
            } unset($category);
        
            $cache->save($categories, $cache_key);
        
        }
        
        return $categories;
    }
    
}