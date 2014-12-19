<?php

class Admin_Form_BookDigital extends App_Form
{
    
    public function init()
    {
//     	$this->addElement('hidden', 'ebook_book', array(
// 			'label' 	=> 'Parent',
// 			'required' 	=> true
//     	));   	

    	$this->addElement('text', 'ebook_published', array(
    		'label' 	=> 'eBook Publication Date',
    		'placeholder' => 'YYYY',
    		'required' 	=> false
    	));    	
    	
    	$this->addElement('text', 'ebook_isbn_pdf', array(
			'label' 		=> 'eBook ISBN (PDF)',
			'required' 		=> false,
			'maxlength' 	=> 13,
    		'filters'		=> array(array('Digits')),
    		'validators'	=> array(array('StringLength', false, array('min' => 13, 'max' => 13))),
    	));    	

    	$this->addElement('text', 'ebook_isbn_epub', array(
			'label' 		=> 'eBook ISBN (ePub)',
			'required' 		=> false,
			'maxlength' 	=> 13,
    		'filters'		=> array(array('Digits')),
    		'validators'	=> array(array('StringLength', false, array('min' => 13, 'max' => 13))),
    	));    	
    	
    	$this->addElement('text', 'ebook_price', array(
			'label' 		=> 'eBook Price',
    		'required'		=> false,
			'validators'	=> array(array(new App_Validator_Decimal())),
    	));

        $this->addElement('select', 'ebook_active', array(
			'label' 		=> 'eBook Active',
			'required' 		=> false,
    		'multiOptions'	=> array(0 => 'No', 1 => 'Yes'),
    	));    	

//     	$this->addElement('button', 'submit', array(
// 			'label' 	=> 'Save Changes',
// 			'type'		=> 'submit',
// 			'class'		=> 'btn',
// 			'ignore' 	=> true,
//     	));
    	 
    	$this->finalise();
    }
    
}