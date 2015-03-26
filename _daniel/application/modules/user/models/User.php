<?php

class User_Model_User extends App_Model_Base
{
	
	protected $_name = 'user';
	
	public function getByUsername($username)
	{
		$select = $this->select()->where('username = ?', $username);
		$result = $this->fetchRow($select);
		if ($result)
			return $result->toArray();
		
		return false;
	}
	
	public function getByHash($hash)
	{
		$select = $this->select()->where('hash = ?', $hash);
		$result = $this->fetchRow($select);
		if ($result)
			return $result->toArray();
		
		return false;		
	}
	
	public function processRegistration(Zend_Form $form)
	{
		$values = $form->getValues();
		
		if ($this->getByUsername($values['email']))
			return 'An account for "' . $values['email'] . '" already exists.';
		
		$values['salt'] 	= $this->generateSalt();
		$values['password']	= sha1($values['password'] . $values['salt']);
		$values['hash']		= sha1($values['password'] . $values['salt']);
		$values['username'] = $values['email'];
		
		return $this->createItem($values, true);
	}
	
	public function processProfile(Zend_Form $form, $item)
	{
		$values = $form->getValues();
		
		if ($values['email'] !== $item['email'] && $item['role'] !== 'admin') {
			
			if ($this->getByUsername($values['email']))
				return 'An account for "' . $values['email'] . '" already exists.';
			
			$values['username'] = $values['email'];
			
		}
		
		if (isset($values['password'])) {
			
			$values['salt'] 	= $this->generateSalt();
			$values['password']	= sha1($values['password'] . $values['salt']);
			
		}
		
		return $this->updateItem($values, $item['id']);
	}

	public function processAdmin(Admin_Form_User $form, $item = null)
	{
		$values = $form->getValues();
		
		if (!$item || ($item && $values['email'] !== $item['email'])) {
				
			if ($this->getByUsername($values['email']))
				return 'An account for "' . $values['email'] . '" already exists.';
				
			$values['username'] = $values['email'];
				
		}
		
		if (isset($values['password'])) {
				
			$values['salt'] 	= $this->generateSalt();
			$values['password']	= sha1($values['password'] . $values['salt']);
				
		}
		
		$values['role'] = 'user';
		
		if ($item)
			return $this->updateItem($values, $item['id']);

		return $this->createItem($values);
	}
	
	public function generateSalt()
	{
		return sha1(time().uniqid(uniqid()));
	}
	
	public function generatePassword($length = 8, $params = array())
	{
		$chars 	= array_merge(range(0, 9), range('A', 'Z'), range('a', 'z'));
		$pass 	= null;
		
		for ($i = 1; $i <= $length; $i++)
			$pass .= $chars[array_rand($chars)];
		
		if (isset($params['uppercase']))
			$pass = strtoupper($pass);
		
		return $pass;
	} 
	
	public function deleteItem($id)
	{
		try {
			
			if (1 === $this->delete('id = '. (int) $id)) {
				
				// do stuff here				
				
				return true;
				
			}
			
			return 'Unable to delete';
			
		} catch (Zend_Exception $e) {
			return $e->getMessage();
		}
	}	
	
}