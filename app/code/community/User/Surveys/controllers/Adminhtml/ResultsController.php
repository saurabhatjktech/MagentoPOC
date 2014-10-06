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
 * @category    User
 * @package     User_Surveys
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class User_Surveys_Adminhtml_ResultsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return User_Surveys_Adminhtml_SurveysController
     */

    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('surveys/manage')
            ->_addBreadcrumb(
                  Mage::helper('user_surveys')->__('Surveys'),
                  Mage::helper('user_surveys')->__('Surveys')
              )
            ->_addBreadcrumb(
                  Mage::helper('user_surveys')->__('Manage Surveys'),
                  Mage::helper('user_surveys')->__('Manage Surveys')
              )
        ;
        return $this;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('surveys/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('surveys/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('surveys/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_published'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Flush Surveys Posts Images Cache action
     */
    public function flushAction()
    {
        if (Mage::helper('user_surveys/image')->flushImagesCache()) {
            $this->_getSession()->addSuccess('Cache successfully flushed');
        } else {
            $this->_getSession()->addError('There was error during flushing cache');
        }
        $this->_forward('index');
    }
    

    public function indexAction()
    {
        $this->_title($this->__('Surveys'))
             ->_title($this->__('Manage Surveys'));

        $this->_initAction();
        $this->renderLayout();
    }


    public function viewAction()
    {
        $this->_title($this->__('View'))
             ->_title($this->__('Surveys Feedback'));
        /*$userId= $this->getRequest()->getParam('userId');
        $formId= $this->getRequest()->getParam('formId');
        $model = Mage::getModel('user_surveys/surveys')->getCollection();
        $model->addFieldToFilter('user_id', array('eq' => $userId));
        $model->addFieldToFilter('form_id', array('eq' => $formId));
        $model->load();
         $collection= Mage::getModel('user_surveys/questions');
        $questionId= $collection->getId(); 
        $collection->getCollection()->addFieldToFilter('id', array('eq' => $questionId));
         echo'<pre>';
        print_r($model);
        echo'</pre>';
         Mage::register('model_view', $model); 
       */       
        
        $this->_initAction();
        $this->renderLayout();
    }

    public function resultsAction()
    {
        
        $this->_title($this->__('Feedbacks'))
             ->_title($this->__('Manage Feedbacks'));

        $this->_initAction();
        $this->renderLayout();
    }
}
