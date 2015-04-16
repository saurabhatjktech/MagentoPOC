<?php
 
class Multivendor_Vendors_Helper_Data extends Mage_Customer_Helper_Data
{
   /**
     * Retrieve Vendor register form url
     *
     * @return string
     */
    public function getVendorRegisterUrl()
    {
        return $this->_getUrl('customer/account/createvendor');
    }
}