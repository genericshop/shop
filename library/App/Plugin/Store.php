<?php

class App_Plugin_Store extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $store = App_Session::getInstance()->get('Store');
        
        if (!$store) {
            $host = $_SERVER['HTTP_HOST'];
            $model = new App_Model_Store();
            $store = $model->getByHost($host);
            
            if (!$store){
                echo "Could not get store";
                die('Host: ' . $host . ' has no configuration data.');
            }
            
            $api  = new App_Service_Rest($store['sid']);
            $info = $api->getStoreDetails();
            
            if (true !== $info->Status){
                echo "Could not get store Status";
                die('Store: ' . $host . ' has no configuration data.');
    		}
            unset($info->Status);
                
            App_Session::getInstance()->set('Store', array(
                'id'      => $store['id'],
                'theme'   => $store['theme'],
                'logo'    => $store['logo'],
                'Info'    => $info,
                'Links'   => $api->getStoreLinks(),
                'StoreID' => $store['sid']
            ));    
        }
    }
    
}