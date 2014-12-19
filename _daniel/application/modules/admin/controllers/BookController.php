<?php

class Admin_BookController extends App_Controller_Admin
{
    private $model_ebook;

    public function init()
    {
        $this->model       = new App_Model_Book();
        $this->model_ebook = new App_Model_BookDigital();
    }

    public function listAction()
    {
    	$search = trim(strip_tags($this->getParam('search', '')));
    	
    	if ($search) {
    	
            $select = $this->model->select();
        	$select
        		->setIntegrityCheck(false)
        		->from('book AS b')
        		->joinLeft('book_digital AS bd', 'bd.ebook_book = b.id', array('ebook_isbn_pdf', 'ebook_isbn_epub'))
        		->group('b.id')
        		->order('b.name ASC');
    
        	$search_isbn = preg_replace('/[^\d]/', '', $search);
        	
        	if (strlen($search_isbn) >= 13) {
            	$select->where('b.isbn = ? OR bd.ebook_isbn_pdf = ? OR ebook_isbn_epub = ?', $search);
        	} else {
        	    $select->where('b.name LIKE ?', '%' . $search .'%');
        	}
        	
        	$result = $this->model->fetchAll($select);
        	
        	if ($result->count()) {
                
        	    if ($result->count() === 1) {
        	        
        	        $item = $result->toArray()[0];
        	        $this->_helper->redirector('edit', null, null, array('id' => $item['id']));
        	        
        	    }
        	    
        	    $this->view->items = $result->toArray();
                
        	}
        	
    	}
    	
    	$this->view->searched = $search ? true : false;
    }
    
    public function addAction()
    {
    	$id 	= $this->getParam('id', null);
    	$item 	= null;
    	
    	if (null !== $id) {
    		
    		$item = $this->model->prepare($this->model->getById($id));
    		$item = $this->loadDigitalData($item);
    		
    		$this->view->item = $item;

    	}
    		
        $form 	 = new Admin_Form_Book();
        $form_e	 = new Admin_Form_BookDigital();

        $request = $this->getRequest();

        if ($item)
            $form->image->setRequired(false);        
        
        if ($request->isPost()) {
            
        	$data = $request->getPost();
        	
        	if (in_array(1, $data['format'])) {
        	    
        	    $form->price->setRequired(true);
        	    
        	}
        	
       		$this->setDigitalFormRequired($data, $form_e);
            
            if ($form->isValid($data) && $form_e->isValid($data)) {
            	
            	App_Util::removeCache('navigation');
            	
				// the new books id will be returned from this function call
				// if there is an error, the error message will be returned.
				
                $result = $book_id = $this->model->processForm($form, $item, array('files' => 'image'));
                
                if (!is_int($result))
                    goto fail;
                
                // We only want to save ebook data to the db if
                // some of it is actually set!
                
                $formats = $form->getValue('format');
                
                if (in_array(2, $formats) || in_array(3, $formats)) {
                	
                    $form_e->addElement('hidden', 'ebook_book', array('value' => $book_id));
                    
                    if (!in_array(2, $formats)) // PDF
                        $form_e->getElement('ebook_isbn_pdf')->setValue('');
                    
                    if (!in_array(3, $formats)) // ePub
                        $form_e->getElement('ebook_isbn_epub')->setValue('');                    
                    
                	// eBookResult is the same as book_id, the db id if successfully,
                	// and error message if false
                	
                	$result_e = $this->model_ebook->processForm($form_e, $item);
                	
                } else {
                	
                    $result_e = 0;
                    $this->model_ebook->delete(array('ebook_book = ?' => $book_id));
                
                }

                if (is_int($result) && is_int($result_e)) {

                    $this->model->updateIndex($result);
                    
                	if (!$item) {
						$this->_helper->flashMessenger(array('success' => 'item created successfully.'));
						$this->_helper->redirector('index');
					} else {
						$this->_helper->flashMessenger(array('success' => 'item updated successfully.'));
                        $this->_helper->redirector('edit', null, null, array('id' => $item['id']));    
					}
					
				} else {
					
					// If either of the results are not ints, we will display the error message
					// to the user
					
				    fail:
				    
					if (!is_int($result))
						$this->_helper->flashMessenger(array('error' => $result));
					
					if (!is_int($result_e))
						$this->_helper->flashMessenger(array('error' => $result_e));
					
				}
				
			} else {
			    
			    $form->populate($data);
			    $form_e->populate($data);
			    
			    $this->_helper->flashMessenger(array('error' => 'The form below contains errors. Please rectify and try again.'));
			    
// 			    Zend_Debug::dump($form->getMessages());
// 			    Zend_Debug::dump($form_e->getMessages());
// 			    exit;
			    
			} 
			
        } else {
            
        	if ($item) {
            	$form->populate($item);
            	$form_e->populate($item);
            }
            
        }
        
    	$this->view->action = $item ? $item['name'] : 'Create';
    	$this->view->form 	= $form;
    	$this->view->form_e = $form_e;
        
        $this->renderScript('book/form.phtml');
        
    }    
    
    public function deleteAction()
    {
    	$id = $this->getParam('id', null);
    	
    	$result = $this->ebookModel->delete(array('ebook_book = ?' => $id));
    	$result = $this->model->deleteItem($id);
    	 
    	if (true !== $result)
    		$this->_helper->flashMessenger(array('error' => $result));
    	 
    	$this->_helper->redirector('index');    	
    }
    
    /*
     * Make inputs on the ebook form required or not based on the formats
     * selected.
     */
    private function setDigitalFormRequired($post, $form)
    {
    	if (!isset($post['format']))
    		return;
    		
    	$model = new App_Model_Format();
    	
    	// Get the rows for each of the formats from the db
    	
    	$formats 	 = $model->getIn($post['format']);
		$flatFormats = array();
		
    	// Modify the formats so that the in_array function can be used
    	// to check if a certain format is in the array
    	
    	foreach ($formats as $k => $v) {
    		$flatFormats[$k] = $v['name'];
    	} unset($formats);
    	
    	// Now we can check to see which formats are set and modify
    	// the ebook form accordingly.
    	
    	$flag = false;
    	
    	if (in_array('PDF', $flatFormats)) {
    		$form->getElement('ebook_isbn_pdf')->setRequired(true);
    		$flag = true;
    	}
    	
    	if (in_array('ePub', $flatFormats)) {
    		$form->getElement('ebook_isbn_epub')->setRequired(true);
    		$flag = true;
    	}
    	
    	if ($flag) {
    		$form->getElement('ebook_price')->setRequired(true);
    		$form->getElement('ebook_active')->setRequired(true);
    	}
    }
    
    /*
     * Inspect the books format, if required, load the ebook data from the ebook table.
     */
    private function loadDigitalData($book)
    {
    	if (in_array('PDF', $book['format_name']) || in_array('ePub', $book['format_name'])) {
    		
    	    $ebook = $this->model_ebook->getByBook($book['id']);
    		
    		if ($ebook !== false)
				$book = array_merge($book, $ebook);
    	}
    	
    	return $book;
    }
    
}