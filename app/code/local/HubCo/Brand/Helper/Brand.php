<?php
class HubCo_Brand_Helper_Brand extends Mage_Core_Helper_Abstract
{
  public function getBrandUrl(HubCo_Brand_Model_Brand $brand)
  {
    if (!$brand instanceof HubCo_Brand_Model_Brand) {
      return '#';
    }

    return $this->_getUrl(
        'hubco_brand/index/view',
        array(
            'url' => $brand->getUrlKey(),
        )
    );
  }
}