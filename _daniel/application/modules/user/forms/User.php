<?php

class User_Form_User extends App_Form
{
    
    public function init()
    {
    	$this->addElementPrefixPath('App_Filter', 'App/Filter/', 'filter');
    	
    	$this->addElement('select', 'title', array(
    		'label' 		=> 'Title',
    		'required' 		=> true,
    		'multiOptions'	=> array('Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms', 'Dr' => 'Dr', 'Prof' => 'Prof'),
    	));    	
    	
    	$this->addElement('text', 'name', array(
    		'label' 	=> 'First Name',
    		'required' 	=> true,
    		'maxlength' => 30,
    		'filters'	=> array(array('UcWords')),
    	));
    	
    	$this->addElement('text', 'surname', array(
    		'label' 	=> 'Last Name',
    		'required' 	=> true,
    		'maxlength' => 30,
			'filters'	=> array(array('UcWords')),
    	));
    	    	
    	/*
    	$this->addElement('text', 'tel', array(
    	    'label' 		=> 'Telephone',
    	    'maxlength' 	=> 15,
    	    'validators'	=> array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
    	));    	
    	
    	$this->addElement('select', 'address_type', array(
    		'label' 	=> 'Is this a Home or Work address?',
    		'multioptions' => array(
    			''	   => 'Please choose',
    			'Home' => 'Home',
    			'Work' => 'Work'
    		),
    		'required' 	=> false,
    	));
    	*/
    	
    	$this->addElement('text', 'email', array(
    		'label' 		=> 'E-mail Address',
    		'required' 		=> true,
    		'maxlength' 	=> 50,
			'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress')),	
    	));    	
    	
    	$this->addElement('text', 'emailc', array(
    		'label' 		=> 'Confirm E-mail',
    		'required' 		=> true,
    		'ignore'		=> true,
    		'maxlength' 	=> 50,
			'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress'), array('Identical', true, 'email')),	
    	));    	
    	
    	$this->addElement('password', 'password', array(
			'label' 		=> 'Password',
			'required' 		=> true,
			'maxlength' 	=> 20,
			'validators'	=> array(array('StringLength', true, array('min' => 6, 'max' => 20))),
    	));
    	
    	$this->addElement('password', 'passwordc', array(
			'label' 		=> 'Confirm Password',
			'required' 		=> true,
    		'ignore'		=> true,
			'maxlength' 	=> 20,
			'validators'	=> array(array('Identical', true, 'password')),
    	));    	
		
    	$this->addElement('button', 'submit', array(
    		'label' 	=> 'Create My Account',
    		'type'		=> 'submit',
    		'class'		=> 'btn',
			'ignore' 	=> true,    			
    	));    	
    	
		$this->finalise(array('class' => 'form-bs'));
		
	}
	
}