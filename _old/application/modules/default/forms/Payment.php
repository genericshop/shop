<?php

class Default_Form_Payment extends App_Form
{
    
    public function getPaymentOptions()
    {
        return array(
            'card'   => _('Credit Card'),
            'eft'    => _('EFT')
        );
    }
    
    public function init()
    {
    	$this->addElementPrefixPath('App_Filter', 'App/Filter/', 'filter');
    	
    	$this->addElement('select', 'paytype', array(
			'label' 	=> _('How would you like to pay?'),
			'required' 	=> true,
    	));
    	
    	$this->addElement('button', 'submit', array(
			'label' 	=> _('Place Order'),
			'type'		=> 'submit',
			'class'		=> 'btn btn-primary',
			'ignore' 	=> true,
    	));    	
    	
    	$this->prepare();
    }
    
    public function prepare()
    {
        $options = array('' => 'Please Choose') + $this->getPaymentOptions();
        $this->paytype->setMultiOptions($options);
        
        $this->finalise();
    }
    
}