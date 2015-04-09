<?php
class Trego_Slidermenu_Model_Sliders
{
    public function toOptionArray()
    {
        return array(
          array('value' => 0, 'label' => Mage::helper('slidermenu')->__('Slider 1')),
          array('value' => 1, 'label' => Mage::helper('slidermenu')->__('Slider 2')),
          array('value' => 2, 'label' => Mage::helper('slidermenu')->__('Slider 3')),
          array('value' => 3, 'label' => Mage::helper('slidermenu')->__('Vertical Showcase Slider')),
          array('value' => 4, 'label' => Mage::helper('slidermenu')->__('Revolution Slider'))
        );
    }
}
