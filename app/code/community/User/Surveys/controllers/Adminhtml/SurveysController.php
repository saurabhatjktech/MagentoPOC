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
class User_Surveys_Adminhtml_SurveysController extends Mage_Adminhtml_Controller_Action
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
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('Surveys'))
             ->_title($this->__('Manage Surveys'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new Surveys item
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit Surveys item
     */
    public function editAction()
    {
        $this->_title($this->__('Surveys'))
             ->_title($this->__('Manage Surveys'));

        // 1. instance surveys model
        /* @var $model User_Surveys_Model_Item */
        $model = Mage::getModel('user_surveys/surveys');

        // 2. if exists id, check it and load data
        $surveysId = $this->getRequest()->getParam('id');
        if ($surveysId) {
            $model->load($surveysId);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('user_surveys')->__('Surveys item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('user_surveys')->__('Edit Item');
        } else {
            $this->_title(Mage::helper('user_surveys')->__('New Item'));
            $breadCrumb = Mage::helper('user_surveys')->__('New Item');
        }

        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('surveys_item', $model);

        // 5. render layout
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            /* @var $model User_Surveys_Model_Item */
            $model = Mage::getModel('user_surveys/surveys');

            // if surveys item exists, try to load it
            $surveysId = $this->getRequest()->getParam('surveys_id');
            if ($surveysId) {
                $model->load($surveysId);
            }
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            $model->addData($data);

            try {
                $hasError = false;
                /* @var $imageHelper User_Surveys_Helper_Image */
                $imageHelper = Mage::helper('user_surveys/image');
                // remove image

                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }

                // upload new image
                $imageFile = $imageHelper->uploadImage('image');
                if ($imageFile) {
                    if ($model->getImage()) {
                        $imageHelper->removeImage($model->getImage());
                    }
                    $model->setImage($imageFile);
                }
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('user_surveys')->__('The surveys item has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('user_surveys')->__('An error occurred while saving the surveys item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model User_Surveys_Model_Item */
                $model = Mage::getModel('user_surveys/surveys');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('user_surveys')->__('Unable to find a event item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('user_surveys')->__('The event item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('user_surveys')->__('An error occurred while deleting the event item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
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
}
