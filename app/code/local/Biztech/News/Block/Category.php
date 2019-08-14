<?php

class Biztech_News_Block_Category extends Mage_Core_Block_Template {

    public function _prepareLayout() {

        return parent::_prepareLayout();
    }

    public function getCategory() { // returns all categories
        $store = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('news/newscategory')->getCollection();
        $collection->getSelect()->join($prefix . 'news_category_data', 'main_table.category_id =' . $prefix . 'news_category_data.category_id AND ' . $prefix . 'news_category_data.store_id = ' . $store .' AND ' . $prefix . 'news_category_data.status = 1'
                ,array('name', 'store_id', 'status'));
                
        $newsCategory = $collection->getData();
        return $newsCategory;
    }
    
   public function getCategorisedNews($id) //returns news count for single category
    {
        $store  = Mage::app()->getStore()->getId();
        $newsCategory = Mage::getModel("news/newscategory");
        $categoryId = $id;
        $newsCategory->load($categoryId,"category_id");

        $catgorisedNews = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('store_id',$store)
                ->addFieldToFilter('category_id',array('finset' => $categoryId))->addFieldToFilter('status' , array('eq' => 1));
 
        $catgorisedNews->addFieldToFilter("date_to_unpublish",array("gteq"=>date('Y-m-d 00:00:00')))->addFieldToFilter("date_to_publish",array("lt"=>date('Y-m-d 23:59:59')))->getData();
        
        return count($catgorisedNews);
    }
}
