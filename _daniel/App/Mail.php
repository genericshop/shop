<?php

class App_Mail extends Zend_Mail 
{
	
	private $template;
	
	public function __construct($charset = 'UTF-8')
	{
	    parent::__construct($charset);
	}	
	
	public function setTemplate($template, $data = null)
	{
		if (!$this->template) {
			
			$config = Zend_Registry::get('config');
			
			$view = new Zend_View();
			$view->setBasePath(APPLICATION_PATH . '/modules/default/views/');
			
			$container	= $view->render('templates/email-container.phtml');
			$template 	= $view->partial('templates/' . $template . '.phtml', $data);
			
			$this->template = str_replace('{TEMPLATE}', $template, $container);
			$this->template = str_replace('{BASE}', $config['site']['url'], $this->template);
		}
		
		$this->setBodyHtml($this->template);		
	}
	
}