<?php

class Admin_Form_Publisher extends App_Form
{
    
    public function init()
    {
    	$this->addElement('text', 'name', array(
			'label' 	=> 'Name',
    	    'translate' => true,
			'required' 	=> true,
			'maxlength' => 50,
    	));
    	 
    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->finalise();
    }
    
}