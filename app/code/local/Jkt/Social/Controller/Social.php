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

abstract class Jkt_Social_Controller_Social extends Mage_Core_Controller_Front_Action {

	abstract function getSocialType();

	protected function getSession(){
		return Mage::getSingleton('customer/session');
	}

	protected function createSocial($social_id, $customer_id){
		return Mage::getModel('jkt_social/entity')
					->setData('social_id', $social_id)
					->setData('type_id', $this->getSocialType())
					->setData('customer_id', $customer_id)
					->setData('website_id', Mage::app()->getWebsite()->getId())
					->save();
	}

	protected function createCustomer($profile){

		$customer = Mage::getModel('customer/customer');
		$password =  $customer->generatePassword(8);

		if (is_array($profile)){
			$profile = (object)$profile;
		}

        $customer->setData('firstname', $profile->first_name)
        		 ->setData('lastname', $profile->last_name)
        		 ->setData('email', $profile->email)
        		 ->setData('password', $password)
        		 ->setSkipConfirmationIfEmail($profile->email)
        		 ->setConfirmation($password);

        $errors = $customer->validate();

        if (is_array($errors) && count($errors)){
        	$this->getSession()->addError(implode(' ', $errors));
        	return false;
        }

        $customer->save();
        $customer->sendNewAccountEmail();

        return $customer;

	}

	protected function _getRedirectUrl($url){
		if (!$url){
    		$url = $this->getRequest()->getParam('gs_url', '');

    		if (!$url && Mage::getSingleton('core/session')->getData('gs_url')){
    			$url = Mage::getSingleton('core/session')->getData('gs_url');
    			Mage::getSingleton('core/session')->unsetData('gs_url');
    		}

    		if ($url){
    			$url = Mage::helper('core')->urlDecode($url);
    		}
    	}
    	if (!$url){
    		$url = Mage::getBaseUrl();
    	}

    	return $url;
	}

	protected function _redirectUrl($url)
    {
    	return parent::_redirectUrl($this->_getRedirectUrl($url));
    }

}