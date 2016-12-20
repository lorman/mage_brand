<?php
class HubCo_Brand_Block_Adminhtml_Brand_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /**
         * Tell Magento which collection to use to display in the grid.
         */
        $collection = Mage::getResourceModel(
            'hubco_brand/brand_collection'
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    public function getRowUrl($row)
    {
        /**
         * When a grid row is clicked, this is where the user should
         * be redirected to - in our example, the method editAction of
         * BrandController.php in BrandDirectory module.
         */
        return $this->getUrl(
            'hubco_brand_admin/brand/edit',
            array(
                'id' => $row->getId()
            )
        );
    }

    protected function _prepareColumns()
    {
        /**
         * Here, we'll define which columns to display in the grid.
         */
        $this->addColumn('entity_id', array(
            'header' => $this->_getHelper()->__('ID'),
            'type' => 'number',
            'index' => 'entity_id',
        ));

        $this->addColumn('created_at', array(
            'header' => $this->_getHelper()->__('Created'),
            'type' => 'date',
            'index' => 'created_at',
        ));

        $this->addColumn('updated_at', array(
            'header' => $this->_getHelper()->__('Updated'),
            'type' => 'date',
            'index' => 'updated_at',
        ));

        $this->addColumn('name', array(
            'header' => $this->_getHelper()->__('Name'),
            'type' => 'text',
            'index' => 'name',
        ));

        $this->addColumn('AKA', array(
            'header' => $this->_getHelper()->__('AKA'),
            'type' => 'text',
            'index' => 'AKA',
        ));

        $this->addColumn('url_key', array(
            'header' => $this->_getHelper()->__('Url Key'),
            'type' => 'text',
            'index' => 'url_key',
        ));

        $brandSingleton = Mage::getSingleton(
            'hubco_brand/brand'
        );

        $this->addColumn('aca_brand_code', array(
            'header' => $this->_getHelper()->__('Brand Code'),
            'type' => 'text',
            'index' => 'aca_brand_code',
        ));

        $this->addColumn('status', array(
            'header' => $this->_getHelper()->__('Status'),
            'index' => 'status',
            'type'    => 'options',
            'options' =>  $brandSingleton->getAvailableStatus()
        ));

        $this->addColumn('visibility', array(
            'header' => $this->_getHelper()->__('Visibility'),
            'type' => 'options',
            'index' => 'visibility',
            'options' => $brandSingleton->getAvailableVisibilies()
        ));

        $this->addColumn('map', array(
            'header' => $this->_getHelper()->__('MAP'),
            'type' => 'options',
            'index' => 'map',
            'options' => $brandSingleton->getAvailableMAP()
        ));

        $this->addColumn('google', array(
            'header' => $this->_getHelper()->__('Google'),
            'index' => 'google',
            'type'    => 'options',
            'options' =>  $brandSingleton->getAvailablePermissions()
        ));

        $this->addColumn('amazon', array(
            'header' => $this->_getHelper()->__('Amazon'),
            'index' => 'amazon',
            'type'    => 'options',
            'options' =>  $brandSingleton->getAvailablePermissions()
        ));

        $this->addColumn('image', array(
            'header' => $this->_getHelper()->__('Logo'),
            'index' => 'logo_file_path',
            'align' => 'left',
            'width'     => '97',
        ));

        /**
         * Finally, we'll add an action column with an edit link.
         */
        $this->addColumn('action', array(
            'header' => $this->_getHelper()->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'actions' => array(
                array(
                    'caption' => $this->_getHelper()->__('Edit'),
                    'url' => array(
                        'base' => 'hubco_brand_admin'
                                  . '/brand/edit',
                    ),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'entity_id',
        ));

        return parent::_prepareColumns();
    }

    protected function _getHelper()
    {
        return Mage::helper('hubco_brand');
    }
}