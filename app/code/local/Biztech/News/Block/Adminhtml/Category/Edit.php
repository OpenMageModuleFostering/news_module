<?php

    class Biztech_News_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();

            $this->_objectId = 'id';
            $this->_blockGroup = 'news';
            $this->_controller = 'adminhtml_category';

            $this->_updateButton('save', 'label', Mage::helper('news')->__('Save Category'));
            $this->_updateButton('delete', 'label', Mage::helper('news')->__('Delete Category'));
            $this->_removeButton('delete');
            $this->_removeButton('back');
            
            $id = $this->getRequest()->getParam('store', 0);
            $this->_addButton('delete', array(
                    'label'     => Mage::helper('news')->__('Delete Category'),
                    'onclick'   => 'confirmSetLocation(\''.Mage::helper('news')->__('Are you sure?').'\', \''.$this->getUrl('*/*/delete', array('id'=>$this->getRequest()->getParam('id'), 'store'=>$id)).'\')',
                    'class'     => 'delete',
                ));
            $this->_addButton('back', array(
                    'label'     => Mage::helper('news')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$id)).'\')',
                    'class'     => 'back',
                ),-1,1);    

            $this->_addButton('saveandcontinue', array(
                    'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit()',
                    'class'     => 'save',
                ), -100);

            $this->_formScripts[] = "
            function toggleEditor() {
            if (tinyMCE.getInstanceById('news_content') == null) {
            tinyMCE.execCommand('mceAddControl', false, 'news_content');
            } else {
            tinyMCE.execCommand('mceRemoveControl', false, 'news_content');
            }
            }

            function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
            }
            ";
        }

        public function getHeaderText()
        {
            if( Mage::registry('news_category_data') && Mage::registry('news_category_data')->getId() ) {
                return Mage::helper('news')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('news_category_data')->getName()));
            } else {
                return Mage::helper('news')->__('Add Category');
            }
        } 
        protected function _prepareLayout() 
        {
            parent::_prepareLayout();
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
        }

}