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
    		'label' 	=> _('First Name / Eerste Naam'),
    		'required' 	=> true,
    		'maxlength' => 30,
    		'filters'	=> array(array('UcWords')),
    	));
    	
    	$this->addElement('text', 'Surname', array(
    		'label' 	=> _('Last Name / Van '),
    		'required' 	=> true,
    		'maxlength' => 30,
			'filters'	=> array(array('UcWords')),
    	));

    	if ($this->account_type === 'parent') {
    	   /*
        	$this->addElement('text', 'HomePhone', array(
        	    'label' 	 => _('Home Telephone / Huis Telefoon'),
        	    'maxlength'  => 15,
        	    'filters'    => array(array('Digits')),
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
        	));
        	*/
        	$this->addElement('text', 'CellPhone / Selfoon', array(
        	    'label' 	 => _('Mobile Telephone'),
        	    'maxlength'  => 15,
        	    'filters'    => array(array('Digits')),
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
        	));
        	
        	$this->addElement('text', 'NationalID', array(
        	    'label' 	 => _('South African ID Number / Suid Afrikaanse ID Nommer'),
        	    'required' 	 => true,
        	    'maxlength'  => 13,
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 13, 'max' => 13))),
        	));
        /*
		$this->addElement('text', 'OldAccount / Our Rekening Nommer ', array(
        	    'label' 	 => _('Old Account Number 2014'),
        	    'required' 	 => false,
        	    'maxlength'  => 13,
        	    'validators' => array(array('Digits'), array('StringLength', false, array('min' => 0, 'max' => 13))),
        	));
        */
        	
    	$this->addElement('text', 'Email', array(
    		'label' 		=> _('E-mail address / E-posadres'),
    		'required' 		=> true,
    		'maxlength' 	=> 50,
			'filters'		=> array(array('StringToLower')),
    		'validators'	=> array(array('EmailAddress')),	
    	));   
    	} elseif ($this->account_type === 'student') {
    	    
    	    $this->addElement('text', 'Email', array(
    	        'label' 	 => _('E-mail (optional, only required for e-books) / E-pos (opsioneel, net nodig vir e-boeke)'),
    	        'maxlength'  => 50,
    	        'required'   => false,
    	        //'filters'    => array(array('Digits')),
    	        //'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
    	    ));
    	    
    	    $this->addElement('select', 'GradeID', array(
    	        'label'    => _('Grade (2015) / Graad (2015)'),
    	        'required' => true,
    	        //'filters'    => array(array('Digits')),
    	        //'validators' => array(array('Digits'), array('StringLength', false, array('min' => 10, 'max' => 15))),
    	    ));
    	    
    	    $this->GradeID->setRegisterInArrayValidator(false);
    	    
    	   $this->addElement('text', 'LearnerNumber', array(
    		'label' 		=> _('ID Number (required to uniquely identify student) / ID Nommer (word vereis om leerder uniek te identifiseer)'),
    		'required' 		=> true,
    		'maxlength' 	=> 50,
		'filters'		=> array(array('Digits')),
    		//'validators'	=> array(array('EmailAddress')),	
    	));   
    	}
    	 	
    	
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
	if ($this->account_type === 'parent') {
	$this->addElement('checkbox', 'agree', array(
    	    'label'      => _('I have read and accept the terms and conditions.'),
    	    'value'      => '',
    	    'required'   => true,
    	    'ignore'     => true,
    	    'validators' => array(array(new Zend_Validate_InArray(array(1)))),
    	));
	}

    	$this->addElement('button', 'submit', array(
    		'label' 	=> _('Create My Account'),
    		'type'		=> 'submit',
    		'class'		=> 'btn btn-primary',
			'ignore' 	=> true,    			
    	));    	
    	
		$this->prepare();
		
	}
	
	public function prepare()
	{
	    $this->finalise();
	}
	
}