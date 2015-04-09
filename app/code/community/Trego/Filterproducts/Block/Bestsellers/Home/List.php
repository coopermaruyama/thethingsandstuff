<?php
class Trego_Filterproducts_Block_Bestsellers_Home_List extends Trego_Filterproducts_Block_Bestsellers_List
{
		
		protected function _getProductCollection()
    	{
        parent::__construct();
        $storeId    = Mage::app()->getStore()->getId();
        $storeCode  = Mage::app()->getStore()->getCode();
        $prefix = Mage::getConfig()->getTablePrefix();
        $select = Mage::getSingleton('core/resource')->getConnection('core_read')
            ->select()
            ->from($prefix.'sales_flat_order_item', array('product_id', 'count' => 'SUM(`qty_ordered`)'))
            ->group('product_id');
 
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);;
        if(Mage::getStoreConfig('filterproducts/bestseller/max_product', $storeCode))
        {
            $collection->setPageSize(Mage::getStoreConfig('filterproducts/bestseller/max_product'));
        }
 
        $collection->getSelect()
            ->join( array ('bs' => $select),
                    'bs.product_id = e.entity_id')
            ->order('bs.count DESC');
        $this->_productCollection = $collection;

        return $this->_productCollection;

    	}
		
		function get_prod_count()
		{
			//unset any saved limits
	    	Mage::getSingleton('catalog/session')->unsLimitPage();
	    	return (isset($_REQUEST['limit'])) ? intval($_REQUEST['limit']) : 9;
		}// get_prod_count

		function get_cur_page()
		{
			return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
		}// get_cur_page

    	function get_order()
		{
			return (isset($_REQUEST['order'])) ? ($_REQUEST['order']) : 'ordered_qty';
		}// get_order

    	function get_order_dir()
		{
			return (isset($_REQUEST['dir'])) ? ($_REQUEST['dir']) : 'desc';
		}// get_direction

		public function getToolbarHtml()
    	{
        
    	}
}