<?php

class PaymentController extends App_Controller_Action
{

    public function cartAction()
    {
        $model = new App_Model_Cart();
        $cart  = $model->getByReference($this->getParam('reference'), $this->_auth->UniqueRef);
        
        if (!$cart) {
            $this->_helper->flashMessenger(array('error' => 'Unable to continue to payment - invalid order reference.'));
            $this->_helper->redirector('confirm', 'checkout');
        }
        
        $form    = new Zend_Form();
    	$config  = Zend_Registry::get('config');
	//$newTotal = $this->_helper->currency($model->getTotal($cart['id']));
        $data = array(
        	'PAYGATE_ID' 	   => $config['paygate']['id'],
        	'REFERENCE'	       => $cart['reference'],
        	'AMOUNT'	       => $model->getTotal($cart['id']) ,
			'CURRENCY'	       => 'ZAR',
        	'RETURN_URL'	   => App_Util::getBaseUrl() . 'payment/response?x=order',
			'TRANSACTION_DATE' => date('Y-m-d H:i:s'),
			'EMAIL'		       => $this->_auth->Email,
        );
        
        $checksum = array();
        
        foreach ($data as $key => $value) {
        	$form->addElement('hidden', $key, array('value' => $value));
		//$form->addElement('text', $key, array('value' => $value));
        	$checksum[] = $value;
        }
        
        $checksum[] = $config['paygate']['key'];
        $signature  = md5(implode('|', $checksum));
        $form->addElement('hidden', 'paygate', array('value' => $config['paygate']['key'])); 
        $form
        	->addElement('hidden', 'CHECKSUM', array('value' => $signature))
        	->setAttrib('id', 'form-payment')
        	->setMethod('post')
        	->setAction($config['paygate']['url'])
        	->setElementDecorators(array('ViewHelper'));
        
        $this->view->form = $form;
        $this->renderScript('payment/redirect.phtml');
    }
    
    public function accountAction()
    {
        $result = $this->_api->getAccountBalance($this->_auth->UniqueRef);
        
        if (true === $result->Status)
            $balance = $result->Balance;
        
        if (!$result->Status || $balance == 0) {
            $this->_helper->redirector->gotoRoute(array(), 'account');
            exit;
        }
        
        $form    = new Zend_Form();
    	$config  = Zend_Registry::get('config');
    	
        $data = array(
        	'PAYGATE_ID' 	   => $config['paygate']['id'],
        	'REFERENCE'	       => $this->_auth->PaymentReference . '|' . date('YmdHis'),
        	'AMOUNT'	       => -$balance ,
			'CURRENCY'	       => 'ZAR',
        	'RETURN_URL'	   => App_Util::getBaseUrl() . 'payment/response?x=account',
			'TRANSACTION_DATE' => date('Y-m-d H:i:s'),
			'EMAIL'		       => $this->_auth->Email,
        );
        
        $checksum = array();
        
        foreach ($data as $key => $value) {
		if($key === 'AMOUNT')
		{
        	$form->addElement('hidden',$key,array('value' => $value));
        	$checksum[] = $value;
		}
		else
		{
		$form->addElement('hidden', $key, array('value' => $value));
        	$checksum[] = $value;
		}
        }
        
        $checksum[] = $config['paygate']['key'];	
        $form->addElement('hidden', 'paygate', array('value' => $config['paygate']['key'])); 

        $signature  = md5(implode('|', $checksum));
       // $form ->addElement('hidden', 'CheckArray', array('value' => $checksum[]));
        $form
        	->addElement('hidden', 'CHECKSUM', array('value' => $signature))
        	->setAttrib('id', 'form-payment')
        	->setMethod('post')
        	->setAction($config['paygate']['url'])
        	->setElementDecorators(array('ViewHelper'));
        
        $this->view->form = $form;
        $this->renderScript('payment/redirect.phtml');
    }
    
