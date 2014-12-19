<?php

class User_Form_Login extends App_Form
{
    
    public function init()
    {
        
    	$this->addElement('text', 'username', array(
    		'label' 	=> 'Email Address',
    		'required' 	=> true,
    		'maxlength' => 50,
    		'filters'	=> array(array('StringToLower')),
    	));
    	
    	$this->addElement('password', 'password', array(
			'label' 	=> 'Password',
			'required' 	=> true,
			'maxlength' => 20,
    	));
		
    	$this->addElement('button', 'submit', array(
    		'label' 	=> 'Sign In',
    		'type'		=> 'submit',
    		'class'		=> 'btn',
			'ignore' 	=> true,    			
    	));    	
    	
		$this->finalise(array('class' => 'form-bs'));
			
	}
	
}