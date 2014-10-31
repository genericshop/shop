<?php

class App_Model_CategoryProduct extends App_Model_BaseCategory
{
	
	protected $_name  = 'category_product';
	protected $_depth = 1;
	
	public $store_id;
	
	public function __construct($store_id = null)
	{
	    parent::__construct(null);
	    $this->store_id = $store_id;
	}
	
	public function getById($id)
	{
	    $result = $this->fetchRow(array('store = ?' => $this->store_id, 'id = ?' => $id));
	    return $result ? $result->toArray() : null;
	}
	
	public function getByUri($uri)
	{
	    $result = $this->fetchRow(array('store = ?' => $this->store_id, 'uri = ?' => $uri));
	    return $result ? $result->toArray() : null;
	}
	
}