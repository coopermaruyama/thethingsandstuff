<?php

class Trego_Filterproducts_Model_Mysql4_Filterproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('filterproducts/filterproducts');
    }
}