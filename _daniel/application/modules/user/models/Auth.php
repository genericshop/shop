<?php

class User_Model_Auth
{
    
	private $omit = array('password', 'salt', 'address_line_1', 'address_line_2', 'address_line_3', 'address_city', 'address_postcode');
	
    protected function _getAdapter()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $adapter_auth = new Zend_Auth_Adapter_DbTable($adapter);
        
        $adapter_auth
        	->setTableName('user')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('SHA1(CONCAT(?,salt))');
            
        return $adapter_auth;
    }
    
    protected function _process($values)
    {
		$adapter = $this->_getAdapter();
        $adapter->setIdentity($values['username']);
        $adapter->setCredential($values['password']);
        
		$auth   = Zend_Auth::getInstance();
		$result = $auth->authenticate($adapter);
        
        if ($result->isValid()) {
            
			$user = $adapter->getResultRowObject(null, $this->omit);
        	
        	if ((int)$user->active === 0)
        		return false;
        	
           	$auth->getStorage()->write($user);
           	return true;
        	
        }
        
        return false;
    }
    
    public function authenticate($values)
    {
        return $this->_process($values);
    }
    
    public function refresh()
    {
		
    	$auth = Zend_Auth::getInstance();
    	
    	if ($auth->hasIdentity()) {    	
    	
			$model 	= new User_Model_User();
			$user 	= $model->getByUsername($auth->getIdentity()->username);
			
			if ($user) {
				
				foreach ($user as $key => $value) {
					if (in_array($key, $this->omit))
						unset($user[$key]);
				}
				
				$auth->clearIdentity();
				$auth->getStorage()->write((object)$user);
				
			}

		}
		
		return;
    }
    
}