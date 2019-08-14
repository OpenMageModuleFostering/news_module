<?php

class Biztech_News_Block_Adminhtml_News_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('news_form', array('legend' => Mage::helper('news')->__('News information')));
        $store = Mage::registry('news_data')->getData('store_id');
        
         $fieldset->addField('category_id', 'multiselect', array(
            'name'      => 'category_id[]',
            'label'     => Mage::helper('news')->__('News Category'),
            'title'     => Mage::helper('news')->__('News Category'),
            'required'  => true,
            'values'    => $this->toOptionArray(),
        ));
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('news')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('news')->__('Published?'),
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
        
        $fieldset->addField('enable_ticker', 'select', array(
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
            'after_element_html' => '<p class="note">Set Yes to enable News in News Ticker.</p>'
        ));

        $fieldset->addField('intro', 'editor', array(
            'name' => 'intro',
            'label' => Mage::helper('news')->__('Intro'),
            'title' => Mage::helper('news')->__('Intro'),
            'style' => 'width:700px; height:500px;',
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/',)),
            'wysiwyg' => true,
            'required' => true,
        ));


        $fieldset->addField('news_content', 'editor', array(
            'name' => 'news_content',
            'label' => Mage::helper('news')->__('Content'),
            'title' => Mage::helper('news')->__('Content'),
            'style' => 'width:700px; height:500px;',
            'config' => Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/',)),
            'wysiwyg' => true,
            'required' => true,
        ));
        $fieldset->addField('store_id', 'hidden', array(
            'label' => Mage::helper('news')->__('Store Id'),
            'required' => false,
            'name' => 'store_id'
        ));

        if (Mage::getSingleton('adminhtml/session')->getNewsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
            Mage::getSingleton('adminhtml/session')->setNewsData(null);
        } elseif (Mage::registry('news_data')) {
            Mage::registry('news_data')->setData('store_id', $store);
            $form->setValues(Mage::registry('news_data')->getData());
        }
        return parent::_prepareForm();
    }
    
    protected function toOptionArray()
    {        
       
        $store_id = Mage::app()->getStore()->getStoreId();       
        $collection = Mage::getModel('news/newscategorydata')->getCollection()->addFieldToFilter('store_id' , $store_id);
        $resultArray = array();
        foreach($collection as $category)
        {
            $tempResult[] = array('value' => $category->getCategoryId(),
                'label' => $category->getName());
        }
        
        $resultArray = $tempResult;
             return $resultArray;
        }

}