    public function responseAction()
    {
        $request = $this->getRequest();
        
        if (!$request->isPost())
            $this->_helper->redirector('index', 'index', 'default');
        
        $type = $this->getParam('x', 'order');
        $data = $check = $request->getPost();
        
        unset($check['CHECKSUM']);

        $checksum = md5(implode('|', $check + array(Zend_Registry::get('config')['paygate']['key'])));
        
        if ($data['CHECKSUM'] !== $checksum)
            $this->_helper->redirector('index', 'index', 'default');
        
        $model_p    = new App_Model_PayGate();
        $paygate_id = $model_p->createItem($data, true);
        
        if ((int)$data['TRANSACTION_STATUS'] === 1) {
            
            $params_pg = array(
                'PayGateRef'      => $data['PAYGATE_ID'],
                'Amount'          => $data['Amount'] ,
                'ParentUniqueRef' => $this->_auth->UniqueRef // what if this is a student account?
            );
            
            if ($type === 'order') {
            
                $model_c = new App_Model_Cart();
                $cart    = $model_c->getByReference($data['REFERENCE']);
                
                if ($cart) {
                    
                    $params = array(
                        'AccountUniqueRef' => $cart['user'],
                        'PaymentMethot'    => 'Credit Card',
                        'OrderState'       => 'Paid',
                    );
                    
                    $order = $this->_api->createOrder($params);
                    
                    if (true === $order->Status) {
                    
                        $items = $model_c->getItems($cart['id']);
                        
                        foreach ($items as $item) {
                    
                            $params = array(
                                'OrderRef'  => $order->OrderRef,
                                'Quantity'  => $item['qty'],
                            );
                            
                            if ('stock' === $item['type']) {
                            
                                $params['StockID'] = $item['sid'];
                            
                            } elseif ('book' === $item['type']) {
                            
                                $params['ISBN'] = $item['sid'];
                            
                            }
                            
                            // specific for bundle items
                            
                            if ($item['bundle_id']) {
                                $params['PriceList'] = $item['bundle_id'];
                                $params['StudentUniqueRef'] = $item['student_id'];
                            }
				$params['Total'] = $item['total'] ;
				 $params['GradeID'] = $item['GradeID'];

                            
                            // end
                            
                            $result = $this->_api->createOrderItem($params);
                    
                            if (true !== $result->Status) {
                                // delete the order (need a function)
                                goto error;
                            }
                    
                    
                        }
                    
                        $model_p->updateItem(array('USER' => $cart['user'], 'ORDER_REF' => $order->OrderRef), $paygate_id);
                        $model_c->deleteItem($cart['id']);
                        
                        $session = new Zend_Session_Namespace('Cart');
                        $session->unsetAll();
                        
                        $params_pg['OrderRef'] = $order->OrderRef;
                        $this->createApiPaygateReference($params_pg);
                        
                        $this->_helper->redirector->gotoRoute(array('reference' => $order->OrderRef), 'checkout-complete');
                    
                    }
                    
                    error:
                    $this->_helper->flashMessenger(array('error' => $this->view->translate('An unknown error has occurred. Please contact us for further assistance' . $data['RESULT_DESC'])));
                    
                }
                
            } elseif ($type === 'account') {
                
                $this->createApiPaygateReference($params_pg);
                $this->_helper->flashMessenger(array('success' => $this->view->translate('Your online payment has been processed successfully. Please allow 72 hours for the payment to reflect on your account.')));
                
            }
            
        } else {
            
            $this->_helper->flashMessenger(array('error' => $this->view->translate('Payment failed - ' . $data['RESULT_DESC'])));
            
        }
        
        if ($type === 'account')
            $this->_helper->redirector->gotoRoute(array(), 'account');
        
        $this->_helper->redirector('confirm', 'checkout');
    }
    
    public function completeAction()
    {
        // note should probably get the paygate payment reference from the url and check those results.
        // $this->view->result = $this->getParam('result');
    }
    
    private function createApiPaygateReference($params)
    {
        $this->_api->createPaygatePayment($params);
    }
    
}