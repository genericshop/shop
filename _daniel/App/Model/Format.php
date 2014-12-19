<?php

class App_Model_Format extends App_Model_Base
{
	
	protected $_name = 'format';
	
	public function getIn(array $ids)
	{
		$select = $this->select()->where('id IN (?)', $ids);
		$result = $this->fetchAll($select);
		if ($result)
			return $result->toArray();
	
		return false;
	}
	
	
}