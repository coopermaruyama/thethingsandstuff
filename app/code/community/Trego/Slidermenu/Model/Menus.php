<?php
class Trego_Slidermenu_Model_Menus
{
    public function toOptionArray()
    {
        return array(
          array('value' => 0, 'label' => Mage::helper('slidermenu')->__('Default Menu')),
          array('value' => 1, 'label' => Mage::helper('slidermenu')->__('Menu with thumbnail image')),
          array('value' => 2, 'label' => Mage::helper('slidermenu')->__('Mega Menu'))
        );
    }
}
