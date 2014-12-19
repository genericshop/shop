<?php

class IndexController extends App_Controller_Action
{

    public function indexAction()
    {   
		$this->view->terms = $this->_api->getStoreTerms();
	//$model  = new App_Model_Banner();
       // $result = $model->fetchAll(array('active = ?' => 1));
        
       // if ($result->count())
   //         $this->view->banners = $result->toArray();            
        
//        $model = new App_Item();
        
//        $this->view->featured = $model->getForParams(array('featured' => 1, 'limit_union' => 8, 'order_union' => 'RAND()'));
//        $this->view->specials = $model->getForParams(array('special'  => 1, 'limit_union' => 8, 'order_union' => 'RAND()'));	 

    }

}