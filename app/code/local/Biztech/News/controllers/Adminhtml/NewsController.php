<?php

    class Biztech_News_Adminhtml_NewsController extends Mage_Adminhtml_Controller_action {

        protected function _initAction() {
            $this->loadLayout()
            ->_setActiveMenu('news/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('News Manager'), Mage::helper('adminhtml')->__('News Manager'));

            return $this;
        }

        public function indexAction() {
            $this->_initAction()
            ->renderLayout();
        }

        public function editAction() {

            $store = $this->getRequest()->getParam('store', 0);
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('news/news')->load($id);

            $model_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('store_id', $store)->addFieldToFilter('news_id', $model->getNewsId())->getData();

            if (count($model_text) == 0) {
                $model_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('store_id', 0)->addFieldToFilter('news_id', $model->getNewsId())->getData();
            }

            $text_data = $model_text[0];
            $model = $model->setTitle($text_data['title'])
            ->setNewsContent($text_data['news_content'])
            ->setStatus($text_data['status'])
            ->setEnableTicker($text_data['enable_ticker'])
            ->setIntro($text_data['intro'])
            ->setDateToPublish($text_data['date_to_publish'])
            ->setDateToUnpublish($text_data['date_to_unpublish'])
            ->setBrowserTitle($text_data['browser_title'])
            ->setSeoKeywords($text_data['seo_keywords'])
            ->setSeoDescription($text_data['seo_description'])
            ->setCategoryId($text_data['category_id']);


            if ($model->getId() || $id == 0) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                    $model_text->setData($data);
                }

                Mage::register('news_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('news/items');

                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('News Manager'), Mage::helper('adminhtml')->__('News Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('news/adminhtml_news_edit'))
                ->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher'))
                ->_addLeft($this->getLayout()->createBlock('news/adminhtml_news_edit_tabs'));

                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('News does not exist'));
                $this->_redirect('*/*/', array('store' => $store));
            }
        }

        public function newAction() {
            $this->_forward('edit');
        }

        public function saveAction() {
            if ($data = $this->getRequest()->getPost()) {

                if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                    try {

                        $uploader = new Varien_File_Uploader('filename');

                        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                        $uploader->setAllowRenameFiles(false);


                        $uploader->setFilesDispersion(false);

                        $path = Mage::getBaseDir('media') . DS;
                        $uploader->save($path, $_FILES['filename']['name']);
                    } catch (Exception $e) {

                    }
                    $data['filename'] = $_FILES['filename']['name'];
                }

                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                try {
                    $publish_date = Mage::app()->getLocale()->date($data['date_to_publish'], $format, null, false);
                    $unpublish_date = Mage::app()->getLocale()->date($data['date_to_unpublish'], $format, null, false);
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError("Invalid Date");
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $data['store_id']));
                    return;
                }

                $data['date_to_publish'] = date('Y-m-d H:i:s', strtotime($data['date_to_publish']));
                $data['date_to_unpublish'] = date('Y-m-d H:i:s', strtotime($data['date_to_unpublish']));
                $model = Mage::getModel('news/news');
                $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));


                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                    ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }


                $model->save();

                $model_text = Mage::getModel('news/newsdata');

                $collection_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('news_id', $model->getNewsId());

                if (!$this->getRequest()->getParam('id')) {

                    
                    if (!empty($data['links']['related_news'])) {
                        $related_news= Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['related_news']);
                        $related_news = implode(",", array_unique($related_news));
                    }

                    if(isset($data['category_id']))
                    {
                        $category_id = implode(",", array_unique($data['category_id']));
                    }
                    $model_text->setData($data)->setNewsId($model->getNewsId())->setStoreId(0)->setRelatedNews($related_news)->setCategoryId($category_id)->setEnableTicker($data['enable_ticker']);
                    $model_text->save();
                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                $model_text->setData($data)->setNewsId($model->getNewsId())->setStoreId($store->getId())->setRelatedNews($related_news)->setCategoryId($category_id)->setEnableTicker($data['enable_ticker']);
                                $model_text->save();
                            }
                        }
                    }
                } else {

                    if ($data['store_id'] == 0) {
                        $text_data = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('news_id', $model->getNewsId())->addFieldToFilter('store_id', $data['store_id'])->getData();

                        
                        if (isset($data['links'])) {
                            $related_news = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['related_news']);
                        }
                        $related_news = implode(",", array_unique($related_news));
                        if (count($related_news) < 1) {
                            $related_news = $text_data[0]['related_news'];
                        }
                        
                        if(isset($data['category_id']))
                        {
                            $category_id = implode(",", array_unique($data['category_id']));
                        }
                        else
                        {
                            $category_id = $text_data[0]['category_id'];
                        }

                        $model_text->setData($data)->setNewsId($model->getNewsId())->setTextId($text_data[0]['text_id'])->setStoreId($data['store_id'])->setRelatedNews($related_news)->setCategoryId($category_id)->setEnableTicker($data['enable_ticker']);
                        $model_text->save();
                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    $collection_text = '';
                                    $text_data = '';
                                    $collection_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('news_id', $model->getNewsId());
                                    $text_data = $collection_text->addFieldToFilter('store_id', $store->getId())->getData();
                                    $model_text->setData($data)->setNewsId($model->getNewsId())->setTextId($text_data[0]['text_id'])->setStoreId($store->getId())->setRelatedNews($related_news)->setCategoryId($category_id)->setEnableTicker($data['enable_ticker']);
                                    $model_text->save();
                                }
                            }
                        }
                    } else {

                        if (isset($data['related_news'])) {
                            if (is_array($data['related_news'])) {
                                $related_news = implode(",", array_unique($data['related_news']));
                            } else {
                                $related_news = '';
                            }
                        } else {
                            $related_news = $text_data[0]['related_news'];
                        }

                        $text_data = $collection_text->addFieldToFilter('store_id', $data['store_id'])->getData();
                        $model_text->setData($data)->setNewsId($model->getNewsId())->setTextId($text_data[0]['text_id'])->setStoreId($data['store_id'])->setRelatedNews($related_news)->setCategoryId($category_id)->setEnableTicker($data['enable_ticker']);
                        $model_text->save();
                    }
                }


                try {
                    if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                        $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                    } else {
                        $model->setUpdateTime(now());
                    }

                    $model->save();
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('news')->__('News was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setFormData(false);

                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId(), 'store' => $data['store_id']));
                        return;
                    }
                    $this->_redirect('*/*/', array('store' => $data['store_id']));
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'), 'store' => $data['store_id']));
                    return;
                }
            }
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Unable to find item to save'));
            $this->_redirect('*/*/');
        }

        public function deleteAction() {
            if ($this->getRequest()->getParam('id') > 0) {
                try {
                    $model = Mage::getModel('news/news');

                    $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                    $this->_redirect('*/*/');
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
            }
            $this->_redirect('*/*/');
        }

        public function massDeleteAction() {
            $newsIds = $this->getRequest()->getParam('news');
            if (!is_array($newsIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            } else {
                try {
                    foreach ($newsIds as $newsId) {
                        $news = Mage::getModel('news/news')->load($newsId);
                        $news->delete();
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                            'Total of %d record(s) were successfully deleted', count($newsIds)
                        )
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index');
        }

        public function massStatusAction() {
            $store_id = $this->getRequest()->getParam('store', 0);
            $newsIds = $this->getRequest()->getParam('news');
            if (!is_array($newsIds)) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
            } else {
                try {
                    foreach ($newsIds as $newsId) {
                        $model_text = Mage::getModel('news/newsdata');
                        $collection_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('news_id', $newsId);
                        if ($store_id != 0) {
                            $text_data = $collection_text->addFieldToFilter('store_id', $store_id)->getData();
                            $model_text->setTextId($text_data[0]['text_id'])->setStoreId($store_id)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                        } else {
                            $text_data = $collection_text->addFieldToFilter('store_id', 0)->getData();
                            $model_text->setTextId($text_data[0]['text_id'])->setStoreId(0)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                            foreach (Mage::app()->getWebsites() as $website) {
                                foreach ($website->getGroups() as $group) {
                                    $stores = $group->getStores();
                                    foreach ($stores as $store) {
                                        $collection_text = '';
                                        $collection_text = Mage::getModel('news/newsdata')->getCollection()->addFieldToFilter('news_id', $newsId);
                                        $text_data = $collection_text->addFieldToFilter('store_id', $store->getId())->getData();
                                        $model_text->setTextId($text_data[0]['text_id'])->setStoreId($store->getId())
                                        ->setStatus($this->getRequest()->getParam('status'))
                                        ->setIsMassupdate(true)
                                        ->save();
                                    }
                                }
                            }
                        }
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($newsIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index', array('store' => $store_id));
        }

        public function exportCsvAction() {
            $fileName = 'news.csv';
            $content = $this->getLayout()->createBlock('news/adminhtml_news_grid')
            ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
        }

        public function exportXmlAction() {
            $fileName = 'news.xml';
            $content = $this->getLayout()->createBlock('news/adminhtml_news_grid')
            ->getXml();

            $this->_sendUploadResponse($fileName, $content);
        }

        protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK', '');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
        }

        protected function _initNews($getRootInstead = false) {
            $newsId = (int) $this->getRequest()->getParam('id', false);
            $news = Mage::getModel('news/news');

            if ($newsId) {
                $news->load($newsId);
            }
            Mage::register('news', $news);
            Mage::register('current_news', $news);
            return $news;

        }
        public function gridAction() {

            if (!$category = $this->_initNews(true)) {
                return;
            }

            $this->loadLayout();
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

            $this->getLayout()->getBlock('related.grid')
            ->setRelatedNews($sel_news);
            $this->renderLayout();
        }
        public function productAction() {

            $this->loadLayout();
            $this->getLayout()->getBlock('related.grid')->setRelatedNews($this->getRequest()->getPost('related_news', null));
            $this->renderLayout();
        }

    }
