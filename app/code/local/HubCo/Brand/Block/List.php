<?php
class HubCo_Brand_Block_List extends Mage_Core_Block_Template
{
    public function getBrandCollection()
    {
        return Mage::getModel('hubco_brand/brand')->getCollection()
            ->addFieldToFilter('visibility', HubCo_Brand_Model_Brand::VISIBILITY_DIRECTORY)
            ->setOrder('name', 'ASC');
    }
}