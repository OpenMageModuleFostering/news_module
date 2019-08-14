<?php
    class Biztech_News_IndexController extends Mage_Core_Controller_Front_Action
    {
        public function indexAction()
        {
          
            $news_id = $this->getRequest()->getParam('id');
            if($news_id != null && $news_id != '')	{
                $news = Mage::getModel('news/news')->load($news_id)->getData();
                if($news['status']==2)
                {
                    $this->_redirect('news.html');
                }
            } else {
                $news = null;
            }	
           
            if($news == null) {
                $resource = Mage::getSingleton('core/resource');
                $read= $resource->getConnection('core_read');
                $newsTable = $resource->getTableName('news');
                $select = $read->select()
                ->from($newsTable,array('news_id','title','news_content','status'))
                ->where('status',1)
                ->order('created_time DESC') ;
                $news = $read->fetchRow($select);
            }
            Mage::register('news', $news);
            $this->loadLayout(); 
            
            $root     = $this->getLayout()->getBlock('root');
            $template = Mage::getStoreConfig('news/news_general/news_list_page_layout');
            $root->setTemplate($template);  
            if($news['seo_keywords'] != "")
                $this->getLayout()->getBlock('head')->setTitle($news['seo_keywords']);
            if($news['seo_description'] != "")
                $this->getLayout()->getBlock('head')->setKeywords($news['seo_description']);
            $this->renderLayout();
        }

        public function viewAction()
        { 
            $id = $this->getRequest()->getParam("id");
            if($id)
            {
                $this->getLayout()->createBlock('news/news')->setData(array("id"=>$id))->setTemplate('news/news.phtml')->toHtml();
            } 
            $this->loadLayout(); 

            $root = $this->getLayout()->getBlock('root');
            $template = Mage::getStoreConfig('news/news_general/news_detail_page_layout');
            $root->setTemplate($template); 
            $news = Mage::getModel('news/news')->load($id)->getData();
            if($news['browser_title'] != "")
                $this->getLayout()->getBlock('head')->setTitle($news['browser_title']);
            if($news['seo_keywords'] != "")
                $this->getLayout()->getBlock('head')->setKeywords($news['seo_keywords']);
            if($news['seo_description'] != "")
                $this->getLayout()->getBlock('head')->setDescription($news['seo_description']);
            $this->renderLayout();
        }
}