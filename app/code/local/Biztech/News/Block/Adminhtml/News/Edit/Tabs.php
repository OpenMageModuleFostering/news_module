<?php

class Biztech_News_Block_Adminhtml_News_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('news_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('news')->__('News Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('news')->__('News Information'),
            'title' => Mage::helper('news')->__('News Information'),
            'content' => $this->getLayout()->createBlock('news/adminhtml_news_edit_tab_form')->toHtml(),
        ));

        $this->addTab('publishing_options', array(
            'label' => Mage::helper('news')->__('Publishing Options'),
            'title' => Mage::helper('news')->__('Publishing Options'),
            'content' => $this->getLayout()->createBlock('news/adminhtml_news_edit_tab_publishingoptions')->toHtml(),
        ));

      

        $this->addTab('seo', array(
            'label' => Mage::helper('news')->__('SEO Options'),
            'title' => Mage::helper('news')->__('SEO Options'),
            'content' => $this->getLayout()->createBlock('news/adminhtml_news_edit_tab_seo')->toHtml(),
        ));

          $this->addTab('relatednews', array(
            'label' => Mage::helper('news')->__('Related News'),
            'title' => Mage::helper('news')->__('Related News'),
            'url' => $this->getUrl('*/*/product', array('_current' => true)),
            'class' => 'ajax',
        ));
        return parent::_beforeToHtml();
    }

}
