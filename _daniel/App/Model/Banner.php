<?php

class App_Model_Banner extends App_Model_Base
{
	
	protected $_name  = 'banner';
	protected $_media = 'media/banner/';
	
	public function deleteItem($id)
	{
		$item = $this->getById($id);

		if (true === parent::deleteItem($id)) {
			
			App_Util::deleteFile($this->_media . $item['image']);
			return true;
			
		}
		
		return false;
	}
	
}