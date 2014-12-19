<?php

class App_Model_Book extends App_Model_Base
{
	
	protected $_name  = 'book';
	protected $_uri	  = 'name';
	protected $_media = 'media/book/';
	protected $_index = 'book'; 
	
	public function updateIndex($id = null, $reset = false)
	{
	    $index = $this->getIndex($reset);
	    
	    if ($id)
	        $this->deleteIndex($id);
	        
	    $params = array('active' => 1);
	    
	    if ($id)
	        $params['id'] = $id;
	    
	    $items     = $id ? array($this->getForParams($params)) : $this->getForParams($params);
	    $languages = App_Util::getLanguages();
	    
	    $filter = new Zend_Filter();
	    $filter
	       ->addFilter(new Zend_Filter_StripTags())
	       ->addFilter(new Zend_Filter_StringTrim());
	       //->addFilter(new Zend_Filter_HtmlEntities());

        foreach ($items as $item) {
        
            $category_id   = explode(',', $item['category_id']);
            $category_name = explode(',', $item['category_name']);
            $category_uri  = explode(',', $item['category_uri']);
            
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::Keyword('uid', $item['id']));
            
            $doc->addField(Zend_Search_Lucene_Field::Text('name', $item['name']));
            $doc->addField(Zend_Search_Lucene_Field::Text('category', implode(' ', $category_name)));
            $doc->addField(Zend_Search_Lucene_Field::Text('description', $filter->filter($item['description'])));
            
            $uri = 'en/catalogue/' . $category_uri[0] . '/' . $item['uri'] . '/' . $category_id[0] . '/' . $item['id'];
            
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('uri', $uri));
            
