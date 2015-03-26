<?php

class User_RegisterController extends Zend_Controller_Action
{
    
    public function init()
    {}
    
    public function indexAction()
    {
    	$form 	 = new User_Form_User();
    	$request = $this->getRequest();
    
    	if ($request->isPost()) {
    		
    		$data = $request->getPost();
    		
    		if ($form->isValid($data)) {
    			
    			$model 	= new User_Model_User();
    			$result = $model->processRegistration($form);
    			
    			if (is_int($result)) {
    				
					$user = $model->getById($result);    				
					
					$mail = new App_Mail();
					$mail->setSubject('Registration Confirmation');
					$mail->setTemplate('email-register-validate', array('user' => $user));
					$mail->addTo($user['email']);
					
					try {
    				    $mail->send();
					} catch (Exception $e) { }
					
    				$this->_helper->redirector->gotoRoute(array(), 'register-success');
    				
    			} else {
    				
    				$this->_helper->flashMessenger(array('error' => $result));    				
    				
    			}
    			
    		}
    		
    	}
    	
    	$this->view->form = $form;
    }

    public function successAction()
    {}
    
    public function validateAction()
    {
    	$hash = $this->getParam('h', null);

    	if (!$hash)
    		$this->_helper->redirector('index', 'index', 'default');
    	
    	$model 	= new User_Model_User();
    	$user	= $model->getByHash($hash);
    	
    	if (!$user)
    		$this->_helper->redirector('index', 'index', 'default');

    	$model->updateItem(array('role' => 'user', 'verified' => 1, 'active' => 1), $user['id']);

    	$this->_helper->flashMessenger(array('success' => 'Your account has been verified and activated. You may now login with the credentials you provided during registration.'));
    	$this->_helper->redirector->gotoRoute(array(), 'login') . 'u=' . $user['email'];
    }    
    
}