<?php

class Admin_VoucherController extends App_Controller_Admin
{
    
    public function init()
    {
    	$this->model = new App_Model_Voucher();
    }
    
    public function listAction()
    {
    	$redeemed = $this->getParam('redeemed', 0);
    	
    	$select = $this->model->select();
    	$select
    		->setIntegrityCheck(false)
    		->from('voucher AS v')
    		->where('v.redeemed = ?', $redeemed);
    	
    	$result = $this->model->fetchAll($select);
    	
    	if ($result->count())
    		$this->view->items = $result->toArray();
    	
    	$this->view->type = $redeemed ? 'redeemed' : 'active';
    }
    
    public function addAction()
    {
    	$form 	 = new Admin_Form_Voucher();
    	$request = $this->getRequest();
    	
    	if ($request->isPost()) {
    		
    		$data = $request->getPost();
    		
    		if ($data['type'] == 'percent')
    			$form->amount->addValidator('LessThan', false, array('max' => 90));
    		
    		if ($form->isValid($data)) {
    			
    			$values = $form->getValues();
    			$count	= $form->getValue('count');
    			
    			if (!$values['book'])
    				unset($values['book']);
    			
    			for ($i = 1; $i <= $count; $i++) {
    				$this->model->insert($values + array('code' => $this->model->getUniqueCode()));
    			}
    			
    			$this->_helper->redirector('index');
    			
    		}
    		
    	} 
    	
    	$this->view->action = 'Create';
    	$this->view->form 	= $form;
    	
    	$this->renderScript('voucher/form.phtml');
    }
    
    public function deleteAction()
    {
    	$item = $this->model->getByCode($this->getParam('code', null));
    	
    	if ($item) {
    		
    		if (!$item['redeemed']) 
    			$this->model->delete(array('code = ?' => $item['code']));
    		else
    			$this->_helper->flashMessenger(array('error' => 'Cannot delete a redeemed voucher code'));
    		
    	}
    	
    	$this->_helper->redirector('index');
    }    
    
}