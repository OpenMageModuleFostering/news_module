<?php

class Biztech_News_Block_News extends Mage_Core_Block_Template {

    public function _prepareLayout() {

        return parent::_prepareLayout();
    }

    public function getNews() {
        $store = Mage::app()->getStore()->getId();
        $news = Mage::getModel("news/news");
        $newsId = $this->getRequest()->getParam("id");
        $news->load($newsId, "news_id");

        $model_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('store_id', $store)->addFieldToFilter('news_id', $newsId)
                ->getData();


        $text_data = $model_text[0];
        $news = $news->setTitle($text_data['title'])
                ->setNewsContent($text_data['news_content'])
                ->setStatus($text_data['status'])
                ->setIntro($text_data['intro'])
                ->setDateToPublish($text_data['date_to_publish'])
                ->setDateToUnpublish($text_data['date_to_unpublish'])
                ->setBrowserTitle($text_data['browser_title'])
                ->setSeoKeywords($text_data['seo_keywords'])
                ->setSeoDescription($text_data['seo_description'])
                ->setRelatedNews($text_data['related_news'])
                ->setCategoryId($text_data['category_id']);

        $this->setData('news', $news);

        return $news;
    }

    protected function _beforeToHtml() {
        Mage::helper('news/toolbar')->create($this, array(
            'default_order' => 'created_time',
            'dir' => 'asc',
            'limits' => Mage::helper('news')->commentsPerPage(),
                )
        );

        return $this;
    }

    public function getPreparedCollection() {
        return $this->_prepareCollection();
    }

    protected function _prepareCollection() {
        
        $cid = $this->getRequest()->getParam('cid');
        $collection = Mage::helper('news')->getNewsCollection($cid);
        $collection->setPageSize(10);

        if ($this->getRequest()->getParam('p') > 0)
            $collection->setCurPage($this->getRequest()->getParam('p'));

        $this->setData('cached_collection', $collection);


        return $this->getData('cached_collection');
    }

    protected function getTickerCollection($pageIdentifier, $store) {

    $collection = Mage::getModel('news/newscategorydata')->getCollection()
                ->addFieldToFilter('add_to_pages', array('finset' => $pageIdentifier))
                ->addFieldToFilter('main_table.store_id', array('eq' => $store))
                ->addFieldToFilter('main_table.enable_ticker', array('eq' => 1));
                
        foreach ($collection as $_collection):
            $catIds.= (strlen($catIds) > 0 ? '|' . $_collection->getCategoryId() : $_collection->getCategoryId()) ;
        endforeach;

        $collection->getSelect()->joinLeft('news_data', 'news_data.store_id=' . $store . '
                    AND news_data.enable_ticker = 1
                    AND news_data.status = 1
                    AND news_data.category_id REGEXP "[[:<:]](' . $catIds . ')[[:>:]]"', 
                    array('intro', 'title', 'news_id'));
        
        $collection->addFieldToFilter("date_to_unpublish",array("gteq"=>date('Y-m-d 00:00:00')))->addFieldToFilter("date_to_publish",array("lt"=>date('Y-m-d 23:59:59')));
        $collection->getSelect()->distinct(true);
        
        return $collection;
    }

}
