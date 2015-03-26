<?php

class User_LoginController extends Zend_Controller_Action
{
    
    public function init()
    {}
    
    public function indexAction()
    {
		$form 	 = new User_Form_Login();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			
			$data = $request->getPost();
			
			if ($form->isValid($data)) {
				
				$model 	= new User_Model_Auth();
				$result = $model->authenticate($form->getValues());
				
				if (true === $result) {
    				
					// check for session redirect
					
                    $this->performRedirect();
					
					// end
					
					$role = Zend_Auth::getInstance()->getIdentity()->role;
					
					if ($role === 'admin')
						$this->_helper->redirector('index', 'index', 'admin');
					
					$this->_helper->redirector->gotoRoute(array(), 'account');
					
				}
				
				$this->_helper->flashMessenger(array('error' => 'Login credentials are incorrect'));
			}
			
		} else {
			
			$form->populate(array('username' => $this->getParam('u', null)));
		
		}
		
		$this->view->form = $form;
    }

    public function acceptAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity())
            Zend_Auth::getInstance()->clearIdentity();
        
        $request  = $this->getRequest();
        $username = $request->getParam('u', null);
        $password = $request->getParam('p', null);
        
        $model  = new User_Model_Auth();
        $result = $model->authenticate(array('username' => $username, 'password' => $password));

        if (true === $result) {
            $this->performRedirect();
        }
        
        $this->_helper->redirector('index', 'index', 'default');
    }
    
    public function forgotPasswordAction()
    {
    	$form = new User_Form_Login();
    	$form->removeElement('password');
    	
    	$request = $this->getRequest();
    	
    	if ($request->isPost()) {
    		 
    		if ($form->isValid($request->getPost())) {

    			$values = $form->getValues();
    			
    			$model 	= new User_Model_User();
				$user	= $model->fetchRow(array('username = ?' => $values['username'], 'active = ?' => 1, 'verified = ?' => 1, 'role != ?' => 'admin'));
				
				if ($user) {

					$salt = $model->generateSalt();
					$pass = $model->generatePassword(10);
					
					$user->setReadOnly(false);
					$user->salt = $salt;
					$user->password = sha1($pass . $salt);
					
					if ($user->save()) {
					
						$mail = new App_Mail();
						$mail->addTo($user->email);
						$mail->setSubject('Password Reset');
						$mail->setTemplate('email-forgot-password', array('user' => $user->toArray(), 'password' => $pass));
						
						try {
						
						  $mail->send();
						  
						} catch (Exception $e) {}
						
						$this->_helper->flashMessenger(array('success' => $this->view->translate('Your password was successfully reset. Please check your email address for your new password.')));
						$this->_helper->redirector->gotoRoute(array(), 'login');
						
					}
					
				}
    		
				$message = $this->view->translate('Unable to reset password for %s Please contact us for further assistance.');
				//$values['username']
				
				$this->_helper->flashMessenger(array('error' => $message));
				
    		}
    		 
    	}
    	
    	$form->submit->setLabel('Reset Password');
    	$this->view->form = $form;
    }

    private function performRedirect()
    {
        $redir = new Zend_Session_Namespace('Redir');
        if ($redir->url) {
            
            $url = $redir->url;
            $redir->unsetAll();
            
            $this->_helper->redirector->gotoUrlAndExit($url);
            
        }
    }
    
}