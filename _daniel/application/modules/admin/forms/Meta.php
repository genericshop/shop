<?php

class Admin_Form_Meta extends App_Form
{
    
    public function init()
    {
        $this->addElement('text', 'meta_title', array(
			'label' 	 => 'Page Title',
            'translate'  => true,
        	'validators' => array(array('StringLength', false, array('max' => 150)))
    	));
        
        $this->addElement('text', 'meta_keywords', array(
        	'label' 	 => 'Meta Keywords',
            'translate'  => true,            
        	'validators' => array(array('StringLength', false, array('max' => 255)))
        ));

        $this->addElement('textarea', 'meta_description', array(
        	'label' 	 => 'Meta Description',
            'translate'  => true,            
        	'attribs'	 => array('rows' => 5),
        	'validators' => array(array('StringLength', false, array('max' => 255)))
        ));
    }
    
}