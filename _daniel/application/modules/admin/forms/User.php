<?php

class Admin_Form_User extends User_Form_User
{
    
    public function init()
    {
    	parent::init();
    	
    	$this->removeElement('emailc');
    	
    	$this->addElement('select', 'active', array(
    		'label' 		=> 'Active',
    		'required' 		=> true,
    		'multiOptions'	=> array(0 => 'No', 1 => 'Yes'),
    	));
    	
    	$this->submit->setLabel('Save Changes')->setOrder(100);
    	$this->finalise();
	}

}