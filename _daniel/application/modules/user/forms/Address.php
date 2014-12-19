<?php

class User_Form_Address extends App_Form
{
    
    public function init()
    {
    	$this->addElementPrefixPath('App_Filter', 'App/Filter/', 'filter');

    	$this->addElement('text', 'address_line_1', array(
    	    'label' 	=> 'Address Line 1',
    	    'maxlength' => 30,
    	    'filters'	=> array(array('UcWords')),
    	));
    	 
    	$this->addElement('text', 'address_line_2', array(
    	    'label' 	=> 'Address Line 2',
    	    'maxlength' => 30,
    	    'filters'	=> array(array('UcWords')),
    	));
    	 
    	$this->addElement('text', 'address_city', array(
    	    'label' 	=> 'City',
    	    'maxlength' => 30,
    	    'filters'	=> array(array('UcWords')),
    	));
    	 
    	$this->addElement('text', 'address_postcode', array(
    	    'label' 	=> 'Postcode',
    	    'maxlength' => 10,
    	    'filters'	=> array(array('StringToUpper')),
    	));    	

		$this->finalise(array('class' => 'form-bs'));
	}
	
}