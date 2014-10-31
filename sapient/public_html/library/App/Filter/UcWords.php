<?php

class App_Filter_UcWords implements Zend_Filter_Interface
{
    
	public function filter($value)
    {
        $value = ucwords(strtolower($value));
        return $value;
    }
    
}