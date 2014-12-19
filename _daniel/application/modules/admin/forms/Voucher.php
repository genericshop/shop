<?php

class Admin_Form_Voucher extends App_Form
{
    
    public function init()
    {
    	$this->addElement('select', 'type', array(
			'label' 		=> 'Value Type',
    		'required'		=> true,
    		'multiOptions'	=> array('' => 'Please Choose', 'fixed' => 'Fixed', 'percent' => 'Percent'),
    	));    	
    	
    	$this->addElement('text', 'amount', array(
			'label' 		=> 'Value Amount',
    		'required'		=> true,
    		'validators'	=> array(
    			array('Regex', false, array('pattern' =>'/^\$?[0-9]+(,[0-9]{3})*(.[0-9]{2})?$/')),
    			array('GreaterThan', false, array('min' => 1))
    		)
    	));    	
    	
    	$this->getElement('amount')->addErrorMessage('Please enter a percentage without the % or a number with two decimal places.');
    	
    	$this->addElement('text', 'expires', array(
			'label'    => 'Expiry Date',
    		'class'    => 'datepicker',
    	    'required' => true
    	));    	
    	
    	$this->addElement('text', 'count', array(
			'label' 	 => 'Generate how many?',
    		'maxlength'	 => 3,
    		'value'		 => 1,
    		'required'	 => true,
			'ignore' 	 => true,
    		'validators' => array(array('Digits')),
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
//     	$model = new App_Model_Book();
//     	$this->book->setMultiOptions(array('' => 'No') + App_Util::toSelect($model->fetchAll('active = 1', 'name')->toArray(), 'id', 'name', false));
    	
    	$this->expires->setAttrib('data-date-start-date', '+1');
    	$this->finalise();
    }    
    
}