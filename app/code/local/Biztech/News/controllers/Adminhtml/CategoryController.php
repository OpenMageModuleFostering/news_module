<?php

class Biztech_News_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('news/category')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {

        $store = $this->getRequest()->getParam('store', 0);
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('news/newscategory')->load($id);

        $model_text = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('store_id', $store)->addFieldToFilter('category_id', $model->getCategoryId())->getData();

        if (count($model_text) == 0) {
            $model_text = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('store_id', 0)->addFieldToFilter('category_id', $model->getCategoryId())->getData();
        }

        $text_data = $model_text[0];
        $model = $model->setName($text_data['name'])
                ->setStoreId($text_data['store_id'])
                ->setEnableTicker($text_data['enable_ticker'])
                ->setAddToPages($text_data['add_to_pages'])
                ->setStatus($text_data['status']);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
                $model_text->setData($data);
            }

            Mage::register('news_category_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('news/category');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Category'), Mage::helper('adminhtml')->__('Item Category'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('news/adminhtml_category_edit'))
                    ->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher'))
                    ->_addLeft($this->getLayout()->createBlock('news/adminhtml_category_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('news')->__('Category does not exist'));
            $this->_redirect('*/*/', array('store' => $store));
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('news/newscategory');
            $model_text = Mage::getModel('news/newscategorydata');
            try {
                if (!$this->getRequest()->getParam('id')) {

                    $model->setId();
                    $model->save();

                    $category_id = $model->getId();

                    $model_text = Mage::getModel('news/newscategorydata');

                    if (isset($data['add_to_pages'])) {
                        $pages = implode(",", array_unique($data['add_to_pages']));
                    }

                    $model_text->setData($data)->setCategoryId($category_id)->setStoreId(0);
                    $model_text->save();

                    foreach (Mage::app()->getWebsites() as $website) {
                        foreach ($website->getGroups() as $group) {
                            $stores = $group->getStores();
                            foreach ($stores as $store) {
                                $model_text->setData($data)->setCategoryId($category_id)->setStoreId($store->getId())->setEnableTicker($data['enable_ticker'])->setAddToPages($pages);
                                $model_text->save();
                            }
                        }
                    }
                } else {

                    $category_id = $this->getRequest()->getParam('id');
                    if ($data['store_id'] == 0) {

                        $text_data = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('category_id', $category_id)->addFieldToFilter('store_id', $data['store_id'])->getData();

                        if (isset($data['add_to_pages'])) {
                            $pages = implode(",", array_unique($data['add_to_pages']));
                        } else {
                            $pages = $text_data[0]['add_to_pages'];
                        }

                        $model_text->setData($data)->setCategoryId($text_data[0]['category_id'])->setId($text_data[0]['data_id'])->setStoreId(0)->setAddToPages($pages);
                        $model_text->save();

                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {

                                    $text_data = '';
                                    $text_data = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('category_id', $category_id)->addFieldToFilter('store_id', $store->getId())->getData();
                                    $model_text->setData($data)->setCategoryId($text_data[0]['category_id'])->setId($text_data[0]['data_id'])->setStoreId($store->getId())->setEnableTicker($data['enable_ticker'])->setAddToPages($pages);
                                    $model_text->save();
                                }
                            }
                        }
                    } else {

                        $text_data = '';
                        $text_data = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('category_id', $category_id)->addFieldToFilter('store_id', $data['store_id'])->getData();
                        if (isset($data['add_to_pages'])) {
                            $pages = implode(",", array_unique($data['add_to_pages']));
                        } else {
                            $pages = $text_data[0]['add_to_pages'];
                        }

                        $model_text->setData($data)->setCategoryId($text_data[0]['category_id'])->setId($text_data[0]['data_id'])->setStoreId($data['store_id'])->setEnableTicker($data['enable_ticker'])->setAddToPages($pages);
                        $model_text->save();
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('news')->__('Category was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $category_id, 'store' => $data['store_id']));
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
                $model = Mage::getModel('news/newscategory');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Category was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $categoryIds = $this->getRequest()->getParam('category');
        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    $newscategory = Mage::getModel('news/newscategory')->load($categoryId);
                    $newscategory->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($categoryIds)
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
        $categoryIds = $this->getRequest()->getParam('category');
        if (!is_array($categoryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($categoryIds as $categoryId) {
                    $model_text = Mage::getModel('news/newscategorydata');
                    $collection_text = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('category_id', $categoryId);
                    if ($store_id != 0) {
                        $text_data = $collection_text->addFieldToFilter('store_id', $store_id)->getData();
                        $model_text->setId($text_data[0]['data_id'])->setStoreId($store_id)
                                ->setStatus($this->getRequest()->getParam('status'))
                                ->setIsMassupdate(true)
                                ->save();
                    } else {
                        $text_data = $collection_text->addFieldToFilter('store_id', 0)->getData();
                        $model_text->setId($text_data[0]['data_id'])->setStoreId(0)
                                ->setStatus($this->getRequest()->getParam('status'))
                                ->setIsMassupdate(true)
                                ->save();
                        foreach (Mage::app()->getWebsites() as $website) {
                            foreach ($website->getGroups() as $group) {
                                $stores = $group->getStores();
                                foreach ($stores as $store) {
                                    $collection_text = '';
                                    $collection_text = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('category_id', $categoryId);
                                    $text_data = $collection_text->addFieldToFilter('store_id', $store->getId())->getData();
                                    $model_text->setId($text_data[0]['data_id'])->setStoreId($store->getId())
                                            ->setStatus($this->getRequest()->getParam('status'))
                                            ->setIsMassupdate(true)
                                            ->save();
                                }
                            }
                        }
                    }
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($categoryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index', array('store' => $store_id));
    }

    public function exportCsvAction() {
        $fileName = 'category.csv';
        $content = $this->getLayout()->createBlock('news/adminhtml_category_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'category.xml';
        $content = $this->getLayout()->createBlock('news/adminhtml_category_grid')
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

}
