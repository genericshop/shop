<?php

class Admin_Form_Product extends App_Form
{
    
    public function init()
    {
        $this->addElement('multiselect', 'category', array(
            'label' 	=> 'Categories',
            'required' 	=> true,
            'ignore'	=> true,
            'size'      => 10,
        ));

        $this->addElement('text', 'name', array(
			'label' 	=> 'Name',
			'required' 	=> true,
            'translate' => true,
    	));   	

        $this->addElement('text', 'price', array(
            'label' 		=> 'Price',
            'required'		=> true,
            'validators'	=> array(array(new App_Validator_Decimal())),
        ));        
        
        $this->addElement('select', 'available', array(
            'label' 		=> 'In Stock',
            'required' 		=> true,
            'multiOptions'	=> array(1 => 'Yes', 0 => 'No'),
        ));        
        
    	$this->addElement('textarea', 'description', array(
			'label' 	=> 'Content',
			'class'		=> 'ckeditor',    			
			'required' 	=> true,
    	    'translate' => true,
    	));    	
    	
    	$this->addElement('file', 'image', array(
    	    'label' 		=> 'Image',
    	    'required'		=> true,
    	    'validators'	=> array(array('Extension', true, 'png,jpg')),
    	));  

    	$this->appendMeta();
    	
    	$this->addElement('select', 'featured', array(
    	    'label' 		=> 'Featured',
    	    'required' 		=> true,
    	    'multiOptions'	=> array(0 => 'No', 1 => 'Yes'),
    	));
    	
    	$this->addElement('select', 'special', array(
    	    'label' 		=> 'On Special',
    	    'required' 		=> true,
    	    'multiOptions'	=> array(0 => 'No', 1 => 'Yes'),
    	));    	
    	
    	$this->addElement('select', 'active', array(
    	    'label' 		=> 'Active',
    	    'required' 		=> true,
    	    'multiOptions'	=> array(1 => 'Yes', 0 => 'No'),
    	));    	
    	
    	$this->addElement('button', 'submit', array(
			'label' 	=> 'Save Changes',
			'type'		=> 'submit',
			'class'		=> 'btn',
			'ignore' 	=> true,
    	));
    	 
    	$this->prepare(array('files' => 'image'));
    	
    }
    
    public function prepare($options)
    {
    	$model = new App_Model_CategoryProduct();
    	$this->category->setMultiOptions($model->getSelectOptions(true));
    	
        $this->finalise($options);
    }
    
    public function appendMeta()
    {
    	$form     = new Admin_Form_Meta();
    	$elements = $form->getElements();
    	
    	foreach ($elements as $el)
    		$this->addElement($el);
    }
    
}