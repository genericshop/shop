<?php

class Admin_Form_Book extends App_Form
{
    
    public function init()
    {
        $this->addElement('text', 'name', array(
            'label' 	=> 'Title',
            'required' 	=> true,
            'maxlength' => 255,
        ));
        
        $this->addElement('text', 'isbn', array(
            'label' 		=> 'ISBN',
            'required' 		=> true,
            'maxlength' 	=> 13,
            'filters'		=> array(array('Digits')),
            'validators'	=> array(array('StringLength', false, array('min' => 13, 'max' => 13))),
        ));        
                
        $this->addElement('text', 'published', array(
			'label' 	=> 'Publication Date',
        	'placeholder' => 'YYYY',
			'required' 	=> false,
    	));   	

        $this->addElement('select', 'publisher', array(
			'label' 	=> 'Publisher',
			'required' 	=> false,
    	));

        $this->addElement('multiselect', 'category', array(
            'label' 	=> 'Categories',
            'required' 	=> true,
            'ignore'	=> true,
            'size'      => 10,
        ));        
    	
    	$this->addElement('multiselect', 'author', array(
			'label' 	=> 'Author(s)',
			'required' 	=> false,
    		'ignore'	=> true,
    	    'size'      => 10,
    	));

        $this->addElement('multiselect', 'format', array(
            'label' 	=> 'Format(s)',
            'required' 	=> true,
            'ignore'    => true
        ));

    	$this->addElement('textarea', 'description', array(
			'label' 	=> 'Description',
			'class'		=> 'ckeditor',    			
			'required' 	=> false,
    	    'translate' => true
    	));    	
    	
    	$this->addElement('text', 'price', array(
			'label' 		=> 'Price',
			'validators'	=> array(array(new App_Validator_Decimal())),
    	));

    	$this->addElement('select', 'available', array(
    	    'label' 		=> 'In Stock',
    	    'required' 		=> true,
    	    'multiOptions'	=> array(1 => 'Yes', 0 => 'No'),
    	));    	
    	
//     	$this->addElement('text', 'grade', array(
// 			'label' 	 => 'Grade',
// 			'required' 	 => false,
// 			'maxlength'  => 2,
//     	    'validators' => array(array('Digits'))
//     	));    	
    	
    	$this->addElement('select', 'language', array(
			'label' 	=> 'Language',
			'required' 	=> false,
    	));    	

    	$this->addElement('file', 'image', array(
    	    'label' 		=> 'Image',
    	    'required'		=> true,
    	    'validators'	=> array(array('Extension', true, 'png,jpg')),
    	));    	
    	
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
    	 
    	$this->setMethod('post');
    	$this->prepare();
    	
    }
    
    public function prepare()
    {
    	$model = new App_Model_Category();
    	$this->category->setMultiOptions($model->getSelectOptions(true));

        $model = new App_Model_Publisher();
        $this->publisher->setMultiOptions(App_Util::toSelect($model->fetchAll(null, 'name')->toArray(), 'id', 'name', true));
    	
    	$model = new App_Model_Author();
    	$this->author->setMultiOptions(App_Util::toSelect($model->fetchAll(null, 'name')->toArray(), 'id', 'name', false));    	
    	
     	$model = new App_Model_Format();
     	$this->format->setMultiOptions(App_Util::toSelect($model->fetchAll(null, 'name')->toArray(), 'id', 'name', false));
    	
    	$model = new App_Model_Language();
    	$this->language->setMultiOptions(App_Util::toSelect($model->fetchAll(null, 'name')->toArray(), 'id', 'name', false));    	
    	
    	$this->finalise(array('files' => 'image'));
    }
    
}