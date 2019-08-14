<?php

class Biztech_News_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getNewsUrl(){
            return $this->_getUrl('news/index');
        }
    public function commentsPerPage($store = null)
        {
            $count = 10;

            if (!$count) {
                return self::DEFAULT_PAGE_COUNT;
            }

            return $count;
        }
        public function defaultPostSort($store = null)
        {
            return "ASC";
        }
        public function getEnabled()
        {
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
}