<?php

class CheckoutController extends App_Controller_Action
{
    
    private $model, $session, $cart;
    
    public function init()
    {
        parent::init();
        $this->session = new Zend_Session_Namespace('Cart');
        $this->model   = new App_Model_Cart();
        $this->cart    = isset($this->session->id) ? $this->model->getById($this->session->id) : null;
    }
    
    public function indexAction()
    {
        if (!$this->cart)
            return;
    
        $this->view->cart  = $this->cart;
        $this->view->items = $this->getCartItems();
    }

    public function confirmAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity() || !$this->cart)
            $this->_helper->redirector('index');       
        
        if (!$this->cart['user'])
            $this->model->updateItem(array('user' => $this->_auth->UniqueRef), $this->cart['id']);
        
        $items = $this->view->items = $this->getCartItems();

        if (!$items || empty($items))
            $this->_helper->redirector('index');
        
        $form    = new Default_Form_Payment();
        $request = $this->getRequest();

        if ($request->isPost()) {
            
            $data = $request->getPost();
            
            if ($form->isValid($data)) {
            
                $paytype = $form->getValue('paytype');
                
                if ($paytype === 'card') {
                    
                    $reference = strtoupper(uniqid(uniqid(), true));
                    $this->model->updateItem(array('reference' => $reference), $this->cart['id']);
                    
                    $this->_helper->redirector->gotoRoute(array('reference' => $reference), 'checkout-paygate');
                    
                } else {
                
                    $paytypes = $form->getPaymentOptions();
                    
                    $params = array(
                        'AccountUniqueRef' => $this->_auth->UniqueRef,
                        'PaymentMethot'    => $paytypes[$form->getValue('paytype')],
                        'OrderState'       => 'Unpaid',
                    );
                    
                    $order = $this->_api->createOrder($params);
                    
                    if (true === $order->Status) {

                        foreach ($items as $item) {
                        
                            $params = array(
                                'OrderRef'  => $order->OrderRef,
                                'Quantity'  => $item['qty'],
                                'StockID'   => $item['sid'],
				'ISBN'	    => $item['isbn'],
                            );
                        
                            // specific for bundle items
                            
                            if ($item['bundle_id']) {
                                $params['PriceList'] = $item['bundle_id'];
                                $params['StudentUniqueRef'] = $item['student_id'];
				
                            }

                            // end
                            
                            $result = $this->_api->createOrderItem($params);
                            
                            if (true !== $result->Status) {
                                
                                if ('development' === APPLICATION_ENV) {
                                    Zend_Debug::dump($params);
                                    Zend_Debug::dump($result);
                                    exit;
                                }
                                
                                // delete the order (need a function)
                                goto error;
                                
                            }
                            
                        }
                        
                        $this->session->unsetAll();
                        $this->model->deleteItem($this->cart['id']);
                        
                        $this->_helper->redirector->gotoRoute(array('reference' => $order->OrderRef), 'checkout-complete');
                        
                    } 
                    
                    error:
                    $this->_helper->flashMessenger(array('error' => $this->view->translate('An unknown error has occurred. Please contact us for further assistance.')));
                    
                }
                
                
            } else {
                
                $this->_helper->flashMessenger(array('error' => $this->view->translate('Please select a payment option before proceeding.')));
                
            }
            
        }
        
        $this->view->cart = $this->cart;
        $this->view->form = $form;
    }

    public function completeAction()
    {
        $result = $this->_api->getOrderDetails($this->getParam('reference', null));
        
        if (true === $result->Status)
            $this->view->order = $result;
    
        // Zend_Debug::dump($result);
    }
    
    private function getItemDetails($list_id, $stock_id)
    {
	// $this->_api->getPriceListDetails
        $result = $this->_api->getPriceListItemDetails($list_id,$stock_id);
        
        if (true === $result->Status)
            $this->view->orderitem = $result;
    
        // Zend_Debug::dump($result);
    }


    private function getCartItems()
    {
        $model = new App_Model_CartItem();
        return $model->getByCart($this->cart['id']);        
    }
    
}