<?php

class Admin_SearchController extends Zend_Controller_Action
{
    
    public function init()
    {
    }
    
    public function indexAction()
    {
    }
    
    public function reindexAction()
    {
        set_time_limit(0);
        
        $model = new App_Model_Book();
        $model->updateIndex(null, true);

        $model = new App_Model_Product();
        $model->updateIndex(null, true);        

        $this->_helper->flashMessenger(array('success' => 'Index reset and optimized successfully.'));
        $this->_helper->redirector('index');
    }
    
}