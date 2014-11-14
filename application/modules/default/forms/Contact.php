<?php

class Default_Form_Contact extends App_Form
{
    
    public function init()
    {
    	$this->addElementPrefixPath('App_Filter', 'App/Filter/', 'filter');
    	
    	$this->addElement('text', 'name', array(
			'label' 	=> _('Name'),
			'required' 	=> true,
			'maxlength' => 50,
    		'filters'	=> array(array('UcWords')),
    	));
    	
    	$this->addElement('text', 'email', array(
			'label' 		=> _('E-mail address'),
			'required' 		=> true,
			'maxlength' 	=> 50,
    		'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress')),
    	));

    	$this->addElement('text', 'tel', array(
			'label' 	=> _('Telephone'),
			'required' 	=> true,
			'maxlength' => 15,
    		'filters'	=> array(array('Digits')),
    	));    	
    	
    	$this->addElement('textarea', 'message', array(
			'label' 	=> _('Your message'),
    		'required'	=> true,   			
    	));

    	$this->addElement('button', 'submit', array(
			'label' 	=> _('Submit'),
			'type'		=> 'submit',
			'class'		=> 'btn btn-primary',
			'ignore' 	=> true,
    	));    	
    	
    	$this->finalise();
    }
    
}