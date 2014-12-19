<?php

class App_Plugin_Language extends Zend_Controller_Plugin_Abstract
{
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        if ($request->getParam('lang', null)) {
            
            $lang = $request->getParam('lang');
            
            if (!in_array($lang, array('en', 'af')))
                $lang = 'en';
            
            setcookie('language', $lang, time() + (10 * 365 * 24 * 60 * 60));
            
        } else {
            
            if (isset($_COOKIE['language'])) {
                
                $lang = $_COOKIE['language'];
                
            } else {
            
                $lang = App_Session::getInstance()->get('Store')['Info']->Language === 'Afrikaans' ? 'af' : 'en';
                setcookie('language', $lang, time() + (10 * 365 * 24 * 60 * 60));
                
            }
            
        }
        
        $locale = new Zend_Locale();
        $locale->setLocale($lang . '_ZA');
        
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('LanguageSuffix', $lang === 'af' ? 'AFR' : 'ENG');
        
        if ($lang !== 'en') {
        
            $translate = new Zend_Translate_Adapter_Gettext(APPLICATION_PATH . '/language/' . $lang . '_ZA.mo', $lang);
            $translate->setLocale($lang);
        
            Zend_Registry::set('Zend_Translate', $translate);
        
        }
        
    } 
    
}