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
 /*Start by Ankush*/
class User_Surveys_Block_Adminhtml_Results_View_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form action
     *
     * @return User_Surveys_Block_Adminhtml_News_Edit_Form
     */
    protected function _prepareForm()
    {
        $id= $this->getRequest()->getParam('userId');

        $formid= $this->getRequest()->getParam('formId');

        $formModel= Mage::getModel('user_surveys/forms')->load($formid);
        $formName= $formModel->getFormName();
        //echo "<pre>"; print($name); echo "</pre>"; die("here...1!!");

        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('entity_id', array('eq' => $id))
        ->addAttributeToSelect('email');
        $user_data = $collection->getData();
        $customerEmail = $user_data[0]['email'];
        $model = Mage::registry('viewModel');
				
		$model->getSelect()
		->joinLeft(array('que' => 'surveys_questions'),
				'main_table.question_id = que.id',
				array('surveys_questions' => 'questions'));

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('User Reviews :   ' .$customerEmail .'<br/>' .'Form Name :  ' . $formName)
            )
        );
       
        $data= $model->getData();
        
        foreach ($data as $key=>$value){
        	$text = 'Question : '. $value['surveys_questions'].
        			' Answer : '.$value['value'];
        	$a= nl2br($text);
        	 $fieldset->addField($value['question_id'], 'text', array(
        			'name'     => 'value'.$value['question_id'],
        			'value'    => $value['value'],
        			'label'    => Mage::helper('user_surveys')->__($a),
        			'title'    => Mage::helper('user_surveys')->__($value['question_id']),
        			'style'    => 'display:none;',
        			'required' => false,        			
        	));
        }
  
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

}
/*End by Ankush*/

