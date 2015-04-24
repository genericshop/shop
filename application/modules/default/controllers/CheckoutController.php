<?php
require_once APPLICATION_PATH . '/../library/QrCode/QrCode.php';

class CheckoutController extends App_Controller_Action
{
    
    private $model, $session, $cart;
    
    public function init()
    {
        parent::init();
        $this->session = new Zend_Session_Namespace('Cart');
        $this->model   = new App_Model_Cart();
        $this->cart    = isset($this->session->id) ? $this->model->getById($this->session->id) : null;

        $this->completeAction();
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

        $cb_token = null;
        if (empty($this->cart['reference_payment'])) {
            $cb_token = $this->getPaymentToken();
            $this->model->updateItem(array('reference_payment' => $cb_token), $this->cart['id']);
        } else {
            $cb_token = $this->cart['reference_payment'];
        }
        
        $this->view->cb_token = $cb_token;

        if ($request->isPost()) {
            $data = $request->getPost();
            
            $this->_helper->redirector->gotourl("http://wallet.cloudbanc.co.za/code/" . $token);
            exit();
        }
        
        $this->view->cart = $this->cart;
        $this->view->form = $form;
    }

    public function getPaymentToken(){
        $url = "http://41.185.31.134:81/requests.aspx?verb=getpaymentcodeforcompanycode&param1=1WY5PJSJ&param2=" . $this->getCartTotal();
        $client = new Zend_Http_Client($url, array('timeout' => 300));
        $token_info = $client->request()->getBody();

        $token = split("\|", $token_info);
        $token = split("\*", $token[1]);
        $token = $token[1];

        return $token;
    }

    public function checkToken(){
        if(!empty($this->cart['reference_payment'])){
            $url = "http://41.185.31.134:81/requests.aspx?verb=checkcode&param1=" . $this->cart['reference_payment'];
            $client = new Zend_Http_Client($url, array('timeout' => 300));

            $token_info = $client->request()->getBody();

            $token = split("\|", $token_info);
            $token = split("\*", $token[1]);
            $token = $token[1];

            if ($token == "Paid") {
                return true;
            }
        }
        return false;
    }

    public function completeAction()
    {
        if(!$this->checkToken()){
            return;
        }

        $items = $this->getCartItems();
        $orderRef = $this->createOrder();

        foreach ($items as $item) {
            $this->createOrderItem($item, $orderRef);
        }

        // Empty cart
        $this->model->deleteItem($this->cart['id']);
    }
    
    private function createOrder(){
        $params = array(
            'AccountUniqueRef' => $this->_auth->UniqueRef,
            'PaymentMethot'    => "cloudbanc",
            'OrderState'       => 'Paid',
        );

        $order = $this->_api->createOrder($params);
        if (true === $order->Status) {
            return $order->OrderRef;
        }

        return null;
    }

    private function createOrderItem($item, $orderRef){
        $params = array(
            'OrderRef'  => $orderRef,
            'Quantity'  => $item['qty'],
        );

        if ('stock' === $item['type']) {
            $params['StockID'] = $item['sid'];
        } elseif ('book' === $item['type']) {
            $params['ISBN'] = $item['sid'];
        }

        $params['Total'] = $item['total'];

        $result = $this->_api->createOrderItem($params);
        return $result->Status;
    }

    private function getCartItems()
    {
        $model = new App_Model_CartItem();
        $form = new Default_Form_Payment();
        $this->view->form = $form;
        if(empty($model->getByCart($this->cart['id']))){
            return array();
        }

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
