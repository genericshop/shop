<?php

class CartController extends App_Controller_Action
{

    private $model, $session;
    
	public function init()
    {
        parent::init();
        
        $this->model   = new App_Model_Cart();
        $this->session = new Zend_Session_Namespace('Cart');
    }

    public function addAction()
    {
        $json = array('result' => 0);
        
        $cart  = $this->getCart();
        $model = new App_Model_CartItem();
        
        $items = json_decode(file_get_contents('php://input'));
        $count = 0;
        
        foreach ($items as $item) {
            
            $item = $this->getApiItem($item);
            
            if (!$item)
                continue;

            // check if item already exists in cart...
            
            $params = array('sid' => $item->sid);
            
            if (isset($item->student_id))
                $params['student_id'] = $item->student_id;
            
            $item_cart = $model->getByCart($cart['id'], $params);
            
            // end
            
            if ($item_cart) {
                
                $total  = $item->price * $item->qty;
                $result = $model->updateItem(array('qty' => $item->qty, 'total' => $total,), $item_cart['id']);
            
            } else {
                
                $item->cart  = $cart['id'];
                $item->total = $item->price * $item->qty;
                
                $result = $model->createItem((array)$item);
                
            }
            
            $count++;
            
        }
        
        $json['result']  = 1;
        $json['count']   = $count;
        $json['message'] = sprintf($this->view->translate('%s item(s) added to cart.'), $count);
        
        $this->_helper->json($json);
    }

    public function updateAction()
    {
        
        $json = array('result' => 0);
        
        $cart  = $this->getCart();
        $model = new App_Model_CartItem();
        $item  = $model->getByCart($cart['id'], array('id' => $this->getParam('id', 0)));
        
        if ($item) {

            $qty   = (int) $this->getParam('qty', 1);
            $total = $item['price'] * $qty ;
            
            $model->updateItem(array('qty' => $qty, 'total' => $total), $item['id']);
            
            $json['result'] = 1;
            $json['total']  = $total;
        }
        
        $this->_helper->json($json);
    }
    
    public function removeAction()
    {
        $json = array('result' => 1);
        $cart = $this->getCart();
        
        $model = new App_Model_CartItem();
        $model->delete(array('cart = ?' => $cart['id'], 'id = ?' => $this->getParam('id', 0)));
        if($this->getParam('redirect', 0)){
            $this->_helper->redirector->gotourl("/checkout");
        }
        
        $this->_helper->json($json);
    }
    
    private function getCart($create = true)
    {
        if (isset($this->session->id)) {
            $cart = $this->model->getById($this->session->id);
            if ($cart)
                return $cart;
        }
        
        if (true === $create) {
            
            $params = array();
            
            if ($this->_auth)
                $params['user'] = $this->_auth->UniqueRef;
            
            $id = $this->session->id = $this->model->createItem($params, true);
            return $this->model->getById($id);
        }
        return false;
    }
	
    private function getApiItem($item)
    {
        if ('stock' === $item->type) {
        
            if (isset($item->bundle_id))
                $result = $this->_api->getPriceListItemDetails($item->bundle_id, $item->sid);
            else
                $result = $this->_api->getProduct($item->sid);
        
            if (true !== $result->Status) {
                //Zend_Debug::dump($result);
                return null;
            }
          //  Zend_Debug::dump($result);
            $item->name    = $result->Name_ENG;
            $item->name_af = $result->Name_AFR;
            $item->price   = $result->Price;
        
        } elseif ('book' === $item->type) {
        
            $result = $this->_api->getBook($item->sid);
            
            if (true !== $result->Status) {
                //Zend_Debug::dump($result);
                return null;
            }
            
            $item->name  = $item->name_af = $result->Name;
            $item->price = $result->Price;
	   // $item->grade_id = $result->Gradeid;
        
        } else {
            
            return null;
            
        }
        
        if (isset($item->bundle_id)) {
        
            // student reference is needed for bundles
            
            if ($this->_auth->AccountType === 'Parent') {
            
                if (!isset($item->student_id) || !$item->student_id)
                    return null;
            
            } elseif ($this->_auth->AccountType === 'Student') {
            
                $item->student_id = $this->_auth->UniqueRef;
            
            }
		
            
        }
        
        return $item;
            
    }
    
}