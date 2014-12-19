<?php

class App_Model_BookCategory extends App_Model_Base
{
	protected $_name = 'book_category';

    public function getByBook($id)
    {
        $select = $this->select()->where('book = ?', $id);
        $result = $this->fetchAll($select);
        if ($result)
            return $result->toArray();

        return false;
    }

    public function getByCategory($id, $return_select = false)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select
            ->from(array('bc' => $this->_name), '')
            ->join(array('c'  => 'category'), 'bc.category = c.id', array('category_id' => 'id', 'category_uri' => 'uri'))
            ->join(array('b' => 'book'), 'bc.book = b.id', '*')
            ->group('b.id')
            ->where('b.price IS NOT NULL')
            ->where('b.active = 1');

        if (is_array($id)) {
        	
            $select->where('bc.category IN (?)', $id);
        
        } else {
        	
            $select->where('bc.category = ?', $id);
        
        }
        
        if ($return_select)
            return $select;

        $result = $this->fetchAll($select);
        return $result->count() ? $result->toArray() : false;
    }
    
    public function getEbooksByCategory($id, $return_select = false)
    {
    	$select = $this->select()->setIntegrityCheck(false);
    	$select
            ->from(array('bc' => $this->_name), '')
            ->join(array('c'  => 'category'), 'bc.category = c.id', array('category_id' => 'id', 'category_uri' => 'uri'))
            ->join(array('b'  => 'book'), 'bc.book = b.id', '*')
            ->join(array('bf' => 'book_format'), 'bf.book = b.id', '')
            ->join(array('f'  => 'format'), 'bf.format = f.id', 'GROUP_CONCAT(DISTINCT f.name) AS format')
            ->join(array('eb' => 'book_digital'), 'eb.ebook_book = b.id', '*')
            ->where('eb.ebook_active = 1')
            ->where('eb.ebook_odt_active = 1')
            ->where('b.price IS NOT NULL')
            ->group('b.id')
            ->having('format LIKE ?', '%ePub%')
            ->orHaving('format LIKE ?', '%PDF%');
    
    	if (is_array($id)) {
    		
    	    $select->where('bc.category_id IN (?)', $id);
    	
    	} else {
    		
    	    $select->where('bc.category_id = ?', $id);
    	
    	}
    	
    	if ($return_select)
    		return $select;
    
    	$result = $this->fetchAll($select);
        return $result->count() ? $result->toArray() : false;
    }    
    
    public function getCategoriesByBook($id)
    {
    	$select = $this->select()->setIntegrityCheck(false);
    	$select
            ->from(array('bc' => $this->_name), array())
            ->join(array('c' => 'category'), 'bc.category = c.id', '*')
            ->where('bc.book = ?', $id);
    
    	$result = $this->fetchAll($select);
    	return $result->count() ? $result->toArray() : false;
    }
        
}