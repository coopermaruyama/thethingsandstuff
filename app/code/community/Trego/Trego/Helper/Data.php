<?php

class Trego_Trego_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    protected $_texturePath;
    
    public function __construct()
    {
        $this->_texturePath = 'wysiwyg/trego/texture/default/';
    }

    public function getCfgGroup($group, $storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trego/' . $group, $storeId);
        else
            return Mage::getStoreConfig('trego/' . $group);
    }
    
    public function getCfgSectionDesign($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trego_design', $storeId);
        else
            return Mage::getStoreConfig('trego_design');
    }

    public function getCfgSectionSettings($storeId = NULL)
    {
        if ($storeId)
            return Mage::getStoreConfig('trego_settings', $storeId);
        else
            return Mage::getStoreConfig('trego_settings');
    }
    
    public function getTexturePath()
    {
        return $this->_texturePath;
    }

    public function getCfg($optionString)
    {
        return Mage::getStoreConfig('trego_settings/' . $optionString);
    }
}
