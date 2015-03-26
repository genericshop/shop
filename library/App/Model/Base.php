<?php

class App_Model_Base extends Zend_Db_Table
{
	
	protected $_uri;
	
	public function truncate()
	{
		if (APPLICATION_ENV !== 'development')
		    return false;
		
	    $this->_db->query('TRUNCATE TABLE ' . $this->_name);
	}
	
	public function createItem($data, $return = false) 
	{
		try {
			$result = $this->insert($this->nullify($this->doUri($data)));
			return true === $return ? (int)$result : true;
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}

	public function updateItem($data, $id) 
	{
		try {
	        $this->update($this->nullify($this->doUri($data)), array('id = ?' => (int) $id));
			return true;
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}

	public function deleteItem($id)
	{
		try {
			$this->delete(array('id = ?' => (int) $id));
			return true;
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}

    public function getById($id)
	{
	    if ($id) {
	    
    	    $select = $this->select();
    		$select->where('id = ?', $id);
    		
    		$result = $this->fetchRow($select);
    		if ($result)
    			return $result->toArray();
    		
	    }
		
		return false;
	}	
	
	public function processForm(Zend_Form $form, $item = null, $params = array())
	{
		$values = $form->getValues();
		
		if (is_array($item) && isset($item['id']))
			return $this->updateItem($values, $item['id']);
		
		return $this->createItem($values);
	}
	
	private function nullify($data)
	{
		foreach ($data as $key => $value) {
			if (!strlen($value))
				$data[$key] = null;
		}
		return $data;
	}	
	
	private function doUri($data)
	{
	    if ($this->_uri && isset($data[$this->_uri])) {
	        $data['uri'] = $this->toUri($data[$this->_uri]);
	    }
	
	    return $data;
	}
	
	private function toUri($string)
	{
	    return App_Util::toUri($string);
	}
	
}