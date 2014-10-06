<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Jkt
 * @package     Jkt_Social
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once (Mage::getBaseDir('lib') . DS . 'Jkt' . DS . 'Facebook' . DS . 'facebook.php');

class Jkt_Social_FacebookController extends Jkt_Social_Controller_Social {

	public function getSocialType(){
		return Jkt_Social_Model_Type::FACEBOOK;
	}

	public function loginAction() {

		if ($this->getSession()->isLoggedIn()){
			return $this->_redirectUrl();
		}

		$facebook = new Facebook(array(
			'appId' => Mage::getStoreConfig('jkt_social/facebook/id'),
			'secret' => Mage::getStoreConfig('jkt_social/facebook/secret')
		));

		$social_id = $facebook->getUser();

		if ($social_id) {

			$social_collection = Mage::getModel('jkt_social/entity')
									->getCollection()
									->addFieldToFilter('social_id', $social_id)
									->addFieldToFilter('type_id', Jkt_Social_Model_Type::FACEBOOK);

			if(Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
            	$social_collection->addFieldToFilter('website_id', Mage::app()->getWebsite()->getId());
        	}
        	$social = $social_collection->getFirstItem();

        	$customer = null;

        	if ($social && $social->getId()){
        		$customer = Mage::getModel('customer/customer');
	        	if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
					$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
				}
        		$customer->load($social->getData('customer_id'));
        	}

        	if ($customer && $customer->getId()){
        		 $this->getSession()->loginById($customer->getId());
        	} else {
	        	try {
					$profile = $facebook->api('/me');
				}
				catch (FacebookApiException $e) {
					$this->getSession()->addError($e->__toString());
					$profile = null;
				}

				if (! is_null($profile)){
					$customer = Mage::getModel('customer/customer');
					if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
						$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
					}
					$customer->loadByEmail($profile['email']);

					if (!$customer->getId()){
						$customer = $this->createCustomer($profile);
					}
					if ($customer && $customer->getId()){
						$this->createSocial($profile['id'], $customer->getId());
						$this->getSession()->loginById($customer->getId());
					}
				}

        	}

		}

		return $this->_redirectUrl();

	}

}