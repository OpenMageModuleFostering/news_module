<?php
    class Biztech_News_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
        public function __construct()
        {
            $this->_controller = 'adminhtml_category';
            $this->_blockGroup = 'news';
            $this->_headerText = Mage::helper('news')->__('Category Manager');
            $this->_addButtonLabel = Mage::helper('news')->__('Add Category');
            if(Mage::getStoreConfig('news/news_general/enabled') == 1){
                parent::__construct();
            }
            else{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('News extension is not enabled. Please enable it from System > Configuration.'));
            }
        }
        public function _prepareLayout()
        {
            return parent::_prepareLayout();
        }

        public function getNewsCategory()     
        { 
            if (!$this->hasData('newscategory')) {
                $this->setData('newscategory', Mage::registry('newscategory'));
            }
            return $this->getData('newscategory');

        }

        public function getPreparedCollection()
        {
            return $this->_prepareCollection();
        }
        protected function _prepareCollection()
        {
            $collection=Mage::getModel('news/newscategory')->getCollection()->addFieldToFilter("status",array("eq"=>1));

            $collection->setPageSize(10);

            if ($this->getRequest()->getParam('p') > 0)
                $collection->setCurPage($this->getRequest()->getParam('p'));

            $this->setData('cached_collection', $collection);

            return $this->getData('cached_collection');
        }
}