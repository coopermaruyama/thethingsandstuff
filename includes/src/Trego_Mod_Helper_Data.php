<?php
class Trego_Mod_Helper_Data extends Mage_Core_Helper_Data {
    public function getHomeUrl() {
        return array(
            "label" => $this->__('Home'),
            "title" => $this->__('Home Page'),
            "link" => Mage::getUrl('')
        );
    }
}
 
?>