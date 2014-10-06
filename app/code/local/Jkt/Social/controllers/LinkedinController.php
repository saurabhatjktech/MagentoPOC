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

require_once (Mage::getBaseDir('lib') . DS . 'Jkt' . DS . 'Linkedin' . DS . 'linkedinoauth.php');

class Jkt_Social_LinkedinController extends Jkt_Social_Controller_Social {

	public function getSocialType() {
		return Jkt_Social_Model_Type::LINKEDIN;
	}

	public function loginAction() {

		if ($this->getSession()->isLoggedIn()) {
			return $this->_redirectUrl();
		}

		$connection = new LinkedInOAuth(Mage::getStoreConfig('jkt_social/linkedin/id'), Mage::getStoreConfig('jkt_social/linkedin/secret'));

		$callback_params = array('_secure' => true);
		if ($this->getRequest()->getParam('gs_url', '')) {
			$callback_params['gs_url'] = $this->getRequest()->getParam('gs_url');
		}

		$callback_url = Mage::getUrl('jkt_social/linkedin/callback', $callback_params);
		$request_token = $connection->getRequestToken($callback_url);

		switch ($connection->http_code) {
			case 200:
				Mage::getSingleton('core/session')->setData('oauth_token', $request_token['oauth_token']);
				Mage::getSingleton('core/session')->setData('oauth_token_secret', $request_token['oauth_token_secret']);

    			$url = $connection->getAuthorizeURL($request_token['oauth_token']);
				return $this->_redirectUrl($url);
			break;
			default:
				$this->getSession()->addError($this->__('Could not connect to LinkedIn. Refresh the page or try again later.'));
		}

		return $this->_redirectUrl();

	}

	public function callbackAction(){

		$oauth_token = $this->getRequest()->getParam('oauth_token');
		$oauth_verifier = $this->getRequest()->getParam('oauth_verifier');

		if (!$oauth_token || !$oauth_verifier){
			return $this->_redirectUrl();
		}

		if ($oauth_token != Mage::getSingleton('core/session')->getData('oauth_token')){
			return $this->_redirectUrl();
		}

		$connection = new LinkedInOAuth(Mage::getStoreConfig('jkt_social/linkedin/id'), Mage::getStoreConfig('jkt_social/linkedin/secret'), Mage::getSingleton('core/session')->getData('oauth_token'), Mage::getSingleton('core/session')->getData('oauth_token_secret'));
		$access_token = $connection->getAccessToken($oauth_verifier);

		Mage::getSingleton('core/session')->unsetData('oauth_token');
		Mage::getSingleton('core/session')->unsetData('oauth_token_secret');

		$profile = null;

		switch ($connection->http_code) {
			case 200:
    			$profile = $connection->get('v1/people/~:(id,first-name,last-name,email-address)', array('format' => 'json'));
			break;
			default:
				$this->getSession()->addError($this->__('Could not connect to LinkedIn. Refresh the page or try again later.'));
				return $this->_redirectUrl();
		}

		if ($profile) {

			$social_collection = Mage::getModel('jkt_social/entity')
									->getCollection()
									->addFieldToFilter('social_id', $profile->id)
									->addFieldToFilter('type_id', Jkt_Social_Model_Type::LINKEDIN);

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

				$customer = Mage::getModel('customer/customer');
				if (Mage::getSingleton('customer/config_share')->isWebsiteScope()) {
					$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
				}

				$profile->email = $profile->emailAddress;
				$profile->first_name = $profile->firstName;
				$profile->last_name = $profile->lastName;

				$customer->loadByEmail($profile->email);

				if (!$customer->getId()){
					$customer = $this->createCustomer($profile);
				}
				if ($customer && $customer->getId()){
					$this->createSocial($profile->id, $customer->getId());
					$this->getSession()->loginById($customer->getId());
				}

        	}
		}

		return $this->_redirectUrl();
	}
}