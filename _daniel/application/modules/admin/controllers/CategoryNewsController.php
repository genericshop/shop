<?php

class Admin_CategoryNewsController extends App_Controller_Admin
{

    public function init()
    {
        $this->model = new App_Model_CategoryNews();
    }

    public function listAction()
    {
        $parent    = $this->view->parent  = $this->model->getById($this->getParam('parent', null));
        $parents   = $this->view->parents = $this->model->getParents($parent['id']);
        
        $depth     = $parent ? $this->model->getDepth($parent['id']) : 0;
        $depth_max = $this->model->getMaxDepth();
        
        $this->view->items  = $this->model->getChildren($parent ? $parent['id'] : null);
        $this->view->create = $parents && count($parents) >= ($depth_max) ? false : true;
        $this->view->follow = $depth >= $depth_max - 1 ? false : true;
    }

    public function addAction()
    {
        $item = $this->view->item = $this->getParam('id', null) ? $this->model->getById($this->getParam('id')) : null;

        $form  	 = new Admin_Form_CategoryNews();
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();

            if ($form->isValid($data)) {

                $result = $this->model->processForm($form, $item);

                if (true === $result) {

                	App_Util::removeCache('navigation');
                	
                    if (!$item) {

                        $this->_helper->flashMessenger(array('success' => 'item created successfully.'));
                        $this->_helper->redirector('index');

                    } else {

                        $this->_helper->flashMessenger(array('success' => 'item updated successfully.'));
                        $this->_helper->redirector('edit', null, null, array('id' => $item['id']));

                    }

                } else {

                    $this->_helper->flashMessenger(array('error' => $result));

                }

            }
        } else {
           
            if ($item)
                $form->populate($item);
            else 
                $form->populate(array('parent' => $this->getParam('parent', null)));
        }

        $this->view->action = $item ? $item['name'] : 'Create';
        $this->view->form 	= $form;

        $this->renderScript('category-news/form.phtml');

    }

    public function deleteAction()
    {
        $result = $this->model->deleteItem($this->getParam('id', null));
    
        if (true !== $result)
            $this->_helper->flashMessenger(array('error' => $result));
    
        if ($this->getParam('parent', null))
            $this->_helper->redirector('list', null, null, array('parent' => $this->getParam('parent')));
    
        $this->_helper->redirector('index');
    }    
    
    public function sortAction()
    {
    	$json	= array('result' => 0);
    	$order 	= $this->getParam('ord', null);
    	$parent	= $this->getParam('parent', null);
    
    	if (null !== $order) {
    		
    	    foreach ($order as $pos => $id)
    			$this->model->update(array('position' => $pos + 1), 'id = ' . (int)$id . (null !== $parent ? ' AND parent = ' . $parent : null));
    		 
    		$json['result']	= 1;
    		
    	}
    
    	$this->_helper->json($json);
    }    
    
}