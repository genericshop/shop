<?php

class App_Model_Cart extends App_Model_Base
{
	
	protected $_name = 'cart';

	public function getTotal($id)
	{
	    $model = new App_Model_CartItem();
	    $items = $model->getByCart($id);
	    $total = 0;
	    
	    if ($items) {
	    	
	        foreach ($items as $item) {
	            $total += ($item['qty'] * $item['price']);
	        }
	        
	    }

	    return $total;
	}
	
	public function getItems($id, $params = array())
	{
	    $model = new App_Model_CartItem();
	    return $model->getByCart($id, $params);
	}
	
	public function getByReference($reference, $user = null)
	{
	    $where = array('reference = ?' => $reference);
	    if ($user)
	        $where['user = ?'] = $user;
	    
	    $result = $this->fetchRow($where);
	    return $result ? $result->toArray() : null;
	}
	
	public function getLatestByUser($user)
	{
        $result = $this->fetchAll(array('user = ?' => $user), 'created DESC');
        return $result->count() ? $result->toArray()[0] : null;
	}
	
	public function deleteItem($id)
	{
        if (true === parent::deleteItem($id)) {
            $model = new App_Model_CartItem();
            $model->delete(array('cart = ?' => $id));
        }
        
        return true;
	}
	
}