<?php

class App_Model_Store extends App_Model_Base
{
	
	protected $_name = 'store';

    public function getByHost($host)
    {
        $result = $this->fetchRow(array('host = ?' => $host));
        return $result ? $result->toArray() : null;
    }
    
}