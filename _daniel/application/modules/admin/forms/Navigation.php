<?php

class Admin_Form_Navigation extends App_Form
{
    
    public function init()
    {
    	$this->addElement('hidden', 'parent', array());    	
    	
    	$this->addElement('text', 'name', array(
			'label' 	=> 'Label',
			'required' 	=> true,
    	    'translate' => true,
			'maxlength' => 50
    	));
    	
    	$this->addElement('select', 'content', array(
			'label' 	=> 'Content Page',
    		'required'	=> false,
    	));

        $this->addElement('select', 'active', array(
			'label' 		=> 'Active',
			'required' 		=> true,
    		'multiOptions'	=> array(0 => 'No', 1 => 'Yes'),
    	));    	
    	
    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->setMethod('post');
    	$this->prepare();
    	
    }
    
    public function prepare()
    {
    	$model = new App_Model_Content();
    	$this->content->setMultiOptions(array('' => 'N/A') + App_Util::toSelect($model->fetchAll(null, 'name')->toArray(), 'id', 'name'));
    	    	
    	$this->finalise();
    }    
    
}