            if ($languages) {
            
               foreach ($languages as $iso2) {
                   
                    $category_name = explode(',', $item['category_name_' . $iso2]);
                    $category_uri  = explode(',', $item['category_uri_' . $iso2]); 
                   
                    $doc->addField(Zend_Search_Lucene_Field::Text('name_' . $iso2, $item['name']));
                    $doc->addField(Zend_Search_Lucene_Field::Text('category_' . $iso2, implode(' ', $category_name)));
                    $doc->addField(Zend_Search_Lucene_Field::Text('description_' . $iso2, $filter->filter($item['description_' . $iso2])));
                    
                    $uri = $iso2 . '/catalogue/' . $category_uri[0] . '/' . $item['uri'] . '/' . $category_id[0] . '/' . $item['id'];
                     
                    $doc->addField(Zend_Search_Lucene_Field::unIndexed('uri_' . $iso2, $uri));
                    
               }
               
            }
           
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('type', 'book'));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('price', $item['price'] ? $item['price'] : $item['ebook_price']));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('image', $item['image']));
            $doc->addField(Zend_Search_Lucene_Field::unIndexed('image_url', $item['image_url']));
        
            $index->addDocument($doc);
           	
        }
        
        $index->commit();
        
        if (true === $reset)
           $index->optimize();	   
	}
	
	public function getByIsbn($isbn)
	{
        $select = $this->select();
        $select->where('isbn = ?', $isbn);
    
        $result = $this->fetchRow($select);
        return $result ? $result->toArray() : false;	    
	}
	
	public function getForParams(array $params = array())
	{
		$select = $this->select()->setIntegrityCheck(false);
		$select
            ->from(array('b'  => $this->_name), '*')
            ->join(array('bc' => 'book_category'), 'bc.book = b.id', '')
            ->join(array('bf' => 'book_format'), 'bf.book = b.id', '')
            ->joinLeft(array('bd' => 'book_digital'), 'bd.ebook_book = b.id', '*')
            ->join(
                array('c'  => 'category'), 
                'bc.category = c.id', 
                array(
                    'GROUP_CONCAT(DISTINCT c.id ORDER BY c.id) AS category_id', 
                    'GROUP_CONCAT(DISTINCT c.name ORDER BY c.id) AS category_name', 
                    'GROUP_CONCAT(DISTINCT c.name_af ORDER BY c.id) AS category_name_af',
                    'GROUP_CONCAT(DISTINCT c.uri ORDER BY c.id) AS category_uri',
                    'GROUP_CONCAT(DISTINCT c.uri_af ORDER BY c.id) AS category_uri_af',
                )
            )
            ->join(array('f' => 'format'), 'bf.format = f.id', 'GROUP_CONCAT(DISTINCT f.name) AS format')
            ->where('b.active = ?', 1)
            ->group('b.id');
		
		if (isset($params['id']))
		    $select->where('b.id = ?', $params['id']);
		
// 		if (isset($params['active']))
// 		    $select->where('b.active = ?', $params['active']);

		$result = $this->fetchAll($select);
		
		if ($result->count()) {
		    
		    $result = $result->toArray();
		    return isset($params['id']) ? $result[0] : $result;
		    
		}
		
		return false;
	}
	
	public function processForm(Zend_Form $form, $item = null, $uploads = array())
	{
		$values = $this->processFormUploads($form, $item, $uploads);
		
		if ($item) {
			
			$result = $this->updateItem($values, $item['id']);

			if (true !== $result)
				return $result;
			
			$item_id = (int)$item['id'];
			
		} else {
			
		    $item_id = $this->createItem($values, true);
		
		}

		if (is_int($item_id)) {
			
			$model = new App_Model_BookAuthor();
			$model->delete(array('book = ?' => $item_id));
			
			if ($form->getValue('author')) {
			
    			foreach ($form->getValue('author') as $id)
    				$model->createItem(array('book' => $item_id, 'author' => $id));
    			
			}

            $model = new App_Model_BookCategory();
            $model->delete(array('book = ?' => $item_id));

            if ($form->getValue('category')) {
            
                foreach ($form->getValue('category') as $id)
                    $model->createItem(array('book' => $item_id, 'category' => $id));
            
            }

            $model = new App_Model_BookFormat();
            $model->delete(array('book = ?' => $item_id));

            if ($form->getValue('format')) {
            
                foreach ($form->getValue('format') as $id)
                    $model->createItem(array('book' => $item_id, 'format' => $id));
            
            }
            
		}
		
		return $item_id;
	}

	public function deleteItem($id)
	{
	    $result = parent::deleteItem($id);
	
	    if (true === $result) {
	        	
	        $model = new App_Model_BookAuthor();
	        $model->delete(array('book = ?' => $id));
	        	
	        $model = new App_Model_BookFormat();
	        $model->delete(array('book = ?' => $id));
	        	
	        $model = new App_Model_BookCategory();
	        $model->delete(array('book = ?' => $id));
	        	
	        return true;
	    }
	
	    return $result;
	}	
	
    public function prepare($item)
    {
        $item['category']      = $this->prepareCategoriesId($item['id']);
        $item['format']        = $this->prepareFormatsId($item['id']);
        $item['author']        = $this->prepareAuthorsId($item['id']);

        $item['category_name'] = $this->prepareCategoriesName($item['id']);
        $item['format_name']   = $this->prepareFormatsName($item['id']);
        $item['author_name']   = $this->prepareAuthorsName($item['id']);

        return $item;
    }

    public function prepareList($items)
    {
        foreach ($items as &$item) {
            $item = $this->prepare($item);
        }
        return $items;
    }    
    
    private function prepareCategoriesId($id)
    {
        $result = array();
        $model  = new App_Model_BookCategory();
        $items  = $model->getByBook($id);
        
        foreach ($items as $item)
            $result[] = $item['category'];
        
        return $result;
    }

    private function prepareFormatsId($id)
    {
        $result = array();
        $model  = new App_Model_BookFormat();
        $items  = $model->getByBook($id);
        
        if ($items) {
        
            foreach ($items as $item)
                $result[] = $item['format'];
        
        }
        
        return $result;
    }

    private function prepareAuthorsId($id)
    {
        $result = array();
        $model  = new App_Model_BookAuthor();
        $items  = $model->getByBook($id);
        
        foreach ($items as $item)
            $result[] = $item['author'];
        
        return $result;
    }

    private function prepareCategoriesName($id)
    {
        $result = array();
        $model  = new App_Model_BookCategory();
    	$items  = $model->getCategoriesByBook($id);
    	
    	if ($items) {
    	
        	foreach ($items as $item)
        		$result[] = $item['name'];
    	
    	}
    	
    	return $result;
    }    
    
    private function prepareAuthorsName($id)
    {
        $result = array();
        $model  = new App_Model_BookAuthor();
        $items  = $model->getAuthorsByBook($id);
        
        foreach ($items as $item)
            $result[] = $item['name'];
        
        return $result;
    }

    private function prepareFormatsName($id)
    {
        $result = array();
        $model  = new App_Model_BookFormat();
    	$items  = $model->getFormatsByBook($id);
    	
    	if ($items) {
    	
        	foreach ($items as $item)
        		$result[] = $item['name'];
    	
    	}
    	
    	return $result;
    }    
    
}