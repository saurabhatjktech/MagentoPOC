<?php
/**
******
**# Controllers are not autoloaded so you will have to do it manually:
**/
?>
<?php
require_once 'Mage/Customer/controllers/AccountController.php';
class Multivendor_Vendors_AccountController extends Mage_Customer_AccountController
{
    
    /**
      * Vendor create function
      *
      * @return session
      */ 
   public function createVendorAction()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }
}