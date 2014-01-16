<?php

class Trego_Filterproducts_Model_Mysql4_Filterproducts extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the filterproducts_id refers to the key field in your database table.
        $this->_init('filterproducts/filterproducts', 'filterproducts_id');
    }
}