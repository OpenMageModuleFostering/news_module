<?php

class Biztech_News_Model_Mysql4_Newscategorydata extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('news/newscategorydata', 'data_id');
    }
}
