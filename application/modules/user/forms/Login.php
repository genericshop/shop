<?php

class User_Form_Login extends App_Form
{
    
    public function init()
    {
        
    	$this->addElement('text', 'Email', array(
    		'label' 	=> _('Username/Phone Number'),
    		'required' 	=> true,
    		'maxlength' => 50,
    		'filters'	=> array(array('StringToLower')),
    	));
    	
    	$this->addElement('password', 'Password', array(
			'label' 	=> _('Password'),
			'required' 	=> true,
			'maxlength' => 20,
    	));
		
    	$this->addElement('button', 'submit', array(
    		'label' 	=> _('Sign In'),
    		'type'		=> 'submit',
    		'class'		=> 'btn btn-primary',
			'ignore' 	=> true,    			
    	));    	
    	
		$this->finalise();
			
	}
	
}