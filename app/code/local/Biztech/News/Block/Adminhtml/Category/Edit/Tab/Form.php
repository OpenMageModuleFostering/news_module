<?php

class Biztech_News_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend' => Mage::helper('news')->__('Category information')));
        $store = Mage::registry('news_category_data')->getData('store_id') ?  Mage::registry('news_category_data')->getData('store_id') : 0;

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('news')->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('news')->__('Enabled'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('news')->__('Yes'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('news')->__('No'),
                ),
            ),
        ));

        $enable_ticker = $fieldset->addField('enable_ticker', 'select', array(
            'label' => Mage::helper('news')->__('Enable For News Ticker'),
            'name' => 'enable_ticker',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('news')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('news')->__('Yes'),
                ),
            ),
            'after_element_html' => '<p class="note">Set Yes to enable News Ticker.</p>'
        ));

        $addToPages = $fieldset->addField('add_to_pages', 'multiselect', array(
            'label' => Mage::helper('news')->__('Add To Pages'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'add_to_pages[]',
            'values'=> Mage::getModel('adminhtml/system_config_source_cms_page')->toOptionArray(),
            'after_element_html' => '<p class="note">Select CMS Pages where you want to add News Ticker.</p>'
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'label' => Mage::helper('news')->__('Store Id'),
            'required' => false,
            'value' => $store,
            'name' => 'store_id'
        ));

        if (Mage::getSingleton('adminhtml/session')->getNewscategoryData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getNewscategoryData());
            Mage::getSingleton('adminhtml/session')->setNewscategoryData(null);
        } elseif (Mage::registry('news_category_data')) {
            Mage::registry('news_category_data')->setData('store_id', $store);
            $form->setValues(Mage::registry('news_category_data')->getData());
        }


        $this->setForm($form);
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                        ->addFieldMap($enable_ticker->getHtmlId(), $enable_ticker->getName())
                        ->addFieldMap($addToPages->getHtmlId(), $addToPages->getName())
                        ->addFieldDependence($addToPages->getName(), $enable_ticker->getName(), 1));

        return parent::_prepareForm();
    }

   /* protected function toOptionArray() {
        $store_id = $this->getRequest()->getParam('store' , 0);  
        $_pages = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter($store_id)
                ->addFieldToFilter('is_active', 1);
        
        $resultArray = array();
        foreach ($_pages as $page) {
            $tempResult[] = array('value' => $page->getIdentifier(),
                'label' => $page->getTitle());
        }
        $resultArray = $tempResult;
        return $resultArray;
    }*/

}
