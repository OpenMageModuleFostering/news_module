<?php
class Biztech_News_Block_News extends Mage_Core_Block_Template
{
	public function _prepareLayout()
        {

            return parent::_prepareLayout();
        }

        public function getNews()     
        { 
            $id = $this->getRequest()->getParam("id");
            $news =  Mage::getModel('news/news')->load($id);
            return $news;

        }

        protected function _beforeToHtml()
        {
            Mage::helper('news/toolbar')->create($this, array(
                    'default_order' => 'created_time',
                    'dir' => 'asc',
                    'limits' => Mage::helper('news')->commentsPerPage(),
                )
            );

            return $this;
        }

        public function getPreparedCollection()
        {
            return $this->_prepareCollection();
        }
        protected function _prepareCollection()
        {

            $collection=Mage::getModel('news/news')->getCollection()->addFieldToFilter("status",array("eq"=>1))->addFieldToFilter("date_to_unpublish",array("gteq"=>date('Y-m-d 00:00:00')))->addFieldToFilter("date_to_publish",array("lt"=>date('Y-m-d 23:59:59')));

            $collection->setPageSize(10);

            if ($this->getRequest()->getParam('p') > 0)
                $collection->setCurPage($this->getRequest()->getParam('p'));

            $this->setData('cached_collection', $collection);


            return $this->getData('cached_collection');
        }

}