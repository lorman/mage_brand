<?php
class HubCo_Brand_Block_Adminhtml_Brand_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        // Instantiate a new form to display our brand for editing.
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                'hubco_brand_admin/brand/edit',
                array(
                    '_current' => true,
                    'continue' => 0,
                )
            ),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        // Define a new fieldset. We need only one for our simple entity.
        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Brand Details')
            )
        );

        $brandSingleton = Mage::getSingleton(
            'hubco_brand/brand'
        );

        $channelSingleton = Mage::getSingleton(
            'hubco_channels/channel'
        );

        // Add the fields that we want to be editable.
        $this->_addFieldsToFieldset($fieldset, array(
            'name' => array(
                'label' => $this->__('Name'),
                'input' => 'text',
                'required' => true,
            ),
            'AKA' => array(
                'label' => $this->__('Name Variants'),
                'input' => 'text',
                'required' => false,
            ),
            'url_key' => array(
                'label' => $this->__('URL Key'),
                'input' => 'text',
                'required' => true,
            ),
            'description' => array(
                'label' => $this->__('Description'),
                'input' => 'textarea',
                'required' => false,
            ),
            'aca_brand_code' => array(
                'label' => $this->__('ACA Brand Code'),
                'input' => 'text',
                'required' => false,
            ),
            'visibility' => array(
                'label' => $this->__('Visibility'),
                'input' => 'select',
                'required' => true,
                'options' => $brandSingleton->getAvailableVisibilies(),
                'default' => $brandSingleton::VISIBILITY_DIRECTORY,
            ),
            'status' => array(
                'label' => $this->__('Status'),
                'input' => 'select',
                'required' => true,
                'options' => $brandSingleton->getAvailableStatus(),
                'default' => $brandSingleton::STATUS_ENABLED,
            ),
            'map' => array(
                'label' => $this->__('MAP'),
                'input' => 'select',
                'required' => true,
                'options' => $brandSingleton->getAvailableMAP(),
            ),
            'max_discount' => array(
                'label' => $this->__('Maximum Discount'),
                'input' => 'text',
                'required' => false,
                'default' => 100
            ),
            'add_handling' => array(
                'label' => $this->__('Additional Handling'),
                'input' => 'text',
                'required' => false
            ),
            'surchargeS' => array(
                'label' => $this->__('$ surcharge'),
                'input' => 'text',
                'required' => false
            ),
            'surchargeP' => array(
                'label' => $this->__('% surcharge'),
                'input' => 'text',
                'required' => false
            ),
            'google' => array(
                'label' => $this->__('Google'),
                'input' => 'select',
                'required' => true,
                'options' => $brandSingleton->getAvailablePermissions(),
                'default' => $brandSingleton::PERMISSION_ALLOWED,
            ),
            'channels' => array(
                'name' => 'channels[]',
                'label' => $this->__('Dis-Allowed Channels'),
                'input' => 'multiselect',
                'required' => false,
                'values' => $channelSingleton->toOptionList(true),
            ),
            'product_types' => array(
                'name' => 'product_types[]',
                'label' => $this->__('Product Types'),
                'input' => 'multiselect',
                'required' => false,
                'default' => $brandSingleton->getAvailableProductTypes(true),
            ),
            'categories' => array(
                'name' => 'categories[]',
                'label' => $this->__('Applicable Categories'),
                'input' => 'multiselect',
                'required' => false,
                'default'=> $brandSingleton->getAvailableCategories(true),
            ),
            /**
             * Note: we have not included created_at or updated_at.
             * We will handle those fields ourself in the model
       * before saving.
             */
        ));

        $fieldset->addField('fileinputname', 'image', array(
            'label'     => $this->__('Logo Image'),
            'required'  => false,
            'name'      => 'fileinputname',
			      'after_element_html' => (''!=Mage::registry('current_brand')->getData('logo_file_path')?'<p style="margin-top: 5px"><img src="'.Mage::getBaseUrl('media') . 'hubco_brand/' . Mage::registry('current_brand')->getData('logo_file_path').'" width="60px" height="60px" /></p>':''),
        ));

        return $this;
    }

    /**
     * This method makes life a little easier for us by pre-populating
     * fields with $_POST data where applicable and wrapping our post data
     * in 'brandData' so that we can easily separate all relevant information
     * in the controller. You could of course omit this method entirely
     * and call the $fieldset->addField() method directly.
     */
    protected function _addFieldsToFieldset(
        Varien_Data_Form_Element_Fieldset $fieldset, $fields)
    {
        $requestData = new Varien_Object($this->getRequest()
            ->getPost('brandData'));

        foreach ($fields as $name => $_data) {
            if ($requestValue = $requestData->getData($name)) {
                $_data['value'] = $requestValue;
            }

            // Wrap all fields with brandData group.
            $_data['name'] = "brandData[$name]";

            // Generally, label and title are always the same.
            $_data['title'] = $_data['label'];

            // If no new value exists, use the existing brand data.
            if (!array_key_exists('value', $_data)) {
                $_data['value'] = $this->_getBrand()->getData($name);
            }
            if (!isset($_data['value']) && isset($_data['default'])) {
              $_data['value'] = $_data['default'];
            }

            // Finally, call vanilla functionality to add field.
            $fieldset->addField($name, $_data['input'], $_data);
        }

        return $this;
    }

    /**
     * Retrieve the existing brand for pre-populating the form fields.
     * For a new brand entry, this will return an empty brand object.
     */
    protected function _getBrand()
    {
        if (!$this->hasData('brand')) {
            // This will have been set in the controller.
            $brand = Mage::registry('current_brand');

            // Just in case the controller does not register the brand.
            if (!$brand instanceof
                    HubCo_Brand_Model_Brand) {
                $brand = Mage::getModel(
                    'hubco_brand/brand'
                );
            }

            $this->setData('brand', $brand);
        }

        return $this->getData('brand');
    }
}