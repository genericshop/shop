<?php

class User_LoginController extends App_Controller_Action
{
    
    public function indexAction()
    {
		$form 	 = new User_Form_Login();
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			
			$data = $request->getPost();
			
			if ($form->isValid($data)) {
				
			    $result = $this->_api->login($form->getValues());

			    if (true === $result->Status) {
			        
			        $user = $this->_api->getAccountDetails($result->UniqueRef);
			        $user->UniqueRef = $result->UniqueRef;
			        
			        unset($user->Status);
			        
			        // need to set roles here
			        
			        $auth = Zend_Auth::getInstance();
			        
			        if ($auth->hasIdentity())
			            $auth->clearIdentity();
			        
			        $auth->getStorage()->write($user);
			        
			        // set account type session
			        
			        /*
			        
			        if ($user->AccountType === 'Parent') {
			        }
			        
			        */
			        
			        // end
			        
			        // try fetch previous cart
			        
			        $cartns = new Zend_Session_Namespace('Cart');
			        
			        if (!$cartns->id) {
			        
                        $model  = new App_Model_Cart();
                        $cart   = $model->getLatestByUser($user->UniqueRef);
                        
                        if ($cart)
                            $cartns->id = $cart['id'];
                        
			        }
			        
			        // end
			        
			        $this->performRedirect(); // session redirects
			        $this->_helper->redirector->gotoUrl("/");
			        
			    } else {
			        
			        $this->_helper->flashMessenger(array('error' => 'Login credentials are incorrect'));
			        
			    }
			    
			}
			
		} else {
			
			$form->populate(array('username' => $this->getParam('u', null)));
		
		}
		
		$this->view->form = $form;
    }

    public function acceptAction()
    {
        die('Recode');
    }
    
    public function forgotPasswordAction()
    {
    	$form = new User_Form_Login();
    	$form->removeElement('Password');
    	
    	$request = $this->getRequest();
    	
    	if ($request->isPost()) {
    		 
    		if ($form->isValid($request->getPost())) {

    		    $result = $this->_api->forgotPassword($form->getValues());
    			
    		    if (true === $result->Status) {
    		        
    		        $this->_helper->flashMessenger(array('success' => $this->view->translate('Your password was successfully reset. Please check your email account for your new password.')));
                    $this->_helper->redirector->gotoRoute(array(), 'login');
    		        
    		    } else {
    		    
				    $this->_helper->flashMessenger(array('error' => $this->view->translate('We were unable to reset your password. Please contact us for further assistance.')));
				
    		    }
				
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