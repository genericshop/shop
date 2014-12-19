<?php

class User_AccountController extends Zend_Controller_Action
{
    
    private $model, $user;
    
    public function init()
    {
    	$this->model = new User_Model_User();
    	$this->user  = $this->model->getById(Zend_Auth::getInstance()->getIdentity()->id);  
    }
    
    public function indexAction()
    {
    }
    
    public function profileAction()
    {
        $form    = new User_Form_User();
        $form_a  = new User_Form_Address();
        
        $epos = 155;
        
        foreach ($form_a->getElements() as $element) {
            $element->setOrder($epos+=10);
            $form->addElement($element);
        }
        
        $description = $this->view->translate('Only required if you want to change your existing password');
        
        $form->removeElement('emailc');
        $form->password->setDescription($description)->setRequired(false)->setIgnore(true);
        $form->passwordc->setDescription($description)->setRequired(false)->setIgnore(true);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            
            $data = $request->getPost();
            
            if ($form->isValid($data)) {
                
                $result = $this->model->processProfile($form, $this->user);
                
                if (true === $result) {
                    $this->_helper->flashMessenger(array('success' => $this->view->translate('Your profile was updated successfully.')));
                    $this->_helper->redirector->gotoRoute(array(), 'account-profile');
                }
                
                $this->_helper->flashMessenger(array('error' => $this->view->translate('An unknown error occurred, please call us for further assistance.')));
                
            }
            
        } else {
            
            $form->populate($this->user);
            
        }
        
        $form->submit->setLabel($this->view->translate('Update Profile'))->setOrder($epos + 10);
        $this->view->form = $form;
    }

    public function historyAction()
    {
        $model = new App_Model_Order();
        $this->view->items = $model->getByUser($this->user['id']);
    }
    
    public function historyItemAction()
    {
        
    }
    
    public function bookAction()
    {
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $select  = $adapter->select();        
        
        $select
            ->from('order AS o', array())
            ->joinInner('order_item AS oi', 'oi.order = o.id', array('item_name', 'item_format', 'download_link'))
            ->where ('o.user = ?', $this->user['id'])
            ->where('o.status IN (?)', array(1,2))
            ->where('o.deleted = ?', 0)
            ->where('oi.item_type = ?', 'book')
            ->where('oi.item_format IN (?)', array('pdf', 'epub'))
            ->where('oi.download_link IS NOT NULL')
            ->order('oi.item_name');

        $items = $this->view->items = $adapter->fetchAll($select);
    } 
	public function processProfile(Zend_Form $form, $item)
	{
		$values = $form->getValues();
		
		if ($values['email'] !== $item['email'] && $item['role'] !== 'admin') {
			
			if ($this->getByUsername($values['email']))
				return 'An account for "' . $values['email'] . '" already exists.';
			
			$values['username'] = $values['email'];
			
		}
		
		if (isset($values['password'])) {
			
			$values['salt'] 	= $this->generateSalt();
			$values['password']	= sha1($values['password'] . $values['salt']);
			
		}
		
		//return $this->updateItem($values, $item['id']);
		return true;
	}  
    
}