<?php

class Admin_Form_CategoryNews extends App_Form
{
    
    public function init()
    {
    	$this->addElement('text', 'name', array(
			'label' 	=> 'Name',
			'required' 	=> true,
    	    'translate' => true,
			'maxlength' => 50
    	));

        $this->addElement('select', 'parent', array(
            'label' 	=> 'Parent',
            'required' 	=> false,
        ));

    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->prepare();
    }
    
    public function prepare()
    {
        $model = new App_Model_CategoryNews();
        
        if ($model->getMaxDepth() > 0) {
        
            $this->parent->setMultiOptions(array('' => 'Top Level') + $model->getSelectOptions());
            
        } else {
            
            $this->removeElement('parent');
            
        }
                
        $this->finalise();
    }
    
}