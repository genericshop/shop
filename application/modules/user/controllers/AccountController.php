<?php

class User_AccountController extends App_Controller_Action
{
    
    public function indexAction()
    {
	
	//Zend_Debug::dump('API CALL : unique ref : '.$this->_auth->UniqueRef);
        $result = $this->_api->getAccountBalance($this->_auth->UniqueRef);
        
        if (true === $result->Status)
            $this->view->balance = $result->Balance;
	
    }
    
    public function profileAction()
    {
        $form = new User_Form_Register('parent');
        
       // foreach (array('HomePhone', 'NationalID', 'Email', 'EmailC', 'Password', 'PasswordC') as $element)

	   foreach (array('HomePhone', 'NationalID', 'Email', 'EmailC') as $element)
            $form->removeElement($element);

        $form->Password->setRequired(false);
	$form->PasswordC->setRequired(false);

        $request = $this->getRequest();
        $account = $this->_api->getAccountDetails($this->_auth->UniqueRef);
        
//         Zend_Debug::dump($account);
//         exit;
        
        if ($request->isPost()) {
            
            if ($form->isValid($request->getPost())) {
                
                $form->addElement('hidden', 'ParentUniqueRef', array('value' => $this->_auth->UniqueRef));
                $form->addElement('hidden', 'Email', array('value' => $account->Email));
                $result = $this->_api->updateParentAccount($form->getValues());
                
                if (true === $result->Status) {
                
                    $this->_helper->flashMessenger(array('success' => $result->StatusMessage));
                    $this->_helper->redirector->gotoRoute(array(), 'account-profile');
                    
                } 
                
                $this->_helper->flashMessenger(array('error' => $result->StatusMessage));
                
            }
            
        } else {
            
            $parts  = explode(' ', $account->FullName);
            
            $data = array(
                'Name'      => $parts[0],
                'Surname'   => $parts[1],
                'CellPhone' => $account->CellPhone,
            );
            
            $form->populate($data);
            
        }
        
         $form->Password->setDescription($this->view->translate('Leave blank if unchanged'));
        
        $form->submit->setLabel(_('Update Details'));
        $this->view->form = $form;
    }

    public function historyAction()
    {
        $this->view->orders = $this->_api->getParentAccountOrders($this->_auth->UniqueRef);
    }
    
    public function historyItemAction()
    {
	//Zend_Debug::dump($this->_api->getParentAccountOrderItems($this->_auth->UniqueRef, $this->getParam('order', null)));
	//Zend_Debug::dump('API CALL : unique ref : '.$this->_auth->UniqueRef  . ' Orderid : '. $this->getParam('order', null));
        $this->view->items = $this->_api->getParentAccountOrderItems($this->_auth->UniqueRef, $this->getParam('order', null));
    }

    public function historyItemPaymentAction()
    {
        $this->view->items = $this->_api->getParentAccountOrderItems($this->_auth->UniqueRef, $this->getParam('order', null));
    }

    
    public function transactionAction()
    {
        $this->view->transactions = $this->_api->getTransactions($this->_auth->UniqueRef);
    }
    
    public function bookAction()
    {

        if ('Parent' === $this->_auth->AccountType) {
            
            $result = $this->_api->getParentBooks($this->_auth->UniqueRef);
            
        } elseif ('Student' === $this->_auth->AccountType) {
            
            $result = $this->_api->getStudentBooks($this->_auth->UniqueRef);
            
        }
        
        if (is_array($result))
            $this->view->items = $result;            
        
    }

    public function getDownloadLinkAction()
    {
        $json = array('result' => 0, 'message' => 'Unable to generate download link.');
        
        $reference_o = $this->getParam('reference', null);
        $reference_i = $this->getParam('id', null);
        
        // lol above is named retarded;
        
        if (!$reference_o || !$reference_i)
            goto out;
        
        // fetch item
        
        $item = $this->_api->getAccountEbook($reference_i);
        
        /*
        if (true !== $item->Status)
            goto out;
        */
        
        // fetch order - check if paid
        
        $order = $this->_api->getOrderDetails($reference_o);
        
//         Zend_Debug::dump($order);
//         Zend_Debug::dump($item);
//         exit;
        
        if (true !== $order->Status)
            goto out;
        
        if ('Paid' !== $order->OrderState) {
            
            $json['message'] = 'Unable to generate download link. Order has not been paid for.';
            goto out;
            
        }
        
        if ($order->OrderRef !== $item->OrderRef)
            goto out;
        
        // end
        
        $iref   = $order->OrderRef . '.' . $item->Reference;
        
        $otd    = new App_OnTheDot();
        $result = $otd->createOrder($item->ISBN, $iref, $this->_auth->UniqueRef); //$this->_auth->PaymentReference
        
        if (!is_int($result)) {
            $json['message'] = $result;
            goto out;
        }
        
        $result = $otd->getDownloadLink($iref);
        
        if (!is_object($result))
            goto out;
        
        // update download link
        
        $params = array(
            'DownloadLink'  => $result->Url,
            'Reference'     => $item->Reference,
        );
        
        if ('Parent' === $this->_auth->AccountType) {
        
            $params['ParentUniqueRef'] = $this->_auth->UniqueRef;
            $this->_api->updateLinkParent($params);
        
        } elseif ('Student' === $this->_auth->AccountType) {
        
            $params['StudentUniqueRef'] = $this->_auth->UniqueRef;
            $this->_api->updateLinkStudent($params);
        
        }
        
        $json['result'] = 1;
        $json['url']    = $result->Url;
        
        out:
        $this->_helper->json($json);
    }
    
}