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
class User_Surveys_Block_Adminhtml_Results_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form action
     *
     * @return User_Surveys_Block_Adminhtml_News_Edit_Form
     */
    protected function _prepareForm()
    {
       die('jask');
        
        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('User Reviews')
            )
        );
        if ($surveys_item->getuserId()) {
            $fieldset->addField('user_id', 'hidden', array(
                'name' => 'user_id',
            ));
        }
        // Add the fields that we want to be editable.
        $fieldset->addField('form_name', 'text', array(
            'name'     => 'form_name',
            'label'    => Mage::helper('user_surveys')->__('Form Name'),
            'title'    => Mage::helper('user_surveys')->__('Form Name'),
            'required' => false,
            
        ));
        
        foreach ($result as $value) {
            $flag = '';
            if( in_array($value['id'], $questions_ids) ){
                $flag = 'checked';
            }else $flag = '';

            $fieldset->addField($value['id'], 'checkbox' , array(
                'name'     => 'questionsid_'.$value['id'],
                'label'    => Mage::helper('user_surveys')->__($value['questions']),
                'title'    => Mage::helper('user_surveys')->__($value['questions']),
                'required' => false,
                'checked'  => $flag,
                'onchange'  => 'this.value = this.checked ? '.$value['id'].' : 0;'
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

}

