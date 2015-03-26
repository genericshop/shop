<?php

class User_StudentController extends App_Controller_Action
{

    public function listAction()
    {
        $this->view->children = $this->_api->getChildrenForParent($this->_auth->UniqueRef);
    }
    
    public function linkAction()
    {
        $form    = new User_Form_StudentLink();
        $request = $this->getRequest();
    
        if ($request->isPost()) {
    
            $json = array('result' => 0);
            $data = $request->getPost();
    
            if ($form->isValid($data)) {
    
                $form->addElement('hidden', 'ParentUniqueRef', array('value' => $this->_auth->UniqueRef));
                $result = $this->_api->linkStudentToParent($form->getValues());
    
                if (true === $result->Status) {
    
                    $json['result']   = 1;
                    $json['callback'] = 'formSuccess()';
    
                    $this->_helper->flashMessenger(array('success' => $result->StatusMessage));
    
                } else {
    
                    $json['callback'] = 'displayMessage("' . $result->StatusMessage . '", "error");';
    
                }
    
            } else {
    
                $json['formErrors'] = $form->pullMessages();
    
            }
    
            $this->_helper->json($json);
    
        }
         
        $this->view->form   = $form;
        $this->view->script = 'student/form-link.phtml';
        $this->view->title  = $this->view->translate('Link Student Account');
    
        $this->renderScript('partial/modal-form.phtml');
    }
    
    public function addAction()
    {
        $form    = new User_Form_Register('student');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
        
            $json = array('result' => 0);
            $data = $request->getPost();
        
            if ($form->isValid($data)) {

                $form->addElement('hidden', 'ParentUniqueRef', array('value' => $this->_auth->UniqueRef));
                $result = $this->_api->addStudentToParent($form->getValues());
        
                if (true === $result->Status) {
                    
                    $json['result']   = 1;
                    $json['callback'] = 'formSuccess()';
                    
                    $this->_helper->flashMessenger(array('success' => $result->StatusMessage));
                    
                } else {

                    $json['callback'] = 'displayMessage("' . $result->StatusMessage . '", "error");';
                
                }
                
            } else {
                
                $json['formErrors'] = $form->pullMessages();
                
            }
        
            $this->_helper->json($json);
            
        }
         
        $options = array();
        foreach ($this->_api->getAllGrades() as $grade)
            $options[$grade->GradeID] = $grade->Grade_ENG;
         
        asort($options, SORT_NATURAL);
        $form->GradeID->setMultiOptions($options);
         
        $this->view->form   = $form;
        $this->view->script = 'student/form.phtml';
        $this->view->title  = $this->view->translate('New Student Account');
        
        $this->renderScript('partial/modal-form.phtml');
    }

    public function editAction()
    {
        $student = $this->view->student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('reference', null));
        
        if (true !== $student->Status)
            return $this->forward('error', 'error', 'default');
        
        $form = new User_Form_Register('student');
        $form->removeElement('LearnerNumber');
        $form->removeElement('EmailC');
        $form->removeElement('PasswordC');
        $form->Password->setRequired(false);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
        
            $json = array('result' => 0);
            $data = $request->getPost();
        
            if ($form->isValid($data)) {

                $values = $form->getValues();                
                $values['StudentUniqueRef'] = $student->StudentUniqueRef;
                
                foreach ($values as $k => $v) {
                    if (!strlen($v))
                        unset($values[$k]);
                }
                
                $result = $this->_api->updateStudentAccount($values);
                
                if (true === $result->Status) {
                    
                    $json['result']   = 1;
                    $json['callback'] = 'formSuccess()';
                    
                    $this->_helper->flashMessenger(array('success' => $result->StatusMessage));
                    
                } else {

                    $json['callback'] = 'displayMessage("' . $result->StatusMessage . '", "error");';
                
                }
                
            } else {
                
                $json['formErrors'] = $form->pullMessages();
                
            }
        
            $this->_helper->json($json);
            
        } else {
            
            $parts = explode(' ', $student->FullName);
            $student->Name    = $parts[0];
            $student->Surname = $parts[1];
            
            $form->populate((array)$student);
            
        }

        $form->Password->setDescription($this->view->translate('Leave blank if unchanged'));
        
        $options = array();
        foreach ($this->_api->getAllGrades() as $grade)
            $options[$grade->GradeID] = $grade->Grade_ENG;
         
        asort($options, SORT_NATURAL);
        $form->GradeID->setMultiOptions($options);

        $this->view->form   = $form;
        $this->view->script = 'student/form.phtml';
        $this->view->params = array('edit' => true, 'action' => $this->view->url());
        $this->view->title  = $this->view->translate('Edit Student Account');
        
        $this->renderScript('partial/modal-form.phtml');
    }
    
    public function subjectManageAction()
    {
        $student = $this->view->student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('reference', null));
    
        if (true !== $student->Status)
            return $this->forward('error', 'error', 'default');
        
        $options = array();
        
        foreach ($this->_api->getAllSubjectsByGrade($student->GradeID) as $subject)
            $options[$subject->SubjectID] = $subject->SubjectName;
        
        $this->view->subjects = $options;        
    }
    
    public function subjectListAction()
    {
        $student = $this->view->student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('reference', null));
        
        if (true !== $student->Status)
            return $this->forward('error', 'error', 'default');
        
        $this->view->items = $this->_api->getSubjectsForStudent($student->StudentUniqueRef);
    }
    
    public function subjectAddAction()
    {
        $json    = array('result' => 0);
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $data    = $request->getPost();
            $student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('reference', null));
            
            $params  = array(
                'SubjectID' => $data['subject'],
                'GradeID'   => $student->GradeID,
                'StudentUniqueRef' => $student->StudentUniqueRef
            );
            
            $result = $this->_api->addStudentSubject($params);
            
            if (true === $result->Status) {
                    
                $json['result']   = 1;
                $json['callback'] = 'subjectList()';
                    
            } else {

                $json['callback'] = 'displayMessage("' . $result->StatusMessage . '", "error");';
            
            }
            
        }
        
        $this->_helper->json($json);
    }
    
    public function subjectRemoveAction()
    {
        $json    = array('result' => 0);
        $student = $this->view->student = $this->_api->getChildForParent($this->_auth->UniqueRef, $this->getParam('reference', null));
        
        if (true !== $student->Status)
            goto out;
        
        $params  = array(
            'SubjectID' => $this->getParam('subject'),
            'StudentUniqueRef' => $student->StudentUniqueRef
        );
    
        $result = $this->_api->removeStudentSubject($params);
    
        if (true === $result->Status) {
    
            $json['result']   = 1;
            $json['callback'] = 'subjectList()';
            
        } else {
    
            $json['callback'] = 'displayMessage("' . $result->StatusMessage . '", "error");';
    
        }
        
        out:
        $this->_helper->json($json);
    }
    
}