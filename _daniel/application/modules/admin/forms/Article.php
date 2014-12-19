<?php

class Admin_Form_Article extends App_Form
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
			'label' 	=> 'Title / Heading',
			'required' 	=> true,
            'translate' => true,
    	));   	

        $this->addElement('text', 'published', array(
			'label' 	=> 'Published',
            'class'     => 'datepicker',
			'required' 	=> true,
    	));

    	$this->addElement('textarea', 'description', array(
			'label' 	=> 'Content',
			'class'		=> 'ckeditor',    			
			'required' 	=> true,
    	    'translate' => true,
    	));    	
    	
    	$this->addElement('file', 'image', array(
    	    'label' 	 => 'Image',
    	    'validators' => array(array('Extension', true, 'png,jpg')),
    	));    	
    	
    	$this->appendMeta();    	
    	
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
    	$this->category->setMultiOptions($model->getSelectOptions(true));
    	
        $this->published->setAttrib('data-date-end-date', '+0');
        $this->finalise(array('files' => 'image'));
    }
    
    public function appendMeta()
    {
    	$form     = new Admin_Form_Meta();
    	$elements = $form->getElements();
    	
    	foreach ($elements as $el)
    		$this->addElement($el);
    }
    
}