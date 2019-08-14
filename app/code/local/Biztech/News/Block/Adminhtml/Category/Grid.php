<?php

class Biztech_News_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('newsCategoryGrid');
      $this->setDefaultSort('category_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $store = $this->getRequest()->getParam('store', 0);      
      $prefix = Mage::getConfig()->getTablePrefix();
      
      $collection = Mage::getModel('news/newscategory')->getCollection();
      $collection->getSelect()->joinLeft($prefix.'news_category_data', 
              'main_table.category_id ='.$prefix.'news_category_data.category_id AND '.$prefix.'news_category_data.store_id = '.$store,
              array('name','store_id','status'));
      
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('category_id', array(
          'header'    => Mage::helper('news')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'category_id',
          'filter_index' => 'main_table.category_id',
      ));

      $this->addColumn('name', array(
          'header'    => Mage::helper('news')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('news')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
      
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('news')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('news')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit','params' => array('store' => $this->getRequest()->getParam('store', 0))),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('news')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('news')->__('XML'));
      
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('news')->__('Delete'),
               'url'      => $this->getUrl('*/*/massDelete', array('store'=>$this->getRequest()->getParam('store', 0))),
             'confirm'  => Mage::helper('news')->__('Are you sure?')
        ));
       
        $statuses = Mage::getSingleton('news/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('news')->__('Change status'),
               'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true,'store'=>$this->getRequest()->getParam('store', 0))),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('news')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId(),'store'=>$this->getRequest()->getParam('store', 0)));
  }

}