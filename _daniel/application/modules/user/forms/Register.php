<?php

class User_Form_Register extends App_Form
{
    
    private $account_type;
    
    public function __construct($account_type = 'parent')
    {
        $this->account_type = $account_type;
        parent::__construct(null);
    }
    
    public function init()
    {
    	$this->addElementPrefixPath('App_Filter', 'App/Filter/', 'filter');
    	
    	$this->addElement('text', 'Name', array(
    		'label' 	=> _('First Name'),
    		'required' 	=> true,
    		'maxlength' => 30,
    		'filters'	=> array(array('UcWords')),
    	));
    	
    	$this->addElement('text', 'Surname', array(
    		'label' 	=> _('Last Name'),
    		'required' 	=> true,
    		'maxlength' => 30,
			'filters'	=> array(array('UcWords')),
    	));

    	if ($this->account_type === 'parent') {
    	
        	$this->addElement('text', 'HomePhone', array(
        	    'label' 	 => _('Home Telephone'),
        	    'maxlength'  => 15,
        	    'filters'    => array(array('Digits')),
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
        	));
        	
        	$this->addElement('text', 'CellPhone', array(
        	    'label' 	 => _('Mobile Telephone'),
        	    'maxlength'  => 15,
        	    'filters'    => array(array('Digits')),
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
        	));
        	
        	$this->addElement('text', 'NationalID', array(
        	    'label' 	 => _('South African ID Number'),
        	    'required' 	 => true,
        	    'maxlength'  => 13,
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 13, 'max' => 13))),
        	));
        	
    	} elseif ($this->account_type === 'student') {
    	    
    	    $this->addElement('text', 'LearnerNumber', array(
    	        'label' 	 => _('Learner Number'),
    	        'maxlength'  => 20,
    	        'required'   => true,
    	        //'filters'    => array(array('Digits')),
    	        //'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
    	    ));
    	    
    	    $this->addElement('select', 'GradeID', array(
    	        'label'    => _('Grade'),
    	        'required' => true,
    	        //'filters'    => array(array('Digits')),
    	        //'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
    	    ));
    	    
    	    $this->GradeID->setRegisterInArrayValidator(false);
    	    
    	}
    	
    	$this->addElement('text', 'Email', array(
    		'label' 		=> _('Email Address'),
    		'required' 		=> true,
    		'maxlength' 	=> 50,
			'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress')),	
    	));    	
    	
    	$this->addElement('text', 'EmailC', array(
    		'label' 		=> _('Confirm Email'),
    		'required' 		=> true,
    		'ignore'		=> true,
    		'maxlength' 	=> 50,
			'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress'), array('Identical', true, 'Email')),	
    	));    	
    	
    	$this->addElement('password', 'Password', array(
			'label' 		=> _('Password'),
			'required' 		=> true,
			'maxlength' 	=> 20,
			'validators'	=> array(array('StringLength', true, array('min' => 6, 'max' => 20))),
    	));
    	
    	$this->addElement('password', 'PasswordC', array(
			'label' 		=> _('Confirm Password'),
			'required' 		=> true,
    		'ignore'		=> true,
			'maxlength' 	=> 20,
			'validators'	=> array(array('Identical', true, 'Password')),
    	));    	
		
    	$this->addElement('button', 'submit', array(
    		'label' 	=> _('Create My Account'),
    		'type'		=> 'submit',
    		'class'		=> 'btn btn-primary',
			'ignore' 	=> true,    			
    	));    	
    	
		$this->finalise();
		
	}
	
}