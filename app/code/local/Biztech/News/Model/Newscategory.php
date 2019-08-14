<?php

class Biztech_News_Model_Newscategory extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('news/newscategory');
    }     
}