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
            $url = "http://41.185.31.134:81/requests.aspx?verb=getpaymentcodeforcompanycode&param1=Q8LSW2N1&param2=" . $this->getCartTotal();
            $client = new Zend_Http_Client($url, array('timeout' => 300));
            $token_info = $client->request()->getBody();

            $token = split("\|", $token_info);
            $token = split("\*", $token[1]);
            $token = $token[1];
            $this->_helper->redirector->gotourl("http://wallet.cloudbanc.co.za/code/" . $token);
            exit();
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
        Zend_Debug::dump($results);
    }
    
    private function getCartItems()
    {
        $model = new App_Model_CartItem();
        $form = new Default_Form_Payment();
        $this->view->form = $form;
        return $model->getByCart($this->cart['id']);
    }
    
    private function getCartTotal()
    {
        $items = $this->getCartItems();
        
        $total = 0;
        foreach ($items as $item) {
            $total += $item["total"];
        }

        return $total;
    }
}
