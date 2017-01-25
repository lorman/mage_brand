<?php
class HubCo_Brand_Model_Brand
    extends Mage_Core_Model_Abstract
{
    const VISIBILITY_HIDDEN = '0';
    const VISIBILITY_DIRECTORY = '1';

    const STATUS_ENABLED = '1';
    const STATUS_DISABLED = '0';

    const PERMISSION_ALLOWED = '1';
    const PERMISSION_DISALLOWED = '0';

    const MAPED = '1';
    const NOT_MAPED = '0';

    const TYPE_APPAREL = '1';
    const TYPE_PARTS = '2';
    const TYPE_CHEMICAL = '3';
    const TYPE_ALL = '4';

    protected function _construct()
    {
        /**
         * This tells Magento where the related resource model can be found.
         *
         * For a resource model, Magento will use the standard model alias -
         * in this case 'hubco_brand' - and look in
         * config.xml for a child node <resourceModel/>. This will be the
         * location that Magento will look for a model when
         * Mage::getResourceModel() is called - in our case,
         * HubCo_Brand_Model_Resource.
         */
        $this->_init('hubco_brand/brand');
    }

    /**
     * This method is used in the grid and form for populating the dropdown.
     */
    public function getAvailableVisibilies()
    {
        return array(
            self::VISIBILITY_DIRECTORY
                => Mage::helper('hubco_brand')
                       ->__('Visible in Directory'),
            self::VISIBILITY_HIDDEN
                => Mage::helper('hubco_brand')
                       ->__('Hidden'),
        );
    }


    /**
     * This method is used in the grid and form for populating the dropdown.
     */
    public function getAvailableStatus()
    {
      return array(
        self::STATUS_ENABLED => Mage::helper('hubco_brand')->__('Enabled'),
        self::STATUS_DISABLED => Mage::helper('hubco_brand')->__('Disabled'),
      );
    }

    public function getAvailablePermissions()
    {
      return array(
          self::PERMISSION_ALLOWED => Mage::helper('hubco_brand')->__('Allowed'),
          self::PERMISSION_DISALLOWED => Mage::helper('hubco_brand')->__('Disallowed'),
      );
    }

    public function getAvailableMAP()
    {
      return array(
          self::MAPED => Mage::helper('hubco_brand')->__('MAPed'),
          self::NOT_MAPED => Mage::helper('hubco_brand')->__('Not MAPed'),
      );
    }

    public function getAvailableProductTypes($multi = false)
    {
      if ($multi) {
        return array(
            array('label' => Mage::helper('hubco_brand')->__('All'), 'value' => self::TYPE_ALL),
            array('label' => Mage::helper('hubco_brand')->__('Apparel'), 'value' => self::TYPE_APPAREL),
            array('label' => Mage::helper('hubco_brand')->__('Parts'), 'value' => self::TYPE_PARTS),
            array('label' => Mage::helper('hubco_brand')->__('Chemicals'), 'value' => self::TYPE_CHEMICAL),
        );
      }
      return array(
          self::TYPE_ALL => Mage::helper('hubco_brand')->__('All'),
          self::TYPE_APPAREL => Mage::helper('hubco_brand')->__('Apparel'),
          self::TYPE_PARTS => Mage::helper('hubco_brand')->__('Parts'),
          self::TYPE_CHEMICAL => Mage::helper('hubco_brand')->__('Chemicals'),
      );
    }

    public function getAvailableCategories($multi = false)
    {
      $categories = array();
      $allCategoriesCollection = Mage::getModel('catalog/category')
      ->getCollection()
      ->addAttributeToSelect('name')
      ->addFieldToFilter('level', array('gt'=>'0'));
      $allCategoriesArray = $allCategoriesCollection->load()->toArray();
      $categoriesArray = $allCategoriesCollection
      ->addAttributeToSelect('level')
      ->addAttributeToSort('path', 'asc')
      ->addFieldToFilter('is_active', array('eq'=>'1'))
      ->addFieldToFilter('level', array('gt'=>'1'))
      ->load()
      ->toArray();
      foreach ($categoriesArray as $categoryId => $category)
      {
        if (!isset($category['name'])) {
          continue;
        }
        $categoryIds = explode('/', $category['path']);
        $nameParts = array();
        foreach($categoryIds as $catId) {
          if($catId == 1) {
            continue;
          }
          $nameParts[] = $allCategoriesArray[$catId]['name'];
        }
        if ($multi)
        {
          $categories[$categoryId] = array(
              'value' => $categoryId,
              'label' => implode(' / ', $nameParts)
          );
        }
        else
        {
          $categories[$categoryId] = implode(' / ', $nameParts);
        }
      }

      return $categories;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        /**
         * Perform some actions just before a brand is saved.
         */
        $this->_updateTimestamps();
        $this->_prepareUrlKey();
        $this->_prepareImage();

        return $this;
    }

    protected function _updateTimestamps()
    {
        $timestamp = now();

        /**
         * Set the last updated timestamp.
         */
        $this->setUpdatedAt($timestamp);

        /**
         * If we have a brand new object, set the created timestamp.
         */
        if ($this->isObjectNew()) {
            $this->setCreatedAt($timestamp);
        }

        return $this;
    }

    protected function _prepareUrlKey()
    {
        /**
         * In this method, you might consider ensuring
         * that the URL Key entered is unique and
         * contains only alphanumeric characters.
         */

        return $this;
    }

    protected function _prepareImage()
    {
      if(isset($_FILES['fileinputname']['name']) and (file_exists($_FILES['fileinputname']['tmp_name']))) {
        try {
          $uploader = new Varien_File_Uploader('fileinputname');
          $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything

          $uploader->setAllowRenameFiles(false);
          $uploader->setFilesDispersion(false);

          $path = Mage::getBaseDir('media') . DS . 'hubco_brand' . DS;
          if (!is_dir($path)) {
            mkdir($path, 0777, true);
          }

          $uploader->save($path, $_FILES['fileinputname']['name']);
          $this->setLogoFilePath($_FILES['fileinputname']['name']);
        }catch(Exception $e) {

        }
      }
      else {
        if(isset($data['fileinputname']['delete']) && $data['fileinputname']['delete'] == 1)
          $this->setLogoFilePath('');
      }
      return $this;
    }

    public function getAvailableBrands($multi = false)
    {
      $brands = array();
      $allBrandsCollection = Mage::getModel('hubco_brand/brand')
      ->getCollection()
      ->addFieldToSelect('entity_id')
      ->addFieldToSelect('name');
      $allBrands = $allBrandsCollection->load()->toArray();
      foreach ($allBrands['items'] as $brandId => $brand)
      {
        if (!isset($brand['name'])) {
          continue;
        }
        if ($multi)
        {
          $brands[$brand['entity_id']] = array(
              'value' => $brand['entity_id'],
              'label' => $brand['name']
          );
        }
        else
        {
          $brands[$brand['entity_id']] = $brand['name'];
        }
      }

      return $brands;
    }

    public function clean($brand) {
      /**
       * Find a brand by iether name or AKA and return the brand name, return null if not found
       */
      $brand = str_replace('  ',' ', $brand);
      $brandRegEx = str_replace(array('(',')','*'), array('\\(','\\)','\\*'), $brand);
      $brandCollection = Mage::getModel('hubco_brand/brand')->getCollection();
      $brandCollection->addFieldToFilter(
          array('name','AKA'),array(array('eq'=>$brand),array('regexp'=>'(,|^)'.$brandRegEx.'(,|$)')));
      //echo $brandCollection->getSelect()->__toString(); exit;
      $brands = $brandCollection->getData();
      if (count($brands) == 1) {
        return $brands[0]['name'];
      }
      else {
        return null;
      }
    }

    public function getBrandID($name) {
      $brandCollection = Mage::getModel('hubco_brand/brand')->getCollection();
      $brandCollection->addFieldToFilter(
          'name',$name);
      //echo $brandCollection->getSelect()->__toString();
      $brands = $brandCollection->getData();
      if (count($brands) == 1) {
        return $brands[0]['entity_id'];
      }
      else {
        return null;
      }
    }
}