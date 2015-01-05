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

        $sortedItems = array();
        foreach ($this->view->items as $cartitem) {
            $sortedItems[$cartitem["student_name"]][] = $cartitem;
        }

        $this->view->sortedItems = $sortedItems;
    }

    public function confirmAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity() || !$this->cart)
            $this->_helper->redirector('index');       
        
        if (!$this->cart['user'])
            $this->model->updateItem(array('user' => $this->_auth->UniqueRef), $this->cart['id']);
        
        $i = $this->view->items = $this->getCartItems();

        if (!$i || empty($i))
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
                    
                    $sortedItems = array();
                    foreach ($i as $cartitem) {
                        $sortedItems[$cartitem["student_name"]][] = $cartitem;
                    }
                    //Zend_Debug::dump($sortedItems);
                    //exit();
                    $orders = "";
                    foreach ($sortedItems as $items) {
                        $params = array(
                            'AccountUniqueRef' => $this->_auth->UniqueRef,
                            'PaymentMethot'    => $paytypes[$form->getValue('paytype')],
                            'OrderState'       => 'Unpaid',
                        );

                        $order = $this->_api->createOrder($params);

                        if (true === $order->Status) {
                            $orders .= $order->OrderRef . "|";
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
                                $params['Total'] = $item['total'];
                                $params['GradeID'] = $item['gradeid'] ;

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
                                    $this->_helper->flashMessenger(array('error' => $this->view->translate('An unknown error has occurred. Please contact us for further assistance.')));
                                }
                            }
                        } else {
                            if ('development' === APPLICATION_ENV) {
                                Zend_Debug::dump($params);
                                Zend_Debug::dump($order);
                                exit();
                            }
                        }
                    }

                    //exit;
                    $this->session->unsetAll();
                    $this->model->deleteItem($this->cart['id']);
                    $this->_helper->redirector->gotoRoute(array('reference' => $orders), 'checkout-complete');
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
        $orders = $this->getParam('reference', null);
        $orders = split("\|", $orders);

        $results = array();
        foreach ($orders as $OrderRef) {
            if(empty($OrderRef)) {
                continue;
            }
            $result = $this->_api->getOrderDetails($OrderRef);
            if (true === $result->Status)
                $results[] = $result;
        }
        
        if (!empty($results)) {
            $result = $results[0];
            $sum = 0;
            foreach ($results as $res) {
                $sum += $res->_TotalAmount;
            }

            $result->_TotalAmount = $sum;
            $this->view->order = $result;
        }
        //if (true === $result->Status)
            //$this->view->order = $result;
        // Zend_Debug::dump($result);
    }
    
    private function getCartItems()
    {
        $model = new App_Model_CartItem();
        $form = new Default_Form_Payment();
        $this->view->form = $form;
        return $model->getByCart($this->cart['id']);
    }
    
}
