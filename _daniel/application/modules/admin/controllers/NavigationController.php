<?php

class Admin_NavigationController extends App_Controller_Admin
{
    
    public function init()
    {
    	$this->model = new App_Model_Navigation();
    }
    
    public function listAction()
    {
    	$where = array('parent IS NULL', 'visible = ?' => 1);
    	
    	if ($this->getParam('parent', null)) {
    		
    		$parent = $this->view->parent = $this->model->getById($this->getParam('parent'));
    		$where  = array('parent = ?' => $parent['id']);
    		
    	}
    	
		$this->view->items = $this->model->fetchAll($where, 'position')->toArray();     	
    }
    
    public function addAction()
    {
        $item    = $this->view->item = $this->getParam('id', null) ? $this->model->getById($this->getParam('id')) : null;
    	
    	$form 	 = new Admin_Form_Navigation();
    	$request = $this->getRequest();
    	
    	if ($item && !$item['editable']) {
    	    
    	    $form->removeElement('content');
    	    $form->removeElement('active');
    	    
    	}
    	
    	if ($request->isPost()) {
    		
    		if ($form->isValid($request->getPost())) {
    			
    			$result = $this->model->processForm($form, $item);
    			
    			if (true === $result) {
    				
    				App_Util::removeCache('navigation');
    				
    				if (!$item) {
                        
                    	$this->_helper->flashMessenger(array('success' => 'item created successfully.'));
                    	
                    	$values = $form->getValues();
                    	
                    	if (!$values['parent'])
							$this->_helper->redirector('index');
                    
                    	$this->_helper->redirector->gotoUrl('admin/navigation/list?parent=' . $values['parent']);
                    	
                    } else {
                        
                    	$this->_helper->flashMessenger(array('success' => 'item updated successfully.'));
                        $this->_helper->redirector('edit', null, null, array('id' => $item['id']));    
                    
                    }
    				
    			}
    			
    			$this->_helper->flashMessenger(array('error' => $result));	
    				
    		}
    		
    	} else {
    		
    		if ($item)
    			$form->populate($item);
    		elseif ($this->getParam('parent', null))
    			$form->populate(array('parent' => $this->getParam('parent')));
    		
    	}
    	
    	$this->view->action = $item ? 'Edit' : 'Create';
    	$this->view->form 	= $form;
    	
    	$this->renderScript('navigation/form.phtml');
    	
    }
    
    public function deleteAction()
    {
    	$item = $this->model->getById($this->getParam('id', 0));
    	if ($item) {
    		$result = $this->model->deleteItem($item['id']);
    		if (true === $result) {
    			App_Util::removeCache('navigation');
    			$this->_helper->flashMessenger(array('success' => 'item deleted successfully'));
    		} else {
    			$this->_helper->flashMessenger(array('error' => $result));
    		}
    	}
    	$this->_helper->redirector('index');
    }    

    public function sortAction()
    {
    	$json	= array('result' => 0);
    	$order 	= $this->getParam('ord', null);
    	$parent	= $this->getParam('parent', null);
    	 
    	if (null !== $order) {
    			
    		App_Util::removeCache('navigation');
    		
    		foreach ($order as $pos => $id)
    			$this->model->update(array('position' => $pos + 1), 'id = ' . (int)$id . (null !== $parent ? ' AND parent = ' . $parent : null));
    			
    		$json['result']	= 1;
    
    	}
    
    	$this->_helper->json($json);
    }    
    
}