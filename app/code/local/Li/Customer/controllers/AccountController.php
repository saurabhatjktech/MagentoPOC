<?php
/**
******
**# Controllers are not autoloaded so you will have to do it manually:
**/
?>
<?php
require_once 'Mage/Customer/controllers/AccountController.php';
class Li_Customer_AccountController extends Mage_Customer_AccountController
{
     
   public function createwholesaleAction()
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