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

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * Creating table user_surveys
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('user_surveys/surveys'))
    ->addColumn('surveys_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable' => true,
    ), 'Description')
    ->addColumn('details', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        'nullable' => true,
        'default'  => null,
    ), 'Details')
    ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, 63, array(
        'nullable' => true,
        'default'  => null,
    ), 'Content')
	->addColumn('eligibility', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        'nullable' => true,
        'default'  => null,
    ), 'Eligibility')
    ->addColumn('entry_fee', Varien_Db_Ddl_Table::TYPE_INTEGER, 100, array(
        'nullable' => true,
        'default'  => null,
    ), 'Entry Fee')
    ->addColumn('contact_details', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable' => true,
    ), 'Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Surveys image media path')
    ->addColumn('published_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'World publish date')
    ->addColumn('end_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'World publish date')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'Creation Time')
    ->addIndex($installer->getIdxName(
            $installer->getTable('user_surveys/surveys'),
            array('published_at'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('published_at'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->setComment('Survey item');

$installer->getConnection()->createTable($table);
