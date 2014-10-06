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

class Jkt_Social_Helper_Data extends Mage_Core_Helper_Abstract {

	public function isFBActive() {
		return Mage::getStoreConfig('jkt_social/facebook/enable') && Mage::getStoreConfig('jkt_social/facebook/id') && Mage::getStoreConfig('jkt_social/facebook/secret');
	}

	public function isGActive() {
		return Mage::getStoreConfig('jkt_social/google/enable') && Mage::getStoreConfig('jkt_social/google/id') && Mage::getStoreConfig('jkt_social/google/secret') && Mage::getStoreConfig('jkt_social/google/api');
	}

	public function isLIActive() {
		return Mage::getStoreConfig('jkt_social/linkedin/enable') && Mage::getStoreConfig('jkt_social/linkedin/id') && Mage::getStoreConfig('jkt_social/linkedin/secret');
	}

	public function isActive() {
		return Mage::getStoreConfig('jkt_social/general/enable') && ($this->isFBActive() || $this->isGActive() || $this->isLIActive());
	}

	public function getServices($place = '') {

		$result = array();

		if (! $place) {
			return $result;
		}

		$selected_services = Mage::getStoreConfig('jkt_social/general/' . $place);

		$selected_services = explode(',', $selected_services);

		if ($this->isFBActive() && in_array(Jkt_Social_Model_Type::FACEBOOK, $selected_services)) {
			$result[Jkt_Social_Model_Type::FACEBOOK] = Mage::getStoreConfig('jkt_social/facebook/order');
		}
		if ($this->isGActive() && in_array(Jkt_Social_Model_Type::GOOGLE, $selected_services)) {
			$result[Jkt_Social_Model_Type::GOOGLE] = Mage::getStoreConfig('jkt_social/google/order');
		}
		if ($this->isLIActive() && in_array(Jkt_Social_Model_Type::LINKEDIN, $selected_services)) {
			$result[Jkt_Social_Model_Type::LINKEDIN] = Mage::getStoreConfig('jkt_social/linkedin/order');
		}

		natcasesort($result);

		return $result;
	}

	public function getAllStoreDomains() {

		$domains = array();

		foreach (Mage::app()->getWebsites() as $website) {

			$url = $website->getConfig('web/unsecure/base_url');

			if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
				$domains[] = $domain;
			}

			$url = $website->getConfig('web/secure/base_url');

			if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
				$domains[] = $domain;
			}
		}
		return array_unique($domains);

	}

	public function setInformation() {

		$value = "";

		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, sprintf('https://www.jkt.com/index.php/jkt_downloadable/key/getinformation'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'sku=social-connector&domains=' . urlencode(implode(',', $this->getAllStoreDomains())) . '&ver=' . urlencode('1.0'));
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

			$value = curl_exec($ch);
		}
		catch (Exception $e) {
		}

		if ($value && ($value != Mage::getStoreConfig('jkt_social/information/text'))) {
			Mage::getModel('core/config')->saveConfig('jkt_social/information/text', $value);
			Mage::getConfig()->reinit();
			Mage::app()->reinitStores();
		}

	}

	public function notify(){

    	$frequency = intval(Mage::app()->loadCache('jkt_notifications_frequency'));
    	if (!$frequency){
    		$frequency = 24;
    	}
    	$last_update = intval(Mage::app()->loadCache('jkt_notifications_last_update'));

    	if (($frequency * 60 * 60 + $last_update) > time()) {
            return false;
        }

        $timestamp = $last_update;
        if (!$timestamp){
        	$timestamp = time();
        }

        try{
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, sprintf('https://www.jkt.com/index.php/jkt_notification/index/data'));
	        curl_setopt($ch, CURLOPT_POST, true);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, 'sku=social-connector&timestamp='.$timestamp.'&ver='.urlencode('1.0'));
	        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	        $content = curl_exec($ch);

	        $result	= Zend_Json::decode($content);

	        if ($result && isset($result['frequency']) && ($result['frequency'] != $frequency)){
	        	Mage::app()->saveCache($result['frequency'], 'jkt_notifications_frequency');
	        }

	    	if ($result && isset($result['data'])){
	        	if (!empty($result['data'])){
	        		Mage::getModel('adminnotification/inbox')->parse($result['data']);
	        	}
	        }
        } catch (Exception $e){}

        Mage::app()->saveCache(time(), 'jkt_notifications_last_update');

    }


}
