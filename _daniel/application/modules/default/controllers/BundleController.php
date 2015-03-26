<?php

class BundleController extends App_Controller_Action
{
	
    public function init()
    {
        parent::init();
    }
    
    public function indexAction()
    {
        if ($this->_auth->AccountType === 'Student')
            return $this->forward('list');

        $children = $this->view->children = $this->_api->getChildrenForParent($this->_auth->UniqueRef);
        $options  = array('' => $this->view->translate('Please Choose'));
        
        foreach ($children as $child)
            $options[$child->StudentUniqueRef] = $child->FullName;
        
        $this->view->children = $options;
    }
    
    public function listAction()
    {
        $student = $this->view->student = $this->getStudent();
        
        if (!$student)
            $this->_helper->redirector->gotoRoute(array(), 'bundle');
        
        $this->view->nav = $this->getNavigation($student->GradeID);
    }
	
    public function viewAction()
    {
        $student = $this->view->student = $this->getStudent();
        
        if (!$student)
            $this->_helper->redirector->gotoRoute(array(), 'bundle');
        
        $bundle = $this->view->bundle = $this->_api->getPriceListDetails($this->getParam('bundle'));
        $items  = $this->view->items  = $this->_api->getPriceListItems($bundle->PriceListID);
        $this->view->nav = $this->getNavigation($student->GradeID);
    }
    
    public function setStudentAction()
    {
        $json    = array('result' => 0);
        $student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('student', null));
        
        if ($student) {

            App_Session::getInstance()->set('BundleStudent', $student);
            $json['result'] = 1;
            
        }
        
        $this->_helper->json($json);
    }

    private function getStudent()
    {
        return App_Session::getInstance()->get('BundleStudent');
    }
    
    private function getNavigation($grade_id)
    {
        $nav   = new Zend_Navigation();
        $items = $this->getNavigationItems($grade_id);
        
        $key_name = 'Description_' . $this->view->lang;
        
        foreach ($items as $item) {
        
            $page = array(
                'label'  => $item->{$key_name},
                'route'  => 'bundle-view',
                'params' => array('bundle' => $item->PriceListID)
            );
            
            $nav->addPage($page);
         
        }
        
        return $nav;
    }
    
    private function getNavigationItems($grade_id)
    {
        $cache      = Zend_Registry::get('Cache');
        $cache_key  = $this->_store['id'] . '_' . $grade_id . '_nav_bundle';
    
        $categories = APPLICATION_ENV === 'production' ? $cache->load($cache_key) : null;
    
        if (!$categories) {
            $categories = $this->_api->getPriceLists($grade_id);
            $cache->save($categories, $cache_key);
        }
    
        return $categories;
    }
    
}