<?php

class Admin_Form_Content extends App_Form
{
    
    public function init()
    {
    	$this->addElement('text', 'name', array(
			'label' 	=> 'Title / Heading',
    	    'translate' => true,
			'required' 	=> true,
			'maxlength' => 50,
    	));

    	$this->addElement('textarea', 'description', array(
			'label' 	=> 'Content',
			'class'		=> 'ckeditor ckeditor-image',    			
			'required' 	=> true,
    	    'translate' => true,
    	));    	
    	
    	$this->appendMeta();
    	
    	$this->addElement('button', 'submit', array(
			'label' 	 => 'Save Changes',
			'type'		 => 'submit',
			'class'		 => 'btn',
			'ignore' 	 => true
    	));
    	 
    	$this->setMethod('post');
    	$this->finalise();
    }
    
    public function appendMeta()
    {
    	$form      = new Admin_Form_Meta();
    	$elements  = $form->getElements();
    	
    	foreach ($elements as $el)
    		$this->addElement($el);
    }
    
}