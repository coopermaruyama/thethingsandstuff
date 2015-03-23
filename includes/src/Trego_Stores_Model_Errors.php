<?php
/**
 * Captcha image model
 *
 * @category   Mage
 * @package    Mage_Captcha
 */
class Trego_Stores_Model_Errors
{
    /**
     * Get options for font selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
          array('value' => 0, 'label' => Mage::helper('stores')->__('404 Page Type 1')),
          array('value' => 1, 'label' => Mage::helper('stores')->__('404 Page Type 2 (Full screen Background)'))
        );
    }
}
