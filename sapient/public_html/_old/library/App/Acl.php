<?php

class App_Acl extends Zend_Acl
{
	
	public function __construct()
	{
		$this->addRole(new Zend_Acl_Role('Guest'));
		$this->addRole(new Zend_Acl_Role('User'), 'Guest');
		$this->addRole(new Zend_Acl_Role('Student'), 'User');
		$this->addRole(new Zend_Acl_Role('Parent'),  'Student');
		$this->addRole(new Zend_Acl_Role('Teacher'), 'Student');
		
		$this->addResource('default');
		$this->addResource('default:bundle');
		
		$this->addResource('user');
		$this->addResource('user:account');
		$this->addResource('user:account:children');
		$this->addResource('user:student');
		$this->addResource('user:login');
		$this->addResource('user:logout');
		$this->addResource('user:register');
		
		$this->allow('Guest', array('default', 'user:register', 'user:login', 'user:logout'));
		$this->allow('User',  array('default:bundle', 'user:account'));
		
		$this->allow('Parent', array('user:account:children', 'user:student'));
	}
	
}