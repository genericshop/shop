<?php

class App_Util 
{

    public static function getBaseUrl()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        return (defined("SERVER_NAME") ? SERVER_NAME : $request->getScheme() . "://" . $request->getHttpHost() . $request->getBasePath() . "/");
    }
    
    public static function getPublicPath()
    {
        return APPLICATION_PATH . '/../public/';
    }    
    
	public static function toUri($string) 
	{
	    $string = strtolower($string);
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    $string = preg_replace("/[\s_]/", "-", $string);
	    return $string;
	}
    
}