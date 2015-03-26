<?php

class Admin_Form_Banner extends App_Form
{
    
    public function init()
    {
        $this->addElement('text', 'name', array(
            'label' 	=> 'Name',
            'required' 	=> true,
            'maxlength' => 50,
        ));        
        
        /*
        $this->addElement('text', 'caption_title', array(
            'label' 	=> 'Caption Title',
            'required' 	=> true,
            'translate' => true,
            'maxlength' => 30,
        ));        
        
        $this->addElement('textarea', 'caption', array(
            'label' 	=> 'Caption',
            'required' 	=> true,
            'translate' => true,
            'maxlength' => 255,
        ));
        */        
        
        $this->addElement('text', 'uri', array(
			'label' 	=> 'Uri',
			'required' 	=> false,
			'maxlength' => 255,
    	));

        $this->addElement('file', 'image', array(
            'label' 		=> 'Image',
            'required'		=> true,
            'validators'	=> array(array('Extension', true, 'png,jpg')),
        ));        
        
    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->finalise(array('files' => 'image'));
    }
    
}