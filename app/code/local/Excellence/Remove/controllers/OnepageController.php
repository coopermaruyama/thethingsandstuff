<?php
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Excellence_Remove_OnepageController extends Mage_Checkout_OnepageController
{
	public function indexAction()
	{
		if (!Mage::helper('checkout')->canOnepageCheckout()) {
			Mage::getSingleton('checkout/session')->addError($this->__('The onepage checkout is disabled.'));
			$this->_redirect('checkout/cart');
			return;
		}
		$quote = $this->getOnepage()->getQuote();
		if (!$quote->hasItems() || $quote->getHasError()) {
			$this->_redirect('checkout/cart');
			return;
		}
		if (!$quote->validateMinimumAmount()) {
			$error = Mage::getStoreConfig('sales/minimum_order/error_message');
			Mage::getSingleton('checkout/session')->addError($error);
			$this->_redirect('checkout/cart');
			return;
		}

		Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
		Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_secure'=>true)));
		$this->getOnepage()->initCheckout();
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Checkout'));

		//If you want guest checkout by default
		$method = Mage_Checkout_Model_Type_Onepage::METHOD_GUEST;
		$this->getOnepage()->saveCheckoutMethod($method);
		
		//If you want register checkout by default, uncomment code below
		/*
		 $method = Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER;
		 $this->getOnepage()->saveCheckoutMethod($method);
		*/
		

		//If you want only logged in users to be able to checkout, uncomment code below
		/*
		if(!Mage::getModel('customer/session')->isLoggedIn()){
			$this->_redirect('customer/account/login');
		}else{
			$method = Mage_Checkout_Model_Type_Onepage::METHOD_CUSTOMER;
			$this->getOnepage()->saveCheckoutMethod($method);
		}
		*/

		$this->renderLayout();
	}
	
	public function saveBillingAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			//            $postData = $this->getRequest()->getPost('billing', array());
			//            $data = $this->_filterPostData($postData);
			$data = $this->getRequest()->getPost('billing', array());
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);

			if (isset($data['email'])) {
				$data['email'] = trim($data['email']);
			}
			$result = $this->getOnepage()->saveBilling($data, $customerAddressId);

			if (!isset($result['error'])) {

				if($data['use_for_shipping'] != 0){
				$method = 'excellence_excellence';
				$result = $this->getOnepage()->saveShippingMethod($method);
				}

				if (!isset($result['error'])) {

					if ($this->getOnepage()->getQuote()->isVirtual()) {
						$result['goto_section'] = 'payment';
						$result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
						);
					} elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
						$result['goto_section'] = 'payment';
						$result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
						);

						$result['allow_sections'] = array('shipping');
						$result['duplicateBillingInfo'] = 'true';
					} else {
						$result['goto_section'] = 'shipping';
					}
				}
			}
			$this->getOnepage()->getQuote()->collectTotals()->save();
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}

	public function saveShippingAction()
	{
		if ($this->_expireAjax()) {
			return;
		}
		if ($this->getRequest()->isPost()) {
			$data = $this->getRequest()->getPost('shipping', array());
			$customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
			$result = $this->getOnepage()->saveShipping($data, $customerAddressId);

			if (!isset($result['error'])) {
				$method = 'excellence_excellence';
				$result = $this->getOnepage()->saveShippingMethod($method);

				if (!isset($result['error'])) {

					$result['goto_section'] = 'payment';
					$result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
					);
				}
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
		}
	}
}