<?php

class App_Model_CartItem extends App_Model_Base
{
	
	protected $_name = 'cart_item';

	public function getByCart($cart, array $params = array())
	{
        $select = $this->select();
        $select
            ->from($this->_name, '*')
            ->where('cart = ?', $cart)
            ->order(array('student_id', 'name'));
        
        if (isset($params['id']) || isset($params['sid'])) {
            
            $return_one = true;
            
            if (isset($params['id']))
                $select->where('id = ?', $params['id']);
            else 
                $select->where('sid = ?', $params['sid']);
            
        }
        
        if (isset($params['student_id']))
            $select->where('student_id = ?', $params['student_id']);
        
        if (isset($params['type']))
            $select->where('type = ?', $params['type']);
        
	    $result = $this->fetchAll($select);
	    
	    if ($result->count()) {
            $result = $result->toArray();
            return isset($return_one) ? $result : $result;
        	//return $result;
	    }
	    return null;
	}
	
	public function deleteItem($id, $cart)
	{
		try {
			$this->delete(array('id = ?' => $id, 'cart = ?' => $cart));
			return true;
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}
	
}