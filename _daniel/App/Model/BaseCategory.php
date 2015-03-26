<?php

class App_Model_BaseCategory extends App_Model_Base
{
    
    protected $_uri = 'name';
    
    public function getMaxDepth()
    {
        return $this->_depth;
    }
    
    public function getChildren($id = null, array $params = array())
    {
        $select = $this->select();
        $select
            ->from($this->_name . ' AS p', '*')
            ->order(isset($params['order']) ? $params['order'] : 'p.name');
        
        if ($this->store_id)
            $select->where('p.store = ?', $this->store_id);
        
        if ($id) {
            
            $select->where('p.parent = ?', $id);
            
        } else {
            
            $select->where('p.parent IS NULL');
            
        }
        
        $result = $this->fetchAll($select);
        return $result->count() ? $result->toArray() : false;    
    }

    public function getTree($id = null, array $params = array())
    {
        $tree     = array();
        $children = $this->getChildren($id, $params);

        if ($children) {
            
            foreach ($children as &$child) {
                
               $_children = $this->getTree($child['id'], $params);
               
               if ($_children)
                   $child['children'] = $_children;
                
               $tree[] = $child;
                
            } unset($child);
            
        }
        
        return $tree;
    }    
    
    public function getParents($id)
    {
        $items = array();
        $item  = $this->getById($id);
    
        while ($item['parent']) {
            $item = $this->getById($item['parent']);
            $items[] = $item;
        }
    
        return array_reverse($items);
    }    
    
    public function getDepth($id)
    {
        $item  = $this->getById($id);
        $depth = 0;
        
        while ($item['parent']) {
            $depth++;
            $item = $this->getById($item['parent']);            
        }

        return $depth;
    } 

}