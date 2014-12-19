<?php

class User_ProfileController extends Zend_Controller_Action
{
    /*
     * Once the link is requested and received, then what?
     * Save the generated links in the order items table. This will make it easy to display them
     * again without having to do the requests again.
     * 
     * If the link is not in the db table, send headers to tell the browser to download what the links points to,
     * if the link is from the db, or is from the requests it will be transparent to the user. One will just take 
     * slightly longer.
     * 
     * If the link cannot be generated, display an error and tell the user to try again later.
     * 
     * Write a script to check sync books in the otd feed and whats in the db
     */
	
	/*
	 * How to retry payment?
	 * 
	 * Append P and the payment try number to the order reference,
	 * get all the required params
	 */
	
	private $auth, $model;
	
    public function init()
    {
    	$this->auth	 = Zend_Auth::getInstance()->getIdentity();
    	$this->model = new User_Model_User();
    }
    
    public function indexAction()
    {
    	$item = $this->model->getById($this->auth->id);
    	if (!$item)
    		$this->_helper->redirector('index', 'index', 'default');
    	
    	$form 	 = new User_Form_Profile();
    	$request = $this->getRequest();
    
    	if ($request->isPost()) {
    		$data = $request->getPost();
    		if ($data['password']) {
    			$form->password->setRequired(true)->setIgnore(false);
    			$form->passwordc->setRequired(true);
    		}
    		
    		if ($form->isValid($data)) {
    			$result = $this->model->processProfile($form, $item);
    			if (true === $result) {
    				$model = new User_Model_Auth();
    				$model->refresh();
    				
    				$this->_helper->flashMessenger(array('success' => 'Your details have been successfully updated.'));
    				$this->_helper->redirector('index');
    			} else {
    				$this->_helper->flashMessenger(array('error' => $result));
    			}
    		}

    	} else {
    		$form->populate($item);
    	}
    	
    	$this->view->form = $form;
    }

    public function booksAction()
    {
        $ident = Zend_Auth::getInstance()->getIdentity();
        $orderModel = new App_Model_Order();
        $orderItemModel = new App_Model_OrderItem();
        $orders = $orderModel->getByUserId($ident->id);

        $bookModel = new App_Model_Book();
        
		$displayOrders = array();
        
        foreach($orders as $k => &$order) {
        	
//         	if (!(int)$order['grand_total_digital']) {
//         		continue;
//         	}
        	
            if (!(int)$order['total_digital']) {
        		continue;
        	}
        	        	
            $order['ref'] = $orderModel->getOrderReference($order);
            $order['items'] = $orderItemModel->getByOrder($order['id']);

            foreach ($order['items'] as &$item) {
                $item['book'] = $bookModel->getByIdWithFormat($item['item_id']);
            }
			
            $displayOrders[] = $order;
            
        }
        $this->view->orders = $displayOrders;
    }

    public function retryPaymentAction()
    {
    	$session = new Zend_Session_Namespace('cart');
    	
    	$request = $this->getRequest();
    	$orderId = $request->getParam('orderId', 0);

    	$ident = Zend_Auth::getInstance()->getIdentity();
    	if ($orderId == 0) {
    		return ;
    	}
    	
    	$bookModel 		= new App_Model_Book();
    	$orderModel 	= new App_Model_Order();
    	$orderItemModel = new App_Model_OrderItem();
    	
    	$order = $orderModel->getById($orderId);
    	
    	if ($order['user_id'] != $ident->id) {
    		return ;
    	}

    	//Set this here, for the approved or declined actions
    	$session->orderId = $orderId;
    	
    	$orderItems = $orderItemModel->getByOrder($orderId);
    	
    	$orderItems = $this->attachBooks($orderItems);
    	
    	$orderModel->updateItem(array(
    		'payment_attempts' => ++$order['payment_attempts']
    	), $orderId);
    	
    	$order['reference'] = $orderModel->getOrderReference($order);
    	$order['reference'] .= 'T'.$order['payment_attempts'];
    	$this->view->order 	= $order;
    }
    
    private function attachBooks($cartItems)
    {
    	$bookModel = new App_Model_Book();
    	foreach ($cartItems as $k => &$item) {
    		$joinEbook = false;
    		if ($item['item_format'] == 'epub' || $item['item_format'] == 'pdf') {
    			$joinEbook = true;
    		}
    		 
    		$item['item'] = $bookModel->getJoinedById($item['item_id'], $joinEbook);
    	}
    
    	return $cartItems;
    }    
    
