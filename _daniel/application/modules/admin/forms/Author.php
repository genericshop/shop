<?php

class Admin_Form_Author extends App_Form
{
    
    public function init()
    {
        
    	$this->addElement('text', 'name', array(
			'label' 	=> 'Name',
			'required' 	=> true,
			'maxlength' => 50,
    	));
    	 
    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->setMethod('post');
    	$this->finalise();
    	
    }
    
}