<?php

class App_Model_BookAuthor extends App_Model_Base
{
	protected $_name = 'book_author';

    public function getByBook($id)
    {
        $select = $this->select()->where('book = ?', $id);
        $result = $this->fetchAll($select);
        if ($result)
            return $result->toArray();

        return false;
    }

    public function getAuthorsByBook($bookId)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from(array('ba' => $this->_name), '')
               ->join(array('a' => 'author'), 'ba.author = a.id', '*')
               ->where('ba.book = ?', $bookId);

        $result = $this->fetchAll($select);
        if ($result)
            return $result->toArray();

        return false;
    }
}