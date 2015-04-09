<?php
class Excellence_Ship_Model_Carrier_Excellence extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'excellence';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}
		
		$destCountry = $request->getDestCountryId();
		$destRegion = $request->getDestRegionId();
		$destRegionCode = $request->getDestRegionCode();
		$country_name=Mage::app()->getLocale()->getCountryTranslation($destCountry);
		//echo $country_name;exit;
		$destStreet = $request->getDestStreet();
		$destCity = $request->getDestCity();
		$destPostcode = $request->getDestPostcode();
		$country_id = $request->getCountryId();
		$region_id = $request->getRegionId();
		$city = $request->getCity();
		$postcode = $request->getPostcode();
		$destinationUrl = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($destPostcode .' '.$destStreet)."+".urlencode($destCity).",".urlencode($destRegionCode).",+".urlencode($destRegion).",+".urlencode($country_name)."&sensor=false";
		$destinationJson = json_decode(file_get_contents($destinationUrl));
		
		$originAdress1 = "1111 S Figueroa St";
		$originAdress2 = "";
		$originCity = "Los Angeles";
		$originState = "CA"; 
		$originUrl = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($originAdress1)."+".urlencode($originAdress2).",".urlencode($originCity).",+".urlencode($originState)."&sensor=false";
		$originJson = json_decode(file_get_contents($originUrl));
		
		$destinationLatitude = floatval($destinationJson->results[0]->geometry->location->lat);
		$destinationLongitude = floatval($destinationJson->results[0]->geometry->location->lng);
		$originLatitude = floatval($originJson->results[0]->geometry->location->lat);
		$originLongitude = floatval($originJson->results[0]->geometry->location->lng);


		//get distance
		$distanceUrl = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$originLatitude.",".$originLongitude."&destinations=".$destinationLatitude.",".$destinationLongitude."&mode=driving&language=fr-FR&sensor=false&units=imperial";

		$distanceJson = json_decode(file_get_contents($distanceUrl));
		$distance=$distanceJson->rows[0]->elements[0]->distance->value;
		$distance = $distance * 0.000621371;
		//echo $distance;exit;
		$distanceThreshold =$this->getConfigData('theshold');
		$distanceWeight =floatval($this->getConfigData('price'));
		$sizeWeight =floatval($this->getConfigData('sizeweight')); 
		$weightWeight =floatval($this->getConfigData('weightweight')); 
		$valueWeight =floatval($this->getConfigData('valueweight')); 
		$shippingP =0;

		//init totals
		$totalSize = 0;
		$totalWeight = 0;
		$totalValue = 0;
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}

				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$product_id = $child->getProductId();
							$productObj = Mage::getModel('catalog/product')->load($product_id);
							$ship_price = $productObj->getData('shipping_fee'); //our shipping attribute code
							$shippingP += (float)$ship_price;
						}
					}
				} else {
					// $product_id = $item->getProductId();
					// $productObj = Mage::getModel('catalog/product')->load($product_id);
					// $ship_price = $productObj->getData('shipping_fee'); //our shipping attribute code
					// $shippingP += (float)$ship_price;

					//add to total
					$totalSize += floatval($item->getProduct()->getSize());
					$totalWeight += floatval($item->getProduct()->getWeight());
					$totalValue += floatval($item->getProduct()->getPrice());
				}
			}
		}
		//calculate weighted values
		$weightedSize = $totalSize * $sizeWeight;
		$weightedWeight = $totalWeight * $weightWeight;
		$weightedValue = $totalValue * $valueWeight;
		$weightedDistance = $distance * $distanceWeight;
		//echo $shippingP;exit;
		if ($distance > $distanceThreshold) { 
			// $distanceFee = $distance * $distanceWeight; 
			// $totalItemsDeliveryFee = $shippingP;

			// $price = $totalItemsDeliveryFee + $distanceFee;

			$price = $weightedSize + $weightedWeight + $weightedValue + $weightedDistance;
		}else
		{
			$price=0;
		}
		
		$handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
		$result = Mage::getModel('shipping/rate_result');
		$show = true;
		if($show && $distance){

			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code);
			$method->setMethod($this->_code);
			$method->setCarrierTitle($this->getConfigData('title'));
			$method->setMethodTitle($this->getConfigData('name'));
			$method->setPrice($price);
			$method->setCost($price);
			$result->append($method);

		}else{
			$error = Mage::getModel('shipping/rate_result_error');
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('name'));
			$error->setErrorMessage($this->getConfigData('specificerrmsg'));
			$result->append($error);
		}
		return $result;
	}
	public function getAllowedMethods()
	{
		return array('excellence'=>$this->getConfigData('name'));
	}
	
	
}