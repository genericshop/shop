<?php

class Admin_OrderController extends App_Controller_Admin
{

    public function init()
    {
    	$this->model = new App_Model_Order();
    }

    public function listAction()
    {
    	$this->view->items = $this->model->fetchAll(array('archived = ?' => 0, 'deleted = ?' => 0))->toArray();
    }
    
    public function manageAction()
    {
        $order = $this->view->order = $this->model->getById($this->getParam('id', null));
    	
    	if (!$order) {
    		$this->_helper->flashMessenger(array('error' => 'Invalid order reference'));
    		$this->_helper->redirector('index');
    	}
    	
    	$model = new App_Model_OrderItem();
    	$items = $this->view->items = $model->getByParent($order['id']);
    	
    	$model = new User_Model_User();
    	$user  = $this->view->user = $model->getById($order['user']);
    }    

    public function processAction()
    {
    	set_time_limit(0);
    	
        $json = array('result' => 0, 'message' => 'Unable to complete request');
    	$item = $this->model->getById($this->getParam('id', null));
    	
    	if (!$item)
    		goto view;

    	$status = $this->getParam('status', null);
		$data   = array();
		
		switch ($status) {
			
			case 'otd':
			    $result = $this->model->processDigitalBooks($item);
			    break;
		    
			case 'paid':
				
			    $data['status']   = 1;
				$json['callback'] = "$.get('admin/order/process', { id: '{$item['id']}', status: 'otd' }, function() { location.href = document.location; });";
				
				break;

			case 'processed':
				
			    $data['status'] = 2;
				
				if ($this->getParam('tracking', '')) 
				    $data['delivery_tracking'] = trim(strip_tags($this->getParam('tracking')));
				    
				break;				
				
			case 'archive':
				$data['archived'] = 1;
				break;				
				
		}
		
		if (true === $this->model->updateItem($data, $item['id']))
			$json['result'] = 1;
    	elseif (isset($json['callback']))
    	   unset($json['callback']);
		
    	view:
    	$this->_helper->json($json);
    	
    }
    
    public function deleteAction() 
    {
        $item = $this->model->getById($this->getParam('id', null));
        
        if ($item) 
            $this->model->update(array('deleted' => 1, 'archived' => 1), array('id = ?' => $item['id']));

        $this->_helper->redirector('index');
    }    
    
    public function downloadAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->model->pdf($this->getParam('id', null));
        exit; 
    }
    
}