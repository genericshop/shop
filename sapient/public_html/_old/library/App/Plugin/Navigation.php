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
            
            'catalogue' => array(
                'id'       => 'nav-book',
                'label'    => _('Books'),
                'route'    => 'book-list',
                'pages'    => array(
                
                    array(
                        'label'    => _('Print Books'),
                        'route'    => 'book-list',
                    ),
                
                    array(
                        'label'    => _('Digital Books'),
                        'route'    => 'book-list',
                        'params'   => array('type' => 'digital')
                    ),
                    
                )
            ),
            
            'product' => array(
                'label'    => _('Products'),
                'route'    => 'product'
            ),
            
            'bundle' => array(
                'label'    => _('Place an Order'),
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
                'label'    => _('My Account'),
                'route'    => 'account',
                'resource' => 'user',
                'pages'    => array(
                
                    array(
                        'label'    => _('My Profile'),
                        'route'    => 'account-profile',
                    ),
                    
                   'children' => array(
                        'label'    => _('My Children'),
                        'route'    => 'account-children',
                        'resource' => 'user:account:children'
                    ),
                    
                    'history' => array(
                        'label'    => _('My Orders'),
                        'route'    => 'account-history',
                    ),
                    
                    'transaction' => array(
                        'label'    => _('My Transactions'),
                        'route'    => 'account-transaction',
                    ),
                    
                    'ebook' => array(
                        'label'    => _('My eBooks'),
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