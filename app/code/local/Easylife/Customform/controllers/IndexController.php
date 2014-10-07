<?php
class Easylife_Customform_IndexController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){ //this will display the form
        $this->loadLayout();
        $this->_initLayoutMessages('core/session'); //this will allow flash messages
        $this->renderLayout();
    }
    public function sendAction(){ //handles the form submit
        $post = $this->getRequest()->getPost();
        //do something with the posted data
        Mage::getSingleton('core/session')->addSuccess($this->__('Your message was sent'));//add success message.
        $this->_redirect('*/*');//will redirect to form page
    }
}
