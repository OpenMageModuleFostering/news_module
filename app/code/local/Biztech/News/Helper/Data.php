<?php

    class Biztech_News_Helper_Data extends Mage_Core_Helper_Abstract
    {
        public function getNewsUrl(){
            return $this->_getUrl('news/index');
        }
        
        public function commentsPerPage($store = null){
            
            $count = '10,20,30,50';
            if (!$count) {
                return self::DEFAULT_PAGE_COUNT;
            }

            return $count;
        }

        public function defaultPostSort($store = null){
            return "ASC";
        }

        public function getEnabled(){
            return true;
        }


        public function filterWYS($text)
        {
            $processorModelName = version_compare(Mage::getVersion(), '1.3.3.0', '>') ? 'widget/template_filter' : 'core/email_template_filter';
            $processor = Mage::getModel($processorModelName);
            if ($processor instanceof Mage_Core_Model_Email_Template_Filter) {
                return $processor->filter($text);
            }
            return $text;
        }

        public function getNewsCollection($cid)
        {
            
            $store  = Mage::app()->getStore()->getId();
            $prefix = Mage::getConfig()->getTablePrefix();
            $collection = Mage::getModel('news/news')->getCollection();
            $collection->getSelect()->join($prefix.'news_data', 'main_table.news_id ='.$prefix.'news_data.news_id AND '.$prefix.'news_data.store_id = '.$store.' AND '.$prefix.'news_data.status = 1',array('status','title','news_content','intro','store_id','date_to_publish','date_to_unpublish','browser_title','seo_keywords','seo_description','category_id'));

            $collection->getSelect()->join($prefix.'news_category_data', $prefix.'news_category_data.category_id IN ('.$prefix.'news_data.category_id) AND '.$prefix.'news_category_data.store_id = '.$store.' AND '.$prefix.'news_category_data.status = 1');

            if($cid)
            {
                $collection->addFieldToFilter($prefix.'news_data.category_id' , array('finset'=> $cid));
            }

            $collection->addFieldToFilter("date_to_unpublish",array("gteq"=>date('Y-m-d 00:00:00')))
                        ->addFieldToFilter("date_to_publish",array("lt"=>date('Y-m-d 23:59:59')));

            return $collection;
        }

}