    public function booksDownloadLinkAction()
    {
    	$jsonResponse = (object)array(
    		'error' => true,
    		'url' => ''
    	);
    	
    	$request = $this->getRequest();
    	
    	$orderId = $request->getParam('orderId', false);
    	$itemId  = $request->getParam('itemId', false);

    	$ident = Zend_Auth::getInstance()->getIdentity();
    	$orderModel 	= new App_Model_Order();
    	$order = $orderModel->getById($orderId);
    	if ($order['user_id'] != $ident->id) {
    		return ;
    	}
    	
    	$conf = $this->getInvokeArg('bootstrap')->getOptions();
    	
    	$url		= $conf['onthedot']['url'];
    	$key		= $conf['onthedot']['key'];
    	$password	= $conf['onthedot']['password'];
    	
    	$orderRef = $this->createProviderOrder($orderId, $itemId, $url, $key, $password);
    	$jsonResult = $this->getProviderDownloadLink($orderRef, $url, $key, $password);
    	
    	if ($jsonResult->ErrorCode !== 0) {
    		$this->_helper->json($jsonResponse);
    		return ;
    	}
    	
    	$this->saveLinkWithItem($jsonResult->Url, $itemId);
    	$jsonResponse->url = $jsonResult->Url;
    	$jsonResponse->error = false;
    	$this->_helper->json($jsonResponse);
    }

    /*
     * Add the number from the quantity modifier parameter to the order reference.
     * This is to allow a book to have 5 download links if a quantity of 5 were paid for.
     */
    private function addQuantityToReference($orderRef)
    {
    	$request = $this->getRequest();
    	$quantity = $request->getParam('quantityModifier', 0);

    	if ($quantity != 0) {
    		return $orderRef . 'Q' . $quantity;
    	}

    	return $orderRef;
    }
    
    private function createProviderOrder($orderId, $itemId, $url, $key, $password)
    {
    	$orderModel = new App_Model_Order();
    	$orderItemModel = new App_Model_OrderItem();
    	$digitalModel = new App_Model_BookDigital();
    	 
    	$order = $orderModel->getById($orderId);
    	$orderItem = $orderItemModel->getById($itemId);
    	 
    	$ebook = $digitalModel->getByBook($orderItem['item_id']);
    	 
    	if ($orderItem['item_format'] == 'pdf') {
    		$ean = $ebook['ebook_isbn_pdf'];
    	} else if ($orderItem['item_format'] == 'epub') {
    		$ean = $ebook['ebook_isbn_epub'];
    	}
    	 
    	$orderRef = $orderModel->getOrderReference($order);
	$orderRef = $this->addQuantityToReference($orderRef);
	$orderRef .= 'I'.$itemId;
    	$customerRef = $order['user_id'];
    	 
    	$bookOrderUrl = "$url/v1/BookOrder/$key/$password/$ean/$orderRef/$customerRef";
    	
    	$jsonResult = $this->doRequest($bookOrderUrl, true);
    	
    	//Always return the order ref, we'll check the result of the next request to
    	//see if this whole process has been successfull or not
    	return $orderRef;
    }
    
    private function getProviderDownloadLink($orderRef, $url, $key, $password)
    {
    	$url = "$url/v1/BookDownloadUrlForReference/$key/$password/$orderRef";
    	$jsonResult = $this->doRequest($url);
    	
    	if ($jsonResult->ErrorCode !== 0) {
			return false;	
		}
		
		return $jsonResult;
    }
    
    private function saveLinkWithItem($url, $itemId)
    {
    	$orderItemModel = new App_Model_OrderItem();
    	$orderItemModel->updateItem(array('download_link' => $url), $itemId);
    }
    
    private function doRequest($url, $post = false)
    {
    	$headers = array("content-length: 0");
    	 
    	$ch = curl_init($url);
    	
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 30);
    	//curl_setopt($ch, CURLOPT_VERBOSE, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		if ($post) {
    		curl_setopt($ch, CURLOPT_POST, true);
		}
		
    	$res = curl_exec($ch);
    	if ($res !== false) {
    		return json_decode($res);
    	} else {
    		return false;
    	}
    	 
    }
    
}
