<?php

class App_Model_BookFormat extends App_Model_Base
{
	protected $_name = 'book_format';

    public function getByBook($id)
    {
        $select = $this->select()->where('book = ?', $id);
        $result = $this->fetchAll($select);
        return $result->count() ? $result->toArray() : false;
    }
    
    public function getFormatsByBook($id)
    {
    	$select = $this->select()->setIntegrityCheck(false);
    	$select
            ->from(array('bf' => $this->_name), '')
            ->join(array('f' => 'format'), 'bf.format = f.id', '*')
            ->where('bf.book = ?', $id);
    
    	$result = $this->fetchAll($select);
    	return $result->count() ? $result->toArray() : false;
    }    
    
}