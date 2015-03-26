<?php

class App_Item
{
    
    private $adapter, $languages;
    
    public function __construct()
    {
        $this->adapter   = Zend_Db_Table::getDefaultAdapter();
        $this->languages = App_Util::getLanguages();
    }
    
    public function getBooks(array $params = array(), $return_select = false)
    {
        $select = $this->adapter->select();
        $select
            ->from(array('b' => 'book'), array())
            ->joinLeft(array('bc' => 'book_category'), 'bc.book = b.id', array())
            ->joinLeft(array('bd' => 'book_digital'), 'bd.ebook_book = b.id', array())
            ->where('b.active = ?', 1)
            ->group('b.id')
            ->having('@price > 0');

        $select->columns(array('b.id', 'b.uri', 'b.name', 'b.image', 'b.image_url', 'b.description', new Zend_Db_Expr('"book" AS type'), 'bc.category'));
        
        if (isset($params['ebook'])) {
            
            $select->columns('@price := (bd.ebook_price) AS price');
            $select->where('bd.ebook_book IS NOT NULL');
            
        } else {
            
            $price  = '@price := IF ((SELECT bf.format FROM book_format AS bf WHERE bf.book = b.id AND bf.format = 1), b.price, bd.ebook_price) AS price';
            $select->columns($price);
            
        }
        
        if ($this->languages) {
            
            $cols = array_map(function($item) { 
                foreach ($this->languages as $iso_2)
                    return $item . '_' . $iso_2;
            }, array('description'));
            
            $select->columns($cols);
            
            // add specific product cols that this table does not have
            
            foreach ($this->languages as $iso_2) {
                foreach (array('name', 'uri') as $extra)
                    $select->columns(new Zend_Db_Expr($extra . ' AS ' . $extra . '_' . $iso_2));
            }
                
        }
        
        if (isset($params['featured']))
            $select->where('b.featured = ?', $params['featured']);
        
        if (isset($params['special']))
            $select->where('b.special = ?', $params['special']);        
        
        if (isset($params['category'])) {
            
            $category = $params['category'];
                
            if (is_array($category))
                $select->where('bc.category IN (?)', $category);
            
        }
        
        if (isset($params['order']))
            $select->order($params['order']);
        
        if (isset($params['limit']))
            $select->limit($params['limit']);
        
        if (true === $return_select)
            return $select;
        
        $result = $this->adapter->fetchAll($select);
        return $result ? $result : false;
    }
    
    public function getProducts(array $params = array(), $return_select = false)
    {
        $select = $this->adapter->select();
        $select
            ->from(array('p' => 'product'), array())
            ->joinLeft(array('pc' => 'product_category'), 'pc.product = p.id', array())
            ->where('p.active = ?', 1)
            ->group('p.id');

        $select->columns(array('p.id', 'p.uri', 'p.name', 'p.image', new Zend_Db_Expr('NULL AS image_url'), 'p.description', new Zend_Db_Expr('"product" AS type'), 'pc.category', 'p.price'));    
            
        if ($this->languages) {
            
            $cols = array_map(function($item) { 
                foreach ($this->languages as $iso_2)
                    return $item . '_' . $iso_2;
            }, array('description', 'name', 'uri'));
            
            $select->columns($cols);
                
        }        
        
        if (isset($params['featured']))
            $select->where('p.featured = ?', $params['featured']);        
        
        if (isset($params['special']))
            $select->where('p.special = ?', $params['special']);        
        
        if (isset($params['category'])) {
        
            $category = $params['category'];
        
            if (is_array($category))
                $select->where('pc.category IN (?)', $category);
        
        }        
        
        if (isset($params['order']))
            $select->order($params['order']);
        
        if (isset($params['limit']))
            $select->limit($params['limit']);
        
        if (true === $return_select)
            return $select;
        
        $result = $this->adapter->fetchAll($select);
        return $result ? $result : false;       
    }
    
    public function getForParams(array $params = array())
    {
        $s1 = $this->getBooks($params, true);
        $s2 = $this->getProducts($params, true);
        
        $select = $this->adapter->select();
        $select->union(array($s1, $s2), 'UNION ALL');

        if (isset($params['order_union']))
            $select->order($params['order_union']);
        
        if (isset($params['limit_union']))
            $select->limit($params['limit_union']);
        
        return $this->adapter->fetchAll($select);
    }
    
}