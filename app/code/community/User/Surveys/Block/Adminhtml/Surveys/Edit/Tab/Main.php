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
class User_Surveys_Block_Adminhtml_Surveys_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form elements for tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::helper('user_surveys')->getSurveysItemInstance();

        /**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('user_surveys/admin')->isActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('news_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('user_surveys')->__('Surveys Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('surveys_id', 'hidden', array(
                'name' => 'surveys_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => Mage::helper('user_surveys')->__('Surveys Title'),
            'title'    => Mage::helper('user_surveys')->__('Surveys Title'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('description', 'text', array(
            'name'     => 'description',
            'label'    => Mage::helper('user_surveys')->__('Description'),
            'title'    => Mage::helper('user_surveys')->__('Description'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));



		 /*$fieldset->addField('entry_fee', 'text', array(
            'name'     => 'entry_fee',
            'label'    => Mage::helper('user_surveys')->__('Entry Fee'),
            'title'    => Mage::helper('user_surveys')->__('Entry Fee'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('contact_details', 'text', array(
            'name'     => 'contact_details',
            'label'    => Mage::helper('user_surveys')->__('Contact Details'),
            'title'    => Mage::helper('user_surveys')->__('Contact Details'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));*/

        $fieldset->addField('published_at', 'date', array(
            'name'     => 'published_at',
            'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('user_surveys')->__('Publishing Date'),
            'title'    => Mage::helper('user_surveys')->__('Publishing Date'),
            'required' => true
        ));

        $fieldset->addField('end_date', 'date', array(
            'name'     => 'end_date',
            'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('user_surveys')->__('End Date'),
            'title'    => Mage::helper('user_surveys')->__('End Date'),
            'required' => true
        ));

        $fieldset->addField('isActive', 'select', array(
            'name'     => 'isActive',
            'label'    => Mage::helper('user_surveys')->__('IsActive'),
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'title'    => Mage::helper('user_surveys')->__('IsActive'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_surveys_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }




    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('user_surveys')->__('Surveys Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('user_surveys')->__('Surveys Info');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
