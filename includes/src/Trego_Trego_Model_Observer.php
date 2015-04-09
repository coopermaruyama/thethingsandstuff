<?php
class Trego_Trego_Model_Observer
{
	public function hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave()
	{
		$section = Mage::app()->getRequest()->getParam('section');
		if ($section == 'trego_settings')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
		
			Mage::getSingleton('trego/cssconfig_generator')->generateCss('settings',   $websiteCode, $storeCode);
		}
		elseif ($section == 'trego_design')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
			
			Mage::getSingleton('trego/cssconfig_generator')->generateCss('design', $websiteCode, $storeCode);
		}
	}
	
	/**
     * After store view is saved
     */
	public function hookTo_storeEdit(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();
		
		Mage::getSingleton('trego/cssconfig_generator')->generateCss('settings', $websiteCode, $storeCode);
		Mage::getSingleton('trego/cssconfig_generator')->generateCss('design', $websiteCode, $storeCode);
	}
}
