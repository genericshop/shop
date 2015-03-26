<?php

class Admin_UserController extends App_Controller_Admin
{

    public function init()
    {
    	$this->model = new User_Model_User();
    }

    public function listAction()
    {
    	$this->view->items = $this->model->fetchAll('username != "admin"', array('surname', 'name'))->toArray();
    }
    
    public function addAction()
    {
        $item = $this->getParam('id', null) ? $this->model->getById($this->getParam('id')) : null;
    		
        $form  	 = new Admin_Form_User();
        $request = $this->getRequest();
        
        if ($item) {
        	
            $form->password->setRequired(false)->setIgnore(true)->setDescription('Leave blank if unchanged');
        	$form->passwordc->setRequired(false)->setDescription('Leave blank if unchanged');
        	
        } 
        
        if ($request->isPost()) {
            
            $data = $request->getPost();
            
            if ($item && $data['password']) {
            	
            	$form->password->setRequired(true)->setIgnore(false);
            	$form->passwordc->setRequired(true);
            	
            }
            
            if ($form->isValid($data)) {
                
                $result = $this->model->processAdmin($form, $item);
                
				if (true === $result) {
                    
					if (!$item) {
						
						$this->_helper->flashMessenger(array('success' => 'item created successfully.'));
						$this->_helper->redirector('index');
					
					} else {
                    	
						$this->_helper->flashMessenger(array('success' => 'item updated successfully.'));
                        $this->_helper->redirector('edit', null, null, array('id' => $item['id']));    
					
					}

				} else {
                    
					$this->_helper->flashMessenger(array('error' => $result));    
                
				}
                
			} 
            
        } else {
            
            if ($item)
            	$form->populate($item);
            
        }
        
    	$this->view->action = $item ? $item['surname'] . ', ' . $item['name'] : 'Create';
    	$this->view->form 	= $form;
        
        $this->renderScript('user/form.phtml');
    }    
    
    public function deleteAction()
    {}
    
}