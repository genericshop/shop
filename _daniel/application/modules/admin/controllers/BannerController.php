<?php

class Admin_BannerController extends App_Controller_Admin
{

    public function init()
    {
        $this->model = new App_Model_Banner();
    }

    public function listAction()
    {
        $this->view->items = $this->model->fetchAll()->toArray();
    }

    public function addAction()
    {
        $item = $this->view->item = $this->getParam('id', null) ? $this->model->getById($this->getParam('id')) : null;

        $form  	 = new Admin_Form_Banner();
        $request = $this->getRequest();

        if ($item)
            $form->image->setRequired(false);
        
        if ($request->isPost()) {

            $data = $request->getPost();

            if ($form->isValid($data)) {

                $result = $this->model->processForm($form, $item, array('files' => 'image'));

                if (true === $result) {

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
           
        }

        $this->view->action = $item ? $item['name'] : 'Create';
        $this->view->form 	= $form;

        $this->renderScript('banner/form.phtml');

    }

}