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
        
        $bundles = $this->_api->getPriceLists($student->GradeID);
       // Zend_Debug::dump($bundles);
        if ($bundles) {
            
            foreach ($bundles as $k => &$bundle) {
                
                $bundle->Items = $this->_api->getPriceListItems($bundle->PriceListID);
                
                if (!is_array($bundle->Items) || empty($bundle->Items))
                    unset($bundles[$k]);
                
            } unset($bundle);

            $key_sort = 'Description_' . $this->_lang;
            
            usort($bundles, function($a, $b) use ($key_sort) {
                return $a->{$key_sort} > $b->{$key_sort};
            });
            //Zend_Debug::dump($bundles);
            $this->view->bundles = $bundles;
            $this->view->GradeID = $student->GradeID;
            
            
        }
        
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
    
}