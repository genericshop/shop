<?php

class App_Form extends Zend_Form
{
	
    private $options;
    
	public function setBootstrapDecorators()
	{
	    
	    $class_label = array('control-label');
	    $class_input = array();
	    
	    if (isset($this->options['col_left']))
	       $class_label[] = $this->options['col_left']; 
	    
	    if (isset($this->options['col_right']))
	        $class_input[] = $this->options['col_right'];
	    
	    $this->setElementDecorators(array(
			'ViewHelper',
			'Description',
			'Errors',
			array('HtmlTag', array('tag' => 'div', 'class' => implode(' ', $class_input))),
			array('Label', array('class' => implode(' ', $class_label))),
			array('decorator' => array('tag' => 'HtmlTag'), 'options' => array('tag' => 'div', 'class' => 'form-group')),
		));

		if ($this->getElement('submit')) {
			
		    $submit = $this->getElement('submit');
		    $submit->removeDecorator('Label');
		    
		    if (isset($this->options['col_right'])) {
		        
		        $class = array();
		        
		        foreach (explode(' ', $this->options['col_right']) as $col)
		            $class[] = $col;
		        
		        foreach (explode(' ', $this->options['col_left']) as $col) {
		            $size    = explode('-', substr($col, strpos($col, '-', 1) + 1));
		            $class[] = 'col-' . implode('-offset-', $size);
		        }
		        
		        $submit->addDecorator('HtmlTag', array('tag' => 'div', 'class' => implode(' ', $class)));
		        
		    }
		    
		}
		
		$this->setDecorators(array(
			'FormElements',
			'Form'
		));
	}
	
	public function setDefaultFilters()
	{
	    $order = 10;
	    
	    foreach ($this->getElements() as $element)
	        $element->addFilter('StringTrim')->addFilter('StripTags')->setOrder($order+=10);
	        
	}
	
	public function finalise($options = array())
	{
	    $this->options = $options;
	    
	    $class = isset($options['class']) ? $options['class'] : null;
	    
	    $this->setDefaultFilters();
	    $this->setBootstrapDecorators();
        
        $this->setMethod(isset($options['method']) ? $options['method'] : 'post')->setAttrib('class', $class);	    
	}
	
	public function pullMessages()
	{
	    $errors = array();
	     
	    foreach ($this->getMessages() as $key => $messages)
	        $errors[$key] = implode('<br>', $messages);
	     
	    return $errors;
	}
	
	public function addStoreId($store = null)
	{
	    if (!$store)
	        $store = App_Session::getInstance()->get('Store');
	    
	    $this->addElement('hidden', 'StoreID', array('value' => $store['StoreID']));
	}
	
}