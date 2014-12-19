<?php

class App_Plugin_Navigation extends Zend_Controller_Plugin_Abstract
{
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $navigation = new Zend_Navigation($this->getPages());
        
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->navigation($navigation)->setAcl(Zend_Registry::get('Zend_Acl'))->setRole(Zend_Registry::get('Zend_Role'));
                
        Zend_Registry::set('Zend_Navigation', $navigation);
    }
    
    private function getPages()
    {
        
        $pages = array(
            
            'home' => array(
                'label'      => _('Home'),
                'module'     => 'default',
                'controller' => 'index',
                'action'     => 'index',
                'route'      => 'default'
            ),
            
            'bundle' => array(
                'label'    => _('Place an order'),
                'route'    => 'bundle',
                'resource' => 'default:bundle',
            ),
            
            'buy-back' => array(
                'label'    => _('Buy Back Books'),
                'route'    => 'buy-back',
            ),
            
            'notice-board' => array(
                'label'    => _('Notice Board'),
                'route'    => 'notice-board',
            ),
            
            'contact' => array(
                'label'    => _('Contact'),
                'route'    => 'contact',
            ),
            
            'account' => array(
                'id'       => 'nav-account',
                'label'    => _('My account'),
                'route'    => 'account',
                'resource' => 'user',
                'pages'    => array(
                
                    array(
                        'label'    => _('My profile'),
                        'route'    => 'account-profile',
                    ),
                    
                   'children' => array(
                        'label'    => _('My children'),
                        'route'    => 'account-children',
                        'resource' => 'user:account:children'
                    ),
                    
                    'history' => array(
                        'label'    => _('My orders'),
                        'route'    => 'account-history',
                    ),
                    
                    'transaction' => array(
                        'label'    => _('My transactions'),
                        'route'    => 'account-transaction',
                    ),
                    
                    'ebook' => array(
                        'label'    => _('My e-books'),
                        'route'    => 'account-books',
                    ),
                    
                )
            ),
            
        );
        
        // todo - rather do this is some kind of resource
        
        $store = App_Session::getInstance()->get('Store');
        
        if (!in_array($store['id'], array(4, 8)))
            unset($pages['buy-back']);
            
        // end    
        
        return $pages;
        
    }
    
}