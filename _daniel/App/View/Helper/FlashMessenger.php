<?php

class App_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    private $fm;
    
    private function getTemplate($type)
    {
        switch ($type) {
            case 'error':
                $template = '<div class="notification notification-error">%s</div>';
                break;
            case 'success':
                $template = '<div class="notification notification-success">%s</div>';
                break;
            default:
                $template = '<div class="notification">%s</div>';
        }
        return $template;
    }
    
    private function getFlashMessenger()
    {
		if (null === $this->fm)
			$this->fm = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
			
		return $this->fm;
    }
    
    public function flashMessenger()
    {
        $fm = $this->getFlashMessenger();
        $messages = $fm->getMessages();
        
        if ($fm->hasCurrentMessages()) {
            $messages = array_merge($messages, $fm->getCurrentMessages());
            $fm->clearCurrentMessages();
        }
        
        $output = '';
        
        foreach ($messages as $message) {
            if (is_array($message)) {
                list ($key, $message) = each($message);
                $output = sprintf($this->getTemplate($key), $message);
            } else {
                $output = sprintf($this->getTemplate(), $message);
            }
        }
        
        return $output;
    }
    
}