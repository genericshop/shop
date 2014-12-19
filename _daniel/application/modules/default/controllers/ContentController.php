<?php

class ContentController extends App_Controller_Action
{
    
    public function noticeBoardAction()
    {
        $result = $this->_api->getStoreNotices();
        if (true === $result->Status)
	{
            $this->view->notice = $result->Notice;
	    $this->view->noticeAFR = $result->Notice_AFR;
	}
    }

    public function buyBackAction()
    {
        $this->view->items = $this->_api->getBuyBackItems();
    }
    
    public function contactAction()
    {
    	$form 	 = new Default_Form_Contact();
    	$request = $this->getRequest();
    	 
    	if ($request->isPost()) {
    	
    		if ($form->isValid($request->getPost())) {

    		    $values = $form->getValues();
    		    
                $mail = new App_Mail();
                $mail->addTo($this->_store['Info']->Email);
                $mail->setReplyTo($values['email']);
                $mail->setSubject('Website Contact Form');
                
                $body  = '<p>' . nl2br($values['message']) . '</p>';
                $body .= '<p>' . $values['name'] . ' ' . ($values['tel'] ? '<br>' . $values['tel'] : null) . '<br>' . $values['email'] . '</p>';
                
                $mail->setBodyHtml($body);
                
                try {
                    $mail->send();
                } catch (Exception $e) {
                    $this->_helper->flashMessenger(array('error' => 'Oops, seems like there were some problems getting your message through to us. Please call us for further assistance.'));
                }
    
				$this->_helper->flashMessenger(array('success' => 'We have received your message and will contact your shortly, thank you.'));
				$this->_helper->redirector->gotoRoute(array(), 'contact');
    		    
    		}
    	
    	} else {
    	    
    	    if ($this->_auth) {
    	        
    	        $form->name->setValue($this->_auth->FullName);
    	        $form->email->setValue($this->_auth->Email);
    	        
    	    }
    	    
    	}

    	$this->view->form = $form;
    }
    
}