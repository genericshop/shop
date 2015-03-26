<?php

class App_Model_BookDigital extends App_Model_Base
{
	
	protected $_name = 'book_digital';
	
	/*
	 * This function overrides the base function here because
	 * the book_digital table does not have an id column and the
	 * base version of this function expects an id column
	 */
	
	public function updateItem($data, $id)
	{
		$data = $this->doUri($data);
		try {
			$this->update($this->nullify($data), array('ebook_id = ?' => $id));
			return true;
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}

    /* this function is used by the otd feed sync script */
	
    public function getAllRows()
    {
        $result = $this->fetchAll();
        if ($result)
            return $result;

        return false;
    }

	public function getByBook($id)
	{
		$select = $this->select();
		$select->where('ebook_book = ?', $id);
	
		$result = $this->fetchRow($select);
		if ($result)
			return $result->toArray();
	
		return false;
	}	

	public function getJoined($id = null, $params = array())
	{
		$select = $this->select();
		$select
			->setIntegrityCheck(false)
			->from($this->_name . ' AS eb')
			->joinInner('book AS b', 'b.id = eb.book', array('name'))
			->joinInner('format AS f', 'f.id = eb.format', array('name AS format_name'));
		
		if ($id) {
			
			$select->where('eb.id = ?', $id);
			$result = $this->fetchRow($select);
			
		} else {
			
			$result = $this->fetchAll($select);
			
		}
		
		if ($result)
			return $result->toArray();
		
		return false;
	}

    public function processForm(Zend_Form $form, $item = null, $uploads = array())
    {
        $values = $form->getValues();

        if ($item && isset($item['ebook_book'])) {

            $result = $this->updateItem($values, $item['ebook_id']);
            
            if (true !== $result)
            	return $result;
            	
            $item_id = (int)$item['ebook_id'];
            
        } else {
            
            $item_id = $this->createItem($values, true);
        
        }

        return $item_id;
    }

}