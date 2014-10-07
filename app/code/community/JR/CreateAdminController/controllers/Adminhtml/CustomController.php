<?php
 
class JR_CreateAdminController_Adminhtml_CustomController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Survey')
            ->_title($this->__('Manage Forms'));
 	
        $this->renderLayout();
    }

    /**
     * Create new Events item
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
 
}