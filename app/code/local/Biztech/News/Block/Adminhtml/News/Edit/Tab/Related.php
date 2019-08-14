<?php
   
    class Biztech_News_Block_Adminhtml_News_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid {

        public function __construct() {
            parent::__construct();
            $this->setId('relatedGrid');
            $this->setUseAjax(true);
            $this->setDefaultSort('news_id');
            $this->setDefaultFilter(array('related_news' => 1));
            $this->setDefaultDir('DESC');
            $this->setSaveParametersInSession(false);
        }


        protected function _addColumnFilterToCollection($column) {
            if ($column->getId() == 'related_news') {

                $newsIds = $this->_getSelectedNews();
                if (empty($newsIds)) {
                    $newsIds = 0;
                }
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('news_id', array('in' => $newsIds));
                } elseif (!empty($newsIds)) {
                    $this->getCollection()->addFieldToFilter('news_id', array('nin' => $newsIds));
                }
            } else {
                parent::_addColumnFilterToCollection($column);
            }
            return $this;
        }

        protected function _prepareCollection() {
           
            $news = $this->_getSelectedNews();
            $store = $this->getRequest()->getParam('store', 0);
            $prefix = Mage::getConfig()->getTablePrefix();
            $collection = Mage::getModel('news/newsdata')->getCollection()
                            ->addFieldToSelect('title')
                            ->addFieldToSelect('news_id')
                            ->addFieldToFilter('news_id' , array("neq" => $this->getRequest()->getParam('id')))
                            ->addFieldToFilter('store_id' , $store);
            
            $this->setCollection($collection);
            
            
            
            
            if (!Mage::registry('news')) {
                $newsIds = $this->_getSelectedNews();
                if (empty($newsIds)) {
                    $newsIds = 0;
                }
                $collection->addFieldToFilter('news_id', array('in' => $newsIds));
            }

            return parent::_prepareCollection();
        }


        protected function _prepareColumns() {

            $this->addColumn('related_news', array(
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'related_news[]',
                    'onclick' => 'test_news()',
                    'values' => $this->_getSelectedNews(),
                    'align' => 'center',
                    'index' => 'news_id',
                    'field_name' => 'related_news[]', 
                    
                ));


            $this->addColumn('news_id', array(
                    'header' => Mage::helper('news')->__('ID'),
                    'sortable' => true,
                    'width' => '60px',
                    'index' => 'news_id'
                ));
            $this->addColumn('relatednews_title', array(
                    'header' => Mage::helper('news')->__('Title'),
                    'index' => 'title',
                    'sortable' => true
                ));

            return parent::_prepareColumns();
        }

        public function getGridUrl() {
            return $this->getUrl('*/*/grid', array('_current' => true));
        }


        protected function _getNews() {
            return Mage::registry('news_data');
        }

        protected function _getSelectedNews() {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            $news = $this->getRequest()->getPost('related_news');
            $related_news = Mage::getModel('news/newsdata')->getCollection()
            ->addFieldToFilter('news_id', (int) $this->getRequest()->getParam('id'))
            ->addFieldToFilter('store_id', (int) $this->getRequest()->getParam('store'))
            ->getFirstItem()
            ->getData();

            $sel_news = explode(",", $related_news['related_news']);
            if (!is_null($news)) {
                $sel_news = array_merge($news, $sel_news);
            }
            return $sel_news;
        }

        public function getRelNews() {
            
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            $news = $this->getRequest()->getPost('related_news');

            $sel_news = '';
            $related_news = Mage::getModel('news/newsdata')->getCollection()
            ->addFieldToFilter('news_id', (int) $this->getRequest()->getParam('id'))
            ->addFieldToFilter('store_id', (int) $this->getRequest()->getParam('store'))
            ->getFirstItem()
            ->getData();
            
            
            $sel_news = explode(",", $related_news['related_news']);    
            if (!is_null($news)) {
                $sel_news = array_merge($news, $sel_news);
            }
            return $sel_news;

        }

    }

?>
