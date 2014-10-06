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

require_once (Mage::getBaseDir('lib') . DS . 'Jkt' . DS . 'Google' . DS . 'Google_Client.php');
require_once (Mage::getBaseDir('lib') . DS . 'Jkt' . DS . 'Google' . DS . 'contrib' . DS . 'Google_Oauth2Service.php');
require_once (Mage::getBaseDir('lib') . DS . 'Jkt' . DS . 'Google' . DS . 'contrib' . DS . 'Google_PlusService.php');

class Jkt_Social_GoogleController extends Jkt_Social_Controller_Social {

	public function getSocialType(){
		return Jkt_Social_Model_Type::GOOGLE;
	}

	private function getGoogleClient(){

		$client = new Google_Client();
		$client->setApplicationName($this->__('Login with Google'));
		$client->setClientId(Mage::getStoreConfig('jkt_social/google/id'));
		$client->setClientSecret(Mage::getStoreConfig('jkt_social/google/secret'));

		if ($this->getRequest()->getParam('google_plus', 0) == 1) {
			$client->setRedirectUri('postmessage');
		}else {
			$callback_params = array('_secure' => true);
			$callback_url = Mage::getUrl('jkt_social/google/callback', $callback_params);
			$client->setRedirectUri($callback_url);
		}

		$client->setDeveloperKey(Mage::getStoreConfig('jkt_social/google/api'));

		return $client;
	}

	public function loginAction() {

		if ($this->getSession()->isLoggedIn()) {
			return $this->_redirectUrl();
		}

		$client = $this->getGoogleClient();

		$google_oauthV2 = new Google_Oauth2Service($client);
		$auth_url = $client->createAuthUrl();

		if ($this->getRequest()->getParam('gs_url', '')) {
			Mage::getSingleton('core/session')->setData('gs_url', $this->getRequest()->getParam('gs_url'));
		}

		return $this->_redirectUrl($auth_url);

	}


	public function callbackAction(){

		$code = $this->getRequest()->getParam('code');

		if ($code){

			$client = $this->getGoogleClient();

			$google_oauthV2 = new Google_Oauth2Service($client);

			if ($this->getRequest()->getParam('google_plus', 0) == 1) {
				$plus = new Google_PlusService($client);
			}

			$client->authenticate($code);

			if ($client->getAccessToken()){

					$profile = $google_oauthV2->userinfo->get();

					if ($profile && is_array($profile) && isset($profile['id'])) {

						$social_collection = Mage::getModel('jkt_social/entity')
												->getCollection()
												->addFieldToFilter('social_id', $profile['id'])
												->addFieldToFilter('type_id', Jkt_Social_Model_Type::GOOGLE);

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
							$customer->loadByEmail($profile['email']);

							if (!$customer->getId()){
								$profile['first_name'] = $profile['given_name'];
								$profile['last_name'] = $profile['family_name'];
								$customer = $this->createCustomer($profile);
							}
							if ($customer && $customer->getId()){
								$this->createSocial($profile['id'], $customer->getId());
								$this->getSession()->loginById($customer->getId());
							}

			        	}

					}

			}else{
				$this->getSession()->addError($this->__('Could not connect to Google. Refresh the page or try again later.'));
			}

		}else{
			$this->getSession()->addError($this->__('Could not connect to Google. Refresh the page or try again later.'));
		}

		if ($this->getRequest()->getParam('google_plus', 0) == 1) {

			$result = array();
			$result['redirect'] = $this->_getRedirectUrl();
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

		}else{

			return $this->_redirectUrl();

		}

	}


}