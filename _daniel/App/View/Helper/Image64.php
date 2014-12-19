<?php

class App_View_Helper_Image64 extends Zend_View_Helper_Abstract
{
    
    public function image64($base64, $width = null, $height = null, $params = array())
    {
    	return '<img class="img-responsive" src="data:image/jpg;base64, ' . $base64 . '">';
    }
    
}