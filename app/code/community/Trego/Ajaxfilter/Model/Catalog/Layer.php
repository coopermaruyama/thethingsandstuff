<?php

class Trego_Ajaxfilter_Model_Catalog_Layer extends Mage_Catalog_Model_Layer
{
   
   
	/*
	* Add Filter in product Collection for new price
	*
	* @return object
	*/
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getProductCollection();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
		
		$this->currentRate = $collection->getCurrencyRate();
		$max=$this->getMaxPriceFilter();
		$min=$this->getMinPriceFilter();
		
		if($min >= 0 && $max > 0){
			$collection->getSelect()->where(' final_price >= "'.$min.'" AND final_price <= "'.$max.'" ');
		}
		
        return $collection;
    }
	
	
	/*
	* convert Price as per currency
	*
	* @return currency
	*/
	public function getMaxPriceFilter(){
		return isset($_GET['max']) ? round($_GET['max']/$this->currentRate) : 0;
	}
	
	
	/*
	* Convert Min Price to current currency
	*
	* @return currency
	*/
	public function getMinPriceFilter(){
		return isset($_GET['min']) ? round($_GET['min']/$this->currentRate) : 0;
	}
    
	
}
