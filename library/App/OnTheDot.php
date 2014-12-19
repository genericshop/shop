<?php

class App_OnTheDot
{

    private $url, $user, $pass;
    
    public function __construct()
    {
        $config = Zend_Registry::get('config');
        
        $this->url  = $config['otd']['url'];
        $this->user = $config['otd']['user'];
        $this->pass = $config['otd']['pass'];
    }
    
    public function createOrder($ean, $ref, $customer)
    {
        $url    = $this->url . '/BookOrder/' . $this->user . '/' . $this->pass . '/' . $ean . '/' . $ref . '/' . $customer;
        $result = $this->doRequest($url, true);

        if (false !== $result) {
        
            if ($result->ErrorCode !== 0) {
                return $result->ErrorMessage;
            }
            
            return $result->OrderId;
            
        }
        
        return $result;
    }
    
    public function getDownloadLink($ref)
    {
    	$url    = $this->url . '/BookDownloadUrlForReference/' . $this->user . '/' . $this->pass . '/' . $ref;
    	$result = $this->doRequest($url);
    	
    	if (false !== $result) {
    	
        	if ($result->ErrorCode !== 0) {
    			return $result->ErrorMessage;	
    		}
		
    	}
		
		return $result;
    }
    
    private function doRequest($url, $post = false)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-length: 0'));
    
        if ($post)
            curl_setopt($ch, CURLOPT_POST, true);
    
        $result = curl_exec($ch);
        curl_close($ch);
        
        if ($result !== false)
            return json_decode($result);

        return false;
    }

}