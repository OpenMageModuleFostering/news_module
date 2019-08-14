<?php
    class Biztech_News_Block_Adminhtml_News_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid {

        public function __construct() {
            parent::__construct();
            $this->setId('news_news_products');
            $this->setDefaultSort('news_id');
            $this->setUseAjax(true);
        }

        public function getFile() {

            if (Mage::registry('news'))
                return Mage::registry('news');
            else
                return Mage::getModel('news/news')->load((int) $this->getRequest()->getParam('id', 0));
        }

        protected function _addColumnFilterToCollection($column) {

            // Set custom filter for in category flag
            if ($column->getId() == 'in_file') {
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

            $store = $this->getRequest()->getParam('store', 0);
            $prefix = Mage::getConfig()->getTablePrefix();
            $collection = Mage::getModel('news/newsdata')->getCollection();
            $collection->addFieldToFilter('news_id' , array("neq" => $this->getRequest()->getParam('id')));
            $collection->addFieldToFilter('store_id' , $store);
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
                          
            if ($this->getFile()->getId()) {
                $this->setDefaultFilter(array('in_file' => 1));
            }
            $this->addColumn('in_file', array(
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_file',
                    'values' => $this->_getSelectedNews(),
                    'align' => 'center',
                    'index' => 'news_id',
                    'filter_index' => 'news_id'
                ));

            $this->addColumn('news_id', array(
                    'header' => Mage::helper('news')->__('ID'),
                    'sortable' => true,
                    'width' => '60px',
                    'index' => 'news_id',                    
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

        protected function _getSelectedNews() {

            $news = $this->getRequest()->getPost('selected_news');
            $files = Mage::getSingleton('core/resource')->getConnection('core_read')->query('SELECT news_id FROM ' . 
                    Mage::getSingleton('core/resource')->getTableName('newsdata') . 
                    ' WHERE news_id="' . (int) $this->getRequest()->getParam('id', 0) . '"
                    AND store_id="'.(int) $this->getRequest()->getParam('store', 0).'" ');
            $array = array();
            foreach ($files as $file) {
                $array[] = $file['related_news'];
            }
            return $array;
        }

    }
