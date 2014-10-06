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

class Jkt_Social_Block_Login extends Mage_Core_Block_Template {

	private $place;

	public function __construct() {
		parent::__construct();
		if (! $this->getSession()->isLoggedIn() && Mage::helper('jkt_social')->isActive()) {
			$this->setTemplate('jkt/social/login.phtml');
		}
	}

	private function getSession() {
		return Mage::getSingleton('customer/session');
	}

	public function setPlace($place) {
		$this->place = $place;
		return $this;
	}

	public function getPlace() {
		return $this->place;
	}

	public function getImage($service = '') {
		if ($service) {
			$image = Mage::getStoreConfig('jkt_social/' . $service . '/image');
			if ($image) {
				return Mage::getBaseUrl('media') . 'jkt/social/' . $image;
			}
		}
		return false;
	}

	public function getText($service = '') {
		if ($service) {
			$text = Mage::getStoreConfig('jkt_social/' . $service . '/text');
			if ($text) {
				return $text;
			}
		}

		return $this->__('Login');
	}

	public function getLoginType($service = '') {

		return Mage::getStoreConfig('jkt_social/'. $service .'/' . $this->getPlace() . '_type');

	}

}
