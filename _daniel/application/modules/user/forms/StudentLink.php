<?php

class User_Form_StudentLink extends App_Form
{
    
    public function init()
    {
        
	    $this->addElement('text', 'LearnerNumber', array(
	        'label' 	 => _('Learner Number'),
	        'maxlength'  => 20,
	        'required'   => true,
	    ));
    	
		$this->finalise();
			
	}
	
}