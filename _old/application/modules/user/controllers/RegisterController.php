<?php

class User_RegisterController extends App_Controller_Action
{
    
    public function indexAction()
    {
    	$form = new User_Form_Register();
    	
    	$form->addElement('checkbox', 'agree', array(
    	    'label'      => _('I have read and accept the terms and conditions.'),
    	    'value'      => '',
    	    'required'   => true,
    	    'ignore'     => true,
    	    'validators' => array(array(new Zend_Validate_InArray(array(1)))),
    	    'order'      => 105
    	));
    	
    	$request = $this->getRequest();
    
    	if ($request->isPost()) {
    		
    		$data = $request->getPost();
    		
    		if ($form->isValid($data)) {
    			
    		    $result = $this->_api->registerParentAccount($form->getValues());
    		    
    			if (true === $result->Status) {
    				
    				$this->_helper->redirector->gotoRoute(array(), 'register-success');
    				
    			} else {
    				
    				$this->_helper->flashMessenger(array('error' => $result->StatusMessage));    				
    				
    			}
    			
    		} else {
    		    
    		    if (!$form->getValue('agree'))
    		        $this->_helper->flashMessenger(array('error' => $this->view->translate('You must agree to the terms and conditions to continue.')));
    		    
    		}
    		
    	}
    	
    	$form->setCheckboxDecorators('agree');
    	
    	$this->view->form  = $form;
    	$this->view->terms = $this->_api->getStoreTerms(); 
    }
    
    public function studentAction()
    {
    	$form 	 = new User_Form_Register('student');
    	$request = $this->getRequest();
    
    	if ($request->isPost()) {
    		
    		$data = $request->getPost();
    		
    		if ($form->isValid($data)) {
    			
    		    $result = $this->_api->registerStudentAccount($form->getValues());
    		    
    			if (true === $result->Status) {
    				
    				$this->_helper->redirector->gotoRoute(array(), 'register-success');
    				
    			} else {
    				
    				$this->_helper->flashMessenger(array('error' => $result->StatusMessage));    				
    				
    			}
    			
            } else {
    		    
    		    if (!$form->getValue('opt_agree'))
    		        $this->_helper->flashMessenger(array('error' => _('You must agree to the terms and conditions to continue.')));
    		    
    		}
    		
    	}
    	
    	$options = array();
    	foreach ($this->_api->getAllGrades() as $grade)
    	    $options[$grade->GradeID] = $grade->Grade_ENG;
    	
    	asort($options, SORT_NATURAL);
    	$form->GradeID->setMultiOptions($options);
    	
    	$this->view->form  = $form;
    	$this->view->terms = $this->_api->getStoreTerms();
    	$this->renderScript('register/index.phtml');
    }

    public function successAction()
    {}
    
}