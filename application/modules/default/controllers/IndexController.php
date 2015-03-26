<?php

class IndexController extends App_Controller_Action
{
    
    public function indexAction()
    {
        $this->view->specials = $this->_api->getAllSpecials();
        $this->view->terms = $this->_api->getStoreTerms();

        $categories = $this->_api->getCategories();
        if (count($categories) > 0) {
            $category_id = $categories[0]->_ID;
        }

        $categories = $this->_api->getCategories();
		$category = $this->view->category = $this->_api->getCategory($category_id);

		$result = $this->_api->getAllProductsByCategory($category->_ID);
            if (is_array($result))
                $items = $this->view->items = $result;
    }

}